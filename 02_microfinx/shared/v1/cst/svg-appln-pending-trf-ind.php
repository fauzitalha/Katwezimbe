<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$TRANSFER_REF = mysql_real_escape_string(trim($_GET['k']));
$st = array();
$st = FetchSavingsTransferApplnsById($TRANSFER_REF);
$RECORD_ID = $st['RECORD_ID'];
$CUST_ID = $st['CUST_ID'];
$SVGS_ACCT_ID_TO_DEBIT = $st['SVGS_ACCT_ID_TO_DEBIT'];
$TRANSFER_AMT = $st['TRANSFER_AMT'];
$SVGS_ACCT_ID_TO_CREDIT = $st['SVGS_ACCT_ID_TO_CREDIT'];
$REASON = $st['REASON'];
$APPLN_SUBMISSION_DATE = $st['APPLN_SUBMISSION_DATE'];
$SVGS_HANDLER_USER_ID = $st['SVGS_HANDLER_USER_ID'];
$FIRST_HANDLED_ON = $st['FIRST_HANDLED_ON'];
$FIRST_HANDLE_RMKS = $st['FIRST_HANDLE_RMKS'];
$COMMITTEE_FLG = $st['COMMITTEE_FLG'];
$COMMITTEE_HANDLER_USER_ID = $st['COMMITTEE_HANDLER_USER_ID'];
$COMMITTEE_STATUS = $st['COMMITTEE_STATUS'];
$COMMITTEE_STATUS_DATE = $st['COMMITTEE_STATUS_DATE'];
$COMMITTEE_RMKS = $st['COMMITTEE_RMKS'];
$APPROVED_AMT = $st['APPROVED_AMT'];
$APPROVED_BY = $st['APPROVED_BY'];
$APPROVAL_DATE = $st['APPROVAL_DATE'];
$APPROVAL_RMKS = $st['APPROVAL_RMKS'];
$PROC_MODE = $st['PROC_MODE'];
$PROC_BATCH_NO = $st['PROC_BATCH_NO'];
$CORE_TXN_ID = $st['CORE_TXN_ID'];
$TRANSFER_APPLN_STATUS = $st['TRANSFER_APPLN_STATUS'];

# ... 01: Get FROM Client Name .........................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$FROM_SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];
$FROM_SVGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];
$FROM_CORE_CUST_ID = $CORE_RESP["clientId"];
$FROM_CORE_CUST_NAME = $CORE_RESP["clientName"];


# ... 02: Get To Client Name .........................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$TO_SVGS_ACCT_NUM_TO_CREDIT = $CORE_RESP["accountNo"];
$TO_SVGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];
$TO_CORE_CUST_ID = $CORE_RESP["clientId"];
$TO_CORE_CUST_NAME = $CORE_RESP["clientName"];



?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Pending Transfer Applns", $APP_SMALL_LOGO); 

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
                <a href="svg-appln-pending-trf" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Pending Transfer Applns</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td width="20%"><b>Transfer Appln Ref</b></td><td colspan="3"><?php echo $TRANSFER_REF; ?></td></tr>
                  <tr><td><b>Appln Submission Date</b></td><td colspan="3"><?php echo $APPLN_SUBMISSION_DATE; ?></td></tr>
                  <tr>
                      <td><b>Appln Verification Date</b></td>
                      <td width="16%"><?php echo $FIRST_HANDLED_ON; ?></td>
                      <td width="20%"><b>Verification Remarks</b></td>
                      <td><?php echo $FIRST_HANDLE_RMKS; ?></td>
                  </tr>
                  <tr>
                      <td><b>Appln Approval Date</b></td>
                      <td><?php echo $APPROVAL_DATE; ?></td>
                      <td><b>Appln Approval Remarks</b></td>
                      <td><?php echo $APPROVAL_RMKS; ?></td>
                  </tr>
                  <tr><td><b>Appln Status</b></td><td colspan="3"><?php echo $TRANSFER_APPLN_STATUS; ?></td></tr>
                </table>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>From Customer Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $FROM_CORE_CUST_NAME; ?>">
                  </div>

                  <div class="col-md-3 col-sm-6 col-xs-6 form-group">
                    <label>From Savings Acct:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $FROM_SVGS_ACCT_NUM_TO_DEBIT; ?>">
                  </div>

                  <div class="col-md-3 col-sm-6 col-xs-6 form-group">
                    <label>From Savings Acct Bal:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($FROM_SVGS_ACCT_BAL); ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>To Customer Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TO_CORE_CUST_NAME; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>To Savings Acct:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $TO_SVGS_ACCT_NUM_TO_CREDIT; ?>">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Transfer Amount:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($TRANSFER_AMT); ?>">
                  </div>



                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Transfer Purpose</label>
                    <textarea class="form-control" rows="3" disabled=""><?php echo $REASON; ?></textarea>
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
