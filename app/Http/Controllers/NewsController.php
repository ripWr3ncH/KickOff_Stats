<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FootballNewsService;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(FootballNewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * Display the main news page
     */
    public function index()
    {
        $latestNews = $this->newsService->getLatestNews(16);
        $trendingNews = $this->newsService->getTrendingNews(8);
        
        // If API fails, use fallback
        if (empty($latestNews)) {
            $latestNews = $this->newsService->getFallbackNews();
        }

        return view('news.index', compact('latestNews', 'trendingNews'));
    }

    /**
     * Search news
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $news = [];
        
        if (!empty($query)) {
            $news = $this->newsService->searchNews($query, 20);
        }
        
        return view('news.search', compact('news', 'query'));
    }

    /**
     * Get team-specific news
     */
    public function team($teamSlug)
    {
        // Convert slug back to team name
        $teamName = str_replace('-', ' ', $teamSlug);
        $teamName = ucwords($teamName);
        
        // Handle special cases for team names
        $teamNameMappings = [
            'Fc Barcelona' => 'FC Barcelona',
            'Real Madrid' => 'Real Madrid',
            'Manchester United' => 'Manchester United',
            'Manchester City' => 'Manchester City',
            'Liverpool' => 'Liverpool FC',
            'Chelsea' => 'Chelsea FC',
            'Arsenal' => 'Arsenal FC',
            'Tottenham' => 'Tottenham Hotspur',
        ];
        
        $searchName = $teamNameMappings[$teamName] ?? $teamName;
        $news = $this->newsService->getTeamNews($searchName, 15);
        
        return view('news.team', compact('news', 'teamName', 'teamSlug'));
    }

    /**
     * Get league-specific news
     */
    public function league($leagueSlug)
    {
        // Convert slug to league name
        $leagueNames = [
            'premier-league' => 'Premier League',
            'la-liga' => 'La Liga',
            'serie-a' => 'Serie A',
            'bundesliga' => 'Bundesliga',
            'ligue-1' => 'Ligue 1',
            'champions-league' => 'Champions League',
            'europa-league' => 'Europa League'
        ];
        
        $leagueName = $leagueNames[$leagueSlug] ?? ucwords(str_replace('-', ' ', $leagueSlug));
        $news = $this->newsService->getLeagueNews($leagueName, 15);
        
        return view('news.league', compact('news', 'leagueName', 'leagueSlug'));
    }

    /**
     * API endpoint for news
     */
    public function api()
    {
        $latestNews = $this->newsService->getLatestNews(10);
        
        return response()->json([
            'success' => true,
            'data' => $latestNews,
            'count' => count($latestNews)
        ]);
    }
}