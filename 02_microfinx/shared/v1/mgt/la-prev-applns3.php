<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Details
$LN_APPLN_NO = mysql_real_escape_string(trim($_GET['k']));

# ... R001: FETCH LN APPLN DETAILS ............................................................................................#
$la = array();
$la =  FetchLoanApplnsById($LN_APPLN_NO);
$RECORD_ID = $la['RECORD_ID'];
$LN_APPLN_NO = $la['LN_APPLN_NO'];
$CUST_ID = $la['CUST_ID'];
$LN_PDT_ID = $la['LN_PDT_ID'];
$LN_APPLN_CREATION_DATE = $la['LN_APPLN_CREATION_DATE'];
$LN_APPLN_PROGRESS_STATUS = $la['LN_APPLN_PROGRESS_STATUS'];
$RQSTD_AMT = $la['RQSTD_AMT'];
$RQSTD_RPYMT_PRD = $la['RQSTD_RPYMT_PRD'];
$PURPOSE = $la['PURPOSE'];
$LN_APPLN_SUBMISSION_DATE = $la['LN_APPLN_SUBMISSION_DATE'];
$LN_APPLN_ASSMT_STATUS = $la['LN_APPLN_ASSMT_STATUS'];
$LN_APPLN_ASSMT_RMKS = $la['LN_APPLN_ASSMT_RMKS'];
$LN_APPLN_ASSMT_DATE = $la['LN_APPLN_ASSMT_DATE'];
$LN_APPLN_ASSMT_USER_ID = $la['LN_APPLN_ASSMT_USER_ID'];
$LN_APPLN_DOC_STATUS = $la['LN_APPLN_DOC_STATUS'];
$LN_APPLN_DOC_RMKS = $la['LN_APPLN_DOC_RMKS'];
$LN_APPLN_DOC_DATE = $la['LN_APPLN_DOC_DATE'];
$LN_APPLN_DOC_USER_ID = $la['LN_APPLN_DOC_USER_ID'];
$LN_APPLN_GRRTR_STATUS = $la['LN_APPLN_GRRTR_STATUS'];
$LN_APPLN_GRRTR_RMKS = $la['LN_APPLN_GRRTR_RMKS'];
$LN_APPLN_GRRTR_DATE = $la['LN_APPLN_GRRTR_DATE'];
$LN_APPLN_GRRTR_USER_ID = $la['LN_APPLN_GRRTR_USER_ID'];
$VERIF_STATUS = $la['VERIF_STATUS'];
$VERIF_DATE = $la['VERIF_DATE'];
$VERIF_RMKS = $la['VERIF_RMKS'];
$VERIF_USER_ID = $la['VERIF_USER_ID'];
$CC_FLG = $la['CC_FLG'];
$CC_RECEIVE_DATE = $la['CC_RECEIVE_DATE'];
$CC_HANDLER_WKFLW_ID = $la['CC_HANDLER_WKFLW_ID'];
$CC_STATUS = $la['CC_STATUS'];
$CC_STATUS_DATE = $la['CC_STATUS_DATE'];
$CC_RMKS = $la['CC_RMKS'];
$CREDIT_OFFICER_RCMNDTN_USER_ID = $la['CREDIT_OFFICER_RCMNDTN_USER_ID'];
$RCMNDTN_REQUEST_SEND_DATE = $la['RCMNDTN_REQUEST_SEND_DATE'];
$RCMNDD_APPLN_AMT = $la['RCMNDD_APPLN_AMT'];
$RCMNDTN_CUST_RESPONSE_DATE = $la['RCMNDTN_CUST_RESPONSE_DATE'];
$APPROVAL_STATUS = $la['APPROVAL_STATUS'];
$APPROVED_AMT = $la['APPROVED_AMT'];
$APPROVED_BY = $la['APPROVED_BY'];
$APPROVAL_DATE = $la['APPROVAL_DATE'];
$APPROVAL_RMKS = $la['APPROVAL_RMKS'];
$FLG_DISB_TO_SVNGS = $la['FLG_DISB_TO_SVNGS'];
$DISB_DATE = $la['DISB_DATE'];
$DISB_USER_ID = $la['DISB_USER_ID'];
$CORE_LOAN_ACCT_ID = $la['CORE_LOAN_ACCT_ID'];
$CORE_SVGS_ACCT_ID = $la['CORE_SVGS_ACCT_ID'];
$CUST_FIN_INST_ID = $la['CUST_FIN_INST_ID'];
$PROC_MODE = $la['PROC_MODE'];
$PROC_BATCH_NO = $la['PROC_BATCH_NO'];
$CORE_RESOURCE_ID = $la['CORE_RESOURCE_ID'];
$LN_APPLN_STATUS = $la['LN_APPLN_STATUS'];

# ... R002: GET LOAN PRODUCT DETAILS ............................................................................................#
$loan_product = array();
$response_msg = FetchLoanProductDetailsById($LN_PDT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$loan_product = $response_msg["CORE_RESP"];

# ... R003: LOAN PDT APPLN CONNFIG RULES ........................................................................................#
$appln_config = array();
$appln_config = FetchLoanApplnConfigByProductId($LN_PDT_ID);
$APPLN_CONFIG_ID = $appln_config['APPLN_CONFIG_ID'];
$APPLN_CONFIG_NAME = $appln_config['APPLN_CONFIG_NAME'];
$APPLN_TYPE_ID = $appln_config['APPLN_TYPE_ID'];
$PDT_ID = $appln_config['PDT_ID'];
$PDT_TYPE_ID = $appln_config['PDT_TYPE_ID'];


# ... R004: GET CUST BANK DETAILS ..............................................................................................#
$CUST_BANK_NAME = GetCustBankFromBankAcct($CUST_ID, $CUST_FIN_INST_ID);


# ... R004: GET CUSTOMER SAVINGS ACCT ID .......................................................................................#
$response_msg = FetchSavingsAcctById($CORE_SVGS_ACCT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$CORE_SVGS_ACCT_NUM = $CORE_RESP["accountNo"];
$CORE_SVGS_ACCT_BAL = $CORE_RESP["summary"]["accountBalance"];


# ... R005: LOAD CUSTOMER DETAILS .....................................................................................#
$cstmr = array();
$cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
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


# ... R006: LOAD LOAN VALIDATION SYSTEM RMKS .....................................................................................#
# ... Get Application Type Menu
$config_param_list = array();
$config_param_list = FetchLoanApplnConfigByProductId($PDT_ID);
$_SESSION["CONFIG_PARAM_LIST"] = $config_param_list;

# ... Define Appln Exclusion Checklist
$LN_APPLN_CHECKLIST = array("PRM_01","PRM_02","PRM_03","PRM_04","PRM_07","PRM_08","PRM_09","PRM_10","PRM_11","","");
$_SESSION["LN_APPLN_CHECKLIST"] = $LN_APPLN_CHECKLIST;


# ... R006: DETERMING SYSTEM COURSE OF ACTION .....................................................................................#
$VV = array();
if ( ($LN_APPLN_ASSMT_STATUS=="")||($LN_APPLN_DOC_STATUS=="")||($LN_APPLN_GRRTR_STATUS=="") ) {
  $VV["DISP_FLG"] = "DONT_DISPLAY";
} else {

  $VV["DISP_FLG"] = "DISPLAY";

  # ... Button type
  if ( ( ($LN_APPLN_ASSMT_STATUS=="GOOD")||($LN_APPLN_ASSMT_STATUS=="NOT_NEEDED") )&&
       ( ($LN_APPLN_DOC_STATUS=="GOOD")||($LN_APPLN_DOC_STATUS=="NOT_NEEDED") )&&
       ( ($LN_APPLN_GRRTR_STATUS=="GOOD")||($LN_APPLN_GRRTR_STATUS=="NOT_NEEDED") ) 
     ) {
    $VV["BTN_TYPE"] = "GOOD_BUTTON";
  } else {
    $VV["BTN_TYPE"] = "BAD_BUTTON";
  }


  # ... Message Type
  $VV["MSG"] = "";

  # ... Appln Assessment
  if ($LN_APPLN_ASSMT_STATUS=="NOT_NEEDED") {
    $VV["MSG"] .= "<br>Application Assessment ---------------------------- <span style='color: black; font-weight: bolder;'>[NOT_NEEDED]</span>";  
  } else if ($LN_APPLN_ASSMT_STATUS=="NOT_GOOD") {
    $VV["MSG"] .= "<br>Application Assessment ---------------------------- <span style='color: red; font-weight: bolder;'>[NOT_OKAY]</span>";  
  } else if ($LN_APPLN_ASSMT_STATUS=="GOOD") {
    $VV["MSG"] .= "<br>Application Assessment ---------------------------- <span style='color: green; font-weight: bolder;'>[OKAY]</span>";  
  }

  # ... Loan Doc Assessment
  if ($LN_APPLN_DOC_STATUS=="NOT_NEEDED") {
    $VV["MSG"] .= "<br>Loan Documents ------------------------------------ <span style='color: black; font-weight: bolder;'>[NOT_NEEDED]</span>";  
  } else if ($LN_APPLN_DOC_STATUS=="NOT_GOOD") {
    $VV["MSG"] .= "<br>Loan Documents ------------------------------------ <span style='color: red; font-weight: bolder;'>[NOT_OKAY]</span>";  
  } else if ($LN_APPLN_DOC_STATUS=="GOOD") {
    $VV["MSG"] .= "<br>Loan Documents ------------------------------------ <span style='color: green; font-weight: bolder;'>[OKAY]</span>";  
  }

  # ... Loan Guarrantor Assessment
  if ($LN_APPLN_GRRTR_STATUS=="NOT_NEEDED") {
    $VV["MSG"] .= "<br>Loan Guarrantor Assessment ------------------------ <span style='color: black; font-weight: bolder;'>[NOT_NEEDED]</span>";  
  } else if ($LN_APPLN_GRRTR_STATUS=="NOT_GOOD") {
    $VV["MSG"] .= "<br>Loan Guarrantor Assessment ------------------------ <span style='color: red; font-weight: bolder;'>[NOT_OKAY]</span>";  
  } else if ($LN_APPLN_GRRTR_STATUS=="GOOD") {
    $VV["MSG"] .= "<br>Loan Guarrantor Assessment ------------------------ <span style='color: green; font-weight: bolder;'>[OKAY]</span>";  
  }
}
//echo "<pre>".print_r($VV,true)."</pre>";


# ... R007: DETERMING LOAN APPLICATION VERIFIER  ..................................................................................#
$USER_DETAILS = array();
$USER_DETAILS = GetUserDetailsFromPortal($VERIF_USER_ID);
$VFD_USER_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
  
$response_msg = FetchUserDetailsFromCore($VFD_USER_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$sys_usr = $response_msg["CORE_RESP"];
$id = $sys_usr["id"];
$CORE_username = $sys_usr["username"];
$firstname = $sys_usr["firstname"];
$lastname = $sys_usr["lastname"];
$email = $sys_usr["email"];

$vfd_full_name = $CORE_username." (".$firstname." ".$lastname.")";

# ... R008: DETERMING LOAN APPLICATION APPROVER  ..................................................................................#
$USER_DETAILS = array();
$USER_DETAILS = GetUserDetailsFromPortal($APPROVED_BY);
$VFD_USER_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
  
$response_msg = FetchUserDetailsFromCore($VFD_USER_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$sys_usr = $response_msg["CORE_RESP"];
$id = $sys_usr["id"];
$CORE_username = $sys_usr["username"];
$firstname = $sys_usr["firstname"];
$lastname = $sys_usr["lastname"];
$email = $sys_usr["email"];

$apprv_full_name = $CORE_username." (".$firstname." ".$lastname.")";



# ... R007: DETERMING LOAN APPLICATION DISBURSER  ..................................................................................#
$USER_DETAILS = array();
$USER_DETAILS = GetUserDetailsFromPortal($DISB_USER_ID);
$VFD_USER_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
  
$response_msg = FetchUserDetailsFromCore($VFD_USER_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$sys_usr = $response_msg["CORE_RESP"];
$id = $sys_usr["id"];
$CORE_username = $sys_usr["username"];
$firstname = $sys_usr["firstname"];
$lastname = $sys_usr["lastname"];
$email = $sys_usr["email"];

$disb_full_name = $CORE_username." (".$firstname." ".$lastname.")";


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Prev Loan Applications", $APP_SMALL_LOGO); 

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

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>

            <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
            <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
            <div class="col-md-12 col-sm-12 col-xs-12">

              <!-- System Message Area -->
              <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>

              <div class="x_panel">
                <div class="x_title">
                <a href="la-prev-applns" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>LN_REF: <?php echo $LN_APPLN_NO; ?></h2>
                <div class="clearfix"></div>
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


            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLICATION DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLICATION DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
            <div class="col-md-6 col-xs-12" >
              <div class="x_panel">
                <div class="x_title">
                  <strong>SECTION 02:</strong> Loan Application Details
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Loan Amount Request</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($RQSTD_AMT); ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Repayment Period (<?php echo $repayment_frequency_type_value; ?>)</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($RQSTD_RPYMT_PRD); ?>">
                  </div>
                 
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Loan Purpose</label>
                    <textarea class="form-control" rows="3" disabled=""><?php echo $PURPOSE; ?></textarea>
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Customer Savings Account</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CORE_SVGS_ACCT_NUM; ?>">
                  </div>

                  
                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account Balance</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($CORE_SVGS_ACCT_BAL); ?>">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Customer Bank Acct for funds Transfer:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CUST_FIN_INST_ID." (".$CUST_BANK_NAME.")"; ?>">
                  </div>

                </div>
              </div>
            </div>  


            <!-- -- -- -- -- -- -- -- -- -- -- LOAN DOCUMENTS -- -- -- -- -- -- -- -- -- -- -- -->       
            <!-- -- -- -- -- -- -- -- -- -- -- LOAN DOCUMENTS -- -- -- -- -- -- -- -- -- -- -- -->       
            <div class="col-md-6 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <strong>SECTION 03:</strong> Loan Appln Documents
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  
                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr valign="top">
                        <th>#</th>
                        <th>File Name</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $LN_APPLN_FILES_LOCATION_CUST = GetSystemParameter("LN_APPLN_FILES_LOCATION_MGT")."/".$_SESSION['ORG_CODE'];
                      $LN_DIR = $LN_APPLN_FILES_LOCATION_CUST."/".$LN_APPLN_NO;
                      $dir = $LN_DIR;

                      $ln_file_list = array();
                      $ln_file_list = FetchLoanApplnFiles($LN_APPLN_NO);
                      for ($i=0; $i < sizeof($ln_file_list); $i++) { 
                        $ln_file = array();
                        $ln_file = $ln_file_list[$i];
                        $F_RECORD_ID = $ln_file['RECORD_ID'];
                        $F_LN_APPLN_NO = $ln_file['LN_APPLN_NO'];
                        $F_CODE = $ln_file['F_CODE'];
                        $F_NAME = $ln_file['F_NAME'];
                        $DATE_UPLOADED = $ln_file['DATE_UPLOADED'];
                        $F_STATUS = $ln_file['F_STATUS'];

                        $file_loc = $dir."/".$F_NAME;
                        $f_id = "f_".($i+1);
                        ?>
                        <tr valign="top">
                          <td><?php echo ($i+1); ?>. </td>
                          <td><?php echo $F_CODE; ?></td>
                          <td>
                            <a href="<?php echo $file_loc; ?>" class="btn btn-info btn-xs">View</a>                            
                          </td>
                        </tr>
                        <?php
                      }

                      ?>
                    </tbody>
                  </table>

                </div>

              </div>
            </div>


            <!-- -- -- -- -- -- -- -- -- -- -- LOAN GUARANTORS -- -- -- -- -- -- -- -- -- -- -- -->       
            <!-- -- -- -- -- -- -- -- -- -- -- LOAN GUARANTORS -- -- -- -- -- -- -- -- -- -- -- -->       
            <div class="col-md-6 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <strong>SECTION 04:</strong> Loan Appln Guarantors
                 
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">

                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr valign="top">
                        <th>#</th>
                        <th>Name</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $grrt_list = array();
                        $grrt_list = FetchLoanApplnGuarantors($LN_APPLN_NO);
                        for ($i=0; $i < sizeof($grrt_list); $i++) { 
                          $g = array();
                          $g = $grrt_list[$i];
                          $G_RECORD_ID = $g['RECORD_ID'];
                          $LN_APPLN_NO = $g['LN_APPLN_NO'];
                          $G_CUST_ID = $g['G_CUST_ID'];
                          $G_NAME = $g['G_NAME'];
                          $G_PHONE = $g['G_PHONE'];
                          $G_EMAIL = $g['G_EMAIL'];
                          $DATE_GENERATED = $g['DATE_GENERATED'];
                          $GUARANTORSHIP_STATUS = $g['GUARANTORSHIP_STATUS'];
                          $RMKS = $g['RMKS'];
                          $USED_FLG = $g['USED_FLG'];
                          $DATE_USED = $g['DATE_USED'];
                          $MIFOS_RESOURCE_ID = $g['MIFOS_RESOURCE_ID'];
                          ?>
                          <tr valign="top">
                            <td><?php echo ($i+1); ?>. </td>
                            <td><?php echo $G_NAME; ?></td>
                          </tr>
                          <?php
                        }
                                    
                      ?>
                    </tbody>
                  </table>

                </div>

              </div>
            </div>


            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN VERIFICATION REMARKS -- -- -- -- -- -- -- -- -- -- -- -->       
            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN VERIFICATION REMARKS -- -- -- -- -- -- -- -- -- -- -- -->       
            <div class="col-md-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <strong>SECTION 05:</strong> Verification Remarks
                 
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">

                  <table class="table table-bordered" style="font-size: 12px;">
                    <tr valign="top"><td><b>Summarized Remarks</b></td><td><?php echo $VV["MSG"]; ?></td></tr>
                    <tr><td><b>Detailed Remarks</b></td><td>
                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <label>Assesment Remarks</label>
                        <textarea class="form-control" disabled=""><?php echo $LN_APPLN_ASSMT_RMKS; ?></textarea>
                      </div>

                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <label>Loan Documents Remarks</label>
                        <textarea class="form-control" disabled=""><?php echo $LN_APPLN_DOC_RMKS; ?></textarea>
                      </div>

                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <label>Loan Guarrantor Remarks</label>
                        <textarea class="form-control" disabled=""><?php echo $LN_APPLN_DOC_RMKS; ?></textarea>
                      </div>

                    </td></tr>
                    <tr><td><b>Final Verification Remark</b></td><td><?php echo $VERIF_RMKS; ?></td></tr>
                    <tr><td><b>Verified On</b></td><td><?php echo $VERIF_DATE; ?></td></tr>
                    <tr><td><b>Final Verification Remark</b></td><td><?php echo $VERIF_RMKS; ?></td></tr>
                    <tr><td><b>Verified On</b></td><td><?php echo $VERIF_DATE; ?></td></tr>
                    <tr><td><b>Verified By</b></td><td><?php echo $vfd_full_name; ?></td></tr>
                  </table>


                </div>

              </div>
            </div>

            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN APPROVAL REMARKS -- -- -- -- -- -- -- -- -- -- -- -->       
            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN APPROVAL REMARKS -- -- -- -- -- -- -- -- -- -- -- -->       
            <div class="col-md-6 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <strong>SECTION 06:</strong> Loan Approval Remarks
                 
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">

                  <table class="table table-bordered" style="font-size: 12px;">
                    <tr><td><b>Approval Status</b></td><td><?php echo $APPROVAL_STATUS; ?></td></tr>
                    <tr><td><b>Approval Rmks</b></td><td><?php echo $APPROVAL_RMKS; ?></td></tr>
                    <tr><td><b>Approval Date</b></td><td><?php echo $APPROVAL_DATE; ?></td></tr>
                    <tr><td><b>Approved Amount</b></td><td><?php echo number_format($APPROVED_AMT); ?></td></tr>
                    <tr><td><b>Approved By</b></td><td><?php echo $apprv_full_name; ?></td></tr>
                  </table>

                </div>

              </div>
            </div>


            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN APPROVAL REMARKS -- -- -- -- -- -- -- -- -- -- -- -->       
            <!-- -- -- -- -- -- -- -- -- -- -- LOAN APPLN APPROVAL REMARKS -- -- -- -- -- -- -- -- -- -- -- -->       
            <div class="col-md-6 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <strong>SECTION 07:</strong> Loan Recommendation & Disbursement
                 
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">

                  <table class="table table-bordered" style="font-size: 12px;">
                    <tr><th colspan="2">Recommendation</th></tr>
                    <tr><td><b>Date for Recommendation Issuance</b></td><td><?php echo $RCMNDTN_REQUEST_SEND_DATE; ?></td></tr>
                    <tr><td><b>Loan Amount Recommended</b></td><td><?php echo number_format($APPROVED_AMT); ?></td></tr>
                    <tr><td><b>Date of Recommendation Response</b></td><td><?php echo $RCMNDTN_CUST_RESPONSE_DATE; ?></td></tr>
                    <tr><th colspan="2">&nbsp;</th></tr>
                    <tr><th colspan="2">Loan Disbursement</th></tr>
                    <tr><td><b>Is Loan Disbursed</b></td><td><?php echo $FLG_DISB_TO_SVNGS; ?></td></tr>
                    <tr><td><b>Disbursement Date</b></td><td><?php echo $DISB_DATE; ?></td></tr>
                    <tr><td><b>Loan Amount Disbursed</b></td><td><?php echo number_format($APPROVED_AMT); ?></td></tr>
                    <tr><td><b>Disbursed By</b></td><td><?php echo $disb_full_name; ?></td></tr>
                  </table>



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
