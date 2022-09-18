<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Details
$LN_APPLN_NO = mysql_real_escape_string(trim($_GET['k']));

# ... Fetch Loan Application Details
$RECORD_ID = "";
$CUST_ID = "";
$DBB_CUST_CORE_ID = "";
$LN_PDT_ID = "";
$LN_APPLN_CREATION_DATE = "";
$LN_APPLN_PROGRESS_STATUS = "";
$RQSTD_AMT = "";
$RQSTD_RPYMT_PRD = "";
$PURPOSE = "";
$CORE_SVGS_ACCT_ID = "";
$CUST_FIN_INST_ID = "";

$loan_appln = array();
$loan_appln = FetchLoanApplnDetailsById_walkin($LN_APPLN_NO);
if (isset($loan_appln['RECORD_ID'])) {
  $RECORD_ID = $loan_appln['RECORD_ID'];

  $CUST_ID = $loan_appln['CUST_ID'];
  $data_details = explode('-', $CUST_ID);
  $DBB_CUST_CORE_ID = $data_details[1];

  $LN_PDT_ID = $loan_appln['LN_PDT_ID'];
  $LN_APPLN_CREATION_DATE = $loan_appln['LN_APPLN_CREATION_DATE'];
  $LN_APPLN_PROGRESS_STATUS = $loan_appln['LN_APPLN_PROGRESS_STATUS'];
  $RQSTD_AMT = $loan_appln['RQSTD_AMT'];
  $RQSTD_RPYMT_PRD = $loan_appln['RQSTD_RPYMT_PRD'];
  $PURPOSE = $loan_appln['PURPOSE'];
  $CORE_SVGS_ACCT_ID = $loan_appln['CORE_SVGS_ACCT_ID'];
  $CUST_FIN_INST_ID = $loan_appln['CUST_FIN_INST_ID'];
}

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
$config_param_list = FetchLoanApplnConfigByProductId_walkin($PDT_ID);
$PRM_01 = $config_param_list["PRM_01"];
$PRM_02 = $config_param_list["PRM_02"];
$GUARANTORS_REQUIRED = $PRM_01;
$CNT_OF_RQRD_GURANTORS = $PRM_02;


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
          $INVOKER_ID = $_SESSION['UPR_USER_ID'];
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
if (isset($_POST['btn_add_grrtor'])) {
  $LN_APPLN_NO = trim(mysql_real_escape_string($_POST['LN_APPLN_NO']));
  $G_CUST_CORE_ID = trim(mysql_real_escape_string($_POST['G_CUST_CORE_ID']));
  $G_NAME = trim(mysql_real_escape_string($_POST['G_NAME']));
  $DATE_ADDED = GetCurrentDateTime(); 

  $DETS = $G_CUST_CORE_ID."|".$G_NAME;

  $q = "INSERT INTO loan_appln_guarantors_walkin(LN_APPLN_NO,G_CUST_CORE_ID,G_NAME,DATE_ADDED) VALUES('$LN_APPLN_NO','$G_CUST_CORE_ID','$G_NAME','$DATE_ADDED')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];

  if ($RESP=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN_GUARANTOR_WALKIN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "ADD";
    $EVENT_OPERATION = "ADD_GUARANTOR_WALKIN";
    $EVENT_RELATION = "loan_appln_guarantors_walkin";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DETS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Guarantor added to loan application.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }
}

# ... Remove Guarantor
if (isset($_POST['btn_rem_grrt'])) {
  $RECORD_ID = $_POST['G_RECORD_ID'];
  
  $TABLE = "loan_appln_guarantors_walkin";
  $TABLE_RECORD_ID = $_POST['G_RECORD_ID'];
  $delete_response = array();
  $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
  $DEL_FLG = $delete_response["DEL_FLG"];
  $DEL_ROW = $delete_response["DEL_ROW"];

  if ($DEL_FLG=="Y") {


    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN_GUARANTOR_WALKIN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "DELETE_GUARANTOR_WALKIN";
    $EVENT_OPERATION = "DELETE_LOAN_APPLN_GUARANTOR_WALKIN";
    $EVENT_RELATION = "loan_appln_guarantors_walkin";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DEL_ROW;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Guarantor has been removed. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }
}

# ... Proceed to signing rules
if (isset($_POST['btn_submit_request'])) {

  $LN_APPLN_NO = trim(mysql_real_escape_string($_POST['LN_APPLN_NO']));
  $LN_APPLN_PROGRESS_STATUS = "4";
  $LN_APPLN_SUBMISSION_DATE = GetCurrentDateTime(); 
  $LN_APPLN_STATUS = "NEW_SUBMISSION";

  # ... SQL
  $q2 = "UPDATE loan_applns SET LN_APPLN_PROGRESS_STATUS='$LN_APPLN_PROGRESS_STATUS', LN_APPLN_SUBMISSION_DATE='$LN_APPLN_SUBMISSION_DATE', LN_APPLN_STATUS='$LN_APPLN_STATUS' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q2);

  # ... Log System Audit Log
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "LOAN_APPLN_WALKIN";
  $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
  $EVENT = "LOAN_APPLN_SUBMISSION_WALKIN";
  $EVENT_OPERATION = "LOAN_APPLN_SUBMISSION_WALKIN";
  $EVENT_RELATION = "loan_applns";
  $EVENT_RELATION_NO = $LN_APPLN_NO;
  $OTHER_DETAILS = "";
  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

  $alert_type = "SUCCESS";
  $alert_msg = "MESSAGE: You have successfully submitted the loan application. Proceed to have it assessed";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  header("Refresh:5; url='main-dashboard'");

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

          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>
            <div class="x_panel">
              <div class="x_title">
                <a href="la-res-appln" class="btn btn-dark btn-xs pull-left">Back</a>
                Loan Appln Files & Guarantors
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <div id="wizard" class="form_wizard wizard_horizontal">
                  <ul class="wizard_steps">
                    <li>
                      <a href="#step-1">
                        <span class="step_no" style="background-color: #1ABB9C;">1</span>
                        <span class="step_descr">
                          Step 1<br />
                          <small>Select Loan Product</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-2" >
                        <span class="step_no" style="background-color: #1ABB9C;">2</span>
                        <span class="step_descr">
                          Step 2<br />
                          <small>Review Personal Info.</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-3">
                        <span class="step_no" style="background-color: #1ABB9C;">3</span>
                        <span class="step_descr">
                            Step 3<br />
                            <small>Enter Loan Details</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-4">
                        <span class="step_no" style="background-color: #006DAE;">4</span>
                        <span class="step_descr">
                            Step 4<br />
                            <small>Loan Docs. & Guarantors</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-5">
                        <span class="step_no" style="background-color: #D1F2F2;">5</span>
                        <span class="step_descr">
                            Step 5<br />
                            <small>Terms & Conditions</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-6">
                        <span class="step_no" style="background-color: #D1F2F2;">6</span>
                        <span class="step_descr">
                            Step 6<br />
                            <small>Signing & Submission</small>
                        </span>
                      </a>
                    </li>
                  </ul>
                </div>
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
                                  <th colspan="6" bgcolor="#EEE">Client List</th>
                                </tr>
                                <tr valign="top">
                                  <th>#</th>
                                  <th>Client Name</th>
                                  <th>Client Id</th>
                                  <th>External Id</th>
                                  <th>Activation Date</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                
                                $client_list = array();
                                $response_msg = FetchAllClients($MIFOS_CONN_DETAILS);
                                $CONN_FLG = $response_msg["CONN_FLG"];
                                $CORE_RESP = $response_msg["CORE_RESP"];
                                $client_list = $CORE_RESP["data"];

                                $x = 0;
                                for ($i=0; $i < sizeof($client_list); $i++) { 
                      
                                    $client = array();
                                    $client = $client_list[$i]["row"];
                                    $CLIENT_CORE_ID = $client[0];
                                    $CLIENT_CORE_ID_NUM = $client[1];
                                    $CLIENT_STATUS_ENUM = $client[2];
                                    $CLIENT_CORE_NAME = $client[3];
                                    $CLIENT_EXTERN_ID = $client[4];
                                    $CLIENT_ACTVN_DATE = $client[5];
      
                                    $data_transfer = $CLIENT_CORE_ID;
                                    $id3 = "FTT3".($i+1);
                                    $target3 = "#".$id3;
                                    $form_id3 = "FORM_".$id3;

                                    if ($CLIENT_CORE_ID==$DBB_CUST_CORE_ID) {
                                      // ... dont display
                                    }
                                    else {

                                      $exists = CheckIfGuarantor_Exists_LoanAppln_walkin($LN_APPLN_NO, $CLIENT_CORE_ID);
                                      if ($exists=="yes") {
                                        // ... dont display
                                      }
                                      else {
                                         ?>
                                          <tr valign="top">
                                            <td><?php echo ($x+1); ?>. </td>
                                            <td><?php echo $CLIENT_CORE_NAME; ?></td>
                                            <td><?php echo $CLIENT_CORE_ID_NUM; ?></td>
                                            <td><?php echo $CLIENT_EXTERN_ID; ?></td>
                                            <td><?php echo $CLIENT_ACTVN_DATE; ?></td>
                                            <td>
                                              <form method="post" id="<?php echo $form_id3; ?>">
                                                <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                                                <input type="hidden" id="G_CUST_CORE_ID" name="G_CUST_CORE_ID" value="<?php echo $CLIENT_CORE_ID; ?>">
                                                <input type="hidden" id="G_NAME" name="G_NAME" value="<?php echo $CLIENT_CORE_NAME; ?>">
                                                <button type="submit" class="btn btn-xs btn-primary" name="btn_add_grrtor">Add Guarantor</button>
                                              </form>
                                            </td>
                                          </tr>
                                          <?php
                                          $x++;
                                      }
                                     
                                    }
                                    
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

                      $grrt_list = FetchGuarantorPool_walkin($LN_APPLN_NO);
                      for ($i=0; $i < sizeof($grrt_list); $i++) { 
                        $g = array();
                        $g = $grrt_list[$i];
                        $G_RECORD_ID = $g['RECORD_ID'];
                        $LN_APPLN_NO = $g['LN_APPLN_NO'];
                        $G_CUST_CORE_ID = $g['G_CUST_CORE_ID'];
                        $G_NAME = $g['G_NAME'];
                        $GUARANTORSHIP_STATUS = $g['GUARANTORSHIP_STATUS'];

                        $f_id = "form_".($i+1);
                        ?>
                        <tr valign="top">
                          <td><?php echo ($i+1); ?>. </td>
                          <td><?php echo $G_NAME; ?></td>
                          <td><?php echo $GUARANTORSHIP_STATUS; ?></td>
                          <td>
                            <form method="post" id="<?php echo $f_id; ?>">
                              <input type="hidden" id="G_RECORD_ID" name="G_RECORD_ID" value="<?php echo $G_RECORD_ID; ?>">
                              <button type="submit" class="btn btn-danger btn-xs" name="btn_rem_grrt">Remove</button>
                            </form>
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
                    <input type="checkbox" id="tcs" name="tcs" required=""> The customer has agreed to the loan Terms & Condition.
                    <div class="ln_solid"></div>
                    <button type="submit" class="btn btn-success btn-lg pull-right" name="btn_submit_request">Submit Loan Application</button>
                    <?php
                  } elseif ($GUARANTORS_REQUIRED=="YES") {
                    $Q_CNT = "SELECT count(*) as RTN_VALUE FROM loan_appln_guarantors_walkin WHERE LN_APPLN_NO='$LN_APPLN_NO' AND GUARANTORSHIP_STATUS='ACTIVE'";
                    $CNT = ReturnOneEntryFromDB($Q_CNT);

                    if ($CNT>=$CNT_OF_RQRD_GURANTORS) {
                      ?>
                      <input type="checkbox" id="tcs" name="tcs" required=""> The customer has agreed to the loan Terms & Condition.
                      <div class="ln_solid"></div>
                      <button type="submit" class="btn btn-success btn-lg pull-right" name="btn_submit_request">Submit Loan Application</button>
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
