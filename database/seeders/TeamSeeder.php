<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\League;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $premierLeague = League::where('slug', 'premier-league')->first();
        $laLiga = League::where('slug', 'la-liga')->first();
        $serieA = League::where('slug', 'serie-a')->first();

        if ($premierLeague) {
            $plTeams = [
                ['name' => 'Manchester City', 'short_name' => 'MCI', 'city' => 'Manchester', 'stadium' => 'Etihad Stadium'],
                ['name' => 'Arsenal', 'short_name' => 'ARS', 'city' => 'London', 'stadium' => 'Emirates Stadium'],
                ['name' => 'Liverpool', 'short_name' => 'LIV', 'city' => 'Liverpool', 'stadium' => 'Anfield'],
                ['name' => 'Manchester United', 'short_name' => 'MUN', 'city' => 'Manchester', 'stadium' => 'Old Trafford'],
                ['name' => 'Chelsea', 'short_name' => 'CHE', 'city' => 'London', 'stadium' => 'Stamford Bridge'],
                ['name' => 'Tottenham', 'short_name' => 'TOT', 'city' => 'London', 'stadium' => 'Tottenham Hotspur Stadium'],
            ];

            foreach ($plTeams as $team) {
                Team::updateOrCreate(
                    ['slug' => str($team['name'])->slug()],
                    array_merge($team, ['league_id' => $premierLeague->id])
                );
            }
        }

        if ($laLiga) {
            $laLigaTeams = [
                ['name' => 'Real Madrid', 'short_name' => 'RMA', 'city' => 'Madrid', 'stadium' => 'Santiago Bernabéu'],
                ['name' => 'Barcelona', 'short_name' => 'BAR', 'city' => 'Barcelona', 'stadium' => 'Camp Nou'],
                ['name' => 'Atletico Madrid', 'short_name' => 'ATM', 'city' => 'Madrid', 'stadium' => 'Wanda Metropolitano'],
                ['name' => 'Sevilla', 'short_name' => 'SEV', 'city' => 'Sevilla', 'stadium' => 'Ramón Sánchez-Pizjuán'],
                ['name' => 'Real Betis', 'short_name' => 'BET', 'city' => 'Sevilla', 'stadium' => 'Benito Villamarín'],
                ['name' => 'Valencia', 'short_name' => 'VAL', 'city' => 'Valencia', 'stadium' => 'Mestalla'],
            ];

            foreach ($laLigaTeams as $team) {
                Team::updateOrCreate(
                    ['slug' => str($team['name'])->slug()],
                    array_merge($team, ['league_id' => $laLiga->id])
                );
            }
        }

        if ($serieA) {
            $serieATeams = [
                ['name' => 'Juventus', 'short_name' => 'JUV', 'city' => 'Turin', 'stadium' => 'Allianz Stadium'],
                ['name' => 'Inter Milan', 'short_name' => 'INT', 'city' => 'Milan', 'stadium' => 'San Siro'],
                ['name' => 'AC Milan', 'short_name' => 'MIL', 'city' => 'Milan', 'stadium' => 'San Siro'],
                ['name' => 'AS Roma', 'short_name' => 'ROM', 'city' => 'Rome', 'stadium' => 'Stadio Olimpico'],
                ['name' => 'Lazio', 'short_name' => 'LAZ', 'city' => 'Rome', 'stadium' => 'Stadio Olimpico'],
                ['name' => 'Napoli', 'short_name' => 'NAP', 'city' => 'Naples', 'stadium' => 'Stadio Diego Armando Maradona'],
            ];

            foreach ($serieATeams as $team) {
                Team::updateOrCreate(
                    ['slug' => str($team['name'])->slug()],
                    array_merge($team, ['league_id' => $serieA->id])
                );
            }
        }
    }
}
