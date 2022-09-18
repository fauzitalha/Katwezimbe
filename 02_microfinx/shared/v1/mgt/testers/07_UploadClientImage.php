<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";
$CLIENT_ID = "2";

# ... GIF, JPEG, PNG
// ... processing the image paths
$path = "D:/__#/wamp/www/wvi-cst/files/activation_requests/DF00000027/NATIONALID_DF00000027.png";
$type = pathinfo($path, PATHINFO_EXTENSION);
//echo $type;
$data = file_get_contents($path);
$base64 = "data:image/" . $type . ";base64," . base64_encode($data);

$data_string = $base64;  


$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$CLIENT_ID."/images?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";





?>