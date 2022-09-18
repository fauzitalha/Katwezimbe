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
    LoadDefaultCSSConfigurations("Previous Withdraw Applns", $APP_SMALL_LOGO); 

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
                <h2>Previous Withdraw Applns</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table class="table table-striped table-bordered" style="font-size: 11px;">
                  <tr bgcolor="#EEE"><th colspan="2">Previous Savings withdraw applications submitted by clients between;</th></tr>
                  <tr><th width="20%">Start Date</th><td><?php echo date('d-M-Y', strtotime($START_DATE)); ?></td></tr>
                  <tr><th>End Date</th><td><?php echo date('d-M-Y', strtotime($END_DATE)); ?></td></tr>
                </table>

                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="10" bgcolor="#EEE">
                        Savings withdraw applications submitted by clients between;
                        <a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download</a>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Appln Ref</th>
                      <th>Client Name</th>
                      <th>Amount</th>
                      <th>Intrnl Acct No</th>
                      <th>Approval Date</th>
                      <th>Extrnl Acct No</th>
                      <th>Extrnl Orgn</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $excel_table_list = array();
                    $sw_list = array();
                    $sw_list = FetchSavingsWithdrawApplnsBtnPeriodsAppDate($START_DATE, $END_DATE);
                    

                    for ($i=0; $i < sizeof($sw_list); $i++) {
                      $sw = array();
                      $sw = $sw_list[$i];
                      $RECORD_ID = $sw['RECORD_ID'];
                      $WITHDRAW_REF = $sw['WITHDRAW_REF'];
                      $CUST_ID = $sw['CUST_ID'];
                      $SVGS_ACCT_ID_TO_DEBIT = $sw['SVGS_ACCT_ID_TO_DEBIT'];
                      $RQSTD_AMT = $sw['RQSTD_AMT'];
                      $REASON = $sw['REASON'];
                      $APPLN_SUBMISSION_DATE = $sw['APPLN_SUBMISSION_DATE'];
                      $SVGS_HANDLER_USER_ID = $sw['SVGS_HANDLER_USER_ID'];
                      $FIRST_HANDLED_ON = $sw['FIRST_HANDLED_ON'];
                      $FIRST_HANDLE_RMKS = $sw['FIRST_HANDLE_RMKS'];
                      $COMMITTEE_FLG = $sw['COMMITTEE_FLG'];
                      $COMMITTEE_HANDLER_USER_ID = $sw['COMMITTEE_HANDLER_USER_ID'];
                      $COMMITTEE_STATUS = $sw['COMMITTEE_STATUS'];
                      $COMMITTEE_STATUS_DATE = $sw['COMMITTEE_STATUS_DATE'];
                      $COMMITTEE_RMKS = $sw['COMMITTEE_RMKS'];
                      $APPROVED_AMT = $sw['APPROVED_AMT'];
                      $APPROVED_BY = $sw['APPROVED_BY'];
                      $APPROVAL_DATE = $sw['APPROVAL_DATE'];
                      $APPROVAL_RMKS = $sw['APPROVAL_RMKS'];
                      $CUST_FIN_INST_ID = $sw['CUST_FIN_INST_ID'];
                      $PROC_MODE = $sw['PROC_MODE'];
                      $PROC_BATCH_NO = $sw['PROC_BATCH_NO'];
                      $CORE_TXN_ID = $sw['CORE_TXN_ID'];
                      $SVGS_APPLN_STATUS = $sw['SVGS_APPLN_STATUS'];

                      # ... 01: Get Client Name
                      $cstmr = array();
                      $cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
                      $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];

                      $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $CORE_CUST_NAME = $CORE_RESP["displayName"];

                      # ... 02: Get Client Acct
                      $response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];

                      # ... 03: Get Client Bank
                      $CUST_BANK_NAME = GetCustBankFromBankAcct($CUST_ID, $CUST_FIN_INST_ID);

                      # ... Building the excel table row
                      $excel_table_row[0] = ($i+1);
                      $excel_table_row[1] = $WITHDRAW_REF;
                      $excel_table_row[2] = $CORE_CUST_NAME;
                      $excel_table_row[3] = number_format($RQSTD_AMT);
                      $excel_table_row[4] = $SVGS_ACCT_NUM_TO_DEBIT;
                      $excel_table_row[5] = $APPROVAL_DATE;
                      $excel_table_row[6] = $CUST_FIN_INST_ID;
                      $excel_table_row[7] = $CUST_BANK_NAME;
                      $excel_table_row[8] = $SVGS_APPLN_STATUS;
                      $excel_table_list[$i] = $excel_table_row;

                      $data_transfer = $WITHDRAW_REF;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $WITHDRAW_REF; ?></td>
                        <td><?php echo $CORE_CUST_NAME; ?></td>
                        <td><?php echo number_format($RQSTD_AMT); ?></td>
                        <td><?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?></td>
                        <td><?php echo $APPROVAL_DATE; ?></td>
                        <td><?php echo $CUST_FIN_INST_ID; ?></td>
                        <td><?php echo $CUST_BANK_NAME; ?></td>
                        <td><?php echo $SVGS_APPLN_STATUS; ?></td>
                        <td>
                          <a href="sw-prev-list3?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
                        </td>
                      </tr>
                      <?php
                    }
                    # ... Excel Data Preparation
                    $_SESSION["EXCEL_HEADER"] = array("#","Appln Ref","Client Name","Amount","Intrnl Acct No","Approval Date","Extrnl Acct No","Extrnl Orgn","Status");
                    $_SESSION["EXCEL_DATA"] = $excel_table_list;
                    $_SESSION["EXCEL_FILE"] = "PreviousSavingsWithdrawalList_".date('dFY', strtotime(GetCurrentDateTime())).".xlsx";
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
