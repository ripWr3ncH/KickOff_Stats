<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerStat extends Model
{
    protected $fillable = [
        'player_id',
        'match_id',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'minutes_played',
        'shots',
        'shots_on_target',
        'passes',
        'passes_completed',
        'pass_accuracy',
        'tackles',
        'interceptions',
        'fouls',
        'offsides',
        'rating'
    ];

    protected $casts = [
        'pass_accuracy' => 'decimal:2',
        'rating' => 'decimal:1'
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }
}
