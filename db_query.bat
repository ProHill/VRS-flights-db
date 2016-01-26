set THEDATABASE=C:\progra~2\Kinetic\BaseStation\Basestation.sqb
set THECSVFILE=c:\sqlite\flights.csv
if exist %THECSVFILE% del %THECSVFILE%
:: allow time for the csv file to be deleted
timeout /t 2 /nobreak
c:\sqlite\sqlite3.exe %THEDATABASE% < "c:\sqlite\dbquerycommands.txt"
::allow time for the csv to be written to file
timeout /t 2 /nobreak
"C:\Program Files (x86)\putty\pscp.exe" -scp %THECSVFILE% <username>@<web.server.ip>:<path to flights directory such as /srv/www/htdocs/flights>