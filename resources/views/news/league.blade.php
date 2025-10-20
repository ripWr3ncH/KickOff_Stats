@extends('layouts.app')

@section('title', $leagueName . ' News - KickOff Stats')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-light mb-4">
            <i class="fas fa-trophy text-primary mr-3"></i>
            {{ $leagueName }} News
        </h1>
        <p class="text-gray-400">Latest news and updates from {{ $leagueName }}</p>
    </div>

    <!-- Navigation -->
    <div class="flex flex-wrap justify-center gap-4 mb-8">
        <a href="{{ route('news.index') }}" class="px-4 py-2 bg-gray-600/20 text-gray-300 hover:bg-gray-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>All News
        </a>
        @if($leagueSlug !== 'premier-league')
        <a href="{{ route('news.league', 'premier-league') }}" class="px-4 py-2 bg-blue-600/20 text-blue-300 hover:bg-blue-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-crown mr-2"></i>Premier League
        </a>
        @endif
        @if($leagueSlug !== 'la-liga')
        <a href="{{ route('news.league', 'la-liga') }}" class="px-4 py-2 bg-yellow-600/20 text-yellow-300 hover:bg-yellow-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-futbol mr-2"></i>La Liga
        </a>
        @endif
        @if($leagueSlug !== 'champions-league')
        <a href="{{ route('news.league', 'champions-league') }}" class="px-4 py-2 bg-purple-600/20 text-purple-300 hover:bg-purple-600/30 rounded-lg transition-colors duration-200">
            <i class="fas fa-star mr-2"></i>Champions League
        </a>
        @endif
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
            <i class="fas fa-trophy text-gray-500 text-6xl mb-6"></i>
            <h3 class="text-2xl font-semibold text-light mb-4">No {{ $leagueName }} News Found</h3>
            <p class="text-gray-400 mb-6">We couldn't find any recent news articles about {{ $leagueName }}.</p>
            
            <div class="space-y-4">
                <p class="text-gray-400 text-sm">Try these alternatives:</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('news.index') }}" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-newspaper mr-2"></i>
                        View All News
                    </a>
                    <a href="{{ route('news.search', ['q' => $leagueName]) }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Search for {{ $leagueName }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Leagues -->
    @if(!empty($news))
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-light mb-6 text-center">Other League News</h2>
            <div class="flex flex-wrap justify-center gap-4">
                @if($leagueSlug !== 'premier-league')
                <a href="{{ route('news.league', 'premier-league') }}" class="px-4 py-2 bg-blue-600/20 text-blue-300 hover:bg-blue-600/30 rounded-lg transition-colors duration-200">
                    <i class="fas fa-crown mr-2"></i>Premier League
                </a>
                @endif
                @if($leagueSlug !== 'la-liga')
                <a href="{{ route('news.league', 'la-liga') }}" class="px-4 py-2 bg-yellow-600/20 text-yellow-300 hover:bg-yellow-600/30 rounded-lg transition-colors duration-200">
                    <i class="fas fa-futbol mr-2"></i>La Liga
                </a>
                @endif
                @if($leagueSlug !== 'serie-a')
                <a href="{{ route('news.league', 'serie-a') }}" class="px-4 py-2 bg-green-600/20 text-green-300 hover:bg-green-600/30 rounded-lg transition-colors duration-200">
                    <i class="fas fa-trophy mr-2"></i>Serie A
                </a>
                @endif
                @if($leagueSlug !== 'bundesliga')
                <a href="{{ route('news.league', 'bundesliga') }}" class="px-4 py-2 bg-red-600/20 text-red-300 hover:bg-red-600/30 rounded-lg transition-colors duration-200">
                    <i class="fas fa-shield-alt mr-2"></i>Bundesliga
                </a>
                @endif
                @if($leagueSlug !== 'champions-league')
                <a href="{{ route('news.league', 'champions-league') }}" class="px-4 py-2 bg-purple-600/20 text-purple-300 hover:bg-purple-600/30 rounded-lg transition-colors duration-200">
                    <i class="fas fa-star mr-2"></i>Champions League
                </a>
                @endif
                @if($leagueSlug !== 'europa-league')
                <a href="{{ route('news.league', 'europa-league') }}" class="px-4 py-2 bg-orange-600/20 text-orange-300 hover:bg-orange-600/30 rounded-lg transition-colors duration-200">
                    <i class="fas fa-globe-europe mr-2"></i>Europa League
                </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
