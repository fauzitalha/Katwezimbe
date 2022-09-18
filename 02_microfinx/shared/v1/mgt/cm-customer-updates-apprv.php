<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Create New Group
if (isset($_POST['btn_action_chng'])) {
  $RECORD_ID = trim(mysql_real_escape_string($_POST['RECORD_ID']));
  $CUST_ID = trim(mysql_real_escape_string($_POST['CUST_ID']));
  $CUST_CORE_ID = trim(mysql_real_escape_string($_POST['CUST_CORE_ID']));
  $CHANGE_TYPE = trim(mysql_real_escape_string($_POST['CHANGE_TYPE']));
  $OLD_VALUE = trim(mysql_real_escape_string($_POST['OLD_VALUE']));
  $NEW_VALUE = trim(mysql_real_escape_string($_POST['NEW_VALUE']));
  $CUST_EMAIL = trim(mysql_real_escape_string($_POST['CUST_EMAIL']));
  $CORE_CUST_NAME = trim(mysql_real_escape_string($_POST['CORE_CUST_NAME']));

  $V_STATUS = trim(mysql_real_escape_string($_POST['V_STATUS']));
  $V_RMKS = trim(mysql_real_escape_string($_POST['V_RMKS']));

  $CHNG_APPRVL_RMKS = $V_RMKS;
  $CHNG_APPRVL_DATE = GetCurrentDateTime(); 
  $CHNG_APPRVL_BY = $_SESSION['UPR_USER_ID'];
  $CHNG_STATUS = $V_STATUS;

  # ... SQL
  $q2 = "UPDATE cstmrs_info_chng_log 
         SET CHNG_APPRVL_RMKS='$CHNG_APPRVL_RMKS'
            ,CHNG_APPRVL_DATE='$CHNG_APPRVL_DATE'
            ,CHNG_APPRVL_BY='$CHNG_APPRVL_BY'
            ,CHNG_STATUS='$CHNG_STATUS'
         WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {
    $IS_ACTION_TAKEN = "";
    if ($CHNG_STATUS=="REJECTED") {
      # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "Change Request Rejected";
      $RECIPIENT_EMAILS = $CUST_EMAIL;
      $EMAIL_MESSAGE = mysql_real_escape_string("Dear ".$CORE_CUST_NAME."<br>"
                      ."We are sorry to inform you that your change request has been denied. Below are the details;<br>"
                      ."<br><b>CHANGE REF:</b> ".$RECORD_ID."."
                      ."<br><b>CHANGE TYPE:</b> ".$CHANGE_TYPE."."
                      ."<br><b>ADDITIONAL RMKS:</b> ".$CHNG_APPRVL_RMKS."."
                      ."Regards<br>"
                      ."Management<br>"
                      ."<i></i>");
      $EMAIL_ATTACHMENT_PATH = "";
      $RECORD_DATE = GetCurrentDateTime();
      $EMAIL_STATUS = "NN";

      $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
      ExecuteEntityInsert($qqq);

      $IS_ACTION_TAKEN = "YES";
    } elseif ($CHNG_STATUS=="APPROVED") {

      # ............................................ EMAIL CHANGE .....................................................
      if ($CHANGE_TYPE=="EMAIL_CHANGE") {
        $CORE_RQST_MSG = BuildUpdateEmailMessage($NEW_VALUE);
        $response_msg = UpdateCustomerEmail($CUST_CORE_ID, $CORE_RQST_MSG, $MIFOS_CONN_DETAILS);
        $CONN_FLG = $response_msg["CONN_FLG"];
        $CORE_RESP = $response_msg["CORE_RESP"];
        if (isset($CORE_RESP["resourceId"])) {
          $CUST_EMAIL = AES256::encrypt($NEW_VALUE);

          $q3 = "UPDATE cstmrs SET CUST_EMAIL='$CUST_EMAIL' WHERE CUST_ID='$CUST_ID'";
          $update_response3 = ExecuteEntityUpdate($q3);
          if ($update_response=="EXECUTED") {
            $IS_ACTION_TAKEN = "YES";
          }
        }
      }

      # ............................................ PHONE CHANGE .....................................................
      if ($CHANGE_TYPE=="PHONE_CHANGE") {
        $CORE_RQST_MSG = BuildUpdatePhoneMessage($NEW_VALUE);
        $response_msg = UpdateCustomerPhone($CUST_CORE_ID, $CORE_RQST_MSG, $MIFOS_CONN_DETAILS);
        $CONN_FLG = $response_msg["CONN_FLG"];
        $CORE_RESP = $response_msg["CORE_RESP"];
        if (isset($CORE_RESP["resourceId"])) {
          $CUST_PHONE = AES256::encrypt($NEW_VALUE);
          $q3 = "UPDATE cstmrs SET CUST_PHONE='$CUST_PHONE' WHERE CUST_ID='$CUST_ID'";
          $update_response3 = ExecuteEntityUpdate($q3);
          if ($update_response=="EXECUTED") {
            $IS_ACTION_TAKEN = "YES";
          }
        }
      }

      # ............................................ PHOTO CHANGE .....................................................
      if ($CHANGE_TYPE=="PHOTO_CHANGE") {
        $PHOTO_CHANGE_BASEPATH = GetSystemParameter("PHOTO_CHANGE_BASEPATH")."/".$_SESSION['ORG_CODE'];
        $dir = $PHOTO_CHANGE_BASEPATH;
        $PHOTO = $dir."/".$NEW_VALUE;

        $IMG_DATA = BuildImageUploadRqstMsg($PHOTO);
        $response_msg = UpdateCustomerImage($CUST_CORE_ID, $IMG_DATA, $MIFOS_CONN_DETAILS);
        $CONN_FLG = $response_msg["CONN_FLG"];
        $CORE_RESP = $response_msg["CORE_RESP"];
        if (isset($CORE_RESP["resourceId"])) {
          $IS_ACTION_TAKEN = "YES";
        }
      }

      # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $cstmr = array();
      $cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
      $CUST_EMAIL = AES256::decrypt($cstmr['CUST_EMAIL']);

      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "Change Request Approved";
      $RECIPIENT_EMAILS = $CUST_EMAIL;
      $EMAIL_MESSAGE = mysql_real_escape_string("Dear ".$CORE_CUST_NAME."<br>"
                      ."Your change request has been approved and taken effect. Below are the details;<br>"
                      ."<br><b>CHANGE REF:</b> ".$RECORD_ID."."
                      ."<br><b>CHANGE TYPE:</b> ".$CHANGE_TYPE."."
                      ."<br><b>ADDITIONAL RMKS:</b> ".$CHNG_APPRVL_RMKS."."
                      ."Regards<br>"
                      ."Management<br>"
                      ."<i></i>");
      $EMAIL_ATTACHMENT_PATH = "";
      $RECORD_DATE = GetCurrentDateTime();
      $EMAIL_STATUS = "NN";

      $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
      ExecuteEntityInsert($qqq);
    } # ...END..IFF..ELSE

    if ($IS_ACTION_TAKEN=="YES") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "CUSTOMER_INFO_CHANGE";
      $ENTITY_ID_AFFECTED = $CUST_ID;
      $EVENT = "APPROVE";
      $EVENT_OPERATION = "APPROVE CUSTOMER UPDATE";
      $EVENT_RELATION = "cstmrs_info_chng_log & cstmrs";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = "A_STATUS: ".$CHNG_STATUS." A_RMKS: ".$CHNG_APPRVL_RMKS;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      if ($CHNG_STATUS=="APPROVED") {
        $alert_type = "SUCCESS";
        $alert_msg = "MESSAGE: Customer update approved. Modification have also taken effect in core system. Re-directing in 5 Seconds";
      } elseif ($CHNG_STATUS=="REJECTED") {
        $alert_type = "ERROR";
        $alert_msg = "MESSAGE: Customer update has been rejected. Re-directing in 5 Seconds";
      }
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5; url=cm-customer-updates-apprv");
    }
  }
}



?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Main Control", $APP_SMALL_LOGO); 

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
                <h2>Approve Customer Updates</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="9" bgcolor="#EEE">Change request list</th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Change Type</th>
                      <th>Client Id</th>
                      <th>Client Name</th>
                      <th>Current Value</th>
                      <th>New Value</th>
                      <th>Verif Date</th>
                      <th>Appln Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $CHNG_STATUS = "VERIFIED";
                    $chng_list = array();
                    $chng_list = FetchClientUpdates($CHNG_STATUS);

                    for ($i=0; $i < sizeof($chng_list); $i++) {
                      $chng = array();
                      $chng = $chng_list[$i];
                      $RECORD_ID = $chng['RECORD_ID'];
                      $CUST_ID = $chng['CUST_ID'];
                      $CHANGE_TYPE = $chng['CHANGE_TYPE'];
                      $OLD_VALUE = $chng['OLD_VALUE'];
                      $NEW_VALUE = $chng['NEW_VALUE'];
                      $CHNG_INIT_DATE = $chng['CHNG_INIT_DATE'];
                      $CHNG_INIT_BY = $chng['CHNG_INIT_BY'];
                      $CHNG_VERIF_RMKS = $chng['CHNG_VERIF_RMKS'];
                      $CHNG_VERIF_DATE = $chng['CHNG_VERIF_DATE'];
                      $CHNG_VERIF_BY = $chng['CHNG_VERIF_BY'];
                      $CHNG_APPRVL_RMKS = $chng['CHNG_APPRVL_RMKS'];
                      $CHNG_APPRVL_DATE = $chng['CHNG_APPRVL_DATE'];
                      $CHNG_APPRVL_BY = $chng['CHNG_APPRVL_BY'];
                      $CHNG_STATUS = $chng['CHNG_STATUS'];

                      # ... Get Client Name
                      $cstmr = array();
                      $cstmr = FetchCustomerLoginDataByCustId($CUST_ID);
                      $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
                      $CUST_EMAIL = AES256::decrypt($cstmr['CUST_EMAIL']);

                      $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $CORE_CUST_NAME = $CORE_RESP["displayName"];


                      # ... Fetch Verifier
                      $ADDED_BY_CORE_ID = GetUserCoreIdFromWebApp($CHNG_VERIF_BY);
                      $response_msg = FetchUserDetailsFromCore($ADDED_BY_CORE_ID, $MIFOS_CONN_DETAILS);
                      //$CONN_FLG = $response_msg["CONN_FLG"];
                      //$RESP_FLG = $response_msg["RESP_FLG"];
                      $ADDED_CORE_RESP = $response_msg["CORE_RESP"];
                      $ADDED_BY_NAME = $ADDED_CORE_RESP["username"]." (".$ADDED_CORE_RESP["firstname"]." ".$ADDED_CORE_RESP["lastname"].")";

                      $id = "FTT".($i+1);
                      $target = "#".$id;
                      $form_id = "FORM_".$id;

                      $id2 = "FTT2".($i+1);
                      $target2 = "#".$id2;
                      $form_id2 = "FORM_".$id2;

                      $id3 = "FTT3".($i+1);
                      $target3 = "#".$id3;
                      $form_id3 = "FORM_".$id3;

                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $CHANGE_TYPE; ?></td>
                        <td><?php echo $CUST_ID; ?></td>
                        <td><?php echo $CHNG_VERIF_DATE; ?></td>
                        <?php
                        if ($CHANGE_TYPE=="PHOTO_CHANGE") {
                          $IMAGE_response_msg = FetchClientImage($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                          ?>
                          <td>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="<?php echo $target; ?>">Current Photo</button>
                            <div id="<?php echo $id; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-mm">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Current Photo</h4>
                                  </div>
                                  <div class="modal-body">
                                     <img src="<?php echo $IMAGE_response_msg; ?>" width="100%">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td>
                            <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="<?php echo $target2; ?>">New Photo</button>
                            <div id="<?php echo $id2; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-mm">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">New Photo</h4>
                                  </div>
                                  <div class="modal-body">
                                    <?php
                                    $PHOTO_CHANGE_BASEPATH_VIEW = GetSystemParameter("PHOTO_CHANGE_BASEPATH_VIEW")."/".$_SESSION['ORG_CODE'];
                                    $dir = $PHOTO_CHANGE_BASEPATH_VIEW;
                                    $PHOTO = $dir."/".$NEW_VALUE;
                                    ?>
                                     <img src="<?php echo $PHOTO; ?>" width="100%">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                          <?php
                        } else {
                          ?>
                          <td><?php echo $OLD_VALUE; ?></td>
                          <td><?php echo $NEW_VALUE; ?></td>
                          <?php
                        }
                        ?>
                        <td><?php echo $CHNG_INIT_DATE; ?></td>
                        <td><?php echo $CHNG_STATUS; ?></td>
                        <td>
                          <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="<?php echo $target3; ?>">Action</button>
                            <div id="<?php echo $id3; ?>" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Action Change Request</h4>
                                  </div>
                                  <div class="modal-body">
                                    <form method="post" id="<?php echo $form_id3; ?>">
                                      <input type="hidden" id="RECORD_ID" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                      <input type="hidden" id="CUST_ID" name="CUST_ID" value="<?php echo $CUST_ID; ?>">
                                      <input type="hidden" id="CUST_CORE_ID" name="CUST_CORE_ID" value="<?php echo $CUST_CORE_ID; ?>">
                                      <input type="hidden" id="CHANGE_TYPE" name="CHANGE_TYPE" value="<?php echo $CHANGE_TYPE; ?>">
                                      <input type="hidden" id="OLD_VALUE" name="OLD_VALUE" value="<?php echo $OLD_VALUE; ?>">
                                      <input type="hidden" id="NEW_VALUE" name="NEW_VALUE" value="<?php echo $NEW_VALUE; ?>">
                                      <input type="hidden" id="CUST_EMAIL" name="CUST_EMAIL" value="<?php echo $CUST_EMAIL; ?>">
                                      <input type="hidden" id="CORE_CUST_NAME" name="CORE_CUST_NAME" value="<?php echo $CORE_CUST_NAME; ?>">


                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <label>Change Type:</label> <br>
                                        <input type="text" disabled="" class="form-control" value="<?php echo $CHANGE_TYPE; ?>"><br><br>
                                      </div>

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <label>Cust Id:</label> <br>
                                        <input type="text" disabled="" class="form-control" value="<?php echo $CUST_ID; ?>"><br><br>
                                      </div>

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <label>Cust Name:</label> <br>
                                        <input type="text" disabled="" class="form-control" value="<?php echo $CORE_CUST_NAME; ?>"><br><br>
                                      </div>

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <label>Verified On:</label> <br>
                                        <input type="text" disabled="" class="form-control" value="<?php echo $CHNG_VERIF_DATE; ?>"><br><br>
                                      </div>

                                       <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <label>Verified By:</label> <br>
                                        <input type="text" disabled="" class="form-control" value="<?php echo $ADDED_BY_NAME; ?>"><br><br>
                                      </div>


                                      <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label>Select Action:</label><br>
                                        <select id="V_STATUS" name="V_STATUS" class="form-control" required="">
                                          <option value="">--------</option>
                                          <option value="APPROVED">Approve</option>
                                          <option value="REJECTED">Reject</option>
                                        </select><br><br>

                                      </div>
                                      <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label>Action Remarks:</label><br>
                                        <textarea id="V_RMKS" name="V_RMKS" class="form-control"></textarea><br><br>
                                      </div>

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <button type="submit" class="btn btn-primary btn-sm" name="btn_action_chng">Submit for Approval</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </td>
                      </tr>
                      <?php
                    } # ... END..LOOP
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
