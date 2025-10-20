@extends('layouts.app')

@section('title', 'Teams')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-light">Teams</h1>
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-lg p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- League Filter -->
            <div>
                <label for="league" class="block text-sm font-medium text-light mb-2">League</label>
                <select name="league" id="league" 
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">All Leagues</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->slug }}" 
                                {{ request('league') == $league->slug ? 'selected' : '' }}>
                            {{ $league->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Submit Button -->
            <div class="flex items-end">
                <button type="submit" 
                        class="bg-primary hover:bg-green-600 text-white px-6 py-2 rounded-lg transition duration-200">
                    Filter Teams
                </button>
            </div>
        </form>
    </div>

    @if($teams->count() > 0)
        <!-- Teams Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($teams as $team)
                <div class="bg-card rounded-lg overflow-hidden hover:shadow-lg transition duration-200 transform hover:scale-105">
                    <a href="{{ route('teams.show', $team->slug) }}" class="block">
                        <!-- Team Logo -->
                        <div class="h-32 bg-gray-800 flex items-center justify-center p-4">
                            @if($team->logo_url)
                                <img src="{{ $team->logo_url }}" 
                                     alt="{{ $team->name }}" 
                                     class="max-h-full max-w-full object-contain">
                            @else
                                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Team Info -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-light mb-2">{{ $team->name }}</h3>
                            
                            @if($team->league)
                                <div class="flex items-center text-sm text-muted mb-2">
                                    <i class="fas fa-trophy mr-2"></i>
                                    {{ $team->league->name }}
                                </div>
                            @endif
                            
                            @if($team->founded_year)
                                <div class="flex items-center text-sm text-muted mb-2">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Founded {{ $team->founded_year }}
                                </div>
                            @endif
                            
                            @if($team->venue)
                                <div class="flex items-center text-sm text-muted">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    {{ $team->venue }}
                                </div>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-6xl mb-4">⚽</div>
            <h3 class="text-xl font-semibold text-light mb-2">No Teams Found</h3>
            <p class="text-muted mb-4">
                @if(request('league'))
                    No teams found for the selected league. Try selecting a different league.
                @else
                    No teams available. Teams will appear here once league data is synced.
                @endif
            </p>
            <a href="{{ route('teams.index') }}" 
               class="bg-primary hover:bg-green-600 text-white px-4 py-2 rounded-lg inline-block">
                View All Teams
            </a>
        </div>
    @endif
</div>
@endsection
