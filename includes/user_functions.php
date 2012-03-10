<?
	/*
		Functions
		=========
		login($username, $password)

		pinLogin($pin)

		getUserAccounts($username)

		addAccountBalance($amount, $accountID, $username)

		addAccountUser($username, $useradd, $type, $accountID)

		getDeviceList()

		getCheckoutDevices($username)

		getReservationInfo($deviceID)

		reserveDevice($deviceID, $username, $accountID, $startTime, $endTime)

		checkoutDevice($deviceID, $username)

		returnDevice($deviceID, $username, $accountID)

	*/

	/* login with username - password */
	function login($username, $password){

		$query = "SELECT username, status, isManager
				  FROM   users
				  WHERE  username = $username
				  AND    password = $password";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
	}

	/* login with PIN number */
	function pinLogin($pin){

		$query = "SELECT username, status, isManager
				  FROM   users
				  WHERE  pin = $pin";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
	}

	/* Pulls list of accounts for a given user */
	function getUserAccounts($username){

		$query = "SELECT A.accountID, A.balance, A.status
				  FROM   accounts A, users U, user_accts UA
				  WHERE  UA.username = $username
				  AND    UA.accountID = A.accountID
				  AND    UA.privilege = 'owner'
				  OR     UA.privilege = 'proxy'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
	}

	/* Adds money to an account and updates the account transactions table */
	function addAccountBalance($amount, $accountID, $username){

		// Add balance
		$query = "UPDATE accounts
				  SET    balance = balance + $amount
				  WHERE  accountID = $accountID";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Insert new account transaction
		$query = "INSERT INTO acct_trans
				  VALUES ($username, $accountID, to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS'), 'added $amount dollars')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}

	/* Adds a user to an account */
	function addAccountUser($username, $useradd, $type, $accountID){

		// Make sure the adding user has the proper permission
		$query = "SELECT privilege
				  FROM   user_accts
				  WHERE  username = $username";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
		if(strcmp($row[privilege], 'owner') != 0) return false;


		// Add user to account
		$query = "INSERT INTO user_accts
				  VALUES ($useradd, $type, $accountID)";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Insert new account transaction
		$query = "INSERT INTO acct_trans
				  VALUES ($username, $accountID, to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS'), 'added $username as $type to account')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}

	/* Retrieve the entire list of devices and associated manager */
	function getDeviceList(){

		$query = "SELECT D.deviceID, D.name, D.type, D.year, D.availability, D.unit,
						 D.cost, D.max_use, D.over_use, U.name
				  FROM   devices D, users U, managers_devices MD
				  WHERE  D.deviceID = MD.deviceID
				  AND    MD.username = U.username;"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
	}

	/* Get list of devices a user currently has checked out */
	function getCheckoutDevices($username){

		$query = "SELECT *
			      FROM   device_trans
			      WHERE  username = $username
			      AND    resv_start <= to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')
			      AND    use_start != NULL
			      AND    use_end = NULL";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
	}

	/* Pull all the current reservations for a device */
	function getReservationInfo($deviceID){

		$query = "SELECT resv_start, resv_end
				  FROM   device_trans
				  WHERE  deviceID = $deviceID
				  AND    resv_start >= to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
	}

	/* User creates a reservation time */
	function reserveDevice($deviceID, $username, $accountID, $startTime, $endTime){
		
		// Check to see if reservation start time intersects with another reservation
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = $deviceID
			      AND    resv_start <= $startTime
			      AND    resv_end >= $startTime";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] != NULL) return false;


		// Check to see if reservation end time intersects with another reservation
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = $deviceID
			      AND    resv_start <= $endTime
			      AND    resv_end >= $endTime";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] != NULL) return false;


		// Create reservation in device transactions table
		$query = "INSERT INTO device_trans
				  VALUES ($deviceID, $username, $startTime, $endTime, NULL, NULL)";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return true;
	}

	/* User checkouts a device */
	function checkoutDevice($deviceID, $username){

		// Checks to see if user has previously reserved the device
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = $deviceID
			      AND    username = $username
			      AND    resv_start <= to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')
			      AND    resv_end >= to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] == NULL) return false;

		// Checks to see if device is available for use
		$query = "SELECT *
			      FROM   devices
			      WHERE  deviceID = $deviceID
			      AND    availablility = 'available'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] == NULL) return false;

		// Update device transactions to track checkout 
		$query = "UPDATE device_trans
				  SET    use_start = to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')
				  WHERE  deviceID = $deviceID
				  AND    username = $username
				  AND    resv_start <= to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')
			      AND    resv_end >= to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "UPDATE devices
				  SET    availability = 'in use'
				  WHERE  deviceID = $deviceID"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}

	/* User returns a device, and an account is charged */
	function returnDevice($deviceID, $username, $accountID){

		// Get use time 
		$query = "SELECT to_char(use_start, 'DD-MM-YYYY HH24:MI:SS') AS use_start, 
		                 to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')   AS use_end
		                 trunc(((86400*(&use_end-use_start))/60)/60)-24*(trunc((((86400*(&&use_end-use_start))/60)/60)/24)) AS hours,
		                 trunc((((86400*(&use_end-use_start))/60)/60)/24) AS days

			  	  FROM   device_trans
			  	  WHERE  deviceID = $deviceID
			  	  AND    username = $username
			  	  AND    resv_start <= to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')
			  	  AND    use_end = NULL";
		
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

		$hours = $row['hours'];
		$days  = $row['days'];



		// Get device info and calculate cost
		$query = "SELECT unit, cost, max_use, over_use
				  FROM devices
				  WHERE deviceID = $deviceID";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

		$unit     = $row['unit'];
		$cost     = $row['cost'];
		$max_use  = $row['max_use'];
		$over_use = $row['over_use'];

		if(strcmp($unit, 'hour') == 0) $use_time = $hours;
		else if(strcmp($unit, 'day') == 0) $use_time = $days;

		if($use_time > $max_use) 
			$total = ($over_use * ($use_time - $max_use)) + ($cost * $max_use);  // Late return penalty
		else
			$total = $cost * $use_time;											 // Normal cost



		// Check if account has enough balance
		$query = "SELECT balance
				  FROM   accounts
				  WHERE  accountID = $accountID";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
		if($row['balance'] < $total) return false;



		// Deduct cost from account
		$query = "UPDATE accounts
				  SET    balance = balance - $total
				  WHERE  accountID = $accountID";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);



		// Insert new account transaction
		$query = "INSERT INTO acct_trans
				  VALUES ($username, $accountID, to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS'), 
				  	billed $total dollars for device $deviceID')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);



		// Update device transactions to track return 
		$query = "UPDATE device_trans
				  SET    use_end = to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')
				  WHERE  deviceID = $deviceID
				  AND    username = $username
				  AND    resv_start <= to_char(sysdate, 'DD-MM-YYYY HH24:MI:SS')
				  AND    use_end = NULL"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "UPDATE devices
				  SET    availability = 'available'
				  WHERE  deviceID = $deviceID"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}
?>