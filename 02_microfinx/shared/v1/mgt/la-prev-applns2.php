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


            <div class="x_panel">
              <div class="x_title">
                <a href="la-prev-applns" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Previous Loan Applications</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">  

                <table class="table table-striped table-bordered" style="font-size: 11px;">
                  <tr><th width="20%">START_DATE</th><td><?php echo date('d-M-Y', strtotime($START_DATE)); ?></td></tr>
                  <tr><th>END_DATE</th><td><?php echo date('d-M-Y', strtotime($END_DATE)); ?></td></tr>
                </table>

                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="9" bgcolor="#EEE">
                      Loan Applications Report
                      <a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download</a>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Appln Ref</th>
                      <th>Client Name</th>
                      <th>Amount</th>
                      <th>Rpymt Period</th>
                      <th>Product</th>
                      <th>Appln Date</th>
                      <th>Appln Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $la_list = array();
                    $la_list = FetchLoanApplnsBetweenPeriods($START_DATE, $END_DATE);
                    $excel_table_list = array();

                    
                    for ($i=0; $i < sizeof($la_list); $i++) {
                      $la = array();
                      $la = $la_list[$i];
                      $RECORD_ID = $la['RECORD_ID'];
                      $LN_APPLN_NO = $la['LN_APPLN_NO'];
                      $CUST_ID = $la['CUST_ID'];
                      $LN_PDT_ID = $la['LN_PDT_ID'];
                      $RQSTD_AMT = $la['RQSTD_AMT'];
                      $RQSTD_RPYMT_PRD = $la['RQSTD_RPYMT_PRD'];
                      $LN_APPLN_SUBMISSION_DATE = $la['LN_APPLN_SUBMISSION_DATE'];
                      $LN_APPLN_STATUS = $la['LN_APPLN_STATUS'];
                      
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

                      $data_transfer = $LN_APPLN_NO;

                      # ... Building the excel table row
                      $excel_table_row[0] = ($i+1);
                      $excel_table_row[1] = $LN_APPLN_NO;
                      $excel_table_row[2] = $CORE_CUST_NAME;
                      $excel_table_row[3] = number_format($RQSTD_AMT);
                      $excel_table_row[4] = $RQSTD_RPYMT_PRD." (".$repayment_frequency_type_value.")";
                      $excel_table_row[5] = $LN_PDT_NAME;
                      $excel_table_row[6] = $LN_APPLN_SUBMISSION_DATE;
                      $excel_table_row[7] = $LN_APPLN_STATUS;

                      $excel_table_list[$i] = $excel_table_row;


                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $LN_APPLN_NO; ?></td>
                        <td><?php echo $CORE_CUST_NAME; ?></td>
                        <td><?php echo number_format($RQSTD_AMT); ?></td>
                        <td><?php echo $RQSTD_RPYMT_PRD." (".$repayment_frequency_type_value.")"; ?></td>
                        <td><?php echo $LN_PDT_NAME." (".$LN_PDT_SHORT_NAME.")"; ?></td>
                        <td><?php echo $LN_APPLN_SUBMISSION_DATE; ?></td>
                        <td><?php echo $LN_APPLN_STATUS; ?></td>
                        <td>
                          <a href="la-prev-applns3?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
                        </td>
                      </tr>
                      <?php

                    }

                    # ... Excel Data Preparation
                    $_SESSION["EXCEL_HEADER"] = array("#","Appln Ref","Client Name","Amount","Rpymt Period","Product","Appln Date","Appln Status");
                    $_SESSION["EXCEL_DATA"] = $excel_table_list;
                    $_SESSION["EXCEL_FILE"] = "LoanApplications_".date('dFY', strtotime(GetCurrentDateTime())).".xlsx";
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
