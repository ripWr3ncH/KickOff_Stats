<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDuplicateTeams extends Command
{
    protected $signature = 'teams:check-duplicates';
    protected $description = 'Check for duplicate team names';

    public function handle()
    {
        $this->info('Checking for duplicate teams...');
        
        $duplicates = DB::select('
            SELECT name, COUNT(*) as count, GROUP_CONCAT(id) as ids
            FROM teams 
            GROUP BY name 
            HAVING count > 1 
            ORDER BY count DESC
        ');
        
        if (empty($duplicates)) {
            $this->info('No duplicate teams found!');
            return;
        }
        
        $this->warn('Found ' . count($duplicates) . ' duplicate team names:');
        
        foreach ($duplicates as $duplicate) {
            $this->line('');
            $this->line("Team: {$duplicate->name}");
            $this->line("Count: {$duplicate->count}");
            $this->line("IDs: {$duplicate->ids}");
            
            // Show details of each duplicate
            $teams = DB::table('teams')
                ->whereIn('id', explode(',', $duplicate->ids))
                ->get(['id', 'name', 'slug', 'league_id', 'created_at']);
            
            foreach ($teams as $team) {
                $league = DB::table('leagues')->where('id', $team->league_id)->first();
                $this->line("  - ID: {$team->id}, Slug: {$team->slug}, League: " . ($league->name ?? 'N/A') . ", Created: {$team->created_at}");
            }
        }
        
        return 0;
    }
}
