<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

/*
Gender Ids
--------------------------------
Male  - 13
Female- 14
Other - 25

Client Type Ids
--------------------------------
KHSK - 23
KHSKG - 24

Client Classification Ids
--------------------------------
Retail Member - 21
Corporate Member - 22

*/
$data_string = '
{
	"officeId": 1,
	"firstname": "Arnold",
	"middlename": "Ruth",
	"lastname": "Mukungu",
	"externalId": "786YYH7YY",
	"mobileNo": "25678090960", 
  "genderId": "14", 
  "clientTypeId": "23", 
  "clientClassificationId": "21",   
	"dateFormat": "dd MMMM yyyy",
	"locale": "en",
	"active": true,
	"activationDate": "19 April 2019",
  "submittedOnDate":"19 April 2019"
}
';  


$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";





?>