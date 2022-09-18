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
$CHANNEL = $la['CHANNEL'];
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
$FLG_OPEN_LOAN_ACCT = $la['FLG_OPEN_LOAN_ACCT'];
$FLG_APPRV_LOAN_ACCT = $la['FLG_APPRV_LOAN_ACCT'];
$FLG_UPLOAD_LOAN_DOCS = $la['FLG_UPLOAD_LOAN_DOCS'];
$FLG_ADD_GRRTRS = $la['FLG_ADD_GRRTRS'];
$FLG_DISB_TO_SVNGS = $la['FLG_DISB_TO_SVNGS'];
$DISB_DATE = $la['DISB_DATE'];
$DISB_USER_ID = $la['DISB_USER_ID'];
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
$CORE_SVGS_ACCT_CRNCY = $CORE_RESP["currency"]["code"];

# ... R005: LOAD CUSTOMER DETAILS .....................................................................................#
$CUST_CORE_ID = "";
$CUST_EMAIL = "";
$CUST_PHONE = "";

if ($IS_WALK_IN == "YES") {
  $data_details = explode('-', $CUST_ID);
  $CUST_CORE_ID = $data_details[1];
}

if ($IS_WALK_IN == "NO") {
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
$LN_APPLN_CHECKLIST = array("PRM_01", "PRM_02", "PRM_03", "PRM_04", "PRM_07", "PRM_08", "PRM_09", "PRM_10", "PRM_11", "", "");
$_SESSION["LN_APPLN_CHECKLIST"] = $LN_APPLN_CHECKLIST;


# ... R006: DETERMING SYSTEM COURSE OF ACTION .....................................................................................#
$VV = array();
if (($LN_APPLN_ASSMT_STATUS == "") || ($LN_APPLN_DOC_STATUS == "") || ($LN_APPLN_GRRTR_STATUS == "")) {
  $VV["DISP_FLG"] = "DONT_DISPLAY";
} else {

  $VV["DISP_FLG"] = "DISPLAY";

  # ... Button type
  if ((($LN_APPLN_ASSMT_STATUS == "GOOD") || ($LN_APPLN_ASSMT_STATUS == "NOT_NEEDED")) &&
    (($LN_APPLN_DOC_STATUS == "GOOD") || ($LN_APPLN_DOC_STATUS == "NOT_NEEDED")) &&
    (($LN_APPLN_GRRTR_STATUS == "GOOD") || ($LN_APPLN_GRRTR_STATUS == "NOT_NEEDED"))
  ) {
    $VV["BTN_TYPE"] = "GOOD_BUTTON";
  } else {
    $VV["BTN_TYPE"] = "BAD_BUTTON";
  }


  # ... Message Type
  $VV["MSG"] = "";

  # ... Appln Assessment
  if ($LN_APPLN_ASSMT_STATUS == "NOT_NEEDED") {
    $VV["MSG"] .= "<br>Application Assessment ---------------------------- <span style='color: black; font-weight: bolder;'>[NOT_NEEDED]</span>";
  } else if ($LN_APPLN_ASSMT_STATUS == "NOT_GOOD") {
    $VV["MSG"] .= "<br>Application Assessment ---------------------------- <span style='color: red; font-weight: bolder;'>[NOT_OKAY]</span>";
  } else if ($LN_APPLN_ASSMT_STATUS == "GOOD") {
    $VV["MSG"] .= "<br>Application Assessment ---------------------------- <span style='color: green; font-weight: bolder;'>[OKAY]</span>";
  }

  # ... Loan Doc Assessment
  if ($LN_APPLN_DOC_STATUS == "NOT_NEEDED") {
    $VV["MSG"] .= "<br>Loan Documents ------------------------------------ <span style='color: black; font-weight: bolder;'>[NOT_NEEDED]</span>";
  } else if ($LN_APPLN_DOC_STATUS == "NOT_GOOD") {
    $VV["MSG"] .= "<br>Loan Documents ------------------------------------ <span style='color: red; font-weight: bolder;'>[NOT_OKAY]</span>";
  } else if ($LN_APPLN_DOC_STATUS == "GOOD") {
    $VV["MSG"] .= "<br>Loan Documents ------------------------------------ <span style='color: green; font-weight: bolder;'>[OKAY]</span>";
  }

  # ... Loan Guarrantor Assessment
  if ($LN_APPLN_GRRTR_STATUS == "NOT_NEEDED") {
    $VV["MSG"] .= "<br>Loan Guarrantor Assessment ------------------------ <span style='color: black; font-weight: bolder;'>[NOT_NEEDED]</span>";
  } else if ($LN_APPLN_GRRTR_STATUS == "NOT_GOOD") {
    $VV["MSG"] .= "<br>Loan Guarrantor Assessment ------------------------ <span style='color: red; font-weight: bolder;'>[NOT_OKAY]</span>";
  } else if ($LN_APPLN_GRRTR_STATUS == "GOOD") {
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

$vfd_full_name = $CORE_username . " (" . $firstname . " " . $lastname . ")";


# ... R008: DETERMING CREDIT_COMMITTEE_ID  ..................................................................................#
$CC_COMMITTEE_ID = FetchCreditCommitteeForLoanProduct($LN_PDT_ID);


# ... F0000001: CREATE LOAN ACCT ..............................................................................................#
if (isset($_POST['btn_create_loan_acct'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));

  # ... 01: Fetch Loan Appln Details
  $la = array();
  $la =  FetchLoanApplnsById($LN_APPLN_NO);
  $CUST_ID = $la['CUST_ID'];
  $LN_PDT_ID = $la['LN_PDT_ID'];
  $RQSTD_AMT = $la['RQSTD_AMT'];
  $RQSTD_RPYMT_PRD = $la['RQSTD_RPYMT_PRD'];
  $CORE_SVGS_ACCT_ID = $la['CORE_SVGS_ACCT_ID'];

  # ... 02: Fetch Cust Core Message
  $CUST_CORE_ID = "";
  $CUST_EMAIL = "";
  $CUST_PHONE = "";

  if ($IS_WALK_IN == "YES") {
    $data_details = explode('-', $CUST_ID);
    $CUST_CORE_ID = $data_details[1];
  }

  if ($IS_WALK_IN == "NO") {
    $cstmr = array();
    $cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
    $CUST_ID = $cstmr['CUST_ID'];
    $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
    $CUST_EMAIL = $cstmr['CUST_EMAIL'];
    $CUST_PHONE = $cstmr['CUST_PHONE'];
  }

  // ... 02.02: check if savings account number is a GROUP SAVINGS ACCT or INDIVIDUAL SAVINGS account
  $identifier_LOAN_TYPE_CAT = "";
  $identifier_CORE_KEY = "";
  $identifier_CORE_ID = "";
  
  $response_msg_33 = GetCustSavingsAccountsGroup($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
  $CONN_FLG_33 = $response_msg_33["CONN_FLG"];
  $CORE_RESP_33 = $response_msg_33["CORE_RESP"];
  $ACCTS_DATA_33 = array();
  $ACCTS_DATA_33 = $CORE_RESP_33["data"];

  if(sizeof($ACCTS_DATA_33)>0)
  {
    for ($m = 0; $m < sizeof($ACCTS_DATA_33); $m++) {

      $row = $ACCTS_DATA_33[$m]["row"];
      $svgs_id = $row[0];
      $svgs_account_no = $row[1];
      $svgs_crncy_code = $row[2];
      $client_id = $row[3];
      $svgs_product_id = $row[4];
      $svgs_product_name = $row[5];
      $svgs_product_shortname = $row[6];
      $group_id =  $row[7];
      $status =  $row[8];

      if($svgs_id==$CORE_SVGS_ACCT_ID){
        $identifier_LOAN_TYPE_CAT = "group";
        $identifier_CORE_KEY = "groupId";
        $identifier_CORE_ID = $group_id;

        break;
      }

    }//..end..loop

    if($identifier_LOAN_TYPE_CAT=="")
    {
      $identifier_LOAN_TYPE_CAT = "individual";
      $identifier_CORE_KEY = "clientId";
      $identifier_CORE_ID = $CUST_CORE_ID;
    }//...end..iff

  }
  else
  {
    $identifier_LOAN_TYPE_CAT = "individual";
    $identifier_CORE_KEY = "clientId";
    $identifier_CORE_ID = $CUST_CORE_ID;
  }//...end


  # ... 03: Fetch Loan Product details from Core
  $loan_product = array();
  $response_msg = FetchLoanProductDetailsById($LN_PDT_ID, $MIFOS_CONN_DETAILS);
  //echo "<pre>".print_r($response_msg,true)."</pre>";

  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $loan_product = $response_msg["CORE_RESP"];
  $repayment_every = $loan_product["repayment_every"];
  $repayment_frequency_type_id = $loan_product["repayment_frequency_type_id"];
  $interest_rate = $loan_product["default_interest_rate_per_period"];
  $amortization_type_id = $loan_product["amortization_type_id"];
  $interest_type_id = $loan_product["interest_type_id"];
  $interest_calculation_period_type_id = $loan_product["interest_calculation_period_type_id"];
  $transaction_processing_strategy_id = $loan_product["transaction_processing_strategy_id"];
  $Submission_Date =  date('d F Y', strtotime(date("ymd", time())));
  $expected_disbursement_date =  date('d F Y', strtotime(date("ymd", time())));


  # ... 03: Build Core Request Message
  $CORE_RQST_MSG = BuildCreateLoanAcctRequestMessage(
    $identifier_LOAN_TYPE_CAT,
    $identifier_CORE_KEY,
    $identifier_CORE_ID,
    $LN_PDT_ID,
    $RQSTD_AMT,
    $RQSTD_RPYMT_PRD,
    $repayment_every,
    $repayment_frequency_type_id,
    $interest_rate,
    $amortization_type_id,
    $interest_type_id,
    $interest_calculation_period_type_id,
    $transaction_processing_strategy_id,
    $expected_disbursement_date,
    $Submission_Date,
    $CORE_SVGS_ACCT_ID
  );

  //echo "<pre>".print_r($CORE_RQST_MSG,true)."</pre>";
  

  $response_msg = CreateLoanApplication($CORE_RQST_MSG, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];

  //echo "<pre>".print_r($CORE_RESP,true)."</pre>";
  //die();

  # ... 01: Track Connection to Core
  if ($CONN_FLG == "NOT_CONNECTED") {
    $alert_type = "ERROR";
    $alert_msg = "NO CONNECTION TO CORE.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else {

    if (!isset($CORE_RESP[$identifier_CORE_KEY])) {

      //echo "<pre>".print_r($CORE_RESP,true)."</pre>";

      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } 
    else if (isset($CORE_RESP[$identifier_CORE_KEY])) {
      /*
      Array
        (
            [officeId] => 1
            [clientId] => 12
            [loanId] => 6
            [resourceId] => 6
        ) 
      Array
        (
            [officeId] => 1
            [groupId] => 12
            [loanId] => 6
            [resourceId] => 6
        ) 
      */
      $officeId = $CORE_RESP["officeId"];
      $ownerId = $CORE_RESP[$identifier_CORE_KEY];
      $loanId = $CORE_RESP["loanId"];
      $resourceId = $CORE_RESP["resourceId"];

      # ... Approve the Opened Account
      $LOAN_ACCT_CORE_ID = $loanId;
      $approvedOnDate =  date('d F Y', strtotime(date("ymd", time())));
      $expected_disbursement_date =  date('d F Y', strtotime(date("ymd", time())));
      $NOTE = "LOAN_APPRVL FOR REF: " . $LN_APPLN_NO;
      $CORE_RQST_MSG_2 = BuildApproveLoanAcctRequestMessage($approvedOnDate, $expected_disbursement_date, $NOTE);
      $response_msg = ApproveLoanApplication($LOAN_ACCT_CORE_ID, $CORE_RQST_MSG_2, $MIFOS_CONN_DETAILS);
      $CONN_FLG = $response_msg["CONN_FLG"];
      $CORE_RESP = $response_msg["CORE_RESP"];
      if (!isset($CORE_RESP[$identifier_CORE_KEY])) {
        $alert_type = "ERROR";
        $alert_msg = $CORE_RESP["defaultUserMessage"];
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      } 
      else if (isset($CORE_RESP[$identifier_CORE_KEY])) {
        $FLG_OPEN_LOAN_ACCT = "YY";
        $FLG_OPEN_LOAN_ACCT_USER_ID = $_SESSION['UPR_USER_ID'];
        $CORE_LOAN_ACCT_ID = $loanId;
        $q = "UPDATE loan_applns 
              SET FLG_OPEN_LOAN_ACCT='$FLG_OPEN_LOAN_ACCT'
                 ,FLG_OPEN_LOAN_ACCT_USER_ID='$FLG_OPEN_LOAN_ACCT_USER_ID'
                 ,CORE_LOAN_ACCT_ID='$CORE_LOAN_ACCT_ID' 
              WHERE LN_APPLN_NO='$LN_APPLN_NO'";

        $update_response = ExecuteEntityUpdate($q);
        if ($update_response == "EXECUTED") {

          # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
          $AUDIT_DATE = GetCurrentDateTime();
          $ENTITY_TYPE = "LOAN_APPLN";
          $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
          $EVENT = "OPEN_LOAN_ACCT";
          $EVENT_OPERATION = "OPEN_LOAN_ACCT_FOR_LOAN_APPLN";
          $EVENT_RELATION = "loan_applns";
          $EVENT_RELATION_NO = $LN_APPLN_NO;
          $OTHER_DETAILS = "CORE_LOAN_ACCT_ID: " . $CORE_LOAN_ACCT_ID;
          $INVOKER_ID = $_SESSION['UPR_USER_ID'];
          LogSystemEvent(
            $AUDIT_DATE,
            $ENTITY_TYPE,
            $ENTITY_ID_AFFECTED,
            $EVENT,
            $EVENT_OPERATION,
            $EVENT_RELATION,
            $EVENT_RELATION_NO,
            $OTHER_DETAILS,
            $INVOKER_ID
          );


          $alert_type = "SUCCESS";
          $alert_msg = "MESSAGE: Loan account opened successfully. Refreshing in 5 seconds.";
          $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
          header("Refresh:5;");
        }
      }
    }
  }
}

# ... F0000002: UPLOAD PENDING DOCUMENTS ......................................................................................#
if (isset($_POST['btn_loan_doc'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $CORE_LOAN_ACCT_ID = mysql_real_escape_string(trim($_POST['CORE_LOAN_ACCT_ID']));

  # ... 01: Get Loan Documents
  $FILE_CNT = 0;
  $LN_APPLN_FILES_LOCATION_CUST = GetSystemParameter("LN_APPLN_FILES_BASEPATH_CUST") . "/" . $_SESSION['ORG_CODE'];
  $LN_DIR = $LN_APPLN_FILES_LOCATION_CUST . "/" . $LN_APPLN_NO;
  $dir = $LN_DIR;

  $ln_file_list = array();
  $ln_file_list = FetchLoanApplnFiles($LN_APPLN_NO);
  for ($i = 0; $i < sizeof($ln_file_list); $i++) {
    $ln_file = array();
    $ln_file = $ln_file_list[$i];
    $F_RECORD_ID = $ln_file['RECORD_ID'];
    $F_LN_APPLN_NO = $ln_file['LN_APPLN_NO'];
    $F_CODE = $ln_file['F_CODE'];
    $F_NAME = $ln_file['F_NAME'];
    $DATE_UPLOADED = $ln_file['DATE_UPLOADED'];
    $F_STATUS = $ln_file['F_STATUS'];

    $file_loc = $dir . "/" . $F_NAME;

    # ... Build Message Request
    $file_path = $file_loc;
    $file_type = mime_content_type($file_path);
    $file_name = $F_NAME;
    $description = $F_NAME;
    $DOC_RQST_MSG = BuildDocumentRqstMsg($file_path, $file_type, $file_name, $description);

    # ... Upload file to Core
    $LOAN_ACCT_ID = $CORE_LOAN_ACCT_ID;
    $doc_data = $DOC_RQST_MSG;
    $response_msg = UploadLoanDocumentToCore($LOAN_ACCT_ID, $doc_data, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];


    if (!isset($CORE_RESP["resourceId"])) {
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      break;
    } else if (isset($CORE_RESP["resourceId"])) {
      $FILE_CNT++;
    }
  }

  # ... 02: Update Database
  if ($FILE_CNT > 0) {
    $FLG_UPLOAD_LOAN_DOCS = "YY";
    $FLG_UPLOAD_LOAN_DOCS_USER_ID = $_SESSION['UPR_USER_ID'];
    $q = "UPDATE loan_applns 
          SET FLG_UPLOAD_LOAN_DOCS='$FLG_UPLOAD_LOAN_DOCS'
             ,FLG_UPLOAD_LOAN_DOCS_USER_ID='$FLG_UPLOAD_LOAN_DOCS_USER_ID'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";

    $update_response = ExecuteEntityUpdate($q);
    if ($update_response == "EXECUTED") {

      # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "LOAN_APPLN";
      $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
      $EVENT = "UPLOAD_LOAN_DOCS";
      $EVENT_OPERATION = "UPLOAD_DOCUMENTS_FOR_LOAN_APPLN";
      $EVENT_RELATION = "loan_applns";
      $EVENT_RELATION_NO = $LN_APPLN_NO;
      $OTHER_DETAILS = $FILE_CNT . " file(s) uploaded";
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent(
        $AUDIT_DATE,
        $ENTITY_TYPE,
        $ENTITY_ID_AFFECTED,
        $EVENT,
        $EVENT_OPERATION,
        $EVENT_RELATION,
        $EVENT_RELATION_NO,
        $OTHER_DETAILS,
        $INVOKER_ID
      );


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: $FILE_CNT file(s) have been uploaded to core. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5;");
    }
  }
}

# ... F0000003: UPLOAD LOAN GUARRANTORS ......................................................................................#
if (isset($_POST['btn_add_grrtr'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $CORE_LOAN_ACCT_ID = mysql_real_escape_string(trim($_POST['CORE_LOAN_ACCT_ID']));
  $IS_WALK_IN = mysql_real_escape_string(trim($_POST['IS_WALK_IN']));

  # ...01: Get Loan Guarantors
  $GRRT_CNT = 0;
  $grrt_list = array();
  if ($IS_WALK_IN == "YES") {
    $grrt_list = FetchGuarantorPool_walkin($LN_APPLN_NO);
  }
  if ($IS_WALK_IN == "NO") {
    $grrt_list = FetchLoanApplnGuarantors($LN_APPLN_NO);
  }

  for ($i = 0; $i < sizeof($grrt_list); $i++) {
    $g = array();
    $g = $grrt_list[$i];
    $G_NAME = $g['G_NAME'];

    $names = array();
    $names = explode(' ', $G_NAME);
    $firstname = $names[0];
    $lastname = $names[1];
    $GRRTR_RQST_MSG = BuildCreateGrrtrRequestMessage($firstname, $lastname);

    $LOAN_ACCT_ID = $CORE_LOAN_ACCT_ID;
    $GRRT_DATA = $GRRTR_RQST_MSG;
    $response_msg = UploadLoanGuarantorsToCore($LOAN_ACCT_ID, $GRRT_DATA, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];


    if (!isset($CORE_RESP["resourceId"])) {
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      break;
    } else if (isset($CORE_RESP["resourceId"])) {
      $GRRT_CNT++;
    }
  } // ... END..LOOP


  # ... 02: Update Database
  if ($GRRT_CNT > 0) {
    $FLG_ADD_GRRTRS = "YY";
    $FLG_ADD_GRRTRS_USER_ID = $_SESSION['UPR_USER_ID'];
    $q = "UPDATE loan_applns 
          SET FLG_ADD_GRRTRS='$FLG_ADD_GRRTRS'
             ,FLG_ADD_GRRTRS_USER_ID='$FLG_ADD_GRRTRS_USER_ID'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";

    $update_response = ExecuteEntityUpdate($q);
    if ($update_response == "EXECUTED") {

      # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "LOAN_APPLN";
      $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
      $EVENT = "ATTACH_LOAN_GUARANTORS";
      $EVENT_OPERATION = "ATTACH_LOAN_GUARANTORS_FOR_LOAN_APPLN";
      $EVENT_RELATION = "loan_applns";
      $EVENT_RELATION_NO = $LN_APPLN_NO;
      $OTHER_DETAILS = $GRRT_CNT . " guarantors(s) uploaded";
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent(
        $AUDIT_DATE,
        $ENTITY_TYPE,
        $ENTITY_ID_AFFECTED,
        $EVENT,
        $EVENT_OPERATION,
        $EVENT_RELATION,
        $EVENT_RELATION_NO,
        $OTHER_DETAILS,
        $INVOKER_ID
      );


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: $GRRT_CNT guarantor(s) have been attached to loan account. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5;");
    }
  }
}

# ... F0000004: DISBURSE LOAN FUNDS ......................................................................................#
if (isset($_POST['btn_disb_loan_acct'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $CORE_LOAN_ACCT_ID = mysql_real_escape_string(trim($_POST['CORE_LOAN_ACCT_ID']));
  $APPROVED_AMT = mysql_real_escape_string(trim($_POST['APPROVED_AMT']));

  $transactionAmount = $APPROVED_AMT;
  $actualDisbursementDate = date('d F Y', strtotime(date("ymd", time())));
  $NOTE = "DISBURSE TO " . $LN_APPLN_NO;
  $RQST_MSG = BuildDisburseToSavingsAcctRequestMessage($transactionAmount, $actualDisbursementDate, $NOTE);

  $response_msg = DisburseLoanToSvngsAcct($CORE_LOAN_ACCT_ID, $RQST_MSG, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];

  if (!isset($CORE_RESP["resourceId"])) {
    $alert_type = "ERROR";
    $alert_msg = $CORE_RESP["defaultUserMessage"];
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if (isset($CORE_RESP["resourceId"])) {
    $FLG_DISB_TO_SVNGS = "YY";
    $FLG_DISB_TO_SVNGS_USER_ID = $_SESSION['UPR_USER_ID'];
    $DISB_DATE = GetCurrentDateTime();
    $DISB_USER_ID = $_SESSION['UPR_USER_ID'];
    $CORE_RESOURCE_ID = $CORE_RESP["resourceId"];
    $LN_APPLN_STATUS = "APPLN_DISBURSED";
    $q = "UPDATE loan_applns 
          SET FLG_DISB_TO_SVNGS='$FLG_DISB_TO_SVNGS'
             ,FLG_DISB_TO_SVNGS_USER_ID='$FLG_DISB_TO_SVNGS_USER_ID'
             ,DISB_DATE='$DISB_DATE'
             ,DISB_USER_ID='$DISB_USER_ID'
             ,CORE_RESOURCE_ID='$CORE_RESOURCE_ID'
             ,LN_APPLN_STATUS='$LN_APPLN_STATUS'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";

    $update_response = ExecuteEntityUpdate($q);
    if ($update_response == "EXECUTED") {

      # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "LOAN_APPLN";
      $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
      $EVENT = "DISBURSE_TO_SAVINGS_ACCT";
      $EVENT_OPERATION = "DISBURSE_TO_SAVINGS_ACCT_FOR_LOAN_APPLN";
      $EVENT_RELATION = "loan_applns";
      $EVENT_RELATION_NO = $LN_APPLN_NO;
      $OTHER_DETAILS = "DISBURSEMENT_NO: " . $CORE_RESOURCE_ID;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent(
        $AUDIT_DATE,
        $ENTITY_TYPE,
        $ENTITY_ID_AFFECTED,
        $EVENT,
        $EVENT_OPERATION,
        $EVENT_RELATION,
        $EVENT_RELATION_NO,
        $OTHER_DETAILS,
        $INVOKER_ID
      );


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: Loan application funds have beenn disbursed to client savings account. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5;");
    }
  }
}

# ... F0000005: DISBURSE LOAN FUNDS ......................................................................................#
if (isset($_POST['btn_disb_loan_acct_02'])) {
  $APPLN_CHANNEL = mysql_real_escape_string(trim($_POST['APPLN_CHANNEL']));
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $CORE_LOAN_ACCT_ID = mysql_real_escape_string(trim($_POST['CORE_LOAN_ACCT_ID']));
  $APPROVED_AMT = mysql_real_escape_string(trim($_POST['APPROVED_AMT']));
  $LA_BANK_ACCT_NO = mysql_real_escape_string(trim($_POST['CUST_FIN_INST_ID']));
  $LA_BANK_NAME = mysql_real_escape_string(trim($_POST['CUST_BANK_NAME']));
  $LA_PDT_ID = mysql_real_escape_string(trim($_POST['LA_PDT_ID']));
  $M_CLIENT_ID = mysql_real_escape_string(trim($_POST['M_CLIENT_ID']));
  $M_SVG_LINK_ACCT_ID = mysql_real_escape_string(trim($_POST['M_SVG_LINK_ACCT_ID']));
  $M_SVG_LINK_ACCT_CRNCY = mysql_real_escape_string(trim($_POST['M_SVG_LINK_ACCT_CRNCY']));

  $transactionAmount = $APPROVED_AMT;
  $actualDisbursementDate = date('d F Y', strtotime(date("ymd", time())));
  $NOTE = "DISBURSE TO " . $LN_APPLN_NO;
  $RQST_MSG = BuildDisburseToSavingsAcctRequestMessage($transactionAmount, $actualDisbursementDate, $NOTE);
  //$RQST_MSG = BuildDisburseLoanRequestMessage($transactionAmount, $actualDisbursementDate, $NOTE, $LN_APPLN_NO, $LA_BANK_ACCT_NO, $LA_BANK_NAME);

  // .. Add Disbursement Charges
  $LA_LOAN_ID = $CORE_LOAN_ACCT_ID;
  $chrg_list = array();
  $CHANNEL = $APPLN_CHANNEL;
  $CHRG_CRNCY = $M_SVG_LINK_ACCT_CRNCY;
  $CHRG_PDT_ID = $LA_PDT_ID;
  $chrg_list = FetchLoanDisbursmentChargeList($CHANNEL, $CHRG_CRNCY, $CHRG_PDT_ID);
  for ($i = 0; $i < sizeof($chrg_list); $i++) {
    $chrg = $chrg_list[$i];
    $loan_RECORDID = $chrg['RECORDID'];
    $loan_CHANNEL = $chrg['CHANNEL'];
    $loan_CHRG_CRNCY = $chrg['CHRG_CRNCY'];
    $loan_CHRG_PDT_ID = $chrg['CHRG_PDT_ID'];
    $loan_CHRG_PDT_NAME = $chrg['CHRG_PDT_NAME'];
    $loan_CHRG_EVENT = $chrg['CHRG_EVENT'];
    $loan_CHRG_EXEC_ORDER = $chrg['CHRG_EXEC_ORDER'];
    $loan_CHRG_TYPE = $chrg['CHRG_TYPE'];
    $loan_CHRG_AMT = $chrg['CHRG_AMT'];
    $loan_MIFOS_CHRG_ID = $chrg['MIFOS_CHRG_ID'];
    $loan_MIFOS_CHRG_NAME = $chrg['MIFOS_CHRG_NAME'];
    $loan_STATUS = $chrg['STATUS'];

    // ...0004.2: Create Charge on Savings Account
    if ($loan_loan_CHRG_AMT == 0) {
      // ... do nothing
    } else {
      // ... 004.1: Create Charge on Loan Acoount
      $DUEDATE = date('d F Y', strtotime(date("ymd", time())));
      $LN_CHRG_RQST_MST = BuildLoanApplnChrgRqstMsg($loan_MIFOS_CHRG_ID, $loan_CHRG_AMT, $DUEDATE);
      $response_msg = AddLoanApplicationCharge($LA_LOAN_ID, $LN_CHRG_RQST_MST, $MIFOS_CONN_DETAILS);
      $CONN_FLG = $response_msg["CONN_FLG"];
      $CORE_RESP = $response_msg["CORE_RESP"];

      if (!isset($CORE_RESP["resourceId"])) {
        $CHRG_EXEC_ID = $CORE_RESP["resourceId"];
      }
    }
  } //... end loop

  //echo "<pre>" . print_r($RQST_MSG, true) . "</pre>";
  $response_msg = DisburseLoanToSvngsAcct($CORE_LOAN_ACCT_ID, $RQST_MSG, $MIFOS_CONN_DETAILS);
  //echo "<pre>" . print_r($response_msg, true) . "</pre>";
  //$response_msg = DisburseLoan($CORE_LOAN_ACCT_ID, $RQST_MSG, $MIFOS_CONN_DETAILS);

  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];

  if (!isset($CORE_RESP["resourceId"])) {
    $alert_type = "ERROR";
    $alert_msg = $CORE_RESP["defaultUserMessage"];
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if (isset($CORE_RESP["resourceId"])) {

    // ... 001: Execure Loan Application Charges
    ExecAllLoanApplicationCharges($LA_LOAN_ID, $MIFOS_CONN_DETAILS);

    // ... 002: Create Loan Repayment Standig Order
    $fromClientId = $M_CLIENT_ID;
    $si_name = $LN_APPLN_NO;
    $fromAccountId = $M_SVG_LINK_ACCT_ID;
    $toClientId =  $M_CLIENT_ID;
    $loan_toAccountId = $LA_LOAN_ID;
    $date_validFrom = date('d F Y', strtotime(date("ymd", time())));

    $SO_RQST_MSG = BuildLoanRpymtStandingInstructionMsg($fromClientId, $si_name, $fromAccountId, $toClientId, $loan_toAccountId, $date_validFrom);
    CreateLoanRpymtStandingOrder($SO_RQST_MSG, $MIFOS_CONN_DETAILS);

    $FLG_DISB_TO_SVNGS = "YY";
    $FLG_DISB_TO_SVNGS_USER_ID = $_SESSION['UPR_USER_ID'];
    $DISB_DATE = GetCurrentDateTime();
    $DISB_USER_ID = $_SESSION['UPR_USER_ID'];
    $CORE_RESOURCE_ID = $CORE_RESP["resourceId"];
    $LN_APPLN_STATUS = "APPLN_DISBURSED";
    $q = "UPDATE loan_applns 
          SET FLG_DISB_TO_SVNGS='$FLG_DISB_TO_SVNGS'
             ,FLG_DISB_TO_SVNGS_USER_ID='$FLG_DISB_TO_SVNGS_USER_ID'
             ,DISB_DATE='$DISB_DATE'
             ,DISB_USER_ID='$DISB_USER_ID'
             ,CORE_RESOURCE_ID='$CORE_RESOURCE_ID'
             ,LN_APPLN_STATUS='$LN_APPLN_STATUS'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";

    $update_response = ExecuteEntityUpdate($q);
    if ($update_response == "EXECUTED") {

      # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "LOAN_APPLN";
      $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
      $EVENT = "DISBURSE_TO_SAVINGS_ACCT";
      $EVENT_OPERATION = "DISBURSE_TO_SAVINGS_ACCT_FOR_LOAN_APPLN";
      $EVENT_RELATION = "loan_applns";
      $EVENT_RELATION_NO = $LN_APPLN_NO;
      $OTHER_DETAILS = "DISBURSEMENT_NO: " . $CORE_RESOURCE_ID;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent(
        $AUDIT_DATE,
        $ENTITY_TYPE,
        $ENTITY_ID_AFFECTED,
        $EVENT,
        $EVENT_OPERATION,
        $EVENT_RELATION,
        $EVENT_RELATION_NO,
        $OTHER_DETAILS,
        $INVOKER_ID
      );


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: Loan application funds have beenn disbursed to client savings account. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      //header("Refresh:5;");
    }
  }
}

# ... F0000006: NOT NEEDED ......................................................................................#
if (isset($_POST['btn_not_needed'])) {
  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $NOT_NEEDED_FLG = mysql_real_escape_string(trim($_POST['NOT_NEEDED_FLG']));

  $q = "";
  if ($NOT_NEEDED_FLG == "LOAN_DOCS") {
    $FLG_UPLOAD_LOAN_DOCS = "NA";
    $FLG_UPLOAD_LOAN_DOCS_USER_ID = $_SESSION['UPR_USER_ID'];
    $q = "UPDATE loan_applns 
          SET FLG_UPLOAD_LOAN_DOCS='$FLG_UPLOAD_LOAN_DOCS'
             ,FLG_UPLOAD_LOAN_DOCS_USER_ID='$FLG_UPLOAD_LOAN_DOCS_USER_ID'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  }

  if ($NOT_NEEDED_FLG == "LOAN_GRRTR") {
    $FLG_ADD_GRRTRS = "NA";
    $FLG_ADD_GRRTRS_USER_ID = $_SESSION['UPR_USER_ID'];
    $q = "UPDATE loan_applns 
          SET FLG_ADD_GRRTRS='$FLG_ADD_GRRTRS'
             ,FLG_ADD_GRRTRS_USER_ID='$FLG_ADD_GRRTRS_USER_ID'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  }


  ExecuteEntityUpdate($q);
  header("Refresh:0;");
}

# ... Loan Application Charges
function ExecAllLoanApplicationCharges($LA_LOAN_ID, $MIFOS_CONN_DETAILS)
{

  // ... 001: Get all Loan Application Charges
  $response_msg = GetLoanApplicationChargesFromApi($LA_LOAN_ID, $MIFOS_CONN_DETAILS);
  $chrg_list = $response_msg["CORE_RESP"];

  for ($i = 0; $i < sizeof($chrg_list); $i++) {

    $chrg = $chrg_list[$i];
    $chrg_exec_id = $chrg["id"];
    $sys_chrg_id = $chrg["chargeId"];

    // ... Execute the Charges on Application
    $la_transactionDate = date('d F Y', strtotime(date("ymd", time())));
    $RQST_MSG = BuildLoanApplnChrgMsg($la_transactionDate);
    ExecuteLoanApplicationCharge($LA_LOAN_ID, $chrg_exec_id, $RQST_MSG, $MIFOS_CONN_DETAILS);
  }
}


?>
<!DOCTYPE html>
<html>

<head>
  <?php
  # ... Device Settings and Global CSS
  LoadDeviceSettings();
  LoadDefaultCSSConfigurations("Disburse Loan Appln", $APP_SMALL_LOGO);

  # ... Javascript
  LoadPriorityJS();
  OnLoadExecutions();
  StartTimeoutCountdown();
  ExecuteProcessStatistics();
  ?>

  <script type="text/javascript">
    function CallTopUpProc() {

      var old_loan_acct_id = document.getElementById('OLD_LOAN_ACCT_ID').value;
      var new_loan_acct_id = document.getElementById('NEW_LOAN_ACCT_ID').value;
      var data_source = document.getElementById('TOP_UP_DATASOURCE').value;
      var top_extern_url = document.getElementById('TOP_UP_EXTERN_URL').value;

      // ... Ajax
      $.ajax({
        type: 'post',
        url: top_extern_url,
        data: {
          old_loan_acct_id: old_loan_acct_id,
          new_loan_acct_id: new_loan_acct_id,
          data_source: data_source
        },
        success: function(response) {
          console.log(response);
          //alert(response);

          // ... Handling of Db responses
          response = JSON.parse(response)
          var UPDATE_TOPUP_ACCT_FLG = response.UP;
          var CREATE_TOPUP_ACCT_FLG = response.IN;
          if ((UPDATE_TOPUP_ACCT_FLG == "SUCCESS") && (CREATE_TOPUP_ACCT_FLG == "SUCCESS")) {
            return true;
          } else {
            return false;
          }
        },
        error: function(xhr) {
          console.log(xhr);
          //alert(console.log(xhr));
          alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
          return false;
        }
      });
    }
  </script>
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
          <div align="center" style="width: 100%;"><?php if (isset($_SESSION['ALERT_MSG'])) {
                                                      echo $_SESSION['ALERT_MSG'];
                                                    } ?></div>

          <div class="x_panel">
            <div class="x_title">
              <a href="la-disburse" class="btn btn-dark btn-sm pull-left">Back</a>
              <h2>LN_REF: <?php echo $LN_APPLN_NO; ?></h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">
              <table class="table table-bordered">
                <thead>
                  <tr valign="top">
                    <th width="3%">#</th>
                    <th width="35%">Activity</th>
                    <th>Status</th>
                    <th>Core Rmk</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr valign="top">
                    <td>1.</td>
                    <td>Open Loan Account in Core</td>
                    <td style="<?php echo BackgrounfColorByStatusFlg($FLG_OPEN_LOAN_ACCT); ?>">
                      <?php echo PendingOrDone($FLG_OPEN_LOAN_ACCT); ?>
                    </td>
                    <td>
                      <?php
                      $status = PendingOrDone($FLG_OPEN_LOAN_ACCT);
                      if ($status == "done") {
                        $response_msg = FetchLoanAcctById($CORE_LOAN_ACCT_ID, $MIFOS_CONN_DETAILS);
                        $CONN_FLG = $response_msg["CONN_FLG"];
                        $CORE_RESP = $response_msg["CORE_RESP"];

                        if (isset($CORE_RESP["accountNo"])) {
                          echo $CORE_RESP["accountNo"];
                        }
                      }
                      ?>
                    </td>
                    <td>
                      <?php
                      $status = PendingOrDone($FLG_OPEN_LOAN_ACCT);
                      if ($status == "done") {
                      } else {
                      ?>
                        <form method="post" id="cccc">
                          <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                          <button type="submit" class="btn btn-primary btn-xs" name="btn_create_loan_acct">Open</button>
                        </form>
                      <?php
                      }
                      ?>
                    </td>
                  </tr>

                  <tr valign="top">
                    <td>2.</td>
                    <td>Upload Loan application documents</td>
                    <td style="<?php echo BackgrounfColorByStatusFlg($FLG_UPLOAD_LOAN_DOCS); ?>">
                      <?php echo PendingOrDone($FLG_UPLOAD_LOAN_DOCS); ?>
                    </td>
                    <td></td>
                    <td>
                      <?php
                      if ($FLG_OPEN_LOAN_ACCT == "YY") {
                        $status = PendingOrDone($FLG_UPLOAD_LOAN_DOCS);
                        if ($status == "done") {
                        } else {
                          if ($IS_TOP_UP == "YES") {
                            $TOP_UP_DATASOURCE = GetSystemParameter("TOP_UP_DATASOURCE");
                            $TOP_UP_EXTERN_URL = GetSystemParameter("TOP_UP_EXTERN_URL");
                      ?>
                            <form method="post" id="dddd" onsubmit="return CallTopUpProc(this)">
                              <input type="hidden" id="OLD_LOAN_ACCT_ID" name="OLD_LOAN_ACCT_ID" value="<?php echo $TOP_UP_LOAN_ID; ?>">
                              <input type="hidden" id="NEW_LOAN_ACCT_ID" name="NEW_LOAN_ACCT_ID" value="<?php echo $CORE_LOAN_ACCT_ID; ?>">
                              <input type="hidden" id="TOP_UP_DATASOURCE_HOST" name="TOP_UP_DATASOURCE_HOST" value="<?php echo $TOP_UP_DATASOURCE_HOST; ?>">
                              <input type="hidden" id="TOP_UP_DATASOURCE_USER" name="TOP_UP_DATASOURCE_USER" value="<?php echo $TOP_UP_DATASOURCE_USER; ?>">
                              <input type="hidden" id="TOP_UP_DATASOURCE_PSWD" name="TOP_UP_DATASOURCE_PSWD" value="<?php echo $TOP_UP_DATASOURCE_PSWD; ?>">
                              <input type="hidden" id="TOP_UP_DATASOURCE" name="TOP_UP_DATASOURCE" value="<?php echo $TOP_UP_DATASOURCE; ?>">
                              <input type="hidden" id="TOP_UP_EXTERN_URL" name="TOP_UP_EXTERN_URL" value="<?php echo $TOP_UP_EXTERN_URL; ?>">

                              <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                              <input type="hidden" id="CORE_LOAN_ACCT_ID" name="CORE_LOAN_ACCT_ID" value="<?php echo $CORE_LOAN_ACCT_ID; ?>">
                              <input type="hidden" id="NOT_NEEDED_FLG" name="NOT_NEEDED_FLG" value="LOAN_DOCS">
                              <button type="submit" class="btn btn-primary btn-xs" name="btn_loan_doc">Upload</button>
                              <button type="submit" class="btn btn-default btn-xs" name="btn_not_needed">Not Needed</button>
                            </form>
                          <?php
                          } else if ($IS_TOP_UP != "YES") {
                          ?>
                            <form method="post" id="dddd">
                              <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                              <input type="hidden" id="CORE_LOAN_ACCT_ID" name="CORE_LOAN_ACCT_ID" value="<?php echo $CORE_LOAN_ACCT_ID; ?>">
                              <input type="hidden" id="NOT_NEEDED_FLG" name="NOT_NEEDED_FLG" value="LOAN_DOCS">
                              <button type="submit" class="btn btn-primary btn-xs" name="btn_loan_doc">Upload</button>
                              <button type="submit" class="btn btn-default btn-xs" name="btn_not_needed">Not Needed</button>
                            </form>
                      <?php
                          }
                        }
                      }
                      ?>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>3.</td>
                    <td>Add guarantors to the loan application</td>
                    <td style="<?php echo BackgrounfColorByStatusFlg($FLG_ADD_GRRTRS); ?>">
                      <?php echo PendingOrDone($FLG_ADD_GRRTRS); ?>
                    </td>
                    <td></td>
                    <td>
                      <?php
                      if ($FLG_UPLOAD_LOAN_DOCS == "YY" || $FLG_UPLOAD_LOAN_DOCS == "NA") {
                        $status = PendingOrDone($FLG_ADD_GRRTRS);
                        if ($status == "done") {
                        } else {
                      ?>
                          <form method="post" id="eeee">
                            <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                            <input type="hidden" id="CORE_LOAN_ACCT_ID" name="CORE_LOAN_ACCT_ID" value="<?php echo $CORE_LOAN_ACCT_ID; ?>">
                            <input type="hidden" id="IS_WALK_IN" name="IS_WALK_IN" value="<?php echo $IS_WALK_IN; ?>">
                            <input type="hidden" id="NOT_NEEDED_FLG" name="NOT_NEEDED_FLG" value="LOAN_GRRTR">
                            <button type="submit" class="btn btn-primary btn-xs" name="btn_add_grrtr">Add</button>
                            <button type="submit" class="btn btn-default btn-xs" name="btn_not_needed">Not Needed</button>
                          </form>
                      <?php
                        }
                      }
                      ?>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>4.</td>
                    <!--<td>Disburse Loan to Savings Account</td>-->
                    <td>Disburse Loan</td>
                    <td style="<?php echo BackgrounfColorByStatusFlg($FLG_DISB_TO_SVNGS); ?>">
                      <?php echo PendingOrDone($FLG_DISB_TO_SVNGS); ?>
                    </td>
                    <td></td>
                    <td>
                      <?php
                      if ($FLG_ADD_GRRTRS == "YY" || $FLG_ADD_GRRTRS == "NA") {
                        $status = PendingOrDone($FLG_DISB_TO_SVNGS);
                        if ($status == "done") {
                        } else {
                      ?>
                          <form method="post" id="ffff">
                            <input type="hidden" id="APPLN_CHANNEL" name="APPLN_CHANNEL" value="<?php echo $CHANNEL; ?>">
                            <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                            <input type="hidden" id="CORE_LOAN_ACCT_ID" name="CORE_LOAN_ACCT_ID" value="<?php echo $CORE_LOAN_ACCT_ID; ?>">
                            <input type="hidden" id="APPROVED_AMT" name="APPROVED_AMT" value="<?php echo $APPROVED_AMT; ?>">
                            <input type="hidden" id="CUST_FIN_INST_ID" name="CUST_FIN_INST_ID" value="<?php echo $CUST_FIN_INST_ID; ?>">
                            <input type="hidden" id="CUST_BANK_NAME" name="CUST_BANK_NAME" value="<?php echo $CUST_BANK_NAME; ?>">
                            <input type="hidden" id="LA_PDT_ID" name="LA_PDT_ID" value="<?php echo $LN_PDT_ID; ?>">
                            <input type="hidden" id="M_CLIENT_ID" name="M_CLIENT_ID" value="<?php echo $CUST_CORE_ID; ?>">
                            <input type="hidden" id="M_SVG_LINK_ACCT_ID" name="M_SVG_LINK_ACCT_ID" value="<?php echo $CORE_SVGS_ACCT_ID; ?>">
                            <input type="hidden" id="M_SVG_LINK_ACCT_CRNCY" name="M_SVG_LINK_ACCT_CRNCY" value="<?php echo $CORE_SVGS_ACCT_CRNCY; ?>">

                            <!--<button type="submit" class="btn btn-success btn-xs" name="btn_disb_loan_acct">Disburse</button>-->
                            <button type="submit" class="btn btn-success btn-xs" name="btn_disb_loan_acct_02">Disburse</button>
                          </form>
                      <?php
                        }
                      }
                      ?>
                    </td>
                  </tr>

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