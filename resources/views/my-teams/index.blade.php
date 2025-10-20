@extends('layouts.app')

@section('title', 'My Favorite Teams - KickOff Stats')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Favorite Teams</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Track matches from your favorite football teams</p>
        </div>
        <a href="{{ route('my-teams.select') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
            <i class="fas fa-plus mr-2"></i>Manage Teams
        </a>
    </div>

    @if($favoriteTeams->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mb-6">
                <i class="fas fa-heart text-6xl text-gray-300 dark:text-gray-600"></i>
            </div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">No Favorite Teams Yet</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                Add your favorite football teams to see their latest matches, scores, and updates all in one place.
            </p>
            <a href="{{ route('my-teams.select') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Add Your First Team
            </a>
        </div>
    @else
        <!-- Favorite Teams Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($favoriteTeams as $team)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        @if($team->logo)
                            <img src="{{ $team->logo }}" alt="{{ $team->name }}" class="w-12 h-12 mr-4">
                        @else
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-futbol text-blue-600 dark:text-blue-400"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $team->league->name ?? 'Unknown League' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('teams.show', $team->slug) }}" 
                           class="flex-1 bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-3 py-2 rounded text-center text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-800 transition duration-200">
                            View Details
                        </a>
                        <button onclick="removeFavorite({{ $team->id }})" 
                                class="bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 px-3 py-2 rounded text-sm font-medium hover:bg-red-100 dark:hover:bg-red-800 transition duration-200">
                            <i class="fas fa-heart-broken"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Recent Matches -->
        @if($matches->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent & Upcoming Matches</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Matches involving your favorite teams</p>
                </div>
                
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($matches as $match)
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Home Team -->
                                    <div class="flex items-center space-x-2 min-w-0 flex-1">
                                        @if($match->homeTeam->logo)
                                            <img src="{{ $match->homeTeam->logo }}" alt="{{ $match->homeTeam->name }}" class="w-8 h-8">
                                        @endif
                                        <span class="font-medium text-gray-900 dark:text-white truncate">{{ $match->homeTeam->name }}</span>
                                    </div>
                                    
                                    <!-- Score/Status -->
                                    <div class="text-center px-4">
                                        @if($match->status === 'finished')
                                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Final</div>
                                        @elseif($match->status === 'live')
                                            <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                                {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                            </div>
                                            <div class="text-xs text-green-600 dark:text-green-400 animate-pulse">● LIVE</div>
                                        @else
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($match->date)->setTimezone('Asia/Dhaka')->format('g:i A') }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($match->date)->setTimezone('Asia/Dhaka')->format('M j') }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Away Team -->
                                    <div class="flex items-center space-x-2 min-w-0 flex-1 flex-row-reverse">
                                        @if($match->awayTeam->logo)
                                            <img src="{{ $match->awayTeam->logo }}" alt="{{ $match->awayTeam->name }}" class="w-8 h-8">
                                        @endif
                                        <span class="font-medium text-gray-900 dark:text-white truncate">{{ $match->awayTeam->name }}</span>
                                    </div>
                                </div>
                                
                                <!-- League Badge -->
                                <div class="ml-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $match->league->name ?? 'Unknown' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>

<script>
function removeFavorite(teamId) {
    if (confirm('Are you sure you want to remove this team from your favorites?')) {
        fetch('{{ route("my-teams.remove-favorite") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ team_id: teamId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}
</script>
@endsection
