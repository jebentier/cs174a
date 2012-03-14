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

	<form action="returnB.php" method="post">
    	<table border="1">
    		<tr>
    			<td colspan=2 style="text-align:center;"><h3>Select Account to Charge</h3></td>
    		</tr>
    		<tr>
    			<td style="text-align:right">
    		    	<label for="accountID">AccountID: </label></td><td><input type="text" name=	"accountID" placeholder="0000" />
    		  	</td>
    		</tr>
    		<tr>
    			<td colspan=2 style="text-align:right;"><input type="submit"/></td>
    		</tr>
    		<input type="hidden" name="deviceID" value="<?=$deviceID;?>"/>
    	</table>
   	</form>

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
	echo "</table>";
?>