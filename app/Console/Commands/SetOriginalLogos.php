<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;

class SetOriginalLogos extends Command
{
    protected $signature = 'leagues:set-original {--source=1}';
    protected $description = 'Set original league logos from multiple reliable sources';

    public function handle()
    {
        $source = $this->option('source');
        
        $logoSources = [
            '1' => [
                'name' => 'SeekLogo CDN (High Quality PNG)',
                'logos' => [
                    'premier-league' => 'https://seeklogo.com/images/P/premier-league-logo-D9EE3E2E5A-seeklogo.com.png',
                    'la-liga' => 'https://seeklogo.com/images/L/la-liga-logo-CB6F14B22A-seeklogo.com.png',
                    'serie-a' => 'https://seeklogo.com/images/S/serie-a-logo-EC9CF2A594-seeklogo.com.png'
                ]
            ],
            '2' => [
                'name' => 'LogoSVG (Vector)',
                'logos' => [
                    'premier-league' => 'https://logosvg.com/svg/premier-league.svg',
                    'la-liga' => 'https://logosvg.com/svg/laliga.svg', 
                    'serie-a' => 'https://logosvg.com/svg/serie-a.svg'
                ]
            ],
            '3' => [
                'name' => 'Wikimedia Commons (Official)',
                'logos' => [
                    'premier-league' => 'https://upload.wikimedia.org/wikipedia/en/f/f2/Premier_League_Logo.svg',
                    'la-liga' => 'https://upload.wikimedia.org/wikipedia/commons/1/13/LaLiga.svg',
                    'serie-a' => 'https://upload.wikimedia.org/wikipedia/en/e/e1/Serie_A_logo_2019.svg'
                ]
            ],
            '4' => [
                'name' => 'Logos World (PNG)',
                'logos' => [
                    'premier-league' => 'https://logos-world.net/wp-content/uploads/2020/06/Premier-League-Logo.png',
                    'la-liga' => 'https://logos-world.net/wp-content/uploads/2020/06/La-Liga-Logo.png',
                    'serie-a' => 'https://logos-world.net/wp-content/uploads/2020/06/Serie-A-Logo.png'
                ]
            ]
        ];

        if (!isset($logoSources[$source])) {
            $this->error("Invalid source. Available sources:");
            foreach ($logoSources as $key => $sourceData) {
                $this->line("{$key}. {$sourceData['name']}");
            }
            return 1;
        }

        $selectedSource = $logoSources[$source];
        $this->info("Using source: {$selectedSource['name']}");

        foreach ($selectedSource['logos'] as $slug => $logoUrl) {
            $league = League::where('slug', $slug)->first();
            if ($league) {
                $league->update(['logo' => $logoUrl]);
                $this->info("✅ Updated logo for {$league->name}");
            } else {
                $this->warn("❌ League with slug '{$slug}' not found");
            }
        }

        $this->info("Original league logos updated successfully from {$selectedSource['name']}!");
        return 0;
    }
}