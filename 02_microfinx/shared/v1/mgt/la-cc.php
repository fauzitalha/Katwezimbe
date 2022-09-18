<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");


# ... F0000001: FORWARED APPLN .....................................................................................#
if (isset($_POST['btn_ffr'])) {

  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $CC_COMMITTEE_ID = mysql_real_escape_string(trim($_POST['CC_COMMITTEE_ID']));
  $CC_HANDLER_WKFLW_ID = $CC_COMMITTEE_ID;
  $CC_STATUS = "APPROVED";
  $CC_STATUS_DATE = GetCurrentDateTime();
  $CC_RMKS = "APPLICATION IS APPROVED BY CC";

  $q2 = "UPDATE loan_applns 
           SET CC_HANDLER_WKFLW_ID='$CC_HANDLER_WKFLW_ID'
              ,CC_STATUS='$CC_STATUS'
              ,CC_STATUS_DATE='$CC_STATUS_DATE'
              ,CC_RMKS='$CC_RMKS'
              ,LN_APPLN_STATUS='READY_4_REVIEW'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q2);

  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "CC_MAIN_APPROVAL";
    $EVENT_OPERATION = "MAIN_CC_APPROVAL_FOR_LOAN_APPLN";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $LN_APPLN_NO;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "MESSAGE: Loan Application has been forwarded for review. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5; URL=la-cc");
  }
}

# ... F0000002: BOUCNE BACK APPLN .....................................................................................#
if (isset($_POST['btn_bb'])) {

  $LN_APPLN_NO = mysql_real_escape_string(trim($_POST['LN_APPLN_NO']));
  $CC_COMMITTEE_ID = mysql_real_escape_string(trim($_POST['CC_COMMITTEE_ID']));
  $CC_HANDLER_WKFLW_ID = $CC_COMMITTEE_ID;
  $CC_STATUS = "BOUNCED_BACK";
  $CC_STATUS_DATE = GetCurrentDateTime();
  $CC_RMKS = "APPLICATION IS BOUNCED BACK BY CC";

  $q2 = "UPDATE loan_applns 
           SET CC_HANDLER_WKFLW_ID='$CC_HANDLER_WKFLW_ID'
              ,CC_STATUS='$CC_STATUS'
              ,CC_STATUS_DATE='$CC_STATUS_DATE'
              ,CC_RMKS='$CC_RMKS'
              ,LN_APPLN_STATUS='CC_BOUNCED_BACK'
          WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q2);

  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "LOAN_APPLN";
    $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
    $EVENT = "CC_MAIN_BOUNCE_BACK";
    $EVENT_OPERATION = "MAIN_CC_BOUNCE_BACK_FOR_LOAN_APPLN";
    $EVENT_RELATION = "loan_applns";
    $EVENT_RELATION_NO = $LN_APPLN_NO;
    $OTHER_DETAILS = $LN_APPLN_NO;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "ERROR";
    $alert_msg = "MESSAGE: Loan Application has been bounced back to the application queue. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5; URL=la-cc");
  }
}


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Credit Committee", $APP_SMALL_LOGO); 

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
                <h2>Loans Applns Credit Committee</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="9" bgcolor="#EEE">Loan Applications due for credit committed approval</th>
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
                      <th>Apprvl Prgrss</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $la_list = array();
                    $la_list = FetchCreditCommitteeLoanApplns();
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


                      # ... 03: Determine if you are a member of CC which is allowed to approve this loan application
                      $CC_COMMITTEE_ID = FetchCreditCommitteeForLoanProduct($LN_PDT_ID);
                      $GRP_MEMBER_ID = $_SESSION['UPR_USER_ID'];
                      $Q_CCNNTT ="SELECT count(*) as RTN_VALUE 
                                  FROM appln_mgt_group_members 
                                  WHERE GRP_ID='$CC_COMMITTEE_ID' 
                                    AND GRP_MEMBER_ID='$GRP_MEMBER_ID' 
                                    AND GRP_MEMBER_STATUS='ACTIVE'";
                      $CNNT_CC = ReturnOneEntryFromDB($Q_CCNNTT);


                      # ... 04: Determine if the entire application is approved by the credit committee members
                      $ACTION_TYPE = "APPRV_LOAN_APPLN";
                      $GRP_ID = $CC_COMMITTEE_ID;
                      $APPLN_NO = $LN_APPLN_NO;
                      $resp = array();
                      $resp = ProcessCCApproval($ACTION_TYPE, $GRP_ID, $APPLN_NO);
                      $MMBR_CNT = $resp["MMBR_CNT"];
                      $MMBR_CNT_APPRV = $resp["MMBR_CNT_APPRV"];
                      $MMBR_CNT_REJN = $resp["MMBR_CNT_REJN"];
                      $MMBR_NO_ACTION = $MMBR_CNT - ($MMBR_CNT_APPRV+$MMBR_CNT_REJN);

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
                          <?php
                          if ($CNNT_CC>0) {
                            ?>
                            <a href="la-cc-ind?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">Action</a>
                            <?php
                          } else {
                            ?>
                            <span style="font-size: 9px;" title="You are not a member of the credit committee which approves these loan applications">
                              <button type="button" class="btn btn-info btn-xs" disabled="">Info</button>
                            </span>
                            <?php
                          }
                          ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="<?php echo $target3; ?>">View</button>
                          <div id="<?php echo $id3; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-mm">
                              <div class="modal-content">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Approval Committee Progress</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="<?php echo $form_id3; ?>" method="post">
                                      <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                                      <input type="hidden" id="CC_COMMITTEE_ID" name="CC_COMMITTEE_ID" value="<?php echo $CC_COMMITTEE_ID; ?>">
                                      
                                      <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                        <label>Total number of Committee Members:</label>
                                        <input type="text" class="form-control" disabled="" value="<?php echo $MMBR_CNT; ?>">
                                      </div>

                                      <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                        <label>Count of pending Approvals:</label>
                                        <input type="text" class="form-control" disabled="" value="<?php echo $MMBR_NO_ACTION; ?>">
                                      </div>

                                      <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                        <label>Count of Approvals:</label>
                                        <input type="text" class="form-control" disabled="" value="<?php echo $MMBR_CNT_APPRV; ?>">
                                      </div>

                                      <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                        <label>Count of Rejections:</label>
                                        <input type="text" class="form-control" disabled="" value="<?php echo $MMBR_CNT_REJN; ?>">
                                      </div>

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <label>All Member Remarks:</label>
                                        <table style="font-size: 10px;" class="table table-striped table-bordered">
                                          <thead>
                                            <tr valign="top">
                                              <th>#</th>
                                              <th>Member</th>
                                              <th>Status</th>
                                              <th>Remarks</th>
                                              <th>Date Taken</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <?php
                                            $uu = 0;
                                            $grp_member_list = array();
                                            $grp_member_list = FetchAppMgtGroupMembers($CC_COMMITTEE_ID);
                                            for ($ttt=0; $ttt < sizeof($grp_member_list); $ttt++) { 
                                              $grp_membr = array();
                                              $grp_membr = $grp_member_list[$ttt];
                                              $GRP_RECORD_ID = $grp_membr['RECORD_ID'];
                                              $GRP_GRP_ID = $grp_membr['GRP_ID'];
                                              $GRP_GRP_MEMBER_ID = $grp_membr['GRP_MEMBER_ID'];
                                              $GRP_ADDED_BY = $grp_membr['ADDED_BY'];
                                              $GRP_CREATED_ON = $grp_membr['CREATED_ON'];
                                              $GRP_GRP_MEMBER_STATUS = $grp_membr['GRP_MEMBER_STATUS'];

                                              # ... FETCH MEMBER NAME
                                              $USER_DETAILS = array();
                                              $USER_DETAILS = GetUserDetailsFromPortal($GRP_GRP_MEMBER_ID);
                                              $GRP_USER_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
                                                
                                              $response_msg = FetchUserDetailsFromCore($GRP_USER_CORE_ID, $MIFOS_CONN_DETAILS);
                                              $CONN_FLG = $response_msg["CONN_FLG"];
                                              $CORE_RESP = $response_msg["CORE_RESP"];
                                              $sys_usr = $response_msg["CORE_RESP"];
                                              $CORE_username = $sys_usr["username"];
                                              $firstname = $sys_usr["firstname"];
                                              $lastname = $sys_usr["lastname"];

                                              $grp_full_name = $firstname." ".$lastname;

                                              if ($GRP_GRP_MEMBER_STATUS!="ACTIVE") {
                                                // ... do nothing
                                              } else if ($GRP_GRP_MEMBER_STATUS=="ACTIVE"){
                                                # ... FETCH MEMBER COMMENT
                                                $ACTION_TYPE='APPRV_LOAN_APPLN';
                                                $appln_grp_action_details = array();
                                                $appln_grp_action_details = FetchApplnGroupActionTakenByIndMember($ACTION_TYPE, $LN_APPLN_NO, $GRP_GRP_ID, $GRP_GRP_MEMBER_ID);

                                                $AA_RECORD_ID = "";
                                                $AA_ACTION_ID = "";
                                                $AA_ACTION_TYPE = "";
                                                $AA_APPLN_NO = "";
                                                $AA_GRP_ID = "";
                                                $AA_GRP_MEMBER_ID = "";
                                                $AA_ACTION_TAKEN = "";
                                                $AA_ACTION_REMARKS = "";
                                                $AA_DATE_ACTION_TAKEN = "";
                                                $AA_ACTION_RETRY_FLG = "";
                                                $AA_CNT_RETRIED = "";
                                                $AA_DATE_LST_RETRIED = "";
                                                $DDATE  = "";

                                                if (isset($appln_grp_action_details['RECORD_ID'])) {
                                                  $AA_RECORD_ID = $appln_grp_action_details['RECORD_ID'];
                                                  $AA_ACTION_ID = $appln_grp_action_details['ACTION_ID'];
                                                  $AA_ACTION_TYPE = $appln_grp_action_details['ACTION_TYPE'];
                                                  $AA_APPLN_NO = $appln_grp_action_details['APPLN_NO'];
                                                  $AA_GRP_ID = $appln_grp_action_details['GRP_ID'];
                                                  $AA_GRP_MEMBER_ID = $appln_grp_action_details['GRP_MEMBER_ID'];
                                                  $AA_ACTION_TAKEN = $appln_grp_action_details['ACTION_TAKEN'];
                                                  $AA_ACTION_REMARKS = $appln_grp_action_details['ACTION_REMARKS'];
                                                  $AA_DATE_ACTION_TAKEN = $appln_grp_action_details['DATE_ACTION_TAKEN'];
                                                  $AA_ACTION_RETRY_FLG = $appln_grp_action_details['ACTION_RETRY_FLG'];
                                                  $AA_CNT_RETRIED = $appln_grp_action_details['CNT_RETRIED'];
                                                  $AA_DATE_LST_RETRIED = $appln_grp_action_details['DATE_LST_RETRIED'];
                                                  $DDATE = ($AA_DATE_LST_RETRIED=="")? $AA_DATE_ACTION_TAKEN : $AA_DATE_LST_RETRIED;
                                                } 
                                                
                                                ?>
                                                 <tr valign="top">
                                                  <td><?php echo ($uu+1); ?>. </td>
                                                  <td><?php echo $grp_full_name; ?></td>
                                                  <td><?php echo $AA_ACTION_TAKEN; ?></td>
                                                  <td><?php echo $AA_ACTION_REMARKS; ?></td>
                                                  <td><?php echo $DDATE; ?></td>
                                                </tr>
                                                <?php

                                                $uu++;
                                              }
                                            }
                                                          
                                            ?>
                                          </tbody>
                                        </table>
                                      </div>

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <?php
                                        if ($MMBR_NO_ACTION==0) {
                                          if ($MMBR_CNT_APPRV==$MMBR_CNT) {
                                            ?>
                                            <br>
                                            <button type="submit" class="btn btn-success btn-sm" name="btn_ffr">Forward For Review</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <?php
                                          } else {
                                            ?>
                                            <br>
                                            <button type="submit" class="btn btn-danger btn-sm" name="btn_bb">Bounce Back</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <?php
                                          }
                                        } else if ($MMBR_NO_ACTION!=0) {
                                          ?>
                                          <br>
                                          <button type="submit" class="btn btn-info btn-sm" disabled=""><?php echo $MMBR_NO_ACTION." member(s) are yet to action this loan application."; ?></button>
                                          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                          <?php
                                        }
                                        ?>
                                      </div>
                                      

                                      
                                      
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
