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


	if(strcmp($action, 'reserve') == 0){
		if(checkoutDevice($deviceID, $username)){
			echo "Succesful checkout. <a href=\"main.php\">Return Home</a>";
		}
		else echo "Error with checkout. <a href=\"main.php\">Return Home</a>";
	}
	else if(strcmp($action, 'quick') == 0){
		if($accountID == NULL){
			echo "Error with checkout. <a href=\"main.php\">Return Home</a>";
			exit(0);
		}
		if(quickCheckout($deviceID, $username, $accountID)){
			echo "Succesful checkout. <a href=\"main.php\">Return Home</a>";
		}
		else echo "Error with checkout. <a href=\"main.php\">Return Home</a>";
	}
?>