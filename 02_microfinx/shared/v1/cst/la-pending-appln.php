<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Pending Loan Applns", $APP_SMALL_LOGO); 

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
              <?php SideNavBar($CUST_ID); ?>
            </div>
            <!-- /sidebar menu -->


          </div>
        </div>

        <!-- top navigation -->
        <?php TopNavBar($firstname); ?>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <h2>Pending Loan Applns</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">List of Incomplete Loan Applications</th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Loan Appln No</th>
                      <th>Amount Requested</th>
                      <th>Loan Product</th>
                      <th>Appln Start Date</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $CUST_ID = $_SESSION['CST_USR_ID'];
                    $loan_appln_list = array();
                    $loan_appln_list = FetchPendingLoanApplns($CUST_ID);
                    for ($i=0; $i < sizeof($loan_appln_list); $i++) { 
                      $loan_appln = array();
                      $loan_appln = $loan_appln_list[$i];

                      $RECORD_ID = $loan_appln['RECORD_ID'];
                      $LN_APPLN_NO = $loan_appln['LN_APPLN_NO'];
                      $LN_PDT_ID = $loan_appln['LN_PDT_ID'];
                      $LN_APPLN_CREATION_DATE = $loan_appln['LN_APPLN_CREATION_DATE'];
                      $LN_APPLN_STATUS = $loan_appln['LN_APPLN_STATUS'];
                      $RQSTD_AMT = $loan_appln['RQSTD_AMT'];

                      # ... Getting Loan Product Details
                      $pdt_name = "";
                      $pdt_short_name = "";
                      $loan_product = array();
                      $response_msg = FetchLoanProductDetailsById($LN_PDT_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $loan_product = $response_msg["CORE_RESP"];
                      $pdt_name = $loan_product["pdt_name"];
                      $pdt_short_name = $loan_product["pdt_short_name"];

                      $data_transfer = $LN_APPLN_NO;

                      


                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $LN_APPLN_NO; ?></td>
                        <td><?php echo number_format($RQSTD_AMT); ?></td>
                        <td><?php echo $pdt_name." ($pdt_short_name)"; ?></td>
                        <td><?php echo $LN_APPLN_CREATION_DATE; ?></td>
                        <td><?php echo $LN_APPLN_STATUS; ?></td>
                        <td>
                          <a href="la-pending-appln-ind?k=<?php echo $LN_APPLN_NO; ?>" class="btn btn-primary btn-xs">Resume</a>
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
