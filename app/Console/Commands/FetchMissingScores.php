<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use App\Services\FootballDataService;
use Carbon\Carbon;

class FetchMissingScores extends Command
{
    protected $signature = 'matches:fetch-missing-scores {--limit=50 : Limit number of matches to process} {--dry-run : Show what would be updated}';
    protected $description = 'Fetch actual scores for finished matches that are missing score data';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    public function handle()
    {
        $limit = $this->option('limit');
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Finding finished matches without scores...');
        
        // Find finished matches that don't have scores
        $matchesWithoutScores = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'finished')
            ->where(function($q) {
                $q->whereNull('home_score')
                  ->orWhereNull('away_score');
            })
            ->whereNotNull('api_match_id')
            ->orderBy('match_date', 'desc')
            ->limit($limit)
            ->get();
            
        if ($matchesWithoutScores->count() === 0) {
            $this->info('No finished matches need score updates!');
            return 0;
        }
        
        $this->info("Found {$matchesWithoutScores->count()} matches to fetch scores for");
        
        if (!$isDryRun && !$this->confirm('Do you want to proceed with fetching scores?')) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $updated = 0;
        $errors = 0;
        $noData = 0;
        
        $progressBar = $this->output->createProgressBar($matchesWithoutScores->count());
        $progressBar->start();
        
        foreach ($matchesWithoutScores as $match) {
            try {
                if ($isDryRun) {
                    $this->line("\nWould fetch score for: {$match->homeTeam->name} vs {$match->awayTeam->name} (API ID: {$match->api_match_id})");
                } else {
                    $result = $this->fetchMatchScore($match);
                    
                    if ($result === 'updated') {
                        $updated++;
                    } elseif ($result === 'no_data') {
                        $noData++;
                    }
                }
                
                $progressBar->advance();
                
                // Rate limiting - pause between API calls
                if (!$isDryRun) {
                    usleep(100000); // 0.1 second pause
                }
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("\nError fetching score for match {$match->id}: " . $e->getMessage());
                $progressBar->advance();
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        if ($isDryRun) {
            $this->info("DRY RUN: Would attempt to fetch scores for {$matchesWithoutScores->count()} matches");
        } else {
            $this->info("Successfully updated {$updated} matches with scores");
            if ($noData > 0) {
                $this->warn("Found {$noData} matches with no score data available");
            }
            if ($errors > 0) {
                $this->error("Encountered {$errors} errors");
            }
        }
        
        return 0;
    }
    
    private function fetchMatchScore($match)
    {
        try {
            $this->line("\nFetching score for: {$match->homeTeam->name} vs {$match->awayTeam->name}");
            
            // Get match details from API
            $apiMatchData = $this->footballService->getMatchDetails($match->api_match_id);
            
            if (!$apiMatchData) {
                $this->warn("No API data returned for match {$match->id}");
                return 'no_data';
            }
            
            // Extract score data
            $homeScore = $apiMatchData['score']['fullTime']['home'] ?? null;
            $awayScore = $apiMatchData['score']['fullTime']['away'] ?? null;
            $halftimeHome = $apiMatchData['score']['halfTime']['home'] ?? null;
            $halftimeAway = $apiMatchData['score']['halfTime']['away'] ?? null;
            $status = $this->mapApiStatus($apiMatchData['status'] ?? 'FINISHED');
            
            if ($homeScore !== null && $awayScore !== null) {
                // Update match with scores
                $match->update([
                    'home_score' => $homeScore,
                    'away_score' => $awayScore,
                    'halftime_home_score' => $halftimeHome,
                    'halftime_away_score' => $halftimeAway,
                    'status' => $status,
                ]);
                
                $this->info("Updated: {$homeScore}-{$awayScore}");
                return 'updated';
            } else {
                $this->warn("No score data available in API response");
                return 'no_data';
            }
            
        } catch (\Exception $e) {
            $this->error("API Error: " . $e->getMessage());
            throw $e;
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