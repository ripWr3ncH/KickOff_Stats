<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class RemoveSeedTeams extends Command
{
    protected $signature = 'teams:remove-seed-data';
    protected $description = 'Remove sample seed teams and keep only API teams';

    // Sample team names from the seeder
    private $seedTeamNames = [
        'Manchester City', 'Arsenal', 'Liverpool', 'Manchester United', 'Chelsea', 'Tottenham',
        'Real Madrid', 'Barcelona', 'Atletico Madrid', 'Sevilla', 'Real Betis', 'Valencia',
        'Juventus', 'Inter Milan', 'AC Milan', 'AS Roma', 'Lazio', 'Napoli'
    ];

    public function handle()
    {
        $this->info('Checking for seed teams vs API teams...');
        
        // Get all teams
        $allTeams = Team::with('league')->get();
        $this->info("Total teams in database: " . $allTeams->count());
        
        // Show all teams first
        $this->warn("\nAll current teams:");
        foreach ($allTeams as $team) {
            $this->line("{$team->id}. {$team->name} (League: {$team->league->name}, Slug: {$team->slug})");
        }
        
        // Find teams that match seed names
        $seedTeams = Team::whereIn('name', $this->seedTeamNames)->get();
        
        if ($seedTeams->isEmpty()) {
            $this->info("\nNo seed teams found! All teams are from API.");
            return 0;
        }
        
        $this->warn("\nFound {$seedTeams->count()} potential seed teams:");
        foreach ($seedTeams as $team) {
            $this->line("  - {$team->id}. {$team->name} (Created: {$team->created_at})");
        }
        
        if (!$this->confirm("\nDo you want to remove these seed teams?", false)) {
            $this->info('Cancelled. No teams were removed.');
            return 0;
        }
        
        // Check if any of these teams have matches or are favorited
        foreach ($seedTeams as $team) {
            $matchCount = DB::table('football_matches')
                ->where('home_team_id', $team->id)
                ->orWhere('away_team_id', $team->id)
                ->count();
            
            $favoriteCount = DB::table('user_favorite_teams')
                ->where('team_id', $team->id)
                ->count();
            
            if ($matchCount > 0 || $favoriteCount > 0) {
                $this->warn("  ⚠ {$team->name} has {$matchCount} matches and {$favoriteCount} favorites - KEEPING");
            } else {
                $team->delete();
                $this->info("  ✓ Deleted: {$team->name}");
            }
        }
        
        $remainingCount = Team::count();
        $this->info("\nDone! Remaining teams: {$remainingCount}");
        
        return 0;
    }
}
