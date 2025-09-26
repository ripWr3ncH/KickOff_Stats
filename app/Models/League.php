<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'country',
        'logo',
        'season',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(FootballMatch::class);
    }

    /**
     * Get the league logo with fallback options
     */
    public function getLogoUrlAttribute()
    {
        // Return the logo URL if it exists, otherwise provide a fallback
        if ($this->logo) {
            return $this->logo;
        }

        // Fallback to reliable local SVG logos
        $fallbacks = [
            'premier-league' => '/images/leagues/premier-league.svg',
            'la-liga' => '/images/leagues/la-liga.svg',
            'serie-a' => '/images/leagues/serie-a.svg'
        ];

        return $fallbacks[$this->slug] ?? '/images/default-league.svg';
    }
}
