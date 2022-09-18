<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$SVG_ID = mysql_real_escape_string(trim($_POST['k']));
$SVG_ACCT_NUM = mysql_real_escape_string(trim($_POST['l']));
$SVG_ACCT_PDT = mysql_real_escape_string(trim($_POST['m']));

$dd1 = mysql_real_escape_string(trim($_POST['dd1']));
$mm1 = mysql_real_escape_string(trim($_POST['mm1']));
$yy1 = mysql_real_escape_string(trim($_POST['yy1']));
$date11 = $yy1 . "-" . $mm1 . "-" . $dd1;

$dd2 = mysql_real_escape_string(trim($_POST['dd2']));
$mm2 = mysql_real_escape_string(trim($_POST['mm2']));
$yy2 = mysql_real_escape_string(trim($_POST['yy2']));
$date22 = $yy2 . "-" . $mm2 . "-" . $dd2;

//$time22 = strtotime($date22."+1 days");
//$date33 = date('Y-m-d', $time22);


$START_DATE = $date11;
$END_DATE = $date22;
# ............................................................................................

# ... 01: Fetch Savings Account Details .....................................................................................#
$AccountNo = "";
$Activated_On = "";
$Currency = "";
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
if (isset($CORE_RESP["summary"]["totalDeposits"])) {
  $AccountNo = $CORE_RESP["accountNo"];
  $Activated_On = $CORE_RESP["timeline"]["activatedOnDate"][0] . "-" . $CORE_RESP["timeline"]["activatedOnDate"][1] . "-" . $CORE_RESP["timeline"]["activatedOnDate"][2];
  $Currency = $CORE_RESP["summary"]["currency"]["code"] . " (" . $CORE_RESP["summary"]["currency"]["name"] . ")";
  $Balance = $CORE_RESP["summary"]["accountBalance"];

  $totalDeposits = isset($CORE_RESP["summary"]["totalDeposits"]) ? $CORE_RESP["summary"]["totalDeposits"] : 0;
  $totalWithdrawals = isset($CORE_RESP["summary"]["totalWithdrawals"]) ? $CORE_RESP["summary"]["totalWithdrawals"] : 0;
  $totalInterestEarned = isset($CORE_RESP["summary"]["totalInterestEarned"]) ? $CORE_RESP["summary"]["totalInterestEarned"] : 0;
  $totalInterestPosted = isset($CORE_RESP["summary"]["totalInterestPosted"]) ? $CORE_RESP["summary"]["totalInterestPosted"] : 0;
  $interestNotPosted = isset($CORE_RESP["summary"]["interestNotPosted"]) ? $CORE_RESP["summary"]["interestNotPosted"] : 0;
  if (isset($CORE_RESP["summary"]["lastInterestCalculationDate"])) {
    $lastInterestCalculationDate = $CORE_RESP["summary"]["lastInterestCalculationDate"][0] . "-" . $CORE_RESP["summary"]["lastInterestCalculationDate"][1] . "-" . $CORE_RESP["summary"]["lastInterestCalculationDate"][2];
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
          <div align="center" style="width: 100%;"><?php if (isset($_SESSION['ALERT_MSG'])) {
                                                      echo $_SESSION['ALERT_MSG'];
                                                    } ?></div>


          <div class="x_panel">
            <div class="x_title">
              <a href="my-accounts" class="btn btn-dark btn-sm pull-left">Back</a>
              <h2>Saving Account</h2>
              <div class="clearfix"></div>
            </div>

            <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->
            <div class="x_content">

              <table class="table table-hover table-bordered table-striped">
                <thead>
                  <tr valign="top" bgcolor="#EEE">
                    <th colspan="6">
                      Transactions
                      <a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download</a>
                    </th>
                  </tr>
                  <tr valign="top">
                    <th width="4%">#</th>
                    <th width="13%">Tran Date</th>
                    <th>Narration</th>
                    <th width="15%">Debit</th>
                    <th width="15%">Credit</th>
                    <th width="15%">Balance</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $excel_table_list = array();
                  $response_msg = GetSavingsAcctTransactionsWithDateRange($SVG_ID, $START_DATE, $END_DATE, $MIFOS_CONN_DETAILS);
                  $CONN_FLG = $response_msg["CONN_FLG"];
                  $CORE_RESP = $response_msg["CORE_RESP"];
                  $TRAN_DATA = array();
                  $TRAN_DATA = $CORE_RESP["data"];

                  for ($i = 0; $i < sizeof($TRAN_DATA); $i++) {

                    $row = $TRAN_DATA[$i]["row"];
                    $TXN_ID = $row[0];

                    # ... Get Tran Details
                    $response_msg2 = GetSavingsAcctTransactionDetails($SVG_ID, $TXN_ID, $MIFOS_CONN_DETAILS);
                    $CONN_FLG2 = $response_msg2["CONN_FLG"];
                    $CORE_RESP2 = $response_msg2["CORE_RESP"];
                    $TXN_INFO = $CORE_RESP2;
                    // echo "<pre>".print_r($TXN_INFO,true)."</pre>";

                    $transactionType = $TXN_INFO["transactionType"]["value"];
                    $tran_date = $TXN_INFO["date"][0] . "-" . $TXN_INFO["date"][1] . "-" . $TXN_INFO["date"][2];
                    $stmt_tran_date = date("d-M-Y", strtotime($tran_date));

                    $tran_crncy = $TXN_INFO["currency"]["code"];
                    $tran_amount = $TXN_INFO["amount"];
                    $tran_runng_bal = $TXN_INFO["runningBalance"];

                    # ... Preparing Txn Narration
                    $tran_nrrtn = $transactionType;
                    if (isset($TXN_INFO["transfer"])) {
                      $transferDescription = $TXN_INFO["transfer"]["transferDescription"];
                      $tran_nrrtn .= " - " . $transferDescription;
                    } else if (isset($TXN_INFO["paymentDetailData"])) {
                      $paymentType = $TXN_INFO["paymentDetailData"]["paymentType"]["name"];
                      $receiptNumber = $TXN_INFO["paymentDetailData"]["receiptNumber"];
                      $tran_nrrtn .= " - " . $paymentType . " - " . $receiptNumber;
                    }

                    # ... Determining debit or credit
                    $dr_amt = 0;
                    $cr_amt = 0;
                    $tran_type = $TXN_INFO["transactionType"]["value"];
                    if ($tran_type == "Deposit" || $tran_type == "Interest posting") {
                      $cr_amt = $tran_amount;
                    } else if (
                      $tran_type == "Withdrawal" ||
                      $tran_type == "Withhold Tax" ||
                      $tran_type == "Pay Charge" ||
                      $tran_type == "Withdrawal fee"
                      ) {
                      $dr_amt = $tran_amount;
                    }


                    # ... Building the excel table row
                    $excel_table_row[0] = ($i + 1);
                    $excel_table_row[1] = $stmt_tran_date;
                    $excel_table_row[2] = $tran_nrrtn;
                    $excel_table_row[3] = ($dr_amt == 0) ? "" : number_format($dr_amt, 2);
                    $excel_table_row[4] = ($cr_amt == 0) ? "" : number_format($cr_amt, 2);
                    $excel_table_row[5] = number_format($tran_runng_bal, 2);
                    $excel_table_list[$i] = $excel_table_row;

                    # ... Displaying the data
                  ?>
                    <tr valign="top">
                      <td><?php echo ($i + 1); ?></td>
                      <td><?php echo $stmt_tran_date; ?></td>
                      <td><?php echo $tran_nrrtn; ?></td>
                      <td align="right">
                        <?php
                        if ($tran_type == "Withdrawal" || 
                            $tran_type =="Withhold Tax" || 
                            $tran_type == "Pay Charge" || 
                            $tran_type == "Withdrawal fee"
                          ) {
                          echo number_format($dr_amt, 2);
                        } else {
                        }
                        ?>
                      </td>
                      <td align="right">
                        <?php
                        if ($tran_type == "Deposit" || $tran_type == "Interest posting") {
                          echo number_format($cr_amt, 2);
                        } else {
                        }
                        ?>
                      </td>
                      <td align="right"><?php echo number_format($tran_runng_bal, 2); ?></td>
                    </tr>
                  <?php
                  }
                  # ... Excel Data Preparation
                  $_SESSION["EXCEL_HEADER"] = array("#", "Tran Date", "Narration", "Debit", "Credit", "Balance");
                  $_SESSION["EXCEL_DATA"] = $excel_table_list;
                  $_SESSION["EXCEL_FILE"] = "Transactions_" . date('dFY', strtotime(GetCurrentDateTime())) . ".xlsx";
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