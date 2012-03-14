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
	$action    = $_GET['action'];

	echo "<div>Signed in as ".$username."</div>";
	echo "<div>";
	echo "<a href=\"../admin.php\">Back</a>&nbsp;|&nbsp;";
	echo "<a href=\"../index.php\">Logout</a><br/><br/>";
	echo "</div>";
	echo "<div>System Date: ".$month."-".$day."-".$year." | Hour: ".$hour."</div>";


	if(strcmp($action, "block") == 0){
		blockUser($usercheck);
		echo "Blocked ".$usercheck.". Go <a href=\"../admin.php\">Back</a>";
	}
	else if(strcmp($action, "unblock") == 0){
		unblockUser($usercheck);
		echo "Unblocked ".$usercheck.". Go <a href=\"../admin.php\">Back</a>";
	}
?>

