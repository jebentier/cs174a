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
	$action    = $_GET['action'];

	echo "<div>Signed in as ".$username."   :--:   ";
	echo "System Date: ".$month."-".$day."-".$year."   Hour: ".$hour."</div>";
	echo "<div>";
	echo "<a href=\"../main.php\">Home</a>&nbsp;|&nbsp;";
	echo "<a href=\"../index.php\">Logout</a><br/><br/>";
	echo "</div>";


	if(strcmp($action, 'users') == 0){
		deleteUsers();
		echo "Deleted blocked users. Go <a href=\"../admin.php\">Back</a>";
	}
	else if(strcmp($action, 'accounts') == 0){
		deleteAccounts();
		echo "Deleted inactive accounts. Go <a href=\"../admin.php\">Back</a>";
	}
?>