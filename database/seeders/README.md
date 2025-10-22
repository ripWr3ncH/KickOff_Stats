# Seeders Directory

Seeders populate database tables with initial or test data. They run after migrations.

## 📄 Seeder Files

### **1. DatabaseSeeder.php** ⭐ (Main Seeder)
- **Purpose:** Orchestrate all seeders (main entry point)
- **Location:** `database/seeders/DatabaseSeeder.php`

**Code:**
```php
public function run()
{
    $this->call([
        LeagueSeeder::class,
        TeamSeeder::class,
        PlayerSeeder::class,
    ]);
}
```

**How it works:**
1. Calls `LeagueSeeder` → Creates 5 leagues
2. Calls `TeamSeeder` → Creates 61 teams
3. Calls `PlayerSeeder` → Creates 1,525 players (optional, commented out)

**Run Command:**
```bash
php artisan db:seed
```

---

### **2. LeagueSeeder.php** ⭐
- **Purpose:** Populate leagues table with 5 major European leagues
- **Table:** `leagues`
- **Records Created:** 5
- **Data Source:** FootballDataService API

**What it does:**
1. Calls `FootballDataService->getLeagues()`
2. Fetches leagues from API-Football
3. Filters for Premier League, La Liga, Serie A, Bundesliga, Ligue 1
4. Inserts/updates leagues in database

**Created Leagues:**
- **Premier League** (England) - ID: 39
- **La Liga** (Spain) - ID: 140
- **Serie A** (Italy) - ID: 135
- **Bundesliga** (Germany) - ID: 78
- **Ligue 1** (France) - ID: 61

**Code Pattern:**
```php
use App\Services\FootballDataService;
use App\Models\League;

public function run(FootballDataService $service)
{
    $leagues = $service->getLeagues();
    
    foreach ($leagues as $leagueData) {
        League::updateOrCreate(
            ['api_league_id' => $leagueData['league']['id']],
            [
                'name' => $leagueData['league']['name'],
                'country' => $leagueData['country']['name'],
                'logo' => $leagueData['league']['logo'],
            ]
        );
    }
}
```

**Run Command:**
```bash
php artisan db:seed --class=LeagueSeeder
```

---

### **3. TeamSeeder.php** ⭐
- **Purpose:** Populate teams table with teams from 5 leagues
- **Table:** `teams`
- **Records Created:** 61 teams (51 with API data + 10 placeholders)
- **Data Source:** FootballDataService API

**What it does:**
1. Gets all leagues from database
2. For each league:
   - Calls `FootballDataService->getTeamsByLeague($leagueId)`
   - Fetches teams from API-Football
   - Inserts/updates teams in database
3. Creates 10 placeholder teams if needed

**Team Data Stored:**
- Team name
- Team code (e.g., "MUN")
- Country
- Founded year
- Logo URL
- Venue (stadium) name
- Venue city
- Venue capacity

**Example Teams:**
- **Premier League:** Manchester United, Liverpool, Arsenal, Chelsea, etc.
- **La Liga:** Real Madrid, Barcelona, Atletico Madrid, etc.
- **Serie A:** Juventus, AC Milan, Inter Milan, etc.
- **Bundesliga:** Bayern Munich, Borussia Dortmund, etc.
- **Ligue 1:** PSG, Monaco, Lyon, etc.

**Code Pattern:**
```php
public function run(FootballDataService $service)
{
    $leagues = League::all();
    
    foreach ($leagues as $league) {
        $teams = $service->getTeamsByLeague($league->api_league_id);
        
        foreach ($teams as $teamData) {
            Team::updateOrCreate(
                ['api_team_id' => $teamData['team']['id']],
                [
                    'league_id' => $league->id,
                    'name' => $teamData['team']['name'],
                    'logo' => $teamData['team']['logo'],
                    'venue_name' => $teamData['venue']['name'],
                    // ... more fields
                ]
            );
        }
    }
    
    // Create placeholder teams
    for ($i = 1; $i <= 10; $i++) {
        Team::create([
            'name' => "Team $i",
            'league_id' => 1,
        ]);
    }
}
```

**Run Command:**
```bash
php artisan db:seed --class=TeamSeeder
```

---

### **4. PlayerSeeder.php** ⭐ (Optional)
- **Purpose:** Populate players table with squad data
- **Table:** `players`
- **Records Created:** 1,525 players
- **Data Source:** FootballDataService API
- **Status:** **Commented out in DatabaseSeeder** (takes long time)

**What it does:**
1. Gets all teams from database
2. For each team:
   - Calls `FootballDataService->getPlayersByTeam($teamId)`
   - Fetches squad from API-Football
   - Inserts/updates players in database

**Player Data Stored:**
- Player name
- Age
- Jersey number
- Position (GK, DEF, MID, FWD)
- Photo URL
- Nationality
- Height (e.g., "179 cm")
- Weight (e.g., "69 kg")
- Birth date
- Birth place
- Market value (e.g., 75000000.00)

**Example Players:**
- Bruno Fernandes (Manchester United, #8, MID)
- Mohamed Salah (Liverpool, #11, FWD)
- Kevin De Bruyne (Manchester City, #17, MID)
- Erling Haaland (Manchester City, #9, FWD)

**Code Pattern:**
```php
public function run(FootballDataService $service)
{
    $teams = Team::whereNotNull('api_team_id')->get();
    
    foreach ($teams as $team) {
        $players = $service->getPlayersByTeam($team->api_team_id);
        
        foreach ($players as $playerData) {
            Player::updateOrCreate(
                ['api_player_id' => $playerData['id']],
                [
                    'team_id' => $team->id,
                    'name' => $playerData['name'],
                    'age' => $playerData['age'],
                    'position' => $playerData['position'],
                    // ... more fields
                ]
            );
        }
    }
}
```

**Run Command:**
```bash
php artisan db:seed --class=PlayerSeeder
```

**Warning:** Takes 5-10 minutes due to API rate limits!

---

## 🔄 Running Seeders

### **Run All Seeders**
```bash
php artisan db:seed
```
- Runs `DatabaseSeeder->run()`
- Calls all seeders in order

### **Run Specific Seeder**
```bash
php artisan db:seed --class=LeagueSeeder
php artisan db:seed --class=TeamSeeder
php artisan db:seed --class=PlayerSeeder
```

### **Fresh Migration + Seed**
```bash
php artisan migrate:fresh --seed
```
- Drops all tables
- Re-runs migrations
- Runs all seeders
- **Perfect for:** Complete database reset

### **Seeder with Force (Production)**
```bash
php artisan db:seed --force
```
- Forces seeding in production environment

---

## 📊 Seeding Results

After running seeders:

| Table | Records | Source |
|-------|---------|--------|
| `leagues` | 5 | API-Football |
| `teams` | 61 | API-Football (51) + Manual (10) |
| `players` | 1,525 | API-Football |

**Total:** 1,591 records

---

## 🎯 Seeder Order (Important!)

Seeders MUST run in this order due to foreign key relationships:

1. **LeagueSeeder** first → Creates leagues
2. **TeamSeeder** second → Requires leagues to exist
3. **PlayerSeeder** third → Requires teams to exist

**Why?**
- Teams have `league_id` foreign key (needs leagues first)
- Players have `team_id` foreign key (needs teams first)

---

## 🔧 updateOrCreate() Method

**Purpose:** Insert if not exists, update if exists

**Pattern:**
```php
League::updateOrCreate(
    ['api_league_id' => 39],  // Search condition
    [                         // Data to insert/update
        'name' => 'Premier League',
        'country' => 'England',
    ]
);
```

**Benefits:**
1. **No duplicates** - Prevents duplicate API data
2. **Rerunnable** - Can run seeder multiple times
3. **Updates existing** - Refreshes data from API

---

## 🚨 Common Issues

### **Issue 1: Foreign Key Error**
```
SQLSTATE[23000]: Integrity constraint violation
```
**Cause:** Seeders ran out of order
**Solution:** Run `php artisan migrate:fresh --seed`

### **Issue 2: API Rate Limit**
```
429 Too Many Requests
```
**Cause:** Exceeded API-Football rate limit
**Solution:** Wait or upgrade API subscription

### **Issue 3: PlayerSeeder Takes Too Long**
**Cause:** 51 API requests for 51 teams
**Solution:** Comment out PlayerSeeder in DatabaseSeeder (already done)

---

## 💡 Key Points for Understanding

1. **DatabaseSeeder** - Main entry point (calls other seeders)
2. **Order matters** - Leagues → Teams → Players (foreign keys)
3. **updateOrCreate()** - Prevents duplicates, allows re-running
4. **API-Football** - Data source for all seeders
5. **FootballDataService** - Service injected into seeders
6. **Dependency injection** - `public function run(FootballDataService $service)`
7. **PlayerSeeder commented out** - Too slow (5-10 minutes)
8. **migrate:fresh --seed** - Complete reset with data

---

## 🛠️ Creating New Seeder

### **Command:**
```bash
php artisan make:seeder NewsSeeder
```

### **Generated File:**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run()
    {
        // Insert data here
    }
}
```

### **Register in DatabaseSeeder:**
```php
public function run()
{
    $this->call([
        LeagueSeeder::class,
        TeamSeeder::class,
        NewsSeeder::class,  // Add here
    ]);
}
```

---

## 📈 Seeding Statistics

**LeagueSeeder:**
- Time: ~2 seconds
- API Requests: 1
- Records: 5

**TeamSeeder:**
- Time: ~10 seconds
- API Requests: 5 (one per league)
- Records: 61

**PlayerSeeder:**
- Time: ~5-10 minutes
- API Requests: 51 (one per team)
- Records: 1,525

**Total Time:** ~10 minutes (if PlayerSeeder included)

---

**Related Directories:**
- 📁 [`database/migrations/`](../migrations/) - Create tables first
- 📁 [`app/Services/`](../../app/Services/) - FootballDataService
- 📁 [`app/Models/`](../../app/Models/) - League, Team, Player models
- 📁 [`app/Console/Commands/`](../../app/Console/Commands/) - Import commands (similar process)
- 📄 [Laravel Seeding Docs](https://laravel.com/docs/11.x/seeding)
