<?php
# ... Important Data
include("conf/db-config.php");
include("fn/sys-functions.php");

# ### ###  ###  ###  ###  Statistical Counts
if(isset($_POST['request_msg']))
//if(isset($_POST))
{
	# ... ... ... Process Request Message
	//$request_msg = mysql_real_escape_string($_POST['request_msg']);
	$request_msg = $_POST['request_msg'];
	$request_msg_details = array();
	$request_msg_details = explode('#', $request_msg);
	$operation_type = $request_msg_details[0];

	# ... ... ... Response Message
	$sys_response = array();


	# ... ... ... F1: Get Connection Status .. ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
	if ($operation_type=="GET_CONN_STATUS") {
		$sys_response = GetConnStatus($request_msg);
	}

	# ... ... ... F2: GetDevConfigs... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
	if ($operation_type=="GET_DEV_CONFIGS") {
		$sys_response = GetDevConfigs($request_msg);
	}

	# ... ... ... F3: Get Temp OTP Access Pin ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
	if ($operation_type=="GET_TOKEN") {
		$sys_response = GetToken($request_msg);
	}

	# ... ... ... F4: Update Pin From Device ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
	if ($operation_type=="UPDATE_DEV_PIN") {
		$sys_response = UpdateDevPinFromDevice($request_msg);
	}
	

	$responseJSON = json_encode($sys_response, true);
 	echo $responseJSON;
 	exit();
}





?>

