<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... 001: Receiving Data ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
$CUST_CORE_ID = mysql_real_escape_string($_GET['k']);
$CLIENT_CORE_ID = "";
$CLIENT_CORE_ID_NUM = "";
$CLIENT_EXTERN_ID = "";
$CLIENT_STATUS = "";
$ACTIVATION_dATE = "";
$CLIENT_DISP_NAME = "";
$GENDER = "";
$CLIENT_CLASFCN = "";
$CLIENT_OFFICE = "";
$CLIENT_STAFF_NAME = "";
$response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$CLIENT_CORE_ID = $CORE_RESP["id"];
$CLIENT_CORE_ID_NUM = $CORE_RESP["accountNo"];
$CLIENT_EXTERN_ID = (isset($CORE_RESP["externalId"])) ? $CORE_RESP["externalId"] : "";
$CLIENT_STATUS = $CORE_RESP["status"]["value"];
$ACTIVATION_dATE = $CORE_RESP["activationDate"][0] . "-" . ["activationDate"][0] . "-" . ["activationDate"][0];
$CLIENT_DISP_NAME = $CORE_RESP["displayName"];
$GENDER = (isset($CORE_RESP["gender"]["name"])) ? $CORE_RESP["gender"]["name"] : "";
$CLIENT_CLASFCN = (isset($CORE_RESP["clientClassification"]["name"])) ? $CORE_RESP["clientClassification"]["name"] : "";
$CLIENT_OFFICE = $CORE_RESP["officeName"];
$CLIENT_STAFF_NAME = (isset($CORE_RESP["staffName"])) ? $CORE_RESP["staffName"] : "";

# ... 001_001: CUSTOMER IMAGE... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .... ... ... ... ... ... ... ...#
$IMAGE_response_msg = "";
$IMAGE_CAPTURE_STATUS = "";
$IMAGE_response_msg = FetchClientImage($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
if (isset($IMAGE_response_msg["defaultUserMessage"])) {
  $IMAGE_CAPTURE_STATUS = "ACCOUNT IS IMAGELESS";
} else {
  $IMAGE_CAPTURE_STATUS = "ACCOUNT HAS IMAGE";
}



# ... 002: CHECK IF CLIENT IS ENROLLED ... ... ... ... ... ... . ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
$CUST_RECORD_ID = "";
$CUST_ID = "";
$APPLN_REF = "";
$APPLN_REF_MOB = "";
$ACTVN_TOKEN = "";
$ACTVN_TOKEN_MOB = "";
$CUST_EMAIL = "";
$CUST_PHONE = "";
$WEB_CHANNEL_LOGIN_ATTEMPTS = "";
$WEB_CHANNEL_ACCESS_FLG = "";
$WEB_CHANNEL_ACTVN_FLG = "";
$WEB_CHANNEL_ACTVN_DATE = "";
$MOB_WALLET = "";
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
if ($C_CHK > 0) {
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
  $APPLN_REF_MOB = $cstmr['APPLN_REF_MOB'];
  $ACTVN_TOKEN = $cstmr['ACTVN_TOKEN'];
  $ACTVN_TOKEN_MOB = $cstmr['ACTVN_TOKEN_MOB'];
  $CUST_EMAIL = $cstmr['CUST_EMAIL'];
  $CUST_PHONE = $cstmr['CUST_PHONE'];
  $WEB_CHANNEL_LOGIN_ATTEMPTS = $cstmr['WEB_CHANNEL_LOGIN_ATTEMPTS'];
  $WEB_CHANNEL_ACCESS_FLG = $cstmr['WEB_CHANNEL_ACCESS_FLG'];
  $WEB_CHANNEL_ACTVN_FLG = $cstmr['WEB_CHANNEL_ACTVN_FLG'];
  $WEB_CHANNEL_ACTVN_DATE = $cstmr['WEB_CHANNEL_ACTVN_DATE'];
  $MOB_WALLET = $cstmr['MOB_WALLET'];
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
$savings_acct_list = (isset($CORE_RESP["data"])) ? $CORE_RESP["data"] : array();


# ... 004: CUSTOMER LOANS ACCT LIST ... ... ... ... . ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
$response_msg = GetCustLoansAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$loans_acct_list = array();
$loans_acct_list = (isset($CORE_RESP["data"])) ? $CORE_RESP["data"] : array();


# ... 005: CUSTOMER SHARES ACCT LIST ... ... ... ... . ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
$response_msg = GetCustSharesAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$shrs_acct_list = array();
$shrs_acct_list = (isset($CORE_RESP["data"])) ? $CORE_RESP["data"] : array();


# ... FM001: BLOCK WEB ACCESS ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_block_web_access'])) {
  $CUST_ID = trim($_POST['CUST_ID']);
  $WEB_CHANNEL_ACCESS_FLG = "NN";

  // ... SQL
  $q = "UPDATE cstmrs SET WEB_CHANNEL_ACCESS_FLG='$WEB_CHANNEL_ACCESS_FLG' WHERE CUST_ID='$CUST_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response == "EXECUTED") {
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $CUST_ID;
    $EVENT = "BLOCK_WEB_ACCESS";
    $EVENT_OPERATION = "BLOCK_WEB_ACCESS";
    $EVENT_RELATION = "cstmrs";
    $EVENT_RELATION_NO = "";
    $OTHER_DETAILS = "";
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


    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "Web access for customer has been blocked";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}


# ... FM002: UNBLOCK WEB ACCESS ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_unblock_web_access'])) {
  $CUST_ID = trim($_POST['CUST_ID']);
  $WEB_CHANNEL_ACCESS_FLG = "YY";

  // ... SQL
  $q = "UPDATE cstmrs SET WEB_CHANNEL_ACCESS_FLG='$WEB_CHANNEL_ACCESS_FLG', WEB_CHANNEL_LOGIN_ATTEMPTS='' WHERE CUST_ID='$CUST_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response == "EXECUTED") {
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $CUST_ID;
    $EVENT = "UNBLOCK_WEB_ACCESS";
    $EVENT_OPERATION = "UNBLOCK_WEB_ACCESS";
    $EVENT_RELATION = "cstmrs";
    $EVENT_RELATION_NO = "";
    $OTHER_DETAILS = "";
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


    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "Web access for customer has been re-enabled";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}


# ... FM003: RESET PASSWORD ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_reset_password'])) {

  $CUST_ID = trim($_POST['CUST_ID']);
  $temp_pwd = GeneratePassKey(10);

  # ... DB INSERT
  $INIT_CHANNEL = "WEB";
  $MSG_TYPE = "TEMP_PSWD";
  $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
  $EMAIL_MESSAGE = "Dear " . $_SESSION['FP_NAME'] . "<br>"
    . "This is your temporary Password: " . $temp_pwd;
  $EMAIL_ATTACHMENT_PATH = "";
  $RECORD_DATE = GetCurrentDateTime();
  $EMAIL_STATUS = "NN";

  $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"];
  $RECORD_ID = $exec_response["RECORD_ID"];

  if ($RESP == "EXECUTED") {

    # ... Updating the Password
    $CUST_PWSD_STATUS = "RR";
    $CUST_PWSD = AES256::encrypt($temp_pwd);
    $CUST_PWSD_LST_CHNG_DATE = GetCurrentDateTime();

    $q2 = "UPDATE cstmrs SET CUST_PWSD_STATUS='$CUST_PWSD_STATUS', CUST_PWSD='$CUST_PWSD', CUST_PWSD_LST_CHNG_DATE='$CUST_PWSD_LST_CHNG_DATE'  WHERE CUST_ID='$CUST_ID'";
    $update_response = ExecuteEntityUpdate($q2);

    if ($update_response == "EXECUTED") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "CUSTOMER";
      $ENTITY_ID_AFFECTED = $CUST_ID;
      $EVENT = "RESET_CUST_PASSWORD";
      $EVENT_OPERATION = "RESET_CUST_PASSWORD_BY_STAFF";
      $EVENT_RELATION = "outbox_email";
      $EVENT_RELATION_NO = $CUST_ID;
      $OTHER_DETAILS = $EMAIL_MESSAGE;
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
      $alert_msg = "MESSAGE: A temporary password has been sent out to customer via email.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    }
  }
}


# ... FM004: BLOCK MOB ACCESS ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_block_mob_access'])) {
  $CUST_ID = trim($_POST['CUST_ID']);
  $MOB_CHANNEL_ACCESS_FLG = "NN";

  // ... SQL
  $q = "UPDATE cstmrs SET MOB_CHANNEL_ACCESS_FLG='$MOB_CHANNEL_ACCESS_FLG' WHERE CUST_ID='$CUST_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response == "EXECUTED") {
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $CUST_ID;
    $EVENT = "BLOCK_MOB_ACCESS";
    $EVENT_OPERATION = "BLOCK_MOB_ACCESS";
    $EVENT_RELATION = "cstmrs";
    $EVENT_RELATION_NO = "";
    $OTHER_DETAILS = "";
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


    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "Mobile access for customer has been blocked";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}


# ... FM005: UNBLOCK MOB ACCESS ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_unblock_mob_access'])) {
  $CUST_ID = trim($_POST['CUST_ID']);
  $MOB_CHANNEL_ACCESS_FLG = "YY";

  // ... SQL
  $q = "UPDATE cstmrs SET MOB_CHANNEL_ACCESS_FLG='$MOB_CHANNEL_ACCESS_FLG', MOB_CHANNEL_LOGIN_ATTEMPTS='' WHERE CUST_ID='$CUST_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response == "EXECUTED") {
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $CUST_ID;
    $EVENT = "UNBLOCK_MOB_ACCESS";
    $EVENT_OPERATION = "UNBLOCK_MOB_ACCESS";
    $EVENT_RELATION = "cstmrs";
    $EVENT_RELATION_NO = "";
    $OTHER_DETAILS = "";
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


    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "Mobile access for customer has been re-enabled";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}


# ... FM006: RESET MOB PIN ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
if (isset($_POST['btn_reset_pin'])) {

  $CUST_ID = trim($_POST['CUST_ID']);
  $temp_pwd = GenerateRandomAccessPin(5);

  # ... DB INSERT (EMAIL)
  $INIT_CHANNEL = "WEB";
  $MSG_TYPE = "TEMP_MOBILE_PIN";
  $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
  $EMAIL_MESSAGE = "Dear " . $_SESSION['FP_NAME'] . "<br>"
    . "This is your temporary mobile access pin: " . $temp_pwd . "<br>";
  $EMAIL_ATTACHMENT_PATH = "";
  $RECORD_DATE = GetCurrentDateTime();
  $EMAIL_STATUS = "NN";

  $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"];
  $RECORD_ID = $exec_response["RECORD_ID"];

  # ... DB INSERT (MOB SMS)
  $INIT_CHANNEL = "WEB";
  $CHRG_SOURCE = "ORG";
  $CHRG_CUST_ID = "";
  $CHRG_ACCT_ID = "";
  $MSG_TYPE = "URGENT";        // ... URGENT or NORMAL
  $RECIPIENT_NO = $_SESSION['FP_PHONE'];
  $SMS_MESSAGE = "Dear " . $_SESSION['FP_NAME'] . ", This is your temporary mobile access pin: --> " . $temp_pwd . " <--";
  $SMS_MESSAGE_LEN = strlen($SMS_MESSAGE);
  $RECORD_DATE = GetCurrentDateTime();

  $qqq = "INSERT INTO outbox_sms(INIT_CHANNEL, CHRG_SOURCE, CHRG_CUST_ID, CHRG_ACCT_ID, MSG_TYPE, RECIPIENT_NO, SMS_MESSAGE, SMS_MESSAGE_LEN, RECORD_DATE) VALUES('$INIT_CHANNEL', '$CHRG_SOURCE', '$CHRG_CUST_ID', '$CHRG_ACCT_ID', '$MSG_TYPE', '$RECIPIENT_NO', '$SMS_MESSAGE', '$SMS_MESSAGE_LEN', '$RECORD_DATE')";
  ExecuteEntityInsert($qqq);

  
  if ($RESP == "EXECUTED") {

    # ... Updating the Password
    $CUST_PIN_STATUS = "RR";
    $CUST_PIN = AES256::encrypt($temp_pwd);
    $CUST_PIN_LST_CHNG_DATE = GetCurrentDateTime();

    $q2 = "UPDATE cstmrs SET CUST_PIN_STATUS='$CUST_PIN_STATUS', CUST_PIN='$CUST_PIN', CUST_PIN_LST_CHNG_DATE='$CUST_PIN_LST_CHNG_DATE' WHERE CUST_ID='$CUST_ID'";
    $update_response = ExecuteEntityUpdate($q2);

    if ($update_response == "EXECUTED") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "CUSTOMER";
      $ENTITY_ID_AFFECTED = $CUST_ID;
      $EVENT = "RESET_CUST_PIN";
      $EVENT_OPERATION = "RESET_CUST_PIN_BY_STAFF";
      $EVENT_RELATION = "cstmrs";
      $EVENT_RELATION_NO = $CUST_ID;
      $OTHER_DETAILS = $EMAIL_MESSAGE;
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
      $alert_msg = "MESSAGE: A temporary pin has been sent out to customer via email and SMS message.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
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
  LoadDefaultCSSConfigurations("Client List", $APP_SMALL_LOGO);

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
          <div align="center" style="width: 100%;"><?php if (isset($_SESSION['ALERT_MSG'])) {
                                                      echo $_SESSION['ALERT_MSG'];
                                                    } ?></div>


          <div class="x_panel">
            <div class="x_title">
              <a href="cm-client-list" class="btn btn-dark btn-sm pull-left">Back</a>
              <h4><small><strong>CLIENT:</strong> <?php echo strtoupper($CLIENT_DISP_NAME); ?> | <?php echo "<strong>E-CLIENT-ID:</strong> " . $CUST_ID . " | <strong>CORE-ID:</strong> " . $CLIENT_CORE_ID_NUM; ?></small></h4>
              <div class="clearfix"></div>
            </div>

            <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->
            <div class="x_content">
              <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                  <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">General Details</a>
                  </li>
                  <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Client Accounts</a>
                  </li>
                  <li role="presentation" class=""><a href="#tab_content3" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">Client Image</a>
                  </li>
                  <li role="presentation" class=""><a href="#tab_content4" role="tab" id="profile-tab3" data-toggle="tab" aria-expanded="false">Security</a>
                  </li>
                </ul>
                <div id="myTabContent" class="tab-content">
                  <!-- --- --- --- --- --- --- --- --- --- ------ --- -GENERAL DETAILS --- --- --- ------ --- --- --- --- --- --- --- -->
                  <!-- --- --- --- --- --- --- --- --- --- ------ --- -GENERAL DETAILS --- --- --- ------ --- --- --- --- --- --- --- -->
                  <!-- --- --- --- --- --- --- --- --- --- ------ --- -GENERAL DETAILS --- --- --- ------ --- --- --- --- --- --- --- -->
                  <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                    <p>
                      <?php
                      $mob_enroll_status = ($APPLN_REF_MOB == "") ? "NOT_ENROLLED" : "ENROLLED";
                      ?>
                      <table class="table table-bordered" style="font-size: 12px;">
                        <tr>
                          <td width="20%"><b>Client Core Id</b></td>
                          <td colspan="3"><?php echo $CLIENT_CORE_ID_NUM; ?></td>
                        </tr>
                        <tr>
                          <td><b>E-enrollment Status</b></td>
                          <td colspan="3">
                            <?php echo $E_STATUS; ?>
                            <?php
                            if ($E_STATUS != "ENROLLED") {
                            ?>
                              <a href="cm-client-enroll?k=<?php echo $CUST_CORE_ID; ?>" class="btn btn-primary btn-xs pull-right">Enroll on e-Platform</button>
                              <?php
                            }
                              ?>
                          </td>
                        </tr>
                        <tr>
                          <td><b>Mobile-enrollment Status</b></td>
                          <td colspan="3">
                            <?php echo $mob_enroll_status; ?>
                          </td>
                        </tr>
                        <tr>
                          <td><b>E-enrollment Id</b></td>
                          <td><?php echo $CUST_ID; ?></td>
                        </tr>
                        <tr>
                          <td><b>Customer e-Status</b></td>
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
                            <input type="text" class="form-control" disabled="" value="<?php echo $_SESSION['FP_EMAIL']; ?>">
                          </div>

                          <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                            <label>Phone:</label>
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

                  <!-- --- --- --- --- --- --- --- --- --- ------ --- -ACCOUNT DETAILS --- --- --- ------ --- --- --- --- --- --- --- -->
                  <!-- --- --- --- --- --- --- --- --- --- ------ --- -ACCOUNT DETAILS --- --- --- ------ --- --- --- --- --- --- --- -->
                  <!-- --- --- --- --- --- --- --- --- --- ------ --- -ACCOUNT DETAILS --- --- --- ------ --- --- --- --- --- --- --- -->
                  <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                    <p>
                      <table class="table table-hover table-bordered table-striped">
                        <thead>
                          <tr valign="top" bgcolor="#EEE">
                            <th colspan="5">
                              Client Savings Accounts
                            </th>
                          </tr>
                          <tr valign="top">
                            <th width="4%">#</th>
                            <th width="20%">Account No</th>
                            <th width="15%">Currency</th>
                            <th>Product</th>
                            <th width="13%">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $response_msg = GetCustSavingsAccountsAll($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                          $CONN_FLG = $response_msg["CONN_FLG"];
                          $CORE_RESP = $response_msg["CORE_RESP"];
                          $ACCTS_DATA = array();
                          $ACCTS_DATA = $CORE_RESP["data"];


                          for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                            $row = $ACCTS_DATA[$i]["row"];
                            $svgs_id = $row[0];
                            $svgs_account_no = $row[1];
                            $svgs_crncy_code = $row[2];
                            $client_id = $row[3];
                            $svgs_product_id = $row[4];
                            $svgs_product_name = $row[5];
                            $svgs_product_shortname = $row[6];
                            $group_id =  $row[7];
                            $status =  $row[8];
                            $gen_status = ($status == "300") ? "ACTIVE" : "NOT_ACTIVE";

                          ?>
                            <tr valign="top">
                              <td><?php echo ($i + 1); ?></td>
                              <td><?php echo $svgs_account_no; ?></td>
                              <td><?php echo $svgs_crncy_code; ?></td>
                              <td><?php echo $svgs_product_name . " (" . $svgs_product_shortname . ")"; ?></td>
                              <td><?php echo $gen_status; ?></td>
                            </tr>
                          <?php

                          }


                          ?>
                        </tbody>
                      </table>

                      <table class="table table-hover table-bordered">
                        <thead>
                          <tr valign="top" bgcolor="#EEE">
                            <th colspan="5">Client Loans Accounts
                            </th>
                          </tr>
                          <tr valign="top">
                            <th width="4%">#</th>
                            <th width="20%">Account No</th>
                            <th width="15%">Currency</th>
                            <th>Product</th>
                            <th width="13%">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $response_msg = GetCustLoansAccountsAll($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                          $CONN_FLG = $response_msg["CONN_FLG"];
                          $CORE_RESP = $response_msg["CORE_RESP"];
                          $ACCTS_DATA = array();
                          $ACCTS_DATA = $CORE_RESP["data"];

                          for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                            $row = $ACCTS_DATA[$i]["row"];
                            $svgs_id = $row[0];
                            $svgs_account_no = $row[1];
                            $svgs_crncy_code = $row[2];
                            $client_id = $row[3];
                            $svgs_product_id = $row[4];
                            $svgs_product_name = $row[5];
                            $svgs_product_shortname = $row[6];
                            $group_id =  $row[7];
                            $status =  $row[8];
                            $status =  $row[8];
                            $gen_status = ($status == "300") ? "ACTIVE" : "NOT_ACTIVE";

                          ?>
                            <tr valign="top">
                              <td><?php echo ($i + 1); ?></td>
                              <td><?php echo $svgs_account_no; ?></td>
                              <td><?php echo $svgs_crncy_code; ?></td>
                              <td><?php echo $svgs_product_name . " (" . $svgs_product_shortname . ")"; ?></td>
                              <td><?php echo $gen_status; ?></td>
                            </tr>
                          <?php

                          }


                          ?>
                        </tbody>
                      </table>

                      <table class="table table-hover table-bordered">
                        <thead>
                          <tr valign="top" bgcolor="#EEE">
                            <th colspan="5">
                              Client Shares Accounts
                            </th>
                          </tr>
                          <tr valign="top">
                            <th width="4%">#</th>
                            <th width="20%">Account No</th>
                            <th width="15%">Currency</th>
                            <th>Product</th>
                            <th width="13%">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $response_msg = GetCustSharesAccountsAll($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                          $CONN_FLG = $response_msg["CONN_FLG"];
                          $CORE_RESP = $response_msg["CORE_RESP"];
                          $ACCTS_DATA = array();
                          $ACCTS_DATA = $CORE_RESP["data"];

                          for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                            $row = $ACCTS_DATA[$i]["row"];
                            $svgs_id = $row[0];
                            $svgs_account_no = $row[1];
                            $svgs_crncy_code = $row[2];
                            $client_id = $row[3];
                            $svgs_product_id = $row[4];
                            $svgs_product_name = $row[5];
                            $svgs_product_shortname = $row[6];
                            $status =  $row[7];
                            $gen_status = ($status == "300") ? "ACTIVE" : "NOT_ACTIVE";



                          ?>
                            <tr valign="top">
                              <td><?php echo ($i + 1); ?></td>
                              <td><?php echo $svgs_account_no; ?></td>
                              <td><?php echo $svgs_crncy_code; ?></td>
                              <td><?php echo $svgs_product_name . " (" . $svgs_product_shortname . ")"; ?></td>
                              <td><?php echo $gen_status; ?></td>
                            </tr>
                          <?php

                          }


                          ?>
                        </tbody>
                      </table>

                    </p>
                  </div>

                  <!-- --- --- --- --- --- --- --- --- --- ------ --- -IMAGE --- --- --- ------ --- --- --- --- --- --- --- -->
                  <!-- --- --- --- --- --- --- --- --- --- ------ --- -IMAGE --- --- --- ------ --- --- --- --- --- --- --- -->
                  <!-- --- --- --- --- --- --- --- --- --- ------ --- -IMAGE --- --- --- ------ --- --- --- --- --- --- --- -->
                  <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="profile-tab">
                    <p>
                      <div class="col-md-10 col-sm-12 col-xs-12 form-group">
                        <label>CLIENT IMAGE:</label>
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
                        <div class="col-md-4 col-sm-6 col-xs-12 form-group">
                          <label>e-ACCOUNT STATUS:</label>
                          <input type="text" class="form-control" disabled="" value="<?php echo $CUST_STATUS; ?>">
                        </div>


                        <div class="col-md-12 col-sm-12 col-xs-12 form-group"></div>
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group"></div>


                        <div class="col-md-4 col-sm-6 col-xs-12 form-group">
                          <label>WEB ACCESS FLAG:</label>
                          <input type="text" class="form-control" disabled="" value="<?php echo $WEB_CHANNEL_ACCESS_FLG; ?>">
                        </div>
                        <?php
                        if ($CUST_STATUS == "ACTIVE") {

                          if ($WEB_CHANNEL_ACCESS_FLG == "YY") {
                          ?>
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <button type="submit" class="btn btn-danger btn-xs" name="btn_block_web_access">Block Web Access</button>
                              <button type="submit" class="btn btn-primary btn-xs" name="btn_reset_password">Reset Web Password</button>
                            </div>
                          <?php
                          } else if ($WEB_CHANNEL_ACCESS_FLG == "NN") {
                          ?>
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <button type="submit" class="btn btn-success btn-xs" name="btn_unblock_web_access">Unblock Web Access</button>
                            </div>
                          <?php
                          }
                        }  # ... EEND..IFF
                        ?>


                        <div class="col-md-12 col-sm-12 col-xs-12 form-group"></div>
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group"></div>


                        <div class="col-md-4 col-sm-6 col-xs-12 form-group">
                          <label>MOBILE ACCESS FLAG:</label>
                          <input type="text" class="form-control" disabled="" value="<?php echo $MOB_CHANNEL_ACCESS_FLG; ?>">
                        </div>
                        <?php
                        if ($CUST_STATUS == "ACTIVE") {
                          if ($MOB_CHANNEL_ACCESS_FLG == "YY") {
                          ?>
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <button type="submit" class="btn btn-warning btn-xs" name="btn_block_mob_access">Block Mobile Access</button>
                              <button type="submit" class="btn btn-dark btn-xs" name="btn_reset_pin">Reset Mobile Pin</button>
                            </div>
                          <?php
                          } else if ($MOB_CHANNEL_ACCESS_FLG == "NN") {
                          ?>
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                              <button type="submit" class="btn btn-success btn-xs" name="btn_unblock_mob_access">Unblock Mobile Access</button>
                            </div>
                          <?php
                          }
                        }  # ... EEND..IFF
                        ?>






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