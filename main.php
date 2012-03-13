<?php
	include('includes/db_connect.php');
	include('includes/user_functions.php');

	session_start(); 
	

	/*** LOGIN VALIDATION ***/

	if(!isset($_SESSION['username'])) $_SESSION['username'] = $_POST['username'];
	if(!isset($_SESSION['password'])) $_SESSION['password'] = $_POST['password'];
	if(!isset($_SESSION['pin'])) $_SESSION['pin'] = $_POST['pin'];

	if(isset($_SESSION['username'])) $username = $_SESSION['username'];
	if(isset($_SESSION['password'])) $password = $_SESSION['password'];
	if(isset($_SESSION['pin'])) $pin = $_SESSION['pin'];

	if( ($username!=NULL) && ($password!=NULL) ){
		$stid = login($username, $password);
		$user = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
		if($user['USERNAME'] == NULL){
			echo "Invalid credentials. <a href=\"index.php\">Go back to the login page.</a>";
			exit(0);
		}
		$isManager = $user['ISMANAGER'];
	}
	else if($pin != NULL){
		$stid = pinLogin($pin);
		$user = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
		if($user['USERNAME'] == NULL){
			echo "Invalid credentials. <a href=\"index.php\">Go back to the login page.</a>";
			exit(0);
		}
		$stid = getCredentials($pin);
		$user = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
		$username = $user['USERNAME'];
		$password = $user['PASSWORD'];
		$isManager = $user['ISMANAGER'];
		$_SESSION['username'] = $username;
		$_SESSION['password'] = $password;
		$_SESSION['pin'] = $pin;
	}
	else{
		echo "Invalid credentials. <a href=\"index.php\">Go back to the login page.</a>";
		exit(0);
	}

	if(!isset($_SESSION['isManager'])) $_SESSION['isManager'] = $isManager;

	/*** HEADER ***/

	if(strcmp($isManager, 't') == 0){
		echo "<div>Signed in as ".$username."</div>";
		echo "<div class=\"header_links\"><a href=\"admin.php\">Manager Page</a>&nbsp;|&nbsp;";
		echo "<a href=\"index.php\">Logout</a><br/><br/></div>";
	}
	else{
		echo "<div>Signed in as ".$username."</div>";
		echo "<div class=\"header_links\"><a href=\"index.php\">Logout</a><br/><br/></div>";
	}
	?>

	<h1>Devices</h1>
	<!--  ALL DEVICES LIST  -->
	<table border="1">
		<tr>
    		<td colspan=11 style="text-align:center;"><h3>Device List</h3></td>
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
		</tr>
		<?
		$stid = getDeviceList();
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

        	if( strcmp($devices['AVAILABILITY'], 'available') == 0 )
        		echo "<td><a href=\"device_info.php?deviceID=".$devices['DEVICEID']."\">Link</a></td>";
        	else echo "<td></td>";
        	
    		echo "</tr>";
		}
		?>
	</table><br/>

	<!--  SEARCH RESERVATIONS  -->
	<form action="reserve.php" method="post">
    	<table border="1">
    		<tr>
    	  		<td colspan=6 style="text-align:center;"><h3>Search Available Reservations Times (Unix Time Format)</h3></td>
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
    	<input type="hidden" name="action" value="search"/>
    </form><br/>

	<!--  USER'S CURRENTLY DEVICE RESERVATIONS  -->
	<table border="1">
		<tr>
    		<td colspan=4 style="text-align:center;"><h3>Your Upcoming Reservations</h3></td>
    	</tr>
		<tr>
			<th>Device ID</th>
			<th>Reservation Start</th>
			<th>Reservation End</th>
			<th>Checkout</th>
		</tr>
		<?
		$stid = getCurrentReservations($username);
		while ($devices = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $devices['DEVICEID'] . "</td>\n";
        	echo "<td>" . $devices['RESV_START'] . "</td>\n";
        	echo "<td>" . $devices['RESV_END'] . "</td>\n";

        	if(strtotime("now") >= strtotime($devices['RESV_START'])) 
        		echo "<td><a href=\"checkout.php?deviceID=".$devices['DEVICEID']."&action=reserve\">Return Device Link</a></td>";
        	else echo "<td></td>";
    		echo "</tr>";
		}
		?>
	</table><br/>

	<!--  USER'S CURRENTLY CHECKED OUT DEVICES LIST  -->
	<table border="1">
		<tr>
    		<td colspan=5 style="text-align:center;"><h3>Your Currently Checked Out Devices</h3></td>
    	</tr>
		<tr>
			<th>Device ID</th>
			<th>Reservation Start</th>
			<th>Reservation End</th>
			<th>Use Start</th>
			<th>Return Device</th>
		</tr>
		<?
		$stid = getCheckoutDevices($username);
		while ($devices = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $devices['DEVICEID'] . "</td>\n";
        	echo "<td>" . $devices['RESV_START'] . "</td>\n";
        	echo "<td>" . $devices['RESV_END'] . "</td>\n";
        	echo "<td>" . $devices['USE_START'] . "</td>\n";
        	echo "<td><a href=\"return.php?deviceID=".$devices['DEVICEID']."\">Return Device Link</a></td>";
    		echo "</tr>";
		}
		?>
	</table><br/><br/>

	<hr/>

	<br/>
	<h1>Accounts</h1>

	<!--  ADD BALANCE TO ACCOUNT  -->
	<form action="accounts.php" method="post">
    	<table border="1">
    		<tr>
    			<td colspan=2 style="text-align:center;"><h3>Add Balance to Account</h3></td>
    		</tr>
    	  <tr><td style="text-align:right">
    	    <label for="accountID">AccountID: </label></td><td><input type="text" name="accountID" placeholder="0000" />
    	  </td></tr>
    	  <tr><td style="text-align:right">
    	    <label for="amount">Balance to add: </label></td><td><input type="text" name="amount" placeholder="minimum $10.00" />
    	  </td></tr>
    	  <tr><td colspan=2 style="text-align:right;"><input type="submit"/></td></tr>
    	</table>
    	<input type="hidden" name="action" value="balance"/>
    </form>

    <!--  ADD USER TO ACCOUNT  -->
	<form action="accounts.php" method="post">
    	<table border="1">
    		<tr>
    			<td colspan=2 style="text-align:center;"><h3>Add User to Account</h3></td>
    		</tr>
    	  	<tr>
    	  		<td style="text-align:right"><label for="accountID">AccountID: </label></td>
    	    	<td><input type="text" name="accountID" placeholder="0000" /></td>
    		</tr>
    	  	<tr>
    	  		<td style="text-align:right"><label for="useradd">Username to Add: </label></td>
    	  		<td><input type="text" name="useradd" placeholder="testuser" /></td>
    	  	</tr>
    	  	<tr>
    	  		<td style="text-align:right"><label for="type">User Type: </label></td>
    	  		<td>
					<input type="radio" name="type" value="proxy" checked> Proxy<br>
					<input type="radio" name="type" value="owner"> Owner<br>
				</td>
    	  	</tr>
    	  	<tr>
    	  		<td colspan=2 style="text-align:right;">
    	  			<input type="submit"/>
    	  		</td>
    	  	</tr>
    	</table>
    	<input type="hidden" name="action" value="useradd"/>
    </form>

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
<??>