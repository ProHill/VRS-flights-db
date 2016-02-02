<?php
function writeFlightTable($result) {
    echo "<table>";
    echo "<thead><tr><th class=\"FlightTable\">Flight ID</th><th class=\"FlightTable\">Mode S Code</th><th class=\"FlightTable\">Registration </th><th class=\"FlightTable\">Aircraft</th><th class=\"FlightTable\">Airline/Operator</th><th class=\"FlightTable\">Callsign </th><th class=\"FlightTable\">Route</th><th class=\"FlightTable\">Last Seen</th></tr></thead>";
    echo "<tbody>";
    while($row = $result->fetch_assoc()) {
        echo '<tr class = "flightTable" onclick=\'document.location = "map.php?id=' . $row["ID"] . '";\'><td>';
        echo '<a href="map.php?id=' . $row["ID"] . '" title="Click for flight details">' . $row["ID"] . '</a>';
	            		
		echo '</td><td><a href="search.php?ModeS=' . $row["ModeS"] . '" title="Search for this ICAO code" style="font-family: Source Code Pro, monospace;">' . $row["ModeS"] . '</a>';
		if ($row["Mlat"] == 1) {
			// MLAT icon
			echo '<img alt="MLAT" src="MLAT.png" title="MLAT"/>';	
			}
		elseif ($row["NumPositionReports"] >= 1) {
			// ADS-B icon
			echo '<img alt="ADS-B" src="ADSB.png" title="ADS-B"/>';
			}
		else {
			// Mode-S icon
			echo '<img alt="ModeS" src="Mode-S.png" title="Mode-S"/>';
			}
		
		echo '</td><td>';		
        if ($row["Registration"] != "") { 
           	echo '<a href="search.php?Registration='.urlencode($row["Registration"]).'" title="Search for this registration">'.$row["Registration"].'</a> <a onclick="stopPropagation(event)" href="http://flightaware.com/live/flight/'.$row["Registration"].'" target="_blank" title="Search on FlightAware" class="fa"><img src="fasource.gif"></a>';
            }
                        
        echo "</td><td>".$row["AircraftModel"];
        if ($row["ModelCode"] != "") {
            echo " (".$row["ModelCode"].")" . "</td><td>";
            }
        else {
            echo "</td><td>";
            }
        echo htmlspecialchars($row["Operator"]);
       
        echo "</td><td>";
        
        if ($row["Callsign"] != "") {
			echo '<a href="search.php?q='.urlencode($row["Callsign"]).'" title="Search for this callsign">'.$row["Callsign"].'</a> <a onclick="stopPropagation(event)" href="http://flightaware.com/live/flight/'.$row["Callsign"].'" target="_blank" title="Search on FlightAware" class="fa"><img src="fasource.gif"></a>';
			}

        // Route
        if ($row["FromIATA"] != "") {
        	echo '</td><td class="route" title="' . $row["FromName"] . ', ' . $row["FromLocation"] . ', ' . $row["FromCountry"] . ' to ' . $row["ToName"] . ', ' . $row["ToLocation"] . ', ' . $row["ToCountry"] . '"><abbr class="route"><a href="map.php?id=' . $row["ID"] . '">' . $row["FromIATA"] . ' to ' . $row["ToIATA"] . '</a></abbr>';
        	}
        else echo "</td><td>";
        echo "</td><td class=\"LastSeen\"><abbr class=\"timeago\" title=\"".$row["LastSeenAgo"]."\">".$row["LastSeenAgo"]."</abbr></td></tr>";
    }
    echo "</tbody></table>";

} // End writeFlightTable function
?>