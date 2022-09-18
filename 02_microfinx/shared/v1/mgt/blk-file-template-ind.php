<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$TEMPLATE_ID = mysql_real_escape_string($_GET['k']);
$tmp = array();
$tmp = FetchBulkTemplateById($TEMPLATE_ID);
$RECORD_ID = $tmp['RECORD_ID'];
$TEMPLATE_NAME = $tmp['TEMPLATE_NAME'];
$CREATED_BY = $tmp['CREATED_BY'];
$CREATED_ON = $tmp['CREATED_ON'];
$DATE_LST_CHNGD = $tmp['DATE_LST_CHNGD'];
$LST_CHNGD_BY = $tmp['LST_CHNGD_BY'];
$TEMPLATE_STATUS = $tmp['TEMPLATE_STATUS'];


# ... Count Entries
$DR_CNT = 0;
$CR_CNT = 0;
$Q_DR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_template_details WHERE TEMPLATE_ID='$TEMPLATE_ID' AND TRAN_TYPE='D'";
$DR_CNT = ReturnOneEntryFromDB($Q_DR);
$Q_CR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_template_details WHERE TEMPLATE_ID='$TEMPLATE_ID' AND TRAN_TYPE='C'";
$CR_CNT = ReturnOneEntryFromDB($Q_CR);

# ... Add Debit Entries
if (isset($_POST['btn_add_dr_ent'])) {
  $TEMPLATE_ID = trim(mysql_real_escape_string($_POST['TEMPLATE_ID']));
  $TRANSIT_ACCT_ID = trim(mysql_real_escape_string($_POST['TRANSIT_ACCT_ID']));

  $CNT_ADDED = 0;
  $svgs_acct_list = array();
  $response_msg = FetchAllSavingsAccountsByCustomRpt($TRANSIT_ACCT_ID, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $svgs_acct_list = $response_msg["CORE_RESP"]["data"];

  for ($i=0; $i < sizeof($svgs_acct_list); $i++) { 
	$json_data = array();
	$json_data = $svgs_acct_list[$i];
	$SVGS_acct_id = $json_data["row"][0];
	$SVGS_acct_no = $json_data["row"][1];
	$SVGS_product_id = $json_data["row"][2];
	$SVGS_product_name = $json_data["row"][3];
	$SVGS_product_short_name = $json_data["row"][4];
	$SVGS_status_enum = $json_data["row"][5];
	$SVGS_client_id = $json_data["row"][6];
	$SVGS_acct_name = $json_data["row"][7];

	if (isset($_POST[$SVGS_acct_id])) {

	  $CUST_CORE_ID = $SVGS_client_id;
	  $SVGS_ACCT_ID = $SVGS_acct_id;
	  $SVGS_ACCT_NUM = $SVGS_acct_no;
	  $PDT_NAME = $SVGS_product_name;
	  $SVGS_ACCT_NAME = $SVGS_acct_name;
	  $CURRENCY = "";
	  $TRAN_TYPE = "D";
	  $TRAN_AMT = "";
	  $TRAN_NARRATION = "";
	  $ADDED_BY = $_SESSION['UPR_USER_ID'];
	  $ADDED_ON = GetCurrentDateTime();

	  $q = "INSERT INTO blk_pymt_template_details(TEMPLATE_ID,CUST_CORE_ID,SVGS_ACCT_ID,SVGS_ACCT_NUM,PDT_NAME,SVGS_ACCT_NAME,CURRENCY,TRAN_TYPE,TRAN_AMT
	  ,TRAN_NARRATION ,ADDED_BY,ADDED_ON) VALUES('$TEMPLATE_ID','$CUST_CORE_ID','$SVGS_ACCT_ID','$SVGS_ACCT_NUM','$PDT_NAME','$SVGS_ACCT_NAME','$CURRENCY','$TRAN_TYPE','$TRAN_AMT','$TRAN_NARRATION','$ADDED_BY','$ADDED_ON')";
	  $exec_response = array();
	  $exec_response = ExecuteEntityInsert($q);
	  $RESP = $exec_response["RESP"]; 
	  $RECORD_ID = $exec_response["RECORD_ID"];

	  if ($RESP=="EXECUTED") {
		$CNT_ADDED++;
	  }

	  # ... Log activity & Display Summary
	  $AUDIT_DATE = GetCurrentDateTime();
	  $ENTITY_TYPE = "BULK_TEMPLATE_DETAILS";
	  $ENTITY_ID_AFFECTED = $TEMPLATE_ID;
	  $EVENT = "ADD_DEBIT_ENTRIES";
	  $EVENT_OPERATION = "ADD_DEBIT_ENTRIES";
	  $EVENT_RELATION = "blk_pymt_template_details";
	  $EVENT_RELATION_NO = $TEMPLATE_ID;
	  $OTHER_DETAILS = "";
	  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
	  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
					 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


	  $alert_type = "SUCCESS";
	  $alert_msg = "$CNT_ADDED debit entries added to payment template successfully. Refreshing in 4 seconds.";
	  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	  header("Refresh:4;");


	} # ... END..IFF
  } # ... END..LOOP
} # ... END..LOOP


# ... Add Credit Entries
if (isset($_POST['btn_add_cr_ent'])) {
  $TEMPLATE_ID = trim(mysql_real_escape_string($_POST['TEMPLATE_ID']));
  $TRANSIT_ACCT_ID = trim(mysql_real_escape_string($_POST['TRANSIT_ACCT_ID']));

  $CNT_ADDED = 0;
  $svgs_acct_list = array();
  $response_msg = FetchAllSavingsAccountsByCustomRpt($TRANSIT_ACCT_ID, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $svgs_acct_list = $response_msg["CORE_RESP"]["data"];

  for ($i=0; $i < sizeof($svgs_acct_list); $i++) { 
	$json_data = array();
	$json_data = $svgs_acct_list[$i];
	$SVGS_acct_id = $json_data["row"][0];
	$SVGS_acct_no = $json_data["row"][1];
	$SVGS_product_id = $json_data["row"][2];
	$SVGS_product_name = $json_data["row"][3];
	$SVGS_product_short_name = $json_data["row"][4];
	$SVGS_status_enum = $json_data["row"][5];
	$SVGS_client_id = $json_data["row"][6];
	$SVGS_acct_name = $json_data["row"][7];

	if (isset($_POST[$SVGS_acct_id])) {

	  $CUST_CORE_ID = $SVGS_client_id;
	  $SVGS_ACCT_ID = $SVGS_acct_id;
	  $SVGS_ACCT_NUM = $SVGS_acct_no;
	  $PDT_NAME = $SVGS_product_name;
	  $SVGS_ACCT_NAME = $SVGS_acct_name;
	  $CURRENCY = "";
	  $TRAN_TYPE = "C";
	  $TRAN_AMT = "";
	  $TRAN_NARRATION = "";
	  $ADDED_BY = $_SESSION['UPR_USER_ID'];
	  $ADDED_ON = GetCurrentDateTime();

	  $q = "INSERT INTO blk_pymt_template_details(TEMPLATE_ID,CUST_CORE_ID,SVGS_ACCT_ID,SVGS_ACCT_NUM,PDT_NAME,SVGS_ACCT_NAME,CURRENCY,TRAN_TYPE,TRAN_AMT
	  ,TRAN_NARRATION ,ADDED_BY,ADDED_ON) VALUES('$TEMPLATE_ID','$CUST_CORE_ID','$SVGS_ACCT_ID','$SVGS_ACCT_NUM','$PDT_NAME','$SVGS_ACCT_NAME','$CURRENCY','$TRAN_TYPE','$TRAN_AMT','$TRAN_NARRATION','$ADDED_BY','$ADDED_ON')";
	  $exec_response = array();
	  $exec_response = ExecuteEntityInsert($q);
	  $RESP = $exec_response["RESP"]; 
	  $RECORD_ID = $exec_response["RECORD_ID"];

	  if ($RESP=="EXECUTED") {
		$CNT_ADDED++;
	  }

	  # ... Log activity & Display Summary
	  $AUDIT_DATE = GetCurrentDateTime();
	  $ENTITY_TYPE = "BULK_TEMPLATE_DETAILS";
	  $ENTITY_ID_AFFECTED = $TEMPLATE_ID;
	  $EVENT = "ADD_CREDIT_ENTRIES";
	  $EVENT_OPERATION = "ADD_CREDIT_ENTRIES";
	  $EVENT_RELATION = "blk_pymt_template_details";
	  $EVENT_RELATION_NO = $TEMPLATE_ID;
	  $OTHER_DETAILS = "";
	  $INVOKER_ID = $_SESSION['UPR_USER_ID'];
	  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
					 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


	  $alert_type = "SUCCESS";
	  $alert_msg = "$CNT_ADDED credit entries added to payment template successfully. Refreshing in 4 seconds.";
	  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	  header("Refresh:4;");


	} # ... END..IFF
  } # ... END..LOOP
} # ... END..LOOP


# ... Remove Entrie from List
if (isset($_POST['btn_delete_entry'])) {
  $M_RECORD_ID = trim(mysql_real_escape_string($_POST['M_RECORD_ID']));

  $TABLE = "blk_pymt_template_details";
  $TABLE_RECORD_ID = $M_RECORD_ID;
  $delete_response = array();
  $delete_response = ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID);
  $DEL_FLG = $delete_response["DEL_FLG"];
  $DEL_ROW = $delete_response["DEL_ROW"];

  if ($DEL_FLG=="Y") {
	# ... Log System Audit Log
	$AUDIT_DATE = GetCurrentDateTime();
	$ENTITY_TYPE = "BULK_TEMPLATE_DETAILS";
	$ENTITY_ID_AFFECTED = $_POST['M_RECORD_ID'];
	$EVENT = "DELETING";
	$EVENT_OPERATION = "DELETING_BULK_ENTRY_DETAIL";
	$EVENT_RELATION = "blk_pymt_template_details";
	$EVENT_RELATION_NO = $M_RECORD_ID;
	$OTHER_DETAILS = $DEL_ROW;
	$INVOKER_ID = $_SESSION['UPR_USER_ID'];
	LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
				   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

	$alert_type = "INFO";
	$alert_msg = "MESSAGE: Entrie has been removed from Template. Refreshing in 5 seconds.";
	$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	header("Refresh:0;");
  }
}



?>
<!DOCTYPE html>
<html>
  <head>
	<?php
	# ... Device Settings and Global CSS
	LoadDeviceSettings(); 
	LoadDefaultCSSConfigurations("Bulk Payments Templates", $APP_SMALL_LOGO); 

	# ... Javascript
	LoadPriorityJS();
	OnLoadExecutions();
	StartTimeoutCountdown();
	ExecuteProcessStatistics();
	?>

	<script type="text/javascript">
		function SelectAllEntries(source){

			//$('#blk_datatable_credits input:checkbox').prop('checked', true);

			// Listen for click on toggle checkbox
			$('#bb_sel_all').click(function(event) {   
			    if(this.checked) {
			        // Iterate each checkbox
			        $('#blk_datatable_debits input:checkbox').each(function() {
			            this.checked = true;                        
			        });
			    } else {
			        $('#blk_datatable_debits input:checkbox').each(function() {
			            this.checked = false;                       
			        });
			    }
			});

		}

		function SelectAllEntries_credits(){

			// Listen for click on toggle checkbox
			$('#unbb_sel_all').click(function(event) {   
			   	if(this.checked) {
			        // Iterate each checkbox
			        $('#blk_datatable_credits input:checkbox').each(function() {
			            this.checked = true;                        
			        });
			    } else {
			        $('#blk_datatable_credits input:checkbox').each(function() {
			            this.checked = false;                       
			        });
			    }
			});

		}
	</script>
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
				<a href="blk-file-templates" class="btn btn-sm btn-dark pull-left">Back</a>
				<strong>TEMPLATE: </strong><?php echo $TEMPLATE_NAME; ?>
				<div class="clearfix"></div>
			  </div>

			  <div class="x_content">         

				<table class="table table-striped table-bordered">
				  <thead>
					<tr valign="top" bgcolor="#EEE">
					  <th>Template ID</th>
					  <th>Template Name</th>
					  <th>Debit Entries
						<button type="button" class="btn btn-warning btn-xs pull-right" data-toggle="modal" data-target="#crt_grp">Add Debit Entries</button>
						<div id="crt_grp" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
						  <div class="modal-dialog modal-lg">
							<div class="modal-content">

								<form id="dddqwqqw2" method="post">
								  <div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
									</button>
									<h4 class="modal-title" id="myModalLabel2">Add Debit Entries to <strong>Template: <?php echo $TEMPLATE_NAME; ?></strong></h4>
								  </div>
									  <div class="modal-body" style="height: 470px; overflow-y: scroll;">
										  
											<table id="blk_datatable_debits" width="100%" class="table table-striped table-bordered">
											  <thead>
												<tr valign="top">
												  <th colspan="3" bgcolor="#EEE">Select Debit Entry Accounts</th>
												  <th bgcolor="#EEE">
												  	<input type="checkbox" class="pull-right"  id="bb_sel_all" name="btn_select_all" onclick="SelectAllEntries(this)"> Select All</th>
												</tr>
												<tr valign="top">
												  <th>#</th>
												  <th>Acct No</th>
												  <th>Acct Name</th>
												  <th>Actions</th>
												</tr>
											  </thead>
											  <tbody>
												<?php
												$TRANSIT_ACCT_ID = $BULK_TXN_CONFIG["BLK_TRANSIT_ACCT_NUM_ID"];
												$x = 0;
												$svgs_acct_list = array();
												$response_msg = FetchAllSavingsAccountsByCustomRpt($TRANSIT_ACCT_ID, $MIFOS_CONN_DETAILS);
												$CONN_FLG = $response_msg["CONN_FLG"];
												$svgs_acct_list = $response_msg["CORE_RESP"]["data"];

												for ($i=0; $i < sizeof($svgs_acct_list); $i++) { 
												  $json_data = array();
												  $json_data = $svgs_acct_list[$i];
												  $SVGS_acct_id = $json_data["row"][0];
												  $SVGS_acct_no = $json_data["row"][1];
												  $SVGS_product_id = $json_data["row"][2];
												  $SVGS_product_name = $json_data["row"][3];
												  $SVGS_product_short_name = $json_data["row"][4];
												  $SVGS_status_enum = $json_data["row"][5];
												  $SVGS_client_id = $json_data["row"][6];
												  $SVGS_acct_name = $json_data["row"][7];

												  # ... Check if Account Exists in Debit Template Already
												  $Q_CNT = "SELECT count(*) as RTN_VALUE 
															FROM blk_pymt_template_details 
															WHERE SVGS_ACCT_ID='$SVGS_acct_id'
															  AND TEMPLATE_ID='$TEMPLATE_ID'
															  AND TEMPLATE_STATUS='ACTIVE'";
												  $C_CNT = ReturnOneEntryFromDB($Q_CNT);

												  // ... Display Data
												  if ($C_CNT>0) {
													// ... do nothing
												  } else {
													?>
													<tr valign="top">
													  <td><?php echo ($x+1); ?>. </td>
													  <td><?php echo $SVGS_acct_no." - ".$SVGS_product_name; ?></td>
													  <td><?php echo $SVGS_acct_name; ?></td>
													  <td>
														  <input type="checkbox" id="<?php echo $SVGS_acct_id; ?>" name="<?php echo $SVGS_acct_id; ?>" />
													  </td>
													</tr>
													<?php
													$x++;
												  }

												}
												?>
											  </tbody>
											</table>

											  <input type="hidden" id="TEMPLATE_ID" name="TEMPLATE_ID" value="<?php echo $TEMPLATE_ID; ?>">
											  <input type="hidden" id="TRANSIT_ACCT_ID" name="TRANSIT_ACCT_ID" value="<?php echo $TRANSIT_ACCT_ID; ?>">
											    
									  </div>
							   		<div class="modal-footer">
											<button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal">Cancel</button>
											<button type="submit" class="btn btn-warning btn-sm pull-right" name="btn_add_dr_ent">Add Debit Entries</button>
									  </div>
							 
								  </form>
							</div>
						  </div>
						</div>
					  </th>
					  <th>Credit Entries
						<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#crt_grp333">Add Credit Entries</button>
						<div id="crt_grp333" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
						  <div class="modal-dialog modal-lg">
							<div class="modal-content">

								<form id="crt_grp333" method="post">

							  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
								</button>
								<h4 class="modal-title" id="myModalLabel2">Add Credit Entries to <strong>Template: <?php echo $TEMPLATE_NAME; ?></strong></h4>
							  </div>
							  <div class="modal-body" style="height: 470px; overflow-y: scroll;">>
								  
									<table id="blk_datatable_credits" width="100%" class="table table-striped table-bordered" style="overflow-y: auto; height: 490px;">
									  <thead>
										<tr valign="top">
										  <th colspan="3" bgcolor="#EEE">Select Credit Entry Accounts</th>
										  <th bgcolor="#EEE">
												  	<input type="checkbox" class="pull-right"  id="unbb_sel_all" name="unbtn_select_all" onclick="SelectAllEntries_credits(this)"> Select All</th>
										</tr>
										<tr valign="top">
										  <th>#</th>
										  <th>Acct No</th>
										  <th>Acct Name</th>
										  <th>Actions</th>
										</tr>
									  </thead>
									  <tbody>
										<?php
										$TRANSIT_ACCT_ID = $BULK_TXN_CONFIG["BLK_TRANSIT_ACCT_NUM_ID"];
										$x = 0;
										$svgs_acct_list = array();
										$response_msg = FetchAllSavingsAccountsByCustomRpt($TRANSIT_ACCT_ID, $MIFOS_CONN_DETAILS);
										$CONN_FLG = $response_msg["CONN_FLG"];
										$svgs_acct_list = $response_msg["CORE_RESP"]["data"];

										for ($i=0; $i < sizeof($svgs_acct_list); $i++) { 
										  $json_data = array();
										  $json_data = $svgs_acct_list[$i];
										  $SVGS_acct_id = $json_data["row"][0];
										  $SVGS_acct_no = $json_data["row"][1];
										  $SVGS_product_id = $json_data["row"][2];
										  $SVGS_product_name = $json_data["row"][3];
										  $SVGS_product_short_name = $json_data["row"][4];
										  $SVGS_status_enum = $json_data["row"][5];
										  $SVGS_client_id = $json_data["row"][6];
										  $SVGS_acct_name = $json_data["row"][7];

										  # ... Check if Account Exists in Debit Template Already
										  $Q_CNT = "SELECT count(*) as RTN_VALUE 
													FROM blk_pymt_template_details 
													WHERE SVGS_ACCT_ID='$SVGS_acct_id'
													  AND TEMPLATE_ID='$TEMPLATE_ID'
													  AND TEMPLATE_STATUS='ACTIVE'";
										  $C_CNT = ReturnOneEntryFromDB($Q_CNT);

										  // ... Display Data
										  if ($C_CNT>0) {
											// ... do nothing
										  } else {
											?>
											<tr valign="top">
											  <td><?php echo ($x+1); ?>. </td>
											  <td><?php echo $SVGS_acct_no." - ".$SVGS_product_name; ?></td>
											  <td><?php echo $SVGS_acct_name; ?></td>
											  <td>
												  <input type="checkbox" id="<?php echo $SVGS_acct_id; ?>" name="<?php echo $SVGS_acct_id; ?>" />
											  </td>
											</tr>
											<?php
											$x++;
										  }

										}
										?>
									  </tbody>
									</table>
									
									

									<br>
									  <input type="hidden" id="TEMPLATE_ID" name="TEMPLATE_ID" value="<?php echo $TEMPLATE_ID; ?>">
									  <input type="hidden" id="TRANSIT_ACCT_ID" name="TRANSIT_ACCT_ID" value="<?php echo $TRANSIT_ACCT_ID; ?>">
									
								 
							  </div>

								  <div class="modal-footer">
										<button type="submit" class="btn btn-primary btn-sm pull-right" name="btn_add_cr_ent">Add Credit Entries</button>
										<button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal">Cancel</button>
								  </div>
							 
							  </form>

							</div>
						  </div>
						</div>
					  </th>
					  <th>Total Entries</th>
					</tr>
				  </thead>
				  <tbody>
					<tr valign="top">
					  <td><?php echo $TEMPLATE_ID; ?> </td>
					  <td><?php echo $TEMPLATE_NAME; ?></td>
					  <td><?php echo $DR_CNT; ?></td>
					  <td><?php echo $CR_CNT; ?></td>
					  <td><?php echo ($DR_CNT+$CR_CNT); ?></td>
					</tr>
				  </tbody>
				</table>

				<div style="height: 470px; overflow-y: scroll;">
				    <table id="blk_datatable_entry_list" class="table table-striped table-bordered" style="font-size: 11px;">
					  <thead>
						<tr valign="top">
						  <th colspan="8" bgcolor="#EEE">
							<span>Template List Details</span>
							<a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download Upload File Template</a>
						  </th>

						</tr>
						<tr valign="top">
						  <th>#</th>
						  <th>Account No</th>
						  <th>Account Name</th>
						  <th>Currency</th>
						  <th>Tran Type</th>
						  <th>Tran Amount</th>
						  <th>Tran Narration</th>
						  <th>Actions</th>
						</tr>
					  </thead>
					  <tbody>
						<?php
						$excel_table_list = array();
						$tmp_dtl_list_debits = array();
						$tmp_dtl_list_debits = FetchBulkTemplateDetailsListDebits($TEMPLATE_ID);
						$tmp_dtl_list_credits = array();
						$tmp_dtl_list_credits = FetchBulkTemplateDetailsListCredits($TEMPLATE_ID);
						$tmp_dtl_list = array();
						$tmp_dtl_list = array_merge($tmp_dtl_list_debits, $tmp_dtl_list_credits);

						for ($i=0; $i < sizeof($tmp_dtl_list); $i++) { 
						  $excel_table_row = array();
						  $tmp_dtl = array();
						  $tmp_dtl = $tmp_dtl_list[$i];
						  $M_RECORD_ID = $tmp_dtl['RECORD_ID'];
						  $TEMPLATE_ID = $tmp_dtl['TEMPLATE_ID'];
						  $CUST_CORE_ID = $tmp_dtl['CUST_CORE_ID'];
						  $SVGS_ACCT_ID = $tmp_dtl['SVGS_ACCT_ID'];
						  $SVGS_ACCT_NUM = $tmp_dtl['SVGS_ACCT_NUM'];
						  $PDT_NAME = $tmp_dtl['PDT_NAME'];
						  $SVGS_ACCT_NAME = $tmp_dtl['SVGS_ACCT_NAME'];
						  $CURRENCY = $tmp_dtl['CURRENCY'];
						  $TRAN_TYPE = $tmp_dtl['TRAN_TYPE'];
						  $TRAN_AMT = $tmp_dtl['TRAN_AMT'];
						  $TRAN_NARRATION = $tmp_dtl['TRAN_NARRATION'];
						  $ADDED_BY = $tmp_dtl['ADDED_BY'];
						  $ADDED_ON = $tmp_dtl['ADDED_ON'];
						  $TEMPLATE_STATUS = $tmp_dtl['TEMPLATE_STATUS'];

						  # ... Building the excel table row
						  $excel_table_row[0] = $CUST_CORE_ID;
						  $excel_table_row[1] = $SVGS_ACCT_ID;
						  $excel_table_row[2] = $SVGS_ACCT_NUM." - ".$PDT_NAME;
						  $excel_table_row[3] = $SVGS_ACCT_NAME;
						  $excel_table_row[4] = $TRAN_TYPE;
						  $excel_table_row[5] = "";
						  $excel_table_row[6] = "";
						  $excel_table_list[$i] = $excel_table_row;


						  $id3 = "FTT3".($i+1);
						  $target3 = "#".$id3;
						  $form_id3 = "FORM_".$id3;
						  ?>
						   <tr valign="top">
							<td><?php echo ($i+1); ?>. </td>
							<td><?php echo $SVGS_ACCT_NUM." - ".$PDT_NAME; ?></td>
							<td><?php echo $SVGS_ACCT_NAME; ?></td>
							<td>UGX</td>
							<td><?php echo $TRAN_TYPE; ?></td>
							<td><?php echo $TRAN_AMT; ?></td>
							<td><?php echo $TRAN_NARRATION; ?></td>
							<td>
							  <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $target3; ?>"><i class="fa fa-trash"></i></button>
							  <div id="<?php echo $id3; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
								<div class="modal-dialog modal-sm">
								  <div class="modal-content">

									<div class="modal-header">
									  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
									  </button>
									  <h4 class="modal-title" id="myModalLabel2">Remove Entrie from List</h4>
									</div>
									<div class="modal-body">
										<form id="<?php echo $form_id3; ?>" method="post">
										  <input type="hidden" id="M_RECORD_ID" name="M_RECORD_ID" value="<?php echo $M_RECORD_ID; ?>">
										  
										  <label>Tran Type:</label><br>
										  <?php echo $TRAN_TYPE; ?><br><br>

										  <label>Account Number:</label><br>
										  <?php echo $SVGS_ACCT_NUM; ?><br><br>
										  
										  <label>Account Name:</label><br>
										  <?php echo $SVGS_ACCT_NAME; ?><br><br>
										  
										  <strong>NOTE:</strong>
										  This action cannot be undone.
										  <br><br>

										  <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_entry">Delete</button>
										  <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
										</form>
									</div>
								   

								  </div>
								</div>
							  </div>
							</td>
						  </tr>
						  <?php
						} # .. END..LOOP

						# ... Excel Data Preparation
						$_SESSION["EXCEL_HEADER"] = array("CCID","ACID","ACCT_NO","ACCT_NAME","TRAN_TYPE","TRAN_AMT","TRAN_NARRATION");
						$_SESSION["EXCEL_DATA"] = $excel_table_list;
						$_SESSION["EXCEL_FILE"] = $TEMPLATE_NAME."_".date('dFY', strtotime(GetCurrentDateTime())).".xlsx";
						?>
					  </tbody>
					</table>
				</div>
				
		   

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
