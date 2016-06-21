# VRS-flights-db
Code to export Virtual Radar Server flight records and track logs to a MySQL database.  This is the code used to power http://flights.hillhome.org.
Also includes example pages written in PHP to display flight info.

##Prerequisites
- VRS installed and running
- VRS database writer plugin configured and enabled
- A web server running PHP and MySQL. These instructions assume you will be using a Linux host, although you can use any OS.
  - PuTTY (including pageant and pscp) installed on your VRS machine
  - You will need to configure Pageant for key-based authentication to your web server using instructions such as http://johannesbrodwall.com/2011/06/15/howto-use-pageant-and-putty/
- [phpMyAdmin](https://www.phpmyadmin.net) is very helpful for viewing your MySQL tables to ensure records are being populated correctly.

##Instructions
Note: These instructions are not exhaustive.  You will need to be handy with Windows, Linux, MySQL, etc.  If you encounter issues, please log them here and I will update the documentation.

### Database schema
You will need to create a database and two tables on your MySQL database host.

```
mysql -u root
$create database adsb;
grant usage on *.* to vrsdbwriter@localhost identified by 'somepasswordhere';
grant all privileges on adsb.* to vrsdbwriter@localhost;
```

Now import the three .sql files in this repository:
```
use adsb;
source path/to/flights.sql
source path/to/track_mlat_lookup.sql
source path/to/mlat_cleanup.sql
```

###Install sqlite for Windows
You will need to install the sqlite3.exe binary on your VRS host.
- Grab the sqlite-tools-win32-x86 zip file from https://www.sqlite.org/download.html
- Unzip and place sqlite3.exe in c:\sqlite

###Windows scripts
Place the files from the windows directory of this repository in your c:\sqlite directory on the VRS host:
- db_query.bat
- db_query.vbs
- dbquerycommands.txt

Double check all the paths in these files, as your setup may differ.

###PHP scripts
Place all the files from the webserver directory of this repository onto your web server in a flights directory under the web server's document root (example: /srv/www/htdocs/flights)

You will need to edit the config-example.php file and fill in your database connection information and your VRS hostname and port, plus username and password if your setup is password protected.  *Then rename the file to config.php.*

Now login to your web server and edit the crontab as follows:
```
crontab -e
```
Enter the following line into the file - this will run the getTrackMlat.php file every minute.
```
*/1 * * * * /usr/bin/php /srv/www/htdocs/flights/getTrackMlat.php >/dev/null
```
Save the file, and the new crontab will be installed.  After a few minutes, you should observe rows being added to the track_mlat_lookup table in your database.

###PuTTY Setup on Windows
First, configure Pageant for password-less authentication to your web server as discussed in the prereqs section.
Next, create a PuTTY profile for your web server that will execute the flightimport.php file, as depicted in the following two images.  Make sure you use the correct path to the php binary on your web server.
![alt tag](https://raw.github.com/ProHill/VRS-flights-db/master/images/putty1.png)
![alt tag](https://raw.github.com/ProHill/VRS-flights-db/master/images/putty2.png)

Save the profile with the name flightimport.

You will now need to use Windows Task Scheduler on your VRS host to run db_query.vbs every 5 minutes, as depicted in the following images.

![alt tag](https://raw.github.com/ProHill/VRS-flights-db/master/images/wintask1.png)
![alt tag](https://raw.github.com/ProHill/VRS-flights-db/master/images/wintask2.png)

That should complete the setup.  New flight records will be added to the flights table every 5 minutes, and the track log and MLAT flag will be merged in from the track_mlat_lookup table as part of the import process.

##Displaying Flight Data
(Work in Progress)

###flights.php
Displays a flight log with clickable links to the map page

###map.php
Displays the route and full track log
- Usage: http://webserver/flights/map.php?id=123456
  - Where 123456 is the flight's ID number from the database

###search.php
Search the database based on flight/aircraft details
