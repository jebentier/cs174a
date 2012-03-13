<?php
	include('includes/db_connect.php');
	include('includes/user_functions.php');
	session_start();
	

	/*** SETTING UP VARIABLES ***/
	$username = $_SESSION['username'];
 	$password = $_SESSION['password'];
	$pin = $_SESSION['pin'];
	$isManager = $_SESSION['isManager'];

	if(strcmp($isManager, 't') == 0){
		echo "<div>Signed in as ".$username."</div>";
		echo "<div class=\"header_links\">";
		echo "<a href=\"main.php\">Home</a>&nbsp;|&nbsp;";
		echo "<a href=\"admin.php\">Manager Page</a>&nbsp;|&nbsp;";
		echo "<a href=\"index.php\">Logout</a>";
		echo "<br/><br/></div>";
	}
	else{
		echo "<div>Signed in as ".$username."</div>";
		echo "<div class=\"header_links\">";
		echo "<a href=\"main.php\">Home</a>&nbsp;|&nbsp;";
		echo "<a href=\"index.php\">Logout</a>";
		echo "<br/></div>";
	}

	$start_hour = $_POST['start_hour'];
	$start_minute = $_POST['start_minute'];
	$start_month = $_POST['start_month'];
	$start_day = $_POST['start_day'];
	$start_year = $_POST['start_year'];

	$end_hour = $_POST['end_hour'];
	$end_minute = $_POST['end_minute'];
	$end_month = $_POST['end_month'];
	$end_day = $_POST['end_day'];
	$end_year = $_POST['end_year'];

	$deviceID =  $_POST['deviceID'];
	$action = $_POST['action'];

	if($start_hour == NULL) $invalid = true;
	if($start_minute == NULL) $invalid = true;
	if($start_month == NULL) $invalid = true;
	if($start_day == NULL) $invalid = true;
	if($start_year == NULL) $invalid = true;
	if($end_hour == NULL) $invalid = true;
	if($end_minute == NULL) $invalid = true;
	if($end_month == NULL) $invalid = true;
	if($end_day == NULL) $invalid = true;
	if($end_year == NULL) $invalid = true;

	if($invalid){
		echo "Invalid reservation input. <a href=\"main.php\">Return Home</a>";
		exit(0);
	}

	/*** ACTUAL LOGIC ***/

	$startTime = date('jmYG', mktime($start_hour, $start_minute, 0, $start_month, $start_day, $start_year));
	$endTime = date('jmYG', mktime($end_hour, $end_minute, 0, $end_month, $end_day, $end_year));

	if(strcmp($action, 'create') == 0){
		$result = reserveDevice($deviceID, $username, $startTime, $endTime);
		if($result) echo "Successfully created reservation. <a href=\"main.php\">Return Home</a>";
		else echo "Error creating reservation. Requested time overlaps with another reservation. <a href=\"main.php\">Return Home</a>";
	}
	else if(strcmp($action, 'search') == 0){
		echo " 
		<table border=\"1\">
		<tr>
    		<td colspan=11 style=\"text-align:center;\"><h3>Available for Reservations</h3></td>
    	</tr>
		<tr>
			<th>Device ID</th>
			<th>Device Name</th>
			<th>Type</th>
			<th>Year</th>
			<th>Current Availability</th>
			<th>Quick Checkout</th>
			<th>Usage Unit</th>
			<th>Cost per unit</th>
			<th>Max single use</th>
			<th>Overuse cost</th>
			<th>Device / Reservation Info</th>
		</tr>";

		$stid = getAvailableReservations($startTime, $endTime);
		while ($devices = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $devices['DEVICEID'] . "</td>\n";
        	echo "<td>" . $devices['NAME'] . "</td>\n";
        	echo "<td>" . $devices['TYPE'] . "</td>\n";
        	echo "<td>" . $devices['YEAR'] . "</td>\n";
        	echo "<td>" . $devices['AVAILABILITY'] . "</td>\n";

        	if( strcmp($devices['AVAILABILITY'], 'available') == 0 )
        		echo "<td><a href=\"checkout.php?deviceID=".$devices['DEVICEID']."\">Checkout</a></td>";
        	else echo "<td></td>";

        	echo "<td>" . $devices['UNIT'] . "</td>\n";
        	echo "<td>" . number_format($devices['COST'], 2, '.', '') . "</td>\n";
        	echo "<td>" . $devices['MAXUSE'] . "</td>\n";
        	echo "<td>" . number_format($devices['OVERUSE'], 2, '.', '') . "</td>\n";
        	if( strcmp($devices['AVAILABILITY'], 'available') == 0 )
        		echo "<td><a href=\"device_info.php?deviceID=".$devices['DEVICEID']."\">Link</a></td>";
        	else echo "<td></td>";
        	echo "</tr>";
		}
		echo "</table>";
	}

?>