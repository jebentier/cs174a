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
	$deviceID  = $_GET['deviceID'];

	echo "<div>Signed in as ".$username."   :--:   ";
	echo "System Date: ".$month."-".$day."-".$year."   Hour: ".$hour."</div>";
	echo "<div>";
	echo "<a href=\"../main.php\">Home</a>&nbsp;|&nbsp;";
	echo "<a href=\"../index.php\">Logout</a><br/><br/>";
	echo "</div>";
?>
	<!--  SEARCH RESERVATIONS  -->
	<form action="deviceModify2.php" method="post">
    	<table border="1">
    		<tr>
    	  		<td colspan=4 style="text-align:center;"><h3>Modify <?=$deviceID;?> Info</h3></td>
    	  	</tr>
    		<tr>
    			<th>Usage Unit</th>
    			<th>Cost Per Unit</th>
    			<th>Max Single Use</th>
    			<th>Overuse Cost</th>
    		</tr>
    	  	<tr>
    	  		<td><input type="text" name="unit" placeholder="days or hours" /></td>
    	  		<td><input type="text" name="cost" placeholder="1.00" /></td>
    	  		<td><input type="text" name="maxuse" placeholder="24" /></td>
    	  		<td><input type="text" name="overuse" placeholder="2.00" /></td>
    	  	</tr>
    	  	<tr>
    	  		<td colspan=6 style="text-align:right;"><input type="submit"/></td>
    	  	</tr>
    	</table>
    	<input type="hidden" name="deviceID" value="<?= $deviceID;?>"/>
    </form><br/>

<??>