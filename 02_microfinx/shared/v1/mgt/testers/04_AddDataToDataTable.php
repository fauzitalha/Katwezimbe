<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";
$CLIENT_ID = "2";


$data_string = '
{
	"Email": "Petra@yakov.ru",
	"WorkID": "347890WRK",
	"NationalId": "CM9204567890",
	"Physical_Address": "Namugongo West",
	"Date_of_Birth": "1996-10-14"
}
';  


$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/datatables/OtherDetails/".$CLIENT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";





?>