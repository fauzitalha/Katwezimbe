<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$DEPOSIT_REF = mysql_real_escape_string(trim($_GET['k']));
$sd = array();
$sd = FetchSavingsDepositApplnsById($DEPOSIT_REF);
$RECORD_ID = $sd['RECORD_ID'];
$DEPOSIT_REF = $sd['DEPOSIT_REF'];
$CUST_ID = $sd['CUST_ID'];
$SVGS_ACCT_ID_TO_CREDIT = $sd['SVGS_ACCT_ID_TO_CREDIT'];
$AMOUNT_BANKED = $sd['AMOUNT_BANKED'];
$REASON = $sd['REASON'];
$BANK_ID = $sd['BANK_ID'];
$BANK_INST_ACCT_NO = $sd['BANK_INST_ACCT_NO'];
$BANK_INST_ACCT_NAME = $sd['BANK_INST_ACCT_NAME'];
$BANK_RECEIPT_REF = $sd['BANK_RECEIPT_REF'];
$BANK_RECEIPT_ATTCHMT = $sd['BANK_RECEIPT_ATTCHMT'];
$RQST_DATE = $sd['RQST_DATE'];
$HANDLED_BY = $sd['HANDLED_BY'];
$HANDLED_ON = $sd['HANDLED_ON'];
$HANDLER_RMKS = $sd['HANDLER_RMKS'];
$APPRVD_BY = $sd['APPRVD_BY'];
$APPRVL_DATE = $sd['APPRVL_DATE'];
$APPRVL_RMKS = $sd['APPRVL_RMKS'];
$CORE_TXN_ID = $sd['CORE_TXN_ID'];
$RQST_STATUS = $sd['RQST_STATUS'];

$RECEIPT_LINK = "../wvi-cst/files/savings_deposit_applns/".$DEPOSIT_REF."/".$BANK_RECEIPT_ATTCHMT;

$fin = array();
$fin = FetchFinInstitutionsById($BANK_ID);
$FIN_INST_ID = $fin['FIN_INST_ID'];
$FIN_INST_NAME = $fin['FIN_INST_NAME'];

# ... 01: Get Deposit Client Name .....................................................................................#
$response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$DEP_SVGS_ACCT_NUM_TO_CREDIT = $CORE_RESP["accountNo"];
$DEP_SVGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];
$DEP_CORE_CUST_ID = $CORE_RESP["clientId"];
$DEP_CORE_CUST_NAME = $CORE_RESP["clientName"];


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Pending Deposit Applns", $APP_SMALL_LOGO); 

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
                <a href="svg-appln-pending-dep" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Pending Deposit Applns</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td width="20%"><b>Deposit Appln Ref</b></td><td colspan="3"><?php echo $DEPOSIT_REF; ?></td></tr>
                  <tr><td><b>Appln Submission Date</b></td><td colspan="3"><?php echo $RQST_DATE; ?></td></tr>
                  <tr>
                      <td><b>Appln Verification Date</b></td>
                      <td width="16%"><?php echo $HANDLED_ON; ?></td>
                      <td width="20%"><b>Verification Remarks</b></td>
                      <td><?php echo $HANDLER_RMKS; ?></td>
                  </tr>
                  <tr>
                      <td><b>Appln Approval Date</b></td>
                      <td><?php echo $APPRVL_DATE; ?></td>
                      <td><b>Appln Approval Remarks</b></td>
                      <td><?php echo $APPRVL_RMKS; ?></td>
                  </tr>
                  <tr><td><b>Appln Status</b></td><td colspan="3"><?php echo $RQST_STATUS; ?></td></tr>
                </table>



                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Appln Deposit Reference:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $DEPOSIT_REF; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Appln Date:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $RQST_DATE; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account to Credit:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $DEP_SVGS_ACCT_NUM_TO_CREDIT; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Amount to be deposited:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($AMOUNT_BANKED); ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Funds were deposited from:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $FIN_INST_NAME; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Bank Account No:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $BANK_INST_ACCT_NO; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Bank Account Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $BANK_INST_ACCT_NAME; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Receipt Reference Number:</label><a class="btn btn-xs btn-info" href="<?php echo $RECEIPT_LINK ?>">View Deposit Receipt</a>
                    <input type="text" class="form-control" disabled="" value="<?php echo $BANK_RECEIPT_REF; ?>">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Deposit Narration</label>
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
