@extends('layouts.app')

@section('title', 'Search News - KickOff Stats')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-light mb-4">
            <i class="fas fa-search text-primary mr-3"></i>
            Search Results
        </h1>
        @if($query)
            <p class="text-gray-400">
                Showing results for: <span class="text-primary font-semibold">"{{ $query }}"</span>
            </p>
        @endif
    </div>

    <!-- Search Bar -->
    <div class="max-w-2xl mb-8">
        <form action="{{ route('news.search') }}" method="GET" class="relative">
            <input 
                type="text" 
                name="q" 
                placeholder="Search football news..." 
                value="{{ $query }}"
                class="w-full px-4 py-3 pl-12 bg-card border border-gray-600 rounded-lg text-light placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            >
            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-primary hover:bg-primary-dark text-white px-4 py-1 rounded transition-colors duration-200">
                Search
            </button>
        </form>
    </div>

    <!-- Results -->
    @if(!empty($news) && count($news) > 0)
        <div class="mb-4">
            <p class="text-gray-400">Found {{ count($news) }} article(s)</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($news as $article)
                <article class="bg-card rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300">
                    @if($article['image'])
                        <div class="h-48 bg-gray-700 overflow-hidden">
                            <img src="{{ $article['image'] }}" 
                                 alt="{{ $article['title'] }}" 
                                 class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                 onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full bg-gray-700\'><i class=\'fas fa-image text-gray-500 text-3xl\'></i></div>'">
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-primary font-medium text-sm">{{ $article['source'] }}</span>
                            @if($article['published_at'])
                                <time class="text-gray-400 text-sm">
                                    {{ $article['published_at']->diffForHumans() }}
                                </time>
                            @endif
                        </div>
                        
                        <h3 class="text-lg font-bold text-light mb-3 leading-tight">
                            <a href="{{ $article['url'] }}" target="_blank" class="hover:text-primary transition-colors duration-200">
                                {{ $article['title'] }}
                            </a>
                        </h3>
                        
                        @if($article['description'])
                            <p class="text-gray-300 text-sm mb-4">{{ Str::limit($article['description'], 120) }}</p>
                        @endif
                        
                        <a href="{{ $article['url'] }}" target="_blank" class="inline-flex items-center text-primary hover:text-primary-light transition-colors duration-200 text-sm">
                            Read more
                            <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @elseif($query)
        <!-- No Results -->
        <div class="bg-card rounded-lg p-12 text-center">
            <i class="fas fa-search text-gray-500 text-6xl mb-6"></i>
            <h3 class="text-2xl font-semibold text-light mb-4">No Results Found</h3>
            <p class="text-gray-400 mb-6">We couldn't find any news articles matching your search query.</p>
            <div class="space-y-2 text-gray-400 text-sm">
                <p>Try these suggestions:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Check your spelling</li>
                    <li>Use different keywords</li>
                    <li>Try broader search terms</li>
                    <li>Search for team names or player names</li>
                </ul>
            </div>
        </div>
    @else
        <!-- Empty Search -->
        <div class="bg-card rounded-lg p-12 text-center">
            <i class="fas fa-newspaper text-gray-500 text-6xl mb-6"></i>
            <h3 class="text-2xl font-semibold text-light mb-4">Search Football News</h3>
            <p class="text-gray-400 mb-6">Enter a search term to find the latest football news and updates.</p>
            <div class="flex flex-wrap justify-center gap-2">
                <span class="text-gray-500 text-sm">Popular searches:</span>
                <a href="{{ route('news.search', ['q' => 'Premier League']) }}" class="text-primary hover:text-primary-light text-sm">Premier League</a>
                <span class="text-gray-600">•</span>
                <a href="{{ route('news.search', ['q' => 'Champions League']) }}" class="text-primary hover:text-primary-light text-sm">Champions League</a>
                <span class="text-gray-600">•</span>
                <a href="{{ route('news.search', ['q' => 'Transfer']) }}" class="text-primary hover:text-primary-light text-sm">Transfer</a>
            </div>
        </div>
    @endif

    @if($query && !empty($news))
        <!-- Back to News -->
        <div class="mt-8 text-center">
            <a href="{{ route('news.index') }}" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to All News
            </a>
        </div>
    @endif
</div>
@endsection
