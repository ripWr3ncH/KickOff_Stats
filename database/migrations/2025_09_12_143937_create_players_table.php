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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('position'); // GK, DEF, MID, FWD
            $table->string('nationality');
            $table->date('date_of_birth');
            $table->integer('jersey_number')->nullable();
            $table->decimal('height', 4, 2)->nullable(); // in meters
            $table->decimal('weight', 5, 2)->nullable(); // in kg
            $table->string('photo')->nullable();
            $table->decimal('market_value', 10, 2)->nullable(); // in millions
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
