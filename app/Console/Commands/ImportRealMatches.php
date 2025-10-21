<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\League;
use App\Models\Team;
use App\Models\FootballMatch;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ImportRealMatches extends Command
{
    protected $signature = 'matches:import-real {--wait=60 : Seconds to wait between API calls}';
    protected $description = 'Import REAL matches from Football Data API';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    public function handle()
    {
        $this->info('🚀 Importing REAL matches from Football Data API...');
        $this->info('⚠️  This will replace any existing fake matches with real data.');
        
        if (!$this->confirm('Continue?')) {
            $this->info('❌ Import cancelled.');
            return;
        }

        // Clear existing fake matches
        $this->info('🗑️  Clearing existing matches...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        FootballMatch::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('✅ Existing matches cleared.');

        $waitTime = $this->option('wait');
        $totalImported = 0;

        // Competition IDs to try
        $competitions = [
            ['id' => 2014, 'name' => 'La Liga', 'league_slug' => 'la-liga'],
            ['id' => 2021, 'name' => 'Premier League', 'league_slug' => 'premier-league'],
            ['id' => 2019, 'name' => 'Serie A', 'league_slug' => 'serie-a'],
        ];

        foreach ($competitions as $competition) {
            $this->info("\n📊 Importing {$competition['name']}...");
            
            try {
                $matchData = $this->footballService->getCompetitionMatches($competition['id']);
                
                if ($matchData && isset($matchData['matches']) && count($matchData['matches']) > 0) {
                    $matches = $matchData['matches'];
                    $this->info("   Found " . count($matches) . " matches from API");
                    
                    $league = League::where('slug', $competition['league_slug'])->first();
                    if (!$league) {
                        $this->error("   ❌ League '{$competition['league_slug']}' not found in database");
                        continue;
                    }

                    $imported = $this->importMatches($matches, $league);
                    $totalImported += $imported;
                    $this->info("   ✅ Imported {$imported} real matches");
                } else {
                    $this->warn("   ⚠️  No data available (API rate limited or restricted)");
                }

                if ($waitTime > 0 && count($competitions) > 1) {
                    $this->info("   ⏳ Waiting {$waitTime} seconds for API cooldown...");
                    sleep($waitTime);
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
            }
        }

        $this->info("\n═══════════════════════════════════════");
        $this->info("🎉 IMPORT COMPLETE!");
        $this->info("═══════════════════════════════════════");
        $this->info("Total real matches imported: {$totalImported}");
        $this->info("Current database status:");
        $this->info("  - Matches: " . FootballMatch::count());
        $this->info("  - Teams: " . Team::count());
        $this->info("  - Leagues: " . League::count());

        if ($totalImported > 0) {
            $this->info("\n✅ You now have REAL matches from your API!");
            $this->info("Visit your app to see the real data:");
            $this->info("  • Home: http://127.0.0.1:8000");
            $this->info("  • Matches: http://127.0.0.1:8000/matches");
            $this->info("  • Leagues: http://127.0.0.1:8000/leagues");
        } else {
            $this->warn("\n⚠️  No matches imported. Your API might be rate limited.");
            $this->info("Try again in a few minutes or check your API status at:");
            $this->info("https://www.football-data.org/client/home");
        }
    }

    private function importMatches($matches, $league)
    {
        $imported = 0;
        $bar = $this->output->createProgressBar(count($matches));
        $bar->start();

        foreach ($matches as $matchData) {
            try {
                $matchDate = Carbon::parse($matchData['utcDate']);
                
                // Find or create home team
                $homeTeam = $this->findOrCreateTeam($matchData['homeTeam'], $league);
                $awayTeam = $this->findOrCreateTeam($matchData['awayTeam'], $league);

                if (!$homeTeam || !$awayTeam) {
                    continue;
                }

                // Map status
                $statusMap = [
                    'SCHEDULED' => 'scheduled',
                    'TIMED' => 'scheduled', 
                    'IN_PLAY' => 'live',
                    'PAUSED' => 'live',
                    'FINISHED' => 'finished',
                    'POSTPONED' => 'postponed',
                    'SUSPENDED' => 'postponed',
                    'CANCELLED' => 'postponed'
                ];
                $status = $statusMap[$matchData['status']] ?? 'scheduled';

                // Create match
                FootballMatch::create([
                    'league_id' => $league->id,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'match_date' => $matchDate,
                    'status' => $status,
                    'home_score' => $matchData['score']['fullTime']['home'] ?? null,
                    'away_score' => $matchData['score']['fullTime']['away'] ?? null,
                    'api_match_id' => $matchData['id'] ?? null,
                ]);

                $imported++;
                $bar->advance();
            } catch (\Exception $e) {
                // Skip problematic matches
                $bar->advance();
                continue;
            }
        }

        $bar->finish();
        return $imported;
    }

    private function findOrCreateTeam($teamData, $league)
    {
        $apiId = $teamData['id'] ?? null;
        $name = $teamData['name'] ?? 'Unknown';

        // Try to find by API ID first
        if ($apiId) {
            $team = Team::where('api_id', $apiId)->first();
            if ($team) {
                return $team;
            }
        }

        // Try to find by name and league
        $team = Team::where('name', $name)
            ->where('league_id', $league->id)
            ->first();

        if ($team) {
            // Update API ID if missing
            if (!$team->api_id && $apiId) {
                $team->update(['api_id' => $apiId]);
            }
            return $team;
        }

        // Create new team if not found
        return Team::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'short_name' => substr($teamData['shortName'] ?? $name, 0, 3),
            'league_id' => $league->id,
            'api_id' => $apiId,
            'logo_url' => $teamData['crest'] ?? null,
        ]);
    }
}