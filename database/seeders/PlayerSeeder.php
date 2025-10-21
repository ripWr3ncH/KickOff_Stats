<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Player;
use Illuminate\Support\Str;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeder.
     * SAFE: Only creates players, does NOT touch teams or matches!
     */
    public function run(): void
    {
        echo "🔒 SAFE MODE: Only creating players (teams and matches untouched)\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        // Get all teams
        $teams = Team::all();
        
        if ($teams->isEmpty()) {
            echo "❌ No teams found! Please seed teams first.\n";
            return;
        }

        echo "✅ Found {$teams->count()} teams in database\n";
        echo "🔄 Generating realistic players for each team...\n\n";

        $positions = ['GK', 'DEF', 'MID', 'FWD'];
        $nationalities = [
            'England', 'Spain', 'France', 'Germany', 'Italy', 'Portugal', 
            'Brazil', 'Argentina', 'Netherlands', 'Belgium', 'Croatia',
            'Uruguay', 'Denmark', 'Sweden', 'Norway', 'Poland', 'Serbia',
            'Scotland', 'Wales', 'Ireland', 'Austria', 'Switzerland'
        ];

        $firstNames = [
            'James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard',
            'Carlos', 'Luis', 'Diego', 'Juan', 'Antonio', 'Marco', 'Andrea',
            'Luca', 'Alessandro', 'Thomas', 'Max', 'Leon', 'Felix', 'Pierre',
            'Alexandre', 'Antoine', 'Kylian', 'Sergio', 'Ivan', 'Mateo',
            'Lucas', 'Gabriel', 'Rafael', 'Bruno', 'Cristiano', 'João',
            'Miguel', 'André', 'Daniel', 'Samuel', 'Oliver', 'Harry',
            'Jack', 'Charlie', 'George', 'Mason', 'Liam', 'Noah', 'Oscar'
        ];

        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Martinez',
            'Rodriguez', 'Lopez', 'Gonzalez', 'Hernandez', 'Perez', 'Sanchez',
            'Silva', 'Santos', 'Oliveira', 'Costa', 'Rossi', 'Russo', 'Ferrari',
            'Müller', 'Schmidt', 'Schneider', 'Fischer', 'Weber', 'Meyer',
            'Wagner', 'Becker', 'Schulz', 'Hoffmann', 'Dubois', 'Martin',
            'Bernard', 'Thomas', 'Robert', 'Petit', 'Durand', 'Leroy',
            'Moreau', 'Simon', 'Laurent', 'Lefebvre', 'Michel', 'Garcia'
        ];

        $totalPlayers = 0;

        foreach ($teams as $team) {
            echo "⚽ {$team->name}:\n";
            
            // Generate squad of 25 players per team
            $playersPerPosition = [
                'GK' => 3,   // 3 goalkeepers
                'DEF' => 8,  // 8 defenders
                'MID' => 8,  // 8 midfielders
                'FWD' => 6   // 6 forwards
            ];

            $teamPlayers = 0;

            foreach ($playersPerPosition as $position => $count) {
                for ($i = 0; $i < $count; $i++) {
                    $firstName = $firstNames[array_rand($firstNames)];
                    $lastName = $lastNames[array_rand($lastNames)];
                    $fullName = $firstName . ' ' . $lastName;
                    
                    // Generate realistic data
                    $age = rand(18, 35);
                    $dateOfBirth = now()->subYears($age)->subDays(rand(1, 365));
                    
                    $player = Player::create([
                        'name' => $fullName,
                        'slug' => Str::slug($fullName . '-' . $team->slug . '-' . rand(1, 999)),
                        'team_id' => $team->id,
                        'position' => $position,
                        'nationality' => $nationalities[array_rand($nationalities)],
                        'date_of_birth' => $dateOfBirth,
                        'jersey_number' => $this->getJerseyNumber($position, $i),
                        'height' => $this->getHeight($position),
                        'weight' => rand(65, 95),
                        'market_value' => $this->getMarketValue($position, $age),
                        'photo' => null,
                        'bio' => "Professional footballer playing as a {$this->getPositionName($position)} for {$team->name}.",
                        'is_active' => true,
                        'api_id' => null
                    ]);

                    $teamPlayers++;
                }
            }

            echo "   ✅ Created {$teamPlayers} players\n";
            $totalPlayers += $teamPlayers;
        }

        echo "\n═══════════════════════════════════════════════════════════════\n";
        echo "🎉 PLAYER SEEDING COMPLETE!\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "Results:\n";
        echo "  ✅ Teams: {$teams->count()} (UNCHANGED)\n";
        echo "  ✅ Players created: {$totalPlayers}\n";
        echo "  ✅ Average per team: " . round($totalPlayers / $teams->count()) . "\n";
        echo "\n📊 Current Database State:\n";
        echo "  Teams: " . Team::count() . "\n";
        echo "  Players: " . Player::count() . "\n";
        echo "  Matches: " . \App\Models\FootballMatch::count() . " (UNCHANGED)\n";
        echo "\n✅ SUCCESS! All teams and matches preserved!\n";
    }

    /**
     * Get jersey number based on position
     */
    private function getJerseyNumber(string $position, int $index): int
    {
        switch ($position) {
            case 'GK':
                return [1, 13, 25][$index] ?? rand(1, 99);
            case 'DEF':
                return rand(2, 6) + ($index * 10);
            case 'MID':
                return rand(6, 11) + ($index * 5);
            case 'FWD':
                return [7, 9, 10, 11, 14, 21][$index] ?? rand(7, 99);
            default:
                return rand(1, 99);
        }
    }

    /**
     * Get realistic height based on position (in meters)
     */
    private function getHeight(string $position): float
    {
        switch ($position) {
            case 'GK':
                return round(rand(185, 200) / 100, 2);
            case 'DEF':
                return round(rand(178, 195) / 100, 2);
            case 'MID':
                return round(rand(170, 185) / 100, 2);
            case 'FWD':
                return round(rand(172, 190) / 100, 2);
            default:
                return round(rand(170, 190) / 100, 2);
        }
    }

    /**
     * Get realistic market value based on position and age
     */
    private function getMarketValue(string $position, int $age): float
    {
        $baseValue = rand(100, 5000) / 100;
        
        if ($age >= 25 && $age <= 28) {
            $ageMultiplier = 1.5;
        } elseif ($age >= 22 && $age <= 24) {
            $ageMultiplier = 1.3;
        } elseif ($age >= 29 && $age <= 31) {
            $ageMultiplier = 1.2;
        } else {
            $ageMultiplier = 0.8;
        }

        $positionMultiplier = match($position) {
            'FWD' => 1.3,
            'MID' => 1.1,
            'DEF' => 0.9,
            'GK' => 0.8,
            default => 1.0
        };

        return round($baseValue * $ageMultiplier * $positionMultiplier, 2);
    }

    /**
     * Get full position name
     */
    private function getPositionName(string $position): string
    {
        return match($position) {
            'GK' => 'Goalkeeper',
            'DEF' => 'Defender',
            'MID' => 'Midfielder',
            'FWD' => 'Forward',
            default => 'Player'
        };
    }
}
