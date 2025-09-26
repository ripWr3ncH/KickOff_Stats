@extends('layouts.app')

@section('title', $match->homeTeam->name . ' vs ' . $match->awayTeam->name . ' - Match Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('matches.index') }}" class="text-primary hover:text-green-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Back to Matches
        </a>
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
                    <span class="text-light">{{ ucfirst($match->status) }}</span>
                </div>
                @if($match->minute)
                    <div class="flex justify-between">
                        <span class="text-muted">Minute:</span>
                        <span class="text-light">{{ $match->minute }}'</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-card rounded-lg p-6">
            <h3 class="text-xl font-bold text-light mb-4">
                <i class="fas fa-chart-bar mr-2"></i>Quick Stats
            </h3>
            
            @if($match->status === 'live' || $match->status === 'finished')
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-muted">Goals</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-light">{{ $match->homeTeam->short_name }}</span>
                            <span class="text-2xl font-bold text-light">{{ $match->home_score ?? 0 }}</span>
                            <span class="text-2xl font-bold text-light">{{ $match->away_score ?? 0 }}</span>
                            <span class="text-light">{{ $match->awayTeam->short_name }}</span>
                        </div>
                    </div>
                    
                    <!-- Additional stats would go here -->
                    <div class="text-center text-muted text-sm mt-4">
                        More detailed statistics coming soon...
                    </div>
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
</script>
@endpush