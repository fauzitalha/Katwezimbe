<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Create New Group
if (isset($_POST['btn_create_tt'])) {
  $TRAN_TYPE_NAME = trim(mysql_real_escape_string($_POST['TRAN_TYPE_NAME']));
  $TRAN_DESC = trim(mysql_real_escape_string($_POST['TRAN_DESC']));
  $CHRG_FLG = "NN";
  $CREATED_BY = $_SESSION['UPR_USER_ID'];
  $CREATED_ON = GetCurrentDateTime(); 
  $TRAN_TYPE_STATUS = "ACTIVE";

  # ... Create tran_record;
  $q = "INSERT INTO txn_types(TRAN_TYPE_NAME,TRAN_DESC,CHRG_FLG,CREATED_BY,CREATED_ON,TRAN_TYPE_STATUS) 
                       VALUES('$TRAN_TYPE_NAME','$TRAN_DESC','$CHRG_FLG','$CREATED_BY','$CREATED_ON','$TRAN_TYPE_STATUS')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];

  # ... Process Entity System ID (Group ID)
  $id_prefix = "TTC";
  $id_len = 7;
  $id_record_id = $RECORD_ID;
  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
  $TRAN_TYPE_ID = $ENTITY_ID;

  # ... Updating the role id
  $q2 = "UPDATE txn_types SET TRAN_TYPE_ID='$TRAN_TYPE_ID' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "TRAN_TYPE";
    $ENTITY_ID_AFFECTED = $TRAN_TYPE_ID;
    $EVENT = "CREATE";
    $EVENT_OPERATION = "CREATE_NEW_TRAN_TYPE";
    $EVENT_RELATION = "txn_types";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Transaction Type has been created. Refreshing in 4 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:4;");
  }
}

# ... Disable Group
if (isset($_POST['btn_disable_tt'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];
  $TRAN_TYPE_STATUS = "DISABLED";

  # ... SQL
  $q = "UPDATE txn_types SET TRAN_TYPE_STATUS='$TRAN_TYPE_STATUS' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "TRAN_TYPE";
    $ENTITY_ID_AFFECTED = $_POST['ADD_RECORD_ID'];
    $EVENT = "DISABLE";
    $EVENT_OPERATION = "DISABLE_TRAN_TYPE";
    $EVENT_RELATION = "txn_types";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "MESSAGE: Tran Type has been disabled. Refreshing in 4 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:4;");

  }
}

# ... Re-enable Group
if (isset($_POST['btn_reenable_tt'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];
  $TRAN_TYPE_STATUS = "ACTIVE";

  # ... SQL
  $q = "UPDATE txn_types SET TRAN_TYPE_STATUS='$TRAN_TYPE_STATUS' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "TRAN_TYPE";
    $ENTITY_ID_AFFECTED =$_POST['ADD_RECORD_ID'];
    $EVENT = "RE_ENABLE";
    $EVENT_OPERATION = "RE_ENABLE_TRAN_TYPE";
    $EVENT_RELATION = "txn_types";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Tran Type has been re-enabled. Refreshing in 3 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:3;");

  }
}

# ... Delete group
if (isset($_POST['btn_delete_tt'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];
  $tt = array();
  $tt = FetchTransactionTypeByRecordId($RECORD_ID);
  $RECORD_ID = $tt['RECORD_ID'];
  $TRAN_TYPE_ID = $tt['TRAN_TYPE_ID'];
  $TRAN_TYPE_NAME = $tt['TRAN_TYPE_NAME'];
  $TRAN_DESC = $tt['TRAN_DESC'];
  $CHRG_FLG = $tt['CHRG_FLG'];
  $CHRG_EVENT_ID = $tt['CHRG_EVENT_ID'];
  $CREATED_BY = $tt['CREATED_BY'];
  $CREATED_ON = $tt['CREATED_ON'];
  $LST_CHNG_BY = $tt['LST_CHNG_BY'];
  $LST_CHNG_ON = $tt['LST_CHNG_ON'];
  $TRAN_TYPE_STATUS = $tt['TRAN_TYPE_STATUS'];

  # ... 02: Save Data to DataBase
  $q = "INSERT INTO txn_types_deleted(RECORD_ID,TRAN_TYPE_ID,TRAN_TYPE_NAME,TRAN_DESC,CHRG_FLG,CHRG_EVENT_ID,CREATED_BY,CREATED_ON
                                     ,LST_CHNG_BY,LST_CHNG_ON,TRAN_TYPE_STATUS) 
                              VALUES('$RECORD_ID','$TRAN_TYPE_ID','$TRAN_TYPE_NAME','$TRAN_DESC','$CHRG_FLG','$CHRG_EVENT_ID','$CREATED_BY'
                                    ,'$CREATED_ON','$LST_CHNG_BY','$LST_CHNG_ON','$TRAN_TYPE_STATUS')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];
  if ( $RESP=="EXECUTED" ) {
    
    $TABLE = "txn_types";
    $TABLE_RECORD_ID = $_POST['ADD_RECORD_ID'];
    $delete_response = array();
    $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
    $DEL_FLG = $delete_response["DEL_FLG"];
    $DEL_ROW = $delete_response["DEL_ROW"];

    if ($DEL_FLG=="Y") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "TRAN_TYPE";
      $ENTITY_ID_AFFECTED = $TRAN_TYPE_ID;
      $EVENT = "DELETE";
      $EVENT_OPERATION = "DELETE_TRAN_TYPE";
      $EVENT_RELATION = "txn_types -> txn_types_deleted";
      $EVENT_RELATION_NO = $_POST['ADD_RECORD_ID'];
      $OTHER_DETAILS = $DEL_ROW;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: Transaction type has been deleted completely. Refreshing in 5 seconds.";
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
    LoadDefaultCSSConfigurations("Tran Types", $APP_SMALL_LOGO); 

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
                <h2>Transaction Type Maintenance</h2>
                <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#crt_grp">New Tran Type</button>
                <div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">

                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel2">Create New Tran Type</h4>
                      </div>
                      <div class="modal-body">
                          <form id="dddqwqqw2" method="post">
                            
                            <label>Tran Type Name:</label><br>
                            <input type="text" id="TRAN_TYPE_NAME" name="TRAN_TYPE_NAME" class="form-control" required=""><br>

                            <label>Tran Description:</label><br>
                            <textarea id="TRAN_DESC" name="TRAN_DESC" class="form-control" required=""></textarea><br>
                            
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm" name="btn_create_tt">Create</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                          </form>
                      </div>
                     

                    </div>
                  </div>
                </div>

                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">
                        <span>List of defined transaction types</span>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Tran Code</th>
                      <th>Tran Name</th>
                      <th>Decription</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $TRAN_TYPE_STATUS = "";
                    $tt_list = array();
                    $tt_list = FetchTransactionTypes($TRAN_TYPE_STATUS);
                    for ($i=0; $i < sizeof($tt_list); $i++) { 
                      $tt = array();
                      $tt = $tt_list[$i];
                      $RECORD_ID = $tt['RECORD_ID'];
                      $TRAN_TYPE_ID = $tt['TRAN_TYPE_ID'];
                      $TRAN_TYPE_NAME = $tt['TRAN_TYPE_NAME'];
                      $TRAN_DESC = $tt['TRAN_DESC'];
                      $CHRG_FLG = $tt['CHRG_FLG'];
                      $CHRG_EVENT_ID = $tt['CHRG_EVENT_ID'];
                      $CREATED_BY = $tt['CREATED_BY'];
                      $CREATED_ON = $tt['CREATED_ON'];
                      $LST_CHNG_BY = $tt['LST_CHNG_BY'];
                      $LST_CHNG_ON = $tt['LST_CHNG_ON'];
                      $TRAN_TYPE_STATUS = $tt['TRAN_TYPE_STATUS'];
                      
                      $id = "FTT".($i+1);
                      $target = "#".$id;
                      $form_id = "FORM_".$id;

                      $id2 = "FTT2".($i+1);
                      $target2 = "#".$id2;
                      $form_id2 = "FORM_".$id2;

                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;

                      $datatotransfer = $TRAN_TYPE_ID;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $TRAN_TYPE_ID; ?></td>
                        <td><?php echo $TRAN_TYPE_NAME; ?></td>
                        <td><?php echo $TRAN_DESC; ?></td>
                        <td><?php echo $TRAN_TYPE_STATUS; ?></td>
                        <td>
                          <?php
                          if ($TRAN_TYPE_STATUS=="ACTIVE") {
                            ?>
                            <a href="tt-types-ind?k=<?php echo $datatotransfer; ?>" class="btn btn-xs btn-primary">Manage</a>
                            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="<?php echo $target; ?>">Disable</button>
                            <div id="<?php echo $id; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="color: #333;">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Disable TranType</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Tran Type Code:</label><br>
                                        <?php echo $TRAN_TYPE_ID; ?><br><br>
                                        
                                        <label>Tran Type Name:</label><br>
                                        <?php echo $TRAN_TYPE_NAME; ?><br><br>
                                        
                                       
                                        <br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_disable_tt">Yes</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                      </form>
                                  </div>
                                 

                                </div>
                              </div>
                            </div>
                            <?php
                          }
                          if ($TRAN_TYPE_STATUS=="DISABLED") {
                            ?>
                            <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="<?php echo $target2; ?>">Re-enable</button>
                            <div id="<?php echo $id2; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Renable Tran Type</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id2; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Tran Type Code:</label><br>
                                        <?php echo $TRAN_TYPE_ID; ?><br><br>
                                        
                                        <label>Tran Type Name:</label><br>
                                        <?php echo $TRAN_TYPE_NAME; ?><br><br>
                                        
                                        
                                        <br>
                                        <button type="submit" class="btn btn-success btn-sm" name="btn_reenable_tt">Renable</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                      </form>
                                  </div>
                                 

                                </div>
                              </div>
                            </div>


                            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $target3; ?>">Delete</button>
                            <div id="<?php echo $id3; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Disable Tran Type</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id3; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Tran Type Code:</label><br>
                                        <?php echo $TRAN_TYPE_ID; ?><br><br>
                                        
                                        <label>Tran Type Name:</label><br>
                                        <?php echo $TRAN_TYPE_NAME; ?><br><br>
                     
                                        <strong>NOTE:</strong>
                                        This action cannot be undone.
                                        <br><br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_tt">Delete</button>
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
