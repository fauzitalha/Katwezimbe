<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("System User List", $APP_SMALL_LOGO); 

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
                <h2>System User List</h2>
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
                      <th>User Status</th>
                      <th>Roles Status</th>
                      <th>2FA Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $usr_list = array();
                    $usr_list_active = array();
                    $usr_list_disabled = array();
                    $USER_STATUS_A = "ACTIVE";
                    $USER_STATUS_B = "DISABLED";
                    $usr_list_active = FetchSysUserList($USER_STATUS_A);
                    $usr_list_disabled = FetchSysUserList($USER_STATUS_B);

                    $usr_list = array_merge($usr_list_active ,$usr_list_disabled);
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
                      $id = isset($sys_usr["id"])? $sys_usr["id"]:"";
                      $CORE_username = isset($sys_usr["username"])? $sys_usr["username"]: "";
                      $officeId = isset($sys_usr["officeId"])? $sys_usr["officeId"] : "";
                      $officeName = isset($sys_usr["officeName"])? $sys_usr["officeName"]  : "";
                      $firstname = isset($sys_usr["firstname"])? $sys_usr["firstname"] : "";
                      $lastname =isset( $sys_usr["lastname"])? $sys_usr["lastname"] : "";
                      $email = isset($sys_usr["email"])? $sys_usr["email"] : "";
                      $passwordNeverExpires = isset($sys_usr["passwordNeverExpires"])?  $sys_usr["passwordNeverExpires"] : "";
                      $selectedRoles = isset($sys_usr["selectedRoles"])? $sys_usr["selectedRoles"] : "";
                      $isSelfServiceUser =isset($sys_usr["isSelfServiceUser"])?  $sys_usr["isSelfServiceUser"] : "";

                      $full_name = $firstname." ".$lastname;

                      # ... 02: Check Roles & 2FA Definition Status
                      $q_roles = "SELECT COUNT(*) AS RTN_VALUE FROM upr_usr_roles WHERE USER_ID='$USER_ID' AND USER_ROLE_STATUS='ACTIVE'";
                      $q_fa = "SELECT COUNT(*) AS RTN_VALUE FROM tfa_devices WHERE ENTITY_ID='$USER_ID' AND DEVICE_STATUS='ACTIVE'";
                      $roles_def = ReturnOneEntryFromDB($q_roles);
                      $fa_def = ReturnOneEntryFromDB($q_fa);

                      $roles_def_status = ($roles_def>0)? "Defined" : "Undefined";
                      $fa_def_status = ($fa_def>0)? "Defined" : "Undefined";

                     
                      $data_transfer = $USER_ID."_".$id;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $CORE_username; ?></td>
                        <td><?php echo $full_name; ?></td>
                        <td><?php echo strtolower($USER_STATUS); ?></td>
                        <td><?php echo strtolower($roles_def_status); ?></td>
                        <td><?php echo strtolower($fa_def_status); ?></td>
                        <td>
                          <a href="usr-view-ind-details?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
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
