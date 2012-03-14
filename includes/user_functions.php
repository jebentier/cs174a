<?php

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
		global $conn, $month, $day, $year, $hour;

		// Check if user is a member of the account
		$query = "SELECT A.accountID, A.balance, UA.privilege, A.status
				  FROM   accounts A LEFT JOIN user_accts UA ON A.accountID = UA.accountID
				  WHERE  UA.username = '$username'
				  AND 	 A.accountId = '$accountID'
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
		$query = "INSERT INTO acct_trans (username, accountID, type, m, d, y, h)
				  VALUES ('$username', '$accountID','added $amount dollars',
				  			'$month', '$day', '$year', '$hour')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}

	/* Adds a user to an account */
	function addAccountUser($username, $useradd, $type, $accountID){
		global $conn, $month, $day, $year, $hour;

		// Make sure the adding user has the proper permission
		$query = "SELECT privilege
				  FROM   user_accts
				  WHERE  username = '$username'
				  AND    accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
		if(strcmp($row['PRIVILEGE'], 'owner') != 0) return false;


		// Add user to account
		$query = "INSERT INTO user_accts (username, privilege, accountID)
				  VALUES ('$useradd', '$type', '$accountID')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Insert new account transaction
		$query = "INSERT INTO acct_trans (username, accountID, type, m, d, y, h)
				  VALUES ('$username', '$accountID', 'added $username as $type to account',
				  			'$month', '$day', '$year', '$hour')";
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
		global $conn, $month, $day, $year, $hour;
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  username = '$username'
			      AND    RS_M <= '$month'
			      AND	 RS_D <= '$day'
			      AND	 RS_Y <= '$year'
			      AND	 RS_H <= '$hour'
			      AND    US_M IS NOT NULL
			      AND    UE_M IS NULL";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* Get list of devices a user currently has checked out */
	function getCurrentReservations($username){
		global $conn;
		$query = "SELECT deviceID, RS_M, RS_D, RS_Y, RS_H,
				  RE_M, RE_D, RE_Y, RE_H
			      FROM   device_trans
			      WHERE  username = '$username'
			      AND    US_M IS NULL";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* Pull all the current reservations for a device */
	function getReservationInfo($deviceID){
		global $conn, $month, $day, $year, $hour;
		$query = "SELECT *
				  FROM   device_trans
				  WHERE  deviceID = '$deviceID'
				  AND    RS_M >= '$month'
			      AND	 RS_D >= '$day'
			      AND	 RS_Y >= '$year'
			      AND	 RS_H >= '$hour'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	/* User searches open reservation times */
	function getAvailableReservations($sm, $sd, $sy, $sh, $em, $ed, $ey, $eh){
		global $conn;
		$query = "SELECT deviceID
				  FROM   device_trans
				  WHERE  RS_M <= '$sm'
			      AND	 RS_D <= '$sd'
			      AND	 RS_Y <= '$sy'
			      AND	 RS_H <= '$sh'
				  AND    RE_M >= '$sm'
			      AND	 RE_D >= '$sd'
			      AND	 RE_Y >= '$sy'
			      AND	 RE_H >= '$sh'
				  INTERSECT
				  SELECT deviceID
				  FROM   device_trans
				  WHERE  RS_M <= '$em'
			      AND	 RS_D <= '$ed'
			      AND	 RS_Y <= '$ey'
			      AND	 RS_H <= '$eh'
				  AND    RE_M >= '$em'
			      AND	 RE_D >= '$ed'
			      AND	 RE_Y >= '$ey'
			      AND	 RE_H >= '$eh'";
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
	function reserveDevice($deviceID, $username, $accountID, $sm, $sd, $sy, $sh, $em, $ed, $ey, $eh){
		global $conn, $month, $day, $year, $hour;
		// Check to see if reservation start time intersects with another reservation
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = '$deviceID'
			      AND    RS_M <= '$sm'
			      AND	 RS_D <= '$sd'
			      AND	 RS_Y <= '$sy'
			      AND	 RS_H <= '$sh'
				  AND    RE_M >= '$sm'
			      AND	 RE_D >= '$sd'
			      AND	 RE_Y >= '$sy'
			      AND	 RE_H >= '$sh'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] != NULL) return false;


		// Check to see if reservation end time intersects with another reservation
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = '$deviceID'
			      AND    RS_M <= '$em'
			      AND	 RS_D <= '$ed'
			      AND	 RS_Y <= '$ey'
			      AND	 RS_H <= '$eh'
				  AND    RE_M >= '$em'
			      AND	 RE_D >= '$ed'
			      AND	 RE_Y >= '$ey'
			      AND	 RE_H >= '$eh'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] != NULL) return false;


		// Get device info to calculate cost
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

		if(strcmp($unit, 'hours') == 0){
			$use_time = ($ey - $sy) * 365 * 24;  
			if($em >= $sm) $use_time += ($em - $sm) * 30 * 24;
			else           $use_time += ((12 - $em) + $sm) * 30 * 24;
			if($ed >= $sd) $use_time += ($ed - $sd) * 24;
			else           $use_time += ((30 - $ed) + $sd) * 24;
			if($eh >= $sh) $use_time += $eh - $sh;
			else           $use_time += (23 - $eh) + $sh;
		}
		else if(strcmp($unit, 'days') == 0){
			$use_time = ($ey - $sy) * 365;  
			if($em >= $sm) $use_time += ($em - $sm) * 30;
			else           $use_time += ((12 - $em) + $sm) * 30;
			if($ed >= $sd) $use_time += $ed - $sd;
			else           $use_time += (30 - $ed) + $sd;
		}
		if($use_time > $maxuse) return false;

		$amount = $use_time * $cost;


		// Check if account has enough balance
		$query = "SELECT balance
				  FROM   accounts
				  WHERE  accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
		if($row['BALANCE'] < $amount) return false;


		// Deduct cost from account
		$query = "UPDATE accounts
				  SET    balance = balance - '$amount'
				  WHERE  accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Insert new account transaction
		$query = "INSERT INTO acct_trans (username, accountID, type, m, d, y, h)
				  VALUES ('$username', '$accountID', 'billed $total dollar reservation fee for device $deviceID',
				  			'$month', '$day', '$year', '$hour')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Create reservation in device transactions table
		$query = "INSERT INTO device_trans (deviceID, username, 
											rs_m, rs_d, rs_y, rs_h, re_m, re_d, re_y, re_h,
											us_m, us_d, us_y, us_h, ue_m, ue_d, ue_y, ue_h)
				  VALUES ('$deviceID', '$username', 
				  			'$sm', '$sd', '$sy', '$sh', '$em', '$ed', '$ey', '$eh',
				  			NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return true;
	}

	/* User checkouts a device */
	function checkoutDevice($deviceID, $username){
		global $conn, $month, $day, $year, $hour;
		// Checks to see if user has previously reserved the device
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = '$deviceID'
			      AND    username = '$username'
			      AND    RS_M <= '$month'
			      AND	 RS_D <= '$day'
			      AND	 RS_Y <= '$year'
			      AND	 RS_H <= '$hour'
				  AND    RE_M >= '$month'
			      AND	 RE_D >= '$day'
			      AND	 RE_Y >= '$year'
			      AND	 RE_H >= '$hour'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] == NULL) return false;

		// Checks to see if device is available for use
		$query = "SELECT *
			      FROM   devices
			      WHERE  deviceID = '$deviceID'
			      AND    availability = 'available'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] == NULL) return false;

		// Update device transactions to track checkout 
		$query = "UPDATE device_trans
				  SET    US_M = '$month',
			         	 US_D = '$day',
			         	 US_Y = '$year',
			         	 US_H = '$hour'
				  WHERE  deviceID = '$deviceID'
				  AND    username = '$username'
				  AND    RS_M <= '$month'
			      AND	 RS_D <= '$day'
			      AND	 RS_Y <= '$year'
			      AND	 RS_H <= '$hour'
				  AND    RE_M >= '$month'
			      AND	 RE_D >= '$day'
			      AND	 RE_Y >= '$year'
			      AND	 RE_H >= '$hour'"; 
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
		global $conn, $month, $day, $year, $hour;
		// Get use time 
		$query = "SELECT us_m, us_d, us_y, us_h
				  FROM   device_trans
				  WHERE  username = '$username'
				  AND    deviceID = '$deviceID'
				  AND    ue_m IS NULL";
		
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

		$us_m = $row['US_M'];
		$us_d = $row['US_D'];
		$us_y = $row['US_Y'];
		$us_h = $row['US_H'];

		// Get device info and calculate cost possible overuse
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

		if(strcmp($unit, 'hours') == 0){
			$use_time  = ($year - $us_y) * 365 * 24;  
			if($month >= $us_m) $use_time += ($month - $us_m) * 30 * 24;
			else                $use_time += ((12 - $month) + $us_m) * 30 * 24;
			if($day >= $us_d)   $use_time += ($day - $us_d) * 24;
			else                $use_time += ((30 - $day) + $us_d) * 24;
			if($hour >= $us_h)  $use_time += $hour - $us_h;
			else                $use_time += (23 - $hour) + $us_h;
		}
		else if(strcmp($unit, 'days') == 0){
			$use_time  = ($year - $us_y) * 365;  
			if($month >= $us_m) $use_time += ($month - $us_m) * 30;
			else                $use_time += ((12 - $month) + $us_m) * 30;
			if($day >= $us_d)   $use_time += $day - $us_d;
			else                $use_time += (30 - $day) + $us_d;
		}

		// Charge account for late fee
		if($maxuse < $use_time){

			$total = $overuse * ($use_time - $maxuse); 
			
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
			$query = "INSERT INTO acct_trans (username, accountID, type, m, d, y, h)
					  VALUES ('$username', '$accountID', 'billed $total dollar late fee for device $deviceID',
					  			'$month', '$day', '$year', '$hour')";
			$stid = oci_parse($conn, $query);
			oci_execute($stid);
		}

		// Update device transactions to track return 
		$query = "UPDATE device_trans
				  SET    UE_M = '$month',
			         	 UE_D = '$day',
			         	 UE_Y = '$year',
			         	 UE_H = '$hour'
				  WHERE  deviceID = '$deviceID'
				  AND    username = '$username'
				  AND    US_M IS NOT NULL
				  AND    UE_M IS NULL";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "UPDATE devices
				  SET    availability = 'available'
				  WHERE  deviceID = '$deviceID'"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return true;
	}


	function findEndReserveTime($unit, $maxuse){
		global $conn, $month, $day, $year, $hour;
		$re_h = $hour;
		$re_m = $month;
		$re_d = $day;
		$re_y = $year;

		// Calculate reservation end time
		if(strcmp($unit, 'hours') == 0){
			for($i = $maxuse; $i > 0; $i--){
				$re_h++;
				if($re_h == 24){
					$re_h = 0;
					$re_d++;
				}
				if($re_d == 31){
					$re_d = 1;
					$re_m++;
				}
				if($re_m == 13){
					$re_m = 1;
					$re_y++;
				}
			}
		}
		else{
			for($i = $maxuse; $i > 0; $i--){
				$re_d++;
				if($re_d == 31){
					$re_d = 1;
					$re_m++;
				}
				if($re_m == 13){
					$re_m = 1;
					$re_y++;
				}
			}
		}

		$arr = array(
    		"re_h" => $re_h,
    		"re_m" => $re_m,
    		"re_y" => $re_y,
    		"re_d" => $re_d
		);
		return $arr;
	}


	// Calculate duration of reservation
	function calculateDuration($unit, $re_m, $re_d, $re_y, $re_h){
		global $conn, $month, $day, $year, $hour;
		if(strcmp($unit, 'hours') == 0){
			$use_time = ($re_y - $year) * 8760;  
			if($re_m >= $month) $use_time += ($re_m - $month) * 720;
			else                $use_time += ((12 - $re_m) + $month) * 720;
			if($re_d >= $day)   $use_time += ($re_d - $day) * 24;
			else                $use_time += ((30 - $re_d) + $day) * 24;
			if($re_h >= $hour)  $use_time += $re_h - $hour;
			else                $use_time += (23 - $re_h) + $hour;
		}
		else if(strcmp($unit, 'days') == 0){
			$use_time = ($re_y - $year) * 365;  
			if($re_m >= $month) $use_time += ($re_m - $month) * 30;
			else                $use_time += ((12 - $re_m) + $month) * 30;
			if($re_d >= $day)   $use_time += $re_d - $day;
			else                $use_time += (30 - $re_d) + $day;
		}
		return $use_time;
	}


	// Quick checkout: create reservation and checkout
	function quickCheckout($deviceID, $username, $accountID){
		global $conn, $month, $day, $year, $hour;

		// Check to see if reservation start time intersects with another reservation
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = '$deviceID'
			      AND    RS_M <= '$month'
			      AND	 RS_D <= '$day'
			      AND	 RS_Y <= '$year'
			      AND	 RS_H <= '$hour'
				  AND    RE_M >= '$month'
			      AND	 RE_D >= '$day'
			      AND	 RE_Y >= '$year'
			      AND	 RE_H >= '$hour'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] != NULL) return false;

		// Get device info to calculate reserve time
		$query = "SELECT unit, maxuse, cost
				  FROM   devices
				  WHERE  deviceID = '$deviceID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
		$unit     = $row['UNIT'];
		$maxuse   = $row['MAXUSE'];
		$cost     = $row['COST'];

		// Find start time + maxuse time
		$arr = findEndReserveTime($unit, $maxuse);
		$re_h = $arr['re_h'];
		$re_m = $arr['re_m'];
		$re_d = $arr['re_d'];
		$re_y = $arr['re_y'];


		// Check to see if reservation end time intersects with another reservation
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = '$deviceID'
			      AND    RS_M <= '$re_m'
			      AND	 RS_D <= '$re_d'
			      AND	 RS_Y <= '$re_y'
			      AND	 RS_H <= '$re_h'
				  AND    RE_M >= '$re_m'
			      AND	 RE_D >= '$re_d'
			      AND	 RE_Y >= '$re_y'
			      AND	 RE_H >= '$re_h'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] != NULL) return false;

		
		// Find reservation duration
		$use_time = calculateDuration($unit, $re_m, $re_d, $re_y, $re_h);
		if($maxuse < $use_time) return false;
		$amount = $use_time * $cost;

		// Check if account has enough balance
		$query = "SELECT balance
				  FROM   accounts
				  WHERE  accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
		if($row['BALANCE'] < $amount) return false;

		
		// Deduct cost from account
		$query = "UPDATE accounts
				  SET    balance = balance - '$amount'
				  WHERE  accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Insert new account transaction
		$query = "INSERT INTO acct_trans (username, accountID, type, m, d, y, h)
				  VALUES ('$username', '$accountID', 'billed $amount dollar reservation fee for device $deviceID',
				  			'$month', '$day', '$year', '$hour')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);


		// Create reservation in device transactions table
		$query = "INSERT INTO device_trans (deviceID, username, 
											rs_m, rs_d, rs_y, rs_h, re_m, re_d, re_y, re_h,
											us_m, us_d, us_y, us_h, ue_m, ue_d, ue_y, ue_h)
				  VALUES ('$deviceID', '$username', 
				  			'$month', '$day', '$year', '$hour', '$re_m', '$re_d', '$re_y', '$re_h',
				  			NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		// Update device transactions to track checkout 
		$query = "UPDATE device_trans
				  SET    US_M = '$month',
			         	 US_D = '$day',
			         	 US_Y = '$year',
			         	 US_H = '$hour'
				  WHERE  deviceID = '$deviceID'
				  AND    username = '$username'
				  AND    RS_M <= '$month'
			      AND	 RS_D <= '$day'
			      AND	 RS_Y <= '$year'
			      AND	 RS_H <= '$hour'
				  AND    RE_M >= '$month'
			      AND	 RE_D >= '$day'
			      AND	 RE_Y >= '$year'
			      AND	 RE_H >= '$hour'"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "UPDATE devices
				  SET    availability = 'in use'
				  WHERE  deviceID = '$deviceID'"; 
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		return true;
	}
?>