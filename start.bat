@echo off
echo Starting BC Attendance System...
echo.
echo Server will be available at: http://localhost:8000
echo Default login: admin / admin123
echo.
echo Press Ctrl+C to stop the server
echo.
php -S localhost:8000 -t public
pause
