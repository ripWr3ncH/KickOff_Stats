<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;

class UpdateLeagueLogos extends Command
{
    protected $signature = 'leagues:update-logos';
    protected $description = 'Update league logos with high-quality URLs';

    public function handle()
    {
        $this->info('Updating league logos...');

        // Using local SVG assets for guaranteed loading
        $leagueLogos = [
            'premier-league' => '/images/leagues/premier-league.svg',
            'la-liga' => '/images/leagues/la-liga.svg',
            'serie-a' => '/images/leagues/serie-a.svg'
        ];

        foreach ($leagueLogos as $slug => $logoUrl) {
            $league = League::where('slug', $slug)->first();
            if ($league) {
                $league->update(['logo' => $logoUrl]);
                $this->info("Updated logo for {$league->name}");
            } else {
                $this->warn("League with slug '{$slug}' not found");
            }
        }

        $this->info('League logos updated successfully!');
        return 0;
    }
}