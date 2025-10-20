<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;

class RemoveSeedBarcelona extends Command
{
    protected $signature = 'teams:remove-seed-barcelona';
    protected $description = 'Remove the seed Barcelona (not FC Barcelona from API)';

    public function handle()
    {
        $this->info('=== Removing Seed Barcelona ===');
        $this->info('');
        
        // Find the seed Barcelona (the older one)
        $seed = Team::where('slug', 'barcelona')->first();
        
        if (!$seed) {
            $this->error('Seed Barcelona not found!');
            return 1;
        }
        
        $this->info("Found: {$seed->name} (Created: {$seed->created_at})");
        $this->info("Slug: {$seed->slug}");
        $this->info("City: {$seed->city}");
        $this->info('');
        
        // Check for favorites
        $favCount = \DB::table('user_favorite_teams')->where('team_id', $seed->id)->count();
        
        if ($favCount > 0) {
            $this->error("This team has {$favCount} user(s) who favorited it!");
            $this->info('');
            $this->info("You need to:");
            $this->info("1. Remove it from your favorites (My Teams page)");
            $this->info("2. Add 'FC-Barcelona' to your favorites instead");
            $this->info("3. Then run this command again");
            return 1;
        }
        
        // Delete
        $seed->delete();
        $this->info('');
        $this->info('✅ Seed Barcelona deleted successfully!');
        $this->info('');
        $this->info('The API team "FC-Barcelona" remains in the database with logo.');
        
        return 0;
    }
}
