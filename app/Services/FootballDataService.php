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
}
