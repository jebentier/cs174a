<?php
	include('includes/db_connect.php');
	include('includes/user_functions.php');
	session_start();
	

	/*** SETTING UP VARIABLES ***/
	$username = $_SESSION['username'];
 	$password = $_SESSION['password'];
	$pin = $_SESSION['pin'];
	$isManager = $_SESSION['isManager'];
	$deviceID = $_POST['deviceID'];
	$action = $_POST['action'];

	if(strcmp($isManager, 't') == 0){
		echo "<div>Signed in as ".$username."</div>";
		echo "<div class=\"header_links\">";
		echo "<a href=\"main.php\">Home</a>&nbsp;|&nbsp;";
		echo "<a href=\"admin.php\">Manager Page</a>&nbsp;|&nbsp;";
		echo "<a href=\"index.php\">Logout</a>";
		echo "<br/><br/></div>";
	}
	else{
		echo "<div>Signed in as ".$username."</div>";
		echo "<div class=\"header_links\">";
		echo "<a href=\"main.php\">Home</a>&nbsp;|&nbsp;";
		echo "<a href=\"index.php\">Logout</a>";
		echo "<br/></div>";
	}

	if(strcmp($action, 'reserve') == 0){
		if(checkoutDevice($deviceID, $username)){
			echo "Succesful checkout. <a href=\"main.php\">Return Home</a>";
		}
		else echo "Error with checkout. <a href=\"main.php\">Return Home</a>";
	}

?>