<?php
	include('includes/db_connect.php');
	include('includes/user_functions.php');
	session_start();

	/*** SETTING UP VARIABLES ***/
	$username  = $_SESSION['username'];
 	$password  = $_SESSION['password'];
	$pin       = $_SESSION['pin'];
	$isManager = $_SESSION['isManager'];
	$month     = $_SESSION['month'];
	$day       = $_SESSION['day'];
	$year      = $_SESSION['year'];
	$hour      = $_SESSION['hour'];

	if(strcmp($isManager, 't') == 0){
		echo "<div>Signed in as ".$username."</div>";
		echo "<div>";
		echo "<a href=\"main.php\">Home</a>&nbsp;|&nbsp;";
		echo "<a href=\"admin.php\">Manager Page</a>&nbsp;|&nbsp;";
		echo "<a href=\"index.php\">Logout</a><br/><br/>";
		echo "</div>";
		echo "<div>System Date: ".$month."-".$day."-".$year."  Hour: ".$hour."</div>";
	}
	else{
		echo "<div>Signed in as ".$username."</div>";
		echo "<div>";
		echo "<a href=\"main.php\">Home</a>&nbsp;|&nbsp;";
		echo "<a href=\"index.php\">Logout</a><br/><br/>";
		echo "</div>";
		echo "<div>System Date: ".$month."-".$day."-".$year." | Hour: ".$hour."</div>";
	}

	$sh = $_POST['start_hour'];
	$sm = $_POST['start_month'];
	$sd = $_POST['start_day'];
	$sy = $_POST['start_year'];

	$eh = $_POST['end_hour'];
	$em = $_POST['end_month'];
	$ed = $_POST['end_day'];
	$ey = $_POST['end_year'];

	$deviceID  = $_POST['deviceID'];
	$accountID = $_POST['accountID'];
	$action    = $_POST['action'];

	if($sh == NULL) $invalid = true;
	if($sm == NULL) $invalid = true;
	if($sd == NULL) $invalid = true;
	if($sy == NULL) $invalid = true;
	if($eh == NULL) $invalid = true;
	if($em == NULL) $invalid = true;
	if($ed == NULL) $invalid = true;
	if($ey == NULL) $invalid = true;

	if($invalid){
		echo "Invalid reservation input. <a href=\"main.php\">Return Home</a>";
		exit(0);
	}

	/*** ACTUAL LOGIC ***/

	if(strcmp($action, 'create') == 0){
		$result = reserveDevice($deviceID, $username, $accountID, $sm, $sd, $sy, $sh, $em, $ed, $ey, $eh);
		if($result) echo "Successfully created reservation. <a href=\"main.php\">Return Home</a>";
		else echo "Error creating reservation. Requested time overlaps with another reservation. <a href=\"main.php\">Return Home</a>";
	}
	else if(strcmp($action, 'search') == 0){
		echo " 
		<table border=\"1\">
		<tr>
    		<td colspan=10 style=\"text-align:center;\"><h3>Available for Reservations</h3></td>
    	</tr>
		<tr>
			<th>Device ID</th>
			<th>Device Name</th>
			<th>Type</th>
			<th>Year</th>
			<th>Current Availability</th>
			<th>Usage Unit</th>
			<th>Cost per unit</th>
			<th>Max single use</th>
			<th>Overuse cost</th>
			<th>Device / Reservation Info</th>
		</tr>";

		$stid = getAvailableReservations($sm, $sd, $sy, $sh, $em, $ed, $ey, $eh);
		while ($devices = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $devices['DEVICEID'] . "</td>\n";
        	echo "<td>" . $devices['NAME'] . "</td>\n";
        	echo "<td>" . $devices['TYPE'] . "</td>\n";
        	echo "<td>" . $devices['YEAR'] . "</td>\n";
        	echo "<td>" . $devices['AVAILABILITY'] . "</td>\n";
        	echo "<td>" . $devices['UNIT'] . "</td>\n";
        	echo "<td>" . number_format($devices['COST'], 2, '.', '') . "</td>\n";
        	echo "<td>" . $devices['MAXUSE'] . "</td>\n";
        	echo "<td>" . number_format($devices['OVERUSE'], 2, '.', '') . "</td>\n";
        	if( strcmp($devices['AVAILABILITY'], 'no') != 0 )
        		echo "<td><a href=\"device_info.php?deviceID=".$devices['DEVICEID']."\">Link</a></td>";
        	else echo "<td></td>";
        	echo "</tr>";
		}
		echo "</table>";
	}

?>