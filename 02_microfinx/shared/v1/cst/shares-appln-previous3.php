<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$SHARES_APPLN_REF = mysql_real_escape_string(trim($_GET['k']));
$shr = array();
$shr = FetchShareRequestApplnsById($SHARES_APPLN_REF);
$RECORD_ID = $shr['RECORD_ID'];
$CUST_ID = $shr['CUST_ID'];
$SVGS_ACCT_ID_TO_DEBIT = $shr['SVGS_ACCT_ID_TO_DEBIT'];
$SHARES_REQUESTED = $shr['SHARES_REQUESTED'];
$SHARES_ACCT_ID_TO_CREDIT = $shr['SHARES_ACCT_ID_TO_CREDIT'];
$APPLN_SUBMISSION_DATE = $shr['APPLN_SUBMISSION_DATE'];
$SHARES_HANDLER_USER_ID = $shr['SHARES_HANDLER_USER_ID'];
$FIRST_HANDLED_ON = $shr['FIRST_HANDLED_ON'];
$FIRST_HANDLE_RMKS = $shr['FIRST_HANDLE_RMKS'];
$APPROVED_AMT = $shr['APPROVED_AMT'];
$APPROVED_BY = $shr['APPROVED_BY'];
$APPROVAL_DATE = $shr['APPROVAL_DATE'];
$APPROVAL_RMKS = $shr['APPROVAL_RMKS'];
$CORE_TXN_ID = $shr['CORE_TXN_ID'];
$SHARES_APPLN_STATUS = $shr['SHARES_APPLN_STATUS'];

# ... 01: Get Shares Client Name .....................................................................................#
$response_msg =  FetchSharesAccountDetailsById($SHARES_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$SHARES_ACCT_NUM = $CORE_RESP["accountNo"];
$SHARES_CUST_NAME = $CORE_RESP["clientName"];
$SHARES_OWNED= $CORE_RESP["summary"]["totalApprovedShares"];
$SHARES_PDT_ID = $CORE_RESP["productId"];
$SHARES_PDT_NAME = $CORE_RESP["productName"];

# ... 02: Get Savings Client Name .....................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$SVGS_CUST_ID = $CORE_RESP["clientId"];
$SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];
$SVGS_ACCT_BAL= $CORE_RESP["summary"]["accountBalance"];


# ... 03: Get Share Products Details .....................................................................................#
$response_msg = FetchShareProductById($SHARES_PDT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$SHARES_UNIT_PRICE = $CORE_RESP["unitPrice"];
$SHARES_MAX_SHR = $CORE_RESP["maximumShares"];
$SHRS_VALUE = ($SHARES_UNIT_PRICE * $SHARES_OWNED);



?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Pending Shares Applns", $APP_SMALL_LOGO); 

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
                <a href="shares-appln-previous" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Pending Savings Applns</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td width="20%"><b>Shares Appln Ref</b></td><td colspan="3"><?php echo $SHARES_APPLN_REF; ?></td></tr>
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
                  <tr><td><b>Appln Status</b></td><td colspan="3"><?php echo $SHARES_APPLN_STATUS; ?></td></tr>
                </table>

                
                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Shares Cust Names</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_CUST_NAME; ?>">
                  </div>
                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Shares Account</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_ACCT_NUM; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Current Shares Owned</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_OWNED; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Share Product Name</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_PDT_NAME; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Share Unit Price</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SHARES_UNIT_PRICE); ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Max Shares Per Client</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_MAX_SHR; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Current Share Value</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SHRS_VALUE); ?>">
                  </div>


                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Maximum Shares Purchasable</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo ($SHARES_MAX_SHR - $SHARES_OWNED); ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Number of shares to buy</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SHARES_REQUESTED; ?>">
                  </div>


                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account to Debitt</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?>">
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account Balance</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SVGS_ACCT_BAL); ?>">
                  </div>

                  
                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Amount to be debitted</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($SHARES_REQUESTED * $SHARES_UNIT_PRICE); ?>">
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
