<?php
	include('../includes/db_connect.php');
	include('../includes/user_functions.php');
	include('../includes/admin_functions.php');
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

	echo "<div>Signed in as ".$username."   :--:   ";
	echo "System Date: ".$month."-".$day."-".$year."   Hour: ".$hour."</div>";
	echo "<div>";
	echo "<a href=\"../main.php\">Home</a>&nbsp;|&nbsp;";
	echo "<a href=\"../index.php\">Logout</a><br/><br/>";
	echo "</div>";
?>

	<!--  CURRENTLY IN USE DEVICES LIST  -->
	<table border="1">
		<tr>
    		<td colspan=6 style="text-align:center;"><h3>Devices Currently In Use</h3></td>
    	</tr>
		<tr>
			<th>Device ID</th>
			<th>Renter</th>
			<th>Username</th>
			<th>Manager</th>
			<th>Email</th>
			<th>Phone #</th>
		</tr>
		<?
		$stid = devicesInUse();
		while ($devices = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $devices['DEVICEID'] . "</td>\n";
        	echo "<td>" . $devices['NAME'] . "</td>\n";
        	echo "<td>" . $devices['USERNAME'] . "</td>\n";
        	echo "<td>" . $devices['ISMANAGER'] . "</td>\n";
        	echo "<td>" . $devices['EMAIL'] . "</td>\n";
        	echo "<td>" . $devices['PHONE'] . "</td>\n";
			echo "</tr>";
		}
		?>
	</table><br/>

<??>