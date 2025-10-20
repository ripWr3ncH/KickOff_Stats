# ⚽ KickOff Stats

A modern, real-time football statistics and live scores web application built with Laravel, featuring comprehensive match tracking, league standings, and live score updates from multiple European leagues.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

## ✨ Features

### 🔴 Live Match Tracking
- **Real-time live scores** with minute-by-minute updates
- **Live match status** indicators (LIVE, HT, FT, etc.)
- **Dynamic score updates** without page refresh
- **Match timeline** with goals, cards, and substitutions

### 📰 Football News
- **Latest football news** from trusted sources (BBC, ESPN, Goal, Sky Sports)
- **Team-specific news** for your favorite clubs
- **League news** for Premier League, La Liga, Serie A, and more
- **Search functionality** to find specific news topics
- **Trending news** section with top sports headlines
- **Cached content** for improved performance

### 🏆 League Coverage
- **Premier League** (England)
- **La Liga** (Spain)  
- **Serie A** (Italy)
- **Bundesliga** (Germany) - *Coming Soon*
- **Ligue 1** (France) - *Coming Soon*

### 📊 Comprehensive Statistics
- **Team standings** and league tables
- **Player statistics** and performance metrics
- **Match history** and head-to-head records
- **Season analytics** and trends

### 🎨 Modern UI/UX
- **Responsive design** optimized for all devices
- **Dark/Light theme** support
- **Real-time notifications** for score updates
- **Clean, intuitive interface** with Tailwind CSS

### 🔄 Real-time Updates
- **WebSocket integration** for live updates
- **API-driven** architecture for fast data delivery
- **Background job processing** for continuous data sync
- **Rate-limited API calls** for optimal performance

## 🚀 Quick Start

### Prerequisites
- **PHP 8.2+**
- **Composer**
- **Node.js 18+**
- **MySQL 8.0+**
- **Laravel 12.x**

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/ripWr3ncH/KickOff_Stats.git
   cd KickOff_Stats
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your environment**
   ```env
   # Database Configuration
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kickoffstats_db
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   # Football Data API (Primary)
   FOOTBALL_DATA_API_KEY=your_football_data_api_key

   # API Football (Alternative)
   API_FOOTBALL_KEY=your_api_football_key
   API_FOOTBALL_HOST=api-football-v1.p.rapidapi.com
   ```

6. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

8. **Start the application**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## 🔧 Configuration

### API Keys Setup

#### Football-Data.org API
1. Register at [Football-Data.org](https://www.football-data.org/client/register)
2. Get your free API key (30 requests/minute)
3. Add to `.env`: `FOOTBALL_DATA_API_KEY=your_key_here`

#### API-Football (Optional)
1. Register at [RapidAPI](https://rapidapi.com/api-sports/api/api-football)
2. Subscribe to API-Football
3. Add to `.env`: `API_FOOTBALL_KEY=your_key_here`

#### NewsAPI.org (For Football News)
1. Register at [NewsAPI.org](https://newsapi.org/register)
2. Get your free API key (1,000 requests/day)
3. Add to `.env`: `NEWS_API_KEY=your_key_here`
4. **Note**: News feature works with fallback content even without API key

### Database Configuration
Create a MySQL database and update your `.env` file with the credentials.

## 🎮 Usage

### Artisan Commands

#### Data Management
```bash
# Fetch latest matches (next 7 days)
php artisan matches:fetch

# Sync live scores and update match status
php artisan football:sync

# Update team logos from API
php artisan teams:update-logos

# Check database content and statistics
php artisan data:check

# Check team logo status
php artisan teams:check-logos

# Test football news service
php artisan news:test
```

#### Background Processing
```bash
# Start queue worker for real-time updates
php artisan queue:work

# Schedule automatic data sync (add to crontab)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### API Endpoints

| Endpoint | Method | Description |
|----------|---------|-------------|
| `/api/live-scores` | GET | Get current live matches |
| `/api/matches` | GET | Get all matches |
| `/api/leagues/{id}` | GET | Get league details and standings |
| `/api/teams/{id}` | GET | Get team information |
| `/api/news` | GET | Get latest football news |

### Frontend Features

#### Live Score Updates
The application automatically updates live scores every 30 seconds using AJAX calls to the API endpoints.

#### Responsive Design
- **Mobile-first** approach
- **Progressive Web App** capabilities
- **Offline mode** for cached data

## 🏗️ Architecture

### Backend (Laravel)
```
app/
├── Console/Commands/     # Artisan commands for data management
├── Http/Controllers/     # Web and API controllers
├── Models/              # Eloquent models (Team, League, Match, etc.)
├── Services/            # Business logic (FootballDataService)
└── Jobs/                # Background job processing
```

### Frontend (Blade + JavaScript)
```
resources/
├── views/               # Blade templates
├── js/                  # JavaScript modules
└── css/                 # Tailwind CSS styles
```

### Database Schema
- **leagues** - Competition information
- **teams** - Team details and logos
- **football_matches** - Match data and scores
- **players** - Player information
- **player_stats** - Performance statistics

## 🔌 API Integration

### Supported APIs
1. **Football-Data.org** (Primary)
   - Free tier: 30 requests/minute
   - Covers major European leagues
   - Includes team logos and detailed match data

2. **API-Football** (Secondary)
   - Backup data source
   - Extended coverage
   - Player statistics

### Rate Limiting
- Automatic rate limiting to respect API quotas
- Intelligent caching to minimize API calls
- Fallback mechanisms for API downtime

## 🎨 Customization

### Adding New Leagues
1. Add league configuration in `FootballDataService::getLeagueMapping()`
2. Create league record in database
3. Update team mapping in `UpdateTeamLogosCommand`

### Styling
The application uses Tailwind CSS for styling. Customize the design by:
- Modifying `tailwind.config.js`
- Updating component styles in Blade templates
- Adding custom CSS in `resources/css/app.css`

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 📊 Performance

### Optimization Features
- **Database indexing** for fast queries
- **API response caching** with Redis
- **Image lazy loading** for team logos
- **Minified assets** for production

### Monitoring
- **Laravel Telescope** for debugging
- **Query optimization** with Laravel Debugbar
- **Error logging** with detailed stack traces

## 🔒 Security

- **API key encryption** in environment variables
- **CSRF protection** on all forms
- **SQL injection prevention** with Eloquent ORM
- **XSS protection** with Blade templating

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation as needed
- Use meaningful commit messages

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Football-Data.org** for providing comprehensive football data
- **Laravel Framework** for the robust backend foundation
- **Tailwind CSS** for the beautiful, responsive design
- **Font Awesome** for the iconography

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/ripWr3ncH/KickOff_Stats/issues)
- **Discussions**: [GitHub Discussions](https://github.com/ripWr3ncH/KickOff_Stats/discussions)
- **Email**: [your-email@example.com]

## 🔮 Roadmap

### Upcoming Features
- [ ] **Push notifications** for favorite teams
- [ ] **Fantasy football** integration
- [ ] **Match predictions** with ML
- [ ] **Social features** (comments, sharing)
- [ ] **Mobile app** (React Native)
- [ ] **Extended league coverage**
- [ ] **Historical data analysis**
- [ ] **Live commentary** integration

---

⭐ **Star this repository** if you find it helpful!

🐛 **Found a bug?** [Report it here](https://github.com/ripWr3ncH/KickOff_Stats/issues/new)

🚀 **Want a feature?** [Request it here](https://github.com/ripWr3ncH/KickOff_Stats/issues/new)

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
#   K i c k O f f _ S t a t s 
 
 