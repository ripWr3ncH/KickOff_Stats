<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KickOff Stats - Football Match Tracker & Player Stats')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        /* Theme Variables */
        :root {
            --primary-green: #00D26A;
            
            /* Dark Theme (default) */
            --bg-primary: #0F1419;
            --bg-secondary: #1A202C;
            --text-primary: #E2E8F0;
            --text-secondary: #A0AEC0;
            --border-color: #374151;
            --shadow-color: rgba(0, 0, 0, 0.3);
        }
        
        /* Light Theme */
        [data-theme="light"] {
            --bg-primary: #e4e5f1;
            --bg-secondary: #e4e5f1;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --border-color: #E5E7EB;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }
        
        .bg-primary { background-color: var(--primary-green); }
        .bg-dark { background-color: var(--bg-primary); }
        .bg-card { background-color: var(--bg-secondary); }
        .text-light { color: var(--text-primary); }
        .text-muted { color: var(--text-secondary); }
        .border-gray-700 { border-color: var(--border-color); }
        .border-gray-200 { border-color: var(--border-color); }
        
        /* Live score animation */
        .live-pulse {
            animation: pulse-green 2s infinite;
        }
        
        @keyframes pulse-green {
            0%, 100% { background-color: var(--primary-green); }
            50% { background-color: #00B359; }
        }
        
        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s ease;
        }
        
        /* Enhanced navigation hover effects */
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 210, 106, 0.1), transparent);
            transition: left 0.5s ease-in-out;
        }
        
        .nav-link:hover::before {
            left: 100%;
        }
        
        /* Logo hover effect */
        .logo-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .logo-hover:hover {
            transform: rotate(10deg) scale(1.1);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--bg-primary);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-green);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #00B359;
        }
        
        /* SVG logo styling */
        .league-logo {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
        }
        
        .league-logo svg {
            width: 100%;
            height: 100%;
        }
        
        /* Theme toggle button styles */
        .theme-toggle {
            position: relative;
            overflow: hidden;
        }
        
        .theme-toggle::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, var(--primary-green) 0%, transparent 70%);
            opacity: 0.1;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover::before {
            width: 40px;
            height: 40px;
        }
        
        /* Smooth theme transitions for all elements */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Light theme specific styles */
        [data-theme="light"] .bg-card {
            background-color: var(--bg-secondary);
            box-shadow: 0 1px 3px var(--shadow-color);
        }
        
        [data-theme="light"] footer {
            border-top: 1px solid var(--border-color);
        }
        
        /* Navigation theme styles */
        nav {
            background-color: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
        }
        
        /* Flash message animations */
        .flash-message {
            animation: slideIn 0.3s ease-out;
            transition: all 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Modal animations */
        #auth-modal {
            animation: fadeIn 0.3s ease-out;
        }
        
        #auth-modal > div {
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* Tab transitions */
        .form-content {
            transition: opacity 0.3s ease-in-out;
        }
        
        .form-content.hidden {
            opacity: 0;
        }
        
        .form-content:not(.hidden) {
            opacity: 1;
        }
    </style>
    
    @stack('styles')
</head>
<body class="h-full bg-dark text-light font-sans transition-colors duration-300">
    <!-- Navigation -->
    <nav class="bg-card shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                        <div class="bg-primary w-10 h-10 rounded-full flex items-center justify-center logo-hover shadow-lg group-hover:shadow-primary/50">
                            <i class="fas fa-futbol text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-light group-hover:text-primary transition-colors duration-300">KickOff Stats</span>
                    </a>
                </div>
                
                <!-- Main Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="nav-link group relative px-3 py-2 rounded-lg transition-all duration-300 hover:bg-primary/10 {{ request()->routeIs('home') ? 'text-primary bg-primary/20' : 'text-light' }}">
                        <i class="fas fa-home mr-2 transform group-hover:scale-110 transition-transform duration-300"></i>Home
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('matches.index') }}" class="nav-link group relative px-3 py-2 rounded-lg transition-all duration-300 hover:bg-primary/10 {{ request()->routeIs('matches.*') ? 'text-primary bg-primary/20' : 'text-light' }}">
                        <i class="fas fa-calendar-alt mr-2 transform group-hover:scale-110 transition-transform duration-300"></i>Matches
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('leagues.index') }}" class="nav-link group relative px-3 py-2 rounded-lg transition-all duration-300 hover:bg-primary/10 {{ request()->routeIs('leagues.*') ? 'text-primary bg-primary/20' : 'text-light' }}">
                        <i class="fas fa-trophy mr-2 transform group-hover:scale-110 transition-transform duration-300"></i>Leagues
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('news.index') }}" class="nav-link group relative px-3 py-2 rounded-lg transition-all duration-300 hover:bg-primary/10 {{ request()->routeIs('news.*') ? 'text-primary bg-primary/20' : 'text-light' }}">
                        <i class="fas fa-newspaper mr-2 transform group-hover:scale-110 transition-transform duration-300"></i>News
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('players.index') }}" class="nav-link group relative px-3 py-2 rounded-lg transition-all duration-300 hover:bg-primary/10 {{ request()->routeIs('players.*') ? 'text-primary bg-primary/20' : 'text-light' }}">
                        <i class="fas fa-user mr-2 transform group-hover:scale-110 transition-transform duration-300"></i>Players
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </div>
                
                <!-- Theme Toggle & Auth Links -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- User Dropdown -->
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 text-light hover:text-primary transition-colors duration-300 px-3 py-2 rounded-lg hover:bg-primary/10">
                                <i class="fas fa-user"></i>
                                <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-card rounded-lg shadow-lg border border-gray-700 py-2">
                                <a href="{{ route('my-teams.index') }}" class="block px-4 py-2 text-light hover:bg-primary/10 hover:text-primary transition-colors duration-300">
                                    <i class="fas fa-heart mr-2"></i>My Teams
                                </a>
                                <a href="{{ route('dream-team.index') }}" class="block px-4 py-2 text-light hover:bg-primary/10 hover:text-primary transition-colors duration-300">
                                    <i class="fas fa-star mr-2"></i>Dream Team
                                </a>
                                <hr class="border-gray-700 my-2">
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-light hover:bg-red-50 dark:hover:bg-red-900 hover:text-red-600 transition-colors duration-300">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Single Login Button -->
                        <button id="auth-modal-trigger" class="bg-primary hover:bg-primary/80 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>
                    @endauth
                    
                    <!-- Theme Toggle Button -->
                    <button id="theme-toggle" class="theme-toggle p-2 rounded-lg text-light hover:text-primary hover:bg-primary/10 transition-all duration-300 group" title="Toggle theme">
                        <i class="fas fa-sun text-lg sun-icon hidden group-hover:rotate-180 transition-transform duration-300"></i>
                        <i class="fas fa-moon text-lg moon-icon group-hover:-rotate-180 transition-transform duration-300"></i>
                    </button>
                    
                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button id="mobile-menu-button" class="text-light hover:text-primary">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-card border-t border-gray-700">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-light hover:text-primary hover:bg-primary/10 transition-all duration-300 transform hover:translate-x-2">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="{{ route('matches.index') }}" class="block px-3 py-2 rounded-lg text-light hover:text-primary hover:bg-primary/10 transition-all duration-300 transform hover:translate-x-2">
                    <i class="fas fa-calendar-alt mr-2"></i>Matches
                </a>
                <a href="{{ route('leagues.index') }}" class="block px-3 py-2 rounded-lg text-light hover:text-primary hover:bg-primary/10 transition-all duration-300 transform hover:translate-x-2">
                    <i class="fas fa-trophy mr-2"></i>Leagues
                </a>
                <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-lg text-light hover:text-primary hover:bg-primary/10 transition-all duration-300 transform hover:translate-x-2">
                    <i class="fas fa-newspaper mr-2"></i>News
                </a>
                <a href="{{ route('players.index') }}" class="block px-3 py-2 rounded-lg text-light hover:text-primary hover:bg-primary/10 transition-all duration-300 transform hover:translate-x-2">
                    <i class="fas fa-user mr-2"></i>Players
                </a>
                
                @auth
                    <hr class="border-gray-700 my-2">
                    <div class="px-3 py-2 text-muted text-sm">{{ Auth::user()->name }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-lg text-light hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900 transition-all duration-300">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                @else
                    <hr class="border-gray-700 my-2">
                    <button id="mobile-auth-modal-trigger" class="block w-full text-left px-3 py-2 rounded-lg text-light hover:text-primary hover:bg-primary/10 transition-all duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flash-message">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flash-message">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-card mt-16 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2 mb-4 md:mb-0">
                    <div class="bg-primary w-8 h-8 rounded-full flex items-center justify-center">
                        <i class="fas fa-futbol text-white"></i>
                    </div>
                    <span class="text-lg font-bold text-light">KickOff Stats</span>
                </div>
                <div class="text-muted text-sm">
                    © {{ date('Y') }} KickOff Stats. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- Auth Modal -->
    @php
        $hasErrors = $errors->any();
        
        // Multiple ways to detect if this was a signup attempt:
        // 1. Check for signup-specific fields in error bag
        $signupSpecificFields = ['name', 'password_confirmation'];
        $hasSignupFields = $hasErrors && collect($errors->keys())->intersect($signupSpecificFields)->isNotEmpty();
        
        // 2. Check for signup-specific validation messages
        $allErrorMessages = $errors->all();
        $hasSignupMessages = collect($allErrorMessages)->contains(function($message) {
            return str_contains($message, 'confirmed') || 
                   str_contains($message, 'unique') ||
                   str_contains($message, 'name field') ||
                   str_contains($message, 'name is required');
        });
        
        // 3. Check if we have old input for name (only sent in signup)
        $hasNameInput = old('name') !== null;
        
        // 4. Check the previous URL to see if it was register route
        $wasRegisterRoute = str_contains(url()->previous(), '/register') || 
                           str_contains(request()->url(), '/register');
        
        $isSignupError = $hasSignupFields || $hasSignupMessages || $hasNameInput || $wasRegisterRoute;
        
        // Debug info (you can remove this later)
        $debugInfo = [
            'hasErrors' => $hasErrors,
            'hasSignupFields' => $hasSignupFields,
            'hasSignupMessages' => $hasSignupMessages,
            'hasNameInput' => $hasNameInput,
            'wasRegisterRoute' => $wasRegisterRoute,
            'errorKeys' => $errors->keys(),
            'oldName' => old('name'),
        ];
    @endphp
    
    <div id="auth-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 {{ $hasErrors ? 'flex' : 'hidden' }} items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 id="modal-title" class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $hasErrors && $isSignupError ? 'Join KickOff Stats' : 'Sign In to KickOff Stats' }}
                </h2>
                <button id="close-auth-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Form Content -->
            <div class="p-6">
                <!-- Error Messages -->
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="ml-2">
                                <p class="font-bold">Please fix the following errors:</p>
                                <ul class="list-disc list-inside text-sm mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Sign In Form -->
                <div id="signin-form" class="form-content">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="space-y-4">
                            <!-- Email -->
                            <div>
                                <label for="modal-signin-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address
                                </label>
                                <input 
                                    id="modal-signin-email" 
                                    name="email" 
                                    type="email" 
                                    required 
                                    value="{{ old('email') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter your email"
                                >
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="modal-signin-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Password
                                </label>
                                <input 
                                    id="modal-signin-password" 
                                    name="password" 
                                    type="password" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter your password"
                                >
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input 
                                        id="modal-remember" 
                                        name="remember" 
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                    >
                                    <label for="modal-remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Remember me
                                    </label>
                                </div>
                                <div class="text-sm">
                                    <a href="{{ route('password.request') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Sign In
                            </button>

                            <!-- Toggle to Sign Up -->
                            <div class="text-center mt-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Don't have an account? 
                                    <button type="button" id="show-signup" class="text-blue-600 hover:text-blue-500 font-medium underline">
                                        Sign up here
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Sign Up Form -->
                <div id="signup-form" class="form-content hidden">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label for="modal-signup-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name
                                </label>
                                <input 
                                    id="modal-signup-name" 
                                    name="name" 
                                    type="text" 
                                    required 
                                    value="{{ old('name') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter your full name"
                                >
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="modal-signup-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address
                                </label>
                                <input 
                                    id="modal-signup-email" 
                                    name="email" 
                                    type="email" 
                                    required 
                                    value="{{ old('email') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter your email"
                                >
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="modal-signup-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Password
                                </label>
                                <input 
                                    id="modal-signup-password" 
                                    name="password" 
                                    type="password" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Choose a password (min 6 characters)"
                                >
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="modal-signup-password-confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Confirm Password
                                </label>
                                <input 
                                    id="modal-signup-password-confirmation" 
                                    name="password_confirmation" 
                                    type="password" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Confirm your password"
                                >
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            >
                                Create Account
                            </button>

                            <!-- Toggle to Sign In -->
                            <div class="text-center mt-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Already have an account? 
                                    <button type="button" id="show-signin" class="text-blue-600 hover:text-blue-500 font-medium underline">
                                        Sign in here
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/live-scores.js') }}"></script>
    <script>
        // Theme Management
        class ThemeManager {
            constructor() {
                this.theme = localStorage.getItem('theme') || 'dark';
                this.init();
            }
            
            init() {
                this.applyTheme(this.theme);
                this.setupEventListeners();
            }
            
            setupEventListeners() {
                const themeToggle = document.getElementById('theme-toggle');
                if (themeToggle) {
                    themeToggle.addEventListener('click', () => this.toggleTheme());
                }
            }
            
            applyTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                this.updateThemeIcon(theme);
                localStorage.setItem('theme', theme);
            }
            
            updateThemeIcon(theme) {
                const sunIcon = document.querySelector('.sun-icon');
                const moonIcon = document.querySelector('.moon-icon');
                
                if (sunIcon && moonIcon) {
                    if (theme === 'light') {
                        sunIcon.classList.remove('hidden');
                        moonIcon.classList.add('hidden');
                    } else {
                        sunIcon.classList.add('hidden');
                        moonIcon.classList.remove('hidden');
                    }
                }
            }
            
            toggleTheme() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark';
                this.applyTheme(this.theme);
                
                // Add smooth transition effect
                document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
                setTimeout(() => {
                    document.body.style.transition = '';
                }, 300);
            }
        }
        
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            new ThemeManager();
            
            // Auto-hide flash messages
            const flashMessages = document.querySelectorAll('.flash-message');
            flashMessages.forEach(message => {
                setTimeout(() => {
                    message.style.transform = 'translateX(100%)';
                    message.style.opacity = '0';
                    setTimeout(() => {
                        message.remove();
                    }, 300);
                }, 4000);
            });
            
            // Authentication Modal Management
            const authModal = document.getElementById('auth-modal');
            const authModalTrigger = document.getElementById('auth-modal-trigger');
            const mobileAuthModalTrigger = document.getElementById('mobile-auth-modal-trigger');
            const closeAuthModal = document.getElementById('close-auth-modal');
            const modalTitle = document.getElementById('modal-title');
            const signinForm = document.getElementById('signin-form');
            const signupForm = document.getElementById('signup-form');
            const showSignupBtn = document.getElementById('show-signup');
            const showSigninBtn = document.getElementById('show-signin');
            
            // Check if modal should be open due to validation errors
            const hasErrors = {{ $hasErrors ? 'true' : 'false' }};
            const isSignupError = {{ $isSignupError ? 'true' : 'false' }};
            const debugInfo = @json($debugInfo ?? []);
            
            console.log('Page loaded with errors:', hasErrors, 'Signup error:', isSignupError);
            console.log('Debug info:', debugInfo);
            
            // Open modal
            function openAuthModal(defaultForm = 'signin') {
                console.log('Opening modal with form:', defaultForm);
                authModal.classList.remove('hidden');
                authModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                
                if (defaultForm === 'signup') {
                    showSignupForm();
                } else {
                    showSigninForm();
                }
            }
            
            // Close modal
            function closeModal() {
                console.log('Closing modal');
                authModal.classList.add('hidden');
                authModal.classList.remove('flex');
                document.body.style.overflow = '';
                // Reset to sign in form when closing
                showSigninForm();
            }
            
            // Show sign in form
            function showSigninForm() {
                console.log('Showing signin form');
                modalTitle.textContent = 'Sign In to KickOff Stats';
                signinForm.classList.remove('hidden');
                signupForm.classList.add('hidden');
            }
            
            // Show sign up form
            function showSignupForm() {
                console.log('Showing signup form');
                modalTitle.textContent = 'Join KickOff Stats';
                signupForm.classList.remove('hidden');
                signinForm.classList.add('hidden');
            }
            
            // Initialize modal state based on errors
            if (hasErrors) {
                console.log('Initializing modal due to validation errors');
                document.body.style.overflow = 'hidden';
                if (isSignupError) {
                    showSignupForm();
                } else {
                    showSigninForm();
                }
            }
            
            // Event listeners
            if (authModalTrigger) {
                authModalTrigger.addEventListener('click', () => openAuthModal('signin'));
            }
            
            if (mobileAuthModalTrigger) {
                mobileAuthModalTrigger.addEventListener('click', () => openAuthModal('signin'));
            }
            
            if (closeAuthModal) {
                closeAuthModal.addEventListener('click', closeModal);
            }
            
            if (showSignupBtn) {
                showSignupBtn.addEventListener('click', showSignupForm);
            }
            
            if (showSigninBtn) {
                showSigninBtn.addEventListener('click', showSigninForm);
            }
            
            // Close modal when clicking outside
            if (authModal) {
                authModal.addEventListener('click', function(e) {
                    if (e.target === authModal) {
                        closeModal();
                    }
                });
                
                // Prevent modal from closing when clicking inside modal content
                const modalContent = authModal.querySelector('.bg-white');
                if (modalContent) {
                    modalContent.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            }
            
            // Close modal with Escape key  
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !authModal.classList.contains('hidden')) {
                    closeModal();
                }
            });
            
            // Form submission handling - prevent modal from closing on validation errors
            const signinFormElement = document.querySelector('#signin-form form');
            const signupFormElement = document.querySelector('#signup-form form');
            
            function handleFormSubmission(form) {
                if (form) {
                    form.addEventListener('submit', function(e) {
                        console.log('Form being submitted:', form);
                        
                        // Debug: Check remember me checkbox for signin form
                        if (form.closest('#signin-form')) {
                            const rememberCheckbox = form.querySelector('input[name="remember"]');
                            console.log('Remember me checkbox:', {
                                exists: !!rememberCheckbox,
                                checked: rememberCheckbox ? rememberCheckbox.checked : false,
                                value: rememberCheckbox ? rememberCheckbox.value : null
                            });
                        }
                        
                        // Add loading state but don't prevent submission
                        const submitBtn = form.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            const originalText = submitBtn.textContent;
                            submitBtn.disabled = true;
                            submitBtn.textContent = 'Please wait...';
                            
                            // Re-enable button after a delay in case of client-side validation issues
                            setTimeout(() => {
                                submitBtn.disabled = false;
                                submitBtn.textContent = originalText;
                            }, 3000);
                        }
                    });
                }
            }
            
            handleFormSubmission(signinFormElement);
            handleFormSubmission(signupFormElement);
            
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    const menu = document.getElementById('mobile-menu');
                    if (menu) {
                        menu.classList.toggle('hidden');
                    }
                });
            }
            
            // User menu toggle
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                
                // Close user menu when clicking outside
                document.addEventListener('click', function() {
                    userMenu.classList.add('hidden');
                });
                
                userMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
        
        // Auto-show modal with errors (moved inside DOMContentLoaded)
        @if($errors->any())
            // Check if errors are signup-related
            const signupErrors = @json($errors->keys());
            const isSignupError = signupErrors.some(key => 
                ['name', 'password_confirmation'].includes(key)
            );
            
            console.log('Validation errors detected:', signupErrors);
            console.log('Is signup error:', isSignupError);
            
            // Auto-open modal immediately when page loads with errors
            setTimeout(() => {
                openAuthModal(isSignupError ? 'signup' : 'signin');
                console.log('Modal should be open now');
                
                // Double-check modal is actually visible
                setTimeout(() => {
                    if (authModal && authModal.classList.contains('hidden')) {
                        console.log('Modal was hidden, forcing it open again');
                        authModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }
                }, 200);
            }, 100);
        @endif
        
        // Live score updates (simulated)
        function updateLiveScores() {
            // This would connect to a real-time API in production
            const liveElements = document.querySelectorAll('.live-score');
            liveElements.forEach(element => {
                // Add pulse animation to live scores
                element.classList.add('live-pulse');
            });
        }
        
        // Update scores every 30 seconds
        setInterval(updateLiveScores, 30000);
        
        // Initial load
        updateLiveScores();
    </script>
    
    @stack('scripts')
</body>
</html>
