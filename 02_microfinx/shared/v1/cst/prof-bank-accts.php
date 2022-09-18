<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... GETTING CUSTOMER DETAILS
$cstmr = array();
$cstmr = FetchCustomerLoginDataByCustId($_SESSION['CST_USR_ID']);
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


# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "CUSTOMER";
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
  $EVENT_TYPE = "ADD BANK ACCOUNT";
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
      $MSG_TYPE = "ADD_BANK_ACCT_AUTH_TAN";
      $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
      $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                      ."Your password reset auth TAN is: <b>".$TAN."</b>";
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


# ... Create New Group
if (isset($_POST['btn_add_acct'])) {
  $CC_BANK_ID = trim(mysql_real_escape_string($_POST['BANK_ID']));
  $ACCT_NO = trim(mysql_real_escape_string($_POST['ACCT_NO']));
  $ACCT_NAME = trim(mysql_real_escape_string($_POST['ACCT_NAME']));
  $TRAN_TAN = trim($_POST['TRAN_TAN']);

  $CUST_ID = $_SESSION['CST_USR_ID'];
  $BANK_ID = $CC_BANK_ID;
  $BANK_ACCOUNT = $ACCT_NO;
  $BANK_ACCOUNT_NAME = $ACCT_NAME;
  $DATE_ADDED = GetCurrentDateTime(); 

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

    $q = "INSERT INTO cstmrs_bank_details(CUST_ID,BANK_ID,BANK_ACCOUNT,BANK_ACCOUNT_NAME,DATE_ADDED) VALUES('$CUST_ID','$BANK_ID','$BANK_ACCOUNT','$BANK_ACCOUNT_NAME','$DATE_ADDED')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP=="EXECUTED") {

      # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $ENTITY_ID = $_SESSION['CST_USR_ID'];
      $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
      ExecuteEntityUpdate($qww);

      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "CUST_BANK_ACCT";
      $ENTITY_ID_AFFECTED = $CUST_ID;
      $EVENT = "ADD_NEW_ACCT";
      $EVENT_OPERATION = "ADD_NEW_ACCT";
      $EVENT_RELATION = "cstmrs_bank_details";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = "";
      $INVOKER_ID = $_SESSION['CST_USR_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "SUCCESS: Acct has been created. Refreshing in 3 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:3;");
    }
  }  
}


# ... Delete group
if (isset($_POST['btn_delete_acct_no'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];
  $BANK_ACCOUNT = $_POST['BANK_ACCOUNT'];
  $CUST_ID = $_SESSION["CST_USR_ID"];

  $bank_acct = array();
  $bank_acct = FetchCustFinInstAcctsById($CUST_ID, $BANK_ACCOUNT);
  $DD_RECORD_ID = $bank_acct['RECORD_ID'];
  $DD_CUST_ID = $bank_acct['CUST_ID'];
  $BANK_ID = $bank_acct['BANK_ID'];
  $BANK_ACCOUNT = $bank_acct['BANK_ACCOUNT'];
  $BANK_ACCOUNT_NAME = $bank_acct['BANK_ACCOUNT_NAME'];
  $DATE_ADDED = $bank_acct['DATE_ADDED'];
  $ACCT_STATUS = $bank_acct['ACCT_STATUS'];

  # ... 02: Save Data to DataBase
  $q = "INSERT INTO cstmrs_bank_details_deleted(RECORD_ID,CUST_ID, BANK_ID, BANK_ACCOUNT,BANK_ACCOUNT_NAME,DATE_ADDED,ACCT_STATUS) VALUES('$DD_RECORD_ID','$DD_CUST_ID','$BANK_ID','$BANK_ACCOUNT','$BANK_ACCOUNT_NAME','$DATE_ADDED','$ACCT_STATUS');";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];
  if ( $RESP=="EXECUTED" ) {
    
    $TABLE = "cstmrs_bank_details";
    $TABLE_RECORD_ID = $_POST['ADD_RECORD_ID'];
    $delete_response = array();
    $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
    $DEL_FLG = $delete_response["DEL_FLG"];
    $DEL_ROW = $delete_response["DEL_ROW"];

    if ($DEL_FLG=="Y") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "CUSTOMER_BANK_ACCT";
      $ENTITY_ID_AFFECTED = $CUST_ID;
      $EVENT = "DELETE";
      $EVENT_OPERATION = "DELETE_CUSTOMER_BANK_ACCT";
      $EVENT_RELATION = "cstmrs_bank_details -> cstmrs_bank_details_deleted";
      $EVENT_RELATION_NO = $_POST['ADD_RECORD_ID'];
      $OTHER_DETAILS = $DEL_ROW;
      $INVOKER_ID = $_SESSION['CST_USR_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: Bank account has been deleted completely. Refreshing in 4 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:4;");
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
    LoadDefaultCSSConfigurations("My Bank Accounts", $APP_SMALL_LOGO); 

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
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <h2>My Bank Accounts</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">
                          <span style="font-size: 14px;">List of Bank Accounts</span>

                          <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#crt_grp">Add New Acct</button>
                          <div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Add new bank account</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="dddqwqqw2" method="post">
                                      <label>Enter Authorization TAN:</label> 
                                      <button type="submit" class="btn btn-warning btn-xs" name="btn_gen_tan">Generate Auth TAN</button>
                                      <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control"> <br><br>

                                      <label>Select Bank Name:</label><br>
                                      <select class="form-control" name="BANK_ID" id="BANK_ID">
                                        <option value="">-------</option>
                                        <?php
                                        $fin_list = array();
                                        $fin_list = FetchFinInstitutions();
                                        for ($i=0; $i < sizeof($fin_list); $i++) { 
                                          $fin = array();
                                          $fin = $fin_list[$i];
                                          $RECORD_ID = $fin['RECORD_ID'];
                                          $FIN_INST_ID = $fin['FIN_INST_ID'];
                                          $FIN_INST_NAME = $fin['FIN_INST_NAME'];
                                          ?>
                                          <option value="<?php echo $FIN_INST_ID; ?>"><?php echo $FIN_INST_NAME; ?></option>
                                          <?php
                                        }
                                        ?>
                                      </select><br><br>

                                      <label>Account Number:</label><br>
                                      <input type="text" id="ACCT_NO" name="ACCT_NO" class="form-control"><br><br>
                                      
                                      <label>Account Name:</label><br>
                                      <input type="text" id="ACCT_NAME" name="ACCT_NAME" class="form-control"><br>
                                      
                                      <br>
                                      <button type="submit" class="btn btn-primary btn-sm" name="btn_add_acct">Add Acct</button>
                                      <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                               

                              </div>
                            </div>
                          </div>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Bank Name</th>
                      <th>Account No</th>
                      <th>Account Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $bank_acct_list = array();
                    $bank_acct_list = FetchCustFinInstAccts($CUST_ID);
                    for ($i=0; $i < sizeof($bank_acct_list); $i++) { 
                      $bank_acct = array();
                      $bank_acct = $bank_acct_list[$i];
                      $RECORD_ID = $bank_acct['RECORD_ID'];
                      $CUST_ID = $bank_acct['CUST_ID'];
                      $BANK_ID = $bank_acct['BANK_ID'];
                      $BANK_ACCOUNT = $bank_acct['BANK_ACCOUNT'];
                      $BANK_ACCOUNT_NAME = $bank_acct['BANK_ACCOUNT_NAME'];
                      $DATE_ADDED = $bank_acct['DATE_ADDED'];
                      $ACCT_STATUS = $bank_acct['ACCT_STATUS'];

                      # ... Get Bank Name
                      $fin = FetchFinInstitutionsById($BANK_ID);
                      $FIN_INST_NAME = $fin['FIN_INST_NAME'];

                      
                      $id = "FTT".($i+1);
                      $target = "#".$id;
                      $form_id = "FORM_".$id;

                      $id2 = "FTT2".($i+1);
                      $target2 = "#".$id2;
                      $form_id2 = "FORM_".$id2;

                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;

                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $FIN_INST_NAME; ?></td>
                        <td><?php echo $BANK_ACCOUNT; ?></td>
                        <td><?php echo $BANK_ACCOUNT_NAME; ?></td>
                        <td>
                          <?php
                          if ($ACCT_STATUS=="ACTIVE") {
                            ?>
                            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $target3; ?>">Delete</button>
                            <div id="<?php echo $id3; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="color: #333;">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Delete Acct</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id3; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        <input type="hidden" id="BANK_ACCOUNT" name="BANK_ACCOUNT" value="<?php echo $BANK_ACCOUNT; ?>">
                                        
                                        <label>Bank Account Number:</label><br>
                                        <?php echo $BANK_ACCOUNT; ?><br><br>
                     
                                        
                                        <label>Bank Name:</label><br>
                                        <?php echo $FIN_INST_NAME; ?><br><br>
                     
                     
                                        <strong>NOTE:</strong>
                                        This action cannot be undone.
                                        <br><br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_acct_no">Delete</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
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
                      <?php

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
