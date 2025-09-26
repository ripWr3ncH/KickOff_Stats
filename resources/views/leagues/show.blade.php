@extends('layouts.app')

@section('title', $league->name . ' - KickOff Stats')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- League Header -->
    <div class="bg-card rounded-lg p-6 mb-8">
        <div class="flex items-center space-x-4">
            @if($league->logo_url)
                <div class="w-16 h-16 bg-white rounded-full p-2 shadow-lg">
                    <img src="{{ $league->logo_url }}" 
                         alt="{{ $league->name }} Logo" 
                         class="w-full h-full object-contain league-logo">
                </div>
            @else
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center">
                    <i class="fas fa-trophy text-white text-2xl"></i>
                </div>
            @endif
            <div>
                <h1 class="text-3xl font-bold text-light">{{ $league->name }}</h1>
                <p class="text-muted">{{ $league->country }} • {{ $league->season }}</p>
            </div>
        </div>
    </div>

    <!-- League Table/Standings -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-light">
                <i class="fas fa-list-ol mr-2"></i>League Table
            </h2>
            <a href="{{ route('leagues.standings', $league->slug) }}" 
               class="bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                <i class="fas fa-external-link-alt mr-1"></i>Full Standings
            </a>
        </div>
        <div class="bg-card rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-800">
                        <tr class="text-left">
                            <th class="px-4 py-3 text-muted text-sm font-medium">#</th>
                            <th class="px-4 py-3 text-muted text-sm font-medium">Team</th>
                            <th class="px-4 py-3 text-muted text-sm font-medium text-center">P</th>
                            <th class="px-4 py-3 text-muted text-sm font-medium text-center">W</th>
                            <th class="px-4 py-3 text-muted text-sm font-medium text-center">D</th>
                            <th class="px-4 py-3 text-muted text-sm font-medium text-center">L</th>
                            <th class="px-4 py-3 text-muted text-sm font-medium text-center">GF</th>
                            <th class="px-4 py-3 text-muted text-sm font-medium text-center">GA</th>
                            <th class="px-4 py-3 text-muted text-sm font-medium text-center">GD</th>
                            <th class="px-4 py-3 text-muted text-sm font-medium text-center">Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($standings as $index => $standing)
                        <tr class="border-b border-gray-700 hover:bg-gray-800 transition-colors">
                            <td class="px-4 py-3">
                                <span class="flex items-center justify-center w-6 h-6 text-sm font-bold
                                    @if(($standing['position'] ?? $index + 1) <= 4) bg-green-600 text-white
                                    @elseif(($standing['position'] ?? $index + 1) <= 6) bg-orange-600 text-white
                                    @elseif(($standing['position'] ?? $index + 1) >= count($standings) - 2) bg-red-600 text-white
                                    @else bg-gray-600 text-white
                                    @endif
                                    rounded-full">
                                    {{ $standing['position'] ?? $index + 1 }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $standing['team']['crest'] ?? $standing['team']->logo ?? '/images/default-logo.png' }}" 
                                         alt="{{ $standing['team']['name'] ?? $standing['team']->name }}" 
                                         class="w-8 h-8 rounded-full">
                                    <span class="text-light font-medium">{{ $standing['team']['shortName'] ?? $standing['team']['name'] ?? $standing['team']->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-light">{{ $standing['playedGames'] ?? $standing['played'] }}</td>
                            <td class="px-4 py-3 text-center text-light">{{ $standing['won'] ?? $standing['wins'] }}</td>
                            <td class="px-4 py-3 text-center text-light">{{ $standing['draw'] ?? $standing['draws'] }}</td>
                            <td class="px-4 py-3 text-center text-light">{{ $standing['lost'] ?? $standing['losses'] }}</td>
                            <td class="px-4 py-3 text-center text-light">{{ $standing['goalsFor'] ?? $standing['goals_for'] }}</td>
                            <td class="px-4 py-3 text-center text-light">{{ $standing['goalsAgainst'] ?? $standing['goals_against'] }}</td>
                            <td class="px-4 py-3 text-center text-light">
                                <span class="@if(($standing['goalDifference'] ?? $standing['goal_difference']) > 0) text-green-400 @elseif(($standing['goalDifference'] ?? $standing['goal_difference']) < 0) text-red-400 @endif">
                                    {{ ($standing['goalDifference'] ?? $standing['goal_difference']) > 0 ? '+' : '' }}{{ $standing['goalDifference'] ?? $standing['goal_difference'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold text-primary">{{ $standing['points'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-muted">
                                No standings data available yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Legend -->
        <div class="mt-4 flex flex-wrap items-center space-x-6 text-xs text-muted">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-green-600 rounded-full"></div>
                <span>Champions League</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-orange-600 rounded-full"></div>
                <span>Europa League</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-red-600 rounded-full"></div>
                <span>Relegation</span>
            </div>
        </div>
    </div>

    <!-- Recent Results & Upcoming Matches -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Matches -->
        <div>
            <h2 class="text-xl font-bold text-light mb-4">
                <i class="fas fa-history mr-2"></i>Recent Results
            </h2>
            <div class="bg-card rounded-lg p-4">
                @forelse($recentMatches as $match)
                <div class="flex items-center justify-between py-3 border-b border-gray-700 last:border-b-0">
                    <div class="flex items-center space-x-2 flex-1">
                        <img src="{{ $match->homeTeam->logo ?? '/images/default-logo.png' }}" 
                             alt="{{ $match->homeTeam->name }}" 
                             class="w-6 h-6 rounded-full">
                        <span class="text-light text-sm">{{ $match->homeTeam->short_name }}</span>
                    </div>
                    <div class="text-center px-3">
                        <div class="text-sm font-bold text-light">
                            {{ $match->home_score }} - {{ $match->away_score }}
                        </div>
                        <div class="text-xs text-muted">
                            {{ $match->match_date->format('M j') }}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 flex-1 justify-end">
                        <span class="text-light text-sm">{{ $match->awayTeam->short_name }}</span>
                        <img src="{{ $match->awayTeam->logo ?? '/images/default-logo.png' }}" 
                             alt="{{ $match->awayTeam->name }}" 
                             class="w-6 h-6 rounded-full">
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-8">No recent matches</p>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Matches -->
        <div>
            <h2 class="text-xl font-bold text-light mb-4">
                <i class="fas fa-clock mr-2"></i>Upcoming Matches
            </h2>
            <div class="bg-card rounded-lg p-4">
                @forelse($upcomingMatches as $match)
                <div class="flex items-center justify-between py-3 border-b border-gray-700 last:border-b-0">
                    <div class="flex items-center space-x-2 flex-1">
                        <img src="{{ $match->homeTeam->logo ?? '/images/default-logo.png' }}" 
                             alt="{{ $match->homeTeam->name }}" 
                             class="w-6 h-6 rounded-full">
                        <span class="text-light text-sm">{{ $match->homeTeam->short_name }}</span>
                    </div>
                    <div class="text-center px-3">
                        <div class="text-xs text-muted">
                            {{ $match->match_date->format('M j, H:i') }}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 flex-1 justify-end">
                        <span class="text-light text-sm">{{ $match->awayTeam->short_name }}</span>
                        <img src="{{ $match->awayTeam->logo ?? '/images/default-logo.png' }}" 
                             alt="{{ $match->awayTeam->name }}" 
                             class="w-6 h-6 rounded-full">
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-8">No upcoming matches</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
