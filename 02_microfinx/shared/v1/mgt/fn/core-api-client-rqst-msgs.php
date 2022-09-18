<?php
# ... Display application errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

# ... Debugging Api
//echo "<pre>".print_r($user_role_details,true)."</pre>";

# **..** **..** **..** **..** **..** **..** **..** SECTION 01: Core Entity Values and Type Ids **..** **..** **..** **..** **..** **..**
# **..** **..** **..** **..** **..** **..** **..** SECTION 01: Core Entity Values and Type Ids **..** **..** **..** **..** **..** **..**
# **..** **..** **..** **..** **..** **..** **..** SECTION 01: Core Entity Values and Type Ids **..** **..** **..** **..** **..** **..**

# ... Office ID ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$CORE_OFFICE_DEF = array(
	"HQ" => "1"
);

# ... Client Gender ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$CORE_GENDER_DEF = array(
	"M" => "22",
	"F" => "24",
	"O" => "29"
);

# ... Client Type Defination ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$CORE_CLIENT_TYPE_DEF = array(
	"KSK" => "35"
);

# ... Client Clasification ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$CORE_CLIENT_CLSSFCN_DEF = array(
	"RETAIL" => "33"
);

# ... Acceptable Image Paths ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$CORE_ACCEPTABLE_IMG_TYPES = array(
	"jpg" => "jpeg",
	"jpeg" => "jpeg",
	"png" => "png",
	"gif" => "gif"
);

# ... Application Charging ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$APPLN_CHRGING = array(
	"LOAN_APPLN_CHRG_FLG" => "NO",   		
	"LOAN_APPLN_CHRG_EVENT_ID" => "",   		
	"SVGS_APPLN_WITHDRAW_CHRG_FLG" => "NO",   		
	"SVGS_APPLN_WITHDRAW_CHRG_EVENT_ID" => "",   		
	"SVGS_APPLN_TRANSFER_CHRG_FLG" => "NO",   		
	"SVGS_APPLN_TRANSFER_CHRG_EVENT_ID" => "",  
	"SVGS_APPLN_DEPOSIT_CHRG_FLG" => "NO",   		
	"SVGS_APPLN_DEPOSIT_CHRG_EVENT_ID" => "", 	
	"SHARES_APPLN_BUY_CHRG_FLG" => "NO",   		
	"SHARES_APPLN_BUY_CHRG_EVENT_ID" => "" 
);

# ... Commission Accts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$CORE_COMM_ACCTS = array(
	// ... CUSTOMER WITHDRAW SUSPENSE
	"SVGS_APPLN_COMM_CUST_ID" => "1575",   			// ... OFA000001575
	"SVGS_APPLN_COMM_ACCT_ID" => "1533",   			// ... BAM000001533

	// ... LOAN APPLICATION SUSPENSE 
	"LOAN_APPLN_COMM_CUST_ID" => "",   				// ... 
	"LOAN_APPLN_COMM_ACCT_ID" => "",   				// ... 

	// ... CUSTOMER DEPOSIT SUSPENSE
	"SVGS_DEP_APPLN_COMM_CUST_ID" => "1575",   		// ... OFA000001575
	"SVGS_DEP_APPLN_SUSP_ACCT_ID" => "1533",   		// ... BAM000001533

	// ... SHARES PURCHASE SUSPENSE ACCOUNT
	"SHARES_BUY_APPLN_COMM_CUST_ID" => "1579",   	// ... OFA000001579
	"SHARES_BUY_APPLN_SUSP_ACCT_ID" => "1536"   	// ... 811000001536
);

# ... Direct Withdraws and Deposits ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$DIRECT_WITHDRAW_DEPOSIT = array(
	// ... CUSTOMER WITHDRAW SUSPENSE
	"WITHDRAW_CUST_ID" => "1578",   			// ... OFA000001578
	"WITHDRAW_ACCT_ID" => "1535",   			// ... 811000001535
	"WITHDRAW_ACCT_NUM" => "811000001535",   	// ... 811000001535

	// ... CUSTOMER DEPOSIT SUSPENSE
	"DEPOSIT_CUST_ID" => "1577",   				// ... OFA000001577
	"DEPOSIT_ACCT_ID" => "1534",   				// ... 811000001534
	"DEPOSIT_ACCT_NUM" => "811000001534"   		// ... 811000001534
);

# ... Loan Application Processing Fees ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$LOAN_PROC_CHRGS = array(
	"PROC_FEE_BELOW_5M" => "6",   		// ... Loan processing fee(Amount Less or Equal to 5M)
	"PROC_FEE_ABOVE_5M" => "7",   		// ... Loan processing fee( Amount Above 5M) 
	"SAL_LOAN_INS_FEES" => "4",   		// ... Salary Loan Insurance fees 
	"CAVEAT_FEES" => "8",  				// ... Caveat Fees 
	"INS_OTHER_LOAN" => "5"   			// ... Insurance fees on other Loans   
);

# ... Excise Duty ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$EXCISE_DUTY = array(
	"EXCISE_DUTY_PERCENT" => "",   				// ... 15%
	"EXCISE_DUTY_CUST_ID" => "",   				// ... 
	"EXCISE_DUTY_ACCT_ID" => "",   				// ... 
);

# ... Bulk Transactions ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
$BULK_TXN_CONFIG = array(

	// ... BULK TRANSFER STAGING TRANSIT SUSPENSE
	"BLK_TRANSIT_ACCT_CUST_ID" => "1580",   	// ... OFA000001580
	"BLK_TRANSIT_ACCT_NUM_ID" => "1537"   		// ... 811000001537
);


# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Core request messages **..** **..** **..** **..** **..** **..** **..** **..**
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Core request messages **..** **..** **..** **..** **..** **..** **..** **..**
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Core request messages **..** **..** **..** **..** **..** **..** **..** **..**

# ... ... ... F1: Get System Parameter ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildCreateClientRequestMessage($OFFICE_ID, $FIRST_NAME, $MIDDLE_NAME, $LAST_NAME, $WORK_ID, $MOBILE_NO, $GENDER_ID, $CLIENT_TYPE_ID, $CLIENT_CLSFCN_ID, $ACTVN_DATE, $SUBMN_DATE){
	$data_string = '
	{
		"officeId": "'.$OFFICE_ID.'",
		"firstname": "'.$FIRST_NAME.'",
		"middlename": "'.$MIDDLE_NAME.'",
		"lastname": "'.$LAST_NAME.'",
		"externalId": "'.$WORK_ID.'",
		"mobileNo": "'.$MOBILE_NO.'", 
	  	"genderId": "'.$GENDER_ID.'", 
	  	"clientTypeId": "'.$CLIENT_TYPE_ID.'", 
	  	"clientClassificationId": "'.$CLIENT_CLSFCN_ID.'",   
		"dateFormat": "dd MMMM yyyy",
		"locale": "en",
		"active": true,
		"activationDate": "'.$ACTVN_DATE.'",
	  "submittedOnDate": "'.$SUBMN_DATE.'"
	}
	';  
	return $data_string;
}

# ... ... ... F2: Build DataTable RqstMsg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildDataTableRqstMsg($DATA_ARRAY){

	$data_string = "";
	$key_value_pair_list = "";

	foreach ($DATA_ARRAY as $key => $value) {
		
		$key_value_pair = '"'.$key.'": "'.$value.'"';

		if ($key_value_pair_list=="") {
			$key_value_pair_list = $key_value_pair;
		}else{
			$key_value_pair_list = $key_value_pair_list.",".$key_value_pair;
		}

	}

	$data_string = "{".$key_value_pair_list."}";
	return $data_string;
}

# ... ... ... F3: Build DataTable RqstMsg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildImageUploadRqstMsg($IMG_PATH){
	$CORE_ACCEPTABLE_IMG_TYPES = array(
		"jpg" => "jpeg",
		"jpeg" => "jpeg",
		"png" => "png",
		"gif" => "gif"
	);
	//$IMG_PATH = "D:/__#/wamp/www/wvi-cst/files/activation_requests/DF00000027/NATIONALID_DF00000027.png";
	$type = $CORE_ACCEPTABLE_IMG_TYPES[pathinfo($IMG_PATH, PATHINFO_EXTENSION)];
	$data = file_get_contents($IMG_PATH);
	$base64 = "data:image/" . $type . ";base64," . base64_encode($data);


	$data_string = $base64;
	return $data_string;
}

# ... ... ... F4: Build Document Rqst Msg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildDocumentRqstMsg($file_path, $file_type, $file_name, $description){

	$file_data_string = array(
		'file' => new CurLFile($file_path, $file_type, $file_name),
		'name' => $file_name,
		'description'=>$description
	); 

	return $file_data_string;
}

# ... ... ... F5: Build Document Rqst Msg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildNewSavingsApplnRqstMsg($client_id, $svngs_pdt_id, $submission_date){
	$data_string = '
	{
		"clientId": "'.$client_id.'",
		"productId": "'.$svngs_pdt_id.'",
		"locale": "en",
		"dateFormat": "dd MMMM yyyy",
	  "submittedOnDate": "'.$submission_date.'"
	}
	';  
	return $data_string;
}

# ... ... ... F6: BuildApproveSavingsApplnRqstMsg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildApproveSavingsApplnRqstMsg($apprvl_date){
	$data_string = '
	{
		"locale": "en",
		"dateFormat": "dd MMMM yyyy",
	  "approvedOnDate": "'.$apprvl_date.'"
	}
	';  
	return $data_string;
}

# ... ... ... F7: BuildActivateSavingsApplnRqstMsg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildActivateSavingsApplnRqstMsg($actvn_date){
	$data_string = '
	{
		"locale": "en",
		"dateFormat": "dd MMMM yyyy",
	  "activatedOnDate": "'.$actvn_date.'"
	}
	';  
	return $data_string;
}

# ... ... ... F8: Build Open Loan Account Request Message ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildCreateLoanAcctRequestMessage($identifier_LOAN_TYPE_CAT, $identifier_CORE_KEY, $identifier_CORE_ID, $LN_PDT_ID, $RQSTD_AMT, $RQSTD_RPYMT_PRD, $repayment_every, $repayment_frequency_type_id, $interest_rate, $amortization_type_id, $interest_type_id, $interest_calculation_period_type_id, $transaction_processing_strategy_id, $expected_disbursement_date, $Submission_Date, $CORE_SVGS_ACCT_ID){
	$data_string = '
	{
	  "dateFormat": "dd MMMM yyyy",		
	  "locale": "en_GB",
	  "'.$identifier_CORE_KEY.'": '.$identifier_CORE_ID.',
	  "productId": '.$LN_PDT_ID.',
	  "principal": "'.$RQSTD_AMT.'",
	  "loanTermFrequency": "'.$RQSTD_RPYMT_PRD.'",		// ... Amended from 12 to aVARIABLE
	  "loanTermFrequencyType": 2,
	  "loanType": "'.$identifier_LOAN_TYPE_CAT.'",
	  "numberOfRepayments": '.$RQSTD_RPYMT_PRD.',
	  "repaymentEvery": '.$repayment_every.',
	  "repaymentFrequencyType": '.$repayment_frequency_type_id.',
	  "interestRatePerPeriod": '.$interest_rate.',
	  "amortizationType": '.$amortization_type_id.',
	  "interestType": '.$interest_type_id.',
	  "interestCalculationPeriodType": '.$interest_calculation_period_type_id.',
	  "transactionProcessingStrategyId": '.$transaction_processing_strategy_id.',
	  "expectedDisbursementDate": "'.$expected_disbursement_date.'",
	  "submittedOnDate": "'.$Submission_Date.'",
	  "linkAccountId" : "'.$CORE_SVGS_ACCT_ID.'"
	}
	';  
	return $data_string;
}

# ... ... ... F9: BuildApproveLoanAcctRequestMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildApproveLoanAcctRequestMessage($approvedOnDate, $expected_disbursement_date, $NOTE){
	$data_string = '
	{
	  "locale": "en",
	  "dateFormat": "dd MMMM yyyy",
	  "approvedOnDate": "'.$approvedOnDate.'",
	  "expectedDisbursementDate" : "'.$expected_disbursement_date.'",
	  "note": "'.$NOTE.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F10: BuildDisburseToSavingsAcctRequestMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildDisburseToSavingsAcctRequestMessage($transactionAmount, $actualDisbursementDate, $NOTE){
	$data_string = '
	{
	  "locale": "en",
	  "dateFormat": "dd MMMM yyyy",
	  "transactionAmount": "'.$transactionAmount.'",
	  "actualDisbursementDate" : "'.$actualDisbursementDate.'",
	  "note": "'.$NOTE.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F10.1: BuildDisburseLoanRequestMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildDisburseLoanRequestMessage($transactionAmount, $actualDisbursementDate, $NOTE, $LN_APPLN_NO, $LA_BANK_ACCT_NO, $LA_BANK_NAME){
	$data_string = '
	{
	  "dateFormat": "dd MMMM yyyy",
	  "locale": "en",
	  "transactionAmount":"'.$transactionAmount.'",
	  "actualDisbursementDate": "'.$actualDisbursementDate.'",
	  "note": "'.$NOTE.'",
	  "accountNumber": "'.$LA_BANK_ACCT_NO.'",
	  "checkNumber": "'.$LN_APPLN_NO.'",
	  "routingCode": "0",
	  "receiptNumber": "'.$LN_APPLN_NO.'",
	  "bankNumber": "'.$LA_BANK_NAME.'"
	}
	'; 
	return $data_string;

	//	  "paymentTypeId": "12",
}

# ... ... ... F10.2: BuildLoanApplnChrgMsg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildLoanApplnChrgMsg($transactionDate){
	$data_string = '
	{
	  "dateFormat": "dd MMMM yyyy",
	  "locale": "en",
	  "transactionDate": "'.$transactionDate.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F11: BuildCreateGrrtrRequestMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildCreateGrrtrRequestMessage($firstname, $lastname){
	$data_string = '
	{
	  guarantorTypeId:3,
	  firstname: "'.$firstname.'",
	  lastname: "'.$lastname.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F12: BuildCreateGrrtrRequestMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildTransferTxnMessage($from_client_id, $from_account_Id, $to_client_id, $to_account_Id, $transaction_date, $transfer_amount, $narration)
{

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
	return $data_string;
}

# ... ... ... F13: BuildApplySharesMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildApplySharesMessage($requestedDate, $requestedShares){
	$data_string = '
	{
		"requestedDate": "'.$requestedDate.'",
		"requestedShares": "'.$requestedShares.'",
		"locale": "en",
		"dateFormat": "dd MMMM yyyy"
	}
	'; 
	return $data_string;
}

# ... ... ... F14: BuildApplySharesMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildApproveSharesMessage($purchase_tran_id){
	$data_string = '
	{
		"requestedShares": [{
			"id": "'.$purchase_tran_id.'"
		}]
	}
	'; 
	return $data_string;
}

# ... ... ... F15: BuildUpdateEmailMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildUpdateEmailMessage($Email){
	$data_string = '
	{
    "Email": "'.$Email.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F16: BuildUpdateEmailMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildUpdatePhoneMessage($mobileNo){
	$data_string = '
	{
    "mobileNo": "'.$mobileNo.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F16.001: BuildUpdateGenderMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildUpdateGenderMessage($GENDER_ID){
	$data_string = '
	{
    "genderId": "'.$GENDER_ID.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F17: BuildCreateSharesApplicationMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildCreateSharesApplicationMessage($clientId, $productId, $requestedShares, $submittedDate, $applicationDate, $savingsAccountId){
	$data_string = '
	{
		"clientId":  "'.$clientId.'",
		"productId": '.$productId.',
		"requestedShares": 2,
		"submittedDate":  "'.$submittedDate.'",
		"applicationDate":  "'.$applicationDate.'",
		"locale": "en",
		"dateFormat": "dd MMMM yyyy",
		"savingsAccountId": '.$savingsAccountId.'
	}
	'; 
	return $data_string;
}

# ... ... ... F18: BuildApproveSharesApplicationMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildApproveSharesApplicationMessage($approvedDate){
	$data_string = '
	{
		"note":"approved",
		"approvedDate":"'.$approvedDate.'",
		"locale":"en",
		"dateFormat":"dd MMMM yyyy"
	}
	'; 
	return $data_string;
}

# ... ... ... F19: BuildActivateSharesApplicationMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildActivateSharesApplicationMessage($activatedDate){
	$data_string = '
	{
		"activatedDate":"'.$activatedDate.'",
		"locale":"en",
		"dateFormat":"dd MMMM yyyy"
	}
	'; 
	return $data_string;
}

# ... ... ... F20: BuildActivateSharesApplicationMessage ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildLoanRpymtStandingInstructionMsg($fromClientId, $si_name, $fromAccountId, $toClientId, $loan_toAccountId, $date_validFrom){
    /*
	fromAccountType: 	[1 - Loan Account, 2 - Savings Account]
	transferType:		[1 - Account Transfer(savings to savings), 2 - Loan Repayment]
	priority:			[1 - URGENT, 2 - HIGH, 3 - MEDIUM, 4 - LOW ]
	status:				[1 - Active, 2 - Disabled, 3 - Deleted]
	toAccountType: 		[1 - Loan Account, 2 - Savings Account]
	instructionType:	[1 - FIXED, 2 - DUES]
	recurrenceType:		[1 - Periodic, 2 - As per dues]
    */

	$data_string = '
	{
		"fromOfficeId": 1,
		"fromClientId": "'.$fromClientId.'",
		"fromAccountType":2,
		"name": "'.$si_name.'",
		"transferType":2,
		"priority":3,
		"status":1,
		"fromAccountId": "'.$fromAccountId.'",
		"toOfficeId":1,
		"toClientId": "'.$toClientId.'",
		"toAccountType":1,
		"toAccountId": "'.$loan_toAccountId.'",
		"instructionType":2,
		"validFrom": "'.$date_validFrom.'",
		"recurrenceType":2,
		"locale":"en",
		"dateFormat":"dd MMMM yyyy"
	}
	'; 
	return $data_string;
}

# ... ... ... F21: BuildLoanApplnChrgRqstMsg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildLoanApplnChrgRqstMsg($chargeId,$amount,$dueDate){
	$data_string = '
	{
		"chargeId": "'.$chargeId.'",
		"locale": "en",
		"amount": "'.$amount.'",
		"dateFormat": "dd MMMM yyyy",
		"dueDate": "'.$dueDate.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F22: BuildRawDepositRqstMsg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildRawDepositRqstMsg($transactionDate,$transactionAmount,$paymentTypeId,$accountNumber,$checkNumber,$routingCode,$receiptNumber,$bankNumber){
	$data_string = '
	{
		"locale": "en",
		"dateFormat": "dd MMMM yyyy",
		"transactionDate": "'.$transactionDate.'",
    "transactionAmount": "'.$transactionAmount.'",
    "paymentTypeId": "'.$paymentTypeId.'",
		"accountNumber": "'.$accountNumber.'",
		"checkNumber": "'.$checkNumber.'",
		"routingCode": "'.$routingCode.'",
		"receiptNumber": "'.$receiptNumber.'",
		"bankNumber": "'.$bankNumber.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F23: BuildRawWithdrawRqstMsg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildRawWithdrawRqstMsg($transactionDate,$transactionAmount,$paymentTypeId,$accountNumber,$checkNumber,$routingCode,$receiptNumber,$bankNumber){
	$data_string = '
	{
		"locale": "en",
		"dateFormat": "dd MMMM yyyy",
    "transactionDate": "'.$transactionDate.'",
    "transactionAmount": "'.$transactionAmount.'",
    "paymentTypeId": "'.$paymentTypeId.'",
		"accountNumber": "'.$accountNumber.'",
		"checkNumber": "'.$checkNumber.'",
		"routingCode": "'.$routingCode.'",
		"receiptNumber": "'.$receiptNumber.'",
		"bankNumber": "'.$bankNumber.'"
	}
	'; 
	return $data_string;
}

# ... ... ... F24: BuildCreateChargeRqstMsg  ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildCreateChargeRqstMsg($chargeId, $amount, $dueDate)
{
	$data_string = '
	{
		"chargeId": "'.$chargeId.'",
		"locale": "en",
		"amount": "'.$amount.'",
		"dateFormat": "dd MMMM yyyy",
		"dueDate": "'.$dueDate.'"
	}
	';
	return $data_string;
}

# ... ... ... F25: BuildCreatePayChargeRqstMsg  ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildCreatePayChargeRqstMsg($amount, $dueDate)
{
	$data_string = '
	{
		"dateFormat": "dd MMMM yyyy",
		"locale": "en",
		"amount": "'.$amount.'",
		"dueDate": "'.$dueDate.'"
	}
	';
	return $data_string;
}

?>
