<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;

class CheckMissingTeams extends Command
{
    protected $signature = 'matches:check-missing-teams';
    protected $description = 'Check for matches with missing team references';

    public function handle()
    {
        $this->info('Checking for matches with missing team references...');
        
        // Check matches with null team IDs
        $nullHomeTeams = FootballMatch::whereNull('home_team_id')->count();
        $nullAwayTeams = FootballMatch::whereNull('away_team_id')->count();
        
        $this->info("Matches with null home_team_id: {$nullHomeTeams}");
        $this->info("Matches with null away_team_id: {$nullAwayTeams}");
        
        // Check matches where team IDs exist but teams don't exist
        $orphanedMatches = FootballMatch::leftJoin('teams as home_teams', 'football_matches.home_team_id', '=', 'home_teams.id')
            ->leftJoin('teams as away_teams', 'football_matches.away_team_id', '=', 'away_teams.id')
            ->where(function($query) {
                $query->whereNull('home_teams.id')->whereNotNull('football_matches.home_team_id')
                      ->orWhere(function($q) {
                          $q->whereNull('away_teams.id')->whereNotNull('football_matches.away_team_id');
                      });
            })
            ->select('football_matches.id', 'football_matches.home_team_id', 'football_matches.away_team_id', 
                    'home_teams.name as home_name', 'away_teams.name as away_name')
            ->get();
            
        if ($orphanedMatches->count() > 0) {
            $this->warn("Found {$orphanedMatches->count()} matches with missing team records:");
            foreach ($orphanedMatches->take(10) as $match) {
                $this->line("Match ID {$match->id}: Home Team ID {$match->home_team_id} ({$match->home_name}), Away Team ID {$match->away_team_id} ({$match->away_name})");
            }
        } else {
            $this->info("No orphaned matches found!");
        }
        
        return 0;
    }
}