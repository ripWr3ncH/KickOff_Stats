<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\FootballMatch;
use App\Models\League;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FetchRealMatchesCommand extends Command
{
    protected $signature = 'matches:fetch-real {--competition=2021 : Competition ID to fetch}';
    protected $description = 'Fetch real matches from specific competitions';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    public function handle()
    {
        $competitionId = $this->option('competition');
        $this->info("Fetching matches for competition ID: {$competitionId}");
        
        // Get competition matches directly
        $response = $this->footballService->getCompetitionMatches($competitionId);
        
        if (!$response || !isset($response['matches'])) {
            $this->error('No matches received from API');
            return 1;
        }
        
        $this->info("API returned {$response['resultSet']['count']} total matches");
        
        $leagueMapping = $this->footballService->getLeagueMapping();
        
        if (!isset($leagueMapping[$competitionId])) {
            $this->error("Competition ID {$competitionId} not in league mapping");
            return 1;
        }
        
        $leagueSlug = $leagueMapping[$competitionId];
        $league = League::where('slug', $leagueSlug)->first();
        
        if (!$league) {
            $this->error("League not found: {$leagueSlug}");
            return 1;
        }
        
        $created = 0;
        $updated = 0;
        
        foreach ($response['matches'] as $matchData) {
            $this->line("Processing: {$matchData['homeTeam']['name']} vs {$matchData['awayTeam']['name']} ({$matchData['utcDate']})");
            
            // Find or create teams
            $homeTeam = $this->findOrCreateTeam($matchData['homeTeam'], $league);
            $awayTeam = $this->findOrCreateTeam($matchData['awayTeam'], $league);
            
            if (!$homeTeam || !$awayTeam) {
                $this->warn("  Could not create teams, skipping...");
                continue;
            }
            
            // Check if match exists
            $existingMatch = FootballMatch::where([
                'league_id' => $league->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
            ])->whereDate('match_date', Carbon::parse($matchData['utcDate'])->format('Y-m-d'))->first();
            
            if ($existingMatch) {
                // Update existing match
                $existingMatch->update([
                    'home_score' => $matchData['score']['fullTime']['home'],
                    'away_score' => $matchData['score']['fullTime']['away'],
                    'status' => $this->mapStatus($matchData['status']),
                    'minute' => $matchData['minute'] ?? null,
                ]);
                $this->line("  ✅ Match updated");
                $updated++;
            } else {
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
                $this->line("  ✅ Match created");
                $created++;
            }
        }
        
        $this->info("✅ Completed! Created: {$created}, Updated: {$updated}");
        return 0;
    }
    
    protected function findOrCreateTeam($teamData, $league)
    {
        $team = Team::where('name', $teamData['name'])->first();
        
        if (!$team) {
            // Create a proper short name from the team name
            $shortName = $teamData['shortName'] ?? substr($teamData['name'], 0, 10);
            if (strlen($shortName) > 10) {
                $shortName = substr($shortName, 0, 10);
            }
            
            $team = Team::create([
                'name' => $teamData['name'],
                'short_name' => $shortName,
                'slug' => Str::slug($teamData['name']),
                'league_id' => $league->id,
                'logo' => $teamData['crest'] ?? null,
                'city' => 'Unknown' // Placeholder since API doesn't provide city
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