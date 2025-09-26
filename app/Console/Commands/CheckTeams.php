<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;
use App\Models\FootballMatch;

class CheckTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teams:check {--search= : Search for specific team}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check teams in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $search = $this->option('search');
        
        if ($search) {
            $teams = Team::where('name', 'like', "%{$search}%")
                        ->orWhere('short_name', 'like', "%{$search}%")
                        ->get();
            
            $this->info("Teams matching '{$search}':");
            foreach ($teams as $team) {
                $this->line("- {$team->name} ({$team->short_name})");
            }
        } else {
            $this->info('Checking for Premier League teams...');
            
            $plTeams = ['Manchester United', 'Chelsea', 'Arsenal', 'Liverpool', 'Manchester City'];
            
            foreach ($plTeams as $teamName) {
                $team = Team::where('name', 'like', "%{$teamName}%")->first();
                if ($team) {
                    $this->line("✓ Found: {$team->name} ({$team->short_name})");
                    
                    // Check for today's matches
                    $todayMatches = FootballMatch::where(function($q) use ($team) {
                        $q->where('home_team_id', $team->id)
                          ->orWhere('away_team_id', $team->id);
                    })
                    ->whereDate('match_date', today())
                    ->with(['homeTeam', 'awayTeam'])
                    ->get();
                    
                    foreach ($todayMatches as $match) {
                        $this->comment("  Today: {$match->homeTeam->short_name} vs {$match->awayTeam->short_name} [{$match->status}]");
                    }
                } else {
                    $this->error("✗ Not found: {$teamName}");
                }
            }
        }
    }
}
