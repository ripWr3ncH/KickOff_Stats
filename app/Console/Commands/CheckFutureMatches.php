<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use Carbon\Carbon;

class CheckFutureMatches extends Command
{
    protected $signature = 'matches:check-future';
    protected $description = 'Check future matches and why they might not be showing';

    public function handle()
    {
        $this->info('=== FUTURE MATCHES ANALYSIS ===');
        $this->info('Current date/time: ' . now()->format('Y-m-d H:i:s'));
        $this->info('Today: ' . today()->format('Y-m-d'));
        $this->newLine();
        
        // Check matches after today
        $futureMatches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->where('match_date', '>', now())
            ->orderBy('match_date')
            ->get();
            
        $this->info("Total future matches: {$futureMatches->count()}");
        
        if ($futureMatches->count() > 0) {
            $this->newLine();
            $this->info('Next 10 upcoming matches:');
            
            $tableData = [];
            foreach($futureMatches->take(10) as $match) {
                $tableData[] = [
                    $match->match_date->format('Y-m-d H:i'),
                    $match->homeTeam->name ?? 'Unknown',
                    $match->awayTeam->name ?? 'Unknown',
                    $match->status,
                    $match->league->name ?? 'Unknown League'
                ];
            }
            
            $this->table(['Date', 'Home Team', 'Away Team', 'Status', 'League'], $tableData);
        } else {
            $this->error('❌ No future matches found!');
            $this->warn('This means you need to fetch upcoming matches from the API.');
        }
        
        // Check next 7 days
        $this->newLine();
        $this->info('=== NEXT 7 DAYS BREAKDOWN ===');
        
        $weekData = [];
        for ($i = 1; $i <= 7; $i++) {
            $date = today()->addDays($i);
            $count = FootballMatch::whereDate('match_date', $date)->count();
            $weekData[] = [
                $date->format('Y-m-d'),
                $date->format('l'),
                $count
            ];
        }
        
        $this->table(['Date', 'Day', 'Matches'], $weekData);
        
        // Check what data you have vs what you should have
        $this->newLine();
        $this->info('=== RECOMMENDATIONS ===');
        
        if ($futureMatches->count() === 0) {
            $this->error('❌ Problem: No future matches in database');
            $this->info('💡 Solution: Run "php artisan matches:fetch --days=14" to get upcoming matches');
        } elseif ($futureMatches->count() < 50) {
            $this->warn('⚠️  You have some future matches but might need more');
            $this->info('💡 Consider running "php artisan matches:fetch --days=30" for more coverage');
        } else {
            $this->info('✅ You have good future match coverage');
        }
        
        return 0;
    }
}