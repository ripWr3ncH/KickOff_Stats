<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DreamTeam;
use App\Models\Player;
use App\Models\League;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DreamTeamController extends Controller
{
    public function index()
    {
        $dreamTeams = DreamTeam::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('dream-team.index', compact('dreamTeams'));
    }

    public function create()
    {
        $leagues = League::where('is_active', true)->get();
        $formations = ['4-3-3', '4-4-2', '3-5-2', '5-3-2', '4-2-3-1'];
        
        return view('dream-team.create', compact('leagues', 'formations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'formation' => 'required|string|in:4-3-3,4-4-2,3-5-2,5-3-2,4-2-3-1',
            'players' => 'required|array',
            'description' => 'nullable|string|max:1000'
        ]);

        $dreamTeam = DreamTeam::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'formation' => $request->formation,
            'players' => $request->players,
            'description' => $request->description,
            'is_public' => $request->boolean('is_public')
        ]);

        // Calculate total value
        $this->updateTotalValue($dreamTeam);

        return redirect()
            ->route('dream-team.show', $dreamTeam)
            ->with('success', 'Dream team created successfully!');
    }

    public function show(DreamTeam $dreamTeam)
    {
        // Check if user can view this dream team
        if ($dreamTeam->user_id !== Auth::id() && !$dreamTeam->is_public) {
            abort(403);
        }

        $playersWithDetails = $dreamTeam->getPlayersWithDetails();
        $formationPositions = $dreamTeam->getFormationPositions();

        return view('dream-team.show', compact('dreamTeam', 'playersWithDetails', 'formationPositions'));
    }

    public function edit(DreamTeam $dreamTeam)
    {
        // Check if user owns this dream team
        if ($dreamTeam->user_id !== Auth::id()) {
            abort(403);
        }

        $leagues = League::where('is_active', true)->get();
        $formations = ['4-3-3', '4-4-2', '3-5-2', '5-3-2', '4-2-3-1'];
        $playersWithDetails = $dreamTeam->getPlayersWithDetails();
        
        return view('dream-team.edit', compact('dreamTeam', 'leagues', 'formations', 'playersWithDetails'));
    }

    public function update(Request $request, DreamTeam $dreamTeam)
    {
        // Check if user owns this dream team
        if ($dreamTeam->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'formation' => 'required|string|in:4-3-3,4-4-2,3-5-2,5-3-2,4-2-3-1',
            'players' => 'required|array',
            'description' => 'nullable|string|max:1000'
        ]);

        $dreamTeam->update([
            'name' => $request->name,
            'formation' => $request->formation,
            'players' => $request->players,
            'description' => $request->description,
            'is_public' => $request->boolean('is_public')
        ]);

        // Update total value
        $this->updateTotalValue($dreamTeam);

        return redirect()
            ->route('dream-team.show', $dreamTeam)
            ->with('success', 'Dream team updated successfully!');
    }

    public function destroy(DreamTeam $dreamTeam)
    {
        // Check if user owns this dream team
        if ($dreamTeam->user_id !== Auth::id()) {
            abort(403);
        }

        $dreamTeam->delete();

        return redirect()
            ->route('dream-team.index')
            ->with('success', 'Dream team deleted successfully!');
    }

    public function searchPlayers(Request $request)
    {
        $query = Player::with(['team.league']);

        if ($request->has('search') && $request->search) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('position') && $request->position) {
            $query->where('position', $request->position);
        }

        if ($request->has('league') && $request->league) {
            $query->whereHas('team.league', function($q) use ($request) {
                $q->where('id', $request->league);
            });
        }

        $players = $query->limit(20)->get();

        return response()->json($players->map(function ($player) {
            return [
                'id' => $player->id,
                'name' => $player->name,
                'position' => $player->position,
                'team' => $player->team ? $player->team->name : 'Unknown',
                'league' => $player->team && $player->team->league ? $player->team->league->name : 'Unknown',
                'photo_url' => $player->photo_url,
                'market_value' => $player->market_value ?? 0
            ];
        }));
    }

    private function updateTotalValue(DreamTeam $dreamTeam)
    {
        $playerIds = collect($dreamTeam->players)->pluck('player_id')->filter();
        
        if ($playerIds->isNotEmpty()) {
            $totalValue = Player::whereIn('id', $playerIds)->sum('market_value');
            $dreamTeam->update(['total_value' => $totalValue]);
        }
    }
}
