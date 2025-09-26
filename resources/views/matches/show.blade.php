@extends('layouts.app')

@section('title', ($match->homeTeam ? $match->homeTeam->name : 'Team') . ' vs ' . ($match->awayTeam ? $match->awayTeam->name : 'Team') . ' - Match Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb Navigation -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="{{ route('home') }}" class="text-primary hover:text-green-300">
                <i class="fas fa-home mr-1"></i>Home
            </a>
            <span class="text-muted">/</span>
            <a href="{{ route('matches.index') }}" class="text-primary hover:text-green-300">
                Matches
            </a>
            <span class="text-muted">/</span>
            <a href="{{ route('matches.index', ['date' => $match->match_date->format('Y-m-d')]) }}" 
               class="text-primary hover:text-green-300">
                {{ $match->match_date->format('M j, Y') }}
            </a>
            <span class="text-muted">/</span>
            <span class="text-light">{{ $match->homeTeam ? $match->homeTeam->short_name : 'HOME' }} vs {{ $match->awayTeam ? $match->awayTeam->short_name : 'AWAY' }}</span>
        </nav>
        
        <div class="mt-3">
            <a href="{{ route('matches.index', ['date' => $match->match_date->format('Y-m-d')]) }}" 
               class="text-primary hover:text-green-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to {{ $match->match_date->format('M j, Y') }} Matches
            </a>
        </div>
    </div>

    <!-- Match Header -->
    <div class="bg-card rounded-lg p-6 mb-8">
        <div class="text-center mb-4">
            <div class="text-sm text-muted mb-2">
                <i class="fas fa-trophy mr-2"></i>{{ $match->league->name }}
            </div>
            <div class="text-sm text-muted">
                <i class="fas fa-calendar mr-2"></i>{{ $match->match_date->format('F j, Y \a\t H:i') }}
            </div>
        </div>

        <!-- Teams and Score -->
        <div class="flex items-center justify-center mb-6">
            <div class="flex items-center space-x-8">
                <!-- Home Team -->
                <div class="text-center">
                    <img src="{{ $match->homeTeam->logo ?? '/images/default-logo.png' }}" 
                         alt="{{ $match->homeTeam->name }}" 
                         class="w-24 h-24 mx-auto mb-4 rounded-full">
                    <h2 class="text-xl font-bold text-light">{{ $match->homeTeam->name }}</h2>
                    <p class="text-muted text-sm">{{ $match->homeTeam->short_name }}</p>
                </div>

                <!-- Score -->
                <div class="text-center px-8">
                    @if($match->status === 'live' || $match->status === 'finished')
                        <div class="text-6xl font-bold text-light mb-2">
                            {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                        </div>
                    @else
                        <div class="text-4xl text-muted mb-2">vs</div>
                    @endif
                    
                    <!-- Match Status -->
                    <div class="text-center">
                        @if($match->status === 'live')
                            <span class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                <span class="live-pulse w-2 h-2 bg-white rounded-full inline-block mr-2"></span>
                                LIVE {{ $match->minute ? $match->minute . "'" : '' }}
                            </span>
                        @elseif($match->status === 'finished')
                            <span class="bg-gray-600 text-white px-4 py-2 rounded-full text-sm">
                                Full Time
                            </span>
                        @elseif($match->status === 'scheduled')
                            <span class="bg-blue-600 text-white px-4 py-2 rounded-full text-sm">
                                {{ $match->match_date->format('H:i') }}
                            </span>
                        @else
                            <span class="bg-yellow-600 text-white px-4 py-2 rounded-full text-sm">
                                {{ ucfirst($match->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Away Team -->
                <div class="text-center">
                    <img src="{{ $match->awayTeam->logo ?? '/images/default-logo.png' }}" 
                         alt="{{ $match->awayTeam->name }}" 
                         class="w-24 h-24 mx-auto mb-4 rounded-full">
                    <h2 class="text-xl font-bold text-light">{{ $match->awayTeam->name }}</h2>
                    <p class="text-muted text-sm">{{ $match->awayTeam->short_name }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($match->status === 'finished')
        <!-- Match Result Summary -->
        <div class="bg-card rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-light mb-4">
                <i class="fas fa-trophy mr-2"></i>Match Result
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="space-y-2">
                    <h4 class="text-lg font-semibold text-light">{{ $match->homeTeam->name }}</h4>
                    <div class="text-3xl font-bold {{ $match->home_score > $match->away_score ? 'text-green-500' : ($match->home_score == $match->away_score ? 'text-yellow-500' : 'text-red-500') }}">
                        {{ $match->home_score ?? 0 }}
                    </div>
                    <div class="text-sm text-muted">{{ $match->home_score > $match->away_score ? 'WINNER' : ($match->home_score == $match->away_score ? 'DRAW' : 'LOSER') }}</div>
                </div>
                <div class="flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-sm text-muted mb-2">Full Time</div>
                        <div class="text-4xl font-bold text-light">-</div>
                        <div class="text-xs text-muted mt-2">{{ $match->match_date->format('M j, Y') }}</div>
                    </div>
                </div>
                <div class="space-y-2">
                    <h4 class="text-lg font-semibold text-light">{{ $match->awayTeam->name }}</h4>
                    <div class="text-3xl font-bold {{ $match->away_score > $match->home_score ? 'text-green-500' : ($match->home_score == $match->away_score ? 'text-yellow-500' : 'text-red-500') }}">
                        {{ $match->away_score ?? 0 }}
                    </div>
                    <div class="text-sm text-muted">{{ $match->away_score > $match->home_score ? 'WINNER' : ($match->home_score == $match->away_score ? 'DRAW' : 'LOSER') }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Match Stats and Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Match Information -->
        <div class="bg-card rounded-lg p-6">
            <h3 class="text-xl font-bold text-light mb-4">
                <i class="fas fa-info-circle mr-2"></i>Match Information
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-muted">Competition:</span>
                    <span class="text-light">{{ $match->league->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted">Date:</span>
                    <span class="text-light">{{ $match->match_date->format('F j, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted">Kick-off:</span>
                    <span class="text-light">{{ $match->match_date->format('H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted">Status:</span>
                    <span class="text-light font-semibold {{ $match->status === 'finished' ? 'text-green-500' : 'text-blue-500' }}">{{ ucfirst($match->status) }}</span>
                </div>
                @if($match->venue)
                    <div class="flex justify-between">
                        <span class="text-muted">Venue:</span>
                        <span class="text-light">{{ $match->venue }}</span>
                    </div>
                @endif
                @if($match->referee)
                    <div class="flex justify-between">
                        <span class="text-muted">Referee:</span>
                        <span class="text-light">{{ $match->referee }}</span>
                    </div>
                @endif
                @if($match->attendance)
                    <div class="flex justify-between">
                        <span class="text-muted">Attendance:</span>
                        <span class="text-light">{{ number_format($match->attendance) }}</span>
                    </div>
                @endif
                @if($match->matchweek)
                    <div class="flex justify-between">
                        <span class="text-muted">Matchweek:</span>
                        <span class="text-light">{{ $match->matchweek }}</span>
                    </div>
                @endif
                @if($match->minute)
                    <div class="flex justify-between">
                        <span class="text-muted">Minute:</span>
                        <span class="text-light">{{ $match->minute }}'</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Match Statistics -->
        <div class="bg-card rounded-lg p-6">
            <h3 class="text-xl font-bold text-light mb-4">
                <i class="fas fa-chart-bar mr-2"></i>Match Statistics
            </h3>
            
            @if($match->status === 'live' || $match->status === 'finished')
                <div class="space-y-4">
                    <!-- Goals -->
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-muted">Goals Scored</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-light">{{ $match->homeTeam->short_name }}</span>
                            <div class="flex items-center space-x-4">
                                <span class="text-2xl font-bold text-light">{{ $match->home_score ?? 0 }}</span>
                                <span class="text-muted">-</span>
                                <span class="text-2xl font-bold text-light">{{ $match->away_score ?? 0 }}</span>
                            </div>
                            <span class="text-light">{{ $match->awayTeam->short_name }}</span>
                        </div>
                    </div>
                    
                    @if($match->status === 'finished')
                        <!-- Goal Difference -->
                        <div class="border-t border-gray-700 pt-4">
                            <div class="text-center">
                                @php
                                    $goalDiff = ($match->home_score ?? 0) - ($match->away_score ?? 0);
                                @endphp
                                @if($goalDiff > 0)
                                    <div class="text-green-500 font-semibold">{{ $match->homeTeam->short_name }} won by {{ $goalDiff }} goal{{ $goalDiff > 1 ? 's' : '' }}</div>
                                @elseif($goalDiff < 0)
                                    <div class="text-green-500 font-semibold">{{ $match->awayTeam->short_name }} won by {{ abs($goalDiff) }} goal{{ abs($goalDiff) > 1 ? 's' : '' }}</div>
                                @else
                                    <div class="text-yellow-500 font-semibold">Match ended in a draw</div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Match Summary -->
                        <div class="border-t border-gray-700 pt-4">
                            <div class="text-center space-y-2">
                                <div class="text-sm text-muted">Total Goals: {{ ($match->home_score ?? 0) + ($match->away_score ?? 0) }}</div>
                                @if(($match->home_score ?? 0) + ($match->away_score ?? 0) > 2)
                                    <div class="text-xs text-green-400">High-scoring match</div>
                                @elseif(($match->home_score ?? 0) + ($match->away_score ?? 0) == 0)
                                    <div class="text-xs text-blue-400">Clean sheets for both teams</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center text-muted py-8">
                    <i class="fas fa-clock text-3xl mb-3"></i>
                    <p>Statistics will be available when the match starts.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Player Stats (if available) -->
    @if($match->playerStats && $match->playerStats->count() > 0)
        <div class="mt-8">
            <div class="bg-card rounded-lg p-6">
                <h3 class="text-xl font-bold text-light mb-4">
                    <i class="fas fa-users mr-2"></i>Player Performance
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 text-muted">Player</th>
                                <th class="text-center py-3 text-muted">Team</th>
                                <th class="text-center py-3 text-muted">Goals</th>
                                <th class="text-center py-3 text-muted">Assists</th>
                                <th class="text-center py-3 text-muted">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($match->playerStats as $stat)
                                <tr class="border-b border-gray-800 last:border-b-0">
                                    <td class="py-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-xs"></i>
                                            </div>
                                            <span class="text-light">{{ $stat->player->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center py-3 text-light">{{ $stat->player->team->short_name ?? 'N/A' }}</td>
                                    <td class="text-center py-3 text-light">{{ $stat->goals ?? 0 }}</td>
                                    <td class="text-center py-3 text-light">{{ $stat->assists ?? 0 }}</td>
                                    <td class="text-center py-3">
                                        @if($stat->rating)
                                            <span class="bg-primary text-white px-2 py-1 rounded text-xs">
                                                {{ number_format($stat->rating, 1) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($match->status === 'finished')
        <!-- Team Comparison Section -->
        <div class="mt-8">
            <div class="bg-card rounded-lg p-6">
                <h3 class="text-xl font-bold text-light mb-6">
                    <i class="fas fa-balance-scale mr-2"></i>Team Comparison
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Home Team -->
                    <div class="text-center">
                        <img src="{{ $match->homeTeam->logo ?? '/images/default-logo.png' }}" 
                             alt="{{ $match->homeTeam->name }}" 
                             class="w-16 h-16 mx-auto mb-3 rounded-full">
                        <h4 class="text-lg font-bold text-light mb-2">{{ $match->homeTeam->name }}</h4>
                        <div class="space-y-2">
                            <div class="text-sm">
                                <span class="text-muted">Goals:</span>
                                <span class="text-light font-bold ml-2">{{ $match->home_score ?? 0 }}</span>
                            </div>
                            <div class="text-sm">
                                <span class="text-muted">League:</span>
                                <span class="text-light ml-2">{{ $match->league->name }}</span>
                            </div>
                            @if($match->homeTeam->city && $match->homeTeam->city !== 'Unknown')
                                <div class="text-sm">
                                    <span class="text-muted">City:</span>
                                    <span class="text-light ml-2">{{ $match->homeTeam->city }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- VS Section -->
                    <div class="flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-muted mb-2">VS</div>
                            <div class="text-sm text-muted">
                                @php
                                    $totalGoals = ($match->home_score ?? 0) + ($match->away_score ?? 0);
                                @endphp
                                {{ $totalGoals }} Total Goals
                            </div>
                        </div>
                    </div>
                    
                    <!-- Away Team -->
                    <div class="text-center">
                        <img src="{{ $match->awayTeam->logo ?? '/images/default-logo.png' }}" 
                             alt="{{ $match->awayTeam->name }}" 
                             class="w-16 h-16 mx-auto mb-3 rounded-full">
                        <h4 class="text-lg font-bold text-light mb-2">{{ $match->awayTeam->name }}</h4>
                        <div class="space-y-2">
                            <div class="text-sm">
                                <span class="text-muted">Goals:</span>
                                <span class="text-light font-bold ml-2">{{ $match->away_score ?? 0 }}</span>
                            </div>
                            <div class="text-sm">
                                <span class="text-muted">League:</span>
                                <span class="text-light ml-2">{{ $match->league->name }}</span>
                            </div>
                            @if($match->awayTeam->city && $match->awayTeam->city !== 'Unknown')
                                <div class="text-sm">
                                    <span class="text-muted">City:</span>
                                    <span class="text-light ml-2">{{ $match->awayTeam->city }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Match Details -->
        <div class="mt-8">
            <div class="bg-card rounded-lg p-6">
                <h3 class="text-xl font-bold text-light mb-6">
                    <i class="fas fa-info-circle mr-2"></i>Match Details
                </h3>
                
                @if($detailedMatch && isset($detailedMatch['score']))
                    <!-- API Enhanced Details -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Score Breakdown -->
                        <div class="bg-gray-800 rounded-lg p-4">
                            <h4 class="font-bold text-primary mb-3">Score Breakdown</h4>
                            @if(isset($detailedMatch['score']['fullTime']))
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-muted">Full Time:</span>
                                <span class="font-bold text-light text-lg">
                                    {{ $detailedMatch['score']['fullTime']['home'] }} - {{ $detailedMatch['score']['fullTime']['away'] }}
                                </span>
                            </div>
                            @endif
                            @if(isset($detailedMatch['score']['halfTime']))
                            <div class="flex justify-between items-center">
                                <span class="text-muted">Half Time:</span>
                                <span class="font-medium text-light">
                                    {{ $detailedMatch['score']['halfTime']['home'] }} - {{ $detailedMatch['score']['halfTime']['away'] }}
                                </span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Match Officials -->
                        @if(isset($detailedMatch['referees']) && count($detailedMatch['referees']) > 0)
                        <div class="bg-gray-800 rounded-lg p-4">
                            <h4 class="font-bold text-primary mb-3">Match Officials</h4>
                            @foreach($detailedMatch['referees'] as $referee)
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-muted text-sm">{{ ucfirst(strtolower($referee['type'] ?? 'Official')) }}:</span>
                                <span class="text-light text-sm font-medium">{{ $referee['name'] ?? 'N/A' }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    
                    <!-- Match Information -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        @if(isset($detailedMatch['matchday']))
                        <div class="text-center bg-gray-800 rounded-lg p-3">
                            <div class="text-2xl font-bold text-primary">{{ $detailedMatch['matchday'] }}</div>
                            <div class="text-sm text-muted">Matchday</div>
                        </div>
                        @endif
                        
                        @if(isset($detailedMatch['stage']))
                        <div class="text-center bg-gray-800 rounded-lg p-3">
                            <div class="text-sm font-bold text-light">{{ str_replace('_', ' ', $detailedMatch['stage']) }}</div>
                            <div class="text-sm text-muted">Stage</div>
                        </div>
                        @endif
                        
                        <div class="text-center bg-gray-800 rounded-lg p-3">
                            <div class="text-sm font-bold text-light">{{ $match->match_date->format('H:i') }}</div>
                            <div class="text-sm text-muted">Kick-off</div>
                        </div>
                        
                        @if(isset($detailedMatch['lastUpdated']))
                        <div class="text-center bg-gray-800 rounded-lg p-3">
                            <div class="text-sm font-bold text-light">{{ \Carbon\Carbon::parse($detailedMatch['lastUpdated'])->format('H:i') }}</div>
                            <div class="text-sm text-muted">Last Updated</div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Betting Information Notice -->
                    @if(isset($detailedMatch['odds']) && !empty($detailedMatch['odds']))
                    <div class="bg-yellow-900 border border-yellow-600 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-chart-line text-yellow-400 mr-2"></i>
                            <p class="text-sm text-yellow-100">
                                <strong>Market Data Available:</strong> This match includes betting market information for analysis purposes.
                            </p>
                        </div>
                    </div>
                    @endif
                    
                @elseif($match->events && is_array($match->events) && count($match->events) > 0)
                    <!-- Timeline from stored events -->
                    <div class="space-y-4">
                        @foreach($match->events as $event)
                            <div class="flex items-center space-x-4 p-3 bg-gray-800 rounded-lg">
                                <div class="text-sm font-bold text-primary min-w-[40px]">{{ $event['minute'] ?? '?' }}'</div>
                                <div class="flex-1">
                                    <div class="text-light font-medium">{{ $event['type'] ?? 'Event' }}</div>
                                    @if(isset($event['player']))
                                        <div class="text-sm text-muted">{{ $event['player'] }}</div>
                                    @endif
                                </div>
                                <div class="text-sm text-muted">{{ $event['team'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Basic match completion info -->
                    <div class="text-center py-8">
                        <div class="space-y-4">
                            <div class="text-4xl text-muted">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="space-y-2">
                                <div class="text-lg text-light font-semibold">Match Completed</div>
                                <div class="text-muted space-y-1">
                                    <div>Kick-off: {{ $match->match_date->format('H:i') }}</div>
                                    <div>Final Score: {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}</div>
                                    @if(!$match->api_match_id)
                                    <div class="text-xs bg-gray-800 inline-block px-2 py-1 rounded mt-2">
                                        Enhanced details available for newer matches
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- External Match Statistics Search -->
        <div class="mt-8">
            <div class="bg-card rounded-lg p-6">
                <h3 class="text-xl font-bold text-light mb-4">
                    <i class="fas fa-search mr-2"></i>Find More Match Statistics
                </h3>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 mb-4">
                    <a href="{{ route('matches.index', ['date' => $match->match_date->format('Y-m-d')]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to {{ $match->match_date->format('M d') }} Matches
                    </a>
                    
                    <!-- Quick Google Search -->
                    <a href="{{ $googleSearchUrl }}" 
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fab fa-google mr-2"></i>
                        Quick Search Stats
                        <i class="fas fa-external-link-alt ml-2 text-sm"></i>
                    </a>
                    
                    <!-- Advanced Search Modal Button -->
                    <button onclick="openSearchModal()" 
                            class="inline-flex items-center px-4 py-2 bg-primary hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-search-plus mr-2"></i>
                        More Search Options
                    </button>
                </div>
                
                <!-- Quick Access Links -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <a href="{{ $espnSearchUrl }}" 
                       target="_blank"
                       class="inline-flex items-center px-3 py-2 bg-red-600/20 text-red-300 hover:bg-red-600/30 font-medium rounded-lg transition-colors duration-200 text-sm">
                        <i class="fas fa-chart-line mr-1"></i>
                        ESPN
                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                    </a>
                    
                    <a href="{{ $bbcSearchUrl }}" 
                       target="_blank"
                       class="inline-flex items-center px-3 py-2 bg-orange-600/20 text-orange-300 hover:bg-orange-600/30 font-medium rounded-lg transition-colors duration-200 text-sm">
                        <i class="fas fa-futbol mr-1"></i>
                        BBC Sport
                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                    </a>
                    
                    <a href="{{ $highlightsSearchUrl }}" 
                       target="_blank"
                       class="inline-flex items-center px-3 py-2 bg-purple-600/20 text-purple-300 hover:bg-purple-600/30 font-medium rounded-lg transition-colors duration-200 text-sm">
                        <i class="fas fa-video mr-1"></i>
                        Highlights
                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                    </a>
                    
                    <a href="{{ $playerRatingsUrl }}" 
                       target="_blank"
                       class="inline-flex items-center px-3 py-2 bg-indigo-600/20 text-indigo-300 hover:bg-indigo-600/30 font-medium rounded-lg transition-colors duration-200 text-sm">
                        <i class="fas fa-users mr-1"></i>
                        Player Ratings
                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                    </a>
                </div>
                
                <p class="text-sm text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Search for detailed match statistics, player ratings, highlights, and analysis from various sports websites.
                </p>
            </div>
        </div>

        <!-- Related Matches -->
        <div class="mt-8">
            <div class="bg-card rounded-lg p-6">
                <h3 class="text-xl font-bold text-light mb-4">
                    <i class="fas fa-calendar mr-2"></i>Related Matches
                </h3>
                

                
                @if($relatedMatches->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($relatedMatches as $relatedMatch)
                            <div class="bg-gray-800 rounded-lg p-4">
                                <div class="text-center mb-3">
                                    <div class="text-xs text-muted">{{ $relatedMatch->match_date->format('M j, Y') }}</div>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="text-light truncate">{{ $relatedMatch->homeTeam->short_name }}</div>
                                    <div class="text-center mx-2">
                                        <div class="text-light font-bold">{{ $relatedMatch->home_score ?? 0 }}-{{ $relatedMatch->away_score ?? 0 }}</div>
                                    </div>
                                    <div class="text-light truncate text-right">{{ $relatedMatch->awayTeam->short_name }}</div>
                                </div>
                                <div class="text-center mt-2">
                                    <a href="{{ route('matches.show', $relatedMatch->id) }}" 
                                       class="text-primary hover:text-green-300 text-xs">
                                        View Match
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <p>No recent matches found for these teams.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Advanced Search Modal -->
<div id="searchModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-card rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-primary">Search Match Information</h3>
                    <button onclick="closeSearchModal()" class="text-muted hover:text-light">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <p class="text-sm text-muted mb-4">
                    Choose where to search for detailed match information:
                </p>
                
                <div class="space-y-3">
                    <a href="{{ $googleSearchUrl }}" 
                       target="_blank"
                       class="flex items-center p-3 bg-blue-600/20 rounded-lg hover:bg-blue-600/30 transition-colors">
                        <i class="fab fa-google text-blue-400 mr-3"></i>
                        <div>
                            <div class="font-medium text-light">Google Search</div>
                            <div class="text-sm text-muted">General match statistics and highlights</div>
                        </div>
                        <i class="fas fa-external-link-alt ml-auto text-muted"></i>
                    </a>
                    
                    <a href="{{ $espnSearchUrl }}" 
                       target="_blank"
                       class="flex items-center p-3 bg-red-600/20 rounded-lg hover:bg-red-600/30 transition-colors">
                        <i class="fas fa-chart-line text-red-400 mr-3"></i>
                        <div>
                            <div class="font-medium text-light">ESPN</div>
                            <div class="text-sm text-muted">Match reports and analysis</div>
                        </div>
                        <i class="fas fa-external-link-alt ml-auto text-muted"></i>
                    </a>
                    
                    <a href="{{ $bbcSearchUrl }}" 
                       target="_blank"
                       class="flex items-center p-3 bg-orange-600/20 rounded-lg hover:bg-orange-600/30 transition-colors">
                        <i class="fas fa-futbol text-orange-400 mr-3"></i>
                        <div>
                            <div class="font-medium text-light">BBC Sport</div>
                            <div class="text-sm text-muted">Live text commentary and reports</div>
                        </div>
                        <i class="fas fa-external-link-alt ml-auto text-muted"></i>
                    </a>
                    
                    <a href="{{ $playerRatingsUrl }}" 
                       target="_blank"
                       class="flex items-center p-3 bg-indigo-600/20 rounded-lg hover:bg-indigo-600/30 transition-colors">
                        <i class="fas fa-users text-indigo-400 mr-3"></i>
                        <div>
                            <div class="font-medium text-light">Player Ratings</div>
                            <div class="text-sm text-muted">Individual player performance ratings</div>
                        </div>
                        <i class="fas fa-external-link-alt ml-auto text-muted"></i>
                    </a>
                    
                    <a href="{{ $highlightsSearchUrl }}" 
                       target="_blank"
                       class="flex items-center p-3 bg-purple-600/20 rounded-lg hover:bg-purple-600/30 transition-colors">
                        <i class="fas fa-video text-purple-400 mr-3"></i>
                        <div>
                            <div class="font-medium text-light">Video Highlights</div>
                            <div class="text-sm text-muted">Match highlights and key moments</div>
                        </div>
                        <i class="fas fa-external-link-alt ml-auto text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Auto-refresh live match data
    @if($match->status === 'live')
        setInterval(function() {
            // In a real app, you'd fetch live updates here
            console.log('Refreshing live match data...');
            
            // You could reload the page or update specific elements
            // location.reload();
        }, 30000);
    @endif
    
    // Search modal functions
    function openSearchModal() {
        document.getElementById('searchModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSearchModal() {
        document.getElementById('searchModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('searchModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeSearchModal();
                }
            });
        }
        
        // Close modal with escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSearchModal();
            }
        });
    });
</script>
@endpush