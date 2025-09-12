@extends('layouts.app')

@section('title', 'Leagues - KickOff Stats')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-light mb-2">Football Leagues</h1>
        <p class="text-muted">Explore the top European football leagues</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($leagues as $league)
        <div class="bg-card rounded-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group">
            <div class="h-48 bg-gradient-to-br from-primary to-green-600 p-6 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-trophy text-white text-6xl mb-4 group-hover:scale-110 transition-transform duration-300"></i>
                    <h3 class="text-white text-xl font-bold">{{ $league->name }}</h3>
                </div>
            </div>
            
            <div class="p-6">
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-muted text-sm">Country:</span>
                        <span class="text-light font-medium">{{ $league->country }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-muted text-sm">Season:</span>
                        <span class="text-light font-medium">{{ $league->season }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted text-sm">Teams:</span>
                        <span class="text-light font-medium">{{ $league->teams->count() }}</span>
                    </div>
                </div>
                
                <a href="{{ route('leagues.show', $league->slug) }}" 
                   class="block w-full bg-primary hover:bg-green-600 text-white text-center py-3 rounded-lg font-medium transition-colors duration-300">
                    View League
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
