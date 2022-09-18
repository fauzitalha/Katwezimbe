<?php
// ... Important Files
session_start();
include("conf/no-session.php");

// ... Recording the Log Out Acction
$CUST_ID = $_SESSION['CST_USR_ID'];
$CHANNEL_ID = "WEB";
$LOG_DETAILS = "SIGN_OUT";
$LOG_DATE = GetCurrentDateTime(); 
$SRC_IP_ADDRESS = GetClientIp();

$q = "INSERT INTO cstmrs_lgn_log(CUST_ID, CHANNEL_ID, LOG_DETAILS, LOG_DATE, SRC_IP_ADDRESS) VALUES('$CUST_ID', '$CHANNEL_ID', '$LOG_DETAILS', '$LOG_DATE', '$SRC_IP_ADDRESS')";
$exec_response = array();
$exec_response = ExecuteEntityInsert($q);

if ( $exec_response["RESP"]=="EXECUTED" )
{
	// ... destroy all sessions
	session_destroy();

	// ... Jump to home page
	$page = "index";
	echo '<meta http-equiv="refresh" content="0; URL='.$page.'" />';
}
?>

