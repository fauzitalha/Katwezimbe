<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

//  clientId, productId, submittedDate, savingsAccountId, requestedShares, applicationDate
$data_string = '
{
	"clientId": "26",
	"productId": 1,
	"requestedShares": 2,
	"submittedDate": "23 Oct 2018",
	"applicationDate": "23 Oct 2018",
	"locale": "en",
	"dateFormat": "dd MMMM yyyy",
	"savingsAccountId": 24
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

// ==========================================================  APPROVE PURCHASE SHARES ============================================================== //



?>