<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FootballNewsService
{
    protected $apiKey;
    protected $baseUrl = 'https://newsapi.org/v2';

    public function __construct()
    {
        $this->apiKey = config('services.news_api.api_key');
    }

    /**
     * Get latest football news
     */
    public function getLatestNews($limit = 20, $language = 'en')
    {
        $cacheKey = "football_news_latest_{$limit}_{$language}";
        
        return Cache::remember($cacheKey, 900, function () use ($limit, $language) { // Cache for 15 minutes
            try {
                $response = Http::get($this->baseUrl . '/everything', [
                    'q' => 'football OR soccer OR "premier league" OR "champions league" OR "europa league" OR "la liga" OR "serie a" OR "bundesliga"',
                    'language' => $language,
                    'sortBy' => 'publishedAt',
                    'pageSize' => $limit,
                    'domains' => 'bbc.co.uk,espn.com,goal.com,skysports.com,theguardian.com,cnn.com,reuters.com,ap.org',
                    'apiKey' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->processNewsArticles($data['articles'] ?? []);
                }

                Log::error('News API Error: ' . $response->body());
                return [];
            } catch (\Exception $e) {
                Log::error('Football News API Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get news by specific team
     */
    public function getTeamNews($teamName, $limit = 10)
    {
        $cacheKey = "football_news_team_" . \Illuminate\Support\Str::slug($teamName) . "_{$limit}";
        
        return Cache::remember($cacheKey, 1800, function () use ($teamName, $limit) { // Cache for 30 minutes
            try {
                $response = Http::get($this->baseUrl . '/everything', [
                    'q' => "\"{$teamName}\" AND (football OR soccer)",
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => $limit,
                    'domains' => 'bbc.co.uk,espn.com,goal.com,skysports.com,theguardian.com',
                    'apiKey' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->processNewsArticles($data['articles'] ?? []);
                }

                return [];
            } catch (\Exception $e) {
                Log::error('Football Team News API Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get news by league
     */
    public function getLeagueNews($leagueName, $limit = 10)
    {
        $cacheKey = "football_news_league_" . \Illuminate\Support\Str::slug($leagueName) . "_{$limit}";
        
        return Cache::remember($cacheKey, 1800, function () use ($leagueName, $limit) {
            try {
                $response = Http::get($this->baseUrl . '/everything', [
                    'q' => "\"{$leagueName}\" AND football",
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => $limit,
                    'domains' => 'bbc.co.uk,espn.com,goal.com,skysports.com,theguardian.com',
                    'apiKey' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->processNewsArticles($data['articles'] ?? []);
                }

                return [];
            } catch (\Exception $e) {
                Log::error('Football League News API Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Search news by keyword
     */
    public function searchNews($keyword, $limit = 20)
    {
        try {
            $response = Http::get($this->baseUrl . '/everything', [
                'q' => "{$keyword} AND (football OR soccer)",
                'language' => 'en',
                'sortBy' => 'publishedAt',
                'pageSize' => $limit,
                'domains' => 'bbc.co.uk,espn.com,goal.com,skysports.com,theguardian.com',
                'apiKey' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->processNewsArticles($data['articles'] ?? []);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Football News Search Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get trending football topics
     */
    public function getTrendingNews($limit = 15)
    {
        $cacheKey = "football_news_trending_{$limit}";
        
        return Cache::remember($cacheKey, 600, function () use ($limit) { // Cache for 10 minutes
            try {
                // First try: Get sports headlines and filter for soccer/football
                $response = Http::get($this->baseUrl . '/top-headlines', [
                    'category' => 'sports',
                    'language' => 'en',
                    'pageSize' => $limit * 3, // Get more to filter from
                    'apiKey' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    // Filter for soccer/football articles (not American football)
                    $articles = collect($data['articles'] ?? [])
                        ->filter(function ($article) {
                            $text = strtolower($article['title'] . ' ' . ($article['description'] ?? ''));
                            
                            // Exclude American football terms
                            if (str_contains($text, 'nfl') || 
                                str_contains($text, 'quarterback') || 
                                str_contains($text, 'browns') && str_contains($text, 'cleveland') ||
                                str_contains($text, 'thursday night football') ||
                                str_contains($text, 'college football') ||
                                str_contains($text, 'michigan football')) {
                                return false;
                            }
                            
                            // Include soccer/football terms
                            return str_contains($text, 'soccer') || 
                                   str_contains($text, 'premier league') ||
                                   str_contains($text, 'champions league') ||
                                   str_contains($text, 'la liga') ||
                                   str_contains($text, 'serie a') ||
                                   str_contains($text, 'bundesliga') ||
                                   str_contains($text, 'uefa') ||
                                   str_contains($text, 'fifa') ||
                                   str_contains($text, 'barcelona') ||
                                   str_contains($text, 'real madrid') ||
                                   str_contains($text, 'manchester united') ||
                                   str_contains($text, 'manchester city') ||
                                   str_contains($text, 'liverpool') ||
                                   str_contains($text, 'arsenal') ||
                                   str_contains($text, 'chelsea') ||
                                   str_contains($text, 'tottenham') ||
                                   str_contains($text, 'psg') ||
                                   str_contains($text, 'juventus') ||
                                   str_contains($text, 'bayern munich') ||
                                   str_contains($text, 'ac milan') ||
                                   str_contains($text, 'inter milan') ||
                                   str_contains($text, 'atletico') ||
                                   str_contains($text, 'borussia') ||
                                   str_contains($text, 'messi') ||
                                   str_contains($text, 'ronaldo') ||
                                   str_contains($text, 'neymar') ||
                                   str_contains($text, 'mbappe') ||
                                   str_contains($text, 'haaland') ||
                                   str_contains($text, 'vinicius') ||
                                   (str_contains($text, 'football') && !str_contains($text, 'american'));
                        })
                        ->take($limit)
                        ->values()
                        ->toArray();
                    
                    // If we found soccer articles, return them
                    if (!empty($articles)) {
                        return $this->processNewsArticles($articles);
                    }
                }
                
                // Fallback: Get general football news using "everything" endpoint
                $fallbackResponse = Http::get($this->baseUrl . '/everything', [
                    'q' => 'soccer OR "champions league" OR "premier league" OR Barcelona OR "Real Madrid"',
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => min($limit, 10),
                    'domains' => 'bbc.co.uk,espn.com,goal.com,skysports.com',
                    'apiKey' => $this->apiKey
                ]);
                
                if ($fallbackResponse->successful()) {
                    $fallbackData = $fallbackResponse->json();
                    return $this->processNewsArticles($fallbackData['articles'] ?? []);
                }
                
                return [];
                
            } catch (\Exception $e) {
                Log::error('Football Trending News Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Process and clean news articles
     */
    private function processNewsArticles($articles)
    {
        return collect($articles)
            ->filter(function ($article) {
                // Filter out articles without images or essential data
                return !empty($article['title']) && 
                       !empty($article['url']) && 
                       !str_contains(strtolower($article['title']), '[removed]');
            })
            ->map(function ($article) {
                return [
                    'title' => $article['title'],
                    'description' => $article['description'] ?? '',
                    'url' => $article['url'],
                    'image' => $article['urlToImage'] ?? null,
                    'source' => $article['source']['name'] ?? 'Unknown',
                    'published_at' => $article['publishedAt'] ? 
                        \Carbon\Carbon::parse($article['publishedAt']) : null,
                    'author' => $article['author'] ?? null,
                ];
            })
            ->toArray();
    }

    /**
     * Get fallback news when API is down
     */
    public function getFallbackNews()
    {
        return [
            [
                'title' => 'Latest Football News',
                'description' => 'Stay updated with the latest football news and updates.',
                'url' => 'https://www.bbc.com/sport/football',
                'image' => null,
                'source' => 'BBC Sport',
                'published_at' => now(),
                'author' => null,
            ],
            [
                'title' => 'Premier League Updates',
                'description' => 'Get the latest Premier League news and match updates.',
                'url' => 'https://www.skysports.com/premier-league',
                'image' => null,
                'source' => 'Sky Sports',
                'published_at' => now(),
                'author' => null,
            ]
        ];
    }
}