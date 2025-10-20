@extends('layouts.app')

@section('title', 'Players')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">Players</h1>
        
        <!-- Sync Button -->
        <form method="GET" class="inline">
            @foreach(request()->query() as $key => $value)
                @if($key !== 'sync_players')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <button type="submit" name="sync_players" value="1" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                🔄 Sync Players
            </button>
        </form>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- League Filter -->
            <div>
                <label for="league" class="block text-sm font-medium text-gray-300 mb-2">League</label>
                <select name="league" id="league" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg p-2">
                    <option value="">All Leagues</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->slug }}" {{ request('league') == $league->slug ? 'selected' : '' }}>
                            {{ $league->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Position Filter -->
            <div>
                <label for="position" class="block text-sm font-medium text-gray-300 mb-2">Position</label>
                <select name="position" id="position" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg p-2">
                    <option value="">All Positions</option>
                    @foreach($positions as $position)
                        <option value="{{ $position }}" {{ request('position') == $position ? 'selected' : '' }}>
                            {{ $position }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                <input type="text" name="search" id="search" 
                       value="{{ request('search') }}"
                       placeholder="Player name..."
                       class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg p-2">
            </div>

            <!-- Submit -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-2">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Players Grid -->
    @if($players->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($players as $player)
                <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-700 transition-colors">
                    <div class="text-center">
                        <!-- Player Avatar -->
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-600 rounded-full flex items-center justify-center">
                            <span class="text-2xl text-white">{{ substr($player->name, 0, 1) }}</span>
                        </div>

                        <!-- Player Name -->
                        <h3 class="text-lg font-semibold text-white mb-2">{{ $player->name }}</h3>

                        <!-- Position -->
                        <div class="inline-block bg-blue-600 text-white text-xs px-2 py-1 rounded-full mb-2">
                            {{ $player->position }}
                        </div>

                        <!-- Team & League -->
                        @if($player->team)
                            <p class="text-gray-300 text-sm mb-1">{{ $player->team->name }}</p>
                            @if($player->team->league)
                                <p class="text-gray-400 text-xs">{{ $player->team->league->name }}</p>
                            @endif
                        @endif

                        <!-- Player Stats -->
                        <div class="mt-4 grid grid-cols-2 gap-2 text-center">
                            <div>
                                <div class="text-2xl font-bold text-green-400">{{ $player->stats()->sum('goals') }}</div>
                                <div class="text-xs text-gray-400">Goals</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-400">{{ $player->stats()->sum('assists') }}</div>
                                <div class="text-xs text-gray-400">Assists</div>
                            </div>
                        </div>

                        <!-- View Details -->
                        @if($player->slug)
                            <a href="{{ route('players.show', $player->slug) }}" 
                               class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">
                                View Details
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $players->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-6xl mb-4">⚽</div>
            <h3 class="text-xl font-semibold text-white mb-2">No Players Available</h3>
            <p class="text-gray-400 mb-4">Players are automatically synced from live matches and team data.</p>
            <p class="text-gray-400 mb-6">Start the auto-updater or sync recent matches to populate player data.</p>
            <div class="space-x-4">
                <form method="GET" class="inline">
                    @foreach(request()->query() as $key => $value)
                        @if($key !== 'sync_players')
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <button type="submit" name="sync_players" value="1" 
                            class="bg-primary hover:bg-primary/80 text-white px-6 py-3 rounded-lg font-medium">
                        🔄 Sync Players from API
                    </button>
                </form>
                <a href="{{ route('matches.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium inline-block">
                    📺 View Live Matches
                </a>
            </div>
        </div>
        </div>
    @endif
</div>
@endsection
