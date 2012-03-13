<?php
	include('includes/db_connect.php');
	include('includes/user_functions.php');

	session_start();  
	$deviceID = $_GET['deviceID'];

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
?>

	<!--  FULL DEVICE INFO  -->
	<table border="1">
		<tr>
    		<td colspan=10 style="text-align:center;">Device Info</td>
    	</tr>
		<tr>
			<th>Device ID</th>
			<th>Name</th>
			<th>Type</th>
			<th>Year</th>
			<th>Current Availability</th>
			<th>Unit</th>
			<th>Cost</th>
			<th>Max Use</th>
			<th>Over Use</th>
			<th>Manager</th>
		</tr>
		<?
		$stid = getDeviceInfo($deviceID);
		while ($devices = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $devices['DEVICEID'] . "</td>\n";
        	echo "<td>" . $devices['NAME'] . "</td>\n";
        	echo "<td>" . $devices['TYPE'] . "</td>\n";
        	echo "<td>" . $devices['YEAR'] . "</td>\n";
        	echo "<td>" . $devices['AVAILABILITY'] . "</td>\n";
        	echo "<td>" . $devices['UNIT'] . "</td>\n";
        	echo "<td>" . $devices['COST'] . "</td>\n";
        	echo "<td>" . $devices['MAXUSE'] . "</td>\n";
        	echo "<td>" . $devices['OVERUSE'] . "</td>\n";
        	echo "<td>" . $devices['MANAGER'] . "</td>\n";
    		echo "</tr>";
    		$type = $devices['TYPE'];
		}
		?>
	</table><br/><br/><br/>

	<? 
		if(strcmp($type, 'printer') == 0){
			echo "<a href=\"checkout.php?deviceID=".$deviceID."&type=printer\">Link to Printer Usage</a>";
			exit(0);
		}	
	?>
	<!--  CREATE RESERVATION  -->
	<form action="reserve.php" method="post">
    	<table border="1">
    		<tr>
    	  		<td colspan=6 style="text-align:center;">Create Reservation (Unix Time Format)</td>
    	  	</tr>
    		<tr>
    			<th></th>
    			<th>Hour</th>
    			<th>Minute</th>
    			<th>Month</th>
    			<th>Day</th>
    			<th>Year</th>
    		</tr>
    	  	<tr>
    	  		<td><label for="start">Reservation Start: </label></td>
    	  		<td><input type="text" name="start_hour" placeholder="12" /></td>
    	  		<td><input type="text" name="start_minute" placeholder="00" /></td>
    	  		<td><input type="text" name="start_month" placeholder="1" /></td>
    	  		<td><input type="text" name="start_day" placeholder="1" /></td>
    	  		<td><input type="text" name="start_year" placeholder="2012" /></td>
    	  	</tr>
    	  	<tr>
    	  		<td><label for="end">Reservation End: </label></td>
    	  		<td><input type="text" name="end_hour" placeholder="12" /></td>
    	  		<td><input type="text" name="end_minute" placeholder="00" /></td>
    	  		<td><input type="text" name="end_month" placeholder="12" /></td>
    	  		<td><input type="text" name="end_day" placeholder="31" /></td>
    	  		<td><input type="text" name="end_year" placeholder="2012" /></td>
    	  	</tr>
    	  	<tr>
    	  		<td colspan=6 style="text-align:right;"><input type="submit"/></td>
    	  	</tr>
    	</table>
    	<input type="hidden" name="deviceID" value="<?= $deviceID;?>"/>
    	<input type="hidden" name="action" value="create"/>
    </form><br/>

	<!--  UPCOMING DEVICE RESERVATIONS  -->
	<table border="1">
		<tr>
    	  	<td colspan=2 style="text-align:center;">Upcoming Reservations</td>
    	</tr>
		<tr>
			<th>Reservation Start</th>
			<th>Reservation End</th>
		</tr>
		<?
		$stid = getReservationInfo($deviceID);
		while ($devices = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $devices['RESV_START'] . "</td>\n";
        	echo "<td>" . $devices['RESV_END'] . "</td>\n";
    		echo "</tr>";
		}
		?>
	</table><br/>
<??>