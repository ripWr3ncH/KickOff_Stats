<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\Team;

class UpdateTeamLogosCommand extends Command
{
    protected $signature = 'teams:update-logos';
    protected $description = 'Update team logos from Football Data API';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    public function handle()
    {
        $this->info('Updating team logos from Football Data API...');
        
        $teams = Team::whereNull('logo')->orWhere('logo', '')->get();
        
        if ($teams->isEmpty()) {
            $this->info('All teams already have logos!');
            return 0;
        }
        
        $this->info("Found {$teams->count()} teams without logos");
        $this->newLine();
        
        $updated = 0;
        
        foreach ($teams as $team) {
            $this->line("Processing: {$team->name}");
            
            // Try to find team by searching for similar names
            $teamData = $this->searchTeamByName($team->name);
            
            if ($teamData && isset($teamData['crest'])) {
                $team->update(['logo' => $teamData['crest']]);
                $this->info("✅ Updated logo for {$team->name}");
                $updated++;
            } else {
                $this->warn("⚠️  Could not find logo for {$team->name}");
            }
            
            // Rate limiting
            sleep(1);
        }
        
        $this->newLine();
        $this->info("✅ Updated logos for {$updated} teams!");
        
        return 0;
    }
    
    protected function searchTeamByName($teamName)
    {
        // For demo purposes, let's map some common teams to their API IDs
        $teamMapping = [
            'Manchester City' => 65,
            'Arsenal' => 57,
            'Liverpool' => 64,
            'Manchester United' => 66,
            'Chelsea' => 61,
            'Tottenham' => 73,
            'Real Madrid' => 86,
            'Barcelona' => 81,
            'Atletico Madrid' => 78,
            'Sevilla' => 559,
            'Valencia' => 95,
            'Athletic Bilbao' => 77,
            'Real Sociedad' => 92,
            'Villarreal' => 94,
            'Juventus' => 109,
            'AC Milan' => 98,
            'Inter' => 108,
            'Napoli' => 113,
            'AS Roma' => 100,
            'Lazio' => 110,
        ];
        
        $teamId = $teamMapping[$teamName] ?? null;
        
        if ($teamId) {
            return $this->footballService->getTeam($teamId);
        }
        
        return null;
    }
}