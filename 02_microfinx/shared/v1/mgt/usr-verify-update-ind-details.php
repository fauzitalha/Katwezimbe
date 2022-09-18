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
$cnt_q_info = $details[2];
$cnt_q_role = $details[3];
$cnt_q_faaa = $details[4];

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


# ... Approve User Change User Account ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_apprv_info_chng'])) {
  $chn_RECORD_ID = trim($_POST['chn_RECORD_ID']);
  $chn_USER_ID = trim($_POST['chn_USER_ID']);
  $chn_NEW_GENDER = trim($_POST['chn_NEW_GENDER']);
  $chn_NEW_PHONE = trim($_POST['chn_NEW_PHONE']);
  $CHNG_VERIF_DATE = GetCurrentDateTime();
  $CHNG_VERIF_BY = $_SESSION['UPR_USER_ID'];
  $CHNG_VERIF_RMKS = "APPROVED";
  $CHNG_STATUS = "APPROVED";

  // ... SQL
  $q1 = "UPDATE upr SET GENDER='$chn_NEW_GENDER', PHONE='$chn_NEW_PHONE' WHERE USER_ID='$chn_USER_ID'";
  $q2 = "UPDATE upr_info_chng_log SET CHNG_VERIF_DATE='$CHNG_VERIF_DATE', CHNG_VERIF_BY='$CHNG_VERIF_BY', CHNG_VERIF_RMKS='$CHNG_VERIF_RMKS', CHNG_STATUS='$CHNG_STATUS' WHERE USER_ID='$chn_USER_ID' AND RECORD_ID='$chn_RECORD_ID'";

  $update_response1 = ExecuteEntityUpdate($q1);
  $update_response2 = ExecuteEntityUpdate($q2);
  if ( ($update_response1=="EXECUTED") && ($update_response2=="EXECUTED")) {
    
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $chn_USER_ID;
    $EVENT = "MODIFICATION_APPROVAL";
    $EVENT_OPERATION = "APPROVE_USER_DETAILS";
    $EVENT_RELATION = "upr_info_chng_log|upr";
    $EVENT_RELATION_NO = $chn_RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "Approval Executed Successfully.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Approve User Change User Account ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_rej_info_chng'])) {
  $chn_RECORD_ID = trim($_POST['chn_RECORD_ID']);
  $chn_USER_ID = trim($_POST['chn_USER_ID']);
  $CHNG_VERIF_DATE = GetCurrentDateTime();
  $CHNG_VERIF_BY = $_SESSION['UPR_USER_ID'];
  $CHNG_VERIF_RMKS = trim($_POST['rej_reason']);;
  $CHNG_STATUS = "REJECTED";

  // ... SQL
  $q = "UPDATE upr_info_chng_log SET CHNG_VERIF_DATE='$CHNG_VERIF_DATE', CHNG_VERIF_BY='$CHNG_VERIF_BY', CHNG_VERIF_RMKS='$CHNG_VERIF_RMKS', CHNG_STATUS='$CHNG_STATUS' WHERE USER_ID='$chn_USER_ID' AND RECORD_ID='$chn_RECORD_ID'";

  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {
    
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $chn_USER_ID;
    $EVENT = "MODIFICATION_REJECTION";
    $EVENT_OPERATION = "REJECT_USER_DETAILS";
    $EVENT_RELATION = "upr_info_chng_log";
    $EVENT_RELATION_NO = $chn_RECORD_ID;
    $OTHER_DETAILS = $CHNG_VERIF_RMKS;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = "Information Change Rejected.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Approve Roles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_apprv_roles'])) {
  $USER_ID = trim($_POST['USER_ID']);

  $ROLE_ASSIGNMENT_INFO = "";
  $CNT_ROLES_ASSIGNED = 0;


  $user_roles_req = GetUserRolesRequested($USER_ID);
  for ($i=0; $i < sizeof($user_roles_req); $i++) { 
    
    # ... 01: Get the role details
    $role_id = $user_roles_req[$i];
    $ROLE_DETAILS = GetRoleDetailsIgnoreStatus($role_id);
    $ROLE_ID = $ROLE_DETAILS['ROLE_ID'];
    $ROLE_NAME = $ROLE_DETAILS['ROLE_NAME'];

    # ... 02: Checking If the checkbox was Ticked
    if (isset($_POST[$ROLE_ID])) {

      $ROLE_APPRVL_DATE = GetCurrentDateTime();
      $ROLE_APPRVD_BY = $_SESSION['UPR_USER_ID'];
      $ROLE_APPRVL_RMKS = "APPROVED";
      $USER_ROLE_STATUS = "ACTIVE";

      # ... SQL
      $q = "UPDATE upr_usr_roles SET ROLE_APPRVL_DATE='$ROLE_APPRVL_DATE', ROLE_APPRVD_BY='$ROLE_APPRVD_BY', ROLE_APPRVL_RMKS='$ROLE_APPRVL_RMKS', USER_ROLE_STATUS='$USER_ROLE_STATUS' WHERE ROLE_ID='$ROLE_ID' AND USER_ID='$USER_ID'";
      $update_response = ExecuteEntityUpdate($q);
      if ($update_response=="EXECUTED") {

        $CNT_ROLES_ASSIGNED++; 

        if ( $ROLE_ASSIGNMENT_INFO=="" ) {
          $ROLE_ASSIGNMENT_INFO = "{".$ROLE_ID."->".$ROLE_NAME."}";
        } else {
          $ROLE_ASSIGNMENT_INFO = $ROLE_ASSIGNMENT_INFO."|"."{".$ROLE_ID."->".$ROLE_NAME."}";
        }
      }

    }     
  }

  # ... Log System Audit Log
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "SYSTEM_USER";
  $ENTITY_ID_AFFECTED = $USER_ID;
  $EVENT = "GRANT_REQUEST_APPROVAL";
  $EVENT_OPERATION = "APPROVE_ROLES_FOR_USER";
  $EVENT_RELATION = "upr_usr_roles";
  $EVENT_RELATION_NO = "";
  $OTHER_DETAILS = $ROLE_ASSIGNMENT_INFO;
  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


  # ... Send System Response
  $alert_type = "SUCCESS";
  $alert_msg = $CNT_ROLES_ASSIGNED." role(s) approved to user account.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
}

# ... Approve Roles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_rej_roles'])) {
  $USER_ID = trim($_POST['USER_ID']);

  $ROLE_ASSIGNMENT_INFO = "";
  $CNT_ROLES_ASSIGNED = 0;


  $user_roles_req = GetUserRolesRequested($USER_ID);
  for ($i=0; $i < sizeof($user_roles_req); $i++) { 
    
    # ... 01: Get the role details
    $role_id = $user_roles_req[$i];
    $ROLE_DETAILS = GetRoleDetailsIgnoreStatus($role_id);
    $ROLE_ID = $ROLE_DETAILS['ROLE_ID'];
    $ROLE_NAME = $ROLE_DETAILS['ROLE_NAME'];

    # ... 02: Checking If the checkbox was Ticked
    if (isset($_POST[$ROLE_ID])) {

      $ROLE_APPRVL_DATE = GetCurrentDateTime();
      $ROLE_APPRVD_BY = $_SESSION['UPR_USER_ID'];
      $ROLE_APPRVL_RMKS = "REJECTED";
      $USER_ROLE_STATUS = "REJECTED";

      # ... SQL
      $q = "UPDATE upr_usr_roles SET ROLE_APPRVL_DATE='$ROLE_APPRVL_DATE', ROLE_APPRVD_BY='$ROLE_APPRVD_BY', ROLE_APPRVL_RMKS='$ROLE_APPRVL_RMKS', USER_ROLE_STATUS='$USER_ROLE_STATUS' WHERE ROLE_ID='$ROLE_ID' AND USER_ID='$USER_ID'";
      $update_response = ExecuteEntityUpdate($q);
      if ($update_response=="EXECUTED") {

        $CNT_ROLES_ASSIGNED++; 

        if ( $ROLE_ASSIGNMENT_INFO=="" ) {
          $ROLE_ASSIGNMENT_INFO = "{".$ROLE_ID."->".$ROLE_NAME."}";
        } else {
          $ROLE_ASSIGNMENT_INFO = $ROLE_ASSIGNMENT_INFO."|"."{".$ROLE_ID."->".$ROLE_NAME."}";
        }
      }

    }     
  }

  # ... Log System Audit Log
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "SYSTEM_USER";
  $ENTITY_ID_AFFECTED = $USER_ID;
  $EVENT = "GRANT_REQUEST_REJECTION";
  $EVENT_OPERATION = "REJECT_ROLES_FOR_USER";
  $EVENT_RELATION = "upr_usr_roles";
  $EVENT_RELATION_NO = "";
  $OTHER_DETAILS = $ROLE_ASSIGNMENT_INFO;
  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


  # ... Send System Response
  $alert_type = "ERROR";
  $alert_msg = $CNT_ROLES_ASSIGNED." role(s) has been rejected.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
}

# ... Approve TFA device ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_approve_tfa_device'])) {

  $RECORD_ID = $_POST['RECORD_ID'];
  $USER_ID = $_POST['USER_ID'];
  $DEVICE_ID = $_POST['DEVICE_ID'];

  # ... SQL
  $APPROVED_ON = GetCurrentDateTime();
  $APPROVED_BY = $_SESSION['UPR_USER_ID'];
  $DEVICE_STATUS = "ACTIVE";
  $q = "UPDATE tfa_devices SET APPROVED_ON='$APPROVED_ON' , APPROVED_BY='$APPROVED_BY', DEVICE_STATUS='$DEVICE_STATUS' WHERE ENTITY_ID='$USER_ID' AND DEVICE_ID='$DEVICE_ID'";

  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $USER_ID;
    $EVENT = "2FA";
    $EVENT_OPERATION = "APPROVE_NEW_2FA_DEVICE";
    $EVENT_RELATION = "tfa_devices";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DEVICE_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "2FA device has been approved successfully.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Approve TFA device ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_rej_tfa_device'])) {

  $RECORD_ID = $_POST['RECORD_ID'];
  $USER_ID = $_POST['USER_ID'];
  $DEVICE_ID = $_POST['DEVICE_ID'];

  # ... SQL
  $APPROVED_ON = GetCurrentDateTime();
  $APPROVED_BY = $_SESSION['UPR_USER_ID'];
  $DEVICE_STATUS = "REJECTED";
  $q = "UPDATE tfa_devices SET APPROVED_ON='$APPROVED_ON' , APPROVED_BY='$APPROVED_BY', DEVICE_STATUS='$DEVICE_STATUS' WHERE ENTITY_ID='$USER_ID' AND DEVICE_ID='$DEVICE_ID'";

  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

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
    $EVENT_OPERATION = "REJECT_NEW_2FA_DEVICE";
    $EVENT_RELATION = "tfa_devices";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DEL_ROW;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = "Device Has been Rejected";
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
    LoadDefaultCSSConfigurations("Verify Changes", $APP_SMALL_LOGO); 

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
                  <a href="usr-verify-update" class="btn btn-dark btn-sm">Back</a>
                </div>
                <h2>Verify Account Changes <small> <?php echo $USER_ID." | ".strtoupper($full_name); ?></small></h2> 
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
              
                <form method="post">
                  <input type="hidden" id="USER_ID" name="USER_ID" value="<?php echo $USER_ID; ?>">
                    

                  <div class="col-xs-2">
                    <!-- required for floating -->
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs tabs-left">
                      <li class="active"><a href="#home" data-toggle="tab" aria-expanded="true">General Details</a></li>
                      <li class=""><a href="#profile" data-toggle="tab" aria-expanded="false">User Roles</a></li>
                      <li class=""><a href="#messages" data-toggle="tab" aria-expanded="false">Security Settings</a></li>
                    </ul>
                  </div>

                  <div class="col-xs-10">
                    <!-- Tab panes -->
                    <div class="tab-content">
                      <?php
                      if ($cnt_q_info>0) {
                        # ... Fetch Information Change
                        $user_info_chng = array();
                        $chn_RECORD_ID = "";
                        $chn_USER_ID = "";
                        $chn_OLD_GENDER = "";
                        $chn_OLD_PHONE = "";
                        $chn_NEW_GENDER = "";
                        $chn_NEW_PHONE = "";
                        $user_info_chng = GetUserInfoChange($USER_ID);
                        if (sizeof($user_info_chng)>0) {
                          $chn_RECORD_ID = $user_info_chng['RECORD_ID'];
                          $chn_USER_ID = $user_info_chng['USER_ID'];
                          $chn_OLD_GENDER = $user_info_chng['OLD_GENDER'];
                          $chn_OLD_PHONE = $user_info_chng['OLD_PHONE'];
                          $chn_NEW_GENDER = $user_info_chng['NEW_GENDER'];
                          $chn_NEW_PHONE = $user_info_chng['NEW_PHONE'];
                        }
                        
                        ?>

                        <input type="hidden" id="chn_RECORD_ID" name="chn_RECORD_ID" value="<?php echo $chn_RECORD_ID; ?>">
                        <input type="hidden" id="chn_USER_ID" name="chn_USER_ID" value="<?php echo $chn_USER_ID; ?>">
                        <input type="hidden" id="chn_NEW_GENDER" name="chn_NEW_GENDER" value="<?php echo $chn_NEW_GENDER; ?>">
                        <input type="hidden" id="chn_NEW_PHONE" name="chn_NEW_PHONE" value="<?php echo $chn_NEW_PHONE; ?>">
                        <div class="tab-pane active" id="home">
                          <div class="lead col-md-6" >
                            User General Details
                          </div>
                          <div class="pull-right">
                            <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#aa">Approve</button>
                            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#mm">Reject</button>
                            <div class="modal fade" id="aa" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                      </button>
                                      <h4 class="modal-title" id="myModalLabel">Approve Change</h4>
                                    </div>
                                    <div class="modal-body">
                                      <p>
                                        Do you want to approve data modification made on user account?<br />
                                      </p>
                                    </div>
                                    <div class="modal-footer">
                                      <table align="right">
                                        <tr>
                                          <td>                                              
                                              <button type="submit" class="btn btn-primary btn-sm" name="btn_apprv_info_chng">Yes</button>
                                          </td>
                                          <td>
                                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                          </td>
                                          
                                        </tr>
                                      </table>                                         
                                    </div>

                                </div>
                              </div>
                            </div>
                            <div class="modal fade" id="mm" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                      </button>
                                      <h4 class="modal-title" id="myModalLabel">Reject Change</h4>
                                    </div>
                                    <div class="modal-body">
                                      <p>

                                        <table width="100%">
                                          <tr valign="top"><th>Rejection Reason</th></tr>
                                          <tr valign="top">
                                            <td><textarea id="rej_reason"  name="rej_reason" required="" style="width: 100%;" rows="4"></textarea>
                                            </td></tr>
                                      </table>
                                      </p>
                                    </div>
                                    <div class="modal-footer">
                                      <table align="right">
                                        <tr>
                                          <td>                                              
                                              <button type="submit" class="btn btn-danger btn-sm" name="btn_rej_info_chng">Submit</button>
                                          </td>
                                          <td>
                                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
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

                            <table width="100%" class="table table-striped table-bordered">
                              <tr valign="top"><th colspan="3">Data Changes in User Information</th></tr>
                              <tr valign="top"><th></th><th>Old Data</th><th>New Data</th></tr>
                              <tr valign="top"><th>Gender</th><td><?php echo $chn_OLD_GENDER; ?></td><td><?php echo $chn_NEW_GENDER; ?></td></tr>
                              <tr valign="top"><th>Phone</th><td><?php echo $chn_OLD_PHONE; ?><td><?php echo $chn_NEW_PHONE; ?></td></tr>
                            </table>
                          </p>
                        </div>
                        <?php
                      } else {
                        ?>
                        <div class="tab-pane active" id="home">
                          <div class="lead col-md-12" >
                            User General Details
                          </div>
                          <div>
                            <br />
                            <br />
                            <br />
                            No Change Requested for the User Details section.
                          </div>
                        </div>
                        <?php
                      }

                      if ($cnt_q_role>0) {
                        ?>
                        <div class="tab-pane" id="profile">
                          <p class="lead" >
                            User Roles
                          </p>
                          <p>
                            <table width="100%" class="table table-bordered">
                              <tr valign="top" bgcolor="#EEE">
                                <td width="33%">Current Roles
                                </td>
                                <td width="33%">Requested Roles 
                                  <div class="pull-right">
                                    <button type="submit" class="btn btn-success btn-xs" name="btn_apprv_roles">Approve</button>
                                    <button type="submit" class="btn btn-danger btn-xs" name="btn_rej_roles">Reject</button>
                                  </div>
                                </td>
                                <td width="33%">Roles Not Assigned
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
                                      </tr>
                                      <?php
                                        
                                    }

                                    ?>
                                  </table>  
                                </td>
                                <td>
                                  <table class="table table-striped table-bordered">
                                    <?php 
                                    $user_roles_req = GetUserRolesRequested($USER_ID);
                                    for ($i=0; $i < sizeof($user_roles_req); $i++) { 
                                      
                                      # ... 01: Get the role details
                                      $role_id = $user_roles_req[$i];
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
                        </div>
                        <?php
                      } else {
                        ?>
                        <div class="tab-pane" id="profile">
                          <p class="lead" >
                            User Roles
                          </p>
                          <p> 
                            <br />
                            <br />
                            No Change Requested for Roles section. 
                          </p>
                        </div>
                        <?php
                      }

                      if ($cnt_q_faaa>0) {
                        ?>
                        <div class="tab-pane" id="messages">
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

                            <?php
                            if ($TFA_FLG=="YES") {
                              ?>
                                  
                                <table width="100%" class="table table-bordered">
                                  <thead>
                                    <tr><th colspan="5" bgcolor="#EEE">List of User's 2FA Devices</th></tr>
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
                                      $DEVICE_TYPE_NAME = $tfa_device['DEVICE_TYPE_NAME'];
                                      $ENTITY_TYPE = $tfa_device['ENTITY_TYPE'];
                                      $ENTITY_ID = $tfa_device['ENTITY_ID'];
                                      $TEMP_ACCESS_PIN = $tfa_device['TEMP_ACCESS_PIN'];
                                      $ACCESS_PIN_RESET_FLG = $tfa_device['ACCESS_PIN_RESET_FLG'];
                                      $KEY_1 = $tfa_device['KEY_1'];
                                      $KEY_2 = $tfa_device['KEY_2'];
                                      $KEY_3 = $tfa_device['KEY_3'];
                                      $ADDED_ON = $tfa_device['ADDED_ON'];
                                      $ADDED_BY = $tfa_device['ADDED_BY'];
                                      $APPROVED_ON = $tfa_device['APPROVED_ON'];
                                      $APPROVED_BY = $tfa_device['APPROVED_BY'];
                                      $LAST_ACCESS_PIN_RESET_DATE = $tfa_device['LAST_ACCESS_PIN_RESET_DATE'];
                                      $LAST_ACCESS_PIN_RESET_DONEBY = $tfa_device['LAST_ACCESS_PIN_RESET_DONEBY'];
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
                                          if ($DEVICE_STATUS=="ACTIVE") {
                                            // ... display nothings
                                          }

                                          if ($DEVICE_STATUS=="PENDING") {
                                            ?>
                                            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="<?php echo $reset_ref; ?>">Approve Device</button>
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
                                                      <h4 class="modal-title" id="myModalLabel">Approve Device</h4>
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
                                                              <button type="submit" class="btn btn-primary btn-sm" name="btn_approve_tfa_device">Approve</button>
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

                                            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $disable_ref; ?>">Reject Device</button>
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
                                                      <h4 class="modal-title" id="myModalLabel">Reject Device</h4>
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
                                                              <button type="submit" class="btn btn-danger btn-sm" name="btn_rej_tfa_device">Reject</button>
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
                        <?php
                      } else {
                        ?>
                        <div class="tab-pane" id="messages">
                          <div class="lead col-md-12" >
                            Security Settings
                          </div>
                          <p>
                            No changes requested Security Settings Section.
                          </p>
                        </div>
                        <?php
                      }

                      ?>
                      
                      
                      
                    </div>
                  </div>

                  <div class="clearfix"></div>
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
