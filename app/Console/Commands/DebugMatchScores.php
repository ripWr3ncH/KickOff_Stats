<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;

class DebugMatchScores extends Command
{
    protected $signature = 'matches:debug-scores';
    protected $description = 'Debug match scores and API IDs';

    public function handle()
    {
        $this->info('=== DEBUGGING MATCH SCORES ===');
        
        // Check finished matches without scores
        $finishedWithoutScores = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->where('status', 'finished')
            ->where(function($q) {
                $q->whereNull('home_score')->orWhereNull('away_score');
            })
            ->limit(10)
            ->get();
            
        $this->info("Finished matches without scores: {$finishedWithoutScores->count()}");
        
        if ($finishedWithoutScores->count() > 0) {
            $tableData = [];
            foreach($finishedWithoutScores as $match) {
                $tableData[] = [
                    $match->id,
                    $match->homeTeam->name ?? 'Unknown',
                    $match->awayTeam->name ?? 'Unknown',
                    $match->home_score ?? 'NULL',
                    $match->away_score ?? 'NULL',
                    $match->api_match_id ?? 'NULL',
                    $match->match_date->format('Y-m-d H:i')
                ];
            }
            
            $this->table(['ID', 'Home', 'Away', 'Home Score', 'Away Score', 'API ID', 'Date'], $tableData);
        }
        
        // Check matches with API IDs
        $this->newLine();
        $withApiIds = FootballMatch::whereNotNull('api_match_id')->count();
        $withoutApiIds = FootballMatch::whereNull('api_match_id')->count();
        
        $this->info("Matches with API IDs: {$withApiIds}");
        $this->info("Matches without API IDs: {$withoutApiIds}");
        
        return 0;
    }
}