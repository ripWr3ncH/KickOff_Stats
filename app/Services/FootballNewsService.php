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
                // Use "everything" endpoint with popular keywords
                // Note: Free API plan only supports sortBy=publishedAt
                $response = Http::get($this->baseUrl . '/everything', [
                    'q' => 'soccer OR "premier league" OR "champions league" OR "la liga" OR Barcelona OR "Real Madrid" OR "Manchester United" OR Liverpool',
                    'language' => 'en',
                    'sortBy' => 'publishedAt', // Free API only supports publishedAt
                    'pageSize' => $limit,
                    'domains' => 'bbc.co.uk,espn.com,goal.com,skysports.com,theguardian.com',
                    'apiKey' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Filter out American football articles
                    $articles = collect($data['articles'] ?? [])
                        ->filter(function ($article) {
                            $text = strtolower($article['title'] . ' ' . ($article['description'] ?? ''));
                            
                            // Exclude American football terms
                            if (str_contains($text, 'nfl') || 
                                str_contains($text, 'quarterback') || 
                                str_contains($text, 'browns') && str_contains($text, 'cleveland') ||
                                str_contains($text, 'thursday night football') ||
                                str_contains($text, 'college football')) {
                                return false;
                            }
                            
                            return true;
                        })
                        ->take($limit)
                        ->values()
                        ->toArray();
                    
                    if (!empty($articles)) {
                        return $this->processNewsArticles($articles);
                    }
                }
                
                Log::error('Trending News API failed: ' . ($response->body() ?? 'Unknown error'));
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