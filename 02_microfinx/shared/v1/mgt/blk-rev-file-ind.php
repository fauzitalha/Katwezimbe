<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$FILE_ID = $_SESSION['REV_FILE_ID'];
$file = array();
$file = FetchBulkFileById($FILE_ID);
$RECORD_ID = $file['RECORD_ID'];
$FILE_NAME = $file['FILE_NAME'];
$UPLOAD_REASON = $file['UPLOAD_REASON'];
$UPLOADED_BY = $file['UPLOADED_BY'];
$UPLOADED_ON = $file['UPLOADED_ON'];
$VERIFIED_RMKS = $file['VERIFIED_RMKS'];
$VERIFIED_BY = $file['VERIFIED_BY'];
$VERIFIED_ON = $file['VERIFIED_ON'];
$APPROVED_RMKS = $file['APPROVED_RMKS'];
$APPROVED_BY = $file['APPROVED_BY'];
$APPROVED_ON = $file['APPROVED_ON'];
$REVERSAL_FLG = $file['REVERSAL_FLG'];
$REV_INIT_RMKS = $file['REV_INIT_RMKS'];
$REV_INIT_BY = $file['REV_INIT_BY'];
$REV_INIT_ON = $file['REV_INIT_ON'];
$REV_APPROVED_RMKS = $file['REV_APPROVED_RMKS'];
$REV_APPROVED_BY = $file['REV_APPROVED_BY'];
$REV_APPROVED_ON = $file['REV_APPROVED_ON'];
$FILE_STATUS = $file['FILE_STATUS'];

# ... ... ... 01: Entry Counts ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID'";
$CNT_TOTAL_FILE = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE);
$Q_SUM_TOTAL_FILE = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID'";
$SUM_TOTAL_FILE = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE);

# ... ... ... 02: Entry Debit Counts ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_DEBITS = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D'";
$CNT_TOTAL_DEBITS = ReturnOneEntryFromDB($Q_CNT_TOTAL_DEBITS);
$Q_SUM_TOTAL_DEBITS = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D'";
$SUM_TOTAL_DEBITS = ReturnOneEntryFromDB($Q_SUM_TOTAL_DEBITS);

# ... ... ... 03: Entry Credit Counts ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_CREDITS = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C'";
$CNT_TOTAL_CREDITS = ReturnOneEntryFromDB($Q_CNT_TOTAL_CREDITS);
$Q_SUM_TOTAL_CREDITS = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C'";
$SUM_TOTAL_CREDITS = ReturnOneEntryFromDB($Q_SUM_TOTAL_CREDITS);

# ... ... ... 04: Entry Counts All Pass ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_PASS = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='PASS'";
$CNT_TOTAL_FILE_PASS = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_PASS);
$Q_SUM_TOTAL_FILE_PASS = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='PASS'";
$SUM_TOTAL_FILE_PASS = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_PASS);

# ... ... ... 05: Entry Counts All (Fail) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_FAIL = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='FAIL'";
$CNT_TOTAL_FILE_FAIL= ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_FAIL);
$Q_SUM_TOTAL_FILE_FAIL = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='FAIL'";
$SUM_TOTAL_FILE_FAIL = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_FAIL);

# ... ... ... 06: Entry Debits Counts (Pass) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_PASS_DR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='PASS'";
$CNT_TOTAL_FILE_PASS_DR = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_PASS_DR);
$Q_SUM_TOTAL_FILE_PASS_DR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='PASS'";
$SUM_TOTAL_FILE_PASS_DR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_PASS_DR);

# ... ... ... 07: Entry Debits Counts (Fail) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_FAIL_DR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='FAIL'";
$CNT_TOTAL_FILE_FAIL_DR= ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_FAIL_DR);
$Q_SUM_TOTAL_FILE_FAIL_DR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='FAIL'";
$SUM_TOTAL_FILE_FAIL_DR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_FAIL_DR);


# ... ... ... 08: Entry Credit Counts (Pass) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_PASS_CR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='PASS'";
$CNT_TOTAL_FILE_PASS_CR = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_PASS_CR);
$Q_SUM_TOTAL_FILE_PASS_CR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='PASS'";
$SUM_TOTAL_FILE_PASS_CR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_PASS_CR);

# ... ... ... 09: Entry Credit Counts (Fail) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_FAIL_CR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='FAIL'";
$CNT_TOTAL_FILE_FAIL_CR= ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_FAIL_CR);
$Q_SUM_TOTAL_FILE_FAIL_CR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='FAIL'";
$SUM_TOTAL_FILE_FAIL_CR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_FAIL_CR);


# ... ... ... 10: Get Uploader Details ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$UPLOADED_BY_COREID = GetUserCoreIdFromWebApp($UPLOADED_BY);
$response_msg = FetchUserDetailsFromCore($UPLOADED_BY_COREID, $MIFOS_CONN_DETAILS);
$CORE_RESP = $response_msg["CORE_RESP"];
$UPLOADED_BY_NAME = $CORE_RESP["username"]." (".$CORE_RESP["firstname"]." ".$CORE_RESP["lastname"].")";

# ... ... ... 11: Porcessing Math ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$CNT_DIFF = ($CNT_TOTAL_FILE - $CNT_TOTAL_FILE_PASS);
$VOL_DIFF = ($SUM_TOTAL_FILE - $SUM_TOTAL_FILE_PASS);


# ... F0000002: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "STAFF_USER";
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $EVENT_TYPE = "REVERSE BULK PAYMENT FILE";
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
      $MSG_TYPE = "BULK_PAYMENT_FILE_REVERSAL_TAN";
      $RECIPIENT_EMAILS = $fff_email;
      $EMAIL_MESSAGE = "Dear ".$fff_name."<br>"
                      ."This is your bulk payment file authentication TAN for reversal is: <b>".$TAN."</b>";
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

# ... F0000003: Verify File .....................................................................................#
if (isset($_POST['btn_rev_file'])) {
  $FILE_ID = trim(mysql_real_escape_string($_POST['FILE_ID']));
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

    ##########################################################################################################
    ##########################################################################################################
    $NEW_FILE_ID = "";
    $txn_list_debits = array();
    $txn_list_debits = FetchBulkTxnListDebits($FILE_ID);
    $txn_list_credits = array();
    $txn_list_credits = FetchBulkTxnListCredits($FILE_ID);

    # ...01: Create New File
    $EXC_FILE_NAME = $FILE_ID."_RVRSL";
    $UPLOAD_REASON = $VERIF_RMKS;
    $UPLOADED_BY = $_SESSION['UPR_USER_ID']; 
    $UPLOADED_ON = GetCurrentDateTime();

    $q = "INSERT INTO blk_pymt_file(FILE_NAME,UPLOAD_REASON,UPLOADED_BY,UPLOADED_ON) VALUES('$EXC_FILE_NAME','$UPLOAD_REASON','$UPLOADED_BY','$UPLOADED_ON')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    # ... Process Entity System ID (Role ID)
    $id_prefix = "BPF";
    $id_len = 10;
    $id_record_id = $RECORD_ID;
    $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
    $NEW_FILE_ID = $ENTITY_ID;

    # ... Updating the role id
    $q2 = "UPDATE blk_pymt_file SET FILE_ID='$NEW_FILE_ID' WHERE RECORD_ID='$RECORD_ID'";
    $update_response = ExecuteEntityUpdate($q2);
    if ($update_response=="EXECUTED") {
      $UPL_CNT = 0;

      # ... REVERSE CREDITS
      for ($i=0; $i < sizeof($txn_list_credits); $i++) { 
        $txn = array();
        $txn = $txn_list_credits[$i];
        $ORIG_TRAN_ID = $txn['TRAN_ID'];
        $SAVINGS_CUST_ID = $txn['SAVINGS_CUST_ID'];
        $SAVINGS_ACCT_ID = $txn['SAVINGS_ACCT_ID'];
        $SAVINGS_ACCT_NUM = $txn['SAVINGS_ACCT_NUM'];
        $SAVINGS_ACCT_NAME = $txn['SAVINGS_ACCT_NAME'];
        $CURRENCY = $txn['CURRENCY'];
        $TRAN_TYPE = "D";
        $TRAN_AMT = $txn['TRAN_AMT'];
        $TRAN_NARRATION = $txn['TRAN_NARRATION'];
        
        # ... Inserting the Transaction
        $q = "INSERT INTO blk_pymt_txns(FILE_ID,SAVINGS_CUST_ID,SAVINGS_ACCT_ID,CURRENCY,SAVINGS_ACCT_NUM,SAVINGS_ACCT_NAME,TRAN_TYPE,TRAN_AMT
          ,TRAN_NARRATION) VALUES('$NEW_FILE_ID','$SAVINGS_CUST_ID','$SAVINGS_ACCT_ID','$CURRENCY','$SAVINGS_ACCT_NUM','$SAVINGS_ACCT_NAME','$TRAN_TYPE','$TRAN_AMT','$TRAN_NARRATION')";
        $exec_response = array();
        $exec_response = ExecuteEntityInsert($q);
        $RESP = $exec_response["RESP"]; 
        $T_RECORD_ID = $exec_response["RECORD_ID"];

        # ... Process Entity System ID (Role ID)
        $id_prefix = "FT";
        $id_len = 15;
        $id_record_id = $T_RECORD_ID;
        $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
        $R_TRAN_ID = $ENTITY_ID."-R";

        # ... Updating the role id
        $qx = "UPDATE blk_pymt_txns SET TRAN_ID='$R_TRAN_ID' WHERE RECORD_ID='$T_RECORD_ID'";
        $update_responsex = ExecuteEntityUpdate($qx);
        if ($update_responsex=="EXECUTED") {
          $qrr = "INSERT INTO blk_pymt_txns_reversals(ORIGINAL_TRAN_ID,REVERSING_TRAN_ID) VALUES('$ORIG_TRAN_ID','$R_TRAN_ID')";
          ExecuteEntityInsert($qrr);
          $UPL_CNT++;
        }
      } # ...END..LOOP

      # ... REVERSE DEBITS
      for ($i=0; $i < sizeof($txn_list_debits); $i++) { 
        $txn = array();
        $txn = $txn_list_debits[$i];
        $ORIG_TRAN_ID = $txn['TRAN_ID'];
        $SAVINGS_CUST_ID = $txn['SAVINGS_CUST_ID'];
        $SAVINGS_ACCT_ID = $txn['SAVINGS_ACCT_ID'];
        $SAVINGS_ACCT_NUM = $txn['SAVINGS_ACCT_NUM'];
        $SAVINGS_ACCT_NAME = $txn['SAVINGS_ACCT_NAME'];
        $CURRENCY = $txn['CURRENCY'];
        $TRAN_TYPE = "C";
        $TRAN_AMT = $txn['TRAN_AMT'];
        $TRAN_NARRATION = $txn['TRAN_NARRATION'];
        
        # ... Inserting the Transaction
        $q = "INSERT INTO blk_pymt_txns(FILE_ID,SAVINGS_CUST_ID,SAVINGS_ACCT_ID,CURRENCY,SAVINGS_ACCT_NUM,SAVINGS_ACCT_NAME,TRAN_TYPE,TRAN_AMT
          ,TRAN_NARRATION) VALUES('$NEW_FILE_ID','$SAVINGS_CUST_ID','$SAVINGS_ACCT_ID','$CURRENCY','$SAVINGS_ACCT_NUM','$SAVINGS_ACCT_NAME','$TRAN_TYPE','$TRAN_AMT','$TRAN_NARRATION')";
        $exec_response = array();
        $exec_response = ExecuteEntityInsert($q);
        $RESP = $exec_response["RESP"]; 
        $T_RECORD_ID = $exec_response["RECORD_ID"];

        # ... Process Entity System ID (Role ID)
        $id_prefix = "FT";
        $id_len = 15;
        $id_record_id = $T_RECORD_ID;
        $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
        $R_TRAN_ID = $ENTITY_ID."-R";

        # ... Updating the role id
        $qx = "UPDATE blk_pymt_txns SET TRAN_ID='$R_TRAN_ID' WHERE RECORD_ID='$T_RECORD_ID'";
        $update_responsex = ExecuteEntityUpdate($qx);
        if ($update_responsex=="EXECUTED") {
          $qrr = "INSERT INTO blk_pymt_txns_reversals(ORIGINAL_TRAN_ID,REVERSING_TRAN_ID) VALUES('$ORIG_TRAN_ID','$R_TRAN_ID')";
          ExecuteEntityInsert($qrr);
          $UPL_CNT++;
        }
      } # ...END..LOOP
    }
    ##########################################################################################################
    ##########################################################################################################


    # ... Process System Response
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "BULK_TXN_REVERSAL";
    $ENTITY_ID_AFFECTED = $NEW_FILE_ID;
    $EVENT = "UPLOAD";
    $EVENT_OPERATION = "UPLOAD_BULK_PAYMENTS_FILE";
    $EVENT_RELATION = "blk_pymt_file|blk_pymt_txns_reversals";
    $EVENT_RELATION_NO = $NEW_FILE_ID;
    $OTHER_DETAILS = $UPL_CNT." entries uploaded for reversal.";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    $alert_type = "INFO";
    $alert_msg = "$UPL_CNT have been reversed. Reversal awaiting verification";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh: 3;");


  } # ... END..IFF
}

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Approve Bulk File", $APP_SMALL_LOGO); 

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
                
                <table width="100%">
                  <tr>
                    <td><a href="blk-rev-file" class="btn btn-sm btn-dark pull-left">Back</a> <h2>Reverse Bulk File</h2></td>
                    <?php
                    if ($FILE_STATUS=="APPROVED") {
                      ?>
                      <td width="10%">
                        <form method="post" id="dhs82bbwosdiowd">
                          <button type="submit" class="btn btn-warning btn-sm pull-right" name="btn_gen_tan">Generate Auth TAN</button>
                        </form>
                      </td>
                      <td width="10%">
                        <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#er3">Reverse File</button>
                        <div id="er3" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                          <div class="modal-dialog modal-sm">
                            <div class="modal-content">

                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel2">Reverse Bulk Payment File</h4>
                              </div>
                              <div class="modal-body">
                                  <form id="dddqwqqw2" method="post">
                                    
                                    <label>Auth TAN:</label><br>
                                    <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control"><br>

                                    <label>Reversal Rmks:</label><br>
                                    <textarea class="form-control" rows="3" id="VERIF_RMKS" name="VERIF_RMKS"></textarea><br><br>
                                    
                                    <input type="hidden" id="FILE_ID" name="FILE_ID" value="<?php echo $FILE_ID; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm" name="btn_rev_file">Reverse</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                  </form>
                              </div>
                             

                            </div>
                          </div>
                        </div>
                      </td>
                      <?php
                    } else if ($FILE_STATUS!="APPROVED"){
                      ?>
                      <td width="25%">
                        <button type="submit" class="btn btn-info btn-sm pull-right" disabled="">File cannot be reversed. It was never approved in the first case <strong>CURRENT_FILE_STATUS: </strong><?php echo $FILE_STATUS; ?></button>
                      </td>
                      <?php
                    }
                    ?>
                    
                  </tr>
                </table>        

                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">  

                <table class="table table-striped table-bordered" style="font-size: 12px;">
                  <thead>
                    <tr valign="top" bgcolor="#EEE">
                      <th>File Id</th>
                      <th>File Name</th>
                      <th>Description</th>
                      <th>Upload Date</th>
                      <th>Uploaded By</th>
                      <th>File Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                      <td><?php echo $FILE_ID; ?></td>
                      <td><?php echo $FILE_NAME; ?></td>
                      <td><?php echo $UPLOAD_REASON; ?></td>
                      <td><?php echo $UPLOADED_ON; ?></td>
                      <td><?php echo $UPLOADED_BY_NAME; ?></td>
                      <td><?php echo $FILE_STATUS; ?></td>
                    </tr>
                  </tbody>
                </table>

                <table class="table table-striped table-bordered" style="font-size: 12px;">
                  <thead>
                    <tr valign="top" bgcolor="#EEE">
                      <th width="15%">CATEGORY</th>
                      <th width="10%">COUNT</th>
                      <th width="20%">VOLUME (UGX)</th>
                      <th>PASSED ENTRIES</th>
                      <th>FAILED ENTRIES</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                        <th>Debit Entries</th>
                        <td><?php echo number_format($CNT_TOTAL_DEBITS); ?></td>
                        <td><?php echo number_format($SUM_TOTAL_DEBITS); ?></td>
                        <td><?php echo ($CNT_TOTAL_FILE_PASS_DR)." (".($SUM_TOTAL_FILE_PASS_DR).")"; ?></td>
                        <td><?php echo ($CNT_TOTAL_FILE_FAIL_DR)." (".($SUM_TOTAL_FILE_FAIL_DR).")"; ?></td>
                    </tr>
                    <tr valign="top">
                        <th>Credit Entries</th>
                        <td><?php echo number_format($CNT_TOTAL_CREDITS); ?></td>
                        <td><?php echo number_format($SUM_TOTAL_CREDITS); ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_PASS_CR." (".$SUM_TOTAL_FILE_PASS_CR.")"; ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_FAIL_CR." (".$SUM_TOTAL_FILE_FAIL_CR.")"; ?></td>
                    </tr>
                    <tr valign="top">
                        <th>Totals</th>
                        <td><?php echo number_format($CNT_TOTAL_FILE); ?></td>
                        <td><?php echo number_format($SUM_TOTAL_FILE); ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_PASS." (".$SUM_TOTAL_FILE_PASS.")"; ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_FAIL." (".$SUM_TOTAL_FILE_FAIL.")"; ?></td>
                    </tr>
                  </tbody>
                </table>

                <div style="overflow-y: auto; height: 490px;">
                  <table id="datatable3" class="table table-striped table-bordered" style="font-size: 11px;">
                    <thead>
                      <tr valign="top">
                        <th colspan="9" bgcolor="#EEE">
                          
                          <table width="100%">
                            <tr>
                              <td><span>List of File Transaction Entries</span></td>
                              <td width="10%"><a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download</a></td>
                            </tr>
                          </table>                                          
                        </th>

                      </tr>
                      <tr valign="top">
                        <th>#</th>
                        <th>Acct No</th>
                        <th>Acct Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Nrrtn</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $excel_table_list = array();
                      $txn_list_debits = array();
                      $txn_list_debits = FetchBulkTxnListDebits($FILE_ID);
                      $txn_list_credits = array();
                      $txn_list_credits = FetchBulkTxnListCredits($FILE_ID);
                      $txn_list = array();
                      $txn_list = array_merge($txn_list_debits, $txn_list_credits);

                      for ($i=0; $i < sizeof($txn_list); $i++) { 
                        //$excel_table_row = array();
                        $txn = array();
                        $txn = $txn_list[$i];
                        $RECORD_ID = $txn['RECORD_ID'];
                        $TRAN_ID = $txn['TRAN_ID'];
                        $FILE_ID = $txn['FILE_ID'];
                        $SAVINGS_CUST_ID = $txn['SAVINGS_CUST_ID'];
                        $SAVINGS_ACCT_ID = $txn['SAVINGS_ACCT_ID'];
                        $SAVINGS_ACCT_NUM = $txn['SAVINGS_ACCT_NUM'];
                        $SAVINGS_ACCT_NAME = $txn['SAVINGS_ACCT_NAME'];
                        $CURRENCY = $txn['CURRENCY'];
                        $TRAN_TYPE = $txn['TRAN_TYPE'];
                        $TRAN_AMT = $txn['TRAN_AMT'];
                        $TRAN_NARRATION = $txn['TRAN_NARRATION'];
                        $PASS_FAIL_FLG = $txn['PASS_FAIL_FLG'];
                        $EXEC_FLG = $txn['EXEC_FLG'];
                        $EXEC_MSG = $txn['EXEC_MSG'];
                        $PASS_FAIL_RMKS = $txn['PASS_FAIL_RMKS'];
                        $TRAN_STATUS = $txn['TRAN_STATUS'];
                        $CORE_REF_ID = $txn['CORE_REF_ID'];

                        # ... Building the excel table row
                        $excel_table_row[0] = ($i+1);
                        $excel_table_row[1] = $SAVINGS_ACCT_NUM;
                        $excel_table_row[2] = $SAVINGS_ACCT_NAME;
                        $excel_table_row[3] = $TRAN_TYPE;
                        $excel_table_row[4] = $TRAN_AMT;
                        $excel_table_row[5] = $TRAN_NARRATION;
                        $excel_table_row[6] = $PASS_FAIL_FLG;
                        $excel_table_row[7] = $PASS_FAIL_RMKS;
                        $excel_table_row[8] = $EXEC_FLG;
                        $excel_table_row[9] = $EXEC_MSG;
                        $excel_table_row[10] = $CORE_REF_ID;

                        $excel_table_list[$i] = $excel_table_row;
                        ?>
                         <tr valign="top">
                          <td><?php echo ($i+1); ?>. </td>
                          <td><?php echo $SAVINGS_ACCT_NUM; ?></td>
                          <td><?php echo $SAVINGS_ACCT_NAME; ?></td>
                          <td><?php echo $TRAN_TYPE; ?></td>
                          <td><?php echo number_format($TRAN_AMT); ?></td>
                          <td><?php echo $TRAN_NARRATION; ?></td>
                          <td><?php echo $TRAN_STATUS; ?></td>
                        </tr>
                        <?php
                      } # .. END..LOOP

                      # ... Excel Data Preparation
                      $_SESSION["EXCEL_HEADER"] = array("#","Acct No","Acct Name","Type","Amount","Nrrtn","Pass/Fail","Pass/Fail Rmks"
                                                       ,"Exec Status", "Exec message", "Tran Staus");
                      $_SESSION["EXCEL_DATA"] = $excel_table_list;
                      $_SESSION["EXCEL_FILE"] = $FILE_ID."_".date('dFY', strtotime(GetCurrentDateTime())).".xlsx";
                      ?>
                    </tbody>
                  </table>
                </div>
                
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
