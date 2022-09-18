<?php
# ... ... ... IMPORT MESSAGE SENDER ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
include "core-api-msg-sender.php";

# ... ... ... F1: FetchCustomerDetailsFromCore ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F2: GetCustSavingsAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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



# ... ... ... F3: GetCustLoansAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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
	//echo "<pre>" . print_r($response_details, true) . "</pre>";
	
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

function GetCustLoansAccountsGroup($cust_id, $MIFOS_CONN_DETAILS)
{
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP . "/" . $MIFOS_PROVIDER . "/api/v1/runreports/GetCustLoansAccountsGroup?R_client_id=" . $cust_id . "&tenantIdentifier=" . $MIFOS_TENANT_ID . "&pretty=true";
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

# ... ... ... F4: GetCustSharesAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F5: FetchSavingsProducts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F6: FetchLoanProducts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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
	$loan_products_list = array();
	$response_msg = array();
	$response_details = array();
	$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);

	# ... 03:  Formatting Response
	for ($i=0; $i < sizeof($response_details) ; $i++) { 
  	$loan_product_item = $response_details[$i];

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

# ... ... ... F7: FetchShareProducts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F8: FetchClientOtherDetails ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchClientOtherDetails($CLIENT_ID, $MIFOS_CONN_DETAILS){
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

# ... ... ... F9: FetchClientImage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchClientImage($CLIENT_ID, $MIFOS_CONN_DETAILS){
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
}

# ... ... ... F10: FetchClientDocuments ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchClientDocuments($CLIENT_ID, $MIFOS_CONN_DETAILS){
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

# ... ... ... F11: FetchClientParticularDocuments ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchClientParticularDocuments($CLIENT_ID, $DOCUMENT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	// ... https://127.0.0.1:8443/fineract-provider/api/v1/clients/9/documents/9?tenantIdentifier=default&pretty=true
	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/clients/".$CLIENT_ID."/documents/".$DOCUMENT_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

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

# ... ... ... F12: FetchDownloadClientDocument ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchDownloadClientDocument($CLIENT_ID, $DOCUMENT_ID, $MIFOS_CONN_DETAILS){
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

# ... ... ... F13: FetchLoanProductDetailsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F14: FetchSavingsAcctById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F15: FetchShareAcctById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F16: FetchShareProductById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F18: FetchOAFTAccountsByCustomRpt ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F19: FetchSharesAccountDetailsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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


# ... ... ... F20: GetCustGetSavingsAcctTransactionsoansAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetSavingsAcctTransactionsWithDateRange($SVNGS_ACCT_ID, $START_DATE, $END_DATE, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	//$endpoint_url = "https://127.0.0.1:8443/fineract-provider/api/v1/runreports/GetSavingsAcctTransactionsWithDateRange?R_acct_id=16&R_sd=2019-07-09&R_ed=2019-07-09&tenantIdentifier=default&pretty=true";
	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetSavingsAcctTransactionsWithDateRange?R_acct_id=".$SVNGS_ACCT_ID."&R_sd=".$START_DATE."&R_ed=".$END_DATE."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
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

# ... ... ... F20: GetCustGetSavingsAcctTransactionsoansAccounts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetSavingsAcctTransactionDetails($SVNGS_ACCT_ID, $TXN_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/savingsaccounts/".$SVNGS_ACCT_ID."/transactions/".$TXN_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

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

# ... ... ... F21: FetchSavingsAcctById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F22: GetLoanAcctTransactions ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetLoanAcctTransactions($LOAN_ACCT_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/runreports/GetLoanAcctTransactions?R_acct_id=".$LOAN_ACCT_ID."&tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
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

# ... ... ... F23: GetLoanAcctTransactionDetails ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetLoanAcctTransactionDetails($LOAN_ACCT_ID, $TXN_ID, $MIFOS_CONN_DETAILS){
	# ... 01: Prepare Request Message
	$data_string = "";
	$MIFOS_IP = $MIFOS_CONN_DETAILS[0];
	$MIFOS_PROVIDER = $MIFOS_CONN_DETAILS[1];
	$MIFOS_TENANT_ID = $MIFOS_CONN_DETAILS[2];
	$MIFOS_USERNAME = $MIFOS_CONN_DETAILS[3];
	$MIFOS_PASSWORD = $MIFOS_CONN_DETAILS[4];

	$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans/".$LOAN_ACCT_ID."/transactions/".$TXN_ID."?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";

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

?>