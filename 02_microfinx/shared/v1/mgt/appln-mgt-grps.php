<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Create New Group
if (isset($_POST['btn_create_group'])) {
  $GRP_NAME = trim(mysql_real_escape_string($_POST['GRP_NAME']));
  $CREATED_ON = GetCurrentDateTime(); 
  $CREATED_BY = $_SESSION['UPR_USER_ID'];


  $q = "INSERT INTO appln_mgt_group(GRP_NAME,CREATED_ON,CREATED_BY) VALUES('$GRP_NAME','$CREATED_ON','$CREATED_BY')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];

  # ... Process Entity System ID (Group ID)
  $id_prefix = "AMG";
  $id_len = 11;
  $id_record_id = $RECORD_ID;
  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
  $GRP_ID = $ENTITY_ID;

  # ... Updating the role id
  $q2 = "UPDATE appln_mgt_group SET GRP_ID='$GRP_ID' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "APPLN_MGT_GROUP";
    $ENTITY_ID_AFFECTED = $GRP_ID;
    $EVENT = "CREATE";
    $EVENT_OPERATION = "CREATE_NEW_APPLN_MGT_GROUP";
    $EVENT_RELATION = "appln_mgt_group";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Group has been created. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }
}

# ... Disable Group
if (isset($_POST['btn_disable_group'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];
  $GRP_STATUS = "DISABLED";

  # ... SQL
  $q = "UPDATE appln_mgt_group SET GRP_STATUS='$GRP_STATUS' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "APPLN_MGT_GROUP";
    $ENTITY_ID_AFFECTED = $_POST['ADD_RECORD_ID'];
    $EVENT = "DISABLE";
    $EVENT_OPERATION = "DISABLE_APPLN_MGT_GROUP";
    $EVENT_RELATION = "appln_mgt_group";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "MESSAGE: Group has been disabled. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");

  }
}

# ... Re-enable Group
if (isset($_POST['btn_reenable_group'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];
  $GRP_STATUS = "ACTIVE";

  # ... SQL
  $q = "UPDATE appln_mgt_group SET GRP_STATUS='$GRP_STATUS' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "APPLN_MGT_GROUP";
    $ENTITY_ID_AFFECTED =$_POST['ADD_RECORD_ID'];
    $EVENT = "RE_ENABLE";
    $EVENT_OPERATION = "RE_ENABLE_APPLN_MGT_GROUP";
    $EVENT_RELATION = "appln_mgt_group";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Group has been re-enabled. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");

  }
}

# ... Delete group
if (isset($_POST['btn_delete_group'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];

  $grp = array();
  $grp = FetchAppMgtGroupByRecordId($RECORD_ID);
  $GRP_ID = $grp['GRP_ID'];
  $GRP_NAME = $grp['GRP_NAME'];
  $CREATED_ON = $grp['CREATED_ON'];
  $CREATED_BY = $grp['CREATED_BY'];
  $GRP_STATUS = $grp['GRP_STATUS'];

  # ... 02: Save Data to DataBase
  $q = "INSERT INTO appln_mgt_group_deleted(RECORD_ID,GRP_ID, GRP_NAME, CREATED_ON,CREATED_BY,GRP_STATUS) VALUES('$RECORD_ID','$GRP_ID','$GRP_NAME','$CREATED_ON','$CREATED_BY','$GRP_STATUS');";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];
  if ( $RESP=="EXECUTED" ) {
    
    $TABLE = "appln_mgt_group";
    $TABLE_RECORD_ID = $_POST['ADD_RECORD_ID'];
    $delete_response = array();
    $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
    $DEL_FLG = $delete_response["DEL_FLG"];
    $DEL_ROW = $delete_response["DEL_ROW"];

    if ($DEL_FLG=="Y") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "APPLN_MGT_GROUP";
      $ENTITY_ID_AFFECTED = $GRP_ID;
      $EVENT = "DELETE";
      $EVENT_OPERATION = "DELETE_APPLN_MGT_GROUP";
      $EVENT_RELATION = "appln_mgt_group -> appln_mgt_group_deleted";
      $EVENT_RELATION_NO = $_POST['ADD_RECORD_ID'];
      $OTHER_DETAILS = $DEL_ROW;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: Application Mgt group has been deleted completely. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5;");
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
    LoadDefaultCSSConfigurations("Appln Mgt Groups", $APP_SMALL_LOGO); 

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
                <h2>Application Mgt Groups</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         

                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">
                          <span style="font-size: 16px;">Current Mgmt Groups</span>

                          <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#crt_grp">Create New Group</button>
                          <div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content" style="color: #333;">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Create New Group</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="dddqwqqw2" method="post">
                                      
                                      <label>Group Name:</label><br>
                                      <input type="text" id="GRP_NAME" name="GRP_NAME" class="form-control" required=""><br><br>
                                      
                                      <br>
                                      <button type="submit" class="btn btn-primary btn-sm" name="btn_create_group">Create</button>
                                      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                               

                              </div>
                            </div>
                          </div>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Group Name</th>
                      <th>Date Added</th>
                      <th>Group Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $GRP_STATUS = "";
                    $grp_list = array();
                    $grp_list = FetchAppMgtGroupList($GRP_STATUS);
                    for ($i=0; $i < sizeof($grp_list); $i++) { 
                      $grp = array();
                      $grp = $grp_list[$i];
                      $RECORD_ID = $grp['RECORD_ID'];
                      $GRP_ID = $grp['GRP_ID'];
                      $GRP_NAME = $grp['GRP_NAME'];
                      $CREATED_ON = $grp['CREATED_ON'];
                      $CREATED_BY = $grp['CREATED_BY'];
                      $GRP_STATUS = $grp['GRP_STATUS'];

                      
                      $id = "FTT".($i+1);
                      $target = "#".$id;
                      $form_id = "FORM_".$id;

                      $id2 = "FTT2".($i+1);
                      $target2 = "#".$id2;
                      $form_id2 = "FORM_".$id2;

                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;

                      $datatotransfer = $GRP_ID;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $GRP_NAME; ?></td>
                        <td><?php echo $CREATED_ON; ?></td>
                        <td><?php echo $GRP_STATUS; ?></td>
                        <td>
                          <?php
                          if ($GRP_STATUS=="ACTIVE") {
                            ?>
                            <a href="appln-mgt-grps-ind?k=<?php echo $datatotransfer; ?>" class="btn btn-xs btn-primary">Manage</a>
                            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="<?php echo $target; ?>">Disable</button>
                            <div id="<?php echo $id; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="color: #333;">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Disable Group</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Group Name:</label><br>
                                        <?php echo $GRP_NAME; ?><br><br>
                                        
                                       
                                        <br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_disable_group">Yes</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                      </form>
                                  </div>
                                 

                                </div>
                              </div>
                            </div>
                            <?php
                          }
                          if ($GRP_STATUS=="DISABLED") {
                            ?>
                            <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="<?php echo $target2; ?>">Re-enable</button>
                            <div id="<?php echo $id2; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="color: #333;">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Renable Group</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id2; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Group Name:</label><br>
                                        <?php echo $GRP_NAME; ?><br><br>
                                        
                                        <br>
                                        <button type="submit" class="btn btn-success btn-sm" name="btn_reenable_group">Renable</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                      </form>
                                  </div>
                                 

                                </div>
                              </div>
                            </div>


                            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $target3; ?>">Delete</button>
                            <div id="<?php echo $id3; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="color: #333;">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Disable Group</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id3; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Group Name:</label><br>
                                        <?php echo $GRP_NAME; ?><br><br>
                     
                                        <strong>NOTE:</strong>
                                        This action cannot be undone.
                                        <br><br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_group">Delete</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
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
