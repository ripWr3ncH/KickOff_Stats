@extends('layouts.app')

@section('title', $teamName . ' News - KickOff Stats')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-light mb-4">
            <i class="fas fa-shield-alt text-primary mr-3"></i>
            {{ $teamName }} News
        </h1>
        <p class="text-gray-400">Latest news and updates about {{ $teamName }}</p>
    </div>

    <!-- Navigation -->
    <div class="flex flex-wrap justify-center gap-4 mb-8">
        <a href="{{ route('news.index') }}" class="px-4 py-2 bg-gray-600/20 text-gray-300 hover:bg-gray-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>All News
        </a>
        <a href="{{ route('news.search') }}" class="px-4 py-2 bg-blue-600/20 text-blue-300 hover:bg-blue-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-search mr-2"></i>Search News
        </a>
    </div>

    <!-- News Articles -->
    @if(!empty($news) && count($news) > 0)
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
                        
                        @if($article['author'])
                            <p class="text-gray-500 text-xs mb-3">By {{ $article['author'] }}</p>
                        @endif
                        
                        <a href="{{ $article['url'] }}" target="_blank" class="inline-flex items-center text-primary hover:text-primary-light transition-colors duration-200 text-sm">
                            Read full article
                            <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <!-- No News Available -->
        <div class="bg-card rounded-lg p-12 text-center">
            <i class="fas fa-newspaper text-gray-500 text-6xl mb-6"></i>
            <h3 class="text-2xl font-semibold text-light mb-4">No {{ $teamName }} News Found</h3>
            <p class="text-gray-400 mb-6">We couldn't find any recent news articles about {{ $teamName }}.</p>
            
            <div class="space-y-4">
                <p class="text-gray-400 text-sm">Try these alternatives:</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('news.index') }}" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-newspaper mr-2"></i>
                        View All News
                    </a>
                    <a href="{{ route('news.search', ['q' => $teamName]) }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Search for {{ $teamName }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Teams -->
    @if(!empty($news))
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-light mb-6 text-center">More Team News</h2>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('news.team', 'manchester-united') }}" class="px-4 py-2 bg-red-600/20 text-red-300 hover:bg-red-600/30 rounded-lg transition-colors duration-200">
                    Manchester United
                </a>
                <a href="{{ route('news.team', 'liverpool') }}" class="px-4 py-2 bg-red-600/20 text-red-300 hover:bg-red-600/30 rounded-lg transition-colors duration-200">
                    Liverpool
                </a>
                <a href="{{ route('news.team', 'chelsea') }}" class="px-4 py-2 bg-blue-600/20 text-blue-300 hover:bg-blue-600/30 rounded-lg transition-colors duration-200">
                    Chelsea
                </a>
                <a href="{{ route('news.team', 'arsenal') }}" class="px-4 py-2 bg-red-600/20 text-red-300 hover:bg-red-600/30 rounded-lg transition-colors duration-200">
                    Arsenal
                </a>
                <a href="{{ route('news.team', 'real-madrid') }}" class="px-4 py-2 bg-white/20 text-white hover:bg-white/30 rounded-lg transition-colors duration-200">
                    Real Madrid
                </a>
                <a href="{{ route('news.team', 'fc-barcelona') }}" class="px-4 py-2 bg-blue-600/20 text-blue-300 hover:bg-blue-600/30 rounded-lg transition-colors duration-200">
                    FC Barcelona
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
