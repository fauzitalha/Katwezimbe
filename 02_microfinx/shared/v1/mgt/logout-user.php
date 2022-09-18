<?php
// ... Important Files
session_start();
include("conf/no-session.php");

// ... Recording the Log Out Acction
$USER_ID = $_SESSION['UPR_USER_ID'];
$LOG_TYPE = "LOGOUT";
$LOG_DATE = date("Y-m-d H:i:s", time());
$LOG_DETAILS = "";
$SRC_IP_ADDRESS = $_SERVER['REMOTE_ADDR'];	
FlagUserLogInStatus($USER_ID, "NO");
$is_log_recorded = LogUserAccessLog($USER_ID,$LOG_TYPE,$LOG_DATE,$LOG_DETAILS,$SRC_IP_ADDRESS); 

if ( $is_log_recorded=="YES" )
{
	// ... destroy all sessions
	session_destroy();

	// ... Jump to home page
	$page = "index";
	echo '<meta http-equiv="refresh" content="0; URL='.$page.'" />';
}
?>

