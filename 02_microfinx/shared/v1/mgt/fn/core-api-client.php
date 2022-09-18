<?php
# ... ... ... IMPORT MESSAGE SENDER ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
include "core-api-msg-sender.php";


# ... ... ... F1: Authenticate Mifos User Accounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function AuthenticateUserCredentials($username, $password, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/authentication?username=".$username."&password=".$password."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);


	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 04: Track Response or Failure  (CUSTOM)
	if (sizeof($response_details)==5) {
		$response_msg["RESP_FLG"] = "FAIL";
	} else if (sizeof($response_details)==9) {
		$response_msg["RESP_FLG"] = "SUCCESS";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F2: Authenticate Mifos User Accounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchUserDetailsFromCore($user_id, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/users/".$user_id."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 04: Track Response or Failure  (CUSTOM)
	if (sizeof($response_details)==5) {
		$response_msg["RESP_FLG"] = "FAIL";
	} else if (sizeof($response_details)==9) {
		$response_msg["RESP_FLG"] = "SUCCESS";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F3: Fetch List of Users ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function FetchAllUsersFromCore($MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/users?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 04: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 05: Return response message
	return $response_msg;
}


# ... ... ... F4: Create New Client ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function CreateNewClient($CORE_RQST_MSG, $MIFOS_CONN_DETAILS){
	
	# ... 01: Prepare Request Message
	$data_string = $CORE_RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 04: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 05: Return response message
	return $response_msg;
}


# ... ... ... F5: Fetch List of Users ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function PostDataToDataTable($DATATABLE_NAME, $DATATABLE_ENTITY_ID, $DATATABLE_RQST_MSG, $MIFOS_CONN_DETAILS){

	# ... 01: Prepare Request Message
	$data_string = $DATATABLE_RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

 	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/datatables/".$DATATABLE_NAME."/".$DATATABLE_ENTITY_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 04: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 05: Return response message
	return $response_msg;
}


# ... ... ... F6: FetchCustomerDetailsFromCore ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustomerDetailsFromCore($cust_id, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$cust_id."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F7: FetchCustomerDetailsFromCore ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function UploadCustomerImage($cust_id, $img_data, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $img_data;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$cust_id."/images?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos_images($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F8: UploadClientDocumentToCore ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function UploadClientDocumentToCore($cust_id, $doc_data, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $doc_data;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$cust_id."/documents?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos_multipart($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F9: FetchSavingsProducts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsProducts($MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsproducts?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F10: CreateSavingsApplication ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function CreateSavingsApplication($RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F11: ApproveSavingsApplication ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ApproveSavingsApplication($SVNGS_ACCT_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$SVNGS_ACCT_ID."?command=approve&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F12: ActivateSavingsApplication ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ActivateSavingsApplication($SVNGS_ACCT_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$SVNGS_ACCT_ID."?command=activate&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F13: FetchLoanProducts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanProducts($MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loanproducts?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F13: FetchLoanProducts (WALKIN) ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanProducts_Walkin($MIFOS_CONN_DETAILS){

	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loanproducts?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$loan_products_list = array();
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03:  Formatting Response
	for ($i=0; $i < sizeof($response_details) ; $i++) { 
  	$loan_product_item = $response_details[$i];

  	$loan_product = array(

  		// ... Procust description
  		"pdt_id"=>isset($loan_product_item["id"])? $loan_product_item["id"] : "",
  		"pdt_name"=>isset($loan_product_item["name"])? $loan_product_item["name"] : "",
  		"pdt_short_name"=>isset($loan_product_item["shortName"])? $loan_product_item["shortName"] : "",
  		"pdt_descrition"=>isset($loan_product_item["description"])? $loan_product_item["description"] : "",
  		"pdt_status"=>isset($loan_product_item["status"])? $loan_product_item["status"] : "",

  		// ... Principal Limits
  		"min_principal"=>isset($loan_product_item["minPrincipal"])? $loan_product_item["minPrincipal"] : "",
  		"default_principal"=>isset($loan_product_item["principal"])? $loan_product_item["principal"] : "",
  		"max_principal"=>isset($loan_product_item["maxPrincipal"])? $loan_product_item["maxPrincipal"] : "",

  		// ... Count of Repayments
  		"min_number_of_repayments"=>isset($loan_product_item["minNumberOfRepayments"])? $loan_product_item["minNumberOfRepayments"] : "",
  		"default_number_of_repayments"=>isset($loan_product_item["numberOfRepayments"])? $loan_product_item["numberOfRepayments"] : "",
  		"max_number_of_repayments"=>isset($loan_product_item["maxNumberOfRepayments"])? $loan_product_item["maxNumberOfRepayments"] : "",

  		// ... Repayment Frequency
  		"repayment_every"=>isset($loan_product_item["repaymentEvery"])? $loan_product_item["repaymentEvery"] : "",
  		"repayment_frequency_type_id"=>isset($loan_product_item["repaymentFrequencyType"]["id"])? $loan_product_item["repaymentFrequencyType"]["id"] : "",
  		"repayment_frequency_type_code"=>isset($loan_product_item["repaymentFrequencyType"]["code"])? $loan_product_item["repaymentFrequencyType"]["code"] : "",
  		"repayment_frequency_type_value"=>isset($loan_product_item["repaymentFrequencyType"]["value"])? $loan_product_item["repaymentFrequencyType"]["value"] : "",

  		// ... Interest Rates Payable per Period
  		"min_interest_rate_per_period"=>isset($loan_product_item["minInterestRatePerPeriod"])? $loan_product_item["minInterestRatePerPeriod"] : "",
  		"default_interest_rate_per_period"=>isset($loan_product_item["interestRatePerPeriod"])? $loan_product_item["interestRatePerPeriod"] : "",
  		"max_interest_rate_per_period"=>isset($loan_product_item["maxInterestRatePerPeriod"])? $loan_product_item["maxInterestRatePerPeriod"] : "",

			// ... Interest Rate Frequency Type
  		"interest_rate_frequency_type_id"=>isset($loan_product_item["interestRateFrequencyType"]["id"])? $loan_product_item["interestRateFrequencyType"]["id"] : "",
  		"interest_rate_frequency_type_code"=>isset($loan_product_item["interestRateFrequencyType"]["code"])? $loan_product_item["interestRateFrequencyType"]["code"] : "",
  		"interest_rate_frequency_type_value"=>isset($loan_product_item["interestRateFrequencyType"]["value"])? $loan_product_item["interestRateFrequencyType"]["value"] : "",

  		// ... Annual Interest Rate
  		"annual_interest_rate"=>isset($loan_product_item["annualInterestRate"])? $loan_product_item["annualInterestRate"] : "",

			// ... Amortization Type Attributes
  		"amortization_type_id"=>isset($loan_product_item["amortizationType"]["id"])? $loan_product_item["amortizationType"]["id"] : "",
  		"amortization_type_code"=>isset($loan_product_item["amortizationType"]["code"])? $loan_product_item["amortizationType"]["code"] : "",
  		"amortization_type_value"=>isset($loan_product_item["amortizationType"]["value"])? $loan_product_item["amortizationType"]["value"] : "",

  		// ... Interest Type Definition
  		"interest_type_id"=>isset($loan_product_item["interestType"]["id"])? $loan_product_item["interestType"]["id"] : "",
  		"interest_type_code"=>isset($loan_product_item["interestType"]["code"])? $loan_product_item["interestType"]["code"] : "",
  		"interest_type_value"=>isset($loan_product_item["interestType"]["value"])? $loan_product_item["interestType"]["value"] : "",

  		// ... Interest Calculation Period Type
  		"interest_calculation_period_type_id"=>isset($loan_product_item["interestCalculationPeriodType"]["id"])? $loan_product_item["interestCalculationPeriodType"]["id"] : "",
  		"interest_calculation_period_type_code"=>isset($loan_product_item["interestCalculationPeriodType"]["code"])? $loan_product_item["interestCalculationPeriodType"]["code"] : "",
  		"interest_calculation_period_type_value"=>isset($loan_product_item["interestCalculationPeriodType"]["value"])? $loan_product_item["interestCalculationPeriodType"]["value"] : "",

  		// ... Transaction Processing Strategy 
  		"transaction_processing_strategy_id"=>isset($loan_product_item["transactionProcessingStrategyId"])? $loan_product_item["transactionProcessingStrategyId"] : "",
  		"transaction_processing_strategy_name"=>isset($loan_product_item["transactionProcessingStrategyName"])? $loan_product_item["transactionProcessingStrategyName"] : "",

  	);

  	// ... Adding Loan Product to Loan Products List
		$loan_products_list[$i] = $loan_product;
  }

	# ... 04: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 06: Attach core respond
	$response_msg["CORE_RESP"] = $loan_products_list;

	# ... 07: Return response message
	return $response_msg;
}


# ... ... ... F14: FetchLoanProducts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchShareProductsByCustomRpt($MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/FetchSharesPdts?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F15: FetchSavingsProductById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .
function FetchSavingsProductById($PDT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsproducts/".$PDT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F16: FetchLoanProductsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanProductById($PDT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loanproducts/".$PDT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F17: GetCustSavingsAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function InquireSvgsAcctDetails($account_no, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/InquireSvgsAcctDetails?R_account_no=".$account_no."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F17: FetchShareProducts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchShareProducts($MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/products/share?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F18: FetchShareProductById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchShareProductById($PDT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/products/share/".$PDT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F18: GetCorePdtDetails ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCorePdtDetails($PDT_TYPE_ID, $PDT_ID, $MIFOS_CONN_DETAILS){

	$core_pdt_details = array();
	if ($PDT_TYPE_ID=="LOAN") {
		$response_msg = FetchLoanProductById($PDT_ID, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];
    $pdt = $response_msg["CORE_RESP"];
    $id = $pdt["id"];
    $name = $pdt["name"];
    $shortName = $pdt["shortName"];

    $core_pdt_details["PDT_ID"] = $id;
    $core_pdt_details["PDT_NAME"] = $name;
    $core_pdt_details["PDT_SHORT"] = $shortName;
	}
	else if($PDT_TYPE_ID=="SVNG") {
		$response_msg = FetchSavingsProductById($PDT_ID, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];
    $pdt = $response_msg["CORE_RESP"];
    $id = $pdt["id"];
    $name = $pdt["name"];
    $shortName = $pdt["shortName"];

    $core_pdt_details["PDT_ID"] = $id;
    $core_pdt_details["PDT_NAME"] = $name;
    $core_pdt_details["PDT_SHORT"] = $shortName;
	}

	return $core_pdt_details;
}


# ... ... ... F19: FetchOAFTAccountsByCustomRpt ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchOAFTAccountsByCustomRpt($MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetOAFTAccounts?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F20: FetchOAFTAccountsByCustomRpt ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsAccountDetailsById($SVNG_ACCT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$SVNG_ACCT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F21: FetchLoanProductDetailsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanProductDetailsById($PDT_ID, $MIFOS_CONN_DETAILS){

	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loanproducts/".$PDT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$loan_product_item = array();
	$loan_product_item = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	$loan_product = array(
		// ... Procust description
		"pdt_id"=>$loan_product_item["id"],
		"pdt_name"=>$loan_product_item["name"],
		"pdt_short_name"=>$loan_product_item["shortName"],
		"pdt_descrition"=>$loan_product_item["description"],
		"pdt_status"=>$loan_product_item["status"],

		// ... Principal Limits
		"min_principal"=>$loan_product_item["minPrincipal"],
		"default_principal"=>$loan_product_item["principal"],
		"max_principal"=>$loan_product_item["maxPrincipal"],

		// ... Count of Repayments
		"min_number_of_repayments"=>$loan_product_item["minNumberOfRepayments"],
		"default_number_of_repayments"=>$loan_product_item["numberOfRepayments"],
		"max_number_of_repayments"=>$loan_product_item["maxNumberOfRepayments"],

		// ... Repayment Frequency
		"repayment_every"=>$loan_product_item["repaymentEvery"],
		"repayment_frequency_type_id"=>$loan_product_item["repaymentFrequencyType"]["id"],
		"repayment_frequency_type_code"=>$loan_product_item["repaymentFrequencyType"]["code"],
		"repayment_frequency_type_value"=>$loan_product_item["repaymentFrequencyType"]["value"],

		// ... Interest Rates Payable per Period
		"min_interest_rate_per_period"=>$loan_product_item["minInterestRatePerPeriod"],
		"default_interest_rate_per_period"=>$loan_product_item["interestRatePerPeriod"],
		"max_interest_rate_per_period"=>$loan_product_item["maxInterestRatePerPeriod"],

		// ... Interest Rate Frequency Type
		"interest_rate_frequency_type_id"=>$loan_product_item["interestRateFrequencyType"]["id"],
		"interest_rate_frequency_type_code"=>$loan_product_item["interestRateFrequencyType"]["code"],
		"interest_rate_frequency_type_value"=>$loan_product_item["interestRateFrequencyType"]["value"],

		// ... Annual Interest Rate
		"annual_interest_rate"=>$loan_product_item["annualInterestRate"],

		// ... Amortization Type Attributes
		"amortization_type_id"=>$loan_product_item["amortizationType"]["id"],
		"amortization_type_code"=>$loan_product_item["amortizationType"]["code"],
		"amortization_type_value"=>$loan_product_item["amortizationType"]["value"],

		// ... Interest Type Definition
		"interest_type_id"=>$loan_product_item["interestType"]["id"],
		"interest_type_code"=>$loan_product_item["interestType"]["code"],
		"interest_type_value"=>$loan_product_item["interestType"]["value"],

		// ... Interest Calculation Period Type
		"interest_calculation_period_type_id"=>$loan_product_item["interestCalculationPeriodType"]["id"],
		"interest_calculation_period_type_code"=>$loan_product_item["interestCalculationPeriodType"]["code"],
		"interest_calculation_period_type_value"=>$loan_product_item["interestCalculationPeriodType"]["value"],

		// ... Transaction Processing Strategy 
		"transaction_processing_strategy_id"=>$loan_product_item["transactionProcessingStrategyId"],
		"transaction_processing_strategy_name"=>$loan_product_item["transactionProcessingStrategyName"],
	);

	# ... 04: Track Connection to Core-Banking
	if (sizeof($loan_product_item)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 06: Attach core respond
	$response_msg["CORE_RESP"] = $loan_product;

	# ... 07: Return response message
	return $response_msg;
}


# ... ... ... F21: FetchSavingsAcctById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsAcctById($SVNGS_ACCT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$SVNGS_ACCT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F22: CreateSavingsApplication ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function CreateLoanApplication($RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;
	//echo "<pre> LOAN CREATION: ".print_r($response_msg,true)."</pre>";

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F23: CreateSavingsApplication ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ApproveLoanApplication($LOAN_ACCT_CORE_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LOAN_ACCT_CORE_ID."?command=approve&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;
	//echo "<pre> LOAN APPROVAL: ".print_r($response_msg,true)."</pre>";

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F24: FetchSavingsAcctById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanAcctById($LOAN_ACCT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LOAN_ACCT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F25: UploadLoanDocumentToCore ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function UploadLoanDocumentToCore($LOAN_ACCT_ID, $doc_data, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $doc_data;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LOAN_ACCT_ID."/documents?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
  //$endpoint = "https://localhost:8443/fineract-provider/api/v1/loans/".$LOAN_ACCT_ID."/documents?tenantIdentifier=default&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos_multipart($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F25: UploadLoanGuarantorsToCore ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function UploadLoanGuarantorsToCore($LOAN_ACCT_ID, $GRRT_DATA, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $GRRT_DATA;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

  $endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LOAN_ACCT_ID."/guarantors?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F26: DisburseLoanToSvngsAcct ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function DisburseLoanToSvngsAcct($LOAN_ACCT_ID, $DISB_DATA, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $DISB_DATA;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

 	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LOAN_ACCT_ID."?command=disburseToSavings&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F26.1: DisburseLoanToSvngsAcct ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function DisburseLoan($LOAN_ACCT_ID, $DISB_DATA, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $DISB_DATA;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

  	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LOAN_ACCT_ID."?command=disburse&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;
	//echo "<pre> DISBURSE_LOAN: ".print_r($response_msg,true)."</pre>";


	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F26.2: ExecuteLoanApplicationCharge ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ExecuteLoanApplicationCharge($LA_LOAN_ID, $LA_CHRG_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

  $endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LA_LOAN_ID."/charges/".$LA_CHRG_ID."?command=pay&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
	//echo "<pre>PAY_LOAN_CHARGE: ".print_r($response_details,true)."</pre>";

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F26.3: ExecuteLoanApplicationCharge ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function CreateLoanRpymtStandingOrder($RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

  	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/standinginstructions?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);


	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F26.4: GetLoanApplnChargeDetails ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetLoanApplnChargeDetails($charge_id, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetLoanApplnChargeDetails?R_charge_id=".$charge_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F26.5: ExecuteLoanApplicationCharge ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function AddLoanApplicationCharge($LA_LOAN_ID, $LN_CHRG_RQST_MST, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $LN_CHRG_RQST_MST;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

  $endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LA_LOAN_ID."/charges?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
	

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F26.6: ExecuteLoanApplicationCharge ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetLoanApplicationChargesFromApi($LA_LOAN_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

  $endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LA_LOAN_ID."/charges?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
	//echo "<pre> LIST_OF_APPENDED_CHARGES: ".print_r($response_details,true)."</pre>";

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F27: PerformFundsTransfer ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function PerformFundsTransfer($TRANSFER_TXN_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $TRANSFER_TXN_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

    $endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounttransfers?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F28: FetchSharesAccountDetailsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSharesAccountDetailsById($SHARES_ACCT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounts/share/".$SHARES_ACCT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F28: ApplyAdditionalShares ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ApplyAdditionalShares($SHARES_ACCT_ID, $SHARE_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $SHARE_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounts/share/".$SHARES_ACCT_ID."?command=applyadditionalshares&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F29: ApplyAdditionalShares ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ApproveAdditionalShares($SHARES_ACCT_ID, $SHARE_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $SHARE_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounts/share/".$SHARES_ACCT_ID."?command=approveadditionalshares&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F30: FetchOAFTAccountsByCustomRpt ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAllSavingsAccountsByCustomRpt($TRANSIT_ACCT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetAllSavingsAccts?R_acct_id=".$TRANSIT_ACCT_ID."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F31: ValidateBulkFileTxnEntry ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ValidateBulkFileTxnEntry($SAVINGS_CUST_ID, $SAVINGS_ACCT_ID, $SAVINGS_ACCT_NUM, $SAVINGS_ACCT_NAME, $TRAN_TYPE, $TRAN_AMT, $MIFOS_CONN_DETAILS){
  $val_resp = array();
  $val_msg = "";

  # ... 01: CUSTOMER
  $CORE_CUST_NAME = "";
  $response_msg = FetchCustomerDetailsFromCore($SAVINGS_CUST_ID, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $CORE_CUST_NAME = trim($CORE_RESP["displayName"]);
  if ($SAVINGS_ACCT_NAME==$CORE_CUST_NAME) {
    $val_resp["CUST_FLG"] = "OKAY";
  } else {
    $val_resp["CUST_FLG"] = "ERROR";
    $val_msg .= " {Invalid Customer Name}";
  }


  # ... 02: SAVINGS ACCOUNT
  $CORE_CUST_ACCT_NUM = "";
  $CORE_CUST_ACCT_BAL = "";
  $response_msg = FetchSavingsAccountDetailsById($SAVINGS_ACCT_ID, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $CORE_CUST_ACCT_NUM = trim($CORE_RESP["accountNo"]);
  $CORE_CUST_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];
  if ($SAVINGS_ACCT_NUM==$CORE_CUST_ACCT_NUM) {
    $val_resp["ACCT_FLG"] = "OKAY";
  } else {
    $val_resp["ACCT_FLG"] = "ERROR";
    $val_msg .= " {Invalid Account Number}";
  }


  # ... 03: AVAILABLE FOR (DEBITS)
  if ($TRAN_TYPE=="D") {
    $BAL_DIFF = ($CORE_CUST_ACCT_BAL - $TRAN_AMT);
    if ($BAL_DIFF==0) {
      $val_resp["BAL_FLG"] = "OKAY";
    } else if ($BAL_DIFF>0) {
      $val_resp["BAL_FLG"] = "OKAY";
    } else if ($BAL_DIFF<0) {
      $val_resp["BAL_FLG"] = "ERROR";
      $val_msg .= " {Insufficient Account Balance}";
    }  
  } else if ($TRAN_TYPE=="C") {
    $val_resp["BAL_FLG"] = "OKAY";
  }


  $val_resp["VAL_MSG"] = $val_msg;
  return $val_resp;
}


# ... ... ... F32: FetchAllClients ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAllClients($MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetAllClients?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F33: FetchClientImage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
/*function FetchClientImage($CLIENT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$CLIENT_ID."/images?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_details = send_to_mifos_images($endpoint_url, $method, $username, $password, $data_string);

	# ... 06: Return response message
	return $response_details;
}*/

function FetchClientImage($CLIENT_ID, $MIFOS_CONN_DETAILS)
{
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP . "/" . $MIFOS_PROVIDER . "/api/v1/clients/" . $CLIENT_ID . "/images?tenantIdentifier=" . $MIFOS_TENANT_ID . "&pretty=true";

	$method = "GET";
	$username = $MIFOS_USERNAME;
	$password = $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_details = send_to_mifos_images_get($endpoint_url, $method, $username, $password, $data_string);

	# ... 06: Return response message
	return $response_details;
}

# ... ... ... F34: GetCustSavingsAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCustSavingsAccounts($cust_id, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetCustSavingsAccounts?R_client_id=".$cust_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F35: GetCustLoansAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCustLoansAccounts($cust_id, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetCustLoansAccounts?R_client_id=".$cust_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F36: GetCustSharesAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCustSharesAccounts($cust_id, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetCustSharesAccounts?R_client_id=".$cust_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F37: GetCustSavingsAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCustSavingsAccountsAll($cust_id, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetCustSavingsAccounts?R_client_id=".$cust_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F38: GetCustLoansAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCustLoansAccountsAll($cust_id, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetCustLoansAccounts?R_client_id=".$cust_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F39: GetCustSharesAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCustSharesAccountsAll($cust_id, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetCustSharesAccounts?R_client_id=".$cust_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F40: UpdateCustomerEmail ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function UpdateCustomerEmail($CUST_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

  $endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/datatables/OtherDetails/".$CUST_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "PUT";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F40: UpdateCustomerPhone ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function UpdateCustomerPhone($CUST_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$CUST_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "PUT";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F41: UpdateCustomerImage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function UpdateCustomerImage($cust_id, $img_data, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $img_data;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$cust_id."/images?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "PUT";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	//$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
	$response_details = send_to_mifos_images($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F42: CreateSharesApplication ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function CreateSharesApplication($RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounts/share?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F43: ApproveSharesApplication ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ApproveSharesApplication($SHRS_ACCT_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounts/share/".$SHRS_ACCT_ID."?command=approve&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F44: ActivateSharesApplication ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ActivateSharesApplication($SHRS_ACCT_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounts/share/".$SHRS_ACCT_ID."?command=activate&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F45: MakeDirectDepositTransaction ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function MakeDirectDepositTransaction($SVG_ACCT_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$SVG_ACCT_ID."/transactions?command=deposit&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F45.002: MakeDirectWithdrawalTransaction ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function MakeDirectWithdrawalTransaction($SVG_ACCT_ID, $RQST_MSG, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = $RQST_MSG;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$SVG_ACCT_ID."/transactions?command=withdrawal&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F46: FetchClientOtherDetails ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchClientOtherDetails_Walkin($CLIENT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/datatables/OtherDetails/".$CLIENT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}


# ... ... ... F47: FetchClientDocuments ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchClientDocuments_Walkin($CLIENT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	// ... https://127.0.0.1:8443/fineract-provider/api/v1/clients/9/documents?tenantIdentifier=default&pretty=true
	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$CLIENT_ID."/documents?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F48: FetchDownloadClientDocument ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchDownloadClientDocument_Walkin($CLIENT_ID, $DOCUMENT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	// ... https://127.0.0.1:8443/fineract-provider/api/v1/clients/1/documents/1/attachment?tenantIdentifier=default&pretty=true
	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$CLIENT_ID."/documents/".$DOCUMENT_ID."/attachment?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";


	return $endpoint_url;
}

# ... ... ... F49: FetchShareAcctById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchShareAcctById($SHARE_ACCT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	// ... https://127.0.0.1:8443/fineract-provider/api/v1/accounts/share/1?tenantIdentifier=default&pretty=true
	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/accounts/share/".$SHARE_ACCT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F50: SVGS_GetChrgDetailById
function SVGS_GetChrgDetailById($chrg_id, $MIFOS_CONN_DETAILS)
{
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/SVGS_GetChrgDetailById?R_chrg_id=".$chrg_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	//echo $endpoint_url;

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F51: SVGS_CreateSavingsAcctCharge ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function SVGS_CreateSavingsAcctCharge($CustSvgAcctId, $ChargeRqstMsg, $MIFOS_CONN_DETAILS)
{	
	# ... 01: Prepare Request Message
	$data_string = $ChargeRqstMsg;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$in_SavingsAcctId = $CustSvgAcctId;
	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$in_SavingsAcctId."/charges?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core response
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F52: SVGS_GetSavingsAcctChargeList ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function SVGS_GetSavingsAcctChargeList($CustSvgAcctId, $MIFOS_CONN_DETAILS)
{	
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$in_SavingsAcctId = $CustSvgAcctId;
	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$in_SavingsAcctId."/charges?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core response
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F53: SVGS_PaySavingsAcctCharge ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function SVGS_PaySavingsAcctCharge($CustSvgAcctId, $chargeId, $PayChargeRqstMsg, $MIFOS_CONN_DETAILS)
{	
	# ... 01: Prepare Request Message
	$data_string = $PayChargeRqstMsg;
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$in_SavingsAcctId = $CustSvgAcctId;
	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$in_SavingsAcctId."/charges/".$chargeId."?command=paycharge&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

	$method = "POST";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core response
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F54: SVGS_GetSavingsChargeId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function SVGS_GetSavingsChargeId($savings_account_id, $charge_id, $MIFOS_CONN_DETAILS)
{
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/FetchSavingsChargeId?R_savings_account_id=".$savings_account_id."&R_charge_id=".$charge_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F55: SVG_GetSavingsChargeTranId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function SVG_GetSavingsChargeTranId($svg_acct_chrg_id, $MIFOS_CONN_DETAILS)
{
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/FetchSavingsChargeTranId?R_svg_acct_chrg_id=".$svg_acct_chrg_id."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
	$method = "GET";
	$username= $MIFOS_USERNAME;
	$password= $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details)>0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

# ... ... ... F56: GetCustSavingsAccountsGroup ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCustSavingsAccountsGroup($cust_id, $MIFOS_CONN_DETAILS)
{
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP . "/" . $MIFOS_PROVIDER . "/api/v1/runreports/GetCustSavingsAccountsGroup?R_client_id=" . $cust_id . "&tenantIdentifier=" . $MIFOS_TENANT_ID . "&pretty=true";
	$method = "GET";
	$username = $MIFOS_USERNAME;
	$password = $MIFOS_PASSWORD;

	# ... 02: Send Request Message to Receive Core Response
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03: Track Connection to Core-Banking
	if (sizeof($response_details) > 0) {
		$response_msg["CONN_FLG"] = "CONNECTED";
	} else {
		$response_msg["CONN_FLG"] = "NOT_CONNECTED";
	}

	# ... 05: Attach core respond
	$response_msg["CORE_RESP"] = $response_details;

	# ... 06: Return response message
	return $response_msg;
}

?>