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
	$usercheck = $_GET['username'];

	echo "<div>Signed in as ".$username."   :--:   ";
	echo "System Date: ".$month."-".$day."-".$year."   Hour: ".$hour."</div>";
	echo "<div>";
	echo "<a href=\"../main.php\">Home</a>&nbsp;|&nbsp;";
	echo "<a href=\"../index.php\">Logout</a><br/><br/>";
	echo "</div>";
?>
	<!--  User device transactions  -->
	<table border="1">
		<tr>
    		<td colspan=5 style="text-align:center;"><h3>Device Transactions</h3></td>
    	</tr>
		<tr>
			<th>Device ID</th>
			<th>Reservation Start</th>
			<th>Reservation End</th>
			<th>Use Start</th>
			<th>Use End</th>
		</tr>
		<?
		$stid = getUserHistoryDevices($usercheck);
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>".$a['DEVICEID'] . "</td>\n";
        	echo "<td>".$a['RS_M']."-".$a['RS_D']."-".$a['RS_Y']." : ".$a['RS_H']."</td>\n";
        	echo "<td>".$a['RE_M']."-".$a['RE_D']."-".$a['RE_Y']." : ".$a['RE_H']."</td>\n";
        	echo "<td>".$a['US_M']."-".$a['US_D']."-".$a['US_Y']." : ".$a['US_H']."</td>\n";
        	echo "<td>".$a['UE_M']."-".$a['UE_D']."-".$a['UE_Y']." : ".$a['UE_H']."</td>\n";
			echo "</tr>";
		}
		?>
	</table><br/><br/>

	<!--  User account transactions  -->
	<table border="1">
		<tr>
    		<td colspan=3 style="text-align:center;"><h3>Account Transactions</h3></td>
    	</tr>
		<tr>
			<th>Account ID</th>
			<th>Date</th>
			<th>Comment</th>
		</tr>
		<?
		$stid = getUserHistoryAccounts($usercheck);
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $a['ACCOUNTID'] . "</td>\n";
        	echo "<td>".$a['M']."-".$a['D']."-".$a['Y']." : ".$a['H']."</td>\n";
        	echo "<td>" . $a['TYPE'] . "</td>\n";
			echo "</tr>";
		}
		?>
	</table><br/>
<??>