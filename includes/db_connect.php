<?php

/* Initializes the connection to the Oracle DB */

	$db = "(DESCRIPTION =
	      	(ADDRESS = (PROTOCOL = TCP)(HOST = uml.cs.ucsb.edu)(PORT = 1521))
	       	(CONNECT_DATA = (SERVICE_NAME = XEXDB))
	      )";

	global $conn;
	
	$conn = oci_connect("alindogan", "3249430", $db);
	if (!$conn) {
	   $m = oci_error();
	   echo $m['message'], "\n";
	   exit;
	}

?>