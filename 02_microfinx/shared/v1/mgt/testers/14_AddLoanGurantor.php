<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

// ... BUILDING THE MIFOS LOAN MESSAGE

$LOAN_ACCT_CORE_ID = "6";
$firstname = "Lutaaya";
$lastname = "Fauzi";

$data_string = '
{
  guarantorTypeId:1,
  firstname: "'.$firstname.'",
  lastname: "'.$lastname.'"
}
';

$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LOAN_ACCT_CORE_ID."/guarantors?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";





?>