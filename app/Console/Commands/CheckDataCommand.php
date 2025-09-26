<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Models\Team;
use App\Models\FootballMatch;

class CheckDataCommand extends Command
{
    protected $signature = 'data:check';
    protected $description = 'Check current database status and content';

    public function handle()
    {
        $leagues = League::count();
        $teams = Team::count();
        $matches = FootballMatch::count();
        $liveMatches = FootballMatch::where('status', 'live')->count();
        $upcomingMatches = FootballMatch::where('match_date', '>', now())->count();
        $todayMatches = FootballMatch::whereDate('match_date', today())->count();

        $this->info("=== DATABASE STATUS ===");
        $this->line("Leagues: $leagues");
        $this->line("Teams: $teams");
        $this->line("Total Matches: $matches");
        $this->line("Live Matches: $liveMatches");
        $this->line("Today's Matches: $todayMatches");
        $this->line("Upcoming Matches: $upcomingMatches");

        if ($matches > 0) {
            $this->info("\n=== SAMPLE MATCHES ===");
            $sampleMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])->take(3)->get();
            
            foreach ($sampleMatches as $match) {
                $this->line("{$match->homeTeam->name} vs {$match->awayTeam->name}");
                $this->line("League: {$match->league->name}");
                $this->line("Date: {$match->match_date}");
                $this->line("Status: {$match->status}");
                $this->line("---");
            }
        }

        return 0;
    }
}