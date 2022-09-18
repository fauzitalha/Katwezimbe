<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Approval
if (isset($_POST['btn_apprv_user'])) {
  $RECORD_ID = $_POST['RECORD_ID'];
  $U_USER_ID = $_POST['USER_ID'];
  $APPROVED_ON = GetCurrentDateTime();
  $APPROVED_BY = $_SESSION['UPR_USER_ID'];
  $APPROVAL_RMKS = "APPROVED";
  $USER_STATUS = "ACTIVE";


  # ... SQL Query
  $q = "UPDATE upr SET APPROVED_ON='$APPROVED_ON', APPROVED_BY='$APPROVED_BY', APPROVAL_RMKS='$APPROVAL_RMKS', USER_STATUS='$USER_STATUS' WHERE USER_ID='$U_USER_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $U_USER_ID;
    $EVENT = "VERIFICATION";
    $EVENT_OPERATION = "VERIFY_NEWLY_ADDED_USER";
    $EVENT_RELATION = "upr";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);



    $alert_type = "SUCCESS";
    $alert_msg = "User has been approved successfully";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }

}


# ... Rejection
if (isset($_POST['btn_reject_user'])) {
  $RECORD_ID = $_POST['RECORD_ID'];
  $U_USER_ID = $_POST['USER_ID'];
  $APPROVED_ON = GetCurrentDateTime();
  $APPROVED_BY = $_SESSION['UPR_USER_ID'];
  $APPROVAL_RMKS = mysql_real_escape_string(trim($_POST['rej_reason']));
  $USER_STATUS = "REJECTED";


  # ... SQL Query
  $q = "UPDATE upr SET APPROVED_ON='$APPROVED_ON', APPROVED_BY='$APPROVED_BY', APPROVAL_RMKS='$APPROVAL_RMKS', USER_STATUS='$USER_STATUS' WHERE USER_ID='$U_USER_ID'";

  $q2 = "DELETE FROM upr WHERE USER_ID='$U_USER_ID'";

  $update_response = ExecuteEntityUpdate($q);
  $TABLE = "upr";
  $TABLE_RECORD_ID = $RECORD_ID;
  $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
  $DEL_FLG = $delete_response["DEL_FLG"];
  $DEL_ROW = $delete_response["DEL_ROW"];

  if ( ($update_response=="EXECUTED")&&($DEL_FLG=="Y") ) {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $U_USER_ID;
    $EVENT = "REJECTION_AND_DELETION";
    $EVENT_OPERATION = "REJECT_NEWLY_ADDED_USER_THEN_DELETE";
    $EVENT_RELATION = "upr";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $DEL_ROW;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);



    $alert_type = "ERROR";
    $alert_msg = "User has been rejected";
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
    LoadDefaultCSSConfigurations("Verify Added User", $APP_SMALL_LOGO); 

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
                <h2>Verify Added User</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
               
                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">Approve Created Users</th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Core UserName</th>
                      <th>Core Full Name</th>
                      <th>Added By</th>
                      <th>Added On</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $usr_list = array();
                    $USER_STATUS = "PENDING";
                    $usr_list = FetchSysUserList($USER_STATUS);
                    for ($i=0; $i < sizeof($usr_list); $i++) { 
                      
                      $usr = array();
                      $usr = $usr_list[$i];
                      $RECORD_ID = $usr['RECORD_ID'];
                      $USER_ID = $usr['USER_ID'];
                      $USER_CORE_ID = $usr['USER_CORE_ID'];
                      $GENDER = $usr['GENDER'];
                      $PHONE = $usr['PHONE'];
                      $EMAIL_ADDRESS = $usr['EMAIL_ADDRESS'];
                      $LOGGED_IN = $usr['LOGGED_IN'];
                      $ADDED_ON = $usr['ADDED_ON'];
                      $ADDED_BY = $usr['ADDED_BY'];
                      $APPROVED_ON = $usr['APPROVED_ON'];
                      $APPROVED_BY = $usr['APPROVED_BY'];
                      $LAST_CHNGD_BY = $usr['LAST_CHNGD_BY'];
                      $LAST_CHNGD_ON = $usr['LAST_CHNGD_ON'];
                      $USER_STATUS = $usr['USER_STATUS'];

                      # ... 01 Get Core User Details
                      $response_msg = FetchUserDetailsFromCore($USER_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      //$RESP_FLG = $response_msg["RESP_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $sys_usr = $response_msg["CORE_RESP"];
                      $id = $sys_usr["id"];
                      $CORE_username = $sys_usr["username"];
                      $officeId = $sys_usr["officeId"];
                      $officeName = $sys_usr["officeName"];
                      $firstname = $sys_usr["firstname"];
                      $lastname = $sys_usr["lastname"];
                      $email = $sys_usr["email"];
                      $passwordNeverExpires = $sys_usr["passwordNeverExpires"];
                      $selectedRoles = $sys_usr["selectedRoles"];
                      $isSelfServiceUser = $sys_usr["isSelfServiceUser"];

                      $full_name = $firstname." ".$lastname;


                      # ... 02: Get Creator's Name
                      $ADDED_BY_CORE_ID = GetUserCoreIdFromWebApp($ADDED_BY);
                      $response_msg = FetchUserDetailsFromCore($ADDED_BY_CORE_ID, $MIFOS_CONN_DETAILS);
                      //$CONN_FLG = $response_msg["CONN_FLG"];
                      //$RESP_FLG = $response_msg["RESP_FLG"];
                      $ADDED_CORE_RESP = $response_msg["CORE_RESP"];
                      $ADDED_BY_NAME = $ADDED_CORE_RESP["username"]." (".$ADDED_CORE_RESP["firstname"]." ".$ADDED_CORE_RESP["lastname"].")";


                      # ... 03: Display Values
                      $modal_id = "modal_".($i+1);
                      $modal_ref = "#".$modal_id;
                      $modal_id_rej = "modal_rej_".($i+1);
                      $modal_ref_rej = "#".$modal_id_rej;

                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $CORE_username; ?></td>
                        <td><?php echo $full_name; ?></td>
                        <td><?php echo $ADDED_BY_NAME; ?></td>
                        <td><?php echo $ADDED_ON; ?></td>
                        <td>
                          <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="<?php echo $modal_ref; ?>">Approve</button>
                          <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $modal_ref_rej; ?>">Reject</button>
                          <div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                              <div class="modal-content">
                                <form method="post" id="<?php echo $modal_id; ?>">
                                    <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                                    <input type="hidden" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Approve Created User</h4>
                                  </div>
                                  <div class="modal-body">
                                    <p>
                                      Do you want to approve this newly created user account?<br />

                                      <table width="100%" class="table table-striped table-bordered">
                                        <tr valign="top"><th width="20%">User App Id</th><th width="3%">:</th><td><?php echo $USER_ID; ?></td></tr>
                                        <tr valign="top"><th width="20%">User Core Id</th><th width="3%">:</th><td><?php echo $USER_CORE_ID; ?></td></tr>
                                        <tr valign="top"><th>UserName</th><th>:</th><td><?php echo $CORE_username; ?></td></tr>
                                        <tr valign="top"><th>Location</th><th>:</th><td><?php echo $officeName; ?></td></tr>
                                        <tr valign="top"><th>First Name</th><th>:</th><td><?php echo $firstname; ?></td></tr>
                                        <tr valign="top"><th>Last Name</th><th>:</th><td><?php echo $lastname; ?></td></tr>
                                        <tr valign="top"><th>Email</th><th>:</th><td><?php echo $EMAIL_ADDRESS; ?></td></tr>
                                        <tr valign="top"><th>User Core Roles</th><th>:</th>
                                            <td><?php
                                            for ($f=0; $f < sizeof($selectedRoles); $f++) { 
                                              $role = $selectedRoles[$f];
                                              $role_name = $role["name"];
                                              echo $role_name."<br />";
                                            }

                                            ?></td></tr>
                                        <tr valign="top"><th>Phone</th><th>:</th><td><?php echo $PHONE; ?></td></tr>
                                        <tr valign="top"><th>Gender</th><th>:</th><td><?php echo $GENDER; ?></td></tr>
                                        <tr valign="top"><th>Added By</th><th>:</th><td><?php echo $ADDED_BY_NAME; ?></td></tr>
                                        <tr valign="top"><th>Added On</th><th>:</th><td><?php echo $ADDED_ON; ?></td></tr>


                                      </table>
                                    </p>
                                  </div>
                                  <div class="modal-footer">
                                    <table align="right">
                                      <tr>
                                        <td>                                              
                                            <button type="submit" class="btn btn-primary btn-sm" name="btn_apprv_user">Approve User</button>
                                        </td>
                                        <td>
                                          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">No</button>
                                        </td>
                                        
                                      </tr>
                                    </table>                                         
                                  </div>

                                </form>
                              </div>
                            </div>
                          </div>

                          <div class="modal fade" id="<?php echo $modal_id_rej; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                              <div class="modal-content">
                                <form method="post" id="<?php echo $modal_id_rej; ?>">
                                    <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                                    <input type="hidden" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Reject Created User</h4>
                                  </div>
                                  <div class="modal-body">
                                    <p>
                                      Do you want to reject this newly created user account?<br />

                                      <table width="100%" class="table table-striped table-bordered">
                                        <tr valign="top"><th width="20%">User App Id</th><th width="3%">:</th><td><?php echo $USER_ID; ?></td></tr>
                                        <tr valign="top"><th width="20%">User Core Id</th><th width="3%">:</th><td><?php echo $USER_CORE_ID; ?></td></tr>
                                        <tr valign="top"><th>UserName</th><th>:</th><td><?php echo $CORE_username; ?></td></tr>
                                        <tr valign="top"><th>Location</th><th>:</th><td><?php echo $officeName; ?></td></tr>
                                        <tr valign="top"><th>Full Name</th><th>:</th><td><?php echo $full_name; ?></td></tr>
                                        <tr valign="top"><th>Added By</th><th>:</th><td><?php echo $ADDED_BY_NAME; ?></td></tr>
                                        <tr valign="top"><th>Added On</th><th>:</th><td><?php echo $ADDED_ON; ?></td></tr>
                                        <tr valign="top"><th>Rejection Reason</th><th>:</th>
                                            <td><textarea id="rej_reason" name="rej_reason" required="" cols="120" rows="4"></textarea></td></tr>


                                      </table>
                                    </p>
                                  </div>
                                  <div class="modal-footer">
                                    <table align="right">
                                      <tr>
                                        <td>                                              
                                            <button type="submit" class="btn btn-danger btn-sm" name="btn_reject_user">Reject User</button>
                                        </td>
                                        <td>
                                          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>
                                        </td>
                                        
                                      </tr>
                                    </table>                                         
                                  </div>

                                </form>
                              </div>
                            </div>
                          </div>
                      

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
