<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Services\FootballDataService;
use Illuminate\Http\Request;

class LiveScoreController extends Controller
{
    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        $this->footballService = $footballService;
    }

    /**
     * Get live scores
     */
    public function index()
    {
        $liveMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'live')
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'home_team' => $match->homeTeam->short_name,
                    'away_team' => $match->awayTeam->short_name,
                    'home_team_logo' => $match->homeTeam->logo,
                    'away_team_logo' => $match->awayTeam->logo,
                    'home_score' => $match->home_score,
                    'away_score' => $match->away_score,
                    'minute' => $match->minute,
                    'status' => $match->status,
                    'league' => $match->league->name
                ];
            });

        return response()->json($liveMatches);
    }

    /**
     * Update live scores from API
     */
    public function update()
    {
        $data = $this->footballService->getLiveMatches();
        
        if (!$data) {
            return response()->json(['error' => 'Failed to fetch live data'], 500);
        }

        $updated = [];
        $leagueMapping = array_flip($this->footballService->getLeagueMapping());

        foreach ($data['matches'] as $matchData) {
            $competitionId = $matchData['competition']['id'];
            
            if (!isset($leagueMapping[$competitionId])) {
                continue;
            }

            // Find match in our database
            $match = FootballMatch::whereHas('league', function($q) use ($leagueMapping, $competitionId) {
                $q->where('slug', $leagueMapping[$competitionId]);
            })
            ->whereHas('homeTeam', function($q) use ($matchData) {
                $q->where('name', $matchData['homeTeam']['name']);
            })
            ->whereHas('awayTeam', function($q) use ($matchData) {
                $q->where('name', $matchData['awayTeam']['name']);
            })
            ->first();

            if ($match) {
                $match->update([
                    'home_score' => $matchData['score']['fullTime']['home'],
                    'away_score' => $matchData['score']['fullTime']['away'],
                    'minute' => $matchData['minute'],
                    'status' => $this->mapStatus($matchData['status'])
                ]);

                $updated[] = [
                    'id' => $match->id,
                    'home_score' => $match->home_score,
                    'away_score' => $match->away_score,
                    'minute' => $match->minute,
                    'status' => $match->status
                ];
            }
        }

        return response()->json([
            'updated' => count($updated),
            'matches' => $updated
        ]);
    }

    protected function mapStatus($apiStatus)
    {
        $statusMap = [
            'SCHEDULED' => 'scheduled',
            'TIMED' => 'scheduled',
            'IN_PLAY' => 'live',
            'PAUSED' => 'live',
            'FINISHED' => 'finished',
            'POSTPONED' => 'postponed',
            'SUSPENDED' => 'postponed',
            'CANCELLED' => 'postponed'
        ];

        return $statusMap[$apiStatus] ?? 'scheduled';
    }
}
