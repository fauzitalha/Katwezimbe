<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$LN_APPLN_NO = mysql_real_escape_string(trim($_GET['k']));

# ... Fetch Loan Application Details
$RECORD_ID = "";
$IS_TOP_UP = "";
$TOP_UP_LOAN_ID = "";
$CUST_ID = "";
$LN_PDT_ID = "";
$LN_APPLN_CREATION_DATE = "";
$LN_APPLN_PROGRESS_STATUS = "";
$RQSTD_AMT = "";
$RQSTD_RPYMT_PRD = "";
$PURPOSE = "";
$CORE_SVGS_ACCT_ID = "";
$CUST_FIN_INST_ID = "";

$loan_appln = array();
$loan_appln = FetchLoanApplnDetailsById($LN_APPLN_NO);
if (isset($loan_appln['RECORD_ID'])) {
  $RECORD_ID = $loan_appln['RECORD_ID'];
  $IS_TOP_UP = $loan_appln['IS_TOP_UP'];
  $TOP_UP_LOAN_ID = $loan_appln['TOP_UP_LOAN_ID'];
  $CUST_ID = $loan_appln['CUST_ID'];
  $LN_PDT_ID = $loan_appln['LN_PDT_ID'];
  $LN_APPLN_CREATION_DATE = $loan_appln['LN_APPLN_CREATION_DATE'];
  $LN_APPLN_PROGRESS_STATUS = $loan_appln['LN_APPLN_PROGRESS_STATUS'];
  $RQSTD_AMT = $loan_appln['RQSTD_AMT'];
  $RQSTD_RPYMT_PRD = $loan_appln['RQSTD_RPYMT_PRD'];
  $PURPOSE = $loan_appln['PURPOSE'];
  $CORE_SVGS_ACCT_ID = $loan_appln['CORE_SVGS_ACCT_ID'];
  $CUST_FIN_INST_ID = $loan_appln['CUST_FIN_INST_ID'];
}

# ... Fetch Savings Product Id
$LN_SVNGS_ACCT_NO = "";
$response_msg = FetchSavingsAcctById($CORE_SVGS_ACCT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$LN_SVNGS_ACCT_NO = $CORE_RESP["accountNo"];

# ... Fetch External Acct Details
$BB_BANK_ID = "";
$BB_BANK_ACCOUNT = "";              
$bank_acct = array();
$bank_acct = FetchCustFinInstAcctsById($CUST_ID, $CUST_FIN_INST_ID);
$BB_BANK_ID = $bank_acct['BANK_ID'];
$BB_BANK_ACCOUNT = $bank_acct['BANK_ACCOUNT'];

$fin = array();
$fin = FetchFinInstitutionsById($BB_BANK_ID);
$BANK_NAME = $fin['FIN_INST_NAME'];


# ... Fetch TopUpLoan Details
$TOP_UP_LOAN_ACCT_NUMBER = "";
if ($IS_TOP_UP=="YES") {
  $response_msg = FetchLoanAcctById($TOP_UP_LOAN_ID, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $Loan_Acct_No = $CORE_RESP["accountNo"];
  $Loan_Product = $CORE_RESP["loanProductName"];
  $TOP_UP_LOAN_ACCT_NUMBER = $Loan_Acct_No." - ".$Loan_Product;
}




# ... Fetch Loan Product Details
$loan_product = array();
$response_msg = FetchLoanProductDetailsById($LN_PDT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$loan_product = $response_msg["CORE_RESP"];


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Loan Appln Details", $APP_SMALL_LOGO); 

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

          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <h2>Loan Appln: <b><i><?php echo $LN_APPLN_NO; ?></i></b></h2>
                <a href="la-new-appln4?k=<?php echo $LN_APPLN_NO; ?>" class="btn btn-primary btn-sm pull-right">Continue</a>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <!-- -- -- -- -- -- -- -- -- -- -- SMART WIZARD -- -- -- -- -- -- -- -- -- -- -- -->       
                <!-- -- -- -- -- -- -- -- -- -- -- SMART WIZARD -- -- -- -- -- -- -- -- -- -- -- -->       
                <div id="wizard" class="form_wizard wizard_horizontal">
                    <ul class="wizard_steps">
                      <li>
                        <a href="#step-1">
                          <span class="step_no" style="background-color: #1ABB9C;">1</span>
                          <span class="step_descr">
                            Step 1<br />
                            <small>Select Loan Product</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-2" >
                          <span class="step_no" style="background-color: #1ABB9C;">2</span>
                          <span class="step_descr">
                            Step 2<br />
                            <small>Review Personal Info.</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-3">
                          <span class="step_no" style="background-color: #1ABB9C;">3</span>
                          <span class="step_descr">
                              Step 3<br />
                              <small>Enter Loan Details</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-4">
                          <span class="step_no" style="background-color: #D1F2F2;">4</span>
                          <span class="step_descr">
                              Step 4<br />
                              <small>Loan Documents & Guarantors</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-5">
                          <span class="step_no" style="background-color: #D1F2F2;">5</span>
                          <span class="step_descr">
                              Step 5<br />
                              <small>Terms & Conditions</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-6">
                          <span class="step_no" style="background-color: #D1F2F2;">6</span>
                          <span class="step_descr">
                              Step 6<br />
                              <small>Signing & Submission</small>
                          </span>
                        </a>
                      </li>
                    </ul>
                </div>
              </div>
            </div>
          </div> 

          <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->
          <div class="col-md-6 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION 01:</strong> Loan Product Details
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <?php
                if (sizeof($loan_product)>0) {
                  
                  // ... Product description
                  $pdt_id = $loan_product["pdt_id"];
                  $pdt_name = $loan_product["pdt_name"];
                  $pdt_short_name = $loan_product["pdt_short_name"];
                  $pdt_descrition = $loan_product["pdt_descrition"];
                  $pdt_status = $loan_product["pdt_status"];

                  // ... Principal Limits
                  $min_principal = $loan_product["min_principal"];
                  $default_principal = $loan_product["default_principal"];
                  $max_principal = $loan_product["max_principal"];
                  $MIN_PRINCIPAL = $min_principal;
                  $MAX_PRINCIPAL = $max_principal;

                  // ... Count of Repayments
                  $min_number_of_repayments = $loan_product["min_number_of_repayments"];
                  $default_number_of_repayments = $loan_product["default_number_of_repayments"];
                  $max_number_of_repayments = $loan_product["max_number_of_repayments"];

                  // ... Repayment Frequency
                  $repayment_every = $loan_product["repayment_every"];
                  $repayment_frequency_type_id = $loan_product["repayment_frequency_type_id"];
                  $repayment_frequency_type_code = $loan_product["repayment_frequency_type_code"];
                  $repayment_frequency_type_value = $loan_product["repayment_frequency_type_value"];

                  // ... Interest Rates Payable per Period
                  $min_interest_rate_per_period = $loan_product["min_interest_rate_per_period"];
                  $default_interest_rate_per_period = $loan_product["default_interest_rate_per_period"];
                  $max_interest_rate_per_period = $loan_product["max_interest_rate_per_period"];

                  // ... Interest Rate Frequency Type
                  $interest_rate_frequency_type_id = $loan_product["interest_rate_frequency_type_id"];
                  $interest_rate_frequency_type_code = $loan_product["interest_rate_frequency_type_code"];
                  $interest_rate_frequency_type_value = $loan_product["interest_rate_frequency_type_value"];

                  // ... Annual Interest Rate
                  $annual_interest_rate = $loan_product["annual_interest_rate"];

                  // ... Amortization Type Attributes
                  $amortization_type_id = $loan_product["amortization_type_id"];
                  $amortization_type_code = $loan_product["amortization_type_code"];
                  $amortization_type_value = $loan_product["amortization_type_value"];

                  // ... Interest Type Definition
                  $interest_type_id = $loan_product["interest_type_id"];
                  $interest_type_code = $loan_product["interest_type_code"];
                  $interest_type_value = $loan_product["interest_type_value"];

                  // ... Interest Calculation Period Type
                  $interest_calculation_period_type_id = $loan_product["interest_calculation_period_type_id"];
                  $interest_calculation_period_type_code = $loan_product["interest_calculation_period_type_code"];
                  $interest_calculation_period_type_value = $loan_product["interest_calculation_period_type_value"];

                  // ... Transaction Processing Strategy 
                  $transaction_processing_strategy_id = $loan_product["transaction_processing_strategy_id"];
                  $transaction_processing_strategy_name = $loan_product["transaction_processing_strategy_name"];

                  $status = ($pdt_status=="loanProduct.active") ? "ACTIVE" : "INACTIVE" ;
                  ?>
                  <table class="table table-bordered" style="font-size: 12px;">
                    <tr><td><b>Name</b></td><td><?php echo $pdt_name."($pdt_short_name)"; ?></td></tr>
                    <tr><td><b>Description</b></td><td><?php echo $pdt_descrition; ?></td></tr>
                    <tr><td><b>Principal</b></td>
                      <td>
                        {<b>MIN:</b> <?php echo number_format($min_principal); ?></b> - <b>MAX:</b><?php echo number_format($max_principal); ?>}
                      </td></tr>

                    <tr><td><b>Repayment Period</b></td>
                      <td>
                        {<b>MIN:</b> <?php echo $min_number_of_repayments." $repayment_frequency_type_value"; ?></b> - 
                         <b>MAX:</b> <?php echo $max_number_of_repayments." $repayment_frequency_type_value"; ?>}
                      </td></tr>


                    <tr><td><b>Repay Every</b></td><td><?php echo $repayment_every." ".$repayment_frequency_type_value; ?></td></tr>
                    <tr><td><b>Interest Rate</b></td>
                      <td><?php echo $default_interest_rate_per_period."% ".$interest_rate_frequency_type_value; ?>
                    </td></tr>
                    <tr><td><b>Amortization </b></td><td><?php echo $amortization_type_value." ($interest_type_value)"; ?></td></tr>
                  </table>


                  <?php
                } else {
                  ?>
                  Failed to display Loan Product Details
                  <?php
                }
                ?>
              </div>

            </div>
          </div>

          <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->
          <div class="col-md-6 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION 02:</strong> Loan Application Detailss
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                
                <table class="table table-bordered" style="font-size: 12px;">
                  <tr><td><b>Loan Appln Number</b></td><td><?php echo $LN_APPLN_NO; ?><td></tr>
                  <tr><td><b>This is a Loan TopUp</b></td><td><?php echo $IS_TOP_UP; ?><td></tr>
                  <?php
                  if ($IS_TOP_UP=="YES") {
                    ?>
                    <tr><td><b>Loan account to topUp</b></td><td><?php echo $TOP_UP_LOAN_ACCT_NUMBER; ?><td></tr>
                    <?php
                  }
                  ?>
                  <tr><td><b>Loan Amount Request</b></td><td><?php echo number_format($RQSTD_AMT); ?></td></tr>
                  <tr><td><b>Repayment Period (<?php echo $repayment_frequency_type_value; ?>)</b></td><td><?php echo $RQSTD_RPYMT_PRD; ?></td></tr>
                  <tr><td><b>Loan Purpose</b></td><td><?php echo $PURPOSE; ?></td></tr>
                  <tr><td><b>Savings Account</b></td><td><?php echo $LN_SVNGS_ACCT_NO; ?><td></tr>
                  <tr><td><b>External Account</b></td><td><?php echo $BB_BANK_ACCOUNT." ($BANK_NAME)"; ?></td></tr>
                  
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
