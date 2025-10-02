<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FootballMatch;
use App\Models\League;
use App\Models\Team;
use Carbon\Carbon;

class MatchSeeder extends Seeder
{
    public function run()
    {
        // Get leagues and teams
        $premierLeague = League::where('slug', 'premier-league')->first();
        $laLiga = League::where('slug', 'la-liga')->first();
        $serieA = League::where('slug', 'serie-a')->first();
        
        if (!$premierLeague || !$laLiga || !$serieA) {
            $this->command->error('Please run league and team seeders first');
            return;
        }
        
        // Get some teams for each league
        $plTeams = Team::where('league_id', $premierLeague->id)->take(6)->get();
        $laLigaTeams = Team::where('league_id', $laLiga->id)->take(6)->get();
        $serieATeams = Team::where('league_id', $serieA->id)->take(6)->get();
        
        // Create sample matches
        $this->createSampleMatches($premierLeague, $plTeams);
        $this->createSampleMatches($laLiga, $laLigaTeams);
        $this->createSampleMatches($serieA, $serieATeams);
    }
    
    private function createSampleMatches($league, $teams)
    {
        if ($teams->count() < 4) {
            return;
        }
        
        $now = Carbon::now();
        
        // Past matches (finished)
        for ($i = 1; $i <= 3; $i++) {
            FootballMatch::create([
                'league_id' => $league->id,
                'home_team_id' => $teams[$i * 2 - 2]->id,
                'away_team_id' => $teams[$i * 2 - 1]->id,
                'match_date' => $now->copy()->subDays($i + 2),
                'home_score' => rand(0, 4),
                'away_score' => rand(0, 4),
                'status' => 'finished',
                'venue' => $teams[$i * 2 - 2]->venue ?? 'Stadium',
                'matchweek' => $i
            ]);
        }
        
        // Today's match (live)
        FootballMatch::create([
            'league_id' => $league->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'match_date' => $now->copy(),
            'home_score' => rand(0, 3),
            'away_score' => rand(0, 3),
            'status' => 'live',
            'minute' => rand(1, 90),
            'venue' => $teams[0]->venue ?? 'Stadium',
            'matchweek' => 4
        ]);
        
        // Future matches (upcoming)
        for ($i = 1; $i <= 5; $i++) {
            FootballMatch::create([
                'league_id' => $league->id,
                'home_team_id' => $teams[($i * 2) % $teams->count()]->id,
                'away_team_id' => $teams[($i * 2 + 1) % $teams->count()]->id,
                'match_date' => $now->copy()->addDays($i),
                'status' => 'scheduled',
                'venue' => $teams[($i * 2) % $teams->count()]->venue ?? 'Stadium',
                'matchweek' => 4 + $i
            ]);
        }
    }
}