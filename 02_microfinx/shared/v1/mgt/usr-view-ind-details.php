<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$data_transfer = mysql_real_escape_string($_GET['k']);
$details = explode('_', $data_transfer);
$USER_ID = $details[0];
$USER_CORE_ID = $details[1];

# ... Get Application Details For the Application System
$USER_DETAILS = GetUserDetailsFromPortal($USER_ID);
$USR_RECORD_ID = $USER_DETAILS['RECORD_ID'];
$USR_GENDER = $USER_DETAILS['GENDER'];
$USR_PHONE = $USER_DETAILS['PHONE'];
$USR_EMAIL_ADDRESS = $USER_DETAILS['EMAIL_ADDRESS'];
$TFA_FLG = $USER_DETAILS['TFA_FLG'];
$USR_LOGGED_IN = $USER_DETAILS['LOGGED_IN'];
$USER_STATUS = $USER_DETAILS['USER_STATUS'];
$USR_USER_ROLE_DETAILS = $USER_DETAILS['USER_ROLE_DETAILS'];

# ... Getting Details From Core
$response_msg = array();
$response_msg = FetchUserDetailsFromCore($USER_CORE_ID, $MIFOS_CONN_DETAILS);
$sys_usr = $response_msg["CORE_RESP"];
$id = $sys_usr["id"];
$username = $sys_usr["username"];
$officeId = $sys_usr["officeId"];
$officeName = $sys_usr["officeName"];
$firstname = $sys_usr["firstname"];
$lastname = $sys_usr["lastname"];
$email = $sys_usr["email"];
$passwordNeverExpires = $sys_usr["passwordNeverExpires"];
$selectedRoles = $sys_usr["selectedRoles"];
$isSelfServiceUser = $sys_usr["isSelfServiceUser"];
$full_name = $firstname." ".$lastname;

# ... Get Roles Pending Approval
$q_pen = "SELECT COUNT(*) AS RTN_VALUE FROM upr_usr_roles WHERE USER_ID='$USER_ID' AND USER_ROLE_STATUS='PENDING'";
$CNT_ROLES_PENDING_APPRVL = ReturnOneEntryFromDB($q_pen);


# ... Disable User Account ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .
if (isset($_POST['btn_disable_usr'])) {
  $TUSER_ID = trim($_POST['TUSER_ID']);
  $USER_STATUS="DISABLED";

  // ... SQL
  $q = "UPDATE upr SET USER_STATUS='$USER_STATUS' WHERE USER_ID='$TUSER_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $TUSER_ID;
    $EVENT = "DEACTIVATION";
    $EVENT_OPERATION = "DEACTIVATE_USER_ACCOUNT";
    $EVENT_RELATION = "upr";
    $EVENT_RELATION_NO = "";
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "User Account has been disabled";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Re-activate User Account ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_activate_usr'])) {
  $TUSER_ID = trim($_POST['TUSER_ID']);
  $USER_STATUS="PENDING";

  // ... SQL
  $q = "UPDATE upr SET USER_STATUS='$USER_STATUS' WHERE USER_ID='$TUSER_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $TUSER_ID;
    $EVENT = "REACTIVATION";
    $EVENT_OPERATION = "REACTIVATE_USER_ACCOUNT";
    $EVENT_RELATION = "upr";
    $EVENT_RELATION_NO = "";
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "Re-activation request logged successfully. Seek Approval from Authorizer.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Disable User Account ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .
if (isset($_POST['btn_logout_usr'])) {
  $TUSER_ID = trim($_POST['TUSER_ID']);
  $LOGGED_IN="NO";

  // ... SQL
  $q = "UPDATE upr SET LOGGED_IN='$LOGGED_IN' WHERE USER_ID='$TUSER_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log out user in the system
    $LOG_TYPE = "LOGOUT - PUSHEDOUT";
    $LOG_DATE = GetCurrentDateTime();
    $LOG_DETAILS = "USER LOGGED OUT FROM SYSTEM BY ADMIN";
    $SRC_IP_ADDRESS = $_SERVER['REMOTE_ADDR'];  
    $is_log_recorded = LogUserAccessLog($TUSER_ID,$LOG_TYPE,$LOG_DATE,$LOG_DETAILS,$SRC_IP_ADDRESS); 


    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $TUSER_ID;
    $EVENT = "LOGOUT_USER";
    $EVENT_OPERATION = "LOGOUT_USER_RECORD_FROM_SYS";
    $EVENT_RELATION = "upr";
    $EVENT_RELATION_NO = "";
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "User Account logged out successfully.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Modify User Details ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
if (isset($_POST['btn_modify_user'])) {
  $USER_ID = trim($_POST['USER_ID']);
  $NEW_GENDER = trim($_POST['NEW_GENDER']);
  $NEW_PHONE = trim($_POST['NEW_PHONE']);
  $OLD_GENDER = trim($_POST['OLD_GENDER']);
  $OLD_PHONE = trim($_POST['OLD_PHONE']);
  $CHNG_INIT_DATE = GetCurrentDateTime();
  $CHNG_INIT_BY = $_SESSION['UPR_USER_ID'];



  # ... CHECK FOR PENDING MODIFICATION REQUEST
  $chk = "SELECT COUNT(*) AS RTN_VALUE FROM upr_info_chng_log WHERE USER_ID='$USER_ID' AND CHNG_STATUS='PENDING'";
  $cnt_chk = ReturnOneEntryFromDB($chk);

  if ($cnt_chk>0) {
    $alert_type = "WARNING";
    $alert_msg = "Un approved modification exists for this record. Request Authorizer to Reject or Approve Pending Modification";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else {

    # ... Check for Changes in Data SubMitted
    $OLD_DATA = $OLD_GENDER."#".$OLD_PHONE;
    $NEW_DATA = $NEW_GENDER."#".$NEW_PHONE;

    if ($OLD_DATA==$NEW_DATA) {
      $alert_type = "WARNING";
      $alert_msg = "No change Detected. Modification not Submitted";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else {
      # ... SQL
      $q = "INSERT INTO upr_info_chng_log(USER_ID,OLD_GENDER,OLD_PHONE,NEW_GENDER,NEW_PHONE,CHNG_INIT_DATE,CHNG_INIT_BY) VALUES('$USER_ID','$OLD_GENDER','$OLD_PHONE','$NEW_GENDER','$NEW_PHONE','$CHNG_INIT_DATE','$CHNG_INIT_BY')";

      $exec_response = array();
      $exec_response = ExecuteEntityInsert($q);
      $RESP = $exec_response["RESP"]; 
      $RECORD_ID = $exec_response["RECORD_ID"];

      if ($RESP=="EXECUTED") {

        # ... Log System Audit Log
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "SYSTEM_USER";
        $ENTITY_ID_AFFECTED = $USER_ID;
        $EVENT = "MODIFICATION";
        $EVENT_OPERATION = "MODIFY_USER_DETAILS";
        $EVENT_RELATION = "upr_info_chng_log";
        $EVENT_RELATION_NO = $RECORD_ID;
        $OTHER_DETAILS = "";
        $INVOKER_ID = $_SESSION['UPR_USER_ID'];
        LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                       $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


        $alert_type = "INFO";
        $alert_msg = "Modification initiated successfully. Seek approval From Authorizer.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

      }

    }
      
  }
}

# ... Assigning Roles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_assign_roles'])) {
  $USER_ID = $_POST['USER_ID'];

  $ROLE_ASSIGNMENT_INFO_IDS = "";
  $ROLE_ASSIGNMENT_INFO = "";
  $CNT_ROLES_ASSIGNED = 0;

  # ... Processing the Roles
  $ROLE_CAT_ID = "RC00001";
  $x = 0;
  $sys_roles_list = GetAllUserSystemRoles($ROLE_CAT_ID);
  for ($i=0; $i < sizeof($sys_roles_list); $i++) { 
    
    # ... 01: Getting the Data
    $sys_role = array();
    $sys_role = $sys_roles_list[$i];
    $ROLE_ID = $sys_role['ROLE_ID'];
    $ROLE_CAT_ID = $sys_role['ROLE_CAT_ID'];
    $ROLE_NAME = $sys_role['ROLE_NAME'];

    # ... 02: Checking If the checkbox was Ticked
    if (isset($_POST[$ROLE_ID])) {
      
      # ... Insert Query
      $ROLE_ASSGNMNT_DATE = GetCurrentDateTime();
      $ROLE_ASSGND_BY = $_SESSION['UPR_USER_ID'];
      $q = "INSERT INTO upr_usr_roles(USER_ID,ROLE_ID,ROLE_ASSGNMNT_DATE,ROLE_ASSGND_BY) VALUES('$USER_ID','$ROLE_ID','$ROLE_ASSGNMNT_DATE','$ROLE_ASSGND_BY')";
      $exec_response = array();
      $exec_response = ExecuteEntityInsert($q);
      $RESP = $exec_response["RESP"]; 
      $RECORD_ID = $exec_response["RECORD_ID"];

      if ( $RESP=="EXECUTED" ) { 
        $CNT_ROLES_ASSIGNED++; 

        if ( $ROLE_ASSIGNMENT_INFO=="" ) {
          $ROLE_ASSIGNMENT_INFO = "{".$ROLE_ID."->".$ROLE_NAME."}";
          $ROLE_ASSIGNMENT_INFO_IDS = $RECORD_ID;
        } else {
          $ROLE_ASSIGNMENT_INFO = $ROLE_ASSIGNMENT_INFO."|"."{".$ROLE_ID."->".$ROLE_NAME."}";
          $ROLE_ASSIGNMENT_INFO_IDS = $ROLE_ASSIGNMENT_INFO_IDS."|".$RECORD_ID;
        }

      }   
    }   
  }


  # ... Log System Audit Log
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "SYSTEM_USER";
  $ENTITY_ID_AFFECTED = $USER_ID;
  $EVENT = "GRANT_REQUEST";
  $EVENT_OPERATION = "ASSIGN_ROLE_TO_USER";
  $EVENT_RELATION = "upr_usr_roles";
  $EVENT_RELATION_NO = "MULIPLE";
  $OTHER_DETAILS = $ROLE_ASSIGNMENT_INFO_IDS."#".$ROLE_ASSIGNMENT_INFO;
  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


  # ... Send System Response
  $alert_type = "INFO";
  $alert_msg = $CNT_ROLES_ASSIGNED." role(s) assigned to user. Seek approval From Authorizer.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
}

# ... Remove Roles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_remove_roles'])) {
  $USER_ID = $_POST['USER_ID'];

  $ROLE_REM_INFO = "";
  $CNT_ROLES_REMED = 0;

  $user_roles = GetUserDefinedRoles($USER_ID);
  for ($i=0; $i < sizeof($user_roles); $i++) { 
    
    # ... 01: Get the role details
    $role_id = $user_roles[$i];
    $ROLE_DETAILS = GetRoleDetailsIgnoreStatus($role_id);
    $ROLE_ID = $ROLE_DETAILS['ROLE_ID'];
    $ROLE_NAME = $ROLE_DETAILS['ROLE_NAME'];

    # ... 02: Displaying the Data
    if (isset($_POST[$ROLE_ID])) {
      $USER_ROLE_STATUS = "REMOVED";

      $q = "UPDATE upr_usr_roles SET USER_ROLE_STATUS='$USER_ROLE_STATUS' WHERE USER_ID='$USER_ID' AND ROLE_ID='$ROLE_ID' AND USER_ROLE_STATUS='ACTIVE'";

      $update_response = ExecuteEntityUpdate($q);
      if ($update_response=="EXECUTED") {
        $CNT_ROLES_REMED++; 

        if ( $ROLE_REM_INFO=="" ) {
          $ROLE_REM_INFO = "{".$ROLE_ID."->".$ROLE_NAME."}";
        } else {
          $ROLE_REM_INFO = $ROLE_REM_INFO."|"."{".$ROLE_ID."->".$ROLE_NAME."}";
        }
      }


    }



  }

  # ... Log System Audit Log
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "SYSTEM_USER";
  $ENTITY_ID_AFFECTED = $USER_ID;
  $EVENT = "REVOKE_PREVILEDGE";
  $EVENT_OPERATION = "REMOVE_USER_ROLE";
  $EVENT_RELATION = "upr_usr_roles";
  $EVENT_RELATION_NO = "";
  $OTHER_DETAILS = $ROLE_REM_INFO;
  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


  # ... Send System Response
  $alert_type = "ERROR";
  $alert_msg = $CNT_ROLES_REMED." role(s) removed from user.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
}

# ... Switch Toggle 2FA ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_tfa_status'])) {
  $USER_ID = $_POST['USER_ID'];
  $TFA_FLG = $_POST['TFA_FLG'];

  $ALERT_DISP = ($TFA_FLG=="NO")? "ERROR":"SUCCESS";
  $ALERT_MSG_DISP = ($TFA_FLG=="NO")? "Two Factor Security Authentication has been disabled.":"Two Factor Security Authentication enabled successfully.";

  # ... SQL
  if ($TFA_FLG=="NO") {
    # ... Disable all TFA devices
    $q1 = "UPDATE tfa_devices SET DEVICE_STATUS='DISABLED' WHERE ENTITY_ID='$USER_ID'";
    $update_response1 = ExecuteEntityUpdate($q1);

    if ($update_response1=="EXECUTED") {

      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "SYSTEM_USER";
      $ENTITY_ID_AFFECTED = $USER_ID;
      $EVENT = "2FA";
      $EVENT_OPERATION = "DISABLE_ALL_2FA_DEVICES_FOR_USER";
      $EVENT_RELATION = "tfa_devices";
      $EVENT_RELATION_NO = "";
      $OTHER_DETAILS = $TFA_FLG;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    }
  }

  # ... SQL 2
  $q = "UPDATE upr SET TFA_FLG='$TFA_FLG' WHERE USER_ID='$USER_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $USER_ID;
    $EVENT = "2FA";
    $EVENT_OPERATION = "CHANGE_2FA_STATUS";
    $EVENT_RELATION = "upr";
    $EVENT_RELATION_NO = "";
    $OTHER_DETAILS = $TFA_FLG;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = $ALERT_DISP;
    $alert_msg = $ALERT_MSG_DISP;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Add 2FA device ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_add_tfa_device'])) {
  $USER_ID = $_POST['USER_ID'];
  $dev_type = $_POST['dev_type'];
  $dev_id = $_POST['dev_id'];

  # ... SQL
  $q = "SELECT COUNT(*) as RTN_VALUE FROM tfa_devices WHERE DEVICE_ID='$dev_id' AND ENTITY_ID='$USER_ID'";
  $cnt_devs = ReturnOneEntryFromDB($q);
  if ($cnt_devs>0) {

    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = "This device already exists for this user";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

  } else {

    # ... Insert SQL
    $DEVICE_ID = $dev_id;
    $DEVICE_TYPE_ID = $dev_type; 
    $ENTITY_TYPE = "SYS_USR";
    $ENTITY_ID = $USER_ID;
    $TTTT_ACCESS_PIN = GenerateRandomAccessPin(5);
    $TEMP_ACCESS_PIN = AES256::encrypt($TTTT_ACCESS_PIN);
    $ACCESS_PIN_RESET_FLG = "Y";
    $KEY_1 = AES256::encrypt(GenerateSecurityKey(14));
    $KEY_2 = AES256::encrypt(GenerateSecurityKey(18));
    $KEY_3 = AES256::encrypt(GenerateSecurityKey(22));
    $ADDED_ON = GetCurrentDateTime();
    $ADDED_BY = $_SESSION['UPR_USER_ID']; 

    $q2 = "INSERT INTO tfa_devices(DEVICE_ID,DEVICE_TYPE_ID,ENTITY_TYPE,ENTITY_ID,TEMP_ACCESS_PIN,ACCESS_PIN_RESET_FLG,KEY_1,KEY_2,KEY_3,ADDED_ON,
          ADDED_BY) VALUES('$DEVICE_ID','$DEVICE_TYPE_ID','$ENTITY_TYPE','$ENTITY_ID','$TEMP_ACCESS_PIN',
          '$ACCESS_PIN_RESET_FLG','$KEY_1','$KEY_2','$KEY_3','$ADDED_ON','$ADDED_BY')";

    $exec_response2 = ExecuteEntityInsert($q2);
    $RESP = $exec_response2["RESP"]; 
    $RECORD_ID = $exec_response2["RECORD_ID"];

    if ( $RESP=="EXECUTED" ) {

      // ... Send Temporary OTP via EMial to device owner
      $USER_CORECC_ID = GetUserCoreIdFromWebApp($ENTITY_ID);
      $response_msg = FetchUserDetailsFromCore($USER_CORECC_ID, $MIFOS_CONN_DETAILS);
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
      $MSG_TYPE = "DEVICE PIN (NEW OTP)";
      $RECIPIENT_EMAILS = $fff_email;
      $EMAIL_MESSAGE = "Dear ".$fff_name."<br>"
                      ."This is your temporary device access PIN: <b>".$TTTT_ACCESS_PIN."</b>";
      $EMAIL_ATTACHMENT_PATH = "";
      $RECORD_DATE = GetCurrentDateTime();
      $EMAIL_STATUS = "NN";

      $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
      ExecuteEntityInsert($q);



      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "SYSTEM_USER";
      $ENTITY_ID_AFFECTED = $ENTITY_ID;
      $EVENT = "2FA";
      $EVENT_OPERATION = "ADD_NEW_2FA_DEVICE";
      $EVENT_RELATION = "tfa_devices";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = "{".$TEMP_ACCESS_PIN."|".$KEY_1."|".$KEY_2."|".$KEY_3."}";
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      # ... Send System Response
      $alert_type = "INFO";
      $alert_msg = "2FA device has been added successfully. Seek approval from Authorizer. Tempory PIN has also been sent out to the intended User";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    }

  }    
}

# ... Reset Device PIN ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_rstpin_device'])) {
  $RECORD_ID = $_POST['RECORD_ID'];
  $USER_ID = $_POST['USER_ID'];
  $DEVICE_ID = $_POST['DEVICE_ID'];

  $TTTT_ACCESS_PIN = GenerateRandomAccessPin(5);
  $TEMP_ACCESS_PIN = AES256::encrypt($TTTT_ACCESS_PIN);
  $ACCESS_PIN_RESET_FLG = "Y";
  $KEY_1 = AES256::encrypt(GenerateSecurityKey(14));
  $KEY_2 = AES256::encrypt(GenerateSecurityKey(18));
  $KEY_3 = AES256::encrypt(GenerateSecurityKey(22));
  $LAST_ACCESS_PIN_RESET_DATE = GetCurrentDateTime();
  $LAST_ACCESS_PIN_RESET_DONEBY = $_SESSION['UPR_USER_ID']; 

  //DEVICE_ID 
  //TEMP_ACCESS_PIN 
  $CREATED_ON = GetCurrentDateTime();
  $CREATED_BY = $_SESSION['UPR_USER_ID'];   


  // ... Killing un-used Access Pins
  $RESET_OUTCOME_DATE = GetCurrentDateTime();                                    
  $q3 = "UPDATE tfa_access_pin_resets SET RESET_OUTCOME='KILLED', RESET_OUTCOME_DATE='$RESET_OUTCOME_DATE' WHERE DEVICE_ID='$DEVICE_ID' AND RESET_OUTCOME='PENDING'";
  $update_response3 = ExecuteEntityUpdate($q3);


  $q1 = "UPDATE tfa_devices SET TEMP_ACCESS_PIN='$TEMP_ACCESS_PIN', ACCESS_PIN_RESET_FLG='$ACCESS_PIN_RESET_FLG', KEY_1='$KEY_1' 
                               ,KEY_2='$KEY_2', KEY_3='$KEY_3', LAST_ACCESS_PIN_RESET_DATE='$LAST_ACCESS_PIN_RESET_DATE'
              ,LAST_ACCESS_PIN_RESET_DONEBY='$LAST_ACCESS_PIN_RESET_DONEBY' WHERE DEVICE_ID='$DEVICE_ID' AND ENTITY_ID='$USER_ID'";

  $q2 = "INSERT INTO tfa_access_pin_resets(DEVICE_ID, TEMP_ACCESS_PIN, CREATED_ON, CREATED_BY) 
                                    VALUES('$DEVICE_ID', '$TEMP_ACCESS_PIN', '$CREATED_ON', '$CREATED_BY')";

  $update_response1 = ExecuteEntityUpdate($q1);
  $exec_response2 = ExecuteEntityInsert($q2);
  $RESP = $exec_response2["RESP"]; 
  $RECORD_ID = $exec_response2["RECORD_ID"];
  

  if ( ($update_response1=="EXECUTED")&&($RESP=="EXECUTED") ) {

    // ... Send Temporary OTP via EMial to device owner
    $USER_CORECC_ID = GetUserCoreIdFromWebApp($USER_ID);
    $response_msg = FetchUserDetailsFromCore($USER_CORECC_ID, $MIFOS_CONN_DETAILS);
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
    $MSG_TYPE = "DEVICE PIN (OTP RESET)";
    $RECIPIENT_EMAILS = $fff_email;
    $EMAIL_MESSAGE = "Dear ".$fff_name."<br>"
                    ."This is your new temporary device access PIN: <b>".$TTTT_ACCESS_PIN."</b>";
    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

    $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
    ExecuteEntityInsert($q);

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $USER_ID;
    $EVENT = "2FA";
    $EVENT_OPERATION = "RESET_2FA_DEVICE_ACCESS_PIN";
    $EVENT_RELATION = "tfa_devices|tfa_access_pin_resets";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "{".$TEMP_ACCESS_PIN."|".$DEVICE_ID."|".$RECORD_ID."}";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    /*
    // ... SEND NOTIFICATIONS OUT
    $NTFCN_RECIPIENT_ID_LIST = array();
    $NTFCN_MESSAGE = "";
    $NTFCN_ATTCHMNT_PATH_LIST = array();
    LogSMSMessage($NTFCN_RECIPIENT_ID_LIST, $NTFCN_MESSAGE);
    LogSYSotification($NTFCN_RECIPIENT_ID_LIST, $NTFCN_MESSAGE, $NTFCN_ATTCHMNT_PATH_LIST)
    LogEmailMessage($NTFCN_RECIPIENT_ID_LIST, $NTFCN_MESSAGE, $NTFCN_ATTCHMNT_PATH_LIST);
    */

    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "Device Access Pin Has Been Reset successfully. An email has been sent out to the device owner.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Disable 2FA device ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_disable_tfa_device'])) {
  $RECORD_ID = $_POST['RECORD_ID'];
  $USER_ID = $_POST['USER_ID'];
  $DEVICE_ID = $_POST['DEVICE_ID'];

  # ... Disable all TFA devices
  $q = "UPDATE tfa_devices SET DEVICE_STATUS='DISABLED' WHERE ENTITY_ID='$USER_ID' AND DEVICE_ID='$DEVICE_ID'";
  $update_response1 = ExecuteEntityUpdate($q);

  if ($update_response1=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $USER_ID;
    $EVENT = "2FA";
    $EVENT_OPERATION = "DISABLE_2FA_DEVICE";
    $EVENT_RELATION = "tfa_devices";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DEVICE_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "WARNING";
    $alert_msg = "Device has been disabled.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

  }
}

# ... Enable TFA device ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_enable_tfa_device'])) {
  $RECORD_ID = $_POST['RECORD_ID'];
  $USER_ID = $_POST['USER_ID'];
  $DEVICE_ID = $_POST['DEVICE_ID'];

  # ... Disable all TFA devices
  $q = "UPDATE tfa_devices SET DEVICE_STATUS='PENDING' WHERE ENTITY_ID='$USER_ID' AND DEVICE_ID='$DEVICE_ID'";
  $update_response1 = ExecuteEntityUpdate($q);

  if ($update_response1=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $USER_ID;
    $EVENT = "2FA";
    $EVENT_OPERATION = "REACTIVATE_2FA_DEVICE";
    $EVENT_RELATION = "tfa_devices";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DEVICE_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "INFO";
    $alert_msg = "Device has been re-enabled. Seek Authorization from Authorizer.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Delete TFA device ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_delete_tfa_device'])) {
  $RECORD_ID = $_POST['RECORD_ID'];
  $USER_ID = $_POST['USER_ID'];
  $DEVICE_ID = $_POST['DEVICE_ID'];

  # ... Disable all TFA devices
  $q = "UPDATE tfa_devices SET DEVICE_STATUS='DELETED' WHERE ENTITY_ID='$USER_ID' AND DEVICE_ID='$DEVICE_ID'";
  $update_response1 = ExecuteEntityUpdate($q);

  if ($update_response1=="EXECUTED") {

    $TABLE = "tfa_devices";
    $TABLE_RECORD_ID = $RECORD_ID;
    $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
    $DEL_FLG = $delete_response["DEL_FLG"];
    $DEL_ROW = $delete_response["DEL_ROW"];

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $USER_ID;
    $EVENT = "2FA";
    $EVENT_OPERATION = "DELETE_2FA_DEVICE";
    $EVENT_RELATION = "tfa_devices";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DEL_ROW;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = "Device has been deleted from System.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("User Details", $APP_SMALL_LOGO); 

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
                <div class="col-md-1 col-sm-1 col-xs-12">
                  <a href="usr-view" class="btn btn-dark btn-sm">Back</a>
                </div>
                <h2>User Details <small> <?php echo $USER_ID." | ".strtoupper($full_name); ?></small></h2> 
                <div class="nav navbar-right panel_toolbox">
                  <form id="tttt" method="post">
                    <input type="hidden" id="TUSER_ID" name="TUSER_ID" value="<?php echo $USER_ID; ?>">
                    <?php
                    if ($USER_STATUS=="ACTIVE") {

                      if ($USER_ID==$_SESSION['UPR_USER_ID']) {
                        // ... display nothing
                      } else {

                        if ($USR_LOGGED_IN=="NO") {
                          // ... display nothing
                        } else {
                          ?>
                          <button type="submit" class="btn btn-default btn-sm" name="btn_logout_usr">Logout User Account</button>
                          <?php
                        }
                      }
                      ?>
                      <button type="submit" class="btn btn-danger btn-sm" name="btn_disable_usr">Disable User Account</button>
                      <?php
                    } else if ($USER_STATUS=="DISABLED") {
                      ?>
                      <button type="submit" class="btn btn-success btn-sm" name="btn_activate_usr">Activate User Account</button>
                      <?php
                    }

                    ?>
                  </form>
                  
                  
                </div>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                       
                  <div class="col-xs-2">
                    <!-- required for floating -->
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs tabs-left">
                      <li class="active"><a href="#home" data-toggle="tab" aria-expanded="true">General Details</a>
                      </li>
                      <li class=""><a href="#profile" data-toggle="tab" aria-expanded="false">User Roles</a>
                      </li>
                      <li class=""><a href="#messages" data-toggle="tab" aria-expanded="false">Security Settings</a>
                      </li>
                    </ul>
                  </div>

                  <div class="col-xs-10">
                    <!-- Tab panes -->
                    <div class="tab-content">

                      
                        <div class="tab-pane active" id="home">
                          <form method="post" id="www">
                              <input type="hidden" id="USER_ID" name="USER_ID" value="<?php echo $USER_ID; ?>">
                              <input type="hidden" id="OLD_GENDER" name="OLD_GENDER" value="<?php echo $USR_GENDER; ?>">
                              <input type="hidden" id="OLD_PHONE" name="OLD_PHONE" value="<?php echo $USR_PHONE; ?>">

                            <div class="lead col-md-6" >
                              User General Details
                            </div>
                            <div class="pull-right">
                              <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#mm">Modify</button>
                              <div class="modal fade" id="mm" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                        </button>
                                        <h4 class="modal-title" id="myModalLabel">Modify User</h4>
                                      </div>
                                      <div class="modal-body">
                                        <p>
                                          Do you want to add modify user account?<br />

                                          <table width="100%" class="table table-striped table-bordered">
                                            <tr valign="top"><th width="20%">User Id</th><th width="3%">:</th><td><?php echo $USER_ID; ?></td></tr>
                                            <tr valign="top"><th>User Core Id</th><th>:</th><td><?php echo $id; ?></td></tr>
                                            <tr valign="top"><th>UserName</th><th>:</th><td><?php echo $username; ?></td></tr>
                                            <tr valign="top"><th>Location</th><th>:</th><td><?php echo $officeName; ?></td></tr>
                                            <tr valign="top"><th>Full Name</th><th>:</th><td><?php echo $full_name; ?></td></tr>
                                            <tr valign="top"><th>Email</th><th>:</th><td><?php echo $email; ?></td></tr>
                                            <tr valign="top"><th>User Core Roles</th><th>:</th>
                                                <td><?php
                                                for ($f=0; $f < sizeof($selectedRoles); $f++) { 
                                                  $role = $selectedRoles[$f];
                                                  $role_name = $role["name"];
                                                  echo $role_name."<br />";
                                                }

                                                ?></td></tr>
                                            <tr valign="top"><th>Phone</th><th>:</th>
                                              <td><input type="number" id="NEW_PHONE" name="NEW_PHONE" required="" value="<?php echo $USR_PHONE ?>"></td></tr>
                                            <tr valign="top"><th>Gender</th><th>:</th><td>
                                              <?php
                                              if ($USR_GENDER=="MALE") {
                                                ?>
                                                <select id="NEW_GENDER" name="NEW_GENDER" required="">
                                                  <option value="">Select Gender</option>
                                                  <option value="MALE" selected="">Male</option>
                                                  <option value="FEMALE">Female</option>
                                                  <option value="OTHER">Other</option>
                                                </select>
                                                <?php
                                              } else if ($USR_GENDER=="FEMALE") {
                                                ?>
                                                <select id="NEW_GENDER" name="NEW_GENDER" required="">
                                                  <option value="">Select Gender</option>
                                                  <option value="MALE">Male</option>
                                                  <option value="FEMALE" selected="">Female</option>
                                                  <option value="OTHER">Other</option>
                                                </select>
                                                <?php
                                              } else if ($USR_GENDER=="OTHER") {
                                                ?>
                                                <select id="NEW_GENDER" name="NEW_GENDER" required="">
                                                  <option value="">Select Gender</option>
                                                  <option value="MALE">Male</option>
                                                  <option value="FEMALE">Female</option>
                                                  <option value="OTHER" selected="">Other</option>
                                                </select>
                                                <?php
                                              }

                                              ?>

                                              
                                            </td></tr>


                                          </table>
                                        </p>
                                      </div>
                                      <div class="modal-footer">
                                        <table align="right">
                                          <tr>
                                            <td>                                              
                                                <button type="submit" class="btn btn-success btn-sm" name="btn_modify_user">Save Changes</button>
                                            </td>
                                            <td>
                                              <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Discard</button>
                                            </td>
                                            
                                          </tr>
                                        </table>                                         
                                      </div>

                                  </div>
                                </div>
                              </div>
                            </div>
                            <p>
                              <table width="100%" class="table table-striped table-bordered">
                                <tr valign="top"><th width="20%">User Id</th><th width="3%">:</th><td><?php echo $USER_ID; ?></td></tr>
                                <tr valign="top"><th>User Core Id</th><th>:</th><td><?php echo $id; ?></td></tr>
                                <tr valign="top"><th>UserName</th><th>:</th><td><?php echo $username; ?></td></tr>
                                <tr valign="top"><th>Location</th><th>:</th><td><?php echo $officeName; ?></td></tr>
                                <tr valign="top"><th>Full Name</th><th>:</th><td><?php echo $full_name; ?></td></tr>
                                <tr valign="top"><th>Gender</th><th>:</th><td><?php echo $USR_GENDER; ?></td></tr>
                                <tr valign="top"><th>Email</th><th>:</th><td><?php echo $email; ?></td></tr>
                                <tr valign="top"><th>Phone</th><th>:</th><td><?php echo $USR_PHONE; ?></td></tr>
                                <tr valign="top"><th>User Core Roles</th><th>:</th>
                                                <td><?php
                                                for ($f=0; $f < sizeof($selectedRoles); $f++) { 
                                                  $role = $selectedRoles[$f];
                                                  $role_name = $role["name"];
                                                  echo $role_name."<br />";
                                                }

                                                ?></td></tr>
                              </table>
                            </p>
                          </form>
                        </div>

                      
                        <div class="tab-pane" id="profile">
                          <form method="post" id="xxxx">
                              <input type="hidden" id="USER_ID" name="USER_ID" value="<?php echo $USER_ID; ?>">
                              <input type="hidden" id="OLD_GENDER" name="OLD_GENDER" value="<?php echo $USR_GENDER; ?>">
                              <input type="hidden" id="OLD_PHONE" name="OLD_PHONE" value="<?php echo $USR_PHONE; ?>">
                            <p class="lead" >
                              User Roles
                            </p>
                            <p>
                              <table width="100%" class="table table-bordered">
                                <tr><td colspan="2"><strong>Roles Pending Approval: </strong><?php echo $CNT_ROLES_PENDING_APPRVL; ?></td></tr>
                                <tr valign="top" bgcolor="#EEE">
                                  <td width="50%">Roles Assigned
                                    <div class="pull-right">
                                      <button type="submit" class="btn btn-danger btn-xs" name="btn_remove_roles">Remove Role(s)</button>
                                    </div>
                                  </td>
                                  <td width="50%">List of Available System Roles
                                    <div class="pull-right">
                                      <button type="submit" class="btn btn-success btn-xs" name="btn_assign_roles">Assign Role(s)</button>
                                    </div>
                                  </td>
                                </tr>
                                <tr valign="top">
                                  <td>
                                    <table class="table table-striped table-bordered">
                                      <?php 
                                      $user_roles = GetUserDefinedRoles($USER_ID);
                                      for ($i=0; $i < sizeof($user_roles); $i++) { 
                                        
                                        # ... 01: Get the role details
                                        $role_id = $user_roles[$i];
                                        $ROLE_DETAILS = GetRoleDetailsIgnoreStatus($role_id);
                                        $ROLE_ID = $ROLE_DETAILS['ROLE_ID'];
                                        $ROLE_NAME = $ROLE_DETAILS['ROLE_NAME'];

                                        # ... 03: Displaying the Data
                                        ?>
                                        <tr valign="top">
                                          <td><?php echo ($i+1); ?>. </td>
                                          <td><?php echo $ROLE_NAME; ?></td>
                                          <td>
                                              
                                              <input type="checkbox" id="<?php echo $ROLE_ID; ?>" name="<?php echo $ROLE_ID; ?>" value="REMOVED">
                                          </td>
                                        </tr>
                                        <?php
                                          
                                      }

                                      ?>
                                    </table>  
                                  </td>
                                  <td>
                                    <table class="table table-striped table-bordered">
                                      <?php 
                                      $ROLE_CAT_ID = "RC00001";
                                      $x = 0;
                                      $sys_roles_list = GetAllUserSystemRoles($ROLE_CAT_ID);
                                      for ($i=0; $i < sizeof($sys_roles_list); $i++) { 
                                        
                                        # ... 01: Getting the Data
                                        $sys_role = array();
                                        $sys_role = $sys_roles_list[$i];
                                        $ROLE_ID = $sys_role['ROLE_ID'];
                                        $ROLE_CAT_ID = $sys_role['ROLE_CAT_ID'];
                                        $ROLE_NAME = $sys_role['ROLE_NAME'];

                                        $ROLE_CAT_DETAILS = GetRoleCategoryDetails($ROLE_CAT_ID);
                                        $ROLE_CAT_NAME = $ROLE_CAT_DETAILS['ROLE_CAT_NAME'];

                                        # ... Check If User Already Has this role
                                        $q = "SELECT COUNT(*) AS RTN_VALUE FROM upr_usr_roles WHERE USER_ID='$USER_ID' AND ROLE_ID='$ROLE_ID' AND USER_ROLE_STATUS in ('ACTIVE','PENDING')";
                                        $roles_def = ReturnOneEntryFromDB($q);

                                        # ... 03: Displaying the Data
                                        if ($roles_def>0) {
                                          // ... do nothing
                                        } else{
                                          ?>
                                          <tr valign="top">
                                            <td><?php echo ($x+1); ?>. </td>
                                            <td><?php echo $ROLE_NAME; ?></td>
                                            <td>
                                                
                                                <input type="checkbox" id="<?php echo $ROLE_ID; ?>" name="<?php echo $ROLE_ID; ?>" value="ASSIGNED">
                                            </td>
                                          </tr>
                                          <?php
                                          $x++;
                                        }
                                        
                                      }

                                      ?>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                            </p>
                          </form>
                        </div>

                      
                        <div class="tab-pane" id="messages">
                          <form method="post" id="yyyyyyy">
                              <input type="hidden" id="USER_ID" name="USER_ID" value="<?php echo $USER_ID; ?>">
                              <input type="hidden" id="OLD_GENDER" name="OLD_GENDER" value="<?php echo $USR_GENDER; ?>">
                              <input type="hidden" id="OLD_PHONE" name="OLD_PHONE" value="<?php echo $USR_PHONE; ?>">
                            <div class="lead col-md-6" >
                              Security Settings
                            </div>
                            <p>
                              <?php
                              $D_TFA = ($TFA_FLG=="YES")? "<span style='color: green;'>ENABLED</span>":"<span style='color: red;'>DISABLED</span>";
                              ?>
                              <table width="100%" class="table table-bordered">
                                <tr><th width="20%">2FA Current Status:</th><td><?php echo $D_TFA; ?></td></tr>
                              </table> 

                              <table width="100%" class="table table-bordered">
                                <tr><th colspan="4" bgcolor="#EEE">2FA Configurations</th></tr>
                                <tr valign="top">
                                  <th width="20%">2FA Status</th>
                                  <th width="3%">:</th>
                                  <td>
                                    <select class="form-control" name="TFA_FLG" id="TFA_FLG">
                                      <?php
                                      if ($TFA_FLG=="NO") {
                                        ?>
                                        <option value="NO" selected="">DISABLE</option>
                                        <option value="YES">ENABLE</option>
                                        <?php
                                      } else if ($TFA_FLG=="YES") {
                                        ?>
                                        <option value="NO">DISABLE</option>
                                        <option value="YES" selected="">ENABLE</option>
                                        <?php
                                      }
                                      ?>
                                    </select>
                                  </td>
                                  <td>
                                    <button type="submit" class="btn btn-primary btn-xs pull-right" name="btn_tfa_status">Save 2FA Status</button>
                                  </td>
                                </tr>
                              </table>
                          </form>

                              <?php
                              if ($TFA_FLG=="YES") {
                                ?>
                          
                            <table width="100%" class="table table-bordered">
                              <thead>
                                <tr><th colspan="5" bgcolor="#EEE">List of User's 2FA Devices
                                  <div class="pull-right">
                                    <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dev">Add Device</button>
                                    <div class="modal fade" id="dev" tabindex="-1" role="dialog" aria-hidden="true">
                                      <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                          <form method="post" id="yyyyyyy">
                                            <input type="hidden" id="USER_ID" name="USER_ID" value="<?php echo $USER_ID; ?>">

                                            <div class="modal-header">
                                              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                              </button>
                                              <h4 class="modal-title" id="myModalLabel">Add Device</h4>
                                            </div>
                                            <div class="modal-body">
                                              <p>
                                                  Device Type: <br /> 
                                                  <select class="form-control" name="dev_type" id="dev_type" required="">
                                                    <option value="">------------</option>
                                                    <?php
                                                    $dev_type_list = array();
                                                    $dev_type_list = FetchTFADeviceTypes();
                                                    for ($i=0; $i < sizeof($dev_type_list); $i++) { 

                                                      $dev_type = array();
                                                      $dev_type = $dev_type_list[$i];
                                                      $DEVICE_TYPE_ID = $dev_type['DEVICE_TYPE_ID'];
                                                      $DEVICE_TYPE_NAME = $dev_type['DEVICE_TYPE_NAME'];

                                                      ?>
                                                      <option value="<?php echo $DEVICE_TYPE_ID; ?>"><?php echo $DEVICE_TYPE_NAME; ?></option>
                                                      <?php
                                                    }
                                                    ?>
                                                    
                                                  </select><br /> 
                                                 
                                                  Device Id:<br /> 
                                                  <input type="text" name="dev_id" id="dev_id" class="form-control" required="">

                                              </p>
                                            </div>
                                            <div class="modal-footer">
                                              <table align="right">
                                                <tr>
                                                  <td>                                              
                                                      <button type="submit" class="btn btn-success btn-sm" name="btn_add_tfa_device">Add Device</button>
                                                  </td>
                                                  <td>
                                                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                                  </td>
                                                  
                                                </tr>
                                              </table>                                         
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </th></tr>
                                <tr bgcolor="#EEE"><th>#</th><th>Device Id</th><th>Device Type</th><th>Status</th><th>Action</th></tr>
                              </thead>
                              <tbody>
                                <?php
                                $tfa_device_list = array();
                                $tfa_device_list = Fetch2FADevicesForEntityId($USER_ID);
                                for ($i=0; $i < sizeof($tfa_device_list); $i++) { 

                                  $tfa_device = array();
                                  $tfa_device = $tfa_device_list[$i];
                                  $RECORD_ID = $tfa_device['RECORD_ID'];
                                  $DEVICE_ID = $tfa_device['DEVICE_ID'];
                                  $DEVICE_TYPE_ID = $tfa_device['DEVICE_TYPE_ID'];
                                  //$DEVICE_TYPE_NAME = $tfa_device['DEVICE_TYPE_NAME'];
                                  $ENTITY_TYPE = $tfa_device['ENTITY_TYPE'];
                                  $ENTITY_ID = $tfa_device['ENTITY_ID'];
                                  //$TEMP_ACCESS_PIN = $tfa_device['TEMP_ACCESS_PIN'];
                                  //$ACCESS_PIN_RESET_FLG = $tfa_device['ACCESS_PIN_RESET_FLG'];
                                  $KEY_1 = $tfa_device['KEY_1'];
                                  $KEY_2 = $tfa_device['KEY_2'];
                                  $KEY_3 = $tfa_device['KEY_3'];
                                  $ADDED_ON = $tfa_device['ADDED_ON'];
                                  $ADDED_BY = $tfa_device['ADDED_BY'];
                                  $APPROVED_ON = $tfa_device['APPROVED_ON'];
                                  $APPROVED_BY = $tfa_device['APPROVED_BY'];
                                  //$LAST_ACCESS_PIN_RESET_DATE = $tfa_device['LAST_ACCESS_PIN_RESET_DATE'];
                                  //$LAST_ACCESS_PIN_RESET_DONEBY = $tfa_device['LAST_ACCESS_PIN_RESET_DONEBY'];
                                  $DEVICE_STATUS = $tfa_device['DEVICE_STATUS'];

                                  # ... Display Content
                                  $reset_id = "reset".($i+1);
                                  $reset_ref = "#".$reset_id;
                                  $disable_id = "disable".($i+1);
                                  $disable_ref = "#".$disable_id;
                                  $activate_id = "activate".($i+1);
                                  $activate_ref = "#".$activate_id;
                                  $delete_id = "delete".($i+1);
                                  $delete_ref = "#".$delete_id;

                                  ?>
                                  <tr valign="top">
                                    <td><?php echo ($i+1); ?></td>
                                    <td><?php echo $DEVICE_ID; ?></td>
                                    <td><?php echo $DEVICE_TYPE_NAME; ?></td>
                                    <td><?php echo $DEVICE_STATUS; ?></td>
                                    <td>
                                      <?php
                                      if ($DEVICE_STATUS=="PENDING") {
                                        // ... display nothings
                                      }

                                      if (($DEVICE_STATUS=="ACTIVE")||($DEVICE_STATUS=="COMPLETE")) {
                                        ?>
                                        <button type="button" class="btn btn-dark btn-xs" data-toggle="modal" data-target="<?php echo $reset_ref; ?>">Reset Access Pin</button>
                                        <div class="modal fade" id="<?php echo $reset_id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                          <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                              <form method="post" id="<?php echo $reset_id; ?>">
                                                  <input type="hidden" id="RECORD_ID" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>"> 
                                                  <input type="hidden" id="USER_ID" name="USER_ID" value="<?php echo $USER_ID; ?>"> 
                                                  <input type="hidden" id="DEVICE_ID" name="DEVICE_ID" value="<?php echo $DEVICE_ID; ?>"> 
                                                <div class="modal-header">
                                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                  </button>
                                                  <h4 class="modal-title" id="myModalLabel">Reset Access Pin</h4>
                                                </div>
                                                <div class="modal-body">
                                                  <p>
                                                      <strong>Device Id:</strong><br /> 
                                                      <?php echo $DEVICE_ID; ?><br /> <br /> 

                                                      <strong>Device Type:</strong><br /> 
                                                      <?php echo $DEVICE_TYPE_NAME; ?><br /> 

                                                  </p>
                                                </div>
                                                <div class="modal-footer">
                                                  <table align="right">
                                                    <tr>
                                                      <td>                                              
                                                          <button type="submit" class="btn btn-primary btn-sm" name="btn_rstpin_device">Reset</button>
                                                      </td>
                                                      <td>
                                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                                      </td>
                                                      
                                                    </tr>
                                                  </table>                                         
                                                </div>
                                              </form>
                                            </div>
                                          </div>
                                        </div>

                                        <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="<?php echo $disable_ref; ?>">Disable</button>
                                        <div class="modal fade" id="<?php echo $disable_id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                          <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                              <form method="post" id="<?php echo $disable_id; ?>">
                                                  <input type="hidden" id="RECORD_ID" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>"> 
                                                  <input type="hidden" id="USER_ID" name="USER_ID" value="<?php echo $USER_ID; ?>"> 
                                                  <input type="hidden" id="DEVICE_ID" name="DEVICE_ID" value="<?php echo $DEVICE_ID; ?>"> 
                                                <div class="modal-header">
                                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                  </button>
                                                  <h4 class="modal-title" id="myModalLabel">Disable Device</h4>
                                                </div>
                                                <div class="modal-body">
                                                  <p>
                                                      <strong>Device Id:</strong><br /> 
                                                      <?php echo $DEVICE_ID; ?><br /> <br /> 

                                                      <strong>Device Type:</strong><br /> 
                                                      <?php echo $DEVICE_TYPE_NAME; ?><br /> 

                                                  </p>
                                                </div>
                                                <div class="modal-footer">
                                                  <table align="right">
                                                    <tr>
                                                      <td>                                              
                                                          <button type="submit" class="btn btn-danger btn-sm" name="btn_disable_tfa_device">Disable</button>
                                                      </td>
                                                      <td>
                                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                                      </td>
                                                      
                                                    </tr>
                                                  </table>                                         
                                                </div>
                                              </form>
                                                

                                            </div>
                                          </div>
                                        </div>
                                        <?php
                                      }


                                      if ($DEVICE_STATUS=="DISABLED") {
                                        ?>
                                        <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="<?php echo $activate_ref; ?>">Enable Device</button>
                                        <div class="modal fade" id="<?php echo $activate_id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                          <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                              <form method="post" id="<?php echo $activate_id; ?>">
                                                  <input type="hidden" id="RECORD_ID" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>"> 
                                                  <input type="hidden" id="USER_ID" name="USER_ID" value="<?php echo $USER_ID; ?>"> 
                                                  <input type="hidden" id="DEVICE_ID" name="DEVICE_ID" value="<?php echo $DEVICE_ID; ?>"> 
                                                <div class="modal-header">
                                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                  </button>
                                                  <h4 class="modal-title" id="myModalLabel">Enable Device</h4>
                                                </div>
                                                <div class="modal-body">
                                                  <p>
                                                      <strong>Device Id:</strong><br /> 
                                                      <?php echo $DEVICE_ID; ?><br /> <br /> 

                                                      <strong>Device Type:</strong><br /> 
                                                      <?php echo $DEVICE_TYPE_NAME; ?><br /> 

                                                  </p>
                                                </div>
                                                <div class="modal-footer">
                                                  <table align="right">
                                                    <tr>
                                                      <td>                                              
                                                          <button type="submit" class="btn btn-success btn-sm" name="btn_enable_tfa_device">Enable</button>
                                                      </td>
                                                      <td>
                                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                                      </td>
                                                      
                                                    </tr>
                                                  </table>                                         
                                                </div>
                                              </form>
                                                

                                            </div>
                                          </div>
                                        </div>

                                        <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $delete_ref; ?>">Delete Device</button>
                                        <div class="modal fade" id="<?php echo $delete_id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                          <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                              <form method="post" id="<?php echo $delete_id; ?>">
                                                  <input type="hidden" id="RECORD_ID" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>"> 
                                                  <input type="hidden" id="USER_ID" name="USER_ID" value="<?php echo $USER_ID; ?>"> 
                                                  <input type="hidden" id="DEVICE_ID" name="DEVICE_ID" value="<?php echo $DEVICE_ID; ?>"> 
                                                <div class="modal-header">
                                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                  </button>
                                                  <h4 class="modal-title" id="myModalLabel">Delete Device</h4>
                                                </div>
                                                <div class="modal-body">
                                                  <p>
                                                      <strong>Device Id:</strong><br /> 
                                                      <?php echo $DEVICE_ID; ?><br /> <br /> 

                                                      <strong>Device Type:</strong><br /> 
                                                      <?php echo $DEVICE_TYPE_NAME; ?><br /> 

                                                  </p>
                                                </div>
                                                <div class="modal-footer">
                                                  <table align="right">
                                                    <tr>
                                                      <td>                                              
                                                          <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_tfa_device">Delete</button>
                                                      </td>
                                                      <td>
                                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                                      </td>
                                                      
                                                    </tr>
                                                  </table>                                         
                                                </div>
                                              </form>
                                                

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
                          
                                <?php
                              }
                              ?>
                              
                            </p>
                          
                        </div>


                    </div>
                  </div>

                  <div class="clearfix"></div>
                

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
