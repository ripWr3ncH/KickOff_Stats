# Models Directory

Models represent database tables and handle data relationships, business logic, and queries.

## 📂 Files in this Directory

### **User.php**
- **Table:** `users`
- **Purpose:** User accounts and authentication
- **Columns:**
  - `id` - Primary key
  - `name` - User's full name
  - `email` - Email address (unique)
  - `password` - Hashed password (bcrypt)
  - `remember_token` - For "Remember Me" functionality
- **Relationships:**
  - `dreamTeams()` - User's fantasy teams (one-to-many)
- **Features:**
  - Password hashing (automatic)
  - Authentication
  - Email verification support
- **Used By:** Authentication system, DreamTeamController

---

### **League.php**
- **Table:** `leagues`
- **Purpose:** Football competitions (Premier League, La Liga, etc.)
- **Columns:**
  - `id` - Primary key
  - `name` - League name (e.g., "Premier League")
  - `country` - Country (e.g., "England")
  - `logo_url` - League logo URL
  - `api_id` - Football Data API ID
- **Relationships:**
  - `teams()` - Teams in this league (one-to-many)
  - `matches()` - Matches in this league (one-to-many)
- **Total Records:** 5 leagues
- **Used By:** LeagueController, HomeController

---

### **Team.php**
- **Table:** `teams`
- **Purpose:** Football teams
- **Columns:**
  - `id` - Primary key
  - `name` - Team name (e.g., "Barcelona")
  - `logo_url` - Team crest URL (from Football Data API)
  - `api_id` - Football Data API ID
  - `league_id` - Foreign key to leagues table
- **Relationships:**
  - `league()` - Belongs to a league (many-to-one)
  - `players()` - Team's players (one-to-many)
  - `homeMatches()` - Matches where team is home (one-to-many)
  - `awayMatches()` - Matches where team is away (one-to-many)
  - `allMatches()` - All matches (home + away)
- **Total Records:** 61 teams (51 with real API data)
- **Used By:** MatchController, LeagueController, PlayerController

---

### **Player.php**
- **Table:** `players`
- **Purpose:** Football players with details and stats
- **Columns:**
  - `id` - Primary key
  - `name` - Player name
  - `slug` - URL-friendly name
  - `team_id` - Foreign key to teams table
  - `position` - Position (GK, DEF, MID, FWD)
  - `jersey_number` - Shirt number
  - `nationality` - Player nationality
  - `date_of_birth` - Birth date
  - `height` - Height in cm
  - `weight` - Weight in kg
  - `market_value` - Market value in dollars
  - `photo_url` - Player photo URL
  - `bio` - Biography
- **Relationships:**
  - `team()` - Belongs to a team (many-to-one)
  - `stats()` - Player statistics (one-to-many)
- **Total Records:** 1,525 players (25 per team)
- **Used By:** PlayerController, DreamTeamController

---

### **FootballMatch.php**
- **Table:** `football_matches`
- **Purpose:** Match data and scores
- **Columns:**
  - `id` - Primary key
  - `league_id` - Foreign key to leagues table
  - `home_team_id` - Foreign key to teams table
  - `away_team_id` - Foreign key to teams table
  - `match_date` - Date and time
  - `status` - Status (scheduled, live, finished, postponed)
  - `home_score` - Home team score
  - `away_score` - Away team score
  - `venue` - Stadium name
  - `minute` - Current minute (for live matches)
  - `api_match_id` - Football Data API ID
- **Relationships:**
  - `league()` - Belongs to a league (many-to-one)
  - `homeTeam()` - Home team (many-to-one)
  - `awayTeam()` - Away team (many-to-one)
  - `playerStats()` - Player statistics for this match (one-to-many)
- **Total Records:** 128 matches
- **Used By:** MatchController, HomeController, LeagueController

---

### **PlayerStat.php**
- **Table:** `player_stats`
- **Purpose:** Player performance in specific matches
- **Columns:**
  - `id` - Primary key
  - `player_id` - Foreign key to players table
  - `match_id` - Foreign key to football_matches table
  - `minutes_played` - Minutes on field
  - `goals` - Goals scored
  - `assists` - Assists made
  - `yellow_cards` - Yellow cards received
  - `red_cards` - Red cards received
- **Relationships:**
  - `player()` - Belongs to a player (many-to-one)
  - `match()` - Belongs to a match (many-to-one)
- **Used By:** MatchController, PlayerController

---

### **DreamTeam.php** ⭐
- **Table:** `dream_teams`
- **Purpose:** User's fantasy football teams
- **Columns:**
  - `id` - Primary key
  - `user_id` - Foreign key to users table (cascade delete)
  - `name` - Team name (default: "My Dream Team")
  - `formation` - Formation (4-3-3, 4-4-2, etc.)
  - `players` - JSON array of player positions
  - `total_value` - Total market value (calculated)
  - `description` - Team description
  - `is_public` - Public or private
- **Relationships:**
  - `user()` - Belongs to a user (many-to-one)
- **Casts:**
  - `players` → `array` (auto JSON encode/decode)
  - `total_value` → `decimal:2`
  - `is_public` → `boolean`
- **Custom Attributes:**
  - `player_count` - Safe player count getter
- **Methods:**
  - `getPlayersWithDetails()` - Load full player data
  - `getFormationPositions()` - Get formation layout
- **Used By:** DreamTeamController

**Players JSON Structure:**
```json
[
  {"position": "GK", "player_id": 1, "x": 50, "y": 90},
  {"position": "DEF", "player_id": 2, "x": 20, "y": 70},
  ...
]
```

---

## 🔗 Relationship Types

### **One to Many (hasMany / belongsTo)**
```php
// Team has many Players
public function players() {
    return $this->hasMany(Player::class);
}

// Player belongs to Team
public function team() {
    return $this->belongsTo(Team::class);
}

// Usage:
$team->players; // Get all players
$player->team; // Get player's team
```

### **Many to Many (belongsToMany)**
Not used in this project, but example:
```php
// User belongs to many Teams (favorites)
public function favoriteTeams() {
    return $this->belongsToMany(Team::class, 'user_favorite_teams');
}
```

---

## 🎯 Model Features

### **1. Fillable Properties**
```php
protected $fillable = ['name', 'email', 'password'];
```
- Allows mass assignment
- Protects against malicious input
- Used in `Model::create([...])`

### **2. Casts (Type Conversion)**
```php
protected $casts = [
    'players' => 'array',      // JSON ↔ Array
    'is_public' => 'boolean',  // 0/1 ↔ true/false
    'total_value' => 'decimal:2', // Decimal with 2 places
];
```

### **3. Timestamps (Automatic)**
```php
// Laravel automatically manages:
$model->created_at; // When record was created
$model->updated_at; // When record was last updated
```

### **4. Hidden Properties**
```php
protected $hidden = ['password', 'remember_token'];
```
- Hides fields from JSON output
- Security for sensitive data

---

## 💡 Common Model Patterns

### **Query Examples:**

```php
// Find by ID
$team = Team::find(1);

// Find or fail (404 if not found)
$team = Team::findOrFail(1);

// Where clause
$teams = Team::where('league_id', 1)->get();

// With relationships (eager loading)
$matches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])->get();

// Create new record
$team = Team::create([
    'name' => 'Barcelona',
    'league_id' => 1,
]);

// Update record
$team->update(['name' => 'FC Barcelona']);

// Delete record
$team->delete();

// Pagination
$players = Player::paginate(20);
```

### **Relationship Examples:**

```php
// Get all players for a team
$team = Team::find(1);
$players = $team->players;

// Get team for a player
$player = Player::find(1);
$team = $player->team;

// Count relationships
$teamCount = League::withCount('teams')->get();

// Filter by relationship
$players = Player::whereHas('team', function($query) {
    $query->where('league_id', 1);
})->get();
```

---

## 🗃️ Database Tables Summary

| Model | Table | Records | Purpose |
|-------|-------|---------|---------|
| User | users | Variable | User accounts |
| League | leagues | 5 | Football leagues |
| Team | teams | 61 | Football teams |
| Player | players | 1,525 | Football players |
| FootballMatch | football_matches | 128 | Matches |
| PlayerStat | player_stats | Variable | Match statistics |
| DreamTeam | dream_teams | User-created | Fantasy teams |

---

## 🎓 Key Points for Understanding

1. **Models = Database Tables** - Each model represents one table
2. **Eloquent ORM** - Laravel's way to interact with database (no SQL needed)
3. **Relationships** - Connect models together (`$team->players`, `$player->team`)
4. **Mass Assignment** - Use `$fillable` to allow `Model::create()`
5. **Casts** - Automatically convert data types
6. **Accessors/Mutators** - Custom getters and setters
7. **Scopes** - Reusable query filters

---

**Related Directories:**
- 📁 [`database/migrations/`](../../database/migrations/) - Database structure
- 📁 [`app/Http/Controllers/`](../Http/Controllers/) - Uses models to fetch data
- 📁 [`resources/views/`](../../resources/views/) - Displays model data
- 📁 [`database/seeders/`](../../database/seeders/) - Populates models with data
