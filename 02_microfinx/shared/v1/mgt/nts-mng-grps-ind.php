<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$GRP_ID = mysql_real_escape_string($_GET['k']);
$grp = array();
$grp = FetchGroupById($GRP_ID);
$RECORD_ID = $grp['RECORD_ID'];
$GRP_TYPE_ID = $grp['GRP_TYPE_ID'];
$GRP_NAME = $grp['GRP_NAME'];
$DATE_CREATED = $grp['DATE_CREATED'];
$CREATED_BY = $grp['CREATED_BY'];
$GRP_STATUS = $grp['GRP_STATUS'];

# ... Get Group Type Name
$grp_type = array();
$grp_type = FetchGroupTypeById($GRP_TYPE_ID);
$GRP_TYPE_NAME = $grp_type['GRP_TYPE_NAME'];

# ... Adding Memebers to the group
if (isset($_POST['btn_add_mmbrs_to_grp'])) {
  $GRP_ID = trim(mysql_real_escape_string($_POST['GRP_ID']));
  $ADDRESS_ENTITY_TYPE = trim(mysql_real_escape_string($_POST['ADDRESS_ENTITY_TYPE']));
  $ADDRESS_STATUS = trim(mysql_real_escape_string($_POST['ADDRESS_STATUS']));

  $address_book = array();
  $address_book = FetchMmbrsToAddGroup($ADDRESS_ENTITY_TYPE, $ADDRESS_STATUS, $GRP_ID);
  $CNT_ADDED = 0;
  $REC_ADDED = "";
  for ($i=0; $i < sizeof($address_book); $i++) { 
    $address = array();
    $address = $address_book[$i];
    $ADDRESS_ENTITY_ID = $address['ADDRESS_ENTITY_ID'];

    if (isset($_POST[$ADDRESS_ENTITY_ID])) {
      
      # ... SQL to save to DB
      $MEMBER_ID = $ADDRESS_ENTITY_ID;
      $DATE_CREATED = GetCurrentDateTime();
      $CREATED_BY = $_SESSION['UPR_USER_ID'];

      $q = "INSERT INTO notification_group_members(MEMBER_ID,GRP_ID,DATE_CREATED,CREATED_BY) VALUES('$MEMBER_ID','$GRP_ID','$DATE_CREATED','$CREATED_BY')";
      $exec_response = array();
      $exec_response = ExecuteEntityInsert($q);
      $RESP = $exec_response["RESP"]; 
      $RECORD_ID = $exec_response["RECORD_ID"];
      if ($RESP=="EXECUTED") {

        if ($REC_ADDED=="") {
          $REC_ADDED = $MEMBER_ID;
        }
        else {
          $REC_ADDED = $REC_ADDED."|".$MEMBER_ID;
        }
        $CNT_ADDED++;
      }
    }
  }  // ... END LOOP


  # ... Log activity & Display Summary
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "NOTIF_GROUP";
  $ENTITY_ID_AFFECTED = $GRP_ID;
  $EVENT = "ADD_MEMBERS";
  $EVENT_OPERATION = "ADD_NEW_MEMBERS";
  $EVENT_RELATION = "notification_group_members";
  $EVENT_RELATION_NO = $RECORD_ID;
  $OTHER_DETAILS = $REC_ADDED;
  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


  $alert_type = "SUCCESS";
  $alert_msg = "$CNT_ADDED member(s) added to notification group successfully. Refreshing in 4 seconds.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  header("Refresh:4;");
}

# ... Remove Member from Group
if (isset($_POST['btn_delete_grp_mmbr'])) {
  $MEM_RECORD_ID = trim(mysql_real_escape_string($_POST['MEM_RECORD_ID']));

  $TABLE = "notification_group_members";
  $TABLE_RECORD_ID = $MEM_RECORD_ID;
  $delete_response = array();
  $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
  $DEL_FLG = $delete_response["DEL_FLG"];
  $DEL_ROW = $delete_response["DEL_ROW"];

  if ($DEL_FLG=="Y") {
    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "NOTIF_GROUP";
    $ENTITY_ID_AFFECTED = $_POST['MEM_RECORD_ID'];
    $EVENT = "DELETING";
    $EVENT_OPERATION = "DELETING_ADDRESS_IN_ADDRESSBOOK";
    $EVENT_RELATION = "notification_group_members";
    $EVENT_RELATION_NO = $MEM_RECORD_ID;
    $OTHER_DETAILS = $DEL_ROW;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    $alert_type = "SUCCESS";
    $alert_msg = "MESSAGE: Member has been removed from Group. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }
}

# ... Re-enable Group
if (isset($_POST['btn_edit_grp_name'])) {
  $GRP_ID = trim(mysql_real_escape_string($_POST['GRP_ID']));
  $GRP_NAME = trim(mysql_real_escape_string($_POST['GRP_NAME']));

  # ... SQL
  $q = "UPDATE notification_groups SET GRP_NAME='$GRP_NAME' WHERE GRP_ID='$GRP_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "NOTIF_GROUP";
    $ENTITY_ID_AFFECTED =$GRP_ID;
    $EVENT = "EDIT_NAME";
    $EVENT_OPERATION = "EDIT_NOTIF_GROUP_AME";
    $EVENT_RELATION = "notification_groups";
    $EVENT_RELATION_NO = $RECORD_ID;
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



?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Manage Group", $APP_SMALL_LOGO); 

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
                <a href="nts-mng-grps" class="btn btn-sm btn-dark pull-left">Back</a>
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
                      <th>Group Type</th>
                      <th>Date Added</th>
                      <th>Group Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                      <td><?php echo $GRP_ID; ?> </td>
                      <td><?php echo $GRP_NAME; ?></td>
                      <td><?php echo $GRP_TYPE_NAME; ?></td>
                      <td><?php echo $DATE_CREATED; ?></td>
                      <td><?php echo $GRP_STATUS; ?></td>
                    </tr>
                  </tbody>
                </table>

                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="6" bgcolor="#EEE">
                        <span style="font-size: 16px;">Group Members</span>
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
                                          <th>Address Name</th>
                                          <th>Actions</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php
                                        $ADDRESS_ENTITY_TYPE = $GRP_TYPE_NAME;
                                        $ADDRESS_STATUS = "ACTIVE";
                                        $address_book = array();
                                        $address_book = FetchMmbrsToAddGroup($ADDRESS_ENTITY_TYPE, $ADDRESS_STATUS, $GRP_ID);
                                        
                                        for ($i=0; $i < sizeof($address_book); $i++) { 
                                          $address = array();
                                          $address = $address_book[$i];
                                          $RECORD_ID = $address['RECORD_ID'];
                                          $ADDRESS_ENTITY_TYPE = $address['ADDRESS_ENTITY_TYPE'];
                                          $ADDRESS_ENTITY_ID = $address['ADDRESS_ENTITY_ID'];
                                          $ADDRESS_ENTITY_NAME = $address['ADDRESS_ENTITY_NAME'];
                                          $ADDRESS_ADDED_DATE = $address['ADDRESS_ADDED_DATE'];
                                          $ADDRESS_STATUS = $address['ADDRESS_STATUS'];

                                          ?>
                                          <tr valign="top">
                                            <td><?php echo ($i+1); ?>. </td>
                                            <td><?php echo $ADDRESS_ENTITY_NAME; ?></td>
                                            <td>
                                              <input type="checkbox" id="<?php echo $ADDRESS_ENTITY_ID; ?>" name="<?php echo $ADDRESS_ENTITY_ID; ?>" value="<?php echo $ADDRESS_ENTITY_ID; ?>">
                                            </td>
                                          </tr>

                                          <?php
                                        }

                                        ?>
                                      </tbody>
                                    </table>
                                    
                                    

                                    <br>
                                      <input type="hidden" id="GRP_ID" name="GRP_ID" value="<?php echo $GRP_ID; ?>">
                                      <input type="hidden" id="ADDRESS_ENTITY_TYPE" name="ADDRESS_ENTITY_TYPE" value="<?php echo $ADDRESS_ENTITY_TYPE; ?>">
                                      <input type="hidden" id="ADDRESS_STATUS" name="ADDRESS_STATUS" value="<?php echo $ADDRESS_STATUS; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm pull-right" name="btn_add_mmbrs_to_grp">Submit Members</button>
                                    <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal">Cancel</button>
                                  </form>
                              </div>
                             

                            </div>
                          </div>
                        </div>
                      </th>

                    </tr>
                    <tr valign="top">
                      <th>Member ID</th>
                      <th>Member Name</th>
                      <th>Member Type</th>
                      <th>Date Added</th>
                      <th>Member Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $grp_member_list = array();
                    $grp_member_list = FetchGroupMembers($GRP_ID);
                    for ($i=0; $i < sizeof($grp_member_list); $i++) { 
                      $grp_membr = array();
                      $grp_membr = $grp_member_list[$i];
                      $MEM_RECORD_ID = $grp_membr['RECORD_ID'];
                      $MEMBER_ID = $grp_membr['MEMBER_ID'];
                      $GRP_ID = $grp_membr['GRP_ID'];
                      $DATE_CREATED = $grp_membr['DATE_CREATED'];
                      $CREATED_BY = $grp_membr['CREATED_BY'];
                      $GRP_MEMBER_STATUS = $grp_membr['GRP_MEMBER_STATUS'];

                      # ... Get Member Details From Addess Book
                      $address = array();
                      $address = FetchAddressFromAddressBookById($MEMBER_ID);
                      $ADDRESS_ENTITY_TYPE = $address['ADDRESS_ENTITY_TYPE'];
                      $ADDRESS_ENTITY_NAME = $address['ADDRESS_ENTITY_NAME'];

                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;
                      ?>
                       <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $ADDRESS_ENTITY_NAME; ?></td>
                        <td><?php echo $ADDRESS_ENTITY_TYPE; ?></td>
                        <td><?php echo $DATE_CREATED; ?></td>
                        <td><?php echo $GRP_MEMBER_STATUS; ?></td>
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
                                      <input type="hidden" id="MEM_RECORD_ID" name="MEM_RECORD_ID" value="<?php echo $MEM_RECORD_ID; ?>">
                                      
                                      <label>Member ID:</label><br>
                                      <?php echo $MEMBER_ID; ?><br><br>
                                      
                                      <label>Member Name:</label><br>
                                      <?php echo $ADDRESS_ENTITY_NAME; ?><br><br>
                                      
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
