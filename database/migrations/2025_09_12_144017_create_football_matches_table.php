<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('football_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->foreignId('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('teams')->onDelete('cascade');
            $table->datetime('match_date');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->enum('status', ['scheduled', 'live', 'finished', 'postponed'])->default('scheduled');
            $table->integer('minute')->nullable(); // current minute for live matches
            $table->string('venue')->nullable();
            $table->integer('attendance')->nullable();
            $table->string('referee')->nullable();
            $table->integer('matchweek')->nullable();
            $table->json('events')->nullable(); // goals, cards, substitutions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_matches');
    }
};
