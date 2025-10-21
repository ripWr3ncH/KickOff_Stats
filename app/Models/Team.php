<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'slug',
        'league_id',
        'logo',
        'logo_url',
        'city',
        'stadium',
        'founded',
        'description',
        'primary_color',
        'secondary_color',
        'api_id'
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(FootballMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(FootballMatch::class, 'away_team_id');
    }

    public function matches()
    {
        return FootballMatch::where('home_team_id', $this->id)
            ->orWhere('away_team_id', $this->id);
    }

    /**
     * Users who have favorited this team.
     */
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_favorite_teams');
    }
}
