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
    LoadDefaultCSSConfigurations("Review Loans Applns", $APP_SMALL_LOGO); 

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
                <h2>Review Loans Applications</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="9" bgcolor="#EEE">Loan Applications due for approval & recommendation</th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Appln Ref</th>
                      <th>Client Name</th>
                      <th>Amount</th>
                      <th>Rpymt Period</th>
                      <th>Product</th>
                      <th>Appln Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $LN_APPLN_STATUS = "READY_4_REVIEW";
                    $la_list = array();
                    $la_list = FetchLoanApplns($LN_APPLN_STATUS);
                    for ($i=0; $i < sizeof($la_list); $i++) {
                      $la = array();
                      $la = $la_list[$i];
                      $RECORD_ID = $la['RECORD_ID'];
                      $LN_APPLN_NO = $la['LN_APPLN_NO'];
                      $IS_WALK_IN = $la['IS_WALK_IN'];
                      $IS_TOP_UP = $la['IS_TOP_UP'];
                      $CUST_ID = $la['CUST_ID'];
                      $LN_PDT_ID = $la['LN_PDT_ID'];
                      $RQSTD_AMT = $la['RQSTD_AMT'];
                      $RQSTD_RPYMT_PRD = $la['RQSTD_RPYMT_PRD'];
                      $LN_APPLN_SUBMISSION_DATE = $la['LN_APPLN_SUBMISSION_DATE'];
                      
                      # ... Loan Type .....................................................................#
                      $CUST_CORE_ID = "";
                      if ($IS_WALK_IN=="YES") {
                        $data_details = explode('-', $CUST_ID);
                        $CUST_CORE_ID = $data_details[1];
                      }

                      if ($IS_WALK_IN=="NO") {
                        # ... 01: Get Client Name
                        $cstmr = array();
                        $cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
                        $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
                      }

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
                      $LN_PDT_NAME = $loan_product["pdt_name"];
                      $LN_PDT_SHORT_NAME = $loan_product["pdt_short_name"];
                      $repayment_frequency_type_value = $loan_product["repayment_frequency_type_value"];
        
                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;

                      $data_transfer = $LN_APPLN_NO;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $LN_APPLN_NO; ?></td>
                        <td><?php echo $CORE_CUST_NAME; ?></td>
                        <td><?php echo number_format($RQSTD_AMT); ?></td>
                        <td><?php echo $RQSTD_RPYMT_PRD." (".$repayment_frequency_type_value.")"; ?></td>
                        <td><?php echo $LN_PDT_NAME." (".$LN_PDT_SHORT_NAME.")"; ?></td>
                        <td><?php echo $LN_APPLN_SUBMISSION_DATE; ?></td>
                        <td>
                          <a href="la-review-ind?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
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
