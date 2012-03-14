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
	$deviceID  = $_GET['deviceID'];
	$action    = $_GET['action'];
	$accountID = $_POST['accountID'];


	echo "<div>Signed in as ".$username."</div>";
	echo "<div>";
	echo "<a href=\"main.php\">Home</a>&nbsp;|&nbsp;";
	echo "<a href=\"index.php\">Logout</a><br/><br/>";
	echo "</div>";
	echo "<div>System Date: ".$month."-".$day."-".$year." | Hour: ".$hour."</div>";

?>

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

<??>