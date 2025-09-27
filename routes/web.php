<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Api\LiveScoreController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api-status', [HomeController::class, 'apiStatus'])->name('api.status');

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

// Player routes
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
Route::get('/players/{slug}', [PlayerController::class, 'show'])->name('players.show');

// API routes for live data
Route::prefix('api')->group(function () {
    Route::get('/live-scores', [LiveScoreController::class, 'index'])->name('api.live-scores');
    Route::post('/live-scores/update', [LiveScoreController::class, 'update'])->name('api.live-scores.update');
    Route::get('/match-events/{id}', [MatchController::class, 'events'])->name('api.match-events');
});
