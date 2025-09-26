<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;

class SetDataUrlLogos extends Command
{
    protected $signature = 'leagues:set-data-urls';
    protected $description = 'Set league logos using base64 data URLs (100% guaranteed to work)';

    public function handle()
    {
        $this->info('Setting league logos with data URLs...');

        // Base64 encoded mini logos that will always work
        $dataUrlLogos = [
            'premier-league' => 'data:image/svg+xml;base64,' . base64_encode('
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" fill="#37003c" stroke="#00ff87" stroke-width="2"/>
                    <text x="50" y="35" font-family="Arial" font-size="10" font-weight="bold" fill="white" text-anchor="middle">PREMIER</text>
                    <text x="50" y="50" font-family="Arial" font-size="10" font-weight="bold" fill="white" text-anchor="middle">LEAGUE</text>
                    <circle cx="50" cy="65" r="8" fill="none" stroke="#00ff87" stroke-width="2"/>
                </svg>
            '),
            'la-liga' => 'data:image/svg+xml;base64,' . base64_encode('
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" fill="#ff4500" stroke="#dc143c" stroke-width="2"/>
                    <text x="50" y="40" font-family="Arial" font-size="12" font-weight="bold" fill="white" text-anchor="middle">LA</text>
                    <text x="50" y="55" font-family="Arial" font-size="12" font-weight="bold" fill="white" text-anchor="middle">LIGA</text>
                    <rect x="35" y="65" width="30" height="4" fill="white" rx="2"/>
                    <rect x="37" y="70" width="26" height="3" fill="#ffff00" rx="1"/>
                </svg>
            '),
            'serie-a' => 'data:image/svg+xml;base64,' . base64_encode('
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" fill="#1e3a8a" stroke="#10b981" stroke-width="2"/>
                    <text x="50" y="40" font-family="Arial" font-size="10" font-weight="bold" fill="white" text-anchor="middle">SERIE</text>
                    <text x="50" y="55" font-family="Arial" font-size="16" font-weight="bold" fill="white" text-anchor="middle">A</text>
                    <circle cx="40" cy="70" r="2" fill="#00aa00"/>
                    <circle cx="50" cy="72" r="2" fill="white"/>
                    <circle cx="60" cy="70" r="2" fill="#dd0000"/>
                </svg>
            ')
        ];

        foreach ($dataUrlLogos as $slug => $dataUrl) {
            $league = League::where('slug', $slug)->first();
            if ($league) {
                $league->update(['logo' => $dataUrl]);
                $this->info("✅ Updated data URL logo for {$league->name}");
            } else {
                $this->warn("❌ League with slug '{$slug}' not found");
            }
        }

        $this->info('Data URL league logos updated successfully! These are guaranteed to work.');
        return 0;
    }
}