<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Variables
$dd1 = mysql_real_escape_string(trim($_POST['dd1']));
$mm1 = mysql_real_escape_string(trim($_POST['mm1']));
$yy1 = mysql_real_escape_string(trim($_POST['yy1']));
$date11 = $yy1."-".$mm1."-".$dd1;

$dd2 = mysql_real_escape_string(trim($_POST['dd2']));
$mm2 = mysql_real_escape_string(trim($_POST['mm2']));
$yy2 = mysql_real_escape_string(trim($_POST['yy2']));
$date22 = $yy2."-".$mm2."-".$dd2;

$time22 = strtotime($date22."+1 days");
$date33 = date('Y-m-d', $time22);


$START_DATE = $date11;
$END_DATE = $date33;
# ............................................................................................


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Get Disbursed Loans", $APP_SMALL_LOGO); 

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


            <div class="x_panel">
              <div class="x_title">
                <a href="la-get-disbursed-list" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Get Disbursed Loans List</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <table class="table table-striped table-bordered" style="font-size: 11px;">
                  <tr bgcolor="#EEE"><th colspan="2">Loans applications disbursed between;</th></tr>
                  <tr><th width="20%">Start Date</th><td><?php echo date('d-M-Y', strtotime($START_DATE)); ?></td></tr>
                  <tr><th>End Date</th><td><?php echo date('d-M-Y', strtotime($END_DATE)); ?></td></tr>
                </table>

                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top" bgcolor="#EEE">
                      <th colspan="7">Loan Disbursal List</th>
                      <th>
                        <form method="post" id="accce23Y89" action="la-get-disbursed-list3">
                          <input type="hidden" id="START_DATE" name="START_DATE" value="<?php echo $START_DATE; ?>">
                          <input type="hidden" id="END_DATE" name="END_DATE" value="<?php echo $END_DATE; ?>">
                          <button type="submit" class="btn btn-success btn-xs" name="btn_excel_export">Export 2 Excel</button>
                        </form>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Appln Ref</th>
                      <th>Client Name</th>
                      <th>Amount</th>
                      <th>Product</th>
                      <th>Disbursement Date</th>
                      <th>Ext Orgn</th>
                      <th>Ext Orgn Acct No</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $la_list = array();
                    $la_list = FetchDisbursedLoanApplns($START_DATE, $END_DATE);
                    for ($i=0; $i < sizeof($la_list); $i++) {
                      $la = array();
                      $la = $la_list[$i];
                      $RECORD_ID = $la['RECORD_ID'];
                      $LN_APPLN_NO = $la['LN_APPLN_NO'];
                      $CUST_ID = $la['CUST_ID'];
                      $LN_PDT_ID = $la['LN_PDT_ID'];
                      $RQSTD_AMT = $la['RQSTD_AMT'];
                      $CUST_FIN_INST_ID = $la['CUST_FIN_INST_ID'];
                      $DISB_DATE = $la['DISB_DATE'];
                      
                      # ... 01: Get Client Name
                      $cstmr = array();
                      $cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
                      $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];

                      $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $CORE_CUST_NAME = $CORE_RESP["displayName"];

                      # ... 02: Get Loan Product Name
                      $loan_product = array();
                      $response_msg = FetchLoanProductDetailsById($LN_PDT_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $loan_product = $response_msg["CORE_RESP"];
                      //echo "<pre>".print_r($loan_product,true)."</pre>";
                      $LN_PDT_NAME = $loan_product["pdt_name"];
                      $LN_PDT_SHORT_NAME = $loan_product["pdt_short_name"];
                      $repayment_frequency_type_value = $loan_product["repayment_frequency_type_value"];

                      # ... 03: Get Client Bank
                      $CUST_BANK_NAME = GetCustBankFromBankAcct($CUST_ID, $CUST_FIN_INST_ID);

                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $LN_APPLN_NO; ?></td>
                        <td><?php echo $CORE_CUST_NAME; ?></td>
                        <td><?php echo number_format($RQSTD_AMT); ?></td>
                        <td><?php echo $LN_PDT_NAME." (".$LN_PDT_SHORT_NAME.")"; ?></td>
                        <td><?php echo $DISB_DATE; ?></td>
                        <td><?php echo $CUST_BANK_NAME; ?></td>
                        <td><?php echo $CUST_FIN_INST_ID; ?></td>
                      </tr>
                      <?php
                    }

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
