<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MyTeamsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\DreamTeamController;
use App\Http\Controllers\Api\LiveScoreController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api-status', [HomeController::class, 'apiStatus'])->name('api.status');

// Debug route to check auth status
Route::get('/auth-debug', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return response()->json([
            'authenticated' => true,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'remember_token_exists' => !empty($user->remember_token),
            'remember_token_length' => $user->remember_token ? strlen($user->remember_token) : 0,
            'session_id' => session()->getId(),
        ]);
    }
    return response()->json(['authenticated' => false]);
})->name('auth.debug');

// Authentication routes (modal-based, no separate pages needed)
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// League routes
Route::get('/leagues', [LeagueController::class, 'index'])->name('leagues.index');
Route::get('/leagues/{slug}', [LeagueController::class, 'show'])->name('leagues.show');
Route::get('/leagues/{slug}/standings', [LeagueController::class, 'standings'])->name('leagues.standings');

// Match routes
Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
Route::get('/matches/live', [MatchController::class, 'live'])->name('matches.live');
Route::get('/matches/{id}', [MatchController::class, 'show'])->name('matches.show');

// Team routes
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{slug}', [TeamController::class, 'show'])->name('teams.show');

// News routes
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/search', [NewsController::class, 'search'])->name('news.search');
Route::get('/news/team/{slug}', [NewsController::class, 'team'])->name('news.team');
Route::get('/news/league/{slug}', [NewsController::class, 'league'])->name('news.league');

// My Teams routes (require authentication)
Route::middleware(['auth.user'])->group(function () {
    Route::get('/my-teams', [MyTeamsController::class, 'index'])->name('my-teams.index');
    Route::get('/my-teams/select', [MyTeamsController::class, 'select'])->name('my-teams.select');
    Route::post('/my-teams/add-favorite', [MyTeamsController::class, 'addFavorite'])->name('my-teams.add-favorite');
    Route::post('/my-teams/remove-favorite', [MyTeamsController::class, 'removeFavorite'])->name('my-teams.remove-favorite');
});

// Dream Team routes (require authentication)
Route::middleware(['auth.user'])->group(function () {
    Route::get('/dream-team', [DreamTeamController::class, 'index'])->name('dream-team.index');
    Route::get('/dream-team/create', [DreamTeamController::class, 'create'])->name('dream-team.create');
    Route::post('/dream-team', [DreamTeamController::class, 'store'])->name('dream-team.store');
    Route::get('/dream-team/{dreamTeam}', [DreamTeamController::class, 'show'])->name('dream-team.show');
    Route::get('/dream-team/{dreamTeam}/edit', [DreamTeamController::class, 'edit'])->name('dream-team.edit');
    Route::put('/dream-team/{dreamTeam}', [DreamTeamController::class, 'update'])->name('dream-team.update');
    Route::delete('/dream-team/{dreamTeam}', [DreamTeamController::class, 'destroy'])->name('dream-team.destroy');
    Route::get('/api/dream-team/search-players', [DreamTeamController::class, 'searchPlayers'])->name('dream-team.search-players');
});

// Player routes
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
Route::get('/players/{slug}', [PlayerController::class, 'show'])->name('players.show');

// API routes for live data
Route::prefix('api')->group(function () {
    Route::get('/live-scores', [LiveScoreController::class, 'index'])->name('api.live-scores');
    Route::post('/live-scores/update', [LiveScoreController::class, 'update'])->name('api.live-scores.update');
    Route::get('/match-events/{id}', [MatchController::class, 'events'])->name('api.match-events');
    Route::get('/news', [NewsController::class, 'api'])->name('api.news');
});
