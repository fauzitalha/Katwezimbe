<?php
// ... Important Files
session_start();
include("conf/no-session.php");

// ... hand over the sessions
$mgtzaazhost = $_SESSION['mgtzaazhost'];
$mgtzaazrssu = $_SESSION['mgtzaazrssu'];
$mgtzaazdwwp = $_SESSION['mgtzaazdwwp'];
$mgtzaazbank = $_SESSION['mgtzaazbank'];

// ... Recording the Log Out Acction
$USER_ID = $_SESSION['UPR_USER_ID'];
$LOG_TYPE = "LOGOUT - TIMEDOUT";
$LOG_DATE = date("Y-m-d H:i:s", time());
$LOG_DETAILS = "";
$SRC_IP_ADDRESS = $_SERVER['REMOTE_ADDR'];	
FlagUserLogInStatus($USER_ID, "NO");
$is_log_recorded = LogUserAccessLog($USER_ID,$LOG_TYPE,$LOG_DATE,$LOG_DETAILS,$SRC_IP_ADDRESS); 

if ( $is_log_recorded=="YES" )
{
	// ... destroy all sessions
	session_destroy();

	// ... hand back the sessions
	$_SESSION['mgtzaazhost'] = $mgtzaazhost;
	$_SESSION['mgtzaazrssu'] = $mgtzaazrssu;
	$_SESSION['mgtzaazdwwp'] = $mgtzaazdwwp;
	$_SESSION['mgtzaazbank'] = $mgtzaazbank;

	echo $_SESSION['mgtzaazhost'];

	// ... Jump to home page
	$page = "index";
	echo '<meta http-equiv="refresh" content="0; URL='.$page.'" />';
}
?>

