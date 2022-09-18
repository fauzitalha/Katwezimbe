<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... 001: Receiving Data ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
$CUST_CORE_ID = $_SESSION["CUST_CORE_ID"];
$CLIENT_CORE_ID = "";
$CLIENT_CORE_ID_NUM = "";
$CLIENT_EXTERN_ID = "";
$CLIENT_STATUS = "";
$ACTIVATION_dATE= "";
$CLIENT_DISP_NAME = "";
$GENDER = "";
$CLIENT_CLASFCN = "";
$CLIENT_OFFICE = "";
$CLIENT_STAFF_NAME = "";
$response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];

/*$CLIENT_CORE_ID = $CORE_RESP["id"];
$CLIENT_CORE_ID_NUM = $CORE_RESP["accountNo"];
$CLIENT_EXTERN_ID = (isset($CORE_RESP["externalId"]))? $CORE_RESP["externalId"]: "";
$CLIENT_STATUS = $CORE_RESP["status"]["value"];
$ACTIVATION_dATE= $CORE_RESP["activationDate"][0]."-".["activationDate"][0]."-".["activationDate"][0];
$CLIENT_DISP_NAME = $CORE_RESP["displayName"];
$GENDER = $CORE_RESP["gender"]["name"];
$CLIENT_CLASFCN = $CORE_RESP["clientClassification"]["name"];
$CLIENT_OFFICE = $CORE_RESP["officeName"];
$CLIENT_STAFF_NAME = (isset($CORE_RESP["staffName"]))? $CORE_RESP["staffName"]: "";*/


$CLIENT_CORE_ID = (isset($CORE_RESP["id"]))? $CORE_RESP["id"]: "";
$CLIENT_CORE_ID_NUM = (isset($CORE_RESP["accountNo"]))? $CORE_RESP["accountNo"]: "";
$CLIENT_EXTERN_ID = (isset($CORE_RESP["externalId"]))? $CORE_RESP["externalId"]: "";
$CLIENT_STATUS = (isset( $CORE_RESP["status"]["value"]))? $CORE_RESP["status"]["value"]: "";
$ACTIVATION_dATE= $CORE_RESP["activationDate"][0]."-".["activationDate"][0]."-".["activationDate"][0];
$CLIENT_DISP_NAME = $CORE_RESP["displayName"];  (isset($CORE_RESP["id"]))? $CORE_RESP["id"]: "";
$GENDER = (isset($CORE_RESP["gender"]["name"]))? $CORE_RESP["gender"]["name"]: "";
$CLIENT_CLASFCN = (isset($CORE_RESP["clientClassification"]["name"]))? $CORE_RESP["clientClassification"]["name"]: "";
$CLIENT_OFFICE = (isset($CORE_RESP["officeName"]))? $CORE_RESP["officeName"]: "";
$CLIENT_STAFF_NAME = (isset($CORE_RESP["staffName"]))? $CORE_RESP["staffName"]: "";



# ... 001_001: CUSTOMER IMAGE... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .... ... ... ... ... ... ... ...#
$IMAGE_response_msg = "";
$IMAGE_CAPTURE_STATUS = "";
$IMAGE_response_msg = FetchClientImage($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
if ( isset($IMAGE_response_msg["defaultUserMessage"])) {
  $IMAGE_CAPTURE_STATUS = "ACCOUNT IS IMAGELESS";
} else {
  $IMAGE_CAPTURE_STATUS = "ACCOUNT HAS IMAGE";
}



# ... 002: CHECK IF CLIENT IS ENROLLED ... ... ... ... ... ... . ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
$CUST_RECORD_ID = "";
$CUST_ID = "";
$APPLN_REF = "";
$ACTVN_TOKEN = "";
$CUST_EMAIL = "";
$CUST_PHONE = "";
$WEB_CHANNEL_LOGIN_ATTEMPTS = "";
$WEB_CHANNEL_ACCESS_FLG = "";
$WEB_CHANNEL_ACTVN_FLG = "";
$WEB_CHANNEL_ACTVN_DATE = "";
$MOB_CHANNEL_LOGIN_ATTEMPTS = "";
$MOB_CHANNEL_ACCESS_FLG = "";
$MOB_CHANNEL_ACTVN_FLG = "";
$MOB_CHANNEL_ACTVN_DATE = "";
$CUST_USR = "";
$CUST_PWSD_STATUS = "";
$CUST_PWSD = "";
$CUST_PWSD_LST_CHNG_DATE = "";
$CUST_PIN_STATUS = "";
$CUST_PIN = "";
$CUST_PIN_LST_CHNG_DATE = "";
$CUST_DEVICE_ID = "";
$CUST_SIM_IMEI = "";
$CUST_STATUS = "";
$E_STATUS = "NOT_ENROLLED";
$_SESSION['FP_NAME'] = "";
$_SESSION['FP_EMAIL'] = "";
$_SESSION['FP_PHONE'] = "";
$Q_CHK = "SELECT count(*) as RTN_VALUE FROM cstmrs WHERE CUST_CORE_ID='$CUST_CORE_ID' AND CUST_STATUS not in ('DELETED','REJECTED')";
$C_CHK = ReturnOneEntryFromDB($Q_CHK);
if ($C_CHK>0) {
  $E_STATUS = "ENROLLED";

  # ... Get Cust ID
  $Q_CUST = "SELECT CUST_ID as RTN_VALUE FROM cstmrs WHERE CUST_CORE_ID='$CUST_CORE_ID'";
  $V_CUST_ID = ReturnOneEntryFromDB($Q_CUST);

  # ... Fetch Cust Details
  $cstmr = array();
  $cstmr = FetchCustomerLoginDataByCustId($V_CUST_ID);
  $CUST_RECORD_ID = $cstmr['RECORD_ID'];
  $CUST_ID = $cstmr['CUST_ID'];
  $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
  $APPLN_REF = $cstmr['APPLN_REF'];
  $ACTVN_TOKEN = $cstmr['ACTVN_TOKEN'];
  $CUST_EMAIL = $cstmr['CUST_EMAIL'];
  $CUST_PHONE = $cstmr['CUST_PHONE'];
  $WEB_CHANNEL_LOGIN_ATTEMPTS = $cstmr['WEB_CHANNEL_LOGIN_ATTEMPTS'];
  $WEB_CHANNEL_ACCESS_FLG = $cstmr['WEB_CHANNEL_ACCESS_FLG'];
  $WEB_CHANNEL_ACTVN_FLG = $cstmr['WEB_CHANNEL_ACTVN_FLG'];
  $WEB_CHANNEL_ACTVN_DATE = $cstmr['WEB_CHANNEL_ACTVN_DATE'];
  $MOB_CHANNEL_LOGIN_ATTEMPTS = $cstmr['MOB_CHANNEL_LOGIN_ATTEMPTS'];
  $MOB_CHANNEL_ACCESS_FLG = $cstmr['MOB_CHANNEL_ACCESS_FLG'];
  $MOB_CHANNEL_ACTVN_FLG = $cstmr['MOB_CHANNEL_ACTVN_FLG'];
  $MOB_CHANNEL_ACTVN_DATE = $cstmr['MOB_CHANNEL_ACTVN_DATE'];
  $CUST_USR = $cstmr['CUST_USR'];
  $CUST_PWSD_STATUS = $cstmr['CUST_PWSD_STATUS'];
  $CUST_PWSD = $cstmr['CUST_PWSD'];
  $CUST_PWSD_LST_CHNG_DATE = $cstmr['CUST_PWSD_LST_CHNG_DATE'];
  $CUST_PIN_STATUS = $cstmr['CUST_PIN_STATUS'];
  $CUST_PIN = $cstmr['CUST_PIN'];
  $CUST_PIN_LST_CHNG_DATE = $cstmr['CUST_PIN_LST_CHNG_DATE'];
  $CUST_DEVICE_ID = $cstmr['CUST_DEVICE_ID'];
  $CUST_SIM_IMEI = $cstmr['CUST_SIM_IMEI'];
  $CUST_STATUS = $cstmr['CUST_STATUS'];


  # ... Decrypt Email & Phone
  $EMAIL = AES256::decrypt($CUST_EMAIL);
  $PHONE = AES256::decrypt($CUST_PHONE);
  $_SESSION['FP_NAME'] = $CLIENT_DISP_NAME;
  $_SESSION['FP_EMAIL'] = $EMAIL;
  $_SESSION['FP_PHONE'] = $PHONE;
}


# ... 003: CUSTOMER SAVINGS ACCT LIST ... ... ... ... . ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
$response_msg = GetCustSavingsAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$savings_acct_list = array();
$savings_acct_list = (isset($CORE_RESP["data"])) ? $CORE_RESP["data"]: array();


# ... 004: CUSTOMER LOANS ACCT LIST ... ... ... ... . ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
$response_msg = GetCustLoansAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$loans_acct_list = array();
$loans_acct_list = (isset($CORE_RESP["data"])) ? $CORE_RESP["data"]: array();


# ... 005: CUSTOMER SHARES ACCT LIST ... ... ... ... . ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
$response_msg = GetCustSharesAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$shrs_acct_list = array();
$shrs_acct_list = (isset($CORE_RESP["data"])) ? $CORE_RESP["data"]: array();


# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "CUSTOMER";
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
  $EVENT_TYPE = "RESET MY PASSWORD";
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
      $MSG_TYPE = "PASSWORD_RESET_AUTH_TAN";
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

# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan_email'])) {
  
  $ENTITY_TYPE = "CUSTOMER";
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
  $EVENT_TYPE = "CHANGE MY EMAIL";
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
      $MSG_TYPE = "CHANGE_EMAIL_AUTH_TAN";
      $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
      $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                      ."Your email change auth TAN is: <b>".$TAN."</b>";
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

# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan_phone'])) {
  
  $ENTITY_TYPE = "CUSTOMER";
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
  $EVENT_TYPE = "CHANGE MY PHONE";
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
      $MSG_TYPE = "CHANGE_PHONE_AUTH_TAN";
      $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
      $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                      ."Your phone number change auth TAN is: <b>".$TAN."</b>";
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

# ... F0000001: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan_photo'])) {
  
  $ENTITY_TYPE = "CUSTOMER";
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
  $EVENT_TYPE = "CHANGE MY PHOTO";
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
      $MSG_TYPE = "CHANGE_PHOTO_AUTH_TAN";
      $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
      $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                      ."Your photo change auth TAN is: <b>".$TAN."</b>";
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


# ... FM003: RESET PASSWORD ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_reset_password'])) {

  $CUST_ID = trim($_POST['CUST_ID']);
  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));
  $temp_pwd = GeneratePassKey(10);

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
    # ... DB INSERT
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "TEMP_PSWD";
    $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
    $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                    ."This is your temporary Password: ".$temp_pwd;
    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

     $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP=="EXECUTED") {

      # ... Updating the Password
      $CUST_PWSD_STATUS = "RR";
      $CUST_PWSD = AES256::encrypt($temp_pwd);
      $CUST_PWSD_LST_CHNG_DATE = GetCurrentDateTime();

      $q2 = "UPDATE cstmrs SET CUST_PWSD_STATUS='$CUST_PWSD_STATUS', CUST_PWSD='$CUST_PWSD', CUST_PWSD_LST_CHNG_DATE='$CUST_PWSD_LST_CHNG_DATE'  WHERE CUST_ID='$CUST_ID'";
      $update_response = ExecuteEntityUpdate($q2);

      if($update_response == "EXECUTED"){

        # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
        $ENTITY_ID = $_SESSION['CST_USR_ID'];
        $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
        ExecuteEntityUpdate($qww);


        # ... Log System Audit Log
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "CUSTOMER";
        $ENTITY_ID_AFFECTED = $CUST_ID;
        $EVENT = "RESET_CUST_PASSWORD";
        $EVENT_OPERATION = "RESET_CUST_PASSWORD_BY_STAFF";
        $EVENT_RELATION = "outbox_email";
        $EVENT_RELATION_NO = $CUST_ID;
        $OTHER_DETAILS = $EMAIL_MESSAGE;
        $INVOKER_ID = $_SESSION['CST_USR_ID'];
        LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                       $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


        $alert_type = "INFO";
        $alert_msg = "MESSAGE: A temporary password has been sent out to customer via email. Logging you out in 4 seconds";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
        header("Refresh:5; URL=cst-lgin");
      }
    }
  } # ... END..IFF.ELSE
}

# ... FM004: CHANGE EMAIL ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_chng_email'])) {

  $CUST_ID = trim($_POST['CUST_ID']);
  $EMAIL = trim($_POST['EMAIL']);
  $TRAN_TAN = trim($_POST['TRAN_TAN']);
  $CHANGE_TYPE = "EMAIL_CHANGE";
  $OLD_VALUE = $_SESSION["FP_EMAIL"];
  $NEW_VALUE = $EMAIL;
  $CHNG_INIT_DATE = GetCurrentDateTime();
  $CHNG_INIT_BY = $_SESSION["CST_USR_ID"];

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

    # ... Detect Change
    if ($OLD_VALUE==$NEW_VALUE) {
      # ... Send System Response
      $alert_type = "WARNING";
      $alert_msg = "No change detected. Change not logged.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if ($OLD_VALUE!=$NEW_VALUE) {
      
      # ... Check if we have existing un-actioned change request
      $Q_CHK = "SELECT count(*) as RTN_VALUE FROM cstmrs_info_chng_log WHERE CUST_ID='$CUST_ID' AND CHANGE_TYPE='EMAIL_CHANGE' AND CHNG_STATUS='PENDING'";
      $C_CHK = ReturnOneEntryFromDB($Q_CHK);
      if ($C_CHK>0) {
        # ... Send System Response
        $alert_type = "WARNING";
        $alert_msg = "You have an un-approved change. Request management to action it.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      } else {
        $q = "INSERT INTO cstmrs_info_chng_log(CUST_ID,CHANGE_TYPE,OLD_VALUE,NEW_VALUE,CHNG_INIT_DATE,CHNG_INIT_BY) VALUES('$CUST_ID','$CHANGE_TYPE','$OLD_VALUE','$NEW_VALUE','$CHNG_INIT_DATE','$CHNG_INIT_BY')";
        $exec_response = array();
        $exec_response = ExecuteEntityInsert($q);
        $RESP = $exec_response["RESP"]; 
        $RECORD_ID = $exec_response["RECORD_ID"];
        if ($RESP=="EXECUTED") {

          # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
          $ENTITY_ID = $_SESSION['CST_USR_ID'];
          $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
          ExecuteEntityUpdate($qww);


          # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
          $AUDIT_DATE = GetCurrentDateTime();
          $ENTITY_TYPE = "CUSTOMER_EMAIL";
          $ENTITY_ID_AFFECTED = $_SESSION['CST_USR_ID'];
          $EVENT = "CHANGE_EMAIL";
          $EVENT_OPERATION = "CHANGE_EMAIL_ADDRESS";
          $EVENT_RELATION = "cstmrs_info_chng_log";
          $EVENT_RELATION_NO = $RECORD_ID;
          $OTHER_DETAILS = "{Old Value: ".$OLD_VALUE."|New Value: ".$NEW_VALUE;
          $INVOKER_ID = $_SESSION['CST_USR_ID'];
          LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                         $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


          $alert_type = "INFO";
          $alert_msg = "MESSAGE: Change of email application has been logged successfully. It will be actioned by management. Refreshing in 5 seconds.";
          $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
          header("Refresh:5; URL=prof-bio-data");
        }  # ... END..IFF
      }
    } #... END..IFF..ELSE
  } #... END..IFF..ELSE
}

# ... FM005: CHANGE PHONE ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_chng_phone'])) {

  $CUST_ID = trim($_POST['CUST_ID']);
  $PHONE = trim($_POST['PHONE']);
  $TRAN_TAN = trim($_POST['TRAN_TAN']);
  $CHANGE_TYPE = "PHONE_CHANGE";
  $OLD_VALUE = $_SESSION["FP_PHONE"];
  $NEW_VALUE = $PHONE;
  $CHNG_INIT_DATE = GetCurrentDateTime();
  $CHNG_INIT_BY = $_SESSION["CST_USR_ID"];

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

    # ... Detect Change
    if ($OLD_VALUE==$NEW_VALUE) {
      # ... Send System Response
      $alert_type = "WARNING";
      $alert_msg = "No change detected. Change not logged.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if ($OLD_VALUE!=$NEW_VALUE) {
      
      # ... Check if we have existing un-actioned change request
      $Q_CHK = "SELECT count(*) as RTN_VALUE FROM cstmrs_info_chng_log WHERE CUST_ID='$CUST_ID' AND CHANGE_TYPE='PHONE_CHANGE' AND CHNG_STATUS='PENDING'";
      $C_CHK = ReturnOneEntryFromDB($Q_CHK);
      if ($C_CHK>0) {
        # ... Send System Response
        $alert_type = "WARNING";
        $alert_msg = "You have an un-approved change. Request management to action it.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      } else {
        $q = "INSERT INTO cstmrs_info_chng_log(CUST_ID,CHANGE_TYPE,OLD_VALUE,NEW_VALUE,CHNG_INIT_DATE,CHNG_INIT_BY) VALUES('$CUST_ID','$CHANGE_TYPE','$OLD_VALUE','$NEW_VALUE','$CHNG_INIT_DATE','$CHNG_INIT_BY')";
        $exec_response = array();
        $exec_response = ExecuteEntityInsert($q);
        $RESP = $exec_response["RESP"]; 
        $RECORD_ID = $exec_response["RECORD_ID"];
        if ($RESP=="EXECUTED") {

          # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
          $ENTITY_ID = $_SESSION['CST_USR_ID'];
          $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
          ExecuteEntityUpdate($qww);


          # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
          $AUDIT_DATE = GetCurrentDateTime();
          $ENTITY_TYPE = "CUSTOMER_PHONE";
          $ENTITY_ID_AFFECTED = $_SESSION['CST_USR_ID'];
          $EVENT = "CHANGE_PHONE";
          $EVENT_OPERATION = "CHANGE_EMAIL_PHONE";
          $EVENT_RELATION = "cstmrs_info_chng_log";
          $EVENT_RELATION_NO = $RECORD_ID;
          $OTHER_DETAILS = "{Old Value: ".$OLD_VALUE."|New Value: ".$NEW_VALUE;
          $INVOKER_ID = $_SESSION['CST_USR_ID'];
          LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                         $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


          $alert_type = "INFO";
          $alert_msg = "MESSAGE: Change of phone application has been logged successfully. It will be actioned by management. Refreshing in 5 seconds.";
          $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
          header("Refresh:5; URL=prof-bio-data");
        }  # ... END..IFF
      }
    } #... END..IFF..ELSE
  } #... END..IFF..ELSE
}

# ... FM006: CHANGE PHOTO ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_chng_photo'])) {

  $CUST_ID = trim($_POST['CUST_ID']);
  $TRAN_TAN = trim($_POST['TRAN_TAN']);
  $CHANGE_TYPE = "PHOTO_CHANGE";
  $OLD_VALUE = "";
  $NEW_VALUE = "";
  $CHNG_INIT_DATE = GetCurrentDateTime();
  $CHNG_INIT_BY = $_SESSION["CST_USR_ID"];

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
      
    # ... Check if we have existing un-actioned change request
    $Q_CHK = "SELECT count(*) as RTN_VALUE FROM cstmrs_info_chng_log WHERE CUST_ID='$CUST_ID' AND CHANGE_TYPE='PHOTO_CHANGE' AND CHNG_STATUS='PENDING'";
    $C_CHK = ReturnOneEntryFromDB($Q_CHK);
    if ($C_CHK>0) {
      # ... Send System Response
      $alert_type = "WARNING";
      $alert_msg = "You have an un-approved change. Request management to action it.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else {

      # ... Upload of Photo.
      $PHOTO_CHANGE_BASEPATH = GetSystemParameter("PHOTO_CHANGE_BASEPATH")."/".$_SESSION['ORG_CODE'];
      if (!is_dir($PHOTO_CHANGE_BASEPATH)) {
        mkdir($PHOTO_CHANGE_BASEPATH);
      }

      $dir = $PHOTO_CHANGE_BASEPATH;
      $file_size = $_FILES['PHOTO']['size'];
      $file_type = $_FILES['PHOTO']['type'];
      $file_ext = strtolower(substr(strrchr($_FILES['PHOTO']['name'],"."),1));
      $file_name = "CUST_PHOTO_".$CUST_ID.".".$file_ext;

      if(is_uploaded_file($_FILES['PHOTO']['tmp_name'])){
        if($file_size >= 700000){ // file size (700KB)
        $alert_type = "ERROR";
        $alert_msg = "ERROR: Files exceeds 700KB. Upload file of a smaller size";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
        }else{
          if(($_FILES['PHOTO']['type']=="image/gif") // gif
          ||($_FILES['PHOTO']['type']=="image/jpeg") // jpeg
          ||($_FILES['PHOTO']['type']=="image/png") // png
           ){
            $result = move_uploaded_file($_FILES['PHOTO']['tmp_name'], $dir."/".$file_name);
            if($result == 1){
              $NEW_VALUE = $file_name;

              $q = "INSERT INTO cstmrs_info_chng_log(CUST_ID,CHANGE_TYPE,OLD_VALUE,NEW_VALUE,CHNG_INIT_DATE,CHNG_INIT_BY) VALUES('$CUST_ID','$CHANGE_TYPE','$OLD_VALUE','$NEW_VALUE','$CHNG_INIT_DATE','$CHNG_INIT_BY')";
              $exec_response = array();
              $exec_response = ExecuteEntityInsert($q);
              $RESP = $exec_response["RESP"]; 
              $RECORD_ID = $exec_response["RECORD_ID"];
              if ($RESP=="EXECUTED") {

                # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
                $ENTITY_ID = $_SESSION['CST_USR_ID'];
                $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
                ExecuteEntityUpdate($qww);


                # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
                $AUDIT_DATE = GetCurrentDateTime();
                $ENTITY_TYPE = "CUSTOMER_PHOTO";
                $ENTITY_ID_AFFECTED = $_SESSION['CST_USR_ID'];
                $EVENT = "CHANGE_PHOTO";
                $EVENT_OPERATION = "CHANGE_PHOTO";
                $EVENT_RELATION = "cstmrs_info_chng_log";
                $EVENT_RELATION_NO = $RECORD_ID;
                $OTHER_DETAILS = "{Old Value: ".$OLD_VALUE."|New Value: ".$NEW_VALUE;
                $INVOKER_ID = $_SESSION['CST_USR_ID'];
                LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                               $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


                $alert_type = "INFO";
                $alert_msg = "MESSAGE: Change of photo has been logged successfully. It will be actioned by management. Refreshing in 5 seconds.";
                $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
                header("Refresh:5; URL=prof-bio-data");
              }  # ... END..IFF


            }
          }else{
            $alert_type = "ERROR";
            $alert_msg = "ERROR: Unacceptable file format. Acceptable formats include '.png', '.jpg', '.gif' and .'pdf'";
            $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);    
          }
        }
      }


      
    }
  } #... END..IFF..ELSE
}

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("My Profile Details", $APP_SMALL_LOGO); 

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
                <h2>My Profile Details</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <div class="" role="tabpanel" data-example-id="togglable-tabs">
                  <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">My Profile Details</a>
                    </li>
                    <li role="presentation" class=""><a href="#tab_content3" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">My Photo</a>
                    </li>
                    <li role="presentation" class=""><a href="#tab_content4" role="tab" id="profile-tab3" data-toggle="tab" aria-expanded="false">Reset My Password</a>
                    </li>
                  </ul>
                  <div id="myTabContent" class="tab-content">
                    <!-- --- --- --- --- --- --- --- --- --- ------ --- -GENERAL DETAILS --- --- --- ------ --- --- --- --- --- --- --- -->
                    <!-- --- --- --- --- --- --- --- --- --- ------ --- -GENERAL DETAILS --- --- --- ------ --- --- --- --- --- --- --- -->
                    <!-- --- --- --- --- --- --- --- --- --- ------ --- -GENERAL DETAILS --- --- --- ------ --- --- --- --- --- --- --- -->
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                      <p>

                        <table class="table table-bordered" style="font-size: 12px;">
                          <tr><td width="20%"><b>Client Core Id</b></td><td colspan="3"><?php echo $CLIENT_CORE_ID_NUM; ?></td></tr>
                          <tr><td><b>E-enrollment Status</b></td><td colspan="3"><?php echo $E_STATUS; ?></td></tr>
                          <tr>
                              <td><b>E-enrollment Id</b></td>
                              <td width="20%"><?php echo $CUST_ID; ?></td>
                              <td width="16%"><b>E-enrollment Status</b></td>
                              <td><?php echo $CUST_STATUS; ?></td>
                          </tr>
                        </table>

                        <div class="col-md-6 col-sm-12 col-xs-12">
                          <div class="x_title">
                            <h2>SECTION A: Core Details</h2> 
                            <div class="clearfix"></div>
                          </div>
                          <div class="x_content"> 
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Core Id:</label>
                              <input type="text" class="form-control" disabled="" value="<?php echo $CLIENT_CORE_ID_NUM; ?>">
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Full Name:</label>
                              <input type="text" class="form-control" disabled="" value="<?php echo $CLIENT_DISP_NAME; ?>">
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Gender:</label>
                              <input type="text" class="form-control" disabled="" value="<?php echo $GENDER; ?>">
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>External ID:</label>
                              <input type="text" class="form-control" disabled="" value="<?php echo $CLIENT_EXTERN_ID; ?>">
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Office/Branch:</label>
                              <input type="text" class="form-control" disabled="" value="<?php echo $CLIENT_OFFICE; ?>">
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Staff/Account Manager:</label>
                              <input type="text" class="form-control" disabled="" value="<?php echo $CLIENT_STAFF_NAME; ?>">
                            </div>

                          </div>
                        </div>

                        <div class="col-md-6 col-sm-12 col-xs-12">
                          <div class="x_title">
                            <h2>SECTION B: E-enrollment Details</h2> 
                            <div class="clearfix"></div>
                          </div>
                          <div class="x_content">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Cust Id:</label>
                              <input type="text" class="form-control" disabled="" value="<?php echo $CUST_ID; ?>">
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Email:</label>
                              <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#verif_gd">Change Email</button>
                              <div id="verif_gd" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-mm">
                                  <div class="modal-content">

                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                      </button>
                                      <h4 class="modal-title" id="myModalLabel2">Change Email</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="verif_gd" method="post">
                                          <input type="hidden" id="CUST_ID" name="CUST_ID" value="<?php echo $CUST_ID; ?>">
                                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <label>Enter Authorization TAN:</label> 
                                            <button type="submit" class="btn btn-warning btn-xs" name="btn_gen_tan_email">Generate Auth TAN</button>
                                            <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">
                                          </div>

                                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <label>Email:</label> 
                                            <input type="text" id="EMAIL" name="EMAIL" class="form-control" value="<?php echo $_SESSION['FP_EMAIL']; ?>">
                                          </div>

                                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <button type="submit" class="btn btn-primary btn-sm" name="btn_chng_email">Submit</button>
                                          </div>
                                        </form>
                                    </div>
                                   

                                  </div>
                                </div>
                              </div>
                              <input type="text" class="form-control" disabled="" value="<?php echo $_SESSION['FP_EMAIL']; ?>">
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Phone:</label>
                              <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#verif_phone">Change Phone</button>
                              <div id="verif_phone" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-mm">
                                  <div class="modal-content">

                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                      </button>
                                      <h4 class="modal-title" id="myModalLabel2">Change Phone</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="verif_phone" method="post">
                                          <input type="hidden" id="CUST_ID" name="CUST_ID" value="<?php echo $CUST_ID; ?>">
                                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <label>Enter Authorization TAN:</label> 
                                            <button type="submit" class="btn btn-warning btn-xs" name="btn_gen_tan_phone">Generate Auth TAN</button>
                                            <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">
                                          </div>

                                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <label>Phone:</label> 
                                            <input type="text" id="PHONE" name="PHONE" class="form-control" value="<?php echo $_SESSION['FP_PHONE']; ?>">
                                          </div>

                                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <button type="submit" class="btn btn-primary btn-sm" name="btn_chng_phone">Submit</button>
                                          </div>
                                        </form>
                                    </div>
                                   

                                  </div>
                                </div>
                              </div>
                              <input type="text" class="form-control" disabled="" value="<?php echo $_SESSION['FP_PHONE']; ?>">
                            </div>

                             <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <label>Enrollment Status:</label>
                              <input type="text" class="form-control" disabled="" value="<?php echo $CUST_STATUS; ?>">
                            </div>
                            
                          </div>
                        </div>

                      </p>
                    </div>

                    <!-- --- --- --- --- --- --- --- --- --- ------ --- -IMAGE --- --- --- ------ --- --- --- --- --- --- --- -->
                    <!-- --- --- --- --- --- --- --- --- --- ------ --- -IMAGE --- --- --- ------ --- --- --- --- --- --- --- -->
                    <!-- --- --- --- --- --- --- --- --- --- ------ --- -IMAGE --- --- --- ------ --- --- --- --- --- --- --- -->
                    <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="profile-tab">
                      <p>
                        <div class="col-md-10 col-sm-12 col-xs-12 form-group">
                          <label>CLIENT IMAGE:</label>
                          <button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#verif_photo">Change Photo</button>
                              <div id="verif_photo" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-mm">
                                  <div class="modal-content">

                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                      </button>
                                      <h4 class="modal-title" id="myModalLabel2">Change Photo</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="verif_photo" method="post" enctype="multipart/form-data">
                                          <input type="hidden" id="CUST_ID" name="CUST_ID" value="<?php echo $CUST_ID; ?>">
                                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <label>Enter Authorization TAN:</label> 
                                            <button type="submit" class="btn btn-warning btn-xs" name="btn_gen_tan_photo">Generate Auth TAN</button>
                                            <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">
                                          </div>

                                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <label>Attach Photo:</label> 
                                            <input type="file" id="PHOTO" name="PHOTO" class="form-control">
                                          </div>

                                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <button type="submit" class="btn btn-primary btn-sm" name="btn_chng_photo">Submit</button>
                                          </div>
                                        </form>
                                    </div>
                                   

                                  </div>
                                </div>
                              </div>
                          <img width="100%" src="<?php echo $IMAGE_response_msg; ?>">
                        </div>
                      </p>
                    </div>

                    <!-- --- --- --- --- --- --- --- --- --- ------ --- -SECURITY --- --- --- ------ --- --- --- --- --- --- --- -->
                    <!-- --- --- --- --- --- --- --- --- --- ------ --- -SECURITY --- --- --- ------ --- --- --- --- --- --- --- -->
                    <!-- --- --- --- --- --- --- --- --- --- ------ --- -SECURITY --- --- --- ------ --- --- --- --- --- --- --- -->
                    <div role="tabpanel" class="tab-pane fade" id="tab_content4" aria-labelledby="profile-tab">
                      <p>
                        <form method="post">
                          <input type="hidden" id="CUST_ID" name="CUST_ID" value="<?php echo $CUST_ID; ?>"> 
                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                            <label>Enter Authorization TAN:</label> 
                            <button type="submit" class="btn btn-warning btn-xs" name="btn_gen_tan">Generate Auth TAN</button>
                            <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">
                          </div>


                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                            <button type="submit" class="btn btn-primary btn-sm" name="btn_reset_password">Reset Password</button>
                          </div>
                        </form>
                        
                        


                      </p>
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
