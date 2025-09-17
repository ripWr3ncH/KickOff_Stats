<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FootballMatch;
use App\Models\League;

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
        
        $matches = $query->orderBy('match_date')->get();
        $leagues = League::where('is_active', true)->get();
        
        return view('matches.index', compact('matches', 'leagues'));
    }

    public function show($id)
    {
        $match = FootballMatch::with([
            'homeTeam', 
            'awayTeam', 
            'league',
            'playerStats.player'
        ])->findOrFail($id);
        
        return view('matches.show', compact('match'));
    }

    public function live()
    {
        $liveMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'live')
            ->orderBy('match_date')
            ->get();
        
        return view('matches.live', compact('liveMatches'));
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
