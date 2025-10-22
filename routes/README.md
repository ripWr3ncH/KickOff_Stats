# Routes Directory

Routes define how HTTP requests map to controller methods. All web routes are in `web.php`.

## 📄 Files

### **web.php** ⭐
- **Purpose:** All application routes (25+ routes)
- **Middleware:** Web middleware group (sessions, CSRF, cookies)

---

## 🛣️ Route Structure

### **Public Routes** (No Login Required)
```php
// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// API Status Check
Route::get('/api-status', [HomeController::class, 'checkApiStatus'])->name('api.status');

// Matches
Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
Route::get('/matches/live', [MatchController::class, 'live'])->name('matches.live');

// Leagues
Route::get('/leagues', [LeagueController::class, 'index'])->name('leagues.index');
Route::get('/leagues/{league}', [LeagueController::class, 'show'])->name('leagues.show');

// Players
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
Route::get('/players/{player}', [PlayerController::class, 'show'])->name('players.show');

// Teams
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');

// News
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
```

### **Protected Routes** (Login Required)
Uses `auth.user` middleware:
```php
Route::middleware(['auth.user'])->group(function () {
    
    // Dream Team Builder
    Route::get('/dream-team', [DreamTeamController::class, 'index'])->name('dream-team.index');
    Route::get('/dream-team/create', [DreamTeamController::class, 'create'])->name('dream-team.create');
    Route::post('/dream-team', [DreamTeamController::class, 'store'])->name('dream-team.store');
    Route::get('/dream-team/{dreamTeam}', [DreamTeamController::class, 'show'])->name('dream-team.show');
    Route::get('/dream-team/{dreamTeam}/edit', [DreamTeamController::class, 'edit'])->name('dream-team.edit');
    Route::put('/dream-team/{dreamTeam}', [DreamTeamController::class, 'update'])->name('dream-team.update');
    Route::delete('/dream-team/{dreamTeam}', [DreamTeamController::class, 'destroy'])->name('dream-team.destroy');
    
    // Favorite Teams
    Route::get('/my-teams', [MyTeamsController::class, 'index'])->name('my-teams.index');
    Route::get('/my-teams/select', [MyTeamsController::class, 'select'])->name('my-teams.select');
    Route::post('/my-teams/add', [MyTeamsController::class, 'add'])->name('my-teams.add');
    Route::delete('/my-teams/remove/{team}', [MyTeamsController::class, 'remove'])->name('my-teams.remove');
});
```

### **Authentication Routes** (Guest Only)
```php
// Login (POST handled by AuthController)
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Register
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
```

### **AJAX/API Routes** (Internal API)
```php
// Player Search for Dream Team Builder
Route::get('/dream-team/search-players', [DreamTeamController::class, 'searchPlayers'])
     ->name('dream-team.search-players')
     ->middleware('auth.user');

// Live Score Updates (if exists)
Route::get('/api/live-scores', [MatchController::class, 'liveScores'])->name('api.live-scores');
```

---

## 🎯 Route Patterns

### **Resource Routes Pattern**
Dream Team follows RESTful pattern:
- `GET /dream-team` - index (list all)
- `GET /dream-team/create` - create (show form)
- `POST /dream-team` - store (save new)
- `GET /dream-team/{id}` - show (view one)
- `GET /dream-team/{id}/edit` - edit (show form)
- `PUT /dream-team/{id}` - update (save changes)
- `DELETE /dream-team/{id}` - destroy (delete)

### **Named Routes**
All routes have names for easy URL generation:
```php
// In controller:
return redirect()->route('dream-team.index');

// In view:
<a href="{{ route('matches.show', $match) }}">View Match</a>

// With parameters:
<form action="{{ route('dream-team.update', $dreamTeam) }}" method="POST">
    @csrf
    @method('PUT')
</form>
```

### **Route Model Binding**
Automatic model loading by ID:
```php
// Route definition:
Route::get('/players/{player}', [PlayerController::class, 'show']);

// Controller receives model instance:
public function show(Player $player) {
    // $player is already loaded from database
}
```

---

## 🔒 Middleware

### **auth.user** (Custom Middleware)
- **File:** `app/Http/Middleware/AuthenticateUser.php`
- **Purpose:** Protect routes requiring login
- **Applied To:** Dream Team routes, My Teams routes
- **Behavior:** Redirects to homepage with error if not logged in

### **web** (Built-in Middleware Group)
- **Applied To:** All routes in web.php
- **Includes:**
  - Session handling
  - CSRF protection
  - Cookie encryption
  - Route model binding

### **guest** (Built-in Middleware)
- **Purpose:** Only allow non-authenticated users
- **Applied To:** Login/Register routes (prevents double login)

---

## 🌐 HTTP Methods

### **GET** - Retrieve data
```php
Route::get('/players', [PlayerController::class, 'index']);
```

### **POST** - Create new resource
```php
Route::post('/dream-team', [DreamTeamController::class, 'store']);
```

### **PUT/PATCH** - Update existing resource
```php
Route::put('/dream-team/{dreamTeam}', [DreamTeamController::class, 'update']);
```

### **DELETE** - Delete resource
```php
Route::delete('/dream-team/{dreamTeam}', [DreamTeamController::class, 'destroy']);
```

---

## 📝 Route Parameters

### **Required Parameters**
```php
Route::get('/matches/{match}', ...);  // {match} is required
```

### **Optional Parameters**
```php
Route::get('/search/{term?}', ...);  // {term?} is optional
```

### **Multiple Parameters**
```php
Route::get('/leagues/{league}/teams/{team}', ...);
```

### **Regular Expression Constraints**
```php
Route::get('/user/{id}', ...)->where('id', '[0-9]+');  // Only numbers
```

---

## 🔍 Route Groups

### **Middleware Group**
```php
Route::middleware(['auth.user'])->group(function () {
    Route::get('/dream-team', ...);
    Route::post('/dream-team', ...);
    // All routes inside use auth.user middleware
});
```

### **Prefix Group**
```php
Route::prefix('admin')->group(function () {
    Route::get('/users', ...);      // URL: /admin/users
    Route::get('/settings', ...);   // URL: /admin/settings
});
```

### **Name Prefix Group**
```php
Route::name('admin.')->group(function () {
    Route::get('/users', ...)->name('users');  // Route name: admin.users
});
```

---

## 🚀 Redirect Routes

### **Simple Redirect**
```php
Route::redirect('/here', '/there');
```

### **Permanent Redirect (301)**
```php
Route::permanentRedirect('/old-url', '/new-url');
```

---

## 📊 Route List Command

View all routes in terminal:
```bash
php artisan route:list
```

Output shows:
- HTTP Method (GET, POST, PUT, DELETE)
- URI (/matches, /players/{player})
- Name (matches.index, players.show)
- Controller Action (MatchController@index)
- Middleware (web, auth.user)

---

## 💡 Key Points for Understanding

1. **web.php** - All web routes (browser-accessible)
2. **Named routes** - Use `route('name')` instead of hardcoding URLs
3. **Route model binding** - Automatic model loading by ID
4. **Middleware** - Protect routes (auth.user for login required)
5. **RESTful pattern** - index, create, store, show, edit, update, destroy
6. **HTTP methods** - GET (view), POST (create), PUT (update), DELETE (remove)
7. **Route groups** - Apply middleware to multiple routes
8. **CSRF protection** - All POST/PUT/DELETE forms need @csrf token

---

**Related Directories:**
- 📁 [`app/Http/Controllers/`](../app/Http/Controllers/) - Route targets
- 📁 [`app/Http/Middleware/`](../app/Http/Middleware/) - Route protection
- 📁 [`resources/views/`](../resources/views/) - Returned by routes
- 📁 [`app/Models/`](../app/Models/) - Route model binding
