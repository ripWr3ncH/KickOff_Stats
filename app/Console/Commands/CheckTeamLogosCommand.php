<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;

class CheckTeamLogosCommand extends Command
{
    protected $signature = 'teams:check-logos';
    protected $description = 'Check team logo data in database';

    public function handle()
    {
        $this->info('Checking team logo data...');
        
        $teams = Team::select('id', 'name', 'logo')->take(10)->get();
        
        if ($teams->isEmpty()) {
            $this->error('No teams found in database!');
            return;
        }
        
        $this->info('Found ' . $teams->count() . ' teams:');
        $this->newLine();
        
        foreach ($teams as $team) {
            $logoStatus = $team->logo ? 'HAS LOGO' : 'NO LOGO';
            $this->line("ID: {$team->id} | Name: {$team->name} | Logo: {$logoStatus}");
            if ($team->logo) {
                $this->line("  Logo URL: {$team->logo}");
            }
            $this->newLine();
        }
        
        $teamsWithLogos = Team::whereNotNull('logo')->count();
        $totalTeams = Team::count();
        
        $this->info("Summary: {$teamsWithLogos}/{$totalTeams} teams have logos");
    }
}