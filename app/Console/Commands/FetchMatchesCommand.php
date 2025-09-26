<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\FootballMatch;
use App\Models\League;
use App\Models\Team;
use Carbon\Carbon;

class FetchMatchesCommand extends Command
{
    protected $signature = 'matches:fetch {--days=7 : Number of days to fetch matches for}';
    protected $description = 'Fetch matches from Football Data API and store in database';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    public function handle()
    {
        $this->info('Fetching matches from Football Data API...');
        
        $days = (int) $this->option('days');
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays($days);
        
        $totalCreated = 0;
        
        // Fetch matches for each day
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $this->info("Fetching matches for {$date->format('Y-m-d')}...");
            
            $matchesData = $this->footballService->getMatchesByDate($date->format('Y-m-d'));
            
            if (!$matchesData || !isset($matchesData['matches'])) {
                $this->warn("No data received for {$date->format('Y-m-d')}");
                continue;
            }
            
            $this->info("API returned {$matchesData['resultSet']['count']} total matches for {$date->format('Y-m-d')}");
            
            $created = $this->processMatches($matchesData['matches']);
            $totalCreated += $created;
            
            $this->info("Created {$created} matches for {$date->format('Y-m-d')}");
            
            // Rate limiting - wait 1 second between requests
            sleep(1);
        }
        
        $this->info("✅ Successfully created {$totalCreated} matches!");
        
        return 0;
    }
    
    protected function processMatches($matches)
    {
        $created = 0;
        $leagueMapping = $this->footballService->getLeagueMapping();
        
        foreach ($matches as $matchData) {
            $competitionId = $matchData['competition']['id'];
            
            // Skip if league not in our mapping
            if (!isset($leagueMapping[$competitionId])) {
                $this->line("Skipping competition: {$matchData['competition']['name']} (ID: {$competitionId})");
                continue;
            }
            
            $leagueSlug = $leagueMapping[$competitionId];
            
            // Find or create league
            $league = League::where('slug', $leagueSlug)->first();
            if (!$league) {
                $this->warn("League not found: {$leagueSlug}");
                continue;
            }
            
            $this->line("Processing match in {$league->name}: {$matchData['homeTeam']['name']} vs {$matchData['awayTeam']['name']}");
            
            // Find or create teams
            $homeTeam = $this->findOrCreateTeam($matchData['homeTeam']);
            $awayTeam = $this->findOrCreateTeam($matchData['awayTeam']);
            
            if (!$homeTeam || !$awayTeam) {
                continue;
            }
            
            // Check if match already exists
            $existingMatch = FootballMatch::where([
                'league_id' => $league->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'match_date' => Carbon::parse($matchData['utcDate'])
            ])->first();
            
            if ($existingMatch) {
                $this->line("  Match already exists, skipping...");
                continue; // Skip if match already exists
            }
            
            // Create new match
            FootballMatch::create([
                'league_id' => $league->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'match_date' => Carbon::parse($matchData['utcDate']),
                'home_score' => $matchData['score']['fullTime']['home'],
                'away_score' => $matchData['score']['fullTime']['away'],
                'status' => $this->mapStatus($matchData['status']),
                'minute' => $matchData['minute'] ?? null,
                'venue' => $matchData['venue'] ?? null,
                'matchweek' => $matchData['matchday'] ?? null
            ]);
            
            $this->line("  ✅ Match created successfully!");
            $created++;
        }
        
        return $created;
    }
    
    protected function findOrCreateTeam($teamData)
    {
        $team = Team::where('name', $teamData['name'])->first();
        
        if (!$team) {
            $team = Team::create([
                'name' => $teamData['name'],
                'short_name' => $teamData['shortName'] ?? substr($teamData['name'], 0, 3),
                'country' => $teamData['area']['name'] ?? 'Unknown',
                'founded' => $teamData['founded'] ?? null,
                'venue' => $teamData['venue'] ?? null,
                'website' => $teamData['website'] ?? null,
                'logo' => $teamData['crest'] ?? null
            ]);
        }
        
        return $team;
    }
    
    protected function mapStatus($apiStatus)
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