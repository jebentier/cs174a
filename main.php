<?php
	include('includes/db_connect.php');
	include('includes/user_functions.php');
	session_start(); 
	
	// Set session variables
	if(!isset($_SESSION['username'])) $_SESSION['username'] = $_POST['username'];
	if(!isset($_SESSION['password'])) $_SESSION['password'] = $_POST['password'];
	if(!isset($_SESSION['pin']))      $_SESSION['pin']      = $_POST['pin'];
	if(!isset($_SESSION['month'])) $_SESSION['month'] = $_POST['month'];
	if(!isset($_SESSION['day']))   $_SESSION['day']   = $_POST['day'];
	if(!isset($_SESSION['year']))  $_SESSION['year']  = $_POST['year'];
	if(!isset($_SESSION['hour']))  $_SESSION['hour']  = $_POST['hour'];


	// Load session variables
	if(isset($_SESSION['username'])) $username = $_SESSION['username'];
	if(isset($_SESSION['password'])) $password = $_SESSION['password'];
	if(isset($_SESSION['pin']))      $pin      = $_SESSION['pin'];
	if(isset($_SESSION['month'])) $month = $_SESSION['month'];
	if(isset($_SESSION['day']))   $day   = $_SESSION['day'];
	if(isset($_SESSION['year']))  $year  = $_SESSION['year'];
	if(isset($_SESSION['hour']))  $hour  = $_SESSION['hour'];


	/*** LOGIN VALIDATION ***/
	if( ($username!=NULL) && ($password!=NULL) ){
		$stid = login($username, $password);
		$user = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
		if($user['USERNAME'] == NULL){
			echo "Invalid credentials. <a href=\"index.php\">Go back to the login page.</a>";
			exit(0);
		}
		if(strcmp($user['STATUS'], 'f') == 0){
			echo "Your account has been suspended. <a href=\"index.php\">Go back to the login page.</a>";
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
		if(strcmp($user['STATUS'], 'f') == 0){
			echo "Your account has been suspended. <a href=\"index.php\">Go back to the login page.</a>";
			exit(0);
		}
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
		echo "<div>System Date: ".$month."-".$day."-".$year."  Hour: ".$hour."</div>";
	}
	else{
		echo "<div>Signed in as ".$username."</div>";
		echo "<div class=\"header_links\"><a href=\"index.php\">Logout</a><br/><br/></div>";
		echo "<div>System Date: ".$month."-".$day."-".$year." | Hour: ".$hour."</div>";
	}
	?>

	<h1>Devices</h1>
	<!--  ALL DEVICES LIST  -->
	<table border="1">
		<tr>
    		<td colspan=12 style="text-align:center;"><h3>Device List</h3></td>
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
			<th>Quick Checkout</th>
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

        	if( strcmp($devices['AVAILABILITY'], 'no') != 0 )
        		echo "<td style=\"text-align:center;\"><a href=\"device_info.php?deviceID=".$devices['DEVICEID']."\">Link</a></td>";
        	else echo "<td></td>";

        	if( strcmp($devices['AVAILABILITY'], 'available') == 0 ){
        		echo "<form action=\"checkout.php?deviceID=".$devices['DEVICEID']."&action=quick\" method=\"post\">";
        		echo "<td><input type=\"text\" name=\"accountID\" placeholder=\"Enter Account ID\" />";
        		echo "<input type=\"submit\" value=\"Checkout\"/></td>";
        		echo "</form>";
        	}
        	else echo "<td></td>";
        	
    		echo "</tr>";
		}
		?>
	</table><br/>

	<!--  SEARCH RESERVATIONS  -->
	<form action="reserve.php" method="post">
    	<table border="1">
    		<tr>
    	  		<td colspan=6 style="text-align:center;"><h3>Search Available Devices Within:</h3></td>
    	  	</tr>
    		<tr>
    			<th></th>
    			<th>Month</th>
    			<th>Day</th>
    			<th>Year</th>
    			<th>Hour</th>
    		</tr>
    	  	<tr>
    	  		<td><label for="start">Reservation Start: </label></td>
    	  		<td><input type="text" name="start_month" placeholder="1" /></td>
    	  		<td><input type="text" name="start_day" placeholder="1" /></td>
    	  		<td><input type="text" name="start_year" placeholder="2012" /></td>
    	  		<td><input type="text" name="start_hour" placeholder="12" /></td>
    	  	</tr>
    	  	<tr>
    	  		<td><label for="end">Reservation End: </label></td>
    	  		<td><input type="text" name="end_month" placeholder="12" /></td>
    	  		<td><input type="text" name="end_day" placeholder="31" /></td>
    	  		<td><input type="text" name="end_year" placeholder="2012" /></td>
    	  		<td><input type="text" name="end_hour" placeholder="12" /></td>
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
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>".$a['DEVICEID']."</td>\n";
        	echo "<td>".$a['RS_M']."-".$a['RS_D']."-".$a['RS_Y']." : ".$a['RS_H']."</td>\n";
        	echo "<td>".$a['RE_M']."-".$a['RE_D']."-".$a['RE_Y']." : ".$a['RE_H']."</td>\n";
        	
        	if(($a['RS_M']<=$month) && ($a['RS_D']<=$day) && ($a['RS_Y']<=$year) && ($a['RS_H']<=$hour)) 
        		echo "<td><a href=\"checkout.php?deviceID=".$a['DEVICEID']."&action=reserve\">Checkout Link</a></td>";
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
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $a['DEVICEID'] . "</td>\n";
        	echo "<td>".$a['RS_M']."-".$a['RS_D']."-".$a['RS_Y']." : ".$a['RS_H']."</td>\n";
        	echo "<td>".$a['RE_M']."-".$a['RE_D']."-".$a['RE_Y']." : ".$a['RE_H']."</td>\n";
        	echo "<td>".$a['US_M']."-".$a['US_D']."-".$a['US_Y']." : ".$a['US_H']."</td>\n";
        	echo "<td><a href=\"returnA.php?deviceID=".$a['DEVICEID']."\">Return Device Link</a></td>";
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