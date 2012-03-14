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

	echo "<div>Signed in as ".$username."</div>";
	echo "<div>";
	echo "<a href=\"../admin.php\">Back</a>&nbsp;|&nbsp;";
	echo "<a href=\"../index.php\">Logout</a><br/><br/>";
	echo "</div>";
	echo "<div>System Date: ".$month."-".$day."-".$year." | Hour: ".$hour."</div>";
?>
	<!--  USER'S ACCOUNTS LIST  -->
	<table border="1">
		<tr>
    		<td colspan=4 style="text-align:center;"><h3><?= $usercheck;?>'s Accounts</h3></td>
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