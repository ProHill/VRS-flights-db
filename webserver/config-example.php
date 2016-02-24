<?php
// Change the following lines to reflect your database information, 
// then rename this file to config.php
$databasehost = "dbhostname";
$databaseusername= "username";
$databasepassword = "password";
$databasename = "adsb";
$flightsdatabasetable = "flightstable";
$lookup_table = "track_mlat_lookup";
$csvfile = "/srv/www/htdocs/flights/flights.csv";
date_default_timezone_set('America/Chicago');
$vrshostname = "vrs.hostname.here";
$vrsport = "8080";
// If VRS is password protected, enter the credentials in the following 2 fields, otherwise leave blank
$vrsusername = "";
$vrspassword = "";
?>