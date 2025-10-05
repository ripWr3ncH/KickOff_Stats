<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use App\Services\FootballDataService;
use Carbon\Carbon;

class UpdatePastMatchResults extends Command
{
    protected $signature = 'matches:update-past-results {--dry-run : Show what would be updated without making changes}';
    protected $description = 'Update past matches that are still scheduled to finished status with results';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Finding past matches that need to be updated...');
        
        // Find matches that occurred more than 2 hours ago but are still scheduled
        $pastMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('match_date', '<', now()->subHours(2))
            ->whereIn('status', ['scheduled', null])
            ->orderBy('match_date', 'desc')
            ->get();
            
        if ($pastMatches->count() === 0) {
            $this->info('No past matches need updating!');
            return 0;
        }
        
        $this->info("Found {$pastMatches->count()} matches to update");
        
        if (!$isDryRun && !$this->confirm('Do you want to proceed with updating these matches?')) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $updated = 0;
        $errors = 0;
        
        $progressBar = $this->output->createProgressBar($pastMatches->count());
        $progressBar->start();
        
        foreach ($pastMatches as $match) {
            try {
                if ($isDryRun) {
                    $this->line("\nWould update: {$match->homeTeam->name} vs {$match->awayTeam->name} ({$match->match_date})");
                } else {
                    // Try to fetch match results from API
                    $this->updateMatchFromAPI($match);
                    $updated++;
                }
                
                $progressBar->advance();
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("\nError updating match {$match->id}: " . $e->getMessage());
                $progressBar->advance();
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        if ($isDryRun) {
            $this->info("DRY RUN: Would update {$pastMatches->count()} matches");
        } else {
            $this->info("Updated {$updated} matches successfully");
            if ($errors > 0) {
                $this->error("Encountered {$errors} errors");
            }
        }
        
        return 0;
    }
    
    private function updateMatchFromAPI($match)
    {
        try {
            // Try to get match details from API
            if ($match->api_match_id) {
                $apiMatchData = $this->footballService->getMatchDetails($match->api_match_id);
                
                if ($apiMatchData && isset($apiMatchData['status'])) {
                    // Update match based on API response
                    $this->updateMatchFromApiData($match, $apiMatchData);
                    return;
                }
            }
            
            // If API call fails or no API ID, mark as finished with no score
            $this->markAsFinishedWithoutScore($match);
            
        } catch (\Exception $e) {
            // If all else fails, mark as finished
            $this->markAsFinishedWithoutScore($match);
        }
    }
    
    private function updateMatchFromApiData($match, $apiData)
    {
        $status = $this->mapApiStatus($apiData['status'] ?? '');
        
        $match->update([
            'status' => $status,
            'home_score' => $apiData['score']['fullTime']['home'] ?? null,
            'away_score' => $apiData['score']['fullTime']['away'] ?? null,
            'halftime_home_score' => $apiData['score']['halfTime']['home'] ?? null,
            'halftime_away_score' => $apiData['score']['halfTime']['away'] ?? null,
        ]);
    }
    
    private function markAsFinishedWithoutScore($match)
    {
        // For matches that are clearly in the past, mark as finished
        if ($match->match_date < now()->subDays(1)) {
            $match->update([
                'status' => 'finished',
                // Keep existing scores if any, otherwise leave as null
            ]);
        } else {
            // For recent matches, just mark as postponed or cancelled
            $match->update([
                'status' => 'postponed'
            ]);
        }
    }
    
    private function mapApiStatus($apiStatus)
    {
        return match(strtoupper($apiStatus)) {
            'FINISHED', 'FULL_TIME', 'FT' => 'finished',
            'IN_PLAY', 'LIVE', 'FIRST_HALF', 'SECOND_HALF', 'HALF_TIME' => 'live',
            'SCHEDULED', 'TIMED' => 'scheduled',
            'POSTPONED' => 'postponed',
            'CANCELLED' => 'cancelled',
            default => 'finished'
        };
    }
}