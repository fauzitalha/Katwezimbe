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
    LoadDefaultCSSConfigurations("Pending Applns", $APP_SMALL_LOGO); 

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
                <h2>Pending Savings Deposit Applns</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="9" bgcolor="#EEE">List of pending deposit appln</th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Appln Ref</th>
                      <th>Deposit Client Name</th>
                      <th>Deposit Client Acct No</th>
                      <th>Amount</th>
                      <th>Appln Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $RQST_STATUS = "PENDING";
                    $sd_list = array();
                    $sd_list = FetchSavingsDepositApplns($RQST_STATUS);
                    

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
                      //echo "<pre>".print_r($CORE_RESP,true)."</pre>";
                      $DEP_SVGS_ACCT_NUM_TO_CREDIT = $CORE_RESP["accountNo"];

                      $DEP_CUST_NAME = "";
                      if(isset($CORE_RESP["clientName"]))
                      {
                        $DEP_CUST_NAME = $CORE_RESP["clientName"];
                      }
                      elseif (isset($CORE_RESP["groupName"])) {
                        $DEP_CUST_NAME = $CORE_RESP["groupName"];
                      }


                      $data_transfer = $DEPOSIT_REF;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $DEPOSIT_REF; ?></td>
                        <td><?php echo $DEP_CUST_NAME; ?></td>
                        <td><?php echo $DEP_SVGS_ACCT_NUM_TO_CREDIT; ?></td>
                        <td><?php echo number_format($AMOUNT_BANKED); ?></td>
                        <td><?php echo $RQST_DATE; ?></td>
                        <td>
                          <a href="sd-queue-ind?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
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
