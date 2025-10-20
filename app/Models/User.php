<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The user's favorite teams.
     */
    public function favoriteTeams()
    {
        return $this->belongsToMany(Team::class, 'user_favorite_teams');
    }

    /**
     * Check if user has favorited a specific team.
     */
    public function hasFavoritedTeam($teamId)
    {
        return $this->favoriteTeams()->where('team_id', $teamId)->exists();
    }

    /**
     * The user's dream teams.
     */
    public function dreamTeams()
    {
        return $this->hasMany(DreamTeam::class);
    }
}
