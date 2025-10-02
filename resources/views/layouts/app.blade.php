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
                    <a href="{{ route('teams.index') }}" class="nav-link group relative px-3 py-2 rounded-lg transition-all duration-300 hover:bg-primary/10 {{ request()->routeIs('teams.*') ? 'text-primary bg-primary/20' : 'text-light' }}">
                        <i class="fas fa-users mr-2 transform group-hover:scale-110 transition-transform duration-300"></i>Teams
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('players.index') }}" class="nav-link group relative px-3 py-2 rounded-lg transition-all duration-300 hover:bg-primary/10 {{ request()->routeIs('players.*') ? 'text-primary bg-primary/20' : 'text-light' }}">
                        <i class="fas fa-user mr-2 transform group-hover:scale-110 transition-transform duration-300"></i>Players
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </div>
                
                <!-- Theme Toggle & Mobile Menu -->
                <div class="flex items-center space-x-4">
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
                <a href="{{ route('teams.index') }}" class="block px-3 py-2 rounded-lg text-light hover:text-primary hover:bg-primary/10 transition-all duration-300 transform hover:translate-x-2">
                    <i class="fas fa-users mr-2"></i>Teams
                </a>
                <a href="{{ route('players.index') }}" class="block px-3 py-2 rounded-lg text-light hover:text-primary hover:bg-primary/10 transition-all duration-300 transform hover:translate-x-2">
                    <i class="fas fa-user mr-2"></i>Players
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
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
        });
        
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
