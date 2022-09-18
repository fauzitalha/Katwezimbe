<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Handle Form Data
if (isset($_POST['btn_submit_appln'])) {

  $_SESSION['SVGS_ACCT_ID_TO_DEBIT'] = mysql_real_escape_string(trim($_POST['SVGS_ACCT_ID_TO_DEBIT']));
  $_SESSION['SVNGS_ACCT_BAL'] = mysql_real_escape_string(trim($_POST['SVNGS_ACCT_BAL']));
  $_SESSION['WITHDRAW_AMT'] = mysql_real_escape_string(trim($_POST['WITHDRAW_AMT']));
  $_SESSION['FIN_INST_ACCT'] = mysql_real_escape_string(trim($_POST['FIN_INST_ACCT']));
  $_SESSION['REASON'] = mysql_real_escape_string(trim($_POST['REASON']));

  # ... GETTING CUSTOMER DETAILS
  $cstmr = array();
  $cstmr = FetchCustomerLoginDataByCustId($_SESSION['CST_USR_ID']);
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

  $next_page = "svg-appln-withdraw2";
  NavigateToNextPage($next_page);
}

?>
<!DOCTYPE html>
<html>

<head>
  <?php
  # ... Device Settings and Global CSS
  LoadDeviceSettings();
  LoadDefaultCSSConfigurations("Savings Withdraw", $APP_SMALL_LOGO);

  # ... Javascript
  LoadPriorityJS();
  OnLoadExecutions();
  StartTimeoutCountdown();
  ExecuteProcessStatistics();
  ?>

  <script type="text/javascript">
    // ... 01: Get Savings Acct Details
    function FetchSavingsAcctDetails() {

      $('#SVNGS_ACCT_BAL').val("");
      $('#SVNGS_ACCT_BAL_DISP').val("");
      var selected_val = document.getElementById('SVGS_ACCT_ID_TO_DEBIT').value;

      //alert(selected_val);

      // ... Ajax
      $.ajax({
        type: 'post',
        url: 'ajax-fetch-savings_acct_details.php',
        data: {
          svngs_id: selected_val
        },
        success: function(response) {
          //console.log(response);

          // ... Handling of Db responses
          response = JSON.parse(response)
          var SVNGS_ACCT_BAL = response.SVNGS_ACCT_BAL;
          var SVNGS_ACCT_BAL_JS = response.SVNGS_ACCT_BAL;
          var num = parseFloat(SVNGS_ACCT_BAL).toLocaleString('en');
          //console.log(num);
          //console.log(response);
          $('#SVNGS_ACCT_BAL').val(SVNGS_ACCT_BAL);
          $('#SVNGS_ACCT_BAL_DISP').val(num);
        }
      });
    }

    // ... 02: Validate Data
    function Validate() {
      var SVNGS_ACCT_BAL = document.getElementById('SVNGS_ACCT_BAL').value;
      var WITHDRAW_AMT = document.getElementById('WITHDRAW_AMT').value;

      var WITH = parseFloat(WITHDRAW_AMT);
      var SVSS = parseFloat(SVNGS_ACCT_BAL);

      if (WITH == 0) {
        alert("Zero values not allowed");
        return false;
      } else {
        if (WITH > SVSS) {
          alert("Withdraw amount is greater than available balance");
          return false;
        } else {
          return true;
        }

      }
    }
  </script>
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
              <h2>Savings Withdraw</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">

              <form method="post" id="ss0ks" onsubmit="return Validate(this)">

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Select Savings Account:</label>
                  <select id="SVGS_ACCT_ID_TO_DEBIT" name="SVGS_ACCT_ID_TO_DEBIT" class="form-control" required="" onchange="FetchSavingsAcctDetails()">
                    <option value="">--- Select Account ----</option>
                    <option value=""></option>
                    <option value="">------- Individual Accounts -------</option>
                    <?php
                    // ... individual
                    $response_msg = GetCustSavingsAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                    $CONN_FLG = $response_msg["CONN_FLG"];
                    $CORE_RESP = $response_msg["CORE_RESP"];
                    $ACCTS_DATA = array();
                    $ACCTS_DATA = $CORE_RESP["data"];

                    // ... group
                    $response_msg2 = GetCustSavingsAccountsGroup($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                    $CONN_FLG2 = $response_msg2["CONN_FLG"];
                    $CORE_RESP2 = $response_msg2["CORE_RESP"];
                    $ACCTS_DATA2 = array();
                    $ACCTS_DATA2 = $CORE_RESP2["data"];

                    for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                      $row = $ACCTS_DATA[$i]["row"];
                      $svgs_id = $row[0];
                      $svgs_account_no = $row[1];
                      $product = $row[5];
                    ?>
                      <option value="<?php echo $svgs_id; ?>"><?php echo $svgs_account_no . " - " . $product; ?></option>
                    <?php
                    }
                    ?>
                    <option value=""></option>
                    <option value="">------- Group Accounts -------</option>
                    <?php
                    for ($v = 0; $v < sizeof($ACCTS_DATA2); $v++) {

                      $row = $ACCTS_DATA2[$v]["row"];
                      $svgs_id = $row[0];
                      $svgs_account_no = $row[1];
                      $product = $row[5];
                    ?>
                      <option value="<?php echo $svgs_id; ?>"><?php echo $svgs_account_no . " - " . $product; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Savings Account Balance:</label>
                  <input type="hidden" id="SVNGS_ACCT_BAL" name="SVNGS_ACCT_BAL">
                  <input type="text" id="SVNGS_ACCT_BAL_DISP" name="SVNGS_ACCT_BAL_DISP" class="form-control" disabled="">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Withdraw Amount Requested:</label>
                  <input type="number" id="WITHDRAW_AMT" name="WITHDRAW_AMT" class="form-control" required="">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Disburse Funds to:</label>
                  <select id="FIN_INST_ACCT" name="FIN_INST_ACCT" class="form-control" required="">
                    <option value="">-------</option>
                    <?php
                    $bank_acct_list = array();
                    $bank_acct_list = FetchCustFinInstAccts($CUST_ID);

                    for ($i = 0; $i < sizeof($bank_acct_list); $i++) {

                      $bank_acct = array();
                      $bank_acct = $bank_acct_list[$i];
                      $BB_RECORD_ID = $bank_acct['RECORD_ID'];
                      $BB_CUST_ID = $bank_acct['CUST_ID'];
                      $BB_BANK_ID = $bank_acct['BANK_ID'];
                      $BB_BANK_ACCOUNT = $bank_acct['BANK_ACCOUNT'];
                      $BB_DATE_ADDED = $bank_acct['DATE_ADDED'];
                      $BB_ACCT_STATUS = $bank_acct['ACCT_STATUS'];

                      $fin = array();
                      $fin = FetchFinInstitutionsById($BB_BANK_ID);
                      $BANK_NAME = $fin['FIN_INST_NAME'];
                    ?>
                      <option value="<?php echo $BB_BANK_ACCOUNT; ?>"><?php echo $BB_BANK_ACCOUNT . " ($BANK_NAME)"; ?></option>
                    <?php
                    }

                    ?>
                  </select>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Withdraw Purpose</label>
                  <textarea class="form-control" rows="3" name="REASON" id="REASON" required=""></textarea>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">

                  <?php
                  # ... Checking for Pending Applications
                  $CCCC_ID = $_SESSION['CST_USR_ID'];;
                  $Q = "SELECT count(*) as RTN_VALUE FROM svgs_withdraw_requests WHERE CUST_ID='$CCCC_ID' AND SVGS_APPLN_STATUS='PENDING'";
                  $Q_CNT = ReturnOneEntryFromDB($Q);

                  if ($Q_CNT > 0) {
                  ?>
                    <button type="submit" class="btn btn-info" disabled="">You have a pending savings withdraw application. Request Management to action the application</button>
                  <?php
                  } else {
                  ?>
                    <button type="submit" class="btn btn-success" name="btn_submit_appln">Submit Appln Details</button>
                  <?php
                  }
                  ?>

                </div>
              </form>



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