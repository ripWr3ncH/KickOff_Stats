# Migrations Directory

Migrations are version control for your database. They create tables and define structure.

## 📄 Migration Files

### **Execution Order:**
Migrations run in chronological order based on file name timestamps.

---

## 📋 Migration List

### **1. 0001_01_01_000000_create_users_table.php** (Built-in Laravel)
- **Purpose:** Create users table for authentication
- **Table:** `users`
- **Columns:**
  - `id` - Primary key (auto-increment)
  - `name` - User's full name (string)
  - `email` - User's email (unique, string)
  - `email_verified_at` - Email verification timestamp (nullable)
  - `password` - Hashed password (string)
  - `remember_token` - "Remember me" cookie token (string, 100 chars)
  - `created_at` - Record creation timestamp
  - `updated_at` - Record update timestamp
- **Indexes:**
  - Unique index on `email`
- **Used By:** AuthController, User model
- **Records:** 0 (no users in database yet)

---

### **2. 0001_01_01_000001_create_cache_table.php** (Built-in Laravel)
- **Purpose:** Create cache storage table
- **Table:** `cache`
- **Columns:**
  - `key` - Cache key (string, primary key)
  - `value` - Cached data (mediumText)
  - `expiration` - Cache expiry timestamp (integer)
- **Used For:** Application caching (sessions, views, routes)
- **Automatic:** Laravel manages this table

---

### **3. 0001_01_01_000002_create_jobs_table.php** (Built-in Laravel)
- **Purpose:** Create queue jobs table
- **Table:** `jobs`
- **Columns:**
  - `id` - Primary key (auto-increment)
  - `queue` - Queue name (string, indexed)
  - `payload` - Job data (longText)
  - `attempts` - Number of attempts (unsignedTinyInteger)
  - `reserved_at` - When job reserved (unsignedInteger, nullable)
  - `available_at` - When job available (unsignedInteger)
  - `created_at` - When job created (unsignedInteger)
- **Used For:** Background job processing
- **Records:** 0 (no queued jobs currently)

---

### **4. 2025_09_12_142628_create_leagues_table.php** ⭐
- **Purpose:** Store football leagues (competitions)
- **Table:** `leagues`
- **Columns:**
  - `id` - Primary key (auto-increment)
  - `api_league_id` - API Football league ID (integer, nullable)
  - `name` - League name (string) - e.g., "Premier League"
  - `country` - League country (string) - e.g., "England"
  - `logo` - League logo URL (string, nullable)
  - `season` - Current season (integer, default: 2024)
  - `created_at` - Record creation timestamp
  - `updated_at` - Record update timestamp
- **Records:** 5 leagues (Premier League, La Liga, Serie A, Bundesliga, Ligue 1)
- **Used By:** League model, LeagueController, LeagueSeeder

---

### **5. 2025_09_12_143742_create_teams_table.php** ⭐
- **Purpose:** Store football teams
- **Table:** `teams`
- **Columns:**
  - `id` - Primary key (auto-increment)
  - `api_team_id` - API Football team ID (integer, nullable)
  - `league_id` - Foreign key to leagues table (unsignedBigInteger)
  - `name` - Team name (string) - e.g., "Manchester United"
  - `code` - Team code (string, nullable) - e.g., "MUN"
  - `country` - Team country (string, nullable)
  - `founded` - Team founded year (integer, nullable) - e.g., 1878
  - `logo` - Team logo URL (string, nullable)
  - `venue_name` - Stadium name (string, nullable) - e.g., "Old Trafford"
  - `venue_city` - Stadium city (string, nullable)
  - `venue_capacity` - Stadium capacity (integer, nullable)
  - `created_at` - Record creation timestamp
  - `updated_at` - Record update timestamp
- **Foreign Key:** `league_id` references `leagues.id` (onDelete: cascade)
- **Records:** 61 teams (51 with API data, 10 placeholders)
- **Used By:** Team model, TeamController, TeamSeeder

---

### **6. 2025_09_12_143937_create_players_table.php** ⭐
- **Purpose:** Store football players
- **Table:** `players`
- **Columns:**
  - `id` - Primary key (auto-increment)
  - `api_player_id` - API Football player ID (integer, nullable)
  - `team_id` - Foreign key to teams table (unsignedBigInteger)
  - `name` - Player name (string) - e.g., "Bruno Fernandes"
  - `age` - Player age (integer, nullable)
  - `number` - Jersey number (integer, nullable) - e.g., 8
  - `position` - Player position (string, nullable) - e.g., "Midfielder"
  - `photo` - Player photo URL (string, nullable)
  - `nationality` - Player nationality (string, nullable)
  - `height` - Player height (string, nullable) - e.g., "179 cm"
  - `weight` - Player weight (string, nullable) - e.g., "69 kg"
  - `birth_date` - Player birth date (date, nullable)
  - `birth_place` - Player birth place (string, nullable)
  - `market_value` - Player market value (decimal, nullable) - e.g., 75000000.00
  - `created_at` - Record creation timestamp
  - `updated_at` - Record update timestamp
- **Foreign Key:** `team_id` references `teams.id` (onDelete: cascade)
- **Records:** 1,525 players
- **Used By:** Player model, PlayerController, PlayerSeeder

---

### **7. 2025_09_12_144017_create_football_matches_table.php** ⭐
- **Purpose:** Store football matches (fixtures)
- **Table:** `football_matches`
- **Columns:**
  - `id` - Primary key (auto-increment)
  - `api_fixture_id` - API Football fixture ID (integer, nullable)
  - `league_id` - Foreign key to leagues table (unsignedBigInteger)
  - `home_team_id` - Foreign key to teams table (unsignedBigInteger)
  - `away_team_id` - Foreign key to teams table (unsignedBigInteger)
  - `match_date` - Match date and time (dateTime)
  - `status` - Match status (string) - e.g., "scheduled", "live", "finished"
  - `home_score` - Home team score (integer, nullable, default: 0)
  - `away_score` - Away team score (integer, nullable, default: 0)
  - `venue` - Match venue (string, nullable)
  - `referee` - Match referee (string, nullable)
  - `round` - League round (string, nullable) - e.g., "Regular Season - 15"
  - `season` - Season year (integer, default: 2024)
  - `created_at` - Record creation timestamp
  - `updated_at` - Record update timestamp
- **Foreign Keys:**
  - `league_id` references `leagues.id` (onDelete: cascade)
  - `home_team_id` references `teams.id` (onDelete: cascade)
  - `away_team_id` references `teams.id` (onDelete: cascade)
- **Records:** 128 matches
- **Used By:** FootballMatch model, MatchController, match import commands

---

### **8. 2025_09_12_144026_create_player_stats_table.php** ⭐
- **Purpose:** Store player statistics per match
- **Table:** `player_stats`
- **Columns:**
  - `id` - Primary key (auto-increment)
  - `match_id` - Foreign key to football_matches table (unsignedBigInteger)
  - `player_id` - Foreign key to players table (unsignedBigInteger)
  - `team_id` - Foreign key to teams table (unsignedBigInteger)
  - `minutes_played` - Minutes played (integer, default: 0)
  - `goals` - Goals scored (integer, default: 0)
  - `assists` - Assists made (integer, default: 0)
  - `yellow_cards` - Yellow cards received (integer, default: 0)
  - `red_cards` - Red cards received (integer, default: 0)
  - `saves` - Goalkeeper saves (integer, default: 0)
  - `shots_on_target` - Shots on target (integer, default: 0)
  - `rating` - Player rating (decimal, nullable) - e.g., 7.5
  - `created_at` - Record creation timestamp
  - `updated_at` - Record update timestamp
- **Foreign Keys:**
  - `match_id` references `football_matches.id` (onDelete: cascade)
  - `player_id` references `players.id` (onDelete: cascade)
  - `team_id` references `teams.id` (onDelete: cascade)
- **Records:** 0 (no stats yet - would be populated from API)
- **Used By:** PlayerStat model, MatchController@show

---

### **9. create_dream_teams_table.php** ⭐ (Assumed - not in list but exists)
- **Purpose:** Store user-created fantasy teams
- **Table:** `dream_teams`
- **Columns:**
  - `id` - Primary key (auto-increment)
  - `user_id` - Foreign key to users table (unsignedBigInteger)
  - `name` - Team name (string) - e.g., "My Ultimate XI"
  - `formation` - Formation (string) - e.g., "4-3-3"
  - `players` - Players JSON (json) - e.g., {"GK": {...}, "DEF1": {...}}
  - `description` - Team description (text, nullable)
  - `is_public` - Public/Private (boolean, default: true)
  - `total_value` - Total market value (decimal, nullable)
  - `created_at` - Record creation timestamp
  - `updated_at` - Record update timestamp
- **Foreign Key:** `user_id` references `users.id` (onDelete: cascade)
- **Used By:** DreamTeam model, DreamTeamController

---

## 🔄 Running Migrations

### **Run All Migrations**
```bash
php artisan migrate
```
- Creates all tables in correct order
- Tracks which migrations ran in `migrations` table

### **Rollback Last Migration**
```bash
php artisan migrate:rollback
```
- Reverts last batch of migrations

### **Rollback All Migrations**
```bash
php artisan migrate:reset
```
- Drops all tables

### **Fresh Migration (Drop + Migrate)**
```bash
php artisan migrate:fresh
```
- Drops all tables and re-runs migrations
- **Warning:** Deletes all data!

### **Fresh Migration + Seed**
```bash
php artisan migrate:fresh --seed
```
- Drops tables, re-runs migrations, runs seeders
- **Perfect for:** Resetting database with fresh data

### **Check Migration Status**
```bash
php artisan migrate:status
```
- Shows which migrations ran

---

## 📊 Database Schema Summary

| Table | Records | Purpose |
|-------|---------|---------|
| `users` | 0 | User accounts |
| `cache` | - | Laravel cache |
| `jobs` | 0 | Queue jobs |
| `leagues` | 5 | Football competitions |
| `teams` | 61 | Football teams |
| `players` | 1,525 | Football players |
| `football_matches` | 128 | Match fixtures |
| `player_stats` | 0 | Player match stats |
| `dream_teams` | ? | User fantasy teams |

**Total Records:** 1,714+ (excluding cache/jobs)

---

## 🔗 Relationships

```
leagues (5)
└── teams (61)
    ├── players (1,525)
    └── football_matches (128)
        └── player_stats (0)

users (0)
└── dream_teams (?)
```

---

## 💡 Key Points for Understanding

1. **Migrations = Database version control** - Track changes over time
2. **Timestamps in filename** - Determines execution order
3. **up() method** - Creates table/columns
4. **down() method** - Reverses migration (drops table)
5. **Foreign keys** - Create relationships between tables
6. **onDelete('cascade')** - Delete related records automatically
7. **migrate:fresh --seed** - Complete database reset with data
8. **migrations table** - Laravel tracks which migrations ran

---

## 🛠️ Creating New Migration

### **Command:**
```bash
php artisan make:migration create_news_table
```

### **Generated File:**
```php
public function up()
{
    Schema::create('news', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('news');
}
```

---

**Related Directories:**
- 📁 [`database/seeders/`](../seeders/) - Populate tables with data
- 📁 [`app/Models/`](../../app/Models/) - Interact with tables
- 📁 [`config/database.php`](../../config/database.php) - Database configuration
- 📄 [Laravel Migrations Docs](https://laravel.com/docs/11.x/migrations)
