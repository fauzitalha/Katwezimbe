<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

// ... Variables
$from_client_id = "9";
$from_account_Id = "4";

$to_client_id = "12";
$to_account_Id = "6";

$transaction_date = date('d F Y', strtotime( date("ymd",time()) ));
$transfer_amount = "0";
$narration = "TEST TRANSFER";

$data_string = '
{
	"fromOfficeId": 1,
	"fromClientId": "'.$from_client_id.'",
	"fromAccountType": 2,
	"fromAccountId": "'.$from_account_Id.'",
	"toOfficeId": 1,
	"toClientId": "'.$to_client_id.'",
	"toAccountType": 2,
	"toAccountId": "'.$to_account_Id.'",
	"dateFormat": "dd MMMM yyyy",
	"locale": "en",
	"transferDate": "'.$transaction_date.'",
	"transferAmount": "'.$transfer_amount.'",
	"transferDescription": "'.$narration.'"
}
';

$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounttransfers?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";





?>