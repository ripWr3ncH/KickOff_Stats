# Middleware Directory

Middleware filters HTTP requests entering your application. Think of it as a security checkpoint.

## 📄 Files

### **AuthenticateUser.php** ⭐ (Custom Middleware)

**Purpose:** Protect routes that require user login

**Location:** `app/Http/Middleware/AuthenticateUser.php`

---

## 🔒 How It Works

### **Code:**
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
            // Redirect to homepage with error message
            return redirect()->route('home')
                ->with('error', 'Please login to access this feature');
        }

        // User is logged in, allow request to proceed
        return $next($request);
    }
}
```

---

## 🎯 Purpose

**Protects routes from unauthenticated access:**
- Dream Team Builder (create, edit, delete)
- My Favorite Teams
- User Dashboard
- Any feature requiring login

**Flow:**
1. User tries to access protected route (e.g., `/dream-team/create`)
2. Middleware checks: `Auth::check()` (is user logged in?)
3. If **NO** → Redirect to homepage with error message
4. If **YES** → Allow request to continue to controller

---

## 📌 Registration

Middleware must be registered in `bootstrap/app.php`:

```php
use App\Http\Middleware\AuthenticateUser;

->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'auth.user' => AuthenticateUser::class,
    ]);
})
```

**Alias:** `auth.user` (shorthand for `AuthenticateUser::class`)

---

## 🛣️ Applied to Routes

### **In routes/web.php:**
```php
Route::middleware(['auth.user'])->group(function () {
    
    // Dream Team routes
    Route::get('/dream-team', [DreamTeamController::class, 'index'])->name('dream-team.index');
    Route::get('/dream-team/create', [DreamTeamController::class, 'create'])->name('dream-team.create');
    Route::post('/dream-team', [DreamTeamController::class, 'store'])->name('dream-team.store');
    Route::get('/dream-team/{dreamTeam}/edit', [DreamTeamController::class, 'edit'])->name('dream-team.edit');
    Route::put('/dream-team/{dreamTeam}', [DreamTeamController::class, 'update'])->name('dream-team.update');
    Route::delete('/dream-team/{dreamTeam}', [DreamTeamController::class, 'destroy'])->name('dream-team.destroy');
    
    // Favorite Teams routes
    Route::get('/my-teams', [MyTeamsController::class, 'index'])->name('my-teams.index');
    Route::get('/my-teams/select', [MyTeamsController::class, 'select'])->name('my-teams.select');
    Route::post('/my-teams/add', [MyTeamsController::class, 'add'])->name('my-teams.add');
    Route::delete('/my-teams/remove/{team}', [MyTeamsController::class, 'remove'])->name('my-teams.remove');
    
});
```

---

## 🔍 Session Check Details

### **Auth::check() Method**
```php
use Illuminate\Support\Facades\Auth;

if (!Auth::check()) {
    // User NOT logged in
}
```

**What it does:**
1. Checks session for `user_id`
2. If exists → User is logged in
3. If NOT exists → User is guest

**Behind the scenes:**
- Session key: `login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d`
- Stores: User ID
- Created when: `Auth::attempt()` or `Auth::login()` succeeds
- Destroyed when: `Auth::logout()` called

---

## 🚨 Error Message

**Flash Message:**
```php
return redirect()->route('home')
    ->with('error', 'Please login to access this feature');
```

**Displayed in:** `resources/views/layouts/app.blade.php` (lines 194-223)

**View Code:**
```blade
@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif
```

---

## 🎨 User Experience

### **Without Login:**
1. User clicks "Create Dream Team"
2. Middleware checks Auth::check() → **false**
3. Redirected to homepage
4. Red error alert: "Please login to access this feature"
5. Login modal appears (user can click "Login" button)

### **With Login:**
1. User clicks "Create Dream Team"
2. Middleware checks Auth::check() → **true**
3. Request proceeds to `DreamTeamController@create`
4. Dream Team builder page loads

---

## 🔄 Middleware Execution Order

**Request Flow:**
```
User Request → Web Middleware Group → Custom Middleware → Controller → Response
```

**Web Middleware Group (built-in):**
1. `EncryptCookies` - Encrypt/decrypt cookies
2. `AddQueuedCookiesToResponse` - Add cookies to response
3. `StartSession` - Start session
4. `VerifyCsrfToken` - Check CSRF token
5. `SubstituteBindings` - Route model binding

**Custom Middleware:**
6. `AuthenticateUser` - Check login status

**Then:**
7. Controller method executes
8. View rendered
9. Response sent back

---

## 🛡️ Security Benefits

1. **Prevents unauthorized access** - Only logged-in users can access protected features
2. **Consistent protection** - Apply to multiple routes with one line
3. **Clear error messages** - User knows why access denied
4. **Centralized logic** - Authentication check in one place (not in every controller)

---

## 📚 Built-in Laravel Middleware

### **auth** (Laravel's Default Auth Middleware)
```php
Route::middleware(['auth'])->group(function () {
    // Routes
});
```
- Redirects to login page if not authenticated
- Default Laravel authentication middleware
- We created custom `auth.user` for custom redirect behavior

### **guest** (Only Allow Guests)
```php
Route::middleware(['guest'])->group(function () {
    // Only accessible if NOT logged in
});
```
- Prevents logged-in users from accessing login/register pages

### **verified** (Email Verification)
```php
Route::middleware(['verified'])->group(function () {
    // Only accessible if email verified
});
```
- Requires email verification before access

---

## 💡 Key Points for Understanding

1. **Middleware = Security checkpoint** before controller
2. **AuthenticateUser** - Custom middleware for login check
3. **Auth::check()** - Returns true if logged in, false if guest
4. **Registered as 'auth.user'** - Shorthand alias
5. **Applied in routes** - Wrap protected routes in middleware group
6. **Redirects to homepage** - With error message if not logged in
7. **Flash messages** - Stored in session, displayed once
8. **Execution order** - Web middleware → Custom middleware → Controller

---

## 🔧 Testing Middleware

### **Test 1: Without Login**
1. Open browser (incognito mode)
2. Navigate to: `http://localhost/dream-team/create`
3. Expected: Redirect to homepage with error message

### **Test 2: With Login**
1. Login with credentials
2. Navigate to: `http://localhost/dream-team/create`
3. Expected: Dream Team builder page loads

---

**Related Files:**
- 📁 [`routes/web.php`](../../routes/web.php) - Applies middleware to routes
- 📁 [`bootstrap/app.php`](../../bootstrap/app.php) - Registers middleware alias
- 📁 [`app/Http/Controllers/Auth/AuthController.php`](../Controllers/Auth/AuthController.php) - Creates session on login
- 📁 [`resources/views/layouts/app.blade.php`](../../resources/views/layouts/app.blade.php) - Displays error messages
- 📄 [Laravel Middleware Docs](https://laravel.com/docs/11.x/middleware)
