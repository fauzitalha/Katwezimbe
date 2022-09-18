<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://10.99.43.220:9443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "demo";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

$data_string = '
{
    "locale": "en" ,
    "officeId": 1,
    "dateFormat": "dd MMMM yyyy",
    "transactionDate": "27 June 2021",
    "comments": "Test Transaction",
    "currencyCode": "UGX",
    "debits":
    [
        { "glAccountId":10, "amount":5505 }
    ],   
    "credits":
    [
        { "glAccountId":266, "amount":5505 }
    ],
    "checkNumber": "",
    "receiptNumber": "TXC00067"
}
';


$endpoint_url = $MIFOS_IP . "/" . $MIFOS_PROVIDER . "/api/v1/journalentries?tenantIdentifier=" . $MIFOS_TENANT_ID . "&pretty=true";
$method = "POST";
$username = $MIFOS_USERNAME;
$password = $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>" . print_r($response_details, true) . "</pre>";

// ==========================================================  APPROVE PURCHASE SHARES ============================================================== //
