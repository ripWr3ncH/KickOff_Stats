# Services Directory

Services contain business logic for external integrations. This separates API logic from controllers.

## 📄 Files

### **FootballDataService.php** ⭐ (Main API Service)

**Purpose:** Fetch football data from API-Football (RapidAPI)

**API Provider:** API-Football (https://www.api-football.com/)
- Host: `api-football-v1.p.rapidapi.com`
- Authentication: RapidAPI key in headers
- Rate Limit: Based on subscription plan

---

## 🔑 Configuration

**Environment Variables (.env):**
```env
RAPIDAPI_KEY=your_key_here
RAPIDAPI_HOST=api-football-v1.p.rapidapi.com
```

**Access in Service:**
```php
$apiKey = env('RAPIDAPI_KEY');
$apiHost = env('RAPIDAPI_HOST');
```

---

## 📡 Available Methods

### **1. getLeagues()** - Fetch Leagues
```php
public function getLeagues()
```
- **Endpoint:** `/v3/leagues`
- **Returns:** Array of leagues
- **Response:** JSON automatically parsed via `->json()`
- **Used By:** LeagueSeeder

**Example Response:**
```json
{
  "response": [
    {
      "league": {
        "id": 39,
        "name": "Premier League",
        "country": "England",
        "logo": "https://..."
      }
    }
  ]
}
```

---

### **2. getTeamsByLeague($leagueId)** - Fetch Teams in League
```php
public function getTeamsByLeague(int $leagueId)
```
- **Endpoint:** `/v3/teams?league={leagueId}&season=2024`
- **Parameters:**
  - `$leagueId` - League ID (39, 140, 135, 78, 61)
- **Returns:** Array of teams
- **Used By:** TeamSeeder

**Example Response:**
```json
{
  "response": [
    {
      "team": {
        "id": 33,
        "name": "Manchester United",
        "logo": "https://..."
      }
    }
  ]
}
```

---

### **3. getPlayersByTeam($teamId)** - Fetch Team Squad
```php
public function getPlayersByTeam(int $teamId)
```
- **Endpoint:** `/v3/players/squads?team={teamId}`
- **Parameters:**
  - `$teamId` - Team ID
- **Returns:** Array of players
- **Used By:** PlayerSeeder

**Example Response:**
```json
{
  "response": [
    {
      "players": [
        {
          "id": 276,
          "name": "Bruno Fernandes",
          "age": 29,
          "number": 8,
          "position": "Midfielder",
          "photo": "https://..."
        }
      ]
    }
  ]
}
```

---

### **4. getFixtures($leagueId, $season)** - Fetch Matches
```php
public function getFixtures(int $leagueId, int $season = 2024)
```
- **Endpoint:** `/v3/fixtures?league={leagueId}&season={season}`
- **Parameters:**
  - `$leagueId` - League ID
  - `$season` - Season year (default: 2024)
- **Returns:** Array of matches
- **Used By:** Match import commands

**Example Response:**
```json
{
  "response": [
    {
      "fixture": {
        "id": 12345,
        "date": "2024-12-20T20:00:00+00:00",
        "status": {
          "short": "FT"
        }
      },
      "teams": {
        "home": {
          "id": 33,
          "name": "Manchester United"
        },
        "away": {
          "id": 34,
          "name": "Newcastle United"
        }
      },
      "goals": {
        "home": 2,
        "away": 1
      }
    }
  ]
}
```

---

### **5. getLiveMatches()** - Fetch Live Matches
```php
public function getLiveMatches()
```
- **Endpoint:** `/v3/fixtures?live=all`
- **Returns:** Array of live matches (currently playing)
- **Used By:** MatchController@live, dashboard live updates

**Example Response:**
```json
{
  "response": [
    {
      "fixture": {
        "status": {
          "short": "2H",
          "elapsed": 67
        }
      },
      "goals": {
        "home": 1,
        "away": 2
      }
    }
  ]
}
```

---

### **6. getMatchStatistics($fixtureId)** - Fetch Match Stats
```php
public function getMatchStatistics(int $fixtureId)
```
- **Endpoint:** `/v3/fixtures/statistics?fixture={fixtureId}`
- **Parameters:**
  - `$fixtureId` - Match ID
- **Returns:** Match statistics (possession, shots, cards, etc.)
- **Used By:** MatchController@show

**Example Response:**
```json
{
  "response": [
    {
      "team": {
        "id": 33,
        "name": "Manchester United"
      },
      "statistics": [
        {
          "type": "Ball Possession",
          "value": "58%"
        },
        {
          "type": "Total Shots",
          "value": 15
        }
      ]
    }
  ]
}
```

---

### **7. getMatchLineups($fixtureId)** - Fetch Lineups
```php
public function getMatchLineups(int $fixtureId)
```
- **Endpoint:** `/v3/fixtures/lineups?fixture={fixtureId}`
- **Parameters:**
  - `$fixtureId` - Match ID
- **Returns:** Starting XI and substitutes
- **Used By:** MatchController@show

---

### **8. getPlayerStatistics($fixtureId)** - Fetch Player Stats
```php
public function getPlayerStatistics(int $fixtureId)
```
- **Endpoint:** `/v3/fixtures/players?fixture={fixtureId}`
- **Parameters:**
  - `$fixtureId` - Match ID
- **Returns:** Player stats (goals, assists, cards, rating)
- **Used By:** PlayerStat model population

**Example Response:**
```json
{
  "response": [
    {
      "team": {
        "id": 33
      },
      "players": [
        {
          "player": {
            "id": 276,
            "name": "Bruno Fernandes"
          },
          "statistics": [
            {
              "goals": {
                "total": 1,
                "assists": 0
              },
              "cards": {
                "yellow": 0,
                "red": 0
              }
            }
          ]
        }
      ]
    }
  ]
}
```

---

### **9. getStandings($leagueId, $season)** - Fetch League Table
```php
public function getStandings(int $leagueId, int $season = 2024)
```
- **Endpoint:** `/v3/standings?league={leagueId}&season={season}`
- **Parameters:**
  - `$leagueId` - League ID
  - `$season` - Season year
- **Returns:** League standings table
- **Used By:** LeagueController@show

**Example Response:**
```json
{
  "response": [
    {
      "league": {
        "standings": [
          [
            {
              "rank": 1,
              "team": {
                "id": 33,
                "name": "Manchester United"
              },
              "points": 45,
              "all": {
                "played": 20,
                "win": 14,
                "draw": 3,
                "lose": 3,
                "goals": {
                  "for": 42,
                  "against": 18
                }
              }
            }
          ]
        ]
      }
    }
  ]
}
```

---

## 🔄 JSON Parsing Process

### **Automatic Parsing**
All methods use Laravel's `Http` facade:
```php
use Illuminate\Support\Facades\Http;

$response = Http::withHeaders([
    'X-RapidAPI-Key' => $apiKey,
    'X-RapidAPI-Host' => $apiHost
])->get($url);

return $response->json(); // Automatically calls json_decode($body, true)
```

**Internal Process:**
1. `Http::get($url)` - Send GET request
2. `$response->json()` - Parse JSON response
3. Returns PHP array (associative)
4. No manual `json_decode()` needed

---

## 🎯 Usage in Controllers

### **Example 1: LeagueController**
```php
use App\Services\FootballDataService;

public function index(FootballDataService $service)
{
    $leagues = $service->getLeagues();
    
    // $leagues is already an array (parsed from JSON)
    return view('leagues.index', compact('leagues'));
}
```

### **Example 2: MatchController**
```php
public function show(Match $match, FootballDataService $service)
{
    $stats = $service->getMatchStatistics($match->api_fixture_id);
    $lineups = $service->getMatchLineups($match->api_fixture_id);
    
    return view('matches.show', compact('match', 'stats', 'lineups'));
}
```

---

## 🚨 Error Handling

### **HTTP Errors**
```php
$response = Http::get($url);

if ($response->failed()) {
    // Handle error (log, return empty, throw exception)
    \Log::error('API request failed', ['url' => $url]);
    return [];
}

return $response->json();
```

### **Rate Limit Handling**
API-Football has rate limits based on subscription:
- Free: 100 requests/day
- Basic: 10,000 requests/day
- Pro: 30,000 requests/day

---

## 🔧 Service Usage Pattern

### **1. Dependency Injection in Controller**
```php
public function index(FootballDataService $service)
{
    // Laravel automatically injects service instance
}
```

### **2. Manual Instantiation**
```php
$service = new FootballDataService();
$leagues = $service->getLeagues();
```

### **3. In Commands**
```php
use App\Services\FootballDataService;

class ImportRealMatches extends Command
{
    public function handle(FootballDataService $service)
    {
        $matches = $service->getFixtures(39);
        // Process matches
    }
}
```

---

## 💡 Key Points for Understanding

1. **FootballDataService** - Centralized API communication
2. **Http facade** - Laravel's HTTP client (automatic JSON parsing)
3. **->json()** - Automatically decodes JSON response
4. **RapidAPI** - API provider requiring key in headers
5. **Dependency injection** - Service auto-injected into controllers
6. **No manual json_decode()** - Laravel handles it
7. **Rate limits** - Be aware of API subscription limits
8. **Error handling** - Always check `$response->failed()`

---

## 🔗 API Documentation

**Official Docs:** https://www.api-football.com/documentation-v3

**Available Endpoints:**
- Leagues & Seasons
- Teams & Venues
- Players & Squads
- Fixtures & Live Scores
- Statistics & Events
- Standings & Tables
- Predictions & Odds

---

**Related Directories:**
- 📁 [`app/Http/Controllers/`](../Http/Controllers/) - Uses service methods
- 📁 [`app/Console/Commands/`](../Console/Commands/) - Imports data via service
- 📁 [`database/seeders/`](../../database/seeders/) - Populates database using service
- 📁 [`app/Models/`](../Models/) - Stores data fetched by service
- 📄 [`.env`](../../.env) - API credentials
