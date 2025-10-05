<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use Carbon\Carbon;

class CheckTimezone extends Command
{
    protected $signature = 'app:check-timezone';
    protected $description = 'Check timezone settings and the Atletico vs Celta Vigo match time';

    public function handle()
    {
        $this->info('=== TIMEZONE ANALYSIS ===');
        
        // Current timezone settings
        $this->info('PHP Default Timezone: ' . date_default_timezone_get());
        $this->info('Laravel App Timezone: ' . config('app.timezone'));
        $this->info('Current Server Time: ' . now()->format('Y-m-d H:i:s T'));
        $this->info('Bangladesh Time (Asia/Dhaka): ' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s T'));
        $this->info('UTC Time: ' . now()->utc()->format('Y-m-d H:i:s T'));
        
        $this->newLine();
        $this->info('=== ATLETICO VS CELTA VIGO MATCH ===');
        
        // Find the specific match (Celta vs Atletico today)
        $match = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->whereHas('homeTeam', function($q) {
                $q->where('name', 'like', '%Celta%');
            })
            ->whereHas('awayTeam', function($q) {
                $q->where('name', 'like', '%Atlético%')->orWhere('name', 'like', '%Atletico%');
            })
            ->where('match_date', '>=', today())
            ->orderBy('match_date')
            ->first();
            
        if ($match) {
            $this->info("Found match: {$match->homeTeam->name} vs {$match->awayTeam->name}");
            $this->info('Database time (as stored): ' . $match->match_date->format('Y-m-d H:i:s T'));
            $this->info('Database time in UTC: ' . $match->match_date->utc()->format('Y-m-d H:i:s T'));
            $this->info('Database time in Bangladesh: ' . $match->match_date->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s T'));
            
            // Check if it's showing as today
            $isToday = $match->match_date->isToday();
            $isTomorrow = $match->match_date->isTomorrow();
            
            $this->info('Is today according to server: ' . ($isToday ? 'YES' : 'NO'));
            $this->info('Is tomorrow according to server: ' . ($isTomorrow ? 'YES' : 'NO'));
            
            // Bangladesh perspective
            $bangladeshTime = $match->match_date->setTimezone('Asia/Dhaka');
            $bangladeshToday = now()->setTimezone('Asia/Dhaka')->startOfDay();
            $bangladeshTomorrow = $bangladeshToday->copy()->addDay();
            
            $this->newLine();
            $this->info('=== BANGLADESH PERSPECTIVE ===');
            $this->info('Match time in Bangladesh: ' . $bangladeshTime->format('Y-m-d H:i:s T'));
            $this->info('Bangladesh today: ' . $bangladeshToday->format('Y-m-d'));
            $this->info('Bangladesh tomorrow: ' . $bangladeshTomorrow->format('Y-m-d'));
            
            $this->info('Is today in Bangladesh: ' . ($match->isTodayLocal() ? 'YES' : 'NO'));
            $this->info('Is tomorrow in Bangladesh: ' . ($match->isTomorrowLocal() ? 'YES' : 'NO'));
            
            // Let's also test what the view would show
            $this->newLine();
            $this->info('=== VIEW DISPLAY ===');
            $this->info('Local match time: ' . $match->getLocalMatchDate()->format('Y-m-d H:i:s T'));
            $this->info('Would show as today in views: ' . ($match->isTodayLocal() ? 'YES' : 'NO'));
            $this->info('Would show as tomorrow in views: ' . ($match->isTomorrowLocal() ? 'YES' : 'NO'));
            
        } else {
            $this->warn('Atletico vs Celta Vigo match not found');
            
            // Show some recent matches for reference
            $this->info('Recent upcoming matches:');
            $upcomingMatches = FootballMatch::with(['homeTeam', 'awayTeam'])
                ->where('match_date', '>', now())
                ->orderBy('match_date')
                ->limit(5)
                ->get();
                
            foreach($upcomingMatches as $upcoming) {
                $this->line($upcoming->match_date->format('Y-m-d H:i T') . ' - ' . $upcoming->homeTeam->name . ' vs ' . $upcoming->awayTeam->name);
            }
        }
        
        return 0;
    }
}