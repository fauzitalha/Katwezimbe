<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

$CLIENT_ID = "2";
$file_name = "WORK_ID";
$file_path = "D:/__#/wamp/www/wvi-cst/files/activation_requests/DF00000030/PP_20190419110546_DF00000030.jpg";
$file_type = mime_content_type($file_path);
//echo $file_type;
$description = "National ID";



$file_data_string = array(
	'file' => new CurLFile($file_path, $file_type, $file_name),
	'name' => $file_name,
	'description'=>$description
);  


$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$CLIENT_ID."/documents?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos_multipart($endpoint_url, $method, $username, $password, $file_data_string);
echo "<pre>".print_r($response_details,true)."</pre>";





?>