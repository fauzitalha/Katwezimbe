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
    LoadDefaultCSSConfigurations("Approve Shares Applns", $APP_SMALL_LOGO); 

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
                <h2>Approve Shares Requests</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="9" bgcolor="#EEE">Approve Buy Shares Applns</th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Appln Ref</th>
                      <th>Client Name</th>
                      <th>Share Acct No</th>
                      <th>Debit Savings Acct No</th>
                      <th>Shares Requested</th>
                      <th>Appln Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $SHARES_APPLN_STATUS = "VERIFIED";
                    $shr_list = array();
                    $shr_list = FetchShareRequestApplns($SHARES_APPLN_STATUS);
                    

                    for ($i=0; $i < sizeof($shr_list); $i++) {
                      $shr = array();
                      $shr = $shr_list[$i];
                      $RECORD_ID = $shr['RECORD_ID'];
                      $SHARES_APPLN_REF = $shr['SHARES_APPLN_REF'];
                      $CUST_ID = $shr['CUST_ID'];
                      $SVGS_ACCT_ID_TO_DEBIT = $shr['SVGS_ACCT_ID_TO_DEBIT'];
                      $SHARES_REQUESTED = $shr['SHARES_REQUESTED'];
                      $SHARES_ACCT_ID_TO_CREDIT = $shr['SHARES_ACCT_ID_TO_CREDIT'];
                      $APPLN_SUBMISSION_DATE = $shr['APPLN_SUBMISSION_DATE'];
                      $SHARES_HANDLER_USER_ID = $shr['SHARES_HANDLER_USER_ID'];
                      $FIRST_HANDLED_ON = $shr['FIRST_HANDLED_ON'];
                      $FIRST_HANDLE_RMKS = $shr['FIRST_HANDLE_RMKS'];
                      $APPROVED_AMT = $shr['APPROVED_AMT'];
                      $APPROVED_BY = $shr['APPROVED_BY'];
                      $APPROVAL_DATE = $shr['APPROVAL_DATE'];
                      $APPROVAL_RMKS = $shr['APPROVAL_RMKS'];
                      $CORE_TXN_ID = $shr['CORE_TXN_ID'];
                      $SHARES_APPLN_STATUS = $shr['SHARES_APPLN_STATUS'];


                      # ... 01: Get Shares Client Name
                      $response_msg =  FetchSharesAccountDetailsById($SHARES_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $SHARES_ACCT_NUM = $CORE_RESP["accountNo"];
                      $SHARES_CUST_NAME = $CORE_RESP["clientName"];

                      # ... 02: Get Savings Client Name
                      $response_msg = FetchSavingsAccountDetailsById($SVGS_ACCT_ID_TO_DEBIT, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $SVGS_ACCT_NUM_TO_DEBIT = $CORE_RESP["accountNo"];


                      $data_transfer = $SHARES_APPLN_REF;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $SHARES_APPLN_REF; ?></td>
                        <td><?php echo $SHARES_CUST_NAME; ?></td>
                        <td><?php echo $SHARES_ACCT_NUM; ?></td>
                        <td><?php echo $SVGS_ACCT_NUM_TO_DEBIT; ?></td>
                        <td><?php echo number_format($SHARES_REQUESTED); ?></td>
                        <td><?php echo $APPLN_SUBMISSION_DATE; ?></td>
                        <td>
                          <a href="shr-apprv-ind?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
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
