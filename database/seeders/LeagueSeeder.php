<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\League;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leagues = [
            [
                'name' => 'Premier League',
                'slug' => 'premier-league',
                'country' => 'England',
                'season' => '2024-25',
                'is_active' => true
            ],
            [
                'name' => 'La Liga',
                'slug' => 'la-liga',
                'country' => 'Spain',
                'season' => '2024-25',
                'is_active' => true
            ],
            [
                'name' => 'Serie A',
                'slug' => 'serie-a',
                'country' => 'Italy',
                'season' => '2024-25',
                'is_active' => true
            ]
        ];

        foreach ($leagues as $league) {
            League::updateOrCreate(
                ['slug' => $league['slug']],
                $league
            );
        }
    }
}
