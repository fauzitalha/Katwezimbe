<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Information
$ACTIVATION_REF = trim($_GET['k']);

# ... Get Activation Details Information
$cstmr_actvn = array();
$cstmr_actvn = FetchActivationRequestById($ACTIVATION_REF);
$RECORD_ID= $cstmr_actvn['RECORD_ID'];
$ACTIVATION_REF= $cstmr_actvn['ACTIVATION_REF'];
$MMBSHP_TYPE= $cstmr_actvn['MMBSHP_TYPE'];
$CHANNEL_ID= $cstmr_actvn['CHANNEL_ID'];
$FIRST_NAME= $cstmr_actvn['FIRST_NAME'];
$MIDDLE_NAME= $cstmr_actvn['MIDDLE_NAME'];
$LAST_NAME= $cstmr_actvn['LAST_NAME'];
$GENDER= $cstmr_actvn['GENDER'];
$DOB= $cstmr_actvn['DOB'];
$BIO_DATA_VERIF_FLG= $cstmr_actvn['BIO_DATA_VERIF_FLG'];
$BIO_DATA_VERIF_RMKS= $cstmr_actvn['BIO_DATA_VERIF_RMKS'];
$BIO_DATA_VERIF_RMKS_BY= $cstmr_actvn['BIO_DATA_VERIF_RMKS_BY'];
$BIO_DATA_VERIF_RMKS_DATE= $cstmr_actvn['BIO_DATA_VERIF_RMKS_DATE'];
$EMAIL= $cstmr_actvn['EMAIL'];
$MOBILE_NO= $cstmr_actvn['MOBILE_NO'];
$PHYSICAL_ADDRESS= $cstmr_actvn['PHYSICAL_ADDRESS'];
$CONTACT_DATA_VERIF_FLG= $cstmr_actvn['CONTACT_DATA_VERIF_FLG'];
$CONTACT_DATA_VERIF_RMKS= $cstmr_actvn['CONTACT_DATA_VERIF_RMKS'];
$CONTACT_DATA_VERIF_BY= $cstmr_actvn['CONTACT_DATA_VERIF_BY'];
$CONTACT_DATA_VERIF_DATE= $cstmr_actvn['CONTACT_DATA_VERIF_DATE'];
$WORK_ID= $cstmr_actvn['WORK_ID'];
$WORK_ID_ATTCHMNT_FLG= $cstmr_actvn['WORK_ID_ATTCHMNT_FLG'];
$WORK_ID_FILE_NAME= $cstmr_actvn['WORK_ID_FILE_NAME'];
$NATIONAL_ID= $cstmr_actvn['NATIONAL_ID'];
$NATIONAL_ID_ATTCHMNT_FLG= $cstmr_actvn['NATIONAL_ID_ATTCHMNT_FLG'];
$NATIONAL_ID_FILE_NAME= $cstmr_actvn['NATIONAL_ID_FILE_NAME'];
$MAF_UPLOAD_FLG= $cstmr_actvn['MAF_UPLOAD_FLG'];
$MAF_UPLOAD_FILE_NAME= $cstmr_actvn['MAF_UPLOAD_FILE_NAME'];
$PASSPORT_PHOTO_UPLOAD_FLG= $cstmr_actvn['PASSPORT_PHOTO_UPLOAD_FLG'];
$PASSPORT_PHOTO_FILE_NAME= $cstmr_actvn['PASSPORT_PHOTO_FILE_NAME'];
$FILE_DATA_VERIF_FLG= $cstmr_actvn['FILE_DATA_VERIF_FLG'];
$FILE_DATA_VERIF_RMKS= $cstmr_actvn['FILE_DATA_VERIF_RMKS'];
$FILE_DATA_VERIF_BY= $cstmr_actvn['FILE_DATA_VERIF_BY'];
$FILE_DATA_VERIF_DATE= $cstmr_actvn['FILE_DATA_VERIF_DATE'];
$REQST_RECORD_DATE= $cstmr_actvn['REQST_RECORD_DATE'];
$VERIF_RMKS= $cstmr_actvn['VERIF_RMKS'];
$VERIF_DATE= $cstmr_actvn['VERIF_DATE'];
$VERIF_DATE= $cstmr_actvn['VERIF_DATE'];
$VERIF_BY= $cstmr_actvn['VERIF_BY'];
$APPRVL_RMKS= $cstmr_actvn['APPRVL_RMKS'];
$APPRVL_DATE= $cstmr_actvn['APPRVL_DATE'];
$APPRVD_BY= $cstmr_actvn['APPRVD_BY'];
$ACTIVATION_STATUS= $cstmr_actvn['ACTIVATION_STATUS'];


# ... File Paths and Links
$BASE = GetSystemParameter("NEW_CUST_ACTIVATION_BASEPATH")."/".$_SESSION['ORG_CODE'];
$WORK_ID_LNK = $BASE."/".$ACTIVATION_REF."/".$WORK_ID_FILE_NAME;
$NATIONAL_ID_LNK = $BASE."/".$ACTIVATION_REF."/".$NATIONAL_ID_FILE_NAME;
$MAF_LNK = $BASE."/".$ACTIVATION_REF."/".$MAF_UPLOAD_FILE_NAME;
$PP_LNK = $BASE."/".$ACTIVATION_REF."/".$PASSPORT_PHOTO_FILE_NAME;


# ... Approve Application
if (isset($_POST['btn_apprv_appln'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $APPRVD_BY = $_SESSION['UPR_USER_ID'];
  $APPRVL_RMKS = trim(mysql_real_escape_string($_POST['apprvl_rmk']));
  $APPRVL_DATE = GetCurrentDateTime();
  $ACTIVATION_STATUS = "APPROVED";

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET APPRVD_BY='$APPRVD_BY', APPRVL_RMKS='$APPRVL_RMKS', APPRVL_DATE='$APPRVL_DATE', ACTIVATION_STATUS='$ACTIVATION_STATUS' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "APPROVAL";
    $EVENT_OPERATION = "APPROVE_CUSTOMER_APPLN";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $APPRVL_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: This record has approved. Re-directing in 5 Seconds";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    //header("Refresh:0");
    header("Refresh:5; url=cm-apprv-applns");

  }
}


# ... Bounce back Application to stage  01
if (isset($_POST['btn_bb_appln'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $APPRVD_BY = $_SESSION['UPR_USER_ID'];
  $APPRVL_RMKS = trim(mysql_real_escape_string($_POST['bb_reason']));
  $APPRVL_DATE = GetCurrentDateTime();
  $ACTIVATION_STATUS = "BOUNCED";

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET APPRVD_BY='$APPRVD_BY', APPRVL_RMKS='$APPRVL_RMKS', APPRVL_DATE='$APPRVL_DATE', ACTIVATION_STATUS='$ACTIVATION_STATUS' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "APPROVAL";
    $EVENT_OPERATION = "BOUNCE_CUSTOMER_APPLN";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $APPRVL_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "WARNING";
    $alert_msg = "ALERT: Application has been bounced back to level 01. Re-directing in 5 Seconds";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    //header("Refresh:0");
    header("Refresh:5; url=cm-apprv-applns");

  }
}


# ... Reject Application
if (isset($_POST['btn_rej_appln'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $APPRVD_BY = $_SESSION['UPR_USER_ID'];
  $APPRVL_RMKS = trim(mysql_real_escape_string($_POST['rej_reason']));
  $APPRVL_DATE = GetCurrentDateTime();
  $ACTIVATION_STATUS = "REJECTED";

  $FIRST_NAME= $cstmr_actvn['FIRST_NAME'];
  $EMAIL= $cstmr_actvn['EMAIL'];
  $MOBILE_NO= $cstmr_actvn['MOBILE_NO'];

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET APPRVD_BY='$APPRVD_BY', APPRVL_RMKS='$APPRVL_RMKS', APPRVL_DATE='$APPRVL_DATE', ACTIVATION_STATUS='$ACTIVATION_STATUS' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "i-KHASAKH account rejection";
    $RECIPIENT_EMAILS = $EMAIL;
    $EMAIL_MESSAGE = mysql_real_escape_string("Dear ".$FIRST_NAME."<br>"
                    ."We are sorry to inform you that your application has been rejected.;<br>"
                    ."<br><b>REG REF:</b> ".$ACTIVATION_REF."."
                    ."<br><b>REASON:</b> ".$APPRVL_RMKS."."
                    ."Regards<br>"
                    ."Management<br>"
                    ."<i></i>");
    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

    $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
    ExecuteEntityInsert($qqq);

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "APPROVAL";
    $EVENT_OPERATION = "REJECT_CUSTOMER_APPLN";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $APPRVL_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "ERROR";
    $alert_msg = "ALERT: Application has been rejected. Re-directing in 5 Seconds";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    //header("Refresh:0");
    header("Refresh:5; url=cm-apprv-applns");

  }
}







?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Approve Application", $APP_SMALL_LOGO); 

    # ... Javascript
    LoadPriorityJS();
    OnLoadExecutions();
    StartTimeoutCountdown();
    ExecuteProcessStatistics();
    ?>

    <script type="text/javascript">
      function printContent(el) {
        var restorepage = document.body.innerHTML;
        var printcontent = document.getElementById(el).innerHTML;
        document.body.innerHTML = printcontent;
        window.print();
        document.body.innerHTML = restorepage;
      }


    </script>
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
                <a href="cm-apprv-applns" class="btn btn-sm btn-dark pull-left">Back</a>
                <h2>Approve Application <small><?php echo $ACTIVATION_REF; ?></small></h2>
                <button type="button" class="btn btn-danger btn-sm pull-right" data-toggle="modal" data-target="#reject">Reject</button>
                <div id="reject" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content" style="color: #333;">

                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel2">Reject Application</h4>
                      </div>
                      <div class="modal-body">
                          <form id="undobiooo" method="post">
                            <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                            You have chosen to reject the underlying application. Please provide reason why?
                            <br>
                            <textarea id="rej_reason" name="rej_reason" class="form-control" required=""></textarea><br>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_rej_appln">Save Rejection</button>
                            <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Cancel</button>
                          </form>
                      </div>
                    </div>
                  </div>
                </div>

				<button type="button" class="btn btn-warning btn-sm pull-right" data-toggle="modal" data-target="#bounce">Bounce</button>
                <div id="bounce" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content" style="color: #333;">

                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel2">Bounce Application</h4>
                      </div>
                      <div class="modal-body">
                          <form id="undobiooo" method="post">
                            <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                            You have chosen to bounce this application to level 01. Please provide reason why?
                            <br>
                            <textarea id="bb_reason" name="bb_reason" class="form-control" required=""></textarea><br>
                            <button type="submit" class="btn btn-warning btn-sm" name="btn_bb_appln">Bounce</button>
                            <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Cancel</button>
                          </form>
                      </div>
                    </div>
                  </div>
                </div>


			
                <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#sssss">Approve</button>
                <div id="sssss" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content" style="color: #333;">

                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel2">Approve Application</h4>
                      </div>
                      <div class="modal-body">
                          <form id="ffff" method="post">
                            <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                            Provide approval remark.
                            <br>
                            <textarea id="apprvl_rmk" name="apprvl_rmk" class="form-control" required=""></textarea><br>
                            <button type="submit" class="btn btn-success btn-sm" name="btn_apprv_appln">Save Approval</button>
                            <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Cancel</button>
                          </form>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="clearfix"></div>
              </div>

              <div class="x_content" id="page_data_data">   


                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="col-md-4 col-sm-6 col-xs-12">
                    <img width="100%" height="250px" src="<?php echo $PP_LNK; ?>"><br><br>
                  </div>
                  <div class="col-md-8 col-sm-6 col-xs-12">
                    <table class="table table-bordered">
                      <tr valign="top"><th width="20%">Appln Ref</th><td><?php echo $ACTIVATION_REF; ?></td></tr>
                      <tr valign="top"><th>Appln Status</th><td><?php echo $ACTIVATION_STATUS; ?></td></tr>
                      <tr valign="top"><th>Appln Type</th><td><?php echo $MMBSHP_TYPE; ?></td></tr>
                      <tr valign="top"><th>Appln Date</th><td><?php echo $REQST_RECORD_DATE; ?></td></tr>
                      <tr valign="top"><th>Verifcn Date</th><td><?php echo $VERIF_DATE; ?></td></tr>
                      <tr valign="top"><th>Approval Date</th><td><?php echo $APPRVL_DATE; ?></td></tr>
                    </table>
                  </div>
                </div>
                
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <strong>SECTION A: </strong> Bio Data
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                      <label for="fn">First Name * :</label>
                      <input type="text" id="fn" name="fn" class="form-control" disabled="" value="<?php echo $FIRST_NAME; ?>">

                      <label for="mn">Middle Name :</label>
                      <input type="text" id="mn" name="mn" class="form-control" disabled="" value="<?php echo $MIDDLE_NAME; ?>">

                      <label for="ln">Last Name / Surname * :</label>
                      <input type="text" id="ln" name="ln" class="form-control" disabled="" value="<?php echo $LAST_NAME; ?>">

                      <label for="gender">Gender * :</label>
                      <input type="text" id="gender" name="gender" class="form-control" disabled="" value="<?php echo $GENDER; ?>">

                      <label for="dob">Date of Birth * :</label><br>
                      <input type="text" id="dob" name="dob" class="form-control" disabled="" value="<?php echo $DOB; ?>">

                    </div>
                  </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <strong>SECTION B: </strong> Contact Details & Identification
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                      <label for="email1">Email * :</label>
                      <input type="email" id="email1" name="email1" class="form-control" disabled="" value="<?php echo $EMAIL; ?>">

                      <label for="phone1">Mobile Number * :</label>
                      <input type="number" id="phone1" name="phone1" class="form-control" disabled="" value="<?php echo $MOBILE_NO; ?>">

                      <label for="phy_address">Physical Address * :</label>
                      <textarea id="phy_address" name="phy_address" class="form-control" disabled=""><?php echo $PHYSICAL_ADDRESS; ?></textarea>

                      <label for="personal_id_doc_no">Work ID/Staff ID/Personal ID * :</label>
                      <input type="text" id="personal_id_doc_no" name="personal_id_doc_no" class="form-control" disabled="" value="<?php echo $WORK_ID; ?>">

                      <label for="nat_id_nin">National ID (NIN) *:</label>
                      <input type="text" id="nat_id_nin" name="nat_id_nin" class="form-control" disabled="" value="<?php echo $NATIONAL_ID; ?>">
                    </div>
                  </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <strong>SECTION C: </strong> File Attachments
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                      <table class="table table-striped table-bordered">
                        <thead>
                          <tr valign="top" bgcolor="#EEE"><th colspan="3">File Attachments</th></tr>
                          <tr valign="top"><th>#</th><th>Name</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                          <tr valign="top"><td>1.</td><td>Work ID/Staff ID/Personal ID</td>
                                <td><a href="<?php echo $WORK_ID_LNK; ?>" target="_blank" class="btn btn-xs btn-default">View File</a></td></tr>
                          <tr valign="top"><td>2.</td><td>National ID</td>
                                <td><a href="<?php echo $NATIONAL_ID_LNK; ?>" target="_blank" class="btn btn-xs btn-default">View File</a></td></tr>
                          <tr valign="top"><td>3.</td><td>Membership Application Form</td>
                                <td><a href="<?php echo $MAF_LNK; ?>" target="_blank" class="btn btn-xs btn-default">View File</a></td></tr>
                          <tr valign="top"><td>4.</td><td>Passport Photo</td>
                                <td><a href="<?php echo $PP_LNK; ?>" target="_blank" class="btn btn-xs btn-default">View File</a></td></tr>
                        </tbody>
                      </table>



                    </div>
                  </div>
                </div>
             
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <?php
                  if ($BIO_DATA_VERIF_FLG!="") {
                    # ... 02: Get Creator's Name
                    $BIO_DATA_VERIF_RMKS_BY_COREID = GetUserCoreIdFromWebApp($BIO_DATA_VERIF_RMKS_BY);
                    $response_msg = FetchUserDetailsFromCore($BIO_DATA_VERIF_RMKS_BY_COREID, $MIFOS_CONN_DETAILS);
                    //$CONN_FLG = $response_msg["CONN_FLG"];
                    //$RESP_FLG = $response_msg["RESP_FLG"];
                    $ADDED_CORE_RESP = $response_msg["CORE_RESP"];
                    $BIO_CORE_NAME = $ADDED_CORE_RESP["username"]." (".$ADDED_CORE_RESP["firstname"]." ".$ADDED_CORE_RESP["lastname"].")";
                    ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <div class="x_panel">
                        <div class="x_title">
                          <strong>Bio Data Verfcn</strong> 
                          <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                          The details supplied by the customer are matching with those on the customer's identification documents (National ID, Work Ids).<br>
                          <input type="text" class="form-control" value="<?php echo $BIO_DATA_VERIF_FLG ?>" disabled="" style="<?php echo ColorByStatusFlg($BIO_DATA_VERIF_FLG); ?>">

           
                          Additional Remarks *
                          <textarea class="form-control" disabled=""><?php echo $BIO_DATA_VERIF_RMKS; ?></textarea>
                          
                          Bio Data Remarks Made By *
                          <input type="text" class="form-control" value="<?php echo $BIO_CORE_NAME; ?>" disabled="">
                          Bio Data Remarks Made On *
                          <input type="text" class="form-control" value="<?php echo $BIO_DATA_VERIF_RMKS_DATE; ?>" disabled="">


                        </div>
                      </div>
                    </div>
                    <?php
                  }
                  ?>

                  <?php
                  if ($CONTACT_DATA_VERIF_FLG!="") {
                    $addd = explode('|', $CONTACT_DATA_VERIF_FLG);
                    $em_flg = $addd[0];
                    $pp_flg = $addd[1];

                    # ... 02: Get Creator's Name
                    $CONTACT_DATA_VERIF_BY_COREID = GetUserCoreIdFromWebApp($CONTACT_DATA_VERIF_BY);
                    $response_msg = FetchUserDetailsFromCore($CONTACT_DATA_VERIF_BY_COREID, $MIFOS_CONN_DETAILS);
                    //$CONN_FLG = $response_msg["CONN_FLG"];
                    //$RESP_FLG = $response_msg["RESP_FLG"];
                    $ADDED_CORE_RESP = $response_msg["CORE_RESP"];
                    $CONTACT_CORE_NAME = $ADDED_CORE_RESP["username"]." (".$ADDED_CORE_RESP["firstname"]." ".$ADDED_CORE_RESP["lastname"].")";
                    ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <div class="x_panel">
                        <div class="x_title">
                          <strong>Contact Details Verfcn</strong> 
                          <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                          Email belongs to Customer<br>
                          <input type="text" class="form-control" value="<?php echo $em_flg ?>" disabled="" style="<?php echo ColorByStatusFlg($em_flg); ?>">


                          Phone belongs to Customer<br>
                          <input type="text" class="form-control" value="<?php echo $pp_flg ?>" disabled="" style="<?php echo ColorByStatusFlg($pp_flg); ?>">

                          Additional Remarks *
                          <textarea id="con_rmks" name="con_rmks" class="form-control" disabled=""><?php echo $CONTACT_DATA_VERIF_RMKS ?></textarea>

                          Contact Remarks Made By *
                          <input type="text" class="form-control" value="<?php echo $CONTACT_CORE_NAME; ?>" disabled="">
                          Contact Remarks Made On *
                          <input type="text" class="form-control" value="<?php echo $CONTACT_DATA_VERIF_DATE; ?>" disabled="">

                        </div>
                      </div>
                    </div>
                    <?php
                  }
                  ?>

                  <?php
                  if ($FILE_DATA_VERIF_FLG!="") {
                    $addd = explode('|', $FILE_DATA_VERIF_FLG);
                    $wkid_flg = $addd[0];
                    $nin_flg = $addd[1];
                    $maf_flg = $addd[2];
                    $php_flg = $addd[3];

                    # ... 02: Get Creator's Name
                    $FILE_DATA_VERIF_COREID = GetUserCoreIdFromWebApp($FILE_DATA_VERIF_BY);
                    $response_msg = FetchUserDetailsFromCore($FILE_DATA_VERIF_COREID, $MIFOS_CONN_DETAILS);
                    //$CONN_FLG = $response_msg["CONN_FLG"];
                    //$RESP_FLG = $response_msg["RESP_FLG"];
                    $ADDED_CORE_RESP = $response_msg["CORE_RESP"];
                    $FILE_CORE_NAME = $ADDED_CORE_RESP["username"]." (".$ADDED_CORE_RESP["firstname"]." ".$ADDED_CORE_RESP["lastname"].")";

                    ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <div class="x_panel">
                        <div class="x_title">
                          <strong>Files Attchmnt Verfcn</strong> 
                          <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                          Work ID belongs to client & valid<br>
                          <input type="text" class="form-control" value="<?php echo $wkid_flg ?>" disabled="" style="<?php echo ColorByStatusFlg($wkid_flg); ?>">


                          National ID ibelongs to client & valid<br>
                          <input type="text" class="form-control" value="<?php echo $nin_flg ?>" disabled="" style="<?php echo ColorByStatusFlg($nin_flg); ?>">

                          Member Application Form belongs to client & valid<br>
                          <input type="text" class="form-control" value="<?php echo $maf_flg ?>" disabled="" style="<?php echo ColorByStatusFlg($maf_flg); ?>">

                          Passport Photo belongs to client & valid<br>
                          <input type="text" class="form-control" value="<?php echo $php_flg ?>" disabled="" style="<?php echo ColorByStatusFlg($php_flg); ?>">

                          Additional Remarks *
                          <textarea id="attmnt_rmks" name="attmnt_rmks" class="form-control" disabled=""><?php echo $FILE_DATA_VERIF_RMKS ?></textarea>
                          File Remarks Made By *
                          <input type="text" class="form-control" value="<?php echo $CONTACT_CORE_NAME; ?>" disabled="">
                          File Remarks Made On *
                          <input type="text" class="form-control" value="<?php echo $CONTACT_DATA_VERIF_DATE; ?>" disabled="">

                        </div>
                      </div>
                    </div>
                    <?php
                  }
                  ?>
                </div>
                

                <div class="col-md-12 col-sm-12 col-xs-12">

                  <?php
                  if ($ACTIVATION_STATUS=="VERIFIED") {
                    ?>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <div class="x_panel">
                        <div class="x_title">
                          <strong>OFFICIAL USE: </strong>For Verifier
                          <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                          <?php
                          # ... 02: Get Creator's Name
                          $VERIF_BY_COREID = GetUserCoreIdFromWebApp($VERIF_BY);
                          $response_msg = FetchUserDetailsFromCore($VERIF_BY_COREID, $MIFOS_CONN_DETAILS);
                          //$CONN_FLG = $response_msg["CONN_FLG"];
                          //$RESP_FLG = $response_msg["RESP_FLG"];
                          $ADDED_CORE_RESP = $response_msg["CORE_RESP"];
                          $VERIF_BY_NAME = $ADDED_CORE_RESP["username"]." (".$ADDED_CORE_RESP["firstname"]." ".$ADDED_CORE_RESP["lastname"].")";

                          ?>
                          <label for="fn">Verified By * :</label>
                          <input type="text" class="form-control" disabled="" value="<?php echo $VERIF_BY_NAME; ?>">

                          <label for="mn">Date Verified :</label>
                          <input type="text" class="form-control" disabled="" value="<?php echo $VERIF_DATE; ?>">

                          <label for="ln">Verifier Signature * :</label>
                          <input type="text" class="form-control" disabled="">

                        </div>
                      </div>
                    </div>
                    <?php
                  }

                  if ($ACTIVATION_STATUS=="APPROVED") {
                    ?>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <div class="x_panel">
                        <div class="x_title">
                          <strong>OFFICIAL USE: </strong> For Approver
                          <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                          <?php
                          # ... 02: Get Creator's Name
                          $APPRVD_BY_COREID = GetUserCoreIdFromWebApp($APPRVD_BY);
                          $response_msg = FetchUserDetailsFromCore($APPRVD_BY_COREID, $MIFOS_CONN_DETAILS);
                          //$CONN_FLG = $response_msg["CONN_FLG"];
                          //$RESP_FLG = $response_msg["RESP_FLG"];
                          $ADDED_CORE_RESP = $response_msg["CORE_RESP"];
                          $APPRVD_BY_NAME = $ADDED_CORE_RESP["username"]." (".$ADDED_CORE_RESP["firstname"]." ".$ADDED_CORE_RESP["lastname"].")";

                          ?>
                          <label for="fn">Approved By * :</label>
                          <input type="text" class="form-control" disabled="" value="<?php echo $APPRVD_BY_NAME; ?>">

                          <label for="mn">Date Approved :</label>
                          <input type="text" class="form-control" disabled="" value="<?php echo $APPRVL_DATE; ?>">

                          <label for="ln">Approver Signature * :</label>
                          <input type="text" class="form-control" disabled="">

                        </div>
                      </div>
                    </div>
                    <?php
                  }
                  ?>  
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
