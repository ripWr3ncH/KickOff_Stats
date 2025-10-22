# Console Commands Directory

Console commands are CLI scripts for background tasks, data imports, and maintenance. Run from terminal.

## 📄 Command Files

### **1. ImportRealMatches.php** ⭐
- **Purpose:** Import real match fixtures from API-Football
- **Signature:** `app:import-real-matches`
- **Description:** "Import real football match data from API"

**What it does:**
1. Connects to FootballDataService
2. Fetches fixtures from API-Football for specific leagues
3. Imports/updates matches in `football_matches` table
4. Includes match date, teams, scores, status, venue, referee

**Run Command:**
```bash
php artisan app:import-real-matches
```

**Example Output:**
```
Importing matches...
✓ Imported Manchester United vs Liverpool (2024-12-20)
✓ Imported Chelsea vs Arsenal (2024-12-21)
✓ Updated Barcelona vs Real Madrid (2024-12-22)
Import complete: 50 matches imported/updated
```

**Code Structure:**
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballDataService;
use App\Models\FootballMatch;

class ImportRealMatches extends Command
{
    protected $signature = 'app:import-real-matches';
    protected $description = 'Import real football match data from API';
    
    public function handle(FootballDataService $service)
    {
        $this->info('Importing matches...');
        
        $leagues = [39, 140, 135, 78, 61]; // Premier League, La Liga, etc.
        
        foreach ($leagues as $leagueId) {
            $fixtures = $service->getFixtures($leagueId);
            
            foreach ($fixtures as $fixtureData) {
                FootballMatch::updateOrCreate(
                    ['api_fixture_id' => $fixtureData['fixture']['id']],
                    [
                        'league_id' => $leagueId,
                        'home_team_id' => $this->getTeamId($fixtureData['teams']['home']['id']),
                        'away_team_id' => $this->getTeamId($fixtureData['teams']['away']['id']),
                        'match_date' => $fixtureData['fixture']['date'],
                        'status' => $fixtureData['fixture']['status']['short'],
                        'home_score' => $fixtureData['goals']['home'] ?? 0,
                        'away_score' => $fixtureData['goals']['away'] ?? 0,
                        'venue' => $fixtureData['fixture']['venue']['name'],
                        'referee' => $fixtureData['fixture']['referee'],
                    ]
                );
                
                $this->line("✓ Imported {$fixtureData['teams']['home']['name']} vs {$fixtureData['teams']['away']['name']}");
            }
        }
        
        $this->info('Import complete!');
    }
}
```

**When to run:**
- Daily (to get new fixtures)
- Before match days
- After matches finish (to update scores)

**Scheduling (Optional):**
```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('app:import-real-matches')->daily();
}
```

---

### **2. SyncHistoricalMatches.php** ⭐
- **Purpose:** Sync historical match data (past matches with results)
- **Signature:** `app:sync-historical-matches`
- **Description:** "Sync historical match data from previous seasons"

**What it does:**
1. Fetches completed matches from past seasons
2. Updates match results (scores, status)
3. Imports player statistics if available
4. Updates team standings

**Run Command:**
```bash
php artisan app:sync-historical-matches
```

**Example Output:**
```
Syncing historical matches...
✓ Synced 2023-2024 season: 380 matches
✓ Updated scores and statistics
✓ Imported player stats: 1,234 records
Sync complete!
```

**Code Structure:**
```php
protected $signature = 'app:sync-historical-matches 
                        {--season=2023 : Season year}
                        {--league= : League ID}';
protected $description = 'Sync historical match data from previous seasons';

public function handle(FootballDataService $service)
{
    $season = $this->option('season') ?? 2023;
    $leagueId = $this->option('league');
    
    $this->info("Syncing historical matches for season {$season}...");
    
    if ($leagueId) {
        $this->syncLeague($leagueId, $season, $service);
    } else {
        $leagues = [39, 140, 135, 78, 61];
        foreach ($leagues as $leagueId) {
            $this->syncLeague($leagueId, $season, $service);
        }
    }
    
    $this->info('Sync complete!');
}

private function syncLeague($leagueId, $season, $service)
{
    $fixtures = $service->getFixtures($leagueId, $season);
    
    foreach ($fixtures as $fixtureData) {
        // Update match
        // Import player stats
        // Update standings
    }
}
```

**Command Options:**
```bash
# Sync specific season
php artisan app:sync-historical-matches --season=2022

# Sync specific league
php artisan app:sync-historical-matches --league=39

# Both
php artisan app:sync-historical-matches --season=2022 --league=39
```

---

## 🎯 Command Structure

### **Parts of a Command:**

```php
class ImportRealMatches extends Command
{
    // 1. Signature - Command name and options
    protected $signature = 'app:import-real-matches 
                            {league? : League ID (optional)}
                            {--force : Force import}';
    
    // 2. Description - Shown in php artisan list
    protected $description = 'Import real football match data from API';
    
    // 3. Constructor - Inject dependencies
    public function __construct()
    {
        parent::__construct();
    }
    
    // 4. Handle - Main logic
    public function handle(FootballDataService $service)
    {
        // Command logic here
        
        return Command::SUCCESS;  // or Command::FAILURE
    }
}
```

---

## 📝 Command Arguments vs Options

### **Arguments** (Required or Optional)
```php
protected $signature = 'app:sync {league} {season?}';
```
- `{league}` - Required argument
- `{season?}` - Optional argument (? makes it optional)

**Usage:**
```bash
php artisan app:sync 39
php artisan app:sync 39 2023
```

### **Options** (Always Optional)
```php
protected $signature = 'app:sync {--force} {--league=}';
```
- `{--force}` - Boolean flag (true/false)
- `{--league=}` - Value option

**Usage:**
```bash
php artisan app:sync --force
php artisan app:sync --league=39
php artisan app:sync --force --league=39
```

---

## 🖥️ Output Methods

### **Display Messages**
```php
$this->info('Success message');      // Green text
$this->error('Error message');       // Red text
$this->warn('Warning message');      // Yellow text
$this->line('Normal text');          // White text
$this->comment('Comment');           // Gray text
```

### **Progress Bar**
```php
$bar = $this->output->createProgressBar(count($matches));

foreach ($matches as $match) {
    // Process match
    $bar->advance();
}

$bar->finish();
```

### **Tables**
```php
$this->table(
    ['League', 'Matches', 'Status'],
    [
        ['Premier League', 380, 'Complete'],
        ['La Liga', 380, 'Complete'],
    ]
);
```

### **Ask User**
```php
$name = $this->ask('What is your name?');
$confirm = $this->confirm('Do you wish to continue?');
$choice = $this->choice('Which league?', ['Premier League', 'La Liga']);
```

---

## ⏰ Scheduling Commands

### **In app/Console/Kernel.php:**
```php
protected function schedule(Schedule $schedule)
{
    // Run daily at midnight
    $schedule->command('app:import-real-matches')->daily();
    
    // Run every hour
    $schedule->command('app:sync-live-scores')->hourly();
    
    // Run weekly on Monday at 1:00 AM
    $schedule->command('app:sync-historical-matches')->weeklyOn(1, '1:00');
    
    // Run on specific days
    $schedule->command('app:backup-database')->weekdays();
    
    // Custom cron expression
    $schedule->command('app:cleanup')->cron('0 */6 * * *');  // Every 6 hours
}
```

### **Start Scheduler (Required):**
```bash
# Add to crontab (Linux/Mac)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

# Or run manually for testing
php artisan schedule:work
```

---

## 🔍 Listing All Commands

### **View Available Commands:**
```bash
php artisan list
```

**Output:**
```
Available commands:
  app:import-real-matches       Import real football match data from API
  app:sync-historical-matches   Sync historical match data from previous seasons
  ...
```

### **Get Command Help:**
```bash
php artisan help app:import-real-matches
```

---

## 🛠️ Creating New Command

### **Generate Command:**
```bash
php artisan make:command ImportPlayerStats
```

**Generated File: `app/Console/Commands/ImportPlayerStats.php`**
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportPlayerStats extends Command
{
    protected $signature = 'app:import-player-stats';
    protected $description = 'Import player statistics from API';
    
    public function handle()
    {
        $this->info('Importing player stats...');
        
        // Your logic here
        
        $this->info('Import complete!');
        
        return Command::SUCCESS;
    }
}
```

---

## 🚀 Running Commands

### **Basic Execution:**
```bash
php artisan app:import-real-matches
```

### **With Arguments:**
```bash
php artisan app:sync-historical-matches 39 2023
```

### **With Options:**
```bash
php artisan app:import-real-matches --force
```

### **Background Execution:**
```bash
# Linux/Mac
php artisan app:import-real-matches > /dev/null 2>&1 &

# Windows (PowerShell)
Start-Process php -ArgumentList "artisan app:import-real-matches" -WindowStyle Hidden
```

---

## 💡 Key Points for Understanding

1. **Console commands = CLI scripts** - Run from terminal
2. **Signature** - Command name and options
3. **Handle method** - Main logic (like controller method)
4. **Dependency injection** - Inject services into handle()
5. **Output methods** - info(), error(), line(), table()
6. **Arguments** - Required/optional parameters
7. **Options** - Always optional flags (--force, --league=39)
8. **Scheduling** - Automate with Laravel scheduler
9. **Return codes** - Command::SUCCESS or Command::FAILURE

---

## 🎓 Common Use Cases

### **Data Import**
- Import matches from API
- Import player stats
- Sync team data

### **Maintenance**
- Clean old records
- Update cached data
- Generate reports

### **Background Jobs**
- Send email notifications
- Generate PDFs
- Process large datasets

### **Testing**
- Seed test data
- Reset database
- Run automated tests

---

## 📊 Command vs Seeder

| Feature | Command | Seeder |
|---------|---------|--------|
| **Purpose** | Ongoing data updates | Initial data population |
| **Run From** | Terminal (`php artisan`) | Migration (`--seed`) |
| **Frequency** | Can run anytime | Usually once |
| **Output** | Progress messages | Silent |
| **Scheduling** | Can be scheduled | No scheduling |

**Example:**
- **Seeder:** Populate initial 5 leagues (once)
- **Command:** Import new matches daily (recurring)

---

**Related Directories:**
- 📁 [`app/Services/`](../../Services/) - FootballDataService used in commands
- 📁 [`database/seeders/`](../../../database/seeders/) - Similar data import logic
- 📁 [`app/Models/`](../../Models/) - Models updated by commands
- 📁 [`app/Console/Kernel.php`](../Kernel.php) - Command scheduling
- 📄 [Laravel Artisan Docs](https://laravel.com/docs/11.x/artisan)
