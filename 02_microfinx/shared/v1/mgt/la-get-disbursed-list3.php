<?php
# ... Important Data
include("conf/session-checker.php");

$START_DATE = mysql_real_escape_string(trim($_POST['START_DATE']));
$END_DATE = mysql_real_escape_string(trim($_POST['END_DATE']));

$file="DISP_LIST_".strtotime(GetCurrentDatetime()).".xls";

$EXCEL_DATE = "<table border='1' align='left'>"
		          ."<tr valign='top'>"
		            ."<th>SN</th>"
		            ."<th>Appln Ref</th>"
		            ."<th>Client Name</th>"
		            ."<th>Amount</th>"
		            ."<th>Product</th>"
		            ."<th>Disbursement Date</th>"
		            ."<th>Ext Orgn</th>"
		            ."<th>Ext Orgn Acct No</th>"
		          ."</tr>";

$la_list = array();
$la_list = FetchDisbursedLoanApplns($START_DATE, $END_DATE);
for ($i=0; $i < sizeof($la_list); $i++) {
  $la = array();
  $la = $la_list[$i];
  $RECORD_ID = $la['RECORD_ID'];
  $LN_APPLN_NO = $la['LN_APPLN_NO'];
  $CUST_ID = $la['CUST_ID'];
  $LN_PDT_ID = $la['LN_PDT_ID'];
  $RQSTD_AMT = $la['RQSTD_AMT'];
  $CUST_FIN_INST_ID = $la['CUST_FIN_INST_ID'];
  $DISB_DATE = $la['DISB_DATE'];
  
  # ... 01: Get Client Name
  $cstmr = array();
  $cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
  $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];

  $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $CORE_CUST_NAME = $CORE_RESP["displayName"];

  # ... 02: Get Loan Product Name
  $loan_product = array();
  $response_msg = FetchLoanProductDetailsById($LN_PDT_ID, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $loan_product = $response_msg["CORE_RESP"];
  //echo "<pre>".print_r($loan_product,true)."</pre>";
  $LN_PDT_NAME = $loan_product["pdt_name"];
  $LN_PDT_SHORT_NAME = $loan_product["pdt_short_name"];
  $repayment_frequency_type_value = $loan_product["repayment_frequency_type_value"];

  # ... 03: Get Client Bank
  $CUST_BANK_NAME = GetCustBankFromBankAcct($CUST_ID, $CUST_FIN_INST_ID);

  $EXCEL_DATE.= "<tr valign='top>'"
							    ."<td style='mso-number-format: \\@;'>".($i+1).". </td>"
							    ."<td style='mso-number-format: \\@;'>".$LN_APPLN_NO."</td>"
							    ."<td style='mso-number-format: \\@;'>".$CORE_CUST_NAME."</td>"
							    ."<td style='mso-number-format: \\@;'>".number_format($RQSTD_AMT)."</td>"
							    ."<td style='mso-number-format: \\@;'>".$LN_PDT_NAME." (".$LN_PDT_SHORT_NAME.")</td>"
							    ."<td style='mso-number-format: \\@;'>".$DISB_DATE."</td>"
							    ."<td style='mso-number-format: \\@;'>".$CUST_BANK_NAME."</td>"
							    ."<td style='mso-number-format: \\@;'>".$CUST_FIN_INST_ID."</td>"
							  ."</tr>";
}	

$EXCEL_DATE.="</table>";	          
                 
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
echo $EXCEL_DATE;
?>
