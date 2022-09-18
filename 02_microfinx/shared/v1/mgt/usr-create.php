<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Adding the User
if (isset($_POST['btn_add_user'])) {

  $id = mysql_real_escape_string($_POST["id"]);
  $username = mysql_real_escape_string($_POST["username"]);
  $officeName = mysql_real_escape_string($_POST["officeName"]);
  $firstname = mysql_real_escape_string($_POST["firstname"]);
  $lastname = mysql_real_escape_string($_POST["lastname"]);
  $email = mysql_real_escape_string($_POST["email"]);
  $phne = mysql_real_escape_string($_POST["phone"]);
  $gndr = mysql_real_escape_string($_POST["gender"]);

  # ... Add user to the system
  $USER_CORE_ID = $id;
  $GENDER = $gndr;
  $PHONE = $phne;
  $EMAIL_ADDRESS = $officeName;
  $LOGGED_IN = "NO";
  $ADDED_ON = GetCurrentDateTime();
  $ADDED_BY = $_SESSION['UPR_USER_ID'];
  

  $q = "INSERT INTO upr(USER_CORE_ID,GENDER,PHONE,EMAIL_ADDRESS,LOGGED_IN,ADDED_ON,ADDED_BY) VALUES('$USER_CORE_ID','$GENDER','$PHONE','$EMAIL_ADDRESS','$LOGGED_IN','$ADDED_ON','$ADDED_BY')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];

  # ... Process Entity System ID (Role ID)
  $id_prefix = "K";
  $id_len = 7;
  $id_record_id = $RECORD_ID;
  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
  $USER_ID = $ENTITY_ID;

  # ... Updating the role id
  $q2 = "UPDATE upr SET USER_ID='$USER_ID' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "SYSTEM_USER";
    $ENTITY_ID_AFFECTED = $USER_ID;
    $EVENT = "CREATE";
    $EVENT_OPERATION = "ADD_NEW_USER";
    $EVENT_RELATION = "upr";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "User added successfully. Seek approval From Authorizer.";
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
    LoadDefaultCSSConfigurations("Create User", $APP_SMALL_LOGO); 

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
                <h2>Add New User</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
              
              <table id="datatable" class="table table-striped table-bordered">
                <thead>
                  <tr valign="top">
                    <th colspan="6" bgcolor="#EEE">List of Core Users</th>
                  </tr>
                  <tr valign="top">
                    <th>#</th>
                    <th>UserName</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Location</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $response_msg = array();
                  $response_msg = FetchAllUsersFromCore($MIFOS_CONN_DETAILS);
                  $CONN_FLG = $response_msg["CONN_FLG"];
                  $CORE_RESP = $response_msg["CORE_RESP"];

                  # ... 01: Track Connection to Core
                  if ($CONN_FLG=="NOT_CONNECTED") {
                    # ... No connection to core
                    $alert_type = "ERROR";
                    $alert_msg = "NO CONNECTION TO CORE.";
                    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
                  }
                  else {
                    if (sizeof($CORE_RESP)>0) {
                      $x = 0;
                      for ($i=0; $i < sizeof($CORE_RESP); $i++) { 
                        
                        $sys_usr = $CORE_RESP[$i];
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

                        # ... 02: Check if user is already added
                        $user_exists = CheckIfUserExists($id);
                        $modal_id = "modal_".($i+1);
                        $modal_ref = "#".$modal_id;

                        if ($user_exists=="YES") {
                          # ... do nothing
                        }
                        else {
                          # ... 03: Displaying the Data
                          ?>
                          <tr valign="top">
                            <td><?php echo ($x+1); ?>. </td>
                            <td><?php echo $username; ?></td>
                            <td><?php echo $full_name; ?></td>
                            <td><?php echo $email; ?></td>
                            <td><?php echo $officeName; ?></td>
                            <td>
                              <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="<?php echo $modal_ref; ?>">Add</button>

                              <div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                  <div class="modal-content">
                                    <form method="post" id="<?php echo $modal_id; ?>">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <input type="hidden" name="username" value="<?php echo $username; ?>">
                                        <input type="hidden" name="officeName" value="<?php echo $officeName; ?>">
                                        <input type="hidden" name="firstname" value="<?php echo $firstname; ?>">
                                        <input type="hidden" name="lastname" value="<?php echo $lastname; ?>">
                                        <input type="hidden" name="email" value="<?php echo $email; ?>">
                                      <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                        </button>
                                        <h4 class="modal-title" id="myModalLabel">Add User</h4>
                                      </div>
                                      <div class="modal-body">
                                        <p>
                                          Do you want to add this user account?<br />

                                          <table width="100%" class="table table-striped table-bordered">
                                            <tr valign="top"><th width="20%">User Core Id</th><th width="3%">:</th><td><?php echo $id; ?></td></tr>
                                            <tr valign="top"><th>UserName</th><th>:</th><td><?php echo $username; ?></td></tr>
                                            <tr valign="top"><th>Location</th><th>:</th><td><?php echo $officeName; ?></td></tr>
                                            <tr valign="top"><th>First Name</th><th>:</th><td><?php echo $firstname; ?></td></tr>
                                            <tr valign="top"><th>Last Name</th><th>:</th><td><?php echo $lastname; ?></td></tr>
                                            <tr valign="top"><th>Email</th><th>:</th><td><?php echo $email; ?></td></tr>
                                            <tr valign="top"><th>User Core Roles</th><th>:</th>
                                                <td><?php
                                                for ($f=0; $f < sizeof($selectedRoles); $f++) { 
                                                  $role = $selectedRoles[$f];
                                                  $role_name = $role["name"];
                                                  echo $role_name."<br />";
                                                }

                                                ?></td></tr>
                                            <tr valign="top"><th>Phone</th><th>:</th><td><input type="number" id="phone" name="phone" required=""></td></tr>
                                            <tr valign="top"><th>Gender</th><th>:</th><td>
                                              <select id="gender" name="gender" required="">
                                                <option value="">Select Gender</option>
                                                <option value="MALE">Male</option>
                                                <option value="FEMALE">Female</option>
                                                <option value="OTHER">Other</option>
                                              </select>
                                            </td></tr>


                                          </table>
                                        </p>
                                      </div>
                                      <div class="modal-footer">
                                        <table align="right">
                                          <tr>
                                            <td>                                              
                                                <button type="submit" class="btn btn-primary btn-sm" name="btn_add_user">Add User</button>
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
                          

                            </td>
                          </tr>

                          <?php
                          $x++;
                        }

                        

                      } // ... End Loop

                    }
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
