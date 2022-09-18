<?php
# ... Important Data
session_start();
include("conf/no-session.php");

# ### ###  ###  ###  ###  Statistical Counts
if(isset($_POST['captcha_data']))
{
	$response_stats = array();

	# ... Captcha
	$sys_captcha = trim($_SESSION['digit']);
	$usr_captcha = $_POST['captcha_data'];

	if ($usr_captcha==$sys_captcha) {
		$response_stats["captcha_response_code"] = "OK";
		$response_stats["captcha_response_msg"] = "Captcha is valid";
	} else {
		$response_stats["captcha_response_code"] = "ERROR";
		$response_stats["captcha_response_msg"] = "Invalid Captcha supplied";
	}

	$responseJSON = json_encode($response_stats);
 	echo $responseJSON;
 	exit();
}

?>

