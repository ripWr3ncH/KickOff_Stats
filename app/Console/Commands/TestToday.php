<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestToday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-today';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test today matches count';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $todayMatches = \App\Models\FootballMatch::get()->filter(function($match) {
            return $match->isTodayLocal();
        });
        
        $oct5Matches = \App\Models\FootballMatch::whereDate('match_date', '2025-10-05')->count();
        $oct6Matches = \App\Models\FootballMatch::whereDate('match_date', '2025-10-06')->count();
        
        $this->info('Today (Bangladesh timezone): ' . $todayMatches->count() . ' matches');
        $this->info('Oct 5 (UTC date): ' . $oct5Matches . ' matches');
        $this->info('Oct 6 (UTC date): ' . $oct6Matches . ' matches');
        
        if ($todayMatches->count() > 0) {
            $this->info('Sample today match: ' . $todayMatches->first()->homeTeam->name . ' vs ' . $todayMatches->first()->awayTeam->name);
            $this->info('Match time: ' . $todayMatches->first()->getLocalMatchDate()->format('M j, g:i A'));
        }
    }
}
