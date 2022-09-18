<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving the Data
$NTFCN_ID = mysql_real_escape_string(trim($_GET['k']));
$notif_msg = array();
$notif_msg = FetchNotifcationMessageById($NTFCN_ID);
$M_RECORD_ID = $notif_msg['RECORD_ID'];
$M_NTFCN_ID = $notif_msg['NTFCN_ID'];
$M_SENDER_ID = $notif_msg['SENDER_ID'];
$M_HAS_ATTCHMT_FLG = $notif_msg['HAS_ATTCHMT_FLG'];
$M_RECALL_FLG = $notif_msg['RECALL_FLG'];
$M_SEND_DATE = $notif_msg['SEND_DATE'];
$M_NTFCN_SUBJECT = $notif_msg['NTFCN_SUBJECT'];
$M_NTFCN_MSG = $notif_msg['NTFCN_MSG'];
$M_NTFCN_THREAD_FLG = $notif_msg['NTFCN_THREAD_FLG'];
$M_NTFCN_THREAD_ID = $notif_msg['NTFCN_THREAD_ID'];
$M_NTFCN_MSG_STATUS = $notif_msg['NTFCN_MSG_STATUS'];

# ... Get Sender Id
$address = FetchAddressFromAddressBookById($M_SENDER_ID);
$SENDER_NAME = $address['ADDRESS_ENTITY_NAME'];

# ... Get All Recipeints
$R_NAME_LIST = "";
$rcp_list = array();
$rcp_list = FetchNotificationRecipientsById($M_NTFCN_ID);
for ($i=0; $i < sizeof($rcp_list); $i++) { 
  $rcp = array();
  $rcp = $rcp_list[$i];
  $CP_RECORD_ID = $rcp['RECORD_ID'];
  $CP_NTFCN_ID = $rcp['NTFCN_ID'];
  $CP_RECIPIENT_TYPE = $rcp['RECIPIENT_TYPE'];
  $CP_RECIPIENT_ID = $rcp['RECIPIENT_ID'];
  $CP_RECEIVED_FLG = $rcp['RECEIVED_FLG'];
  $CP_RECEIVED_DATE = $rcp['RECEIVED_DATE'];

  
  $address = FetchAddressFromAddressBookById($CP_RECIPIENT_ID);
  $ADDRESS_ENTITY_NAME = $address['ADDRESS_ENTITY_NAME'];
  $ADDRESS_ENTITY_TYPE = $address['ADDRESS_ENTITY_TYPE'];
  $type = "";
  if ($ADDRESS_ENTITY_TYPE=="USER") { $type = " [SACCO staff]"; }
  if ($ADDRESS_ENTITY_TYPE=="GROUP") { $type = " [Group]"; }

  $R_NAME = "<span class='label label-default' style='font-size: 12px;'>".$ADDRESS_ENTITY_NAME.$type."</span>";

  if ($R_NAME_LIST=="") {
    $R_NAME_LIST = $R_NAME;
  }
  else{
    $R_NAME_LIST = $R_NAME_LIST." ".$R_NAME;
  }
}

# ... Get Notification Attachments
$attmt_list = array();
$attmt_list = FetchNotifcationAttachmentsById($M_NTFCN_ID);
  
  
# ... Trash Email ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
if (isset($_POST['btn_trash_email'])) {
  $M_NTFCN_ID = trim(mysql_real_escape_string($_POST['M_NTFCN_ID']));
  $M_NTFCN_THREAD_ID = trim(mysql_real_escape_string($_POST['M_NTFCN_THREAD_ID']));
  $SENDER_DEL_FLG="YY";
  $SENDER_ID=$_SESSION['UPR_USER_ID'];

  // ... SQL
  $q = "UPDATE notifications SET SENDER_DEL_FLG='$SENDER_DEL_FLG' WHERE NTFCN_ID='$M_NTFCN_ID' AND SENDER_ID='$SENDER_ID'";
  $update_response = ExecuteEntityUpdate($q);

  if ($update_response=="EXECUTED") {

    # ... Deleting thread for this user alone
    $THREAD_ID = $M_NTFCN_THREAD_ID;
    $NTFCN_ID =$M_NTFCN_ID;
    $ENTITY_ID = $SENDER_ID;
    $DEL_DATE = GetCurrentDateTime();
    $DEL_FLG = "YY";
    
    # ... Delete Thread
    $q2 = "INSERT INTO notification_thread_delete(THREAD_ID, NTFCN_ID, ENTITY_ID, DEL_DATE, DEL_FLG) 
                                         VALUES('$THREAD_ID', '$NTFCN_ID', '$ENTITY_ID', '$DEL_DATE', '$DEL_FLG')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q2);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($exec_response["RESP"]=="EXECUTED") {
      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "NOTIFICATION";
      $ENTITY_ID_AFFECTED = $M_NTFCN_ID;
      $EVENT = "TRASH_NTFCN";
      $EVENT_OPERATION = "TRASH_NTFCN_FOR_PARTICULAR_SENDER";
      $EVENT_RELATION = "notifications";
      $EVENT_RELATION_NO = $M_NTFCN_ID."|".$SENDER_ID."|".$M_NTFCN_THREAD_ID;
      $OTHER_DETAILS = $M_NTFCN_ID."|".$SENDER_ID."|".$M_NTFCN_THREAD_ID;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      # ... Send System Response
      $alert_type = "INFO";
      $alert_msg = "Message has been trashed.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:2; URL=nt-sent-messages");
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
    LoadDefaultCSSConfigurations("Details", $APP_SMALL_LOGO); 

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
                <a href="nt-trash" class="btn btn-sm btn-dark pull-left">Back</a>
                <h2>Subject: <?php echo $M_NTFCN_SUBJECT; ?></h2>
                <div class="clearfix"></div>
              </div>


              <div class="x_content">         
              
                <label>From:</label><br>
                <input type="text" class="form-control" disabled="" value="<?php echo $SENDER_NAME; ?>"><br>

                <label>To:</label><br>
                <div><?php echo $R_NAME_LIST; ?></div><br>

                <label>Subject:</label><br>
                <input type="text" class="form-control" disabled="" value="<?php echo $M_NTFCN_SUBJECT; ?>"><br>
             
                <label>Message Details:</label><br>
                <textarea rows="6" class="form-control" disabled=""><?php echo $M_NTFCN_MSG; ?></textarea><br>

                <?php
                if (sizeof($attmt_list)>0) {
                  ?>
                  <label>Attachments:</label><br>
                  <table>
                   
                      <?php
                      $NTFCN_FILE_ACCESS_PATH = GetSystemParameter("NTFCN_FILE_ACCESS_PATH")."/".$_SESSION['ORG_CODE'];
                      
                      for ($u=0; $u < sizeof($attmt_list); $u++) { 

                        $attmt = array();
                        $attmt = $attmt_list[$u];
                        $FL_RECORD_ID = $attmt['RECORD_ID'];
                        $FL_NTFCN_ID = $attmt['NTFCN_ID'];
                        $FL_ATTCHMT_NAME = $attmt['ATTCHMT_NAME'];
                        $FL_ATTCHMT_STATUS = $attmt['ATTCHMT_STATUS'];

                        $data_transfer = $NTFCN_FILE_ACCESS_PATH."/".$FL_NTFCN_ID."/".$FL_ATTCHMT_NAME;

                        ?>
                        <tr valign="top">
                          <td width="13"><?php echo ($u+1); ?>. </td>
                          <td><?php echo $FL_ATTCHMT_NAME; ?></td>
                          <td>
                              <a href="download-file?f=<?php echo $data_transfer; ?>" class="btn btn-default btn-xs pull-right"><i class="fa fa-download"></i> Download</a>
                          </td>
                        </tr>
                        <?php
                      }
                      ?>
                  </table>
                  <?php
                }
                ?>
                          
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
