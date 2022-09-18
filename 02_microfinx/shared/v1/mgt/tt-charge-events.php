<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");


# ... Create New Group
if (isset($_POST['btn_create_te'])) {
  $CHRG_EVNT_NAME = trim(mysql_real_escape_string($_POST['CHRG_EVNT_NAME']));
  $CHRG_EVNT_DESC = trim(mysql_real_escape_string($_POST['CHRG_EVNT_DESC']));
  $CREATED_BY = $_SESSION['UPR_USER_ID'];
  $CREATED_ON = GetCurrentDateTime(); 
  $TRAN_CHRG_STATUS = "ACTIVE";

  # ... Create tran_record;
  $q = "INSERT INTO txn_charge_events(CHRG_EVNT_NAME,CHRG_EVNT_DESC,CREATED_BY,CREATED_ON,TRAN_CHRG_STATUS) 
        VALUES('$CHRG_EVNT_NAME','$CHRG_EVNT_DESC','$CREATED_BY','$CREATED_ON','$TRAN_CHRG_STATUS')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];

  # ... Process Entity System ID (Group ID)
  $id_prefix = "TCE";
  $id_len = 7;
  $id_record_id = $RECORD_ID;
  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
  $CHRG_EVNT_ID = $ENTITY_ID;

  # ... Updating the role id
  $q2 = "UPDATE txn_charge_events SET CHRG_EVNT_ID='$CHRG_EVNT_ID' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "TRAN_CHARGE_EVENT";
    $ENTITY_ID_AFFECTED = $CHRG_EVNT_ID;
    $EVENT = "CREATE";
    $EVENT_OPERATION = "CREATE_NEW_TRAN_CHARGE_EVENT";
    $EVENT_RELATION = "txn_charge_events";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Transaction Charge Event has been created. Refreshing in 4 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:4;");
  }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Tran Charge Events", $APP_SMALL_LOGO); 

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
                <h2>Transaction Charge Events</h2>
                <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#crt_grp">New Chrg Event</button>
                <div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">

                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel2">Create New Charge Event</h4>
                      </div>
                      <div class="modal-body">
                          <form id="dddqwqqw2" method="post">
                            
                            <label>Charge Event Name:</label><br>
                            <input type="text" id="CHRG_EVNT_NAME" name="CHRG_EVNT_NAME" class="form-control" required=""><br>

                            <label>Charge Event Description:</label><br>
                            <textarea id="CHRG_EVNT_DESC" name="CHRG_EVNT_DESC" class="form-control" required=""></textarea><br>
                            
                         

                            <br>
                            <button type="submit" class="btn btn-primary btn-sm" name="btn_create_te">Create</button>
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
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
                      <th>Event Chrg Code</th>
                      <th>Event Chrg Name</th>
                      <th>Decription</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $TRAN_CHRG_STATUS = "";
                    $tt_list = array();
                    $tt_list = FetchTransactionChargeEvents($TRAN_CHRG_STATUS);
                    for ($i=0; $i < sizeof($tt_list); $i++) { 
                      $tt = array();
                      $tt = $tt_list[$i];
                      $RECORD_ID = $tt['RECORD_ID'];
                      $CHRG_EVNT_ID = $tt['CHRG_EVNT_ID'];
                      $CHRG_EVNT_NAME = $tt['CHRG_EVNT_NAME'];
                      $CHRG_EVNT_DESC = $tt['CHRG_EVNT_DESC'];
                      $CREATED_BY = $tt['CREATED_BY'];
                      $CREATED_ON = $tt['CREATED_ON'];
                      $LST_CHNG_BY = $tt['LST_CHNG_BY'];
                      $LST_CHNG_ON = $tt['LST_CHNG_ON'];
                      $TRAN_CHRG_STATUS = $tt['TRAN_CHRG_STATUS'];



                      $id = "FTT".($i+1);
                      $target = "#".$id;
                      $form_id = "FORM_".$id;

                      $id2 = "FTT2".($i+1);
                      $target2 = "#".$id2;
                      $form_id2 = "FORM_".$id2;

                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;

                      $datatotransfer = $CHRG_EVNT_ID;
                      ?>
                      <tr valign="top">


                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $CHRG_EVNT_ID; ?></td>
                        <td><?php echo $CHRG_EVNT_NAME; ?></td>
                        <td><?php echo $CHRG_EVNT_DESC; ?></td>
                        <td><?php echo $TRAN_CHRG_STATUS; ?></td>
                        <td>
                          <?php
                          if ($TRAN_CHRG_STATUS=="ACTIVE") {
                            ?>
                            <a href="tt-charges-events-ind?k=<?php echo $datatotransfer; ?>" class="btn btn-xs btn-primary">Manage</a>
                            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="<?php echo $target; ?>">Disable</button>
                            <div id="<?php echo $id; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="color: #333;">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Disable Tran Event Chrg</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Event Charge Code:</label><br>
                                        <?php echo $CHRG_EVNT_ID; ?><br><br>
                                        
                                        <label>Charge Name:</label><br>
                                        <?php echo $CHRG_EVNT_NAME; ?><br><br>
                                        
                                       
                                        <br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_disable_te">Yes</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                      </form>
                                  </div>
                                 

                                </div>
                              </div>
                            </div>
                            <?php
                          }
                          if ($TRAN_CHRG_STATUS=="DISABLED") {
                            ?>
                            <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="<?php echo $target2; ?>">Re-enable</button>
                            <div id="<?php echo $id2; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Renable Event Charge</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id2; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Event Charge Code:</label><br>
                                        <?php echo $CHRG_EVNT_ID; ?><br><br>
                                        
                                        <label>Charge Name:</label><br>
                                        <?php echo $CHRG_EVNT_NAME; ?><br><br>
                                        
                                        
                                        
                                        <br>
                                        <button type="submit" class="btn btn-success btn-sm" name="btn_reenable_te">Renable</button>
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
                                    <h4 class="modal-title" id="myModalLabel2">Disable Event Charge</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id3; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Event Charge Code:</label><br>
                                        <?php echo $CHRG_EVNT_ID; ?><br><br>
                                        
                                        <label>Charge Name:</label><br>
                                        <?php echo $CHRG_EVNT_NAME; ?><br><br>
                                        
                                        <strong>NOTE:</strong>
                                        This action cannot be undone.
                                        <br><br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_te">Delete</button>
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
