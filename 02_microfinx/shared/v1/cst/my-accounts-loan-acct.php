<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$LOAN_ACCT_ID = mysql_real_escape_string(trim($_GET['k']));

# ... 01: Fetch Savings Account Details .....................................................................................#
$response_msg = FetchLoanAcctById($LOAN_ACCT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
//echo "<pre>".print_r($CORE_RESP, true)."</pre>";


# .................. GENERAL DETAILS ........................................................................................#
# .................. GENERAL DETAILS ........................................................................................#
$Loan_Acct_No = $CORE_RESP["accountNo"];
$Loan_Product = $CORE_RESP["loanProductName"];
//$Loan_Operative_Acct = $CORE_RESP["clientAccountNo"];
$Disbursement_Date = $CORE_RESP["timeline"]["actualDisbursementDate"][0] . "-" . $CORE_RESP["timeline"]["actualDisbursementDate"][1] . "-" . $CORE_RESP["timeline"]["actualDisbursementDate"][2];
$Currency = $CORE_RESP["summary"]["currency"]["code"] . " (" . $CORE_RESP["summary"]["currency"]["name"] . ")";
$Disburse_Amount = $CORE_RESP["summary"]["principalDisbursed"];
$Arrears_By = $CORE_RESP["summary"]["totalOverdue"];

# .................. GENERAL DETAILS ........................................................................................#
# .................. GENERAL DETAILS ........................................................................................#



# .................. SUMMARY DETAILS ........................................................................................#
# .................. SUMMARY DETAILS ........................................................................................#
$principalDisbursed = $CORE_RESP["summary"]["principalDisbursed"];
$principalPaid = $CORE_RESP["summary"]["principalPaid"];
$principalWrittenOff = $CORE_RESP["summary"]["principalWrittenOff"];
$principalOutstanding = $CORE_RESP["summary"]["principalOutstanding"];
$principalOverdue = $CORE_RESP["summary"]["principalOverdue"];

$interestCharged = $CORE_RESP["summary"]["interestCharged"];
$interestPaid = $CORE_RESP["summary"]["interestPaid"];
$interestWaived = $CORE_RESP["summary"]["interestWaived"];
$interestWrittenOff = $CORE_RESP["summary"]["interestWrittenOff"];
$interestOutstanding = $CORE_RESP["summary"]["interestOutstanding"];
$interestOverdue = $CORE_RESP["summary"]["interestOverdue"];

$feeChargesCharged = $CORE_RESP["summary"]["feeChargesCharged"];
$feeChargesDueAtDisbursementCharged = $CORE_RESP["summary"]["feeChargesDueAtDisbursementCharged"];
$feeChargesPaid = $CORE_RESP["summary"]["feeChargesPaid"];
$feeChargesWaived = $CORE_RESP["summary"]["feeChargesWaived"];
$feeChargesWrittenOff = $CORE_RESP["summary"]["feeChargesWrittenOff"];
$feeChargesOutstanding = $CORE_RESP["summary"]["feeChargesOutstanding"];
$feeChargesOverdue = $CORE_RESP["summary"]["feeChargesOverdue"];

$penaltyChargesCharged = $CORE_RESP["summary"]["penaltyChargesCharged"];
$penaltyChargesPaid = $CORE_RESP["summary"]["penaltyChargesPaid"];
$penaltyChargesWaived = $CORE_RESP["summary"]["penaltyChargesWaived"];
$penaltyChargesWrittenOff = $CORE_RESP["summary"]["penaltyChargesWrittenOff"];
$penaltyChargesOutstanding = $CORE_RESP["summary"]["penaltyChargesOutstanding"];
$penaltyChargesOverdue = $CORE_RESP["summary"]["penaltyChargesOverdue"];

$totalExpectedRepayment = $CORE_RESP["summary"]["totalExpectedRepayment"];
$totalRepayment = $CORE_RESP["summary"]["totalRepayment"];
$totalExpectedCostOfLoan = $CORE_RESP["summary"]["totalExpectedCostOfLoan"];
$totalCostOfLoan = $CORE_RESP["summary"]["totalCostOfLoan"];
$totalWaived = $CORE_RESP["summary"]["totalWaived"];
$totalWrittenOff = $CORE_RESP["summary"]["totalWrittenOff"];
$totalOutstanding = $CORE_RESP["summary"]["totalOutstanding"];
$totalOverdue = $CORE_RESP["summary"]["totalOverdue"];
# .................. SUMMARY DETAILS ........................................................................................#
# .................. SUMMARY DETAILS ........................................................................................#



?>
<!DOCTYPE html>
<html>

<head>
  <?php
  # ... Device Settings and Global CSS
  LoadDeviceSettings();
  LoadDefaultCSSConfigurations("Loan Acct Details", $APP_SMALL_LOGO);

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
              <h2>Loan Acct Details</h2>
              <div class="clearfix"></div>
            </div>

            <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->
            <div class="x_content">

              <table class="table table-bordered" style="font-size: 12px;">
                <tr>
                  <td width="24%"><b>Account No</b></td>
                  <td><?php echo $Loan_Acct_No; ?></td>
                </tr>
                <tr>
                  <td><b>Loan Product</b></td>
                  <td><?php echo $Loan_Product; ?></td>
                </tr>
                <!--<tr>
                  <td><b>Operative Account</b></td>
                  <td><?php //echo $Loan_Operative_Acct; ?></td>
                </tr>-->
                <tr>
                  <td><b>Disbursement Date</b></td>
                  <td><?php echo $Disbursement_Date; ?></td>
                </tr>
                <tr>
                  <td><b>Currency</b></td>
                  <td><?php echo $Currency; ?></td>
                </tr>
                <tr>
                  <td><b>Amount Disbursed</b></td>
                  <td><?php echo number_format($Disburse_Amount, 2); ?></td>
                </tr>
                <tr>
                  <td><b>Loan Arrears</b></td>
                  <td><?php echo number_format($Arrears_By, 2); ?></td>
                </tr>
              </table>



              <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                  <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Summary</a>
                  </li>
                  <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Loan Transactions</a>
                  </li>
                </ul>
                <div id="myTabContent" class="tab-content">
                  <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                    <p>
                      <table class="table table-bordered" style="font-size: 12px;">
                        <thead>
                          <tr valign="top">
                            <th></th>
                            <th>Original</th>
                            <th>Paid</th>
                            <th>Waived</th>
                            <th>Written Off</th>
                            <th>Outstanding</th>
                            <th>Over Due</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr valign="top">
                            <th>Principal</th>
                            <td><?php echo number_format($principalDisbursed, 2); ?></td>
                            <td><?php echo number_format($principalPaid, 2); ?></td>
                            <td></td>
                            <td><?php echo number_format($principalWrittenOff, 2); ?></td>
                            <td><?php echo number_format($principalOutstanding, 2); ?></td>
                            <td><?php echo number_format($principalOverdue, 2); ?></td>
                          </tr>
                          <tr valign="top">
                            <th>Interest</th>
                            <td><?php echo number_format($interestCharged, 2); ?></td>
                            <td><?php echo number_format($interestPaid, 2); ?></td>
                            <td><?php echo number_format($interestWaived, 2); ?></td>
                            <td><?php echo number_format($interestWrittenOff, 2); ?></td>
                            <td><?php echo number_format($interestOutstanding, 2); ?></td>
                            <td><?php echo number_format($interestOverdue, 2); ?></td>
                          </tr>
                          <tr valign="top">
                            <th>Fees</th>
                            <td><?php echo number_format($feeChargesCharged, 2); ?></td>
                            <td><?php echo number_format($feeChargesPaid, 2); ?></td>
                            <td><?php echo number_format($feeChargesWaived, 2); ?></td>
                            <td><?php echo number_format($feeChargesWrittenOff, 2); ?></td>
                            <td><?php echo number_format($feeChargesOutstanding, 2); ?></td>
                            <td><?php echo number_format($feeChargesOverdue, 2); ?></td>
                          </tr>
                          <tr valign="top">
                            <th>Penalties</th>
                            <td><?php echo number_format($penaltyChargesCharged, 2); ?></td>
                            <td><?php echo number_format($penaltyChargesPaid, 2); ?></td>
                            <td><?php echo number_format($penaltyChargesWaived, 2); ?></td>
                            <td><?php echo number_format($penaltyChargesWrittenOff, 2); ?></td>
                            <td><?php echo number_format($penaltyChargesOutstanding, 2); ?></td>
                            <td><?php echo number_format($penaltyChargesOverdue, 2); ?></td>
                          </tr>

                          <tr valign="top">
                            <th>Total</th>
                            <td><?php echo number_format($totalExpectedRepayment, 2); ?></td>
                            <td><?php echo number_format($totalRepayment, 2); ?></td>
                            <td><?php echo number_format($totalWaived, 2); ?></td>
                            <td><?php echo number_format($totalWrittenOff, 2); ?></td>
                            <td><?php echo number_format($totalOutstanding, 2); ?></td>
                            <td><?php echo number_format($totalOverdue, 2); ?></td>
                          </tr>
                        </tbody>
                      </table>
                    </p>
                  </div>
                  <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                    <p>
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
                            <th>Tran Type</th>
                            <th width="15%">Amount</th>
                            <th width="15%">Loan Balance</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $excel_table_list = array();
                          $response_msg = GetLoanAcctTransactions($LOAN_ACCT_ID, $MIFOS_CONN_DETAILS);
                          $CONN_FLG = $response_msg["CONN_FLG"];
                          $CORE_RESP = $response_msg["CORE_RESP"];
                          $TRAN_DATA = array();
                          $TRAN_DATA = $CORE_RESP["data"];
                          $x = 0;

                          for ($i = 0; $i < sizeof($TRAN_DATA); $i++) {
                            $row = $TRAN_DATA[$i]["row"];
                            $TXN_ID = $row[0];

                            # ... Get Tran Details
                            $response_msg2 = GetLoanAcctTransactionDetails($LOAN_ACCT_ID, $TXN_ID, $MIFOS_CONN_DETAILS);
                            $CONN_FLG2 = $response_msg2["CONN_FLG"];
                            $CORE_RESP2 = $response_msg2["CORE_RESP"];
                            $TXN_INFO = $CORE_RESP2;

                            $transactionType = $TXN_INFO["type"]["value"];
                            if ($transactionType == "Accrual") {
                              // ... do nothing
                            } else {
                              $tran_date = $TXN_INFO["date"][0] . "-" . $TXN_INFO["date"][1] . "-" . $TXN_INFO["date"][2];
                              $stmt_tran_date = date("d-M-Y", strtotime($tran_date));

                              $tran_crncy = $TXN_INFO["currency"]["code"];
                              $tran_amount = $TXN_INFO["amount"];
                              $outstandingLoanBalance = $TXN_INFO["outstandingLoanBalance"];

                              # ... Building the excel table row
                              $excel_table_row[0] = ($x + 1);
                              $excel_table_row[1] = $stmt_tran_date;
                              $excel_table_row[2] = $transactionType;
                              $excel_table_row[3] = number_format($tran_amount, 2);
                              $excel_table_row[4] = number_format($outstandingLoanBalance, 2);
                              $excel_table_list[$x] = $excel_table_row;

                              # ... Displaying the data
                          ?>
                              <tr valign="top">
                                <td><?php echo ($x + 1); ?></td>
                                <td><?php echo $stmt_tran_date; ?></td>
                                <td><?php echo $transactionType; ?></td>
                                <td align="right"><?php echo number_format($tran_amount, 2); ?></td>
                                <td align="right"><?php echo number_format($outstandingLoanBalance, 2); ?></td>
                              </tr>
                          <?php
                              $x++;
                            }
                          }

                          # ... Excel Data Preparation
                          $_SESSION["EXCEL_HEADER"] = array("#", "Tran Date", "Tran Type", "Amount", "Loan Balance");
                          $_SESSION["EXCEL_DATA"] = $excel_table_list;
                          $_SESSION["EXCEL_FILE"] = "Loan_Transactions_" . date('dFY', strtotime(GetCurrentDateTime())) . ".xlsx";
                          ?>
                        </tbody>
                      </table>
                    </p>
                  </div>

                </div>
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