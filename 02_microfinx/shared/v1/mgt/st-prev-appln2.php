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
    LoadDefaultCSSConfigurations("Previous Applns", $APP_SMALL_LOGO); 

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
                <h2>Prevoius Savings Transfer Applns</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table class="table table-striped table-bordered" style="font-size: 11px;">
                  <tr bgcolor="#EEE"><th colspan="2">Prevoius Savings transfer applications submitted by clients between;</th></tr>
                  <tr><th width="20%">Start Date</th><td><?php echo date('d-M-Y', strtotime($START_DATE)); ?></td></tr>
                  <tr><th>End Date</th><td><?php echo date('d-M-Y', strtotime($END_DATE)); ?></td></tr>
                </table>

                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="10" bgcolor="#EEE">
                        Prevoius Savings transfer applications
                        <a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download</a>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Appln Ref</th>
                      <th>From Client Name</th>
                      <th>From Client Acct No</th>
                      <th>Amount</th>
                      <th>To Client Name</th>
                      <th>To Client Acct No</th>
                      <th>Appln Date</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $st_list = array();
                    $st_list = FetchSavingsTransferApplnsPerPeriod($START_DATE, $END_DATE);
                    

                    for ($i=0; $i < sizeof($st_list); $i++) {
                      $st = array();
                      $st = $st_list[$i];
                      $RECORD_ID = $st['RECORD_ID'];
                      $TRANSFER_REF = $st['TRANSFER_REF'];
                      $CUST_ID = $st['CUST_ID'];
                      $SVGS_ACCT_ID_TO_DEBIT = $st['SVGS_ACCT_ID_TO_DEBIT'];
                      $TRANSFER_AMT = $st['TRANSFER_AMT'];
                      $SVGS_ACCT_ID_TO_CREDIT = $st['SVGS_ACCT_ID_TO_CREDIT'];
                      $REASON = $st['REASON'];
                      $APPLN_SUBMISSION_DATE = $st['APPLN_SUBMISSION_DATE'];
                      $SVGS_HANDLER_USER_ID = $st['SVGS_HANDLER_USER_ID'];
                      $FIRST_HANDLED_ON = $st['FIRST_HANDLED_ON'];
                      $FIRST_HANDLE_RMKS = $st['FIRST_HANDLE_RMKS'];
                      $COMMITTEE_FLG = $st['COMMITTEE_FLG'];
                      $COMMITTEE_HANDLER_USER_ID = $st['COMMITTEE_HANDLER_USER_ID'];
                      $COMMITTEE_STATUS = $st['COMMITTEE_STATUS'];
                      $COMMITTEE_STATUS_DATE = $st['COMMITTEE_STATUS_DATE'];
                      $COMMITTEE_RMKS = $st['COMMITTEE_RMKS'];
                      $APPROVED_AMT = $st['APPROVED_AMT'];
                      $APPROVED_BY = $st['APPROVED_BY'];
                      $APPROVAL_DATE = $st['APPROVAL_DATE'];
                      $APPROVAL_RMKS = $st['APPROVAL_RMKS'];
                      $PROC_MODE = $st['PROC_MODE'];
                      $PROC_BATCH_NO = $st['PROC_BATCH_NO'];
                      $CORE_TXN_ID = $st['CORE_TXN_ID'];
                      $TRANSFER_APPLN_STATUS = $st['TRANSFER_APPLN_STATUS'];

                      # ... 01: Get From Client Name
                      $response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];
                      $FROM_CUST_NAME = $CORE_RESP["clientName"];

                      # ... 02: Get To Client Name
                      $response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $SVGS_ACCT_NUM_TO_CREDIT = $CORE_RESP["accountNo"];
                      $TO_CUST_NAME = $CORE_RESP["clientName"];


                      $data_transfer = $TRANSFER_REF;

                      # ... Building the excel table row
                      $excel_table_row[0] = ($i+1);
                      $excel_table_row[1] = $TRANSFER_REF;
                      $excel_table_row[2] = $FROM_CUST_NAME;
                      $excel_table_row[3] = $SVGS_ACCT_NUM_TO_DEBIT;
                      $excel_table_row[4] = number_format($TRANSFER_AMT);
                      $excel_table_row[5] = $TO_CUST_NAME;
                      $excel_table_row[6] = $SVGS_ACCT_NUM_TO_CREDIT;
                      $excel_table_row[7] = $APPLN_SUBMISSION_DATE;
                      $excel_table_row[8] = $TRANSFER_APPLN_STATUS;
                      $excel_table_list[$i] = $excel_table_row;

                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $TRANSFER_REF; ?></td>
                        <td><?php echo $FROM_CUST_NAME; ?></td>
                        <td><?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?></td>
                        <td><?php echo number_format($TRANSFER_AMT); ?></td>
                        <td><?php echo $TO_CUST_NAME; ?></td>
                        <td><?php echo $SVGS_ACCT_NUM_TO_CREDIT; ?></td>
                        <td><?php echo $APPLN_SUBMISSION_DATE; ?></td>
                        <td><?php echo $TRANSFER_APPLN_STATUS; ?></td>
                        <td>
                          <a href="st-prev-appln3?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
                        </td>
                      </tr>
                      <?php
                    }
                    # ... Excel Data Preparation
                    $_SESSION["EXCEL_HEADER"] = array("#","Appln Ref","From Client Name","From Client Acct No","Amount","To Client Name","To Client Acct No","Appln Date","Status");
                    $_SESSION["EXCEL_DATA"] = $excel_table_list;
                    $_SESSION["EXCEL_FILE"] = "PreviousSavingsTransfers_".date('dFY', strtotime(GetCurrentDateTime())).".xlsx";
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
