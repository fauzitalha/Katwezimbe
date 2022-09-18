<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Details
$LN_APPLN_NO = mysql_real_escape_string(trim($_GET['k']));


# ... R001: FETCH LN APPLN DETAILS ............................................................................................#
$la = array();
$la =  FetchLoanApplnsById($LN_APPLN_NO);
$RECORD_ID = $la['RECORD_ID'];
$LN_APPLN_NO = $la['LN_APPLN_NO'];
$IS_WALK_IN = $la['IS_WALK_IN'];
$IS_TOP_UP = $la['IS_TOP_UP'];
$TOP_UP_LOAN_ID = $la['TOP_UP_LOAN_ID'];
$CUST_ID = $la['CUST_ID'];
$LN_PDT_ID = $la['LN_PDT_ID'];
$LN_APPLN_CREATION_DATE = $la['LN_APPLN_CREATION_DATE'];
$LN_APPLN_PROGRESS_STATUS = $la['LN_APPLN_PROGRESS_STATUS'];
$RQSTD_AMT = $la['RQSTD_AMT'];
$RQSTD_RPYMT_PRD = $la['RQSTD_RPYMT_PRD'];
$PURPOSE = $la['PURPOSE'];
$LN_APPLN_SUBMISSION_DATE = $la['LN_APPLN_SUBMISSION_DATE'];
$LN_APPLN_ASSMT_STATUS = $la['LN_APPLN_ASSMT_STATUS'];
$LN_APPLN_ASSMT_RMKS = $la['LN_APPLN_ASSMT_RMKS'];
$LN_APPLN_ASSMT_DATE = $la['LN_APPLN_ASSMT_DATE'];
$LN_APPLN_ASSMT_USER_ID = $la['LN_APPLN_ASSMT_USER_ID'];
$LN_APPLN_DOC_STATUS = $la['LN_APPLN_DOC_STATUS'];
$LN_APPLN_DOC_RMKS = $la['LN_APPLN_DOC_RMKS'];
$LN_APPLN_DOC_DATE = $la['LN_APPLN_DOC_DATE'];
$LN_APPLN_DOC_USER_ID = $la['LN_APPLN_DOC_USER_ID'];
$LN_APPLN_GRRTR_STATUS = $la['LN_APPLN_GRRTR_STATUS'];
$LN_APPLN_GRRTR_RMKS = $la['LN_APPLN_GRRTR_RMKS'];
$LN_APPLN_GRRTR_DATE = $la['LN_APPLN_GRRTR_DATE'];
$LN_APPLN_GRRTR_USER_ID = $la['LN_APPLN_GRRTR_USER_ID'];
$VERIF_STATUS = $la['VERIF_STATUS'];
$VERIF_DATE = $la['VERIF_DATE'];
$VERIF_RMKS = $la['VERIF_RMKS'];
$VERIF_USER_ID = $la['VERIF_USER_ID'];
$CC_FLG = $la['CC_FLG'];
$CC_RECEIVE_DATE = $la['CC_RECEIVE_DATE'];
$CC_HANDLER_WKFLW_ID = $la['CC_HANDLER_WKFLW_ID'];
$CC_STATUS = $la['CC_STATUS'];
$CC_STATUS_DATE = $la['CC_STATUS_DATE'];
$CC_RMKS = $la['CC_RMKS'];
$CREDIT_OFFICER_RCMNDTN_USER_ID = $la['CREDIT_OFFICER_RCMNDTN_USER_ID'];
$RCMNDTN_REQUEST_SEND_DATE = $la['RCMNDTN_REQUEST_SEND_DATE'];
$RCMNDD_APPLN_AMT = $la['RCMNDD_APPLN_AMT'];
$RCMNDTN_CUST_RESPONSE_DATE = $la['RCMNDTN_CUST_RESPONSE_DATE'];
$APPROVAL_STATUS = $la['APPROVAL_STATUS'];
$APPROVED_AMT = $la['APPROVED_AMT'];
$APPROVED_BY = $la['APPROVED_BY'];
$APPROVAL_DATE = $la['APPROVAL_DATE'];
$APPROVAL_RMKS = $la['APPROVAL_RMKS'];
$CORE_LOAN_ACCT_ID = $la['CORE_LOAN_ACCT_ID'];
$CORE_SVGS_ACCT_ID = $la['CORE_SVGS_ACCT_ID'];
$CUST_FIN_INST_ID = $la['CUST_FIN_INST_ID'];
$PROC_MODE = $la['PROC_MODE'];
$PROC_BATCH_NO = $la['PROC_BATCH_NO'];
$CORE_RESOURCE_ID = $la['CORE_RESOURCE_ID'];
$LN_APPLN_STATUS = $la['LN_APPLN_STATUS'];


# ... R002: GET LOAN PRODUCT DETAILS ............................................................................................#
$loan_product = array();
$response_msg = FetchLoanProductDetailsById($LN_PDT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$loan_product = $response_msg["CORE_RESP"];

# ... R003: LOAN PDT APPLN CONNFIG RULES ........................................................................................#
$appln_config = array();
$appln_config = FetchLoanApplnConfigByProductId($LN_PDT_ID);
$APPLN_CONFIG_ID = $appln_config['APPLN_CONFIG_ID'];
$APPLN_CONFIG_NAME = $appln_config['APPLN_CONFIG_NAME'];
$APPLN_TYPE_ID = $appln_config['APPLN_TYPE_ID'];
$PDT_ID = $appln_config['PDT_ID'];
$PDT_TYPE_ID = $appln_config['PDT_TYPE_ID'];


# ... R004: GET CUST BANK DETAILS ..............................................................................................#
$CUST_BANK_NAME = GetCustBankFromBankAcct($CUST_ID, $CUST_FIN_INST_ID);


# ... R004: GET CUSTOMER SAVINGS ACCT ID .......................................................................................#
$response_msg = FetchSavingsAcctById($CORE_SVGS_ACCT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$CORE_SVGS_ACCT_NUM = $CORE_RESP["accountNo"];
$CORE_SVGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];


# ... R005: LOAD CUSTOMER DETAILS .....................................................................................#
$CUST_CORE_ID = "";
$CUST_EMAIL = "";
$CUST_PHONE = "";

if ($IS_WALK_IN=="YES") {
  $data_details = explode('-', $CUST_ID);
  $CUST_CORE_ID = $data_details[1];
}

if ($IS_WALK_IN=="NO") {
  $cstmr = array();
  $cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
  $CUST_ID = $cstmr['CUST_ID'];
  $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
  $CUST_EMAIL = $cstmr['CUST_EMAIL'];
  $CUST_PHONE = $cstmr['CUST_PHONE'];
}


# ... Get Customer Name From Core
$response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$displayName = strtoupper($CORE_RESP["displayName"]);

# ... Decrypt Email & Phone
$EMAIL = AES256::decrypt($CUST_EMAIL);
$PHONE = AES256::decrypt($CUST_PHONE);
$_SESSION['FP_NAME'] = $displayName;
$_SESSION['FP_EMAIL'] = $EMAIL;
$_SESSION['FP_PHONE'] = $PHONE;


# ... R006: LOAD LOAN VALIDATION SYSTEM RMKS .....................................................................................#
# ... Get Application Type Menu
$config_param_list = array();
$config_param_list = FetchLoanApplnConfigByProductId($PDT_ID);
$_SESSION["CONFIG_PARAM_LIST"] = $config_param_list;

# ... Define Appln Exclusion Checklist
$LN_APPLN_CHECKLIST = array("PRM_01","PRM_02","PRM_03","PRM_04","PRM_07","PRM_08","PRM_09","PRM_10","PRM_11","","");
$_SESSION["LN_APPLN_CHECKLIST"] = $LN_APPLN_CHECKLIST;


# ... R006: DETERMING SYSTEM COURSE OF ACTION .....................................................................................#
$VV = array();
if ( ($LN_APPLN_ASSMT_STATUS=="")||($LN_APPLN_DOC_STATUS=="")||($LN_APPLN_GRRTR_STATUS=="") ) {
  $VV["DISP_FLG"] = "DONT_DISPLAY";
} else {

  $VV["DISP_FLG"] = "DISPLAY";

  # ... Button type
  if ( ( ($LN_APPLN_ASSMT_STATUS=="GOOD")||($LN_APPLN_ASSMT_STATUS=="NOT_NEEDED") )&&
       ( ($LN_APPLN_DOC_STATUS=="GOOD")||($LN_APPLN_DOC_STATUS=="NOT_NEEDED") )&&
       ( ($LN_APPLN_GRRTR_STATUS=="GOOD")||($LN_APPLN_GRRTR_STATUS=="NOT_NEEDED") ) 
     ) {
    $VV["BTN_TYPE"] = "GOOD_BUTTON";
  } else {
    $VV["BTN_TYPE"] = "BAD_BUTTON";
  }


  # ... Message Type
  $VV["MSG"] = "";

  # ... Appln Assessment
  if ($LN_APPLN_ASSMT_STATUS=="NOT_NEEDED") {
    $VV["MSG"] .= "<br>Application Assessment ---------------------------- <span style='color: black; font-weight: bolder;'>[NOT_NEEDED]</span>";  
  } else if ($LN_APPLN_ASSMT_STATUS=="NOT_GOOD") {
    $VV["MSG"] .= "<br>Application Assessment ---------------------------- <span style='color: red; font-weight: bolder;'>[NOT_OKAY]</span>";  
  } else if ($LN_APPLN_ASSMT_STATUS=="GOOD") {
    $VV["MSG"] .= "<br>Application Assessment ---------------------------- <span style='color: green; font-weight: bolder;'>[OKAY]</span>";  
  }

  # ... Loan Doc Assessment
  if ($LN_APPLN_DOC_STATUS=="NOT_NEEDED") {
    $VV["MSG"] .= "<br>Loan Documents ------------------------------------ <span style='color: black; font-weight: bolder;'>[NOT_NEEDED]</span>";  
  } else if ($LN_APPLN_DOC_STATUS=="NOT_GOOD") {
    $VV["MSG"] .= "<br>Loan Documents ------------------------------------ <span style='color: red; font-weight: bolder;'>[NOT_OKAY]</span>";  
  } else if ($LN_APPLN_DOC_STATUS=="GOOD") {
    $VV["MSG"] .= "<br>Loan Documents ------------------------------------ <span style='color: green; font-weight: bolder;'>[OKAY]</span>";  
  }

  # ... Loan Guarrantor Assessment
  if ($LN_APPLN_GRRTR_STATUS=="NOT_NEEDED") {
    $VV["MSG"] .= "<br>Loan Guarrantor Assessment ------------------------ <span style='color: black; font-weight: bolder;'>[NOT_NEEDED]</span>";  
  } else if ($LN_APPLN_GRRTR_STATUS=="NOT_GOOD") {
    $VV["MSG"] .= "<br>Loan Guarrantor Assessment ------------------------ <span style='color: red; font-weight: bolder;'>[NOT_OKAY]</span>";  
  } else if ($LN_APPLN_GRRTR_STATUS=="GOOD") {
    $VV["MSG"] .= "<br>Loan Guarrantor Assessment ------------------------ <span style='color: green; font-weight: bolder;'>[OKAY]</span>";  
  }
}

//echo "<pre>".print_r($VV,true)."</pre>";



# ... F0000001: RUN LOAN APPLN ASSESSMENT .....................................................................................#
if (isset($_POST['btn_val_details'])) {

  // ... Data Variables
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));

  $FIN_INST_ACCT = mysql_real_escape_string(trim($_POST['FIN_INST_ACCT']));
  $LN_AMT_RQSTD = mysql_real_escape_string(trim($_POST['LN_AMT_RQSTD']));
  $LN_RPYMT_PRD = mysql_real_escape_string(trim($_POST['LN_RPYMT_PRD']));
  $LN_PURPOSE = mysql_real_escape_string(trim($_POST['LN_PURPOSE']));

  $SVNGS_ACCT_ID = mysql_real_escape_string(trim($_POST['SVNGS_ACCT_ID']));
  $SVNGS_ACCT_BAL = mysql_real_escape_string(trim($_POST['SVNGS_ACCT_BAL']));


  // ...  ...  ...  ...  ...  ...  ...  ...  ... VALIDATION OF INPUT ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... //
  $CONFIG_PARAM_LIST = $_SESSION["CONFIG_PARAM_LIST"];
  $VAL_OBSRVN = array();
  $VAL_FINAL_RESULT = array();

  // ... 01: Requires Guarantorship (PRM_01, PRM_02)
  $PRM_01 = $CONFIG_PARAM_LIST["PRM_01"];
  $PRM_02 = $CONFIG_PARAM_LIST["PRM_02"];
  if ($PRM_01=="NO") {    
    $VAL_OBSRVN["PRM_01_OBSRVN"] = "N/A";
    $VAL_FINAL_RESULT["PRM_01_FINAL_RESULT"] = "N/A";
    $VAL_OBSRVN["PRM_02_OBSRVN"] = "N/A";
    $VAL_FINAL_RESULT["PRM_02_FINAL_RESULT"] = "N/A";
  }  elseif ($PRM_01=="YES") {

    # ... Fetching Number of gurantors provided
    $Q_KK = "SELECT count(*) as RTN_VALUE FROM loan_appln_guarantors WHERE LN_APPLN_NO='$LN_APPLN_NO' AND GUARANTORSHIP_STATUS='APPROVED'";
    $CNT_KK = ReturnOneEntryFromDB($Q_KK);

    if ($CNT_KK>=$PRM_02) {
      $VAL_OBSRVN["PRM_01_OBSRVN"] = "Validated";
      $VAL_FINAL_RESULT["PRM_01_FINAL_RESULT"] = "";

      $VAL_OBSRVN["PRM_02_OBSRVN"] = "Customer has ".number_format($CNT_KK)." guarantor(s). This is okay";
      $VAL_FINAL_RESULT["PRM_02_FINAL_RESULT"] = "<span style='color: green; font-weight: bolder;'>Pass</span>";
    } elseif ($CNT_KK<$PRM_02) {
      $VAL_OBSRVN["PRM_01_OBSRVN"] = "Validated";
      $VAL_FINAL_RESULT["PRM_01_FINAL_RESULT"] = "";

      $VAL_OBSRVN["PRM_02_OBSRVN"] = "Customer has ".number_format($CNT_KK)." guarantor(s). This is not enough since the requirement is ".number_format($PRM_02)." guarantor(s).";
      $VAL_FINAL_RESULT["PRM_02_FINAL_RESULT"] = "<span style='color: red; font-weight: bold;'>Fail</span>";
    }
  }

  // ... 02: Apply Current Savings to Loan Amount Requested (PRM_03, PRM_04)
  $PRM_03 = $CONFIG_PARAM_LIST["PRM_03"];
  $PRM_04 = $CONFIG_PARAM_LIST["PRM_04"];
  if ($PRM_03=="NO") {    
    $VAL_OBSRVN["PRM_03_OBSRVN"] = "N/A";
    $VAL_FINAL_RESULT["PRM_03_FINAL_RESULT"] = "N/A";
    $VAL_OBSRVN["PRM_04_OBSRVN"] = "N/A";
    $VAL_FINAL_RESULT["PRM_04_FINAL_RESULT"] = "N/A";
  } elseif ($PRM_03=="YES") {

    $CUR_SVNGS_SLR = ($PRM_04*$SVNGS_ACCT_BAL);   // ...........  SAVINGS BALANCES WITH SLR

    if ($LN_AMT_RQSTD>$CUR_SVNGS_SLR) {
      $VAL_OBSRVN["PRM_03_OBSRVN"] = "Validated";
      $VAL_FINAL_RESULT["PRM_03_FINAL_RESULT"] = "";

      $VAL_OBSRVN["PRM_04_OBSRVN"] = "Requested loan amount (".number_format($LN_AMT_RQSTD).") exceeds $PRM_04 times (".number_format($CUR_SVNGS_SLR).") the amount of your current savings balance (".number_format($SVNGS_ACCT_BAL).")";
      $VAL_FINAL_RESULT["PRM_04_FINAL_RESULT"] = "<span style='color: red; font-weight: bold;'>Fail</span>";
    } else if ($LN_AMT_RQSTD<=$CUR_SVNGS_SLR) {
      $VAL_OBSRVN["PRM_03_OBSRVN"] = "Validated";
      $VAL_FINAL_RESULT["PRM_03_FINAL_RESULT"] = "";

      $VAL_OBSRVN["PRM_04_OBSRVN"] = "Requested loan amount is okay";
      $VAL_FINAL_RESULT["PRM_04_FINAL_RESULT"] = "<span style='color: green; font-weight: bolder;'>Pass</span>";
    }
  }

  // ... 03: Allow Concurrent Loan (PRM_07, PRM_08)
  $PRM_07 = $CONFIG_PARAM_LIST["PRM_07"];
  $PRM_08 = $CONFIG_PARAM_LIST["PRM_08"];
  if ($PRM_07=="NO") {
    $VAL_OBSRVN["PRM_07_OBSRVN"] = "N/A";
    $VAL_FINAL_RESULT["PRM_07_FINAL_RESULT"] = "N/A";
    $VAL_OBSRVN["PRM_08_OBSRVN"] = "N/A";
    $VAL_FINAL_RESULT["PRM_08_FINAL_RESULT"] = "N/A";
  }  elseif ($PRM_07=="YES") {

    // ... Get List of Loan Accounts
    $response_msg = GetCustLoansAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];
    $ACCTS_DATA = array();
    $ACCTS_DATA = $CORE_RESP["data"];

    $LN_ACCT_COUNT = sizeof($ACCTS_DATA);
    if ($LN_ACCT_COUNT>=$PRM_08) {
      $VAL_OBSRVN["PRM_07_OBSRVN"] = "Validated";
      $VAL_FINAL_RESULT["PRM_07_FINAL_RESULT"] = "";

      $VAL_OBSRVN["PRM_08_OBSRVN"] = "You are observed to have ".number_format($LN_ACCT_COUNT)." active loan account(s). Expected concurrent loans allowed is ".number_format($PRM_08)." loan accounts";
      $VAL_FINAL_RESULT["PRM_08_FINAL_RESULT"] = "<span style='color: red; font-weight: bold;'>Fail</span>";
    } else if ($LN_ACCT_COUNT<$PRM_08) {
      $VAL_OBSRVN["PRM_07_OBSRVN"] = "Validated";
      $VAL_FINAL_RESULT["PRM_07_FINAL_RESULT"] = "";

      $VAL_OBSRVN["PRM_08_OBSRVN"] = "You are observed to have ".number_format($LN_ACCT_COUNT)." active loan account(s). This is okay";
      $VAL_FINAL_RESULT["PRM_08_FINAL_RESULT"] = "<span style='color: red; font-weight: bold;'>Pass</span>";
    }
  }

  // ... 04: Involve Credit Committee if loan amount requested is big (PRM_09, PRM_10, PRM_11)
  $PRM_09 = $CONFIG_PARAM_LIST["PRM_09"];
  $PRM_10 = $CONFIG_PARAM_LIST["PRM_10"];
  $PRM_11 = $CONFIG_PARAM_LIST["PRM_11"];
  if ($PRM_09=="NO") {
    $VAL_OBSRVN["PRM_09_OBSRVN"] = "N/A";
    $VAL_FINAL_RESULT["PRM_09_FINAL_RESULT"] = "N/A";
    $VAL_OBSRVN["PRM_10_OBSRVN"] = "N/A";
    $VAL_FINAL_RESULT["PRM_10_FINAL_RESULT"] = "N/A";
    $VAL_OBSRVN["PRM_11_OBSRVN"] = "N/A";
    $VAL_FINAL_RESULT["PRM_11_FINAL_RESULT"] = "N/A";
  } elseif ($PRM_09=="YES") {
    $VAL_OBSRVN["PRM_07_OBSRVN"] = "Validated";
    $VAL_FINAL_RESULT["PRM_07_FINAL_RESULT"] = "";
    
    if ($LN_AMT_RQSTD>$PRM_10) {
      $VAL_OBSRVN["PRM_10_OBSRVN"] = "Loan amount requested <b>".number_format($LN_AMT_RQSTD)."</b> requires credit committee approval because it exceeds <b>".number_format($PRM_10)."</b>";
      $VAL_FINAL_RESULT["PRM_10_FINAL_RESULT"] = "<span style='color: red; font-weight: bold;'>CC_APPRVL_RQRD</span>";
    } else if ($LN_AMT_RQSTD==$PRM_10) {
      $VAL_OBSRVN["PRM_10_OBSRVN"] = "Loan amount requested <b>".number_format($LN_AMT_RQSTD)."</b> requires credit committee approval because it equals <b>".number_format($PRM_10)."</b>";
      $VAL_FINAL_RESULT["PRM_10_FINAL_RESULT"] = "<span style='color: red; font-weight: bold;'>CC_APPRVL_RQRD</span>";
    } else if ($LN_AMT_RQSTD<$PRM_10) {
      $VAL_OBSRVN["PRM_10_OBSRVN"] = "Loan amount requested <b>".number_format($LN_AMT_RQSTD)."</b> doesnot  requires credit committee approval because it is less than <b>".number_format($PRM_10)."</b>";
      $VAL_FINAL_RESULT["PRM_10_FINAL_RESULT"] = "<span style='color: green; font-weight: bold;'>CC_APPRVL_NOT_RQRD</span>";
    }

    $VAL_OBSRVN["PRM_11_OBSRVN"] = "";
    $VAL_FINAL_RESULT["PRM_11_FINAL_RESULT"] = "";
  }


  // ... Assembling the Responses
  $_SESSION["VAL_OBSRVN"] = $VAL_OBSRVN;
  $_SESSION["VAL_FINAL_RESULT"] = $VAL_FINAL_RESULT;


  // ...  ...  ...  ...  ...  ...  ...  ...  ... VALIDATION OF INPUT ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... //

  $data_transfer = "k=$LN_APPLN_NO";

  $next_page = "la-queue-ind?$data_transfer";
  NavigateToNextPage($next_page);
}

# ... F0000002: SAVE ASSESSMENT ..............................................................................................#
if (isset($_POST['btn_sub_loan_assmt'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $LN_APPLN_ASSMT_STATUS = mysql_real_escape_string(trim($_POST['LN_APPLN_ASSMT_STATUS']));
  $LN_APPLN_ASSMT_RMKS = mysql_real_escape_string(trim($_POST['LN_APPLN_ASSMT_RMKS']));
  $LN_APPLN_ASSMT_DATE = GetCurrentDateTime();
  $LN_APPLN_ASSMT_USER_ID = $_SESSION['UPR_USER_ID']; 

  // ... SQL
  $q = "UPDATE loan_applns SET LN_APPLN_ASSMT_STATUS='$LN_APPLN_ASSMT_STATUS'
                              ,LN_APPLN_ASSMT_RMKS='$LN_APPLN_ASSMT_RMKS'
                              ,LN_APPLN_ASSMT_DATE='$LN_APPLN_ASSMT_DATE'
                              ,LN_APPLN_ASSMT_USER_ID='$LN_APPLN_ASSMT_USER_ID' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "SAVE_ASSESSMENT";
    $EVENT_OPERATION = "SAVE_LOAN_APPL_ASSESSMENT_DETAILS";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $LN_APPLN_ASSMT_STATUS."|".$LN_APPLN_ASSMT_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Assessment has been saved.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:0;");
  }
}

# ... F0000003: SAVE ASSESSMENT (AMENDMENT) ..............................................................................................#
if (isset($_POST['btn_sub_loan_assmt_amend'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $LN_APPLN_ASSMT_STATUS = mysql_real_escape_string(trim($_POST['LN_APPLN_ASSMT_STATUS']));
  $LN_APPLN_ASSMT_RMKS = mysql_real_escape_string(trim($_POST['LN_APPLN_ASSMT_RMKS']));
  $LN_APPLN_ASSMT_DATE = GetCurrentDateTime();
  $LN_APPLN_ASSMT_USER_ID = $_SESSION['UPR_USER_ID']; 

  // ... SQL
  $q = "UPDATE loan_applns SET LN_APPLN_ASSMT_STATUS='$LN_APPLN_ASSMT_STATUS'
                              ,LN_APPLN_ASSMT_RMKS='$LN_APPLN_ASSMT_RMKS'
                              ,LN_APPLN_ASSMT_DATE='$LN_APPLN_ASSMT_DATE'
                              ,LN_APPLN_ASSMT_USER_ID='$LN_APPLN_ASSMT_USER_ID' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "AMEND_ASSESSMENT";
    $EVENT_OPERATION = "AMEND_LOAN_APPL_ASSESSMENT_DETAILS";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $LN_APPLN_ASSMT_STATUS."|".$LN_APPLN_ASSMT_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Assessment has been amended.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:0;");
  }
}

# ... F0000003: SAVE LOAN DOC ASSMT  ..........................................................................................#
if (isset($_POST['btn_sub_loan_doc'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $LN_APPLN_DOC_STATUS = mysql_real_escape_string(trim($_POST['LN_APPLN_DOC_STATUS']));
  $LN_APPLN_DOC_RMKS = mysql_real_escape_string(trim($_POST['LN_APPLN_DOC_RMKS']));
  $LN_APPLN_DOC_DATE = GetCurrentDateTime();
  $LN_APPLN_DOC_USER_ID = $_SESSION['UPR_USER_ID']; 

  // ... SQL
  $q = "UPDATE loan_applns SET LN_APPLN_DOC_STATUS='$LN_APPLN_DOC_STATUS'
                              ,LN_APPLN_DOC_RMKS='$LN_APPLN_DOC_RMKS'
                              ,LN_APPLN_DOC_DATE='$LN_APPLN_DOC_DATE'
                              ,LN_APPLN_DOC_USER_ID='$LN_APPLN_DOC_USER_ID' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "SAVE_LOAN_DOC_ASSMT";
    $EVENT_OPERATION = "SAVE_LOAN_DOC_ASSESSMENT_DETAILS";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $LN_APPLN_DOC_STATUS."|".$LN_APPLN_DOC_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Loan documents assessment has been saved.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... F0000004: SAVE LOAN DOC ASSMT (AMENDMENT) ..........................................................................................#
if (isset($_POST['btn_sub_loan_doc_amend'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $LN_APPLN_DOC_STATUS = mysql_real_escape_string(trim($_POST['LN_APPLN_DOC_STATUS']));
  $LN_APPLN_DOC_RMKS = mysql_real_escape_string(trim($_POST['LN_APPLN_DOC_RMKS']));
  $LN_APPLN_DOC_DATE = GetCurrentDateTime();
  $LN_APPLN_DOC_USER_ID = $_SESSION['UPR_USER_ID']; 

  // ... SQL
  $q = "UPDATE loan_applns SET LN_APPLN_DOC_STATUS='$LN_APPLN_DOC_STATUS'
                              ,LN_APPLN_DOC_RMKS='$LN_APPLN_DOC_RMKS'
                              ,LN_APPLN_DOC_DATE='$LN_APPLN_DOC_DATE'
                              ,LN_APPLN_DOC_USER_ID='$LN_APPLN_DOC_USER_ID' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "AMEND_LOAN_DOC_ASSMT";
    $EVENT_OPERATION = "AMEND_LOAN_DOC_ASSESSMENT_DETAILS";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $LN_APPLN_DOC_STATUS."|".$LN_APPLN_DOC_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Loan documents assessment has been amended.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:0;");
  }
}

# ... F0000007: SAVE GUARRANTOR ASSMT ..........................................................................................#
if (isset($_POST['btn_sub_loan_grrt'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $LN_APPLN_GRRTR_STATUS = mysql_real_escape_string(trim($_POST['LN_APPLN_GRRTR_STATUS']));
  $LN_APPLN_GRRTR_RMKS = mysql_real_escape_string(trim($_POST['LN_APPLN_GRRTR_RMKS']));
  $LN_APPLN_GRRTR_DATE = GetCurrentDateTime();
  $LN_APPLN_GRRTR_USER_ID = $_SESSION['UPR_USER_ID']; 

  // ... SQL
  $q = "UPDATE loan_applns SET LN_APPLN_GRRTR_STATUS='$LN_APPLN_GRRTR_STATUS'
                              ,LN_APPLN_GRRTR_RMKS='$LN_APPLN_GRRTR_RMKS'
                              ,LN_APPLN_GRRTR_DATE='$LN_APPLN_GRRTR_DATE'
                              ,LN_APPLN_GRRTR_USER_ID='$LN_APPLN_GRRTR_USER_ID' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "SAVE_GRRTR_ASSMT";
    $EVENT_OPERATION = "SAVE_GUARRANTOR_ASSESSMENT_DETAILS";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $LN_APPLN_GRRTR_STATUS."|".$LN_APPLN_GRRTR_USER_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Assessment has been saved.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:0;");
  }
}

# ... F0000006: SAVE GUARRANTOR ASSMT  (AMENDMENT) ..........................................................................................#
if (isset($_POST['btn_sub_loan_grrt_amend'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $LN_APPLN_GRRTR_STATUS = mysql_real_escape_string(trim($_POST['LN_APPLN_GRRTR_STATUS']));
  $LN_APPLN_GRRTR_RMKS = mysql_real_escape_string(trim($_POST['LN_APPLN_GRRTR_RMKS']));
  $LN_APPLN_GRRTR_DATE = GetCurrentDateTime();
  $LN_APPLN_GRRTR_USER_ID = $_SESSION['UPR_USER_ID']; 

  // ... SQL
  $q = "UPDATE loan_applns SET LN_APPLN_GRRTR_STATUS='$LN_APPLN_GRRTR_STATUS'
                              ,LN_APPLN_GRRTR_RMKS='$LN_APPLN_GRRTR_RMKS'
                              ,LN_APPLN_GRRTR_DATE='$LN_APPLN_GRRTR_DATE'
                              ,LN_APPLN_GRRTR_USER_ID='$LN_APPLN_GRRTR_USER_ID' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "AMEND_GRRTR_ASSMT";
    $EVENT_OPERATION = "AMEND_GUARRANTOR_ASSESSMENT_DETAILS";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $LN_APPLN_GRRTR_STATUS."|".$LN_APPLN_GRRTR_USER_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Assessment has been amended.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:0;");
  }
}


# ... F0000007: REJECT LOAN APPLN VERIFICATION  ..........................................................................................#
if (isset($_POST['btn_reject_loan_appln_verif'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $VERIF_STATUS = "REJECTED";
  $VERIF_DATE = GetCurrentDateTime();
  $VERIF_RMKS = mysql_real_escape_string(trim($_POST['VERIF_RMKS']));
  $VERIF_USER_ID = $_SESSION['UPR_USER_ID']; 
  $LN_APPLN_STATUS = "RETURNED_TO_CUSTOMER";

  // ... SQL
  $q = "UPDATE loan_applns SET VERIF_STATUS='$VERIF_STATUS'
                              ,VERIF_DATE='$VERIF_DATE'
                              ,VERIF_RMKS='$VERIF_RMKS'
                              ,VERIF_USER_ID='$VERIF_USER_ID'
                              ,LN_APPLN_STATUS='$LN_APPLN_STATUS' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {


    # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "LOAN APPLICATION RETURNED";
    $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
    $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                    ."Your loan application has not been approved yet because it is not meeting the expected requirements for this loan product. Below are the details;<br>"
                    ."-------------------------------------------------------------------------------------------------<br>"
                    ."<b>APPLN REF:</b> <i>".$LN_APPLN_NO."</i><br>"
                    ."<b>REMARKS FROM MANAGEMENT:</b><br> "
                    ."<i>".$VERIF_RMKS."</i><br>"
                    ."-------------------------------------------------------------------------------------------------<br>"
                    ."<br/>"
                    ."You are requested to log in and make ammendments as advised. Then re-submit as soon as possible.<br>"
                    ."Regards<br>"
                    ."Management<br>"
                    ."<i></i>";
    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

    $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
    ExecuteEntityInsert($qqq);

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "RETURN_TO_CUSTOMER_FOR_REVIEW";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $VERIF_RMKS."|".$VERIF_USER_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Loan application has been returned to customer for review. An email has been sent out to the customer";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:0;");
  }
}


# ... F0000007: VERIFY & ACCEPT LOAN APPLN VERIFICATION ........................................................................................#
if (isset($_POST['btn_accept_loan_appln_verif'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $VERIF_STATUS = "APPROVED";
  $VERIF_DATE = GetCurrentDateTime();
  $VERIF_RMKS = mysql_real_escape_string(trim($_POST['VERIF_RMKS']));
  $VERIF_USER_ID = $_SESSION['UPR_USER_ID']; 

  # ... Determining Credit Committee Status
  $la = array();
  $la =  FetchLoanApplnsById($LN_APPLN_NO);
  $VV_RQSTD_AMT = $la['RQSTD_AMT'];
  $VV_LN_PDT_ID = $la['LN_PDT_ID'];

  $CC_FLG = "";
  $LN_APPLN_STATUS = "";
  $CC_RECEIVE_DATE = GetCurrentDateTime();
  $VV_THRESHOLD = GetCCThresholdValue($VV_LN_PDT_ID);
  if ($VV_RQSTD_AMT>=$VV_THRESHOLD) {
    $CC_FLG = "YY";
    $LN_APPLN_STATUS = "VERIFIED";
  } else if ($VV_RQSTD_AMT<$VV_THRESHOLD) {
    $CC_FLG = "NN";
    $LN_APPLN_STATUS = "READY_4_REVIEW";
  }

  

  // ... SQL
  $q = "UPDATE loan_applns SET VERIF_STATUS='$VERIF_STATUS'
                              ,VERIF_DATE='$VERIF_DATE'
                              ,VERIF_RMKS='$VERIF_RMKS'
                              ,VERIF_USER_ID='$VERIF_USER_ID'
                              ,CC_FLG='$CC_FLG'
                              ,CC_RECEIVE_DATE='$CC_RECEIVE_DATE'
                              ,LN_APPLN_STATUS='$LN_APPLN_STATUS' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "VERIFY_LOAN_APPLN";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $VERIF_RMKS."|".$VERIF_USER_ID."|".$CC_FLG."|".$CC_RECEIVE_DATE;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Loan application has been verified. Redirecting in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5; URL=la-queue");
  }
}




?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Loan Appln Details", $APP_SMALL_LOGO); 

    # ... Javascript
    LoadPriorityJS();
    OnLoadExecutions();
    StartTimeoutCountdown();
    ExecuteProcessStatistics();
    ?>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">

            <div class="navbar nav_title" style="border: 0;">
              <a href="main-dashboard" class="site_title"> <span><?php echo $APP_NAME; ?></span></a>
            </div>

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <?php SideNavBar($UPR_USER_ID, $UPR_USER_ROLE_DETAILS); ?>
            </div>
            <!-- /sidebar menu -->


          </div>
        </div>

        <!-- top navigation -->
        <?php TopNavBar($UPR_USER_ID, $core_username, $core_role_name); ?>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">

          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>

            <div class="x_panel">
              <div class="x_title">
              <a href="la-queue" class="btn btn-dark btn-sm pull-left">Back</a>
              <h2>REF: <?php echo $LN_APPLN_NO; ?></h2>
              <?php
              if ($VV["DISP_FLG"]=="DONT_DISPLAY") {
                // ... do nothing
              }
              if ($VV["DISP_FLG"]=="DISPLAY") {
                
                # .... BAD BUTTON ...............................................................................................#
                if ($VV["BTN_TYPE"]=="BAD_BUTTON") {                
                  ?>
                  <button type="button" class="btn btn-danger btn-sm pull-right" data-toggle="modal" data-target="#verif_bd">Cannot Verify</button>
                  <div id="verif_bd" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-mm">
                      <div class="modal-content" style="color: #333;">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Cannot Verify Loan Application</h4>
                        </div>
                        <div class="modal-body">
                            <form id="verif_bd" method="post">
                              <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                              <b>This loan application doesnot meet the requirements for verification because;</b><br>
                              <?php echo $VV["MSG"]; ?><br><br>
                             

                              <b>Therefore you will have to return it to the loan applicant for correction;</b><br>
                              Additional Remarks * <small>(This will be sent to the customer. You are encouraged to communicate clearly to prevent customer from misunderstanding.)</small>
                              <textarea id="VERIF_RMKS" name="VERIF_RMKS" class="form-control" required=""></textarea><br>
                              <button type="submit" class="btn btn-default btn-sm" name="btn_reject_loan_appln_verif">Submit Rejection Remarks</button>
                            </form>
                        </div>
                       

                      </div>
                    </div>
                  </div>
                  <?php
                }

                # .... GOOD BUTTON ...............................................................................................#
                if ($VV["BTN_TYPE"]=="GOOD_BUTTON") {                
                  ?>
                  <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#verif_gd">Verify</button>
                  <div id="verif_gd" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-mm">
                      <div class="modal-content" style="color: #333;">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Verify Activation Request</h4>
                        </div>
                        <div class="modal-body">
                            <form id="verif_gd" method="post">
                              <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                              <b>Every things seems in order as shown below;</b><br>
                              <?php echo $VV["MSG"]; ?><br><br>
                             

                              <b>Therefore you can proceed to verify this loan application;</b><br>
                              Additional Remarks *
                              <textarea id="VERIF_RMKS" name="VERIF_RMKS" class="form-control" required=""></textarea><br>
                              <button type="submit" class="btn btn-primary btn-sm" name="btn_accept_loan_appln_verif">Submit Verification</button>
                            </form>
                        </div>
                       

                      </div>
                    </div>
                  </div>
                  <?php
                }
              }
              ?>
              <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td width="20%"><b>Loan Appln Ref</b></td><td colspan="3"><?php echo $LN_APPLN_NO; ?></td></tr>
                  <tr><td><b>Is Walk?</b></td><td colspan="3"><?php echo $IS_WALK_IN; ?></td></tr>
                  <tr><td><b>Is Loan TopUp?</b></td><td colspan="3"><?php echo $IS_TOP_UP; ?></td></tr>
                  <?php
                  if ($IS_TOP_UP=="YES") {
                    $response_msg = FetchLoanAcctById($TOP_UP_LOAN_ID, $MIFOS_CONN_DETAILS);
                    $CONN_FLG = $response_msg["CONN_FLG"];
                    $CORE_RESP = $response_msg["CORE_RESP"];
                    $Loan_Acct_No = $CORE_RESP["accountNo"];
                    $Loan_Product = $CORE_RESP["loanProductName"];
                    $TOP_UP_LOAN_ACCT_NUMBER = $Loan_Acct_No." - ".$Loan_Product;
                    ?>
                    <tr><td><b>Loan Account to TopUp</b></td><td colspan="3"><?php echo $TOP_UP_LOAN_ACCT_NUMBER; ?></td></tr>
                    <?php
                  }
                  ?>
                  <tr><td><b>Appln Submission Date</b></td><td colspan="3"><?php echo $LN_APPLN_SUBMISSION_DATE; ?></td></tr>
                  <tr>
                      <td><b>Appln Verification Date</b></td>
                      <td width="16%"><?php echo $VERIF_DATE; ?></td>
                      <td width="20%"><b>Verification Remarks</b></td>
                      <td><?php echo $VERIF_RMKS; ?></td>
                  </tr>
                  <tr>
                      <td><b>Appln Committee Date</b></td>
                      <td><?php echo $CC_STATUS_DATE; ?></td>
                      <td><b>Committee Remarks</b></td>
                      <td><?php echo $CC_RMKS; ?></td>
                  </tr>
                  <tr>
                      <td><b>Appln Approval Date</b></td>
                      <td><?php echo $APPROVAL_DATE; ?></td>
                      <td><b>Appln Approval Remarks</b></td>
                      <td><?php echo $APPROVAL_RMKS; ?></td>
                  </tr>
                  <tr>
                      <td><b>Appln Completion Date</b></td>
                      <td><?php echo ""; ?></td>
                      <td><b>Appln Completion Remarks</b></td>
                      <td><?php echo ""; ?></td>
                  </tr>
                  <tr><td><b>Appln Status</b></td><td colspan="3"><?php echo $LN_APPLN_STATUS; ?></td></tr>
                </table>
              </div>
            </div>
          </div>  
            

          <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-6 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION 01:</strong> Loan Product Details
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <?php
                if (sizeof($loan_product)>0) {
                  
                  // ... Product description
                  $pdt_id = $loan_product["pdt_id"];
                  $pdt_name = $loan_product["pdt_name"];
                  $pdt_short_name = $loan_product["pdt_short_name"];
                  $pdt_descrition = $loan_product["pdt_descrition"];
                  $pdt_status = $loan_product["pdt_status"];

                  // ... Principal Limits
                  $min_principal = $loan_product["min_principal"];
                  $default_principal = $loan_product["default_principal"];
                  $max_principal = $loan_product["max_principal"];
                  $MIN_PRINCIPAL = $min_principal;
                  $MAX_PRINCIPAL = $max_principal;

                  // ... Count of Repayments
                  $min_number_of_repayments = $loan_product["min_number_of_repayments"];
                  $default_number_of_repayments = $loan_product["default_number_of_repayments"];
                  $max_number_of_repayments = $loan_product["max_number_of_repayments"];

                  // ... Repayment Frequency
                  $repayment_every = $loan_product["repayment_every"];
                  $repayment_frequency_type_id = $loan_product["repayment_frequency_type_id"];
                  $repayment_frequency_type_code = $loan_product["repayment_frequency_type_code"];
                  $repayment_frequency_type_value = $loan_product["repayment_frequency_type_value"];

                  // ... Interest Rates Payable per Period
                  $min_interest_rate_per_period = $loan_product["min_interest_rate_per_period"];
                  $default_interest_rate_per_period = $loan_product["default_interest_rate_per_period"];
                  $max_interest_rate_per_period = $loan_product["max_interest_rate_per_period"];

                  // ... Interest Rate Frequency Type
                  $interest_rate_frequency_type_id = $loan_product["interest_rate_frequency_type_id"];
                  $interest_rate_frequency_type_code = $loan_product["interest_rate_frequency_type_code"];
                  $interest_rate_frequency_type_value = $loan_product["interest_rate_frequency_type_value"];

                  // ... Annual Interest Rate
                  $annual_interest_rate = $loan_product["annual_interest_rate"];

                  // ... Amortization Type Attributes
                  $amortization_type_id = $loan_product["amortization_type_id"];
                  $amortization_type_code = $loan_product["amortization_type_code"];
                  $amortization_type_value = $loan_product["amortization_type_value"];

                  // ... Interest Type Definition
                  $interest_type_id = $loan_product["interest_type_id"];
                  $interest_type_code = $loan_product["interest_type_code"];
                  $interest_type_value = $loan_product["interest_type_value"];

                  // ... Interest Calculation Period Type
                  $interest_calculation_period_type_id = $loan_product["interest_calculation_period_type_id"];
                  $interest_calculation_period_type_code = $loan_product["interest_calculation_period_type_code"];
                  $interest_calculation_period_type_value = $loan_product["interest_calculation_period_type_value"];

                  // ... Transaction Processing Strategy 
                  $transaction_processing_strategy_id = $loan_product["transaction_processing_strategy_id"];
                  $transaction_processing_strategy_name = $loan_product["transaction_processing_strategy_name"];

                  $status = ($pdt_status=="loanProduct.active") ? "ACTIVE" : "INACTIVE" ;
                  ?>
                  <table class="table table-bordered" style="font-size: 12px;">
                    <tr><td><b>Name</b></td><td><?php echo $pdt_name."($pdt_short_name)"; ?></td></tr>
                    <tr><td><b>Description</b></td><td><?php echo $pdt_descrition; ?></td></tr>
                    <tr><td><b>Principal</b></td>
                      <td>
                        {<b>MIN:</b> <?php echo number_format($min_principal); ?></b> - <b>MAX:</b><?php echo number_format($max_principal); ?>}
                      </td></tr>

                    <tr><td><b>Repayment Period</b></td>
                      <td>
                        {<b>MIN:</b> <?php echo $min_number_of_repayments." $repayment_frequency_type_value"; ?></b> - 
                         <b>MAX:</b> <?php echo $max_number_of_repayments." $repayment_frequency_type_value"; ?>}
                      </td></tr>


                    <tr><td><b>Repay Every</b></td><td><?php echo $repayment_every." ".$repayment_frequency_type_value; ?></td></tr>
                    <tr><td><b>Interest Rate</b></td>
                      <td><?php echo $default_interest_rate_per_period."% ".$interest_rate_frequency_type_value; ?>
                    </td></tr>
                    <tr><td><b>Amortization </b></td><td><?php echo $amortization_type_value." ($interest_type_value)"; ?></td></tr>
                  </table>


                  <?php
                } else {
                  ?>
                  Failed to display Loan Product Details
                  <?php
                }
                ?>
              </div>

            </div>
          </div>  


          <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLICATION DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLICATION DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-6 col-xs-12" >
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION 02:</strong> Loan Application Details
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Loan Amount Request</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo number_format($RQSTD_AMT); ?>">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Repayment Period (<?php echo $repayment_frequency_type_value; ?>)</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo number_format($RQSTD_RPYMT_PRD); ?>">
                </div>
               
                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Loan Purpose</label>
                  <textarea class="form-control" rows="3" disabled=""><?php echo $PURPOSE; ?></textarea>
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Customer Savings Account</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $CORE_SVGS_ACCT_NUM; ?>">
                </div>

                
                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Savings Account Balance</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $CORE_SVGS_ACCT_BAL; ?>">
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Customer Bank Acct for funds Transfer:</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $CUST_FIN_INST_ID." (".$CUST_BANK_NAME.")"; ?>">
                </div>

              </div>
            </div>
          </div>  


          <!-- -- -- -- -- -- -- -- -- -- -- LOAN VALIDATION DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN VALIDATION DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-12 col-xs-12" >
            <div class="x_panel">
                <strong>SECTION 03:</strong> Assess loan application against expected reguirements for this product.
                <table>
                  <tr valign="top">
                    <td>
                      <form method="post" id="awoifyhsdpocmh">
                        <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">

                        <input type="hidden" id="FIN_INST_ACCT" name="FIN_INST_ACCT" value="<?php echo $CUST_FIN_INST_ID; ?>">
                        <input type="hidden" id="LN_AMT_RQSTD" name="LN_AMT_RQSTD" value="<?php echo $RQSTD_AMT; ?>">
                        <input type="hidden" id="LN_RPYMT_PRD" name="LN_RPYMT_PRD" value="<?php echo $RQSTD_RPYMT_PRD; ?>">
                        <input type="hidden" id="LN_PURPOSE" name="LN_PURPOSE" value="<?php echo $PURPOSE; ?>">

                        <input type="hidden" id="SVNGS_ACCT_ID" name="SVNGS_ACCT_ID" value="<?php echo $CORE_SVGS_ACCT_ID; ?>">
                        <input type="hidden" id="SVNGS_ACCT_BAL" name="SVNGS_ACCT_BAL" value="<?php echo $CORE_SVGS_ACCT_BAL; ?>">
                        <button type="submit" class="btn btn-warning btn-xs pull-right" name="btn_val_details">Assess Appln</button>
                      </form>
                    </td>
                    <td>
                      <?php
                      if ($LN_APPLN_ASSMT_STATUS=="") {
                        ?>
                        <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#we5t">Verify Assessment</button>
                        <div id="we5t" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                          <div class="modal-dialog modal-sm">
                            <div class="modal-content">

                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel2">Verify Application Assessment</h4>
                              </div>
                              <div class="modal-body">
                                  <form method="post" id="eldihdosdfg">
                                    <input type="hidden" name="LN_APPLN_NO" id="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">

                                    <label>Assessment Observation:</label><br>
                                    <select class="form-control" id="LN_APPLN_ASSMT_STATUS" name="LN_APPLN_ASSMT_STATUS" required="">
                                      <option value="">------------</option>
                                      <option value="NOT_NEEDED">Assessment is not neccessary</option>
                                      <option value="NOT_GOOD">Appln failed assessment</option>
                                      <option value="GOOD">Appln passed assessment</option>
                                    </select><br>
                                    
                                    <label>Additional Remarks:</label><br>
                                    <textarea class="form-control" rows="3" id="LN_APPLN_ASSMT_RMKS" name="LN_APPLN_ASSMT_RMKS" required=""></textarea><br>
                                    
                                    <button type="submit" class="btn btn-primary btn-sm" name="btn_sub_loan_assmt">Save</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                  </form>
                              </div>
                             

                            </div>
                          </div>
                        </div>
                        <?php
                      } else if ($LN_APPLN_ASSMT_STATUS!="") {
                        ?>
                         <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#amend_we5t">Amend Assessment</button>
                        <div id="amend_we5t" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                          <div class="modal-dialog modal-sm">
                            <div class="modal-content">

                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel2">Amend Application Assessment</h4>
                              </div>
                              <div class="modal-body">
                                  <form method="post" id="amend_we5teldihdosdfg">
                                    <input type="hidden" name="LN_APPLN_NO" id="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">

                                    <label>Assessment Observation:</label><br>
                                    <?php
                                    if ($LN_APPLN_ASSMT_STATUS=="NOT_NEEDED") {
                                      ?>
                                      <select class="form-control" id="LN_APPLN_ASSMT_STATUS" name="LN_APPLN_ASSMT_STATUS" required="">
                                        <option value="">------------</option>
                                        <option value="NOT_NEEDED" selected="">Assessment is not neccessary</option>
                                        <option value="NOT_GOOD">Appln failed assessment</option>
                                        <option value="GOOD">Appln passed assessment</option>
                                      </select><br>
                                      <?php
                                    } else if ($LN_APPLN_ASSMT_STATUS=="NOT_GOOD") {
                                      ?>
                                      <select class="form-control" id="LN_APPLN_ASSMT_STATUS" name="LN_APPLN_ASSMT_STATUS" required="">
                                        <option value="">------------</option>
                                        <option value="NOT_NEEDED">Assessment is not neccessary</option>
                                        <option value="NOT_GOOD" selected="">Appln failed assessment</option>
                                        <option value="GOOD">Appln passed assessment</option>
                                      </select><br>
                                      <?php
                                    } else if ($LN_APPLN_ASSMT_STATUS=="GOOD") {
                                      ?>
                                      <select class="form-control" id="LN_APPLN_ASSMT_STATUS" name="LN_APPLN_ASSMT_STATUS" required="">
                                        <option value="">------------</option>
                                        <option value="NOT_NEEDED">Assessment is not neccessary</option>
                                        <option value="NOT_GOOD">Appln failed assessment</option>
                                        <option value="GOOD" selected="">Appln passed assessment</option>
                                      </select><br>
                                      <?php
                                    } else if ($LN_APPLN_ASSMT_STATUS=="") {
                                      ?>
                                      <select class="form-control" id="LN_APPLN_ASSMT_STATUS" name="LN_APPLN_ASSMT_STATUS" required="">
                                        <option value="">------------</option>
                                        <option value="NOT_NEEDED">Assessment is not neccessary</option>
                                        <option value="NOT_GOOD">Appln failed assessment</option>
                                        <option value="GOOD">Appln passed assessment</option>
                                      </select><br>
                                      <?php
                                    }
                                    ?>
                                    
                                    <label>Additional Remarks:</label><br>
                                    <textarea class="form-control" rows="3" id="LN_APPLN_ASSMT_RMKS" name="LN_APPLN_ASSMT_RMKS" required=""><?php echo $LN_APPLN_ASSMT_RMKS; ?></textarea><br>
                                    
                                    <button type="submit" class="btn btn-primary btn-sm" name="btn_sub_loan_assmt_amend">Save</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                  </form>
                              </div>
                             

                            </div>
                          </div>
                        </div>
                        <?php
                      }
                      ?>
                      
                    </td>
                  </tr>
                </table>
                <div class="clearfix"></div>
 
              <div class="x_content">

               <table class="table table-bordered" style="font-size: 12px;">
                  <tr valign="top">
                    <th bgcolor="#EEE">#</th>
                    <th bgcolor="#EEE">Requirement</th>
                    <th bgcolor="#EEE">Requirement Value</th>
                    <th bgcolor="#EEE">Assessment Observation</th>
                    <th bgcolor="#EEE">Final Result</th>
                  </tr>

                  <?php
                  $config_list = array();
                  $config_list = FetchApplnTypeMenu($APPLN_TYPE_ID);
                  $y = 0;

                  $VAL_OBSRVN = array();
                  $VAL_FINAL_RESULT = array();
                  $VAL_OBSRVN = (isset($_SESSION["VAL_OBSRVN"]))? $_SESSION["VAL_OBSRVN"] : array();
                  $VAL_FINAL_RESULT= (isset($_SESSION["VAL_FINAL_RESULT"]))? $_SESSION["VAL_FINAL_RESULT"] : array();

                  for ($x=0; $x < sizeof($config_list); $x++) { 
                    
                    $config_param = array();
                    $config_param = $config_list[$x];
                    $PP_RECORD_ID = $config_param['RECORD_ID'];
                    $PP_APPLN_TYPE_ID = $config_param['APPLN_TYPE_ID'];
                    $PP_PRM_FEATURE_ID = $config_param['PRM_FEATURE_ID'];
                    $PP_PRM_FEATURE_VALUE = $config_param['PRM_FEATURE_VALUE'];
                    $PP_PRM_INPUT_TYPE = $config_param['PRM_INPUT_TYPE'];
                    $PP_PRM_STATUS = $config_param['PRM_STATUS'];

                    # ... Obervation & Final Result Keys
                    $OBSRVN_KEY = $PP_PRM_FEATURE_ID."_OBSRVN";
                    $FINAL_RESULT_KEY = $PP_PRM_FEATURE_ID."_FINAL_RESULT";
                    $OBSRVN_VAL = "";
                    $FINAL_RESULT_VAL = "";
                    if (isset($VAL_OBSRVN[$OBSRVN_KEY])) {
                      $OBSRVN_VAL = $VAL_OBSRVN[$OBSRVN_KEY];
                    }
                    if (isset($VAL_FINAL_RESULT[$FINAL_RESULT_KEY])) {
                      $FINAL_RESULT_VAL = $VAL_FINAL_RESULT[$FINAL_RESULT_KEY];
                    }


                    # ... Get Feature Values
                    $F_VAL = $appln_config[$PP_PRM_FEATURE_ID];
                    $R_BGCOLOR = "";
                    if($F_VAL=="YES"){
                      $R_BGCOLOR = "#C1F5C3";
                    } elseif ($F_VAL=="NO") {
                      $R_BGCOLOR = "#FCC8C8";
                    } else {
                      $R_BGCOLOR = "white";
                    } 


                    # ... Formating PRM Feature into desired format
                    $FFTT_VALL = "";
                    if ($PP_PRM_FEATURE_ID=="PRM_10") {
                      $FFTT_VALL = number_format($appln_config[$PP_PRM_FEATURE_ID]);
                    } else if ($PP_PRM_FEATURE_ID=="PRM_11") {
                      $grp = array();
                      $grp = FetchAppMgtGroupById($appln_config[$PP_PRM_FEATURE_ID]);
                      $FFTT_VALL = isset($grp['GRP_NAME'])? $grp['GRP_NAME'] : "";
                    } else {
                      $FFTT_VALL = $appln_config[$PP_PRM_FEATURE_ID];
                    }

                    if (in_array($PP_PRM_FEATURE_ID, $LN_APPLN_CHECKLIST)) {
                      ?>
                      <tr valign="top">
                        <td width="4%"><?php echo ($y+1)."."; ?></td>
                        <td width="40%"><?php echo $PP_PRM_FEATURE_VALUE; ?></td>
                        <td width="18%" bgcolor="<?php echo $R_BGCOLOR; ?>"><?php echo $FFTT_VALL; ?></td>
                        <td width="25%"><?php echo $OBSRVN_VAL; ?></td>
                        <td width="10%"><?php echo $FINAL_RESULT_VAL; ?></td>
                      </tr>
                      <?php
                      $y++;
                    }                     
                  }
                  ?>

                   <tr valign="top">
                      <td colspan="5">
                        <strong>NOTE:</strong><br>
                        <em><strong>
                          The results of this system based assessment is to aid you in making an informed decision regarding the loan application from this customer. However you remain with the final power to decide whether the application passes or fails the assessment.
                        </strong></em>
                      </td>
                    </tr>
                </table>
                
              </div>
            </div>
          </div>

          <!-- -- -- -- -- -- -- -- -- -- -- LOAN DOCUMENTS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN DOCUMENTS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-6 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION 03:</strong> Loan Appln Documents
                <?php
                if ($LN_APPLN_DOC_STATUS=="") {
                  ?>
                  <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#crt_grp">Verify</button>
                  <div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Verify Loan Documents</h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="eldihdosdfg">
                              <input type="hidden" name="LN_APPLN_NO" id="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">

                              <label>Documents Observation:</label><br>
                              <select class="form-control" id="LN_APPLN_DOC_STATUS" name="LN_APPLN_DOC_STATUS" required="">
                                <option value="">------------</option>
                                <option value="NOT_NEEDED">Document(s) are not needed</option>
                                <option value="NOT_GOOD">Document(s) are not okay</option>
                                <option value="GOOD">Document(s) are okay</option>
                              </select><br>
                              
                              <label>Additional Remarks:</label><br>
                              <textarea class="form-control" rows="3" id="LN_APPLN_DOC_RMKS" name="LN_APPLN_DOC_RMKS" required=""></textarea><br>
                              
                              <button type="submit" class="btn btn-primary btn-sm" name="btn_sub_loan_doc">Save</button>
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </form>
                        </div>
                       

                      </div>
                    </div>
                  </div>
                  <?php
                } else if ($LN_APPLN_DOC_STATUS!="") {
                  ?>
                  <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#crt_grp_amend">Amend</button>
                  <div id="crt_grp_amend" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Amend Verification for Loan Documents</h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="eldihdosdfg">
                              <input type="hidden" name="LN_APPLN_NO" id="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">

                              <label>Documents Observation:</label><br>
                              <?php
                              if ($LN_APPLN_DOC_STATUS=="NOT_NEEDED") {
                                ?>
                                <select class="form-control" id="LN_APPLN_DOC_STATUS" name="LN_APPLN_DOC_STATUS" required="">
                                  <option value="">------------</option>
                                  <option value="NOT_NEEDED" selected="">Document(s) are not needed</option>
                                  <option value="NOT_GOOD">Document(s) are not okay</option>
                                  <option value="GOOD">Document(s) are okay</option>
                                </select><br>
                                <?php
                              } else if ($LN_APPLN_DOC_STATUS=="NOT_GOOD") {
                                ?>
                                <select class="form-control" id="LN_APPLN_DOC_STATUS" name="LN_APPLN_DOC_STATUS" required="">
                                  <option value="">------------</option>
                                  <option value="NOT_NEEDED">Document(s) are not needed</option>
                                  <option value="NOT_GOOD" selected="">Document(s) are not okay</option>
                                  <option value="GOOD">Document(s) are okay</option>
                                </select><br>
                                <?php
                              } else if ($LN_APPLN_DOC_STATUS=="GOOD") {
                                ?>
                                <select class="form-control" id="LN_APPLN_DOC_STATUS" name="LN_APPLN_DOC_STATUS" required="">
                                  <option value="">------------</option>
                                  <option value="NOT_NEEDED">Document(s) are not needed</option>
                                  <option value="NOT_GOOD">Document(s) are not okay</option>
                                  <option value="GOOD" selected="">Document(s) are okay</option>
                                </select><br>
                                <?php
                              } else if ($LN_APPLN_DOC_STATUS=="") {
                                ?>
                                <select class="form-control" id="LN_APPLN_DOC_STATUS" name="LN_APPLN_DOC_STATUS" required="">
                                  <option value="">------------</option>
                                  <option value="NOT_NEEDED">Document(s) are not needed</option>
                                  <option value="NOT_GOOD">Document(s) are not okay</option>
                                  <option value="GOOD">Document(s) are okay</option>
                                </select><br>
                                <?php
                              }
                              ?>
                              
                              
                              <label>Additional Remarks:</label><br>
                              <textarea class="form-control" rows="3" id="LN_APPLN_DOC_RMKS" name="LN_APPLN_DOC_RMKS" required=""><?php echo $LN_APPLN_DOC_RMKS; ?></textarea><br>
                              
                              <button type="submit" class="btn btn-primary btn-sm" name="btn_sub_loan_doc_amend">Save</button>
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </form>
                        </div>
                       

                      </div>
                    </div>
                  </div>
                  <?php
                }
                ?>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th>#</th>
                      <th>File Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $LN_APPLN_FILES_LOCATION_CUST = GetSystemParameter("LN_APPLN_FILES_LOCATION_MGT")."/".$_SESSION['ORG_CODE'];
                    $LN_DIR = $LN_APPLN_FILES_LOCATION_CUST."/".$LN_APPLN_NO;
                    $dir = $LN_DIR;

                    $ln_file_list = array();
                    $ln_file_list = FetchLoanApplnFiles($LN_APPLN_NO);
                    for ($i=0; $i < sizeof($ln_file_list); $i++) { 
                      $ln_file = array();
                      $ln_file = $ln_file_list[$i];
                      $F_RECORD_ID = $ln_file['RECORD_ID'];
                      $F_LN_APPLN_NO = $ln_file['LN_APPLN_NO'];
                      $F_CODE = $ln_file['F_CODE'];
                      $F_NAME = $ln_file['F_NAME'];
                      $DATE_UPLOADED = $ln_file['DATE_UPLOADED'];
                      $F_STATUS = $ln_file['F_STATUS'];

                      $file_loc = $dir."/".$F_NAME;
                      $f_id = "f_".($i+1);
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $F_CODE; ?></td>
                        <td>
                          <a href="<?php echo $file_loc; ?>" class="btn btn-info btn-xs">View</a>                            
                        </td>
                      </tr>
                      <?php
                    }

                    ?>
                  </tbody>
                </table>

              </div>

            </div>
          </div>


          <!-- -- -- -- -- -- -- -- -- -- -- LOAN GUARANTORS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN GUARANTORS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-6 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION 04:</strong> Loan Appln Guarantors
                <?php
                if ($LN_APPLN_GRRTR_STATUS=="") {
                  ?>
                  <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#ctfxxx">Verify</button>
                  <div id="ctfxxx" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Verify Loan Guarantors</h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="sdfdsfsd">
                              <input type="hidden" name="LN_APPLN_NO" id="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">

                              <label>Guarantor Observation:</label><br>
                              <select class="form-control" id="LN_APPLN_GRRTR_STATUS" name="LN_APPLN_GRRTR_STATUS" required="">
                                <option value="">------------</option>
                                <option value="NOT_NEEDED">Guarantor(s) are not needed</option>
                                <option value="NOT_GOOD">Guarantor(s) are not okay</option>
                                <option value="GOOD">Guarantor(s) are okay</option>
                              </select><br>
                              
                              <label>Additional Remarks:</label><br>
                              <textarea class="form-control" rows="3" id="LN_APPLN_GRRTR_RMKS" name="LN_APPLN_GRRTR_RMKS" required=""></textarea><br>
                              
                              <button type="submit" class="btn btn-primary btn-sm" name="btn_sub_loan_grrt">Save</button>
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </form>
                        </div>
                       

                      </div>
                    </div>
                  </div>
                  <?php
                } else if ($LN_APPLN_GRRTR_STATUS!="") {
                  ?>
                  <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#ctfxxx">Amend</button>
                  <div id="ctfxxx" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Amend verification for Loan Guarantors</h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="sdfdsfsd">
                              <input type="hidden" name="LN_APPLN_NO" id="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">

                              <label>Guarantor Observation:</label><br>
                              <?php
                              if ($LN_APPLN_GRRTR_STATUS=="NOT_NEEDED") {
                                ?>
                                <select class="form-control" id="LN_APPLN_GRRTR_STATUS" name="LN_APPLN_GRRTR_STATUS" required="">
                                  <option value="">------------</option>
                                  <option value="NOT_NEEDED" selected="">Guarantor(s) are not needed</option>
                                  <option value="NOT_GOOD">Guarantor(s) are not okay</option>
                                  <option value="GOOD">Guarantor(s) are okay</option>
                                </select><br>
                                <?php
                              } else if ($LN_APPLN_GRRTR_STATUS=="NOT_GOOD") {
                                ?>
                                <select class="form-control" id="LN_APPLN_GRRTR_STATUS" name="LN_APPLN_GRRTR_STATUS" required="">
                                  <option value="">------------</option>
                                  <option value="NOT_NEEDED">Guarantor(s) are not needed</option>
                                  <option value="NOT_GOOD" selected="">Guarantor(s) are not okay</option>
                                  <option value="GOOD">Guarantor(s) are okay</option>
                                </select><br>
                                <?php
                              } else if ($LN_APPLN_GRRTR_STATUS=="GOOD") {
                                ?>
                                <select class="form-control" id="LN_APPLN_GRRTR_STATUS" name="LN_APPLN_GRRTR_STATUS" required="">
                                  <option value="">------------</option>
                                  <option value="NOT_NEEDED">Guarantor(s) are not needed</option>
                                  <option value="NOT_GOOD">Guarantor(s) are not okay</option>
                                  <option value="GOOD" selected="">Guarantor(s) are okay</option>
                                </select><br>
                                <?php
                              } else if ($LN_APPLN_GRRTR_STATUS=="") {
                                ?>
                                <select class="form-control" id="LN_APPLN_GRRTR_STATUS" name="LN_APPLN_GRRTR_STATUS" required="">
                                  <option value="">------------</option>
                                  <option value="NOT_NEEDED">Guarantor(s) are not needed</option>
                                  <option value="NOT_GOOD">Guarantor(s) are not okay</option>
                                  <option value="GOOD">Guarantor(s) are okay</option>
                                </select><br>
                                <?php
                              }
                              ?>

                              
                              
                              <label>Additional Remarks:</label><br>
                              <textarea class="form-control" rows="3" id="LN_APPLN_GRRTR_RMKS" name="LN_APPLN_GRRTR_RMKS" required=""><?php echo $LN_APPLN_GRRTR_RMKS; ?></textarea><br>
                              
                              <button type="submit" class="btn btn-primary btn-sm" name="btn_sub_loan_grrt_amend">Save</button>
                              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </form>
                        </div>
                       

                      </div>
                    </div>
                  </div>
                  <?php
                }
                ?>
                
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

                <table class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th>#</th>
                      <th>Name</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      if ($IS_WALK_IN=="YES") {
                        $grrt_list = FetchGuarantorPool_walkin($LN_APPLN_NO);
                        for ($i=0; $i < sizeof($grrt_list); $i++) { 
                          $g = array();
                          $g = $grrt_list[$i];
                          $G_RECORD_ID = $g['RECORD_ID'];
                          $LN_APPLN_NO = $g['LN_APPLN_NO'];
                          $G_CUST_CORE_ID = $g['G_CUST_CORE_ID'];
                          $G_NAME = $g['G_NAME'];
                          $GUARANTORSHIP_STATUS = $g['GUARANTORSHIP_STATUS'];

                          $f_id = "form_".($i+1);
                          ?>
                          <tr valign="top">
                            <td><?php echo ($i+1); ?>. </td>
                            <td><?php echo $G_NAME; ?></td>
                          </tr>
                          <?php
                        }
                      }
                      if ($IS_WALK_IN=="NO") {
                        $grrt_list = array();
                        $grrt_list = FetchLoanApplnGuarantors($LN_APPLN_NO);
                        for ($i=0; $i < sizeof($grrt_list); $i++) { 
                          $g = array();
                          $g = $grrt_list[$i];
                          $G_RECORD_ID = $g['RECORD_ID'];
                          $LN_APPLN_NO = $g['LN_APPLN_NO'];
                          $G_CUST_ID = $g['G_CUST_ID'];
                          $G_NAME = $g['G_NAME'];
                          $G_PHONE = $g['G_PHONE'];
                          $G_EMAIL = $g['G_EMAIL'];
                          $DATE_GENERATED = $g['DATE_GENERATED'];
                          $GUARANTORSHIP_STATUS = $g['GUARANTORSHIP_STATUS'];
                          $RMKS = $g['RMKS'];
                          $USED_FLG = $g['USED_FLG'];
                          $DATE_USED = $g['DATE_USED'];
                          $MIFOS_RESOURCE_ID = $g['MIFOS_RESOURCE_ID'];
                          ?>
                          <tr valign="top">
                            <td><?php echo ($i+1); ?>. </td>
                            <td><?php echo $G_NAME; ?></td>
                          </tr>
                          <?php
                        }
                      }
                    ?>
                  </tbody>
                </table>

              </div>

            </div>
          </div>





        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            <?php echo $COPY_RIGHT_STMT; ?> 
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>



    <?php LoadDefaultJavaScriptConfigurations(); ?>
  
  </body>
</html>
