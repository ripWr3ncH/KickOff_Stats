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
        Schema::table('football_matches', function (Blueprint $table) {
            $table->unsignedBigInteger('api_match_id')->nullable()->after('id');
            $table->index('api_match_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->dropIndex(['api_match_id']);
            $table->dropColumn('api_match_id');
        });
    }
};
