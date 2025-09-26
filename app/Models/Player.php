<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Player extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'team_id',
        'position',
        'nationality',
        'date_of_birth',
        'jersey_number',
        'height',
        'weight',
        'photo',
        'market_value',
        'bio',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'market_value' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(PlayerStat::class);
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function getTotalGoalsAttribute()
    {
        return $this->stats()->sum('goals');
    }

    public function getTotalAssistsAttribute()
    {
        return $this->stats()->sum('assists');
    }
}
