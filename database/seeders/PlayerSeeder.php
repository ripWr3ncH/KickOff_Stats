<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\FootballDataService;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('PlayerSeeder now uses real API data only.');
        $this->command->info('Legitimate teams are available (' . \App\Models\Team::count() . ' teams from 3 leagues).');
        $this->command->info('Players will be automatically populated when:');
        $this->command->info('1. Teams are synced from the Football Data API');
        $this->command->info('2. Match data is fetched (includes player lineups)');
        $this->command->info('3. You manually run player sync commands');
        $this->command->line('');
        $this->command->info('To sync real players, run:');
        $this->command->info('php artisan matches:update-live (fetches current match lineups)');
        $this->command->info('Or use the auto-updater.bat for continuous syncing');
        
        // Optionally, you could trigger an API sync here
        // $footballService = app(FootballDataService::class);
        // $footballService->syncCurrentMatchPlayers();
    }
}
