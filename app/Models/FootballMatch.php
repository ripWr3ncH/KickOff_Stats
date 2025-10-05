<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FootballMatch extends Model
{
    protected $fillable = [
        'api_match_id',
        'league_id',
        'home_team_id',
        'away_team_id',
        'match_date',
        'home_score',
        'away_score',
        'status',
        'minute',
        'venue',
        'attendance',
        'referee',
        'matchweek',
        'events'
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'events' => 'array'
    ];

    /**
     * Get match date in Bangladesh timezone
     */
    public function getLocalMatchDateAttribute()
    {
        return $this->match_date->setTimezone('Asia/Dhaka');
    }

    /**
     * Direct method to get local match date (non-attribute version)
     */
    public function getLocalMatchDate()
    {
        return $this->match_date->setTimezone('Asia/Dhaka');
    }

    /**
     * Check if match is today in Bangladesh timezone
     */
    public function isTodayLocal()
    {
        $bangladeshNow = now('Asia/Dhaka');
        return $this->getLocalMatchDateAttribute()->isSameDay($bangladeshNow);
    }

    /**
     * Check if match is tomorrow in Bangladesh timezone
     */
    public function isTomorrowLocal()
    {
        $bangladeshTomorrow = now('Asia/Dhaka')->addDay();
        return $this->getLocalMatchDateAttribute()->isSameDay($bangladeshTomorrow);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function playerStats(): HasMany
    {
        return $this->hasMany(PlayerStat::class, 'match_id');
    }

    public function getIsLiveAttribute()
    {
        return $this->status === 'live';
    }

    public function getIsFinishedAttribute()
    {
        return $this->status === 'finished';
    }

    public function getScoreAttribute()
    {
        if ($this->home_score !== null && $this->away_score !== null) {
            return $this->home_score . ' - ' . $this->away_score;
        }
        return 'vs';
    }
}
