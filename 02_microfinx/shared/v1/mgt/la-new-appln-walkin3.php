<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Details
$data = mysql_real_escape_string(trim($_GET['k']));
$data_details = explode('-', $data);
$pdt_id = $data_details[0];
$pdt_name = $data_details[1];
$CUST_CORE_ID = $data_details[2];


# ... Get Loan Product Information
$loan_product = array();
$response_msg = FetchLoanProductDetailsById($pdt_id, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$loan_product = $response_msg["CORE_RESP"];

# ... Get Loan Product Application Config rules
$appln_config = array();
$appln_config = FetchLoanApplnConfigByProductId($pdt_id);
$APPLN_CONFIG_ID = $appln_config['APPLN_CONFIG_ID'];
$APPLN_CONFIG_NAME = $appln_config['APPLN_CONFIG_NAME'];
$APPLN_TYPE_ID = $appln_config['APPLN_TYPE_ID'];
$PDT_ID = $appln_config['PDT_ID'];
$PDT_TYPE_ID = $appln_config['PDT_TYPE_ID'];

# ... Get Borrowing Loan Ratios
$LOAN_TO_SVNGS_RATIO = GetSystemParameter("LOAN_TO_SVNGS_RATIO");
$LOAN_TO_SHARES_RATIO = GetSystemParameter("LOAN_TO_SHARES_RATIO");


# ... Get Application Type Menu
$config_param_list = array();
$config_param_list = FetchLoanApplnConfigByProductId($PDT_ID);
$_SESSION["CONFIG_PARAM_LIST"] = $config_param_list;


# ...Submit Appliction Details
if (isset($_POST['btn_submit_appln'])) {
  
  // ... Data Variables
  $data = mysql_real_escape_string(trim($_POST['k']));
  $data_details = explode('-', $data);
  $PDT_ID = $data_details[0];
  $PDT_NAME = $data_details[1];
  $CUST_CORE_ID = $data_details[2];

  // ... Form Data
  //$FIN_INST_ACCT = mysql_real_escape_string(trim($_POST['FIN_INST_ACCT']));
  $FIN_INST_ACCT = "";

  $IS_TOP_UP_FLG = "";
  $TOPUP_LN_ACCT_CLOSE_ID = "";
  $IS_WALK_IN = "YES";
  $IS_TOP_UP = mysql_real_escape_string(trim($_POST['IS_TOP_UP']));
  if ($IS_TOP_UP=="NEW_LOAN") {
    $IS_TOP_UP_FLG = "NO";
    $TOPUP_LN_ACCT_CLOSE_ID = "";
  }
  else if ($IS_TOP_UP=="TOPUP_LOAN") {
    $IS_TOP_UP_FLG = "YES";
    $TOPUP_LN_ACCT_CLOSE_ID = mysql_real_escape_string(trim($_POST['TOPUP_LOAN_ACCT_ID']));
  }

  $LN_AMT_RQSTD = mysql_real_escape_string(trim($_POST['LN_AMT_RQSTD']));
  $CONS_NETPAY = mysql_real_escape_string(trim($_POST['CONS_NETPAY']));
  $NETPAY_VALUE = (isset($_POST['TXT_NETPAY'])? mysql_real_escape_string(trim($_POST['TXT_NETPAY'])) : "" );
  $CONS_GRATUITY = mysql_real_escape_string(trim($_POST['CONS_GRATUITY']));
  $GRATUITY_VALUE = (isset($_POST['TXT_GRATUITY'])? mysql_real_escape_string(trim($_POST['TXT_GRATUITY'])) : "" );
  $CONS_FSV = mysql_real_escape_string(trim($_POST['CONS_FSV']));
  $FSV_VALUE = (isset($_POST['TXT_FSV'])? mysql_real_escape_string(trim($_POST['TXT_FSV'])) : "" );
  $LN_RPYMT_PRD = mysql_real_escape_string(trim($_POST['LN_RPYMT_PRD']));
  $LN_PURPOSE = mysql_real_escape_string(trim($_POST['LN_PURPOSE']));
  $SVNGS_ACCT_ID = mysql_real_escape_string(trim($_POST['SVNGS_ACCT_ID']));
  $SVNGS_ACCT_BAL = mysql_real_escape_string(trim($_POST['SVNGS_ACCT_BAL']));
  $SHRS_ACCT_ID = mysql_real_escape_string(trim($_POST['SHRS_ACCT_ID']));
  $SHRS_OWNED = mysql_real_escape_string(trim($_POST['SHRS_OWNED']));
  $SHRS_UNIT_PRICE = mysql_real_escape_string(trim($_POST['SHRS_UNIT_PRICE']));
  $SHRS_VALUE = mysql_real_escape_string(trim($_POST['SHRS_VALUE']));

  # ... DB_VALUES 
  $CUST_ID = "CORE_ID-".$CUST_CORE_ID;
  $LN_PDT_ID = $PDT_ID;
  $LN_APPLN_CREATION_DATE = GetCurrentDateTime();
  $LN_APPLN_PROGRESS_STATUS = "1";
  $RQSTD_AMT = $LN_AMT_RQSTD;
  $RQSTD_RPYMT_PRD = $LN_RPYMT_PRD;
  $PURPOSE = $LN_PURPOSE;
  $CORE_SVGS_ACCT_ID = $SVNGS_ACCT_ID;
  $CUST_FIN_INST_ID = $FIN_INST_ACCT;

  # ... SQL INSERT
  $q = "INSERT INTO loan_applns(IS_WALK_IN,IS_TOP_UP,TOP_UP_LOAN_ID,CUST_ID,LN_PDT_ID,LN_APPLN_CREATION_DATE,LN_APPLN_PROGRESS_STATUS
  ,RQSTD_AMT,CONS_NETPAY,NETPAY_VALUE,CONS_GRATUITY,GRATUITY_VALUE,CONS_FSV,FSV_VALUE,RQSTD_RPYMT_PRD,PURPOSE,CORE_SVGS_ACCT_ID
  ,CUST_FIN_INST_ID) VALUES('$IS_WALK_IN','$IS_TOP_UP_FLG','$TOPUP_LN_ACCT_CLOSE_ID','$CUST_ID','$LN_PDT_ID','$LN_APPLN_CREATION_DATE',
  '$LN_APPLN_PROGRESS_STATUS','$RQSTD_AMT','$CONS_NETPAY','$NETPAY_VALUE','$CONS_GRATUITY','$GRATUITY_VALUE','$CONS_FSV','$FSV_VALUE',
  '$RQSTD_RPYMT_PRD','$PURPOSE','$CORE_SVGS_ACCT_ID','$CUST_FIN_INST_ID')";

  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];

  # ... Process Entity System ID (Role ID)
  $id_prefix = "LAWK";
  $id_len = 15;

  if ($IS_TOP_UP_FLG=="YES") {
    $id_prefix = "LTPWK";
    $id_len = 13;
  }
  $id_record_id = $RECORD_ID;
  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
  $LN_APPLN_NO = $ENTITY_ID;

  # ... Updating the role id
  $q2 = "UPDATE loan_applns SET LN_APPLN_NO='$LN_APPLN_NO' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "CREATE_WALK_IN";
    $EVENT_OPERATION = "ADD_LOAN_APPLN_DETAILS_WALK_IN";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $RECORD_ID;
    $OTHER_DETAILS = $LN_APPLN_NO;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $next_page = "la-new-appln-walkin3_3?k=$LN_APPLN_NO";
    NavigateToNextPage($next_page);
  }
}




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
    <script type="text/javascript">

      // ... 00: The OnLoad Method
      $(document).ready(function() {
        
        // ... Perform Loan Topup Checkup
        IsLoanTopUp();

        // ... Perform Savings Check
        FetchSavingsAcctDetails();

        // ... Perform Shares Check
        FetchSharesAcctDetails();

        // ... 05: CONS_CONS_NETPAY
        CONS_CONS_NETPAY();

        // ... 06: CONS_CONS_GRATUITY
        CONS_CONS_GRATUITY();

        // ... 07: CONS_CONS_GRATUITY
        CONS_CONS_FSV();

        // .....
        CalculateEligibleAmount();


      });

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
            CalculateEligibleAmount();
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
            var SHARE_VAL = TT_APPRVD_SHARES*SHARES_UNIT_PRICE;

            var num_TT_APPRVD_SHARES = parseFloat(TT_APPRVD_SHARES).toLocaleString('en');
            var num_SHARES_UNIT_PRICE = parseFloat(SHARES_UNIT_PRICE).toLocaleString('en');
            var num_SHARE_VAL = parseFloat(SHARE_VAL).toLocaleString('en');


            $('#SHRS_OWNED').val(TT_APPRVD_SHARES);
            $('#SHRS_OWNED_DISP').val(num_TT_APPRVD_SHARES);
            $('#SHRS_UNIT_PRICE').val(SHARES_UNIT_PRICE);
            $('#SHRS_UNIT_PRICE_DISP').val(num_SHARES_UNIT_PRICE);
            $('#SHRS_VALUE').val(SHARE_VAL);
            $('#SHRS_VALUE_DISP').val(num_SHARE_VAL);
            CalculateEligibleAmount();
          }
        });
      }

      // ... 03: Calculate Eligible amount
      function CalculateEligibleAmount() {
        
        var SVNGS_ACCT_BAL = document.getElementById('SVNGS_ACCT_BAL').value;
        var SHRS_VALUE = document.getElementById('SHRS_VALUE').value;
        var LNR_SVNGS = document.getElementById('LNR_SVNGS').value;
        var LNR_SHARES = document.getElementById('LNR_SHARES').value;
        var TXT_NETPAY = document.getElementById('TXT_NETPAY').value;
        var TXT_GRATUITY = document.getElementById('TXT_GRATUITY').value;
        var TXT_FSV = document.getElementById('TXT_FSV').value;

        
        var LNR_C_SVNGS = SVNGS_ACCT_BAL * LNR_SVNGS;
        var LNR_C_SHARES = SHRS_VALUE * LNR_SHARES;

        var ELL_AMT = Number(LNR_C_SVNGS) + Number(LNR_C_SHARES) + Number(TXT_NETPAY) + Number(TXT_GRATUITY) + Number(TXT_FSV);


        var ELL_AMT_DISP= parseFloat(ELL_AMT).toLocaleString('en');
        $('#LN_ELIGIBLE').val(ELL_AMT_DISP); 

        // ... Enforcing maxium loan amount
        document.getElementById("LN_AMT_RQSTD").max = ELL_AMT;
      }

      // ... 04: IsLoanTopUp
      function IsLoanTopUp(){

        var is_top_up = document.getElementById('IS_TOP_UP').value;
        if (is_top_up=="NEW_LOAN") {
          document.getElementById("TOPUP_LOAN_ACCT_ID").value="";
          document.getElementById("TOPUP_LOAN_ACCT_ID").disabled=true;
        }
        else if (is_top_up=="TOPUP_LOAN") {
          document.getElementById("TOPUP_LOAN_ACCT_ID").disabled=false;
        }
        else if (is_top_up=="") {
          document.getElementById("TOPUP_LOAN_ACCT_ID").disabled=false;
          document.getElementById("TOPUP_LOAN_ACCT_ID").value="";
        }
      }

      // ... 05: CONS_CONS_NETPAY
      function CONS_CONS_NETPAY(){

        var apply_flg = document.getElementById('CONS_NETPAY').value;
        if (apply_flg=="YES") {
          document.getElementById("TXT_NETPAY").value=0;
          document.getElementById("TXT_NETPAY").disabled=false;
        }
        else if (apply_flg=="NO") {
          document.getElementById("TXT_NETPAY").value=0;
          document.getElementById("TXT_NETPAY").disabled=true;
        }
        else if (apply_flg=="") {
          document.getElementById("TXT_NETPAY").value=0;
          document.getElementById("TXT_NETPAY").disabled=false;
        }
        CalculateEligibleAmount();
      }

      // ... 06: CONS_CONS_GRATUITY
      function CONS_CONS_GRATUITY(){

        var apply_flg = document.getElementById('CONS_GRATUITY').value;
        if (apply_flg=="YES") {
          document.getElementById("TXT_GRATUITY").value=0;
          document.getElementById("TXT_GRATUITY").disabled=false;
        }
        else if (apply_flg=="NO") {
          document.getElementById("TXT_GRATUITY").value=0;
          document.getElementById("TXT_GRATUITY").disabled=true;
        }
        else if (apply_flg=="") {
          document.getElementById("TXT_GRATUITY").value=0;
          document.getElementById("TXT_GRATUITY").disabled=false;
        }
        CalculateEligibleAmount();
      }

      // ... 07: CONS_CONS_FSV
      function CONS_CONS_FSV(){

        var apply_flg = document.getElementById('CONS_FSV').value;
        if (apply_flg=="YES") {
          document.getElementById("TXT_FSV").value=0;
          document.getElementById("TXT_FSV").disabled=false;
        }
        else if (apply_flg=="NO") {
          document.getElementById("TXT_FSV").value=0;
          document.getElementById("TXT_FSV").disabled=true;
        }
        else if (apply_flg=="") {
          document.getElementById("TXT_FSV").value=0;
          document.getElementById("TXT_FSV").disabled=false;
        }
        CalculateEligibleAmount();
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

          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <a href="la-new-appln2?k=<?php echo $pdt_id."-".$pdt_name; ?>" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>STEP 03: New Loan Appln</h2>
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
                          <span class="step_no" style="background-color: #006DAE;">3</span>
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

          <form method="post">
            <input type="hidden" id="k" name="k" value="<?php echo $pdt_id."-".$pdt_name."-".$CUST_CORE_ID; ?>">

            <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
            <!-- -- -- -- -- -- -- -- -- -- -- LOAN PRODUCT DETAILS -- -- -- -- -- -- -- -- -- -- -- -->
            <?php
            $MIN_PRINCIPAL = "";
            $MAX_PRINCIPAL = "";
            ?>       
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


            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLICATION DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLICATION DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
            <div class="col-md-6 col-xs-12" >
              <div class="x_panel">
                <div class="x_title">
                  <strong>SECTION 02:</strong> Loan Application Details
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">

                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Customer wants:</label>
                    <select id="IS_TOP_UP" name="IS_TOP_UP" class="form-control" required="" onchange="IsLoanTopUp()">
                      <option value="">-------</option>
                      <option value="NEW_LOAN">A fresh loan</option>
                      <option value="TOPUP_LOAN">A topup loan</option>
                    </select>
                  </div>

                  <div class="col-md-8 col-sm-12 col-xs-12 form-group">
                    <label>Select Loan account to Topup</label>
                    <select id="TOPUP_LOAN_ACCT_ID" name="TOPUP_LOAN_ACCT_ID" class="form-control" required="">
                      <option value="">-------</option>
                      <?php
                      $response_msg = GetCustLoansAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $ACCTS_DATA = array();
                      $ACCTS_DATA = $CORE_RESP["data"];

                      //["843","1421000000843","UGX","36","1","SALARY LOAN","1421",null,"300"]

                      for ($i=0; $i < sizeof($ACCTS_DATA); $i++) { 

                        $row = $ACCTS_DATA[$i]["row"];
                        $ll_loan_id = $row[0];
                        $ll_loan_account_no = $row[1]; 
                        $ll_loan_pdt_det = $row[5]; 
                        ?>
                        <option value="<?php echo $ll_loan_id; ?>"><?php echo $ll_loan_account_no." - ".$ll_loan_pdt_det; ?></option>
                        <?php
                      }

                      ?>
                    </select>
                  </div>

                  <div class="col-md-8 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account</label>
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
                        $svgs_pdt_det = $row[5]; 

                        //["36","2210000000036","UGX","36","1","GENERAL SAVER","2210",null,"300"]
                        ?>
                        <option value="<?php echo $svgs_id; ?>"><?php echo $svgs_account_no." - ".$svgs_pdt_det; ?></option>
                        <?php
                      }

                      ?>
                    </select>
                  </div>
                 
                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Savings Balance</label>
                    <input type="hidden" id="SVNGS_ACCT_BAL" name="SVNGS_ACCT_BAL">
                    <input type="text" id="SVNGS_ACCT_BAL_DISP" name="SVNGS_ACCT_BAL_DISP" class="form-control" disabled="">
                  </div>


                  <div class="col-md-8 col-sm-12 col-xs-12 form-group">
                    <label>Shares Account</label>
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
                        $shares_pdt_det = $row[5]; 

                        //["1039","000001039","UGX","36","1","SACCO SHARES","701", "300"]
                        ?>
                        <option value="<?php echo $shares_id; ?>"><?php echo $shares_account_no." - ".$shares_pdt_det; ?></option>
                        <?php
                      }

                      ?>
                    </select>
                  </div>
                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Shares Owned</label>
                    <input type="hidden" id="SHRS_OWNED" name="SHRS_OWNED">
                    <input type="number" id="SHRS_OWNED_DISP" name="SHRS_OWNED_DISP" class="form-control" disabled="">
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Share UnitPrice</label>
                    <input type="hidden" id="SHRS_UNIT_PRICE" name="SHRS_UNIT_PRICE">
                    <input type="text" id="SHRS_UNIT_PRICE_DISP" name="SHRS_UNIT_PRICE_DISP" class="form-control" disabled="">
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Share Value</label>
                    <input type="hidden" id="SHRS_VALUE" name="SHRS_VALUE">
                    <input type="text" id="SHRS_VALUE_DISP" name="SHRS_VALUE_DISP" class="form-control" disabled="">
                  </div>


                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>NetPay:</label>
                    <select id="CONS_NETPAY" name="CONS_NETPAY" class="form-control" required="" onchange="CONS_CONS_NETPAY()">
                      <option value="">-------</option>
                      <option value="NO">Dont Apply</option>
                      <option value="YES">Apply</option>
                    </select>
                  </div>
                  <div class="col-md-8 col-sm-12 col-xs-12 form-group">
                    <label>Net Pay Value</label>
                    <input type="number" id="TXT_NETPAY" name="TXT_NETPAY" class="form-control" onchange="CalculateEligibleAmount()">
                  </div>


                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Gratuity:</label>
                    <select id="CONS_GRATUITY" name="CONS_GRATUITY" class="form-control" required="" onchange="CONS_CONS_GRATUITY()">
                      <option value="">-------</option>
                      <option value="NO">Dont Apply</option>
                      <option value="YES">Apply</option>
                    </select>
                  </div>
                  <div class="col-md-8 col-sm-12 col-xs-12 form-group">
                    <label>Gratuity Value</label>
                    <input type="number" id="TXT_GRATUITY" name="TXT_GRATUITY" class="form-control" onchange="CalculateEligibleAmount()">
                  </div>


                  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <label>Forced Sale Value:</label>
                    <select id="CONS_FSV" name="CONS_FSV" class="form-control" required="" onchange="CONS_CONS_FSV()">
                      <option value="">-------</option>
                      <option value="NO">Dont Apply</option>
                      <option value="YES">Apply</option>
                    </select>
                  </div>
                  <div class="col-md-8 col-sm-12 col-xs-12 form-group">
                    <label>The Forced Sale Value</label>
                    <input type="number" id="TXT_FSV" name="TXT_FSV" class="form-control" onchange="CalculateEligibleAmount()">
                  </div>




                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Amount Eligible for Borrowing </label>
                    <button type="button" class="btn btn-sm btn-dark pull-right" name="btn_calculate" onclick="CalculateEligibleAmount();">Calculate</button>
                    <input type="hidden" id="LNR_SVNGS" name="LNR_SVNGS" value="<?php echo $LOAN_TO_SVNGS_RATIO; ?>">
                    <input type="hidden" id="LNR_SHARES" name="LNR_SHARES" value="<?php echo $LOAN_TO_SHARES_RATIO; ?>">
                    <input type="text" id="LN_ELIGIBLE" name="LN_ELIGIBLE" class="form-control" disabled="" value="">
                  </div>


                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Loan Amount Requested</label>
                    <input type="number" class="form-control" id="LN_AMT_RQSTD" name="LN_AMT_RQSTD" min="<?php echo $MIN_PRINCIPAL; ?>" max="<?php echo $MAX_PRINCIPAL; ?>" required="">
                  </div>


                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Repayment Period (<?php echo $repayment_frequency_type_value; ?>) </label>
                    <select class="form-control" id="LN_RPYMT_PRD" name="LN_RPYMT_PRD" required="">
                      <option value="">-------</option>
                      <?php
                      for ($i=$min_number_of_repayments; $i<=$max_number_of_repayments ; $i++) { 
                        ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Loan Purpose</label>
                    <textarea class="form-control" rows="3" name="LN_PURPOSE" id="LN_PURPOSE" required=""></textarea>
                  </div>

                  <button type="submit" class="btn btn-success pull-right" name="btn_submit_appln">Submit Appln Details</button>

                </div>
              </div>
            </div>




          </form>

    
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
