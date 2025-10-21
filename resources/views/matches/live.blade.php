@extends('layouts.app')

@section('title', 'Live Matches')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-light mb-2">
            <i class="fas fa-broadcast-tower mr-3 text-red-500"></i>Live Matches
        </h1>
        <p class="text-muted">Real-time updates for ongoing football matches</p>
    </div>

    <!-- Auto-refresh indicator -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="text-sm text-muted">
                <i class="fas fa-sync-alt mr-2" id="refresh-icon"></i>
                <span id="last-update">Updates every 30 seconds</span>
            </div>
            <button onclick="refreshMatches()" class="text-primary hover:text-green-300 text-sm">
                <i class="fas fa-refresh mr-1"></i>Refresh Now
            </button>
        </div>
    </div>

    @if($liveMatches->count() > 0)
        <!-- Live Matches Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="live-matches-container">
            @foreach($liveMatches as $match)
                <div class="bg-card rounded-lg border-2 border-red-500 shadow-lg relative overflow-hidden match-card" 
                     data-match-id="{{ $match->id }}"
                     data-home-score="{{ $match->home_score ?? 0 }}"
                     data-away-score="{{ $match->away_score ?? 0 }}">
                    
                    <!-- Live indicator -->
                    <div class="absolute top-0 left-0 right-0 bg-red-600 text-white text-center py-1">
                        <span class="text-xs font-semibold">
                            <span class="live-pulse w-2 h-2 bg-white rounded-full inline-block mr-2"></span>
                            LIVE {{ $match->minute ? $match->minute . "'" : '' }}
                        </span>
                    </div>
                    
                    <div class="p-6 pt-12">
                        <!-- League -->
                        <div class="text-center mb-4">
                            <span class="text-xs text-muted bg-gray-800 px-2 py-1 rounded">
                                {{ $match->league->name }}
                            </span>
                        </div>
                        
                        <!-- Teams and Score -->
                        <div class="space-y-4">
                            <!-- Home Team -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    <img src="{{ $match->homeTeam->logo_url ?? '/images/default-logo.svg' }}" 
                                         alt="{{ $match->homeTeam->name }}" 
                                         class="w-8 h-8 rounded-full">
                                    <span class="text-light font-medium truncate">{{ $match->homeTeam->name }}</span>
                                </div>
                                <span class="text-2xl font-bold text-light min-w-[2rem] text-center">
                                    {{ $match->home_score ?? 0 }}
                                </span>
                            </div>
                            
                            <!-- Away Team -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    <img src="{{ $match->awayTeam->logo_url ?? '/images/default-logo.svg' }}" 
                                         alt="{{ $match->awayTeam->name }}" 
                                         class="w-8 h-8 rounded-full">
                                    <span class="text-light font-medium truncate">{{ $match->awayTeam->name }}</span>
                                </div>
                                <span class="text-2xl font-bold text-light min-w-[2rem] text-center">
                                    {{ $match->away_score ?? 0 }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Match Time -->
                        <div class="text-center mt-4">
                            <div class="text-sm text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                Started at {{ $match->match_date->format('H:i') }}
                            </div>
                        </div>
                        
                        <!-- View Details Button -->
                        <div class="mt-4">
                            <a href="{{ route('matches.show', $match) }}" 
                               class="w-full bg-primary hover:bg-green-600 text-white text-center py-2 px-4 rounded-lg text-sm font-medium inline-block transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Statistics Summary -->
        <div class="mt-8 bg-card rounded-lg p-6">
            <h3 class="text-lg font-bold text-light mb-4">Live Statistics</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-500">{{ $liveMatches->count() }}</div>
                    <div class="text-sm text-muted">Live Matches</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary">{{ $liveMatches->sum('home_score') + $liveMatches->sum('away_score') }}</div>
                    <div class="text-sm text-muted">Total Goals</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-500">{{ $liveMatches->groupBy('league_id')->count() }}</div>
                    <div class="text-sm text-muted">Leagues</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-500">{{ $liveMatches->avg('minute') ? round($liveMatches->avg('minute')) : 0 }}'</div>
                    <div class="text-sm text-muted">Avg. Minute</div>
                </div>
            </div>
        </div>
    @else
        <!-- No Live Matches -->
        <div class="bg-card rounded-lg p-12 text-center">
            <div class="text-gray-500 mb-4">
                <i class="fas fa-tv text-6xl"></i>
            </div>
            <h3 class="text-xl font-bold text-light mb-2">No Live Matches</h3>
            <p class="text-muted mb-6">There are currently no live matches being played.</p>
            
            <!-- Quick Links -->
            <div class="space-y-3">
                <a href="{{ route('matches.index') }}" 
                   class="inline-block bg-primary hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    View All Matches
                </a>
                <div class="text-sm text-muted">
                    <a href="{{ route('home') }}" class="text-primary hover:text-green-300">Return to Dashboard</a>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Upcoming Live Matches -->
    @if(isset($upcomingMatches) && $upcomingMatches->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold text-light mb-4">
                <i class="fas fa-clock mr-2"></i>Starting Soon
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($upcomingMatches as $match)
                    <div class="bg-card rounded-lg p-4 border border-blue-500">
                        <div class="text-center mb-3">
                            <span class="text-xs text-blue-400 bg-blue-900 px-2 py-1 rounded">
                                Starts {{ $match->match_date->format('H:i') }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-light truncate flex-1">{{ $match->homeTeam->short_name }}</span>
                            <span class="text-muted mx-2">vs</span>
                            <span class="text-light truncate flex-1 text-right">{{ $match->awayTeam->short_name }}</span>
                        </div>
                        
                        <div class="text-xs text-center text-muted mt-2">
                            {{ $match->league->name }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    let refreshInterval;
    
    function refreshMatches() {
        const icon = document.getElementById('refresh-icon');
        const lastUpdate = document.getElementById('last-update');
        
        // Show spinning animation
        icon.classList.add('fa-spin');
        lastUpdate.textContent = 'Updating...';
        
        // In a real implementation, you'd make an AJAX call here
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(data => {
            // Parse the response and update match cards
            // For now, just reload the page
            setTimeout(() => {
                location.reload();
            }, 1000);
        })
        .catch(error => {
            console.error('Error refreshing matches:', error);
            icon.classList.remove('fa-spin');
            lastUpdate.textContent = 'Update failed';
        });
    }
    
    // Auto-refresh every 30 seconds
    function startAutoRefresh() {
        refreshInterval = setInterval(() => {
            refreshMatches();
        }, 30000);
        
        // Update the last update time
        setInterval(() => {
            const now = new Date();
            document.getElementById('last-update').textContent = 
                'Last updated: ' + now.toLocaleTimeString();
        }, 1000);
    }
    
    // Start auto-refresh when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startAutoRefresh();
    });
    
    // Stop auto-refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            startAutoRefresh();
        }
    });
</script>

<style>
    .live-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
    
    .match-card {
        transition: transform 0.2s;
    }
    
    .match-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush
