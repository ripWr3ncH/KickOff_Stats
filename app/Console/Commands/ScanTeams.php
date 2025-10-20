<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;

class ScanTeams extends Command
{
    protected $signature = 'teams:scan';
    protected $description = 'Scan for duplicate teams (seed vs API)';

    public function handle()
    {
        $this->info('=== Scanning for Duplicate Teams ===');
        $this->info('');
        
        // Check for Barcelona
        $this->info('Teams with "Barcelona" in name:');
        Team::where('name', 'like', '%Barcelona%')->orderBy('name')->get()->each(function($t) {
            $this->line("  - {$t->name} (Slug: {$t->slug}, City: {$t->city}, Created: {$t->created_at})");
        });
        
        $this->info('');
        
        // Check for Milan
        $this->info('Teams with "Milan" in name:');
        Team::where('name', 'like', '%Milan%')->orderBy('name')->get()->each(function($t) {
            $this->line("  - {$t->name} (Slug: {$t->slug}, City: {$t->created_at})");
        });
        
        $this->info('');
        
        // Check for Roma
        $this->info('Teams with "Roma" in name:');
        Team::where('name', 'like', '%Roma%')->orderBy('name')->get()->each(function($t) {
            $this->line("  - {$t->name} (Slug: {$t->slug}, City: {$t->city}, Created: {$t->created_at})");
        });
        
        return 0;
    }
}
