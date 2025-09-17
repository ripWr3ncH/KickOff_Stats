<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\League;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = Team::with('league');
        
        // Filter by league if specified
        if ($request->has('league')) {
            $query->whereHas('league', function($q) use ($request) {
                $q->where('slug', $request->league);
            });
        }
        
        $teams = $query->orderBy('name')->get();
        $leagues = League::where('is_active', true)->get();
        
        return view('teams.index', compact('teams', 'leagues'));
    }

    public function show($slug)
    {
        $team = Team::with([
            'league',
            'players' => function($query) {
                $query->where('is_active', true)->orderBy('jersey_number');
            }
        ])->where('slug', $slug)->firstOrFail();
        
        // Get recent matches
        $recentMatches = collect()
            ->merge($team->homeMatches()->with(['awayTeam', 'league'])->where('status', 'finished')->orderBy('match_date', 'desc')->limit(5)->get())
            ->merge($team->awayMatches()->with(['homeTeam', 'league'])->where('status', 'finished')->orderBy('match_date', 'desc')->limit(5)->get())
            ->sortByDesc('match_date')
            ->take(10);
        
        // Get upcoming matches
        $upcomingMatches = collect()
            ->merge($team->homeMatches()->with(['awayTeam', 'league'])->where('status', 'scheduled')->orderBy('match_date')->limit(5)->get())
            ->merge($team->awayMatches()->with(['homeTeam', 'league'])->where('status', 'scheduled')->orderBy('match_date')->limit(5)->get())
            ->sortBy('match_date')
            ->take(10);
        
        return view('teams.show', compact('team', 'recentMatches', 'upcomingMatches'));
    }
}
