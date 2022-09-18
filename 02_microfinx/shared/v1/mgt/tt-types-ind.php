<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$TRAN_TYPE_ID = mysql_real_escape_string($_GET['k']);
$tt = array();
$tt = FetchTransactionTypeById($TRAN_TYPE_ID);
$RECORD_ID = $tt['RECORD_ID'];
//$TRAN_TYPE_ID = $tt['TRAN_TYPE_ID'];
$TRAN_TYPE_NAME = $tt['TRAN_TYPE_NAME'];
$TRAN_DESC = $tt['TRAN_DESC'];
$CHRG_FLG = $tt['CHRG_FLG'];
$CHRG_EVENT_ID = $tt['CHRG_EVENT_ID'];
$CREATED_BY = $tt['CREATED_BY'];
$CREATED_ON = $tt['CREATED_ON'];
$LST_CHNG_BY = $tt['LST_CHNG_BY'];
$LST_CHNG_ON = $tt['LST_CHNG_ON'];
$TRAN_TYPE_STATUS = $tt['TRAN_TYPE_STATUS'];


# ... Get Charge Event Details
$E_RECORD_ID = "";
$E_CHRG_EVNT_ID = "";
$E_CHRG_EVNT_NAME = "";
$E_CHRG_EVNT_DESC = "";
$E_CREATED_BY = "";
$E_CREATED_ON = "";
$E_LST_CHNG_BY = "";
$E_LST_CHNG_ON = "";
$E_TRAN_CHRG_STATUS = "";

$tt2 = array();
$tt2 = FetchTransactionChargeEventsById($CHRG_EVENT_ID);
if (isset($tt2['RECORD_ID'])) {
	$E_RECORD_ID = $tt2['RECORD_ID'];
	$E_CHRG_EVNT_ID = $tt2['CHRG_EVNT_ID'];
	$E_CHRG_EVNT_NAME = $tt2['CHRG_EVNT_NAME'];
	$E_CHRG_EVNT_DESC = $tt2['CHRG_EVNT_DESC'];
	$E_CREATED_BY = $tt2['CREATED_BY'];
	$E_CREATED_ON = $tt2['CREATED_ON'];
	$E_LST_CHNG_BY = $tt2['LST_CHNG_BY'];
	$E_LST_CHNG_ON = $tt2['LST_CHNG_ON'];
	$E_TRAN_CHRG_STATUS = $tt2['TRAN_CHRG_STATUS'];
}


# ... Attach Charge Event ID ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .
if (isset($_POST['btn_edit_chrg_evnt_conf'])) {

  $RECORD_ID = trim($_POST['RECORD_ID']);
  $TRAN_TYPE_ID = trim($_POST['TRAN_TYPE_ID']);
	$CHRG_FLG = trim(mysql_real_escape_string($_POST['CHRG_FLG']));
	$CHRG_EVENT_ID = trim(mysql_real_escape_string($_POST['CHRG_EVENT_ID']));

  // ... SQL
  $q = "UPDATE txn_types SET CHRG_FLG='$CHRG_FLG', CHRG_EVENT_ID='$CHRG_EVENT_ID' WHERE TRAN_TYPE_ID='$TRAN_TYPE_ID' AND RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "TRAN_TYPE";
    $ENTITY_ID_AFFECTED = $TRAN_TYPE_ID;
    $EVENT = "ATTACH_CHARGE_EVENT_ID";
    $EVENT_OPERATION = "ATTACH_CHARGE_EVENT_ID";
    $EVENT_RELATION = "txn_types";
    $EVENT_RELATION_NO = $TRAN_TYPE_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    # ... Send System Response
    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: Credit account has been added. Refreshing in 5 seconds.";
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
    LoadDefaultCSSConfigurations("Tran Maintenance", $APP_SMALL_LOGO); 

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
              	<a href="tt-types" class="btn btn-sm btn-dark pull-left">Back</a>
                <h2><strong>Tran Maintenance: </strong><?php echo $TRAN_TYPE_NAME; ?></h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
              	<!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- TRAN DETAILS -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -->
              	<!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- TRAN DETAILS -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -->
              	<table class="table table-striped table-bordered">
                  <thead>
                  	<tr valign="top" bgcolor="#EEE"><th colspan="4">Tran Details</th></tr>
                    <tr valign="top">
                      <th>Tran Code</th>
                      <th>Tran Name</th>
                      <th>Decription</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                      <td><?php echo $TRAN_TYPE_ID; ?></td>
                      <td><?php echo $TRAN_TYPE_NAME; ?></td>
                      <td><?php echo $TRAN_DESC; ?></td>
                      <td><?php echo $TRAN_TYPE_STATUS; ?></td>
                    </tr>
                  </tbody>
                </table>

                <!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE EVENT DETAILS -- -- -- -- -- -- -- -- -- -- -- -- -- -->
                <!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE EVENT DETAILS -- -- -- -- -- -- -- -- -- -- -- -- -- -->
                <?php
                $TRAN_CHRG_STATUS = "";
                $tt_tt_list = array();
                $tt_tt_list = FetchTransactionChargeEvents($TRAN_CHRG_STATUS);
                ?>
	              <table class="table table-bordered">
	            		<thead>
	            			<tr valign="top" bgcolor="#EEE"><th colspan="4" bgcolor="#EEE">Tran Charge Event
	                		<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#cccc">Amend Charge Event</button>
			                <div id="cccc" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
			                  <div class="modal-dialog modal-sm">
			                    <div class="modal-content">

			                      <div class="modal-header">
			                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
			                        </button>
			                        <h4 class="modal-title" id="myModalLabel2">Change Event Configuration</h4>
			                      </div>
			                      <div class="modal-body">
			                        <form id="dgdfs23hshs" method="post">
			                        	<input type="hidden" id="RECORD_ID" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>">
			                        	<input type="hidden" id="TRAN_TYPE_ID" name="TRAN_TYPE_ID" value="<?php echo $TRAN_TYPE_ID; ?>">

		                            <label>Apply Charge to this Transaction:</label><br>
		                            <select id="CHRG_FLG" name="CHRG_FLG" class="form-control" required="">
		                            	<option value="">--------------</option>
		                            	<?php
		                            	if ($CHRG_FLG=="NN") {
		                            		?>
		                            		<option value="NN" selected="selected">No</option>
		                            		<option value="YY">Yes</option>
		                            		<?php
		                            	}
		                            	if ($CHRG_FLG=="YY") {
		                            		?>
		                            		<option value="NN">No</option>
		                            		<option value="YY" selected="selected">Yes</option>
		                            		<?php
		                            	}
		                            	?>
		                            </select><br>


		                            <label>Charge Event:</label><br>
		                            <select id="CHRG_EVENT_ID" name="CHRG_EVENT_ID" class="form-control">
		                            	<option value="">--------------</option>
		                            	<?php
			                            for ($xx=0; $xx < sizeof($tt_tt_list); $xx++) { 
							                      $tt = array();
							                      $tt = $tt_tt_list[$xx];
							                      $DB_CHRG_EVNT_ID = $tt['CHRG_EVNT_ID'];
							                      $DB_CHRG_EVNT_NAME = $tt['CHRG_EVNT_NAME'];

							                      if ($DB_CHRG_EVNT_ID==$CHRG_EVENT_ID) {
							                      	?>
								                     	<option selected="selected" value="<?php echo $DB_CHRG_EVNT_ID; ?>"><?php echo $DB_CHRG_EVNT_NAME; ?></option>
								                     	<?php
							                      }
							                      else{
							                      	?>
								                     	<option value="<?php echo $DB_CHRG_EVNT_ID; ?>"><?php echo $DB_CHRG_EVNT_NAME; ?></option>
								                     	<?php
							                      }
							                    }
			                            ?>
		                            </select>
		                            

		                            
		                            <br>
		                            <button type="submit" class="btn btn-primary btn-sm" name="btn_edit_chrg_evnt_conf">Save</button>
		                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
	                            </form> 
			                      </div>
			                    </div>
			                  </div>
			                </div>
	              		</th></tr>
	                  <tr valign="top">
	                    <th width="25%">Charge Event Id</th>
	                    <th>Charge Event Name</th>
	                    <th>Charge Event Description</th>
	                    <th>Status</th>
	                  </tr>
	            		</thead>
	            		<tbody>
	            			<tr valign="top">
	                    <td><?php echo $E_CHRG_EVNT_ID; ?></td>
	                    <td><?php echo $E_CHRG_EVNT_NAME; ?></td>
	                    <td><?php echo $E_CHRG_EVNT_DESC; ?></td>
	                    <td><?php echo $E_TRAN_CHRG_STATUS; ?></td>
	                  </tr>
	            		</tbody>
	              </table>

	              <?php
	              if ($CHRG_FLG=="YY") {
	              	?>
	              	<table class="table table-striped table-bordered">
	                  <thead>
	                    <tr valign="top">
	                      <th colspan="7" bgcolor="#EEE">
	                        <span>List of Charges</span>
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
	                    $tt_list = FetchChargesRelatedToChrgEventId($E_CHRG_EVNT_ID);
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
	                           
	                        </td>
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
