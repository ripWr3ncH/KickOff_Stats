<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DreamTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'formation',
        'players',
        'total_value',
        'description',
        'is_public'
    ];

    protected $casts = [
        'players' => 'array',
        'total_value' => 'decimal:2',
        'is_public' => 'boolean'
    ];

    /**
     * Get the user that owns the dream team
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get players in the dream team with their details
     */
    public function getPlayersWithDetails()
    {
        if (empty($this->players)) {
            return collect();
        }

        $playerIds = collect($this->players)->pluck('player_id')->filter();
        
        if ($playerIds->isEmpty()) {
            return collect();
        }

        $players = Player::with(['team'])->whereIn('id', $playerIds)->get();
        
        return collect($this->players)->map(function ($slot) use ($players) {
            $player = $players->firstWhere('id', $slot['player_id'] ?? null);
            return [
                'position' => $slot['position'],
                'x' => $slot['x'] ?? 0,
                'y' => $slot['y'] ?? 0,
                'player' => $player
            ];
        });
    }

    /**
     * Get formation positions
     */
    public function getFormationPositions()
    {
        $formations = [
            '4-3-3' => [
                'GK' => [['x' => 50, 'y' => 90]],
                'DEF' => [
                    ['x' => 20, 'y' => 70], ['x' => 40, 'y' => 70], 
                    ['x' => 60, 'y' => 70], ['x' => 80, 'y' => 70]
                ],
                'MID' => [
                    ['x' => 30, 'y' => 45], ['x' => 50, 'y' => 45], ['x' => 70, 'y' => 45]
                ],
                'FWD' => [
                    ['x' => 25, 'y' => 20], ['x' => 50, 'y' => 20], ['x' => 75, 'y' => 20]
                ]
            ],
            '4-4-2' => [
                'GK' => [['x' => 50, 'y' => 90]],
                'DEF' => [
                    ['x' => 20, 'y' => 70], ['x' => 40, 'y' => 70], 
                    ['x' => 60, 'y' => 70], ['x' => 80, 'y' => 70]
                ],
                'MID' => [
                    ['x' => 20, 'y' => 45], ['x' => 40, 'y' => 45], 
                    ['x' => 60, 'y' => 45], ['x' => 80, 'y' => 45]
                ],
                'FWD' => [['x' => 35, 'y' => 20], ['x' => 65, 'y' => 20]]
            ],
            '3-5-2' => [
                'GK' => [['x' => 50, 'y' => 90]],
                'DEF' => [
                    ['x' => 30, 'y' => 70], ['x' => 50, 'y' => 70], ['x' => 70, 'y' => 70]
                ],
                'MID' => [
                    ['x' => 15, 'y' => 45], ['x' => 35, 'y' => 45], ['x' => 50, 'y' => 45],
                    ['x' => 65, 'y' => 45], ['x' => 85, 'y' => 45]
                ],
                'FWD' => [['x' => 35, 'y' => 20], ['x' => 65, 'y' => 20]]
            ]
        ];

        return $formations[$this->formation] ?? $formations['4-3-3'];
    }
}