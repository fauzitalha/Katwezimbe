<?php
# ... Display all application errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if( !isset($_SESSION['CST_USR_ID']) ){ // ... if there is no created session.

	$page = "index";
	echo '<meta http-equiv="refresh" content="0; URL='.$page.'" />';
}
else
{
	# ... Load Configurations
	include("conf/db-config.php");
	include("fn/db-functions.php");
	include("fn/sys-functions.php");
	include("fn/core-api-client.php");

	include("conf/app-config.php");
	include("conf/css-functions.php");
	include("conf/js-functions.php");
	include("conf/navigations.php");

	# ... Load Other Configs
	include("aes/AES256.php");
	require 'fezzmail/r/PHPMailerAutoload.php';

}
?>