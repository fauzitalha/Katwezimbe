<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Apprv Grrtship
if (isset($_POST['btn_apprv_grrtrship'])) {

  $RG_RECORD_ID = trim(mysql_real_escape_string($_POST['RG_RECORD_ID']));
  $LN_APPLN_NO = trim(mysql_real_escape_string($_POST['LN_APPLN_NO']));
  $GUARANTORSHIP_STATUS = trim(mysql_real_escape_string($_POST['GRRT_APPVL']));
  $RMKS = trim(mysql_real_escape_string($_POST['RMKS']));

  # ... SQL
  $q2 = "UPDATE loan_appln_guarantors SET GUARANTORSHIP_STATUS='$GUARANTORSHIP_STATUS', RMKS='$RMKS' WHERE RECORD_ID='$RG_RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN_GUARANTOR";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = $GUARANTORSHIP_STATUS;
    $EVENT_OPERATION = $GUARANTORSHIP_STATUS."_LOAN_APPLN_GUARANTORSHIP";
    $EVENT_RELATION = "loan_appln_guarantors";
    $EVENT_RELATION_NO = $RG_RECORD_ID;
    $OTHER_DETAILS = $RMKS;
    $INVOKER_ID = $_SESSION['CST_USR_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "INFO";
    $alert_msg = "SUCCESS: Guarantorship request has been actioned. Refreshing in 5 seconds";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }


}


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Guarrant Loan", $APP_SMALL_LOGO); 

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
                <h2>Guarrant Loan</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         

                <table class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th>#</th>
                      <th>Appln No</th>
                      <th>Applicant</th>
                      <th>Request Date</th>
                      <th>Loan Amount</th>
                      <th>Loan Product</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $g_list = array();
                    $g_list = FetchGrrtrshipRequests($_SESSION['CST_USR_ID']);
                    for ($i=0; $i < sizeof($g_list); $i++) { 
                      $g = array();
                      $g = $g_list[$i];
                      $RG_RECORD_ID = $g['RECORD_ID'];
                      $LN_APPLN_NO = $g['LN_APPLN_NO'];
                      $DATE_GENERATED = $g['DATE_GENERATED'];
                      
                      # ... Loan Appln Details
                      $R_CUST_ID = "";
                      $LN_PDT_ID = "";
                      $RQSTD_AMT = "";
                      $RQSTD_RPYMT_PRD = "";
                      $PURPOSE = "";

                      $loan_appln = array();
                      $loan_appln = FetchLoanApplnDetailsById($LN_APPLN_NO);
                      if (isset($loan_appln['RECORD_ID'])) {
                        $R_CUST_ID = $loan_appln['CUST_ID'];
                        $LN_PDT_ID = $loan_appln['LN_PDT_ID'];
                        $RQSTD_AMT = $loan_appln['RQSTD_AMT'];
                        $RQSTD_RPYMT_PRD = $loan_appln['RQSTD_RPYMT_PRD'];
                        $PURPOSE = $loan_appln['PURPOSE'];
                      }

                      # ... GET LOAN PRODUCT DETAILS
                      $loan_product = array();
                      $response_msg = FetchLoanProductDetailsById($LN_PDT_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $loan_product = $response_msg["CORE_RESP"];
                      $LN_PDT_NAME = $loan_product["pdt_name"];
                      $LN_PDT_SHORT_NAME = $loan_product["pdt_short_name"];
                      $LN_PDT_DESC = $loan_product["pdt_descrition"];

                      # ... GET REQUESTOR DETAILS
                      $cstmr = array();
                      $cstmr = FetchCustomerLoginDataByCustId($R_CUST_ID);
                      $R_CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
                      $response_msg = FetchCustomerDetailsFromCore($R_CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $APPLICANT_NAME = $CORE_RESP["displayName"];

                      $id = "FTT".($i+1);
                      $target = "#".$id;
                      $form_id = "FORM_".$id;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $LN_APPLN_NO; ?></td>
                        <td><?php echo $APPLICANT_NAME; ?></td>
                        <td><?php echo $DATE_GENERATED; ?></td>
                        <td><?php echo number_format($RQSTD_AMT); ?></td>
                        <td><?php echo $LN_PDT_NAME." ($LN_PDT_SHORT_NAME)"; ?></td>
                        <td>
                          <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="<?php echo $target; ?>">View</button>
                            <div id="<?php echo $id; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-mm">
                                <div class="modal-content" >

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Guarantorship Request</h4>
                                  </div>
                                  <div class="modal-body">
                                    <form id="<?php echo $form_id; ?>" method="post">
                                      <input type="hidden" id="RG_RECORD_ID" name="RG_RECORD_ID" value="<?php echo $RG_RECORD_ID; ?>">
                                      <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                                      <table class="table table-bordered" style="font-size: 12px;">
                                        <tr bgcolor="#EEE"><th colspan="2">Loan Details</th></tr>
                                        <tr><td><b>Guarrant Request Date:</b></td><td><?php echo $DATE_GENERATED; ?></td></tr>
                                        <tr><td><b>Loan Appln No:</b></td><td><?php echo $LN_APPLN_NO; ?></td></tr>
                                        <tr><td><b>Loan Product</b></td><td><?php echo $LN_PDT_NAME." ($LN_PDT_SHORT_NAME)";; ?></td></tr>
                                        <tr><td><b>Applicant</b></td><td><?php echo $APPLICANT_NAME; ?></td></tr>
                                        <tr><td><b>Loan Amount</b></td><td><?php echo number_format($RQSTD_AMT); ?></td></tr>
                                        <tr><td><b>Loan Purpose</b></td><td><?php echo $PURPOSE; ?></td></tr>
                                        <tr><td><b>Repayment Period</b></td><td><?php echo number_format($RQSTD_RPYMT_PRD)." ".$loan_product['repayment_frequency_type_value']; ?></td></tr>

                                        <tr><th colspan="2"></th></tr>
                                        <tr bgcolor="#EEE"><th colspan="2">Approval Details</th></tr>
                                        <tr><td colspan="2">
                                          <label>I am: </label><br>
                                          <select class="form-control" id="GRRT_APPVL" name="GRRT_APPVL">
                                            <option value="">----------</option>
                                            <option value="APPROVED">Approving</option>
                                            <option value="REJECTED">Rejecting</option>
                                          </select><br>

                                          <label>Additional Remarks: </label><br>
                                          <textarea class="form-control" id="RMKS" name="RMKS"></textarea><br>

                                          <span class="pull-right">
                                            <button type="submit" class="btn btn-primary btn-sm" name="btn_apprv_grrtrship">Save</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                          </span>
                                          
                                        </td></tr>

                                      </table>

                                        
                                       
                                        
                                      </form>
                                  </div>
                                 

                                </div>
                              </div>
                            </div>
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
