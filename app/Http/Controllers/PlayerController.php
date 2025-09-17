<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\League;
use App\Services\FootballDataService;
use Illuminate\Support\Facades\Log;

class PlayerController extends Controller
{
    protected $footballService;
    
    public function __construct(FootballDataService $footballService)
    {
        $this->footballService = $footballService;
    }

    public function index(Request $request)
    {
        $query = Player::with(['team.league']);
        
        // Filter by league if specified
        if ($request->has('league')) {
            $query->whereHas('team.league', function($q) use ($request) {
                $q->where('slug', $request->league);
            });
        }
        
        // Filter by position if specified
        if ($request->has('position')) {
            $query->where('position', $request->position);
        }
        
        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }
        
        // Sync player data if requested
        if ($request->has('sync_players')) {
            $this->syncPlayersFromAPI($request->league);
        }
        
        $players = $query->orderBy('name')->paginate(20);
        $leagues = League::where('is_active', true)->get();
        $positions = ['GK', 'DEF', 'MID', 'FWD'];
        
        return view('players.index', compact('players', 'leagues', 'positions'));
    }

    public function show($slug)
    {
        $player = Player::with([
            'team.league',
            'stats.match.homeTeam',
            'stats.match.awayTeam'
        ])->where('slug', $slug)->firstOrFail();
        
        // Try to get latest player data from API if available
        $this->syncPlayerFromAPI($player);
        
        // Calculate season stats
        $seasonStats = [
            'goals' => $player->stats()->sum('goals'),
            'assists' => $player->stats()->sum('assists'),
            'yellow_cards' => $player->stats()->sum('yellow_cards'),
            'red_cards' => $player->stats()->sum('red_cards'),
            'minutes_played' => $player->stats()->sum('minutes_played'),
            'matches_played' => $player->stats()->distinct('match_id')->count(),
            'average_rating' => $player->stats()->whereNotNull('rating')->avg('rating')
        ];
        
        // Get recent matches with this player
        $recentMatches = $player->stats()
            ->with(['match.homeTeam', 'match.awayTeam'])
            ->latest()
            ->limit(5)
            ->get();
        
        return view('players.show', compact('player', 'seasonStats', 'recentMatches'));
    }
    
    private function syncPlayersFromAPI($leagueSlug = null)
    {
        // Use your existing FootballDataService to sync players
        try {
            if ($leagueSlug) {
                $league = League::where('slug', $leagueSlug)->first();
                if ($league) {
                    $this->footballService->syncTeamPlayers($league->api_id);
                }
            }
        } catch (\Exception $e) {
            Log::error('Player sync failed: ' . $e->getMessage());
        }
    }
    
    private function syncPlayerFromAPI($player)
    {
        // Sync individual player if they have an API ID
        try {
            if ($player->api_id) {
                $this->footballService->updatePlayerData($player->api_id);
            }
        } catch (\Exception $e) {
            Log::error('Individual player sync failed: ' . $e->getMessage());
        }
    }
}
