<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

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
              <h2>My Accounts</h2>
              <div class="clearfix"></div>
            </div>

            <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->
            <div class="x_content">

              <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                  <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Savings</a>
                  </li>
                  <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Loans</a>
                  </li>
                  <li role="presentation" class=""><a href="#tab_content3" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">Shares</a>
                  </li>
                </ul>
                <div id="myTabContent" class="tab-content">
                  <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                    <p>
                      <table class="table table-hover table-bordered table-striped">
                        <thead>
                          <tr valign="top" bgcolor="#EEE">
                            <th colspan="6">
                              My Savings Account
                              <a href="svg-appln-transfer" class="btn btn-xs btn-dark pull-right">Apply For Transfer</a>
                              <a href="svg-appln-deposit" class="btn btn-xs btn-success pull-right">Apply For Deposit</a>
                              <a href="svg-appln-withdraw" class="btn btn-xs btn-warning pull-right">Apply For Withdraw</a>
                            </th>
                          </tr>
                          <tr valign="top">
                            <th>#</th>
                            <th>Account No</th>
                            <th>Currency</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $response_msg = GetCustSavingsAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                          $CONN_FLG = $response_msg["CONN_FLG"];
                          $CORE_RESP = $response_msg["CORE_RESP"];
                          $ACCTS_DATA = array();
                          $ACCTS_DATA = $CORE_RESP["data"];

                          for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                            $row = $ACCTS_DATA[$i]["row"];
                            $svgs_id = $row[0];
                            $svgs_account_no = $row[1];
                            $svgs_crncy_code = $row[2];
                            $client_id = $row[3];
                            $svgs_product_id = $row[4];
                            $svgs_product_name = $row[5];
                            $svgs_product_shortname = $row[6];
                            $group_id =  $row[7];
                            $status =  $row[8];

                            $data_transfer = $svgs_id;
                            $data_transfer2 = $svgs_account_no;
                            $data_transfer3 = $svgs_product_name . " (" . $svgs_product_shortname . ")";
                          ?>
                            <tr valign="top">
                              <td><?php echo ($i + 1); ?></td>
                              <td><?php echo $svgs_account_no; ?></td>
                              <td><?php echo $svgs_crncy_code; ?></td>
                              <td><?php echo $svgs_product_name . " (" . $svgs_product_shortname . ")"; ?></td>
                              <td><?php echo "Active"; ?></td>
                              <td>
                                <a href="my-accounts-svg?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">Acct Details</a>
                                <a href="my-accounts-svg-stmt?k=<?php echo $data_transfer; ?>&l=<?php echo $data_transfer2; ?>&m=<?php echo $data_transfer3; ?>" class="btn btn-default btn-xs">View Stmt</a>
                              </td>
                            </tr>
                          <?php

                          }


                          ?>
                        </tbody>
                      </table>
                      <br>


                      <table class="table table-hover table-bordered table-striped">
                        <thead>
                          <tr valign="top" bgcolor="#EEE">
                            <th colspan="6">
                              My Group Savings Account
                              <a href="svg-appln-transfer" class="btn btn-xs btn-dark pull-right">Apply For Transfer</a>
                              <a href="svg-appln-deposit" class="btn btn-xs btn-success pull-right">Apply For Deposit</a>
                              <a href="svg-appln-withdraw" class="btn btn-xs btn-warning pull-right">Apply For Withdraw</a>
                            </th>
                          </tr>
                          <tr valign="top">
                            <th>#</th>
                            <th>Account No</th>
                            <th>Currency</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $response_msg = GetCustSavingsAccountsGroup($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                          $CONN_FLG = $response_msg["CONN_FLG"];
                          $CORE_RESP = $response_msg["CORE_RESP"];
                          $ACCTS_DATA = array();
                          $ACCTS_DATA = $CORE_RESP["data"];

                          for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                            $row = $ACCTS_DATA[$i]["row"];
                            $svgs_id = $row[0];
                            $svgs_account_no = $row[1];
                            $svgs_crncy_code = $row[2];
                            $client_id = $row[3];
                            $svgs_product_id = $row[4];
                            $svgs_product_name = $row[5];
                            $svgs_product_shortname = $row[6];
                            $group_id =  $row[7];
                            $status =  $row[8];

                            $data_transfer = $svgs_id;
                            $data_transfer2 = $svgs_account_no;
                            $data_transfer3 = $svgs_product_name . " (" . $svgs_product_shortname . ")";
                          ?>
                            <tr valign="top">
                              <td><?php echo ($i + 1); ?></td>
                              <td><?php echo $svgs_account_no; ?></td>
                              <td><?php echo $svgs_crncy_code; ?></td>
                              <td><?php echo $svgs_product_name . " (" . $svgs_product_shortname . ")"; ?></td>
                              <td><?php echo "Active"; ?></td>
                              <td>
                                <a href="my-accounts-svg?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">Acct Details</a>
                                <a href="my-accounts-svg-stmt?k=<?php echo $data_transfer; ?>&l=<?php echo $data_transfer2; ?>&m=<?php echo $data_transfer3; ?>" class="btn btn-default btn-xs">View Stmt</a>
                              </td>
                            </tr>
                          <?php

                          }


                          ?>
                        </tbody>
                      </table>
                    </p>
                  </div>
                  <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                    <p>
                      <table class="table table-hover table-bordered">
                        <thead>
                          <tr valign="top" bgcolor="#EEE">
                            <th colspan="6">My Loans Accounts
                              <a href="la-new-appln" class="btn btn-sm btn-warning pull-right">Apply For New Loan</a>
                            </th>
                          </tr>
                          <tr valign="top">
                            <th>#</th>
                            <th>Account No</th>
                            <th>Currency</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $response_msg = GetCustLoansAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                          $CONN_FLG = $response_msg["CONN_FLG"];
                          $CORE_RESP = $response_msg["CORE_RESP"];
                          $ACCTS_DATA = array();
                          $ACCTS_DATA = $CORE_RESP["data"];

                          for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                            $row = $ACCTS_DATA[$i]["row"];
                            $svgs_id = $row[0];
                            $svgs_account_no = $row[1];
                            $svgs_crncy_code = $row[2];
                            $client_id = $row[3];
                            $svgs_product_id = $row[4];
                            $svgs_product_name = $row[5];
                            $svgs_product_shortname = $row[6];
                            $group_id =  $row[7];
                            $status =  $row[8];

                            $data_transfer = $svgs_id;
                          ?>
                            <tr valign="top">
                              <td><?php echo ($i + 1); ?></td>
                              <td><?php echo $svgs_account_no; ?></td>
                              <td><?php echo $svgs_crncy_code; ?></td>
                              <td><?php echo $svgs_product_name . " (" . $svgs_product_shortname . ")"; ?></td>
                              <td><?php echo "Active"; ?></td>
                              <td>
                                <a href="my-accounts-loan-acct?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">Acct Details</a>
                              </td>
                            </tr>
                          <?php
                          }
                          ?>
                        </tbody>
                      </table>
                      <br>

                      <table class="table table-hover table-bordered">
                        <thead>
                          <tr valign="top" bgcolor="#EEE">
                            <th colspan="6">My Group Loans Accounts
                              <a href="la-new-appln" class="btn btn-sm btn-warning pull-right">Apply For New Loan</a>
                            </th>
                          </tr>
                          <tr valign="top">
                            <th>#</th>
                            <th>Account No</th>
                            <th>Currency</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $response_msg = GetCustLoansAccountsGroup($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                          $CONN_FLG = $response_msg["CONN_FLG"];
                          $CORE_RESP = $response_msg["CORE_RESP"];
                          $ACCTS_DATA = array();
                          $ACCTS_DATA = $CORE_RESP["data"];

                          for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                            $row = $ACCTS_DATA[$i]["row"];
                            $svgs_id = $row[0];
                            $svgs_account_no = $row[1];
                            $svgs_crncy_code = $row[2];
                            $client_id = $row[3];
                            $svgs_product_id = $row[4];
                            $svgs_product_name = $row[5];
                            $svgs_product_shortname = $row[6];
                            $group_id =  $row[7];
                            $status =  $row[8];

                            $data_transfer = $svgs_id;
                          ?>
                            <tr valign="top">
                              <td><?php echo ($i + 1); ?></td>
                              <td><?php echo $svgs_account_no; ?></td>
                              <td><?php echo $svgs_crncy_code; ?></td>
                              <td><?php echo $svgs_product_name . " (" . $svgs_product_shortname . ")"; ?></td>
                              <td><?php echo "Active"; ?></td>
                              <td>
                                <a href="my-accounts-loan-acct?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">Acct Details</a>
                              </td>
                            </tr>
                          <?php
                          }
                          ?>
                        </tbody>
                      </table>
                    </p>
                  </div>
                  <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="profile-tab">
                    <p>
                      <table class="table table-hover table-bordered">
                        <thead>
                          <tr valign="top" bgcolor="#EEE">
                            <th colspan="6">
                              My Shares Accounts
                              <a href="shares-appln-buy" class="btn btn-sm btn-success pull-right">Buy Shares</a>
                            </th>
                          </tr>
                          <tr valign="top">
                            <th>#</th>
                            <th>Account No</th>
                            <th>Currency</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Approved Shares</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $response_msg = GetCustSharesAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                          $CONN_FLG = $response_msg["CONN_FLG"];
                          $CORE_RESP = $response_msg["CORE_RESP"];
                          $ACCTS_DATA = array();
                          $ACCTS_DATA = $CORE_RESP["data"];

                          for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                            $row = $ACCTS_DATA[$i]["row"];
                            $svgs_id = $row[0];
                            $svgs_account_no = $row[1];
                            $svgs_crncy_code = $row[2];
                            $client_id = $row[3];
                            $svgs_product_id = $row[4];
                            $svgs_product_name = $row[5];
                            $svgs_product_shortname = $row[6];
                            $group_id =  $row[7];

                            # ... Get Tran Details
                            $response_msg2 = FetchShareAcctById($svgs_id, $MIFOS_CONN_DETAILS);
                            $CONN_FLG2 = $response_msg2["CONN_FLG"];
                            $CORE_RESP2 = $response_msg2["CORE_RESP"];
                            $tttt = $CORE_RESP2["summary"]["totalApprovedShares"];
                          ?>
                            <tr valign="top">
                              <td><?php echo ($i + 1); ?></td>
                              <td><?php echo $svgs_account_no; ?></td>
                              <td><?php echo $svgs_crncy_code; ?></td>
                              <td><?php echo $svgs_product_name . " (" . $svgs_product_shortname . ")"; ?></td>
                              <td><?php echo "Active"; ?></td>
                              <td><?php echo $tttt; ?></td>
                            </tr>
                          <?php

                          }


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