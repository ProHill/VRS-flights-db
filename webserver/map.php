<?php
// Change the following 4 lines to reflect your database information
$servername = "dbhostname";
$username = "dbusername";
$password = "dbpassword";
$dbname = "adsb";

echo '
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css">
<script src="//code.jquery.com/jquery.min.js"></script>
<meta content="IE=edge" http-equiv="X-UA-Compatible" />
<title>Flight Map</title>

<script src="http://maps.googleapis.com/maps/api/js?libraries=geometry"></script>
<script>
var map_types = {
	vfrc: {
		max_zoom: 12,
		default_zoom: 10
	},
	tac: {
		max_zoom: 12,
		default_zoom: 10
	},
	sec: {
		max_zoom: 12,
		default_zoom: 10
	},
	wac: {
		max_zoom: 10,
		default_zoom: 9
	},
	enrl: {
		max_zoom: 11,
		default_zoom: 10
	},
	enrh: {
		max_zoom: 10,
		default_zoom: 9
	},
	"default": {
		default_zoom: 10,
		clean_zoom: 7
	}
};

function getMapTypeOption(a) {
	var b = GoogleMap.getMapTypeId();
	if (map_types[b] && map_types[b][a]) {
		return map_types[b][a]
	}
	else {
		return map_types["default"][a]
	}
}

function addMapTypes() {
	for (var b in map_types) {
		if (b == "default") {
			continue
		}
		var a = {
			minZoom: 4,
			maxZoom: map_types[b].max_zoom,
			name: b,
			tileSize: new google.maps.Size(256, 256),
			getTileUrl: (function(c) {
				return function(f, e) {
					var d = f.x % (1 << e);
					if (d < 0) {
						d = d + (1 << e)
					}
					var g = (1 << e) - f.y - 1;
					return "http://wms.chartbundle.com/tms/1.0.0/" + c + "/" + e + "/" + d + "/" + g + ".png"
				}
			})(b)
		};
		route_map.mapTypes.set(b, new google.maps.ImageMapType(a))
		track_map.mapTypes.set(b, new google.maps.ImageMapType(a))
	}
}

function changeMapType(gMap) {
	var b = document.getElementById("menu_pulldown");
	var a = b.options[b.selectedIndex].value;
	gMap.setMapTypeId(a);
	setStateCookie()
}

function changeMapType2(gMap) {
	var b = document.getElementById("menu_pulldown2");
	var a = b.options[b.selectedIndex].value;
	gMap.setMapTypeId(a);
	setStateCookie()
}


function showMapType(gMap) {
	var b = document.getElementById("menu_pulldown");
	for (var a = 0; a < b.options.length; a++) {
		if (b.options[a].value == gMap.getMapTypeId()) {
			b.options[a].selected = true;
			break
		}
	}
}

function showMapType2(gMap) {
	var b = document.getElementById("menu_pulldown2");
	for (var a = 0; a < b.options.length; a++) {
		if (b.options[a].value == gMap.getMapTypeId()) {
			b.options[a].selected = true;
			break
		}
	}
}

var route_map, track_map;

function initialize() {
	var l = "hybrid";

	var mapOptions = {
		zoom: 10,
		maxZoom: 12,
		disableDefaultUI: true
	};

	route_map = new google.maps.Map(document.getElementById("route-map"),
		mapOptions);

	var mapOptions = {
		zoom: 6,
		disableDefaultUI: true
	};

	track_map = new google.maps.Map(document.getElementById("track-map"),
		mapOptions);

	addMapTypes();
	route_map.setMapTypeId(l);
	track_map.setMapTypeId(l);
	showMapType(route_map);
	showMapType2(track_map);

	route_map.controls[google.maps.ControlPosition.TOP_RIGHT].push(
		document.getElementById("route_map_toolbar"));

	track_map.controls[google.maps.ControlPosition.TOP_RIGHT].push(
		document.getElementById("track_map_toolbar"));
}

function showLine(pointa, pointb, map_object, draw_markers) {
	var lineSymbol = {
		path: "M 0,-1 0,1",
		strokeOpacity: 1,
		strokeWeight: 2,
		scale: 5
	};
	var routeline = new google.maps.Polyline({
		path: [pointa, pointb],
		strokeColor: "#347AED",
		strokeOpacity: 0.3,
		icons: [{
			icon: lineSymbol,
			offset: "0",
			repeat: "15px"
		}],
		strokeWeight: 2,
		geodesic: true,
		map: map_object
	});

	if (draw_markers == true) {
		var marker1 = new google.maps.Marker({
			position: pointa,
			map: map_object,
			icon: "origin.png",
			title: "Start"
		});
		var marker2 = new google.maps.Marker({
			position: pointb,
			map: map_object,
			icon: "destination.png",
			title: "End"
		});
	}
}

function setBounds(pointa, pointb, map_object) {
	var bounds = new google.maps.LatLngBounds();

	bounds.extend(pointa);
	bounds.extend(pointb);

	map_object.fitBounds(bounds);
	map_object.panToBounds(bounds);
}

function showTrack(track, pointa, pointb, map_object) {
	var iconsetngs = {
		path: google.maps.SymbolPath.FORWARD_OPEN_ARROW
	};

	var line = new google.maps.Polyline({
		path: track,
		strokeColor: "#00FF00",
		strokeOpacity: 0.9,
		strokeWeight: 2,
		geodesic: true,
		map: map_object,
		icons: [{
			icon: iconsetngs,
			repeat: "100px",
			offset: "100%"
		}]
	});

	var marker1 = new google.maps.Marker({
		position: pointa,
		map: map_object,
		icon: "origin.png",
		title: "Start"
	});
	var marker2 = new google.maps.Marker({
		position: pointb,
		map: map_object,
		icon: "destination.png",
		title: "End"
	});
	var bounds = new google.maps.LatLngBounds();
	line.getPath().forEach(function(latLng) {
		bounds.extend(latLng);
	});
	track_map.fitBounds(bounds);
}

google.maps.event.addDomListenerOnce(window, "load", initialize);
</script>
</head>
<body class="page-latest">
	<section class="container main-content">';

$id       = '';
$sqlwhere = '';

if (isset($_GET['id'])) {
	if (preg_match("/^[0-9].*/", $_GET['id'])) {
		$id = $_GET['id'];
		$sqlwhere .= 'WHERE ID =' . $id;
	} else {
		$id = 0;
		$sqlwhere .= 'WHERE ID = ' . $id;
	}
} else {
	$id = 0;
	$sqlwhere .= 'WHERE ID = ' . $id;
}

$stmt           = "SELECT * FROM flights " . $sqlwhere;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query($stmt);

echo '<div class="info column"><h1>Flight Map</h1></div>
<div class="column"><div class="info">id = <span>' . $id . '</span></div>';

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	
	if ($row["Track"] != null) {
		$trackarray = json_decode($row["Track"]);
	}
	
	echo '<table><thead><tr><th class="RouteOverview">Route Overview</th><th class="TrackedRoute">Tracked Log</th></tr></thead>
	<tbody><tr><td class="RouteOverview">
	<div class="mapcontainer">
	<div id="route-map" style="position: relative; background-color: rgb(229, 227, 223); overflow: hidden; -webkit-transform: translateZ(0px);"></div></div>';
	// Display message if route map isn't available
	if ($row["FromLat"] == 0) {
		echo '<div class="mapmsg">Flight route could not be determined. Check FlightAware.</div>';
		echo '<script>$("#route-map").hide();</script>';
	} else { // Show the map drop-down menu
		echo '<script>window.onload = $(function () { $("#route_map_toolbar").show();});</script>';
	}
	echo '</td>
	<td class="TrackedRoute">
	<div class="mapcontainer">
	<div id="track-map" style="position: relative; background-color: rgb(229, 227, 223); overflow: hidden; -webkit-transform: translateZ(0px);"></div></div>';
	
	// Display message if track map isn't available
	if (($row["FirstLatitude"] == 0) || ($row["FirstLongitude"] == 0) || ($row["LastLatitude"] == 0) || ($row["LastLongitude"] == 0)) {
		if ($row["Track"] == null) {
			echo '<div class="mapmsg">Aircraft was not broadcasting its position.</div>';
			echo '<script>$("#track-map").hide();</script>';
		} else { // Show the map drop-down menu
			echo '<script>window.onload = $(function () { $("#track_map_toolbar").show();});</script>';
		}
	} else { // Show the map drop-down menu
		echo '<script>window.onload = $(function () { $("#track_map_toolbar").show();});</script>';
	}
	echo '</td></tr>
	<tr class="mapfooter"><td colspan="2"><em>
	<svg height="24" width="110">
	<g fill="none" stroke="#347AED" stroke-width="2">
	<path stroke-dasharray="10,5" d="M5 20 l100 0" />
	</g>
	</svg> Direct route 
	&nbsp;&nbsp;&nbsp;&nbsp; <svg height="24" width="110">
	<g fill="none">
	<path stroke="#00FF00" d="M5 20 l100 0" />
	</g>
	</svg> Actual route flown (if available)</em></td></tr></tbody></table></div>';
}

else {
	echo "0 results";
}
$conn->close();
echo '
    <div id="route_map_toolbar" style="display: none;">
      <form id="charts_form" class="charts_form" method="post">
        <select id="menu_pulldown" class="mapButton" onChange="changeMapType(route_map)">
          <optgroup label="VFR">
			<option selected value="vfrc">Hybrid VFR</option>
            <option value="sec">Sectional</option>
            <option value="tac">Terminal</option>
            <option value="wac">WAC</option>
          </optgroup>
          <optgroup label="IFR">
            <option value="enrl">Low IFR</option>
            <option value="enrh">High IFR</option>
          </optgroup>
          <optgroup label="Google">
            <option value="roadmap">Roadmap</option>
            <option value="hybrid">Satellite</option>
            <option value="terrain">Terrain</option>
          </optgroup>
        </select>
      </form>
    </div>
    
    <div id="track_map_toolbar" style="display: none;">
      <form id="charts_form2" class="charts_form" method="post">
        <select id="menu_pulldown2" class="mapButton" onChange="changeMapType2(track_map)">
          <optgroup label="VFR">
			<option selected value="vfrc">Hybrid VFR</option>
            <option value="sec">Sectional</option>
            <option value="tac">Terminal</option>
            <option value="wac">WAC</option>
          </optgroup>
          <optgroup label="IFR">
            <option value="enrl">Low IFR</option>
            <option value="enrh">High IFR</option>
          </optgroup>
          <optgroup label="Google">
            <option value="roadmap">Roadmap</option>
            <option value="hybrid">Satellite</option>
            <option value="terrain">Terrain</option>
          </optgroup>
        </select>
      </form>
    </div>
   </section>

<script>
window.onload = function() { ';
if ($trackarray != null) {
	// Discard the heading - we don't need it
	$size = count($trackarray);
	for ($i = 2; $i < $size; $i += 3) {
		unset($trackarray[$i]);
	}
	$trackarray = array_merge($trackarray);
	echo "var Track = [";
	foreach ($trackarray as $coord) {
		if ($coord > 0) {
			// Latitude
			echo "new google.maps.LatLng(" . $coord . ",";
		} elseif ($coord < 0) {
			// Longitude
			echo $coord . "), ";
		}
	}
	$trackFirstLatitude  = $trackarray[0];
	$trackFirstLongitude = $trackarray[1];
	$trackLastArray      = array_slice($trackarray, -2, 2, false);
	$trackLastLatitude   = $trackLastArray[0];
	$trackLastLongitude  = $trackLastArray[1];
	
	echo "];\n";
	
	echo 'showTrack(Track, new google.maps.LatLng(' . $trackFirstLatitude . ', ' . $trackFirstLongitude . '), new google.maps.LatLng(' . $trackLastLatitude . ', ' . $trackLastLongitude . '), track_map);';
	if (($row["FirstLatitude"] != 0) && ($row["FirstLongitude"] != 0) && ($row["LastLatitude"] != 0) && ($row["LastLongitude"] != 0)) {
		echo 'showLine(new google.maps.LatLng(' . $row["FirstLatitude"] . ', ' . $row["FirstLongitude"] . '), new google.maps.LatLng(' . $row["LastLatitude"] . ', ' . $row["LastLongitude"] . '), track_map, false); ';
	} else {
		echo 'showLine(new google.maps.LatLng(' . $trackFirstLatitude . ', ' . $trackFirstLongitude . '), new google.maps.LatLng(' . $trackLastLatitude . ', ' . $trackLastLongitude . '), track_map, false); ';
	}
}

elseif (($row["FirstLatitude"] != 0) && ($row["FirstLongitude"] != 0) && ($row["LastLatitude"] != 0) && ($row["LastLongitude"] != 0)) {
	echo 'showLine(new google.maps.LatLng(' . $row["FirstLatitude"] . ', ' . $row["FirstLongitude"] . '), new google.maps.LatLng(' . $row["LastLatitude"] . ', ' . $row["LastLongitude"] . '), track_map, true); ';
	echo 'setBounds(new google.maps.LatLng(' . $row["FirstLatitude"] . ', ' . $row["FirstLongitude"] . '), new google.maps.LatLng(' . $row["LastLatitude"] . ', ' . $row["LastLongitude"] . '), track_map); ';
}

if ($row["FromLat"] != 0) {
	echo 'showLine(new google.maps.LatLng(' . $row["FromLat"] . ', ' . $row["FromLong"] . '), new google.maps.LatLng(' . $row["ToLat"] . ', ' . $row["ToLong"] . '), route_map, true); ';
	echo 'setBounds(new google.maps.LatLng(' . $row["FromLat"] . ', ' . $row["FromLong"] . '), new google.maps.LatLng(' . $row["ToLat"] . ', ' . $row["ToLong"] . '), route_map); ';
}
echo ' } </script></body></html>';
?>
