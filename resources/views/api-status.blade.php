@extends('layouts.app')

@section('title', 'API Status - KickOff Stats')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-light mb-2">API Status Dashboard</h1>
        <p class="text-muted">Real-time data connection status and statistics</p>
    </div>

    <!-- API Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-card rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-light">API Connection</h3>
                <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
            </div>
            <div class="text-2xl font-bold text-primary mb-2">✅ Active</div>
            <p class="text-sm text-muted">Football-Data.org API</p>
        </div>

        <div class="bg-card rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-light">Today's Matches</h3>
                <i class="fas fa-calendar-day text-primary"></i>
            </div>
            <div class="text-2xl font-bold text-light mb-2" id="today-matches">{{ $todayMatches ?? 0 }}</div>
            <p class="text-sm text-muted">Scheduled for today</p>
        </div>

        <div class="bg-card rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-light">Live Matches</h3>
                <i class="fas fa-broadcast-tower text-red-400"></i>
            </div>
            <div class="text-2xl font-bold text-red-400 mb-2" id="live-matches">{{ $liveMatches ?? 0 }}</div>
            <p class="text-sm text-muted">Currently in progress</p>
        </div>
    </div>

    <!-- League Status -->
    <div class="bg-card rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-light mb-4">League Data Status</h2>
        <div class="space-y-4">
            @foreach($leagues ?? [] as $league)
            <div class="flex items-center justify-between py-3 border-b border-gray-700 last:border-b-0">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                        <i class="fas fa-trophy text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-light font-medium">{{ $league->name }}</h3>
                        <p class="text-muted text-sm">{{ $league->country }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-light font-medium">{{ $league->teams->count() }} teams</div>
                    <div class="text-muted text-sm">Last updated: {{ $league->updated_at->diffForHumans() }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- API Commands -->
    <div class="bg-card rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-light mb-4">Manual Sync Commands</h2>
        <div class="space-y-3">
            <button onclick="syncData('today')" class="w-full bg-primary hover:bg-green-600 text-white py-3 px-4 rounded-lg text-left transition-colors duration-300">
                <i class="fas fa-calendar-day mr-2"></i>
                <span class="font-medium">Sync Today's Matches</span>
                <span class="block text-sm opacity-80">Update fixtures and results for today</span>
            </button>
            
            <button onclick="syncData('live')" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg text-left transition-colors duration-300">
                <i class="fas fa-broadcast-tower mr-2"></i>
                <span class="font-medium">Sync Live Matches</span>
                <span class="block text-sm opacity-80">Update scores for matches in progress</span>
            </button>
            
            <button onclick="syncData('full')" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg text-left transition-colors duration-300">
                <i class="fas fa-sync-alt mr-2"></i>
                <span class="font-medium">Full Data Sync</span>
                <span class="block text-sm opacity-80">Update teams, standings, and all matches</span>
            </button>
        </div>
    </div>

    <!-- Sync Log -->
    <div class="bg-card rounded-lg p-6">
        <h2 class="text-xl font-bold text-light mb-4">Recent Sync Activity</h2>
        <div id="sync-log" class="space-y-2 max-h-64 overflow-y-auto">
            <div class="text-muted text-sm py-2">
                <i class="fas fa-info-circle mr-2"></i>
                Manual sync commands will appear here
            </div>
        </div>
    </div>
</div>

<script>
async function syncData(type) {
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...';
    button.disabled = true;
    
    try {
        const response = await fetch(`/api/sync/${type}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        
        // Add to sync log
        addToSyncLog(type, result.success ? 'success' : 'error', result.message);
        
        // Update counters if needed
        if (result.success && result.data) {
            if (result.data.todayMatches !== undefined) {
                document.getElementById('today-matches').textContent = result.data.todayMatches;
            }
            if (result.data.liveMatches !== undefined) {
                document.getElementById('live-matches').textContent = result.data.liveMatches;
            }
        }
        
    } catch (error) {
        addToSyncLog(type, 'error', 'Network error occurred');
    } finally {
        // Restore button
        button.innerHTML = originalContent;
        button.disabled = false;
    }
}

function addToSyncLog(type, status, message) {
    const log = document.getElementById('sync-log');
    const timestamp = new Date().toLocaleTimeString();
    const statusColor = status === 'success' ? 'text-green-400' : 'text-red-400';
    const icon = status === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const entry = document.createElement('div');
    entry.className = `text-sm py-2 border-b border-gray-700 ${statusColor}`;
    entry.innerHTML = `
        <i class="fas ${icon} mr-2"></i>
        <span class="text-light">[${timestamp}]</span> 
        ${type.charAt(0).toUpperCase() + type.slice(1)} sync: ${message}
    `;
    
    log.insertBefore(entry, log.firstChild);
    
    // Keep only last 10 entries
    while (log.children.length > 10) {
        log.removeChild(log.lastChild);
    }
}

// Auto-update live match count every 30 seconds
setInterval(async () => {
    try {
        const response = await fetch('/api/live-scores');
        const liveMatches = await response.json();
        document.getElementById('live-matches').textContent = liveMatches.length;
    } catch (error) {
        console.log('Failed to update live match count');
    }
}, 30000);
</script>
@endsection
