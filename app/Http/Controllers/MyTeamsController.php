<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\League;
use App\Models\FootballMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyTeamsController extends Controller
{
    /**
     * Display user's favorite teams and their matches.
     */
    public function index()
    {
        $user = Auth::user();
        $favoriteTeams = $user->favoriteTeams()->with('league')->get();
        
        // Get recent and upcoming matches for favorite teams
        $matches = collect();
        
        foreach ($favoriteTeams as $team) {
            $teamMatches = FootballMatch::where(function ($query) use ($team) {
                $query->where('home_team_id', $team->id)
                      ->orWhere('away_team_id', $team->id);
            })
            ->with(['homeTeam', 'awayTeam', 'league'])
            ->orderBy('match_date', 'desc')
            ->limit(10)
            ->get();
            
            $matches = $matches->merge($teamMatches);
        }
        
        // Sort matches by date
        $matches = $matches->sortByDesc('match_date')->take(20);
        
        return view('my-teams.index', compact('favoriteTeams', 'matches'));
    }

    /**
     * Show team selection page.
     */
    public function select()
    {
        $user = Auth::user();
        $leagues = League::with(['teams' => function ($query) use ($user) {
            $query->withCount(['favoritedByUsers' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }]);
        }])->get();
        
        return view('my-teams.select', compact('leagues'));
    }

    /**
     * Add team to user's favorites.
     */
    public function addFavorite(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id'
        ]);

        $user = Auth::user();
        $team = Team::findOrFail($request->team_id);
        
        if (!$user->hasFavoritedTeam($team->id)) {
            $user->favoriteTeams()->attach($team->id);
            return response()->json([
                'success' => true,
                'message' => "Added {$team->name} to your favorites!"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Team is already in your favorites!'
        ]);
    }

    /**
     * Remove team from user's favorites.
     */
    public function removeFavorite(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id'
        ]);

        $user = Auth::user();
        $team = Team::findOrFail($request->team_id);
        
        if ($user->hasFavoritedTeam($team->id)) {
            $user->favoriteTeams()->detach($team->id);
            return response()->json([
                'success' => true,
                'message' => "Removed {$team->name} from your favorites!"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Team is not in your favorites!'
        ]);
    }

    /**
     * Get matches for favorite teams with Bangladesh timezone.
     */
    private function getMatchesForTeams($teams)
    {
        if ($teams->isEmpty()) {
            return collect();
        }
        
        $teamIds = $teams->pluck('id');
        
        return FootballMatch::where(function($query) use ($teamIds) {
                $query->whereIn('home_team_id', $teamIds)
                      ->orWhereIn('away_team_id', $teamIds);
            })
            ->with(['homeTeam', 'awayTeam', 'league'])
            ->orderBy('match_date', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($match) {
                // Convert to Bangladesh timezone
                $match->bd_date = Carbon::parse($match->match_date)->setTimezone('Asia/Dhaka');
                return $match;
            });
    }
}