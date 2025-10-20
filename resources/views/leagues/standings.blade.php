@extends('layouts.app')

@section('title', $league->name . ' Standings - KickOff Stats')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- League Header -->
    <div class="bg-card rounded-lg p-6 mb-8">
        <div class="flex items-center justify-between">
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
                    <p class="text-muted">{{ $league->country }} • {{ $competitionInfo['currentSeason']['startDate'] ?? $league->season }}</p>
                </div>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('leagues.show', $league->slug) }}" 
                   class="bg-gray-700 hover:bg-gray-600 text-light px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to League
                </a>
            </div>
        </div>
    </div>

    <!-- Competition Stats -->
    @if($competitionInfo)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-card rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-primary">{{ $competitionInfo['currentSeason']['currentMatchday'] ?? 'N/A' }}</div>
            <div class="text-sm text-muted">Current Matchday</div>
        </div>
        <div class="bg-card rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-primary">{{ count($standings) }}</div>
            <div class="text-sm text-muted">Teams</div>
        </div>
        <div class="bg-card rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-primary">{{ $competitionInfo['currentSeason']['startDate'] ? \Carbon\Carbon::parse($competitionInfo['currentSeason']['startDate'])->format('M Y') : 'N/A' }}</div>
            <div class="text-sm text-muted">Season Start</div>
        </div>
        <div class="bg-card rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-primary">{{ $competitionInfo['currentSeason']['endDate'] ? \Carbon\Carbon::parse($competitionInfo['currentSeason']['endDate'])->format('M Y') : 'N/A' }}</div>
            <div class="text-sm text-muted">Season End</div>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        <!-- League Table -->
        <div class="xl:col-span-3">
            <div class="bg-card rounded-lg overflow-hidden">
                <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                    <h2 class="text-xl font-bold text-light flex items-center">
                        <i class="fas fa-list-ol mr-3"></i>League Standings
                        <span class="ml-auto text-sm text-muted">{{ $competitionInfo['currentSeason']['startDate'] ?? $league->season }}</span>
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-800 border-b border-gray-700">
                            <tr class="text-left">
                                <th class="px-4 py-4 text-muted text-sm font-medium">#</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium">Team</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium text-center">P</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium text-center">W</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium text-center">D</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium text-center">L</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium text-center">GF</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium text-center">GA</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium text-center">GD</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium text-center">Pts</th>
                                <th class="px-4 py-4 text-muted text-sm font-medium text-center">Form</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($standings as $standing)
                            <tr class="border-b border-gray-700 hover:bg-gray-800 transition-colors group">
                                <td class="px-4 py-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="flex items-center justify-center w-8 h-8 text-sm font-bold
                                            @if($standing['position'] <= 4) bg-green-600 text-white
                                            @elseif($standing['position'] <= 6) bg-orange-600 text-white
                                            @elseif($standing['position'] >= count($standings) - 2) bg-red-600 text-white
                                            @else bg-gray-600 text-white
                                            @endif
                                            rounded-full group-hover:scale-110 transition-transform">
                                            {{ $standing['position'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $standing['team']['crest'] ?? '/images/default-logo.svg' }}" 
                                             alt="{{ $standing['team']['name'] }}" 
                                             class="w-10 h-10 rounded-full">
                                        <div>
                                            <div class="text-light font-medium">{{ $standing['team']['name'] }}</div>
                                            <div class="text-xs text-muted">{{ $standing['team']['shortName'] ?? $standing['team']['name'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center text-light font-medium">{{ $standing['playedGames'] }}</td>
                                <td class="px-4 py-4 text-center text-green-400 font-medium">{{ $standing['won'] }}</td>
                                <td class="px-4 py-4 text-center text-yellow-400 font-medium">{{ $standing['draw'] }}</td>
                                <td class="px-4 py-4 text-center text-red-400 font-medium">{{ $standing['lost'] }}</td>
                                <td class="px-4 py-4 text-center text-light font-medium">{{ $standing['goalsFor'] }}</td>
                                <td class="px-4 py-4 text-center text-light font-medium">{{ $standing['goalsAgainst'] }}</td>
                                <td class="px-4 py-4 text-center font-medium">
                                    <span class="@if($standing['goalDifference'] > 0) text-green-400 @elseif($standing['goalDifference'] < 0) text-red-400 @else text-light @endif">
                                        {{ $standing['goalDifference'] > 0 ? '+' : '' }}{{ $standing['goalDifference'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="font-bold text-primary text-lg">{{ $standing['points'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if(isset($standing['form']) && $standing['form'])
                                        <div class="flex justify-center space-x-1">
                                            @foreach(str_split($standing['form']) as $result)
                                                <span class="w-6 h-6 text-xs font-bold rounded-full flex items-center justify-center
                                                    @if($result === 'W') bg-green-600 text-white
                                                    @elseif($result === 'D') bg-yellow-600 text-white  
                                                    @elseif($result === 'L') bg-red-600 text-white
                                                    @else bg-gray-600 text-white
                                                    @endif">
                                                    {{ $result }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted text-xs">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center text-muted">
                                    <i class="fas fa-exclamation-triangle text-4xl mb-4 text-gray-600"></i>
                                    <div>No standings data available</div>
                                    <div class="text-sm">Please try again later</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Legend -->
                <div class="bg-gray-800 px-6 py-4 border-t border-gray-700">
                    <div class="flex flex-wrap items-center gap-6 text-xs text-muted">
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
                        <div class="ml-auto text-muted">
                            <span class="font-medium">P</span>=Played, <span class="font-medium">W</span>=Won, 
                            <span class="font-medium">D</span>=Draw, <span class="font-medium">L</span>=Lost, 
                            <span class="font-medium">GF</span>=Goals For, <span class="font-medium">GA</span>=Goals Against, 
                            <span class="font-medium">GD</span>=Goal Difference, <span class="font-medium">Pts</span>=Points
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Scorers Sidebar -->
        <div class="xl:col-span-1">
            <div class="bg-card rounded-lg overflow-hidden">
                <div class="bg-gray-800 px-4 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-bold text-light flex items-center">
                        <i class="fas fa-futbol mr-2"></i>Top Scorers
                    </h3>
                </div>
                
                <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
                    @forelse($topScorers as $index => $scorer)
                    <div class="flex items-center space-x-3 p-3 bg-gray-800 rounded-lg hover:bg-gray-700 transition-colors">
                        <div class="flex-shrink-0">
                            <span class="flex items-center justify-center w-8 h-8 text-sm font-bold
                                @if($index === 0) bg-yellow-600
                                @elseif($index === 1) bg-gray-400  
                                @elseif($index === 2) bg-yellow-700
                                @else bg-gray-600
                                @endif
                                text-white rounded-full">
                                {{ $index + 1 }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-light font-medium truncate">{{ $scorer['player']['name'] }}</div>
                            <div class="flex items-center space-x-2 text-xs text-muted">
                                <img src="{{ $scorer['team']['crest'] ?? '/images/default-logo.svg' }}" 
                                     alt="{{ $scorer['team']['name'] }}" 
                                     class="w-4 h-4 rounded-full">
                                <span class="truncate">{{ $scorer['team']['shortName'] ?? $scorer['team']['name'] }}</span>
                            </div>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <div class="text-lg font-bold text-primary">{{ $scorer['goals'] }}</div>
                            <div class="text-xs text-muted">goals</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-8">
                        <i class="fas fa-futbol text-3xl mb-2 text-gray-600"></i>
                        <div>No scorer data available</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh standings every 5 minutes
    setInterval(function() {
        window.location.reload();
    }, 300000);
</script>
@endpush
