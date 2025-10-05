<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;

class GenerateRandomScores extends Command
{
    protected $signature = 'matches:generate-scores {--dry-run : Show what would be updated}';
    protected $description = 'Generate realistic random scores for finished matches without scores';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Finding finished matches without scores...');
        
        // Find finished matches that don't have scores
        $matchesWithoutScores = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->where('status', 'finished')
            ->where(function($q) {
                $q->whereNull('home_score')->orWhereNull('away_score');
            })
            ->orderBy('match_date', 'desc')
            ->get();
            
        if ($matchesWithoutScores->count() === 0) {
            $this->info('No finished matches need score updates!');
            return 0;
        }
        
        $this->info("Found {$matchesWithoutScores->count()} matches to generate scores for");
        
        if (!$isDryRun) {
            $this->warn('This will generate realistic random scores for matches without actual data.');
            if (!$this->confirm('Do you want to proceed?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }
        
        $updated = 0;
        
        $progressBar = $this->output->createProgressBar($matchesWithoutScores->count());
        $progressBar->start();
        
        foreach ($matchesWithoutScores as $match) {
            $scores = $this->generateRealisticScore();
            
            if ($isDryRun) {
                $this->line("\nWould set: {$match->homeTeam->name} {$scores['home']}-{$scores['away']} {$match->awayTeam->name}");
            } else {
                $match->update([
                    'home_score' => $scores['home'],
                    'away_score' => $scores['away'],
                    'halftime_home_score' => $scores['ht_home'],
                    'halftime_away_score' => $scores['ht_away'],
                ]);
                $updated++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        if ($isDryRun) {
            $this->info("DRY RUN: Would generate scores for {$matchesWithoutScores->count()} matches");
        } else {
            $this->info("Generated scores for {$updated} matches successfully");
        }
        
        return 0;
    }
    
    private function generateRealisticScore()
    {
        // Common football score patterns with weights
        $scorePatterns = [
            // Very common scores (higher weight)
            [0, 0], [1, 0], [0, 1], [1, 1], [2, 0], [0, 2], [2, 1], [1, 2],
            [2, 0], [0, 2], [1, 0], [0, 1], [1, 1], // Repeat common ones
            
            // Moderately common scores
            [3, 0], [0, 3], [3, 1], [1, 3], [2, 2], [3, 2], [2, 3],
            
            // Less common but realistic scores
            [4, 0], [0, 4], [4, 1], [1, 4], [3, 3], [4, 2], [2, 4],
            
            // Rare but possible scores
            [5, 0], [0, 5], [5, 1], [1, 5], [4, 3], [3, 4], [5, 2], [2, 5]
        ];
        
        $selectedScore = $scorePatterns[array_rand($scorePatterns)];
        $homeScore = $selectedScore[0];
        $awayScore = $selectedScore[1];
        
        // Generate halftime scores (should be <= full time scores)
        $htHome = $homeScore > 0 ? rand(0, $homeScore) : 0;
        $htAway = $awayScore > 0 ? rand(0, $awayScore) : 0;
        
        // Make halftime scores more realistic (usually lower)
        if ($homeScore > 2) {
            $htHome = min($htHome, $homeScore - 1);
        }
        if ($awayScore > 2) {
            $htAway = min($htAway, $awayScore - 1);
        }
        
        return [
            'home' => $homeScore,
            'away' => $awayScore,
            'ht_home' => $htHome,
            'ht_away' => $htAway
        ];
    }
}