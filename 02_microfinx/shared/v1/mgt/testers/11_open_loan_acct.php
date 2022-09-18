<?php
include "mifos_sender.php";

// ... PREPARE REQUEST BODY
$MIFOS_IP = "https://127.0.0.1:8443";
$MIFOS_PROVIDER = "fineract-provider";
$MIFOS_TENANT_ID = "default";
$MIFOS_USERNAME = "mifos";
$MIFOS_PASSWORD = "password";

// ... BUILDING THE MIFOS LOAN MESSAGE
/*

Array
(
    [pdt_id] => 2
    [pdt_name] => Car Loan
    [pdt_short_name] => 102
    [pdt_descrition] => Car Loan
    [pdt_status] => loanProduct.active
    [min_principal] => 4000000
    [default_principal] => 7000000
    [max_principal] => 15000000
    [min_number_of_repayments] => 5
    [default_number_of_repayments] => 7
    [max_number_of_repayments] => 12
    [repayment_every] => 1
    [repayment_frequency_type_id] => 2
    [repayment_frequency_type_code] => repaymentFrequency.periodFrequencyType.months
    [repayment_frequency_type_value] => Months
    [min_interest_rate_per_period] => 14
    [default_interest_rate_per_period] => 14
    [max_interest_rate_per_period] => 14
    [interest_rate_frequency_type_id] => 2
    [interest_rate_frequency_type_code] => interestRateFrequency.periodFrequencyType.months
    [interest_rate_frequency_type_value] => Per month
    [annual_interest_rate] => 168
    [amortization_type_id] => 1
    [amortization_type_code] => amortizationType.equal.installments
    [amortization_type_value] => Equal installments
    [interest_type_id] => 0
    [interest_type_code] => interestType.declining.balance
    [interest_type_value] => Declining Balance
    [interest_calculation_period_type_id] => 1
    [interest_calculation_period_type_code] => interestCalculationPeriodType.same.as.repayment.period
    [interest_calculation_period_type_value] => Same as repayment period
    [transaction_processing_strategy_id] => 1
    [transaction_processing_strategy_name] => Penalties, Fees, Interest, Principal order
)
*/
$CUST_CORE_ID = "12";
$LN_PDT_ID = "2";
$RQSTD_AMT = "40000";
$RQSTD_RPYMT_PRD = "11";
$repayment_every = "1";
$repayment_frequency_type_id = "2";
$interest_rate = "14";
$amortization_type_id = "1";
$interest_type_id = "0";
$interest_calculation_period_type_id = "1";
$transaction_processing_strategy_id = "1";
$Submission_Date =  date('d F Y', strtotime( date("ymd",time()) ));
$expected_disbursement_date =  date('d F Y', strtotime( date("ymd",time()) ));
//$expected_disbursement_date =  date('d F Y', strtotime( $Submission_Date.' + 1 days' ));
$CORE_SVGS_ACCT_ID = "8";

$data_string = '
{
  "dateFormat": "dd MMMM yyyy",		
  "locale": "en_GB",
  "clientId": '.$CUST_CORE_ID.',
  "productId": '.$LN_PDT_ID.',
  "principal": "'.$RQSTD_AMT.'",
  "loanTermFrequency": 12,
  "loanTermFrequencyType": 2,
  "loanType": "individual",
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

$endpoint_url = $MIFOS_IP."/".$MIFOS_PROVIDER."/api/v1/loans?tenantIdentifier=".$MIFOS_TENANT_ID."&pretty=true";
$method = "POST";
$username= $MIFOS_USERNAME;
$password= $MIFOS_PASSWORD;

// ... sending to mifos
$response_details = array();
$response_details = send_to_mifos($endpoint_url, $method, $username, $password, $data_string);
echo "<pre>".print_r($response_details,true)."</pre>";





?>