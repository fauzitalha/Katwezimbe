<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Details
$TRANSFER_REF = mysql_real_escape_string(trim($_GET['k']));
$st = array();
$st = FetchSavingsTransferApplnsById($TRANSFER_REF);
$RECORD_ID = $st['RECORD_ID'];
$CUST_ID = $st['CUST_ID'];
$SVGS_ACCT_ID_TO_DEBIT = $st['SVGS_ACCT_ID_TO_DEBIT'];
$TRANSFER_AMT = $st['TRANSFER_AMT'];
$SVGS_ACCT_ID_TO_CREDIT = $st['SVGS_ACCT_ID_TO_CREDIT'];
$REASON = $st['REASON'];
$APPLN_SUBMISSION_DATE = $st['APPLN_SUBMISSION_DATE'];
$SVGS_HANDLER_USER_ID = $st['SVGS_HANDLER_USER_ID'];
$FIRST_HANDLED_ON = $st['FIRST_HANDLED_ON'];
$FIRST_HANDLE_RMKS = $st['FIRST_HANDLE_RMKS'];
$COMMITTEE_FLG = $st['COMMITTEE_FLG'];
$COMMITTEE_HANDLER_USER_ID = $st['COMMITTEE_HANDLER_USER_ID'];
$COMMITTEE_STATUS = $st['COMMITTEE_STATUS'];
$COMMITTEE_STATUS_DATE = $st['COMMITTEE_STATUS_DATE'];
$COMMITTEE_RMKS = $st['COMMITTEE_RMKS'];
$APPROVED_AMT = $st['APPROVED_AMT'];
$APPROVED_BY = $st['APPROVED_BY'];
$APPROVAL_DATE = $st['APPROVAL_DATE'];
$APPROVAL_RMKS = $st['APPROVAL_RMKS'];
$PROC_MODE = $st['PROC_MODE'];
$PROC_BATCH_NO = $st['PROC_BATCH_NO'];
$CORE_TXN_ID = $st['CORE_TXN_ID'];
$TRANSFER_APPLN_STATUS = $st['TRANSFER_APPLN_STATUS'];

# ... 01: Get FROM Client Name .........................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$FROM_SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];
$FROM_SVGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];
$FROM_CORE_CUST_ID = $CORE_RESP["clientId"];
$FROM_CORE_CUST_NAME = $CORE_RESP["clientName"];


# ... 02: Get To Client Name .........................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$TO_SVGS_ACCT_NUM_TO_CREDIT = $CORE_RESP["accountNo"];
$TO_SVGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];
$TO_CORE_CUST_ID = $CORE_RESP["clientId"];
$TO_CORE_CUST_NAME = $CORE_RESP["clientName"];


# ... 03: LOAD CUSTOMER DETAILS .....................................................................................#
$cstmr = array();
$cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
$CUST_ID = $cstmr['CUST_ID'];
$CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
$CUST_EMAIL = $cstmr['CUST_EMAIL'];
$CUST_PHONE = $cstmr['CUST_PHONE'];

# ... Decrypt Email & Phone
$EMAIL = AES256::decrypt($CUST_EMAIL);
$PHONE = AES256::decrypt($CUST_PHONE);
$_SESSION['FP_NAME'] = $FROM_CORE_CUST_NAME;
$_SESSION['FP_EMAIL'] = $EMAIL;
$_SESSION['FP_PHONE'] = $PHONE;

# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "STAFF_USER";
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $EVENT_TYPE = "SAVINGS TRANSFER APPLN APPROVAL";
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
      $MSG_TYPE = "SAVINGS_WITHDRAW_TAN";
      $RECIPIENT_EMAILS = $fff_email;
      $EMAIL_MESSAGE = "Dear ".$fff_name."<br>"
                      ."This is your savings transfer auth TAN is: <b>".$TAN."</b>";
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

  $TRANSFER_REF = mysql_real_escape_string(trim($_POST['TRANSFER_REF']));
  $CUST_ID = mysql_real_escape_string(trim($_POST['CUST_ID']));
  $TRANSFER_AMT = mysql_real_escape_string(trim($_POST['TRANSFER_AMT']));
  $FROM_CORE_CUST_ID = mysql_real_escape_string(trim($_POST['FROM_CORE_CUST_ID']));
  $FROM_SVGS_ACCT_ID_TO_DEBIT = mysql_real_escape_string(trim($_POST['FROM_SVGS_ACCT_ID_TO_DEBIT']));
  $TO_CORE_CUST_ID = mysql_real_escape_string(trim($_POST['TO_CORE_CUST_ID']));
  $TO_SVGS_ACCT_ID_TO_CREDIT = mysql_real_escape_string(trim($_POST['TO_SVGS_ACCT_ID_TO_CREDIT']));
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

    # ... 00: Appln Configuration Information 
    $CHRG_APPLN = $APPLN_CHRGING['SVGS_APPLN_TRANSFER_CHRG_FLG'];
    $CHRG_TXN_TYPE = $APPLN_CHRGING['SVGS_APPLN_TRANSFER_CHRG_EVENT_ID'];

    # ... 01: Executing Main Txn 
    $from_client_id = $FROM_CORE_CUST_ID;
    $from_account_Id = $FROM_SVGS_ACCT_ID_TO_DEBIT;
    $to_client_id = $TO_CORE_CUST_ID;
    $to_account_Id = $TO_SVGS_ACCT_ID_TO_CREDIT;
    $transaction_date = date('d F Y', strtotime( date("ymd",time()) ));
    $transfer_amount = $TRANSFER_AMT;
    $narration = "SAVINGS TRANSFER. REF: ".$TRANSFER_REF;
    $TRANSFER_TXN_MSG = BuildTransferTxnMessage($from_client_id, $from_account_Id, $to_client_id, $to_account_Id, $transaction_date, $transfer_amount, $narration); 

    $response_msg = PerformFundsTransfer($TRANSFER_TXN_MSG, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];


    if (!isset($CORE_RESP["resourceId"])) {
      # ... Send System Response
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["errors"][0]["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["resourceId"])){

      $CORE_TXN_TRANSFER_ID = $CORE_RESP["resourceId"];

       # ... 02: Executing Appln Charges
      if ($CHRG_APPLN=="YES") {
        $DEBIT_ACCT_ID = $SVGS_ACCT_ID_TO_DEBIT;
        $TXN_AMT = $TRANSFER_AMT;
        $APPLN_REF = $TRANSFER_REF;
        $CHRG_CODE = $CHRG_TXN_TYPE;

        $CHRG_LIST = array();
        $CHRG_LIST = FetchTransactionChargesByTranTypeId($CHRG_CODE);
        for ($x=0; $x < sizeof($CHRG_LIST); $x++) { 
          $tt = array();
          $tt = $CHRG_LIST[$x];
          $TRAN_CHRG_ID = $tt['TRAN_CHRG_ID'];
          $TRAN_CHRG_NAME = $tt['TRAN_CHRG_NAME'];
          $TRAN_CHRG_TYPE = $tt['TRAN_CHRG_TYPE'];
          $COMM_CORE_CR_ACCT_ID = $tt['CORE_CR_ACCT_ID'];
          $TRAN_NRRTN_PREFIX = $tt['TRAN_NRRTN_PREFIX'];

          # ... Get COMM cust ID for the commission acct
          $response_msg = FetchSavingsAccountDetailsById($COMM_CORE_CR_ACCT_ID, $MIFOS_CONN_DETAILS);
          $CONN_FLG = $response_msg["CONN_FLG"];
          $CORE_RESP = $response_msg["CORE_RESP"];
          $COMM_CORE_CR_CUST_ID = $CORE_RESP["clientId"];


          # ... Deduce Charge Fee Amount
          $CHRG_TXN_FEE = DeduceChargeFee($TXN_AMT, $TRAN_CHRG_ID, $TRAN_CHRG_TYPE);
          

          # ... Excise Duty
          $EXCISE_DUTY_PERCENT = $EXCISE_DUTY["EXCISE_DUTY_PERCENT"];
          $EXCISE_DUTY_CUST_ID = $EXCISE_DUTY["EXCISE_DUTY_CUST_ID"];
          $EXCISE_DUTY_ACCT_ID = $EXCISE_DUTY["EXCISE_DUTY_ACCT_ID"];
          $EXCISE_CHRG_TXN_FEE = (($EXCISE_DUTY_PERCENT/100)*$CHRG_TXN_FEE);

          # ... 000: Build & Pass Charge
          $from_client_id1 = $FROM_CORE_CUST_ID;
          $from_account_Id1 = $FROM_SVGS_ACCT_ID_TO_DEBIT;
          $to_client_id1 = $COMM_CORE_CR_CUST_ID;
          $to_account_Id1 = $COMM_CORE_CR_ACCT_ID;
          $transaction_date1 = date('d F Y', strtotime( date("ymd",time()) ));
          $transfer_amount1 = $CHRG_TXN_FEE;
          $narration1 = $TRAN_NRRTN_PREFIX."_REF: ".$APPLN_REF;
          $CHRG_TRANSFER_TXN_MSG = BuildTransferTxnMessage($from_client_id1, $from_account_Id1, $to_client_id1, $to_account_Id1, $transaction_date1, $transfer_amount1, $narration1);
          $response_msg = PerformFundsTransfer($CHRG_TRANSFER_TXN_MSG, $MIFOS_CONN_DETAILS);
          $CONN_FLG = $response_msg["CONN_FLG"];
          $CORE_RESP = $response_msg["CORE_RESP"];

          # ... 111: Build & Pass Excise Duty
          $from_client_id2 = $FROM_CORE_CUST_ID;
          $from_account_Id2 = $FROM_SVGS_ACCT_ID_TO_DEBIT;
          $to_client_id2 = $EXCISE_DUTY_CUST_ID;
          $to_account_Id2 = $EXCISE_DUTY_ACCT_ID;
          $transaction_date2 = date('d F Y', strtotime( date("ymd",time()) ));
          $transfer_amount2 = $EXCISE_CHRG_TXN_FEE;
          $narration2 = "EXCISE_DUTY. REF: ".$APPLN_REF;
          $EXCISE_TRANSFER_TXN_MSG = BuildTransferTxnMessage($from_client_id2, $from_account_Id2, $to_client_id2, $to_account_Id2, $transaction_date2, $transfer_amount2, $narration2);
          PerformFundsTransfer($EXCISE_TRANSFER_TXN_MSG, $MIFOS_CONN_DETAILS);
        } # ... END..LOOP
      } # ... IFF

      # ... 03: Update the Database
      $APPROVED_AMT = $TRANSFER_AMT;
      $APPROVED_BY = $_SESSION['UPR_USER_ID'];
      $APPROVAL_DATE = GetCurrentDateTime();
      $APPROVAL_RMKS = $VERIF_RMKS;
      $TRANSFER_APPLN_STATUS = "APPROVED";

      # ... Updating the role id
      $q2 = "UPDATE svgs_transfer_requests
                SET CORE_TXN_ID='$CORE_TXN_TRANSFER_ID'
                   ,APPROVED_AMT='$APPROVED_AMT' 
                   ,APPROVED_BY='$APPROVED_BY' 
                   ,APPROVAL_DATE='$APPROVAL_DATE' 
                   ,APPROVAL_RMKS='$APPROVAL_RMKS' 
                   ,TRANSFER_APPLN_STATUS='$TRANSFER_APPLN_STATUS' 
             WHERE TRANSFER_REF='$TRANSFER_REF'";
      $update_response = ExecuteEntityUpdate($q2);
      if ($update_response=="EXECUTED") {

        # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
        $INIT_CHANNEL = "WEB";
        $MSG_TYPE = "SAVINGS TRANSFER APPROVAL";
        $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
        $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                        ."Your savings application has been <b>$TRANSFER_APPLN_STATUS</b>. Below are the details;<br>"
                        ."-------------------------------------------------------------------------------------------------<br>"
                        ."<b>APPLN REF:</b> <i>".$TRANSFER_REF."</i><br>"
                        ."<b>AMOUNT:</b> <i>".number_format($APPROVED_AMT)."</i><br>"
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
        $ENTITY_TYPE = "SAVINGS_TRANSFER_APPLN";
        $ENTITY_ID_AFFECTED = $TRANSFER_REF;
        $EVENT = "APPROVAL";
        $EVENT_OPERATION = "VERIFY_SAVINGS_WITHDRAW_APPLN";
        $EVENT_RELATION = "svgs_withdraw_requests";
        $EVENT_RELATION_NO = $TRANSFER_REF;
        $OTHER_DETAILS = $TRANSFER_REF."|".$APPROVAL_RMKS;
        $INVOKER_ID = $_SESSION['UPR_USER_ID'];
        LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                       $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);
      
        $alert_type = "SUCCESS";
        $alert_msg = "MESSAGE: Application has been approved successfully. Refreshing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
        header("Refresh:5; URL=st-apprv");
      }
    } # ...IFF..ELSE

  }
}

# ... F0000003: REJECTION APPLN .....................................................................................#
if (isset($_POST['btn_reject_appln'])) {

  $TRANSFER_REF = mysql_real_escape_string(trim($_POST['TRANSFER_REF']));
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
    $TRANSFER_APPLN_STATUS = "REJECTED";

    # ... Updating the role id
    $q2 = "UPDATE svgs_transfer_requests
              SET APPROVED_BY='$APPROVED_BY' 
                 ,APPROVAL_DATE='$APPROVAL_DATE' 
                 ,APPROVAL_RMKS='$APPROVAL_RMKS' 
                 ,TRANSFER_APPLN_STATUS='$TRANSFER_APPLN_STATUS' 
           WHERE TRANSFER_REF='$TRANSFER_REF'";
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
      $MSG_TYPE = "SAVINGS WITHDRAW APPLICATION REJECTION";
      $RECIPIENT_EMAILS = $FP_EMAIL;
      $EMAIL_MESSAGE = "Dear ".$FP_NAME."<br>"
                      ."Your savings transfer application has been <b>$TRANSFER_APPLN_STATUS</b>. Below are the details;<br>"
                      ."-------------------------------------------------------------------------------------------------<br>"
                      ."<b>APPLN REF:</b> <i>".$TRANSFER_REF."</i><br>"
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
      $ENTITY_TYPE = "SAVINGS_TRANSFER_APPLN";
      $ENTITY_ID_AFFECTED = $TRANSFER_REF;
      $EVENT = "REJECT";
      $EVENT_OPERATION = "REJECT_SAVINGS_TRANSFER_APPLN";
      $EVENT_RELATION = "svgs_transfer_requests";
      $EVENT_RELATION_NO = $TRANSFER_REF;
      $OTHER_DETAILS = $TRANSFER_REF."|".$FIRST_HANDLE_RMKS;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "ERROR";
      $alert_msg = "MESSAGE: Transfer Application has been rejected. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5; URL=st-queue");
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
    LoadDefaultCSSConfigurations("Pending Internal Transfers", $APP_SMALL_LOGO); 

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
                <a href="st-apprv" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Pending Internal Transfers</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td width="20%"><b>Transfer Appln Ref</b></td><td colspan="3"><?php echo $TRANSFER_REF; ?></td></tr>
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
                  <tr><td><b>Appln Status</b></td><td colspan="3"><?php echo $TRANSFER_APPLN_STATUS; ?></td></tr>
                </table>

                <form method="post" id="dgdhasjERTYDGHDH">
                  <input type="hidden" id="TRANSFER_REF" name="TRANSFER_REF" value="<?php echo $TRANSFER_REF; ?>">
                  <input type="hidden" id="CUST_ID" name="CUST_ID" value="<?php echo $CUST_ID; ?>">
                  <input type="hidden" id="TRANSFER_AMT" name="TRANSFER_AMT" value="<?php echo $TRANSFER_AMT; ?>">
                  <input type="hidden" id="FROM_CORE_CUST_ID" name="FROM_CORE_CUST_ID" value="<?php echo $FROM_CORE_CUST_ID; ?>">
                  <input type="hidden" id="FROM_SVGS_ACCT_ID_TO_DEBIT" name="FROM_SVGS_ACCT_ID_TO_DEBIT" value="<?php echo $SVGS_ACCT_ID_TO_DEBIT; ?>">
                  <input type="hidden" id="TO_CORE_CUST_ID" name="TO_CORE_CUST_ID" value="<?php echo $TO_CORE_CUST_ID; ?>">
                  <input type="hidden" id="TO_SVGS_ACCT_ID_TO_CREDIT" name="TO_SVGS_ACCT_ID_TO_CREDIT" value="<?php echo $SVGS_ACCT_ID_TO_CREDIT; ?>">

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>From Customer Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $FROM_CORE_CUST_NAME; ?>">
                  </div>

                  <div class="col-md-3 col-sm-6 col-xs-6 form-group">
                    <label>From Savings Acct:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $FROM_SVGS_ACCT_NUM_TO_DEBIT; ?>">
                  </div>

                  <div class="col-md-3 col-sm-6 col-xs-6 form-group">
                    <label>From Savings Acct Bal:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($FROM_SVGS_ACCT_BAL); ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>To Customer Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TO_CORE_CUST_NAME; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>To Savings Acct:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TO_SVGS_ACCT_NUM_TO_CREDIT; ?>">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Transfer Amount:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($TRANSFER_AMT); ?>">
                  </div>



                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Transfer Purpose</label>
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
