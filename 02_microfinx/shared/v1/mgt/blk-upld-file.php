<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");


# ... UPLOAD BULK FILE  ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
if (isset($_POST["btn_upload_file"])) {
  $TXN_CRNCY =mysql_real_escape_string(trim($_POST["TXN_CRNCY"]));
  $DESC_REASON =mysql_real_escape_string(trim($_POST["DESC_REASON"]));

  // ... 01: Uploading the Payments File to remote directory ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..#
  $UPLD_FLG = "OFF";
  $BULK_FILES_BASE_PATH = GetSystemParameter("BULK_FILES_BASE_PATH")."/".$_SESSION['ORG_CODE'];
  $dir = $BULK_FILES_BASE_PATH;
  if (!is_dir($dir)) {
    mkdir($dir);
  }

  $file_size = $_FILES['BANK_RECEIPT_ATTCHMT']['size'];
  $file_type = $_FILES['BANK_RECEIPT_ATTCHMT']['type'];
  $ext = strtolower(substr(strrchr($_FILES['BANK_RECEIPT_ATTCHMT']['name'],"."),1));
  $file_name = "UPLOADFILE_".date('dFY', strtotime(GetCurrentDateTime()))."_".strtotime(GetCurrentDateTime()).".".$ext;

  if(is_uploaded_file($_FILES['BANK_RECEIPT_ATTCHMT']['tmp_name'])){
    if ($ext!='xlsx') {
      $alert_type = "ERROR";
      $alert_msg = "ERROR: Invalid file extension. Only <strong>.xls</strong> is acceptable for upload";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    } else if ($ext=="xlsx") {
      if($file_size >= 5000000){ // file size (5000KB)
        $alert_type = "ERROR";
        $alert_msg = "ERROR: Files exceeds 5MB. Upload file of a smaller size";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      }else{                                      
        if( ($_FILES['BANK_RECEIPT_ATTCHMT']['type']=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") ){ 
          $result = move_uploaded_file($_FILES['BANK_RECEIPT_ATTCHMT']['tmp_name'], $dir."/".$file_name);
          if($result == 1){
            $UPLD_FLG = "OKAY";
          } else{
            echo "ERROR";
          }
        }else{
          $alert_type = "ERROR";
          $alert_msg = "ERROR: Unacceptable file format. Acceptable formats include '.xls'";
          $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);    
        }
      }
    }
  }
  

  // ... 02: Reading from the excel file uploaded ... ... ... .. ... .. ..... ... ... ... ... ... ... ... ... ... ... ... ... ... ..#
  if ($UPLD_FLG!="OKAY") {
    $alert_type = "ERROR";
    $alert_msg = "ERROR: Unable to proceed. Upload was not successful.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if ($UPLD_FLG=="OKAY") {
    
    # ... Creating the Bulk Payment File
    $EXC_FILE_NAME = $file_name;
    $UPLOAD_REASON = $DESC_REASON;
    $UPLOADED_BY = $_SESSION['UPR_USER_ID']; 
    $UPLOADED_ON = GetCurrentDateTime();

    $q = "INSERT INTO blk_pymt_file(FILE_NAME,UPLOAD_REASON,UPLOADED_BY,UPLOADED_ON) VALUES('$EXC_FILE_NAME','$UPLOAD_REASON','$UPLOADED_BY','$UPLOADED_ON')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    # ... Process Entity System ID (Role ID)
    $id_prefix = "BPF";
    $id_len = 10;
    $id_record_id = $RECORD_ID;
    $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
    $FILE_ID = $ENTITY_ID;

    # ... Updating the role id
    $q2 = "UPDATE blk_pymt_file SET FILE_ID='$FILE_ID' WHERE RECORD_ID='$RECORD_ID'";
    $update_response = ExecuteEntityUpdate($q2);
    if ($update_response=="EXECUTED") {

      # ... Process Xcel File
      include('PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
      $EXCEL_FILE_NAME = $dir."/".$file_name;
      $inputFileName = $EXCEL_FILE_NAME;
      
      try {
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
      } catch (Exception $e) {
        die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . 
            $e->getMessage());
      }

      $UPL_CNT = 0;
      $sheet = $objPHPExcel->getSheet(0);
      $highestRow = $sheet->getHighestRow();
      $highestColumn = $sheet->getHighestColumn();
      for ($row = 1; $row <= $highestRow; $row++) { 
        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
        //Prints out data in each row.
        //Replace this with whatever you want to do with the data.
        /*echo '<pre>';
          print_r($rowData);
        echo '</pre>';*/

        $BK_CCID = mysql_real_escape_string(trim($rowData[0][0]));
        $BK_ACID = mysql_real_escape_string(trim($rowData[0][1]));
        $BK_ACCT_NO = mysql_real_escape_string(trim($rowData[0][2]));
        $BK_ACCT_NAME = mysql_real_escape_string(trim($rowData[0][3]));
        $BK_TRAN_TYPE = mysql_real_escape_string(trim($rowData[0][4]));
        $BK_TRAN_AMT = mysql_real_escape_string(trim($rowData[0][5]));
        $BK_TRAN_NARRATION = mysql_real_escape_string(trim($rowData[0][6]));

        # ... SKIPPING THE HEADER ROW
        if($BK_CCID=="CCID"){
          // ... DONT PROCESS
        } else if($BK_CCID!="CCID") {

          // ... START DB INSERTION
          $SAVINGS_CUST_ID = $BK_CCID;
          $SAVINGS_ACCT_ID = $BK_ACID;
          $SAVINGS_ACCT_NUM = $BK_ACCT_NO;
          $SAVINGS_ACCT_NAME = $BK_ACCT_NAME;
          $CURRENCY = $TXN_CRNCY;
          $TRAN_TYPE = $BK_TRAN_TYPE;
          $TRAN_AMT = $BK_TRAN_AMT; 
          $TRAN_NARRATION = $BK_TRAN_NARRATION;

          $q = "INSERT INTO blk_pymt_txns(FILE_ID,SAVINGS_CUST_ID,SAVINGS_ACCT_ID,CURRENCY,SAVINGS_ACCT_NUM,SAVINGS_ACCT_NAME,TRAN_TYPE,TRAN_AMT
          ,TRAN_NARRATION) VALUES('$FILE_ID','$SAVINGS_CUST_ID','$SAVINGS_ACCT_ID','$CURRENCY','$SAVINGS_ACCT_NUM','$SAVINGS_ACCT_NAME','$TRAN_TYPE','$TRAN_AMT','$TRAN_NARRATION')";
          $exec_response = array();
          $exec_response = ExecuteEntityInsert($q);
          $RESP = $exec_response["RESP"]; 
          $RECORD_ID = $exec_response["RECORD_ID"];

          # ... Process Entity System ID (Role ID)
          $id_prefix = "FT";
          $id_len = 15;
          $id_record_id = $RECORD_ID;
          $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
          $TRAN_ID = $ENTITY_ID;

          # ... Updating the role id
          $qx = "UPDATE blk_pymt_txns SET TRAN_ID='$TRAN_ID' WHERE RECORD_ID='$RECORD_ID'";
          $update_responsex = ExecuteEntityUpdate($qx);
          if ($update_responsex=="EXECUTED") {
            $UPL_CNT++;
          }
        }
      } # ... END..LOOP


      # ... Process System Response
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "BULK_TXN_UPLOAD";
      $ENTITY_ID_AFFECTED = $FILE_ID;
      $EVENT = "UPLOAD";
      $EVENT_OPERATION = "UPLOAD_BULK_PAYMENTS_FILE";
      $EVENT_RELATION = "blk_pymt_file";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = $UPL_CNT." entries uploaded.";
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

      $alert_type = "INFO";
      $alert_msg = "$UPL_CNT entries uploaded from the upload file. File awaiting verification";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    }  # ... END..IFF
  } # ...END.IFF
  

} 



?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Upload File", $APP_SMALL_LOGO); 

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
                <h2>Upload File</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">

                <form method="post" enctype="multipart/form-data">

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                      <label>Select Transacting Currency:</label>
                      <select id="TXN_CRNCY" name="TXN_CRNCY" class="form-control" required="">
                        <option value="">-----</option>
                        <option value="UGX">UGX</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                      <label>Describe Transaction Reason</label>
                      <textarea class="form-control" rows="3" name="DESC_REASON" id="DESC_REASON" required=""></textarea>
                    </div>
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                      <label>Select Upload Files:</label><small><em>(*Only .xlsx is accepted)</em></small>
                      <input type="file" id="BANK_RECEIPT_ATTCHMT" name="BANK_RECEIPT_ATTCHMT" class="form-control" required="">
                    </div>
                  </div>
                  

                  

                  
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <button type="submit" class="btn btn-primary" name="btn_upload_file">Upload File</button>
                    </div>
                  </div>
                </form>
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
