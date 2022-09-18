<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Files Attachments SESSION

# ... Debugging Utilities
//echo "<pre>".print_r($_SESSION['ATTCHMNTS'],true)."</pre>";

# ... Add Attachments
if (isset($_POST['btn_add_file'])) {
  
  $file_size = $_FILES['attmt']['size'];
  $file_type = $_FILES['attmt']['type'];
  $file_ext = strtolower(substr(strrchr($_FILES['attmt']['name'],"."),1));
  $file_name = $_FILES['attmt']['name'];
  $file_temp_name = $_SESSION['CST_USR_ID']."_".TimeStamp()."_File.".$file_ext;
  $file_temp = $_FILES['attmt']['tmp_name'];


  // ... File Verfication
  $valid_file_types = array("image/gif", "image/jpeg", "image/png", "application/pdf", "application/msword", "application/msword"
                           ,"application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.ms-excel"
                           ,"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.ms-powerpoint"
                           ,"application/vnd.openxmlformats-officedocument.presentationml.presentation", "text/plain");
  $valid_file_extensions = array("gif", "png", "jpg", "jpeg", "pdf", "doc", "dot", "docx", "xls", "xlsx", "ppt", "pptx", "txt");

  $required_specs = array();
  $required_specs["FILE_SIZE"] = 700000;        // ... 700KB                       
  $required_specs["FILE_TYPES"] = $valid_file_types; 
  $required_specs["FILE_EXTENSIONS"] = $valid_file_extensions; 

  $file_specs = array();
  $file_specs["FILE_SIZE"] = $file_size;                        
  $file_specs["FILE_TYPE"] = $file_type; 
  $file_specs["FILE_EXTENSION"] = $file_ext; 
  $file_results = ValidateFileAttachment($required_specs, $file_specs);
  $FILE_SIZE_CHK = $file_results["FILE_SIZE_CHK"];
  $FILE_TYPE_CHK = $file_results["FILE_TYPE_CHK"];
  $FILE_EXTSN_CHK = $file_results["FILE_EXTSN_CHK"];
  $FILE_RMKS = $file_results["FILE_RMKS"];


  if ($FILE_SIZE_CHK&&$FILE_TYPE_CHK&&$FILE_EXTSN_CHK) {

    # ... Uploading file to temp staging location
    $temp_dir = GetSystemParameter("NTFCN_FILE_BASEPATH_TEMP")."/".$_SESSION['ORG_CODE'];
    if (!is_dir($temp_dir)) {
      mkdir($temp_dir);
    }
    $result = move_uploaded_file($file_temp, $temp_dir."/".$file_temp_name);

    if ($result==1) {
      $file = array();
      $file["file_size"] = $file_size;
      $file["file_type"] = $file_type;
      $file["file_ext"] = $file_ext;
      $file["file_name"] = $file_name;
      $file["file_temp_name"] = $file_temp_name;
      $file["file_temp"] = $temp_dir."/".$file_temp_name;

      # ... Adding to session array
      if (!isset($_SESSION['ATTCHMNTS'])) {
        $_SESSION['ATTCHMNTS'][0] = $file;
      }
      else{
        $new_index = sizeof($_SESSION['ATTCHMNTS']);
        $_SESSION['ATTCHMNTS'][$new_index] = $file;
      }
    }

    

  } else {
    $RESP_RMKS = $FILE_RMKS;
    $alert_type = "ERROR";
    $alert_msg = $RESP_RMKS;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

# ... Remove Attachments
if (isset($_POST['btn_rem_file'])) { 
  $f_index = $_POST['f_index'];
  $_SESSION['ATTCHMNTS'][$f_index] = "REMOVED";
}

# ... Send Message
if (isset($_POST["btn_send_message"])) {
  
  # ... 01: Create Database Record
  $SENDER_ID = $_SESSION['CST_USR_ID'];
  $SEND_DATE = GetCurrentDateTime();
  $NTFCN_SUBJECT = trim(mysql_real_escape_string($_POST['subject']));
  $NTFCN_MSG = trim(mysql_real_escape_string($_POST['message']));
  $NTFCN_THREAD_FLG = "YY";
  $HAS_ATTCHMT_FLG = "NN";

  $q = "INSERT INTO notifications(SENDER_ID, HAS_ATTCHMT_FLG, SEND_DATE, NTFCN_SUBJECT, NTFCN_MSG, NTFCN_THREAD_FLG) VALUES('$SENDER_ID', '$HAS_ATTCHMT_FLG', '$SEND_DATE', '$NTFCN_SUBJECT', '$NTFCN_MSG', '$NTFCN_THREAD_FLG')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];


  # ... 02: Create the Notification ID & Thread Id
  $id_prefix = "ZP";
  $id_len = 20;
  $id_record_id = NotificationId()."000".$RECORD_ID;
  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
  $NTFCN_ID = $ENTITY_ID;

  $nt_id_prefix = "THD";
  $nt_id_len = 40;
  $nt_id_record_id = TimeStamp()."000".$RECORD_ID;
  $NTFCN_THREAD_ID_DD = ProcessEntityID($nt_id_prefix, $nt_id_len, $nt_id_record_id);
  $NTFCN_THREAD_ID = $NTFCN_THREAD_ID_DD;


  $q2 = "UPDATE notifications SET NTFCN_ID='$NTFCN_ID', NTFCN_THREAD_ID='$NTFCN_THREAD_ID' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);


  # ... Creating Thread in Threads
  $THD_NTFCN_THREAD_ID = $NTFCN_THREAD_ID;
  $THD_NTFCN_ID = $NTFCN_ID;
  $THD_THREAD_TYPE = "FIRST_MAIL";
  $THD_THREAD_DATE = GetCurrentDateTime();
  $q7 = "INSERT INTO notification_thread(NTFCN_THREAD_ID, NTFCN_ID, THREAD_TYPE, THREAD_DATE) VALUES('$THD_NTFCN_THREAD_ID', '$THD_NTFCN_ID', '$THD_THREAD_TYPE', '$THD_THREAD_DATE')";
  ExecuteEntityInsert($q7);


  # ... 03: Process Notification Recipients
  $RECIPIENTS = array();
  $RECIPIENTS = $_POST['RECIPIENTS'];
  foreach ($RECIPIENTS as $r){
    $r_record = $r;
    $r_ff = explode('#', $r_record);
    $ADDRESS_ENTITY_ID = $r_ff[0];
    $ADDRESS_ENTITY_TYPE = $r_ff[1];

    $RECIPIENT_TYPE = $ADDRESS_ENTITY_TYPE;
    $RECIPIENT_ID = $ADDRESS_ENTITY_ID;
    $RECEIVED_FLG = "YY";
    $RECEIVED_DATE = GetCurrentDateTime();

    #  ... SQL
    $q4 = "INSERT INTO notification_recipients(NTFCN_ID, RECIPIENT_TYPE, RECIPIENT_ID, RECEIVED_FLG, RECEIVED_DATE) VALUES('$NTFCN_ID', '$RECIPIENT_TYPE', '$RECIPIENT_ID', '$RECEIVED_FLG', '$RECEIVED_DATE')";
    ExecuteEntityInsert($q4);
  }

  # ... 04: Process Notification Attachments
  $FILE_CNT = 0;
  if (isset($_SESSION['ATTCHMNTS'])) {

    
    $NTFCN_FILE_BASEPATH = GetSystemParameter("NTFCN_FILE_BASEPATH")."/".$_SESSION['ORG_CODE'];
    if (!is_dir($NTFCN_FILE_BASEPATH)) {
      mkdir($NTFCN_FILE_BASEPATH);
    }

    $AR_DIR = $NTFCN_FILE_BASEPATH."/".$NTFCN_ID;
    $dir = $AR_DIR;
    if (!is_dir($AR_DIR)) {
      mkdir($AR_DIR);
    }

    for ($i=0; $i < sizeof($_SESSION['ATTCHMNTS']); $i++) { 
      
      if ($_SESSION['ATTCHMNTS'][$i]=="REMOVED") {
        # ... do nothing
      }
      else
      {
        $HAS_ATTCHMT_FLG = "YY";

        $file = array();
        $file = $_SESSION['ATTCHMNTS'][$i];
        $file_size = $file["file_size"];
        $file_type = $file["file_type"];
        $file_ext = $file["file_ext"];
        $file_name = $file["file_name"];
        $file_temp = $file["file_temp"];

        # ... Upload Attachmentn Files
        $result = copy($file_temp, $dir."/".$file_name);

        if($result == 1){

          # ... Insert to DB
          $ATTCHMT_NAME = $file_name;
          $q5 = "INSERT INTO notification_attachments(NTFCN_ID, ATTCHMT_NAME) VALUES('$NTFCN_ID','$ATTCHMT_NAME')";
          $exec_response = array();
          $exec_response = ExecuteEntityInsert($q5);
          $RESP = $exec_response["RESP"]; 
          $RECORD_ID = $exec_response["RECORD_ID"];

          unlink($file_temp);
          $FILE_CNT++;
        }

      }
      
    }
  }


  # ... 05: Updating the Database
  $NTFCN_MSG_STATUS = "ACTIVE";
  $q6 = "UPDATE notifications SET HAS_ATTCHMT_FLG='$HAS_ATTCHMT_FLG', NTFCN_MSG_STATUS='$NTFCN_MSG_STATUS' WHERE NTFCN_ID='$NTFCN_ID'";
  $update_response6 = ExecuteEntityUpdate($q6);
  if ($update_response6=="EXECUTED") {
    
    # ... unset Session
    unset($_SESSION['ATTCHMNTS']);

    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: message has been sent with $FILE_CNT attachment(s)";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5;");
  }

}

?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="vendors/fastselect/select2.min.css" />
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Send Message", $APP_SMALL_LOGO); 

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
                <h2>Send Message</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         

                <div class="col-md-12 col-sm-12 col-xs-12">

                  <label>Attach Files:
                    <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#erty">Attach Files File</button>
                    <div id="erty" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">

                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel2">Add File Attachments</h4>
                          </div>
                          <div class="modal-body">
                              <form id="dyhsus" method="post" enctype="multipart/form-data">
                                
                                <label>Select File:</label><br>
                                <input type="file" id="attmt" name="attmt" class="form-control" required=""><br><br>
                                

                                <br>
                                <button type="submit" class="btn btn-primary btn-sm pull-right" name="btn_add_file">Add File</button>
                                <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal">Cancel</button>
                              </form>
                          </div>
                         

                        </div>
                      </div>
                    </div>
                  </label><br>
                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr valign="top">
                        <th colspan="3" bgcolor="#EEE">List of Attached Files</th>
                      </tr>
                      <tr valign="top">
                        <th>#</th>
                        <th>File Name</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (isset($_SESSION['ATTCHMNTS'])) {
                        $xx = 0;
                        for ($i=0; $i < sizeof($_SESSION['ATTCHMNTS']); $i++) { 
                          
                          if ($_SESSION['ATTCHMNTS'][$i]=="REMOVED") {
                            # ... do nothing
                          }
                          else
                          {
                            $file = array();
                            $file = $_SESSION['ATTCHMNTS'][$i];
                            $file_size = $file["file_size"];
                            $file_type = $file["file_type"];
                            $file_ext = $file["file_ext"];
                            $file_name = $file["file_name"];
                            $file_temp = $file["file_temp"];
                            ?>
                            <tr valign="top">
                              <td><?php echo ($xx+1); ?>. </td>
                              <td><?php echo $file_name; ?></td>
                              <td>
                                <form method="post" id="<?php echo "FORM_".($xx+1); ?>">
                                  <input type="hidden" id="f_index" name="f_index" value="<?php echo $i; ?>">
                                  <button type="submit" class="btn btn-danger btn-xs" name="btn_rem_file">Remove</button>
                                </form>
                              </td>
                            </tr>
                            <?php 
                            $xx++;
                          }
                          
                        }
                      }
                      ?>
                    </tbody>
                  </table>

                  <form method="post" id="not_form">
                    <label>Enter Recipients:</label><br>
                    <select class="form-control select2" name="RECIPIENTS[]" multiple="multiple" style="width: 100%;" required="">
                      <option value="" disabled="">Add Recipients</option>
                      <?php
                      $ADDRESS_STATUS = "";
                      $address_book = array();
                      $address_book = FetchAddressBook($ADDRESS_STATUS);
                      
                      for ($i=0; $i < sizeof($address_book); $i++) { 
                        $address = array();
                        $address = $address_book[$i];
                        $RECORD_ID = $address['RECORD_ID'];
                        $ADDRESS_ENTITY_TYPE = $address['ADDRESS_ENTITY_TYPE'];
                        $ADDRESS_ENTITY_ID = $address['ADDRESS_ENTITY_ID'];
                        $ADDRESS_ENTITY_NAME = $address['ADDRESS_ENTITY_NAME'];
                        $ADDRESS_ADDED_DATE = $address['ADDRESS_ADDED_DATE'];
                        $ADDRESS_STATUS = $address['ADDRESS_STATUS'];

                        $type = "";
                        if ($ADDRESS_ENTITY_TYPE=="USER") { $type = " [Admin Staff]"; }
                        if ($ADDRESS_ENTITY_TYPE=="GROUP") { $type = " [Group]"; }

                        $R_ID = $ADDRESS_ENTITY_ID."#".$ADDRESS_ENTITY_TYPE;
                        $R_NAME = $ADDRESS_ENTITY_NAME.$type;

                        
                        $id = "FTT".($i+1);

                        ?>
                        <option value="<?php echo $R_ID; ?>"><?php echo $R_NAME; ?></option>
                        <?php
                      }
                      ?>
                    </select><br><br>

                    <label>Subject:</label><br>
                    <input type="text" class="form-control" id="subject" name="subject" required=""><br>
                 
                    <label>Enter your message below:</label><br>
                    <textarea id="message" name="message" required="" class="form-control" rows="10"></textarea><br>
                    <button type="submit" class="btn btn-primary btn-sm pull-right" name="btn_send_message">Send Message</button>
                  </form>
                  
                        
                </div>


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
    <script src="vendors/fastselect/jquery-3.3.1.min.js"></script>
    <script src="vendors/fastselect/jquery-ui.min.js"></script>
    <script src="vendors/fastselect/select2.full.min.js"></script>
    <script>
        $('.select2').select2({
            data: [],
            tags: true,
            maximumSelectionLength: 10,
            tokenSeparators: [',', ' '],
            placeholder: "Select or type keywords"
        });
    </script>
    
  </body>
</html>
