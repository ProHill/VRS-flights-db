<?php
include 'functions.php';
include 'config.php';
echo '
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="//code.jquery.com/jquery.min.js"></script>
<script type="text/javascript" src="jquery.timeago.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("abbr.timeago").timeago();
	});
function stopPropagation(e) {
	if(!e) var e = window.event;
	    if (e.stopPropagation) {
       		e.stopPropagation();
	    } else {
    		e.cancelBubble = true;
			e.returnValue = false;
	    }
	}
</script>
<style>
  .custom-combobox {
    position: relative;
    display: inline-block;
  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
  }
  .custom-combobox-input {
    margin-left: -3px;
    padding: 5px 10px;
  }
  .ui-autocomplete-input
  {
    width: 317px;
  }
  .ui-state-default {
    background: #ffffff;
  }
  .ui-corner-all, .ui-corner-bottom, .ui-corner-left, .ui-corner-bl {
    border-bottom-left-radius: 0px;
  }
  .ui-corner-all, .ui-corner-top, .ui-corner-left, .ui-corner-tl {
    border-top-left-radius: 0px;
  }
  .ui-corner-all, .ui-corner-bottom, .ui-corner-right, .ui-corner-br {
    border-bottom-right-radius: 0px;
  }
  .ui-corner-all, .ui-corner-top, .ui-corner-right, .ui-corner-tr {
    border-top-right-radius: 0px;
  }
  </style>
<meta content="IE=edge" http-equiv="X-UA-Compatible" />
<title>Search Flights</title>
</head>
<body>';            

//check if the starting row variable was passed in the URL or not
if (!isset($_GET['limit'])) {
    //we give the value of the starting row to 0 because nothing was found in URL
    $limit = "0,25";
    //otherwise we take the value from the URL
    } 
else {
	$limit = $_GET['limit'];
    }
$conn = new mysqli($databasehost, $databaseusername, $databasepassword, $databasename);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

// Build where clause
$querystring = '';
$searchquery = '';
$sqlwhere = '1 = 1 ';
$q = '';
$AircraftModel = '';
$ModeS = '';
$Registration = '';
$Operator = '';
$Callsign = '';
$FromAirport = '';
$ToAirport = '';

$modes_distinct = "false";
if(isset($_GET['modes_distinct'])){
	$modes_distinct = "true";
	}
	
if(isset($_GET['q'])){ 
	if(preg_match("/^[A-Za-z0-9].*/", $_GET['q'])){ 
   		$q=$_GET['q'];
   		$sqlwhere .= 'AND (Operator LIKE "%' . $q . '%" OR AircraftModel LIKE "%' . $q . '%" 
		OR Callsign LIKE "%' . $q . '%" OR Country LIKE "%' . $q . '%" OR FromIATA LIKE "%' . $q . '%" 
		OR FromName LIKE "%' . $q . '%" OR FromLocation LIKE "%' . $q . '%" 
		OR ToIATA LIKE "%' . $q . '%" OR ToName LIKE "%' . $q . '%" OR ToLocation LIKE "%' . $q . '%" OR ModeS LIKE "%' . $q . '%") ';
		} 
	else {
		$q="";
		}
	} 

if(isset($_GET['AircraftModel'])){ 
	if(preg_match("/^[A-Za-z0-9].*/", $_GET['AircraftModel'])){ 
   		$AircraftModel=$_GET['AircraftModel']; 
   		$sqlwhere .= 'AND AircraftModel LIKE "%' . $AircraftModel . '%" ';
		} 
	else {
		$AircraftModel="";
   		}
	} 

if(isset($_GET['Callsign'])){ 
	if(preg_match("/^[A-Za-z0-9].*/", $_GET['Callsign'])){ 
   		$Callsign=$_GET['Callsign']; 
   		$sqlwhere .= 'AND Callsign LIKE "%' . $Callsign . '%" ';	
		}
	else {
		$Callsign="";
		}
	} 

if(isset($_GET['ModeS'])){ 
	if(preg_match("/^[A-Za-z0-9].*/", $_GET['ModeS'])){ 
   		$ModeS=$_GET['ModeS']; 
	   	$sqlwhere .= 'AND ModeS = "' . $ModeS . '" ';
		}
	else {
		$ModeS="";
		}
	} 

if(isset($_GET['Registration'])){ 
	if(preg_match("/^[A-Za-z0-9].*/", $_GET['Registration'])){ 
   		$Registration=$_GET['Registration']; 
   		$sqlwhere .= 'AND Registration LIKE "%' . $Registration . '%" ';
		} 
	else {
		$Registration = "";
		}
	} 

if(isset($_GET['Operator'])){ 
	if(preg_match("/^[A-Za-z0-9].*/", $_GET['Operator'])){ 
	   	$Operator=$_GET['Operator']; 
		$sqlwhere .= 'AND Operator LIKE "%' . $Operator . '%" ';
		} 
	else {
		$Operator="";
		} 
	}

if(isset($_GET['FromAirport'])){ 
	if(preg_match("/^[A-Za-z0-9].*/", $_GET['FromAirport'])){ 
	   	$FromAirport=$_GET['FromAirport']; 
		$sqlwhere .= 'AND FromIATA LIKE "%' . $FromAirport . '%" ';
		} 
	else {
		$FromAirport="";
		} 
	}

if(isset($_GET['ToAirport'])){ 
	if(preg_match("/^[A-Za-z0-9].*/", $_GET['ToAirport'])){ 
	   	$ToAirport=$_GET['ToAirport']; 
		$sqlwhere .= 'AND ToIATA LIKE "%' . $ToAirport . '%" ';
		} 
	else {
		$ToAirport="";
		} 
	}
	
// There is something on the query string - build the query
if ($sqlwhere != "1 = 1 ") {
	$searchquery = "SELECT ID, ModeS, Country, Registration, AircraftModel, ModelCode, Operator, OperatorCode, Callsign, FromIATA, FromICAO, FromName, FromLocation, FromCountry, ToIATA, ToICAO, ToName, ToLocation, ToCountry, NumPositionReports, Interesting, Mlat,
		   DATE_FORMAT(`LastSeen`, '%Y-%m-%dT%H:%i:%s') as LastSeenAgo
		   FROM $flightsdatabasetable WHERE ";
	$searchquery .= $sqlwhere;
	$countquery = "SELECT COUNT(*) AS flightcount FROM $flightsdatabasetable WHERE ";
	$countquery .= $sqlwhere;
	if ($modes_distinct == "true") {
		$searchquery .= " GROUP BY ModeS DESC ";
		$countquery .= " GROUP BY ModeS ";
		}
	$searchquery .= "ORDER by LastSeen DESC LIMIT $limit";
	}

list($offsetzero, $recordcount) = explode(",", $limit);
$offset=$offsetzero+1;

echo '
<body class="page-latest">
<section class="container main-content">';
if ($searchquery != "") {
	echo '<div class="info column"><h1>Search Results</h1></div>';
	}
echo '<div class="table column">';

if ($searchquery != "") {
	$result = $conn->query($searchquery);
	$countresult = $conn->query($countquery);
	$countresultrow = $countresult->fetch_assoc();
	$resultcount = $countresultrow["flightcount"];

	// If using distinct, count the rows differently
	if ($modes_distinct == "true") {
		$resultcount = mysqli_num_rows($countresult);
		}
		
	// Display navigation links
	// Only print a previous link if a Next was clicked
	$prev = $offsetzero - $recordcount;
	echo '<div class="pagination">';
	// Rewrite query string
	parse_str($_SERVER['QUERY_STRING'], $result_array);
	unset($result_array['limit']);
	$freshquerystring = http_build_query($result_array);

	if ($prev >=0)
		echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$freshquerystring.'&limit='.$prev.','.($recordcount).'">«Previous Page</a>';
	if ($resultcount > ($recordcount + $offsetzero)) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$freshquerystring.'&limit='.($offsetzero+$recordcount).','.($recordcount).'">Next Page»</a>';
		}
	echo "</div>";

	echo '	<div class="info">';
	echo 'results = <span>' . $resultcount . '</span>';
	if ($q != "") {
		echo 'keyword = <span>' . $q . '</span>';
		}
	if ($ModeS != "") {
		echo 'ModeS = <span>' . $ModeS . '</span>';
		}				
	if ($AircraftModel != "") {
		echo 'aircraft = <span>' . $AircraftModel . '</span>';
		}
	if ($Operator != "") {
		echo 'operator = <span>' . $Operator . '</span>';
		}
	if ($Callsign != "") {
		echo 'callsign = <span>' . $Callsign . '</span>';
		}
	if ($Registration != "") {
		echo 'registration = <span>' . $Registration . '</span>';
		}
	if ($FromAirport != "") {
		echo 'from = <span>' . $FromAirport . '</span>';
		}
	if ($ToAirport != "") {
		echo 'to = <span>' . $ToAirport . '</span>';
		}								
	echo 'distinct airframes = <span>' . $modes_distinct . '</span>
	limit per page = <span>' . $recordcount . '</div>';

	if ($result->num_rows > 0) {
		echo "<div class=\"table-responsive\">";

		// Call function to write the flight table
		writeFlightTable($result, $conn);
		echo "</div>";
		// Display navigation links
		echo '<div class="pagination">';
		echo '<p>';
		if ($prev >=0)
			echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$freshquerystring.'&limit='.$prev.','.($recordcount).'">«Previous Page</a>';
		if ($resultcount > ($recordcount + $offsetzero)) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$freshquerystring.'&limit='.($offsetzero+$recordcount).','.($recordcount).'">Next Page»</a>';
			}
		echo "</div>";
		} 
	else {
		echo "0 results";
		} 
	} 
echo '<div class="info column"><h1>Search</h1></div>
<div class="column">
    <form action="search.php" method="get">
	   <fieldset><div>
           <label>Keyword</label>';
           if ($q != "") {
           		echo '<input type="text" id="q" name="q" size="10" value="' . $q . '" />';
           }
           else {
           		echo '<input type="text" id="q" name="q" size="10">';
           }
           echo '  </div></fieldset>';

// Populate AircraftModel field
$ModelQuery = "select distinct AircraftModel from $flightsdatabasetable order by AircraftModel ASC";
$ModelResult = $conn->query($ModelQuery);
$ModelOptions = "";
while($ModelRow = $ModelResult->fetch_assoc()) {
	if ($AircraftModel == $ModelRow["AircraftModel"]) {
		$ModelOptions .= '<option label=" " selected>' . htmlspecialchars($ModelRow["AircraftModel"]) . '</option>';
		}
	else {
		$ModelOptions .="<option>" . htmlspecialchars($ModelRow["AircraftModel"]) . "</option>";
		}
	}
$ModelMenu = '<div class="advanced-form">
        		<fieldset>
        		<!--<legend>Aircraft</legend>-->
    	    	<label>Model</label> 
    		    <select name="AircraftModel" class="selectpicker" id="AircraftModel" data-live-search="true">'
    		    . $ModelOptions . '</select></fieldset></div>';

echo $ModelMenu;

// ModeS, Callsign, and Registration fields
echo '   <div>
    		<label>Mode S Code</label>';
			if ($ModeS != "") {
           		echo '<input type="text" id="ModeS" name="ModeS" size="10" value="' . $ModeS . '" />';
           	}
           	else {
           		echo '<input type="text" name="ModeS" value="" size="8" />';
           	}

echo '</div><p><div>	<label>Callsign</label>';
			if ($Callsign != "") {
           		echo '<input type="text" id="Callsign" name="Callsign" size="8" value="' . $Callsign . '" />';
           	}
           	else {
           		echo '<input type="text" name="Callsign" value="" size="8" />';
           	}

echo '</div><p><div>	<label>Registration</label>';
			if ($Registration != "") {
           		echo '<input type="text" id="Registration" name="Registration" size="10" value="' . $Registration . '" />';
           	}
           	else {
           		echo '<input type="text" name="Registration" value="" size="8" />';
           	}
			echo '</div><p>';

// Populate Operator field
$OperatorQuery = "select distinct Operator from $flightsdatabasetable order by Operator ASC";
$OperatorResult = $conn->query($OperatorQuery);
$OperatorOptions = "";
while($OperatorRow = $OperatorResult->fetch_assoc()) {
	if ($Operator == $OperatorRow["Operator"]) {
		$OperatorOptions .= '<option label=" " selected>' . htmlspecialchars($OperatorRow["Operator"]) . '</option>';
		}
	else {
		$OperatorOptions .= '<option>' . htmlspecialchars($OperatorRow["Operator"]) . '</option>';
		}
	}
$OperatorMenu = '<fieldset>
        	  <!--<legend>Airline</legend>-->
    		  <div>
    		  	<label>Airline or Operator</label> 
    		    <select name="Operator" class="selectpicker" id="Operator" data-live-search="true">
    		      ' . $OperatorOptions . '</select></div></fieldset>';

echo $OperatorMenu;    		      

echo '<fieldset><div>
	<label>Origin</label>';
			if ($FromAirport != "") {
           		echo '<input type="text" class="Airport" id="FromAirport" name="FromAirport" size="10" value="' . $FromAirport . '" /></div>';
           	}
           	else {
           		echo '<input type="text" class="Airport" id="FromAirport" name="FromAirport" value="" size="8" /></div>';
           	}
echo '<p><div><label>Destination</label>';
			if ($ToAirport != "") {
           		echo '<input type="text" class="Airport" id="ToAirport" name="ToAirport" size="10" value="' . $ToAirport . '" />';
           	}
           	else {
           		echo '<input type="text" class="Airport" id="ToAirport" name="ToAirport" value="" size="8" />';
           	}
echo '</div></fieldset>';
           	
echo '<fieldset>
        	<!--<legend>Limit per Page</legend>-->
    		  <div>
    		  	<label>Number of Results per Page</label> 
    		    <select name="limit" id="limit">
    		    <option value="0,25">25</option><option value="0,50">50</option><option value="0,100">100</option><option value="0,150">150</option><option value="0,200">200</option><option value="0,250">250</option><option value="300">300</option><option value="400">400</option><option value="0,500">500</option><option value="0,600">600</option><option value="0,700">700</option><option value="0,800">800</option><option value="0,900">900</option><option value="0,1000">1000</option>    		      </select>
				<script type="text/javascript">document.getElementById("limit").value = "' . $limit . '";</script>
    		  </div>
              <div>';
              if ($modes_distinct == "true") {
              	echo '<input type="checkbox" name="modes_distinct" id="modes_distinct" checked value="true"  /> <label for="modes_distinct">Display Only Distinct Airframes</label>';
              	}
              else {
              	echo '<input type="checkbox" name="modes_distinct" id="modes_distinct" value="true"  /> <label for="modes_distinct">Display Only Distinct Airframes</label>';
              	}
    			echo '</div>
        </fieldset>';


// Search Button
echo '<fieldset>
		<div>
		   <input type="submit" value="Search" />
		</div>
	  </fieldset></form>';

$conn->close();
echo '</div></section>';
echo '<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>';
echo '<script>
(function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn\'t match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
 
  $(function() {
    $( "#AircraftModel" ).combobox();
    $( "#Operator" ).combobox();
  });
</script>';
echo '</body></html>';
?>
