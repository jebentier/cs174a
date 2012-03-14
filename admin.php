<?php
	include('includes/db_connect.php');
	include('includes/user_functions.php');
	include('includes/admin_functions.php');
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
	echo "<div>Signed in as ".$username."   :--:   ";
	echo "System Date: ".$month."-".$day."-".$year."   Hour: ".$hour."</div>";
	echo "<div>";
	echo "<a href=\"main.php\">Home</a>&nbsp;|&nbsp;";
	echo "<a href=\"index.php\">Logout</a><br/><br/>";
	echo "</div>";
?>

	<h2>MANGER PAGE</h2>


	<a href="admin/in_use.php">Currently Used Devices</a>&nbsp;&nbsp;&nbsp;
	<a href="admin/delete.php?action=users">Delete Blocked Users</a>&nbsp;&nbsp;&nbsp;
	<a href="admin/delete.php?action=accounts">Delete Inactive Accounts</a>

	<br/><br/>
	<!--  ALL DEVICES LIST  -->
	<table border="1">
		<tr>
    		<td colspan=13 style="text-align:center;"><h3>Device List</h3></td>
    	</tr>
		<tr>
			<th>Device ID</th>
			<th>Device Name</th>
			<th>Type</th>
			<th>Year</th>
			<th>Current Availability</th>
			<th>Retire Device</th>
			<th>Usage Unit</th>
			<th>Cost per unit</th>
			<th>Max single use</th>
			<th>Overuse cost</th>
			<th>Device Usage Info</th>
			<th>Modify Device Info</th>
		</tr>
		<?
		$stid = getDeviceList();
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $a['DEVICEID'] . "</td>\n";
        	echo "<td>" . $a['NAME'] . "</td>\n";
        	echo "<td>" . $a['TYPE'] . "</td>\n";
        	echo "<td>" . $a['YEAR'] . "</td>\n";
        	echo "<td>" . $a['AVAILABILITY'] . "</td>\n";

        	if(strcmp($a['AVAILABILITY'], 'no') != 0) 
        		echo "<td style=\"text-align:center;\"><a href=\"admin/deviceRetire.php?deviceID=".$a['DEVICEID']."\">Link</a></td>";       	
    		else echo "<td></td>";

        	echo "<td>" . $a['UNIT'] . "</td>\n";
        	echo "<td>" . number_format($a['COST'], 2, '.', '') . "</td>\n";
        	echo "<td>" . $a['MAXUSE'] . "</td>\n";
        	echo "<td>" . number_format($a['OVERUSE'], 2, '.', '') . "</td>\n";
			echo "<td style=\"text-align:center;\"><a href=\"admin/device_usage.php?deviceID=".$a['DEVICEID']."&action=query\">Link</a></td>"; 
			echo "<td style=\"text-align:center;\"><a href=\"admin/deviceModify.php?deviceID=".$a['DEVICEID']."\">Link</a></td>";       	    	
    		echo "</tr>";
		}
		?>
	</table><br/>

	<!--  USER LIST  -->
	<table border="1">
		<tr>
    		<td colspan=12 style="text-align:center;"><h3>Users</h3></td>
    	</tr>
		<tr>
			<th>Name</th>
			<th>Username</th>
			<th>Password</th>
			<th>PIN</th>
			<th>Manager</th>
			<th>Status</th>
			<th>Block User</th>
			<th>Unblock User</th>
			<th>Email</th>
			<th>Phone #</th>
			<th>Past Month Activity</th>
			<th>User's Accounts</th>
		</tr>
		<?
		$stid = getUserList();
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $a['NAME'] . "</td>\n";
        	echo "<td>" . $a['USERNAME'] . "</td>\n";
        	echo "<td>" . $a['PASSWORD'] . "</td>\n";
        	echo "<td>" . $a['PIN'] . "</td>\n";
        	
        	if(strcmp($a['ISMANAGER'], 't') == 0) 
        		echo "<td>Yes</td>";
        	else echo "<td></td>";

        	if(strcmp($a['STATUS'], 't') == 0) {
        		echo "<td>active</td>\n";
        		echo "<td style=\"text-align:center;\">";
        		echo "<a href=\"admin/user_control.php?username=".$a['USERNAME']."&action=block\">Link</a>";
        		echo "</td>";
        		echo "<td></td>";
        	}
        	else{ 
        		echo "<td>blocked</td>\n";
        		echo "<td></td>";
        		echo "<td style=\"text-align:center;\">";
        		echo "<a href=\"admin/user_control.php?username=".$a['USERNAME']."&action=unblock\">Link</a>";
        		echo "</td>";
        		
        	}

        	echo "<td>" . $a['EMAIL'] . "</td>\n";
        	echo "<td>" . $a['PHONE'] . "</td>\n";
        	echo "<td style=\"text-align:center;\"><a href=\"admin/user_history.php?username=".$a['USERNAME']."\">Link</a></td>";
        	echo "<td style=\"text-align:center;\"><a href=\"admin/user_accounts.php?username=".$a['USERNAME']."\">Link</a></td>";
			echo "</tr>";
		}
		?>
	</table><br/>

	<!--  ACCOUNT LIST  -->
	<table border="1">
		<tr>
    		<td colspan=4 style="text-align:center;"><h3>Accounts</h3></td>
    	</tr>
		<tr>
			<th>Account ID</th>
			<th>Balance</th>
			<th>Status</th>
			<th>Close Account</th>
		</tr>
		<?
		$stid = getAccountList();
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $a['ACCOUNTID'] . "</td>\n";
        	echo "<td>" . $a['BALANCE'] . "</td>\n";
        	echo "<td>" . $a['STATUS'] . "</td>\n";
        	if(strcmp($a['STATUS'], 'active') == 0) 
        		echo "<td style=\"text-align:center;\"><a href=\"admin/close_account.php?accountID=".$a['ACCOUNTID']."\">Link</a></td>";
        	else echo "<td></td>";
        	echo "</tr>";
		}
		?>
	</table><br/>
<??>