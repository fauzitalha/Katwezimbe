<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Details
$SHARES_APPLN_REF = mysql_real_escape_string(trim($_GET['k']));
$shr = array();
$shr = FetchShareRequestApplnsById($SHARES_APPLN_REF);
$RECORD_ID = $shr['RECORD_ID'];
$CUST_ID = $shr['CUST_ID'];
$CHANNEL = $shr['CHANNEL'];
$SVGS_ACCT_ID_TO_DEBIT = $shr['SVGS_ACCT_ID_TO_DEBIT'];
$SHARES_REQUESTED = $shr['SHARES_REQUESTED'];
$SHARES_ACCT_ID_TO_CREDIT = $shr['SHARES_ACCT_ID_TO_CREDIT'];
$APPLN_SUBMISSION_DATE = $shr['APPLN_SUBMISSION_DATE'];
$SHARES_HANDLER_USER_ID = $shr['SHARES_HANDLER_USER_ID'];
$FIRST_HANDLED_ON = $shr['FIRST_HANDLED_ON'];
$FIRST_HANDLE_RMKS = $shr['FIRST_HANDLE_RMKS'];
$APPROVED_AMT = $shr['APPROVED_AMT'];
$APPROVED_BY = $shr['APPROVED_BY'];
$APPROVAL_DATE = $shr['APPROVAL_DATE'];
$APPROVAL_RMKS = $shr['APPROVAL_RMKS'];
$CORE_TXN_ID = $shr['CORE_TXN_ID'];
$SHARES_APPLN_STATUS = $shr['SHARES_APPLN_STATUS'];

# ... 01: Get Shares Client Name .....................................................................................#
$response_msg =  FetchSharesAccountDetailsById($SHARES_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$SHARES_ACCT_NUM = $CORE_RESP["accountNo"];
$SHARES_CUST_NAME = $CORE_RESP["clientName"];
$SHARES_OWNED= $CORE_RESP["summary"]["totalApprovedShares"];
$SHARES_PDT_ID = $CORE_RESP["productId"];
$SHARES_PDT_NAME = $CORE_RESP["productName"];

# ... 02: Get Savings Account Details ................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$SVGS_CUST_ID = $CORE_RESP["clientId"];
$SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];
$SVGS_ACCT_BAL= $CORE_RESP["summary"]["accountBalance"];
$SVGS_ACCT_CRNCY = $CORE_RESP["currency"]["code"];


# ... 03: Get Share Products Details .....................................................................................#
$response_msg = FetchShareProductById($SHARES_PDT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$SHARES_UNIT_PRICE = $CORE_RESP["unitPrice"];
$SHARES_MAX_SHR = $CORE_RESP["maximumShares"];
$SHRS_VALUE = ($SHARES_UNIT_PRICE * $SHARES_OWNED);

# ... 02: LOAD CUSTOMER DETAILS .....................................................................................#
$cstmr = array();
$cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
$CUST_ID = $cstmr['CUST_ID'];
$CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
$CUST_EMAIL = $cstmr['CUST_EMAIL'];
$CUST_PHONE = $cstmr['CUST_PHONE'];

# ... Decrypt Email & Phone
$EMAIL = AES256::decrypt($CUST_EMAIL);
$PHONE = AES256::decrypt($CUST_PHONE);
$_SESSION['FP_NAME'] = $SHARES_CUST_NAME;
$_SESSION['FP_EMAIL'] = $EMAIL;
$_SESSION['FP_PHONE'] = $PHONE;

# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "STAFF_USER";
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $EVENT_TYPE = "BUY SHARES APPLN APPROVAL";
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
      $MSG_TYPE = "BUY_SHARES_APPLN_TAN";
      $RECIPIENT_EMAILS = $fff_email;
      $EMAIL_MESSAGE = "Dear ".$fff_name."<br>"
                      ."This is your shares auth TAN is: <b>".$TAN."</b>";
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

  $SHARES_APPLN_REF = mysql_real_escape_string(trim($_POST['SHARES_APPLN_REF']));
  $APPLN_CHANNEL = mysql_real_escape_string(trim($_POST['APPLN_CHANNEL']));
  $CUST_ID = mysql_real_escape_string(trim($_POST['CUST_ID']));

  $SHARES_PDT_ID = mysql_real_escape_string(trim($_POST['SHARES_PDT_ID']));
  $SHARES_ACCT_ID_TO_CREDIT = mysql_real_escape_string(trim($_POST['SHARES_ACCT_ID_TO_CREDIT']));
  $SHARES_REQUESTED = mysql_real_escape_string(trim($_POST['SHARES_REQUESTED']));
  $SHARES_UNIT_PRICE = mysql_real_escape_string(trim($_POST['SHARES_UNIT_PRICE']));

  $SVGS_ACCT_CRNCY = mysql_real_escape_string(trim($_POST['SVGS_ACCT_CRNCY']));
  $SVGS_ACCT_ID_TO_DEBIT = mysql_real_escape_string(trim($_POST['SVGS_ACCT_ID_TO_DEBIT']));
  $SVGS_CUST_ID = mysql_real_escape_string(trim($_POST['SVGS_CUST_ID']));

  $VERIF_RMKS = mysql_real_escape_string(trim($_POST['VERIF_RMKS']));
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

    # ... 0001: Apply the Shares
    $SHARES_ACCT_ID = $SHARES_ACCT_ID_TO_CREDIT;
    $requestedDate = date('d F Y', strtotime( date("ymd",time()) ));
    $requestedShares = $SHARES_REQUESTED;
    $SHARE_MSG = BuildApplySharesMessage($requestedDate, $requestedShares);
    $response_msg = ApplyAdditionalShares($SHARES_ACCT_ID, $SHARE_MSG, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];

    if (!isset($CORE_RESP["resourceId"])) {
      # ... Send System Response
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["errors"][0]["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["resourceId"])){

      # ... 0003: Approve the Applied Shares
      $purchase_tran_id = $CORE_RESP["changes"]["additionalshares"];
      $SHARES_MSG_APPRV = BuildApproveSharesMessage($purchase_tran_id);
      $response_msg = ApproveAdditionalShares($SHARES_ACCT_ID, $SHARES_MSG_APPRV, $MIFOS_CONN_DETAILS);
      $CONN_FLG = $response_msg["CONN_FLG"];
      $CORE_RESP = $response_msg["CORE_RESP"];
      if (!isset($CORE_RESP["resourceId"])) {
        # ... Send System Response
        $alert_type = "ERROR";
        $alert_msg = $CORE_RESP["errors"][0]["defaultUserMessage"];
      } else if (isset($CORE_RESP["resourceId"])){

        # ... 0004:  Apply share purchase charge
        // ...0004.1: Fetch Purchase shares charge list
        $chrg_list = array();
        $CHANNEL = $APPLN_CHANNEL;
        $CHRG_CRNCY = $SVGS_ACCT_CRNCY;
        $CHRG_PDT_ID = $SHARES_PDT_ID;
        $chrg_list = FetchSharesPurchaseChargeList($CHANNEL, $CHRG_CRNCY, $CHRG_PDT_ID);
        for ($i=0; $i < sizeof($chrg_list); $i++) { 
          $chrg = $chrg_list[$i];
          $shr_RECORDID = $chrg['RECORDID'];
          $shr_CHANNEL = $chrg['CHANNEL'];
          $shr_CHRG_CRNCY = $chrg['CHRG_CRNCY'];
          $shr_CHRG_PDT_ID = $chrg['CHRG_PDT_ID'];
          $shr_CHRG_PDT_NAME = $chrg['CHRG_PDT_NAME'];
          $shr_CHRG_EVENT = $chrg['CHRG_EVENT'];
          $shr_CHRG_EXEC_ORDER = $chrg['CHRG_EXEC_ORDER'];
          $shr_CHRG_TYPE = $chrg['CHRG_TYPE'];
          $shr_MIFOS_CHRG_ID = $chrg['MIFOS_CHRG_ID'];
          $shr_MIFOS_CHRG_NAME = $chrg['MIFOS_CHRG_NAME'];
          $shr_STATUS = $chrg['STATUS'];

          // ...0004.2: Get charge details from Mifos Core
          $response_msg = SVGS_GetChrgDetailById($shr_MIFOS_CHRG_ID, $MIFOS_CONN_DETAILS);
          $CONN_FLG = $response_msg["CONN_FLG"];
          $CORE_RESP = $response_msg["CORE_RESP"];
          $MIFOS_CHRG_DETAILS = $CORE_RESP["data"][0]["row"];
          $_mif_chrg_id = $MIFOS_CHRG_DETAILS[0];
          $_mif_chrg_name = $MIFOS_CHRG_DETAILS[1];
          $_mif_chrg_currency_code = $MIFOS_CHRG_DETAILS[2];
          $_mif_chrg_amount = $MIFOS_CHRG_DETAILS[3];


          // ...0004.3: Create Charge on Savings Account
          $CustSvgAcctId = $SVGS_ACCT_ID_TO_DEBIT;
          $chrgId = $shr_MIFOS_CHRG_ID;
          $chrgamt = ($SHARES_REQUESTED * $_mif_chrg_amount);
          $chrgduedate = date('d F Y', strtotime( date("ymd",time()) ));
          $ChargeRqstMsg = BuildCreateChargeRqstMsg($chrgId, $chrgamt, $chrgduedate);
          $response_msg = SVGS_CreateSavingsAcctCharge($CustSvgAcctId, $ChargeRqstMsg, $MIFOS_CONN_DETAILS);
          $CONN_FLG = $response_msg["CONN_FLG"];
          $CORE_RESP = $response_msg["CORE_RESP"];
          if (!isset($CORE_RESP["savingsId"])) {
            $alert_type = "ERROR";
            $alert_msg = "STAGE 001: " . $CORE_RESP["errors"][0]["defaultUserMessage"];
            $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
          } else {
            
            // ...0004.4: Getting the pay charge id from savings account
            $response_msg = SVGS_GetSavingsChargeId($CustSvgAcctId, $chrgId, $MIFOS_CONN_DETAILS);
            $CONN_FLG = $response_msg["CONN_FLG"];
            $CORE_RESP = $response_msg["CORE_RESP"];
            if (!isset($CORE_RESP["data"])) {
              $alert_type = "ERROR";
              $alert_msg = "STAGE 002: " . $CORE_RESP["errors"][0]["defaultUserMessage"];
              $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
            } else {
              $SavingsAcctChargeId = $CORE_RESP["data"][0]["row"][0];

              // ...0004.5: Pay the charge
              $client_chrg_amt = $chrgamt;
              $client_due_date = date('d F Y', strtotime( date("ymd",time()) ));
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

                  # ...
                  $CORE_TXN_ID = $savings_chrg_tran_id;
                  $APPROVED_BY = $_SESSION['UPR_USER_ID'];
                  $APPROVAL_DATE = GetCurrentDateTime();
                  $APPROVAL_RMKS = $VERIF_RMKS;
                  $SHARES_APPLN_STATUS = "APPROVED";

                  # ... Updating the role id
                  $q2 = "UPDATE shares_appln_requests 
                        SET APPROVED_BY='$APPROVED_BY' 
                            ,APPROVAL_DATE='$APPROVAL_DATE' 
                            ,APPROVAL_RMKS='$APPROVAL_RMKS' 
                            ,CORE_TXN_ID='$CORE_TXN_ID' 
                            ,SHARES_APPLN_STATUS='$SHARES_APPLN_STATUS' 
                        WHERE SHARES_APPLN_REF='$SHARES_APPLN_REF'";

                  $update_response = ExecuteEntityUpdate($q2);
                  if ($update_response=="EXECUTED") {

                    # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
                    $INIT_CHANNEL = "WEB";
                    $MSG_TYPE = "BUY SHARES APPLN APPROVAL";
                    $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
                    $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                                    ."Your buy shares application has been <b>$SHARES_APPLN_STATUS</b>. Below are the details;<br>"
                                    ."-------------------------------------------------------------------------------------------------<br>"
                                    ."<b>APPLN REF:</b> <i>".$SHARES_APPLN_REF."</i><br>"
                                    ."<b>SHARES_BOUGHT:</b> <i>".number_format($SHARES_REQUESTED)."</i><br>"
                                    ."-------------------------------------------------------------------------------------------------<br>"
                                    ."<br/>"
                                    ."Regards<br>"
                                    ."Management<br>";
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
                    $ENTITY_TYPE = "BUY_SHARES_APPLN";
                    $ENTITY_ID_AFFECTED = $SHARES_APPLN_REF;
                    $EVENT = "APPROVE";
                    $EVENT_OPERATION = "APPROVE_BUY_SHARES_APPLN";
                    $EVENT_RELATION = "shares_appln_requests";
                    $EVENT_RELATION_NO = $SHARES_APPLN_REF;
                    $OTHER_DETAILS = $SHARES_APPLN_REF."|".$FIRST_HANDLE_RMKS;
                    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
                    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                                  $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

                    $alert_type = "SUCCESS";
                    $alert_msg = "MESSAGE: Buy shares application has been approved successfully. Refreshing in 5 seconds.";
                    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
                    header("Refresh:5; URL=shr-queue");
                  }
                }
              }
            }
          }
        }
      } # ...END..IFF..ELSE
    } # ...END..IFF..ELSE


  }
}

# ... F0000003: REJECTION APPLN .....................................................................................#
if (isset($_POST['btn_reject_appln'])) {

  $SHARES_APPLN_REF = mysql_real_escape_string(trim($_POST['SHARES_APPLN_REF']));
  $CUST_ID = mysql_real_escape_string(trim($_POST['CUST_ID']));
  $VERIF_RMKS = mysql_real_escape_string(trim($_POST['VERIF_RMKS']));
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

    $APPROVED_BY = $_SESSION['UPR_USER_ID'];
    $APPROVAL_DATE = GetCurrentDateTime();
    $APPROVAL_RMKS = $VERIF_RMKS;
    $SHARES_APPLN_STATUS = "REJECTED";

    # ... Updating the role id
    $q2 = "UPDATE shares_appln_requests 
              SET APPROVED_BY='$APPROVED_BY' 
                 ,APPROVAL_DATE='$APPROVAL_DATE' 
                 ,APPROVAL_RMKS='$APPROVAL_RMKS' 
                 ,SHARES_APPLN_STATUS='$SHARES_APPLN_STATUS' 
           WHERE SHARES_APPLN_REF='$SHARES_APPLN_REF'";
    $update_response = ExecuteEntityUpdate($q2);
    if ($update_response=="EXECUTED") {

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
      $MSG_TYPE = "BUY SHARES APPLICATION REJECTION";
      $RECIPIENT_EMAILS = $FP_EMAIL;
      $EMAIL_MESSAGE = "Dear ".$FP_NAME."<br>"
                      ."Your buy shares application has been <b>$SHARES_APPLN_STATUS</b>. Below are the details;<br>"
                      ."-------------------------------------------------------------------------------------------------<br>"
                      ."<b>APPLN REF:</b> <i>".$SHARES_APPLN_REF."</i><br>"
                      ."<b>REMARKS FROM MANAGEMENT:</b><br> "
                      ."<i>".$APPROVAL_RMKS."</i><br>"
                      ."-------------------------------------------------------------------------------------------------<br>"
                      ."<br/>"
                      ."Regards<br>"
                      ."Management<br>"
                      ."<i></i>";
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
      $ENTITY_TYPE = "BUY_SHARES_APPLN";
      $ENTITY_ID_AFFECTED = $SHARES_APPLN_REF;
      $EVENT = "REJECT";
      $EVENT_OPERATION = "REJECT_BUY_SHARES_APPLN";
      $EVENT_RELATION = "shares_appln_requests";
      $EVENT_RELATION_NO = $SHARES_APPLN_REF;
      $OTHER_DETAILS = $SHARES_APPLN_REF."|".$APPROVAL_RMKS;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

      $alert_type = "ERROR";
      $alert_msg = "MESSAGE: Buy shares application has been rejected. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5; URL=shr-apprv");
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
    LoadDefaultCSSConfigurations("Pending Shares", $APP_SMALL_LOGO); 

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
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>

            <div class="x_panel">
              <div class="x_title">
                <a href="shr-apprv" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Buy Shares Application</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content"> 
                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td width="20%"><b>Shares Appln Ref</b></td><td colspan="3"><?php echo $SHARES_APPLN_REF; ?></td></tr>
                  <tr><td><b>Appln Submission Date</b></td><td colspan="3"><?php echo $APPLN_SUBMISSION_DATE; ?></td></tr>
                  <tr>
                      <td><b>Appln Verification Date</b></td>
                      <td width="16%"><?php echo $FIRST_HANDLED_ON; ?></td>
                      <td width="20%"><b>Verification Remarks</b></td>
                      <td><?php echo $FIRST_HANDLE_RMKS; ?></td>
                  </tr>
                  <tr>
                      <td><b>Appln Approval Date</b></td>
                      <td><?php echo $APPROVAL_DATE; ?></td>
                      <td><b>Appln Approval Remarks</b></td>
                      <td><?php echo $APPROVAL_RMKS; ?></td>
                  </tr>
                  <tr><td><b>Appln Status</b></td><td colspan="3"><?php echo $SHARES_APPLN_STATUS; ?></td></tr>
                </table>

        
                <form method="post" id="sjdjshw272SJSJ">
                  <input type="hidden" id="SHARES_APPLN_REF" name="SHARES_APPLN_REF" value="<?php echo $SHARES_APPLN_REF; ?>">
                  <input type="hidden" id="APPLN_CHANNEL" name="APPLN_CHANNEL" value="<?php echo $CHANNEL; ?>">
                  <input type="hidden" id="CUST_ID" name="CUST_ID" value="<?php echo $CUST_ID; ?>">
                  <input type="hidden" id="SHARES_PDT_ID" name="SHARES_PDT_ID" value="<?php echo $SHARES_PDT_ID; ?>">
                  <input type="hidden" id="SHARES_ACCT_ID_TO_CREDIT" name="SHARES_ACCT_ID_TO_CREDIT" value="<?php echo $SHARES_ACCT_ID_TO_CREDIT; ?>">
                  <input type="hidden" id="SHARES_REQUESTED" name="SHARES_REQUESTED" value="<?php echo $SHARES_REQUESTED; ?>">
                  <input type="hidden" id="SHARES_UNIT_PRICE" name="SHARES_UNIT_PRICE" value="<?php echo $SHARES_UNIT_PRICE; ?>">
                  <input type="hidden" id="SVGS_ACCT_CRNCY" name="SVGS_ACCT_CRNCY" value="<?php echo $SVGS_ACCT_CRNCY; ?>">
                  <input type="hidden" id="SVGS_ACCT_ID_TO_DEBIT" name="SVGS_ACCT_ID_TO_DEBIT" value="<?php echo $SVGS_ACCT_ID_TO_DEBIT; ?>">
                  <input type="hidden" id="SVGS_CUST_ID" name="SVGS_CUST_ID" value="<?php echo $SVGS_CUST_ID; ?>">

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Shares Cust Names</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_CUST_NAME; ?>">
                  </div>
                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Shares Account</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_ACCT_NUM; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Current Shares Owned</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_OWNED; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Share Product Name</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_PDT_NAME; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Share Unit Price</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SHARES_UNIT_PRICE); ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Max Shares Per Client</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_MAX_SHR; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Current Share Value</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SHRS_VALUE); ?>">
                  </div>


                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Maximum Shares Purchasable</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo ($SHARES_MAX_SHR - $SHARES_OWNED); ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Number of shares to buy</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_REQUESTED; ?>">
                  </div>


                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account to Debitt</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account Balance</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SVGS_ACCT_BAL); ?>">
                  </div>

                  
                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Amount to be debitted</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SHARES_REQUESTED * $SHARES_UNIT_PRICE); ?>">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Enter Auth TAN:</label> 
                    <button type="submit" class="btn btn-warning btn-xs" name="btn_gen_tan">Generate TAN</button>
                    <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Approval Remarks</label>
                    <textarea class="form-control" rows="3" id="VERIF_RMKS" name="VERIF_RMKS"></textarea>
                  </div>


                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <button type="submit" class="btn btn-danger" name="btn_reject_appln">Reject</button>
                    <button type="submit" class="btn btn-success" name="btn_apprv_appln">Approve</button>
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
