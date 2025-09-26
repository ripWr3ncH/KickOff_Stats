<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\League;
use App\Models\Team;
use App\Models\FootballMatch;
use Carbon\Carbon;

class SyncHistoricalMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:sync-historical {--from= : Start date (Y-m-d format)} {--to= : End date (Y-m-d format)} {--days=30 : Number of days back from today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync historical matches from Football Data API for a specified date range';

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
            // Determine date range
            if ($this->option('from') && $this->option('to')) {
                $dateFrom = Carbon::parse($this->option('from'))->format('Y-m-d');
                $dateTo = Carbon::parse($this->option('to'))->format('Y-m-d');
            } else {
                $days = $this->option('days') ?? 30;
                $dateFrom = today()->subDays($days)->format('Y-m-d');
                $dateTo = today()->format('Y-m-d');
            }

            $this->info("Syncing historical matches from {$dateFrom} to {$dateTo}...");
            
            // Sync matches for the date range
            $data = $this->footballService->getMatchesByDate($dateFrom, $dateTo);

            if (!$data || !isset($data['matches'])) {
                $this->error('Failed to fetch matches from API');
                return;
            }

            $leagueMapping = $this->footballService->getLeagueMapping();
            $this->info('Processing ' . count($data['matches']) . ' matches...');
            
            $syncedCount = 0;
            $updatedCount = 0;

            foreach ($data['matches'] as $matchData) {
                $competitionId = $matchData['competition']['id'];
                
                if (!isset($leagueMapping[$competitionId])) {
                    continue;
                }

                $league = League::where('slug', $leagueMapping[$competitionId])->first();
                if (!$league) {
                    continue;
                }

                // Parse match date
                $matchDate = Carbon::parse($matchData['utcDate']);
                
                // Find or create teams
                $homeTeam = $this->findOrCreateTeam($matchData['homeTeam']['name'], $matchData['homeTeam']['shortName'] ?? null);
                $awayTeam = $this->findOrCreateTeam($matchData['awayTeam']['name'], $matchData['awayTeam']['shortName'] ?? null);

                // Check if match already exists
                $existingMatch = FootballMatch::where('league_id', $league->id)
                    ->where('home_team_id', $homeTeam->id)
                    ->where('away_team_id', $awayTeam->id)
                    ->whereDate('match_date', $matchDate->toDateString())
                    ->whereTime('match_date', $matchDate->format('H:i:s'))
                    ->first();

                if (!$existingMatch) {
                    FootballMatch::create([
                        'league_id' => $league->id,
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $awayTeam->id,
                        'match_date' => $matchDate,
                        'status' => $this->mapStatus($matchData['status']),
                        'home_score' => $matchData['score']['fullTime']['home'] ?? 0,
                        'away_score' => $matchData['score']['fullTime']['away'] ?? 0,
                        'minute' => $matchData['minute'] ?? null,
                    ]);

                    $syncedCount++;
                    
                    if ($syncedCount % 50 == 0) {
                        $this->comment("Processed {$syncedCount} matches...");
                    }
                } else {
                    // Update existing match if status or scores changed
                    $updated = $existingMatch->update([
                        'status' => $this->mapStatus($matchData['status']),
                        'home_score' => $matchData['score']['fullTime']['home'] ?? $existingMatch->home_score,
                        'away_score' => $matchData['score']['fullTime']['away'] ?? $existingMatch->away_score,
                        'minute' => $matchData['minute'] ?? $existingMatch->minute,
                    ]);
                    
                    if ($updated) {
                        $updatedCount++;
                    }
                }
            }

            $this->info("Historical sync completed!");
            $this->info("- New matches: {$syncedCount}");
            $this->info("- Updated matches: {$updatedCount}");
            
            // Show current database stats
            $totalMatches = FootballMatch::count();
            $earliestMatch = FootballMatch::orderBy('match_date')->first();
            $latestMatch = FootballMatch::orderBy('match_date', 'desc')->first();
            
            $this->info("Database now contains {$totalMatches} matches");
            if ($earliestMatch && $latestMatch) {
                $this->info("Date range: {$earliestMatch->match_date->format('M j, Y')} to {$latestMatch->match_date->format('M j, Y')}");
            }

        } catch (\Exception $e) {
            $this->error('Failed to sync historical matches: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    private function findOrCreateTeam($name, $shortName = null)
    {
        // Try exact match first
        $team = Team::where('name', $name)->first();
        
        if (!$team) {
            // Try flexible matching
            $cleanName = str_replace([' FC', 'FC ', ' CF'], '', $name);
            $team = Team::where('name', 'like', "%{$cleanName}%")->first();
        }

        if (!$team) {
            // Create new team if not found
            $team = Team::create([
                'name' => $name,
                'short_name' => $shortName ?? substr($name, 0, 3),
                'league_id' => 1, // Default to first league
            ]);
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