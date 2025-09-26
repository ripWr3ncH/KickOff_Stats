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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name', 10);
            $table->string('slug')->unique();
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->string('logo')->nullable();
            $table->string('city');
            $table->string('stadium')->nullable();
            $table->integer('founded')->nullable();
            $table->text('description')->nullable();
            $table->string('primary_color', 7)->nullable(); // hex color
            $table->string('secondary_color', 7)->nullable(); // hex color
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
