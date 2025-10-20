<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\League;
use App\Models\Team;
use App\Models\FootballMatch;
use Carbon\Carbon;

class SyncTodayMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:sync-today {--live : Sync live matches instead of today\'s matches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync today\'s matches or live matches from Football Data API';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
        if ($this->option('live')) {
            $this->info('Syncing LIVE matches from Football Data API...');
            $data = $this->footballService->getLiveMatches();
        } else {
            $this->info('Syncing today\'s matches from Football Data API...');
            // Use broader date range to catch matches
            $dateFrom = today()->subDays(1)->format('Y-m-d');
            $dateTo = today()->addDays(1)->format('Y-m-d');
            $this->comment("Date range: {$dateFrom} to {$dateTo}");
            $data = $this->footballService->getMatchesByDate($dateFrom, $dateTo);
        }            if (!$data || !isset($data['matches'])) {
                $this->error('Failed to fetch matches from API');
                return;
            }

            $leagueMapping = $this->footballService->getLeagueMapping();
            $this->info('League mapping: ' . json_encode($leagueMapping));
            $syncedCount = 0;

            foreach ($data['matches'] as $matchData) {
                $competitionId = $matchData['competition']['id'];
                
                $this->comment("Processing: {$matchData['homeTeam']['name']} vs {$matchData['awayTeam']['name']} [Competition: {$competitionId}]");
                
                if (!isset($leagueMapping[$competitionId])) {
                    $this->warn("  → Skipped: Competition ID {$competitionId} not in mapping");
                    continue;
                }

                $league = League::where('slug', $leagueMapping[$competitionId])->first();
                if (!$league) {
                    continue;
                }

                // Parse match date first
                $matchDate = Carbon::parse($matchData['utcDate']);
                
                // Find or create home team
                $homeTeam = $this->findOrCreateTeam($matchData['homeTeam']['name'], $matchData['homeTeam']['shortName'] ?? null);
                $awayTeam = $this->findOrCreateTeam($matchData['awayTeam']['name'], $matchData['awayTeam']['shortName'] ?? null);

                // Check if match already exists - try multiple ways
                // 1. Try by API match ID first (most reliable)
                $existingMatch = FootballMatch::where('api_match_id', $matchData['id'])->first();
                
                // 2. If not found, try by date and teams
                if (!$existingMatch) {
                    $existingMatch = FootballMatch::where('league_id', $league->id)
                        ->where('home_team_id', $homeTeam->id)
                        ->where('away_team_id', $awayTeam->id)
                        ->whereDate('match_date', $matchDate->toDateString())
                        ->first();
                }
                
                // 3. Check for reverse fixture on same date (home/away swapped)
                if (!$existingMatch) {
                    $existingMatch = FootballMatch::where('league_id', $league->id)
                        ->where('home_team_id', $awayTeam->id)
                        ->where('away_team_id', $homeTeam->id)
                        ->whereDate('match_date', $matchDate->toDateString())
                        ->first();
                }

                if (!$existingMatch) {
                    FootballMatch::create([
                        'api_match_id' => $matchData['id'] ?? null,
                        'league_id' => $league->id,
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $awayTeam->id,
                        'match_date' => $matchDate,
                        'status' => $this->mapStatus($matchData['status']),
                        'home_score' => $matchData['score']['fullTime']['home'] ?? 0,
                        'away_score' => $matchData['score']['fullTime']['away'] ?? 0,
                        'minute' => $matchData['minute'] ?? null,
                    ]);

                    $this->line("✓ Added: {$homeTeam->short_name} vs {$awayTeam->short_name} [{$matchData['status']}]");
                    $syncedCount++;
                } else {
                    // Update existing match
                    $existingMatch->update([
                        'api_match_id' => $matchData['id'] ?? $existingMatch->api_match_id,
                        'status' => $this->mapStatus($matchData['status']),
                        'home_score' => $matchData['score']['fullTime']['home'] ?? $existingMatch->home_score,
                        'away_score' => $matchData['score']['fullTime']['away'] ?? $existingMatch->away_score,
                        'minute' => $matchData['minute'] ?? $existingMatch->minute,
                    ]);
                    
                    $this->comment("↻ Updated: {$homeTeam->short_name} vs {$awayTeam->short_name} [{$matchData['status']}]");
                }
            }

            $this->info("Synced {$syncedCount} new matches for today!");
            
            // Show live matches count
            $liveCount = FootballMatch::where('status', 'live')->count();
            $this->info("Current live matches: {$liveCount}");

        } catch (\Exception $e) {
            $this->error('Failed to sync matches: ' . $e->getMessage());
        }
    }

    private function findOrCreateTeam($name, $shortName = null)
    {
        // Try exact match first
        $team = Team::where('name', $name)->first();
        
        if (!$team) {
            // Comprehensive team name variations for better matching
            $variations = [
                // Remove common suffixes
                str_replace([' FC', ' CF', ' AFC', ' United FC', ' City FC', ' Hotspur FC', 
                            'FC ', 'CF ', 'AFC ', 'Fútbol Club', 'Club de Fútbol', 'Calcio',
                            'Club', ' 1909', ' 1907', ' 1963', 'de Madrid', 'de Barcelona', 
                            'Balompié', 'de Fútbol', 'Società Sportiva', 'CFC', 'GFC'], '', $name),
                // Simplify 'Deportivo' prefix
                str_replace('Deportivo ', '', $name),
                str_replace('Deportivo Alavés', 'Alaves', $name),
                // Handle 'Real' prefix
                str_replace('Real ', '', $name),
                // Handle 'Athletic' variations
                str_replace('Athletic Club', 'Athletic Bilbao', $name),
                str_replace('Athletic Bilbao', 'Athletic Club', $name),
            ];
            
            // Try each variation
            foreach ($variations as $variation) {
                $cleanName = trim($variation);
                if (empty($cleanName)) continue;
                
                $team = Team::where('name', 'like', "%{$cleanName}%")
                    ->orWhere('name', $cleanName)
                    ->first();
                
                if ($team) {
                    $this->comment("  → Matched '{$name}' to existing team '{$team->name}'");
                    return $team;
                }
            }
            
            // Try matching by significant words (at least 5 characters)
            $words = explode(' ', $name);
            foreach ($words as $word) {
                if (strlen($word) >= 5) {
                    $team = Team::where('name', 'like', "%{$word}%")->first();
                    if ($team) {
                        $this->comment("  → Matched '{$name}' to existing team '{$team->name}' via keyword");
                        return $team;
                    }
                }
            }
        } else {
            return $team;
        }

        // Only create if no match found
        if (!$team) {
            $slug = \Illuminate\Support\Str::slug($name);
            
            // Check if slug already exists and make it unique
            $originalSlug = $slug;
            $counter = 1;
            while (Team::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $shortName = $shortName ?? substr($name, 0, 10); // Max 10 characters
            $team = Team::create([
                'name' => $name,
                'short_name' => substr($shortName, 0, 10), // Ensure max 10 chars
                'slug' => $slug,
                'city' => 'Unknown', // Required field
                'league_id' => 1, // Default to first league
            ]);
            $this->warn("  ⚠️  Created new team: {$name} (no existing match found)");
        }

        return $team;
    }

    private function mapStatus($apiStatus)
    {
        $statusMap = [
            'SCHEDULED' => 'scheduled',
            'TIMED' => 'scheduled',
            'IN_PLAY' => 'live',
            'PAUSED' => 'live',
            'FINISHED' => 'finished',
            'POSTPONED' => 'postponed',
            'SUSPENDED' => 'postponed',
            'CANCELLED' => 'postponed'
        ];

        return $statusMap[$apiStatus] ?? 'scheduled';
    }
}
