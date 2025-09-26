<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\League;
use App\Models\Team;

class SyncPlayersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'players:sync {--league= : Specific league slug to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync player data from Football Data API';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting player synchronization...');
        
        $leagueSlug = $this->option('league');
        
        if ($leagueSlug) {
            $league = League::where('slug', $leagueSlug)->first();
            if (!$league) {
                $this->error("League with slug '{$leagueSlug}' not found.");
                return 1;
            }
            $this->syncLeagueTeams($league);
        } else {
            $leagues = League::where('is_active', true)->get();
            foreach ($leagues as $league) {
                $this->syncLeagueTeams($league);
            }
        }
        
        $this->info('✅ Player synchronization completed!');
        return 0;
    }
    
    private function syncLeagueTeams($league)
    {
        $this->info("Syncing players for {$league->name}...");
        
        $teams = Team::where('league_id', $league->id)->get();
        
        foreach ($teams as $team) {
            if ($team->api_id) {
                $this->line("  Processing team: {$team->name}");
                $players = $this->footballService->syncTeamPlayers($team->api_id);
                $this->line("    Found " . count($players) . " players");
            }
        }
    }
}
