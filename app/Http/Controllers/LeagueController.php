<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\League;
use App\Models\Team;
use App\Models\FootballMatch;

class LeagueController extends Controller
{
    public function index()
    {
        $leagues = League::with('teams')->where('is_active', true)->get();
        return view('leagues.index', compact('leagues'));
    }

    public function show($slug)
    {
        $league = League::where('slug', $slug)->firstOrFail();
        
        // Get league table (standings)
        $teams = Team::where('league_id', $league->id)->get();
        
        // Calculate standings
        $standings = $teams->map(function ($team) {
            $homeMatches = FootballMatch::where('home_team_id', $team->id)
                ->where('status', 'finished')
                ->get();
            
            $awayMatches = FootballMatch::where('away_team_id', $team->id)
                ->where('status', 'finished')
                ->get();
            
            $played = $homeMatches->count() + $awayMatches->count();
            $wins = $homeMatches->where('home_score', '>', 'away_score')->count() +
                   $awayMatches->where('away_score', '>', 'home_score')->count();
            $draws = $homeMatches->where('home_score', 'away_score')->count() +
                    $awayMatches->where('away_score', 'home_score')->count();
            $losses = $played - $wins - $draws;
            
            $goalsFor = $homeMatches->sum('home_score') + $awayMatches->sum('away_score');
            $goalsAgainst = $homeMatches->sum('away_score') + $awayMatches->sum('home_score');
            $goalDifference = $goalsFor - $goalsAgainst;
            $points = ($wins * 3) + $draws;
            
            return [
                'team' => $team,
                'played' => $played,
                'wins' => $wins,
                'draws' => $draws,
                'losses' => $losses,
                'goals_for' => $goalsFor,
                'goals_against' => $goalsAgainst,
                'goal_difference' => $goalDifference,
                'points' => $points
            ];
        })->sortByDesc('points');

        // Get recent matches
        $recentMatches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->where('league_id', $league->id)
            ->where('status', 'finished')
            ->orderBy('match_date', 'desc')
            ->limit(10)
            ->get();

        // Get upcoming matches
        $upcomingMatches = FootballMatch::with(['homeTeam', 'awayTeam'])
            ->where('league_id', $league->id)
            ->where('status', 'scheduled')
            ->orderBy('match_date')
            ->limit(10)
            ->get();

        return view('leagues.show', compact('league', 'standings', 'recentMatches', 'upcomingMatches'));
    }
}
