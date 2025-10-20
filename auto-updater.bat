@echo off
title KickOff Stats - Live Data Auto-Updater
echo ============================================
echo KickOff Stats Live Data Auto-Updater
echo ============================================
echo Live scores update every 2 minutes
echo Match data syncs every 30 minutes  
echo News updates every 15 minutes
echo Press Ctrl+C to stop
echo ============================================
echo.

cd /d "c:\xampp\htdocs\KickOff_Stats"

:loop
echo [%date% %time%] Running scheduled tasks...
php artisan schedule:run
if %errorlevel% neq 0 (
    echo [%date% %time%] ERROR: Scheduler failed with exit code %errorlevel%
) else (
    echo [%date% %time%] Scheduler completed successfully
)
echo [%date% %time%] Waiting 30 seconds...
echo.
timeout /t 30 /nobreak >nul
goto loop