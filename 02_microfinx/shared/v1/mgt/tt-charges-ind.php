<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$TRAN_CHRG_ID = mysql_real_escape_string($_GET['k']);
$tt = array();
$tt = FetchTransactionChargesById($TRAN_CHRG_ID);
$RECORD_ID = $tt['RECORD_ID'];
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
$TRAN_CHRG_TYPE_NAME = ($TRAN_CHRG_TYPE=="PP")? "Percentage" : "Fixed/Flat";

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
$tt_list = array();
$tt_list = FetchTranChargeAmountsForChargeId($TRAN_CHRG_ID);
$CC_RECORD_ID="";
$CC_TRAN_CHRG_AMT_ID="";
$CC_TRAN_CHRG_ID="";
$CC_CHRG_LOW="";
$CC_CHRG_HIGH="";
$CC_CHRG_AMT="";
$CC_CREATED_BY="";
$CC_CREATED_ON="";
$CC_TRAN_CHRG_AMT_STATUS="";

if ($TRAN_CHRG_TYPE=="PP") {
	for ($i=0; $i < sizeof($tt_list); $i++) { 
		$tt = array();
		$tt = $tt_list[$i];
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
}

# ... Attach Credit Charge Account ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .
if (isset($_POST['btn_attch_acct'])) {
  $TRAN_CHRG_ID = trim($_POST['TRAN_CHRG_ID']);
  $CORE_CR_ACCT_ID = trim($_POST['CORE_CR_ACCT_ID']);

  // ... SQL
  $q = "UPDATE txn_charges SET CORE_CR_ACCT_ID='$CORE_CR_ACCT_ID' WHERE TRAN_CHRG_ID='$TRAN_CHRG_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "TRAN_CHARGE";
    $ENTITY_ID_AFFECTED = $TRAN_CHRG_ID;
    $EVENT = "ATTACH_CORE_CREDIT_ACCT";
    $EVENT_OPERATION = "ATTACH_CORE_CREDIT_ACCT";
    $EVENT_RELATION = "txn_charges";
    $EVENT_RELATION_NO = $TRAN_CHRG_ID;
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

# ... Create / Modify ......  Tran Charge ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
if (isset($_POST['btn_edit_chrg_amt_percentage'])) {
	$TRAN_CHRG_ID = trim($_POST['TRAN_CHRG_ID']);	
	$CC_RECORD_ID = trim($_POST['CC_RECORD_ID']);	
	$PP_CC_CHRG_AMT = trim(mysql_real_escape_string($_POST['CC_CHRG_AMT']));

	# ... Check if Record exists 
	$CHK_RCRD = "SELECT count(*) as RTN_VALUE FROM txn_charge_amounts WHERE RECORD_ID='$CC_RECORD_ID' AND TRAN_CHRG_AMT_STATUS='ACTIVE'";
	$CHK_RCRD_CNT = ReturnOneEntryFromDB($CHK_RCRD);
	if ($CHK_RCRD_CNT>0) {
		
		# ... Update Chrg Amt
	  $q = "UPDATE txn_charge_amounts SET CHRG_AMT='$PP_CC_CHRG_AMT' WHERE RECORD_ID='$CC_RECORD_ID'";
	  $update_response = ExecuteEntityUpdate($q);
	  if ($update_response=="EXECUTED") {
	  	# ... Log System Audit Log
	    $AUDIT_DATE = GetCurrentDateTime();
	    $ENTITY_TYPE = "TXN_CHRG_AMT_PP";
	    $ENTITY_ID_AFFECTED = $CC_RECORD_ID;
	    $EVENT = "MODIFY";
	    $EVENT_OPERATION = "MODIFY_TXN_CHRG_AMT_PP";
	    $EVENT_RELATION = "txn_charge_amounts";
	    $EVENT_RELATION_NO = $CC_RECORD_ID;
	    $OTHER_DETAILS = "";
	    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
	    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
	                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


	    $alert_type = "SUCCESS";
	    $alert_msg = "SUCCESS: Percentage transaction charge amount has been modified. Refreshing in 5 seconds.";
	    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	    header("Refresh:5;");

	  }

	}
	else{

		# ... Create Charge Amount
		$CC_TRAN_CHRG_ID=$TRAN_CHRG_ID;
		$CC_CHRG_AMT=$PP_CC_CHRG_AMT;
		$CC_CREATED_BY=$_SESSION['UPR_USER_ID'];
		$CC_CREATED_ON=GetCurrentDateTime();

		# ... DB Insert
		$q = "INSERT INTO txn_charge_amounts(TRAN_CHRG_ID,CHRG_AMT,CREATED_BY,CREATED_ON) VALUES('$CC_TRAN_CHRG_ID','$CC_CHRG_AMT','$CC_CREATED_BY','$CC_CREATED_ON')";
	  $exec_response = array();
	  $exec_response = ExecuteEntityInsert($q);
	  $RESP = $exec_response["RESP"]; 
	  $TCA_RECORD_ID = $exec_response["RECORD_ID"];
   
    # ... Process Entity System ID (Role ID)
	  $id_prefix = "TCA";
	  $id_len = 13;
	  $id_record_id = $TCA_RECORD_ID;
	  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
	  $TRAN_CHRG_AMT_ID = $ENTITY_ID;

	  # ... Updating the role id
	  $q2 = "UPDATE txn_charge_amounts SET TRAN_CHRG_AMT_ID='$TRAN_CHRG_AMT_ID' WHERE RECORD_ID='$TCA_RECORD_ID'";
	  $update_response = ExecuteEntityUpdate($q2);
	  if ($update_response=="EXECUTED") {

	  	# ... Log System Audit Log
	    $AUDIT_DATE = GetCurrentDateTime();
	    $ENTITY_TYPE = "TXN_CHRG_AMT_PP";
	    $ENTITY_ID_AFFECTED = $TRAN_CHRG_AMT_ID;
	    $EVENT = "CREATE";
	    $EVENT_OPERATION = "CREATE_NEW_TXN_CHRG_AMT";
	    $EVENT_RELATION = "txn_charge_amounts";
	    $EVENT_RELATION_NO = $TCA_RECORD_ID;
	    $OTHER_DETAILS = $TRAN_CHRG_AMT_ID;
	    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
	    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
	                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


	    $alert_type = "SUCCESS";
	    $alert_msg = "SUCCESS: Percentage transaction charge created and applied. Refreshing in 5 seconds.";
	    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	    header("Refresh:5;");
	  }	# ... END..IFF


	}
}

# ... Create Charge Block ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .
if (isset($_POST['btn_create_chrg_block'])) {
	$TRAN_CHRG_ID = trim($_POST['TRAN_CHRG_ID']);	
	$CHRG_LOW = trim(mysql_real_escape_string($_POST['CHRG_LOW']));
	$CHRG_HIGH = trim(mysql_real_escape_string($_POST['CHRG_HIGH']));
	$CHRG_AMT = trim(mysql_real_escape_string($_POST['CHRG_AMT']));

	# ... Validate Charge Tier Block
	$resp = array();
	$resp = ValidateChargeBlock($TRAN_CHRG_ID, $CHRG_LOW, $CHRG_HIGH);
	$CODE = $resp["CODE"];
	$MSSG = $resp["MSSG"];
	if ($CODE=="ERRR") {
		$alert_type = "ERROR";
    $alert_msg = "ALERT: $MSSG";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
	} else {
		$CC_TRAN_CHRG_ID=$TRAN_CHRG_ID;
		$CC_CHRG_LOW = $CHRG_LOW;
		$CC_CHRG_HIGH = $CHRG_HIGH;
		$CC_CHRG_AMT=$CHRG_AMT;
		$CC_CREATED_BY=$_SESSION['UPR_USER_ID'];
		$CC_CREATED_ON=GetCurrentDateTime();

		# ... DB Insert
		$q = "INSERT INTO txn_charge_amounts(TRAN_CHRG_ID,CHRG_LOW,CHRG_HIGH,CHRG_AMT,CREATED_BY,CREATED_ON) VALUES('$CC_TRAN_CHRG_ID','$CC_CHRG_LOW','$CC_CHRG_HIGH','$CC_CHRG_AMT','$CC_CREATED_BY','$CC_CREATED_ON')";
	  $exec_response = array();
	  $exec_response = ExecuteEntityInsert($q);
	  $RESP = $exec_response["RESP"]; 
	  $TCA_RECORD_ID = $exec_response["RECORD_ID"];

	  # ... Process Entity System ID (Role ID)
	  $id_prefix = "TCA";
	  $id_len = 13;
	  $id_record_id = $TCA_RECORD_ID;
	  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
	  $TRAN_CHRG_AMT_ID = $ENTITY_ID;

	  # ... Updating the role id
	  $q2 = "UPDATE txn_charge_amounts SET TRAN_CHRG_AMT_ID='$TRAN_CHRG_AMT_ID' WHERE RECORD_ID='$TCA_RECORD_ID'";
	  $update_response = ExecuteEntityUpdate($q2);
	  if ($update_response=="EXECUTED") {

	  	# ... Log System Audit Log
	    $AUDIT_DATE = GetCurrentDateTime();
	    $ENTITY_TYPE = "TXN_CHRG_AMT_FF";
	    $ENTITY_ID_AFFECTED = $TRAN_CHRG_AMT_ID;
	    $EVENT = "CREATE";
	    $EVENT_OPERATION = "CREATE_NEW_TXN_CHRG_AMT";
	    $EVENT_RELATION = "txn_charge_amounts";
	    $EVENT_RELATION_NO = $TCA_RECORD_ID;
	    $OTHER_DETAILS = $TRAN_CHRG_AMT_ID;
	    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
	    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
	                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

	    $alert_type = "SUCCESS";
	    $alert_msg = "SUCCESS: Charge transaction bloack created and applied. Refreshing in 5 seconds.";
	    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	    header("Refresh:5;");
	  }
	}	# ... END..IFF
}

# ... Delete Charge Block ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .
if (isset($_POST['btn_del_chrg_block'])) {
  $RECORD_ID = $_POST['ADD_RECORD_ID'];

  $tt = array();
  $tt = FetchTranChargeAmountsForRecordId($RECORD_ID);
  $CC_RECORD_ID = $tt['RECORD_ID'];
	$CC_TRAN_CHRG_AMT_ID = $tt['TRAN_CHRG_AMT_ID'];
	$CC_TRAN_CHRG_ID = $tt['TRAN_CHRG_ID'];
	$CC_CHRG_LOW = $tt['CHRG_LOW'];
	$CC_CHRG_HIGH = $tt['CHRG_HIGH'];
	$CC_CHRG_AMT = $tt['CHRG_AMT'];
	$CC_CREATED_BY = $tt['CREATED_BY'];
	$CC_CREATED_ON = $tt['CREATED_ON'];
	$CC_TRAN_CHRG_AMT_STATUS = $tt['TRAN_CHRG_AMT_STATUS'];


  # ... 02: Save Data to DataBase
  $q = "INSERT INTO txn_charge_amounts_deleted(RECORD_ID,TRAN_CHRG_AMT_ID,TRAN_CHRG_ID,CHRG_LOW,CHRG_HIGH,CHRG_AMT,CREATED_BY
                                       ,CREATED_ON,TRAN_CHRG_AMT_STATUS) 
        VALUES('$CC_RECORD_ID','$CC_TRAN_CHRG_AMT_ID','$CC_TRAN_CHRG_ID','$CC_CHRG_LOW','$CC_CHRG_HIGH','$CC_CHRG_AMT','$CC_CREATED_BY','$CC_CREATED_ON','$CC_TRAN_CHRG_AMT_STATUS')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];
  if ( $RESP=="EXECUTED" ) {
    
    $TABLE = "txn_charge_amounts";
    $TABLE_RECORD_ID = $_POST['ADD_RECORD_ID'];
    $delete_response = array();
    $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
    $DEL_FLG = $delete_response["DEL_FLG"];
    $DEL_ROW = $delete_response["DEL_ROW"];

    if ($DEL_FLG=="Y") {
      # ... Log System Audit Log
	    $AUDIT_DATE = GetCurrentDateTime();
	    $ENTITY_TYPE = "TXN_CHRG_AMT_FF";
	    $ENTITY_ID_AFFECTED = $CC_TRAN_CHRG_AMT_ID;
	    $EVENT = "DELETE";
	    $EVENT_OPERATION = "DELETE_TXN_CHRG_AMT";
	    $EVENT_RELATION = "txn_charge_amounts->txn_charge_amounts_deleted";
	    $EVENT_RELATION_NO = $_POST['ADD_RECORD_ID'];
	    $OTHER_DETAILS = $DEL_ROW;
	    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
	    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
	                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

	    $alert_type = "INFO";
	    $alert_msg = "MESSAGE: Charge transaction block has been deleted. Refreshing in 5 seconds.";
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
              	<a href="tt-charges" class="btn btn-sm btn-dark pull-left">Back</a>
                <h2><strong>CHARGE MGT: </strong><?php echo $TRAN_CHRG_NAME; ?></h2>

                <div class="clearfix"></div>
              </div>

              <div class="x_content">         

              	<!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE DETAILS -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -->
              	<!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE DETAILS -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -->
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


              	<!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE CREDIT ACCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -- -->
              	<!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE CREDIT ACCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -- -->
                <table class="table table-striped table-bordered">
                  <thead>
                  	<tr valign="top" bgcolor="#EEE"><th colspan="3">Charge Credit Account Details
                  		<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#edit_chrg">Modify Credit Acct</button>
			                <div id="edit_chrg" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
			                  <div class="modal-dialog modal-lg">
			                    <div class="modal-content">

			                      <div class="modal-header">
			                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
			                        </button>
			                        <h4 class="modal-title" id="myModalLabel2">Modify Credit Account</h4>
			                      </div>
			                      <div class="modal-body">
			                         <form id="sswwe" method="post">
                                  <table id="datatable2" width="100%" class="table table-striped table-bordered">
                                    <thead>
                                      <tr valign="top">
                                        <th colspan="4" bgcolor="#EEE">List of Available Office Accounts</th>
                                      </tr>
                                      <tr valign="top">
                                        <th>#</th>
                                        <th>Account #</th>
                                        <th>Account Name</th>
                                        <th>Actions</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                      $ofat_acct_list = array();
                                      $response_msg = FetchOAFTAccountsByCustomRpt($MIFOS_CONN_DETAILS);
								                      $CONN_FLG = $response_msg["CONN_FLG"];
								                      $CORE_RESP = $response_msg["CORE_RESP"];
								                      $ofat_acct_list = $response_msg["CORE_RESP"]["data"];

                                      for ($i=0; $i < sizeof($ofat_acct_list); $i++) { 
                                        $json_data = array();
                                        $json_data = $ofat_acct_list[$i];
                                        $OAFT_acct_id = $json_data["row"][0];
                                        $OAFT_acct_no = $json_data["row"][1];
                                        $OAFT_product_id = $json_data["row"][2];
                                        $OAFT_product_name = $json_data["row"][3];
                                        $OAFT_product_short_name = $json_data["row"][4];
                                        $OAFT_status_enum = $json_data["row"][5];
                                        $OAFT_client_id = $json_data["row"][6];
                                        $OAFT_acct_name = $json_data["row"][7];

                                        $id = "FTT".($i+1);
									                      $target = "#".$id;
									                      $form_id = "FORM_".$id;
                                        ?>
                                        <tr valign="top">
                                          <td><?php echo ($i+1); ?>. </td>
                                          <td><?php echo $OAFT_acct_no; ?></td>
                                          <td><?php echo $OAFT_acct_name; ?></td>
                                          <td>
			                         							<form id="<?php echo $form_id; ?>" method="post">
			                         								<input type="hidden" id="TRAN_CHRG_ID" name="TRAN_CHRG_ID" value="<?php echo $TRAN_CHRG_ID; ?>">
			                         								<input type="hidden" id="CORE_CR_ACCT_ID" name="CORE_CR_ACCT_ID" value="<?php echo $OAFT_acct_id; ?>">
                                          		<button type="submit" class="btn btn-sm btn-default" name="btn_attch_acct">Attach Acct</button>
                                          	</form>
                                            
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
                  	</th></tr>
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


              	<!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE AMOUNT DETAILS FOR TRAN CHRG --  -- -- -- -- -- -- -- -- -->
              	<!-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- CHARGE AMOUNT DETAILS FOR TRAN CHRG --  -- -- -- -- -- -- -- -- -->
                <?php
                // ... PERCENTAGE CHRGE
                if($TRAN_CHRG_TYPE=="PP"){
                	?>
                	<table class="table table-bordered">
                		<thead>
                			<tr valign="top" bgcolor="#EEE"><th colspan="3" bgcolor="#EEE">Charge Percentage
	                  		<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#cccc">Amend Charge</button>
				                <div id="cccc" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
				                  <div class="modal-dialog modal-sm">
				                    <div class="modal-content">

				                      <div class="modal-header">
				                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
				                        </button>
				                        <h4 class="modal-title" id="myModalLabel2">Change Percentage Charge</h4>
				                      </div>
				                      <div class="modal-body">
				                        <form id="dgdfs23hshs" method="post">
				                        	<input type="hidden" id="CC_RECORD_ID" name="CC_RECORD_ID" value="<?php echo $CC_RECORD_ID; ?>">
				                        	<input type="hidden" id="TRAN_CHRG_ID" name="TRAN_CHRG_ID" value="<?php echo $TRAN_CHRG_ID; ?>">
			                            <label>Charge (%):</label><br>
			                            <input type="text" id="CC_CHRG_AMT" name="CC_CHRG_AMT" class="form-control" value="<?php echo $CC_CHRG_AMT; ?>" required=""><br>

			                            
			                            <br>
			                            <button type="submit" class="btn btn-primary btn-sm" name="btn_edit_chrg_amt_percentage">Save</button>
			                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
	                              </form> 
				                      </div>
				                    </div>
				                  </div>
				                </div>
	                		</th></tr>
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
	                  	<tr valign="top" bgcolor="#EEE"><th colspan="6">Transaction Charge Amounts (Flat/Tiered)
	                  		<button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#crt_grp">New Charge Block</button>
				                <div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
				                  <div class="modal-dialog modal-sm">
				                    <div class="modal-content">

				                      <div class="modal-header">
				                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
				                        </button>
				                        <h4 class="modal-title" id="myModalLabel2">Create New Charge Block</h4>
				                      </div>
				                      <div class="modal-body">
				                          <form id="dddqwqqw2" method="post">
				                            <input type="hidden" id="TRAN_CHRG_ID" name="TRAN_CHRG_ID" value="<?php echo $TRAN_CHRG_ID; ?>">

				                            <label>Lower Limit:</label><br>
				                            <input type="number" id="CHRG_LOW" name="CHRG_LOW" class="form-control" required=""><br>

				                            <label>Upper Limit:</label><br>
				                            <input type="number" id="CHRG_HIGH" name="CHRG_HIGH" class="form-control" required=""><br>

				                            <label>Charge Amount:</label><br>
				                            <input type="number" id="CHRG_AMT" name="CHRG_AMT" class="form-control" required=""><br>
				                            
				                            <br>
				                            <button type="submit" class="btn btn-primary btn-sm" name="btn_create_chrg_block">Create</button>
				                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
				                          </form>
				                      </div>
				                     

				                    </div>
				                  </div>
				                </div>
	                  	</th></tr>
	                    <tr valign="top" >
	                      <th>#</th>
	                      <th>Chrg Block Code</th>
	                      <th>Lower Limit</th>
	                      <th>Upper Limit</th>
	                      <th>Charge Amount</th>
	                      <th>Actions</th>
	                    </tr>
	                  </thead>
	                  <tbody>
	                  	<?php
	                  	for ($i=0; $i < sizeof($tt_list); $i++) { 
												$tt = array();
												$tt = $tt_list[$i];
												$CC_RECORD_ID = $tt['RECORD_ID'];
												$CC_TRAN_CHRG_AMT_ID = $tt['TRAN_CHRG_AMT_ID'];
												$CC_TRAN_CHRG_ID = $tt['TRAN_CHRG_ID'];
												$CC_CHRG_LOW = $tt['CHRG_LOW'];
												$CC_CHRG_HIGH = $tt['CHRG_HIGH'];
												$CC_CHRG_AMT = $tt['CHRG_AMT'];
												$CC_CREATED_BY = $tt['CREATED_BY'];
												$CC_CREATED_ON = $tt['CREATED_ON'];
												$CC_TRAN_CHRG_AMT_STATUS = $tt['TRAN_CHRG_AMT_STATUS'];

												$id = "FTT".($i+1);
	                      $target = "#".$id;
	                      $form_id = "FORM_".$id;

												?>
												<tr valign="top">
		                    	<td><?php echo ($i+1); ?>. </td>
	                        <td><?php echo $CC_TRAN_CHRG_AMT_ID; ?></td>
	                        <td><?php echo number_format($CC_CHRG_LOW); ?></td>
	                        <td><?php echo number_format($CC_CHRG_HIGH); ?></td>
	                        <td><?php echo number_format($CC_CHRG_AMT); ?></td>
	                        <td>
	                        	<form method="post" id="<?php echo $form_id; ?>">
	                        		<input type="hidden" id="ADD_RECORD_ID" name="ADD_RECORD_ID" value="<?php echo $CC_RECORD_ID; ?>">
	                        		<button type="submit" class="btn btn-danger btn-xs" name="btn_del_chrg_block">Delete</button>
	                        	</form>
	                        	
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
