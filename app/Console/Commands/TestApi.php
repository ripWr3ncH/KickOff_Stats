<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use Illuminate\Support\Facades\Http;

class TestApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Football Data API connection and response';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Football Data API...');
        
        $apiKey = config('services.football_data.api_key');
        $this->info('API Key: ' . ($apiKey ? 'Configured (' . substr($apiKey, 0, 8) . '...)' : 'NOT CONFIGURED'));
        
        try {
            // Test direct API call
            $response = Http::withHeaders([
                'X-Auth-Token' => $apiKey
            ])->get('https://api.football-data.org/v4/matches', [
                'status' => 'LIVE'
            ]);

            $this->info('API Status Code: ' . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                $this->info('Matches found: ' . count($data['matches'] ?? []));
                
                if (isset($data['matches']) && count($data['matches']) > 0) {
                    foreach ($data['matches'] as $match) {
                        $this->info(sprintf(
                            'LIVE: %s vs %s (%s) [ID: %d]',
                            $match['homeTeam']['name'],
                            $match['awayTeam']['name'],
                            $match['competition']['name'],
                            $match['competition']['id']
                        ));
                    }
                }
                
                // Always check today's matches regardless of live matches
                $this->info("\n--- TODAY'S MATCHES ---");
                $this->info('Date being searched: ' . today()->format('Y-m-d'));
                
                $response2 = Http::withHeaders([
                    'X-Auth-Token' => $apiKey
                ])->get('https://api.football-data.org/v4/matches', [
                    'dateFrom' => today()->subDays(1)->format('Y-m-d'),
                    'dateTo' => today()->addDays(1)->format('Y-m-d')
                ]);
                
                if ($response2->successful()) {
                    $todayData = $response2->json();
                    $this->info('Today\'s matches: ' . count($todayData['matches'] ?? []));
                    
                    // Group by competition for better display
                    $matchesByCompetition = collect($todayData['matches'] ?? [])->groupBy('competition.id');
                    
                    foreach ($matchesByCompetition as $competitionId => $matches) {
                        $competitionName = $matches->first()['competition']['name'] ?? 'Unknown';
                        $this->comment("\n{$competitionName} (ID: {$competitionId}):");
                        
                        foreach ($matches->take(5) as $match) {
                            $homeScore = $match['score']['fullTime']['home'] ?? '-';
                            $awayScore = $match['score']['fullTime']['away'] ?? '-';
                            $this->info(sprintf(
                                '  %s vs %s [%s] %s-%s',
                                $match['homeTeam']['name'],
                                $match['awayTeam']['name'],
                                $match['status'],
                                $homeScore,
                                $awayScore
                            ));
                        }
                    }
                }
                
            } else {
                $this->error('API Error: ' . $response->body());
            }
            
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
        }
    }
}
