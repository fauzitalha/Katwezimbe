<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://10.99.43.220:9443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "demo";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

// ... Variables
$savingsAcctId = "28";
$transaction_date = date('d F Y', strtotime(date("ymd", time())));
$amount = "34000";
$paymentTypeId = "14";   // .... MOMO TRANSFER (mtn)
$accountNumber = "256776899089";
$checkNumber = "256776899089";
$routingCode = "256776899089";
$receiptNumber = "1600559594944";  // ... REF TO MTN
$bankNumber = "1600559594944";


$data_string = '
{
    "locale": "en",
    "dateFormat": "dd MMMM yyyy",
    "transactionDate": "'.$transaction_date.'",
    "transactionAmount": "'.$amount. '",
    "paymentTypeId": "'.$paymentTypeId. '",
    "accountNumber":  "' . $accountNumber . '",
    "checkNumber":  "' . $checkNumber . '",
    "routingCode":  "' . $routingCode . '",
    "receiptNumber":  "' . $receiptNumber . '",
    "bankNumber":  "' . $bankNumber . '"
}
';

$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER. "/api/v1/savingsaccounts/". $savingsAcctId. "/transactions?command=withdrawal&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";



?>