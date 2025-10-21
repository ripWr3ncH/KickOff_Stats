@extends('layouts.app')

@section('title', 'My Dream Teams')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-light mb-2">
                <i class="fas fa-star text-primary mr-3"></i>My Dream Teams
            </h1>
            <p class="text-muted">Build and manage your perfect football lineups</p>
        </div>
        <a href="{{ route('dream-team.create') }}" class="bg-primary hover:bg-primary/80 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:scale-105 shadow-lg">
            <i class="fas fa-plus mr-2"></i>Create New Dream Team
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($dreamTeams->count() > 0)
        <!-- Dream Teams Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($dreamTeams as $dreamTeam)
                <div class="bg-card rounded-xl border border-gray-700 hover:border-primary/50 transition-all duration-300 hover:shadow-xl hover:shadow-primary/10 group">
                    <!-- Team Header -->
                    <div class="p-6 border-b border-gray-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-light group-hover:text-primary transition-colors duration-300">
                                    {{ $dreamTeam->name }}
                                </h3>
                                <div class="flex items-center gap-4 mt-2 text-sm text-muted">
                                    <span>
                                        <i class="fas fa-chess-board mr-1"></i>{{ $dreamTeam->formation }}
                                    </span>
                                    <span>
                                        <i class="fas fa-users mr-1"></i>{{ $dreamTeam->player_count }} players
                                    </span>
                                </div>
                            </div>
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

                    <!-- Team Info -->
                    <div class="p-6">
                        @if($dreamTeam->description)
                            <p class="text-muted mb-4 line-clamp-2">{{ $dreamTeam->description }}</p>
                        @endif

                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-muted">Total Value</span>
                            <span class="text-lg font-bold text-primary">
                                ${{ number_format($dreamTeam->total_value, 0) }}
                            </span>
                        </div>

                        <div class="text-xs text-muted mb-4">
                            Created {{ $dreamTeam->created_at->diffForHumans() }}
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="{{ route('dream-team.show', $dreamTeam) }}" class="flex-1 bg-primary/20 hover:bg-primary text-light hover:text-white px-4 py-2 rounded-lg text-center transition-all duration-300 text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('dream-team.edit', $dreamTeam) }}" class="flex-1 bg-blue-500/20 hover:bg-blue-500 text-blue-300 hover:text-white px-4 py-2 rounded-lg text-center transition-all duration-300 text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form action="{{ route('dream-team.destroy', $dreamTeam) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this dream team?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500/20 hover:bg-red-500 text-red-300 hover:text-white px-4 py-2 rounded-lg transition-all duration-300 text-sm font-medium">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="bg-card rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-star text-4xl text-primary/50"></i>
                </div>
                <h3 class="text-xl font-bold text-light mb-3">No Dream Teams Yet</h3>
                <p class="text-muted mb-6">Start building your perfect football lineup by creating your first dream team.</p>
                <a href="{{ route('dream-team.create') }}" class="bg-primary hover:bg-primary/80 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:scale-105 shadow-lg inline-block">
                    <i class="fas fa-plus mr-2"></i>Create Your First Dream Team
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
