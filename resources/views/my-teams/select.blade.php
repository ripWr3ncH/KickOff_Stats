@extends('layouts.app')

@section('title', 'Select Favorite Teams - KickOff Stats')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Select Your Favorite Teams</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Choose teams to follow their matches and updates</p>
        </div>
        <a href="{{ route('my-teams.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Back to My Teams
        </a>
    </div>

    <!-- Search Box -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" 
                   id="teamSearch" 
                   placeholder="Search for teams..." 
                   class="w-full px-4 py-3 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Teams by League -->
    @foreach($leagues as $league)
        @if($league->teams->isNotEmpty())
            <div class="mb-8 league-section" data-league="{{ strtolower($league->name) }}">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    @if($league->logo)
                        <img src="{{ $league->logo }}" alt="{{ $league->name }}" class="w-8 h-8 mr-3">
                    @endif
                    {{ $league->name }}
                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ $league->teams->count() }} teams)</span>
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($league->teams as $team)
                        <div class="team-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition duration-200" 
                             data-team-name="{{ strtolower($team->name) }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 min-w-0 flex-1">
                                    @if($team->logo_url)
                                        <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" class="w-10 h-10 flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-futbol text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-medium text-gray-900 dark:text-white truncate">{{ $team->name }}</h3>
                                        @if($team->city)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $team->city }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <button onclick="toggleFavorite({{ $team->id }}, this)" 
                                        class="favorite-btn ml-3 p-2 rounded-full transition duration-200 {{ $team->favorited_by_users_count > 0 ? 'text-red-600 hover:bg-red-50 dark:hover:bg-red-900' : 'text-gray-400 hover:text-red-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                                        data-team-id="{{ $team->id }}"
                                        data-favorited="{{ $team->favorited_by_users_count > 0 ? 'true' : 'false' }}">
                                    <i class="fas fa-heart text-xl {{ $team->favorited_by_users_count > 0 ? 'text-red-600' : 'text-gray-400' }}"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if($leagues->isEmpty() || $leagues->every(function($league) { return $league->teams->isEmpty(); }))
        <div class="text-center py-16">
            <div class="mb-6">
                <i class="fas fa-users text-6xl text-gray-300 dark:text-gray-600"></i>
            </div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">No Teams Available</h2>
            <p class="text-gray-600 dark:text-gray-400">Teams will be available once leagues and teams are added to the system.</p>
        </div>
    @endif
</div>

<!-- Success/Error Messages -->
<div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

<script>
// Search functionality
document.getElementById('teamSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const teamCards = document.querySelectorAll('.team-card');
    const leagueSections = document.querySelectorAll('.league-section');
    
    teamCards.forEach(card => {
        const teamName = card.dataset.teamName;
        const isVisible = teamName.includes(searchTerm);
        card.style.display = isVisible ? 'block' : 'none';
    });
    
    // Hide/show league sections based on visible teams
    leagueSections.forEach(section => {
        const visibleTeams = section.querySelectorAll('.team-card[style*="block"], .team-card:not([style*="none"])');
        section.style.display = visibleTeams.length > 0 ? 'block' : 'none';
    });
});

// Toggle favorite functionality
function toggleFavorite(teamId, button) {
    const isFavorited = button.dataset.favorited === 'true';
    const url = isFavorited ? '{{ route("my-teams.remove-favorite") }}' : '{{ route("my-teams.add-favorite") }}';
    
    fetch(url, {
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
            // Update button state
            const heart = button.querySelector('i');
            if (isFavorited) {
                button.dataset.favorited = 'false';
                button.className = 'favorite-btn ml-3 p-2 rounded-full transition duration-200 text-gray-400 hover:text-red-600 hover:bg-gray-50 dark:hover:bg-gray-700';
                heart.className = 'fas fa-heart text-xl text-gray-400';
            } else {
                button.dataset.favorited = 'true';
                button.className = 'favorite-btn ml-3 p-2 rounded-full transition duration-200 text-red-600 hover:bg-red-50 dark:hover:bg-red-900';
                heart.className = 'fas fa-heart text-xl text-red-600';
            }
            
            // Show success message
            showMessage(data.message, 'success');
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    });
}

// Show message function
function showMessage(message, type) {
    const container = document.getElementById('messageContainer');
    const messageDiv = document.createElement('div');
    
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    messageDiv.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg mb-4 transform transition-transform duration-300 translate-x-full`;
    messageDiv.textContent = message;
    
    container.appendChild(messageDiv);
    
    // Animate in
    setTimeout(() => {
        messageDiv.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        messageDiv.classList.add('translate-x-full');
        setTimeout(() => {
            container.removeChild(messageDiv);
        }, 300);
    }, 3000);
}
</script>
@endsection
