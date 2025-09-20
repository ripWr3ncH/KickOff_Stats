class LiveScoreUpdater {
    constructor() {
        this.updateInterval = 30000; // 30 seconds
        this.isUpdating = false;
        this.init();
    }

    init() {
        // Start automatic updates if there are live matches
        if (document.querySelectorAll('.live-score').length > 0) {
            this.startUpdates();
        }

        // Add manual refresh button
        this.addRefreshButton();
    }

    startUpdates() {
        this.updateLiveScores();
        this.intervalId = setInterval(() => {
            this.updateLiveScores();
        }, this.updateInterval);
    }

    stopUpdates() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
        }
    }

    async updateLiveScores() {
        if (this.isUpdating) return;
        
        this.isUpdating = true;
        
        try {
            const response = await fetch('/api/live-scores/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateMatchElements(data.matches);
                this.showUpdateNotification(data.updated);
            }
        } catch (error) {
            console.error('Failed to update live scores:', error);
        } finally {
            this.isUpdating = false;
        }
    }

    updateMatchElements(matches) {
        matches.forEach(match => {
            const matchElement = document.querySelector(`[data-match-id="${match.id}"]`);
            if (matchElement) {
                // Update score
                const scoreElement = matchElement.querySelector('.score');
                if (scoreElement) {
                    scoreElement.textContent = `${match.home_score} - ${match.away_score}`;
                }

                // Update minute
                const minuteElement = matchElement.querySelector('.minute');
                if (minuteElement) {
                    minuteElement.textContent = match.minute ? `${match.minute}'` : '';
                }

                // Update status
                const statusElement = matchElement.querySelector('.status');
                if (statusElement) {
                    statusElement.textContent = this.formatStatus(match.status);
                }

                // Add pulse animation for live matches
                if (match.status === 'live') {
                    matchElement.classList.add('live-pulse');
                } else {
                    matchElement.classList.remove('live-pulse');
                }
            }
        });
    }

    formatStatus(status) {
        const statusMap = {
            'live': 'LIVE',
            'finished': 'FT',
            'scheduled': '',
            'postponed': 'POSTPONED'
        };
        return statusMap[status] || status.toUpperCase();
    }

    showUpdateNotification(count) {
        if (count > 0) {
            // Create a subtle notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-primary text-white px-4 py-2 rounded-lg text-sm z-50 transform translate-x-full transition-transform duration-300';
            notification.textContent = `${count} match${count > 1 ? 'es' : ''} updated`;
            
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Hide notification after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    }

    addRefreshButton() {
        const refreshButton = document.createElement('button');
        refreshButton.innerHTML = '<i class="fas fa-sync-alt"></i>';
        refreshButton.className = 'fixed bottom-4 right-4 bg-primary text-white w-12 h-12 rounded-full shadow-lg hover:bg-green-600 transition-colors duration-300 z-50 flex items-center justify-center';
        refreshButton.title = 'Refresh live scores';
        
        refreshButton.addEventListener('click', () => {
            refreshButton.querySelector('i').classList.add('fa-spin');
            this.updateLiveScores().then(() => {
                refreshButton.querySelector('i').classList.remove('fa-spin');
            });
        });
        
        document.body.appendChild(refreshButton);
    }

    // WebSocket connection for real-time updates (optional)
    connectWebSocket() {
        if (!window.WebSocket) return;

        const ws = new WebSocket(`ws://${window.location.host}/ws/live-scores`);
        
        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.type === 'score_update') {
                this.updateMatchElements([data.match]);
            }
        };

        ws.onclose = () => {
            // Reconnect after 5 seconds
            setTimeout(() => this.connectWebSocket(), 5000);
        };
    }
}

// Initialize live score updater when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new LiveScoreUpdater();
});

// Export for use in other scripts
window.LiveScoreUpdater = LiveScoreUpdater;
