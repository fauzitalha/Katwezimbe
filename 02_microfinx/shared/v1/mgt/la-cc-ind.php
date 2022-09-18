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



# ... R007: DETERMING LOAN APPLICATION VERIFIER  ..................................................................................#
$USER_DETAILS = array();
$USER_DETAILS = GetUserDetailsFromPortal($VERIF_USER_ID);
$VFD_USER_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
  
$response_msg = FetchUserDetailsFromCore($VFD_USER_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$sys_usr = $response_msg["CORE_RESP"];
$id = $sys_usr["id"];
$CORE_username = $sys_usr["username"];
$firstname = $sys_usr["firstname"];
$lastname = $sys_usr["lastname"];
$email = $sys_usr["email"];

$vfd_full_name = $CORE_username." (".$firstname." ".$lastname.")";


# ... R008: DETERMING CREDIT_COMMITTEE_ID  ..................................................................................#
$CC_COMMITTEE_ID = FetchCreditCommitteeForLoanProduct($LN_PDT_ID);


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

  $next_page = "la-cc-ind?$data_transfer";
  NavigateToNextPage($next_page);
}


# ... F0000002: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "STAFF_USER";
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $EVENT_TYPE = "CREDIT COMMITTEE LOAN APPLN";
  $TAN = GeneratePassKey(8);
  $ENC_TAN = AES256::encrypt($TAN);
  $TAN_GEN_DATE = GetCurrentDateTime();

  # ... UPDATE UN-USED TANS
  $q = "UPDATE txn_tans SET TAN_STATUS='KILLED (UNUSED)' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {
    # ... SQL INSERT
    $q = "INSERT INTO txn_tans(ENTITY_TYPE,ENTITY_ID,EVENT_TYPE,TAN,TAN_GEN_DATE) VALUES('$ENTITY_TYPE','$ENTITY_ID','$EVENT_TYPE','$ENC_TAN','$TAN_GEN_DATE')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP=="EXECUTED") {
      
      $response_msg = FetchUserDetailsFromCore($_SESSION['UPR_USER_CORE_ID'], $MIFOS_CONN_DETAILS);
      $CONN_FLG = $response_msg["CONN_FLG"];
      $CORE_RESP = $response_msg["CORE_RESP"];
      $sys_usr = $response_msg["CORE_RESP"];
      $id = $sys_usr["id"];
      $CORE_username = $sys_usr["username"];
      $firstname = $sys_usr["firstname"];
      $lastname = $sys_usr["lastname"];
      $email = $sys_usr["email"];
      //echo "<pre>".print_r($CORE_RESP,true)."</pre>";
      $fff_name= $firstname." ".$lastname;
      $fff_email = $email;

      # ... DB INSERT
      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "CREDIT_COMMITTEE_TAN";
      $RECIPIENT_EMAILS = $fff_email;
      $EMAIL_MESSAGE = "Dear ".$fff_name."<br>"
                      ."This is your credit committee TAN which is: <b>".$TAN."</b>";
      $EMAIL_ATTACHMENT_PATH = "";
      $RECORD_DATE = GetCurrentDateTime();
      $EMAIL_STATUS = "NN";

      $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
      ExecuteEntityInsert($q);

      # ... Send System Response
      $alert_type = "INFO";
      $alert_msg = "ALERT: TAN has been sent out to your registered email. TAN expires after 5 minutes";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    }
  }
}


# ... F0000003: SUBMIT APPROVAL .....................................................................................#
if (isset($_POST['btn_submit_appln'])) {

  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $CC_COMMITTEE_ID = mysql_real_escape_string(trim($_POST['CC_COMMITTEE_ID']));
  $APPRV_ACTION = mysql_real_escape_string(trim($_POST['APPRV_ACTION']));
  $APPRV_REASON = mysql_real_escape_string(trim($_POST['APPRV_REASON']));
  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));

  # ... 01: Validate Enterred TAN
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $val_results = array();
  $val_results = ValidateTranTAN($ENTITY_ID, $TRAN_TAN);
  $TAN_MSG_CODE = $val_results["TAN_MSG_CODE"];
  $TAN_MSG_MSG = $val_results["TAN_MSG_MSG"];

  if ($TAN_MSG_CODE=="FALSE") {
    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = $TAN_MSG_MSG;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if ($TAN_MSG_CODE=="TRUE") {

    # ... 02: Insert
    $ACTION_TYPE = "APPRV_LOAN_APPLN";
    $APPLN_NO = $LN_APPLN_NO;
    $GRP_ID = $CC_COMMITTEE_ID;
    $GRP_MEMBER_ID = $_SESSION['UPR_USER_ID'];
    $ACTION_TAKEN = $APPRV_ACTION;
    $ACTION_REMARKS = $APPRV_REASON;
    $DATE_ACTION_TAKEN = GetCurrentDateTime();

    $q = "INSERT INTO appln_mgt_group_actions(ACTION_TYPE,APPLN_NO,GRP_ID,GRP_MEMBER_ID,ACTION_TAKEN,ACTION_REMARKS,DATE_ACTION_TAKEN) 
             VALUES('$ACTION_TYPE','$APPLN_NO','$GRP_ID','$GRP_MEMBER_ID','$ACTION_TAKEN','$ACTION_REMARKS','$DATE_ACTION_TAKEN')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    # ... Process Entity System ID (Role ID)
    $id_prefix = "VBX";
    $id_len = 11;
    $id_record_id = $RECORD_ID;
    $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
    $ACTION_ID = $ENTITY_ID;

    # ... Updating the role id
    $q2 = "UPDATE appln_mgt_group_actions SET ACTION_ID='$ACTION_ID' WHERE RECORD_ID='$RECORD_ID'";
    $update_response = ExecuteEntityUpdate($q2);
    if ($update_response=="EXECUTED") {

      # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $ENTITY_ID = $_SESSION['UPR_USER_ID'];
      $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
      ExecuteEntityUpdate($qww);

      # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "LOAN_APPLN";
      $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
      $EVENT = "CC_APPROVE";
      $EVENT_OPERATION = "APPROVE_LOAN_APPLN_BY_CC_MEMBER";
      $EVENT_RELATION = "appln_mgt_group_actions";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = $LN_APPLN_NO."|".$APPRV_ACTION."|".$APPRV_REASON;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: You are $APPRV_ACTION the loan application. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5; URL=la-cc");


    } // ... END..IFF

  } # ... END..IFF..ELSE
}


# ... F0000003: SUBMIT APPROVAL .....................................................................................#
if (isset($_POST['btn_submit_appln_modifcns'])) {

  $ACT_RECORD_ID = mysql_real_escape_string(trim($_POST['ACT_RECORD_ID']));
  $ACTION_ID = mysql_real_escape_string(trim($_POST['ACTION_ID']));
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $ACT_RECORD_ID = mysql_real_escape_string(trim($_POST['ACT_RECORD_ID']));
  $FF_CNT_RETRIED = mysql_real_escape_string(trim($_POST['FF_CNT_RETRIED']));
  $APPRV_ACTION = mysql_real_escape_string(trim($_POST['APPRV_ACTION']));
  $APPRV_REASON = mysql_real_escape_string(trim($_POST['APPRV_REASON']));
  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));

  # ... 01: Validate Enterred TAN
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $val_results = array();
  $val_results = ValidateTranTAN($ENTITY_ID, $TRAN_TAN);
  $TAN_MSG_CODE = $val_results["TAN_MSG_CODE"];
  $TAN_MSG_MSG = $val_results["TAN_MSG_MSG"];

  if ($TAN_MSG_CODE=="FALSE") {
    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = $TAN_MSG_MSG;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if ($TAN_MSG_CODE=="TRUE") {

    $RECORD_ID = $ACT_RECORD_ID;
    $ACTION_TAKEN = $APPRV_ACTION;
    $ACTION_REMARKS = $APPRV_REASON;
    $ACTION_RETRY_FLG = "YY";
    $CNT_RETRIED = ($FF_CNT_RETRIED + 1);
    $DATE_LST_RETRIED = GetCurrentDateTime();

    $q2 = "UPDATE appln_mgt_group_actions 
           SET ACTION_TAKEN='$ACTION_TAKEN'
              ,ACTION_REMARKS='$ACTION_REMARKS'
              ,ACTION_RETRY_FLG='$ACTION_RETRY_FLG'
              ,CNT_RETRIED='$CNT_RETRIED'
              ,DATE_LST_RETRIED='$DATE_LST_RETRIED' 
          WHERE RECORD_ID='$RECORD_ID'";
    $update_response = ExecuteEntityUpdate($q2);
    if ($update_response=="EXECUTED") {

      # ... INSERT RETRY LOG
      $qqqqww = "INSERT INTO appln_mgt_group_actions_retry_log(ACTION_ID,ACTION_TAKEN,ACTION_REMARKS,DATE_OF_RETRY) 
                     VALUES('$ACTION_ID','$ACTION_TAKEN','$ACTION_REMARKS','$DATE_LST_RETRIED')";
      ExecuteEntityInsert($qqqqww);

      # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "LOAN_APPLN";
      $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
      $EVENT = "CC_MODIF_APPROVAL";
      $EVENT_OPERATION = "MODIF_APPRVAL_LOAN_APPLN_BY_CC_MEMBER";
      $EVENT_RELATION = "appln_mgt_group_actions|appln_mgt_group_actions_retry_log";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = $LN_APPLN_NO."|".$APPRV_ACTION."|".$APPRV_REASON;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: You are $APPRV_ACTION the loan application by modification. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5; URL=la-cc");

    }
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
              <a href="la-cc" class="btn btn-dark btn-sm pull-left">Back</a>
              <h2>CC_REF: <?php echo $LN_APPLN_NO; ?></h2>
              <div class="clearfix"></div>
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

                  <table class="table table-bordered" style="font-size: 12px;">
                    <tr><td width="36%" bgcolor="#EEE"><b>Is Loan TopUp?</b></td><td colspan="3"><?php echo $IS_TOP_UP; ?></td></tr>
                    <?php
                    if ($IS_TOP_UP=="YES") {
                      $response_msg = FetchLoanAcctById($TOP_UP_LOAN_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $Loan_Acct_No = $CORE_RESP["accountNo"];
                      $Loan_Product = $CORE_RESP["loanProductName"];
                      $TOP_UP_LOAN_ACCT_NUMBER = $Loan_Acct_No." - ".$Loan_Product;
                      ?>
                      <tr><td bgcolor="#EEE"><b>Loan Account to TopUp</b></td><td colspan="3"><?php echo $TOP_UP_LOAN_ACCT_NUMBER; ?></td></tr>
                      <?php
                    }
                    ?>
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
                  <input type="text" class="form-control" disabled="" value="<?php echo number_format($CORE_SVGS_ACCT_BAL); ?>">
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Customer Bank Acct for funds Transfer:</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $CUST_FIN_INST_ID." (".$CUST_BANK_NAME.")"; ?>">
                </div>

              </div>
            </div>
          </div>  


          <!-- -- -- -- -- -- -- -- -- -- -- LOAN ASSESMENT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN ASSESMENT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
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
                        <button type="submit" class="btn btn-warning btn-xs pull-right" name="btn_val_details">Run Own Assessment of Appln</button>
                      </form>
                    </td>
                    <td>

                      
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
                      $FFTT_VALL = $grp['GRP_NAME'];
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


          <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN VERIFICATION REMARKS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN VERIFICATION REMARKS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION 05:</strong> Verification Remarks
               
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

                <table class="table table-bordered" style="font-size: 12px;">
                  <tr valign="top"><td><b>Summarized Remarks</b></td><td><?php echo $VV["MSG"]; ?></td></tr>
                  <tr><td><b>Detailed Remarks</b></td><td>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <label>Assesment Remarks</label>
                      <textarea class="form-control" disabled=""><?php echo $LN_APPLN_ASSMT_RMKS; ?></textarea>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <label>Loan Documents Remarks</label>
                      <textarea class="form-control" disabled=""><?php echo $LN_APPLN_DOC_RMKS; ?></textarea>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <label>Loan Guarrantor Remarks</label>
                      <textarea class="form-control" disabled=""><?php echo $LN_APPLN_DOC_RMKS; ?></textarea>
                    </div>

                  </td></tr>
                  <tr><td><b>Final Verification Remark</b></td><td><?php echo $VERIF_RMKS; ?></td></tr>
                  <tr><td><b>Verified By</b></td><td><?php echo $vfd_full_name; ?></td></tr>
                  <tr><td><b>Verified On</b></td><td><?php echo $VERIF_DATE; ?></td></tr>
                </table>


              </div>

            </div>
          </div>


          <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN CC VERIFICATION RMKS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN CC VERIFICATION RMKS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-7 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION 06:</strong> Other Committe Member Remarks
               
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

                <table style="font-size: 10px;" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th>#</th>
                      <th>Member</th>
                      <th>Status</th>
                      <th>Remarks</th>
                      <th>Date Taken</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $uu = 0;
                    $grp_member_list = array();
                    $grp_member_list = FetchAppMgtGroupMembers($CC_COMMITTEE_ID);
                    for ($i=0; $i < sizeof($grp_member_list); $i++) { 
                      $grp_membr = array();
                      $grp_membr = $grp_member_list[$i];
                      $GRP_RECORD_ID = $grp_membr['RECORD_ID'];
                      $GRP_GRP_ID = $grp_membr['GRP_ID'];
                      $GRP_GRP_MEMBER_ID = $grp_membr['GRP_MEMBER_ID'];
                      $GRP_ADDED_BY = $grp_membr['ADDED_BY'];
                      $GRP_CREATED_ON = $grp_membr['CREATED_ON'];
                      $GRP_GRP_MEMBER_STATUS = $grp_membr['GRP_MEMBER_STATUS'];

                      # ... FETCH MEMBER NAME
                      $USER_DETAILS = array();
                      $USER_DETAILS = GetUserDetailsFromPortal($GRP_GRP_MEMBER_ID);
                      $GRP_USER_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
                        
                      $response_msg = FetchUserDetailsFromCore($GRP_USER_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $sys_usr = $response_msg["CORE_RESP"];
                      $CORE_username = $sys_usr["username"];
                      $firstname = $sys_usr["firstname"];
                      $lastname = $sys_usr["lastname"];

                      $grp_full_name = $firstname." ".$lastname;

                      if ($GRP_GRP_MEMBER_STATUS!="ACTIVE") {
                        // ... do nothing
                      } else if ($GRP_GRP_MEMBER_STATUS=="ACTIVE"){
                        # ... FETCH MEMBER COMMENT
                        $ACTION_TYPE='APPRV_LOAN_APPLN';
                        $appln_grp_action_details = array();
                        $appln_grp_action_details = FetchApplnGroupActionTakenByIndMember($ACTION_TYPE, $LN_APPLN_NO, $GRP_GRP_ID, $GRP_GRP_MEMBER_ID);

                        $AA_RECORD_ID = "";
                        $AA_ACTION_ID = "";
                        $AA_ACTION_TYPE = "";
                        $AA_APPLN_NO = "";
                        $AA_GRP_ID = "";
                        $AA_GRP_MEMBER_ID = "";
                        $AA_ACTION_TAKEN = "";
                        $AA_ACTION_REMARKS = "";
                        $AA_DATE_ACTION_TAKEN = "";
                        $AA_ACTION_RETRY_FLG = "";
                        $AA_CNT_RETRIED = "";
                        $AA_DATE_LST_RETRIED = "";
                        $DDATE  = "";

                        if (isset($appln_grp_action_details['RECORD_ID'])) {
                          $AA_RECORD_ID = $appln_grp_action_details['RECORD_ID'];
                          $AA_ACTION_ID = $appln_grp_action_details['ACTION_ID'];
                          $AA_ACTION_TYPE = $appln_grp_action_details['ACTION_TYPE'];
                          $AA_APPLN_NO = $appln_grp_action_details['APPLN_NO'];
                          $AA_GRP_ID = $appln_grp_action_details['GRP_ID'];
                          $AA_GRP_MEMBER_ID = $appln_grp_action_details['GRP_MEMBER_ID'];
                          $AA_ACTION_TAKEN = $appln_grp_action_details['ACTION_TAKEN'];
                          $AA_ACTION_REMARKS = $appln_grp_action_details['ACTION_REMARKS'];
                          $AA_DATE_ACTION_TAKEN = $appln_grp_action_details['DATE_ACTION_TAKEN'];
                          $AA_ACTION_RETRY_FLG = $appln_grp_action_details['ACTION_RETRY_FLG'];
                          $AA_CNT_RETRIED = $appln_grp_action_details['CNT_RETRIED'];
                          $AA_DATE_LST_RETRIED = $appln_grp_action_details['DATE_LST_RETRIED'];
                          $DDATE = ($AA_DATE_LST_RETRIED=="")? $AA_DATE_ACTION_TAKEN : $AA_DATE_LST_RETRIED;
                        } 
                        
                        ?>
                         <tr valign="top">
                          <td><?php echo ($uu+1); ?>. </td>
                          <td><?php echo $grp_full_name; ?></td>
                          <td><?php echo $AA_ACTION_TAKEN; ?></td>
                          <td><?php echo $AA_ACTION_REMARKS; ?></td>
                          <td><?php echo $DDATE; ?></td>
                        </tr>
                        <?php

                        $uu++;
                      }
                     
                    }
                                  
                    ?>
                  </tbody>
                </table>

              </div>

            </div>
          </div>


          <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN CC VERIFICATION POINT -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN CC VERIFICATION POINT -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-5 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION 07:</strong> Approve or Reject Loan application
                <table>
                  <tr>
                    <td>
                       <form method="post" id="asdfh12345fdasjk">
                          <button type="submit" class="btn btn-warning btn-xs pull-left" name="btn_gen_tan">Generate Auth TAN</button>
                        </form>
                    </td>
                    <td>
                      <?php
                  $GRP_MEMBER_ID = $_SESSION['UPR_USER_ID'];
                  $Q_CCNNTT ="SELECT count(*) as RTN_VALUE 
                              FROM appln_mgt_group_actions 
                              WHERE ACTION_TYPE='APPRV_LOAN_APPLN'
                                AND APPLN_NO='$LN_APPLN_NO'
                                AND GRP_ID='$CC_COMMITTEE_ID'
                                AND GRP_MEMBER_ID='$GRP_MEMBER_ID'";
                  $CNNT_CC = ReturnOneEntryFromDB($Q_CCNNTT);
                  if ($CNNT_CC>0) {
                    $ACTION_TYPE='APPRV_LOAN_APPLN';
                    $APPLN_NO=$LN_APPLN_NO;
                    $GRP_ID=$CC_COMMITTEE_ID;
                    $GRP_MEMBER_ID=$GRP_MEMBER_ID;
                    $appln_grp_action_details = array();
                    $appln_grp_action_details = FetchApplnGroupActionTakenByIndMember($ACTION_TYPE, $APPLN_NO, $GRP_ID, $GRP_MEMBER_ID);
                    $RECORD_ID = $appln_grp_action_details['RECORD_ID'];
                    $ACTION_ID = $appln_grp_action_details['ACTION_ID'];
                    $ACTION_TYPE = $appln_grp_action_details['ACTION_TYPE'];
                    $APPLN_NO = $appln_grp_action_details['APPLN_NO'];
                    $GRP_ID = $appln_grp_action_details['GRP_ID'];
                    $GRP_MEMBER_ID = $appln_grp_action_details['GRP_MEMBER_ID'];
                    $ACTION_TAKEN = $appln_grp_action_details['ACTION_TAKEN'];
                    $ACTION_REMARKS = $appln_grp_action_details['ACTION_REMARKS'];
                    $DATE_ACTION_TAKEN = $appln_grp_action_details['DATE_ACTION_TAKEN'];
                    $ACTION_RETRY_FLG = $appln_grp_action_details['ACTION_RETRY_FLG'];
                    $CNT_RETRIED = $appln_grp_action_details['CNT_RETRIED'];
                    $DATE_LST_RETRIED = $appln_grp_action_details['DATE_LST_RETRIED'];

                    $CNT_RRRTTTDD = ($CNT_RETRIED=="")? 0 : $CNT_RETRIED;
                    ?>
                    <button type="button" class="btn btn-primary btn-xs pull-left" data-toggle="modal" data-target="#verif_gd">Change Action</button>
                    <div id="verif_gd" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                      <div class="modal-dialog modal-mm">
                        <div class="modal-content">

                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel2">Change Action on Approve/Reject</h4>
                          </div>
                          <div class="modal-body">
                            <form method="post" id="ggguigiuf">
                              <input type="hidden" id="ACT_RECORD_ID" name="ACT_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                              <input type="hidden" id="ACTION_ID" name="ACTION_ID" value="<?php echo $ACTION_ID; ?>">
                              <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                              <input type="hidden" id="FF_CNT_RETRIED" name="FF_CNT_RETRIED" value="<?php echo $CNT_RETRIED; ?>">
                              
                                <label>Select Approval Action:</label>
                                <?php
                                if ($ACTION_TAKEN=="APPROVE") {
                                  ?>
                                  <select id="APPRV_ACTION" name="APPRV_ACTION" class="form-control" required="">
                                    <option value="">-------</option>
                                    <option value="APPROVE" selected="">APPROVE</option>
                                    <option value="REJECT">REJECT</option>
                                  </select>
                                  <?php
                                } else if ($ACTION_TAKEN=="REJECT") {
                                  ?>
                                  <select id="APPRV_ACTION" name="APPRV_ACTION" class="form-control" required="">
                                    <option value="">-------</option>
                                    <option value="APPROVE">APPROVE</option>
                                    <option value="REJECT" selected="">REJECT</option>
                                  </select>
                                  <?php
                                } else {
                                  ?>
                                  <select id="APPRV_ACTION" name="APPRV_ACTION" class="form-control" required="">
                                    <option value="">-------</option>
                                    <option value="APPROVE">APPROVE</option>
                                    <option value="REJECT">REJECT</option>
                                  </select>
                                  <?php
                                }
                                ?>
                                <br>
                                <label>Approval Remarks</label>
                                <textarea class="form-control" rows="3" name="APPRV_REASON" id="APPRV_REASON" required=""><?php echo $ACTION_REMARKS; ?></textarea>
                                <br>
                                <label>Enter Transaction TAN:</label> 
                                <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control" required="">
                                <br>
                                <button type="submit" class="btn btn-success" name="btn_submit_appln_modifcns">Submit Modifications</button>
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
              </div>
              <div class="x_content">

                <?php
                $GRP_MEMBER_ID = $_SESSION['UPR_USER_ID'];
                $Q_CCNNTT ="SELECT count(*) as RTN_VALUE 
                            FROM appln_mgt_group_actions 
                            WHERE ACTION_TYPE='APPRV_LOAN_APPLN'
                              AND APPLN_NO='$LN_APPLN_NO'
                              AND GRP_ID='$CC_COMMITTEE_ID'
                              AND GRP_MEMBER_ID='$GRP_MEMBER_ID'";
                $CNNT_CC = ReturnOneEntryFromDB($Q_CCNNTT);
                if ($CNNT_CC>0) {
                  $ACTION_TYPE='APPRV_LOAN_APPLN';
                  $APPLN_NO=$LN_APPLN_NO;
                  $GRP_ID=$CC_COMMITTEE_ID;
                  $GRP_MEMBER_ID=$GRP_MEMBER_ID;
                  $appln_grp_action_details = array();
                  $appln_grp_action_details = FetchApplnGroupActionTakenByIndMember($ACTION_TYPE, $APPLN_NO, $GRP_ID, $GRP_MEMBER_ID);
                  $RECORD_ID = $appln_grp_action_details['RECORD_ID'];
                  $ACTION_ID = $appln_grp_action_details['ACTION_ID'];
                  $ACTION_TYPE = $appln_grp_action_details['ACTION_TYPE'];
                  $APPLN_NO = $appln_grp_action_details['APPLN_NO'];
                  $GRP_ID = $appln_grp_action_details['GRP_ID'];
                  $GRP_MEMBER_ID = $appln_grp_action_details['GRP_MEMBER_ID'];
                  $ACTION_TAKEN = $appln_grp_action_details['ACTION_TAKEN'];
                  $ACTION_REMARKS = $appln_grp_action_details['ACTION_REMARKS'];
                  $DATE_ACTION_TAKEN = $appln_grp_action_details['DATE_ACTION_TAKEN'];
                  $ACTION_RETRY_FLG = $appln_grp_action_details['ACTION_RETRY_FLG'];
                  $CNT_RETRIED = $appln_grp_action_details['CNT_RETRIED'];
                  $DATE_LST_RETRIED = $appln_grp_action_details['DATE_LST_RETRIED'];


                  ?>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <label>Selected Approval Action:</label>
                      <input type="text" class="form-control" disabled="" value="<?php echo $ACTION_TAKEN; ?>">
                    </div>


                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <label>Approval Remarks</label>
                      <textarea class="form-control" rows="3" disabled=""><?php echo $ACTION_REMARKS; ?></textarea>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <label>Approval Date:</label>
                      <input type="text" class="form-control" disabled="" value="<?php echo $DATE_ACTION_TAKEN; ?>">
                    </div>
                  <?php
                } else {
                  ?>
                  <form method="post" id="dmsjj">
                    <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                    <input type="hidden" id="CC_COMMITTEE_ID" name="CC_COMMITTEE_ID" value="<?php echo $CC_COMMITTEE_ID; ?>">
                    
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <label>Select Approval Action:</label>
                      <select id="APPRV_ACTION" name="APPRV_ACTION" class="form-control" required="">
                        <option value="">-------</option>
                        <option value="APPROVE">APPROVE</option>
                        <option value="REJECT">REJECT</option>
                      </select>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <label>Approval Remarks</label>
                      <textarea class="form-control" rows="3" name="APPRV_REASON" id="APPRV_REASON" required=""></textarea>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <label>Enter Transaction TAN:</label> 
                      <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <button type="submit" class="btn btn-success" name="btn_submit_appln">Submit Approval Details</button>
                    </div>
                  </form>
                  <?php
                }
                ?>
                
                

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
