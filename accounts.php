<?php
	include('includes/db_connect.php');
	include('includes/user_functions.php');

	session_start();

	$username = $_SESSION['username'];
 	$password = $_SESSION['password'];
	$pin = $_SESSION['pin'];
	$isManager = $_SESSION['isManager'];
	$accountID = $_POST['accountID'];
	$amount = $_POST['amount'];
	$action = $_POST['action'];
	$type = $_POST['type'];
	$useradd = $_POST['useradd'];

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


	/*** ADD BALANCE TO ACCOUNT ***/
	if(strcmp($action, 'balance') == 0){
		if(number_format($amount, 2, '.', '') < 10){
			echo "Amount to add must be at least $10. <a href=\"main.php\">Return Home</a>";
			exit(0);
		}
		if(addAccountBalance($amount, $accountID, $username)){
			echo "Successfully added ".number_format($amount, 2, '.', '')." "; 
			echo "to account ".$accountID.". <a href=\"main.php\">Return Home</a>";
		}
		else echo "Account verification error. Please try again. <a href=\"main.php\">Return Home</a>"; 
	}


	/*** ADD USER TO ACCOUNT ***/
	else if(strcmp($action, 'useradd') == 0){
		if(($useradd == NULL) || ($accountID == NULL)){
			echo "Error: Invalid input. <a href=\"main.php\">Return Home</a>";
			exit(0);
		}
		if(addAccountUser($username, $useradd, $type, $accountID)){
			echo "Successfully added ".$useradd." as a ".$type." user "; 
			echo "to account ".$accountID.". <a href=\"main.php\">Return Home</a>";
		}
		else echo "You do not have the credentials to add a user to this account. <a href=\"main.php\">Return Home</a>"; 
	}
?>