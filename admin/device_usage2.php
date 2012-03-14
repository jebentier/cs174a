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

	$deviceID  = $_POST['deviceID'];
	$sm  = $_POST['sm'];
	$sd  = $_POST['sd'];
	$sy  = $_POST['sy'];
	$sh  = $_POST['sh'];
	$em  = $_POST['em'];
	$ed  = $_POST['ed'];
	$ey  = $_POST['ey'];
	$eh  = $_POST['eh'];

	echo "<div>Signed in as ".$username."</div>";
	echo "<div>";
	echo "<a href=\"../admin.php\">Back</a>&nbsp;|&nbsp;";
	echo "<a href=\"../index.php\">Logout</a><br/><br/>";
	echo "</div>";
	echo "<div>System Date: ".$month."-".$day."-".$year." | Hour: ".$hour."</div>";
?>

	<table border="1">
		<tr>
    		<td colspan=3 style="text-align:center;"><h3><?=$deviceID;?> Usage History</h3></td>
    	</tr>
		<tr>
			<th>Username</th>
			<th>Use Start</th>
			<th>Use End</th>
		</tr>
<?
		$stid = deviceUsage($deviceID, $sm, $sd, $sy, $sh, $em, $ed, $ey, $eh);
		while ($a = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    		echo "<tr>";
    		echo "<td>" . $a['USERNAME'] . "</td>\n";
    		echo "<td>".$a['US_M']."-".$a['US_D']."-".$a['US_Y']." | ".$a['US_H']."</td>";
    		echo "<td>".$a['UE_M']."-".$a['UE_D']."-".$a['UE_Y']." | ".$a['UE_H']."</td>";
    		echo "</tr>";
		}
	echo "</table>";
?>