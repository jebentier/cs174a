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
	$accountID = $_GET['accountID'];

	echo "<div>Signed in as ".$username."   :--:   ";
	echo "System Date: ".$month."-".$day."-".$year."   Hour: ".$hour."</div>";
	echo "<div>";
	echo "<a href=\"../main.php\">Home</a>&nbsp;|&nbsp;";
	echo "<a href=\"../index.php\">Logout</a><br/><br/>";
	echo "</div>";
?>
	<!--  ACCOUNT TRANSACTIONS LIST  -->
	<table border="1">
		<tr>
    		<td colspan=3 style="text-align:center;"><h3>Account History</h3></td>
    	</tr>
		<tr>
			<th>Username</th>
			<th>Date</th>
			<th>Comment</th>
		</tr>
		<?
		$stid = closeAccount($accountID);
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
        	echo "<td>" . $a['USERNAME'] . "</td>\n";
        	echo "<td>".$a['M']."-".$a['D']."-".$a['Y']." : ".$a['H']."</td>\n";
        	echo "<td>" . $a['TYPE'] . "</td>\n";
			echo "</tr>";
		}
		?>
	</table><br/>
<??>