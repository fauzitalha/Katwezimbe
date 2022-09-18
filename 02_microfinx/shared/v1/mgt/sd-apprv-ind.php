<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");


# ... Receiving Details
$DEPOSIT_REF = mysql_real_escape_string(trim($_GET['k']));
$sd = array();
$sd = FetchSavingsDepositApplnsById($DEPOSIT_REF);
$RECORD_ID = $sd['RECORD_ID'];
$DEPOSIT_REF = $sd['DEPOSIT_REF'];
$CUST_ID = $sd['CUST_ID'];
$SVGS_ACCT_ID_TO_CREDIT = $sd['SVGS_ACCT_ID_TO_CREDIT'];
$AMOUNT_BANKED = $sd['AMOUNT_BANKED'];
$REASON = $sd['REASON'];
$BANK_ID = $sd['BANK_ID'];
$BANK_INST_ACCT_NO = $sd['BANK_INST_ACCT_NO'];
$BANK_INST_ACCT_NAME = $sd['BANK_INST_ACCT_NAME'];
$BANK_RECEIPT_REF = $sd['BANK_RECEIPT_REF'];
$BANK_RECEIPT_ATTCHMT = $sd['BANK_RECEIPT_ATTCHMT'];
$RQST_DATE = $sd['RQST_DATE'];
$HANDLED_BY = $sd['HANDLED_BY'];
$HANDLED_ON = $sd['HANDLED_ON'];
$HANDLER_RMKS = $sd['HANDLER_RMKS'];
$APPRVD_BY = $sd['APPRVD_BY'];
$APPRVL_DATE = $sd['APPRVL_DATE'];
$APPRVL_RMKS = $sd['APPRVL_RMKS'];
$CORE_TXN_ID = $sd['CORE_TXN_ID'];
$RQST_STATUS = $sd['RQST_STATUS'];

$RECEIPT_LINK_BASEPATH = GetSystemParameter("DEPOSIT_RECEIPT_LINK")."/".$_SESSION['ORG_CODE'];
$RECEIPT_LINK = $RECEIPT_LINK_BASEPATH."/".$DEPOSIT_REF."/".$BANK_RECEIPT_ATTCHMT;

//echo $BANK_ID;
$fin = array();
$fin = FetchFinInstitutionsById($BANK_ID);
$FIN_INST_ID = $fin['FIN_INST_ID'];
$FIN_INST_NAME = $fin['FIN_INST_NAME'];

# ... 01: Get Deposit Client Name .....................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$DEP_SVGS_ACCT_NUM_TO_CREDIT = $CORE_RESP["accountNo"];
$DEP_SVGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];
//echo "<pre>".print_r($CORE_RESP,true)."</pre>";

$DEP_CORE_CUST_ID = "";
$DEP_CORE_CUST_NAME = "";
if (isset($CORE_RESP["clientId"]) && isset($CORE_RESP["clientName"])) {
  $DEP_CORE_CUST_ID = $CORE_RESP["clientId"];
  $DEP_CORE_CUST_NAME = $CORE_RESP["clientName"];
} elseif (isset($CORE_RESP["groupId"]) && isset($CORE_RESP["groupName"])) {
  $DEP_CORE_CUST_ID = $CORE_RESP["groupId"];
  $DEP_CORE_CUST_NAME = $CORE_RESP["groupName"];
}



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
$_SESSION['FP_NAME'] = $DEP_CORE_CUST_NAME;
$_SESSION['FP_EMAIL'] = $EMAIL;
$_SESSION['FP_PHONE'] = $PHONE;


# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "STAFF_USER";
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $EVENT_TYPE = "SAVINGS DEPOSIT APPLN APPROVAL";
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
                      ."This is your savings deposit auth TAN is: <b>".$TAN."</b>";
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

  $DEPOSIT_REF = mysql_real_escape_string(trim($_POST['DEPOSIT_REF']));
  $CUST_ID = mysql_real_escape_string(trim($_POST['CUST_ID']));
  $AMOUNT_BANKED = mysql_real_escape_string(trim($_POST['AMOUNT_BANKED']));
  $DEP_CORE_CUST_ID = mysql_real_escape_string(trim($_POST['DEP_CORE_CUST_ID']));
  $DEP_SVGS_ACCT_ID_TO_CREDIT = mysql_real_escape_string(trim($_POST['DEP_SVGS_ACCT_ID_TO_CREDIT']));
  $VERIF_RMKS = mysql_real_escape_string(trim($_POST['VERIF_RMKS']));
  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));
  $FIN_BANK_ID = mysql_real_escape_string(trim($_POST['FIN_BANK_ID']));

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

    # ... 01:  Build Transaction request message
    $fin = array();
    $fin = FetchFinInstitutionsById($FIN_BANK_ID);
		$fin_FIN_INST_NAME = $fin['FIN_INST_NAME'];	
		$fin_SORTCODE = $fin['SORTCODE'];	
		$fin_SWIFT_CODE = $fin['SWIFT_CODE'];
    $fin_BANK_CODE = $fin['BANK_CODE'];	
    $fin_ORG_ACCT_NUM = $fin['ORG_ACCT_NUM'];
		$fin_MIFOS_PYMT_TYPE_ID = $fin['MIFOS_PYMT_TYPE_ID'];	
    $fin_MIFOS_GL_ACCT_ID = $fin['MIFOS_GL_ACCT_ID'];	

    $t_transactionDate =  date('d F Y', strtotime( date("ymd",time()) ));
    $t_transactionAmount = $AMOUNT_BANKED;
    $t_paymentTypeId = $fin_MIFOS_PYMT_TYPE_ID;
    $t_accountNumber = $fin_ORG_ACCT_NUM."-".$fin_BANK_CODE;
    $t_checkNumber = "";
    $t_routingCode = $fin_SORTCODE;
    $t_receiptNumber = "SAVINGS DEPOSIT. REF: ".$DEPOSIT_REF;
    $t_bankNumber = $fin_FIN_INST_NAME;
    $DEPOSIT_TXN_MSG = BuildRawDepositRqstMsg($t_transactionDate, $t_transactionAmount, $t_paymentTypeId, $t_accountNumber, $t_checkNumber, $t_routingCode, $t_receiptNumber, $t_bankNumber);
    
    // ... execute withdrawal
    $response_msg = MakeDirectDepositTransaction($DEP_SVGS_ACCT_ID_TO_CREDIT, $DEPOSIT_TXN_MSG, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];

    if (!isset($CORE_RESP["resourceId"])) {
      # ... Send System Response
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["errors"][0]["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["resourceId"])){

      $CORE_TXN_TRANSFER_ID = $CORE_RESP["resourceId"];

      # ... 03: Update the Database
      $APPRVD_BY = $_SESSION['UPR_USER_ID'];
      $APPRVL_DATE = GetCurrentDateTime();
      $APPRVL_RMKS = $VERIF_RMKS;
      $RQST_STATUS = "APPROVED";

      # ... Updating the role id
      $q2 = "UPDATE svgs_deposit_requests 
                SET APPRVD_BY='$APPRVD_BY' 
                   ,APPRVL_DATE='$APPRVL_DATE' 
                   ,APPRVL_RMKS='$APPRVL_RMKS' 
                   ,RQST_STATUS='$RQST_STATUS' 
             WHERE DEPOSIT_REF='$DEPOSIT_REF'";
      $update_response = ExecuteEntityUpdate($q2);
      if ($update_response=="EXECUTED") {

        # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
        $INIT_CHANNEL = "WEB";
        $MSG_TYPE = "SAVINGS DEPOSIT APPROVAL";
        $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
        $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                        ."Your savings application has been <b>$RQST_STATUS</b>. Below are the details;<br>"
                        ."-------------------------------------------------------------------------------------------------<br>"
                        ."<b>APPLN REF:</b> <i>".$DEPOSIT_REF."</i><br>"
                        ."<b>AMOUNT:</b> <i>".number_format($AMOUNT_BANKED)."</i><br>"
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
        $ENTITY_TYPE = "SAVINGS_DEPOSIT_APPLN";
        $ENTITY_ID_AFFECTED = $DEPOSIT_REF;
        $EVENT = "APPROVAL";
        $EVENT_OPERATION = "APPROVE_SAVINGS_WITHDRAW_APPLN";
        $EVENT_RELATION = "svgs_deposit_requests";
        $EVENT_RELATION_NO = $DEPOSIT_REF;
        $OTHER_DETAILS = $DEPOSIT_REF."|".$APPRVL_RMKS;
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

  $DEPOSIT_REF = mysql_real_escape_string(trim($_POST['DEPOSIT_REF']));
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

    $APPRVD_BY = $_SESSION['UPR_USER_ID'];
    $APPRVL_DATE = GetCurrentDateTime();
    $APPRVL_RMKS = $VERIF_RMKS;
    $RQST_STATUS = "REJECTED";

    # ... Updating the role id
    $q2 = "UPDATE svgs_deposit_requests 
              SET APPRVD_BY='$APPRVD_BY' 
                 ,APPRVL_DATE='$APPRVL_DATE' 
                 ,APPRVL_RMKS='$APPRVL_RMKS' 
                 ,RQST_STATUS='$RQST_STATUS' 
           WHERE DEPOSIT_REF='$DEPOSIT_REF'";
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
      $MSG_TYPE = "SAVINGS DEPOSIT APPLICATION REJECTION";
      $RECIPIENT_EMAILS = $FP_EMAIL;
      $EMAIL_MESSAGE = "Dear ".$FP_NAME."<br>"
                      ."Your savings deposit application has been <b>$RQST_STATUS</b>. Below are the details;<br>"
                      ."-------------------------------------------------------------------------------------------------<br>"
                      ."<b>APPLN REF:</b> <i>".$DEPOSIT_REF."</i><br>"
                      ."<b>REMARKS FROM MANAGEMENT:</b><br> "
                      ."<i>".$HANDLER_RMKS."</i><br>"
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
      $ENTITY_TYPE = "SAVINGS_DEPOSIT_APPLN";
      $ENTITY_ID_AFFECTED = $DEPOSIT_REF;
      $EVENT = "REJECT";
      $EVENT_OPERATION = "REJECT_SAVINGS_DEPOSITS_APPLN";
      $EVENT_RELATION = "svgs_deposit_requests";
      $EVENT_RELATION_NO = $DEPOSIT_REF;
      $OTHER_DETAILS = $DEPOSIT_REF."|".$HANDLER_RMKS;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "ERROR";
      $alert_msg = "MESSAGE: Deposit Application has been rejected. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5; URL=sd-apprv");
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
    LoadDefaultCSSConfigurations("Main Control", $APP_SMALL_LOGO); 

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
                <a href="sd-queue" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Deposit Appln</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td width="20%"><b>Deposit Appln Ref</b></td><td colspan="3"><?php echo $DEPOSIT_REF; ?></td></tr>
                  <tr><td><b>Appln Submission Date</b></td><td colspan="3"><?php echo $RQST_DATE; ?></td></tr>
                  <tr>
                      <td><b>Appln Verification Date</b></td>
                      <td width="16%"><?php echo $HANDLED_ON; ?></td>
                      <td width="20%"><b>Verification Remarks</b></td>
                      <td><?php echo $HANDLER_RMKS; ?></td>
                  </tr>
                  <tr>
                      <td><b>Appln Approval Date</b></td>
                      <td><?php echo $APPRVL_DATE; ?></td>
                      <td><b>Appln Approval Remarks</b></td>
                      <td><?php echo $APPRVL_RMKS; ?></td>
                  </tr>
                  <tr><td><b>Appln Status</b></td><td colspan="3"><?php echo $RQST_STATUS; ?></td></tr>
                </table>

                <form method="post" id="dgdhasjERTYDGHDH">
                  <input type="hidden" id="DEPOSIT_REF" name="DEPOSIT_REF" value="<?php echo $DEPOSIT_REF; ?>">
                  <input type="hidden" id="CUST_ID" name="CUST_ID" value="<?php echo $CUST_ID; ?>">
                  <input type="hidden" id="AMOUNT_BANKED" name="AMOUNT_BANKED" value="<?php echo $AMOUNT_BANKED; ?>">
                  <input type="hidden" id="DEP_CORE_CUST_ID" name="DEP_CORE_CUST_ID" value="<?php echo $DEP_CORE_CUST_ID; ?>">
                  <input type="hidden" id="DEP_SVGS_ACCT_ID_TO_CREDIT" name="DEP_SVGS_ACCT_ID_TO_CREDIT" value="<?php echo $SVGS_ACCT_ID_TO_CREDIT; ?>">
                  <input type="hidden" id="FIN_BANK_ID" name="FIN_BANK_ID" value="<?php echo $BANK_ID; ?>">
           


                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Appln Deposit Reference:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $DEPOSIT_REF; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Appln Date:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $RQST_DATE; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account to Credit:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $DEP_SVGS_ACCT_NUM_TO_CREDIT; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Amount to be deposited:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($AMOUNT_BANKED); ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Funds were deposited from:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $FIN_INST_NAME; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Bank Account No:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $BANK_INST_ACCT_NO; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Bank Account Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $BANK_INST_ACCT_NAME; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Receipt Reference Number:</label><a class="btn btn-xs btn-info" href="<?php echo $RECEIPT_LINK ?>">View Deposit Receipt</a>
                    <input type="text" class="form-control" disabled="" value="<?php echo $BANK_RECEIPT_REF; ?>">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Deposit Narration</label>
                    <textarea class="form-control" rows="3" disabled=""><?php echo $REASON; ?></textarea>
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Enter Authorization TAN:</label> 
                    <button type="submit" class="btn btn-warning btn-xs" name="btn_gen_tan">Generate Auth TAN</button>
                    <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Approvale Remarks</label>
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
