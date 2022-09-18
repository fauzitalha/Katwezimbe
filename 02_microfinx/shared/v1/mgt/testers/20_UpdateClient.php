<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

//bart@yahoo.com
$data_string = '
{
    "mobileNo": "256700900911"
}
';


$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/6669?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "PUT";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";


?>