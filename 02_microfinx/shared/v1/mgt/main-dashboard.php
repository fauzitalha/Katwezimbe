<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

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

            <!-- --- --- --- --- PANEL 01 -- --- --- --- -- -- -->
            <!-- --- --- --- --- PANEL 01 -- --- --- --- -- -- -->
            <?php
            $DB_New_Self_Enrollments = "SELECT count(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE ACTIVATION_STATUS in ('PENDING','RESUBMITTED')";
            $DB_Applns_4_Review = "SELECT count(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE ACTIVATION_STATUS='NEEDS_CUSTOMER_REVIEW'";
            $DB_Approve_Applns = "SELECT count(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE ACTIVATION_STATUS='VERIFIED'";
            $DB_Finalize_Enrollment = "SELECT count(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE ACTIVATION_STATUS='APPROVED'";
            $DB_Customer_Updates = "SELECT count(*) as RTN_VALUE FROM cstmrs_info_chng_log WHERE CHNG_STATUS='PENDING'";
            $DB_Apprv_Customer_Updates = "SELECT count(*) as RTN_VALUE FROM cstmrs_info_chng_log WHERE CHNG_STATUS='VERIFIED'";
            $New_Self_Enrollments = ReturnOneEntryFromDB($DB_New_Self_Enrollments);
            $Applns_4_Review = ReturnOneEntryFromDB($DB_Applns_4_Review);
            $Approve_Applns = ReturnOneEntryFromDB($DB_Approve_Applns);
            $Finalize_Enrollment = ReturnOneEntryFromDB($DB_Finalize_Enrollment);
            $Customer_Updates = ReturnOneEntryFromDB($DB_Customer_Updates);
            $Apprv_Customer_Updates = ReturnOneEntryFromDB($DB_Apprv_Customer_Updates);
            ?>
            <div class="col-md-3 col-sm-4 col-xs-6">
              <div>
                <h2>Client Queue</h2>
                <div class="clearfix"></div>
              </div>

              <table class="table table-bordered" style="background-color: #FFF;">
                <tr><td>New Applns <div class="badge bg-green pull-right"><?php echo $New_Self_Enrollments; ?></div></td></tr>
                <tr><td>Applns 4 Review <div class="badge bg-red pull-right"><?php echo $Applns_4_Review; ?></div></td></tr>
                <tr><td>Approve Applns <div class="badge bg-purple pull-right"><?php echo $Approve_Applns; ?></div></td></tr>
                <tr><td>Finalize Enrollment <div class="badge bg-green pull-right"><?php echo $Finalize_Enrollment; ?></div></td></tr>
                <tr><td>Customer Updates <div class="badge bg-amber pull-right"><?php echo $Customer_Updates; ?></div></td></tr>
                <tr><td>Approve Customer Updates <div class="badge bg-blue pull-right"><?php echo $Apprv_Customer_Updates; ?></div></td></tr>
              </table>
            </div> 


            <!-- --- --- --- --- PANEL 03 -- --- --- --- -- -- -->
            <!-- --- --- --- --- PANEL 03 -- --- --- --- -- -- -->
            <?php
            $DB_Pending_Applns_Withdraws = "SELECT count(*) as RTN_VALUE FROM svgs_withdraw_requests WHERE SVGS_APPLN_STATUS='PENDING'";
            $DB_Approve_Withdraw = "SELECT count(*) as RTN_VALUE FROM svgs_withdraw_requests WHERE SVGS_APPLN_STATUS='VERIFIED'";
            $DB_Pending_Applns_Deposits = "SELECT count(*) as RTN_VALUE FROM svgs_deposit_requests WHERE RQST_STATUS='PENDING'";
            $DB_Approve_Deposit = "SELECT count(*) as RTN_VALUE FROM svgs_deposit_requests WHERE RQST_STATUS='VERIFIED'";
            $DB_Trans_Pending_Applns_Deposits = "SELECT count(*) as RTN_VALUE FROM svgs_transfer_requests WHERE TRANSFER_APPLN_STATUS='PENDING'";
            $DB_Trans_Approve_Deposit = "SELECT count(*) as RTN_VALUE FROM svgs_transfer_requests WHERE TRANSFER_APPLN_STATUS='VERIFIED'";
            $Pending_Applns_Withdraws = ReturnOneEntryFromDB($DB_Pending_Applns_Withdraws);
            $Approve_Withdraw = ReturnOneEntryFromDB($DB_Approve_Withdraw);
            $Pending_Applns_Deposits = ReturnOneEntryFromDB($DB_Pending_Applns_Deposits);
            $Approve_Deposit = ReturnOneEntryFromDB($DB_Approve_Deposit);
            $Trans_Pending_Applns_Deposits = ReturnOneEntryFromDB($DB_Trans_Pending_Applns_Deposits);
            $Trans_Approve_Deposit = ReturnOneEntryFromDB($DB_Trans_Approve_Deposit);
            ?>
            <div class="col-md-3 col-sm-4 col-xs-6">
              <div>
                <h2>Savings Queue</h2>
                <div class="clearfix"></div>
              </div>

              <table class="table table-bordered" style="background-color: #FFF;">
                <tr><td>Withdrawals Pending <div class="badge bg-red pull-right"><?php echo $Pending_Applns_Withdraws; ?></div></td></tr>
                <tr><td>Withdrawal Approvals<div class="badge bg-red pull-right"><?php echo $Approve_Withdraw; ?></div></td></tr>
                <tr><td>Deposits Pending <div class="badge bg-green pull-right"><?php echo $Pending_Applns_Deposits; ?></div></td></tr>
                <tr><td>Deposit Approvals<div class="badge bg-green pull-right"><?php echo $Approve_Deposit; ?></div></td></tr>
                <tr><td>Transfers Pending <div class="badge bg-blue pull-right"><?php echo $Trans_Pending_Applns_Deposits; ?></div></td></tr>
                <tr><td>Transfer Approvals<div class="badge bg-blue pull-right"><?php echo $Trans_Approve_Deposit; ?></div></td></tr>
              </table>
            </div> 



            <!-- --- --- --- --- PANEL 02 -- --- --- --- -- -- -->
            <!-- --- --- --- --- PANEL 02 -- --- --- --- -- -- -->
            <?php
            $DB_Loan_Applns_Queue = "SELECT count(*) as RTN_VALUE FROM loan_applns WHERE LN_APPLN_STATUS in ('NEW_SUBMISSION','AA_BOUNCED_BACK','CC_BOUNCED_BACK')";
            $DB_Credit_Committee = "SELECT count(*) as RTN_VALUE FROM loan_applns WHERE CC_FLG='YY' AND LN_APPLN_STATUS='VERIFIED'";
            $DB_Review_Applns = "SELECT count(*) as RTN_VALUE FROM loan_applns WHERE LN_APPLN_STATUS='READY_4_REVIEW'";
            $DB_Loan_Disbursement = "SELECT count(*) as RTN_VALUE FROM loan_applns WHERE LN_APPLN_STATUS='READY_4_DISBURSAL'";
            $Loan_Applns_Queue = ReturnOneEntryFromDB($DB_Loan_Applns_Queue);
            $Credit_Committee = ReturnOneEntryFromDB($DB_Credit_Committee);
            $Review_Applns = ReturnOneEntryFromDB($DB_Review_Applns);
            $Loan_Disbursement = ReturnOneEntryFromDB($DB_Loan_Disbursement);
            ?>
            <div class="col-md-3 col-sm-4 col-xs-6">
              <div>
                <h2>Loans Queue</h2>
                <div class="clearfix"></div>
              </div>

              <table class="table table-bordered" style="background-color: #FFF;">
                <tr><td>Loan Applns Queue <div class="badge bg-blue pull-right"><?php echo $Loan_Applns_Queue; ?></div></td></tr>
                <tr><td>Credit Committee <div class="badge bg-purple pull-right"><?php echo $Credit_Committee; ?></div></td></tr>
                <tr><td>Review Applns <div class="badge bg-amber pull-right"><?php echo $Review_Applns; ?></div></td></tr>
                <tr><td>Loan Disbursement <div class="badge bg-green pull-right"><?php echo $Loan_Disbursement; ?></div></td></tr>
              </table>
            </div> 


            



            <!-- --- --- --- --- PANEL 04 -- --- --- --- -- -- -->
            <!-- --- --- --- --- PANEL 04 -- --- --- --- -- -- -->
            <?php
            $DB_shr_rqst = "SELECT count(*) as RTN_VALUE FROM shares_appln_requests WHERE SHARES_APPLN_STATUS='PENDING'";
            $DB_shr_apprv = "SELECT count(*) as RTN_VALUE FROM shares_appln_requests WHERE SHARES_APPLN_STATUS='VERIFIED'";
            $shr_rqst = ReturnOneEntryFromDB($DB_shr_rqst);
            $shr_apprv = ReturnOneEntryFromDB($DB_shr_apprv);
            ?>
            <div class="col-md-3 col-sm-4 col-xs-6">
              <div>
                <h2>Shares Queue</h2>
                <div class="clearfix"></div>
              </div>

              <table class="table table-bordered" style="background-color: #FFF;">
                <tr><td>Pending Shares Request <div class="badge bg-blue pull-right"><?php echo $shr_rqst; ?></div></td></tr>
                <tr><td>Approve Shares Request <div class="badge bg-purple pull-right"><?php echo $shr_apprv; ?></div></td></tr>
              </table>
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
