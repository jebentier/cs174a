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

	echo "<div>Signed in as ".$username."</div>";
	echo "<div>";
	echo "<a href=\"../admin.php\">Back</a>&nbsp;|&nbsp;";
	echo "<a href=\"../index.php\">Logout</a><br/><br/>";
	echo "</div>";
	echo "<div>System Date: ".$month."-".$day."-".$year." | Hour: ".$hour."</div>";
?>
	<!--  SEARCH RESERVATIONS  -->
	<form action="device_usage2.php" method="post">
    	<table border="1">
    		<tr>
    	  		<td colspan=6 style="text-align:center;"><h3>Search <?=$deviceID;?> Usage Within:</h3></td>
    	  	</tr>
    		<tr>
    			<th></th>
    			<th>Month</th>
    			<th>Day</th>
    			<th>Year</th>
    			<th>Hour</th>
    		</tr>
    	  	<tr>
    	  		<td><label for="start">Start: </label></td>
    	  		<td><input type="text" name="sm" placeholder="1" /></td>
    	  		<td><input type="text" name="sd" placeholder="1" /></td>
    	  		<td><input type="text" name="sy" placeholder="2012" /></td>
    	  		<td><input type="text" name="sh" placeholder="12" /></td>
    	  	</tr>
    	  	<tr>
    	  		<td><label for="end">End: </label></td>
    	  		<td><input type="text" name="em" placeholder="12" /></td>
    	  		<td><input type="text" name="ed" placeholder="31" /></td>
    	  		<td><input type="text" name="ey" placeholder="2012" /></td>
    	  		<td><input type="text" name="eh" placeholder="12" /></td>
    	  	</tr>
    	  	<tr>
    	  		<td colspan=6 style="text-align:right;"><input type="submit"/></td>
    	  	</tr>
    	</table>
    	<input type="hidden" name="deviceID" value="<?= $deviceID;?>"/>
    </form><br/>

<??>