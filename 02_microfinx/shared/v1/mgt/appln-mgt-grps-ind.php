<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$GRP_ID = mysql_real_escape_string($_GET['k']);
$grp = array();
$grp = FetchAppMgtGroupById($GRP_ID);
$RECORD_ID = $grp['RECORD_ID'];
$GRP_NAME = $grp['GRP_NAME'];
$CREATED_ON = $grp['CREATED_ON'];
$CREATED_BY = $grp['CREATED_BY'];
$GRP_STATUS = $grp['GRP_STATUS'];

# ... Edit Group Name
if (isset($_POST['btn_edit_grp_name'])) {
  $GRP_ID = trim(mysql_real_escape_string($_POST['GRP_ID']));
  $GRP_NAME = trim(mysql_real_escape_string($_POST['GRP_NAME']));

  # ... SQL
  $q = "UPDATE appln_mgt_group SET GRP_NAME='$GRP_NAME' WHERE GRP_ID='$GRP_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "APPLN_MGT_GROUP";
    $ENTITY_ID_AFFECTED =$GRP_ID;
    $EVENT = "EDIT_GRP_NAME";
    $EVENT_OPERATION = "EDIT_NEW_APPLN_MGT_GROUP";
    $EVENT_RELATION = "appln_mgt_group";
    $EVENT_RELATION_NO = $GRP_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Group name has been changed. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");

  }
}

# ... Adding Memebers to the group
if (isset($_POST['btn_add_mmbrs_to_grp'])) {
  $GRP_ID = trim(mysql_real_escape_string($_POST['GRP_ID']));

  $CNT_ADDED = 0;
  $REC_ADDED = "";
  $usr_list = FetchAppMgtSysUserList($GRP_ID);
  for ($i=0; $i < sizeof($usr_list); $i++) { 
    $usr = array();
    $usr = $usr_list[$i];
    $USER_ID = $usr['USER_ID'];
    $USER_CORE_ID = $usr['USER_CORE_ID'];

    $response_msg = FetchUserDetailsFromCore($USER_CORE_ID, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];
    $sys_usr = $response_msg["CORE_RESP"];
    $firstname = $sys_usr["firstname"];
    $lastname = $sys_usr["lastname"];
    $full_name = $firstname." ".$lastname;

    if (isset($_POST[$USER_ID])) {

      $GRP_MEMBER_ID = $_POST[$USER_ID];
      $ADDED_BY = $_SESSION['UPR_USER_ID'];
      $CREATED_ON = GetCurrentDateTime();

      $q = "INSERT INTO appln_mgt_group_members(GRP_ID,GRP_MEMBER_ID,ADDED_BY,CREATED_ON) VALUES('$GRP_ID','$GRP_MEMBER_ID','$ADDED_BY','$CREATED_ON')";
      $exec_response = array();
      $exec_response = ExecuteEntityInsert($q);
      $RESP = $exec_response["RESP"]; 
      $RECORD_ID = $exec_response["RECORD_ID"];

      if ($RESP=="EXECUTED") {
        if ($REC_ADDED=="") {
          $REC_ADDED = $GRP_MEMBER_ID;
        }
        else {
          $REC_ADDED = $REC_ADDED."|".$GRP_MEMBER_ID;
        }
        $CNT_ADDED++;
      }

    } // ... END
  }


  # ... Log activity & Display Summary
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "APPLN_MGT_GROUP";
  $ENTITY_ID_AFFECTED = $GRP_ID;
  $EVENT = "ADD_MEMBERS";
  $EVENT_OPERATION = "ADD_NEW_MEMBERS";
  $EVENT_RELATION = "appln_mgt_group_members";
  $EVENT_RELATION_NO = $GRP_ID;
  $OTHER_DETAILS = $REC_ADDED;
  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


  $alert_type = "SUCCESS";
  $alert_msg = "$CNT_ADDED member(s) added to management group successfully. Refreshing in 4 seconds.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  header("Refresh:4;");
}

# ... Remove Member from Group
if (isset($_POST['btn_delete_grp_mmbr'])) {
  $M_RECORD_ID = trim(mysql_real_escape_string($_POST['M_RECORD_ID']));

  $TABLE = "appln_mgt_group_members";
  $TABLE_RECORD_ID = $M_RECORD_ID;
  $delete_response = array();
  $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
  $DEL_FLG = $delete_response["DEL_FLG"];
  $DEL_ROW = $delete_response["DEL_ROW"];

  if ($DEL_FLG=="Y") {
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "APPLN_MGT_GROUP";
    $ENTITY_ID_AFFECTED = $_POST['M_RECORD_ID'];
    $EVENT = "DELETING";
    $EVENT_OPERATION = "DELETING_APPLN_GRP_MEMBER";
    $EVENT_RELATION = "notification_group_members";
    $EVENT_RELATION_NO = $M_RECORD_ID;
    $OTHER_DETAILS = $DEL_ROW;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Member has been removed from Group. Refreshing in 5 seconds.";
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
    LoadDefaultCSSConfigurations("Appln Grp Details", $APP_SMALL_LOGO); 

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
                <a href="appln-mgt-grps" class="btn btn-sm btn-dark pull-left">Back</a>
                <h2><strong>GROUP: </strong><?php echo $GRP_NAME; ?></h2>
                <button type="button" class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#edit_grp">Edit Group Name</button>
                <div id="edit_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content" style="color: #333;">

                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel2">Edit Group Name</h4>
                      </div>
                      <div class="modal-body">
                          <form id="dddqwqqw2" method="post">
                            <input type="hidden" id="GRP_ID" name="GRP_ID" value="<?php echo $GRP_ID; ?>">
                            <label>Group Name:</label><br>
                            <input type="text" id="GRP_NAME" name="GRP_NAME" class="form-control" required="" value="<?php echo $GRP_NAME; ?>"><br><br>
                            

                            <br>
                            <button type="submit" class="btn btn-primary btn-sm" name="btn_edit_grp_name">Save Changes</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                          </form>
                      </div>
                     

                    </div>
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         

                <table class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top" bgcolor="#EEE">
                      <th>Group ID</th>
                      <th>Group Name</th>
                      <th>Date Added</th>
                      <th>Group Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                      <td><?php echo $GRP_ID; ?> </td>
                      <td><?php echo $GRP_NAME; ?></td>
                      <td><?php echo $CREATED_ON; ?></td>
                      <td><?php echo $GRP_STATUS; ?></td>
                    </tr>
                  </tbody>
                </table>

                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="6" bgcolor="#EEE">
                        <span style="font-size: 16px;">Appln Group Members</span>
                        <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#crt_grp">Add Members</button>
                        <div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel2">Add Members to <strong>Group: <?php echo $GRP_NAME; ?></strong></h4>
                              </div>
                              <div class="modal-body">
                                  <form id="dddqwqqw2" method="post">
                                    <table id="datatable2" width="100%" class="table table-striped table-bordered">
                                      <thead>
                                        <tr valign="top">
                                          <th colspan="3" bgcolor="#EEE">Select Users</th>
                                        </tr>
                                        <tr valign="top">
                                          <th>#</th>
                                          <th>Name</th>
                                          <th>Actions</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php
                                        $usr_list = FetchAppMgtSysUserList($GRP_ID);
                                        
                                        for ($i=0; $i < sizeof($usr_list); $i++) { 
                                          $usr = array();
                                          $usr = $usr_list[$i];
                                          $USER_ID = $usr['USER_ID'];
                                          $USER_CORE_ID = $usr['USER_CORE_ID'];
                                          # ... 01 Get Core User Details
                                          $response_msg = FetchUserDetailsFromCore($USER_CORE_ID, $MIFOS_CONN_DETAILS);
                                          $CONN_FLG = $response_msg["CONN_FLG"];
                                          //$RESP_FLG = $response_msg["RESP_FLG"];
                                          $CORE_RESP = $response_msg["CORE_RESP"];
                                          $sys_usr = $response_msg["CORE_RESP"];
                                          $firstname = $sys_usr["firstname"];
                                          $lastname = $sys_usr["lastname"];
                                          

                                          $full_name = $firstname." ".$lastname;

                                          ?>
                                          <tr valign="top">
                                            <td><?php echo ($i+1); ?>. </td>
                                            <td><?php echo $full_name; ?></td>
                                            <td>
                                              <input type="checkbox" id="<?php echo $USER_ID; ?>" name="<?php echo $USER_ID; ?>" value="<?php echo $USER_ID; ?>">
                                            </td>
                                          </tr>

                                          <?php
                                        }

                                        ?>
                                      </tbody>
                                    </table>
                                    
                                    

                                    <br>
                                      <input type="hidden" id="GRP_ID" name="GRP_ID" value="<?php echo $GRP_ID; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm pull-right" name="btn_add_mmbrs_to_grp">Add Members</button>
                                    <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal">Cancel</button>
                                  </form>
                              </div>
                             

                            </div>
                          </div>
                        </div>
                      </th>

                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Member ID</th>
                      <th>Member Name</th>
                      <th>Date Added</th>
                      <th>Member Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $grp_member_list = array();
                    $grp_member_list = FetchAppMgtGroupMembers($GRP_ID);
                    for ($i=0; $i < sizeof($grp_member_list); $i++) { 
                      $grp_membr = array();
                      $grp_membr = $grp_member_list[$i];
                      $M_RECORD_ID = $grp_membr['RECORD_ID'];
                      $M_GRP_ID = $grp_membr['GRP_ID'];
                      $M_GRP_MEMBER_ID = $grp_membr['GRP_MEMBER_ID'];
                      $M_ADDED_BY = $grp_membr['ADDED_BY'];
                      $M_CREATED_ON = $grp_membr['CREATED_ON'];
                      $M_GRP_MEMBER_STATUS = $grp_membr['GRP_MEMBER_STATUS'];

                      # ... GET MEMBER DETAILS
                      $USER_DETAILS = GetUserDetailsFromPortal($M_GRP_MEMBER_ID);
                      $M_USER_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
                      $response_msg = FetchUserDetailsFromCore($M_USER_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      //$RESP_FLG = $response_msg["RESP_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $sys_usr = $response_msg["CORE_RESP"];
                      $firstname = $sys_usr["firstname"];
                      $lastname = $sys_usr["lastname"];

                      $M_full_name = $firstname." ".$lastname;

                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;
                      ?>
                       <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $M_GRP_MEMBER_ID; ?></td>
                        <td><?php echo $M_full_name; ?></td>
                        <td><?php echo $M_CREATED_ON; ?></td>
                        <td><?php echo $M_GRP_MEMBER_STATUS; ?></td>
                        <td>
                          <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $target3; ?>">Remove</button>
                          <div id="<?php echo $id3; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Remove Member from Group</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="<?php echo $form_id3; ?>" method="post">
                                      <input type="hidden" id="M_RECORD_ID" name="M_RECORD_ID" value="<?php echo $M_RECORD_ID; ?>">
                                      
                                      <label>Member ID:</label><br>
                                      <?php echo $M_GRP_MEMBER_ID; ?><br><br>
                                      
                                      <label>Member Name:</label><br>
                                      <?php echo $M_full_name; ?><br><br>
                                      
                                      <strong>NOTE:</strong>
                                      This action cannot be undone.
                                      <br><br>
                                      <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_grp_mmbr">Delete</button>
                                      <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                    </form>
                                </div>
                               

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
