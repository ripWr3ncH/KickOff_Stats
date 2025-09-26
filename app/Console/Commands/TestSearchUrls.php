<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;

class TestSearchUrls extends Command
{
    protected $signature = 'matches:test-search {id=1}';
    protected $description = 'Test search URL generation for a match';

    public function handle()
    {
        $matchId = $this->argument('id');
        
        $match = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])->find($matchId);
        
        if (!$match) {
            $this->error("Match with ID {$matchId} not found");
            return;
        }
        
        $this->info("Testing search URLs for match: {$match->homeTeam->name} vs {$match->awayTeam->name}");
        $this->info("Date: {$match->match_date->format('Y-m-d H:i')}");
        $this->info("League: {$match->league->name}");
        
        // Generate search URLs (same logic as controller)
        $homeTeam = $match->homeTeam ? $match->homeTeam->name : 'Team';
        $awayTeam = $match->awayTeam ? $match->awayTeam->name : 'Team';
        $league = $match->league ? $match->league->name : '';
        $date = $match->match_date->format('Y-m-d');
        
        // Clean team names for better search results
        $homeTeamClean = preg_replace('/\b(FC|CF|United|City|Athletic|Club)\b/i', '', $homeTeam);
        $awayTeamClean = preg_replace('/\b(FC|CF|United|City|Athletic|Club)\b/i', '', $awayTeam);
        
        $homeTeamClean = trim($homeTeamClean);
        $awayTeamClean = trim($awayTeamClean);
        
        $baseQuery = trim("{$homeTeamClean} vs {$awayTeamClean} {$league} {$date}");
        
        $searchUrls = [
            'Google Search' => 'https://www.google.com/search?' . http_build_query([
                'q' => "{$baseQuery} match statistics highlights"
            ]),
            
            'ESPN Search' => 'https://www.google.com/search?' . http_build_query([
                'q' => "site:espn.com {$baseQuery} match report"
            ]),
            
            'BBC Search' => 'https://www.google.com/search?' . http_build_query([
                'q' => "site:bbc.com/sport {$baseQuery} report"
            ]),
            
            'Highlights Search' => 'https://www.google.com/search?' . http_build_query([
                'q' => "{$homeTeam} vs {$awayTeam} highlights video {$date}"
            ]),
            
            'Player Ratings' => 'https://www.google.com/search?' . http_build_query([
                'q' => "{$homeTeam} vs {$awayTeam} player ratings {$date}"
            ])
        ];
        
        $this->info("\nGenerated Search URLs:");
        foreach ($searchUrls as $name => $url) {
            $this->line("\n{$name}:");
            $this->comment($url);
        }
        
        return 0;
    }
}