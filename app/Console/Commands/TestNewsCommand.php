<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FootballNewsService;

class TestNewsCommand extends Command
{
    protected $signature = 'news:test {--limit=5 : Number of articles to fetch}';
    protected $description = 'Test the football news service';

    protected $newsService;

    public function __construct(FootballNewsService $newsService)
    {
        parent::__construct();
        $this->newsService = $newsService;
    }

    public function handle()
    {
        $this->info('Testing Football News Service...');
        $this->info('');

        $limit = (int) $this->option('limit');

        // Test latest news
        $this->info("Fetching {$limit} latest football news articles...");
        $news = $this->newsService->getLatestNews($limit);

        if (!empty($news)) {
            $this->info("✅ Successfully fetched " . count($news) . " articles");
            $this->info('');
            
            foreach ($news as $index => $article) {
                $this->info(($index + 1) . ". " . $article['title']);
                $this->comment("   Source: " . $article['source']);
                if ($article['published_at']) {
                    $this->comment("   Published: " . $article['published_at']->diffForHumans());
                }
                $this->info('');
            }
        } else {
            $this->warn("⚠️  No news articles fetched. This could be due to:");
            $this->warn("   1. Missing NEWS_API_KEY in .env file");
            $this->warn("   2. API rate limit reached");
            $this->warn("   3. Network connectivity issues");
            $this->info('');
            $this->info("💡 To set up News API:");
            $this->info("   1. Visit https://newsapi.org/register");
            $this->info("   2. Get your free API key");
            $this->info("   3. Add NEWS_API_KEY=your_key to your .env file");
            $this->info('');
            $this->info("The news feature will show fallback content without an API key.");
        }

        // Test fallback news
        $this->info("Testing fallback news...");
        $fallbackNews = $this->newsService->getFallbackNews();
        $this->info("✅ Fallback news available: " . count($fallbackNews) . " articles");

        return 0;
    }
}