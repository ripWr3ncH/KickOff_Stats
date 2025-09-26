<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;

class FinishLiveMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:finish-live';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finish all live matches (set status to finished)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $liveMatches = FootballMatch::where('status', 'live')->get();
        
        if ($liveMatches->count() === 0) {
            $this->info('No live matches found.');
            return;
        }
        
        $count = 0;
        foreach ($liveMatches as $match) {
            $match->update([
                'status' => 'finished',
                'minute' => 90
            ]);
            $count++;
        }
        
        $this->info("Finished {$count} live matches.");
    }
}
