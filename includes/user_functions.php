<?php
	date_default_timezone_set('America/Los_Angeles');

	/* login with username - password */
	function login($username, $password){
		global $conn;
		$query = "SELECT username, status, isManager
				  FROM   users
				  WHERE  username = '$username'
				  AND    password = '$password'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* login with PIN number */
	function pinLogin($pin){
		global $conn;
		$query = "SELECT username, status, isManager
				  FROM   users
				  WHERE  pin = '$pin'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	function getCredentials($pin){
		global $conn;
		$query = "SELECT username, status, isManager
				  FROM   users
				  WHERE  pin = '$pin'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* Pulls list of accounts for a given user */
	function getUserAccounts($username){
		global $conn;
		$query = "SELECT A.accountID, A.balance, UA.privilege, A.status
				  FROM   accounts A LEFT JOIN user_accts UA ON A.accountID = UA.accountID
				  WHERE  UA.username = '$username'
				  AND    (UA.privilege = 'owner'
				  OR     UA.privilege = 'proxy')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* Adds money to an account and updates the account transactions table */
	function addAccountBalance($amount, $accountID, $username){
		global $conn;

		// Check if user is a member of the account
		$query = "SELECT A.accountID, A.balance, UA.privilege, A.status
				  FROM   accounts A LEFT JOIN user_accts UA ON A.accountID = UA.accountID
				  WHERE  UA.username = '$username'
				  AND    (UA.privilege = 'owner'
				  OR     UA.privilege = 'proxy')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
		if($row['BALANCE'] == NULL) return false;

		// Add balance
		$query = "UPDATE accounts
				  SET    balance = balance + '$amount'
				  WHERE  accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Insert new account transaction
		$query = "INSERT INTO acct_trans (username, accountID, datetime, type)
				  VALUES ('$username', '$accountID', sysdate, 'added $amount dollars')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}

	/* Adds a user to an account */
	function addAccountUser($username, $useradd, $type, $accountID){
		global $conn;

		// Make sure the adding user has the proper permission
		$query = "SELECT privilege
				  FROM   user_accts
				  WHERE  username = '$username'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
		if(strcmp($row['PRIVILEGE'], 'owner') == 0) return false;


		// Add user to account
		$query = "INSERT INTO user_accts (username, privilege, accountID)
				  VALUES ('$useradd', '$type', '$accountID')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Insert new account transaction
		$query = "INSERT INTO acct_trans (username, accountID, datetime, type)
				  VALUES ('$username', '$accountID', sysdate, 'added $username as $type to account')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}

	/* Retrieve the entire list of devices  */
	function getDeviceList(){
		global $conn;
		$query = "SELECT deviceID, name, type, year, availability, unit,
						 cost, maxuse, overuse
				  FROM   devices"; 
		$stid =  oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* Retrieve the full info for a given device */
	function getDeviceInfo($deviceID){
		global $conn;
		$query = "SELECT D.deviceID, D.name, D.type, D.year, D.availability, D.unit,
						 D.cost, D.maxuse, D.overuse, U.name AS manager
				  FROM   devices D, users U, managers_devices MD
				  WHERE  D.deviceID = '$deviceID'
				  AND    MD.deviceID = '$deviceID'
				  AND    MD.username = U.username"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* Get list of devices a user currently has checked out */
	function getCheckoutDevices($username){
		global $conn;
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  username = '$username'
			      AND    to_char(resv_start, 'DDMMYYYYHH24') <= to_char(sysdate, 'DDMMYYYYHH24')
			      AND    use_start IS NOT NULL
			      AND    use_end IS NULL";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* Get list of devices a user currently has checked out */
	function getCurrentReservations($username){
		global $conn;
		$query = "SELECT deviceID, to_char(resv_start, 'YYYY-MM-DD HH24:SS') AS resv_start, 
				  to_char(resv_end, 'YYYY-MM-DD HH24:SS') AS resv_end
			      FROM   device_trans
			      WHERE  username = '$username'
			      AND    use_start IS NULL";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* Pull all the current reservations for a device */
	function getReservationInfo($deviceID){
		global $conn;
		$query = "SELECT to_char(resv_start, 'YYYY-MM-DD HH24:SS') AS resv_start, to_char(resv_end, 'YYYY-MM-DD HH24:SS') AS resv_end
				  FROM   device_trans
				  WHERE  deviceID = '$deviceID'
				  AND    to_char(sysdate, 'DDMMYYYYHH24') <= to_char(resv_start, 'DDMMYYYYHH24')"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* User searches open reservation times */
	function getAvailableReservations($startTime, $endTime){
		global $conn;
		$query = "SELECT deviceID
				  FROM   device_trans
				  WHERE  to_char(resv_start, 'DDMMYYYYHH24') <= '$startTime'
				  AND    to_char(resv_end, 'DDMMYYYYHH24') >= '$startTime'
				  INTERSECT
				  SELECT deviceID
				  FROM   device_trans
				  WHERE  to_char(resv_start, 'DDMMYYYYHH24') <= '$endTime'
				  AND    to_char(resv_end, 'DDMMYYYYHH24') >= '$endTime'";
		$stid =  oci_parse($conn, $query);
		oci_execute($stid);

		$query = "SELECT deviceID, name, type, year, availability, unit,
						 cost, maxuse, overuse
				  FROM   devices ";
		$whereGuard = false;
		while($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)){
			if(!$whereGuard){
				$query .= "WHERE deviceID != '".$row['DEVICEID']."'";
				$whereGuard = true;
			}
			else $query .= " AND deviceID != '".$row['DEVICEID']."'";
		}
		$stid =  oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* User creates a reservation time */
	function reserveDevice($deviceID, $username, $startTime, $endTime){
		global $conn;
		// Check to see if reservation start time intersects with another reservation
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = '$deviceID'
			      AND    to_char(resv_start, 'DDMMYYYYHH24') <= '$startTime'
			      AND    '$startTime' <= to_char(resv_end, 'DDMMYYYYHH24')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] != NULL) return false;


		// Check to see if reservation end time intersects with another reservation
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = '$deviceID'
			      AND    to_char(resv_start, 'DDMMYYYYHH24') <= '$endTime'
			      AND    '$endTime' <= to_char(resv_end, 'DDMMYYYYHH24')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] != NULL) return false;


		// Create reservation in device transactions table
		$query = "INSERT INTO device_trans (deviceID, username, resv_start, resv_end, use_start, use_end)
				  VALUES ('$deviceID', '$username', to_date('$startTime', 'DDMMYYYYHH24'), to_date('$endTime', 'DDMMYYYYHH24'), NULL, NULL)";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return true;
	}

	/* User checkouts a device */
	function checkoutDevice($deviceID, $username){
		global $conn;
		// Checks to see if user has previously reserved the device
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = '$deviceID'
			      AND    username = '$username'
			      AND    to_char(resv_start, 'DDMMYYYYHH24') <= to_char(sysdate, 'DDMMYYYYHH24')
			      AND    to_char(sysdate, 'DDMMYYYYHH24') < to_char(resv_end, 'DDMMYYYYHH24')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] == NULL) return false;

		// Checks to see if device is available for use
		$query = "SELECT *
			      FROM   devices
			      WHERE  deviceID = '$deviceID'
			      AND    availablility = 'available'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] == NULL) return false;

		// Update device transactions to track checkout 
		$query = "UPDATE device_trans
				  SET    use_start = sysdate
				  WHERE  deviceID = '$deviceID'
				  AND    username = '$username'
				  AND    to_char(resv_start, 'DDMMYYYYHH24') <= to_char(sysdate, 'DDMMYYYYHH24')
			      AND    to_char(sysdate, 'DDMMYYYYHH24') <= to_char(resv_end, 'DDMMYYYYHH24')"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "UPDATE devices
				  SET    availability = 'in use'
				  WHERE  deviceID = '$deviceID'"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}

	/* User returns a device, and an account is charged */
	function returnDevice($deviceID, $username, $accountID){
		global $conn;
		// Get use time 
		$query = "SELECT to_char(use_start, 'YYYY-DD-MM HH24:MI:SS') AS use_start, 
		                 to_char(sysdate, 'YYYY-DD-MM HH24:MI:SS')   AS use_end
		                 trunc(((86400*(&use_end-use_start))/60)/60)-24*(trunc((((86400*(&&use_end-use_start))/60)/60)/24)) AS hours,
		                 trunc((((86400*(&use_end-use_start))/60)/60)/24) AS days

			  	  FROM   device_trans
			  	  WHERE  deviceID = '$deviceID'
			  	  AND    username = '$username'
			  	  AND    to_char(resv_start, 'DDMMYYYYHH24') <= to_char(sysdate, 'DDMMYYYYHH24')
			  	  AND    use_end IS NULL";
		
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

		$hours = $row['HOURS'];
		$days  = $row['DAYS'];



		// Get device info and calculate cost
		$query = "SELECT unit, cost, maxuse, overuse
				  FROM   devices
				  WHERE  deviceID = '$deviceID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

		$unit     = $row['UNIT'];
		$cost     = $row['COST'];
		$maxuse   = $row['MAXUSE'];
		$overuse  = $row['OVERUSE'];

		if(strcmp($unit, 'hours') == 0) $use_time = $hours;
		else if(strcmp($unit, 'days') == 0) $use_time = $days;

		if($maxuse < $use_time) 
			$total = ($overuse * ($use_time - $maxuse)) + ($cost * $maxuse);  // Late return penalty
		else
			$total = $cost * $use_time;										  // Normal cost

		// Check if account has enough balance
		$query = "SELECT balance
				  FROM   accounts
				  WHERE  accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
		if($row['BALANCE'] < $total) return false;



		// Deduct cost from account
		$query = "UPDATE accounts
				  SET    balance = balance - '$total'
				  WHERE  accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Insert new account transaction
		$query = "INSERT INTO acct_trans (username, accountID, datetime, type)
				  VALUES ('$username', '$accountID', sysdate, 
				  	billed $total dollar late fee for device $deviceID')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Update device transactions to track return 
		$query = "UPDATE device_trans
				  SET    use_end = sysdate
				  WHERE  deviceID = '$deviceID'
				  AND    username = '$username'
				  AND    to_char(resv_start, 'DDMMYYYYHH24') <= to_char(sysdate, 'DDMMYYYYHH24')
				  AND    use_end IS NULL"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "UPDATE devices
				  SET    availability = 'available'
				  WHERE  deviceID = '$deviceID'"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}
?>