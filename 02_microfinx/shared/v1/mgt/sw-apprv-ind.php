<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Details
$WITHDRAW_REF = mysql_real_escape_string(trim($_GET['k']));
$sw = array();
$sw = FetchSavingsWithdrawApplnById($WITHDRAW_REF);
$RECORD_ID = $sw['RECORD_ID'];
$CHANNEL = $sw['CHANNEL'];
$CUST_ID = $sw['CUST_ID'];
$SVGS_ACCT_ID_TO_DEBIT = $sw['SVGS_ACCT_ID_TO_DEBIT'];
$RQSTD_AMT = $sw['RQSTD_AMT'];
$REASON = $sw['REASON'];
$APPLN_SUBMISSION_DATE = $sw['APPLN_SUBMISSION_DATE'];
$SVGS_HANDLER_USER_ID = $sw['SVGS_HANDLER_USER_ID'];
$FIRST_HANDLED_ON = $sw['FIRST_HANDLED_ON'];
$FIRST_HANDLE_RMKS = $sw['FIRST_HANDLE_RMKS'];
$COMMITTEE_FLG = $sw['COMMITTEE_FLG'];
$COMMITTEE_HANDLER_USER_ID = $sw['COMMITTEE_HANDLER_USER_ID'];
$COMMITTEE_STATUS = $sw['COMMITTEE_STATUS'];
$COMMITTEE_STATUS_DATE = $sw['COMMITTEE_STATUS_DATE'];
$COMMITTEE_RMKS = $sw['COMMITTEE_RMKS'];
$APPROVED_AMT = $sw['APPROVED_AMT'];
$APPROVED_BY = $sw['APPROVED_BY'];
$APPROVAL_DATE = $sw['APPROVAL_DATE'];
$APPROVAL_RMKS = $sw['APPROVAL_RMKS'];
$CUST_FIN_INST_ID = $sw['CUST_FIN_INST_ID'];
$PROC_MODE = $sw['PROC_MODE'];
$PROC_BATCH_NO = $sw['PROC_BATCH_NO'];
$CORE_TXN_ID = $sw['CORE_TXN_ID'];
$SVGS_APPLN_STATUS = $sw['SVGS_APPLN_STATUS'];

# ... 01: Get Client Name .........................................................................................#
$cstmr = array();
$cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
$CUST_CORE_ID = $cstmr['CUST_CORE_ID'];

$response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$CORE_CUST_NAME = $CORE_RESP["displayName"];

# ... 02: Get Client Acct  .........................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];
$SVNGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];
$SVGS_PDT_ID = $CORE_RESP["savingsProductId"];
$SVGS_PDT_NAME = $CORE_RESP["savingsProductName"];
$SVGS_ACCT_CRNCY = $CORE_RESP["currency"]["code"];

$CUST_BANK_NAME = GetCustBankFromBankAcct($CUST_ID, $CUST_FIN_INST_ID);
$FIN_INST_ACCT = $CUST_FIN_INST_ID . " ($CUST_BANK_NAME')";

# ... 03: LOAD CUSTOMER DETAILS .....................................................................................#
$cstmr = array();
$cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
$CUST_ID = $cstmr['CUST_ID'];
$CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
$CUST_EMAIL = $cstmr['CUST_EMAIL'];
$CUST_PHONE = $cstmr['CUST_PHONE'];

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


# ... 04: GET VERIFIER NAME .....................................................................................#
$VERIFIER_NAME = "";
$USER_DETAILS = array();
$USER_DETAILS = GetUserDetailsFromPortal($SVGS_HANDLER_USER_ID);
$VERIF_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
$response_msg = FetchUserDetailsFromCore($VERIF_CORE_ID, $MIFOS_CONN_DETAILS);
$sys_usr = $response_msg["CORE_RESP"];
if (isset($sys_usr["firstname"])) {
  $firstname = $sys_usr["firstname"];
  $lastname = $sys_usr["lastname"];
  $VERIFIER_NAME = $firstname . " " . $lastname;
}


# ... 05: GET APPROVER NAME .....................................................................................#
$APPROVER_NAME = "";
$APPRV_CORE_ID = "";
$USER_DETAILS = array();
$USER_DETAILS = GetUserDetailsFromPortal($APPROVED_BY);
if (isset($USER_DETAILS["USER_CORE_ID"])) {
  $APPRV_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
}
$response_msg = FetchUserDetailsFromCore($APPRV_CORE_ID, $MIFOS_CONN_DETAILS);
$sys_usr = $response_msg["CORE_RESP"];
if (isset($sys_usr["firstname"])) {
  $firstname = $sys_usr["firstname"];
  $lastname = $sys_usr["lastname"];
  $APPROVER_NAME = $firstname . " " . $lastname;
}

# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {

  $ENTITY_TYPE = "STAFF_USER";
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $EVENT_TYPE = "SAVINGS WITHDRAW APPLN APPROVAL";
  $TAN = GeneratePassKey(8);
  $ENC_TAN = AES256::encrypt($TAN);
  $TAN_GEN_DATE = GetCurrentDateTime();

  # ... UPDATE UN-USED TANS
  $q = "UPDATE txn_tans SET TAN_STATUS='KILLED (UNUSED)' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response == "EXECUTED") {
    # ... SQL INSERT
    $q = "INSERT INTO txn_tans(ENTITY_TYPE,ENTITY_ID,EVENT_TYPE,TAN,TAN_GEN_DATE) VALUES('$ENTITY_TYPE','$ENTITY_ID','$EVENT_TYPE','$ENC_TAN','$TAN_GEN_DATE')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"];
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP == "EXECUTED") {

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
      $fff_name = $firstname . " " . $lastname;
      $fff_email = $email;

      # ... DB INSERT
      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "SAVINGS_WITHDRAW_APPROVAL_TAN";
      $RECIPIENT_EMAILS = $fff_email;
      $EMAIL_MESSAGE = "Dear " . $fff_name . "<br>"
        . "This is your authentication TAN is: <b>" . $TAN . "</b>";
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

# ... F0000002: VERIF APPLN .....................................................................................#
if (isset($_POST['btn_apprv_appln'])) {

  $WITHDRAW_REF = mysql_real_escape_string(trim($_POST['WITHDRAW_REF']));
  $APPLN_CHANNEL = mysql_real_escape_string(trim($_POST['APPLN_CHANNEL']));
  $CUST_ID = mysql_real_escape_string(trim($_POST['CUST_ID']));
  $RQSTD_AMT = mysql_real_escape_string(trim($_POST['RQSTD_AMT']));
  $SVGS_PDT_ID = mysql_real_escape_string(trim($_POST['SVGS_PDT_ID']));
  $SVGS_ACCT_CRNCY = mysql_real_escape_string(trim($_POST['SVGS_ACCT_CRNCY']));
  $SVGS_ACCT_ID_TO_DEBIT = mysql_real_escape_string(trim($_POST['SVGS_ACCT_ID_TO_DEBIT']));
  $CUST_CORE_ID = mysql_real_escape_string(trim($_POST['CUST_CORE_ID']));
  $VERIF_RMKS = mysql_real_escape_string(trim($_POST['VERIF_RMKS']));
  $PROC_BANK_ID = mysql_real_escape_string(trim($_POST['PROC_BANK_ID']));
  $PROC_METHOD = mysql_real_escape_string(trim($_POST['PROC_METHOD']));
  $PROC_CHEQ_NO = mysql_real_escape_string(trim($_POST['PROC_CHEQ_NO']));

  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));

  # ... 01: Validate Enterred TAN
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $val_results = array();
  $val_results = ValidateTranTAN($ENTITY_ID, $TRAN_TAN);
  $TAN_MSG_CODE = $val_results["TAN_MSG_CODE"];
  $TAN_MSG_MSG = $val_results["TAN_MSG_MSG"];

  if ($TAN_MSG_CODE == "FALSE") {
    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = $TAN_MSG_MSG;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if ($TAN_MSG_CODE == "TRUE") {

    # ... 01:  Build Transaction request message
    $fin = array();
    $fin = FetchFinInstitutionsById($PROC_BANK_ID);
    $fin_FIN_INST_NAME = $fin['FIN_INST_NAME'];
    $fin_SORTCODE = $fin['SORTCODE'];
    $fin_SWIFT_CODE = $fin['SWIFT_CODE'];
    $fin_BANK_CODE = $fin['BANK_CODE'];
    $fin_ORG_ACCT_NUM = $fin['ORG_ACCT_NUM'];
    $fin_MIFOS_PYMT_TYPE_ID = $fin['MIFOS_PYMT_TYPE_ID'];
    $fin_MIFOS_GL_ACCT_ID = $fin['MIFOS_GL_ACCT_ID'];

    $t_transactionDate =  date('d F Y', strtotime(date("ymd", time())));
    $t_transactionAmount = $RQSTD_AMT;
    $t_paymentTypeId = $fin_MIFOS_PYMT_TYPE_ID;
    $t_accountNumber = $fin_ORG_ACCT_NUM . "-" . $fin_BANK_CODE;
    $t_checkNumber = $PROC_CHEQ_NO . "-" . $PROC_METHOD;
    $t_routingCode = $fin_SORTCODE;
    $t_receiptNumber = "SAVINGS WITHDRAW. REF: " . $WITHDRAW_REF;
    $t_bankNumber = $fin_FIN_INST_NAME;
    $WITHDRAW_TXN_MSG = BuildRawWithdrawRqstMsg($t_transactionDate, $t_transactionAmount, $t_paymentTypeId, $t_accountNumber, $t_checkNumber, $t_routingCode, $t_receiptNumber, $t_bankNumber);

    // ... execute withdrawal
    $response_msg = MakeDirectWithdrawalTransaction($SVGS_ACCT_ID_TO_DEBIT, $WITHDRAW_TXN_MSG, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];


    if (!isset($CORE_RESP["resourceId"])) {
      # ... Send System Response
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["errors"][0]["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["resourceId"])) {

      $CORE_TXN_TRANSFER_ID = $CORE_RESP["resourceId"];

      # ... 0004:  Apply share purchase charge
      // ...0004.1: Fetch Purchase shares charge list
      $chrg_list = array();
      $CHANNEL = $APPLN_CHANNEL;
      $CHRG_CRNCY = $SVGS_ACCT_CRNCY;
      $CHRG_PDT_ID = $SVGS_PDT_ID;
      $TOTAL_CHRG_AMT = 0;
      $CHRG_CORE_TRAN_IDS = "";
      $chrg_list = FetchSavingsWithdrawChargeList($CHANNEL, $CHRG_CRNCY, $CHRG_PDT_ID);
      //echo "<pre>".print_r($chrg_list,true)."</pre>";
      //die();
      for ($i = 0; $i < sizeof($chrg_list); $i++) {
        $chrg_block = $chrg_list[$i];
        $TT_CHRG_TYPE = $chrg_block["TT_CHRG_TYPE"];
        $TT_CHRG_LIST = $chrg_block["TT_CHRG_LIST"];

        // ... deduce charge amount
        $svg_CHRG_AMT = 0;
        $svg_MIFOS_CHRG_ID = 0;
        if ($TT_CHRG_TYPE == "RANGE") {
          $RANGE_MATRIX = $TT_CHRG_LIST;
          $WW_TRAN_AMOUNT = $RQSTD_AMT;

          $chrg_tarrif = array();
          $chrg_tarrif = GetChargeFeeFromRange($RANGE_MATRIX, $WW_TRAN_AMOUNT);
          $svg_CHRG_AMT = $chrg_tarrif["CHRG_TXN_FEE"];
          $svg_MIFOS_CHRG_ID = $chrg_tarrif["CHRG_MIFOS_CHRG_ID"];

          $TOTAL_CHRG_AMT = $TOTAL_CHRG_AMT + $svg_CHRG_AMT;
        } else if ($TT_CHRG_TYPE == "FLAT") {

          $svg_CHRG_AMT = $TT_CHRG_LIST[0]["CHRG_AMT"];
          $svg_MIFOS_CHRG_ID = $TT_CHRG_LIST[0]["MIFOS_CHRG_ID"];

          $TOTAL_CHRG_AMT = $TOTAL_CHRG_AMT + $svg_CHRG_AMT;
        } else if ($TT_CHRG_TYPE == "PERCENT") {

          $svg_CHRG_AMT = (($TT_CHRG_LIST[0]["CHRG_AMT"] / 100) * $RQSTD_AMT);
          $svg_MIFOS_CHRG_ID = $TT_CHRG_LIST[0]["MIFOS_CHRG_ID"];

          $TOTAL_CHRG_AMT = $TOTAL_CHRG_AMT + $svg_CHRG_AMT;
        } else if ($TT_CHRG_TYPE == "EXCISE_PERCENT") {

          $svg_CHRG_AMT = (($TT_CHRG_LIST[0]["CHRG_AMT"] / 100) * $TOTAL_CHRG_AMT);
          $svg_MIFOS_CHRG_ID = $TT_CHRG_LIST[0]["MIFOS_CHRG_ID"];
        }


        // ...0004.2: Create Charge on Savings Account
        if ($svg_CHRG_AMT == 0) {
          // ... do nothing
        } else {

          $CustSvgAcctId = $SVGS_ACCT_ID_TO_DEBIT;
          $chrgId = $svg_MIFOS_CHRG_ID;
          $chrgamt = $svg_CHRG_AMT;
          $chrgduedate = date('d F Y', strtotime(date("ymd", time())));
          $ChargeRqstMsg = BuildCreateChargeRqstMsg($chrgId, $chrgamt, $chrgduedate);
          $response_msg = SVGS_CreateSavingsAcctCharge($CustSvgAcctId, $ChargeRqstMsg, $MIFOS_CONN_DETAILS);
          $CONN_FLG = $response_msg["CONN_FLG"];
          $CORE_RESP = $response_msg["CORE_RESP"];
          if (!isset($CORE_RESP["savingsId"])) {
            $alert_type = "ERROR";
            $alert_msg = "STAGE 001: " . $CORE_RESP["errors"][0]["defaultUserMessage"];
            $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
          } else {

            // ...0004.3: Getting the pay charge id from savings account
            $response_msg = SVGS_GetSavingsChargeId($CustSvgAcctId, $chrgId, $MIFOS_CONN_DETAILS);
            $CONN_FLG = $response_msg["CONN_FLG"];
            $CORE_RESP = $response_msg["CORE_RESP"];
            if (!isset($CORE_RESP["data"])) {
              $alert_type = "ERROR";
              $alert_msg = "STAGE 002: " . $CORE_RESP["errors"][0]["defaultUserMessage"];
              $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
            } else {
              $SavingsAcctChargeId = $CORE_RESP["data"][0]["row"][0];

              // ...0004.4: Pay the charge
              $client_chrg_amt = $chrgamt;
              $client_due_date = date('d F Y', strtotime(date("ymd", time())));
              $PayChargeRqstMsg = BuildCreatePayChargeRqstMsg($client_chrg_amt, $client_due_date);
              $response_msg = SVGS_PaySavingsAcctCharge($CustSvgAcctId, $SavingsAcctChargeId, $PayChargeRqstMsg, $MIFOS_CONN_DETAILS);
              $CONN_FLG = $response_msg["CONN_FLG"];
              $CORE_RESP = $response_msg["CORE_RESP"];
              if (!isset($CORE_RESP["resourceId"])) {
                $alert_type = "ERROR";
                $alert_msg = "STAGE 003: " . $CORE_RESP["errors"][0]["defaultUserMessage"];
                $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
              } else {

                $response_msg = SVG_GetSavingsChargeTranId($SavingsAcctChargeId, $MIFOS_CONN_DETAILS);
                $CONN_FLG = $response_msg["CONN_FLG"];
                $CORE_RESP = $response_msg["CORE_RESP"];
                if (!isset($CORE_RESP["data"])) {
                  $alert_type = "ERROR";
                  $alert_msg = "STAGE 004: " . $CORE_RESP["errors"][0]["defaultUserMessage"];
                  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
                } else {
                  $savings_chrg_tran_id = $CORE_RESP["data"][0]["row"][1];

                  # ... assemble all charge tran ids for recording
                  if ($CHRG_CORE_TRAN_IDS == "") {
                    $CHRG_CORE_TRAN_IDS = $savings_chrg_tran_id;
                  } else {
                    $CHRG_CORE_TRAN_IDS = $CHRG_CORE_TRAN_IDS . "^" . $savings_chrg_tran_id;
                  }
                }
              }
            }
          }
        }
      }

      # ... 03: Update the Database
      $TRAN_CORE_REF = $CORE_TXN_TRANSFER_ID;
      $CORE_TXN_CHRG_IDS = $CHRG_CORE_TRAN_IDS;
      $APPROVED_AMT = $RQSTD_AMT;
      $APPROVED_BY = $_SESSION['UPR_USER_ID'];
      $APPROVAL_DATE = GetCurrentDateTime();
      $APPROVAL_RMKS = $VERIF_RMKS;
      $SVGS_APPLN_STATUS = "APPROVED";


      # ... Updating the role id
      $q2 = "UPDATE svgs_withdraw_requests 
                SET CORE_TXN_ID='$TRAN_CORE_REF'
                   ,CORE_TXN_CHRG_IDS='$CORE_TXN_CHRG_IDS' 
                   ,APPROVED_AMT='$APPROVED_AMT' 
                   ,APPROVED_BY='$APPROVED_BY' 
                   ,APPROVAL_DATE='$APPROVAL_DATE' 
                   ,APPROVAL_RMKS='$APPROVAL_RMKS' 
                   ,SVGS_APPLN_STATUS='$SVGS_APPLN_STATUS' 
             WHERE WITHDRAW_REF='$WITHDRAW_REF'";
      $update_response = ExecuteEntityUpdate($q2);
      if ($update_response == "EXECUTED") {

        # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
        $INIT_CHANNEL = "WEB";
        $MSG_TYPE = "SAVINGS APPLICATION APPROVAL";
        $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
        $EMAIL_MESSAGE = "Dear " . $_SESSION['FP_NAME'] . "<br>"
          . "Your savings application has been <b>$SVGS_APPLN_STATUS</b>. Below are the details;<br>"
          . "-------------------------------------------------------------------------------------------------<br>"
          . "<b>APPLN REF:</b> <i>" . $WITHDRAW_REF . "</i><br>"
          . "<b>AMOUNT:</b> <i>" . number_format($APPROVED_AMT) . "</i><br>"
          . "-------------------------------------------------------------------------------------------------<br>"
          . "<br/>"
          . "Regards<br>"
          . "Management<br>";
        $EMAIL_ATTACHMENT_PATH = "";
        $RECORD_DATE = GetCurrentDateTime();
        $EMAIL_STATUS = "NN";

        $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
        ExecuteEntityInsert($qqq);


        # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
        $ENTITY_ID = $_SESSION['UPR_USER_ID'];
        $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
        ExecuteEntityUpdate($qww);

        # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "SAVINGS_WITHDRAW_APPLN";
        $ENTITY_ID_AFFECTED = $WITHDRAW_REF;
        $EVENT = "APPROVAL";
        $EVENT_OPERATION = "VERIFY_SAVINGS_WITHDRAW_APPLN";
        $EVENT_RELATION = "svgs_withdraw_requests";
        $EVENT_RELATION_NO = $WITHDRAW_REF;
        $OTHER_DETAILS = $WITHDRAW_REF . "|" . $APPROVAL_RMKS;
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
        $alert_msg = "MESSAGE: Application has been approved successfully. Refreshing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
        header("Refresh:5; URL=sw-apprv");
      }
    } # ...IFF..ELSE

  }
}

# ... F0000003: REJECTION APPLN .....................................................................................#
if (isset($_POST['btn_reject_appln'])) {

  $WITHDRAW_REF = mysql_real_escape_string(trim($_POST['WITHDRAW_REF']));
  $CUST_ID = mysql_real_escape_string(trim($_POST['CUST_ID']));
  $VERIF_RMKS = mysql_real_escape_string(trim($_POST['VERIF_RMKS']));
  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));

  # ... 01: Validate Enterred TAN
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $val_results = array();
  $val_results = ValidateTranTAN($ENTITY_ID, $TRAN_TAN);
  $TAN_MSG_CODE = $val_results["TAN_MSG_CODE"];
  $TAN_MSG_MSG = $val_results["TAN_MSG_MSG"];

  if ($TAN_MSG_CODE == "FALSE") {
    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = $TAN_MSG_MSG;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if ($TAN_MSG_CODE == "TRUE") {

    $APPROVED_BY = $_SESSION['UPR_USER_ID'];
    $APPROVAL_DATE = GetCurrentDateTime();
    $APPROVAL_RMKS = $VERIF_RMKS;
    $SVGS_APPLN_STATUS = "REJECTED";

    # ... Updating the role id
    $q2 = "UPDATE svgs_withdraw_requests 
              SET APPROVED_BY='$APPROVED_BY' 
                 ,APPROVAL_DATE='$APPROVAL_DATE' 
                 ,APPROVAL_RMKS='$APPROVAL_RMKS' 
                 ,SVGS_APPLN_STATUS='$SVGS_APPLN_STATUS' 
           WHERE WITHDRAW_REF='$WITHDRAW_REF'";
    $update_response = ExecuteEntityUpdate($q2);
    if ($update_response == "EXECUTED") {

      # ... Send Email
      # ... R005: LOAD CUSTOMER DETAILS .....................................................................................#
      $cstmr = array();
      $cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
      $CUST_ID = $cstmr['CUST_ID'];
      $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
      $CUST_EMAIL = $cstmr['CUST_EMAIL'];
      $CUST_PHONE = $cstmr['CUST_PHONE'];

      # ... Get Customer Name From Core
      $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
      $CONN_FLG = $response_msg["CONN_FLG"];
      $CORE_RESP = $response_msg["CORE_RESP"];
      $displayName = strtoupper($CORE_RESP["displayName"]);

      # ... Decrypt Email & Phone
      $EMAIL = AES256::decrypt($CUST_EMAIL);
      $PHONE = AES256::decrypt($CUST_PHONE);
      $FP_NAME = $displayName;
      $FP_EMAIL = $EMAIL;
      $FP_PHONE = $PHONE;

      # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "SAVINGS WITHDRAW APPLICATION REJECTION";
      $RECIPIENT_EMAILS = $FP_EMAIL;
      $EMAIL_MESSAGE = "Dear " . $FP_NAME . "<br>"
        . "Your savings withdraw application has been <b>$SVGS_APPLN_STATUS</b>. Below are the details;<br>"
        . "-------------------------------------------------------------------------------------------------<br>"
        . "<b>APPLN REF:</b> <i>" . $WITHDRAW_REF . "</i><br>"
        . "<b>REMARKS FROM MANAGEMENT:</b><br> "
        . "<i>" . $APPROVAL_RMKS . "</i><br>"
        . "-------------------------------------------------------------------------------------------------<br>"
        . "<br/>"
        . "Regards<br>"
        . "Management<br>"
        . "<i></i>";
      $EMAIL_ATTACHMENT_PATH = "";
      $RECORD_DATE = GetCurrentDateTime();
      $EMAIL_STATUS = "NN";

      $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
      ExecuteEntityInsert($qqq);


      # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $ENTITY_ID = $_SESSION['UPR_USER_ID'];
      $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
      ExecuteEntityUpdate($qww);

      # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "SAVINGS_WITHDRAW_APPLN";
      $ENTITY_ID_AFFECTED = $WITHDRAW_REF;
      $EVENT = "APPRVAL_REJECT";
      $EVENT_OPERATION = "REJECT_SAVINGS_WITHDRAW_APPLN";
      $EVENT_RELATION = "svgs_withdraw_requests";
      $EVENT_RELATION_NO = $WITHDRAW_REF;
      $OTHER_DETAILS = $WITHDRAW_REF . "|" . $FIRST_HANDLE_RMKS;
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


      $alert_type = "ERROR";
      $alert_msg = "MESSAGE: Application has been rejected. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5; URL=sw-queue");
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
  LoadDefaultCSSConfigurations("Approve Withdraws", $APP_SMALL_LOGO);

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
        <div class="col-md-12 col-sm-12 col-xs-12">

          <!-- System Message Area -->
          <div align="center" style="width: 100%;"><?php if (isset($_SESSION['ALERT_MSG'])) {
                                                      echo $_SESSION['ALERT_MSG'];
                                                    } ?></div>


          <div class="x_panel">
            <div class="x_title">
              <a href="sw-apprv" class="btn btn-dark btn-sm pull-left">Back</a>
              <h2>Savings Withdrawal Application</h2>
              <div class="clearfix"></div>
            </div>

            <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->
            <div class="x_content">

              <table class="table table-bordered" style="font-size: 12px;">
                <tr>
                  <td width="20%"><b>Withdraw Appln Ref</b></td>
                  <td colspan="5"><?php echo $WITHDRAW_REF; ?></td>
                </tr>
                <tr>
                  <td><b>Appln Submission Date</b></td>
                  <td colspan="5"><?php echo $APPLN_SUBMISSION_DATE; ?></td>
                </tr>
                <tr>
                  <td><b>Appln Verification Date</b></td>
                  <td width="16%"><?php echo $FIRST_HANDLED_ON; ?></td>
                  <td><b>Verified By</b></td>
                  <td width="16%"><?php echo $VERIFIER_NAME; ?></td>
                  <td width="20%"><b>Verification Remarks</b></td>
                  <td><?php echo $FIRST_HANDLE_RMKS; ?></td>
                </tr>
                <tr>
                  <td><b>Appln Approval Date</b></td>
                  <td><?php echo $APPROVAL_DATE; ?></td>
                  <td><b>Approved By</b></td>
                  <td><?php echo $APPROVER_NAME; ?></td>
                  <td><b>Appln Approval Remarks</b></td>
                  <td><?php echo $APPROVAL_RMKS; ?></td>
                </tr>
                <tr>
                  <td><b>Appln Status</b></td>
                  <td colspan="3"><?php echo $SVGS_APPLN_STATUS; ?></td>
                </tr>
              </table>

              <form method="post" id="dgdhasjERTYDGHDH">
                <input type="hidden" id="WITHDRAW_REF" name="WITHDRAW_REF" value="<?php echo $WITHDRAW_REF; ?>">
                <input type="hidden" id="APPLN_CHANNEL" name="APPLN_CHANNEL" value="<?php echo $CHANNEL; ?>">
                <input type="hidden" id="CUST_ID" name="CUST_ID" value="<?php echo $CUST_ID; ?>">
                <input type="hidden" id="RQSTD_AMT" name="RQSTD_AMT" value="<?php echo $RQSTD_AMT; ?>">
                <input type="hidden" id="SVGS_PDT_ID" name="SVGS_PDT_ID" value="<?php echo $SVGS_PDT_ID; ?>">
                <input type="hidden" id="SVGS_PDT_NAME" name="SVGS_PDT_NAME" value="<?php echo $SVGS_PDT_NAME; ?>">
                <input type="hidden" id="SVGS_ACCT_CRNCY" name="SVGS_ACCT_CRNCY" value="<?php echo $SVGS_ACCT_CRNCY; ?>">
                <input type="hidden" id="SVGS_ACCT_ID_TO_DEBIT" name="SVGS_ACCT_ID_TO_DEBIT" value="<?php echo $SVGS_ACCT_ID_TO_DEBIT; ?>">
                <input type="hidden" id="CUST_CORE_ID" name="CUST_CORE_ID" value="<?php echo $CUST_CORE_ID; ?>">


                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Customer Id:</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $CUST_ID; ?>">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Customer Name:</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $CORE_CUST_NAME; ?>">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Savings Account To Debit:</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?>">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Savings Account Balance:</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo number_format($SVNGS_ACCT_BAL); ?>">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Withdraw Amount Requested:</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo number_format($RQSTD_AMT); ?>">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Deposit Funds on:</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $FIN_INST_ACCT; ?>">
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Withdraw Purpose</label>
                  <textarea class="form-control" rows="3" disabled=""><?php echo $REASON; ?></textarea>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Enter Authorization TAN:</label>
                  <button type="submit" class="btn btn-warning btn-xs" name="btn_gen_tan">Generate Auth TAN</button>
                  <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Approval Remarks</label>
                  <textarea class="form-control" rows="3" id="VERIF_RMKS" name="VERIF_RMKS"></textarea>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 form-group">
                  <label>Transaction is to be processed from:</label>
                  <Select id="PROC_BANK_ID" name="PROC_BANK_ID" class="form-control">
                    <option value="">-------</option>
                    <?php
                    $proc_bank_list = array();
                    $proc_bank_list = FetchTranProcBanks();

                    for ($i = 0; $i < sizeof($proc_bank_list); $i++) {

                      $proc_bank = $proc_bank_list[$i];
                      $FIN_INST_ID = $proc_bank['FIN_INST_ID'];
                      $FIN_INST_NAME = $proc_bank['FIN_INST_NAME'];
                    ?>
                      <option value="<?php echo $FIN_INST_ID; ?>"><?php echo $FIN_INST_NAME; ?></option>
                    <?php
                    }
                    ?>
                  </Select>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 form-group">
                  <label>Transaction processing Method:</label>
                  <Select id="PROC_METHOD" name="PROC_METHOD" class="form-control">
                    <option value="">-------</option>
                    <option value="cheque">CHEQUE</option>
                    <option value="eft">EFT</option>
                    <option value="rtgs">RTGS</option>
                    <option value="swift">SWIFT</option>
                  </Select>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Cheque Number (If method selected is cheque):</label>
                  <input type="text" id="PROC_CHEQ_NO" name="PROC_CHEQ_NO" class="form-control">
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <button type="submit" class="btn btn-danger" name="btn_reject_appln">Reject</button>
                  <button type="submit" class="btn btn-success" name="btn_apprv_appln">Approve Appln</button>
                </div>



              </form>

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