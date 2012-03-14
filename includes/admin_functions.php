<?
	function deviceUsage($deviceID, $sm, $sd, $sy, $sh, $em, $ed, $ey, $eh){
		global $conn;
		$query = "SELECT *
			      FROM   device_trans
			      WHERE  deviceID = '$deviceID'
			      AND    (US_M >= '$sm'
			      OR	 US_D >= '$sd'
			      OR	 US_Y >= '$sy'
			      OR	 US_H >= '$sh'
				  OR	 UE_M <= '$em'
			      OR	 UE_D <= '$ed'
			      OR	 UE_Y <= '$ey'
			      OR	 UE_H <= '$eh')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	function devicesInUse(){
		global $conn;
		$query = "SELECT DT.deviceID, U.name, U.username, U.isManager, U.email, U.phone
				  FROM   users U LEFT JOIN device_trans DT ON U.username = DT.username
				  WHERE  DT.ue_m IS NULL
				  AND    DT.us_m IS NOT NULL";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	function getUserList(){
		global $conn;
		$query = "SELECT * FROM users";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	function getUserHistoryDevices($username){
		global $conn, $month, $day, $year, $hour;
		$m = $month;
		$d = $day;
		$y = $year;
		$h = $hour;
		if($m == 1){
			$m = 12;
			$y--;
		}
		else $m--;
		$query = "SELECT * 
				  FROM   device_trans 
				  WHERE  username='$username'
				  AND	 RS_M >= '$m'
			      AND	 RS_D >= '$d'
			      AND	 RS_Y >= '$y'
			      AND	 RS_H >= '$h'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	function getUserHistoryAccounts($username){
		global $conn, $month, $day, $year, $hour;
		$m = $month;
		$d = $day;
		$y = $year;
		$h = $hour;
		if($m == 1){
			$m = 12;
			$y--;
		}
		else $m--;
		$query = "SELECT * 
				  FROM   acct_trans 
				  WHERE  username='$username'
				  AND	 m >= '$m'
			      AND	 d >= '$d'
			      AND	 y >= '$y'
			      AND	 h >= '$h'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	function getAccountList(){
		global $conn;
		$query = "SELECT * FROM accounts";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	function closeAccount($accountID){
		global $conn;

		$query = "UPDATE accounts SET status = 'inactive' WHERE accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "SELECT * FROM acct_trans WHERE accountID = '$accountID'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	function blockUser($username){
		global $conn;
		$query = "UPDATE users SET status = 'f' WHERE username='$username'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
	}

	function unblockUser($username){
		global $conn;
		$query = "UPDATE users SET status = 't' WHERE username='$username'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
	}

	function deleteAccounts(){
		global $conn;
		$query = "DELETE FROM acct_trans 
				  WHERE accountID = (
				  	SELECT accountID
				  	FROM   accounts
				  	WHERE status = 'inactive')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "DELETE FROM user_accts 
				  WHERE accountID = (
				  	SELECT accountID
				  	FROM   accounts
				  	WHERE status = 'inactive')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "DELETE FROM accounts WHERE status = 'inactive'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
	}

	function deleteUsers(){
		global $conn;
		$query = "DELETE FROM user_accts 
				  WHERE username = (
				  	SELECT username
				  	FROM   users
				  	WHERE status = 'f')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "DELETE FROM device_trans 
				  WHERE username = (
				  	SELECT username
				  	FROM   users
				  	WHERE status = 'f')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "DELETE FROM extensions 
				  WHERE username = (
				  	SELECT username
				  	FROM   users
				  	WHERE status = 'f')";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);

		$query = "DELETE FROM users WHERE status = 'f'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
	}

	function retireDevice($deviceID){
		global $conn;
		$query = "SELECT * 
				  FROM   devices
				  WHERE  deviceID = '$deviceID'
				  AND    availability = 'in use'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_NUM + OCI_RETURN_NULLS);
		if($row[0] != NULL) return false;

		$query = "UPDATE devices
				  SET    availability = 'no' 
				  WHERE  deviceID = '$deviceID'
				  AND    availability != 'in use'";
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return true;
	}
?>