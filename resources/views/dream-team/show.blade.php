@extends('layouts.app')

@section('title', $dreamTeam->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('dream-team.index') }}" class="text-muted hover:text-primary transition-colors duration-300">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-light">
                <i class="fas fa-star text-primary mr-3"></i>{{ $dreamTeam->name }}
            </h1>
            <div class="flex items-center gap-6 mt-2 text-muted">
                <span><i class="fas fa-chess-board mr-1"></i>{{ $dreamTeam->formation }}</span>
                <span><i class="fas fa-users mr-1"></i>{{ count($dreamTeam->players) }} players</span>
                <span><i class="fas fa-dollar-sign mr-1"></i>${{ number_format($dreamTeam->total_value, 0) }}</span>
                @if($dreamTeam->is_public)
                    <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded-full text-xs">
                        <i class="fas fa-globe mr-1"></i>Public
                    </span>
                @else
                    <span class="bg-gray-500/20 text-gray-300 px-2 py-1 rounded-full text-xs">
                        <i class="fas fa-lock mr-1"></i>Private
                    </span>
                @endif
            </div>
        </div>
        @if($dreamTeam->user_id === Auth::id())
            <div class="flex gap-2">
                <a href="{{ route('dream-team.edit', $dreamTeam) }}" class="bg-blue-500/20 hover:bg-blue-500 text-blue-300 hover:text-white px-4 py-2 rounded-lg transition-all duration-300">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('dream-team.destroy', $dreamTeam) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this dream team?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500/20 hover:bg-red-500 text-red-300 hover:text-white px-4 py-2 rounded-lg transition-all duration-300">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formation Display -->
        <div class="lg:col-span-2">
            <div class="bg-card rounded-xl border border-gray-700 p-6">
                <h3 class="text-xl font-bold text-light mb-6">Formation</h3>
                
                <!-- Football Field -->
                <div class="formation-field relative bg-gradient-to-b from-green-800/30 to-green-900/30 rounded-lg border-2 border-white/20 aspect-[2/3] overflow-hidden">
                    <!-- Field Lines -->
                    <div class="absolute inset-0">
                        <!-- Center line -->
                        <div class="absolute left-0 right-0 top-1/2 h-0.5 bg-white/30"></div>
                        <!-- Center circle -->
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-20 h-20 border border-white/30 rounded-full"></div>
                        <!-- Penalty boxes -->
                        <div class="absolute top-0 left-1/4 right-1/4 h-16 border-l border-r border-b border-white/30"></div>
                        <div class="absolute bottom-0 left-1/4 right-1/4 h-16 border-l border-r border-t border-white/30"></div>
                        <!-- Goal areas -->
                        <div class="absolute top-0 left-2/5 right-2/5 h-8 border-l border-r border-b border-white/30"></div>
                        <div class="absolute bottom-0 left-2/5 right-2/5 h-8 border-l border-r border-t border-white/30"></div>
                    </div>

                    <!-- Players -->
                    <div class="absolute inset-0">
                        @foreach($playersWithDetails as $playerSlot)
                            <div class="absolute player-display" style="left: {{ $playerSlot['x'] }}%; top: {{ $playerSlot['y'] }}%; transform: translate(-50%, -50%);">
                                <div class="bg-primary rounded-full w-12 h-12 flex items-center justify-center border-2 border-white shadow-lg hover:scale-110 transition-transform duration-300 cursor-pointer group">
                                    @if($playerSlot['player'])
                                        <span class="text-white text-xs font-bold">{{ substr($playerSlot['player']->name, 0, 1) }}{{ substr(explode(' ', $playerSlot['player']->name)[1] ?? '', 0, 1) }}</span>
                                        <!-- Player tooltip -->
                                        <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-dark text-white p-2 rounded-lg text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-10">
                                            <div class="font-medium">{{ $playerSlot['player']->name }}</div>
                                            <div class="text-muted">{{ $playerSlot['player']->team->name ?? 'No Team' }}</div>
                                            <div class="text-primary">${{ number_format($playerSlot['player']->market_value ?? 0, 1) }}M</div>
                                        </div>
                                    @else
                                        <span class="text-white text-xs">{{ $playerSlot['position'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Details -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Description -->
            @if($dreamTeam->description)
                <div class="bg-card rounded-xl border border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-light mb-3">Description</h3>
                    <p class="text-muted">{{ $dreamTeam->description }}</p>
                </div>
            @endif

            <!-- Team Statistics -->
            <div class="bg-card rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-bold text-light mb-4">Team Statistics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-muted">Total Value</span>
                        <span class="text-primary font-bold">${{ number_format($dreamTeam->total_value, 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted">Formation</span>
                        <span class="text-light">{{ $dreamTeam->formation }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted">Players</span>
                        <span class="text-light">{{ count($dreamTeam->players) }}/11</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted">Created</span>
                        <span class="text-light">{{ $dreamTeam->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Player List -->
            <div class="bg-card rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-bold text-light mb-4">Players</h3>
                <div class="space-y-3">
                    @foreach(['GK' => 'Goalkeepers', 'DEF' => 'Defenders', 'MID' => 'Midfielders', 'FWD' => 'Forwards'] as $position => $positionName)
                        @php
                            $positionPlayers = $playersWithDetails->where('position', $position)->where('player', '!=', null);
                        @endphp
                        @if($positionPlayers->count() > 0)
                            <div>
                                <h4 class="text-primary text-sm font-medium mb-2">{{ $positionName }}</h4>
                                @foreach($positionPlayers as $playerSlot)
                                    <div class="flex items-center gap-3 py-2 px-3 bg-dark/50 rounded-lg mb-2">
                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">{{ substr($playerSlot['player']->name, 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-light text-sm font-medium">{{ $playerSlot['player']->name }}</div>
                                            <div class="text-muted text-xs">{{ $playerSlot['player']->team->name ?? 'No Team' }}</div>
                                        </div>
                                        <div class="text-primary text-sm font-medium">
                                            ${{ number_format($playerSlot['player']->market_value ?? 0, 1) }}M
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
