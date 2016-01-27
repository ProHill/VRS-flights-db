<?php
include 'config.php';
$ch = curl_init();
$options = [
   	CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => TRUE,
    CURLOPT_VERBOSE => FALSE,
    CURLOPT_ENCODING => "gzip",
    CURLOPT_URL            => "http://$vrshostname:$vrsport/VirtualRadar/AircraftList.json?trFmt=f&refreshTrails=1"
	];
curl_setopt_array($ch, $options);
$data = curl_exec($ch);

$data = json_decode($data); 

try {
    $conn = new PDO("mysql:host=$databasehost;dbname=$databasename", $databaseusername, $databasepassword);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO $lookup_table (Id, Icao, Callsign, Registration, Mlat, Track, MultiTrack) VALUES (:Id, :Icao, :Callsign, :Registration, :Mlat, :Track, :MultiTrack) 
    		ON DUPLICATE KEY UPDATE Id = :Id, Icao = :Icao, Callsign = :Callsign, Registration = :Registration, Mlat = :Mlat, Track = :Track, MultiTrack = :MultiTrack";

	// Look for previous tracks in the past few minutes to address aircraft that drop out for a few minutes
	$tracklookupsql = "SELECT Track from $lookup_table where Id = :Id AND Timestamp >= NOW() - INTERVAL 23 MINUTE ORDER BY Timestamp DESC LIMIT 1";
	
    // Prepare statement
    $stmt = $conn->prepare($sql);
	$tracklookupstmt = $conn->prepare($tracklookupsql);
    
    $Id = null;
	$Icao = null;
	$Callsign = null;
	$Registration = null;
	$Mlat = null;
	$Track = null;
	$MultiTrack = null;

	$stmt->bindParam(':Id', $Id, PDO::PARAM_INT);
	$stmt->bindParam(':Icao', $Icao, PDO::PARAM_STR);
	$stmt->bindParam(':Callsign', $Callsign, PDO::PARAM_STR);
	$stmt->bindParam(':Registration', $Registration, PDO::PARAM_STR);
	$stmt->bindParam(':Mlat', $Mlat, PDO::PARAM_BOOL);
	$stmt->bindParam(':Track', $Track, PDO::PARAM_STR);
	$stmt->bindParam(':MultiTrack', $MultiTrack, PDO::PARAM_STR);
	$tracklookupstmt->bindParam(':Id', $Id, PDO::PARAM_INT);

	//$aircraftIDs = array();
	foreach($data->acList as $aircraft) {
		if ($aircraft->Id == 0) {
			continue;
			}
		$MultiTrack = 0;
		$Id = $aircraft->Id;
		//$aircraftIDs[] = $Id;
		$Icao = $aircraft->Icao;
		if (isset($aircraft->Call)) {
			$Callsign = $aircraft->Call;
			}
		else {
			$Callsign = null;
			}
		if (isset($aircraft->Reg)) {
			$Registration = $aircraft->Reg;
			}
		else {
			$Registration = null;
			}	
		if (isset($aircraft->Mlat)) {
			$Mlat = $aircraft->Mlat;
			}
		else {
			$Mlat = false;
			}
		if (isset($aircraft->Cot)) {
			$Track = json_encode($aircraft->Cot);
			// Put previous track lookup here
			$tracklookupresult = $tracklookupstmt->execute();
			$row = $tracklookupstmt->fetch();
			//echo $row[0];
			$storedTrack = $row[0];
			$Trackarray = explode('|', $storedTrack);

			if ((count($Trackarray) == 1) && $storedTrack != "") {
				//echo "Only 1 track" . PHP_EOL;
				if (substr($storedTrack, 1, 18) == substr($Track, 1, 18)) {
					// Same track, just overwrite it as before
					//echo "Same track - overwrite";					
					}
				else {
					// New track, need to append it
					//echo "New track - append" . PHP_EOL;
					$Track = $storedTrack . "|" . $Track;
					$MultiTrack = 1;
					}
				}
			elseif ((count($Trackarray) == 1) && $storedTrack == "") {
				if (substr($storedTrack, 1, 18) == substr($Track, 1, 18)) {
					// Same track, just overwrite it as before
					// echo "Brand new track" . PHP_EOL;					
					}
				}
	
			elseif (count($Trackarray) > 1) {
				$lastindex = count($Trackarray) - 1;
				if (substr($Trackarray[$lastindex], 1, 18) == substr($Track, 1, 18)) {
					//echo "Last Track matches - need to overwrite it" . PHP_EOL;
					$Trackarray[$lastindex] = $Track;
					$Track = implode("|", $Trackarray);
					$MultiTrack = 1;
					}
				else {
					//echo "New track, need to append another one" . PHP_EOL;
					array_push($Trackarray, $Track);
					$Track = implode("|", $Trackarray);
					$MultiTrack = 1;
					}	
				}	
			}
		else {
			$Track = null;
			}
		// execute the query
		//$stmt->debugDumpParams();
    	$stmt->execute();
		}

    // UPDATE succeeded
    echo "Records added successfully\n";
    }
catch(PDOException $e)
    {
    echo $sql . "<br>" . $e->getMessage();
    }

$conn = null;
?>