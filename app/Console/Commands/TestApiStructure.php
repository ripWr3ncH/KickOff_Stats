<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;

class TestApiStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:test-structure {--date=2025-09-25}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Football Data API structure to see what detailed information is available';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $footballService = new FootballDataService();
        $date = $this->option('date');
        
        $this->info("Testing API structure for date: {$date}");
        
        // Test basic match data structure
        $matches = $footballService->getMatchesByDate($date);
        
        if (!$matches || !isset($matches['matches']) || empty($matches['matches'])) {
            $this->warn("No matches found for {$date}. Trying today's date...");
            $matches = $footballService->getMatchesByDate(today()->format('Y-m-d'));
        }
        
        if ($matches && isset($matches['matches'][0])) {
            $sampleMatch = $matches['matches'][0];
            
            $this->info('Available match fields:');
            foreach (array_keys($sampleMatch) as $field) {
                $this->line("  - {$field}");
            }
            
            $this->info("\nScore structure:");
            if (isset($sampleMatch['score'])) {
                foreach ($sampleMatch['score'] as $scoreType => $scoreData) {
                    $this->line("  - {$scoreType}: " . json_encode($scoreData));
                }
            }
            
            $this->info("\nMatch details:");
            $this->line("  - ID: " . ($sampleMatch['id'] ?? 'N/A'));
            $this->line("  - Status: " . ($sampleMatch['status'] ?? 'N/A'));
            $this->line("  - Venue: " . ($sampleMatch['venue'] ?? 'N/A'));
            $this->line("  - Attendance: " . ($sampleMatch['attendance'] ?? 'N/A'));
            
            if (isset($sampleMatch['referees'])) {
                $this->line("  - Referees: " . count($sampleMatch['referees']));
            }
            
            // Test individual match details API
            if (isset($sampleMatch['id'])) {
                $this->info("\nTesting individual match details API...");
                $matchDetails = $footballService->getMatchDetails($sampleMatch['id']);
                
                if ($matchDetails) {
                    $this->info("Individual match API provides:");
                    foreach (array_keys($matchDetails) as $field) {
                        $this->line("  - {$field}");
                    }
                    
                    if (isset($matchDetails['statistics'])) {
                        $this->info("\nStatistics available: YES");
                    } else {
                        $this->warn("Statistics available: NO");
                    }
                    
                    if (isset($matchDetails['events'])) {
                        $this->info("Events available: YES (" . count($matchDetails['events']) . " events)");
                    } else {
                        $this->warn("Events available: NO");
                    }
                    
                    if (isset($matchDetails['lineups'])) {
                        $this->info("Lineups available: YES");
                    } else {
                        $this->warn("Lineups available: NO");
                    }
                } else {
                    $this->error("Individual match details API failed!");
                }
            }
            
        } else {
            $this->error("No matches found to analyze API structure!");
        }
    }
}