# Controllers Directory

Controllers handle HTTP requests, process data, and return responses (views/JSON).

## 📂 Files in this Directory

### **HomeController.php**
- **Purpose:** Homepage/Dashboard logic
- **Routes:** `/` (GET)
- **Methods:**
  - `index()` - Displays homepage with live matches, upcoming matches, and leagues
  - `apiStatus()` - Shows API connection status page
- **Views:** `dashboard.blade.php`, `api-status.blade.php`
- **Used By:** Main landing page

---

### **MatchController.php**
- **Purpose:** Match listing, details, and filtering
- **Routes:** 
  - `/matches` (GET) - All matches
  - `/matches/{match}` (GET) - Single match details
  - `/matches/live` (GET) - Live matches only
- **Methods:**
  - `index()` - List all matches with filters (date, league, status)
  - `show()` - Display single match details with stats
  - `live()` - Show only live matches
- **Views:** `matches/index.blade.php`, `matches/show.blade.php`, `matches/live.blade.php`
- **Features:** Date picker, league filter, pagination

---

### **LeagueController.php**
- **Purpose:** League standings and details
- **Routes:** 
  - `/leagues` (GET) - All leagues
  - `/leagues/{league}` (GET) - League table with standings
- **Methods:**
  - `index()` - List all leagues
  - `show()` - Display league table with team standings
- **Views:** `leagues/index.blade.php`, `leagues/show.blade.php`
- **Features:** Points calculation, win/loss stats, goal difference

---

### **PlayerController.php**
- **Purpose:** Player profiles and search
- **Routes:**
  - `/players` (GET) - All players with search
  - `/players/{player}` (GET) - Player profile
- **Methods:**
  - `index()` - List players with search/filter
  - `show()` - Display player profile with stats
- **Views:** `players/index.blade.php`, `players/show.blade.php`
- **Features:** Search by name, filter by position

---

### **DreamTeamController.php** ⭐ (Main Feature)
- **Purpose:** Fantasy team builder - CRUD operations
- **Routes:** (All require authentication)
  - `/dream-team` (GET) - User's dream teams list
  - `/dream-team/create` (GET) - Formation builder
  - `/dream-team` (POST) - Save new dream team
  - `/dream-team/{dreamTeam}` (GET) - View team details
  - `/dream-team/{dreamTeam}/edit` (GET) - Edit team form
  - `/dream-team/{dreamTeam}` (PUT) - Update team
  - `/dream-team/{dreamTeam}` (DELETE) - Delete team
  - `/api/dream-team/search-players` (GET) - AJAX player search
- **Methods:**
  - `index()` - List user's teams
  - `create()` - Show formation builder
  - `store()` - Save new team (validates 11 players)
  - `show()` - Display team details
  - `edit()` - Show edit form
  - `update()` - Save changes
  - `destroy()` - Delete team
  - `searchPlayers()` - AJAX endpoint for player search
- **Views:** `dream-team/*.blade.php`
- **Features:** 
  - 5 formations (4-3-3, 4-4-2, 3-5-2, 5-3-2, 4-2-3-1)
  - Visual field layout
  - Player search with AJAX
  - 11-player validation
  - Market value calculation
  - Public/Private teams

---

### **MyTeamsController.php**
- **Purpose:** User's favorite teams
- **Routes:**
  - `/my-teams` (GET) - List favorite teams
  - `/my-teams/{team}/toggle` (POST) - Add/remove favorite
  - `/my-teams/select` (GET) - Team selection page
- **Methods:**
  - `index()` - Show user's favorite teams
  - `toggle()` - Add/remove team from favorites
  - `select()` - Team picker interface
- **Views:** `my-teams/*.blade.php`
- **Middleware:** `auth.user` (requires login)

---

### **TeamController.php**
- **Purpose:** Team details and management
- **Routes:** `/teams/{team}` (GET)
- **Methods:**
  - `show()` - Display team details with players and matches
- **Views:** `teams/show.blade.php`

---

### **NewsController.php**
- **Purpose:** News/blog system
- **Routes:** `/news` (GET)
- **Methods:**
  - `index()` - List all news articles
- **Views:** `news/index.blade.php`

---

## 📂 Auth Subdirectory

### **Auth/AuthController.php**
- **Purpose:** User authentication (login, register, logout)
- **Routes:**
  - `/login` (POST) - Process login
  - `/register` (POST) - Create account
  - `/logout` (POST) - Log out user
- **Methods:**
  - `login()` - Authenticate user with `Auth::attempt()`
  - `register()` - Create new user account
  - `logout()` - End session
- **Features:**
  - Password hashing (bcrypt)
  - Session management
  - "Remember Me" functionality
  - Flash messages

---

### **Auth/PasswordResetController.php**
- **Purpose:** Password reset with email tokens
- **Routes:**
  - `/forgot-password` (GET) - Email input form
  - `/forgot-password` (POST) - Send reset link
  - `/reset-password/{token}` (GET) - New password form
  - `/reset-password` (POST) - Update password
- **Methods:**
  - `showForgotForm()` - Display email form
  - `sendResetLink()` - Generate token and email link
  - `showResetForm()` - Display password reset form
  - `reset()` - Update password in database
- **Views:** `auth/forgot-password.blade.php`, `auth/reset-password.blade.php`
- **Security:**
  - Token hashing with bcrypt
  - 60-minute expiry
  - Email validation

---

## 🔄 Request Flow

```
User Request → Route → Controller Method → Model (Database) → View → Response
```

**Example:**
```
User visits /matches/5
    ↓
Route: Route::get('/matches/{match}', [MatchController::class, 'show'])
    ↓
Controller: MatchController@show($match)
    ↓
Model: FootballMatch::with(['homeTeam', 'awayTeam', 'league'])->find(5)
    ↓
View: return view('matches.show', compact('match'))
    ↓
Browser: HTML page displayed
```

---

## 🛡️ Middleware Protected Routes

Controllers that require authentication (user must be logged in):
- ✅ **DreamTeamController** - All methods
- ✅ **MyTeamsController** - All methods

Controllers accessible without login:
- ✅ **HomeController**
- ✅ **MatchController**
- ✅ **LeagueController**
- ✅ **PlayerController**
- ✅ **TeamController**

---

## 💡 Common Controller Patterns

### **1. List Resources (index)**
```php
public function index() {
    $items = Model::paginate(20);
    return view('folder.index', compact('items'));
}
```

### **2. Show Single Resource (show)**
```php
public function show(Model $model) {
    $model->load('relationships');
    return view('folder.show', compact('model'));
}
```

### **3. Create Resource (store)**
```php
public function store(Request $request) {
    $request->validate([...]);
    Model::create($request->validated());
    return redirect()->route('route.name')->with('success', 'Created!');
}
```

### **4. Update Resource (update)**
```php
public function update(Request $request, Model $model) {
    $request->validate([...]);
    $model->update($request->validated());
    return redirect()->route('route.show', $model)->with('success', 'Updated!');
}
```

### **5. Delete Resource (destroy)**
```php
public function destroy(Model $model) {
    $model->delete();
    return redirect()->route('route.index')->with('success', 'Deleted!');
}
```

---

## 🎯 Key Points for Understanding

1. **Controllers are the "traffic controllers"** - They decide what happens with each request
2. **Keep controllers thin** - Complex logic should be in Models or Services
3. **Use type-hinting** - `Model $model` automatically loads the model
4. **Flash messages** - Use `->with('success', 'Message')` for one-time messages
5. **Validation** - Always validate user input with `$request->validate()`
6. **Authorization** - Check ownership with `if ($model->user_id !== Auth::id())`

---

**Related Directories:**
- 📁 [`app/Models/`](../Models/) - Database models
- 📁 [`resources/views/`](../../resources/views/) - View templates
- 📁 [`routes/web.php`](../../../routes/web.php) - Route definitions
- 📁 [`app/Http/Middleware/`](../Middleware/) - Request filters
