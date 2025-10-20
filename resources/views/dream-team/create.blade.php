@extends('layouts.app')

@section('title', 'Create Dream Team')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('dream-team.index') }}" class="text-muted hover:text-primary transition-colors duration-300">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-light">
                <i class="fas fa-star text-primary mr-3"></i>Create Dream Team
            </h1>
            <p class="text-muted">Build your perfect football lineup</p>
        </div>
    </div>

    <form action="{{ route('dream-team.store') }}" method="POST" id="dream-team-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Team Settings -->
            <div class="lg:col-span-1">
                <div class="bg-card rounded-xl border border-gray-700 p-6 sticky top-8">
                    <h3 class="text-xl font-bold text-light mb-6">Team Settings</h3>
                    
                    <!-- Team Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-light font-medium mb-2">Team Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', 'My Dream Team') }}" 
                               class="w-full bg-dark border border-gray-700 rounded-lg px-4 py-3 text-light focus:border-primary focus:outline-none transition-colors duration-300"
                               required>
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Formation -->
                    <div class="mb-6">
                        <label for="formation" class="block text-light font-medium mb-2">Formation</label>
                        <select id="formation" name="formation" 
                                class="w-full bg-dark border border-gray-700 rounded-lg px-4 py-3 text-light focus:border-primary focus:outline-none transition-colors duration-300"
                                required>
                            @foreach($formations as $formation)
                                <option value="{{ $formation }}" {{ old('formation', '4-3-3') == $formation ? 'selected' : '' }}>
                                    {{ $formation }}
                                </option>
                            @endforeach
                        </select>
                        @error('formation')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-light font-medium mb-2">Description (Optional)</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full bg-dark border border-gray-700 rounded-lg px-4 py-3 text-light focus:border-primary focus:outline-none transition-colors duration-300 resize-none"
                                  placeholder="Describe your dream team strategy...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Public/Private -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="toggle-checkbox bg-gray-600 w-12 h-6 rounded-full relative cursor-pointer transition-colors duration-300">
                                <div class="toggle-switch bg-white w-5 h-5 rounded-full absolute top-0.5 left-0.5 transition-transform duration-300"></div>
                            </div>
                            <span class="ml-3 text-light">Make team public</span>
                        </label>
                        <p class="text-muted text-sm mt-1">Public teams can be viewed by other users</p>
                    </div>

                    <!-- Save Button -->
                    <button type="submit" class="w-full bg-primary hover:bg-primary/80 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:scale-105 shadow-lg">
                        <i class="fas fa-save mr-2"></i>Create Dream Team
                    </button>
                </div>
            </div>

            <!-- Right Column: Formation Field -->
            <div class="lg:col-span-2">
                <div class="bg-card rounded-xl border border-gray-700 p-6">
                    <h3 class="text-xl font-bold text-light mb-6">Formation Builder</h3>
                    
                    <!-- Formation Field -->
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

                        <!-- Player Positions -->
                        <div id="player-positions" class="absolute inset-0">
                            <!-- Positions will be generated by JavaScript -->
                        </div>
                    </div>

                    <!-- Player Search -->
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-light mb-4">Search Players</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <input type="text" id="player-search" placeholder="Search by name..."
                                   class="bg-dark border border-gray-700 rounded-lg px-4 py-2 text-light focus:border-primary focus:outline-none transition-colors duration-300">
                            <select id="position-filter"
                                    class="bg-dark border border-gray-700 rounded-lg px-4 py-2 text-light focus:border-primary focus:outline-none transition-colors duration-300">
                                <option value="">All Positions</option>
                                <option value="GK">Goalkeeper</option>
                                <option value="DEF">Defender</option>
                                <option value="MID">Midfielder</option>
                                <option value="FWD">Forward</option>
                            </select>
                            <select id="league-filter"
                                    class="bg-dark border border-gray-700 rounded-lg px-4 py-2 text-light focus:border-primary focus:outline-none transition-colors duration-300">
                                <option value="">All Leagues</option>
                                @foreach($leagues as $league)
                                    <option value="{{ $league->id }}">{{ $league->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Search Results -->
                        <div id="player-results" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-60 overflow-y-auto">
                            <!-- Search results will be loaded here -->
                            <div class="col-span-full text-center py-8 text-muted">
                                <i class="fas fa-search text-2xl mb-2"></i>
                                <p>Search for players above to start building your dream team</p>
                                <p class="text-sm mt-1">Players are automatically synced from live matches</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden players input -->
        <input type="hidden" name="players" id="players-input" value="[]">
    </form>
</div>

<style>
.toggle-checkbox:checked {
    background-color: #00D26A;
}

.toggle-checkbox:checked .toggle-switch {
    transform: translateX(1.5rem);
}

.player-slot {
    width: 50px;
    height: 50px;
    border: 2px dashed #6B7280;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.player-slot:hover {
    border-color: #00D26A;
    background-color: rgba(0, 210, 106, 0.1);
}

.player-slot.filled {
    border: 2px solid #00D26A;
    background-color: rgba(0, 210, 106, 0.2);
}

.player-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.player-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 210, 106, 0.3);
}
</style>

<script>
let selectedPlayers = [];
let formations = {
    '4-3-3': {
        'GK': [{x: 50, y: 90}],
        'DEF': [{x: 20, y: 70}, {x: 40, y: 70}, {x: 60, y: 70}, {x: 80, y: 70}],
        'MID': [{x: 30, y: 45}, {x: 50, y: 45}, {x: 70, y: 45}],
        'FWD': [{x: 25, y: 20}, {x: 50, y: 20}, {x: 75, y: 20}]
    },
    '4-4-2': {
        'GK': [{x: 50, y: 90}],
        'DEF': [{x: 20, y: 70}, {x: 40, y: 70}, {x: 60, y: 70}, {x: 80, y: 70}],
        'MID': [{x: 20, y: 45}, {x: 40, y: 45}, {x: 60, y: 45}, {x: 80, y: 45}],
        'FWD': [{x: 35, y: 20}, {x: 65, y: 20}]
    },
    '3-5-2': {
        'GK': [{x: 50, y: 90}],
        'DEF': [{x: 30, y: 70}, {x: 50, y: 70}, {x: 70, y: 70}],
        'MID': [{x: 15, y: 45}, {x: 35, y: 45}, {x: 50, y: 45}, {x: 65, y: 45}, {x: 85, y: 45}],
        'FWD': [{x: 35, y: 20}, {x: 65, y: 20}]
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const formationSelect = document.getElementById('formation');
    const playerSearch = document.getElementById('player-search');
    const positionFilter = document.getElementById('position-filter');
    const leagueFilter = document.getElementById('league-filter');
    const playerResults = document.getElementById('player-results');
    
    // Initialize formation
    updateFormation();
    
    // Formation change handler
    formationSelect.addEventListener('change', updateFormation);
    
    // Search handlers
    let searchTimeout;
    [playerSearch, positionFilter, leagueFilter].forEach(element => {
        element.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchPlayers, 300);
        });
    });
    
    // Initial search
    searchPlayers();
});

function updateFormation() {
    const formation = document.getElementById('formation').value;
    const positions = formations[formation] || formations['4-3-3'];
    const container = document.getElementById('player-positions');
    
    container.innerHTML = '';
    selectedPlayers = [];
    
    Object.entries(positions).forEach(([position, coords]) => {
        coords.forEach((coord, index) => {
            const slot = document.createElement('div');
            slot.className = 'player-slot absolute';
            slot.style.left = coord.x + '%';
            slot.style.top = coord.y + '%';
            slot.style.transform = 'translate(-50%, -50%)';
            slot.dataset.position = position;
            slot.dataset.index = index;
            
            slot.innerHTML = `<span class="text-xs text-gray-400">${position}</span>`;
            
            slot.addEventListener('click', () => openPlayerSelection(slot));
            
            container.appendChild(slot);
        });
    });
    
    updatePlayersInput();
}

function searchPlayers() {
    const search = document.getElementById('player-search').value;
    const position = document.getElementById('position-filter').value;
    const league = document.getElementById('league-filter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (position) params.append('position', position);
    if (league) params.append('league', league);
    
    fetch(`{{ route('dream-team.search-players') }}?${params}`)
        .then(response => response.json())
        .then(players => {
            const container = document.getElementById('player-results');
            container.innerHTML = '';
            
            players.forEach(player => {
                const card = document.createElement('div');
                card.className = 'player-card bg-dark border border-gray-700 rounded-lg p-3 hover:border-primary transition-all duration-300';
                card.innerHTML = `
                    <div class="text-sm font-medium text-light">${player.name}</div>
                    <div class="text-xs text-muted">${player.team}</div>
                    <div class="text-xs text-primary">${player.position}</div>
                `;
                
                card.addEventListener('click', () => selectPlayer(player));
                container.appendChild(card);
            });
        })
        .catch(error => console.error('Error searching players:', error));
}

let currentSlot = null;

function openPlayerSelection(slot) {
    currentSlot = slot;
    // Highlight the slot
    document.querySelectorAll('.player-slot').forEach(s => s.classList.remove('ring-2', 'ring-primary'));
    slot.classList.add('ring-2', 'ring-primary');
}

function selectPlayer(player) {
    if (!currentSlot) return;
    
    const position = currentSlot.dataset.position;
    const index = parseInt(currentSlot.dataset.index);
    
    // Remove player from previous position if exists
    selectedPlayers = selectedPlayers.filter(p => p.player_id !== player.id);
    
    // Add player to new position
    selectedPlayers.push({
        position: position,
        player_id: player.id,
        x: parseInt(currentSlot.style.left),
        y: parseInt(currentSlot.style.top)
    });
    
    // Update slot display
    currentSlot.classList.add('filled');
    currentSlot.innerHTML = `
        <div class="text-center">
            <div class="text-xs font-medium text-white truncate" style="max-width: 40px;">${player.name.split(' ').pop()}</div>
        </div>
    `;
    
    // Clear selection
    currentSlot.classList.remove('ring-2', 'ring-primary');
    currentSlot = null;
    
    updatePlayersInput();
}

function updatePlayersInput() {
    document.getElementById('players-input').value = JSON.stringify(selectedPlayers);
}
</script>
@endsection
