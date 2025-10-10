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
                ['name' => 'Newcastle United', 'short_name' => 'NEW', 'city' => 'Newcastle', 'stadium' => 'St. James\' Park'],
                ['name' => 'Brighton & Hove Albion', 'short_name' => 'BHA', 'city' => 'Brighton', 'stadium' => 'Amex Stadium'],
                ['name' => 'West Ham United', 'short_name' => 'WHU', 'city' => 'London', 'stadium' => 'London Stadium'],
                ['name' => 'Everton', 'short_name' => 'EVE', 'city' => 'Liverpool', 'stadium' => 'Goodison Park'],
                ['name' => 'Leicester City', 'short_name' => 'LEI', 'city' => 'Leicester', 'stadium' => 'King Power Stadium'],
                ['name' => 'Aston Villa', 'short_name' => 'AVL', 'city' => 'Birmingham', 'stadium' => 'Villa Park'],
                ['name' => 'Crystal Palace', 'short_name' => 'CRY', 'city' => 'London', 'stadium' => 'Selhurst Park'],
                ['name' => 'Fulham', 'short_name' => 'FUL', 'city' => 'London', 'stadium' => 'Craven Cottage'],
                ['name' => 'Brentford', 'short_name' => 'BRE', 'city' => 'London', 'stadium' => 'Brentford Community Stadium'],
                ['name' => 'Wolverhampton Wanderers', 'short_name' => 'WOL', 'city' => 'Wolverhampton', 'stadium' => 'Molineux Stadium'],
                ['name' => 'AFC Bournemouth', 'short_name' => 'BOU', 'city' => 'Bournemouth', 'stadium' => 'Vitality Stadium'],
                ['name' => 'Nottingham Forest', 'short_name' => 'NFO', 'city' => 'Nottingham', 'stadium' => 'City Ground'],
                ['name' => 'Sheffield United', 'short_name' => 'SHU', 'city' => 'Sheffield', 'stadium' => 'Bramall Lane'],
                ['name' => 'Burnley', 'short_name' => 'BUR', 'city' => 'Burnley', 'stadium' => 'Turf Moor'],
            ];

            foreach ($plTeams as $team) {
                Team::updateOrCreate(
                    ['slug' => str($team['name'])->slug()],
                    array_merge($team, [
                        'league_id' => $premierLeague->id,
                        'founded' => rand(1880, 1960)
                    ])
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
                ['name' => 'Real Sociedad', 'short_name' => 'RSO', 'city' => 'San Sebastián', 'stadium' => 'Reale Arena'],
                ['name' => 'Athletic Bilbao', 'short_name' => 'ATH', 'city' => 'Bilbao', 'stadium' => 'San Mamés'],
                ['name' => 'Villarreal', 'short_name' => 'VIL', 'city' => 'Villarreal', 'stadium' => 'Estadio de la Cerámica'],
                ['name' => 'Getafe', 'short_name' => 'GET', 'city' => 'Getafe', 'stadium' => 'Coliseum Alfonso Pérez'],
                ['name' => 'Osasuna', 'short_name' => 'OSA', 'city' => 'Pamplona', 'stadium' => 'El Sadar'],
                ['name' => 'Celta Vigo', 'short_name' => 'CEL', 'city' => 'Vigo', 'stadium' => 'Balaídos'],
                ['name' => 'Rayo Vallecano', 'short_name' => 'RAY', 'city' => 'Madrid', 'stadium' => 'Campo de Fútbol de Vallecas'],
                ['name' => 'Las Palmas', 'short_name' => 'LPA', 'city' => 'Las Palmas', 'stadium' => 'Estadio Gran Canaria'],
                ['name' => 'Alaves', 'short_name' => 'ALA', 'city' => 'Vitoria-Gasteiz', 'stadium' => 'Mendizorrotza'],
                ['name' => 'Mallorca', 'short_name' => 'MLL', 'city' => 'Palma', 'stadium' => 'Son Moix'],
                ['name' => 'Girona', 'short_name' => 'GIR', 'city' => 'Girona', 'stadium' => 'Estadi Montilivi'],
                ['name' => 'Cadiz', 'short_name' => 'CAD', 'city' => 'Cádiz', 'stadium' => 'Nuevo Mirandilla'],
                ['name' => 'Almeria', 'short_name' => 'ALM', 'city' => 'Almería', 'stadium' => 'Power Horse Stadium'],
                ['name' => 'Granada', 'short_name' => 'GRA', 'city' => 'Granada', 'stadium' => 'Nuevo Los Cármenes'],
            ];

            foreach ($laLigaTeams as $team) {
                Team::updateOrCreate(
                    ['slug' => str($team['name'])->slug()],
                    array_merge($team, [
                        'league_id' => $laLiga->id,
                        'founded' => rand(1900, 1970)
                    ])
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
                ['name' => 'Atalanta', 'short_name' => 'ATA', 'city' => 'Bergamo', 'stadium' => 'Gewiss Stadium'],
                ['name' => 'Fiorentina', 'short_name' => 'FIO', 'city' => 'Florence', 'stadium' => 'Stadio Artemio Franchi'],
                ['name' => 'Torino', 'short_name' => 'TOR', 'city' => 'Turin', 'stadium' => 'Stadio Olimpico Grande Torino'],
                ['name' => 'Bologna', 'short_name' => 'BOL', 'city' => 'Bologna', 'stadium' => 'Stadio Renato Dall\'Ara'],
                ['name' => 'Sassuolo', 'short_name' => 'SAS', 'city' => 'Sassuolo', 'stadium' => 'Mapei Stadium'],
                ['name' => 'Hellas Verona', 'short_name' => 'VER', 'city' => 'Verona', 'stadium' => 'Stadio Marcantonio Bentegodi'],
                ['name' => 'Udinese', 'short_name' => 'UDI', 'city' => 'Udine', 'stadium' => 'Dacia Arena'],
                ['name' => 'Monza', 'short_name' => 'MON', 'city' => 'Monza', 'stadium' => 'U-Power Stadium'],
                ['name' => 'Empoli', 'short_name' => 'EMP', 'city' => 'Empoli', 'stadium' => 'Stadio Carlo Castellani'],
                ['name' => 'Lecce', 'short_name' => 'LEC', 'city' => 'Lecce', 'stadium' => 'Stadio Via del Mare'],
                ['name' => 'Frosinone', 'short_name' => 'FRO', 'city' => 'Frosinone', 'stadium' => 'Stadio Benito Stirpe'],
                ['name' => 'Genoa', 'short_name' => 'GEN', 'city' => 'Genoa', 'stadium' => 'Stadio Luigi Ferraris'],
                ['name' => 'Cagliari', 'short_name' => 'CAG', 'city' => 'Cagliari', 'stadium' => 'Sardegna Arena'],
                ['name' => 'Salernitana', 'short_name' => 'SAL', 'city' => 'Salerno', 'stadium' => 'Stadio Arechi'],
            ];

            foreach ($serieATeams as $team) {
                Team::updateOrCreate(
                    ['slug' => str($team['name'])->slug()],
                    array_merge($team, [
                        'league_id' => $serieA->id,
                        'founded' => rand(1900, 1970)
                    ])
                );
            }
        }

        $this->command->info('Teams restored successfully! Total: ' . Team::count());
    }
}
