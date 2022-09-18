<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Updateing the Address Book
if (isset($_POST['btn_updt_addrss_bk'])) {
  
  $address_book = array();

  # ... 01: Get Users, Groups & Customer Lists
  $user_list = array();
  $cust_list = array();
  $group_list = array();
  $user_list = FetchSysUserListAddressBook("ACTIVE");
  $cust_list = FetchCustListAddressBook("ACTIVE");
  $group_list = FetchGroupListAddressBook("ACTIVE");

  # ... 02: Format for User Address Book
  $user_addrs_bk = array();
  for ($i=0; $i < sizeof($user_list); $i++) { 
    $usr = array();
    $usr = $user_list[$i];
    $USER_ID = $usr['USER_ID'];
    $USER_CORE_ID = $usr['USER_CORE_ID'];

    $response_msg = FetchUserDetailsFromCore($USER_CORE_ID, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];
    $firstname = $CORE_RESP["firstname"];
    $lastname = $CORE_RESP["lastname"];
    $full_name = $firstname." ".$lastname;

    $addrss = array();
    $ADDRESS_ENTITY_TYPE = "USER";
    $ADDRESS_ENTITY_ID = $USER_ID;
    $ADDRESS_ENTITY_NAME = $full_name;
    $ADDRESS_ADDED_DATE = GetCurrentDateTime();
    $ADDRESS_STATUS = "ACTIVE";

    $addrss["ADDRESS_ENTITY_TYPE"] = $ADDRESS_ENTITY_TYPE;
    $addrss["ADDRESS_ENTITY_ID"] = $ADDRESS_ENTITY_ID;
    $addrss["ADDRESS_ENTITY_NAME"] = $ADDRESS_ENTITY_NAME;
    $addrss["ADDRESS_ADDED_DATE"] = $ADDRESS_ADDED_DATE;
    $addrss["ADDRESS_STATUS"] = $ADDRESS_STATUS;

    $user_addrs_bk[$i] = $addrss;
  }

  # ... 03: Format for Cust Address Book
  $cust_addrs_bk = array();
  for ($i=0; $i < sizeof($cust_list); $i++) { 
    $cust = array();
    $cust = $cust_list[$i];
    $CUST_ID = $cust['CUST_ID'];
    $CUST_CORE_ID = $cust['CUST_CORE_ID'];

    $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];
    $displayName = $CORE_RESP["displayName"];

    $addrss = array();
    $ADDRESS_ENTITY_TYPE = "CUSTOMER";
    $ADDRESS_ENTITY_ID = $CUST_ID;
    $ADDRESS_ENTITY_NAME = $displayName;
    $ADDRESS_ADDED_DATE = GetCurrentDateTime();
    $ADDRESS_STATUS = "ACTIVE";

    $addrss["ADDRESS_ENTITY_TYPE"] = $ADDRESS_ENTITY_TYPE;
    $addrss["ADDRESS_ENTITY_ID"] = $ADDRESS_ENTITY_ID;
    $addrss["ADDRESS_ENTITY_NAME"] = $ADDRESS_ENTITY_NAME;
    $addrss["ADDRESS_ADDED_DATE"] = $ADDRESS_ADDED_DATE;
    $addrss["ADDRESS_STATUS"] = $ADDRESS_STATUS;

    $cust_addrs_bk[$i] = $addrss;
  }

  # ... 04: Format for Group Address Book
  $group_addrs_bk = array();
  for ($i=0; $i < sizeof($group_list); $i++) {

    $grp = array();
    $grp = $group_list[$i];
    $GRP_ID = $grp['GRP_ID'];
    $GRP_NAME = $grp['GRP_NAME'];

    $addrss = array();
    $ADDRESS_ENTITY_TYPE = "GROUP";
    $ADDRESS_ENTITY_ID = $GRP_ID;
    $ADDRESS_ENTITY_NAME = $GRP_NAME;
    $ADDRESS_ADDED_DATE = GetCurrentDateTime();
    $ADDRESS_STATUS = "ACTIVE";

    $addrss["ADDRESS_ENTITY_TYPE"] = $ADDRESS_ENTITY_TYPE;
    $addrss["ADDRESS_ENTITY_ID"] = $ADDRESS_ENTITY_ID;
    $addrss["ADDRESS_ENTITY_NAME"] = $ADDRESS_ENTITY_NAME;
    $addrss["ADDRESS_ADDED_DATE"] = $ADDRESS_ADDED_DATE;
    $addrss["ADDRESS_STATUS"] = $ADDRESS_STATUS;

    $group_addrs_bk[$i] = $addrss;
  }

  # ... 05: Add to Address Book
  $address_book = array_merge($user_addrs_bk, $cust_addrs_bk, $group_addrs_bk);
  $CNT_ADDRESS = 0;
  for ($i=0; $i < sizeof($address_book); $i++) { 
    $addrss = array();
    $addrss = $address_book[$i];

    $ADDRESS_ENTITY_TYPE = $addrss["ADDRESS_ENTITY_TYPE"];
    $ADDRESS_ENTITY_ID = $addrss["ADDRESS_ENTITY_ID"];
    $ADDRESS_ENTITY_NAME = $addrss["ADDRESS_ENTITY_NAME"];
    $ADDRESS_ADDED_DATE = GetCurrentDateTime();
    $ADDRESS_STATUS = $addrss["ADDRESS_STATUS"];

    # ... SQL
    $q = "INSERT INTO notification_addressbook(ADDRESS_ENTITY_TYPE, ADDRESS_ENTITY_ID, ADDRESS_ENTITY_NAME, ADDRESS_ADDED_DATE,ADDRESS_STATUS) VALUES('$ADDRESS_ENTITY_TYPE','$ADDRESS_ENTITY_ID','$ADDRESS_ENTITY_NAME','$ADDRESS_ADDED_DATE','$ADDRESS_STATUS');";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    if ($exec_response["RESP"] == "EXECUTED") {
      $CNT_ADDRESS++; 
    }
  }

  # ... 06: Displaying Results
  $alert_type = "SUCCESS";
  $alert_msg = "$CNT_ADDRESS address(es) have been added to address book. Refreshing in 5 seconds.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

  header("Refresh:5;");
}

# ... Disable Record froma ddress book
if (isset($_POST['btn_disable_address'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];
  $ADDRESS_STATUS = "DISABLED";

  # ... SQL
  $q = "UPDATE notification_addressbook SET ADDRESS_STATUS='$ADDRESS_STATUS' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "ADDRESS_BOOK";
    $ENTITY_ID_AFFECTED = $RECORD_ID;
    $EVENT = "DISABLING";
    $EVENT_OPERATION = "DISABLE_ADDRESS_FROM_ADDRESSBOOK";
    $EVENT_RELATION = "notification_addressbook";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "RECORD_ID: ".$RECORD_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "MESSAGE: Address has been disabled. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }
}

# ... Renable Record froma ddress book
if (isset($_POST['btn_reenable_address'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];
  $ADDRESS_STATUS = "ACTIVE";

  # ... SQL
  $q = "UPDATE notification_addressbook SET ADDRESS_STATUS='$ADDRESS_STATUS' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "ADDRESS_BOOK";
    $ENTITY_ID_AFFECTED = $RECORD_ID;
    $EVENT = "RE_ENABLING";
    $EVENT_OPERATION = "RE-ENABLING_ADDRESS_IN_ADDRESSBOOK";
    $EVENT_RELATION = "notification_addressbook";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = "RECORD_ID: ".$RECORD_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Address has been re-enabled. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }
}

# ... Delete Item from Address Book
if (isset($_POST['btn_delete_address'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];

  $address = array();
  $address = FetchAddressFromAddressBook($RECORD_ID);
  $ADDRESS_ENTITY_TYPE = $address['ADDRESS_ENTITY_TYPE'];
  $ADDRESS_ENTITY_ID = $address['ADDRESS_ENTITY_ID'];
  $ADDRESS_ENTITY_NAME = $address['ADDRESS_ENTITY_NAME'];
  $ADDRESS_ADDED_DATE = $address['ADDRESS_ADDED_DATE'];
  $ADDRESS_STATUS = $address['ADDRESS_STATUS'];

  # ... 02: Save Data to DataBase
  $q = "INSERT INTO notification_addressbook_deleted(RECORD_ID,ADDRESS_ENTITY_TYPE, ADDRESS_ENTITY_ID, ADDRESS_ENTITY_NAME, ADDRESS_ADDED_DATE,ADDRESS_STATUS) VALUES('$RECORD_ID','$ADDRESS_ENTITY_TYPE','$ADDRESS_ENTITY_ID','$ADDRESS_ENTITY_NAME','$ADDRESS_ADDED_DATE','$ADDRESS_STATUS');";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];
  if ( $RESP=="EXECUTED" ) {
    
    $TABLE = "notification_addressbook";
    $TABLE_RECORD_ID = $_POST['ADD_RECORD_ID'];
    $delete_response = array();
    $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
    $DEL_FLG = $delete_response["DEL_FLG"];
    $DEL_ROW = $delete_response["DEL_ROW"];

    if ($DEL_FLG=="Y") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "ADDRESS_BOOK";
      $ENTITY_ID_AFFECTED = $_POST['ADD_RECORD_ID'];
      $EVENT = "DELETING";
      $EVENT_OPERATION = "DELETING_ADDRESS_IN_ADDRESSBOOK";
      $EVENT_RELATION = "notification_addressbook -> notification_addressbook_deleted";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = $DEL_ROW;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: Address has been deleted from the address book completely. Refreshing in 5 seconds.";
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
    LoadDefaultCSSConfigurations("Main Control", $APP_SMALL_LOGO); 

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
                <h2>Address Book</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         

                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">
                        <form method="post" id="addd">
                          <span style="font-size: 16px;">Notification Address List</span>
                          <button type="submit" class="btn btn-sm btn-dark pull-right" name="btn_updt_addrss_bk">Update Address Book</button>
                        </form>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Address Name</th>
                      <th>Address Type</th>
                      <th>Date Added</th>
                      <th>Address Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $ADDRESS_STATUS = "";
                    $address_book = array();
                    $address_book = FetchAddressBook($ADDRESS_STATUS);
                    for ($i=0; $i < sizeof($address_book); $i++) { 
                      $address = array();
                      $address = $address_book[$i];
                      $RECORD_ID = $address['RECORD_ID'];
                      $ADDRESS_ENTITY_TYPE = $address['ADDRESS_ENTITY_TYPE'];
                      $ADDRESS_ENTITY_ID = $address['ADDRESS_ENTITY_ID'];
                      $ADDRESS_ENTITY_NAME = $address['ADDRESS_ENTITY_NAME'];
                      $ADDRESS_ADDED_DATE = $address['ADDRESS_ADDED_DATE'];
                      $ADDRESS_STATUS = $address['ADDRESS_STATUS'];

                      
                      $id = "FTT".($i+1);
                      $target = "#".$id;
                      $form_id = "FORM_".$id;

                      $id2 = "FTT2".($i+1);
                      $target2 = "#".$id2;
                      $form_id2 = "FORM_".$id2;

                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $ADDRESS_ENTITY_NAME; ?></td>
                        <td><?php echo $ADDRESS_ENTITY_TYPE; ?></td>
                        <td><?php echo $ADDRESS_ADDED_DATE; ?></td>
                        <td><?php echo $ADDRESS_STATUS; ?></td>
                        <td>
                          <?php
                          if ($ADDRESS_STATUS=="ACTIVE") {
                            ?>
                            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="<?php echo $target; ?>">Disable</button>
                            <div id="<?php echo $id; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="color: #333;">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Disable Address Account</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Address Name:</label><br>
                                        <?php echo $ADDRESS_ENTITY_NAME; ?><br><br>
                                        
                                        <label>Address Type:</label><br>
                                        <?php echo $ADDRESS_ENTITY_TYPE; ?><br><br>
                                        

                                        <br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_disable_address">Yes</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                      </form>
                                  </div>
                                 

                                </div>
                              </div>
                            </div>
                            <?php
                          }
                          if ($ADDRESS_STATUS=="DISABLED") {
                            ?>
                            <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="<?php echo $target2; ?>">Re-enable</button>
                            <div id="<?php echo $id2; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="color: #333;">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Renable Address Account</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id2; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Address Name:</label><br>
                                        <?php echo $ADDRESS_ENTITY_NAME; ?><br><br>
                                        
                                        <label>Address Type:</label><br>
                                        <?php echo $ADDRESS_ENTITY_TYPE; ?><br><br>
                                        

                                        <br>
                                        <button type="submit" class="btn btn-success btn-sm" name="btn_reenable_address">Renable</button>
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
                                    <h4 class="modal-title" id="myModalLabel2">Disable Address Account</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id3; ?>" method="post">
                                        <input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        
                                        <label>Address Name:</label><br>
                                        <?php echo $ADDRESS_ENTITY_NAME; ?><br><br>
                                        
                                        <label>Address Type:</label><br>
                                        <?php echo $ADDRESS_ENTITY_TYPE; ?><br><br>
                                        
                                        <strong>NOTE:</strong>
                                        This action cannot be undone.
                                        <br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_address">Delete</button>
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
