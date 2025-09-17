@extends('layouts.app')

@section('title', $player->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Player Header -->
    <div class="bg-gray-800 rounded-lg p-8 mb-8">
        <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
            <!-- Player Avatar -->
            <div class="w-24 h-24 bg-gray-600 rounded-full flex items-center justify-center">
                <span class="text-4xl text-white">{{ substr($player->name, 0, 1) }}</span>
            </div>

            <!-- Player Info -->
            <div class="text-center md:text-left flex-1">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $player->name }}</h1>
                
                <!-- Position Badge -->
                <div class="inline-block bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium mb-3">
                    {{ $player->position }}
                </div>

                <!-- Team & League -->
                @if($player->team)
                    <div class="flex items-center justify-center md:justify-start space-x-2 mb-2">
                        @if($player->team->logo)
                            <img src="{{ $player->team->logo }}" alt="{{ $player->team->name }}" class="w-6 h-6">
                        @endif
                        <span class="text-lg text-gray-300">{{ $player->team->name }}</span>
                    </div>
                    @if($player->team->league)
                        <p class="text-gray-400">{{ $player->team->league->name }}</p>
                    @endif
                @endif

                <!-- Additional Info -->
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-center md:text-left">
                    @if($player->age)
                        <div>
                            <div class="text-sm text-gray-400">Age</div>
                            <div class="text-white font-semibold">{{ $player->age }}</div>
                        </div>
                    @endif
                    @if($player->nationality)
                        <div>
                            <div class="text-sm text-gray-400">Nationality</div>
                            <div class="text-white font-semibold">{{ $player->nationality }}</div>
                        </div>
                    @endif
                    @if($player->height)
                        <div>
                            <div class="text-sm text-gray-400">Height</div>
                            <div class="text-white font-semibold">{{ $player->height }} cm</div>
                        </div>
                    @endif
                    @if($player->weight)
                        <div>
                            <div class="text-sm text-gray-400">Weight</div>
                            <div class="text-white font-semibold">{{ $player->weight }} kg</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Season Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Stats Cards -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold text-white mb-6">Season Statistics</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Goals -->
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2">{{ $seasonStats['goals'] ?? 0 }}</div>
                    <div class="text-gray-400 text-sm">Goals</div>
                </div>

                <!-- Assists -->
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-2">{{ $seasonStats['assists'] ?? 0 }}</div>
                    <div class="text-gray-400 text-sm">Assists</div>
                </div>

                <!-- Matches -->
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2">{{ $seasonStats['matches_played'] ?? 0 }}</div>
                    <div class="text-gray-400 text-sm">Matches</div>
                </div>

                <!-- Minutes -->
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-orange-400 mb-2">{{ $seasonStats['minutes_played'] ?? 0 }}</div>
                    <div class="text-gray-400 text-sm">Minutes</div>
                </div>

                <!-- Yellow Cards -->
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-yellow-400 mb-2">{{ $seasonStats['yellow_cards'] ?? 0 }}</div>
                    <div class="text-gray-400 text-sm">Yellow Cards</div>
                </div>

                <!-- Red Cards -->
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-red-400 mb-2">{{ $seasonStats['red_cards'] ?? 0 }}</div>
                    <div class="text-gray-400 text-sm">Red Cards</div>
                </div>

                <!-- Average Rating -->
                @if($seasonStats['average_rating'])
                    <div class="bg-gray-800 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-indigo-400 mb-2">{{ number_format($seasonStats['average_rating'], 1) }}</div>
                        <div class="text-gray-400 text-sm">Avg Rating</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Info -->
        <div>
            <h2 class="text-2xl font-bold text-white mb-6">Player Info</h2>
            <div class="bg-gray-800 rounded-lg p-6">
                <div class="space-y-4">
                    @if($player->date_of_birth)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Date of Birth</span>
                            <span class="text-white">{{ \Carbon\Carbon::parse($player->date_of_birth)->format('M d, Y') }}</span>
                        </div>
                    @endif
                    @if($player->market_value)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Market Value</span>
                            <span class="text-white">€{{ number_format($player->market_value) }}</span>
                        </div>
                    @endif
                    @if($player->contract_until)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Contract Until</span>
                            <span class="text-white">{{ \Carbon\Carbon::parse($player->contract_until)->format('Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Matches -->
    @if($recentMatches && $recentMatches->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Recent Matches</h2>
            <div class="bg-gray-800 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Match</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Goals</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Assists</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Minutes</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Rating</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($recentMatches as $stat)
                                <tr class="hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ \Carbon\Carbon::parse($stat->match->match_date)->format('M d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        {{ $stat->match->homeTeam->short_name }} vs {{ $stat->match->awayTeam->short_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-green-400">
                                        {{ $stat->goals ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-blue-400">
                                        {{ $stat->assists ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-300">
                                        {{ $stat->minutes_played ?? 0 }}'
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-indigo-400">
                                        {{ $stat->rating ? number_format($stat->rating, 1) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Back Button -->
    <div class="text-center">
        <a href="{{ route('players.index') }}" 
           class="inline-block bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors">
            ← Back to Players
        </a>
    </div>
</div>
@endsection