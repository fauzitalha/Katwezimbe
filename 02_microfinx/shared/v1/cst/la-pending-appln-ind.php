<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$LN_APPLN_NO = mysql_real_escape_string(trim($_GET['k']));

# ... Fetch Loan Application Details
$la = array();
$la = FetchLoanApplnDetailsById($LN_APPLN_NO);
$RECORD_ID = $la['RECORD_ID'];
$LN_APPLN_NO = $la['LN_APPLN_NO'];
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


# ... Get Loan Product Application Config rules
$appln_config = array();
$appln_config = FetchLoanApplnConfigByProductId($LN_PDT_ID);
$APPLN_CONFIG_ID = $appln_config['APPLN_CONFIG_ID'];
$APPLN_CONFIG_NAME = $appln_config['APPLN_CONFIG_NAME'];
$APPLN_TYPE_ID = $appln_config['APPLN_TYPE_ID'];
$PDT_ID = $appln_config['PDT_ID'];
$PDT_TYPE_ID = $appln_config['PDT_TYPE_ID'];

# ... Get Application Type Menu
$config_param_list = array();
$config_param_list = FetchLoanApplnConfigByProductId($PDT_ID);
$PRM_01 = $config_param_list["PRM_01"];
$PRM_02 = $config_param_list["PRM_02"];
$GUARANTORS_REQUIRED = $PRM_01;
$CNT_OF_RQRD_GURANTORS = $PRM_02;

# ... Fetching Loan Application Guarantors
$grrt_list = array();
$grrt_list = FetchLoanApplnGuarantors($LN_APPLN_NO);

# ... Add loan application file
if (isset($_POST['btn_add_lnfile'])) {

  $LN_APPLN_NO = trim(mysql_real_escape_string($_POST['LN_APPLN_NO']));
  $F_CODE = trim(mysql_real_escape_string($_POST['F_CODE']));
  $DATE_UPLOADED = GetCurrentDateTime(); 


  // ... CREATING LN_APPLICATION DIRECTORY
  $LN_APPLN_FILES_BASEPATH_CUST = GetSystemParameter("LN_APPLN_FILES_BASEPATH_CUST")."/".$_SESSION['ORG_CODE'];
  $LN_DIR = $LN_APPLN_FILES_BASEPATH_CUST."/".$LN_APPLN_NO;
  $dir = $LN_DIR;
  if (!is_dir($LN_DIR)) {
    mkdir($LN_DIR);
  }

  // ... FILE 01
  $file_size = $_FILES['UPLOAD_FILE']['size'];
  $ext = strtolower(substr(strrchr($_FILES['UPLOAD_FILE']['name'],"."),1));
  $file_name = $F_CODE.".".$ext;

  if(is_uploaded_file($_FILES['UPLOAD_FILE']['tmp_name'])){
    if($file_size >= 700000){ // file size (700KB)
      $alert_type = "ERROR";
      $alert_msg = "ERROR: Files exceeds 700KB. Upload file of a smaller size";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    }else{
      if(($_FILES['UPLOAD_FILE']['type']=="image/gif") // gif
      ||($_FILES['UPLOAD_FILE']['type']=="image/jpeg") // jpeg
      ||($_FILES['UPLOAD_FILE']['type']=="image/png") // png
      ||($_FILES['UPLOAD_FILE']['type']=="application/pdf") // pdf
       ){
        $result = move_uploaded_file($_FILES['UPLOAD_FILE']['tmp_name'], $dir."/".$file_name);
        if($result == 1){


          $q = "INSERT INTO loan_appln_files(LN_APPLN_NO,F_CODE,F_NAME,DATE_UPLOADED) VALUES('$LN_APPLN_NO','$F_CODE','$file_name','$DATE_UPLOADED')";
          $exec_response = array();
          $exec_response = ExecuteEntityInsert($q);
          $RESP = $exec_response["RESP"]; 
          $RECORD_ID = $exec_response["RECORD_ID"];

          # ... Log System Audit Log
          $AUDIT_DATE = GetCurrentDateTime();
          $ENTITY_TYPE = "LOAN_APPLN_FILE";
          $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
          $EVENT = "ADD_FILE";
          $EVENT_OPERATION = "ADD_LOAN_APPLN_FILE";
          $EVENT_RELATION = "loan_appln_files";
          $EVENT_RELATION_NO = $RECORD_ID;
          $OTHER_DETAILS = "";
          $INVOKER_ID = $_SESSION['CST_USR_ID'];
          LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                         $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


          $alert_type = "SUCCESS";
          $alert_msg = "SUCCESS: File has been added successfully. Refreshing in 5 seconds.";
          $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
          header("Refresh:5;");


          
        }
      }else{
        $alert_type = "ERROR";
        $alert_msg = "ERROR: Unacceptable file format. Acceptable formats include '.png', '.jpg', '.gif' and .'pdf'";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);    
      }
    }
  }
}

# ... Delete File
if (isset($_POST['btn_rem_lnfile'])) {
  $RECORD_ID = $_POST['F_RECORD_ID'];
  $LN_APPLN_NO = $_POST['LN_APPLN_NO'];
  $F_NAME = $_POST['F_NAME'];
  
  $TABLE = "loan_appln_files";
  $TABLE_RECORD_ID = $_POST['F_RECORD_ID'];
  $delete_response = array();
  $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
  $DEL_FLG = $delete_response["DEL_FLG"];
  $DEL_ROW = $delete_response["DEL_ROW"];

  if ($DEL_FLG=="Y") {

    # ... Delete file from file system
    $LN_APPLN_FILES_BASEPATH_CUST = GetSystemParameter("LN_APPLN_FILES_BASEPATH_CUST")."/".$_SESSION['ORG_CODE'];
    $LN_DIR = $LN_APPLN_FILES_BASEPATH_CUST."/".$LN_APPLN_NO;
    $dir = $LN_DIR;
    $file_name = $dir."/".$F_NAME;
    unlink($file_name);

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN_FILE";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "DELETE_FILE";
    $EVENT_OPERATION = "DELETE_LOAN_APPLN_FILE";
    $EVENT_RELATION = "loan_appln_files";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DEL_ROW;
    $INVOKER_ID = $_SESSION['CST_USR_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "INFO";
    $alert_msg = "MESSAGE: File has been removed. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }
}

# ... Request Guarantorship
if (isset($_POST['btn_rqst_grrtor'])) {
  $LN_APPLN_NO = trim(mysql_real_escape_string($_POST['LN_APPLN_NO']));
  $G_CUST_ID = trim(mysql_real_escape_string($_POST['G_CUST_ID']));
  $G_NAME = trim(mysql_real_escape_string($_POST['G_NAME']));
  $G_PHONE = trim(mysql_real_escape_string($_POST['G_PHONE']));
  $G_EMAIL = trim(mysql_real_escape_string($_POST['G_EMAIL']));
  $DATE_GENERATED = GetCurrentDateTime(); 

  $DETS = $G_CUST_ID."|".$G_NAME."|".$G_PHONE."|".$G_EMAIL;

  $q = "INSERT INTO loan_appln_guarantors(LN_APPLN_NO,G_CUST_ID,G_NAME,G_PHONE,G_EMAIL,DATE_GENERATED) VALUES('$LN_APPLN_NO','$G_CUST_ID','$G_NAME','$G_PHONE','$G_EMAIL','$DATE_GENERATED')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];

  if ($RESP=="EXECUTED") {

    # ... Log Email
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "GUARANTOR REQUEST";
    $RECIPIENT_EMAILS = $G_EMAIL;
    $EMAIL_MESSAGE = "Dear ".$G_NAME.",<br>"
                    ."I am requesting you to be my guarantor for my loan application.<br>"
                    ."Log into you account to view my loan application details.<br><br>"
                    ."Regards,<br>"
                    .$_SESSION['displayName'];

    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

     $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";

    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP=="EXECUTED") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "LOAN_APPLN_GUARANTOR";
      $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
      $EVENT = "REQUEST";
      $EVENT_OPERATION = "REQUEST_GUARANTORSHIP";
      $EVENT_RELATION = "loan_appln_guarantors";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = $DETS;
      $INVOKER_ID = $_SESSION['CST_USR_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "SUCCESS: Guarantorship request has been sent to $G_NAME. An email has also been sent out to $G_NAME.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5;");
    }
  }
}

# ... Remove Guarantor
if (isset($_POST['btn_rem_grrt'])) {
  $RECORD_ID = $_POST['G_RECORD_ID'];
  
  $TABLE = "loan_appln_guarantors";
  $TABLE_RECORD_ID = $_POST['G_RECORD_ID'];
  $delete_response = array();
  $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
  $DEL_FLG = $delete_response["DEL_FLG"];
  $DEL_ROW = $delete_response["DEL_ROW"];

  if ($DEL_FLG=="Y") {


    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN_GUARANTOR";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "DELETE_GUARANTOR";
    $EVENT_OPERATION = "DELETE_LOAN_APPLN_GUARANTOR";
    $EVENT_RELATION = "loan_appln_guarantors";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DEL_ROW;
    $INVOKER_ID = $_SESSION['CST_USR_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Guarantor has been removed. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }
}

# ... Proceed to signing rules
if (isset($_POST['btn_grrt_proc'])) {

  $LN_APPLN_NO = trim(mysql_real_escape_string($_POST['LN_APPLN_NO']));
  $LN_APPLN_SUBMISSION_DATE = $la['LN_APPLN_SUBMISSION_DATE'];
  $LN_APPLN_ASSMT_STATUS = "";
  $LN_APPLN_ASSMT_RMKS = "";
  $LN_APPLN_DOC_STATUS = "";
  $LN_APPLN_DOC_RMKS = "";
  $LN_APPLN_GRRTR_STATUS = "";
  $LN_APPLN_GRRTR_RMKS = "";
  $LN_APPLN_STATUS = "NEW_SUBMISSION";

  # ... SQL
  $q2 = "UPDATE loan_applns SET LN_APPLN_ASSMT_STATUS='$LN_APPLN_ASSMT_STATUS'
                               ,LN_APPLN_ASSMT_RMKS='$LN_APPLN_ASSMT_RMKS'
                               ,LN_APPLN_DOC_STATUS='$LN_APPLN_DOC_STATUS'
                               ,LN_APPLN_DOC_RMKS='$LN_APPLN_DOC_RMKS'
                               ,LN_APPLN_GRRTR_STATUS='$LN_APPLN_GRRTR_STATUS'
                               ,LN_APPLN_GRRTR_RMKS='$LN_APPLN_GRRTR_RMKS'
                               ,LN_APPLN_STATUS='$LN_APPLN_STATUS' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "RESUBMISSION";
    $EVENT_OPERATION = "RESUBMIT_LOAN_APPLN";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['CST_USR_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "MESSAGE: Loan Application has bee resubmitted. Redirecting in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5; URL=la-pending-appln");
  }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("New Loan Appln", $APP_SMALL_LOGO); 

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

          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>
            <div class="x_panel">
              <div class="x_title">
                <a href="la-pending-appln" class="btn btn-dark btn-xs pull-left">Back</a>
                Loan Appln Files & Guarantors
                <div class="clearfix"></div>
              </div>

            </div>
          </div>   


          <!-- -- -- -- -- -- -- -- -- -- -- LOAN DOCUMENTS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN DOCUMENTS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-6 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                Loan Application Documents
                <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#crt_grp">Add File</button>
                <div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">

                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel2">Add File</h4>
                      </div>
                      <div class="modal-body">
                          <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="LN_APPLN_NO" id="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">

                            <label>File Name:</label><br>
                            <input type="text" id="F_CODE" name="F_CODE" class="form-control" required=""><br>
                            
                            <label>Attach file:</label><br>
                            <input type="file" id="UPLOAD_FILE" name="UPLOAD_FILE" class="form-control" required=""><br>
                            
                            <button type="submit" class="btn btn-primary btn-sm" name="btn_add_lnfile">Upload</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                          </form>
                      </div>
                     

                    </div>
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                
                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Management Response</label>
                  <textarea class="form-control" rows="3" disabled=""><?php echo $LN_APPLN_DOC_RMKS; ?></textarea>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Response Date</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $LN_APPLN_DOC_DATE; ?>">
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Document List</label>
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
                      $LN_APPLN_FILES_LOCATION_CUST = GetSystemParameter("LN_APPLN_FILES_LOCATION_CUST")."/".$_SESSION['ORG_CODE'];
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
                            <table>
                              <tr>
                                <td><a href="<?php echo $file_loc; ?>" class="btn btn-info btn-xs">View</a></td>
                                <td>
                                  <form method="post" id="<?php echo $f_id; ?>">
                                    <input type="hidden" id="F_RECORD_ID" name="F_RECORD_ID" value="<?php echo $F_RECORD_ID; ?>">
                                    <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $F_LN_APPLN_NO; ?>">
                                    <input type="hidden" id="F_NAME" name="F_NAME" value="<?php echo $F_NAME; ?>">
                                    <button type="submit" class="btn btn-danger btn-xs" name="btn_rem_lnfile">Remove</button>
                                  </form>
                                </td>
                              </tr>
                            </table>
                            
                            
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


          <!-- -- -- -- -- -- -- -- -- -- -- LOAN GUARANTORS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN GUARANTORS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-6 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                Loan Application Guarantors
                <?php
                if ($GUARANTORS_REQUIRED=="NO") {
                  // ... display nothing
                } elseif ($GUARANTORS_REQUIRED=="YES") {
                  ?>
                  <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#ggg">Add Guarantor</button>
                  <div id="ggg" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Select Guarantor</h4>
                        </div>
                        <div class="modal-body">
                            <table id="datatable" width="100%" class="table table-striped table-bordered">
                              <thead>
                                <tr valign="top">
                                  <th colspan="3" bgcolor="#EEE">List of Possible Guarantors</th>
                                </tr>
                                <tr valign="top">
                                  <th>#</th>
                                  <th>Name</th>
                                  <th>Actions</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                $CURRENT_CST_ID = $_SESSION['CST_USR_ID'];
                                $g_list = FetchGuarantorPool($LN_APPLN_NO,$CURRENT_CST_ID);
                                
                                for ($i=0; $i < sizeof($g_list); $i++) { 
                                  $g = array();
                                  $g = $g_list[$i];
                                  $CUST_ID = $g['CUST_ID'];
                                  $CUST_CORE_ID = $g['CUST_CORE_ID'];
                                  $CUST_EMAIL = AES256::decrypt($g['CUST_EMAIL']);
                                  $CUST_PHONE = AES256::decrypt($g['CUST_PHONE']);

                                  # ... Fetch Customer Details From Core
                                  $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                                  $CONN_FLG = $response_msg["CONN_FLG"];
                                  $CORE_RESP = $response_msg["CORE_RESP"];
                                  $displayName = $CORE_RESP["displayName"];

                                  $id3 = "FTT3".($i+1);
                                  $target3 = "#".$id3;
                                  $form_id3 = "FORM_".$id3;
                                  ?>
                                  <tr valign="top">
                                    <td><?php echo ($i+1); ?>. </td>
                                    <td><?php echo $displayName; ?></td>
                                    <td>
                                      <form method="post" id="<?php echo $form_id3; ?>">
                                        <input type="hidden" id="G_CUST_ID" name="G_CUST_ID" value="<?php echo $CUST_ID; ?>">
                                        <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                                        <input type="hidden" id="G_NAME" name="G_NAME" value="<?php echo $displayName; ?>">
                                        <input type="hidden" id="G_EMAIL" name="G_EMAIL" value="<?php echo $CUST_EMAIL; ?>">
                                        <input type="hidden" id="G_PHONE" name="G_PHONE" value="<?php echo $CUST_PHONE; ?>">
                                        <button type="submit" class="btn btn-xs btn-primary" name="btn_rqst_grrtor">Request Guarantorship</button>
                                      </form>
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
                  <?php
                }
                ?>
                
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Management Response</label>
                  <textarea class="form-control" rows="3" disabled=""><?php echo $LN_APPLN_GRRTR_RMKS; ?></textarea>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Response Date</label>
                  <input type="text" class="form-control" disabled="" value="<?php echo $LN_APPLN_GRRTR_DATE; ?>">
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Guarantor List</label>
                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr valign="top">
                        <th>#</th>
                        <th>Name</th>
                        <th>Grrt Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($GUARANTORS_REQUIRED=="NO") {
                        ?>
                        <tr valign="top">
                          <td colspan="4">Guarantors are not required for this Loan Product. Please proceed forward.</td>
                        </tr>
                        <?php
                      } elseif ($GUARANTORS_REQUIRED=="YES") {

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
                            <td><?php echo $GUARANTORSHIP_STATUS; ?></td>
                            <td>
                              <?php
                              if ($GUARANTORSHIP_STATUS=="APPROVED") {
                                // ... display nothing
                              } else {
                                ?>
                                <table>
                                  <tr>
                                    <td>
                                      <form method="post" id="<?php echo $f_id; ?>">
                                        <input type="hidden" id="G_RECORD_ID" name="G_RECORD_ID" value="<?php echo $G_RECORD_ID; ?>">
                                        <button type="submit" class="btn btn-danger btn-xs" name="btn_rem_grrt">Remove</button>
                                      </form>
                                    </td>
                                  </tr>
                                </table>
                                <?php
                              }
                              ?>
                            </td>
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
          </div>


          <!-- -- -- -- -- -- -- -- -- -- -- PROCEEDING WITH APPLN -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- PROCEEDING WITH APPLN -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              
              <div class="x_content">   
                <?php $LN_APPLN_NO = mysql_real_escape_string(trim($_GET['k']));  ?>
                <form method="post" id="gproc">
                  <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                  <?php
                  if ($GUARANTORS_REQUIRED=="NO") {
                    ?>
                    <button type="submit" class="btn btn-success btn-lg pull-right" name="btn_grrt_proc">Proceed</button>
                    <?php
                  } elseif ($GUARANTORS_REQUIRED=="YES") {
                    $Q_CNT = "SELECT count(*) as RTN_VALUE FROM loan_appln_guarantors WHERE LN_APPLN_NO='$LN_APPLN_NO' AND GUARANTORSHIP_STATUS='APPROVED'";
                    $CNT = ReturnOneEntryFromDB($Q_CNT);

                    if ($CNT>=$CNT_OF_RQRD_GURANTORS) {
                      ?>
                      <button type="submit" class="btn btn-success btn-lg pull-right" name="btn_grrt_proc">Re-Submit</button>
                      <?php
                    } else {
                      ?>
                      <span class="pull-right">Atleast <?php echo $CNT_OF_RQRD_GURANTORS; ?> guarantors must approve before you can proceed.</span><br>
                      <button type="submit" class="btn btn-danger btn-lg pull-right" disabled="">Cannot Proceed</button>
                      <?php
                    }                  
                  }

                  ?>
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
