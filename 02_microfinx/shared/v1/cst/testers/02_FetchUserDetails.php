<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$details = "NOT_UPDATED";
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";
$USER_ID = "8";
$data_string = "";  


$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/users/".$USER_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "GET";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";
//die();

// ... Processing the response
//if (sizeof($response_details)==4) {
//	$details = "UPDATED";
//}




?>