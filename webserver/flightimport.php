<?php
include 'config.php';
// Edit the following line as required
$csvfile = "/srv/www/htdocs/flights/flights.csv";
// End edits

$fieldseparator = "|";
$lineseparator = "\n";

if(!file_exists($csvfile)) {
die("File not found. Make sure you specified the correct path.");
}
try {
$pdo = new PDO("mysql:host=$databasehost;dbname=$databasename",
$databaseusername, $databasepassword,
array(
	PDO::MYSQL_ATTR_LOCAL_INFILE => true,
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	)
);
} catch (PDOException $e) {
die("database connection failed: ".$e->getMessage());
}
$affectedRows = $pdo->exec("
LOAD DATA LOCAL INFILE ".$pdo->quote($csvfile)." IGNORE INTO TABLE $flightsdatabasetable
FIELDS TERMINATED BY ".$pdo->quote($fieldseparator)."
ESCAPED BY '' 
LINES TERMINATED BY ".$pdo->quote($lineseparator));
date_default_timezone_set('America/Chicago');
$date = date('m/d/Y h:i:s a');
echo "Loaded a total of $affectedRows records from this csv file at $date\n";

$stmt = "SELECT * FROM $flightsdatabasetable ORDER BY LastSeen DESC LIMIT " . $affectedRows;

$conn = new mysqli($databasehost, $databaseusername, $databasepassword, $databasename);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

$result = $conn->query($stmt);

//ob_start();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
		$ModeS = $row["ModeS"];
				
		// Check if the flight's positions are MLAT-derived
		// Also grab the flight's full track log, if there is one
		$mlat_stmt = 'SELECT Icao, Mlat, Track from ' . $lookup_table . ' WHERE Icao = "' . $ModeS . '" AND Timestamp >= NOW() - INTERVAL 2 HOUR ORDER BY Timestamp DESC LIMIT 1';
		$mlat_result = $conn->query($mlat_stmt);
		if ($mlat_result->num_rows > 0) {
			while ($mlat_row = $mlat_result->fetch_assoc()) {
								
				// Override MLAT flag to true if NumPositionReports is 0 but there is a track
				if (($row["NumPositionReports"] == 0) && (!empty($mlat_row["Track"]))) {
					$mlat_row["Mlat"] = 1;
					}
				// Add logic here to deal with multiple tracks in the Track field in the mlat_lookup table
				$Trackarray = explode('|', $mlat_row["Track"]);
				if (count($Trackarray) > 1)  {
					$mlat_row["Track"] = str_replace("]|[", ",", $mlat_row["Track"]);
					}
				$mlat_update_stmt = 'UPDATE ' . $flightsdatabasetable . ' SET Mlat = ' . $mlat_row["Mlat"] . ', Track = "' . $mlat_row["Track"] . '" WHERE ModeS = "' . $mlat_row["Icao"] . '" AND LastSeen >= NOW() - INTERVAL 1 HOUR';
				$mlat_update_result = $conn->query($mlat_update_stmt);
				}
			}
    	}
    }
//ob_end_clean();    
$conn->close();

?>