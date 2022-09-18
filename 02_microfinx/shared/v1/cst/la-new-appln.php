<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Checking for Pending Applications
$CURRENT_CST_ID = $_SESSION['CST_USR_ID'];
$Q = "SELECT count(*) as RTN_VALUE FROM loan_applns WHERE LN_APPLN_PROGRESS_STATUS='1' AND CUST_ID='$CURRENT_CST_ID'";
$Q_CNT = ReturnOneEntryFromDB($Q);

if ($Q_CNT>0) {
  $alert_type = "INFO";
  $alert_msg = "ALERT: You have an incomplete loan application. Redirecting in 4 seconds.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  header("Refresh:4; url='la-res-appln'");
}


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("New Loan Appln", $APP_SMALL_LOGO); 

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
                <h2>New Loan Application</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">  
                <!-- -- -- -- -- -- -- -- -- -- -- SMART WIZARD -- -- -- -- -- -- -- -- -- -- -- -->       
                <!-- -- -- -- -- -- -- -- -- -- -- SMART WIZARD -- -- -- -- -- -- -- -- -- -- -- -->       
                <div id="wizard" class="form_wizard wizard_horizontal">
                  <ul class="wizard_steps">
                    <li>
                      <a href="#step-1">
                        <span class="step_no" style="background-color: #006DAE;">1</span>
                        <span class="step_descr">
                          Step 1<br />
                          <small>Select Loan Product</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-2" >
                        <span class="step_no" style="background-color: #D1F2F2;">2</span>
                        <span class="step_descr">
                          Step 2<br />
                          <small>Review Personal Info.</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-3">
                        <span class="step_no" style="background-color: #D1F2F2;">3</span>
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


                <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS WIZARD -- -- -- -- -- -- -- -- -- -- -- -->       
                <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS WIZARD -- -- -- -- -- -- -- -- -- -- -- -->  
                <?php
                $loan_product_list = array();
                $response_msg = FetchLoanProducts($MIFOS_CONN_DETAILS);
                $CONN_FLG = $response_msg["CONN_FLG"];
                $CORE_RESP = $response_msg["CORE_RESP"];
                $loan_product_list = $response_msg["CORE_RESP"];
                //echo "<pre>".print_r($loan_product_list,true)."</pre>";

                if ( sizeof($loan_product_list)>0 ) {
                  ?>
                  <!-- -- -- -- -- -- -- -- -- -- -- TAB LIST -- -- -- -- -- -- -- -- -- -->
                  <!-- -- -- -- -- -- -- -- -- -- -- TAB LIST -- -- -- -- -- -- -- -- -- -->
                  <div class="col-xs-3">
                    <ul class="nav nav-tabs tabs-left">   
                      <?php
                      for ($i=0; $i < sizeof($loan_product_list); $i++) {
                        $loan_product = array();
                        $loan_product = $loan_product_list[$i];
                        $pdt_id = $loan_product["pdt_id"];
                        $pdt_name = $loan_product["pdt_name"];

                        $href_details = "#collapseOne".$pdt_id;

                        $active = ($i==0) ? "class=active" : "" ;
                        ?>
                        <li <?php echo $active; ?>><a href="<?php echo $href_details; ?>" data-toggle="tab" aria-expanded="false"><?php echo $pdt_name; ?></a></li>
                        <?php
                      }   // ...END..LOOP
                      ?>
                    </ul>
                  </div>

                  <!-- -- -- -- -- -- -- -- -- -- -- TAB CONTENT LIST -- -- -- -- -- -- -- -- -- -->
                  <!-- -- -- -- -- -- -- -- -- -- -- TAB CONTENT LIST -- -- -- -- -- -- -- -- -- -->
                  <div class="col-xs-9">
                    <div class="tab-content">
                      <?php
                      for ($i=0; $i < sizeof($loan_product_list); $i++) {
                        $loan_product = array();
                        $loan_product = $loan_product_list[$i];

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

                        // ... DISPLAYING LOAN PRODUCT INFORMATION TO THE CUSTOMER
                        $collapse_id = "collapseOne".$pdt_id;
                        $status = ($pdt_status=="loanProduct.active") ? "ACTIVE" : "INACTIVE" ;
                        $active = ($i==0) ? "active" : "" ;

                        // ... DISPLAYING THE PRODUCT INFO.
                        ?>
                        <div class="tab-pane <?php echo $active; ?>" id="<?php echo $collapse_id; ?>">
                          <fieldset style="border: solid; size: 1px; 
                                           border-color:#EEEEEE; border-radius: 7px; -moz-border-radius: 7px; -webkit-border-radius: 7px;">
                            <p class="lead" style="text-align: center;"><?php echo $pdt_name; ?></p>
                            <p>
                              <table class="table table-hover">
                                <tr bgcolor="#EEEEEE"><td colspan="3"><b>Product Description</b></td></tr>
                                <tr><td><b>Name</b></td><td><b>:</b></td><td><?php echo $pdt_name; ?></td></tr>
                                <tr><td><b>Short Name</b></td><td><b>:</b></td><td><?php echo $pdt_short_name; ?></td></tr>
                                <tr><td><b>Description</b></td><td><b>:</b></td><td><?php echo $pdt_descrition; ?></td></tr>
                                <tr><td><b>Status</b></td><td><b>:</b></td><td><?php echo $status; ?></td></tr>

                                <tr><td colspan="3">&nbsp;</td></tr>
                                <tr bgcolor="#EEEEEE"><td colspan="3"><b>Principal</b></td></tr>
                                <tr><td><b>Minimum</b></td><td><b>:</b></td><td><?php echo number_format($min_principal); ?></td></tr>
                                <tr><td><b>Maximum</b></td><td><b>:</b></td><td><?php echo number_format($max_principal); ?></td></tr>

                                <tr><td colspan="3">&nbsp;</td></tr>
                                <tr bgcolor="#EEEEEE"><td colspan="3"><b>Repayments Information</b></td></tr>
                                <tr><td><b>Minimum Repayment Period</b></td><td><b>:</b></td><td><?php echo $min_number_of_repayments." $repayment_frequency_type_value"; ?></td></tr>
                                <tr><td><b>Maximum Repayment Period</b></td><td><b>:</b></td><td><?php echo $max_number_of_repayments." $repayment_frequency_type_value"; ?></td></tr>

                                <tr><td><b>Repay Every</b></td><td><b>:</b></td><td><?php echo $repayment_every." ".$repayment_frequency_type_value; ?></td></tr>
                                <tr><td><b>Nominal Interest Rate</b></td><td><b>:</b></td>
                                  <td><?php echo $default_interest_rate_per_period."% ".$interest_rate_frequency_type_value; ?>
                                </td></tr>


                                <tr><td><b>Annual Interest Rate </b></td><td><b>:</b></td><td><?php echo $annual_interest_rate." Per Annum"; ?></td></tr>
                                <tr><td><b>Amortization </b></td><td><b>:</b></td><td><?php echo $amortization_type_value; ?></td></tr>
                                <tr><td><b>Interest Calculation Period</b></td><td><b>:</b></td><td><?php echo $interest_calculation_period_type_value; ?></td></tr>
                                <tr><td><b>Repayment Strategy</b></td><td><b>:</b></td><td><?php echo $transaction_processing_strategy_name; ?></td></tr>
                              </table>

                           
                              <a href="la-new-appln2?k=<?php echo $pdt_id."-".$pdt_name; ?>" class="btn btn-success btn-sm pull-right">SELECT: <?php echo strtoupper($pdt_name); ?></a>
                            </p>
                          </fieldset>
                        </div>
                        <?php
                      } # ... END..LOOP
                      ?>
                    </div>
                  </div>
                  <?php
                }
                else{
                  ?>
                  <br>
                  <br>
                  No Loan Products Defined. Please Contact Support
                  <?php
                }
                ?>     
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
