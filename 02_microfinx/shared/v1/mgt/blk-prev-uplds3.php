<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$FILE_ID = mysql_real_escape_string($_GET['k']);
$file = array();
$file = FetchBulkFileById($FILE_ID);
$RECORD_ID = $file['RECORD_ID'];
$FILE_NAME = $file['FILE_NAME'];
$UPLOAD_REASON = $file['UPLOAD_REASON'];
$UPLOADED_BY = $file['UPLOADED_BY'];
$UPLOADED_ON = $file['UPLOADED_ON'];
$VERIFIED_RMKS = $file['VERIFIED_RMKS'];
$VERIFIED_BY = $file['VERIFIED_BY'];
$VERIFIED_ON = $file['VERIFIED_ON'];
$APPROVED_RMKS = $file['APPROVED_RMKS'];
$APPROVED_BY = $file['APPROVED_BY'];
$APPROVED_ON = $file['APPROVED_ON'];
$REVERSAL_FLG = $file['REVERSAL_FLG'];
$REV_INIT_RMKS = $file['REV_INIT_RMKS'];
$REV_INIT_BY = $file['REV_INIT_BY'];
$REV_INIT_ON = $file['REV_INIT_ON'];
$REV_APPROVED_RMKS = $file['REV_APPROVED_RMKS'];
$REV_APPROVED_BY = $file['REV_APPROVED_BY'];
$REV_APPROVED_ON = $file['REV_APPROVED_ON'];
$FILE_STATUS = $file['FILE_STATUS'];

# ... ... ... 01: Entry Counts ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID'";
$CNT_TOTAL_FILE = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE);
$Q_SUM_TOTAL_FILE = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID'";
$SUM_TOTAL_FILE = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE);

# ... ... ... 02: Entry Debit Counts ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_DEBITS = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D'";
$CNT_TOTAL_DEBITS = ReturnOneEntryFromDB($Q_CNT_TOTAL_DEBITS);
$Q_SUM_TOTAL_DEBITS = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D'";
$SUM_TOTAL_DEBITS = ReturnOneEntryFromDB($Q_SUM_TOTAL_DEBITS);

# ... ... ... 03: Entry Credit Counts ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_CREDITS = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C'";
$CNT_TOTAL_CREDITS = ReturnOneEntryFromDB($Q_CNT_TOTAL_CREDITS);
$Q_SUM_TOTAL_CREDITS = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C'";
$SUM_TOTAL_CREDITS = ReturnOneEntryFromDB($Q_SUM_TOTAL_CREDITS);

# ... ... ... 04: Entry Counts All Pass ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_PASS = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='PASS'";
$CNT_TOTAL_FILE_PASS = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_PASS);
$Q_SUM_TOTAL_FILE_PASS = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='PASS'";
$SUM_TOTAL_FILE_PASS = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_PASS);

# ... ... ... 05: Entry Counts All (Fail) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_FAIL = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='FAIL'";
$CNT_TOTAL_FILE_FAIL= ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_FAIL);
$Q_SUM_TOTAL_FILE_FAIL = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='FAIL'";
$SUM_TOTAL_FILE_FAIL = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_FAIL);

# ... ... ... 06: Entry Debits Counts (Pass) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_PASS_DR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='PASS'";
$CNT_TOTAL_FILE_PASS_DR = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_PASS_DR);
$Q_SUM_TOTAL_FILE_PASS_DR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='PASS'";
$SUM_TOTAL_FILE_PASS_DR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_PASS_DR);

# ... ... ... 07: Entry Debits Counts (Fail) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_FAIL_DR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='FAIL'";
$CNT_TOTAL_FILE_FAIL_DR= ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_FAIL_DR);
$Q_SUM_TOTAL_FILE_FAIL_DR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='FAIL'";
$SUM_TOTAL_FILE_FAIL_DR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_FAIL_DR);


# ... ... ... 08: Entry Credit Counts (Pass) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_PASS_CR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='PASS'";
$CNT_TOTAL_FILE_PASS_CR = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_PASS_CR);
$Q_SUM_TOTAL_FILE_PASS_CR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='PASS'";
$SUM_TOTAL_FILE_PASS_CR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_PASS_CR);

# ... ... ... 09: Entry Credit Counts (Fail) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_FAIL_CR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='FAIL'";
$CNT_TOTAL_FILE_FAIL_CR= ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_FAIL_CR);
$Q_SUM_TOTAL_FILE_FAIL_CR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='FAIL'";
$SUM_TOTAL_FILE_FAIL_CR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_FAIL_CR);


# ... ... ... 10: Get Uploader Details ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$UPLOADED_BY_NAME = "";
$UPLOADED_BY_COREID = GetUserCoreIdFromWebApp($UPLOADED_BY);
$response_msg = FetchUserDetailsFromCore($UPLOADED_BY_COREID, $MIFOS_CONN_DETAILS);
$CORE_RESP = $response_msg["CORE_RESP"];
if (isset($CORE_RESP["username"])) {
  $UPLOADED_BY_NAME = $CORE_RESP["username"]." (".$CORE_RESP["firstname"]." ".$CORE_RESP["lastname"].")";
}

# ... ... ... 10: Get Uploader Details ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$VFD_BY_NAME = "";
$UPLOADED_BY_COREID = GetUserCoreIdFromWebApp($APPROVED_BY);
$response_msg = FetchUserDetailsFromCore($UPLOADED_BY_COREID, $MIFOS_CONN_DETAILS);
$CORE_RESP = $response_msg["CORE_RESP"];
if (isset($CORE_RESP["username"])) {
  $VFD_BY_NAME = $CORE_RESP["username"]." (".$CORE_RESP["firstname"]." ".$CORE_RESP["lastname"].")";
}

# ... ... ... 11: Porcessing Math ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$CNT_DIFF = ($CNT_TOTAL_FILE - $CNT_TOTAL_FILE_PASS);
$VOL_DIFF = ($SUM_TOTAL_FILE - $SUM_TOTAL_FILE_PASS);


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Previous Files", $APP_SMALL_LOGO); 

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
                <h2>Prevoius Bulk File</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table class="table table-striped table-bordered" style="font-size: 12px;">
                  <thead>
                    <tr valign="top" bgcolor="#EEE">
                      <th>File Id</th>
                      <th>File Name</th>
                      <th>Description</th>
                      <th>Upload Date</th>
                      <th>Uploaded By</th>
                      <th>Verified On</th>
                      <th>Verified By</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                      <td><?php echo $FILE_ID; ?></td>
                      <td><?php echo $FILE_NAME; ?></td>
                      <td><?php echo $UPLOAD_REASON; ?></td>
                      <td><?php echo $UPLOADED_ON; ?></td>
                      <td><?php echo $UPLOADED_BY_NAME; ?></td>
                      <td><?php echo $APPROVED_ON; ?></td>
                      <td><?php echo $VFD_BY_NAME; ?></td>
                    </tr>
                  </tbody>
                </table>

                <table class="table table-striped table-bordered" style="font-size: 12px;">
                  <thead>
                    <tr valign="top" bgcolor="#EEE">
                      <th width="15%">CATEGORY</th>
                      <th width="10%">COUNT</th>
                      <th width="20%">VOLUME (UGX)</th>
                      <th>PASSED ENTRIES</th>
                      <th>FAILED ENTRIES</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                        <th>Debit Entries</th>
                        <td><?php echo number_format($CNT_TOTAL_DEBITS); ?></td>
                        <td><?php echo number_format($SUM_TOTAL_DEBITS); ?></td>
                        <td><?php echo ($CNT_TOTAL_FILE_PASS_DR)." (".($SUM_TOTAL_FILE_PASS_DR).")"; ?></td>
                        <td><?php echo ($CNT_TOTAL_FILE_FAIL_DR)." (".($SUM_TOTAL_FILE_FAIL_DR).")"; ?></td>
                    </tr>
                    <tr valign="top">
                        <th>Credit Entries</th>
                        <td><?php echo number_format($CNT_TOTAL_CREDITS); ?></td>
                        <td><?php echo number_format($SUM_TOTAL_CREDITS); ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_PASS_CR." (".$SUM_TOTAL_FILE_PASS_CR.")"; ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_FAIL_CR." (".$SUM_TOTAL_FILE_FAIL_CR.")"; ?></td>
                    </tr>
                    <tr valign="top">
                        <th>Totals</th>
                        <td><?php echo number_format($CNT_TOTAL_FILE); ?></td>
                        <td><?php echo number_format($SUM_TOTAL_FILE); ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_PASS." (".$SUM_TOTAL_FILE_PASS.")"; ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_FAIL." (".$SUM_TOTAL_FILE_FAIL.")"; ?></td>
                    </tr>
                  </tbody>
                </table>

                <div style="overflow-y: auto; height: 490px;">
                  <table id="datatable3" class="table table-striped table-bordered" style="font-size: 11px;">
                    <thead>
                      <tr valign="top">
                        <th colspan="9" bgcolor="#EEE">
                          
                          <table width="100%">
                            <tr>
                              <td><span>List of File Transaction Entries</span></td>
                              <td width="10%"><a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download</a></td>
                            </tr>
                          </table>                                          
                        </th>

                      </tr>
                      <tr valign="top">
                        <th>#</th>
                        <th>Acct No</th>
                        <th>Acct Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Nrrtn</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $excel_table_list = array();
                      $txn_list_debits = array();
                      $txn_list_debits = FetchBulkTxnListDebits($FILE_ID);
                      $txn_list_credits = array();
                      $txn_list_credits = FetchBulkTxnListCredits($FILE_ID);
                      $txn_list = array();
                      $txn_list = array_merge($txn_list_debits, $txn_list_credits);

                      for ($i=0; $i < sizeof($txn_list); $i++) { 
                        //$excel_table_row = array();
                        $txn = array();
                        $txn = $txn_list[$i];
                        $RECORD_ID = $txn['RECORD_ID'];
                        $TRAN_ID = $txn['TRAN_ID'];
                        $FILE_ID = $txn['FILE_ID'];
                        $SAVINGS_CUST_ID = $txn['SAVINGS_CUST_ID'];
                        $SAVINGS_ACCT_ID = $txn['SAVINGS_ACCT_ID'];
                        $SAVINGS_ACCT_NUM = $txn['SAVINGS_ACCT_NUM'];
                        $SAVINGS_ACCT_NAME = $txn['SAVINGS_ACCT_NAME'];
                        $CURRENCY = $txn['CURRENCY'];
                        $TRAN_TYPE = $txn['TRAN_TYPE'];
                        $TRAN_AMT = $txn['TRAN_AMT'];
                        $TRAN_NARRATION = $txn['TRAN_NARRATION'];
                        $PASS_FAIL_FLG = $txn['PASS_FAIL_FLG'];
                        $EXEC_FLG = $txn['EXEC_FLG'];
                        $EXEC_MSG = $txn['EXEC_MSG'];
                        $PASS_FAIL_RMKS = $txn['PASS_FAIL_RMKS'];
                        $TRAN_STATUS = $txn['TRAN_STATUS'];
                        $CORE_REF_ID = $txn['CORE_REF_ID'];

                        # ... Building the excel table row
                        $excel_table_row[0] = ($i+1);
                        $excel_table_row[1] = $SAVINGS_ACCT_NUM;
                        $excel_table_row[2] = $SAVINGS_ACCT_NAME;
                        $excel_table_row[3] = $TRAN_TYPE;
                        $excel_table_row[4] = $TRAN_AMT;
                        $excel_table_row[5] = $TRAN_NARRATION;
                        $excel_table_row[6] = $PASS_FAIL_FLG;
                        $excel_table_row[7] = $PASS_FAIL_RMKS;
                        $excel_table_row[8] = $EXEC_FLG;
                        $excel_table_row[9] = $EXEC_MSG;
                        $excel_table_row[10] = $CORE_REF_ID;

                        $excel_table_list[$i] = $excel_table_row;
                        ?>
                         <tr valign="top">
                          <td><?php echo ($i+1); ?>. </td>
                          <td><?php echo $SAVINGS_ACCT_NUM; ?></td>
                          <td><?php echo $SAVINGS_ACCT_NAME; ?></td>
                          <td><?php echo $TRAN_TYPE; ?></td>
                          <td><?php echo number_format($TRAN_AMT); ?></td>
                          <td><?php echo $TRAN_NARRATION; ?></td>
                          <td><?php echo $TRAN_STATUS; ?></td>
                        </tr>
                        <?php
                      } # .. END..LOOP

                      # ... Excel Data Preparation
                      $_SESSION["EXCEL_HEADER"] = array("#","Acct No","Acct Name","Type","Amount","Nrrtn","Pass/Fail","Pass/Fail Rmks"
                                                       ,"Exec Status", "Exec message", "Tran Staus");
                      $_SESSION["EXCEL_DATA"] = $excel_table_list;
                      $_SESSION["EXCEL_FILE"] = $FILE_ID."_".date('dFY', strtotime(GetCurrentDateTime())).".xlsx";
                      ?>
                    </tbody>
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
