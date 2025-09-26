<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:cleanup-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate matches from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for duplicate matches...');
        
        // Find duplicates based on same teams, same date, and same league
        $duplicates = DB::select("
            SELECT 
                MIN(id) as keep_id,
                COUNT(*) as count,
                home_team_id,
                away_team_id,
                league_id,
                DATE(match_date) as match_day,
                TIME(match_date) as match_time
            FROM football_matches 
            GROUP BY home_team_id, away_team_id, league_id, DATE(match_date), TIME(match_date)
            HAVING COUNT(*) > 1
        ");

        if (empty($duplicates)) {
            $this->info('No duplicate matches found!');
            return;
        }

        $this->warn('Found ' . count($duplicates) . ' sets of duplicate matches.');
        
        $deletedCount = 0;
        
        foreach ($duplicates as $duplicate) {
            $this->comment("Processing duplicates for match on {$duplicate->match_day} {$duplicate->match_time}");
            
            // Get all matches for this duplicate set
            $matches = FootballMatch::where('home_team_id', $duplicate->home_team_id)
                ->where('away_team_id', $duplicate->away_team_id)
                ->where('league_id', $duplicate->league_id)
                ->whereDate('match_date', $duplicate->match_day)
                ->whereTime('match_date', $duplicate->match_time)
                ->orderBy('updated_at', 'desc') // Keep the most recently updated one
                ->get();
            
            // Keep the first one (most recently updated) and delete the rest
            $keepMatch = $matches->first();
            $duplicateMatches = $matches->skip(1);
            
            foreach ($duplicateMatches as $matchToDelete) {
                $this->line("  → Deleting duplicate match ID: {$matchToDelete->id}");
                $matchToDelete->delete();
                $deletedCount++;
            }
            
            $this->info("  → Kept match ID: {$keepMatch->id} (Status: {$keepMatch->status})");
        }
        
        $this->info("Cleanup complete! Deleted {$deletedCount} duplicate matches.");
    }
}