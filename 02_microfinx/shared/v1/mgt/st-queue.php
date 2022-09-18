<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Pending Internal Transfers", $APP_SMALL_LOGO); 

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
                <h2>Pending Internal Transfers</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="9" bgcolor="#EEE">List of pending Internal Transfers</th>
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
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $TRANSFER_APPLN_STATUS = "PENDING";
                    $st_list = array();
                    $st_list = FetchSavingsTransferApplns($TRANSFER_APPLN_STATUS);
                    

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
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $TRANSFER_REF; ?></td>
                        <td><?php echo $FROM_CUST_NAME; ?></td>
                        <td><?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?></td>
                        <td><?php echo number_format($TRANSFER_AMT); ?></td>
                        <td><?php echo $TO_CUST_NAME; ?></td>
                        <td><?php echo $TO_CUST_NAME; ?></td>
                        <td><?php echo $APPLN_SUBMISSION_DATE; ?></td>
                        <td>
                          <a href="st-queue-ind?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
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
