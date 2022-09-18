<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Handle Form Data
if (isset($_POST['btn_submit_appln'])) {
  
  $_SESSION['SHRS_ACCT_ID'] = mysql_real_escape_string(trim($_POST['SHRS_ACCT_ID']));
  $_SESSION['SHRS_OWNED'] = mysql_real_escape_string(trim($_POST['SHRS_OWNED']));
  $_SESSION['SHRS_VALUE'] = mysql_real_escape_string(trim($_POST['SHRS_VALUE']));
  $_SESSION['SHRS_PDT_NAME'] = mysql_real_escape_string(trim($_POST['SHRS_PDT_NAME']));
  $_SESSION['SHRS_UNIT_PRICE'] = mysql_real_escape_string(trim($_POST['SHRS_UNIT_PRICE']));
  $_SESSION['SHRS_M'] = mysql_real_escape_string(trim($_POST['SHRS_M']));
  $_SESSION['SHRS_MAX'] = mysql_real_escape_string(trim($_POST['SHRS_MAX']));
  $_SESSION['SVNGS_ACCT_ID'] = mysql_real_escape_string(trim($_POST['SVNGS_ACCT_ID']));
  $_SESSION['SVNGS_ACCT_BAL'] = mysql_real_escape_string(trim($_POST['SVNGS_ACCT_BAL']));
  $_SESSION['SHRS_CNT'] = mysql_real_escape_string(trim($_POST['SHRS_CNT']));
  $_SESSION['SHARES_COST'] = mysql_real_escape_string(trim($_POST['SHARES_COST']));


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

  $next_page = "shares-appln-buy2";
  NavigateToNextPage($next_page); 
}



?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Buy Shares Appln", $APP_SMALL_LOGO); 

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
        var selected_val = document.getElementById('SVNGS_ACCT_ID').value;

        //alert(selected_val);

        // ... Ajax
        $.ajax
        ({
          type:'post',
          url:'ajax-fetch-savings_acct_details.php',
          data:{
            svngs_id: selected_val
          },
          success:function(response) 
          {
            //console.log(response);

            // ... Handling of Db responses
            response = JSON.parse(response)
            var SVNGS_ACCT_BAL = response.SVNGS_ACCT_BAL;
            var SVNGS_ACCT_BAL_JS = response.SVNGS_ACCT_BAL;
            var num= parseFloat(SVNGS_ACCT_BAL).toLocaleString('en');
            //console.log(num);
            //console.log(response);
            $('#SVNGS_ACCT_BAL').val(SVNGS_ACCT_BAL);
            $('#SVNGS_ACCT_BAL_DISP').val(num);
          }
        });
      }

      // ... 02: Get Shares Acct Details
      function FetchSharesAcctDetails() {
        
        $('#SHRS_OWNED').val("");
        $('#SHRS_OWNED_DISP').val("");
        $('#SHRS_UNIT_PRICE').val("");
        $('#SHRS_UNIT_PRICE_DISP').val("");
        $('#SHRS_VALUE').val("");
        $('#SHRS_VALUE_DISP').val("");
        $('#SHRS_PDT_NAME').val("");
        $('#SHRS_PDT_NAME_DISP').val("");
        $('#SHRS_MAX').val("");
        $('#SHRS_MAX_DISP').val("");
        $('#SHRS_M').val("");
        $('#SHRS_M_DISP').val("");
        
        var selected_val = document.getElementById('SHRS_ACCT_ID').value;

        //alert(selected_val);

        // ... Ajax
        $.ajax
        ({
          type:'post',
          url:'ajax-fetch-shares_acct_details.php',
          data:{
            shares_acct_id: selected_val
          },
          success:function(response) 
          {
            //console.log(response);

            // ... Handling of Db responses
            response = JSON.parse(response)
            var TT_APPRVD_SHARES = response.TT_APPRVD_SHARES;
            var SHARES_UNIT_PRICE = response.SHARES_UNIT_PRICE;
            var SHARE_PDT_ID = response.SHARE_PDT_ID;
            var SHARE_PDT_NAME = response.SHARE_PDT_NAME;
            var SHARE_PDT_SHORT_NAME = response.SHARE_PDT_SHORT_NAME;
            var SHARE_PDT_DESC = response.SHARE_PDT_DESC;
            var SHARES_MAXIMUM = response.SHARES_MAXIMUM;
            var SHARE_VAL = TT_APPRVD_SHARES*SHARES_UNIT_PRICE;
            var DIFF_SHRS = SHARES_MAXIMUM - TT_APPRVD_SHARES;

            var num_TT_APPRVD_SHARES = parseFloat(TT_APPRVD_SHARES).toLocaleString('en');
            var num_SHARES_UNIT_PRICE = parseFloat(SHARES_UNIT_PRICE).toLocaleString('en');
            var num_SHARE_VAL = parseFloat(SHARE_VAL).toLocaleString('en');


            $('#SHRS_OWNED').val(TT_APPRVD_SHARES);
            $('#SHRS_OWNED_DISP').val(num_TT_APPRVD_SHARES);
            $('#SHRS_UNIT_PRICE').val(SHARES_UNIT_PRICE);
            $('#SHRS_UNIT_PRICE_DISP').val(num_SHARES_UNIT_PRICE);
            $('#SHRS_VALUE').val(SHARE_VAL);
            $('#SHRS_VALUE_DISP').val(num_SHARE_VAL);
            $('#SHRS_PDT_NAME').val(SHARE_PDT_NAME);
            $('#SHRS_PDT_NAME_DISP').val(SHARE_PDT_NAME);
            $('#SHRS_MAX').val(DIFF_SHRS);
            $('#SHRS_MAX_DISP').val(DIFF_SHRS);
            $('#SHRS_M').val(SHARES_MAXIMUM);
            $('#SHRS_M_DISP').val(SHARES_MAXIMUM);

            $("input").attr({
               "max" : DIFF_SHRS,        // substitute your own
               "min" : 1          // values (or variables) here
            });



          }
        });
      }

      // ... 03: CalculateSharesPurchaseCost
      function CalculateSharesPurchaseCost() {
        $('#SHARES_COST').val("");
        $('#SHARES_COST_DISP').val("");


        var SHRS_UNIT_PRICE = document.getElementById('SHRS_UNIT_PRICE').value;
        var SHRS_CNT = document.getElementById('SHRS_CNT').value;
        var SHARES_COST = document.getElementById('SHRS_CNT').value;
        var PUCHASE_VAL = SHRS_UNIT_PRICE * SHRS_CNT;

        var num_PUCHASE_VAL = parseFloat(PUCHASE_VAL).toLocaleString('en');

        $('#SHARES_COST').val(PUCHASE_VAL);
        $('#SHARES_COST_DISP').val(num_PUCHASE_VAL);
      }


      // ... 04: Validate Data
      function Validate() {
        var SVNGS_ACCT_BAL = document.getElementById('SVNGS_ACCT_BAL').value;
        var SHARES_COST = document.getElementById('SHARES_COST').value;
        
        var COST = parseFloat(SHARES_COST);
        var SVSS = parseFloat(SVNGS_ACCT_BAL);

        if (COST>SVSS) {
            alert("Cost of shares need exceeds the available account balance");
            return false;
        } else {
            return true;
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
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <h2>Buy Shares Appln</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <form method="post" id="sjdjshw272SJSJ" onsubmit="return Validate(this)">
                  
                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Select Shares Account</label>
                    <select id="SHRS_ACCT_ID" name="SHRS_ACCT_ID" class="form-control" required="" onchange="FetchSharesAcctDetails()">
                      <option value="">-------</option>
                      <?php
                      $response_msg = GetCustSharesAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $ACCTS_DATA = array();
                      $ACCTS_DATA = $CORE_RESP["data"];

                      for ($i=0; $i < sizeof($ACCTS_DATA); $i++) { 

                        $row = $ACCTS_DATA[$i]["row"];
                        $shares_id = $row[0];
                        $shares_account_no = $row[1]; 
                        ?>
                        <option value="<?php echo $shares_id; ?>"><?php echo $shares_account_no; ?></option>
                        <?php
                      }

                      ?>
                    </select>
                  </div>

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Current Shares Owned</label>
                    <input type="hidden" id="SHRS_OWNED" name="SHRS_OWNED">
                    <input type="number" id="SHRS_OWNED_DISP" name="SHRS_OWNED_DISP" class="form-control" disabled="">
                  </div>

                   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Current Share Value</label>
                    <input type="hidden" id="SHRS_VALUE" name="SHRS_VALUE">
                    <input type="text" id="SHRS_VALUE_DISP" name="SHRS_VALUE_DISP" class="form-control" disabled="">
                  </div>


                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Share Product Name</label>
                    <input type="hidden" id="SHRS_PDT_NAME" name="SHRS_PDT_NAME">
                    <input type="text" id="SHRS_PDT_NAME_DISP" name="SHRS_PDT_NAME_DISP" class="form-control" disabled="">
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Share Unit Price</label>
                    <input type="hidden" id="SHRS_UNIT_PRICE" name="SHRS_UNIT_PRICE">
                    <input type="text" id="SHRS_UNIT_PRICE_DISP" name="SHRS_UNIT_PRICE_DISP" class="form-control" disabled="">
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Max Shares Per Client</label>
                    <input type="hidden" id="SHRS_M" name="SHRS_M">
                    <input type="text" id="SHRS_M_DISP" name="SHRS_M_DISP" class="form-control" disabled="">
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Maximum Shares Purchasable</label>
                    <input type="hidden" id="SHRS_MAX" name="SHRS_MAX">
                    <input type="text" id="SHRS_MAX_DISP" name="SHRS_MAX_DISP" class="form-control" disabled="">
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Select Savings Account to Debitt</label>
                    <select id="SVNGS_ACCT_ID" name="SVNGS_ACCT_ID" class="form-control" required="" onchange="FetchSavingsAcctDetails()">
                      <option value="">-------</option>
                      <?php
                      $response_msg = GetCustSavingsAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $ACCTS_DATA = array();
                      $ACCTS_DATA = $CORE_RESP["data"];

                      for ($i=0; $i < sizeof($ACCTS_DATA); $i++) { 

                        $row = $ACCTS_DATA[$i]["row"];
                        $svgs_id = $row[0];
                        $svgs_account_no = $row[1]; 
                        ?>
                        <option value="<?php echo $svgs_id; ?>"><?php echo $svgs_account_no; ?></option>
                        <?php
                      }

                      ?>
                    </select>
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account Balance</label>
                    <input type="hidden" id="SVNGS_ACCT_BAL" name="SVNGS_ACCT_BAL">
                    <input type="text" id="SVNGS_ACCT_BAL_DISP" name="SVNGS_ACCT_BAL_DISP" class="form-control" disabled="">
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Enter Number of shares to buy</label>
                    <input type="number" id="SHRS_CNT" max="???" min="???" name="SHRS_CNT" class="form-control" required="" onkeyup="CalculateSharesPurchaseCost()">
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Amount to be debitted</label>
                    <input type="hidden" id="SHARES_COST" name="SHARES_COST">
                    <input type="text" id="SHARES_COST_DISP" name="SHARES_COST_DISP" class="form-control" disabled="">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">

                    <?php
                    # ... Checking for Pending Applications
                    $CCCC_ID = $_SESSION['CST_USR_ID'];;
                    $Q = "SELECT count(*) as RTN_VALUE FROM shares_appln_requests WHERE CUST_ID='$CCCC_ID' AND SHARES_APPLN_STATUS='PENDING'";
                    $Q_CNT = ReturnOneEntryFromDB($Q);

                    if ($Q_CNT>0) {
                      ?>
                      <button type="submit" class="btn btn-info" disabled="">You have a pending buy shares application. Request Management to action the application</button>
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
