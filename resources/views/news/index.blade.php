@extends('layouts.app')

@section('title', 'Football News - KickOff Stats')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-light mb-4">
            <i class="fas fa-newspaper text-primary mr-3"></i>
            Football News
        </h1>
        <p class="text-gray-400 text-lg">Stay updated with the latest football news from around the world</p>
    </div>

    <!-- Search Bar -->
    <div class="max-w-2xl mx-auto mb-8">
        <form action="{{ route('news.search') }}" method="GET" class="relative">
            <input 
                type="text" 
                name="q" 
                placeholder="Search football news..." 
                value="{{ request('q') }}"
                class="w-full px-4 py-3 pl-12 bg-card border border-gray-600 rounded-lg text-light placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            >
            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-primary hover:bg-primary-dark text-white px-4 py-1 rounded transition-colors duration-200">
                Search
            </button>
        </form>
    </div>

    <!-- Quick Links -->
    <div class="flex flex-wrap justify-center gap-4 mb-8">
        <a href="{{ route('news.league', 'premier-league') }}" class="px-4 py-2 bg-blue-600/20 text-blue-300 hover:bg-blue-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-crown mr-2"></i>Premier League
        </a>
        <a href="{{ route('news.league', 'la-liga') }}" class="px-4 py-2 bg-yellow-600/20 text-yellow-300 hover:bg-yellow-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-futbol mr-2"></i>La Liga
        </a>
        <a href="{{ route('news.league', 'serie-a') }}" class="px-4 py-2 bg-green-600/20 text-green-300 hover:bg-green-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-trophy mr-2"></i>Serie A
        </a>
        <a href="{{ route('news.league', 'champions-league') }}" class="px-4 py-2 bg-purple-600/20 text-purple-300 hover:bg-purple-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-star mr-2"></i>Champions League
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main News Section -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold text-light mb-6 flex items-center">
                <i class="fas fa-clock text-primary mr-2"></i>
                Latest News
            </h2>
            
            @if(!empty($latestNews))
                <div class="space-y-6">
                    @foreach($latestNews as $index => $article)
                        @if($index === 0)
                            <!-- Featured Article -->
                            <article class="bg-card rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300">
                                @if($article['image'])
                                    <div class="relative h-64 bg-gray-700">
                                        <img src="{{ $article['image'] }}" 
                                             alt="{{ $article['title'] }}" 
                                             class="w-full h-full object-cover"
                                             onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full bg-gray-700\'><i class=\'fas fa-image text-gray-500 text-4xl\'></i></div>'">
                                        <div class="absolute top-4 left-4">
                                            <span class="bg-primary text-white px-3 py-1 rounded-full text-sm font-medium">
                                                Featured
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-primary font-medium">{{ $article['source'] }}</span>
                                        @if($article['published_at'])
                                            <time class="text-gray-400 text-sm">
                                                {{ $article['published_at']->diffForHumans() }}
                                            </time>
                                        @endif
                                    </div>
                                    <h3 class="text-xl font-bold text-light mb-3 leading-tight">
                                        <a href="{{ $article['url'] }}" target="_blank" class="hover:text-primary transition-colors duration-200">
                                            {{ $article['title'] }}
                                        </a>
                                    </h3>
                                    @if($article['description'])
                                        <p class="text-gray-300 mb-4">{{ Str::limit($article['description'], 150) }}</p>
                                    @endif
                                    <a href="{{ $article['url'] }}" target="_blank" class="inline-flex items-center text-primary hover:text-primary-light transition-colors duration-200">
                                        Read more
                                        <i class="fas fa-external-link-alt ml-2 text-sm"></i>
                                    </a>
                                </div>
                            </article>
                        @else
                            <!-- Regular Articles -->
                            <article class="bg-card rounded-lg p-6 hover:bg-gray-750 transition-colors duration-200">
                                <div class="flex gap-4">
                                    @if($article['image'])
                                        <div class="flex-shrink-0 w-24 h-24 bg-gray-700 rounded-lg overflow-hidden">
                                            <img src="{{ $article['image'] }}" 
                                                 alt="{{ $article['title'] }}" 
                                                 class="w-full h-full object-cover"
                                                 onerror="this.style.display='none'">
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-primary text-sm font-medium">{{ $article['source'] }}</span>
                                            @if($article['published_at'])
                                                <time class="text-gray-400 text-xs">
                                                    {{ $article['published_at']->diffForHumans() }}
                                                </time>
                                            @endif
                                        </div>
                                        <h3 class="text-light font-semibold mb-2 leading-tight">
                                            <a href="{{ $article['url'] }}" target="_blank" class="hover:text-primary transition-colors duration-200">
                                                {{ Str::limit($article['title'], 80) }}
                                            </a>
                                        </h3>
                                        @if($article['description'])
                                            <p class="text-gray-400 text-sm">{{ Str::limit($article['description'], 100) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="bg-card rounded-lg p-8 text-center">
                    <i class="fas fa-newspaper text-gray-500 text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-light mb-2">No News Available</h3>
                    <p class="text-gray-400">Unable to load football news at the moment. Please try again later.</p>
                </div>
            @endif
        </div>

        <!-- Trending Sidebar -->
        <div class="lg:col-span-1">
            <h2 class="text-2xl font-bold text-light mb-6 flex items-center">
                <i class="fas fa-fire text-red-500 mr-2"></i>
                Trending
            </h2>
            
            @if(!empty($trendingNews))
                <div class="space-y-4">
                    @foreach($trendingNews as $article)
                        <article class="bg-card rounded-lg p-4 hover:bg-gray-750 transition-colors duration-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-primary text-sm font-medium">{{ $article['source'] }}</span>
                                @if($article['published_at'])
                                    <time class="text-gray-400 text-xs">
                                        {{ $article['published_at']->diffForHumans() }}
                                    </time>
                                @endif
                            </div>
                            <h3 class="text-light font-semibold mb-2 leading-tight">
                                <a href="{{ $article['url'] }}" target="_blank" class="hover:text-primary transition-colors duration-200">
                                    {{ Str::limit($article['title'], 70) }}
                                </a>
                            </h3>
                            @if($article['description'])
                                <p class="text-gray-400 text-sm">{{ Str::limit($article['description'], 80) }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="bg-card rounded-lg p-6 text-center">
                    <i class="fas fa-fire text-gray-500 text-3xl mb-3"></i>
                    <p class="text-gray-400">No trending news available</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
