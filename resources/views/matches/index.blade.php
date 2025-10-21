@extends('layouts.app')

@section('title', 'Matches - KickOff Stats')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-light mb-2">
                <i class="fas fa-calendar-alt mr-3"></i>Matches
            </h1>
            <p class="text-muted">Find and track football matches across leagues</p>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-4 mt-4 lg:mt-0">
            <form method="GET" action="{{ route('matches.index') }}" class="flex flex-col sm:flex-row gap-4">
                <!-- League Filter -->
                <select name="league" class="bg-card text-light border border-gray-600 rounded-lg px-4 py-2 focus:border-primary focus:outline-none">
                    <option value="">All Leagues</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->slug }}" {{ request('league') === $league->slug ? 'selected' : '' }}>
                            {{ $league->name }}
                        </option>
                    @endforeach
                </select>
                
                <!-- Date Filter -->
                @php
                    $bangladeshToday = now('Asia/Dhaka');
                    $minDate = $earliestMatch ? $earliestMatch->getLocalMatchDate()->format('Y-m-d') : $bangladeshToday->copy()->subMonth()->format('Y-m-d');
                    $maxDate = $latestMatch ? $latestMatch->getLocalMatchDate()->format('Y-m-d') : $bangladeshToday->copy()->addMonth()->format('Y-m-d');
                @endphp
                <input type="date" 
                       name="date" 
                       value="{{ request('date', $bangladeshToday->format('Y-m-d')) }}"
                       min="{{ $minDate }}"
                       max="{{ $maxDate }}"
                       class="bg-card text-light border border-gray-600 rounded-lg px-4 py-2 focus:border-primary focus:outline-none">
                
                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </form>
            
            <!-- Quick Date Ranges -->
            <div class="flex gap-2">
                @php
                    $lastWeekDate = $bangladeshToday->copy()->subWeek();
                    $isLastWeekAvailable = $earliestMatch && $lastWeekDate->gte($earliestMatch->getLocalMatchDate()->startOfDay());
                @endphp
                
                @if($isLastWeekAvailable)
                    <a href="{{ route('matches.index', ['date' => $lastWeekDate->format('Y-m-d')]) }}" 
                       class="px-3 py-2 rounded-lg bg-gray-700 text-light hover:bg-gray-600 transition-colors text-sm">
                        Last Week
                    </a>
                @endif
                
                @if($earliestMatch)
                    <a href="{{ route('matches.index', ['date' => $earliestMatch->getLocalMatchDate()->format('Y-m-d')]) }}" 
                       class="px-3 py-2 rounded-lg bg-gray-700 text-light hover:bg-gray-600 transition-colors text-sm">
                        First Available ({{ $earliestMatch->getLocalMatchDate()->format('M j') }})
                    </a>
                @endif
            </div>
        </div>
    </div>
    
    <!-- No Matches Today - Show Upcoming -->
    @if($matches->count() == 0 && !request('date'))
        @php
            $upcomingMatches = \App\Models\FootballMatch::with(['homeTeam', 'awayTeam', 'league'])
                ->get()
                ->filter(function($match) {
                    return $match->getLocalMatchDate() > now('Asia/Dhaka');
                })
                ->sortBy('match_date')
                ->take(6);
        @endphp
        
        @if($upcomingMatches->count() > 0)
            <div class="bg-card rounded-lg p-6 mb-8">
                <h3 class="text-xl font-bold text-light mb-4">
                    <i class="fas fa-calendar-plus text-primary mr-2"></i>
                    No matches today - Here are the next upcoming matches:
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($upcomingMatches as $upcoming)
                        <a href="{{ route('matches.index', ['date' => $upcoming->getLocalMatchDate()->format('Y-m-d')]) }}" 
                           class="block p-4 bg-gray-800 rounded-lg hover:bg-gray-700 transition-colors">
                            <div class="text-sm text-primary font-medium mb-2">
                                {{ $upcoming->getLocalMatchDate()->format('M j, Y - g:i A') }}
                            </div>
                            <div class="text-light font-semibold">
                                {{ $upcoming->homeTeam->name }} <span class="text-muted">vs</span> {{ $upcoming->awayTeam->name }}
                            </div>
                            <div class="text-sm text-muted mt-1">
                                {{ $upcoming->league->name ?? 'Unknown League' }}
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="mt-4 text-center">
                    @if($upcomingMatches->first())
                        <a href="{{ route('matches.index', ['date' => $upcomingMatches->first()->getLocalMatchDate()->format('Y-m-d')]) }}" 
                           class="inline-block bg-primary text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fas fa-arrow-right mr-2"></i>
                            View Next Match Day ({{ $upcomingMatches->first()->getLocalMatchDate()->format('M j') }})
                        </a>
                    @endif
                </div>
            </div>
        @endif
    @endif

    <!-- Quick Navigation -->
    <div class="flex flex-wrap gap-2 mb-6">
        <!-- Date Navigation -->
        @php
            $currentDate = request('date') ? \Carbon\Carbon::parse(request('date')) : $bangladeshToday->toDateString();
            $currentDateObj = \Carbon\Carbon::parse($currentDate);
            
            $canGoWeekBack = $earliestMatch && $currentDateObj->copy()->subWeek()->gte($earliestMatch->getLocalMatchDate()->startOfDay());
            $canGoDayBack = $earliestMatch && $currentDateObj->copy()->subDay()->gte($earliestMatch->getLocalMatchDate()->startOfDay());
            $canGoDayForward = $latestMatch && $currentDateObj->copy()->addDay()->lte($latestMatch->getLocalMatchDate()->endOfDay());
            $canGoWeekForward = $latestMatch && $currentDateObj->copy()->addWeek()->lte($latestMatch->getLocalMatchDate()->endOfDay());
        @endphp
        

        
        <div class="flex items-center gap-2">
            @if($canGoWeekBack)
                <a href="{{ route('matches.index', ['date' => $currentDateObj->copy()->subWeek()->format('Y-m-d')]) }}" 
                   class="px-3 py-2 rounded-lg bg-card text-light hover:bg-gray-700 transition-colors">
                    <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i>
                </a>
            @else
                <span class="px-3 py-2 rounded-lg bg-gray-800 text-gray-600 cursor-not-allowed">
                    <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i>
                </span>
            @endif
            
            @if($canGoDayBack)
                <a href="{{ route('matches.index', ['date' => $currentDateObj->copy()->subDay()->format('Y-m-d')]) }}" 
                   class="px-3 py-2 rounded-lg bg-card text-light hover:bg-gray-700 transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @else
                <span class="px-3 py-2 rounded-lg bg-gray-800 text-gray-600 cursor-not-allowed">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @endif
        </div>
        
        <a href="{{ route('matches.index', ['date' => $bangladeshToday->copy()->subDays(2)->format('Y-m-d')]) }}" 
           class="px-4 py-2 rounded-lg {{ request('date') === $bangladeshToday->copy()->subDays(2)->format('Y-m-d') ? 'bg-primary text-white' : 'bg-card text-light hover:bg-gray-700' }} transition-colors">
            {{ $bangladeshToday->copy()->subDays(2)->format('M j') }}
        </a>
        <a href="{{ route('matches.index', ['date' => $bangladeshToday->copy()->subDay()->format('Y-m-d')]) }}" 
           class="px-4 py-2 rounded-lg {{ request('date') === $bangladeshToday->copy()->subDay()->format('Y-m-d') ? 'bg-primary text-white' : 'bg-card text-light hover:bg-gray-700' }} transition-colors">
            Yesterday
        </a>
        <a href="{{ route('matches.index', ['date' => $bangladeshToday->format('Y-m-d')]) }}" 
           class="px-4 py-2 rounded-lg {{ !request('date') || request('date') === $bangladeshToday->format('Y-m-d') ? 'bg-primary text-white' : 'bg-card text-light hover:bg-gray-700' }} transition-colors">
            Today
        </a>
        <a href="{{ route('matches.index', ['date' => $bangladeshToday->copy()->addDay()->format('Y-m-d')]) }}" 
           class="px-4 py-2 rounded-lg {{ request('date') === $bangladeshToday->copy()->addDay()->format('Y-m-d') ? 'bg-primary text-white' : 'bg-card text-light hover:bg-gray-700' }} transition-colors">
            Tomorrow
        </a>
        <a href="{{ route('matches.index', ['date' => $bangladeshToday->copy()->addDays(2)->format('Y-m-d')]) }}" 
           class="px-4 py-2 rounded-lg {{ request('date') === $bangladeshToday->copy()->addDays(2)->format('Y-m-d') ? 'bg-primary text-white' : 'bg-card text-light hover:bg-gray-700' }} transition-colors">
            {{ $bangladeshToday->copy()->addDays(2)->format('M j') }}
        </a>
        

        
        <div class="flex items-center gap-2">
            @if($canGoDayForward)
                <a href="{{ route('matches.index', ['date' => $currentDateObj->copy()->addDay()->format('Y-m-d')]) }}" 
                   class="px-3 py-2 rounded-lg bg-card text-light hover:bg-gray-700 transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="px-3 py-2 rounded-lg bg-gray-800 text-gray-600 cursor-not-allowed">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
            
            @if($canGoWeekForward)
                <a href="{{ route('matches.index', ['date' => $currentDateObj->copy()->addWeek()->format('Y-m-d')]) }}" 
                   class="px-3 py-2 rounded-lg bg-card text-light hover:bg-gray-700 transition-colors">
                    <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="px-3 py-2 rounded-lg bg-gray-800 text-gray-600 cursor-not-allowed">
                    <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
        
        <a href="{{ route('matches.live') }}" 
           class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors ml-4">
            <span class="live-pulse w-2 h-2 bg-white rounded-full inline-block mr-2"></span>
            Live
        </a>
    </div>
    
    <!-- Current Date Display -->
    @if(request('date'))
        <div class="mb-6 text-center">
            <h2 class="text-xl text-light font-semibold">
                Matches for {{ \Carbon\Carbon::parse(request('date'))->format('l, F j, Y') }}
            </h2>
        </div>
    @endif

    @if($matches->count() > 0)
        <!-- Matches by League -->
        @php
            $matchesByLeague = $matches->groupBy('league.name');
        @endphp
        
        @foreach($matchesByLeague as $leagueName => $leagueMatches)
            <div class="mb-8">
                <h2 class="text-xl font-bold text-light mb-4 flex items-center">
                    @if($leagueMatches->first()->league->logo_url)
                        <img src="{{ $leagueMatches->first()->league->logo_url }}" 
                             alt="{{ $leagueName }}" 
                             class="w-6 h-6 mr-3">
                    @else
                        <i class="fas fa-trophy text-primary mr-3"></i>
                    @endif
                    {{ $leagueName }}
                </h2>
                
                <div class="bg-card rounded-lg overflow-hidden">
                    @foreach($leagueMatches as $match)
                        <div class="border-b border-gray-700 last:border-b-0 p-4 hover:bg-gray-700 transition-colors">
                            <!-- Desktop Layout -->
                            <div class="hidden md:grid grid-cols-12 gap-4 items-center">
                                <!-- Time/Status Column -->
                                <div class="col-span-2 text-center">
                                    @if($match->status === 'live')
                                        <div class="text-xs bg-red-600 text-white px-2 py-1 rounded-full mb-1">
                                            LIVE
                                        </div>
                                        @if($match->minute)
                                            <div class="text-xs text-muted">{{ $match->minute }}'</div>
                                        @endif
                                    @elseif($match->status === 'finished')
                                        <div class="text-xs bg-gray-600 text-white px-2 py-1 rounded mb-1">FT</div>
                                        <div class="text-xs text-muted">{{ $match->getLocalMatchDate()->format('g:i A') }}</div>
                                    @elseif($match->status === 'scheduled')
                                        <div class="text-sm text-light font-medium">{{ $match->getLocalMatchDate()->format('g:i A') }}</div>
                                        <div class="text-xs text-muted">{{ $match->getLocalMatchDate()->format('M j') }}</div>
                                    @else
                                        <div class="text-xs text-yellow-500 px-2 py-1 rounded">{{ strtoupper($match->status) }}</div>
                                        <div class="text-xs text-muted">{{ $match->getLocalMatchDate()->format('g:i A') }}</div>
                                    @endif
                                </div>
                                
                                <!-- Home Team Column -->
                                <div class="col-span-3 flex items-center justify-end space-x-3">
                                    <span class="text-light font-medium text-right truncate">{{ $match->homeTeam->name }}</span>
                                    <img src="{{ $match->homeTeam->logo_url ?? '/images/default-logo.svg' }}" 
                                         alt="{{ $match->homeTeam->name }}" 
                                         class="w-8 h-8 rounded-full flex-shrink-0">
                                </div>
                                
                                <!-- Score/VS Column -->
                                <div class="col-span-2 text-center">
                                    @if($match->status === 'live' || $match->status === 'finished')
                                        <div class="text-2xl font-bold text-light">
                                            {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                        </div>
                                    @else
                                        <div class="text-lg font-semibold text-muted">VS</div>
                                    @endif
                                </div>
                                
                                <!-- Away Team Column -->
                                <div class="col-span-3 flex items-center justify-start space-x-3">
                                    <img src="{{ $match->awayTeam->logo_url ?? '/images/default-logo.svg' }}" 
                                         alt="{{ $match->awayTeam->name }}" 
                                         class="w-8 h-8 rounded-full flex-shrink-0">
                                    <span class="text-light font-medium text-left truncate">{{ $match->awayTeam->name }}</span>
                                </div>
                                
                                <!-- Actions Column -->
                                <div class="col-span-2 text-right">
                                    <a href="{{ route('matches.show', $match->id) }}" 
                                       class="text-primary hover:text-green-300 text-sm inline-flex items-center">
                                        <i class="fas fa-eye mr-1"></i>Details
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Mobile Layout -->
                            <div class="md:hidden space-y-3">
                                <!-- Match Status and Time -->
                                <div class="flex justify-between items-center">
                                    <div class="text-center">
                                        @if($match->status === 'live')
                                            <span class="text-xs bg-red-600 text-white px-2 py-1 rounded-full">
                                                LIVE {{ $match->minute ? $match->minute . "'" : '' }}
                                            </span>
                                        @elseif($match->status === 'finished')
                                            <span class="text-xs bg-gray-600 text-white px-2 py-1 rounded">FT</span>
                                        @elseif($match->status === 'scheduled')
                                            <span class="text-sm text-light font-medium">{{ $match->getLocalMatchDate()->format('g:i A') }}</span>
                                        @else
                                            <span class="text-xs text-yellow-500 px-2 py-1 rounded">{{ strtoupper($match->status) }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('matches.show', $match->id) }}" 
                                       class="text-primary hover:text-green-300 text-sm">
                                        <i class="fas fa-eye mr-1"></i>Details
                                    </a>
                                </div>
                                
                                <!-- Teams and Score -->
                                <div class="grid grid-cols-5 gap-3 items-center">
                                    <!-- Home Team -->
                                    <div class="col-span-2 flex items-center justify-end space-x-2">
                                        <span class="text-light font-medium text-right text-sm truncate">{{ $match->homeTeam->short_name ?? $match->homeTeam->name }}</span>
                                        <img src="{{ $match->homeTeam->logo_url ?? '/images/default-logo.svg' }}" 
                                             alt="{{ $match->homeTeam->name }}" 
                                             class="w-6 h-6 rounded-full flex-shrink-0">
                                    </div>
                                    
                                    <!-- Score/VS -->
                                    <div class="col-span-1 text-center">
                                        @if($match->status === 'live' || $match->status === 'finished')
                                            <div class="text-lg font-bold text-light">
                                                {{ $match->home_score ?? 0 }}-{{ $match->away_score ?? 0 }}
                                            </div>
                                        @else
                                            <div class="text-sm font-semibold text-muted">VS</div>
                                        @endif
                                    </div>
                                    
                                    <!-- Away Team -->
                                    <div class="col-span-2 flex items-center justify-start space-x-2">
                                        <img src="{{ $match->awayTeam->logo_url ?? '/images/default-logo.svg' }}" 
                                             alt="{{ $match->awayTeam->name }}" 
                                             class="w-6 h-6 rounded-full flex-shrink-0">
                                        <span class="text-light font-medium text-left text-sm truncate">{{ $match->awayTeam->short_name ?? $match->awayTeam->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mb-4">
                <i class="fas fa-calendar-times text-6xl text-gray-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-light mb-2">No Matches Found</h3>
            <p class="text-muted mb-6">
                @if(request('date'))
                    @php
                        $requestedDate = \Carbon\Carbon::parse(request('date'));
                    @endphp
                    No matches scheduled for {{ $requestedDate->format('F j, Y') }}.
                    @if($earliestMatch && $latestMatch)
                        <br><small class="text-xs">Available matches from {{ $earliestMatch->getLocalMatchDate()->format('M j, Y') }} to {{ $latestMatch->getLocalMatchDate()->format('M j, Y') }}</small>
                    @endif
                @else
                    No matches found with the current filters.
                @endif
            </p>
            <a href="{{ route('matches.index') }}" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors">
                <i class="fas fa-calendar-alt mr-2"></i>View All Matches
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh live match scores
    setInterval(function() {
        const liveMatches = document.querySelectorAll('[data-status="live"]');
        if (liveMatches.length > 0) {
            // In a real app, you'd fetch updates here
            console.log('Refreshing live match scores...');
        }
    }, 30000);
</script>
@endpush
