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
                <h2>Prevoius Savings Deposit Applns</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                
                <table class="table table-striped table-bordered" style="font-size: 11px;">
                  <tr bgcolor="#EEE"><th colspan="2">Prevoius Savings deposit applications submitted by clients between;</th></tr>
                  <tr><th width="20%">Start Date</th><td><?php echo date('d-M-Y', strtotime($START_DATE)); ?></td></tr>
                  <tr><th>End Date</th><td><?php echo date('d-M-Y', strtotime($END_DATE)); ?></td></tr>
                </table>

                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="9" bgcolor="#EEE">
                        Prevoius Savings deposit applications
                        <a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download</a>
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Appln Ref</th>
                      <th>Deposit Client Name</th>
                      <th>Deposit Client Acct No</th>
                      <th>Amount</th>
                      <th>Appln Date</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $excel_table_list = array();
                    $sd_list = array();
                    $sd_list = FetchSavingsDepositApplnsPerPeriod($START_DATE, $END_DATE);
                    
                    for ($i=0; $i < sizeof($sd_list); $i++) {
                      $sd = array();
                      $sd = $sd_list[$i];
                      $RECORD_ID = $sd['RECORD_ID'];
                      $DEPOSIT_REF = $sd['DEPOSIT_REF'];
                      $CUST_ID = $sd['CUST_ID'];
                      $SVGS_ACCT_ID_TO_CREDIT = $sd['SVGS_ACCT_ID_TO_CREDIT'];
                      $AMOUNT_BANKED = $sd['AMOUNT_BANKED'];
                      $REASON = $sd['REASON'];
                      $BANK_ID = $sd['BANK_ID'];
                      $BANK_INST_ACCT_NO = $sd['BANK_INST_ACCT_NO'];
                      $BANK_INST_ACCT_NAME = $sd['BANK_INST_ACCT_NAME'];
                      $BANK_RECEIPT_REF = $sd['BANK_RECEIPT_REF'];
                      $BANK_RECEIPT_ATTCHMT = $sd['BANK_RECEIPT_ATTCHMT'];
                      $RQST_DATE = $sd['RQST_DATE'];
                      $HANDLED_BY = $sd['HANDLED_BY'];
                      $HANDLED_ON = $sd['HANDLED_ON'];
                      $HANDLER_RMKS = $sd['HANDLER_RMKS'];
                      $CORE_TXN_ID = $sd['CORE_TXN_ID'];
                      $RQST_STATUS = $sd['RQST_STATUS'];

                      # ... 01: Get Deposit Client Name
                      $response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $DEP_SVGS_ACCT_NUM_TO_CREDIT = $CORE_RESP["accountNo"];
                      $DEP_CUST_NAME = $CORE_RESP["clientName"];


                      $data_transfer = $DEPOSIT_REF;

                      # ... Building the excel table row
                      $excel_table_row[0] = ($i+1);
                      $excel_table_row[1] = $DEPOSIT_REF;
                      $excel_table_row[2] = $DEP_CUST_NAME;
                      $excel_table_row[3] = $DEP_SVGS_ACCT_NUM_TO_CREDIT;
                      $excel_table_row[4] = number_format($AMOUNT_BANKED);
                      $excel_table_row[5] = $RQST_DATE;
                      $excel_table_row[6] = $RQST_STATUS;
                      $excel_table_list[$i] = $excel_table_row;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $DEPOSIT_REF; ?></td>
                        <td><?php echo $DEP_CUST_NAME; ?></td>
                        <td><?php echo $DEP_SVGS_ACCT_NUM_TO_CREDIT; ?></td>
                        <td><?php echo number_format($AMOUNT_BANKED); ?></td>
                        <td><?php echo $RQST_DATE; ?></td>
                        <td><?php echo $RQST_STATUS; ?></td>
                        <td>
                          <a href="sd-prev-appln3?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
                        </td>
                      </tr>
                      <?php
                    }

                    # ... Excel Data Preparation
                    $_SESSION["EXCEL_HEADER"] = array("#","Appln Ref","Deposit Client Name","Deposit Client Acct No","Amount","Appln Date","Status");
                    $_SESSION["EXCEL_DATA"] = $excel_table_list;
                    $_SESSION["EXCEL_FILE"] = "PreviousSavingsDepositList_".date('dFY', strtotime(GetCurrentDateTime())).".xlsx";
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
