<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# .... Receiving Data
$TXT_ACCT = $_SESSION['TXT_ACCT'];
$TO_ACCT_ID = $_SESSION['TO_ACCT_ID'];
$TO_ACCT_NUM = $_SESSION['TO_ACCT_NUM'];
$TO_ACCT_CRNCY = $_SESSION['TO_ACCT_CRNCY'];
$TO_ACCT_NAME = $_SESSION['TO_ACCT_NAME'];
$SVGS_ACCT_ID_TO_DEBIT = $_SESSION['SVGS_ACCT_ID_TO_DEBIT'];
$SVNGS_ACCT_BAL = $_SESSION['SVNGS_ACCT_BAL'];
$TRANSFER_AMT = $_SESSION['TRANSFER_AMT'];
$REASON = $_SESSION['REASON'];

# ... GET SAVINGS ACCT ID
$response_msg = FetchSavingsAcctById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];


# ... GENERATE TAN
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "CUSTOMER";
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
  $EVENT_TYPE = "SAVINGS TRANSFER APPLN";
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
      
      # ... DB INSERT
      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "TRANSACTION_TAN";
      $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
      $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                      ."This is your transaction TAN is: <b>".$TAN."</b>";
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


# ... SAVINGS APPLICATION SUBMISSION
if (isset($_POST['btn_submit_appln'])) {

  $TXT_ACCT = $_SESSION['TXT_ACCT'];
  $TXT_TO_ACCT_ID = $_SESSION['TO_ACCT_ID'];
  $TO_ACCT_NUM = $_SESSION['TO_ACCT_NUM'];
  $TO_ACCT_CRNCY = $_SESSION['TO_ACCT_CRNCY'];
  $TO_ACCT_NAME = $_SESSION['TO_ACCT_NAME'];
  $TXT_SVGS_ACCT_ID_TO_DEBIT = $_SESSION['SVGS_ACCT_ID_TO_DEBIT'];
  $SVGS_ACCT_NUM_TO_DEBIT = $_POST['SVGS_ACCT_NUM_TO_DEBIT'];
  $SVNGS_ACCT_BAL = $_SESSION['SVNGS_ACCT_BAL'];
  $TXT_TRANSFER_AMT = $_SESSION['TRANSFER_AMT'];
  $TXT_REASON = $_SESSION['REASON'];

  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));

  # ... 01: Validate Enterred TAN
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
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
    $CUST_ID = $_SESSION['CST_USR_ID'];
    $SVGS_ACCT_ID_TO_DEBIT = $TXT_SVGS_ACCT_ID_TO_DEBIT;
    $TRANSFER_AMT = $TXT_TRANSFER_AMT;
    $SVGS_ACCT_ID_TO_CREDIT = $TXT_TO_ACCT_ID;
    $REASON = $TXT_REASON;
    $APPLN_SUBMISSION_DATE = GetCurrentDateTime();

    $q = "INSERT INTO svgs_transfer_requests(CUST_ID,SVGS_ACCT_ID_TO_DEBIT,TRANSFER_AMT,SVGS_ACCT_ID_TO_CREDIT,REASON,APPLN_SUBMISSION_DATE) VALUES('$CUST_ID','$SVGS_ACCT_ID_TO_DEBIT','$TRANSFER_AMT','$SVGS_ACCT_ID_TO_CREDIT','$REASON','$APPLN_SUBMISSION_DATE')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    # ... Process Entity System ID (Role ID)
    $id_prefix = "STA";
    $id_len = 20;
    $id_record_id = $RECORD_ID;
    $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
    $TRANSFER_REF = $ENTITY_ID;

    # ... Updating the role id
    $q2 = "UPDATE svgs_transfer_requests SET TRANSFER_REF='$TRANSFER_REF' WHERE RECORD_ID='$RECORD_ID'";
    $update_response = ExecuteEntityUpdate($q2);
    if ($update_response=="EXECUTED") {

      # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "SAVINGS APPLICATION TRANSFER";
      $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
      $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                      ."Your savings transfer application has been received. Below are the details;<br>"
                      ."-------------------------------------------------------------------------------------------------<br>"
                      ."<b>APPLN REF:</b> <i>".$TRANSFER_REF."</i><br>"
                      ."<b>SOURCE SAVINGS ACCOUNT:</b> <i>".$SVGS_ACCT_NUM_TO_DEBIT."</i><br>"
                      ."<b>TRANSFER AMOUNT:</b> <i>".number_format($TRANSFER_AMT)."</i><br>"
                      ."<b>DESTINATION SAVINGS ACCOUNT:</b><i>".$TO_ACCT_NUM."</i><br>"
                      ."<b>DESTINATION SAVINGS ACCOUNT NAME:</b><i>".$TO_ACCT_NAME."</i><br>"
                      ."<b>TRANSFER REASON:</b><i>".$REASON."</i><br>"
                      ."-------------------------------------------------------------------------------------------------<br>"
                      ."<br/>"
                      ."Upon procesing this request, we shall inform you of the progress.<br>"
                      ."Regards<br>"
                      ."Management<br>"
                      ."<i></i>";
      $EMAIL_ATTACHMENT_PATH = "";
      $RECORD_DATE = GetCurrentDateTime();
      $EMAIL_STATUS = "NN";

      $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
      ExecuteEntityInsert($qqq);


      # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $ENTITY_ID = $_SESSION['CST_USR_ID'];
      $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
      ExecuteEntityUpdate($qww);


      # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "SAVINGS_TRANSFER_APPLN";
      $ENTITY_ID_AFFECTED = $_SESSION['CST_USR_ID'];
      $EVENT = "SUBMIT";
      $EVENT_OPERATION = "SUBMIT_SAVINGS_TRANSFER_APPLN";
      $EVENT_RELATION = "svgs_transfer_requests";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = $TRANSFER_REF;
      $INVOKER_ID = $_SESSION['CST_USR_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "SUCCESS: savings transfer application has been logged successfully. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5; URL=svg-appln-transfer");

    } # ... END..IFF



  } # ... END..IFF..ELSE
} # ... END..IFF


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Savings Transfer", $APP_SMALL_LOGO); 

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
              <?php SideNavBar($CUST_ID); ?>
            </div>
            <!-- /sidebar menu -->

          </div>
        </div>

        <!-- top navigation -->
        <?php TopNavBar($firstname); ?>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" id="MSG_AREA" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <a href="svg-appln-transfer" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Savings Transfer Appln</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         

                <form method="post" id="dmsjj">
                  <input type="hidden" id="SVGS_ACCT_NUM_TO_DEBIT" name="SVGS_ACCT_NUM_TO_DEBIT" value="<?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?>">
                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Destination Account No:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TO_ACCT_NUM; ?>">
                  </div>

                  <div class="col-md-2 col-sm-12 col-xs-12 form-group">
                    <label>Currency:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TO_ACCT_CRNCY; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Destination Account Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TO_ACCT_NAME; ?>">
                  </div>


                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Source Savings Account:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Available Source Account Balance:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SVNGS_ACCT_BAL); ?>">
                  </div>


                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Amount to Transfer:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($TRANSFER_AMT); ?>">
                  </div>


                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Transfer Narration</label>
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
