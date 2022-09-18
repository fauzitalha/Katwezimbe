<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$WITHDRAW_REF = mysql_real_escape_string(trim($_GET['k']));
$sw = array();
$sw = FetchSavingsWithdrawApplnById($WITHDRAW_REF);
$RECORD_ID = $sw['RECORD_ID'];
$CUST_ID = $sw['CUST_ID'];
$SVGS_ACCT_ID_TO_DEBIT = $sw['SVGS_ACCT_ID_TO_DEBIT'];
$RQSTD_AMT = $sw['RQSTD_AMT'];
$REASON = $sw['REASON'];
$APPLN_SUBMISSION_DATE = $sw['APPLN_SUBMISSION_DATE'];
$SVGS_HANDLER_USER_ID = $sw['SVGS_HANDLER_USER_ID'];
$FIRST_HANDLED_ON = $sw['FIRST_HANDLED_ON'];
$FIRST_HANDLE_RMKS = $sw['FIRST_HANDLE_RMKS'];
$COMMITTEE_FLG = $sw['COMMITTEE_FLG'];
$COMMITTEE_HANDLER_USER_ID = $sw['COMMITTEE_HANDLER_USER_ID'];
$COMMITTEE_STATUS = $sw['COMMITTEE_STATUS'];
$COMMITTEE_STATUS_DATE = $sw['COMMITTEE_STATUS_DATE'];
$COMMITTEE_RMKS = $sw['COMMITTEE_RMKS'];
$APPROVED_AMT = $sw['APPROVED_AMT'];
$APPROVED_BY = $sw['APPROVED_BY'];
$APPROVAL_DATE = $sw['APPROVAL_DATE'];
$APPROVAL_RMKS = $sw['APPROVAL_RMKS'];
$CUST_FIN_INST_ID = $sw['CUST_FIN_INST_ID'];
$PROC_MODE = $sw['PROC_MODE'];
$PROC_BATCH_NO = $sw['PROC_BATCH_NO'];
$CORE_TXN_ID = $sw['CORE_TXN_ID'];
$SVGS_APPLN_STATUS = $sw['SVGS_APPLN_STATUS'];

# ... 01: Get Client Name .........................................................................................#
$cstmr = array();
$cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
$CUST_CORE_ID = $cstmr['CUST_CORE_ID'];

$response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$CORE_CUST_NAME = $CORE_RESP["displayName"];

# ... 02: Get Client Acct  .........................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];
$SVNGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];
$SVGS_PDT_ID = $CORE_RESP["savingsProductId"];
$SVGS_PDT_NAME = $CORE_RESP["savingsProductName"];

$CUST_BANK_NAME = GetCustBankFromBankAcct($CUST_ID, $CUST_FIN_INST_ID);
$FIN_INST_ACCT = $CUST_FIN_INST_ID." ($CUST_BANK_NAME')";
?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Pending Withdraw Applns", $APP_SMALL_LOGO); 

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
                <a href="svg-appln-prev-with" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Pending Withdraw Applns</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td width="20%"><b>Withdraw Appln Ref</b></td><td colspan="3"><?php echo $WITHDRAW_REF; ?></td></tr>
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
                      <td><b>Approved Remarks</b></td>
                      <td><?php echo $APPROVAL_RMKS; ?></td>
                  </tr>
                  <tr><td><b>Appln Status</b></td><td colspan="3"><?php echo $SVGS_APPLN_STATUS; ?></td></tr>
                </table>



                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Customer Id:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CUST_ID; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Customer Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CORE_CUST_NAME; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account To Debit:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account Balance:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SVNGS_ACCT_BAL); ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Amount Requested:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($RQSTD_AMT); ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Deposit Funds on:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $FIN_INST_ACCT; ?>">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Purpose</label>
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
