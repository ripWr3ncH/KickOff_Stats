<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\FootballMatch;
use Carbon\Carbon;

class TestApiStructureNew extends Command
{
    protected $signature = 'api:test-new {--from-db : Use API ID from database}';
    protected $description = 'Test Football Data API structure using existing match IDs';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    public function handle()
    {
        $this->info('Testing Football Data API with existing match IDs...');
        
        // Get a match with API ID from database
        $match = FootballMatch::whereNotNull('api_match_id')
            ->with(['homeTeam', 'awayTeam'])
            ->first();
            
        if (!$match) {
            $this->error("No matches with API IDs found in database");
            return;
        }

        $this->info("Testing with match: {$match->homeTeam->name} vs {$match->awayTeam->name}");
        $this->info("API Match ID: {$match->api_match_id}");
        
        // Test match details endpoint
        $this->info("\n=== TESTING MATCH DETAILS ENDPOINT ===");
        $details = $this->footballService->getMatchDetails($match->api_match_id);
        
        if ($details) {
            $this->info("✓ Match details retrieved successfully!");
            $this->analyzeMatchData($details);
        } else {
            $this->warn("✗ No detailed match data available");
        }
        
        // Test statistics endpoint
        $this->info("\n=== TESTING MATCH STATISTICS ENDPOINT ===");
        $stats = $this->footballService->getMatchStatistics($match->api_match_id);
        
        if ($stats) {
            $this->info("✓ Match statistics retrieved successfully!");
            $this->analyzeStatistics($stats);
        } else {
            $this->warn("✗ No match statistics available");
        }
        
        return 0;
    }
    
    private function analyzeMatchData($data)
    {
        $this->info("Available match data fields:");
        
        // Check for key fields we might want
        $keyFields = [
            'id', 'utcDate', 'status', 'minute', 'venue', 'attendance',
            'referee', 'score', 'homeTeam', 'awayTeam', 'competition',
            'season', 'matchday', 'stage', 'group', 'lastUpdated',
            'odds', 'referees', 'head2head'
        ];
        
        foreach ($keyFields as $field) {
            if (isset($data[$field])) {
                if (is_array($data[$field]) || is_object($data[$field])) {
                    $this->line("  ✓ {$field}: [complex data]");
                    if ($field === 'score') {
                        $this->displayScore($data[$field]);
                    } elseif ($field === 'referee' || $field === 'referees') {
                        $this->displayReferees($data[$field]);
                    } elseif ($field === 'venue') {
                        $this->displayVenue($data[$field]);
                    }
                } else {
                    $this->line("  ✓ {$field}: " . $data[$field]);
                }
            } else {
                $this->comment("  ✗ {$field}: not available");
            }
        }
    }
    
    private function analyzeStatistics($data)
    {
        $this->info("Statistics data structure:");
        if (isset($data['statistics'])) {
            $this->info("  ✓ Statistics array found");
            // This would show us what match stats are available
        } else {
            $this->warn("  ✗ No statistics in response");
        }
    }
    
    private function displayScore($score)
    {
        $this->line("    Score details:");
        if (isset($score['fullTime'])) {
            $this->line("      Full Time: {$score['fullTime']['home']} - {$score['fullTime']['away']}");
        }
        if (isset($score['halfTime'])) {
            $this->line("      Half Time: {$score['halfTime']['home']} - {$score['halfTime']['away']}");
        }
    }
    
    private function displayReferees($referees)
    {
        if (is_array($referees)) {
            foreach ($referees as $ref) {
                if (isset($ref['name'])) {
                    $type = $ref['type'] ?? 'unknown';
                    $this->line("    Referee: {$ref['name']} ({$type})");
                }
            }
        } else {
            $this->line("    Referee: {$referees}");
        }
    }
    
    private function displayVenue($venue)
    {
        if (isset($venue['name'])) {
            $this->line("    Venue: {$venue['name']}");
        }
    }
}