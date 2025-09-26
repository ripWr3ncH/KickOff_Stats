<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\League;
use App\Models\Team;
use App\Models\FootballMatch;
use Carbon\Carbon;

class SyncFootballData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'football:sync {--live : Sync only live matches} {--today : Sync today\'s matches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync football data from external API';

    protected $footballService;

    public function __construct(FootballDataService $footballService)
    {
        parent::__construct();
        $this->footballService = $footballService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting football data synchronization...');

        if ($this->option('live')) {
            $this->syncLiveMatches();
        } elseif ($this->option('today')) {
            $this->syncTodayMatches();
        } else {
            $this->syncAllData();
        }

        $this->info('Football data synchronization completed!');
    }

    protected function syncLiveMatches()
    {
        $this->info('Fetching live matches...');
        
        $data = $this->footballService->getLiveMatches();
        
        if (!$data) {
            $this->error('Failed to fetch live matches');
            return;
        }

        $this->updateMatches($data['matches'] ?? []);
        $this->info('Live matches updated successfully!');
    }

    protected function syncTodayMatches()
    {
        $this->info('Fetching today\'s matches...');
        
        $today = Carbon::today()->format('Y-m-d');
        $data = $this->footballService->getMatchesByDate($today);
        
        if (!$data) {
            $this->error('Failed to fetch today\'s matches');
            return;
        }

        $this->updateMatches($data['matches'] ?? []);
        $this->info('Today\'s matches updated successfully!');
    }

    protected function syncAllData()
    {
        $this->info('Performing full data synchronization...');
        
        // Sync teams for each league
        $leagueMapping = $this->footballService->getLeagueMapping();
        
        foreach ($leagueMapping as $slug => $apiId) {
            $this->info("Syncing data for {$slug}...");
            
            // Get standings (includes team data)
            $standings = $this->footballService->getLeagueStandings($apiId);
            
            if ($standings && isset($standings['standings'][0]['table'])) {
                $this->updateTeamsFromStandings($slug, $standings['standings'][0]['table']);
            }
        }
        
        // Sync recent and upcoming matches
        $this->syncTodayMatches();
    }

    protected function updateMatches($matches)
    {
        $leagueMapping = array_flip($this->footballService->getLeagueMapping());
        
        foreach ($matches as $matchData) {
            $competitionId = $matchData['competition']['id'];
            
            if (!isset($leagueMapping[$competitionId])) {
                continue; // Skip leagues we don't track
            }

            $league = League::where('slug', $leagueMapping[$competitionId])->first();
            
            if (!$league) {
                continue;
            }

            // Find or create teams
            $homeTeam = $this->findOrCreateTeam($matchData['homeTeam'], $league);
            $awayTeam = $this->findOrCreateTeam($matchData['awayTeam'], $league);

            // Update or create match
            $match = FootballMatch::updateOrCreate(
                [
                    'league_id' => $league->id,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'match_date' => Carbon::parse($matchData['utcDate'])
                ],
                [
                    'home_score' => $matchData['score']['fullTime']['home'],
                    'away_score' => $matchData['score']['fullTime']['away'],
                    'status' => $this->mapStatus($matchData['status']),
                    'minute' => $matchData['minute'] ?? null,
                    'matchweek' => $matchData['matchday'] ?? null,
                    'referee' => $matchData['referees'][0]['name'] ?? null
                ]
            );

            $this->line("Updated: {$homeTeam->short_name} vs {$awayTeam->short_name}");
        }
    }

    protected function findOrCreateTeam($teamData, $league)
    {
        return Team::firstOrCreate(
            ['slug' => str($teamData['name'])->slug()],
            [
                'name' => $teamData['name'],
                'short_name' => $teamData['tla'] ?? substr($teamData['name'], 0, 3),
                'league_id' => $league->id,
                'city' => $teamData['venue'] ?? 'Unknown'
            ]
        );
    }

    protected function updateTeamsFromStandings($leagueSlug, $standings)
    {
        $league = League::where('slug', $leagueSlug)->first();
        
        if (!$league) {
            return;
        }

        foreach ($standings as $standing) {
            $teamData = $standing['team'];
            
            Team::updateOrCreate(
                ['slug' => str($teamData['name'])->slug()],
                [
                    'name' => $teamData['name'],
                    'short_name' => $teamData['tla'] ?? substr($teamData['name'], 0, 3),
                    'league_id' => $league->id,
                    'city' => 'Unknown' // API doesn't provide city in standings
                ]
            );
        }
    }

    protected function mapStatus($apiStatus)
    {
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

        return $statusMap[$apiStatus] ?? 'scheduled';
    }
}
