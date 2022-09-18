<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

$clientId = "12";
$productId = "1";
$requestedShares = "344";
$externalId = "KHG0004567";
$submittedDate = date('d F Y', strtotime( date("ymd",time()) ));
$applicationDate = date('d F Y', strtotime( date("ymd",time()) ));
$savingsAccountId = "4";

$data_string = '
{
	"clientId": "'.$clientId.'",
	"productId": '.$productId.',
	"requestedShares": '.$requestedShares.',
	"externalId": "'.$externalId.'",
	"locale": "en",
	"dateFormat": "dd MMMM yyyy",
	"submittedDate": "'.$submittedDate.'",
	"applicationDate": "'.$applicationDate.'",
	"savingsAccountId": '.$savingsAccountId.'
}
';


$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounts/share?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";


?>