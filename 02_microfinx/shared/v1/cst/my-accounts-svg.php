<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$SVG_ID = mysql_real_escape_string(trim($_GET['k']));

# ... 01: Fetch Savings Account Details .....................................................................................#
$AccountNo = "";
$Activated_On = "";
$Currency ="";
$Balance = 0;

$totalDeposits = 0;
$totalWithdrawals = 0;
$totalInterestEarned = 0;
$totalInterestPosted = 0;
$interestNotPosted = 0;
$lastInterestCalculationDate = 0;
$response_msg = FetchSavingsAccountDetailsById($SVG_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
if(isset($CORE_RESP["summary"]["totalDeposits"])){
  $AccountNo = $CORE_RESP["accountNo"];
  $Activated_On = $CORE_RESP["timeline"]["activatedOnDate"][0]."-".$CORE_RESP["timeline"]["activatedOnDate"][1]."-".$CORE_RESP["timeline"]["activatedOnDate"][2];
  $Currency = $CORE_RESP["summary"]["currency"]["code"]." (".$CORE_RESP["summary"]["currency"]["name"].")";
  $Balance = $CORE_RESP["summary"]["accountBalance"];

  $totalDeposits = isset($CORE_RESP["summary"]["totalDeposits"])? $CORE_RESP["summary"]["totalDeposits"] : 0;
  $totalWithdrawals = isset($CORE_RESP["summary"]["totalWithdrawals"])? $CORE_RESP["summary"]["totalWithdrawals"] : 0;
  $totalInterestEarned = isset($CORE_RESP["summary"]["totalInterestEarned"])? $CORE_RESP["summary"]["totalInterestEarned"] : 0;
  $totalInterestPosted = isset($CORE_RESP["summary"]["totalInterestPosted"])? $CORE_RESP["summary"]["totalInterestPosted"] : 0;
  $interestNotPosted = isset($CORE_RESP["summary"]["interestNotPosted"])? $CORE_RESP["summary"]["interestNotPosted"] : 0;
  if (isset($CORE_RESP["summary"]["lastInterestCalculationDate"])){
    $lastInterestCalculationDate = $CORE_RESP["summary"]["lastInterestCalculationDate"][0]."-".$CORE_RESP["summary"]["lastInterestCalculationDate"][1]."-".$CORE_RESP["summary"]["lastInterestCalculationDate"][2];
  }
}



//$ = $CORE_RESP["summary"][""];




?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("My Accounts", $APP_SMALL_LOGO); 

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
                <a href="my-accounts" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Saving Account</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         

                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td width="24%"><b>Account No</b></td><td><?php echo $AccountNo; ?></td></tr>
                  <tr><td><b>Activated On</b></td><td><?php echo $Activated_On; ?></td></tr>
                  <tr><td><b>Currency</b></td><td><?php echo $Currency; ?></td></tr>
                  <tr><td><b>Balance</b></td><td><?php echo number_format($Balance,2); ?></td></tr>
                  <tr><td><b>Total Deposits</b></td><td><?php echo number_format($totalDeposits,2); ?></td></tr>
                  <tr><td><b>Total Withdrawals</b></td><td><?php echo number_format($totalWithdrawals,2); ?></td></tr>
                  <tr><td><b>Interest Earned</b></td><td><?php echo number_format($totalInterestEarned,2); ?></td></tr>
                  <tr><td><b>Interest Posted</b></td><td><?php echo number_format($totalInterestPosted,2); ?></td></tr>
                  <tr><td><b>Earned interest not posted</b></td><td><?php echo number_format($interestNotPosted,2); ?></td></tr>
                  <tr><td><b>Last Active Transaction Date</b></td><td><?php echo $lastInterestCalculationDate; ?></td></tr>
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
