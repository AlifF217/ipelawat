@echo off
REM Auto-detect local IP address
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr "IPv4"') do set IP=%%a
set IP=%IP: =%

cls
echo Current IP detected: %IP%

REM Run cron using detected IP
curl http://%IP%/myproject/public/cron/runDailyTask