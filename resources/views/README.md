# Views Directory

Views are Blade templates that generate HTML displayed to users. Blade is Laravel's templating engine.

## 📂 Directory Structure

```
resources/views/
├── layouts/              # Master layouts
│   └── app.blade.php    # Main layout (header, footer, nav)
├── auth/                # Authentication views
│   ├── forgot-password.blade.php
│   └── reset-password.blade.php
├── emails/              # Email templates
│   └── password-reset.blade.php
├── dashboard.blade.php  # Homepage
├── welcome.blade.php    # Alternative homepage
├── api-status.blade.php # API status page
├── matches/             # Match-related views
├── leagues/             # League-related views
├── players/             # Player-related views
├── dream-team/          # Dream Team builder views
├── my-teams/            # Favorite teams views
├── teams/               # Team details views
└── news/                # News views
```

---

## 📄 Main Files

### **layouts/app.blade.php** ⭐ (Master Layout)
- **Purpose:** Main layout used by all pages
- **Contains:**
  - Header with navigation
  - Login/Register modals
  - Flash message notifications
  - Footer
  - JavaScript (session management, animations)
  - CSS (Tailwind + custom styles)
- **Sections:**
  - `@yield('title')` - Page title
  - `@yield('content')` - Main page content
  - `@yield('scripts')` - Page-specific JavaScript
- **Features:**
  - Responsive navigation with mobile menu
  - User dropdown (if logged in)
  - Dark theme (primary: #00D1FF)
  - Flash messages (success, error, info)
  - Live score updates
- **Used By:** Every page in the application

---

### **dashboard.blade.php** (Homepage)
- **Controller:** HomeController@index
- **Route:** `/`
- **Extends:** `layouts/app.blade.php`
- **Shows:**
  - Live matches (if any)
  - Upcoming matches
  - League cards
  - Quick stats
- **Features:**
  - Real-time score updates
  - Match status badges
  - Team logos
  - League logos

---

## 📂 Subdirectories

### **matches/** (Match Views)

#### **matches/index.blade.php**
- **Purpose:** All matches listing with filters
- **Controller:** MatchController@index
- **Route:** `/matches`
- **Features:**
  - Date picker filter
  - League dropdown filter
  - Status filter (scheduled, live, finished)
  - Pagination
  - Grouped by league
  - Match cards with scores
- **Shows:** All matches or filtered results

#### **matches/show.blade.php**
- **Purpose:** Single match details
- **Controller:** MatchController@show
- **Route:** `/matches/{match}`
- **Features:**
  - Match header (teams, scores, date)
  - Match statistics
  - Player lineup
  - Player stats (goals, assists, cards)
  - Match events timeline
- **Shows:** Complete match information

#### **matches/live.blade.php**
- **Purpose:** Live matches only
- **Controller:** MatchController@live
- **Route:** `/matches/live`
- **Features:**
  - Auto-refresh
  - Pulse animation on live matches
  - Current minute display
  - Live score updates
- **Shows:** Only matches with status "live"

---

### **leagues/** (League Views)

#### **leagues/index.blade.php**
- **Purpose:** All leagues listing
- **Controller:** LeagueController@index
- **Route:** `/leagues`
- **Features:**
  - League cards with logos
  - Team count
  - Match count
  - Link to league standings
- **Shows:** 5 leagues (Premier League, La Liga, Serie A, Bundesliga, Ligue 1)

#### **leagues/show.blade.php**
- **Purpose:** League table with standings
- **Controller:** LeagueController@show
- **Route:** `/leagues/{league}`
- **Features:**
  - League header with logo
  - Standings table (position, team, played, won, drawn, lost, GF, GA, GD, points)
  - Team logos
  - Sortable columns
  - Color-coded positions (top 4, relegation zone)
- **Shows:** Complete league table

---

### **players/** (Player Views)

#### **players/index.blade.php**
- **Purpose:** All players listing with search
- **Controller:** PlayerController@index
- **Route:** `/players`
- **Features:**
  - Search by name
  - Filter by position (GK, DEF, MID, FWD)
  - Filter by team
  - Pagination
  - Player cards with photo
  - Market value display
- **Shows:** 1,525 players

#### **players/show.blade.php**
- **Purpose:** Player profile page
- **Controller:** PlayerController@show
- **Route:** `/players/{player}`
- **Features:**
  - Player photo
  - Personal info (age, nationality, height, weight)
  - Market value
  - Position and jersey number
  - Team affiliation
  - Career statistics
  - Recent matches
- **Shows:** Complete player profile

---

### **dream-team/** (Dream Team Views) ⭐

#### **dream-team/index.blade.php**
- **Purpose:** User's dream teams list
- **Controller:** DreamTeamController@index
- **Route:** `/dream-team`
- **Requires:** Login (auth.user middleware)
- **Features:**
  - Team cards
  - Formation display
  - Player count
  - Total market value
  - Public/Private badge
  - Edit/Delete buttons
  - "Create New" button
- **Shows:** User's created dream teams

#### **dream-team/create.blade.php** ⭐ (Main Feature)
- **Purpose:** Formation builder for creating dream teams
- **Controller:** DreamTeamController@create
- **Route:** `/dream-team/create`
- **Requires:** Login
- **Features:**
  - Team settings form (name, formation, description, public/private)
  - Visual football field with CSS-drawn lines
  - Formation selector (4-3-3, 4-4-2, 3-5-2, 5-3-2, 4-2-3-1)
  - Player position slots on field
  - Player search (AJAX)
  - Search filters (name, position, league)
  - Player cards with photos and stats
  - Real-time player counter (0/11 → 11/11)
  - Market value display
  - Form validation (11 players required)
  - Loading state on submit
- **JavaScript Features:**
  - Formation position mapping
  - Player slot management
  - AJAX player search
  - JSON serialization
  - Click to select player
  - Visual feedback
- **Shows:** Interactive dream team builder

#### **dream-team/show.blade.php**
- **Purpose:** View dream team details
- **Controller:** DreamTeamController@show
- **Route:** `/dream-team/{dreamTeam}`
- **Features:**
  - Team name and description
  - Formation display
  - Visual field with player names
  - Player details
  - Total market value
  - Edit/Delete buttons (if owner)
  - Share button (if public)
- **Shows:** Complete dream team

#### **dream-team/edit.blade.php**
- **Purpose:** Edit existing dream team
- **Controller:** DreamTeamController@edit
- **Route:** `/dream-team/{dreamTeam}/edit`
- **Requires:** Ownership check
- **Features:** Same as create.blade.php but pre-filled
- **Shows:** Edit form with current team data

---

### **my-teams/** (Favorite Teams Views)

#### **my-teams/index.blade.php**
- **Purpose:** User's favorite teams
- **Controller:** MyTeamsController@index
- **Route:** `/my-teams`
- **Requires:** Login
- **Features:**
  - Team cards with logos
  - Upcoming matches for each team
  - Recent results
  - Remove favorite button
- **Shows:** User's selected favorite teams

#### **my-teams/select.blade.php**
- **Purpose:** Team selection interface
- **Controller:** MyTeamsController@select
- **Route:** `/my-teams/select`
- **Requires:** Login
- **Features:**
  - All teams grid
  - Search by name
  - Filter by league
  - Add/Remove toggle buttons
- **Shows:** Team picker interface

---

### **auth/** (Authentication Views)

#### **auth/forgot-password.blade.php**
- **Purpose:** Password reset request form
- **Controller:** PasswordResetController@showForgotForm
- **Route:** `/forgot-password`
- **Features:**
  - Email input field
  - Submit button
  - Link back to login
- **Shows:** Email input for password reset

#### **auth/reset-password.blade.php**
- **Purpose:** New password form
- **Controller:** PasswordResetController@showResetForm
- **Route:** `/reset-password/{token}`
- **Features:**
  - Hidden token field
  - Email field
  - New password field
  - Password confirmation field
  - Submit button
- **Shows:** Password reset form with token

---

### **emails/** (Email Templates)

#### **emails/password-reset.blade.php**
- **Purpose:** Password reset email template
- **Used By:** PasswordResetController@sendResetLink
- **Features:**
  - HTML email design
  - Reset link button
  - Expiry warning (60 minutes)
  - Footer with app info
- **Sent When:** User requests password reset

---

## 🎨 Blade Templating Syntax

### **Extending Layouts**
```blade
@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
    <h1>Page Content</h1>
@endsection
```

### **Displaying Data**
```blade
{{ $variable }}           <!-- Escaped (safe) -->
{!! $htmlContent !!}      <!-- Unescaped (use carefully) -->
{{ $user->name ?? 'Guest' }}  <!-- With default value -->
```

### **Control Structures**
```blade
@if($matches->count() > 0)
    <p>We have matches!</p>
@else
    <p>No matches found</p>
@endif

@foreach($matches as $match)
    <div>{{ $match->homeTeam->name }}</div>
@endforeach

@auth
    <p>User is logged in</p>
@endauth

@guest
    <p>User is not logged in</p>
@endguest
```

### **Including Subviews**
```blade
@include('partials.header')
@include('partials.footer')
```

### **Components**
```blade
<x-button color="primary">Click Me</x-button>
```

### **CSRF Protection**
```blade
<form method="POST">
    @csrf   <!-- Security token -->
    <input type="text" name="name">
</form>
```

### **Method Spoofing**
```blade
<form method="POST">
    @csrf
    @method('PUT')   <!-- For PUT/PATCH/DELETE -->
</form>
```

### **Error Messages**
```blade
@error('email')
    <div class="error">{{ $message }}</div>
@enderror
```

### **Session Flash Messages**
```blade
@if(session('success'))
    <div class="alert">{{ session('success') }}</div>
@endif
```

### **Old Input (After Validation Error)**
```blade
<input type="text" name="name" value="{{ old('name') }}">
```

---

## 🎯 Common View Patterns

### **Master-Detail Pattern**
```blade
<!-- master view: leagues/index.blade.php -->
@foreach($leagues as $league)
    <a href="{{ route('leagues.show', $league) }}">
        {{ $league->name }}
    </a>
@endforeach

<!-- detail view: leagues/show.blade.php -->
<h1>{{ $league->name }}</h1>
<p>{{ $league->country }}</p>
```

### **List with Search**
```blade
<form method="GET">
    <input type="text" name="search" value="{{ request('search') }}">
    <button type="submit">Search</button>
</form>

@foreach($players as $player)
    <div>{{ $player->name }}</div>
@endforeach

{{ $players->links() }}  <!-- Pagination -->
```

### **Form with Validation**
```blade
<form method="POST" action="{{ route('dream-team.store') }}">
    @csrf
    
    <input type="text" name="name" value="{{ old('name') }}">
    @error('name')
        <span class="error">{{ $message }}</span>
    @enderror
    
    <button type="submit">Create</button>
</form>
```

---

## 🎨 Styling

All views use:
- **Tailwind CSS** - Utility-first CSS framework
- **Custom CSS** - In `layouts/app.blade.php` `<style>` section
- **Font Awesome** - For icons
- **Responsive Design** - Mobile-first approach

**Color Scheme:**
- Primary: `#00D1FF` (cyan)
- Background: `#1a1a1a` (dark)
- Card: `#2d2d2d` (dark gray)
- Text: `#ffffff` (white/light)

---

## 💡 JavaScript in Views

### **Inline JavaScript**
```blade
@section('scripts')
<script>
    console.log('Page-specific JavaScript');
</script>
@endsection
```

### **AJAX Example (Dream Team Player Search)**
```javascript
function searchPlayers() {
    fetch("{{ route('dream-team.search-players') }}?name=" + searchName)
        .then(response => response.json())
        .then(players => {
            // Display players
        });
}
```

---

## 🎓 Key Points for Understanding

1. **Blade = Laravel's template engine** - Extends PHP with cleaner syntax
2. **@extends and @section** - Build pages from layouts
3. **{{ }}** - Always use for displaying variables (auto-escapes)
4. **@csrf** - Always include in forms (security)
5. **@error** - Display validation errors
6. **session()** - Access flash messages
7. **route()** - Generate URLs from route names
8. **old()** - Repopulate form after validation error

---

**Related Directories:**
- 📁 [`app/Http/Controllers/`](../../app/Http/Controllers/) - Passes data to views
- 📁 [`app/Models/`](../../app/Models/) - Data displayed in views
- 📁 [`routes/web.php`](../../../routes/web.php) - Routes that return views
- 📁 [`public/css/`](../../public/css/) - Additional stylesheets
- 📁 [`public/js/`](../../public/js/) - Additional JavaScript files
