<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\League;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Models\Player;

class HomeController extends Controller
{
    public function index()
    {
        $leagues = League::where('is_active', true)->get();
        
        // Get today's matches
        $todayMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->whereDate('match_date', today())
            ->orderBy('match_date')
            ->get();
        
        // Get live matches
        $liveMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'live')
            ->get();
        
        // Get recent results
        $recentResults = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'finished')
            ->orderBy('match_date', 'desc')
            ->limit(6)
            ->get();
        
        // Get upcoming matches
        $upcomingMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'scheduled')
            ->where('match_date', '>', now())
            ->orderBy('match_date')
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'leagues', 
            'todayMatches', 
            'liveMatches', 
            'recentResults', 
            'upcomingMatches'
        ));
    }
    
    public function apiStatus()
    {
        $leagues = League::with('teams')->where('is_active', true)->get();
        $todayMatches = FootballMatch::whereDate('match_date', today())->count();
        $liveMatches = FootballMatch::where('status', 'live')->count();
        
        return view('api-status', compact('leagues', 'todayMatches', 'liveMatches'));
    }
}
