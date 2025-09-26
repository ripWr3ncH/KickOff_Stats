<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;

class CheckApiIds extends Command
{
    protected $signature = 'matches:check-api-ids';
    protected $description = 'Check which matches have API IDs';

    public function handle()
    {
        $withApiIds = FootballMatch::whereNotNull('api_match_id')->count();
        $total = FootballMatch::count();
        
        $this->info("Matches with API IDs: {$withApiIds} out of {$total}");
        
        if ($withApiIds > 0) {
            $this->info("\nSample matches with API IDs:");
            FootballMatch::whereNotNull('api_match_id')
                ->with(['homeTeam', 'awayTeam'])
                ->take(5)
                ->get()
                ->each(function($match) {
                    $this->line("Match {$match->id} - API ID: {$match->api_match_id} - {$match->homeTeam->name} vs {$match->awayTeam->name} - Status: {$match->status}");
                });
        }
        
        return 0;
    }
}