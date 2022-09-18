<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Handle Form Dat
$TXT_ACCT = mysql_real_escape_string(trim($_SESSION['TXT_ACCT']));
$TO_CLIENT_ID = mysql_real_escape_string(trim($_SESSION['TO_CLIENT_ID']));
$TO_ACCT_ID = mysql_real_escape_string(trim($_SESSION['TO_ACCT_ID']));
$TO_ACCT_NUM = mysql_real_escape_string(trim($_SESSION['TO_ACCT_NUM']));
$TO_ACCT_CRNCY = mysql_real_escape_string(trim($_SESSION['TO_ACCT_CRNCY']));
$TO_ACCT_NAME = mysql_real_escape_string(trim($_SESSION['TO_ACCT_NAME']));
$SVNGS_ACCT_BAL = mysql_real_escape_string(trim($_SESSION['SVNGS_ACCT_BAL']));
$TRANSFER_AMT = mysql_real_escape_string(trim($_SESSION['TRANSFER_AMT']));
$REASON = mysql_real_escape_string(trim($_SESSION['REASON']));


# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "STAFF_USER";
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $EVENT_TYPE = "DIRECT SAVINGS WITHDRAW APPLN APPROVAL";
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
      $MSG_TYPE = "DIRECT_SAVINGS_WITHDRAW_APPROVAL_TAN";
      $RECIPIENT_EMAILS = $fff_email;
      $EMAIL_MESSAGE = "Dear ".$fff_name."<br>"
                      ."This is your authentication TAN: <b>".$TAN."</b>";
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

# ... F0000002: PERFORM TRANSACTION ..............................................................................#
if (isset($_POST['btn_submit_appln'])) {

  $TXT_ACCT = mysql_real_escape_string(trim($_SESSION['TXT_ACCT']));
  $TO_CLIENT_ID = mysql_real_escape_string(trim($_SESSION['TO_CLIENT_ID']));
  $TO_ACCT_ID = mysql_real_escape_string(trim($_SESSION['TO_ACCT_ID']));
  $TO_ACCT_NUM = mysql_real_escape_string(trim($_SESSION['TO_ACCT_NUM']));
  $TO_ACCT_CRNCY = mysql_real_escape_string(trim($_SESSION['TO_ACCT_CRNCY']));
  $TO_ACCT_NAME = mysql_real_escape_string(trim($_SESSION['TO_ACCT_NAME']));
  $SVNGS_ACCT_BAL = mysql_real_escape_string(trim($_SESSION['SVNGS_ACCT_BAL']));
  $TRANSFER_AMT = mysql_real_escape_string(trim($_SESSION['TRANSFER_AMT']));
  $REASON = mysql_real_escape_string(trim($_SESSION['REASON']));
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

    # ... 00: Create Withdraw record in Application Database ... ... .. ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
    $DB_CUST_ID = $TO_CLIENT_ID;
    $DB_SVGS_ACCT_ID_TO_DEBIT = $TO_ACCT_ID;
    $DB_RQSTD_AMT = $TRANSFER_AMT;
    $DB_REASON = $REASON;
    $DB_APPLN_SUBMISSION_DATE = GetCurrentDateTime();
    $DB_CUST_FIN_INST_ID = "";

    $q = "INSERT INTO svgs_withdraw_requests(CUST_ID,SVGS_ACCT_ID_TO_DEBIT,RQSTD_AMT,REASON,APPLN_SUBMISSION_DATE,CUST_FIN_INST_ID) VALUES('$DB_CUST_ID','$DB_SVGS_ACCT_ID_TO_DEBIT','$DB_RQSTD_AMT','$DB_REASON','$DB_APPLN_SUBMISSION_DATE','$DB_CUST_FIN_INST_ID')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    # ... Process Entity System ID (Role ID)
    $id_prefix = "DSW";
    $id_len = 20;
    $id_record_id = $RECORD_ID;
    $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
    $WITHDRAW_REF = $ENTITY_ID;

    # ... Updating the role id
    $q2 = "UPDATE svgs_withdraw_requests SET WITHDRAW_REF='$WITHDRAW_REF' WHERE RECORD_ID='$RECORD_ID'";
    $update_response = ExecuteEntityUpdate($q2);


    # ... 01: Executing Main Txn ... ... .. ... ... ... ... ... ... ... ... ... ... ... ... ... ...  ...  ... ... ... ...  ... ...# 
    $from_client_id = $TO_CLIENT_ID;
    $from_account_Id = $TO_ACCT_ID;
    $to_client_id = $DIRECT_WITHDRAW_DEPOSIT["WITHDRAW_CUST_ID"];
    $to_account_Id = $DIRECT_WITHDRAW_DEPOSIT["WITHDRAW_ACCT_ID"];
    $transaction_date = date('d F Y', strtotime( date("ymd",time()) ));
    $transfer_amount = $TRANSFER_AMT;
    $narration = "Direct savings withdraw. REF: ".$WITHDRAW_REF;
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

      # ... 03: Update the Database
      $APPROVED_AMT = $TRANSFER_AMT;
      $APPROVED_BY = $_SESSION['UPR_USER_ID'];
      $APPROVAL_DATE = GetCurrentDateTime();
      $APPROVAL_RMKS = "Direct Savings Withdraw";
      $SVGS_APPLN_STATUS = "APPROVED";

      # ... Updating the role id
      $q2 = "UPDATE svgs_withdraw_requests 
                SET CORE_TXN_ID='$CORE_TXN_TRANSFER_ID'
                   ,APPROVED_AMT='$APPROVED_AMT' 
                   ,APPROVED_BY='$APPROVED_BY' 
                   ,APPROVAL_DATE='$APPROVAL_DATE' 
                   ,APPROVAL_RMKS='$APPROVAL_RMKS' 
                   ,SVGS_APPLN_STATUS='$SVGS_APPLN_STATUS' 
             WHERE WITHDRAW_REF='$WITHDRAW_REF'";
      $update_response = ExecuteEntityUpdate($q2);
      if ($update_response=="EXECUTED") {

        # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
        $ENTITY_ID = $_SESSION['UPR_USER_ID'];
        $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
        ExecuteEntityUpdate($qww);

        # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "DIRECT_SAVINGS_WITHDRAW";
        $ENTITY_ID_AFFECTED = $WITHDRAW_REF;
        $EVENT = "INIT_AND_APPROVAL";
        $EVENT_OPERATION = "INITIATE AND APPROVE TRANSACTION";
        $EVENT_RELATION = "svgs_withdraw_requests";
        $EVENT_RELATION_NO = $WITHDRAW_REF;
        $OTHER_DETAILS = $WITHDRAW_REF."|".$APPROVAL_RMKS;
        $INVOKER_ID = $_SESSION['UPR_USER_ID'];
        LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                       $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

        $alert_type = "SUCCESS";
        $alert_msg = "MESSAGE: Direct Savings Withdraw has been initiated and executed successfully. Refreshing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
        header("Refresh:5; URL=sw-direct");
      }
    } # ...IFF..ELSE

  }
}



?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Make Direct Withdraw", $APP_SMALL_LOGO); 

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
            <div id="MSG_AREA" align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <h2>Make Direct Withdraw of Funds from Customer</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         

                 <form method="post" id="dmsEEAjj">
                  
                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Account No:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TXT_ACCT; ?>">
                  </div>

                  <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                    <label>Currency:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TO_ACCT_CRNCY; ?>">
                  </div>

                  <div class="col-md-5 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Account Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TO_ACCT_NAME; ?>">
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Account Balance:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SVNGS_ACCT_BAL); ?>">
                  </div>


                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Amount to Withdraw:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($TRANSFER_AMT); ?>">
                  </div>


                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Narration</label>
                    <textarea class="form-control" rows="3" disabled=""><?php echo $REASON; ?></textarea>
                  </div>

                  
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Enter Transaction TAN:</label> 
                    <button type="submit" class="btn btn-warning btn-xs" name="btn_gen_tan">Generate TAN</button>
                    <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    
                    <button type="submit" class="btn btn-success" name="btn_submit_appln">Submit Appln Details</button>
                     
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
