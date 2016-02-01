<?php
include 'config.php';
include 'functions.php';
echo '
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="//code.jquery.com/jquery.min.js"></script>
<script type="text/javascript" src="jquery.timeago.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("abbr.timeago").timeago();
	});
</script>

<meta content="IE=edge" http-equiv="X-UA-Compatible" />
<title>Latest Flights</title>
</head>
<body class="page-latest">
	<section class="container main-content">';

echo '<div class="info column"><h1>Flight Log</h1></div>
<div class="table column">'; 

//check if the starting row variable was passed in the URL or not
if (!isset($_GET['limit'])) {
	if (isset($_GET['recordcount'])) {
		$limit = "0," . $_GET['recordcount'];
		}
	else {
	    //we give the value of the starting row 0 because nothing was found in URL
    	$limit = "0,100";
    	}
	}
else {
	$limit = $_GET['limit'];
    }

$conn = new mysqli($databasehost, $databaseusername, $databasepassword, $databasename);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

echo '<p></p>';
list($offsetzero, $recordcount) = explode(",", $limit);
$offset=$offsetzero+1;

// Display navigation links
// Only print a previous link if a Next was clicked
$prev = $offsetzero - $recordcount;
echo '<div class="pagination">';
if ($prev >=0)
    echo '<a href="'.$_SERVER['PHP_SELF'].'?limit='.$prev.','.($recordcount).'">«Previous Page</a>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?limit='.($offsetzero+$recordcount).','.($recordcount).'">Next Page»</a>';
echo "</div>";

$stmt = "SELECT ID, ModeS, Country, Registration, AircraftModel, ModelCode, Operator, OperatorCode, Callsign, FromIATA, FromICAO, FromName, FromLocation, FromCountry, ToIATA, ToICAO, ToName, ToLocation, ToCountry, NumPositionReports, Mlat, DATE_FORMAT(`LastSeen`, '%Y-%m-%dT%H:%i:%s') as LastSeenAgo FROM $flightsdatabasetable ORDER by LastSeen DESC LIMIT $limit"; 

$result = $conn->query($stmt);
	echo '<div class="info">Flights ' . $offset . ' to ' . ($recordcount+$offsetzero) . 
 	'	<form action="' . $_SERVER['PHP_SELF'] . '" method="get" class="limit">
       		  	<div class="limit"><label class="limit">Number of Results per Page</label> 
    		    <select name="recordcount" id="limit" onchange="this.form.submit()">
    		    <option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="150">150</option><option value="200">200</option><option value="250">250</option><option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option><option value="800">800</option><option value="900">900</option><option value="1000">1000</option></select></div>
				<script type="text/javascript">document.getElementById("limit").value = "' . $recordcount . '";</script>
    		  </form></div>';

if ($result->num_rows > 0) {
    echo "<div class=\"table-responsive\">";

	// *** Call function to write the flight table ***
	writeFlightTable($result);
	echo "</div>";
    // Display navigation links
    echo '<div class="pagination">';

	if ($prev >=0) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?limit='.$prev.','.($recordcount).'">«Previous Page</a>';
		}
	echo '<a href="'.$_SERVER['PHP_SELF'].'?limit='.($offsetzero+$recordcount).','.($recordcount).'">Next Page»</a>';
	echo "</div>";

} else {
    echo "0 results";
}
$conn->close();
echo '</div></section>';
echo '</body></html>';
?>