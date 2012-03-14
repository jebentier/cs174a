<?php
	include('includes/db_connect.php');
	include('includes/user_functions.php');

	session_start();  
	$deviceID = $_GET['deviceID'];

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

	<!--  USER'S ACCOUNTS LIST  -->
	<table border="1">
		<tr>
    		<td colspan=4 style="text-align:center;"><h3>Your Accounts</h3></td>
    	</tr>
		<tr>
			<th>Account ID</th>
			<th>Balance</th>
			<th>Privilege</th>
			<th>Account Status</th>
		</tr>
		<?
		$stid = getUserAccounts($username);
		while ($accounts = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $accounts['ACCOUNTID'] . "</td>\n";
        	echo "<td>" . number_format($accounts['BALANCE'], 2, '.', '') . "</td>\n";
        	echo "<td>" . $accounts['PRIVILEGE'] . "</td>\n";
        	echo "<td>" . $accounts['STATUS'] . "</td>\n";
    		echo "</tr>";
		}
		?>
	</table><br/><br/>


	<!--  CREATE RESERVATION  -->
	<form action="reserve.php" method="post">
    	<table border="1">
    		<tr>
    	  		<td colspan=5 style="text-align:center;">Create Reservation (Unix Time Format)</td>
    	  	</tr>
    		<tr>
    			<th></th>
    			<th>Month</th>
    			<th>Day</th>
    			<th>Year</th>
    			<th>Hour</th>
    		</tr>
    	  	<tr>
    	  		<td><label>Reservation Start: </label></td>
    	  		<td><input type="text" name="start_month" placeholder="1" /></td>
    	  		<td><input type="text" name="start_day" placeholder="1" /></td>
    	  		<td><input type="text" name="start_year" placeholder="2012" /></td>
    	  		<td><input type="text" name="start_hour" placeholder="12" /></td>
    	  	</tr>
    	  	<tr>
    	  		<td><label>Reservation End: </label></td>
    	  		<td><input type="text" name="end_month" placeholder="12" /></td>
    	  		<td><input type="text" name="end_day" placeholder="31" /></td>
    	  		<td><input type="text" name="end_year" placeholder="2012" /></td>
    	  		<td><input type="text" name="end_hour" placeholder="12" /></td>
    	  	</tr>
    	  	<tr>
    	  		<td colspan=4 style="text-align:right;"><label>Account to Charge: </label></td>
    	  		<td style="text-align:right;"><input type="text" name="accountID" placeholder="0000"/><input type="submit"/></td>
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
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>".$a['RS_M']."-".$a['RS_D']."-".$a['RS_Y']." : ".$a['RS_H']."</td>\n";
        	echo "<td>".$a['RE_M']."-".$a['RE_D']."-".$a['RE_Y']." : ".$a['RE_H']."</td>\n";
    		echo "</tr>";
		}
		?>
	</table><br/>
<??>