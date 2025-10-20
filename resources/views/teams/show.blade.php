@extends('layouts.app')

@section('title', $team->name . ' - Team Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Team Header -->
    <div class="bg-card rounded-lg p-8 mb-8">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-8">
            <!-- Team Logo -->
            <div class="flex-shrink-0">
                @if($team->logo_url)
                    <img src="{{ $team->logo_url }}" 
                         alt="{{ $team->name }}" 
                         class="w-32 h-32 object-contain">
                @else
                    <div class="w-32 h-32 bg-primary rounded-full flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-5xl"></i>
                    </div>
                @endif
            </div>
            
            <!-- Team Info -->
            <div class="flex-1">
                <h1 class="text-4xl font-bold text-light mb-4">{{ $team->name }}</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($team->league)
                        <div class="flex items-center text-muted">
                            <i class="fas fa-trophy mr-3 text-primary"></i>
                            <div>
                                <span class="block text-sm">League</span>
                                <span class="text-light font-medium">{{ $team->league->name }}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if($team->founded_year)
                        <div class="flex items-center text-muted">
                            <i class="fas fa-calendar mr-3 text-primary"></i>
                            <div>
                                <span class="block text-sm">Founded</span>
                                <span class="text-light font-medium">{{ $team->founded_year }}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if($team->venue)
                        <div class="flex items-center text-muted">
                            <i class="fas fa-map-marker-alt mr-3 text-primary"></i>
                            <div>
                                <span class="block text-sm">Venue</span>
                                <span class="text-light font-medium">{{ $team->venue }}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if($team->website)
                        <div class="flex items-center text-muted">
                            <i class="fas fa-globe mr-3 text-primary"></i>
                            <div>
                                <span class="block text-sm">Website</span>
                                <a href="{{ $team->website }}" target="_blank" 
                                   class="text-primary hover:text-green-400 font-medium">
                                    Visit Website
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Squad Section -->
            @if($team->players && $team->players->count() > 0)
                <div class="bg-card rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-light mb-6 flex items-center">
                        <i class="fas fa-users mr-3 text-primary"></i>
                        Squad ({{ $team->players->count() }} Players)
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($team->players as $player)
                            <div class="flex items-center p-4 bg-gray-800 rounded-lg hover:bg-gray-700 transition duration-200">
                                <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white font-bold mr-4">
                                    {{ $player->jersey_number ?? '?' }}
                                </div>
                                <div class="flex-1">
                                    <a href="{{ route('players.show', $player->slug) }}" 
                                       class="text-light hover:text-primary font-medium">
                                        {{ $player->name }}
                                    </a>
                                    <div class="text-sm text-muted">
                                        {{ $player->position ?? 'Unknown Position' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Matches -->
            @if($recentMatches && $recentMatches->count() > 0)
                <div class="bg-card rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-light mb-6 flex items-center">
                        <i class="fas fa-history mr-3 text-primary"></i>
                        Recent Matches
                    </h2>
                    
                    <div class="space-y-4">
                        @foreach($recentMatches as $match)
                            <div class="flex items-center justify-between p-4 bg-gray-800 rounded-lg hover:bg-gray-700 transition duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="text-sm text-muted">
                                        {{ $match->match_date->format('M d') }}
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($match->homeTeam->logo_url)
                                            <img src="{{ $match->homeTeam->logo_url }}" alt="{{ $match->homeTeam->name }}" class="w-6 h-6">
                                        @endif
                                        <span class="text-light">{{ $match->homeTeam->name }}</span>
                                    </div>
                                    <span class="text-muted">vs</span>
                                    <div class="flex items-center space-x-2">
                                        @if($match->awayTeam->logo_url)
                                            <img src="{{ $match->awayTeam->logo_url }}" alt="{{ $match->awayTeam->name }}" class="w-6 h-6">
                                        @endif
                                        <span class="text-light">{{ $match->awayTeam->name }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    @if($match->home_score !== null && $match->away_score !== null)
                                        <span class="text-light font-bold">
                                            {{ $match->home_score }} - {{ $match->away_score }}
                                        </span>
                                    @endif
                                    <a href="{{ route('matches.show', $match->id) }}" 
                                       class="text-primary hover:text-green-400">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Upcoming Matches -->
            @if($upcomingMatches && $upcomingMatches->count() > 0)
                <div class="bg-card rounded-lg p-6">
                    <h3 class="text-xl font-bold text-light mb-4 flex items-center">
                        <i class="fas fa-calendar-alt mr-3 text-primary"></i>
                        Upcoming Matches
                    </h3>
                    
                    <div class="space-y-3">
                        @foreach($upcomingMatches as $match)
                            <div class="p-3 bg-gray-800 rounded-lg">
                                <div class="text-sm text-muted mb-1">
                                    {{ $match->match_date->format('M d, Y - H:i') }}
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-sm">
                                        <div class="text-light">{{ $match->homeTeam->name }}</div>
                                        <div class="text-light">{{ $match->awayTeam->name }}</div>
                                    </div>
                                    <a href="{{ route('matches.show', $match->id) }}" 
                                       class="text-primary hover:text-green-400 text-sm">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-card rounded-lg p-6">
                <h3 class="text-xl font-bold text-light mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('teams.index') }}" 
                       class="block w-full bg-gray-700 hover:bg-gray-600 text-light px-4 py-2 rounded-lg text-center transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        All Teams
                    </a>
                    
                    @if($team->league)
                        <a href="{{ route('leagues.show', $team->league->slug) }}" 
                           class="block w-full bg-primary hover:bg-green-600 text-white px-4 py-2 rounded-lg text-center transition duration-200">
                            <i class="fas fa-trophy mr-2"></i>
                            View League
                        </a>
                    @endif
                    
                    <a href="{{ route('matches.index', ['team' => $team->slug]) }}" 
                       class="block w-full bg-gray-700 hover:bg-gray-600 text-light px-4 py-2 rounded-lg text-center transition duration-200">
                        <i class="fas fa-futbol mr-2"></i>
                        Team Matches
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
