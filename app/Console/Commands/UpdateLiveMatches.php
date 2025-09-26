<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use App\Services\FootballDataService;
use App\Http\Controllers\Api\LiveScoreController;

class UpdateLiveMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:update-live {--demo : Create demo live matches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update live match scores and status from Football Data API or create demo data';

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
        if ($this->option('demo')) {
            return $this->createDemoLiveMatches();
        }

        $this->info('Fetching live matches from Football Data API...');

        try {
            // Use the LiveScoreController update method
            $controller = new LiveScoreController($this->footballService);
            $response = $controller->update();
            $data = $response->getData(true);

            if (isset($data['updated'])) {
                $this->info("Updated {$data['updated']} live matches successfully!");
                
                if ($data['updated'] > 0) {
                    $this->table(
                        ['Match ID', 'Score', 'Minute', 'Status'],
                        collect($data['matches'])->map(function ($match) {
                            return [
                                $match['id'],
                                $match['home_score'] . ' - ' . $match['away_score'],
                                $match['minute'] ?? '-',
                                $match['status']
                            ];
                        })->toArray()
                    );
                }
            } else {
                $this->warn('No live match updates available from API.');
            }

        } catch (\Exception $e) {
            $this->error('Failed to update live matches: ' . $e->getMessage());
            
            // Offer to create demo data
            if ($this->confirm('Would you like to create demo live matches instead?')) {
                return $this->createDemoLiveMatches();
            }
        }
    }

    private function createDemoLiveMatches()
    {
        $this->info('Creating demo live matches...');

        // Get some scheduled matches to convert to live
        $scheduledMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->where('status', 'scheduled')
            ->whereDate('match_date', today())
            ->limit(3)
            ->get();

        if ($scheduledMatches->count() === 0) {
            $this->warn('No scheduled matches found for today. Getting any recent matches...');
            
            $scheduledMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
                ->where('status', 'scheduled')
                ->limit(3)
                ->get();
        }

        if ($scheduledMatches->count() === 0) {
            $this->error('No matches available to convert to live demos.');
            return;
        }

        $updatedCount = 0;

        foreach ($scheduledMatches as $match) {
            $match->update([
                'status' => 'live',
                'home_score' => rand(0, 3),
                'away_score' => rand(0, 3),
                'minute' => rand(15, 85)
            ]);

            $this->line("✓ {$match->homeTeam->short_name} vs {$match->awayTeam->short_name} - {$match->home_score}:{$match->away_score} ({$match->minute}')");
            $updatedCount++;
        }

        $this->info("Created {$updatedCount} demo live matches successfully!");
        $this->comment('You can now view live matches on your dashboard.');
        $this->comment('Run "php artisan matches:update-live" without --demo to update from real API data.');
    }
}
