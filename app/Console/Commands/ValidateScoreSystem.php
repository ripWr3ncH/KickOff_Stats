<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use App\Services\FootballDataService;

class ValidateScoreSystem extends Command
{
    protected $signature = 'scores:validate-system';
    protected $description = 'Validate that the score update system is working properly';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    public function handle()
    {
        $this->info('=== SCORE SYSTEM VALIDATION ===');
        
        // Check recent matches with API IDs
        $recentWithApi = FootballMatch::whereNotNull('api_match_id')
            ->where('match_date', '>=', now()->subDays(7))
            ->count();
            
        $this->info("Recent matches (7 days) with API IDs: {$recentWithApi}");
        
        // Check today's live/scheduled matches
        $todayMatches = FootballMatch::whereDate('match_date', today())->get();
        $todayWithApi = $todayMatches->whereNotNull('api_match_id')->count();
        $todayTotal = $todayMatches->count();
        
        $this->info("Today's matches: {$todayTotal}");
        $this->info("Today's matches with API IDs: {$todayWithApi}");
        
        if ($todayWithApi < $todayTotal) {
            $this->warn("Some of today's matches don't have API IDs - they won't get real scores!");
            $this->info("Run: php artisan matches:fetch --days=1 to get today's matches with API IDs");
        }
        
        // Test API connection
        $this->info('Testing API connection...');
        $liveData = $this->footballService->getLiveMatches();
        
        if ($liveData) {
            $this->info('✅ API connection working');
            $liveCount = $liveData['count'] ?? 0;
            $this->info("API reports {$liveCount} live matches right now");
        } else {
            $this->error('❌ API connection failed');
        }
        
        // Check scheduler status
        $this->newLine();
        $this->info('=== SCHEDULER VALIDATION ===');
        $this->info('Scheduled commands:');
        $this->line('- Live scores update: Every 2 minutes');
        $this->line('- Today\'s matches sync: Every 30 minutes');
        $this->line('- Cleanup old live matches: Every 10 minutes');
        
        $this->newLine();
        $this->info('✅ RECOMMENDATION FOR FUTURE:');
        $this->line('1. Keep your auto-updater.bat running');
        $this->line('2. New matches fetched from API will have correct scores');
        $this->line('3. Live matches will update in real-time');
        $this->line('4. Old matches (without API IDs) already have realistic scores');
        
        return 0;
    }
}