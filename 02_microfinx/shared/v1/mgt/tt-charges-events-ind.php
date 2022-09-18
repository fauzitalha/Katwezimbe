<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$CHRG_EVNT_ID = mysql_real_escape_string($_GET['k']);
$tt = array();
$tt = FetchTransactionChargeEventsById($CHRG_EVNT_ID);
$RECORD_ID = $tt['RECORD_ID'];
$CHRG_EVNT_ID = $tt['CHRG_EVNT_ID'];
$CHRG_EVNT_NAME = $tt['CHRG_EVNT_NAME'];
$CHRG_EVNT_DESC = $tt['CHRG_EVNT_DESC'];
$CREATED_BY = $tt['CREATED_BY'];
$CREATED_ON = $tt['CREATED_ON'];
$LST_CHNG_BY = $tt['LST_CHNG_BY'];
$LST_CHNG_ON = $tt['LST_CHNG_ON'];
$TRAN_CHRG_STATUS = $tt['TRAN_CHRG_STATUS'];


# ... Adding Memebers to the group
if (isset($_POST['btn_save_chrg_items'])) {

  $CHRG_EVNT_ID = trim(mysql_real_escape_string($_POST['CHRG_EVNT_ID']));

  $tt_list = array();
  $tt_list = FetchChargesToAddToChargeEvent($CHRG_EVNT_ID);
  $CNT_ADDED = 0;
  $REC_ADDED = "";
  for ($i=0; $i < sizeof($tt_list); $i++) { 
    $tt = array();
    $tt = $tt_list[$i];
    $RECORD_ID = $tt['RECORD_ID'];
    $TRAN_CHRG_ID = $tt['TRAN_CHRG_ID'];
    $TRAN_CHRG_NAME = $tt['TRAN_CHRG_NAME'];
    $TRAN_CHRG_DESC = $tt['TRAN_CHRG_DESC'];
    $TRAN_CHRG_TYPE = $tt['TRAN_CHRG_TYPE'];
    $CORE_CR_ACCT_ID = $tt['CORE_CR_ACCT_ID'];
    $TRAN_NRRTN_PREFIX = $tt['TRAN_NRRTN_PREFIX'];
    $CREATED_BY = $tt['CREATED_BY'];
    $CREATED_ON = $tt['CREATED_ON'];
    $LST_CHNG_BY = $tt['LST_CHNG_BY'];
    $LST_CHNG_ON = $tt['LST_CHNG_ON'];
    $TRAN_CHRG_STATUS = $tt['TRAN_CHRG_STATUS'];

    if (isset($_POST[$TRAN_CHRG_ID])) {
      
      # ... SQL to save to DB
      $SS_TRAN_CHRG_ID = $_POST[$TRAN_CHRG_ID];
      $CREATED_BY = $_SESSION['UPR_USER_ID'];
      $CREATED_ON = GetCurrentDateTime();  


      $q = "INSERT INTO txn_charge_event_items(CHRG_EVNT_ID,TRAN_CHRG_ID,CREATED_BY,CREATED_ON) VALUES('$CHRG_EVNT_ID','$SS_TRAN_CHRG_ID','$CREATED_BY','$CREATED_ON')";
      $exec_response = array();
      $exec_response = ExecuteEntityInsert($q);
      $RESP = $exec_response["RESP"]; 
      $RECORD_ID = $exec_response["RECORD_ID"];
      if ($RESP=="EXECUTED") {

        if ($REC_ADDED=="") {
          $REC_ADDED = $SS_TRAN_CHRG_ID;
        }
        else {
          $REC_ADDED = $REC_ADDED."|".$SS_TRAN_CHRG_ID;
        }
        $CNT_ADDED++;
      }
    }
  }  // ... END LOOP


  # ... Log activity & Display Summary
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "CHRG_EVNT_ITEM";
  $ENTITY_ID_AFFECTED = $CHRG_EVNT_ID;
  $EVENT = "ADD";
  $EVENT_OPERATION = "CREATE_CHRG_EVENT_ITEM";
  $EVENT_RELATION = "txn_charge_event_items";
  $EVENT_RELATION_NO = $CHRG_EVNT_ID;
  $OTHER_DETAILS = $REC_ADDED;
  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


  $alert_type = "SUCCESS";
  $alert_msg = "SUCCESS: $CNT_ADDED item(s) added to charge event. Refreshing in 4 seconds.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  header("Refresh:4;");
}


# ... Delete group
if (isset($_POST['btn_delete_chrg_evnt_item'])) {

  $CHRG_EVNT_ID = $_POST['CHRG_EVNT_ID'];
  $TRAN_CHRG_ID = $_POST['TRAN_CHRG_ID'];

  $RECORD_ID = FetchChargesEventItemRecordId($CHRG_EVNT_ID, $TRAN_CHRG_ID);
  echo $RECORD_ID;

  $tt = array();
  $tt = FetchChargeEventItemByRecordId($RECORD_ID);
  $CREATED_BY = $tt['CREATED_BY'];
  $CREATED_ON = $tt['CREATED_ON'];
  $CHRG_EVENT_ITEM_STATUS = $tt['CHRG_EVENT_ITEM_STATUS'];



  # ... 02: Save Data to DataBase
  $q = "INSERT INTO txn_charge_event_items_deleted(RECORD_ID,CHRG_EVNT_ID,TRAN_CHRG_ID,CREATED_BY,CREATED_ON,CHRG_EVENT_ITEM_STATUS) 
        VALUES('$RECORD_ID','$CHRG_EVNT_ID','$TRAN_CHRG_ID','$CREATED_BY','$CREATED_ON','$CHRG_EVENT_ITEM_STATUS')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  //$RECORD_ID = $exec_response["RECORD_ID"];
  if ( $RESP=="EXECUTED" ) {
    
    $TABLE = "txn_charge_event_items";
    $TABLE_RECORD_ID = $RECORD_ID;
    $delete_response = array();
    $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
    $DEL_FLG = $delete_response["DEL_FLG"];
    $DEL_ROW = $delete_response["DEL_ROW"];

    if ($DEL_FLG=="Y") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "CHRG_EVNT_ITEM";
      $ENTITY_ID_AFFECTED = $RECORD_ID;
      $EVENT = "DELETE";
      $EVENT_OPERATION = "DELETE_CHRG_EVENT_ITEM";
      $EVENT_RELATION = "txn_charge_event_items -> txn_charge_event_items_deleted";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = $DEL_ROW;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "MESSAGE: Charge event item has been deleted completely. Refreshing in 5 seconds.";
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
    LoadDefaultCSSConfigurations("Charge Events", $APP_SMALL_LOGO); 

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
                <a href="tt-charge-events" class="btn btn-sm btn-dark pull-left">Back</a>
                <h2><strong>Charge Event: </strong><?php echo $CHRG_EVNT_NAME; ?></h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">  

                <table class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top" bgcolor="#EEE">
                      <th>Event Chrg Code</th>
                      <th>Event Chrg Name</th>
                      <th>Decription</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                      <td><?php echo $CHRG_EVNT_ID; ?></td>
                      <td><?php echo $CHRG_EVNT_NAME; ?></td>
                      <td><?php echo $CHRG_EVNT_DESC; ?></td>
                      <td><?php echo $TRAN_CHRG_STATUS; ?></td>
                    </tr>
                  </tbody>
                </table>


                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">
                        <span style="font-size: 16px;">List of Charges</span>
                        <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#crt_grp">Add Charges</button>
                        <div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel2">Add Charge to <strong>Charge Event: <?php echo $CHRG_EVNT_NAME; ?></strong></h4>
                              </div>
                              <div class="modal-body">
                                  <form id="ttu68euej" method="post">
                                    <table id="datatable2" width="100%" class="table table-striped table-bordered">
                                      <thead>
                                        <tr valign="top">
                                          <th colspan="4" bgcolor="#EEE">Select Charges to Add</th>
                                        </tr>
                                        <tr valign="top">
                                          <th>#</th>
                                          <th>Chrg Id</th>
                                          <th>Chrg Name</th>
                                          <th>Action</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php
                                        $TRAN_CHRG_STATUS = "";
                                        $tt_list = array();
                                        $tt_list = FetchChargesToAddToChargeEvent($CHRG_EVNT_ID);
                                        for ($i=0; $i < sizeof($tt_list); $i++) { 
                                          $tt = array();
                                          $tt = $tt_list[$i];
                                          $RECORD_ID = $tt['RECORD_ID'];
                                          $TRAN_CHRG_ID = $tt['TRAN_CHRG_ID'];
                                          $TRAN_CHRG_NAME = $tt['TRAN_CHRG_NAME'];
                                          $TRAN_CHRG_DESC = $tt['TRAN_CHRG_DESC'];
                                          $TRAN_CHRG_TYPE = $tt['TRAN_CHRG_TYPE'];
                                          $CORE_CR_ACCT_ID = $tt['CORE_CR_ACCT_ID'];
                                          $TRAN_NRRTN_PREFIX = $tt['TRAN_NRRTN_PREFIX'];
                                          $CREATED_BY = $tt['CREATED_BY'];
                                          $CREATED_ON = $tt['CREATED_ON'];
                                          $LST_CHNG_BY = $tt['LST_CHNG_BY'];
                                          $LST_CHNG_ON = $tt['LST_CHNG_ON'];
                                          $TRAN_CHRG_STATUS = $tt['TRAN_CHRG_STATUS'];

                                          ?>
                                          <tr valign="top">
                                            <td><?php echo ($i+1); ?>. </td>
                                            <td><?php echo $TRAN_CHRG_ID; ?></td>
                                            <td><?php echo $TRAN_CHRG_NAME; ?></td>
                                            <td>
                                              <input type="checkbox" id="<?php echo $TRAN_CHRG_ID; ?>" name="<?php echo $TRAN_CHRG_ID; ?>" value="<?php echo $TRAN_CHRG_ID; ?>">
                                            </td>
                                          </tr>

                                          <?php
                                        }

                                        ?>
                                      </tbody>
                                    </table>
                                    
                                    

                                    <br>
                                      <input type="hidden" id="CHRG_EVNT_ID" name="CHRG_EVNT_ID" value="<?php echo $CHRG_EVNT_ID; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm pull-right" name="btn_save_chrg_items">Submit</button>
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
                      <th>Chrg Code</th>
                      <th>Chrg Type</th>
                      <th>Chrg Name</th>
                      <th>Decription</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $TRAN_CHRG_STATUS = "";
                    $tt_list = array();
                    $tt_list = FetchChargesRelatedToChrgEventId($CHRG_EVNT_ID);
                    for ($i=0; $i < sizeof($tt_list); $i++) { 
                      $tt = array();
                      $tt = $tt_list[$i];
                      $RECORD_ID = $tt['RECORD_ID'];
                      $TRAN_CHRG_ID = $tt['TRAN_CHRG_ID'];
                      $TRAN_CHRG_NAME = $tt['TRAN_CHRG_NAME'];
                      $TRAN_CHRG_DESC = $tt['TRAN_CHRG_DESC'];
                      $TRAN_CHRG_TYPE = $tt['TRAN_CHRG_TYPE'];
                      $CORE_CR_ACCT_ID = $tt['CORE_CR_ACCT_ID'];
                      $TRAN_NRRTN_PREFIX = $tt['TRAN_NRRTN_PREFIX'];
                      $CREATED_BY = $tt['CREATED_BY'];
                      $CREATED_ON = $tt['CREATED_ON'];
                      $LST_CHNG_BY = $tt['LST_CHNG_BY'];
                      $LST_CHNG_ON = $tt['LST_CHNG_ON'];
                      $TRAN_CHRG_STATUS = $tt['TRAN_CHRG_STATUS'];

                      $TRAN_CHRG_TYPE_NAME = ($TRAN_CHRG_TYPE=="PP")? "Percentage" : "Fixed/Flat" ;

                      # ... Get Credit Core Charge Details
                      $SVNG_ACCT_ID = $CORE_CR_ACCT_ID;
                      $SVNGS_accountNo = "";
                      $SVNGS_clientName = "";
                      $SVNGS_savingsProductName = "";
                      $svngs_acct_details = array();
                      $response_msg = FetchSavingsAccountDetailsById($SVNG_ACCT_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $svngs_acct_details = $CORE_RESP;

                      if (isset($svngs_acct_details["accountNo"])) {
                        $SVNGS_accountNo = $svngs_acct_details["accountNo"];
                        $SVNGS_clientName = $svngs_acct_details["clientName"];
                        $SVNGS_savingsProductName = $svngs_acct_details["savingsProductName"];
                      }

                      # ... Getting Charge Amount if Percentage
                      $tt_tt_list = array();
                      $tt_tt_list = FetchTranChargeAmountsForChargeId($TRAN_CHRG_ID);
                      $CC_RECORD_ID="";
                      $CC_TRAN_CHRG_AMT_ID="";
                      $CC_TRAN_CHRG_ID="";
                      $CC_CHRG_LOW="";
                      $CC_CHRG_HIGH="";
                      $CC_CHRG_AMT="";
                      $CC_CREATED_BY="";
                      $CC_CREATED_ON="";
                      $CC_TRAN_CHRG_AMT_STATUS="";

                      $id = "FTT".($i+1);
                      $target = "#".$id;
                      $form_id = "FORM_".$id;

                      $id2 = "FTT2".($i+1);
                      $target2 = "#".$id2;
                      $form_id2 = "FORM_".$id2;


                      $datatotransfer = $TRAN_CHRG_ID;
                      ?>
                      <tr valign="top">


                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $TRAN_CHRG_ID; ?></td>
                        <td><?php echo $TRAN_CHRG_TYPE_NAME; ?></td>
                        <td><?php echo $TRAN_CHRG_NAME; ?></td>
                        <td><?php echo $TRAN_CHRG_DESC; ?></td>
                        <td><?php echo $TRAN_CHRG_STATUS; ?></td>
                        <td>
                            <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="<?php echo $target; ?>">View</button>
                            <div id="<?php echo $id; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Tran Charge Details</h4>
                                  </div>
                                  <div class="modal-body">
                                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE DETAILS -- -- -- -- -- -- -- -- -- -- -- -->
                                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE DETAILS -- -- -- -- -- -- -- -- -- -- -- -->
                                    <table class="table table-striped table-bordered">
                                      <thead>
                                        <tr valign="top" bgcolor="#EEE"><th colspan="5">Charge Details</th></tr>
                                        <tr valign="top" >
                                          <th>Chrg Code</th>
                                          <th>Chrg Type</th>
                                          <th>Chrg Name</th>
                                          <th>Decription</th>
                                          <th>Status</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr valign="top">
                                          <td><?php echo $TRAN_CHRG_ID; ?></td>
                                          <td><?php echo $TRAN_CHRG_TYPE_NAME; ?></td>
                                          <td><?php echo $TRAN_CHRG_NAME; ?></td>
                                          <td><?php echo $TRAN_CHRG_DESC; ?></td>
                                          <td><?php echo $TRAN_CHRG_STATUS; ?></td>
                                        </tr>
                                      </tbody>
                                    </table>

                                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE CREDIT ACCT DETAILS -- -- -- -- -- -- -- -- -- -- -->
                                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE CREDIT ACCT DETAILS -- -- -- -- -- -- -- -- -- -- -->
                                    <table class="table table-striped table-bordered">
                                      <thead>
                                        <tr valign="top" bgcolor="#EEE"><th colspan="3">Charge Credit Account Details</th></tr>
                                        <tr valign="top">
                                          <th>Account #</th>
                                          <th>Account Name</th>
                                          <th>Account Product</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr valign="top">
                                          <td><?php echo $SVNGS_accountNo; ?></td>
                                          <td><?php echo $SVNGS_clientName; ?></td>
                                          <td><?php echo $SVNGS_savingsProductName; ?></td>
                                        </tr>
                                      </tbody>
                                    </table>

                                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE AMOUNT DETAILS FOR TRAN CHRG --  -- -- -- -- -- -- -->
                                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE AMOUNT DETAILS FOR TRAN CHRG --  -- -- -- -- -- -- -->
                                    <?php
                                    // ... PERCENTAGE CHRGE
                                    if($TRAN_CHRG_TYPE=="PP"){
                                      for ($cc=0; $cc < sizeof($tt_tt_list); $cc++) { 
                                        $tt = array();
                                        $tt = $tt_tt_list[$cc];
                                        $CC_RECORD_ID = $tt['RECORD_ID'];
                                        $CC_TRAN_CHRG_AMT_ID = $tt['TRAN_CHRG_AMT_ID'];
                                        $CC_TRAN_CHRG_ID = $tt['TRAN_CHRG_ID'];
                                        $CC_CHRG_LOW = $tt['CHRG_LOW'];
                                        $CC_CHRG_HIGH = $tt['CHRG_HIGH'];
                                        $CC_CHRG_AMT = $tt['CHRG_AMT'];
                                        $CC_CREATED_BY = $tt['CREATED_BY'];
                                        $CC_CREATED_ON = $tt['CREATED_ON'];
                                        $CC_TRAN_CHRG_AMT_STATUS = $tt['TRAN_CHRG_AMT_STATUS'];
                                      }
                                      ?>
                                      <table class="table table-bordered">
                                        <thead>
                                          <tr valign="top" bgcolor="#EEE"><th colspan="3" bgcolor="#EEE">Charge Percentage</th></tr>
                                          <tr valign="top">
                                            <th width="25%">Amt_Chrg_Id</th>
                                            <th>Percentage</th>
                                            <th>Status</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr valign="top">
                                            <td><?php echo $CC_TRAN_CHRG_AMT_ID; ?></td>
                                            <td><?php echo $CC_CHRG_AMT."%"; ?></td>
                                            <td><?php echo $TRAN_CHRG_STATUS; ?></td>
                                          </tr>
                                        </tbody>                                      
                                      </table>
                                      <?php
                                    }

                                    // ... FIXED/FLAT CHRGE
                                    if($TRAN_CHRG_TYPE=="FF"){
                                      ?>
                                      <table class="table table-striped table-bordered">
                                        <thead>
                                          <tr valign="top" bgcolor="#EEE"><th colspan="5">Transaction Charge Amounts (Flat/Tiered)</th></tr>
                                          <tr valign="top" >
                                            <th>#</th>
                                            <th>Chrg Block Code</th>
                                            <th>Lower Limit</th>
                                            <th>Upper Limit</th>
                                            <th>Charge Amount</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php
                                          for ($dd=0; $dd < sizeof($tt_tt_list); $dd++) { 
                                            $tt = array();
                                            $tt = $tt_tt_list[$dd];
                                            $CC_RECORD_ID = $tt['RECORD_ID'];
                                            $CC_TRAN_CHRG_AMT_ID = $tt['TRAN_CHRG_AMT_ID'];
                                            $CC_TRAN_CHRG_ID = $tt['TRAN_CHRG_ID'];
                                            $CC_CHRG_LOW = $tt['CHRG_LOW'];
                                            $CC_CHRG_HIGH = $tt['CHRG_HIGH'];
                                            $CC_CHRG_AMT = $tt['CHRG_AMT'];
                                            $CC_CREATED_BY = $tt['CREATED_BY'];
                                            $CC_CREATED_ON = $tt['CREATED_ON'];
                                            $CC_TRAN_CHRG_AMT_STATUS = $tt['TRAN_CHRG_AMT_STATUS'];
                                            ?>
                                            <tr valign="top">
                                              <td><?php echo ($dd+1); ?>. </td>
                                              <td><?php echo $CC_TRAN_CHRG_AMT_ID; ?></td>
                                              <td><?php echo number_format($CC_CHRG_LOW); ?></td>
                                              <td><?php echo number_format($CC_CHRG_HIGH); ?></td>
                                              <td><?php echo number_format($CC_CHRG_AMT); ?></td>
                                            </tr>
                                            <?php
                                          }


                                          ?>
                                          
                                        </tbody>
                                      </table>
                                      <?php
                                    }
                                    ?>
                                  </div>
                                 

                                </div>
                              </div>
                            </div>


                            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $target2; ?>">Delete</button>
                            <div id="<?php echo $id2; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Delete Charge from Charge Event</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="<?php echo $form_id2; ?>" method="post">
                                        <input type="hidden" id="CHRG_EVNT_ID" name="CHRG_EVNT_ID" value="<?php echo $CHRG_EVNT_ID; ?>">
                                        <input type="hidden" id="TRAN_CHRG_ID" name="TRAN_CHRG_ID" value="<?php echo $TRAN_CHRG_ID; ?>">
                                        
                                        <label>Charge Event Code:</label><br>
                                        <?php echo $CHRG_EVNT_ID; ?><br><br>
                                        
                                        <label>Charge Event Name:</label><br>
                                        <?php echo $CHRG_EVNT_NAME; ?><br><br>
                                        
                                        <label>Charge Code:</label><br>
                                        <?php echo $TRAN_CHRG_ID; ?><br><br>
                                        
                                        <label>Charge Name:</label><br>
                                        <?php echo $TRAN_CHRG_NAME; ?><br><br>
                                        
                                        <strong>NOTE:</strong>
                                        This action cannot be undone.
                                        <br><br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_chrg_evnt_item">Delete</button>
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
