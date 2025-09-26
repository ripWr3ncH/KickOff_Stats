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
        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->constrained('football_matches')->onDelete('cascade');
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);
            $table->integer('minutes_played')->default(0);
            $table->integer('shots')->default(0);
            $table->integer('shots_on_target')->default(0);
            $table->integer('passes')->default(0);
            $table->integer('passes_completed')->default(0);
            $table->decimal('pass_accuracy', 5, 2)->default(0); // percentage
            $table->integer('tackles')->default(0);
            $table->integer('interceptions')->default(0);
            $table->integer('fouls')->default(0);
            $table->integer('offsides')->default(0);
            $table->decimal('rating', 3, 1)->nullable(); // player rating out of 10
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_stats');
    }
};
