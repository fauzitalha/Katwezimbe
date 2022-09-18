<?php
session_start();
include("conf/no-session.php");

$_SESSION['ALERT_MSG'] = "";

# ... Receiving Information
$ACTIVATION_REF = trim($_SESSION['ACTIVATION_REF']);

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
$BASE = "../wvi-cst/files/activation_requests/".$_SESSION['ORG_CODE'];
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



# ... BIO DATA RESUBMISSION
if (isset($_POST['btn_resubmit_bio'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $BIO_DATA_VERIF_FLG = trim($_POST['BIO_DATA_VERIF_FLG']);
  $CONTACT_DATA_VERIF_FLG = trim($_POST['CONTACT_DATA_VERIF_FLG']);
  $FILE_DATA_VERIF_FLG = trim($_POST['FILE_DATA_VERIF_FLG']);

  $fn = mysql_real_escape_string(trim($_POST['fn']));
  $mn = mysql_real_escape_string(trim($_POST['mn']));
  $ln = mysql_real_escape_string(trim($_POST['ln']));
  $gn = mysql_real_escape_string(trim($_POST['gender']));
  $dob_dd = mysql_real_escape_string(trim($_POST['dob_dd']));
  $dob_mm = mysql_real_escape_string(trim($_POST['dob_mm']));
  $dob_yy = mysql_real_escape_string(trim($_POST['dob_yy']));
  $dob_conc = $dob_yy."-".$dob_mm."-".$dob_dd;

  $FIRST_NAME = $fn;
  $MIDDLE_NAME = $mn;
  $LAST_NAME = $ln;
  $GENDER = $gn;
  $DOB = $dob_conc;
  $RMKS = $FIRST_NAME."|".$MIDDLE_NAME."|".$LAST_NAME."|".$GENDER."|".$DOB;

  $BIO_DATA_VERIF_FLG = "";
  $BIO_DATA_VERIF_RMKS= "";
  $BIO_DATA_VERIF_RMKS_BY= "";
  $ACTIVATION_STATUS = ReflagActivationStatus($BIO_DATA_VERIF_FLG, $CONTACT_DATA_VERIF_FLG, $FILE_DATA_VERIF_FLG);

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET FIRST_NAME='$FIRST_NAME', MIDDLE_NAME='$MIDDLE_NAME', LAST_NAME='$LAST_NAME', GENDER='$GENDER', DOB='$DOB', BIO_DATA_VERIF_FLG='$BIO_DATA_VERIF_FLG', BIO_DATA_VERIF_RMKS='$BIO_DATA_VERIF_RMKS', BIO_DATA_VERIF_RMKS_BY='$BIO_DATA_VERIF_RMKS_BY', ACTIVATION_STATUS='$ACTIVATION_STATUS' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "APPLN_RESUBMISSION";
    $EVENT_OPERATION = "SUBMIT_AMENDED_BIODATA";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $RMKS;
    $INVOKER_ID = $ACTIVATION_REF;
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    if ($ACTIVATION_STATUS=="NEEDS_CUSTOMER_REVIEW") {
      $alert_type = "INFO";
      $alert_msg = "Bio data Amendment saved successfully. Complete other amendments so that your application can be resubmitted.";
    }
    if ($ACTIVATION_STATUS=="RESUBMITTED") {
      $alert_type = "SUCCESS";
      $alert_msg = "Application has been re-submitted for verification.";
    }
    
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    //header("Refresh:0");
    header("Refresh:4; url=cst-track-actvn-rqst-details");

  }  
}


# ... CONTACT DATA RESUBMISSION
if (isset($_POST['btn_resubmit_con'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $BIO_DATA_VERIF_FLG = trim($_POST['BIO_DATA_VERIF_FLG']);
  $CONTACT_DATA_VERIF_FLG = trim($_POST['CONTACT_DATA_VERIF_FLG']);
  $FILE_DATA_VERIF_FLG = trim($_POST['FILE_DATA_VERIF_FLG']);

  # ...: Contact_Details
  $email1 = mysql_real_escape_string(trim($_POST['email1']));
  $phone1 = mysql_real_escape_string(trim($_POST['phone1']));
  $phy_address = mysql_real_escape_string(trim($_POST['phy_address']));

  $EMAIL = $email1;
  $MOBILE_NO = $phone1;
  $PHYSICAL_ADDRESS = $phy_address;
  $RMKS = $EMAIL."|".$MOBILE_NO."|".$PHYSICAL_ADDRESS;

  $CONTACT_DATA_VERIF_FLG = "";
  $CONTACT_DATA_VERIF_RMKS= "";
  $CONTACT_DATA_VERIF_BY= "";
  $ACTIVATION_STATUS = ReflagActivationStatus($BIO_DATA_VERIF_FLG, $CONTACT_DATA_VERIF_FLG, $FILE_DATA_VERIF_FLG);

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET EMAIL='$EMAIL', MOBILE_NO='$MOBILE_NO', PHYSICAL_ADDRESS='$PHYSICAL_ADDRESS', CONTACT_DATA_VERIF_FLG='$CONTACT_DATA_VERIF_FLG', CONTACT_DATA_VERIF_RMKS='$CONTACT_DATA_VERIF_RMKS', CONTACT_DATA_VERIF_BY='$CONTACT_DATA_VERIF_BY', ACTIVATION_STATUS='$ACTIVATION_STATUS' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "APPLN_RESUBMISSION";
    $EVENT_OPERATION = "SUBMIT_AMENDED_CONTACT_DATA";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $RMKS;
    $INVOKER_ID = $ACTIVATION_REF;
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    if ($ACTIVATION_STATUS=="NEEDS_CUSTOMER_REVIEW") {
      $alert_type = "INFO";
      $alert_msg = "Contact data Amendment saved successfully. Complete other amendments so that your application can be resubmitted.";
    }
    if ($ACTIVATION_STATUS=="RESUBMITTED") {
      $alert_type = "SUCCESS";
      $alert_msg = "Application has been re-submitted for verification.";
    }
    
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    header("Refresh:4; url=cst-track-actvn-rqst-details");


  }
}


# ... FILE DATA RESUBMISSION
if (isset($_POST['btn_resubmit_file'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $BIO_DATA_VERIF_FLG = trim($_POST['BIO_DATA_VERIF_FLG']);
  $CONTACT_DATA_VERIF_FLG = trim($_POST['CONTACT_DATA_VERIF_FLG']);
  $FILE_DATA_VERIF_FLG = trim($_POST['FILE_DATA_VERIF_FLG']);
  $FILE_DETAILS = "";
  $FILE_UPLOAD_FLAGS = "";

  $FILE_UPLOAD_REMARKS = array();
  $valid_file_types = array("image/gif", "image/jpeg", "image/png", "application/pdf");
  $valid_file_extensions = array("gif", "png", "jpg", "jpeg", "pdf");


  # ... 00: CREATING ACTIVATION DIRECTORY
  $ACTVN_RQSTS_BASE_FILEPATH = GetSystemParameter("ACTVN_RQSTS_BASE_FILEPATH")."/".$_SESSION['ORG_CODE'];
  //echo $ACTVN_RQSTS_BASE_FILEPATH;
  $AR_DIR = $ACTVN_RQSTS_BASE_FILEPATH."/".$ACTIVATION_REF;
  $dir = $AR_DIR;
  if (!is_dir($AR_DIR)) {
    mkdir($AR_DIR);
  }

  # ... FILE 01: WORK_ID ATTACHMENT
  if (isset($_FILES['personal_id_doc_attcnt'])) {
    // ----------------------------------------------------------------------------------------------------------------
    $file_size = $_FILES['personal_id_doc_attcnt']['size'];
    $file_type = $_FILES['personal_id_doc_attcnt']['type'];
    $file_ext = strtolower(substr(strrchr($_FILES['personal_id_doc_attcnt']['name'],"."),1));
    $file_name = "WorkID_".date("YmdHis", time())."_".$ACTIVATION_REF.".".$file_ext;

    $required_specs = array();
    $required_specs["FILE_SIZE"] = 5000000;       // ... 3MB    
    $required_specs["FILE_TYPES"] = $valid_file_types; 
    $required_specs["FILE_EXTENSIONS"] = $valid_file_extensions; 

    $file_specs = array();
    $file_specs["FILE_SIZE"] = $file_size;                        
    $file_specs["FILE_TYPE"] = $file_type; 
    $file_specs["FILE_EXTENSION"] = $file_ext; 
    $file_results = ValidateFileAttachment($required_specs, $file_specs);
    $FILE_SIZE_CHK = $file_results["FILE_SIZE_CHK"];
    $FILE_TYPE_CHK = $file_results["FILE_TYPE_CHK"];
    $FILE_EXTSN_CHK = $file_results["FILE_EXTSN_CHK"];
    $FILE_RMKS = $file_results["FILE_RMKS"];
	//echo $file_specs["FILE_SIZE"]; die();


    if ($FILE_SIZE_CHK&&$FILE_TYPE_CHK&&$FILE_EXTSN_CHK) {
      $result = move_uploaded_file($_FILES['personal_id_doc_attcnt']['tmp_name'], $dir."/".$file_name);
      if($result == 1){
        $WORK_ID_ATTCHMNT_FLG = "YY";
        $WORK_ID_FILE_NAME =  $file_name;

        $q33 = "UPDATE cstmrs_actvn_rqsts SET WORK_ID_ATTCHMNT_FLG='$WORK_ID_ATTCHMNT_FLG', WORK_ID_FILE_NAME='$WORK_ID_FILE_NAME' 
                WHERE ACTIVATION_REF='$ACTIVATION_REF'";
        $update_response33 = ExecuteEntityUpdate($q33);
        if ($update_response33=="EXECUTED") {
          $FILE_UPLOAD_REMARKS["WORKID_RMKS"] = "WorkID Uploaded Successfully.";
          $FILE_DETAILS = $FILE_DETAILS."|".$file_name;
          $FILE_UPLOAD_FLAGS = $FILE_UPLOAD_FLAGS."|".$FILE_UPLOAD_REMARKS["WORKID_RMKS"];
        }
      }
    } else {
      $FILE_UPLOAD_REMARKS["WORKID_RMKS"] = "WorkID Not Uploaded. REASON: ".$FILE_RMKS;
      $FILE_DETAILS = $FILE_DETAILS."|".$file_name;
      $FILE_UPLOAD_FLAGS = $FILE_UPLOAD_FLAGS."|".$FILE_UPLOAD_REMARKS["WORKID_RMKS"];
    }
    // ----------------------------------------------------------------------------------------------------------------
  }

  # ... FILE 02: NATIONAL_ID ATTACHMENT
  if (isset($_FILES['nat_id_attchmnt'])) {
    // ----------------------------------------------------------------------------------------------------------------
    $file_size = $_FILES['nat_id_attchmnt']['size'];
    $file_type = $_FILES['nat_id_attchmnt']['type'];
    $file_ext = strtolower(substr(strrchr($_FILES['nat_id_attchmnt']['name'],"."),1));
    $file_name = "NATIONALID_".date("YmdHis", time())."_".$ACTIVATION_REF.".".$file_ext;

    $required_specs = array();
    $required_specs["FILE_SIZE"] = 5000000;        // ... 3MB        
    $required_specs["FILE_TYPES"] = $valid_file_types; 
    $required_specs["FILE_EXTENSIONS"] = $valid_file_extensions; 

    $file_specs = array();
    $file_specs["FILE_SIZE"] = $file_size;                        
    $file_specs["FILE_TYPE"] = $file_type; 
    $file_specs["FILE_EXTENSION"] = $file_ext; 
    $file_results = ValidateFileAttachment($required_specs, $file_specs);
    $FILE_SIZE_CHK = $file_results["FILE_SIZE_CHK"];
    $FILE_TYPE_CHK = $file_results["FILE_TYPE_CHK"];
    $FILE_EXTSN_CHK = $file_results["FILE_EXTSN_CHK"];
    $FILE_RMKS = $file_results["FILE_RMKS"];

    if ($FILE_SIZE_CHK&&$FILE_TYPE_CHK&&$FILE_EXTSN_CHK) {
      $result = move_uploaded_file($_FILES['nat_id_attchmnt']['tmp_name'], $dir."/".$file_name);
      if($result == 1){
        $NATIONAL_ID_ATTCHMNT_FLG = "YY";
        $NATIONAL_ID_FILE_NAME =  $file_name;


        $q33 = "UPDATE cstmrs_actvn_rqsts SET NATIONAL_ID_ATTCHMNT_FLG='$NATIONAL_ID_ATTCHMNT_FLG', 
                NATIONAL_ID_FILE_NAME='$NATIONAL_ID_FILE_NAME' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
        $update_response33 = ExecuteEntityUpdate($q33);
        if ($update_response33=="EXECUTED") {
          $FILE_UPLOAD_REMARKS["NATIONALID_RMKS"] = "National Id Uploaded Successfully.";
          $FILE_DETAILS = $FILE_DETAILS."|".$file_name;
          $FILE_UPLOAD_FLAGS = $FILE_UPLOAD_FLAGS."|".$FILE_UPLOAD_REMARKS["NATIONALID_RMKS"];
        }
      }
    } else {
      $FILE_UPLOAD_REMARKS["NATIONALID_RMKS"] = "National Id Not Uploaded. REASON: ".$FILE_RMKS;
      $FILE_DETAILS = $FILE_DETAILS."|".$file_name;
      $FILE_UPLOAD_FLAGS = $FILE_UPLOAD_FLAGS."|".$FILE_UPLOAD_REMARKS["NATIONALID_RMKS"];
    }
    // ----------------------------------------------------------------------------------------------------------------
  }

  # ... FILE 03: MAF ATTACHMENT
  if (isset($_FILES['maf_attchmnt'])) {
    // ----------------------------------------------------------------------------------------------------------------
    $file_size = $_FILES['maf_attchmnt']['size'];
    $file_type = $_FILES['maf_attchmnt']['type'];
    $file_ext = strtolower(substr(strrchr($_FILES['maf_attchmnt']['name'],"."),1));
    $file_name = "MAF_".date("YmdHis", time())."_".$ACTIVATION_REF.".".$file_ext;

    $required_specs = array();
    $required_specs["FILE_SIZE"] = 5000000;        // ... 3MB                 
    $required_specs["FILE_TYPES"] = $valid_file_types; 
    $required_specs["FILE_EXTENSIONS"] = $valid_file_extensions; 

    $file_specs = array();
    $file_specs["FILE_SIZE"] = $file_size;                        
    $file_specs["FILE_TYPE"] = $file_type; 
    $file_specs["FILE_EXTENSION"] = $file_ext; 
    $file_results = ValidateFileAttachment($required_specs, $file_specs);
    $FILE_SIZE_CHK = $file_results["FILE_SIZE_CHK"];
    $FILE_TYPE_CHK = $file_results["FILE_TYPE_CHK"];
    $FILE_EXTSN_CHK = $file_results["FILE_EXTSN_CHK"];
    $FILE_RMKS = $file_results["FILE_RMKS"];

    if ($FILE_SIZE_CHK&&$FILE_TYPE_CHK&&$FILE_EXTSN_CHK) {
      $result = move_uploaded_file($_FILES['maf_attchmnt']['tmp_name'], $dir."/".$file_name);
      if($result == 1){
        $MAF_UPLOAD_FLG = "YY";
        $MAF_UPLOAD_FILE_NAME =  $file_name;

        $q33 = "UPDATE cstmrs_actvn_rqsts SET MAF_UPLOAD_FLG='$MAF_UPLOAD_FLG', 
                MAF_UPLOAD_FILE_NAME='$MAF_UPLOAD_FILE_NAME' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
        $update_response33 = ExecuteEntityUpdate($q33);
        if ($update_response33=="EXECUTED") {
          $FILE_UPLOAD_REMARKS["MAF_RMKS"] = "Membership Application Form Uploaded Successfully.";
          $FILE_DETAILS = $FILE_DETAILS."|".$file_name;
          $FILE_UPLOAD_FLAGS = $FILE_UPLOAD_FLAGS."|".$FILE_UPLOAD_REMARKS["MAF_RMKS"];
        }
      }
    } else {
      $FILE_UPLOAD_REMARKS["MAF_RMKS"] = "Membership Application Form Not Uploaded. REASON: ".$FILE_RMKS;
      $FILE_DETAILS = $FILE_DETAILS."|".$file_name;
      $FILE_UPLOAD_FLAGS = $FILE_UPLOAD_FLAGS."|".$FILE_UPLOAD_REMARKS["MAF_RMKS"];
    }
    // ----------------------------------------------------------------------------------------------------------------
  }

  # ... FILE 04: PASSPORT PHOTO
  if (isset($_FILES['passport_photo'])) {
    // ----------------------------------------------------------------------------------------------------------------
    $file_size = $_FILES['passport_photo']['size'];
    $file_type = $_FILES['passport_photo']['type'];
    $file_ext = strtolower(substr(strrchr($_FILES['passport_photo']['name'],"."),1));
    $file_name = "PP_".date("YmdHis", time())."_".$ACTIVATION_REF.".".$file_ext;

    $required_specs = array();
    $required_specs["FILE_SIZE"] = 5000000;        // ... 3MB     
    $required_specs["FILE_TYPES"] = $valid_file_types; 
    $required_specs["FILE_EXTENSIONS"] = $valid_file_extensions; 

    $file_specs = array();
    $file_specs["FILE_SIZE"] = $file_size;                        
    $file_specs["FILE_TYPE"] = $file_type; 
    $file_specs["FILE_EXTENSION"] = $file_ext; 
    $file_results = ValidateFileAttachment($required_specs, $file_specs);
    $FILE_SIZE_CHK = $file_results["FILE_SIZE_CHK"];
    $FILE_TYPE_CHK = $file_results["FILE_TYPE_CHK"];
    $FILE_EXTSN_CHK = $file_results["FILE_EXTSN_CHK"];
    $FILE_RMKS = $file_results["FILE_RMKS"];

    if ($FILE_SIZE_CHK&&$FILE_TYPE_CHK&&$FILE_EXTSN_CHK) {
      $result = move_uploaded_file($_FILES['passport_photo']['tmp_name'], $dir."/".$file_name);
      if($result == 1){
        $PASSPORT_PHOTO_UPLOAD_FLG = "YY";
        $PASSPORT_PHOTO_FILE_NAME =  $file_name;

        $q33 = "UPDATE cstmrs_actvn_rqsts SET PASSPORT_PHOTO_UPLOAD_FLG='$PASSPORT_PHOTO_UPLOAD_FLG', 
                PASSPORT_PHOTO_FILE_NAME='$PASSPORT_PHOTO_FILE_NAME' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
        $update_response33 = ExecuteEntityUpdate($q33);
        if ($update_response33=="EXECUTED") {
          $FILE_UPLOAD_REMARKS["PP_RMKS"] = "Passport Photo Uploaded Successfully.";
          $FILE_DETAILS = $FILE_DETAILS."|".$file_name;
          $FILE_UPLOAD_FLAGS = $FILE_UPLOAD_FLAGS."|".$FILE_UPLOAD_REMARKS["PP_RMKS"];
        }
      }
    } else {
      $FILE_UPLOAD_REMARKS["PP_RMKS"] = "Passport Photo Not Uploaded. REASON: ".$FILE_RMKS;
      $FILE_DETAILS = $FILE_DETAILS."|".$file_name;
      $FILE_UPLOAD_FLAGS = $FILE_UPLOAD_FLAGS."|".$FILE_UPLOAD_REMARKS["PP_RMKS"];
    }
    // ----------------------------------------------------------------------------------------------------------------
  }
        

  # ... FILE 05: SUMMARY OF SUBMITTION
  // ----------------------------------------------------------------------------------------------------------------
  $FILE_DATA_VERIF_FLG = "";
  $FILE_DATA_VERIF_RMKS= "";
  $FILE_DATA_VERIF_BY= "";
  $ACTIVATION_STATUS = ReflagActivationStatus($BIO_DATA_VERIF_FLG, $CONTACT_DATA_VERIF_FLG, $FILE_DATA_VERIF_FLG);
  $RMKS = "{".$FILE_DETAILS."}#{".$FILE_UPLOAD_FLAGS."}";

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET FILE_DATA_VERIF_FLG='$FILE_DATA_VERIF_FLG', FILE_DATA_VERIF_RMKS='$FILE_DATA_VERIF_RMKS', FILE_DATA_VERIF_BY='$FILE_DATA_VERIF_BY', ACTIVATION_STATUS='$ACTIVATION_STATUS' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "APPLN_RESUBMISSION";
    $EVENT_OPERATION = "SUBMIT_AMENDED_FILE_DATA";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = $RMKS;
    $INVOKER_ID = $ACTIVATION_REF;
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    if ($ACTIVATION_STATUS=="NEEDS_CUSTOMER_REVIEW") {
      $alert_type = "INFO";
      $alert_msg = "File amendments uploaded and saved successfully. Complete other amendments so that your application can be resubmitted.";
    }
    if ($ACTIVATION_STATUS=="RESUBMITTED") {
      $alert_type = "SUCCESS";
      $alert_msg = "Application has been re-submitted for verification";
    }
    
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    //header("Refresh:0");
    header("Refresh:4; url=cst-track-actvn-rqst-details");
  }
}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php     
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Track Request", $APP_SMALL_LOGO); 
    ?>   
    
  </head>

  <body>

    <div style="background: #FFF;">

      <!-- top navigation -->
      <div class="top_nav">
        <div class="nav_menu">
            <ul class="nav navbar-nav navbar-right">
              <li class="list-group-item-success"><a href="cst-acct-actvn">Account Activation</a></li>
              <li class="list-group-item-danger"><a href="cst-lgin">Sign In</a></li>
              <li><a href="index"><?php echo $APP_NAME; ?></a></li>
            </ul>
        </div>
        <div class="clearfix"></div>
      </div>
      
      <!-- /top navigation -->



      <!-- article feed -->
      <div class="row">
        <div class="col-md-2 col-sm-0 col-xs-0">
        </div>

        <div class="col-md-9 col-sm-12 col-xs-12">
          <!-- System Message Area -->
          <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>

          <div class="x_panel">
            <a href="cst-acct-actvn" class="btn btn-sm btn-dark pull-left">Back</a>
            <div class="x_title">
              <h2>Activation Request Details</h2>
              
              <div class="clearfix"></div>
            </div>
            <div class="x_content" id="page_data_data">   


                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="col-md-6 col-sm-6 col-xs-12">
                  
                      <table class="table table-bordered table-striped">
                        <tr valign="top"><th>Appln Ref</th><td><?php echo $ACTIVATION_REF; ?></td></tr>
                        <tr valign="top"><th>Appln Status</th><td><?php echo $ACTIVATION_STATUS; ?></td></tr>
                        <tr valign="top"><th>Appln Type</th><td><?php echo $MMBSHP_TYPE; ?></td></tr>
                        <tr valign="top"><th>Appln Date</th><td><?php echo $REQST_RECORD_DATE; ?></td></tr>
                        <tr valign="top"><th>Review Date</th><td><?php echo $VERIF_DATE; ?></td></tr>
                      </table>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <strong>Verification Remarks </strong>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                      <?php echo $BTN_DISP_MSG; ?>
                      
                    </div>
                  </div>
                </div>

                </div>

                 <div class="col-md-12 col-sm-12 col-xs-12">
                  <?php
                  if ($BIO_DATA_VERIF_FLG!="") {
                    ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <div class="x_panel">
                        <div class="x_title">
                          <strong>Personal Data Verfcn</strong> 

                          <?php 
                          if ($BIO_DATA_VERIF_FLG=="NN") {
                            ?>
                            <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#undobio">Amend</button>
                            <?php
                          }
                          ?>
                          <div id="undobio" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content" style="color: #333;">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Amend Bio Data</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="undobiooo" method="post">
                                      <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                      <input type="hidden" id="BIO_DATA_VERIF_FLG" name="BIO_DATA_VERIF_FLG" value="<?php echo $BIO_DATA_VERIF_FLG; ?>">
                                      <input type="hidden" id="CONTACT_DATA_VERIF_FLG" name="CONTACT_DATA_VERIF_FLG" value="<?php echo $CONTACT_DATA_VERIF_FLG; ?>">
                                      <input type="hidden" id="FILE_DATA_VERIF_FLG" name="FILE_DATA_VERIF_FLG" value="<?php echo $FILE_DATA_VERIF_FLG; ?>">
                                      
                                      <label for="fn">First Name * :</label>
                                      <input type="text" id="fn" name="fn" class="form-control" value="<?php echo $FIRST_NAME; ?>" required="">

                                      <label for="mn">Middle Name :</label>
                                      <input type="text" id="mn" name="mn" class="form-control" value="<?php echo $MIDDLE_NAME; ?>" >

                                      <label for="ln">Last Name / Surname * :</label>
                                      <input type="text" id="ln" name="ln" class="form-control" value="<?php echo $LAST_NAME; ?>" required="">

                                      <?php
                                      $selected = "";
                                      if ($GENDER=="M") { 
                                        ?>
                                        <label for="gender">Gender * :</label>
                                        <select id="gender" name="gender" class="form-control" required="">
                                          <option value="">Select Gender</option>
                                          <option value="M" selected="">Male</option>
                                          <option value="F">Female</option>
                                          <option value="O">Other</option>
                                        </select>
                                        <?php
                                      }
                                      if ($GENDER=="F") { 
                                        ?>
                                        <label for="gender">Gender * :</label>
                                        <select id="gender" name="gender" class="form-control" required="">
                                          <option value="">Select Gender</option>
                                          <option value="M">Male</option>
                                          <option value="F" selected="">Female</option>
                                          <option value="O">Other</option>
                                        </select>
                                        <?php
                                      }
                                      if ($GENDER=="O") {
                                        ?>
                                        <label for="gender">Gender * :</label>
                                        <select id="gender" name="gender" class="form-control" required="">
                                          <option value="">Select Gender</option>
                                          <option value="M">Male</option>
                                          <option value="F">Female</option>
                                          <option value="O" selected="">Other</option>
                                        </select>
                                        <?php
                                      }
                                      ?>
                                      

                                      <?php
                                      $date_details = array();
                                      $date_details = explode('-', $DOB);
                                      $yyyy = $date_details[0];
                                      $mmmm = $date_details[1];
                                      $dddd = $date_details[2];
                                      ?>
                                      <label for="dob">Date of Birth * :</label><br>
                                      <select id="dob_dd" name="dob_dd" required="">
                                        <option value="">Day</option>
                                        <?php
                                        for ($i=1; $i < 32; $i++) { 

                                          if ($i==$dddd) {
                                            ?>
                                            <option selected="" value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php
                                          } else {
                                            ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php
                                          }

                                        }
                                        ?>
                                      </select>
                                      <select id="dob_mm" name="dob_mm" required="">
                                        <option value="">Month</option>
                                        <?php
                                        $months = array("Jan","Feb","March","April","May","June","July","Aug","Sep","Oct","Nov","Dec");
                                        for ($i=0; $i < 12; $i++) { 

                                          if ($i==$mmmm) {
                                            ?>
                                            <option selected="" value="<?php echo ($i+1); ?>"><?php echo $months[$i]; ?></option>
                                            <?php
                                          } else {
                                            ?>
                                            <option value="<?php echo ($i+1); ?>"><?php echo $months[$i]; ?></option>
                                            <?php
                                          }
                                        }
                                        ?>
                                      </select>
                                      <select id="dob_yy" name="dob_yy" required="">
                                        <option value="">Year</option>
                                        <?php
                                        $current_year = date("Y", time());
                                        for ($i=1900; $i < ($current_year+1); $i++) { 

                                          if ($i==$yyyy) {
                                            ?>
                                            <option selected="" value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php
                                          } else {
                                            ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php
                                          }
                                          
                                        }
                                        ?>
                                      </select>

                                      <br>
                                      <br>
                                      <button type="submit" class="btn btn-primary btn-sm" name="btn_resubmit_bio">Save Bio Data</button>
                                    </form>
                                </div>
                               

                              </div>
                            </div>
                          </div>

                          <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                          In this section, we compared your supplied personal data against your application documents i.e. National ID, Work Id<br><br>

                          <label>Verification Result * :</label>
                          <input type="text" class="form-control" value="<?php echo PassOrFail($BIO_DATA_VERIF_FLG); ?>" disabled="" style="<?php echo ColorByStatusFlg($BIO_DATA_VERIF_FLG); ?>">

           
                          <label>Verification Remarks * :</label>
                          <textarea class="form-control" disabled=""><?php echo $BIO_DATA_VERIF_RMKS; ?></textarea>
                          

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
                    ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <div class="x_panel">
                        <div class="x_title">
                          <strong>Contact Details Verfcn</strong> 
                          <?php
                          if ( ($em_flg=="NN")||($pp_flg=="NN") ) {
                            ?>
                            <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#undocon">Amend</button>
                            <?php
                          }
                          ?>
                          <div id="undocon" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content" style="color: #333;">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Amend Contact Verifications</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="undobiooo" method="post">
                                      <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                      <input type="hidden" id="BIO_DATA_VERIF_FLG" name="BIO_DATA_VERIF_FLG" value="<?php echo $BIO_DATA_VERIF_FLG; ?>">
                                      <input type="hidden" id="CONTACT_DATA_VERIF_FLG" name="CONTACT_DATA_VERIF_FLG" value="<?php echo $CONTACT_DATA_VERIF_FLG; ?>">
                                      <input type="hidden" id="FILE_DATA_VERIF_FLG" name="FILE_DATA_VERIF_FLG" value="<?php echo $FILE_DATA_VERIF_FLG; ?>">
                                      
                                      <label for="email1">Email * :</label>
                                      <input type="email" id="email1" name="email1" class="form-control" value="<?php echo $EMAIL; ?>" style="<?php echo ColorByStatusFlg($em_flg); ?>" required="">

                                      <label for="phone1">Mobile Number * :</label>
                                      <input type="number" id="phone1" name="phone1" class="form-control" value="<?php echo $MOBILE_NO; ?>" style="<?php echo ColorByStatusFlg($pp_flg); ?>" required="">

                                      <label for="phy_address">Physical Address * :</label>
                                      <textarea id="phy_address" name="phy_address" class="form-control" required="required"><?php echo $PHYSICAL_ADDRESS; ?></textarea>

                                      <br>
                                      <br>
                                      <button type="submit" class="btn btn-primary btn-sm" name="btn_resubmit_con">Save Contact Modifications</button>
                                    </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                          In this section, we checked if contacts details supplied are owned by you. i.e. They are in your names.<br><br>

                          <label>Email Verification Result :</label>
                          <input type="text" class="form-control" value="<?php echo PassOrFail($em_flg); ?>" disabled="" style="<?php echo ColorByStatusFlg($em_flg); ?>">

                          <label>Phone Verification Result :</label>
                          <input type="text" class="form-control" value="<?php echo PassOrFail($pp_flg); ?>" disabled="" style="<?php echo ColorByStatusFlg($pp_flg); ?>">

                          <label>Verification Remarks :</label>
                          <textarea id="con_rmks" name="con_rmks" class="form-control" disabled=""><?php echo $CONTACT_DATA_VERIF_RMKS ?></textarea>

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

                    ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <div class="x_panel">
                        <div class="x_title">
                          <strong>Files Attchmnt Verfcn</strong> 
                          <?php
                          if ( ($wkid_flg=="NN")||($nin_flg=="NN")||($maf_flg=="NN")||($php_flg=="NN") ) {
                            ?>
                            <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#undofil">Amend</button>
                            <?php
                          }
                          ?>
                          <div id="undofil" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content" style="color: #333;">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Amend Wrong File Attachments</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="undofileee" method="post" enctype="multipart/form-data">
                                      <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                      <input type="hidden" id="BIO_DATA_VERIF_FLG" name="BIO_DATA_VERIF_FLG" value="<?php echo $BIO_DATA_VERIF_FLG; ?>">
                                      <input type="hidden" id="CONTACT_DATA_VERIF_FLG" name="CONTACT_DATA_VERIF_FLG" value="<?php echo $CONTACT_DATA_VERIF_FLG; ?>">
                                      <input type="hidden" id="FILE_DATA_VERIF_FLG" name="FILE_DATA_VERIF_FLG" value="<?php echo $FILE_DATA_VERIF_FLG; ?>">

                                      <?php
                                      if ($wkid_flg=="NN") {
                                        ?>
                                        <label for="personal_id_doc_no">Work ID/Staff ID/Personal ID * :</label>
                                        <input type="text" id="personal_id_doc_no" name="personal_id_doc_no" class="form-control" value="<?php echo $WORK_ID; ?>" style="<?php echo ColorByStatusFlg($wkid_flg); ?>" required="">
                                        <label for="personal_id_doc_attcnt">Work ID attachment :</label>
                                        <input type="file" id="personal_id_doc_attcnt" name="personal_id_doc_attcnt" class="form-control" required="">
                                        <?php
                                      }

                                      if ($nin_flg=="NN") {
                                        ?>
                                        <label for="nat_id_nin">National ID (NIN) *:</label>
                                        <input type="text" id="nat_id_nin" name="nat_id_nin" class="form-control" value="<?php echo $NATIONAL_ID; ?>" style="<?php echo ColorByStatusFlg($nin_flg); ?>" required="">
                                        <label for="nat_id_attchmnt">National ID attachment :</label>
                                        <input type="file" id="nat_id_attchmnt" name="nat_id_attchmnt" class="form-control" required=""><br>
                                        <?php
                                      }

                                      if ($maf_flg=="NN") {
                                        ?>
                                        <label for="maf_attchmnt">Membership Application Form *:<br> 
                                          <small>(The hard copy of the filled in Membership Application Form. Scan and upload)</small></label>
                                        <input type="file" id="maf_attchmnt" name="maf_attchmnt" class="form-control" required=""><br>
                                        <?php
                                      }

                                      if ($php_flg=="NN") {
                                        ?>
                                        <label for="passport_photo">Passport Photo *:<br> 
                                          <small>(soft copy taken from studio on a white background)</small></label>
                                        <input type="file" id="passport_photo" name="passport_photo" class="form-control" required="">
                                        <?php
                                      }

                                      ?>

                                      <br>
                                      <br>
                                      <button type="submit" class="btn btn-primary btn-sm" name="btn_resubmit_file">Upload File Amendments</button>

                                      
                                    </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                          In this section, we checked if your documents are authentic.<br><br>
                          
                          <label>Work ID Authenticity</label>
                          <input type="text" class="form-control" value="<?php echo PassOrFail($wkid_flg); ?>" disabled="" style="<?php echo ColorByStatusFlg($wkid_flg); ?>">


                          <label>National ID Authenticity</label>
                          <input type="text" class="form-control" value="<?php echo PassOrFail($nin_flg); ?>" disabled="" style="<?php echo ColorByStatusFlg($nin_flg); ?>">

                          <label>Membership Application Form Authenticity</label>
                          <input type="text" class="form-control" value="<?php echo PassOrFail($maf_flg); ?>" disabled="" style="<?php echo ColorByStatusFlg($maf_flg); ?>">

                          <label>Passport photo Authenticity</label>
                          <input type="text" class="form-control" value="<?php echo PassOrFail($php_flg); ?>" disabled="" style="<?php echo ColorByStatusFlg($php_flg); ?>">

                          Additional Remarks *
                          <textarea id="attmnt_rmks" name="attmnt_rmks" class="form-control" disabled=""><?php echo $FILE_DATA_VERIF_RMKS ?></textarea>

                        </div>
                      </div>
                    </div>
                    <?php
                  }
                  ?>
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
             
               

              </div>
          </div>


        </div>
        <div class="col-md-1 col-sm-0 col-xs-0">
        </div>

        
      </div>
      <!-- /article feed -->


      <!-- Bottom Link -->
      <div class="row" style="color: #FFF; background: #2f4357; padding-left: 25px; padding-right: 25px;">
        <span style="font-family: calibri; font-size: 35px;"><?php echo $APP_NAME; ?></span>
        <hr style="margin-top: 3px; margin-bottom: 10px;" />
        <div>
          <div class="pull-left" style="font-family: calibri; font-size: 14px;"><?php echo $COPY_RIGHT_STMT; ?></div>
          <br />
          <br />
        </div>
      </div>
      <!-- /Bottom Link -->



      <!-- Copy right Statement -->
      <div>
        
      </div>
      <!-- /Copy right Statement -->



    </div>



  </body>

  <?php
  LoadDefaultJavaScriptConfigurations();
  ?>
</html>


