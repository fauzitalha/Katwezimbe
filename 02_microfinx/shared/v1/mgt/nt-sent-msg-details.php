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
                <a href="nt-sent-messages" class="btn btn-sm btn-dark pull-left">Back</a>
                <h2>Subject: <?php echo $M_NTFCN_SUBJECT; ?></h2>
                <div class="btn-group pull-right">
                  <a class="btn btn-sm btn-primary" href="nt-inbox-msg-actionate?k=<?php echo $M_NTFCN_ID; ?>&a=RALL"><i class="fa fa-mail-reply-all"></i> Reply All</a>
                  <a class="btn btn-sm btn-info" href="nt-inbox-msg-actionate?k=<?php echo $M_NTFCN_ID; ?>&a=RONE"><i class="fa fa-mail-reply"></i> Reply</a>
                  <a class="btn btn-sm btn-success" href="nt-inbox-msg-actionate?k=<?php echo $M_NTFCN_ID; ?>&a=FWD"><i class="fa fa-share"></i> Forward</a>

                  <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#undobio"><i class="fa fa-trash-o"></i> Trash</button>
                  <div id="undobio" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel2">Delete mail</h4>
                        </div>
                        <div class="modal-body">
                            <form id="undobiooo" method="post">
                              <input type="hidden" id="M_NTFCN_ID" name="M_NTFCN_ID" value="<?php echo $M_NTFCN_ID; ?>">
                              <input type="hidden" id="M_NTFCN_THREAD_ID" name="M_NTFCN_THREAD_ID" value="<?php echo $M_NTFCN_THREAD_ID; ?>">
                              
                              Do you want to delete this email from your inbox?<br>
                              

                              <br>
                              <button type="submit" class="btn btn-danger btn-sm" name="btn_trash_email">Delete</button>
                              <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>
                            </form>
                        </div>
                       

                      </div>
                    </div>
                  </div>


                </div>
                <div class="clearfix"></div>
              </div>


              <div class="x_content">         

                <?php
                # ... Get Notification Thread
                $thread_list = array();
                $thread_list = FetchNotificationThread($M_NTFCN_THREAD_ID);
                if (sizeof($thread_list)>0) {
                  //echo "<pre>".print_r($thread_list,true)."</pre>";
                  ?>
                  <div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
                    <?php
                    for ($i=0; $i < sizeof($thread_list); $i++) { 
                      
                      $thread = array();
                      $thread = $thread_list[$i];
                      $Z_RECORD_ID = $thread['RECORD_ID'];
                      $Z_NTFCN_THREAD_ID = $thread['NTFCN_THREAD_ID'];
                      $Z_NTFCN_ID = $thread['NTFCN_ID'];
                      $Z_THREAD_TYPE = $thread['THREAD_TYPE'];
                      $Z_THREAD_DATE = $thread['THREAD_DATE'];

                      //echo $Z_NTFCN_ID."<br>";
                      # .... .... .... .... .... MESSAGE DETAILS  .... .... .... ..... .... ..... .... .... .... ....
                      $notif_msg_msg = array();
                      $notif_msg_msg = FetchNotifcationMessageById($Z_NTFCN_ID);
                      $M_RECORD_ID = $notif_msg_msg['RECORD_ID'];
                      $M_NTFCN_ID = $notif_msg_msg['NTFCN_ID'];
                      $M_SENDER_ID = $notif_msg_msg['SENDER_ID'];
                      $M_HAS_ATTCHMT_FLG = $notif_msg_msg['HAS_ATTCHMT_FLG'];
                      $M_RECALL_FLG = $notif_msg_msg['RECALL_FLG'];
                      $M_SEND_DATE = $notif_msg_msg['SEND_DATE'];
                      $M_NTFCN_SUBJECT = $notif_msg_msg['NTFCN_SUBJECT'];
                      $M_NTFCN_MSG = $notif_msg_msg['NTFCN_MSG'];
                      $M_NTFCN_THREAD_FLG = $notif_msg_msg['NTFCN_THREAD_FLG'];
                      $M_NTFCN_THREAD_ID = $notif_msg_msg['NTFCN_THREAD_ID'];
                      $M_NTFCN_MSG_STATUS = $notif_msg_msg['NTFCN_MSG_STATUS'];

                      # ... Get Sender Id
                      $address_msg = array();
                      $address_msg = FetchAddressFromAddressBookById($M_SENDER_ID);
                      $SENDER_NAME = $address_msg['ADDRESS_ENTITY_NAME'];

                      # ... Get All Recipeints
                      $R_NAME_LIST = "";
                      $rcp_list = array();
                      $rcp_list = FetchNotificationRecipientsById($M_NTFCN_ID);
                      for ($c=0; $c < sizeof($rcp_list); $c++) { 
                        $rcp = array();
                        $rcp = $rcp_list[$c];
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

                        $R_NAME = "<span class='label label-default' style='font-size: 12px; margin-bottom: 4px;'>".$ADDRESS_ENTITY_NAME.$type."</span>";

                        if ($R_NAME_LIST=="") {
                          $R_NAME_LIST = $R_NAME;
                        }
                        else{
                          $R_NAME_LIST = $R_NAME_LIST." ".$R_NAME;
                        }
                      }
                      # .... .... .... .... .... MESSAGE DETAILS  .... .... .... .... .... ....
                      # .... .... .... .... .... MESSAGE DETAILS  .... .... .... .... .... ....


                      # ... Get Notification Attachments
                      $file_flg = "";
                      $attmt_list = array();
                      $attmt_list = FetchNotifcationAttachmentsById($M_NTFCN_ID);
                      if (sizeof($attmt_list)>0) {
                        $file_flg = "<i class='fa fa-paperclip pull-right'></i>";
                      } else {
                        $file_flg = "";
                      }
					  
					  # ... Active Thread
					  $ACTV_THD = ($CP_NTFCN_ID==mysql_real_escape_string(trim($_GET['k'])))? "in": "";

                      # ... Build Collapsible
                      $collapse_id = "colps".($i+1);
                      $collapse_target = "#".$collapse_id;

                      ?>
                      <div class="panel">
                        <a class="panel-heading collapsed" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordion" href="<?php echo $collapse_target; ?>" aria-expanded="true" aria-controls="<?php echo $collapse_id; ?>">
                          <h4 class="panel-title">
                            <span class="badge badge-success"><?php echo (sizeof($thread_list)+1) - ($i+1); ?></span>
                             <?php echo $SENDER_NAME; ?>
                            <small><label class="pull-right">DATE: <?php echo $file_flg." ".$M_SEND_DATE; ?></label></small>
                          </h4>
                        </a>
                        <div id="<?php echo $collapse_id; ?>" class="panel-collapse collapse <?php echo $ACTV_THD; ?>" role="tabpanel" aria-labelledby="headingOne" aria-expanded="true" style="">
                          <div class="panel-body">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                      
                              <label>From:</label><br>
                              <input type="text" class="form-control" disabled="" value="<?php echo $SENDER_NAME; ?>"><br>

                              <label>To:</label><br>
                              <div><?php echo $R_NAME_LIST; ?></div><br>

                              <label>Subject:</label><br>
                              <input type="text" class="form-control" disabled="" value="<?php echo $M_NTFCN_SUBJECT; ?>"><br>
                           
                              <label>Message Details:</label><br>
                              <textarea class="form-control" disabled=""><?php echo $M_NTFCN_MSG; ?></textarea><br>

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
                      <?php
                    } // ... END..LOOP

                    ?>
                  </div>
                  <?php
                } // ... END..IFF..ELSE
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
