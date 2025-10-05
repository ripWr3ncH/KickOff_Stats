<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use Carbon\Carbon;

class CheckMatchStatus extends Command
{
    protected $signature = 'matches:check-status';
    protected $description = 'Check and display match status analysis';

    public function handle()
    {
        $this->info('=== MATCH STATUS ANALYSIS ===');
        
        // Overall counts
        $total = FootballMatch::count();
        $live = FootballMatch::where('status', 'live')->count();
        $finished = FootballMatch::where('status', 'finished')->count();
        $scheduled = FootballMatch::where('status', 'scheduled')->count();
        $noStatus = FootballMatch::whereNull('status')->count();
        
        $this->table(['Status', 'Count'], [
            ['Total', $total],
            ['Live', $live],
            ['Finished', $finished],
            ['Scheduled', $scheduled],
            ['No Status', $noStatus],
        ]);
        
        $this->newLine();
        $this->warn('=== PAST MATCHES WITHOUT RESULTS ===');
        $this->info('These matches occurred more than 2 hours ago but still show as scheduled or without scores:');
        
        // Find past matches that should be finished
        $pastMatches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->where('match_date', '<', now()->subHours(2))
            ->where(function($q) {
                $q->whereNotIn('status', ['finished'])
                  ->orWhere(function($q2) {
                      $q2->whereNull('home_score')->whereNull('away_score');
                  });
            })
            ->orderBy('match_date', 'desc')
            ->limit(15)
            ->get();
            
        if ($pastMatches->count() > 0) {
            $tableData = [];
            foreach($pastMatches as $match) {
                $tableData[] = [
                    $match->match_date->format('Y-m-d H:i'),
                    $match->homeTeam->name ?? 'Unknown',
                    $match->awayTeam->name ?? 'Unknown',
                    $match->status ?? 'NULL',
                    ($match->home_score ?? 'N') . '-' . ($match->away_score ?? 'N'),
                    $match->id
                ];
            }
            
            $this->table(['Date', 'Home Team', 'Away Team', 'Status', 'Score', 'ID'], $tableData);
            
            $this->newLine();
            $this->error("Found {$pastMatches->count()} matches that need to be updated!");
            $this->info('These matches should be marked as finished with final scores.');
        } else {
            $this->info('No problematic matches found!');
        }
        
        return 0;
    }
}