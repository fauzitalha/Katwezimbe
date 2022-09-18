<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... R005: LOAD CUSTOMER DETAILS .....................................................................................#
$cstmr = array();
$cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
$CUST_ID = $cstmr['CUST_ID'];
$CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
$CUST_EMAIL = $cstmr['CUST_EMAIL'];
$CUST_PHONE = $cstmr['CUST_PHONE'];

# ... Get Customer Name From Core
$response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$displayName = strtoupper($CORE_RESP["displayName"]);

# ... Decrypt Email & Phone
$EMAIL = AES256::decrypt($CUST_EMAIL);
$PHONE = AES256::decrypt($CUST_PHONE);
$_SESSION['FP_NAME'] = $displayName;
$_SESSION['FP_EMAIL'] = $EMAIL;
$_SESSION['FP_PHONE'] = $PHONE;


# ... F0000002: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "CUSTOMER";
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
  $EVENT_TYPE = "LOAN APPLN RECOMMENDATION";
  $TAN = GeneratePassKey(8);
  $ENC_TAN = AES256::encrypt($TAN);
  $TAN_GEN_DATE = GetCurrentDateTime();

  # ... UPDATE UN-USED TANS
  $q = "UPDATE txn_tans SET TAN_STATUS='KILLED (UNUSED)' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {
    # ... SQL INSERT
    $q = "INSERT INTO txn_tans(ENTITY_TYPE,ENTITY_ID,EVENT_TYPE,TAN,TAN_GEN_DATE) VALUES('$ENTITY_TYPE','$ENTITY_ID','$EVENT_TYPE','$ENC_TAN','$TAN_GEN_DATE')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP=="EXECUTED") {
      
      # ... DB INSERT
      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "TRANSACTION_TAN";
      $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
      $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                      ."This is your loan recommendation authorization TAN is: <b>".$TAN."</b>";
      $EMAIL_ATTACHMENT_PATH = "";
      $RECORD_DATE = GetCurrentDateTime();
      $EMAIL_STATUS = "NN";

      $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
      ExecuteEntityInsert($q);

      # ... Send System Response
      $alert_type = "INFO";
      $alert_msg = "ALERT: TAN has been sent out to your registered email. TAN expires after 5 minutes";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    }
  }
}

# ... F0000001: FORWARED APPLN .....................................................................................#
if (isset($_POST['btn_accept'])) {

  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $RCMNDTN_CUST_RESPONSE_DATE = GetCurrentDateTime();
  $LN_APPLN_STATUS = "READY_4_DISBURSAL";
  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));

  # ... 01: Validate Enterred TAN
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
  $val_results = array();
  $val_results = ValidateTranTAN($ENTITY_ID, $TRAN_TAN);
  $TAN_MSG_CODE = $val_results["TAN_MSG_CODE"];
  $TAN_MSG_MSG = $val_results["TAN_MSG_MSG"];

  if ($TAN_MSG_CODE=="FALSE") {
    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = $TAN_MSG_MSG;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if ($TAN_MSG_CODE=="TRUE") {
  	$q2 = "UPDATE loan_applns 
            SET RCMNDTN_CUST_RESPONSE_DATE='$RCMNDTN_CUST_RESPONSE_DATE'
               ,LN_APPLN_STATUS='$LN_APPLN_STATUS'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  	$update_response = ExecuteEntityUpdate($q2);

	  if ($update_response=="EXECUTED") {

	    # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
	    $AUDIT_DATE = GetCurrentDateTime();
	    $ENTITY_TYPE = "LOAN_APPLN";
	    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
	    $EVENT = "RECOMM_APPROVAL";
	    $EVENT_OPERATION = "ACCEPT_RECOMMENDATION_LOAN_APPLN";
	    $EVENT_RELATION = "loan_applns";
	    $EVENT_RELATION_NO = $LN_APPLN_NO;
	    $OTHER_DETAILS = $LN_APPLN_NO;
	    $INVOKER_ID = $_SESSION['CST_USR_ID'];
	    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
	                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


	    $alert_type = "SUCCESS";
	    $alert_msg = "MESSAGE: Loan application recommendation accepted. Refreshing in 5 seconds.";
	    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	    header("Refresh:5;");
	  }
  }
}

# ... F0000003: FORWARED APPLN .....................................................................................#
if (isset($_POST['btn_dec'])) {

  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $RCMNDTN_CUST_RESPONSE_DATE = GetCurrentDateTime();
  $LN_APPLN_STATUS = "RECOMMENDATION_DECLINED";
  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));

  # ... 01: Validate Enterred TAN
  $ENTITY_ID = $_SESSION['CST_USR_ID'];
  $val_results = array();
  $val_results = ValidateTranTAN($ENTITY_ID, $TRAN_TAN);
  $TAN_MSG_CODE = $val_results["TAN_MSG_CODE"];
  $TAN_MSG_MSG = $val_results["TAN_MSG_MSG"];

  if ($TAN_MSG_CODE=="FALSE") {
    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = $TAN_MSG_MSG;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if ($TAN_MSG_CODE=="TRUE") {

  	  $q2 = "UPDATE loan_applns 
            SET RCMNDTN_CUST_RESPONSE_DATE='$RCMNDTN_CUST_RESPONSE_DATE'
               ,LN_APPLN_STATUS='$LN_APPLN_STATUS'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  		$update_response = ExecuteEntityUpdate($q2);

		  if ($update_response=="EXECUTED") {

		    # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
		    $AUDIT_DATE = GetCurrentDateTime();
		    $ENTITY_TYPE = "LOAN_APPLN";
		    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
		    $EVENT = "RECOMM_REJECT";
		    $EVENT_OPERATION = "REJECT_RECOMMENDATION_LOAN_APPLN";
		    $EVENT_RELATION = "loan_applns";
		    $EVENT_RELATION_NO = $LN_APPLN_NO;
		    $OTHER_DETAILS = $LN_APPLN_NO;
		    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
		    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
		                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


		    $alert_type = "ERROR";
		    $alert_msg = "MESSAGE: Loan application recommendation declined. Loan application closed because you have declined. Refreshing in 5 seconds.";
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
    LoadDefaultCSSConfigurations("Loan Recommendations", $APP_SMALL_LOGO); 

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
              <?php SideNavBar($CUST_ID); ?>
            </div>
            <!-- /sidebar menu -->


          </div>
        </div>

        <!-- top navigation -->
        <?php TopNavBar($firstname); ?>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <h2>Loan Recommendations</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
              	<table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">List of loan applications due for recommendation acceptance
                      	<form method="post" id="asdfh12345fdasjk">
                          <button type="submit" class="btn btn-warning btn-xs pull-left" name="btn_gen_tan">Generate Auth TAN</button>
                        </form>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Loan Appln No</th>
                      <th>Amount Requested</th>
                      <th>Loan Product</th>
                      <th>Appln Approval Date</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $LN_APPLN_STATUS = "APPROVED";
                    $CST_ID = $_SESSION['CST_USR_ID'];
                    $loan_appln_list = array();
                    $loan_appln_list = FetchLoanApplnsByStatus($LN_APPLN_STATUS, $CST_ID);
                    for ($i=0; $i < sizeof($loan_appln_list); $i++) { 
                      $loan_appln = array();
                      $loan_appln = $loan_appln_list[$i];

                      $RECORD_ID = $loan_appln['RECORD_ID'];
                      $LN_APPLN_NO = $loan_appln['LN_APPLN_NO'];
                      $LN_PDT_ID = $loan_appln['LN_PDT_ID'];
                      $LN_APPLN_CREATION_DATE = $loan_appln['LN_APPLN_CREATION_DATE'];
                      $APPROVAL_DATE = $loan_appln['APPROVAL_DATE'];
                      $RQSTD_AMT = $loan_appln['RQSTD_AMT'];
                      $RQSTD_AMT = $loan_appln['RQSTD_AMT'];
                      $APPROVED_AMT = $loan_appln['APPROVED_AMT'];
                      $LN_APPLN_STATUS = $loan_appln['LN_APPLN_STATUS'];

                      # ... Getting Loan Product Details
                      $pdt_name = "";
                      $pdt_short_name = "";
                      $loan_product = array();
                      $response_msg = FetchLoanProductDetailsById($LN_PDT_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $loan_product = $response_msg["CORE_RESP"];
                      $pdt_name = $loan_product["pdt_name"];
                      $pdt_short_name = $loan_product["pdt_short_name"];

                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;

                      $id4 = "FTT4".($i+1);
                      $target4 = "#".$id4;
                      $form_id4 = "FORM_".$id4;

                      $data_transfer = $LN_APPLN_NO;

                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $LN_APPLN_NO; ?></td>
                        <td><?php echo number_format($RQSTD_AMT); ?></td>
                        <td><?php echo $pdt_name." ($pdt_short_name)"; ?></td>
                        <td><?php echo $APPROVAL_DATE; ?></td>
                        <td><?php echo $LN_APPLN_STATUS; ?></td>
                        <td>
                          <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="<?php echo $target3; ?>">Accept</button>
                          <div id="<?php echo $id3; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Loan Application Recommendation</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="<?php echo $form_id3; ?>" method="post">
                                      <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                                      
                                      <label>Request Amount:</label>
                                      <input type="text" class="form-control" disabled="" value="<?php echo number_format($RQSTD_AMT); ?>">

                                      <label>Approved Amount:</label>
                                      <input type="text" class="form-control" disabled="" value="<?php echo number_format($APPROVED_AMT); ?>">

								                      <label>Authorization TAN:</label> 
								                      <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                      	<br>
                                        <button type="submit" class="btn btn-success btn-sm" name="btn_accept">Accept</button>
                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                      </div>
                                      

                                      
                                      
                                    </form>
                                </div>
                               

                              </div>
                            </div>
                          </div>

                          <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="<?php echo $target4; ?>">Decline</button>
                          <div id="<?php echo $id4; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Loan Application Recommendation</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="<?php echo $form_id4; ?>" method="post">
                                      <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                                      
                                      <label>Request Amount:</label>
                                      <input type="text" class="form-control" disabled="" value="<?php echo number_format($RQSTD_AMT); ?>">

                                      <label>Approved Amount:</label>
                                      <input type="text" class="form-control" disabled="" value="<?php echo number_format($APPROVED_AMT); ?>">

								                      <label>Authorization TAN:</label> 
								                      <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control">

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                      	<strong>NOTE:</strong><br>
                                      	This process is cannot be undone. The system will close this application meaning you shall apply for a new one.
                                      	<br>
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_dec">Decline</button>
                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                      </div>
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
