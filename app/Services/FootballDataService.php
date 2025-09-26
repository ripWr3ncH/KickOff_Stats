<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FootballDataService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.football-data.org/v4';

    public function __construct()
    {
        $this->apiKey = config('services.football_data.api_key');
    }

    /**
     * Get API match ID from match data (for future enrichment)
     */
    public function getApiMatchId($matchData)
    {
        return $matchData['id'] ?? null;
    }

    /**
     * Get live matches
     */
    public function getLiveMatches()
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . '/matches', [
                'status' => 'LIVE'
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get matches for specific date or date range
     */
    public function getMatchesByDate($dateFrom, $dateTo = null)
    {
        try {
            $params = [
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo ?? $dateFrom
            ];
            
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . '/matches', $params);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get matches for a specific competition
     */
    public function getCompetitionMatches($competitionId, $season = null)
    {
        try {
            $params = [];
            if ($season) {
                $params['season'] = $season;
            }
            
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . "/competitions/{$competitionId}/matches", $params);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get league standings
     */
    public function getLeagueStandings($leagueId)
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . "/competitions/{$leagueId}/standings");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get top scorers for a competition
     */
    public function getTopScorers($leagueId)
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . "/competitions/{$leagueId}/scorers");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error (Top Scorers): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get detailed match information by match ID
     */
    public function getMatchDetails($matchId)
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . "/matches/{$matchId}");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error (Match Details): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get match statistics and events
     */
    public function getMatchStatistics($matchId)
    {
        try {
            // The match details endpoint usually includes statistics
            $matchData = $this->getMatchDetails($matchId);
            
            if ($matchData) {
                return [
                    'match' => $matchData,
                    'statistics' => $matchData['statistics'] ?? null,
                    'events' => $matchData['events'] ?? null,
                    'lineups' => $matchData['lineups'] ?? null,
                    'referees' => $matchData['referees'] ?? null
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error (Match Statistics): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get team information
     */
    public function getTeam($teamId)
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . "/teams/{$teamId}");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Map API league IDs to our database
     */
    public function getLeagueMapping()
    {
        return [
            2021 => 'premier-league', // Premier League
            2014 => 'la-liga',        // La Liga  
            2019 => 'serie-a'         // Serie A
        ];
    }

    /**
     * Enrich existing match with detailed API data
     */
    public function enrichMatchData($matchId, $apiMatchId = null)
    {
        try {
            // If we don't have the API match ID, we'll need to find it
            if (!$apiMatchId) {
                // This would require storing the API match ID in our database
                Log::warning("API match ID not provided for match {$matchId}");
                return null;
            }
            
            $matchDetails = $this->getMatchDetails($apiMatchId);
            
            if ($matchDetails) {
                return [
                    'venue' => $matchDetails['venue'] ?? null,
                    'attendance' => $matchDetails['attendance'] ?? null,
                    'referee' => $matchDetails['referees'][0]['name'] ?? null,
                    'events' => $matchDetails['events'] ?? [],
                    'statistics' => $matchDetails['statistics'] ?? null,
                    'lineups' => $matchDetails['lineups'] ?? null,
                    'raw_data' => $matchDetails
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("Error enriching match data for match {$matchId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get team players from API
     */
    public function getTeamPlayers($teamId)
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . "/teams/{$teamId}");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error (Team Players): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get player matches/statistics
     */
    public function getPlayerMatches($playerId, $season = null)
    {
        try {
            $season = $season ?? date('Y');
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . "/persons/{$playerId}/matches", [
                'season' => $season
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error (Player Matches): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync team players (placeholder for future implementation)
     */
    public function syncTeamPlayers($teamApiId)
    {
        $teamData = $this->getTeamPlayers($teamApiId);
        if ($teamData && isset($teamData['squad'])) {
            // TODO: Implement player sync logic
            // This would involve creating/updating Player records
            Log::info("Team players data retrieved for team ID: {$teamApiId}");
            return $teamData['squad'];
        }
        return [];
    }

    /**
     * Update player data (placeholder for future implementation)
     */
    public function updatePlayerData($playerApiId)
    {
        $playerData = $this->getPlayerMatches($playerApiId);
        if ($playerData) {
            // TODO: Implement player update logic
            Log::info("Player data retrieved for player ID: {$playerApiId}");
            return $playerData;
        }
        return null;
    }

    /**
     * Debug: Get sample match data structure
     */
    public function debugMatchStructure($date = null)
    {
        try {
            $date = $date ?? '2025-08-15';
            $matches = $this->getMatchesByDate($date);
            
            if ($matches && isset($matches['matches'][0])) {
                $sampleMatch = $matches['matches'][0];
                
                return [
                    'available_fields' => array_keys($sampleMatch),
                    'score_structure' => $sampleMatch['score'] ?? null,
                    'has_statistics' => isset($sampleMatch['statistics']),
                    'has_events' => isset($sampleMatch['events']),
                    'has_lineups' => isset($sampleMatch['lineups']),
                    'sample_match' => $sampleMatch
                ];
            }
            
            return ['error' => 'No matches found for debugging'];
        } catch (\Exception $e) {
            Log::error('Football Data API Debug Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get head-to-head statistics between two teams
     */
    public function getHeadToHead($team1Id, $team2Id, $limit = 10)
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => $this->apiKey
            ])->get($this->baseUrl . "/teams/{$team1Id}/matches", [
                'limit' => $limit
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Football Data API Error (Head to Head): ' . $e->getMessage());
            return null;
        }
    }
}
