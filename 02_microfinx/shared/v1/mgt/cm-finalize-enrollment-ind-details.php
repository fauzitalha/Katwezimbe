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
$RECORD_ID = $cstmr_actvn['RECORD_ID'];
$ACTIVATION_REF = $cstmr_actvn['ACTIVATION_REF'];
$MMBSHP_TYPE = $cstmr_actvn['MMBSHP_TYPE'];
$CHANNEL_ID = $cstmr_actvn['CHANNEL_ID'];
$FIRST_NAME = $cstmr_actvn['FIRST_NAME'];
$MIDDLE_NAME = $cstmr_actvn['MIDDLE_NAME'];
$LAST_NAME = $cstmr_actvn['LAST_NAME'];
$GENDER = $cstmr_actvn['GENDER'];
$DOB = $cstmr_actvn['DOB'];
$BIO_DATA_VERIF_FLG = $cstmr_actvn['BIO_DATA_VERIF_FLG'];
$BIO_DATA_VERIF_RMKS = $cstmr_actvn['BIO_DATA_VERIF_RMKS'];
$BIO_DATA_VERIF_RMKS_BY = $cstmr_actvn['BIO_DATA_VERIF_RMKS_BY'];
$BIO_DATA_VERIF_RMKS_DATE = $cstmr_actvn['BIO_DATA_VERIF_RMKS_DATE'];
$EMAIL = $cstmr_actvn['EMAIL'];
$MOBILE_NO = $cstmr_actvn['MOBILE_NO'];
$PHYSICAL_ADDRESS = $cstmr_actvn['PHYSICAL_ADDRESS'];
$CONTACT_DATA_VERIF_FLG = $cstmr_actvn['CONTACT_DATA_VERIF_FLG'];
$CONTACT_DATA_VERIF_RMKS = $cstmr_actvn['CONTACT_DATA_VERIF_RMKS'];
$CONTACT_DATA_VERIF_BY = $cstmr_actvn['CONTACT_DATA_VERIF_BY'];
$CONTACT_DATA_VERIF_DATE = $cstmr_actvn['CONTACT_DATA_VERIF_DATE'];
$WORK_ID = $cstmr_actvn['WORK_ID'];
$WORK_ID_ATTCHMNT_FLG = $cstmr_actvn['WORK_ID_ATTCHMNT_FLG'];
$WORK_ID_FILE_NAME = $cstmr_actvn['WORK_ID_FILE_NAME'];
$NATIONAL_ID = $cstmr_actvn['NATIONAL_ID'];
$NATIONAL_ID_ATTCHMNT_FLG = $cstmr_actvn['NATIONAL_ID_ATTCHMNT_FLG'];
$NATIONAL_ID_FILE_NAME = $cstmr_actvn['NATIONAL_ID_FILE_NAME'];
$MAF_UPLOAD_FLG = $cstmr_actvn['MAF_UPLOAD_FLG'];
$MAF_UPLOAD_FILE_NAME = $cstmr_actvn['MAF_UPLOAD_FILE_NAME'];
$PASSPORT_PHOTO_UPLOAD_FLG = $cstmr_actvn['PASSPORT_PHOTO_UPLOAD_FLG'];
$PASSPORT_PHOTO_FILE_NAME = $cstmr_actvn['PASSPORT_PHOTO_FILE_NAME'];
$FILE_DATA_VERIF_FLG = $cstmr_actvn['FILE_DATA_VERIF_FLG'];
$FILE_DATA_VERIF_RMKS = $cstmr_actvn['FILE_DATA_VERIF_RMKS'];
$FILE_DATA_VERIF_BY = $cstmr_actvn['FILE_DATA_VERIF_BY'];
$FILE_DATA_VERIF_DATE = $cstmr_actvn['FILE_DATA_VERIF_DATE'];
$REQST_RECORD_DATE = $cstmr_actvn['REQST_RECORD_DATE'];
$VERIF_RMKS = $cstmr_actvn['VERIF_RMKS'];
$VERIF_DATE = $cstmr_actvn['VERIF_DATE'];
$VERIF_DATE = $cstmr_actvn['VERIF_DATE'];
$VERIF_BY = $cstmr_actvn['VERIF_BY'];
$APPRVL_RMKS = $cstmr_actvn['APPRVL_RMKS'];
$APPRVL_DATE = $cstmr_actvn['APPRVL_DATE'];
$APPRVD_BY = $cstmr_actvn['APPRVD_BY'];
$CST_CORE_CRTN_FLG = $cstmr_actvn['CST_CORE_CRTN_FLG'];
$CST_CORE_ID = $cstmr_actvn['CST_CORE_ID'];
$CORE_IMG_UPLD_FLG = $cstmr_actvn['CORE_IMG_UPLD_FLG'];
$CORE_IMG_UPLD_USER_ID = $cstmr_actvn['CORE_IMG_UPLD_USER_ID'];
$CORE_IMG_UPLD_DATE = $cstmr_actvn['CORE_IMG_UPLD_DATE'];
$WRKID_UPLD_FLG = $cstmr_actvn['WRKID_UPLD_FLG'];
$WRKID_UPLD_USER_ID = $cstmr_actvn['WRKID_UPLD_USER_ID'];
$WRKID_UPLD_DATE = $cstmr_actvn['WRKID_UPLD_DATE'];
$NIN_UPLD_FLG = $cstmr_actvn['NIN_UPLD_FLG'];
$NIN_UPLD_USER_ID = $cstmr_actvn['NIN_UPLD_USER_ID'];
$NIN_UPLD_DATE = $cstmr_actvn['NIN_UPLD_DATE'];
$MAF_UPLD_FLG = $cstmr_actvn['MAF_UPLD_FLG'];
$MAF_UPLD_USER_ID = $cstmr_actvn['MAF_UPLD_USER_ID'];
$MAF_UPLD_DATE = $cstmr_actvn['MAF_UPLD_DATE'];
$SVNGS_ACCT_CRTN_FLG = $cstmr_actvn['SVNGS_ACCT_CRTN_FLG'];
$SVNGS_ACCT_CRTN_USER_ID = $cstmr_actvn['SVNGS_ACCT_CRTN_USER_ID'];
$SVNGS_ACCT_CRTN_DATE = $cstmr_actvn['SVNGS_ACCT_CRTN_DATE'];
$OAA_FLG = $cstmr_actvn['OAA_FLG'];
$OAA_USER_ID = $cstmr_actvn['OAA_USER_ID'];
$OAA_DATE = $cstmr_actvn['OAA_DATE'];
$ACTIVATION_STATUS = $cstmr_actvn['ACTIVATION_STATUS'];


# ... File Paths and Links
$BASE = GetSystemParameter("NEW_CUST_ACTIVATION_BASEPATH") . "/" . $_SESSION['ORG_CODE'];
$WORK_ID_LNK = $BASE . "/" . $ACTIVATION_REF . "/" . $WORK_ID_FILE_NAME;
$NATIONAL_ID_LNK = $BASE . "/" . $ACTIVATION_REF . "/" . $NATIONAL_ID_FILE_NAME;
$MAF_LNK = $BASE . "/" . $ACTIVATION_REF . "/" . $MAF_UPLOAD_FILE_NAME;
$PP_LNK = $BASE . "/" . $ACTIVATION_REF . "/" . $PASSPORT_PHOTO_FILE_NAME;



# ... Create Cust Core Account
if (isset($_POST['btn_create_cust_core'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $client_type = trim($_POST['client_type']);
  $client_classfcn = trim($_POST['client_classfcn']);


  $cstmr_actvn = array();
  $cstmr_actvn = FetchActivationRequestById($ACTIVATION_REF);
  $OFFICE_ID = $CORE_OFFICE_DEF["HQ"];
  $FIRST_NAME = $cstmr_actvn['FIRST_NAME'];
  $MIDDLE_NAME = $cstmr_actvn['MIDDLE_NAME'];
  $LAST_NAME = $cstmr_actvn['LAST_NAME'];
  $WORK_ID = $cstmr_actvn['WORK_ID'];
  $MOBILE_NO = $cstmr_actvn['MOBILE_NO'];
  $GENDER = $cstmr_actvn['GENDER'];
  //$GENDER_ID = $CORE_GENDER_DEF[$GENDER];
  $GENDER_ID = $GENDER;
  //$CLIENT_TYPE_ID = $CORE_CLIENT_TYPE_DEF["KSK"];
  $CLIENT_TYPE_ID = $client_type;
  //$CLIENT_CLSFCN_ID = $CORE_CLIENT_CLSSFCN_DEF["RETAIL"];
  $CLIENT_CLSFCN_ID = $client_classfcn;
  $ACTVN_DATE = date("d F Y", time());
  $SUBMN_DATE = date("d F Y", time());

  $CORE_RQST_MSG = BuildCreateClientRequestMessage(
    $OFFICE_ID,
    $FIRST_NAME,
    $MIDDLE_NAME,
    $LAST_NAME,
    $WORK_ID,
    $MOBILE_NO,
    $GENDER_ID,
    $CLIENT_TYPE_ID,
    $CLIENT_CLSFCN_ID,
    $ACTVN_DATE,
    $SUBMN_DATE
  );

  $response_msg = CreateNewClient($CORE_RQST_MSG, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];

  # ... 01: Track Connection to Core
  if ($CONN_FLG == "NOT_CONNECTED") {
    $alert_type = "ERROR";
    $alert_msg = "NO CONNECTION TO CORE.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else {

    if (!isset($CORE_RESP["clientId"])) {
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["clientId"])) {
      /*Array
      (
          [officeId] => 1
          [clientId] => 7
          [resourceId] => 7
      ) */

      $clientId = $CORE_RESP["clientId"];
      $officeId = $CORE_RESP["officeId"];
      $resourceId = $CORE_RESP["resourceId"];

      # ... Push Other Details to Core
      $EMAIL = $cstmr_actvn['EMAIL'];
      $WORK_ID = $cstmr_actvn['WORK_ID'];
      $NATIONAL_ID = $cstmr_actvn['NATIONAL_ID'];
      $PHYSICAL_ADDRESS = $cstmr_actvn['PHYSICAL_ADDRESS'];
      $DOB = $cstmr_actvn['DOB'];

      $OtherDetailsList = array(
        "Email" => $EMAIL,
        "WorkID" => $WORK_ID,
        "NationalId" => $NATIONAL_ID,
        "Physical_Address" => $PHYSICAL_ADDRESS,
        "Date_of_Birth" => $DOB
      );

      $DATATABLE_NAME = "OtherDetails";
      $DATATABLE_ENTITY_ID = $clientId;
      $DATATABLE_RQST_MSG = BuildDataTableRqstMsg($OtherDetailsList);
      $response_msg = PostDataToDataTable($DATATABLE_NAME, $DATATABLE_ENTITY_ID, $DATATABLE_RQST_MSG, $MIFOS_CONN_DETAILS);
      $CONN_FLG = $response_msg["CONN_FLG"];
      $CORE_RESP = $response_msg["CORE_RESP"];
      if ($CONN_FLG == "NOT_CONNECTED") {
        $alert_type = "ERROR";
        $alert_msg = "NO CONNECTION TO CORE.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      } else {

        if (!isset($CORE_RESP["resourceId"])) {
          $alert_type = "ERROR";
          $alert_msg = $CORE_RESP["defaultUserMessage"];
          $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
        } else if (isset($CORE_RESP["resourceId"])) {

          $f_officeId = $CORE_RESP["officeId"];
          $f_clientId = $CORE_RESP["clientId"];
          $f_resourceId = $CORE_RESP["resourceId"];

          $CST_CORE_CRTN_FLG = "YY";
          $CST_CORE_ID = $clientId;
          $CST_CORE_CRTN_USER_ID = $_SESSION['UPR_USER_ID'];
          $CST_CORE_CRTN_DATE = GetCurrentDateTime();

          # ... SQL Query
          $q = "UPDATE cstmrs_actvn_rqsts SET CST_CORE_CRTN_FLG='$CST_CORE_CRTN_FLG', CST_CORE_ID='$CST_CORE_ID', CST_CORE_CRTN_USER_ID='$CST_CORE_CRTN_USER_ID', CST_CORE_CRTN_DATE='$CST_CORE_CRTN_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
          $update_response = ExecuteEntityUpdate($q);
          if ($update_response == "EXECUTED") {

            # ... Log System Audit Log
            $AUDIT_DATE = GetCurrentDateTime();
            $ENTITY_TYPE = "CUSTOMER";
            $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
            $EVENT = "FINAL_SETUP";
            $EVENT_OPERATION = "CREATE_CUSTOMER_DETAILS_IN_CORE";
            $EVENT_RELATION = "cstmrs_actvn_rqsts";
            $EVENT_RELATION_NO = $ACTIVATION_REF;
            $OTHER_DETAILS = "Client_Creation {officeId: " . $officeId . ", clientId: " . $clientId . ", client_resourceId: " . $resourceId . "}|OtherDetails {f_officeId: " . $f_officeId . ", f_clientId: " . $f_clientId . ", f_resourceId: " . $f_resourceId . "}";
            $INVOKER_ID = $_SESSION['UPR_USER_ID'];
            LogSystemEvent(
              $AUDIT_DATE,
              $ENTITY_TYPE,
              $ENTITY_ID_AFFECTED,
              $EVENT,
              $EVENT_OPERATION,
              $EVENT_RELATION,
              $EVENT_RELATION_NO,
              $OTHER_DETAILS,
              $INVOKER_ID
            );


            $alert_type = "SUCCESS";
            $alert_msg = "SUCCESS: customer details have been created successfully. Re-directing in 5 seconds.";
            $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

            header("Refresh:5;");
          }
        }
      }
    }
  }
}

# ... Upload Customer Photo
if (isset($_POST['btn_upld_pp'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $CST_CORE_ID = trim($_POST['CST_CORE_ID']);

  $cstmr_actvn = array();
  $cstmr_actvn = FetchActivationRequestById($ACTIVATION_REF);

  $PASSPORT_PHOTO_FILE_NAME = $cstmr_actvn['PASSPORT_PHOTO_FILE_NAME'];
  $ACTVN_RQSTS_BASE_FILEPATH = GetSystemParameter("ACTVN_RQSTS_BASE_FILEPATH") . "/" . $_SESSION['ORG_CODE'];
  $IMG_PATH = $ACTVN_RQSTS_BASE_FILEPATH . "/" . $ACTIVATION_REF . "/" . $PASSPORT_PHOTO_FILE_NAME;
  $IMG_PATH_ENCODED = BuildImageUploadRqstMsg($IMG_PATH);

  $response_msg = UploadCustomerImage($CST_CORE_ID, $IMG_PATH_ENCODED, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];

  # ... 01: Track Connection to Core
  if ($CONN_FLG == "NOT_CONNECTED") {
    $alert_type = "ERROR";
    $alert_msg = "NO CONNECTION TO CORE.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else {

    if (!isset($CORE_RESP["resourceIdentifier"])) {
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["resourceIdentifier"])) {
      $resourceId = $CORE_RESP["resourceId"];
      $resourceIdentifier = $CORE_RESP["resourceIdentifier"];

      $CORE_IMG_UPLD_FLG = "YY";
      $CORE_IMG_UPLD_USER_ID = $_SESSION['UPR_USER_ID'];
      $CORE_IMG_UPLD_DATE = GetCurrentDateTime();

      # ... SQL Query
      $q = "UPDATE cstmrs_actvn_rqsts SET CORE_IMG_UPLD_FLG='$CORE_IMG_UPLD_FLG', CORE_IMG_UPLD_USER_ID='$CORE_IMG_UPLD_USER_ID', CORE_IMG_UPLD_DATE='$CORE_IMG_UPLD_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";

      $update_response = ExecuteEntityUpdate($q);
      if ($update_response == "EXECUTED") {

        # ... Log System Audit Log
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "CUSTOMER";
        $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
        $EVENT = "FINAL_SETUP";
        $EVENT_OPERATION = "UPLOAD_PASSPHOTO_TO_CORE";
        $EVENT_RELATION = "cstmrs_actvn_rqsts";
        $EVENT_RELATION_NO = $ACTIVATION_REF;
        $OTHER_DETAILS = "Core_Response {resourceId: " . $resourceId . ", resourceIdentifier: " . $resourceIdentifier . "}";
        $INVOKER_ID = $_SESSION['UPR_USER_ID'];
        LogSystemEvent(
          $AUDIT_DATE,
          $ENTITY_TYPE,
          $ENTITY_ID_AFFECTED,
          $EVENT,
          $EVENT_OPERATION,
          $EVENT_RELATION,
          $EVENT_RELATION_NO,
          $OTHER_DETAILS,
          $INVOKER_ID
        );


        $alert_type = "SUCCESS";
        $alert_msg = "SUCCESS: Passport photo uploaded successfully to core. Refreshing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

        header("Refresh:5;");
      }
    }
  }
}

# ... Upload Work Document
if (isset($_POST['btn_upld_wrk'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $CST_CORE_ID = trim($_POST['CST_CORE_ID']);

  $cstmr_actvn = array();
  $cstmr_actvn = FetchActivationRequestById($ACTIVATION_REF);

  $WORK_ID_FILE_NAME = $cstmr_actvn['WORK_ID_FILE_NAME'];
  $ACTVN_RQSTS_BASE_FILEPATH = GetSystemParameter("ACTVN_RQSTS_BASE_FILEPATH") . "/" . $_SESSION['ORG_CODE'];
  $FILE_PATH_B = $ACTVN_RQSTS_BASE_FILEPATH . "/" . $ACTIVATION_REF . "/" . $WORK_ID_FILE_NAME;

  $file_path = $FILE_PATH_B;
  $file_type = mime_content_type($file_path);
  $file_name = $WORK_ID_FILE_NAME;
  $description = "Work ID/Staff ID/Personal ID";
  $DOC_RQST_MSG = BuildDocumentRqstMsg($file_path, $file_type, $file_name, $description);

  $response_msg = UploadClientDocumentToCore($CST_CORE_ID, $DOC_RQST_MSG, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];

  # ... 01: Track Connection to Core
  if ($CONN_FLG == "NOT_CONNECTED") {
    $alert_type = "ERROR";
    $alert_msg = "NO CONNECTION TO CORE.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else {

    if (!isset($CORE_RESP["resourceIdentifier"])) {
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["resourceIdentifier"])) {
      $resourceId = $CORE_RESP["resourceId"];
      $resourceIdentifier = $CORE_RESP["resourceIdentifier"];

      $WRKID_UPLD_FLG = "YY";
      $WRKID_UPLD_USER_ID = $_SESSION['UPR_USER_ID'];
      $WRKID_UPLD_DATE = GetCurrentDateTime();

      # ... SQL Query
      $q = "UPDATE cstmrs_actvn_rqsts SET WRKID_UPLD_FLG='$WRKID_UPLD_FLG', WRKID_UPLD_USER_ID='$WRKID_UPLD_USER_ID', WRKID_UPLD_DATE='$WRKID_UPLD_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";

      $update_response = ExecuteEntityUpdate($q);
      if ($update_response == "EXECUTED") {
        # ... Log System Audit Log
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "CUSTOMER";
        $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
        $EVENT = "FINAL_SETUP";
        $EVENT_OPERATION = "UPLOAD_WORKID_TO_CORE";
        $EVENT_RELATION = "cstmrs_actvn_rqsts";
        $EVENT_RELATION_NO = $ACTIVATION_REF;
        $OTHER_DETAILS = "Core_Response {resourceId: " . $resourceId . ", resourceIdentifier: " . $resourceIdentifier . "}";
        $INVOKER_ID = $_SESSION['UPR_USER_ID'];
        LogSystemEvent(
          $AUDIT_DATE,
          $ENTITY_TYPE,
          $ENTITY_ID_AFFECTED,
          $EVENT,
          $EVENT_OPERATION,
          $EVENT_RELATION,
          $EVENT_RELATION_NO,
          $OTHER_DETAILS,
          $INVOKER_ID
        );


        $alert_type = "SUCCESS";
        $alert_msg = "SUCCESS: Work Id uploaded successfully to core. Refreshing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

        header("Refresh:5;");
      }
    }
  }
}

# ... Upload National ID Document
if (isset($_POST['btn_upld_nin'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $CST_CORE_ID = trim($_POST['CST_CORE_ID']);

  $cstmr_actvn = array();
  $cstmr_actvn = FetchActivationRequestById($ACTIVATION_REF);

  $NATIONAL_ID_FILE_NAME = $cstmr_actvn['NATIONAL_ID_FILE_NAME'];
  $ACTVN_RQSTS_BASE_FILEPATH = GetSystemParameter("ACTVN_RQSTS_BASE_FILEPATH") . "/" . $_SESSION['ORG_CODE'];
  $FILE_PATH_B = $ACTVN_RQSTS_BASE_FILEPATH . "/" . $ACTIVATION_REF . "/" . $NATIONAL_ID_FILE_NAME;

  $file_path = $FILE_PATH_B;
  $file_type = mime_content_type($file_path);
  $file_name = $NATIONAL_ID_FILE_NAME;
  $description = "National ID";
  $DOC_RQST_MSG = BuildDocumentRqstMsg($file_path, $file_type, $file_name, $description);

  $response_msg = UploadClientDocumentToCore($CST_CORE_ID, $DOC_RQST_MSG, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];

  # ... 01: Track Connection to Core
  if ($CONN_FLG == "NOT_CONNECTED") {
    $alert_type = "ERROR";
    $alert_msg = "NO CONNECTION TO CORE.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else {

    if (!isset($CORE_RESP["resourceIdentifier"])) {
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["resourceIdentifier"])) {
      $resourceId = $CORE_RESP["resourceId"];
      $resourceIdentifier = $CORE_RESP["resourceIdentifier"];

      $NIN_UPLD_FLG = "YY";
      $NIN_UPLD_USER_ID = $_SESSION['UPR_USER_ID'];
      $NIN_UPLD_DATE = GetCurrentDateTime();

      # ... SQL Query
      $q = "UPDATE cstmrs_actvn_rqsts SET NIN_UPLD_FLG='$NIN_UPLD_FLG', NIN_UPLD_USER_ID='$NIN_UPLD_USER_ID', NIN_UPLD_DATE='$NIN_UPLD_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";

      $update_response = ExecuteEntityUpdate($q);
      if ($update_response == "EXECUTED") {
        # ... Log System Audit Log
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "CUSTOMER";
        $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
        $EVENT = "FINAL_SETUP";
        $EVENT_OPERATION = "UPLOAD_NATIONALID_TO_CORE";
        $EVENT_RELATION = "cstmrs_actvn_rqsts";
        $EVENT_RELATION_NO = $ACTIVATION_REF;
        $OTHER_DETAILS = "Core_Response {resourceId: " . $resourceId . ", resourceIdentifier: " . $resourceIdentifier . "}";
        $INVOKER_ID = $_SESSION['UPR_USER_ID'];
        LogSystemEvent(
          $AUDIT_DATE,
          $ENTITY_TYPE,
          $ENTITY_ID_AFFECTED,
          $EVENT,
          $EVENT_OPERATION,
          $EVENT_RELATION,
          $EVENT_RELATION_NO,
          $OTHER_DETAILS,
          $INVOKER_ID
        );


        $alert_type = "SUCCESS";
        $alert_msg = "SUCCESS: National ID uploaded successfully to core. Refreshing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

        header("Refresh:5;");
      }
    }
  }
}

# ... Upload Membership Application Document
if (isset($_POST['btn_upld_maf'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $CST_CORE_ID = trim($_POST['CST_CORE_ID']);

  $cstmr_actvn = array();
  $cstmr_actvn = FetchActivationRequestById($ACTIVATION_REF);

  $MAF_UPLOAD_FILE_NAME = $cstmr_actvn['MAF_UPLOAD_FILE_NAME'];
  $ACTVN_RQSTS_BASE_FILEPATH = GetSystemParameter("ACTVN_RQSTS_BASE_FILEPATH") . "/" . $_SESSION['ORG_CODE'];
  $FILE_PATH_B = $ACTVN_RQSTS_BASE_FILEPATH . "/" . $ACTIVATION_REF . "/" . $MAF_UPLOAD_FILE_NAME;

  $file_path = $FILE_PATH_B;
  $file_type = mime_content_type($file_path);
  $file_name = $MAF_UPLOAD_FILE_NAME;
  $description = "Membership Application Form";
  $DOC_RQST_MSG = BuildDocumentRqstMsg($file_path, $file_type, $file_name, $description);

  $response_msg = UploadClientDocumentToCore($CST_CORE_ID, $DOC_RQST_MSG, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];

  # ... 01: Track Connection to Core
  if ($CONN_FLG == "NOT_CONNECTED") {
    $alert_type = "ERROR";
    $alert_msg = "NO CONNECTION TO CORE.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else {

    if (!isset($CORE_RESP["resourceIdentifier"])) {
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["resourceIdentifier"])) {
      $resourceId = $CORE_RESP["resourceId"];
      $resourceIdentifier = $CORE_RESP["resourceIdentifier"];

      $MAF_UPLD_FLG = "YY";
      $MAF_UPLD_USER_ID = $_SESSION['UPR_USER_ID'];
      $MAF_UPLD_DATE = GetCurrentDateTime();

      # ... SQL Query
      $q = "UPDATE cstmrs_actvn_rqsts SET MAF_UPLD_FLG='$MAF_UPLD_FLG', MAF_UPLD_USER_ID='$MAF_UPLD_USER_ID', MAF_UPLD_DATE='$MAF_UPLD_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";

      $update_response = ExecuteEntityUpdate($q);
      if ($update_response == "EXECUTED") {
        # ... Log System Audit Log
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "CUSTOMER";
        $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
        $EVENT = "FINAL_SETUP";
        $EVENT_OPERATION = "UPLOAD_MAF_TO_CORE";
        $EVENT_RELATION = "cstmrs_actvn_rqsts";
        $EVENT_RELATION_NO = $ACTIVATION_REF;
        $OTHER_DETAILS = "Core_Response {resourceId: " . $resourceId . ", resourceIdentifier: " . $resourceIdentifier . "}";
        $INVOKER_ID = $_SESSION['UPR_USER_ID'];
        LogSystemEvent(
          $AUDIT_DATE,
          $ENTITY_TYPE,
          $ENTITY_ID_AFFECTED,
          $EVENT,
          $EVENT_OPERATION,
          $EVENT_RELATION,
          $EVENT_RELATION_NO,
          $OTHER_DETAILS,
          $INVOKER_ID
        );


        $alert_type = "SUCCESS";
        $alert_msg = "SUCCESS: Membership Application Form uploaded successfully to core. Refreshing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

        header("Refresh:5;");
      }
    }
  }
}

# ... Create Savings Account
if (isset($_POST['btn_crt_svngs_acct'])) {

  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $CST_CORE_ID = trim($_POST['CST_CORE_ID']);
  $SVNGS_PDT_ID = trim($_POST['SVNGS_PDT_ID']);
  $submission_date = date("d F Y", time());

  # ... Shares Details
  $SHARES_PDT_ID = "";
  $CREATE_SHRS_FLG = trim($_POST['CREATE_SHRS_FLG']);
  if ($CREATE_SHRS_FLG == "YY") {
    $SHARES_PDT_ID = trim($_POST['SHARES_PDT_ID']);
  }

  $RQST_MSG = BuildNewSavingsApplnRqstMsg($CST_CORE_ID, $SVNGS_PDT_ID, $submission_date);
  $response_msg = CreateSavingsApplication($RQST_MSG, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];

  # ... 01: Track Connection to Core
  if ($CONN_FLG == "NOT_CONNECTED") {
    $alert_type = "ERROR";
    $alert_msg = "NO CONNECTION TO CORE.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else {

    if (!isset($CORE_RESP["savingsId"])) {
      $alert_type = "ERROR";
      $alert_msg = $CORE_RESP["defaultUserMessage"];
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if (isset($CORE_RESP["savingsId"])) {

      $officeId = $CORE_RESP["officeId"];
      $clientId = $CORE_RESP["clientId"];
      $savingsId = $CORE_RESP["savingsId"];
      $resourceId = $CORE_RESP["resourceId"];

      # ... Approving Created Account
      $apprvl_date = date("d F Y", time());
      $RQST_MSG = BuildApproveSavingsApplnRqstMsg($apprvl_date);
      ApproveSavingsApplication($savingsId, $RQST_MSG, $MIFOS_CONN_DETAILS);

      # ... Activate Created Account
      $actvn_date = date("d F Y", time());
      $RQST_MSG = BuildActivateSavingsApplnRqstMsg($actvn_date);
      ActivateSavingsApplication($savingsId, $RQST_MSG, $MIFOS_CONN_DETAILS);


      $SVNGS_ACCT_CRTN_FLG = "YY";
      $SVNGS_ACCT_CRTN_USER_ID = $_SESSION['UPR_USER_ID'];
      $SVNGS_ACCT_CRTN_DATE = GetCurrentDateTime();

      # ... SQL Query
      $q = "UPDATE cstmrs_actvn_rqsts SET SVNGS_ACCT_CRTN_FLG='$SVNGS_ACCT_CRTN_FLG', SVNGS_ACCT_CRTN_USER_ID='$SVNGS_ACCT_CRTN_USER_ID', SVNGS_ACCT_CRTN_DATE='$SVNGS_ACCT_CRTN_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";

      $update_response = ExecuteEntityUpdate($q);
      if ($update_response == "EXECUTED") {

        # ... Log System Audit Log
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "CUSTOMER";
        $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
        $EVENT = "FINAL_SETUP";
        $EVENT_OPERATION = "CREATE_SAVINGS_ACCT";
        $EVENT_RELATION = "cstmrs_actvn_rqsts";
        $EVENT_RELATION_NO = $ACTIVATION_REF;
        $OTHER_DETAILS = "Core_Response {resourceId: " . $resourceId . ", savingsId: " . $savingsId . "}";
        $INVOKER_ID = $_SESSION['UPR_USER_ID'];
        LogSystemEvent(
          $AUDIT_DATE,
          $ENTITY_TYPE,
          $ENTITY_ID_AFFECTED,
          $EVENT,
          $EVENT_OPERATION,
          $EVENT_RELATION,
          $EVENT_RELATION_NO,
          $OTHER_DETAILS,
          $INVOKER_ID
        );


        # ... Create Shares Application
        if ($CREATE_SHRS_FLG == "YY") {

          # ... 0001:  SUBMIT THE APPLICATION
          $shr_clientId = $CST_CORE_ID;
          $shr_productId = $SHARES_PDT_ID;
          $shr_requestedShares = GetSystemParameter("START_SHARES_COUNT");
          $shr_submittedDate = date("d F Y", time());
          $shr_applicationDate = date("d F Y", time());
          $shr_savingsAccountId = $savingsId;

          $RQST_MSG = BuildCreateSharesApplicationMessage($shr_clientId, $shr_productId, $shr_requestedShares, $shr_submittedDate, $shr_applicationDate, $shr_savingsAccountId);
          $response_msg = CreateSharesApplication($RQST_MSG, $MIFOS_CONN_DETAILS);
          $CONN_FLG = $response_msg["CONN_FLG"];
          $CORE_RESP = $response_msg["CORE_RESP"];

          $CORE_SHR_ID = $CORE_RESP["resourceId"];

          # ... 0002: APPROVE SHARES APPLICATION
          $shr_approvedDate = date("d F Y", time());
          $APP_RQST_MSG = BuildApproveSharesApplicationMessage($shr_approvedDate);
          ApproveSharesApplication($CORE_SHR_ID, $APP_RQST_MSG, $MIFOS_CONN_DETAILS);

          # ... 0003: APPROVE SHARES APPLICATION
          $shr_activatedDate = date("d F Y", time());
          $ACT_RQST_MSG = BuildActivateSharesApplicationMessage($shr_activatedDate);
          ActivateSharesApplication($CORE_SHR_ID, $ACT_RQST_MSG, $MIFOS_CONN_DETAILS);

          # ... Log System Audit Log
          $AUDIT_DATE = GetCurrentDateTime();
          $ENTITY_TYPE = "CUSTOMER";
          $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
          $EVENT = "FINAL_SETUP";
          $EVENT_OPERATION = "CREATE_SHARES_ACCT";
          $EVENT_RELATION = "cstmrs_actvn_rqsts";
          $EVENT_RELATION_NO = $ACTIVATION_REF;
          $OTHER_DETAILS = "Core_Response {Shares_Acct_Id: " . $savingsId . "}";
          $INVOKER_ID = $_SESSION['UPR_USER_ID'];
          LogSystemEvent(
            $AUDIT_DATE,
            $ENTITY_TYPE,
            $ENTITY_ID_AFFECTED,
            $EVENT,
            $EVENT_OPERATION,
            $EVENT_RELATION,
            $EVENT_RELATION_NO,
            $OTHER_DETAILS,
            $INVOKER_ID
          );
        }


        $alert_type = "SUCCESS";
        $alert_msg = "SUCCESS: Savings Account successfully created. Refreshing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

        header("Refresh:5;");
      }
    }
  }
}

# ... Activate Online Appln
if (isset($_POST['btn_actvte_acct'])) {

  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $FIRST_NAME = $cstmr_actvn['FIRST_NAME'];
  $EMAIL = $cstmr_actvn['EMAIL'];
  $MOBILE_NO = $cstmr_actvn['MOBILE_NO'];
  $CST_CORE_ID = $cstmr_actvn['CST_CORE_ID'];

  # ... 01: Prepare Insert Message
  $CUST_CORE_ID = $CST_CORE_ID;
  $APPLN_REF = $ACTIVATION_REF;
  $ACTVN_TOKEN_RAW = GenerateRandomAccessPin(14);
  $ACTVN_TOKEN = AES256::encrypt($ACTVN_TOKEN_RAW);
  $CUST_EMAIL = AES256::encrypt($EMAIL);
  $CUST_PHONE = AES256::encrypt($MOBILE_NO);
  $WEB_CHANNEL_ACCESS_FLG = "YY";
  $CUST_STATUS = "ACTIVE";

  # ... 02: Prepare Mobile Account
  $APPLN_REF_MOB = "MB" . substr($APPLN_REF, 2, (strlen($APPLN_REF) - 2));
  $ACTVN_TOKEN_MOB_RAW = GenerateRandomAccessPin(5);
  $ACTVN_TOKEN_MOB = AES256::encrypt($ACTVN_TOKEN_MOB_RAW);
  $MOB_CHANNEL_ACCESS_FLG = "YY";
  $FULL_MOB_ACTVN_CODE = $ACTVN_TOKEN_MOB_RAW.GetSystemParameter("ORGCODE");

  # ... 02: Save Data to DataBase
  $q = "INSERT INTO cstmrs(CUST_CORE_ID, APPLN_REF, APPLN_REF_MOB, ACTVN_TOKEN, ACTVN_TOKEN_MOB, CUST_EMAIL, CUST_PHONE, WEB_CHANNEL_ACCESS_FLG, MOB_CHANNEL_ACCESS_FLG, CUST_STATUS) VALUES('$CUST_CORE_ID', '$APPLN_REF', '$APPLN_REF_MOB', '$ACTVN_TOKEN', '$ACTVN_TOKEN_MOB', '$CUST_EMAIL', '$CUST_PHONE', '$WEB_CHANNEL_ACCESS_FLG', '$MOB_CHANNEL_ACCESS_FLG', '$CUST_STATUS')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"];
  $RECORD_ID = $exec_response["RECORD_ID"];

  # ... Process Entity System ID (Cust ID)
  $id_prefix = "M";
  $id_len = 7;
  $id_record_id = $RECORD_ID;
  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
  $CUST_ID = $ENTITY_ID;

  # ... Updating the role id
  $q2 = "UPDATE cstmrs SET CUST_ID='$CUST_ID' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);

  # ... Updating the role id
  $OAA_FLG = "YY";
  $OAA_USER_ID = $_SESSION['UPR_USER_ID'];
  $OAA_DATE = GetCurrentDateTime();
  $ACTIVATION_STATUS = "COMPLETE";

  $ACTIVATION_REF_MOB = $APPLN_REF_MOB;
  $q3 = "UPDATE cstmrs_actvn_rqsts SET ACTIVATION_REF_MOB='$ACTIVATION_REF_MOB', OAA_FLG='$OAA_FLG', OAA_USER_ID='$OAA_USER_ID', OAA_DATE='$OAA_DATE', ACTIVATION_STATUS='$ACTIVATION_STATUS' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response3 = ExecuteEntityUpdate($q3);
  if ($update_response3 == "EXECUTED") {

    # ... 0001: Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "e-PLATFORM account activation";
    $RECIPIENT_EMAILS = $EMAIL;
    $EMAIL_MESSAGE = mysql_real_escape_string("Dear " . $FIRST_NAME . "<br>"
      . "Congratulations! your e-PLATFORM account has been activated. Follow these steps to complete the activation by yourself;<br>"
      . "<br> 1. Click account activation."
      . "<br> 2. Click 'Activate Account Button'."
      . "<br> 3. Enter Activation Reference which is: <b>" . $ACTIVATION_REF . "</b> "
      . "<br> 4. Enter Activation Token <em>You have received this in a seperate email</em>."
      . "<br> 5. Follow through with the system to complete account set up."
      . "<br/>"
      . "<br/>"
      . "Should you need clarification, do not hesitate to contact us back.<br>"
      . "Regards<br>"
      . "Management<br>"
      . "<i></i>");
    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

    $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
    ExecuteEntityInsert($qqq);

    # ... 0002: Sending mail 2 ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "e-PLATFORM activation token";
    $RECIPIENT_EMAILS = $EMAIL;
    $EMAIL_MESSAGE = "Dear " . $FIRST_NAME . "<br>"
      . "Your e-PLATFORM activation token is: <b>" . $ACTVN_TOKEN_RAW . "</b><br>"
      . "<br>"
      . "Regards<br>"
      . "Management<br>"
      . "<i></i>";
    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

    $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
    ExecuteEntityInsert($qqq);

    # ... 0003: Sending mail 3 (mobile activation)  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "e-MOBILE account activation";
    $RECIPIENT_EMAILS = $EMAIL;
    $EMAIL_MESSAGE = mysql_real_escape_string("Dear " . $FIRST_NAME . "<br>"
      . "Congratulations! your e-MOBILE account has been activated. Follow these steps to complete the activation by yourself;<br>"
      . "<br> 1. Open google play store on your android phone."
      . "<br> 2. Search for <b>Mavuno</b> and then install."
      . "<br> 3. Enter Appln Ref which is: <b>" . $APPLN_REF_MOB . "</b> "
      . "<br> 4. Enter Activation Code which is: <b>" . $FULL_MOB_ACTVN_CODE . "</b> "
      . "<br> 5. Follow through with the system to complete account set up."
      . "<br/>"
      . "<br/>"
      . "Should you need clarification, do not hesitate to contact us back.<br>"
      . "Regards<br>"
      . "Management<br>"
      . "<i></i>");
    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

    $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
    ExecuteEntityInsert($qqq);

    # ... 0004: Sending sms message (mobile activation)  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $INIT_CHANNEL = "WEB";
    $CHRG_SOURCE = "ORG";
    $CHRG_CUST_ID = "";
    $CHRG_ACCT_ID = "";
    $MSG_TYPE = "URGENT";        // ... URGENT or NORMAL
    $RECIPIENT_NO = $MOBILE_NO;
    $SMS_MESSAGE = "Dear " . $FIRST_NAME . ", activate your e-MOBILE app with APPLN REF: -->". $APPLN_REF_MOB."<-- and CODE: -->". $FULL_MOB_ACTVN_CODE."<--";
    $SMS_MESSAGE_LEN = strlen($SMS_MESSAGE);
    $RECORD_DATE = GetCurrentDateTime();

    $qqq = "INSERT INTO outbox_sms(INIT_CHANNEL, CHRG_SOURCE, CHRG_CUST_ID, CHRG_ACCT_ID, MSG_TYPE, RECIPIENT_NO, SMS_MESSAGE, SMS_MESSAGE_LEN, RECORD_DATE) VALUES('$INIT_CHANNEL', '$CHRG_SOURCE', '$CHRG_CUST_ID', '$CHRG_ACCT_ID', '$MSG_TYPE', '$RECIPIENT_NO', '$SMS_MESSAGE', '$SMS_MESSAGE_LEN', '$RECORD_DATE')";
    ExecuteEntityInsert($qqq);


    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "FINAL_SETUP";
    $EVENT_OPERATION = "ACTIVATING APPLICATION REQUEST";
    $EVENT_RELATION = "cstmrs_actvn_rqsts -> cstmrs";
    $EVENT_RELATION_NO = $CUST_ID;
    $OTHER_DETAILS = $CUST_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent(
      $AUDIT_DATE,
      $ENTITY_TYPE,
      $ENTITY_ID_AFFECTED,
      $EVENT,
      $EVENT_OPERATION,
      $EVENT_RELATION,
      $EVENT_RELATION_NO,
      $OTHER_DETAILS,
      $INVOKER_ID
    );


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Congratulations! account setup is complete. An activation Token has been sent out to the client. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    header("Refresh:5;");
  }
}

# ... Link Exisitng Customer
if (isset($_POST['btn_link_cstmr'])) {
  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $CLIENT_CORE_ID = trim($_POST['CLIENT_CORE_ID']);

  // ... 01: Update Customer Details (Gender & PhonrNumber)
  $cstmr_actvn = array();
  $cstmr_actvn = FetchActivationRequestById($ACTIVATION_REF);
  $OFFICE_ID = $CORE_OFFICE_DEF["HQ"];
  $MOBILE_NO = $cstmr_actvn['MOBILE_NO'];
  $GENDER = $cstmr_actvn['GENDER'];
  $GENDER_ID = $CORE_GENDER_DEF[$GENDER];

  $CORE_RQST_MSG1 = BuildUpdateGenderMessage($GENDER_ID);
  UpdateCustomerPhone($CLIENT_CORE_ID, $CORE_RQST_MSG1, $MIFOS_CONN_DETAILS);

  $CORE_RQST_MSG2 = BuildUpdatePhoneMessage($MOBILE_NO);
  UpdateCustomerPhone($CLIENT_CORE_ID, $CORE_RQST_MSG2, $MIFOS_CONN_DETAILS);

  // ... 02: Push Other Details to Core
  $EMAIL = $cstmr_actvn['EMAIL'];
  $WORK_ID = $cstmr_actvn['WORK_ID'];
  $NATIONAL_ID = $cstmr_actvn['NATIONAL_ID'];
  $PHYSICAL_ADDRESS = $cstmr_actvn['PHYSICAL_ADDRESS'];
  $DOB = $cstmr_actvn['DOB'];

  $OtherDetailsList = array(
    "Email" => $EMAIL,
    "WorkID" => $WORK_ID,
    "NationalId" => $NATIONAL_ID,
    "Physical_Address" => $PHYSICAL_ADDRESS,
    "Date_of_Birth" => $DOB
  );

  $DATATABLE_NAME = "OtherDetails";
  $DATATABLE_ENTITY_ID = $CLIENT_CORE_ID;
  $DATATABLE_RQST_MSG = BuildDataTableRqstMsg($OtherDetailsList);
  PostDataToDataTable($DATATABLE_NAME, $DATATABLE_ENTITY_ID, $DATATABLE_RQST_MSG, $MIFOS_CONN_DETAILS);


  // ... 03: Completing the process of customer creation
  $CST_CORE_CRTN_FLG = "YY";
  $CST_CORE_ID = $CLIENT_CORE_ID;
  $CST_CORE_CRTN_USER_ID = $_SESSION['UPR_USER_ID'];
  $CST_CORE_CRTN_DATE = GetCurrentDateTime();

  # ... SQL Query
  $q = "UPDATE cstmrs_actvn_rqsts SET CST_CORE_CRTN_FLG='$CST_CORE_CRTN_FLG', CST_CORE_ID='$CST_CORE_ID', CST_CORE_CRTN_USER_ID='$CST_CORE_CRTN_USER_ID', CST_CORE_CRTN_DATE='$CST_CORE_CRTN_DATE' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response == "EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "FINAL_SETUP";
    $EVENT_OPERATION = "LINK_CUSTOMER_DETAILS_TO_CORE_DETAILS";
    $EVENT_RELATION = "cstmrs_actvn_rqsts";
    $EVENT_RELATION_NO = $ACTIVATION_REF;
    $OTHER_DETAILS = "Client_Creation {clientId: " . $CLIENT_CORE_ID . "}";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent(
      $AUDIT_DATE,
      $ENTITY_TYPE,
      $ENTITY_ID_AFFECTED,
      $EVENT,
      $EVENT_OPERATION,
      $EVENT_RELATION,
      $EVENT_RELATION_NO,
      $OTHER_DETAILS,
      $INVOKER_ID
    );


    $alert_type = "INFO";
    $alert_msg = "SUCCESS: customer details have been linked up successfully. Re-directing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    header("Refresh:5;");
  }
}

?>
<!DOCTYPE html>
<html>

<head>
  <?php
  # ... Device Settings and Global CSS
  LoadDeviceSettings();
  LoadDefaultCSSConfigurations("Finalize SetUp", $APP_SMALL_LOGO);

  # ... Javascript
  LoadPriorityJS();
  OnLoadExecutions();
  StartTimeoutCountdown();
  ExecuteProcessStatistics();

  ?>
  <script type="text/javascript">
    function ValidateCreateShr() {
      var creat_shr = document.getElementById('CREATE_SHRS_FLG').value;
      if (creat_shr == "NN") {
        $('#SHARES_PDT_ID').prop('required', false);
        $('#SHARES_PDT_ID').prop('disabled', true);
      }
      if (creat_shr == "YY") {
        $('#SHARES_PDT_ID').prop('required', true);
        $('#SHARES_PDT_ID').prop('disabled', false);
      }
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
          <div align="center" style="width: 100%;"><?php if (isset($_SESSION['ALERT_MSG'])) {
                                                      echo $_SESSION['ALERT_MSG'];
                                                    } ?></div>


          <div class="x_panel">
            <div class="x_title">
              <a href="cm-finalize-enrollment" class="btn btn-sm btn-dark pull-left">Back</a>
              <h2>Finalize SetUp <small><?php echo $ACTIVATION_REF; ?></small></h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content" id="page_data_data">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="">
                  <div class="x_title">
                    <strong>SET UP PROGRESS</strong>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <table class="table table-bordered">
                      <thead>
                        <tr valign="top">
                          <th width="3%">#</th>
                          <th width="35%">Activity</th>
                          <th>Status</th>
                          <th>Core Rmk</th>
                          <th>Action</th>
                        </tr>
                      </thead>

                      <?php
                      if ($MMBSHP_TYPE == "NEW") {
                      ?>
                        <tr valign="top">
                          <td>1.</td>
                          <td>Creation of Customer Details in Core</td>
                          <td align="center" style="<?php echo BackgrounfColorByStatusFlg($CST_CORE_CRTN_FLG); ?>">
                            <?php echo PendingOrDone($CST_CORE_CRTN_FLG); ?>
                          </td>
                          <td>
                            <?php
                            $status = PendingOrDone($CST_CORE_CRTN_FLG);
                            if ($status == "done") {
                              $response_msg = FetchCustomerDetailsFromCore($CST_CORE_ID, $MIFOS_CONN_DETAILS);
                              $CONN_FLG = $response_msg["CONN_FLG"];
                              $CORE_RESP = $response_msg["CORE_RESP"];

                              if (isset($CORE_RESP["accountNo"])) {
                                echo $CORE_RESP["accountNo"];
                              }
                            }
                            ?>
                          </td>
                          <td>
                            <?php
                            $status = PendingOrDone($CST_CORE_CRTN_FLG);
                            if ($status == "done") {
                            } else {
                            ?>
                              <!--<form method="post" id="cccc">
                                <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                <input type="hidden" id="CST_CORE_ID" name="CST_CORE_ID" value="<?php echo $CST_CORE_ID; ?>">
                                <button type="submit" class="btn btn-primary btn-xs" name="btn_create_cust_core">Create</button>
                              </form>-->

                              <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#ucuwtwaa">Create Customer</button>                              <div id="ucuwtwaa" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-sm">
                                  <div class="modal-content" style="color: #333;">

                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                      </button>
                                      <h4 class="modal-title" id="myModalLabel2">Create Customer</h4>
                                    </div>
                                    <div class="modal-body">
                                      <form id="create_customer_core" method="post">
                                        <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                        <input type="hidden" id="CST_CORE_ID" name="CST_CORE_ID" value="<?php echo $CST_CORE_ID; ?>">

                                        Select Client Type:<br>
                                        <select id="client_type" name="client_type" class="form-control" required="">
                                          <option value="">Select Client Type</option>
                                          <?php
                                          $ct_list = array();
                                          $ct_list = explode('^', GetSystemParameter("CORE_CLIENT_TYPE_DEF"));
                                          for ($i = 0; $i < sizeof($ct_list); $i++) {
                                            $ct_map = array();
                                            $ct_map = explode('-', $ct_list[$i]);
                                            $ct_code = $ct_map[0];
                                            $ct_name = $ct_map[1];
                                          ?>
                                            <option value="<?php echo $ct_code; ?>"><?php echo $ct_name; ?></option>
                                          <?php
                                          }
                                          ?>
                                        </select><br><br>


                                        The client is classified as a:<br>
                                        <select id="client_classfcn" name="client_classfcn" class="form-control" required="">
                                          <option value="">Select Client Classification</option>
                                          <?php
                                          $cttt_list = array();
                                          $cttt_list = explode('^', GetSystemParameter("CORE_CLIENT_CLSSFCN_DEF"));
                                          for ($i = 0; $i < sizeof($cttt_list); $i++) {
                                            $cttt_map = array();
                                            $cttt_map = explode('-', $cttt_list[$i]);
                                            $cttt_code = $cttt_map[0];
                                            $cttt_name = $cttt_map[1];
                                          ?>
                                            <option value="<?php echo $cttt_code; ?>"><?php echo $cttt_name; ?></option>
                                          <?php
                                          }
                                          ?>
                                        </select><br><br>



                                        <br>
                                        <br>
                                        <button type="submit" class="btn btn-primary btn-sm" name="btn_create_cust_core">Create Client</button>
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

                      if ($MMBSHP_TYPE == "EXST") {
                      ?>
                        <tr valign="top">
                          <td>1.</td>
                          <td>Link customer to record in CORE</td>
                          <td align="center" style="<?php echo BackgrounfColorByStatusFlg($CST_CORE_CRTN_FLG); ?>">
                            <?php echo PendingOrDone($CST_CORE_CRTN_FLG); ?>
                          </td>
                          <td>
                            <?php
                            $status = PendingOrDone($CST_CORE_CRTN_FLG);
                            if ($status == "done") {
                              $response_msg = FetchCustomerDetailsFromCore($CST_CORE_ID, $MIFOS_CONN_DETAILS);
                              $CONN_FLG = $response_msg["CONN_FLG"];
                              $CORE_RESP = $response_msg["CORE_RESP"];

                              if (isset($CORE_RESP["accountNo"])) {
                                echo $CORE_RESP["accountNo"];
                              }
                            }
                            ?>
                          </td>
                          <td>
                            <?php
                            $status = PendingOrDone($CST_CORE_CRTN_FLG);
                            if ($status == "done") {
                            } else {
                            ?>
                              <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#link_c">Link Customer</button>
                              <div id="link_c" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg">
                                  <div class="modal-content">

                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                      </button>
                                      <h4 class="modal-title" id="myModalLabel2">Link customer to record in CORE</h4>
                                    </div>
                                    <div class="modal-body">
                                      <table id="datatable2" class="table table-striped table-bordered">
                                        <thead>
                                          <tr valign="top">
                                            <th colspan="7" bgcolor="#EEE">Client List</th>
                                          </tr>
                                          <tr valign="top">
                                            <th>#</th>
                                            <th>Client Name</th>
                                            <th>Client Id</th>
                                            <th>External Id</th>
                                            <th>Activation Date</th>
                                            <th>e-Status</th>
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
                                          for ($i = 0; $i < sizeof($client_list); $i++) {

                                            $client = array();
                                            $client = $client_list[$i]["row"];
                                            $CLIENT_CORE_ID = $client[0];
                                            $CLIENT_CORE_ID_NUM = $client[1];
                                            $CLIENT_STATUS_ENUM = $client[2];
                                            $CLIENT_CORE_NAME = $client[3];
                                            $CLIENT_EXTERN_ID = $client[4];
                                            $CLIENT_ACTVN_DATE = $client[5];
                                            $E_STATUS = "NOT_ENROLLED";
                                            $Q_CHK = "SELECT count(*) as RTN_VALUE FROM cstmrs WHERE CUST_CORE_ID='$CLIENT_CORE_ID' AND CUST_STATUS not in ('DELETED','REJECTED')";
                                            $C_CHK = ReturnOneEntryFromDB($Q_CHK);
                                            if ($C_CHK > 0) {
                                              $E_STATUS = "ENROLLED";
                                            }



                                            if ($E_STATUS == "NOT_ENROLLED") {
                                              $form_id = "F_" . ($x + 1);
                                          ?>
                                              <tr valign="top">
                                                <td><?php echo ($x + 1); ?>. </td>
                                                <td><?php echo $CLIENT_CORE_NAME; ?></td>
                                                <td><?php echo $CLIENT_CORE_ID_NUM; ?></td>
                                                <td><?php echo $CLIENT_EXTERN_ID; ?></td>
                                                <td><?php echo $CLIENT_ACTVN_DATE; ?></td>
                                                <td><?php echo $E_STATUS; ?></td>
                                                <td>
                                                  <form id="<?php echo $form_id; ?>" method="post">
                                                    <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                                    <input type="hidden" id="CLIENT_CORE_ID" name="CLIENT_CORE_ID" value="<?php echo $CLIENT_CORE_ID; ?>">
                                                    <button type="submit" class="btn btn-primary btn-xs" name="btn_link_cstmr">Select</button>
                                                  </form>

                                                </td>
                                              </tr>
                                          <?php
                                              $x++;
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
                          </td>
                        </tr>
                      <?php
                      }
                      ?>
                      <tr valign="top">
                        <td>2.</td>
                        <td>Upload of Passport Photo</td>
                        <td align="center" style="<?php echo BackgrounfColorByStatusFlg($CORE_IMG_UPLD_FLG); ?>">
                          <?php echo PendingOrDone($CORE_IMG_UPLD_FLG); ?>
                        </td>
                        <td></td>
                        <td>
                          <form method="post" id="mmmmmm">
                            <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                            <input type="hidden" id="CST_CORE_ID" name="CST_CORE_ID" value="<?php echo $CST_CORE_ID; ?>">
                            <a href="<?php echo $PP_LNK; ?>" target="_blank" class="btn btn-xs btn-default">View File</a>

                            <?php
                            $status = PendingOrDone($CORE_IMG_UPLD_FLG);
                            if ($status == "done") {
                            } else {
                            ?>

                              <button type="submit" class="btn btn-warning btn-xs" <?php echo DisableByStatusFlg($CST_CORE_CRTN_FLG); ?> name="btn_upld_pp">Upload</button>

                            <?php
                            }
                            ?>
                          </form>
                        </td>
                      </tr>
                      <tr valign="top">
                        <td>3.</td>
                        <td>Upload of Work ID</td>
                        <td align="center" style="<?php echo BackgrounfColorByStatusFlg($WRKID_UPLD_FLG); ?>">
                          <?php echo PendingOrDone($WRKID_UPLD_FLG); ?>
                        </td>
                        <td></td>
                        <td>
                          <form method="post" id="eeerte">
                            <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                            <input type="hidden" id="CST_CORE_ID" name="CST_CORE_ID" value="<?php echo $CST_CORE_ID; ?>">
                            <a href="<?php echo $WORK_ID_LNK; ?>" target="_blank" class="btn btn-xs btn-default">View File</a>
                            <?php
                            $status = PendingOrDone($WRKID_UPLD_FLG);
                            if ($status == "done") {
                            } else {
                            ?>

                              <button type="submit" class="btn btn-warning btn-xs" <?php echo DisableByStatusFlg($CORE_IMG_UPLD_FLG); ?> name="btn_upld_wrk">Upload</button>

                            <?php
                            }
                            ?>
                          </form>
                        </td>
                      </tr>
                      <tr valign="top">
                        <td>4.</td>
                        <td>Upload of National ID</td>
                        <td align="center" style="<?php echo BackgrounfColorByStatusFlg($NIN_UPLD_FLG); ?>">
                          <?php echo PendingOrDone($NIN_UPLD_FLG); ?>
                        </td>
                        <td></td>
                        <td>
                          <form method="post" id="ertygfxsss">
                            <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                            <input type="hidden" id="CST_CORE_ID" name="CST_CORE_ID" value="<?php echo $CST_CORE_ID; ?>">
                            <a href="<?php echo $NATIONAL_ID_LNK; ?>" target="_blank" class="btn btn-xs btn-default">View File</a>
                            <?php
                            $status = PendingOrDone($NIN_UPLD_FLG);
                            if ($status == "done") {
                            } else {
                            ?>
                              <button type="submit" class="btn btn-warning btn-xs" <?php echo DisableByStatusFlg($WRKID_UPLD_FLG); ?> name="btn_upld_nin">Upload</button>

                            <?php
                            }
                            ?>
                          </form>
                        </td>
                      </tr>
                      <tr valign="top">
                        <td>5.</td>
                        <td>Upload of Membership Application Form</td>
                        <td align="center" style="<?php echo BackgrounfColorByStatusFlg($MAF_UPLD_FLG); ?>">
                          <?php echo PendingOrDone($MAF_UPLD_FLG); ?>
                        </td>
                        <td></td>
                        <td>
                          <form method="post" id="ttyuuioo">
                            <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                            <input type="hidden" id="CST_CORE_ID" name="CST_CORE_ID" value="<?php echo $CST_CORE_ID; ?>">
                            <a href="<?php echo $MAF_LNK; ?>" target="_blank" class="btn btn-xs btn-default">View File</a>
                            <?php
                            $status = PendingOrDone($MAF_UPLD_FLG);
                            if ($status == "done") {
                            } else {
                            ?>
                              <button type="submit" class="btn btn-warning btn-xs" <?php echo DisableByStatusFlg($NIN_UPLD_FLG); ?> name="btn_upld_maf">Upload</button>

                            <?php
                            }
                            ?>
                          </form>
                        </td>
                      </tr>

                      <?php
                      if ($MMBSHP_TYPE == "EXST") {
                      ?>
                        <tr valign="top">
                          <td>6.</td>
                          <td>Online Account Activation</td>
                          <td align="center" style="<?php echo BackgrounfColorByStatusFlg($OAA_FLG); ?>">
                            <?php echo PendingOrDone($OAA_FLG); ?>
                          </td>
                          <td></td>
                          <td>
                            <?php
                            $status = PendingOrDone($OAA_FLG);
                            if ($status == "done") {
                            } else {
                            ?>
                              <form method="post" id="aazassxs">
                                <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                <input type="hidden" id="CST_CORE_ID" name="CST_CORE_ID" value="<?php echo $CST_CORE_ID; ?>">
                                <button type="submit" class="btn btn-success btn-xs" <?php echo DisableByStatusFlg($MAF_UPLD_FLG); ?> name="btn_actvte_acct">Activate Online Account</button>
                              </form>
                            <?php
                            }
                            ?>
                          </td>
                        </tr>
                      <?php
                      } else if ($MMBSHP_TYPE == "NEW") {
                      ?>
                        <tr valign="top">
                          <td>6.</td>
                          <td>Creation of Savings & Shares Accounts</td>
                          <td align="center" style="<?php echo BackgrounfColorByStatusFlg($SVNGS_ACCT_CRTN_FLG); ?>">
                            <?php echo PendingOrDone($SVNGS_ACCT_CRTN_FLG); ?>
                          </td>
                          <td></td>
                          <td>
                            <?php
                            $status = PendingOrDone($SVNGS_ACCT_CRTN_FLG);
                            if ($status == "done") {
                            } else {
                            ?>
                              <button type="button" class="btn btn-dark btn-xs" data-toggle="modal" data-target="#undobio" <?php echo DisableByStatusFlg($MAF_UPLD_FLG); ?>>Create Accounts</button>
                              <div id="undobio" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-sm">
                                  <div class="modal-content" style="color: #333;">

                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                      </button>
                                      <h4 class="modal-title" id="myModalLabel2">Create Accounts</h4>
                                    </div>
                                    <div class="modal-body">
                                      <form id="undobiooo" method="post">
                                        <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                        <input type="hidden" id="CST_CORE_ID" name="CST_CORE_ID" value="<?php echo $CST_CORE_ID; ?>">

                                        Select Savings Product:<br>
                                        <select id="SVNGS_PDT_ID" name="SVNGS_PDT_ID" class="form-control" required="">
                                          <option value="">Select Value</option>
                                          <?php
                                          $response_msg = FetchSavingsProducts($MIFOS_CONN_DETAILS);
                                          $CONN_FLG = $response_msg["CONN_FLG"];
                                          $CORE_RESP = $response_msg["CORE_RESP"];
                                          $savings_pdt_list = $CORE_RESP;
                                          for ($i = 0; $i < sizeof($savings_pdt_list); $i++) {

                                            $savings_pdt = $savings_pdt_list[$i];
                                            $id = $savings_pdt["id"];
                                            $name = $savings_pdt["name"];
                                            $shortName = $savings_pdt["shortName"];
                                          ?>
                                            <option value="<?php echo $id; ?>"><?php echo $name . " (" . $shortName . ")"; ?></option>
                                          <?php
                                          }
                                          ?>
                                        </select><br><br>


                                        Create Shares Account:<br>
                                        <select id="CREATE_SHRS_FLG" name="CREATE_SHRS_FLG" class="form-control" required="" onchange="ValidateCreateShr()">
                                          <option value="">Select Value</option>
                                          <option value="NN">Dont Create</option>
                                          <option value="YY">Create Shares Account</option>
                                        </select><br><br>


                                        Select Shares Product:<br>
                                        <select id="SHARES_PDT_ID" name="SHARES_PDT_ID" class="form-control">
                                          <option value="">Select Value</option>
                                          <?php
                                          $response_msg = FetchShareProducts($MIFOS_CONN_DETAILS);
                                          $CONN_FLG = $response_msg["CONN_FLG"];
                                          $CORE_RESP = $response_msg["CORE_RESP"]["pageItems"];
                                          $shares_pdt_list = $CORE_RESP;
                                          for ($x = 0; $x < sizeof($shares_pdt_list); $x++) {

                                            $shares_pdt = $shares_pdt_list[$x];
                                            $id = $shares_pdt["id"];
                                            $name = $shares_pdt["name"];
                                            $shortName = $shares_pdt["shortName"];
                                          ?>
                                            <option value="<?php echo $id; ?>"><?php echo $name . " (" . $shortName . ")"; ?></option>
                                          <?php
                                          }
                                          ?>
                                        </select><br>
                                        <br>
                                        <button type="submit" class="btn btn-primary btn-sm" name="btn_crt_svngs_acct">Submit</button>
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
                        <tr valign="top">
                          <td>7.</td>
                          <td>Online Account Activation</td>
                          <td align="center" style="<?php echo BackgrounfColorByStatusFlg($OAA_FLG); ?>">
                            <?php echo PendingOrDone($OAA_FLG); ?>
                          </td>
                          <td></td>
                          <td>
                            <?php
                            $status = PendingOrDone($OAA_FLG);
                            if ($status == "done") {
                            } else {
                            ?>
                              <form method="post" id="aazassxs">
                                <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                                <input type="hidden" id="CST_CORE_ID" name="CST_CORE_ID" value="<?php echo $CST_CORE_ID; ?>">
                                <button type="submit" class="btn btn-success btn-xs" <?php echo DisableByStatusFlg($SVNGS_ACCT_CRTN_FLG); ?> name="btn_actvte_acct">Activate Online Account</button>
                              </form>
                              
                            <?php
                            }
                            ?>
                          </td>
                        </tr>
                      <?php
                      } # ... END..IFF 
                      ?>



                    </table>

                  </div>
                </div>
              </div>


              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="">
                  <div class="x_title">
                    <strong>CUSTOMER DETAILS</strong>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <div class="col-md-6 col-sm-6 col-xs-12">
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

                    <div class="col-md-6 col-sm-6 col-xs-12">
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