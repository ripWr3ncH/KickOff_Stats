<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;

class AutoUpdateLiveScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scores:auto-update {--interval=120 : Update interval in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Continuously update live scores in the background';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $interval = (int) $this->option('interval');
        $this->info("Starting auto-update with {$interval}s interval...");
        $this->info('Press Ctrl+C to stop');

        while (true) {
            try {
                // First, check if there are live matches in database
                $liveCount = FootballMatch::where('status', 'live')->count();
                
                if ($liveCount > 0) {
                    $this->comment('[' . now()->format('H:i:s') . "] Updating {$liveCount} live matches...");
                    
                    // Run the sync command to update currently live matches
                    $this->call('matches:sync-today', ['--live' => true]);
                    
                    // After sync, check if any matches should be finished
                    // (i.e., they're marked live in DB but not live in API anymore)
                    $this->comment('[' . now()->format('H:i:s') . '] Checking for finished matches...');
                    
                    // Get current live matches from API
                    $footballService = app(\App\Services\FootballDataService::class);
                    $apiLiveData = $footballService->getLiveMatches();
                    
                    if ($apiLiveData && isset($apiLiveData['matches'])) {
                        $apiLiveCount = count($apiLiveData['matches']);
                        $dbLiveCount = FootballMatch::where('status', 'live')->count();
                        
                        // If DB has more live matches than API, some have finished
                        if ($dbLiveCount > $apiLiveCount) {
                            $this->warn('[' . now()->format('H:i:s') . "] DB shows {$dbLiveCount} live, API shows {$apiLiveCount}. Finishing old matches...");
                            
                            // Check each DB live match against API
                            $dbLiveMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])->where('status', 'live')->get();
                            
                            foreach ($dbLiveMatches as $dbMatch) {
                                $stillLive = false;
                                
                                // Check if this match is in API live matches
                                foreach ($apiLiveData['matches'] as $apiMatch) {
                                    $homeMatch = stripos($apiMatch['homeTeam']['name'], $dbMatch->homeTeam->name) !== false ||
                                                stripos($dbMatch->homeTeam->name, $apiMatch['homeTeam']['name']) !== false;
                                    $awayMatch = stripos($apiMatch['awayTeam']['name'], $dbMatch->awayTeam->name) !== false ||
                                                stripos($dbMatch->awayTeam->name, $apiMatch['awayTeam']['name']) !== false;
                                    
                                    if ($homeMatch && $awayMatch) {
                                        $stillLive = true;
                                        break;
                                    }
                                }
                                
                                // If not found in API, mark as finished
                                if (!$stillLive) {
                                    $dbMatch->update(['status' => 'finished', 'minute' => 90]);
                                    $this->info('[' . now()->format('H:i:s') . "] ✓ Finished: {$dbMatch->homeTeam->short_name} vs {$dbMatch->awayTeam->short_name}");
                                }
                            }
                        }
                    }
                    
                } else {
                    $this->info('[' . now()->format('H:i:s') . '] No live matches to update');
                }
                
            } catch (\Exception $e) {
                $this->error('[' . now()->format('H:i:s') . '] Update failed: ' . $e->getMessage());
            }
            
            $this->line('[' . now()->format('H:i:s') . "] Waiting {$interval}s for next update...");
            sleep($interval);
        }
    }
}
