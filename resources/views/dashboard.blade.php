@extends('layouts.app')

@section('title', 'KickOff Stats - Live Football Scores & Stats')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Section with Live Matches -->
    @if($liveMatches->count() > 0)
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-light mb-4 flex items-center">
            <span class="live-pulse w-3 h-3 rounded-full mr-2"></span>
            Live Matches
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($liveMatches as $match)
            <div class="match-card bg-card rounded-lg p-4 border border-green-500 live-score hover:border-primary hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 group cursor-pointer transform hover:scale-102" data-match-id="{{ $match->id }}" onclick="window.location.href='{{ route('matches.show', $match->id) }}'">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-muted group-hover:text-light transition-colors duration-300">{{ $match->league->name }}</span>
                    <span class="text-xs bg-primary text-white px-2 py-1 rounded-full status group-hover:bg-green-400 transition-colors duration-300">
                        LIVE <span class="minute">{{ $match->minute }}'</span>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 flex-1 transform group-hover:scale-105 transition-transform duration-300">
                        <img src="{{ $match->homeTeam->logo ?? '/images/default-logo.svg' }}" 
                             alt="{{ $match->homeTeam->name }}" 
                             class="w-8 h-8 rounded-full group-hover:shadow-lg transition-all duration-300"
                             onerror="this.src='/images/default-logo.svg'">
                        <span class="text-light text-sm group-hover:text-primary transition-colors duration-300">{{ $match->homeTeam->short_name }}</span>
                    </div>
                    <div class="text-center px-4">
                        <div class="text-xl font-bold text-light score group-hover:text-primary group-hover:scale-110 transition-all duration-300">
                            {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 flex-1 justify-end transform group-hover:scale-105 transition-transform duration-300">
                        <span class="text-light text-sm group-hover:text-primary transition-colors duration-300">{{ $match->awayTeam->short_name }}</span>
                        <img src="{{ $match->awayTeam->logo ?? '/images/default-logo.svg' }}" 
                             alt="{{ $match->awayTeam->name }}" 
                             class="w-8 h-8 rounded-full group-hover:shadow-lg transition-all duration-300"
                             onerror="this.src='/images/default-logo.svg'">
                    </div>
                </div>
                <div class="mt-2 text-center opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                    <span class="text-primary text-xs font-medium">
                        <i class="fas fa-eye mr-1"></i>Click to view details
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Today's Matches -->
    @if($todayMatches->count() > 0)
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-light mb-4">
            <i class="fas fa-calendar-day mr-2"></i>Today's Matches
        </h2>
        <div class="bg-card rounded-lg p-6">
            <div class="space-y-4">
                @foreach($todayMatches as $match)
                <div class="match-row flex items-center justify-between py-3 border-b border-gray-700 last:border-b-0 hover:bg-gray-700/50 rounded-lg px-2 transition-all duration-300 group cursor-pointer transform hover:translate-x-2" onclick="window.location.href='{{ route('matches.show', $match->id) }}'">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="text-xs text-muted w-16 group-hover:text-primary transition-colors duration-300">
                            {{ $match->getLocalMatchDate()->format('g:i A') }}
                        </div>
                        <div class="flex items-center space-x-2 transform group-hover:scale-105 transition-transform duration-300">
                            <img src="{{ $match->homeTeam->logo ?? '/images/default-logo.svg' }}" 
                                 alt="{{ $match->homeTeam->name }}" 
                                 class="w-6 h-6 rounded-full group-hover:shadow-md transition-all duration-300"
                                 onerror="this.src='/images/default-logo.svg'">
                            <span class="text-light text-sm group-hover:text-primary transition-colors duration-300">{{ $match->homeTeam->short_name }}</span>
                        </div>
                    </div>
                    
                    <div class="text-center px-4">
                        @if($match->status === 'live')
                            <div class="text-lg font-bold text-light">
                                {{ $match->home_score }} - {{ $match->away_score }}
                            </div>
                            <div class="text-xs text-primary">{{ $match->minute }}'</div>
                        @elseif($match->status === 'finished')
                            <div class="text-lg font-bold text-light">
                                {{ $match->home_score }} - {{ $match->away_score }}
                            </div>
                            <div class="text-xs text-muted">FT</div>
                        @else
                            <div class="text-sm text-muted">vs</div>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2 flex-1 justify-end">
                        <span class="text-light text-sm">{{ $match->awayTeam->short_name }}</span>
                        <img src="{{ $match->awayTeam->logo ?? '/images/default-logo.svg' }}" 
                             alt="{{ $match->awayTeam->name }}" 
                             class="w-6 h-6 rounded-full"
                             onerror="this.src='/images/default-logo.svg'">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- League Quick Access -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-light mb-4">
            <i class="fas fa-trophy mr-2"></i>Leagues
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($leagues as $league)
            <a href="{{ route('leagues.show', $league->slug) }}" 
               class="league-card bg-card rounded-lg p-6 hover:bg-gray-700 transition-all duration-300 group transform hover:scale-105 hover:shadow-xl hover:shadow-primary/20 relative overflow-hidden">
                <!-- Animated background overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-primary/0 via-primary/5 to-primary/0 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out"></div>
                
                <div class="flex items-center space-x-4 relative z-10">
                    <div class="w-16 h-16 flex items-center justify-center group-hover:scale-110 group-hover:rotate-12 transition-all duration-300">
                        @if($league->logo_url)
                            <img src="{{ $league->logo_url }}" 
                                 alt="{{ $league->name }} logo" 
                                 class="w-16 h-16 object-contain league-logo filter group-hover:brightness-110">
                        @else
                            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center group-hover:bg-green-400 transition-colors duration-300">
                                <i class="fas fa-futbol text-white text-2xl group-hover:animate-spin"></i>
                            </div>
                        @endif
                    </div>
                    <div class="transform group-hover:translate-x-2 transition-transform duration-300">
                        <h3 class="text-lg font-bold text-light group-hover:text-primary transition-colors duration-300">{{ $league->name }}</h3>
                        <p class="text-muted text-sm group-hover:text-light transition-colors duration-300">{{ $league->country }}</p>
                        <p class="text-muted text-xs group-hover:text-primary/80 transition-colors duration-300">{{ $league->season }}</p>
                    </div>
                </div>
                
                <!-- Hover indicator -->
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                    <i class="fas fa-arrow-right text-primary"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Recent Results & Upcoming Matches -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Results -->
        <div>
            <h2 class="text-xl font-bold text-light mb-4">
                <i class="fas fa-history mr-2"></i>Recent Results
            </h2>
            <div class="bg-card rounded-lg p-4">
                @if($recentResults->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentResults as $match)
                        <div class="result-item flex items-center justify-between py-2 border-b border-gray-700 last:border-b-0 hover:bg-gray-700/30 rounded px-2 transition-all duration-300 group cursor-pointer transform hover:scale-105" onclick="window.location.href='{{ route('matches.show', $match->id) }}'">
                            <div class="flex items-center space-x-2 flex-1 transform group-hover:translate-x-1 transition-transform duration-300">
                                <img src="{{ $match->homeTeam->logo ?? '/images/default-logo.svg' }}" 
                                     alt="{{ $match->homeTeam->name }}" 
                                     class="w-5 h-5 rounded-full group-hover:shadow-sm transition-all duration-300">
                                <span class="text-light text-sm group-hover:text-primary transition-colors duration-300">{{ $match->homeTeam->short_name }}</span>
                            </div>
                            <div class="text-center px-3">
                                <div class="text-sm font-bold text-light group-hover:text-primary group-hover:scale-110 transition-all duration-300">
                                    {{ $match->home_score }} - {{ $match->away_score }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-1 justify-end transform group-hover:-translate-x-1 transition-transform duration-300">
                                <span class="text-light text-sm group-hover:text-primary transition-colors duration-300">{{ $match->awayTeam->short_name }}</span>
                                <img src="{{ $match->awayTeam->logo ?? '/images/default-logo.svg' }}" 
                                     alt="{{ $match->awayTeam->name }}" 
                                     class="w-5 h-5 rounded-full group-hover:shadow-sm transition-all duration-300">
                            </div>
                            <!-- Hover indicator -->
                            <div class="absolute right-2 opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                                <i class="fas fa-chevron-right text-primary text-xs"></i>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-8">No recent results available</p>
                @endif
            </div>
        </div>

        <!-- Upcoming Matches -->
        <div>
            <h2 class="text-xl font-bold text-light mb-4">
                <i class="fas fa-clock mr-2"></i>Upcoming Matches
            </h2>
            <div class="bg-card rounded-lg p-4">
                @if($upcomingMatches->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingMatches as $match)
                        <div class="upcoming-item flex items-center justify-between py-2 border-b border-gray-700 last:border-b-0 hover:bg-gray-700/30 rounded px-2 transition-all duration-300 group cursor-pointer relative transform hover:scale-105" onclick="window.location.href='{{ route('matches.show', $match->id) }}'">
                            <div class="flex items-center space-x-2 flex-1 transform group-hover:translate-x-1 transition-transform duration-300">
                                <img src="{{ $match->homeTeam->logo ?? '/images/default-logo.svg' }}" 
                                     alt="{{ $match->homeTeam->name }}" 
                                     class="w-5 h-5 rounded-full group-hover:shadow-sm transition-all duration-300">
                                <span class="text-light text-sm group-hover:text-primary transition-colors duration-300">{{ $match->homeTeam->short_name }}</span>
                            </div>
                            <div class="text-center px-3">
                                <div class="text-xs text-muted group-hover:text-primary group-hover:font-semibold transition-all duration-300">
                                    {{ $match->getLocalMatchDate()->format('M j, g:i A') }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-1 justify-end transform group-hover:-translate-x-1 transition-transform duration-300">
                                <span class="text-light text-sm group-hover:text-primary transition-colors duration-300">{{ $match->awayTeam->short_name }}</span>
                                <img src="{{ $match->awayTeam->logo ?? '/images/default-logo.svg' }}" 
                                     alt="{{ $match->awayTeam->name }}" 
                                     class="w-5 h-5 rounded-full group-hover:shadow-sm transition-all duration-300">
                            </div>
                            <!-- Hover indicator -->
                            <div class="absolute right-2 opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                                <i class="fas fa-chevron-right text-primary text-xs"></i>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-8">No upcoming matches scheduled</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/live-scores.js') }}"></script>
<script>
    // Auto-refresh live scores every 30 seconds (API-friendly interval)
    setInterval(function() {
        if (document.querySelectorAll('.live-score').length > 0) {
            console.log('Refreshing live scores...');
            fetch('/api/live-scores')
                .then(response => response.json())
                .then(data => {
                    console.log('Live scores updated:', data);
                    // Update live scores dynamically
                    data.forEach(match => {
                        const matchElement = document.querySelector(`[data-match-id="${match.id}"]`);
                        if (matchElement) {
                            // Update score
                            const scoreElement = matchElement.querySelector('.score');
                            if (scoreElement) {
                                scoreElement.textContent = `${match.home_score} - ${match.away_score}`;
                            }
                            
                            // Update minute
                            const minuteElement = matchElement.querySelector('.minute');
                            if (minuteElement && match.minute) {
                                minuteElement.textContent = `${match.minute}'`;
                            }
                        }
                    });
                })
                .catch(error => console.log('Live score update failed:', error));
        }
    }, 30000); // Back to 30 seconds for API rate limits
</script>
@endpush

@push('styles')
<style>
    .live-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .7;
        }
    }
    
    .live-score {
        box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.5);
        animation: live-glow 2s ease-in-out infinite alternate;
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        border-color: var(--primary-green);
    }
    
    @keyframes live-glow {
        from {
            box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.5);
        }
        to {
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.8), 0 0 0 1px rgba(34, 197, 94, 0.7);
        }
    }
    
    /* Enhanced hover effects */
    .league-card {
        position: relative;
        overflow: hidden;
    }
    
    .league-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 210, 106, 0.1), transparent);
        transition: left 0.8s ease-in-out;
        z-index: 1;
    }
    
    .league-card:hover::before {
        left: 100%;
    }
    
    .match-card {
        position: relative;
        overflow: hidden;
    }
    
    .match-card::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: radial-gradient(circle, rgba(0, 210, 106, 0.1) 0%, transparent 70%);
        transform: translate(-50%, -50%);
        transition: all 0.6s ease-out;
        z-index: 1;
    }
    
    .match-card:hover::after {
        width: 300px;
        height: 300px;
    }
    
    .match-row {
        position: relative;
        overflow: hidden;
    }
    
    .match-row::before {
        content: '';
        position: absolute;
        left: -100%;
        top: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 210, 106, 0.05), transparent);
        transition: left 0.5s ease-in-out;
    }
    
    .match-row:hover::before {
        left: 100%;
    }
    
    /* Floating animation for cards */
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-2px);
        }
    }
    
    .league-card:hover {
        animation: float 3s ease-in-out infinite;
    }
    
    /* Pulse effect for live matches */
    @keyframes live-pulse-border {
        0%, 100% {
            border-color: rgba(34, 197, 94, 0.5);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
        }
        50% {
            border-color: rgba(34, 197, 94, 0.8);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        }
    }
    
    .live-score:hover {
        animation: live-pulse-border 1.5s ease-in-out infinite;
    }
    
    /* Scale utility classes */
    .hover\:scale-102:hover {
        transform: scale(1.02);
    }
    
    /* Smooth transitions for all interactive elements */
    .match-card, .league-card, .match-row {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: var(--bg-secondary);
        color: var(--text-primary);
    }
    
    /* Theme-aware elements */
    .bg-card {
        background-color: var(--bg-secondary) !important;
        border-color: var(--border-color);
    }
    
    .text-light {
        color: var(--text-primary) !important;
    }
    
    .text-muted {
        color: var(--text-secondary) !important;
    }
    
    /* Light theme specific adjustments */
    [data-theme="light"] .live-score {
        border-color: var(--primary-green);
        background-color: #f0fdf4;
    }
    
    [data-theme="light"] .league-card:hover,
    [data-theme="light"] .match-card:hover {
        box-shadow: 0 10px 25px var(--shadow-color);
    }
    
    /* Dark theme specific adjustments */
    [data-theme="dark"] .league-card:hover,
    [data-theme="dark"] .match-card:hover {
        box-shadow: 0 10px 25px rgba(0, 210, 106, 0.1);
    }
    
    /* Text shadow on hover for better visibility */
    .group:hover .text-light {
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    
    /* Result and upcoming items positioning */
    .result-item, .upcoming-item {
        position: relative;
        overflow: hidden;
    }
    
    /* Subtle background slide effect */
    .result-item::before, .upcoming-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 210, 106, 0.05), transparent);
        transition: left 0.4s ease-in-out;
    }
    
    .result-item:hover::before, .upcoming-item:hover::before {
        left: 100%;
    }
    
    /* Glow effect on hover */
    .league-card:hover, .match-card:hover {
        box-shadow: 0 10px 25px rgba(0, 210, 106, 0.1);
    }
    
    /* Smooth logo rotation */
    @keyframes logo-bounce {
        0%, 100% {
            transform: scale(1) rotate(0deg);
        }
        50% {
            transform: scale(1.1) rotate(5deg);
        }
    }
    
    .league-card:hover .league-logo {
        animation: logo-bounce 0.6s ease-in-out;
    }
</style>
@endpush
