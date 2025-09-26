<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;

class TestFootballApi extends Command
{
    protected $signature = 'football:test';
    protected $description = 'Test Football Data API connection and show sample data';

    public function handle()
    {
        $this->info('Testing Football-Data.org API...');
        $this->newLine();

        $service = new FootballDataService();

        // Test API connection
        $this->info('🔍 Testing API connection...');
        $liveMatches = $service->getLiveMatches();
        
        if ($liveMatches === null) {
            $this->error('❌ Failed to connect to Football-Data.org API');
            $this->info('Please check your API key in .env file');
            return;
        }

        $this->info('✅ API connection successful!');
        $this->newLine();

        // Show today's matches
        $this->info('📅 Today\'s matches:');
        $todayMatches = $service->getMatchesByDate(now()->format('Y-m-d'));
        
        if ($todayMatches && isset($todayMatches['matches'])) {
            $matches = collect($todayMatches['matches'])->take(5);
            
            if ($matches->isEmpty()) {
                $this->warn('No matches scheduled for today');
            } else {
                $this->table(
                    ['Time', 'Home Team', 'Score', 'Away Team', 'Status'],
                    $matches->map(function ($match) {
                        return [
                            \Carbon\Carbon::parse($match['utcDate'])->format('H:i'),
                            $match['homeTeam']['name'],
                            isset($match['score']['fullTime']['home']) 
                                ? $match['score']['fullTime']['home'] . '-' . $match['score']['fullTime']['away']
                                : 'vs',
                            $match['awayTeam']['name'],
                            $match['status']
                        ];
                    })
                );
            }
        }

        $this->newLine();

        // Show live matches
        $this->info('🔴 Live matches:');
        if ($liveMatches && isset($liveMatches['matches'])) {
            $live = collect($liveMatches['matches']);
            
            if ($live->isEmpty()) {
                $this->warn('No live matches at the moment');
            } else {
                $this->table(
                    ['Competition', 'Home Team', 'Score', 'Away Team', 'Minute'],
                    $live->map(function ($match) {
                        return [
                            $match['competition']['name'],
                            $match['homeTeam']['name'],
                            $match['score']['fullTime']['home'] . '-' . $match['score']['fullTime']['away'],
                            $match['awayTeam']['name'],
                            ($match['minute'] ?? 0) . '\''
                        ];
                    })
                );
            }
        }

        $this->newLine();

        // Test league standings
        $this->info('🏆 Testing league standings...');
        $leagueMapping = $service->getLeagueMapping();
        
        foreach ($leagueMapping as $slug => $apiId) {
            $standings = $service->getLeagueStandings($apiId);
            if ($standings && isset($standings['standings'][0]['table'])) {
                $this->info("✅ {$slug}: " . count($standings['standings'][0]['table']) . " teams");
            } else {
                $this->warn("⚠️  {$slug}: No standings data");
            }
        }

        $this->newLine();
        $this->info('🎯 API Test Complete!');
        $this->info('💡 Run "php artisan football:sync" to import all data');
        $this->info('🔄 Run "php artisan schedule:work" for automatic updates');
    }
}
