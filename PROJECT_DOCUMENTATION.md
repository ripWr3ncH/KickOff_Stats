# KickOff Stats - Complete Technical Documentation

## 📚 Table of Contents
1. [MVC Architecture](#mvc-architecture)
2. [Routing System](#routing-system)
3. [Database & Migrations](#database--migrations)
4. [Middleware Explained](#middleware-explained)
5. [Sessions & Cookies](#sessions--cookies)
6. [API Data Fetching & Storage](#api-data-fetching--storage)
7. [Feature Locations & File Paths](#feature-locations--file-paths)
8. [Controllers Deep Dive](#controllers-deep-dive)
9. [Models & Relationships](#models--relationships)
10. [Authentication Flow](#authentication-flow)

---

## 🏗️ MVC Architecture

### **What is MVC?**
MVC stands for Model-View-Controller. It's a design pattern that separates your application into three main components:

### **Model** (app/Models/)
**Purpose:** Represents database tables and business logic

**Your Models:**
```
app/Models/User.php              - User accounts
app/Models/Team.php              - Football teams (61 teams)
app/Models/League.php            - Football leagues (5 leagues)
app/Models/Player.php            - Players (1,525 players)
app/Models/FootballMatch.php     - Matches (128 matches)
app/Models/PlayerStat.php        - Player statistics
app/Models/DreamTeam.php         - User fantasy teams
```

**Example - Team.php:**
```php
class Team extends Model {
    protected $fillable = ['name', 'logo_url', 'api_id', 'league_id'];
    
    // Relationships
    public function league() {
        return $this->belongsTo(League::class);
    }
    
    public function players() {
        return $this->hasMany(Player::class);
    }
}
```

### **View** (resources/views/)
**Purpose:** HTML templates that users see

**Your Views Structure:**
```
resources/views/
├── layouts/
│   └── app.blade.php                    # Master layout (header, footer, nav)
├── dashboard.blade.php                  # Homepage
├── matches/
│   ├── index.blade.php                  # All matches
│   ├── show.blade.php                   # Single match details
│   └── live.blade.php                   # Live matches
├── leagues/
│   ├── index.blade.php                  # All leagues
│   └── show.blade.php                   # League standings
├── players/
│   ├── index.blade.php                  # All players
│   └── show.blade.php                   # Player profile
├── dream-team/
│   ├── index.blade.php                  # User's dream teams list
│   ├── create.blade.php                 # Formation builder
│   ├── show.blade.php                   # View dream team
│   └── edit.blade.php                   # Edit dream team
├── my-teams/
│   ├── index.blade.php                  # Favorite teams
│   └── select.blade.php                 # Team selection
├── auth/
│   ├── forgot-password.blade.php        # Email input
│   └── reset-password.blade.php         # New password form
└── emails/
    └── password-reset.blade.php         # Email template
```

### **Controller** (app/Http/Controllers/)
**Purpose:** Handles requests, processes data, returns responses

**Your Controllers:**
```
app/Http/Controllers/
├── HomeController.php                   # Homepage/Dashboard logic
├── MatchController.php                  # Match pages
├── LeagueController.php                 # League standings
├── PlayerController.php                 # Player profiles
├── DreamTeamController.php              # Fantasy team CRUD
├── MyTeamController.php                 # Favorite teams
└── Auth/
    └── PasswordResetController.php      # Password recovery
```

**How They Work Together:**
```
User Request → Route → Controller → Model (Database) → Controller → View → Response
```

---

## 🛣️ Routing System

**File Location:** `routes/web.php`

### **Route Types:**

```php
// GET - Retrieve/display data
Route::get('/matches', [MatchController::class, 'index']);

// POST - Submit data (forms)
Route::post('/dream-team', [DreamTeamController::class, 'store']);

// PUT/PATCH - Update data
Route::put('/dream-team/{dreamTeam}', [DreamTeamController::class, 'update']);

// DELETE - Remove data
Route::delete('/dream-team/{dreamTeam}', [DreamTeamController::class, 'destroy']);
```

### **Your Complete Route Structure:**

```php
// PUBLIC ROUTES (No login required)
Route::get('/', [HomeController::class, 'index'])->name('home');
// File: app/Http/Controllers/HomeController.php

Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
Route::get('/matches/live', [MatchController::class, 'live'])->name('matches.live');
// File: app/Http/Controllers/MatchController.php

Route::resource('leagues', LeagueController::class);
// Creates: index, show, create, store, edit, update, destroy
// File: app/Http/Controllers/LeagueController.php

Route::resource('players', PlayerController::class);
// File: app/Http/Controllers/PlayerController.php

// PASSWORD RESET ROUTES
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])
    ->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->name('password.update');
// File: app/Http/Controllers/Auth/PasswordResetController.php

// PROTECTED ROUTES (Login required - uses auth.user middleware)
Route::middleware('auth.user')->group(function() {
    // My Teams
    Route::get('/my-teams', [MyTeamController::class, 'index'])->name('my-teams.index');
    Route::post('/my-teams/{team}/toggle', [MyTeamController::class, 'toggle'])
        ->name('my-teams.toggle');
    Route::get('/my-teams/select', [MyTeamController::class, 'select'])
        ->name('my-teams.select');
    
    // Dream Team
    Route::get('/dream-team', [DreamTeamController::class, 'index'])
        ->name('dream-team.index');
    Route::get('/dream-team/create', [DreamTeamController::class, 'create'])
        ->name('dream-team.create');
    Route::post('/dream-team', [DreamTeamController::class, 'store'])
        ->name('dream-team.store');
    Route::get('/dream-team/{dreamTeam}', [DreamTeamController::class, 'show'])
        ->name('dream-team.show');
    Route::get('/dream-team/{dreamTeam}/edit', [DreamTeamController::class, 'edit'])
        ->name('dream-team.edit');
    Route::put('/dream-team/{dreamTeam}', [DreamTeamController::class, 'update'])
        ->name('dream-team.update');
    Route::delete('/dream-team/{dreamTeam}', [DreamTeamController::class, 'destroy'])
        ->name('dream-team.destroy');
    
    // AJAX Route for player search
    Route::get('/api/dream-team/search-players', [DreamTeamController::class, 'searchPlayers'])
        ->name('dream-team.search-players');
});
```

### **Named Routes Benefits:**
```php
// In views:
<a href="{{ route('matches.show', $match->id) }}">View Match</a>
// Generates: /matches/5

// In controllers:
return redirect()->route('dream-team.index');
// Redirects to: /dream-team
```

---

## 🗄️ Database & Migrations

**Database:** MySQL - `kickoffstats_db`
**Configuration File:** `config/database.php`
**Environment File:** `.env` (contains password)

### **Migration Files Location:**
```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 2025_09_12_142628_create_leagues_table.php
├── 2025_09_12_143742_create_teams_table.php
├── 2025_09_12_143937_create_players_table.php
├── 2025_09_12_144017_create_football_matches_table.php
├── 2025_09_12_144026_create_player_stats_table.php
├── 2025_10_10_200816_create_dream_teams_table.php
├── 2025_10_21_152647_add_logo_url_to_teams_table.php
└── 2025_10_21_161745_create_password_reset_tokens_table.php
```

### **Database Tables:**

#### **1. users**
```sql
id (primary key)
name (varchar)
email (varchar, unique)
password (hashed with bcrypt)
remember_token (for "remember me" functionality)
created_at, updated_at
```
**File:** `database/migrations/0001_01_01_000000_create_users_table.php`

#### **2. leagues**
```sql
id (primary key)
name (varchar) - e.g., "Premier League"
country (varchar) - e.g., "England"
logo_url (varchar, nullable)
api_id (integer, nullable) - Football Data API ID
created_at, updated_at
```
**File:** `database/migrations/2025_09_12_142628_create_leagues_table.php`

#### **3. teams**
```sql
id (primary key)
name (varchar) - e.g., "Barcelona"
logo_url (varchar, nullable) - Team crest URL
api_id (integer, nullable) - Football Data API ID
league_id (foreign key → leagues.id)
created_at, updated_at
```
**File:** `database/migrations/2025_09_12_143742_create_teams_table.php`
**Total Teams:** 61 (51 with real API data)

#### **4. players**
```sql
id (primary key)
name (varchar)
slug (varchar, unique)
team_id (foreign key → teams.id)
position (enum: GK, DEF, MID, FWD)
jersey_number (integer)
nationality (varchar)
date_of_birth (date)
height (integer, cm)
weight (integer, kg)
market_value (decimal) - in dollars
photo_url (varchar, nullable)
bio (text, nullable)
created_at, updated_at
```
**File:** `database/migrations/2025_09_12_143937_create_players_table.php`
**Total Players:** 1,525 (25 per team)

#### **5. football_matches**
```sql
id (primary key)
league_id (foreign key → leagues.id)
home_team_id (foreign key → teams.id)
away_team_id (foreign key → teams.id)
match_date (datetime)
status (enum: scheduled, live, finished, postponed)
home_score (integer, default 0)
away_score (integer, default 0)
venue (varchar, nullable)
minute (integer, nullable) - for live matches
api_match_id (integer, nullable)
created_at, updated_at
```
**File:** `database/migrations/2025_09_12_144017_create_football_matches_table.php`
**Total Matches:** 128

#### **6. player_stats**
```sql
id (primary key)
player_id (foreign key → players.id)
match_id (foreign key → football_matches.id)
minutes_played (integer)
goals (integer, default 0)
assists (integer, default 0)
yellow_cards (integer, default 0)
red_cards (integer, default 0)
created_at, updated_at
```
**File:** `database/migrations/2025_09_12_144026_create_player_stats_table.php`

#### **7. dream_teams**
```sql
id (primary key)
user_id (foreign key → users.id, cascade delete)
name (varchar, default 'My Dream Team')
formation (varchar, default '4-3-3')
players (json) - Array of player objects
total_value (decimal, default 0)
description (text, nullable)
is_public (boolean, default false)
created_at, updated_at

Index: (user_id, created_at)
```
**File:** `database/migrations/2025_10_10_200816_create_dream_teams_table.php`

**Players JSON Structure:**
```json
[
  {
    "position": "GK",
    "player_id": 1,
    "x": 50,
    "y": 90
  },
  {
    "position": "DEF",
    "player_id": 2,
    "x": 20,
    "y": 70
  }
]
```

#### **8. password_reset_tokens**
```sql
email (varchar, primary key)
token (varchar) - hashed with bcrypt
created_at (timestamp)
```
**File:** `database/migrations/2025_10_21_161745_create_password_reset_tokens_table.php`

### **Running Migrations:**
```bash
# Run all pending migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset and re-run all migrations
php artisan migrate:fresh

# Reset and run with seeders
php artisan migrate:fresh --seed
```

### **Seeder Files Location:**
```
database/seeders/
├── DatabaseSeeder.php        # Main seeder that calls others
├── LeagueSeeder.php          # Creates 5 leagues
├── TeamSeeder.php            # Creates 61 teams
└── PlayerSeeder.php          # Generates 1,525 players
```

**How Seeders Work:**

**LeagueSeeder.php:**
```php
League::create([
    'name' => 'Premier League',
    'country' => 'England',
    'logo_url' => 'https://example.com/epl.png',
    'api_id' => 2021
]);
```

**PlayerSeeder.php:**
```php
// For each team, creates:
// - 3 Goalkeepers
// - 8 Defenders
// - 8 Midfielders
// - 6 Forwards
// Total: 25 players per team × 61 teams = 1,525 players

$teams = Team::all();
foreach ($teams as $team) {
    // Create GK
    for ($i = 0; $i < 3; $i++) {
        Player::create([
            'name' => $faker->name,
            'position' => 'GK',
            'team_id' => $team->id,
            'nationality' => $faker->randomElement(['England', 'Spain', ...]),
            'market_value' => $faker->numberBetween(500000, 5000000),
        ]);
    }
}
```

---

## 🔐 Middleware Explained

**What is Middleware?**
Middleware is a layer that sits between the user's request and your controller. It filters/modifies requests before they reach your application.

**Location:** `app/Http/Middleware/`

### **Your Custom Middleware:**

**File:** `app/Http/Middleware/AuthenticateUser.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateUser
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            // Not logged in - redirect to homepage with error
            return redirect('/')
                ->with('error', 'Please login to access this feature');
        }
        
        // User is logged in - allow request to proceed
        return $next($request);
    }
}
```

### **How Middleware Works:**

```
User Request
    ↓
Middleware (AuthenticateUser)
    ↓ (if logged in)
Controller
    ↓
Response
```

### **Registering Middleware:**

**File:** `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'auth.user' => \App\Http\Middleware\AuthenticateUser::class,
    ]);
})
```

### **Using Middleware in Routes:**

```php
// Single route
Route::get('/dream-team', [DreamTeamController::class, 'index'])
    ->middleware('auth.user');

// Group of routes
Route::middleware('auth.user')->group(function() {
    Route::resource('dream-team', DreamTeamController::class);
    Route::get('/my-teams', [MyTeamController::class, 'index']);
});
```

### **Laravel's Built-in Middleware:**

Your app uses these automatically:

1. **EncryptCookies** - Encrypts cookies for security
2. **VerifyCsrfToken** - Prevents cross-site request forgery attacks
3. **StartSession** - Starts PHP session
4. **ShareErrorsFromSession** - Makes validation errors available in views
5. **SubstituteBindings** - Automatically loads models from route parameters

**File:** `app/Http/Kernel.php` (or `bootstrap/app.php` in Laravel 11)

---

## 🍪 Sessions & Cookies

### **What are Sessions?**
Sessions store user data on the server. Each user gets a unique session ID stored in a cookie.

**Configuration File:** `config/session.php`

### **Session Configuration:**
```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => 120, // minutes
'expire_on_close' => false,
'encrypt' => false,
'files' => storage_path('framework/sessions'),
'connection' => null,
'table' => 'sessions',
'store' => null,
'lottery' => [2, 100],
'cookie' => env('SESSION_COOKIE', 'kickoff-stats-session'),
'path' => '/',
'domain' => env('SESSION_DOMAIN', null),
'secure' => env('SESSION_SECURE_COOKIE', false),
'http_only' => true,
'same_site' => 'lax',
```

### **How Sessions Work in Your Project:**

#### **1. Login Process:**
```php
// When user logs in (built-in Laravel auth):
Auth::login($user, $remember = true);

// Laravel automatically:
// 1. Creates session file: storage/framework/sessions/xyz123
// 2. Stores user ID in session
// 3. Sends session cookie to browser
```

#### **2. Session Storage:**
**Location:** `storage/framework/sessions/`

Each session file contains:
```php
// storage/framework/sessions/xyz123
user_id: 1
_token: "csrf_token_here"
url: {...}
flash: {...}
```

#### **3. Database Sessions:**
Your app also stores sessions in database:

**Table:** `sessions`
```sql
id (varchar) - Session ID
user_id (bigint, nullable)
ip_address (varchar)
user_agent (text)
payload (longtext) - Encrypted session data
last_activity (integer) - Timestamp
```

**Migration:** `database/migrations/0001_01_01_000001_create_cache_table.php`

### **Session Usage in Your Code:**

```php
// Store data
session(['key' => 'value']);
$request->session()->put('key', 'value');

// Retrieve data
$value = session('key');
$value = $request->session()->get('key');

// Flash data (available only for next request)
session()->flash('success', 'Dream team created!');
return redirect()->route('dream-team.index')
    ->with('success', 'Dream team created!');

// In view:
@if(session('success'))
    <div class="alert">{{ session('success') }}</div>
@endif

// Remove data
session()->forget('key');

// Remove all data
session()->flush();
```

### **Cookies:**

**Configuration File:** `config/session.php`

**Cookies in Your App:**

1. **Session Cookie:** `kickoff-stats-session`
   - Stores session ID
   - HttpOnly (JavaScript can't access)
   - Encrypted

2. **CSRF Token Cookie:** `XSRF-TOKEN`
   - Prevents CSRF attacks
   - Readable by JavaScript

3. **Remember Me Cookie:** (if "Remember Me" checked)
   - Lasts 5 years
   - Keeps user logged in

**Cookie Storage in Browser:**
```
Name: kickoff-stats-session
Value: eyJpdiI6ImJKQWh1WWZ6TE... (encrypted)
Domain: 127.0.0.1
Path: /
Expires: Session (or 5 years if remembered)
HttpOnly: ✓
Secure: ✗ (localhost)
SameSite: Lax
```

### **Authentication State Check:**

```php
// In controllers:
if (Auth::check()) {
    // User is logged in
    $userId = Auth::id();
    $user = Auth::user();
}

// In views:
@auth
    <p>Welcome, {{ Auth::user()->name }}</p>
@endauth

@guest
    <a href="/login">Login</a>
@endguest
```

---

## 🌐 API Data Fetching & Storage

### **Football Data API Integration**

**API Provider:** Football-Data.org
**API Key Storage:** `.env` file
```
FOOTBALL_DATA_API_KEY=your_api_key_here
```

### **Service File Location:**
**File:** `app/Services/FootballDataService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FootballDataService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.football-data.org/v4';

    public function __construct()
    {
        $this->apiKey = config('services.football_data.api_key');
    }

    /**
     * Fetch teams from API
     */
    public function getTeams($leagueId)
    {
        $response = Http::withHeaders([
            'X-Auth-Token' => $this->apiKey
        ])->get("{$this->baseUrl}/competitions/{$leagueId}/teams");

        return $response->json();
    }

    /**
     * Fetch matches
     */
    public function getMatches($teamId)
    {
        $response = Http::withHeaders([
            'X-Auth-Token' => $this->apiKey
        ])->get("{$this->baseUrl}/teams/{$teamId}/matches");

        return $response->json();
    }
}
```

### **API Configuration File:**
**File:** `config/services.php`

```php
'football_data' => [
    'api_key' => env('FOOTBALL_DATA_API_KEY'),
    'base_url' => 'https://api.football-data.org/v4',
],
```

### **How API Data is Fetched and Stored:**

#### **1. Importing Team Logos**

**Script Location:** You created temporary PHP scripts (now deleted)
**Example process:**

```php
// fetch-logos.php (example)
<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Team;
use Illuminate\Support\Facades\Http;

$apiKey = config('services.football_data.api_key');

// Fetch teams with API IDs
$teams = Team::whereNotNull('api_id')->get();

foreach ($teams as $team) {
    // API endpoint
    $url = "https://api.football-data.org/v4/teams/{$team->api_id}";
    
    // Make API request
    $response = Http::withHeaders([
        'X-Auth-Token' => $apiKey
    ])->get($url);
    
    if ($response->successful()) {
        $data = $response->json();
        
        // Update team with logo URL
        $team->update([
            'logo_url' => $data['crest'] ?? null
        ]);
        
        echo "Updated: {$team->name}\n";
    }
    
    // Respect API rate limit (10 requests per minute)
    sleep(6);
}
```

#### **2. Importing Barcelona Matches**

**Command Location:** `app/Console/Commands/ImportRealMatches.php`

**How to Run:**
```bash
php artisan import:barcelona-matches
```

**Command Code:**
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;
use App\Models\FootballMatch;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ImportRealMatches extends Command
{
    protected $signature = 'import:barcelona-matches';
    protected $description = 'Import Barcelona matches from Football Data API';

    public function handle()
    {
        $apiKey = config('services.football_data.api_key');
        $barcelona = Team::where('name', 'Barcelona')->first();
        
        if (!$barcelona || !$barcelona->api_id) {
            $this->error('Barcelona team not found or missing API ID');
            return;
        }

        // Fetch matches from API
        $response = Http::withHeaders([
            'X-Auth-Token' => $apiKey
        ])->get("https://api.football-data.org/v4/teams/{$barcelona->api_id}/matches");

        if (!$response->successful()) {
            $this->error('API request failed');
            return;
        }

        $matches = $response->json()['matches'];
        $imported = 0;
        $skipped = 0;

        foreach ($matches as $matchData) {
            // Find home and away teams in database
            $homeTeam = Team::where('api_id', $matchData['homeTeam']['id'])->first();
            $awayTeam = Team::where('api_id', $matchData['awayTeam']['id'])->first();

            if (!$homeTeam || !$awayTeam) {
                $skipped++;
                continue;
            }

            // Map API status to our database enum
            $status = match($matchData['status']) {
                'SCHEDULED', 'TIMED' => 'scheduled',
                'IN_PLAY', 'PAUSED' => 'live',
                'FINISHED' => 'finished',
                'POSTPONED', 'CANCELLED' => 'postponed',
                default => 'scheduled'
            };

            // Create or update match
            FootballMatch::updateOrCreate(
                ['api_match_id' => $matchData['id']],
                [
                    'league_id' => $homeTeam->league_id,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'match_date' => Carbon::parse($matchData['utcDate']),
                    'status' => $status,
                    'home_score' => $matchData['score']['fullTime']['home'] ?? 0,
                    'away_score' => $matchData['score']['fullTime']['away'] ?? 0,
                    'venue' => $matchData['venue'] ?? null,
                ]
            );

            $imported++;
        }

        $this->info("Imported: {$imported} matches");
        $this->info("Skipped: {$skipped} matches");
    }
}
```

### **Data Flow Diagram:**

```
Football Data API
        ↓
HTTP Request (with API key)
        ↓
JSON Response
        ↓
Parse & Validate Data
        ↓
Store in MySQL Database
        ↓
Display in Views
```

### **API Endpoints Used:**

1. **Get Competition Teams:**
   ```
   GET https://api.football-data.org/v4/competitions/{id}/teams
   ```

2. **Get Team Details:**
   ```
   GET https://api.football-data.org/v4/teams/{id}
   ```

3. **Get Team Matches:**
   ```
   GET https://api.football-data.org/v4/teams/{id}/matches
   ```

### **Rate Limiting:**

Football Data API limits:
- **Free Tier:** 10 requests per minute
- **Solution:** Added `sleep(6)` between requests (6 seconds = 10 requests/minute)

---

## 📂 Feature Locations & File Paths

### **1. Dashboard (Homepage)**

**Route:**
```php
Route::get('/', [HomeController::class, 'index'])->name('home');
```

**Files:**
- **Controller:** `app/Http/Controllers/HomeController.php`
- **View:** `resources/views/dashboard.blade.php`
- **Model Used:** `FootballMatch`, `League`, `Team`

**Controller Logic:**
```php
public function index()
{
    $liveMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
        ->where('status', 'live')
        ->latest('match_date')
        ->take(10)
        ->get();

    $upcomingMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
        ->where('status', 'scheduled')
        ->where('match_date', '>', now())
        ->orderBy('match_date')
        ->take(10)
        ->get();

    $leagues = League::withCount('teams')->get();

    return view('dashboard', compact('liveMatches', 'upcomingMatches', 'leagues'));
}
```

**Features:**
- Live matches with real-time updates
- Upcoming matches
- League overview
- Top teams

---

### **2. Matches System**

**Routes:**
```php
Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
Route::get('/matches/live', [MatchController::class, 'live'])->name('matches.live');
```

**Files:**
- **Controller:** `app/Http/Controllers/MatchController.php`
- **Model:** `app/Models/FootballMatch.php`
- **Views:**
  - `resources/views/matches/index.blade.php` - All matches
  - `resources/views/matches/show.blade.php` - Single match
  - `resources/views/matches/live.blade.php` - Live matches

**Controller Methods:**

```php
// List all matches
public function index(Request $request)
{
    $query = FootballMatch::with(['homeTeam', 'awayTeam', 'league']);

    // Filter by date
    if ($request->date) {
        $query->whereDate('match_date', $request->date);
    }

    // Filter by league
    if ($request->league) {
        $query->where('league_id', $request->league);
    }

    // Filter by status
    if ($request->status) {
        $query->where('status', $request->status);
    }

    $matches = $query->orderBy('match_date', 'desc')->paginate(20);
    $leagues = League::all();

    return view('matches.index', compact('matches', 'leagues'));
}

// Show single match
public function show(FootballMatch $match)
{
    $match->load(['homeTeam', 'awayTeam', 'league', 'playerStats.player']);
    
    return view('matches.show', compact('match'));
}

// Live matches only
public function live()
{
    $liveMatches = FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
        ->where('status', 'live')
        ->orderBy('match_date', 'desc')
        ->get();

    return view('matches.live', compact('liveMatches'));
}
```

**Features:**
- Date picker filter
- League filter
- Status filter (scheduled, live, finished)
- Pagination
- Match statistics

---

### **3. League Standings**

**Routes:**
```php
Route::resource('leagues', LeagueController::class);
```

**Files:**
- **Controller:** `app/Http/Controllers/LeagueController.php`
- **Model:** `app/Models/League.php`
- **Views:**
  - `resources/views/leagues/index.blade.php` - All leagues
  - `resources/views/leagues/show.blade.php` - League table

**Controller Logic:**
```php
public function show(League $league)
{
    $teams = Team::where('league_id', $league->id)
        ->withCount(['homeMatches as wins' => function($query) {
            $query->where('status', 'finished')
                  ->whereColumn('home_score', '>', 'away_score');
        }])
        ->get();

    // Calculate points, goals, etc.
    
    return view('leagues.show', compact('league', 'teams'));
}
```

**Features:**
- League table with points
- Win/Draw/Loss statistics
- Goal difference
- Team logos

---

### **4. Player Profiles**

**Routes:**
```php
Route::resource('players', PlayerController::class);
```

**Files:**
- **Controller:** `app/Http/Controllers/PlayerController.php`
- **Model:** `app/Models/Player.php`
- **Views:**
  - `resources/views/players/index.blade.php` - All players
  - `resources/views/players/show.blade.php` - Player profile

**Controller Logic:**
```php
public function index(Request $request)
{
    $query = Player::with('team');

    if ($request->search) {
        $query->where('name', 'like', "%{$request->search}%");
    }

    if ($request->position) {
        $query->where('position', $request->position);
    }

    $players = $query->paginate(20);

    return view('players.index', compact('players'));
}

public function show(Player $player)
{
    $player->load('team.league', 'stats.match');

    return view('players.show', compact('player'));
}
```

**Features:**
- Search by name
- Filter by position
- Player statistics
- Team affiliation

---

### **5. Dream Team Builder** ⭐

**Routes:**
```php
Route::middleware('auth.user')->group(function() {
    Route::get('/dream-team', [DreamTeamController::class, 'index'])
        ->name('dream-team.index');
    Route::get('/dream-team/create', [DreamTeamController::class, 'create'])
        ->name('dream-team.create');
    Route::post('/dream-team', [DreamTeamController::class, 'store'])
        ->name('dream-team.store');
    Route::get('/dream-team/{dreamTeam}', [DreamTeamController::class, 'show'])
        ->name('dream-team.show');
    Route::get('/dream-team/{dreamTeam}/edit', [DreamTeamController::class, 'edit'])
        ->name('dream-team.edit');
    Route::put('/dream-team/{dreamTeam}', [DreamTeamController::class, 'update'])
        ->name('dream-team.update');
    Route::delete('/dream-team/{dreamTeam}', [DreamTeamController::class, 'destroy'])
        ->name('dream-team.destroy');
    
    // AJAX endpoint
    Route::get('/api/dream-team/search-players', [DreamTeamController::class, 'searchPlayers'])
        ->name('dream-team.search-players');
});
```

**Files:**
- **Controller:** `app/Http/Controllers/DreamTeamController.php`
- **Model:** `app/Models/DreamTeam.php`
- **Migration:** `database/migrations/2025_10_10_200816_create_dream_teams_table.php`
- **Views:**
  - `resources/views/dream-team/index.blade.php` - User's teams list
  - `resources/views/dream-team/create.blade.php` - Formation builder
  - `resources/views/dream-team/show.blade.php` - View team
  - `resources/views/dream-team/edit.blade.php` - Edit team

**Controller Methods:**

```php
// List user's dream teams
public function index()
{
    $dreamTeams = DreamTeam::where('user_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();

    return view('dream-team.index', compact('dreamTeams'));
}

// Show creation form
public function create()
{
    $leagues = League::all();
    $formations = ['4-3-3', '4-4-2', '3-5-2', '5-3-2', '4-2-3-1'];

    return view('dream-team.create', compact('leagues', 'formations'));
}

// Save new dream team
public function store(Request $request)
{
    // Decode JSON if needed
    if (is_string($request->input('players'))) {
        $playersArray = json_decode($request->input('players'), true);
        $request->merge(['players' => $playersArray]);
    }

    // Validate
    $request->validate([
        'name' => 'required|string|max:255',
        'formation' => 'required|string|in:4-3-3,4-4-2,3-5-2,5-3-2,4-2-3-1',
        'players' => 'required|array|min:11',
        'description' => 'nullable|string|max:1000'
    ]);

    // Create dream team
    $dreamTeam = DreamTeam::create([
        'user_id' => Auth::id(),
        'name' => $request->name,
        'formation' => $request->formation,
        'players' => $request->players,
        'description' => $request->description,
        'is_public' => $request->boolean('is_public')
    ]);

    // Calculate total value
    $this->updateTotalValue($dreamTeam);

    return redirect()
        ->route('dream-team.show', $dreamTeam)
        ->with('success', 'Dream team created successfully!');
}

// AJAX player search
public function searchPlayers(Request $request)
{
    $query = Player::with(['team', 'team.league']);

    if ($request->name) {
        $query->where('name', 'like', "%{$request->name}%");
    }

    if ($request->position) {
        $query->where('position', $request->position);
    }

    if ($request->league) {
        $query->whereHas('team', function($q) use ($request) {
            $q->where('league_id', $request->league);
        });
    }

    $players = $query->limit(20)->get();

    return response()->json($players);
}
```

**Features:**
- 5 formations (4-3-3, 4-4-2, 3-5-2, 5-3-2, 4-2-3-1)
- Visual football field
- Player search with AJAX
- Real-time player counter (0/11)
- Formation validation (must select 11 players)
- Total market value calculation
- Public/Private teams
- Edit and delete teams

**JavaScript Features (create.blade.php):**
- Formation position mapping
- Player slot management
- AJAX search
- Form validation
- JSON serialization

---

### **6. Password Reset System**

**Routes:**
```php
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])
    ->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->name('password.update');
```

**Files:**
- **Controller:** `app/Http/Controllers/Auth/PasswordResetController.php`
- **Migration:** `database/migrations/2025_10_21_161745_create_password_reset_tokens_table.php`
- **Views:**
  - `resources/views/auth/forgot-password.blade.php` - Enter email
  - `resources/views/auth/reset-password.blade.php` - New password
  - `resources/views/emails/password-reset.blade.php` - Email template

**Controller Flow:**

```php
// Step 1: Show email form
public function showForgotForm()
{
    return view('auth.forgot-password');
}

// Step 2: Send reset link
public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => 'User not found']);
    }

    // Generate token
    $token = Str::random(64);

    // Store token (60-minute expiry)
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        [
            'token' => Hash::make($token),
            'created_at' => now()
        ]
    );

    // Send email
    Mail::to($user->email)->send(new PasswordResetMail($user, $token));

    return back()->with('success', 'Reset link sent to your email');
}

// Step 3: Show reset form
public function showResetForm($token)
{
    return view('auth.reset-password', compact('token'));
}

// Step 4: Update password
public function reset(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    // Find token
    $resetRecord = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    // Validate token
    if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
        return back()->withErrors(['email' => 'Invalid token']);
    }

    // Check expiry (60 minutes)
    if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
        return back()->withErrors(['email' => 'Token expired']);
    }

    // Update password
    $user = User::where('email', $request->email)->first();
    $user->update(['password' => Hash::make($request->password)]);

    // Delete token
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return redirect()->route('dashboard')
        ->with('success', 'Password reset successfully');
}
```

**Security Features:**
- Token hashing with bcrypt
- 60-minute token expiry
- Email validation
- Password confirmation
- CSRF protection

---

### **7. My Teams (Favorites)**

**Routes:**
```php
Route::middleware('auth.user')->group(function() {
    Route::get('/my-teams', [MyTeamController::class, 'index'])
        ->name('my-teams.index');
    Route::post('/my-teams/{team}/toggle', [MyTeamController::class, 'toggle'])
        ->name('my-teams.toggle');
    Route::get('/my-teams/select', [MyTeamController::class, 'select'])
        ->name('my-teams.select');
});
```

**Files:**
- **Controller:** `app/Http/Controllers/MyTeamController.php`
- **Views:**
  - `resources/views/my-teams/index.blade.php`
  - `resources/views/my-teams/select.blade.php`

---

## 🎯 Controllers Deep Dive

### **Controller Naming Convention:**
- **Resource Controllers:** Use standard CRUD methods
  - `index()` - List all
  - `create()` - Show creation form
  - `store()` - Save new record
  - `show($id)` - Display single record
  - `edit($id)` - Show edit form
  - `update($id)` - Save changes
  - `destroy($id)` - Delete record

### **DreamTeamController - Complete Example:**

**File:** `app/Http/Controllers/DreamTeamController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DreamTeam;
use App\Models\Player;
use App\Models\League;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DreamTeamController extends Controller
{
    /**
     * Display user's dream teams
     */
    public function index()
    {
        $dreamTeams = DreamTeam::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dream-team.index', compact('dreamTeams'));
    }

    /**
     * Show formation builder
     */
    public function create()
    {
        $leagues = League::all();
        $formations = ['4-3-3', '4-4-2', '3-5-2', '5-3-2', '4-2-3-1'];

        return view('dream-team.create', compact('leagues', 'formations'));
    }

    /**
     * Store new dream team
     */
    public function store(Request $request)
    {
        // Debug logging
        Log::info('Dream Team Store Request', [
            'all_data' => $request->all(),
            'players_raw' => $request->input('players'),
            'players_type' => gettype($request->input('players'))
        ]);

        // Handle JSON string input
        if (is_string($request->input('players'))) {
            $playersArray = json_decode($request->input('players'), true);
            $request->merge(['players' => $playersArray]);
        }

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'formation' => 'required|string|in:4-3-3,4-4-2,3-5-2,5-3-2,4-2-3-1',
            'players' => 'required|array|min:11',
            'description' => 'nullable|string|max:1000'
        ], [
            'players.required' => 'Please select players for your dream team.',
            'players.min' => 'You must select at least 11 players for your dream team.'
        ]);

        // Create dream team
        $dreamTeam = DreamTeam::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'formation' => $request->formation,
            'players' => $request->players,
            'description' => $request->description,
            'is_public' => $request->boolean('is_public')
        ]);

        // Calculate total value
        $this->updateTotalValue($dreamTeam);

        return redirect()
            ->route('dream-team.show', $dreamTeam)
            ->with('success', 'Dream team created successfully!');
    }

    /**
     * Display single dream team
     */
    public function show(DreamTeam $dreamTeam)
    {
        // Check permission
        if ($dreamTeam->user_id !== Auth::id() && !$dreamTeam->is_public) {
            abort(403, 'Unauthorized access');
        }

        $dreamTeam->load('user');

        return view('dream-team.show', compact('dreamTeam'));
    }

    /**
     * Show edit form
     */
    public function edit(DreamTeam $dreamTeam)
    {
        // Check ownership
        if ($dreamTeam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $leagues = League::all();
        $formations = ['4-3-3', '4-4-2', '3-5-2', '5-3-2', '4-2-3-1'];

        return view('dream-team.edit', compact('dreamTeam', 'leagues', 'formations'));
    }

    /**
     * Update dream team
     */
    public function update(Request $request, DreamTeam $dreamTeam)
    {
        // Check ownership
        if ($dreamTeam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Handle JSON
        if (is_string($request->input('players'))) {
            $playersArray = json_decode($request->input('players'), true);
            $request->merge(['players' => $playersArray]);
        }

        // Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'formation' => 'required|string|in:4-3-3,4-4-2,3-5-2,5-3-2,4-2-3-1',
            'players' => 'required|array|min:11',
            'description' => 'nullable|string|max:1000'
        ]);

        // Update
        $dreamTeam->update([
            'name' => $request->name,
            'formation' => $request->formation,
            'players' => $request->players,
            'description' => $request->description,
            'is_public' => $request->boolean('is_public')
        ]);

        $this->updateTotalValue($dreamTeam);

        return redirect()
            ->route('dream-team.show', $dreamTeam)
            ->with('success', 'Dream team updated successfully!');
    }

    /**
     * Delete dream team
     */
    public function destroy(DreamTeam $dreamTeam)
    {
        // Check ownership
        if ($dreamTeam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $dreamTeam->delete();

        return redirect()
            ->route('dream-team.index')
            ->with('success', 'Dream team deleted successfully!');
    }

    /**
     * AJAX: Search players
     */
    public function searchPlayers(Request $request)
    {
        $query = Player::with(['team', 'team.league']);

        // Filter by name
        if ($request->name) {
            $query->where('name', 'like', "%{$request->name}%");
        }

        // Filter by position
        if ($request->position) {
            $query->where('position', $request->position);
        }

        // Filter by league
        if ($request->league) {
            $query->whereHas('team', function($q) use ($request) {
                $q->where('league_id', $request->league);
            });
        }

        $players = $query->limit(20)->get();

        return response()->json($players);
    }

    /**
     * Calculate total market value
     */
    protected function updateTotalValue(DreamTeam $dreamTeam)
    {
        $playerIds = collect($dreamTeam->players)->pluck('player_id')->filter();
        
        if ($playerIds->isEmpty()) {
            $dreamTeam->update(['total_value' => 0]);
            return;
        }

        $totalValue = Player::whereIn('id', $playerIds)->sum('market_value');
        $dreamTeam->update(['total_value' => $totalValue]);
    }
}
```

---

## 🗃️ Models & Relationships

### **Relationship Types:**

1. **One to Many:** `hasMany()` and `belongsTo()`
2. **Many to Many:** `belongsToMany()`

### **Complete Model Examples:**

#### **Team Model**

**File:** `app/Models/Team.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'logo_url',
        'api_id',
        'league_id'
    ];

    /**
     * One team belongs to one league
     */
    public function league()
    {
        return $this->belongsTo(League::class);
    }

    /**
     * One team has many players
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Matches where team is home
     */
    public function homeMatches()
    {
        return $this->hasMany(FootballMatch::class, 'home_team_id');
    }

    /**
     * Matches where team is away
     */
    public function awayMatches()
    {
        return $this->hasMany(FootballMatch::class, 'away_team_id');
    }

    /**
     * All matches (home + away)
     */
    public function allMatches()
    {
        return FootballMatch::where('home_team_id', $this->id)
            ->orWhere('away_team_id', $this->id);
    }
}
```

#### **DreamTeam Model**

**File:** `app/Models/DreamTeam.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DreamTeam extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'formation',
        'players',
        'total_value',
        'description',
        'is_public'
    ];

    protected $casts = [
        'players' => 'array',  // Automatically converts JSON to array
        'total_value' => 'decimal:2',
        'is_public' => 'boolean'
    ];

    /**
     * Belongs to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get player count safely
     */
    public function getPlayerCountAttribute()
    {
        if (is_array($this->players)) {
            return count($this->players);
        }
        return 0;
    }

    /**
     * Get players with full details
     */
    public function getPlayersWithDetails()
    {
        if (empty($this->players)) {
            return collect();
        }

        $playerIds = collect($this->players)->pluck('player_id')->filter();
        
        if ($playerIds->isEmpty()) {
            return collect();
        }

        $players = Player::with(['team'])->whereIn('id', $playerIds)->get();
        
        return collect($this->players)->map(function ($slot) use ($players) {
            $player = $players->firstWhere('id', $slot['player_id'] ?? null);
            return [
                'position' => $slot['position'],
                'x' => $slot['x'] ?? 0,
                'y' => $slot['y'] ?? 0,
                'player' => $player
            ];
        });
    }
}
```

---

## 🔐 Authentication Flow

### **Complete Authentication Process:**

```
1. User visits homepage
   ↓
2. Clicks "Login" button
   ↓
3. Modal opens (in app.blade.php)
   ↓
4. User enters email + password
   ↓
5. Form submits to /login (POST)
   ↓
6. Laravel's Auth system:
   - Validates credentials
   - Checks password hash
   ↓
7. If valid:
   - Creates session
   - Stores user ID in session
   - Sets session cookie
   - Regenerates session ID (security)
   ↓
8. Redirects to dashboard
   ↓
9. User is now authenticated
```

### **Session Files Created:**

**Location:** `storage/framework/sessions/`

**Example session file:**
```
user_id: 1
_token: "csrf_token_here"
_flash: {}
_previous: {"url": "http://127.0.0.1:8000/dashboard"}
login_web_xxx: user_id_here
```

### **Database Session Record:**

**Table:** `sessions`
```sql
id: "xyz123abc"
user_id: 1
ip_address: "127.0.0.1"
user_agent: "Mozilla/5.0..."
payload: "encrypted_session_data"
last_activity: 1634567890
```

### **Checking Authentication:**

```php
// In controllers:
if (Auth::check()) {
    $userId = Auth::id();
    $user = Auth::user();
    $userName = Auth::user()->name;
}

// In routes:
Route::middleware('auth.user')->group(function() {
    // Protected routes
});

// In views:
@auth
    <p>Welcome, {{ Auth::user()->name }}</p>
@endauth

@guest
    <a href="/login">Login</a>
@endguest
```

---

## 📊 Project Statistics

### **Database:**
- **Tables:** 8
- **Total Records:** ~2,700+
  - Users: Test accounts
  - Leagues: 5
  - Teams: 61
  - Players: 1,525
  - Matches: 128
  - Dream Teams: User-created

### **Code Structure:**
- **Controllers:** 7
- **Models:** 7
- **Views:** 30+
- **Routes:** 25+
- **Migrations:** 11
- **Seeders:** 4

### **Features:**
1. User Authentication
2. Password Reset
3. Dashboard with Live Matches
4. Match Listings & Details
5. League Standings
6. Player Profiles
7. Dream Team Builder
8. Favorite Teams
9. AJAX Search
10. Responsive Design

---

## 🎓 Key Points for Teacher Presentation

### **1. MVC Explained:**
"Our project uses MVC architecture which separates data (Models), display (Views), and logic (Controllers). For example, when viewing a match, the MatchController fetches data from FootballMatch model and passes it to the show.blade.php view."

### **2. Database Design:**
"We have 8 tables with proper relationships. For instance, a Team belongs to a League, has many Players, and participates in Matches. We use foreign keys for data integrity."

### **3. Routing:**
"Routes map URLs to controllers. When a user visits /matches/5, Laravel calls MatchController's show method with ID 5. We use named routes like 'matches.show' for flexibility."

### **4. Middleware:**
"Middleware acts as a filter. Our auth.user middleware checks if users are logged in before accessing Dream Team features. If not logged in, it redirects to homepage."

### **5. Sessions:**
"When users log in, Laravel creates a session file storing their ID. A session cookie connects the browser to this file. Sessions last 120 minutes and are encrypted for security."

### **6. API Integration:**
"We fetch real football data from Football-Data.org API. We make HTTP requests with an API key, parse JSON responses, and store data in our MySQL database. We respect rate limits (10 requests/minute)."

### **7. Dream Team Feature:**
"This is our main feature. Users select 11 players in formations like 4-3-3. JavaScript handles the visual field, AJAX searches players, and we validate 11 players minimum. Player data is stored as JSON in the database."

### **8. Security:**
- CSRF protection on all forms
- Password hashing with bcrypt
- Token-based password reset (60-min expiry)
- Middleware authentication
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade escaping)

---

## 📝 Common Questions & Answers

**Q: What's the difference between a route and a controller?**
A: Routes define URL patterns and map them to controllers. Controllers contain the actual logic to handle requests.

**Q: Why use Eloquent instead of raw SQL?**
A: Eloquent provides object-oriented database interaction, prevents SQL injection, and makes relationships easy. For example, `$team->players` instead of writing JOIN queries.

**Q: How do migrations work?**
A: Migrations are version control for your database. Each migration file has `up()` to create/modify and `down()` to rollback. Run with `php artisan migrate`.

**Q: What's the purpose of seeders?**
A: Seeders populate database with test data. We use LeagueSeeder, TeamSeeder, and PlayerSeeder to create 1,525 sample players.

**Q: How does middleware protect routes?**
A: Middleware runs before the controller. Our `auth.user` middleware checks `Auth::check()`. If false, it redirects. Only logged-in users can create Dream Teams.

**Q: What are sessions used for?**
A: Sessions store user state across requests. They remember who's logged in, flash messages (success/error), and shopping cart data.

**Q: How do you fetch API data?**
A: We use Laravel's HTTP client with API key headers. We parse JSON responses and use Eloquent to store in database.

**Q: What's JSON casting in models?**
A: The `'players' => 'array'` cast automatically converts JSON database field to PHP array when retrieving, and array to JSON when saving.

---

## 🚀 Project Highlights

✅ Full-stack Laravel application  
✅ MVC architecture  
✅ 8-table database with relationships  
✅ Real API integration (Football Data API)  
✅ User authentication & password reset  
✅ Dream Team builder with AJAX  
✅ Responsive design (Tailwind CSS)  
✅ Form validation  
✅ Security best practices  
✅ Clean, organized code structure  

---

**Good luck with your presentation! ⚽🎓**
