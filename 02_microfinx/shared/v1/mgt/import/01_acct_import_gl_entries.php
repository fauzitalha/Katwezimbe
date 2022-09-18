<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://octafinance.slankinit.com:5001";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

$file_path = "f_failed_f5.csv";

// ... connecting to the csv file
$x=0;
$file_handle = fopen($file_path, "r");
while (!feof($file_handle) ) {

	$line_of_text = fgetcsv($file_handle, 1024);

	# ... Reading the Data
	$transactionDate = trim($line_of_text[0]);
	$comments = trim($line_of_text[1]);
	$currencyCode = trim($line_of_text[2]);
	$DR_ACCT_ID = trim($line_of_text[3]);
	$DR_AMT = trim($line_of_text[4]);
	$CR_ACCT_ID = trim($line_of_text[5]);
	$CR_AMT = trim($line_of_text[6]);
	$receiptNumber = trim($line_of_text[7]);
	$checkNumber = trim($line_of_text[8]);

  # ... 01:  Data .......................................................................#
	$data_string = '
	{
		"locale": "en" ,
		"officeId": 1,
		"dateFormat": "dd MMMM yyyy",
		"transactionDate": "'. $transactionDate.'",
		"comments": "'. $comments.'",
		"currencyCode": "'. $currencyCode.'",
		"debits":
		[
			{ "glAccountId":'. $DR_ACCT_ID.', "amount":'. $DR_AMT.' }
		],   
		"credits":
		[
			{ "glAccountId":'. $CR_ACCT_ID.', "amount":'. $CR_AMT.' }
		],
		"checkNumber": "'. $checkNumber.'",
		"receiptNumber": "'. $receiptNumber.'"
	}
	';


	$endpoint_url = $MIFOS_IP . "/" . $MIFOS_PROVIDER . "/api/v1/journalentries?tenantIdentifier=" . $MIFOS_TENANT_ID . "&pretty=true";
	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	// ... sending to mifos
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
	echo "<pre>".print_r($response_details,true)."</pre>";

  # ... 02:  Response Details .......................................................................#
	if (isset($response_details["transactionId"])) {
		$transactionId = $response_details["transactionId"];
		echo ($x+1)."->".$receiptNumber . "|" . $transactionId . "|success<br>";
	} else {
		echo ($x + 1) . "->" . $receiptNumber . "|Failed" ."<br>";
	}# ...END..IFF

	$x++;
}
