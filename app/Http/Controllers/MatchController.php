<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FootballMatch;
use App\Models\League;
use App\Services\FootballDataService;
use Illuminate\Support\Facades\Log;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $query = FootballMatch::with(['homeTeam', 'awayTeam', 'league']);
        
        // Filter by league if specified
        if ($request->has('league')) {
            $query->whereHas('league', function($q) use ($request) {
                $q->where('slug', $request->league);
            });
        }
        
        // Filter by date if specified
        if ($request->has('date')) {
            $query->whereDate('match_date', $request->date);
        } else {
            // Default to today's matches
            $query->whereDate('match_date', today());
        }
        
        // Remove potential duplicates by grouping by unique match identifiers
        // and selecting the most recent status update for each match
        $matches = $query->orderBy('match_date')
                        ->orderBy('updated_at', 'desc')
                        ->get()
                        ->unique(function ($match) {
                            return $match->home_team_id . '-' . $match->away_team_id . '-' . $match->match_date->format('Y-m-d H:i');
                        });
        
        $leagues = League::where('is_active', true)->get();
        
        // Get date boundaries for navigation
        $earliestMatch = FootballMatch::orderBy('match_date')->first();
        $latestMatch = FootballMatch::orderBy('match_date', 'desc')->first();
        
        return view('matches.index', compact('matches', 'leagues', 'earliestMatch', 'latestMatch'));
    }

    public function show($id)
    {
        // Find the match with relationships
        $match = FootballMatch::with(['league', 'homeTeam', 'awayTeam'])->findOrFail($id);
        
        // Debug: Check if relationships loaded properly
        if (!$match->homeTeam) {
            Log::warning("Match {$match->id} missing home team (ID: {$match->home_team_id})");
            $match->setRelation('homeTeam', (object)[
                'id' => null,
                'name' => 'Home Team (Missing)',
                'short_name' => 'HOME',
                'logo' => null
            ]);
        }
        if (!$match->awayTeam) {
            Log::warning("Match {$match->id} missing away team (ID: {$match->away_team_id})");
            $match->setRelation('awayTeam', (object)[
                'id' => null,
                'name' => 'Away Team (Missing)',
                'short_name' => 'AWAY', 
                'logo' => null
            ]);
        }
        
        // Get detailed match data from API if available
        $detailedMatch = null;
        if ($match->api_match_id) {
            $footballService = app(FootballDataService::class);
            $detailedMatch = $footballService->getMatchDetails($match->api_match_id);
        }
        
        // Get related matches (same teams)
        $relatedMatches = FootballMatch::where(function($query) use ($match) {
            $query->where('home_team_id', $match->home_team_id)
                  ->where('away_team_id', $match->away_team_id);
        })
        ->orWhere(function($query) use ($match) {
            $query->where('home_team_id', $match->away_team_id)
                  ->where('away_team_id', $match->home_team_id);
        })
        ->where('id', '!=', $match->id)
        ->orderBy('match_date', 'desc')
        ->limit(5)
        ->get();
        
        // Generate search URLs for external match statistics
        $searchUrls = $this->generateSearchUrls($match);
        
        return view('matches.show', compact('match', 'relatedMatches', 'detailedMatch') + $searchUrls);
    }

    public function live()
    {
        $liveMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'live')
            ->orderBy('match_date')
            ->get();
        
        // Get matches starting in the next 2 hours
        $upcomingMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'scheduled')
            ->whereBetween('match_date', [now(), now()->addHours(2)])
            ->orderBy('match_date')
            ->limit(6)
            ->get();
        
        return view('matches.live', compact('liveMatches', 'leagues'));
    }
    
    /**
     * Generate search URLs for external match statistics
     */
    private function generateSearchUrls($match)
    {
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
        
        return [
            'googleSearchUrl' => 'https://www.google.com/search?' . http_build_query([
                'q' => "{$baseQuery} match statistics highlights"
            ]),
            
            'espnSearchUrl' => 'https://www.google.com/search?' . http_build_query([
                'q' => "site:espn.com {$baseQuery} match report"
            ]),
            
            'bbcSearchUrl' => 'https://www.google.com/search?' . http_build_query([
                'q' => "site:bbc.com/sport {$baseQuery} report"
            ]),
            
            'highlightsSearchUrl' => 'https://www.google.com/search?' . http_build_query([
                'q' => "{$homeTeam} vs {$awayTeam} highlights video {$date}"
            ]),
            
            'playerRatingsUrl' => 'https://www.google.com/search?' . http_build_query([
                'q' => "{$homeTeam} vs {$awayTeam} player ratings {$date}"
            ])
        ];
    }

    public function liveScores()
    {
        $liveMatches = FootballMatch::where('status', 'live')
            ->with(['homeTeam', 'awayTeam'])
            ->get();
        
        return response()->json($liveMatches);
    }

    public function events($id)
    {
        $match = FootballMatch::findOrFail($id);
        return response()->json($match->events ?? []);
    }
}
