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


# ... Process Verification Buttons
$display_buttons_details = array();
$BTN_DISP_FLG = "";
$BTN_DISP_MSG = "";

if( ($BIO_DATA_VERIF_FLG=="")||($CONTACT_DATA_VERIF_FLG=="")||($FILE_DATA_VERIF_FLG=="") ){
  $BTN_DISP_FLG = "";
  $BTN_DISP_MSG = "";
}
if( ($BIO_DATA_VERIF_FLG!="")&&($CONTACT_DATA_VERIF_FLG!="")&&($FILE_DATA_VERIF_FLG!="") ){
  $display_buttons_details = ProcessVerifButtonDisplay($BIO_DATA_VERIF_FLG, $CONTACT_DATA_VERIF_FLG, $FILE_DATA_VERIF_FLG);
  $BTN_DISP_FLG = $display_buttons_details["BTN_DISP_FLG"];
  $BTN_DISP_MSG = $display_buttons_details["BTN_DISP_MSG"];
}




# ... Bio Verification
if (isset($_POST['btn_sub_bioverif'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $bio_flg = trim($_POST['bio_flg']);
  $bio_rmks = trim($_POST['bio_rmks']);

  $BIO_DATA_VERIF_FLG= $bio_flg;
  $BIO_DATA_VERIF_RMKS= $bio_rmks;
  $BIO_DATA_VERIF_RMKS_BY= $_SESSION['UPR_USER_ID'];
  $BIO_DATA_VERIF_RMKS_DATE= GetCurrentDateTime();

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET BIO_DATA_VERIF_FLG='$BIO_DATA_VERIF_FLG', BIO_DATA_VERIF_RMKS='$BIO_DATA_VERIF_RMKS', BIO_DATA_VERIF_RMKS_BY='$BIO_DATA_VERIF_RMKS_BY', BIO_DATA_VERIF_RMKS_DATE='$BIO_DATA_VERIF_RMKS_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "VERIFY_CUST_BIO_DETAILS";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $BIO_DATA_VERIF_FLG."|".$BIO_DATA_VERIF_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "INFO";
    $alert_msg = "Bio Data verification details submitted successfully.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);


    header("Refresh:0");

  }
}

# ... Bio Verification Modification MODIFICATION
if (isset($_POST['btn_undo_bio'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $bio_flg = trim($_POST['bio_flg']);
  $bio_rmks = trim($_POST['bio_rmks']);

  $BIO_DATA_VERIF_FLG= $bio_flg;
  $BIO_DATA_VERIF_RMKS= $bio_rmks;
  $BIO_DATA_VERIF_RMKS_BY= $_SESSION['UPR_USER_ID'];
  $BIO_DATA_VERIF_RMKS_DATE= GetCurrentDateTime();

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET BIO_DATA_VERIF_FLG='$BIO_DATA_VERIF_FLG', BIO_DATA_VERIF_RMKS='$BIO_DATA_VERIF_RMKS', BIO_DATA_VERIF_RMKS_BY='$BIO_DATA_VERIF_RMKS_BY', BIO_DATA_VERIF_RMKS_DATE='$BIO_DATA_VERIF_RMKS_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "MODIFY_VERIFY_CUST_BIO_DETAILS";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $BIO_DATA_VERIF_FLG."|".$BIO_DATA_VERIF_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "WARNING";
    $alert_msg = "Bio Data verification details modified successfully.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    header("Refresh:0");


  }
}

# ... Contact Details Verification 
if (isset($_POST['btn_sub_contverif'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $em_flg = trim($_POST['em_flg']);
  $pp_flg = trim($_POST['pp_flg']);
  $con_rmks = trim($_POST['con_rmks']);

  $CONTACT_DATA_VERIF_FLG= $em_flg."|".$pp_flg;
  $CONTACT_DATA_VERIF_RMKS= $con_rmks;
  $CONTACT_DATA_VERIF_BY= $_SESSION['UPR_USER_ID'];
  $CONTACT_DATA_VERIF_DATE= GetCurrentDateTime();

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET CONTACT_DATA_VERIF_FLG='$CONTACT_DATA_VERIF_FLG', CONTACT_DATA_VERIF_RMKS='$CONTACT_DATA_VERIF_RMKS', CONTACT_DATA_VERIF_BY='$CONTACT_DATA_VERIF_BY', CONTACT_DATA_VERIF_DATE='$CONTACT_DATA_VERIF_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "CONTACT_DETAILS_VERIFICATION";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $CONTACT_DATA_VERIF_FLG."#".$CONTACT_DATA_VERIF_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "INFO";
    $alert_msg = "Contacts verification details submitted successfully.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    header("Refresh:0");
  }
}

# ... Contact Details Verification MODIFICATION
if (isset($_POST['btn_undo_con'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $em_flg = trim($_POST['em_flg']);
  $pp_flg = trim($_POST['pp_flg']);
  $con_rmks = trim($_POST['con_rmks']);

  $CONTACT_DATA_VERIF_FLG= $em_flg."|".$pp_flg;
  $CONTACT_DATA_VERIF_RMKS= $con_rmks;
  $CONTACT_DATA_VERIF_BY= $_SESSION['UPR_USER_ID'];
  $CONTACT_DATA_VERIF_DATE= GetCurrentDateTime();

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET CONTACT_DATA_VERIF_FLG='$CONTACT_DATA_VERIF_FLG', CONTACT_DATA_VERIF_RMKS='$CONTACT_DATA_VERIF_RMKS', CONTACT_DATA_VERIF_BY='$CONTACT_DATA_VERIF_BY', CONTACT_DATA_VERIF_DATE='$CONTACT_DATA_VERIF_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "MODIFY_CONTACT_DETAILS_VERIFICATION";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $CONTACT_DATA_VERIF_FLG."#".$CONTACT_DATA_VERIF_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "WARNING";
    $alert_msg = "Contacts verification modification details saved successfully.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    header("Refresh:0");

  }
}

# ... File Details Verification 
if (isset($_POST['btn_sub_attmtverif'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $wkid_flg = trim($_POST['wkid_flg']);
  $nin_flg = trim($_POST['nin_flg']);
  $maf_flg = trim($_POST['maf_flg']);
  $php_flg = trim($_POST['php_flg']);
  $attmnt_rmks = trim($_POST['attmnt_rmks']);

  $FILE_DATA_VERIF_FLG= $wkid_flg."|".$nin_flg."|".$maf_flg."|".$php_flg;
  $FILE_DATA_VERIF_RMKS= $attmnt_rmks;
  $FILE_DATA_VERIF_BY= $_SESSION['UPR_USER_ID'];
  $FILE_DATA_VERIF_DATE= GetCurrentDateTime();


  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET FILE_DATA_VERIF_FLG='$FILE_DATA_VERIF_FLG', FILE_DATA_VERIF_RMKS='$FILE_DATA_VERIF_RMKS', FILE_DATA_VERIF_BY='$FILE_DATA_VERIF_BY', FILE_DATA_VERIF_DATE='$FILE_DATA_VERIF_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "FILE_ATTMT_DETAILS_VERIFICATION";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $FILE_DATA_VERIF_FLG."#".$FILE_DATA_VERIF_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "INFO";
    $alert_msg = "File verification details submitted successfully.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    header("Refresh:0");


  }
}

# ... File Details Verification MODIFICATION
if (isset($_POST['btn_undo_file'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $wkid_flg = trim($_POST['wkid_flg']);
  $nin_flg = trim($_POST['nin_flg']);
  $maf_flg = trim($_POST['maf_flg']);
  $php_flg = trim($_POST['php_flg']);
  $attmnt_rmks = trim($_POST['attmnt_rmks']);

  $FILE_DATA_VERIF_FLG= $wkid_flg."|".$nin_flg."|".$maf_flg."|".$php_flg;
  $FILE_DATA_VERIF_RMKS= $attmnt_rmks;
  $FILE_DATA_VERIF_BY= $_SESSION['UPR_USER_ID'];
  $FILE_DATA_VERIF_DATE= GetCurrentDateTime();


  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET FILE_DATA_VERIF_FLG='$FILE_DATA_VERIF_FLG', FILE_DATA_VERIF_RMKS='$FILE_DATA_VERIF_RMKS', FILE_DATA_VERIF_BY='$FILE_DATA_VERIF_BY', FILE_DATA_VERIF_DATE='$FILE_DATA_VERIF_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "MODIFY_FILE_ATTMT_DETAILS_VERIFICATION";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $FILE_DATA_VERIF_FLG."#".$FILE_DATA_VERIF_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "WARNING";
    $alert_msg = "File verification modifications saved successfully.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    header("Refresh:0");
  }
}

# ... Submit Verfication
if (isset($_POST['btn_submit_actvn_verif'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $VERIF_BY = $_SESSION['UPR_USER_ID'];
  $VERIF_RMKS = trim($_POST['verif_rmks']);
  $VERIF_DATE = GetCurrentDateTime();
  $ACTIVATION_STATUS = "VERIFIED";

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET VERIF_BY='$VERIF_BY', VERIF_RMKS='$VERIF_RMKS', VERIF_DATE='$VERIF_DATE', ACTIVATION_STATUS='$ACTIVATION_STATUS' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "VERIFY_ACTIVATION_REQUEST";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $VERIF_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "INFO";
    $alert_msg = "Verification has been completed successfully. Seek approval from approver. Re-directing in 4 Seconds";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    //header("Refresh:0");
    header("Refresh:4; url=cm-new-self-enrollments");

  }
}

# ... Dont Approve Verfication
if (isset($_POST['btn_reject_actvn_verif'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $FP_EMAIL = trim($_POST['FP_EMAIL']);
  $FP_NAME = trim($_POST['FP_NAME']);
  $VERIF_BY = $_SESSION['UPR_USER_ID'];
  $VERIF_RMKS = trim($_POST['verif_rmks']);
  $VERIF_DATE = GetCurrentDateTime();
  $ACTIVATION_STATUS = "NEEDS_CUSTOMER_REVIEW";

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET VERIF_BY='$VERIF_BY', VERIF_RMKS='$VERIF_RMKS', VERIF_DATE='$VERIF_DATE', ACTIVATION_STATUS='$ACTIVATION_STATUS' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "i-KHASAKH registration (More Info. Needed)";
    $RECIPIENT_EMAILS = $FP_EMAIL;
    $EMAIL_MESSAGE = "Dear ".$FP_NAME."<br>"
                    ."Your registration request needs more information. Below are the details;<br>"
                    ."-------------------------------------------------------------------------------------------------<br>"
                    ."<b>REGISTRATION REF:</b> <i>".$ACTIVATION_REF."</i><br>"
                    ."<b>REMARKS:</b> <i>".$VERIF_RMKS."</i><br>"
                    ."-------------------------------------------------------------------------------------------------<br>"
                    ."<br/>"
                    ."You are requested to provide more data by going to TRACK APPLICATION. Use your registration reference.<br>"
                    ."Regards<br>"
                    ."Management<br>"
                    ."<i></i>";
    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

    $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
    ExecuteEntityInsert($qqq);

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "DONT_VERIFY_ACTIVATION_REQUEST";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $VERIF_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "ERROR";
    $alert_msg = "This record has not been verified. Customer should review and correct their application. Re-directing in 4 Seconds";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    //header("Refresh:0");
    header("Refresh:4; url=cm-new-self-enrollments");

  }
}


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Self Enrollment", $APP_SMALL_LOGO); 

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
                <a href="cm-new-self-enrollments" class="btn btn-sm btn-dark pull-left">Back</a>
                <h2>Self Enrollment Details</h2>
                <?php
                if ($BTN_DISP_FLG=="DISPLAY_VERIFY") {
                  ?>
                  <button class="btn btn-default btn-sm pull-right" onclick="printContent('page_data_data')"><i class="fa fa-print"></i> Print</button>
                  <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#verif_gd">Verify</button>
                  <div id="verif_gd" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-mm">
                      <div class="modal-content" style="color: #333;">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Verify Activation Request</h4>
                        </div>
                        <div class="modal-body">
                            <form id="verif_gd" method="post">
                              <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                              <b>Every things seems in order as shown below;</b><br>
                              <?php echo $BTN_DISP_MSG; ?><br>
                             

                              <b>Therefore you can proceed to verify this activation request;</b><br>
                              Additional Remarks *
                              <textarea id="verif_rmks" name="verif_rmks" class="form-control" required=""></textarea><br>
                              <button type="submit" class="btn btn-primary btn-sm" name="btn_submit_actvn_verif">Submit Verification</button>
                            </form>
                        </div>
                       

                      </div>
                    </div>
                  </div>
                  <?php
                }
                if ($BTN_DISP_FLG=="DISPLAY_DONT_VERIFY") {
                  ?>
                  <button class="btn btn-default btn-sm pull-right" onclick="printContent('page_data_data')"><i class="fa fa-print"></i> Print</button>
                  <button type="button" class="btn btn-danger btn-sm pull-right" data-toggle="modal" data-target="#verif_bd">Cannot Verify</button>
                  <div id="verif_bd" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-mm">
                      <div class="modal-content" style="color: #333;">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Cannot Verify Activation Request</h4>
                        </div>
                        <div class="modal-body">
                            <form id="verif_bd" method="post">
                              <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                              <input type="hidden" id="FP_EMAIL" name="FP_EMAIL" value="<?php echo $EMAIL; ?>">
                              <input type="hidden" id="FP_NAME" name="FP_NAME" value="<?php echo $FIRST_NAME; ?>">

                              <b>This request doesnot meet the requirements for verification because;</b><br>
                              <?php echo $BTN_DISP_MSG; ?><br>
                             

                              <b>Therefore you will have to return it to the applicant for correction;</b><br>
                              Additional Remarks * <small>(This will sent to the customer. You are encouraged to communicate clearly to prevent customer from misunderstanding.)</small>
                              <textarea id="verif_rmks" name="verif_rmks" class="form-control" required=""></textarea><br>
                              <button type="submit" class="btn btn-warning btn-sm" name="btn_reject_actvn_verif">Submit Rejection Remarks</button>
                            </form>
                        </div>
                       

                      </div>
                    </div>
                  </div>
                  <?php
                }

                ?>
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
                  if ($BIO_DATA_VERIF_FLG=="") {
                    ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <form id="bio" method="post">
                        <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                        <div class="x_panel">
                          <div class="x_title">
                            <strong>Bio Data Verfcn</strong> 
                            <button type="submit" class="btn btn-primary btn-xs pull-right" name="btn_sub_bioverif">Submit</button>
                            <div class="clearfix"></div>
                          </div>
                          <div class="x_content">

                            The details supplied by the customer are matching with those on the customer's identification documents (National ID, Work Ids).<br>
                            <select id="bio_flg" name="bio_flg" class="form-control" required="">
                              <option value="">Select Value</option>
                              <option value="YY">YES - They are matching</option>
                              <option value="NN">NO</option>
                            </select>

                            <br>
                            Additional Remarks *
                            <textarea id="bio_rmks" name="bio_rmks" class="form-control" required=""></textarea>

                          </div>
                        </div>
                      </form>
                    </div>
                    <?php
                  }

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
                          <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#undobio">Modify</button>
                          <div id="undobio" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content" style="color: #333;">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Modify Bio Verifications</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="undobiooo" method="post">
                                      <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                      The details supplied by the customer are matching with those on the customer's identification documents (National ID, Work Ids).<br>
                                      <select id="bio_flg" name="bio_flg" class="form-control" required="">
                                        <option value="">Select Value</option>
                                        <option value="YY">YES - They are matching</option>
                                        <option value="NN">NO</option>
                                      </select>

                                      <br>
                                      Additional Remarks *
                                      <textarea id="bio_rmks" name="bio_rmks" class="form-control" required=""></textarea><br>
                                      <button type="submit" class="btn btn-primary btn-sm" name="btn_undo_bio">Save Modifications</button>
                                    </form>
                                </div>
                               

                              </div>
                            </div>
                          </div>

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
                  if ($CONTACT_DATA_VERIF_FLG=="") {
                    ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <form id="contacts" method="post">
                        <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                        <div class="x_panel">
                          <div class="x_title">
                            <strong>Contact Details Verfcn</strong> 
                            <button type="submit" class="btn btn-primary btn-xs pull-right" name="btn_sub_contverif">Submit</button>
                            <div class="clearfix"></div>
                          </div>
                          <div class="x_content">

                            Email belongs to Customer<br>
                            <select id="em_flg" name="em_flg" class="form-control" required="">
                              <option value="">Select Value</option>
                              <option value="YY">YES</option>
                              <option value="NN">NO</option>
                            </select>

                            Phone belongs to Customer<br>
                            <select id="pp_flg" name="pp_flg" class="form-control" required="">
                              <option value="">Select Value</option>
                              <option value="YY">YES</option>
                              <option value="NN">NO</option>
                            </select>

                            <br>
                            Additional Remarks *
                            <textarea id="con_rmks" name="con_rmks" class="form-control" required=""></textarea>

                          </div>
                        </div>
                      </form>
                    </div>
                    <?php
                  }

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
                          <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#undocon">Modify</button>
                          <div id="undocon" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content" style="color: #333;">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Modify Contact Verifications</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="undobiooo" method="post">
                                      <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                      

                                      Email belongs to Customer<br>
                                      <select id="em_flg" name="em_flg" class="form-control" required="">
                                        <option value="">Select Value</option>
                                        <option value="YY">YES</option>
                                        <option value="NN">NO</option>
                                      </select>

                                      Phone belongs to Customer<br>
                                      <select id="pp_flg" name="pp_flg" class="form-control" required="">
                                        <option value="">Select Value</option>
                                        <option value="YY">YES</option>
                                        <option value="NN">NO</option>
                                      </select>

                                      <br>
                                      Additional Remarks *
                                      <textarea id="con_rmks" name="con_rmks" class="form-control" required=""></textarea><br>
                                      <button type="submit" class="btn btn-primary btn-sm" name="btn_undo_con">Save Contact Modifications</button>
                                    </form>
                                </div>
                              </div>
                            </div>
                          </div>
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
                  if ($FILE_DATA_VERIF_FLG=="") {
                    ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <form id="attcmnt" method="post">
                        <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                        <div class="x_panel">
                          <div class="x_title">
                            <strong>Files Attchmnt Verfcn</strong> 
                            <button type="submit" class="btn btn-primary btn-xs pull-right" name="btn_sub_attmtverif">Submit</button>
                            <div class="clearfix"></div>
                          </div>
                          <div class="x_content">

                            Work ID belongs to client & valid<br>
                            <select id="wkid_flg" name="wkid_flg" class="form-control" required="">
                              <option value="">Select Value</option>
                              <option value="YY">YES</option>
                              <option value="NN">NO</option>
                            </select>

                            National ID ibelongs to client & valid<br>
                            <select id="nin_flg" name="nin_flg" class="form-control" required="">
                              <option value="">Select Value</option>
                              <option value="YY">YES</option>
                              <option value="NN">NO</option>
                            </select>

                            Member Application Form belongs to client & valid<br>
                            <select id="maf_flg" name="maf_flg" class="form-control" required="">
                              <option value="">Select Value</option>
                              <option value="YY">YES</option>
                              <option value="NN">NO</option>
                            </select>

                            Passport Photo belongs to client & valid<br>
                            <select id="php_flg" name="php_flg" class="form-control" required="">
                              <option value="">Select Value</option>
                              <option value="YY">YES</option>
                              <option value="NN">NO</option>
                            </select>

                            <br>
                            Additional Remarks *
                            <textarea id="attmnt_rmks" name="attmnt_rmks" class="form-control" required=""></textarea>

                          </div>
                        </div>
                      </form>
                    </div>
                    <?php
                  }

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
                            <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#undofil">Modify</button>
                            <div id="undofil" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="color: #333;">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Modify File Attachments</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="undofileee" method="post">
                                        <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">

                                        Work ID belongs to client & valid<br>
                                        <select id="wkid_flg" name="wkid_flg" class="form-control" required="">
                                          <option value="">Select Value</option>
                                          <option value="YY">YES</option>
                                          <option value="NN">NO</option>
                                        </select>

                                        National ID ibelongs to client & valid<br>
                                        <select id="nin_flg" name="nin_flg" class="form-control" required="">
                                          <option value="">Select Value</option>
                                          <option value="YY">YES</option>
                                          <option value="NN">NO</option>
                                        </select>

                                        Member Application Form belongs to client & valid<br>
                                        <select id="maf_flg" name="maf_flg" class="form-control" required="">
                                          <option value="">Select Value</option>
                                          <option value="YY">YES</option>
                                          <option value="NN">NO</option>
                                        </select>

                                        Passport Photo belongs to client & valid<br>
                                        <select id="php_flg" name="php_flg" class="form-control" required="">
                                          <option value="">Select Value</option>
                                          <option value="YY">YES</option>
                                          <option value="NN">NO</option>
                                        </select>

                                        <br>
                                        Additional Remarks *
                                        <textarea id="attmnt_rmks" name="attmnt_rmks" class="form-control" required=""></textarea> <br>
                                       

                                        <button type="submit" class="btn btn-primary btn-sm" name="btn_undo_file">Save File Modifications</button>
                                      </form>
                                  </div>
                                </div>
                              </div>
                            </div>
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
                  if ($VERIF_RMKS=="VERIFIED") {
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

                  if ($APPRVL_RMKS=="APPROVED") {
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
