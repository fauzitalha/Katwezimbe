<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://10.99.43.220:9443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "demo";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

// ... BUILDING THE MIFOS LOAN MESSAGE

$LOAN_ACCT_CORE_ID = "22";
$transactionAmount = "3000000";
$actualDisbursementDate =  date('d F Y', strtotime( date("ymd",time()) ));
$NOTE = "Loan Disbursed";

$data_string = '
{
  "locale": "en",
  "dateFormat": "dd MMMM yyyy",
  "transactionAmount": "'.$transactionAmount.'",
  "actualDisbursementDate" : "'.$actualDisbursementDate.'",
  "note": "'.$NOTE.'"
}
';

$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LOAN_ACCT_CORE_ID."?command=disburseToSavings&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";





?>