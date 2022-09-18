<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... to start from here In Sha Allah

# ... Receiving Data
$FILE_ID = mysql_real_escape_string($_GET['k']);
$file = array();
$file = FetchBulkFileById($FILE_ID);
$RECORD_ID = $file['RECORD_ID'];
$FILE_NAME = $file['FILE_NAME'];
$UPLOAD_REASON = $file['UPLOAD_REASON'];
$UPLOADED_BY = $file['UPLOADED_BY'];
$UPLOADED_ON = $file['UPLOADED_ON'];
$VERIFIED_RMKS = $file['VERIFIED_RMKS'];
$VERIFIED_BY = $file['VERIFIED_BY'];
$VERIFIED_ON = $file['VERIFIED_ON'];
$APPROVED_RMKS = $file['APPROVED_RMKS'];
$APPROVED_BY = $file['APPROVED_BY'];
$APPROVED_ON = $file['APPROVED_ON'];
$REVERSAL_FLG = $file['REVERSAL_FLG'];
$REV_INIT_RMKS = $file['REV_INIT_RMKS'];
$REV_INIT_BY = $file['REV_INIT_BY'];
$REV_INIT_ON = $file['REV_INIT_ON'];
$REV_APPROVED_RMKS = $file['REV_APPROVED_RMKS'];
$REV_APPROVED_BY = $file['REV_APPROVED_BY'];
$REV_APPROVED_ON = $file['REV_APPROVED_ON'];
$FILE_STATUS = $file['FILE_STATUS'];

# ... ... ... 01: Entry Counts ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID'";
$CNT_TOTAL_FILE = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE);
$Q_SUM_TOTAL_FILE = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID'";
$SUM_TOTAL_FILE = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE);

# ... ... ... 02: Entry Debit Counts ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_DEBITS = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D'";
$CNT_TOTAL_DEBITS = ReturnOneEntryFromDB($Q_CNT_TOTAL_DEBITS);
$Q_SUM_TOTAL_DEBITS = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D'";
$SUM_TOTAL_DEBITS = ReturnOneEntryFromDB($Q_SUM_TOTAL_DEBITS);

# ... ... ... 03: Entry Credit Counts ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_CREDITS = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C'";
$CNT_TOTAL_CREDITS = ReturnOneEntryFromDB($Q_CNT_TOTAL_CREDITS);
$Q_SUM_TOTAL_CREDITS = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C'";
$SUM_TOTAL_CREDITS = ReturnOneEntryFromDB($Q_SUM_TOTAL_CREDITS);

# ... ... ... 04: Entry Counts All Pass ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_PASS = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='PASS'";
$CNT_TOTAL_FILE_PASS = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_PASS);
$Q_SUM_TOTAL_FILE_PASS = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='PASS'";
$SUM_TOTAL_FILE_PASS = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_PASS);

# ... ... ... 05: Entry Counts All (Fail) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_FAIL = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='FAIL'";
$CNT_TOTAL_FILE_FAIL= ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_FAIL);
$Q_SUM_TOTAL_FILE_FAIL = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND PASS_FAIL_FLG='FAIL'";
$SUM_TOTAL_FILE_FAIL = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_FAIL);

# ... ... ... 06: Entry Debits Counts (Pass) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_PASS_DR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='PASS'";
$CNT_TOTAL_FILE_PASS_DR = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_PASS_DR);
$Q_SUM_TOTAL_FILE_PASS_DR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='PASS'";
$SUM_TOTAL_FILE_PASS_DR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_PASS_DR);

# ... ... ... 07: Entry Debits Counts (Fail) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_FAIL_DR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='FAIL'";
$CNT_TOTAL_FILE_FAIL_DR= ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_FAIL_DR);
$Q_SUM_TOTAL_FILE_FAIL_DR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' AND PASS_FAIL_FLG='FAIL'";
$SUM_TOTAL_FILE_FAIL_DR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_FAIL_DR);


# ... ... ... 08: Entry Credit Counts (Pass) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_PASS_CR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='PASS'";
$CNT_TOTAL_FILE_PASS_CR = ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_PASS_CR);
$Q_SUM_TOTAL_FILE_PASS_CR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='PASS'";
$SUM_TOTAL_FILE_PASS_CR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_PASS_CR);

# ... ... ... 09: Entry Credit Counts (Fail) ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$Q_CNT_TOTAL_FILE_FAIL_CR = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='FAIL'";
$CNT_TOTAL_FILE_FAIL_CR= ReturnOneEntryFromDB($Q_CNT_TOTAL_FILE_FAIL_CR);
$Q_SUM_TOTAL_FILE_FAIL_CR = "SELECT sum(TRAN_AMT) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' AND PASS_FAIL_FLG='FAIL'";
$SUM_TOTAL_FILE_FAIL_CR = ReturnOneEntryFromDB($Q_SUM_TOTAL_FILE_FAIL_CR);


# ... ... ... 10: Get Uploader Details ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$UPLOADED_BY_COREID = GetUserCoreIdFromWebApp($UPLOADED_BY);
$response_msg = FetchUserDetailsFromCore($UPLOADED_BY_COREID, $MIFOS_CONN_DETAILS);
$CORE_RESP = $response_msg["CORE_RESP"];
$UPLOADED_BY_NAME = $CORE_RESP["username"]." (".$CORE_RESP["firstname"]." ".$CORE_RESP["lastname"].")";

# ... ... ... 11: Porcessing Math ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ...  ... ... ... ...#
$CNT_DIFF = ($CNT_TOTAL_FILE - $CNT_TOTAL_FILE_PASS);
$VOL_DIFF = ($SUM_TOTAL_FILE - $SUM_TOTAL_FILE_PASS);
$VOL_MTCH = ($SUM_TOTAL_FILE_PASS_DR - $SUM_TOTAL_FILE_PASS_CR);



# ... F0000001: Validate File Entries .....................................................................................#
if (isset($_POST['btn_proc_txn_ent'])) {
  $FILE_ID = trim(mysql_real_escape_string($_POST['FILE_ID']));
  $txn_list_debits = FetchBulkTxnListDebits($FILE_ID);
  $txn_list_credits = array();
  $txn_list_credits = FetchBulkTxnListCredits($FILE_ID);
  $txn_list = array();
  $txn_list = array_merge($txn_list_debits, $txn_list_credits);

  for ($i=0; $i < sizeof($txn_list); $i++) { 
    //$excel_table_row = array();
    $txn = array();
    $txn = $txn_list[$i];
    $RECORD_ID = $txn['RECORD_ID'];
    $TRAN_ID = $txn['TRAN_ID'];
    $FILE_ID = $txn['FILE_ID'];
    $SAVINGS_CUST_ID = $txn['SAVINGS_CUST_ID'];
    $SAVINGS_ACCT_ID = $txn['SAVINGS_ACCT_ID'];
    $SAVINGS_ACCT_NUM_LIST = explode('-', $txn['SAVINGS_ACCT_NUM']);
    $SAVINGS_ACCT_NUM = trim($SAVINGS_ACCT_NUM_LIST[0]);
    $SAVINGS_ACCT_NAME = $txn['SAVINGS_ACCT_NAME'];
    $CURRENCY = $txn['CURRENCY'];
    $TRAN_TYPE = $txn['TRAN_TYPE'];
    $TRAN_AMT = $txn['TRAN_AMT'];
    $TRAN_NARRATION = $txn['TRAN_NARRATION'];
    $CORE_REF_ID = $txn['CORE_REF_ID'];
    $TRAN_STATUS = $txn['TRAN_STATUS'];

    # ... Validate Transaction Entry
    $val_resp = array();
    $val_resp = ValidateBulkFileTxnEntry($SAVINGS_CUST_ID, $SAVINGS_ACCT_ID, $SAVINGS_ACCT_NUM, $SAVINGS_ACCT_NAME, $TRAN_TYPE, $TRAN_AMT, $MIFOS_CONN_DETAILS);
    $CUST_FLG = $val_resp["CUST_FLG"];
    $ACCT_FLG = $val_resp["ACCT_FLG"];
    $BAL_FLG = $val_resp["BAL_FLG"];
    $VAL_MSG = $val_resp["VAL_MSG"];

    $PASS_FAIL_FLG = "";
    $PASS_FAIL_RMKS = "";
    if ( ($CUST_FLG=="OKAY")&&($ACCT_FLG=="OKAY")&&($BAL_FLG=="OKAY") ) {
      $PASS_FAIL_FLG = "PASS";
      $PASS_FAIL_RMKS = "Entry is valid";
    } else {
      $PASS_FAIL_FLG = "FAIL";
      $PASS_FAIL_RMKS = $VAL_MSG;
    }

    # ... Updating the database
    $q = "UPDATE blk_pymt_txns SET PASS_FAIL_FLG='$PASS_FAIL_FLG', PASS_FAIL_RMKS='$PASS_FAIL_RMKS' WHERE RECORD_ID='$RECORD_ID'";
    ExecuteEntityUpdate($q);

    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Entry validation complete.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

    header("Refresh:0;");


  } # ... END..LOOP
}

# ... F0000002: GENERATE TAN .....................................................................................#
if (isset($_POST['btn_gen_tan'])) {
  
  $ENTITY_TYPE = "STAFF_USER";
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $EVENT_TYPE = "VERIFY BULK PAYMENT FILE";
  $TAN = GeneratePassKey(8);
  $ENC_TAN = AES256::encrypt($TAN);
  $TAN_GEN_DATE = GetCurrentDateTime();

  # ... UPDATE UN-USED TANS
  $q = "UPDATE txn_tans SET TAN_STATUS='KILLED (UNUSED)' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {
    # ... SQL INSERT
    $q = "INSERT INTO txn_tans(ENTITY_TYPE,ENTITY_ID,EVENT_TYPE,TAN,TAN_GEN_DATE) VALUES('$ENTITY_TYPE','$ENTITY_ID','$EVENT_TYPE','$ENC_TAN','$TAN_GEN_DATE')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP=="EXECUTED") {
      
      $response_msg = FetchUserDetailsFromCore($_SESSION['UPR_USER_CORE_ID'], $MIFOS_CONN_DETAILS);
      $CONN_FLG = $response_msg["CONN_FLG"];
      $CORE_RESP = $response_msg["CORE_RESP"];
      $sys_usr = $response_msg["CORE_RESP"];
      $id = $sys_usr["id"];
      $CORE_username = $sys_usr["username"];
      $firstname = $sys_usr["firstname"];
      $lastname = $sys_usr["lastname"];
      $email = $sys_usr["email"];
      //echo "<pre>".print_r($CORE_RESP,true)."</pre>";
      $fff_name= $firstname." ".$lastname;
      $fff_email = $email;

      # ... DB INSERT
      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "BULK_PAYMENT_FILE_VERIF_TAN";
      $RECIPIENT_EMAILS = $fff_email;
      $EMAIL_MESSAGE = "Dear ".$fff_name."<br>"
                      ."This is your bulk payment file authentication TAN : <b>".$TAN."</b>";
      $EMAIL_ATTACHMENT_PATH = "";
      $RECORD_DATE = GetCurrentDateTime();
      $EMAIL_STATUS = "NN";

      $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
      ExecuteEntityInsert($q);

      # ... Send System Response
      $alert_type = "INFO";
      $alert_msg = "ALERT: TAN has been sent out to your registered email. TAN expires after 5 minutes";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    }
  }
}

# ... F0000003: Verify File .....................................................................................#
if (isset($_POST['btn_verify_file'])) {
  $FILE_ID = trim(mysql_real_escape_string($_POST['FILE_ID']));
  $VERIF_RMKS = mysql_real_escape_string(trim($_POST['VERIF_RMKS']));
  $TRAN_TAN = mysql_real_escape_string(trim($_POST['TRAN_TAN']));

  # ... 01: Validate Enterred TAN
  $ENTITY_ID = $_SESSION['UPR_USER_ID'];
  $val_results = array();
  $val_results = ValidateTranTAN($ENTITY_ID, $TRAN_TAN);
  $TAN_MSG_CODE = $val_results["TAN_MSG_CODE"];
  $TAN_MSG_MSG = $val_results["TAN_MSG_MSG"];

  if ($TAN_MSG_CODE=="FALSE") {
    # ... Send System Response
    $alert_type = "ERROR";
    $alert_msg = $TAN_MSG_MSG;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  } else if ($TAN_MSG_CODE=="TRUE") {

    # ... Update All File Entries
    $TRAN_STATUS = "VERIFIED";
    $q22 = "UPDATE blk_pymt_txns SET TRAN_STATUS='$TRAN_STATUS' WHERE FILE_ID='$FILE_ID'";
    $update_response22 = ExecuteEntityUpdate($q22);

    # ... Update File Record
    $VERIFIED_RMKS = $VERIF_RMKS;
    $VERIFIED_BY = $_SESSION['UPR_USER_ID'];
    $VERIFIED_ON = GetCurrentDateTime();
    $FILE_STATUS = "VERIFIED";
    $q33 = "UPDATE blk_pymt_file 
              SET VERIFIED_RMKS='$VERIFIED_RMKS'
                 ,VERIFIED_BY='$VERIFIED_BY' 
                 ,VERIFIED_ON='$VERIFIED_ON' 
                 ,FILE_STATUS='$FILE_STATUS'
           WHERE FILE_ID='$FILE_ID'";
    $update_response33 = ExecuteEntityUpdate($q33);

    if ( ($update_response22=="EXECUTED") && ($update_response33=="EXECUTED") ) {

      # ... MARK TAN AS USED ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $ENTITY_ID = $_SESSION['UPR_USER_ID'];
      $qww = "UPDATE txn_tans SET TAN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
      ExecuteEntityUpdate($qww);

      # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "BULK_PAYMENT_FILE";
      $ENTITY_ID_AFFECTED = $FILE_ID;
      $EVENT = "VERIF";
      $EVENT_OPERATION = "VERIFY_BULK_PAYMENT_FILE";
      $EVENT_RELATION = "blk_pymt_txns & blk_pymt_file";
      $EVENT_RELATION_NO = $FILE_ID;
      $OTHER_DETAILS = $FILE_ID."|".$VERIFIED_RMKS;
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

      $alert_type = "INFO";
      $alert_msg = "MESSAGE: Bulk payment file has been verified. It is pending approval. Refreshing in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      header("Refresh:5; URL=blk-vrff-file");
    }




  } # ... END..IFF
}

# ... F0000004: RE-UPLOAD BULK FILE  ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
if (isset($_POST["btn_reupload_file"])) {
  $FILE_ID =mysql_real_escape_string(trim($_POST["FILE_ID"]));
  $TXN_CRNCY =mysql_real_escape_string(trim($_POST["TXN_CRNCY"]));

  // ... 01: Uploading the Payments File to remote directory ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..#
  $UPLD_FLG = "OFF";
  $BULK_FILES_BASE_PATH = GetSystemParameter("BULK_FILES_BASE_PATH")."/".$_SESSION['ORG_CODE'];
  $dir = $BULK_FILES_BASE_PATH;

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
    
    # ... Update the Bulk Payment File Attributes
    $EXC_FILE_NAME = $file_name;
    $UPLOADED_BY = $_SESSION['UPR_USER_ID']; 
    $UPLOADED_ON = GetCurrentDateTime();

    mysql_query("DELETE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID'") or die("ERROR 1: ".mysql_error());
    $q = "UPDATE blk_pymt_file SET FILE_NAME='$EXC_FILE_NAME', UPLOADED_BY='$UPLOADED_BY',UPLOADED_ON='$UPLOADED_ON' WHERE FILE_ID='$FILE_ID'";

    $update_response = ExecuteEntityUpdate($q);
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
      header("Refresh:0;");

    }  # ... END..IFF
  } # ...END.IFF
} 

# ... F0000005: CANCEL BULK FILE  ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
if (isset($_POST['btn_cancel_file'])) {
  $FILE_ID = trim($_POST['FILE_ID']);
  $FILE_STATUS="CANCELLED";
  $TRAN_STATUS="CANCELLED";

  // ... SQL
  $q = "UPDATE blk_pymt_file SET FILE_STATUS='$FILE_STATUS' WHERE FILE_ID='$FILE_ID'";
  $q2 = "UPDATE blk_pymt_txns SET TRAN_STATUS='$TRAN_STATUS' WHERE FILE_ID='$FILE_ID'";
  $update_response = ExecuteEntityUpdate($q);
  $update_response2 = ExecuteEntityUpdate($q2);
  if (($update_response=="EXECUTED")&&($update_response2=="EXECUTED")) {
    # ... Log System Audit Log ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "BULK_PAYMENT_FILE";
    $ENTITY_ID_AFFECTED = $FILE_ID;
    $EVENT = "CANCEL";
    $EVENT_OPERATION = "CANCEL_BULK_PAYMENT_FILE";
    $EVENT_RELATION = "blk_pymt_txns & blk_pymt_file";
    $EVENT_RELATION_NO = $FILE_ID;
    $OTHER_DETAILS = $FILE_ID;
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    $alert_type = "INFO";
    $alert_msg = "MESSAGE: Bulk payment file has been cancelled. Refreshing in 5 seconds.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5; URL=blk-vrff-file");
  }
}






?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Verify Bulk File", $APP_SMALL_LOGO); 

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
                
                <table width="100%">
                  <tr>
                    <td><a href="blk-vrff-file" class="btn btn-sm btn-dark pull-left">Back</a> Verify Bulk File</td>
                    <?php
                    if (($VOL_DIFF==0)&&($CNT_DIFF==0)) {
                      if ($VOL_MTCH==0) {
                        ?>
                        <td width="50%">
                          <form method="post" id="dhs82bbwosdiowd">
                            <button type="submit" class="btn btn-warning btn-sm pull-right" name="btn_gen_tan">Generate Auth TAN</button>
                            <button type="button" class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#cancelup">Cancel Upload</button>
                            <div id="cancelup" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                              <div class="modal-dialog modal-sm">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel2">Cancel File Upload</h4>
                                  </div>
                                  <div class="modal-body">
                                      <form id="dddqwqqw2" method="post">
                                        
                                        <label>You have chosen to cancel the upload of this file. This action can not be undone if invoked.
                                               Click PROCEED to cancel file upload.</label><br><br>
                                        
                                        <input type="hidden" id="FILE_ID" name="FILE_ID" value="<?php echo $FILE_ID; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" name="btn_cancel_file">Proceed</button>
                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Dont Cancel</button>
                                      </form>
                                  </div>
                                 

                                </div>
                              </div>
                            </div>
                          </form>
                        </td>
                        <td width="10%">
                          <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#er3">Verify File</button>
                          <div id="er3" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Verify Bulk Payment File</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="dddqwqqw2" method="post">
                                      
                                      <label>Auth TAN:</label><br>
                                      <input type="text" id="TRAN_TAN" name="TRAN_TAN" class="form-control"><br>

                                      <label>Verification Rmks:</label><br>
                                      <textarea class="form-control" rows="3" id="VERIF_RMKS" name="VERIF_RMKS"></textarea><br><br>
                                      
                                      <input type="hidden" id="FILE_ID" name="FILE_ID" value="<?php echo $FILE_ID; ?>">
                                      <button type="submit" class="btn btn-primary btn-sm" name="btn_verify_file">Verify</button>
                                      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                               

                              </div>
                            </div>
                          </div>
                        </td>
                        <?php
                      } else {
                        ?>
                        <td width="50%">
                          <button type="button" class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#cancelup">Cancel Upload</button>
                          <div id="cancelup" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Cancel File Upload</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="dddqwqqw2" method="post">
                                      
                                      <label>You have chosen to cancel the upload of this file. This action can not be undone if invoked.
                                             Click PROCEED to cancel file upload.</label><br><br>
                                      
                                      <input type="hidden" id="FILE_ID" name="FILE_ID" value="<?php echo $FILE_ID; ?>">
                                      <button type="submit" class="btn btn-danger btn-sm" name="btn_cancel_file">Proceed</button>
                                      <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Dont Cancel</button>
                                    </form>
                                </div>
                               

                              </div>
                            </div>
                          </div>
                          <button type="button" class="btn btn-dark btn-sm pull-right" data-toggle="modal" data-target="#reuploadddd">Re-upload</button>
                          <div id="reuploadddd" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">

                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel2">Re-upload corrected file entries</h4>
                                </div>
                                <div class="modal-body">
                                    <form method="post" id="reuploadblk" enctype="multipart/form-data">

                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                          <label>Select Transacting Currency:</label>
                                          <select id="TXN_CRNCY" name="TXN_CRNCY" class="form-control" required="">
                                            <option value="">-----</option>
                                            <option value="UGX">UGX</option>
                                          </select>
                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                          <label>Select Upload Files:</label><small><em>(*Only .xlsx is accepted)</em></small>
                                          <input type="file" id="BANK_RECEIPT_ATTCHMT" name="BANK_RECEIPT_ATTCHMT" class="form-control" required="">
                                        </div>
                                    
                                      
                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                          <input type="hidden" id="FILE_ID" name="FILE_ID" value="<?php echo $FILE_ID; ?>">
                                          <button type="submit" class="btn btn-primary pull-right" name="btn_reupload_file">Upload File</button>
                                        </div>
                                      </div>

                                    </form>
                                </div>
                               

                              </div>
                            </div>
                          </div>
                          <button type="submit" class="btn btn-danger btn-sm pull-right" disabled="">Total Debit and Credit amounts are not matching</button>
                        </td>
                      <?php
                      }
                    } else {
                      ?>
                      <td width="50%">
                        <button type="button" class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#cancelup">Cancel Upload</button>
                        <div id="cancelup" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                          <div class="modal-dialog modal-sm">
                            <div class="modal-content">

                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel2">Cancel File Upload</h4>
                              </div>
                              <div class="modal-body">
                                  <form id="dddqwqqw2" method="post">
                                    
                                    <label>You have chosen to cancel the upload of this file. This action can not be undone if invoked.
                                           Click PROCEED to cancel file upload.</label><br><br>
                                    
                                    <input type="hidden" id="FILE_ID" name="FILE_ID" value="<?php echo $FILE_ID; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" name="btn_cancel_file">Proceed</button>
                                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Dont Cancel</button>
                                  </form>
                              </div>
                             

                            </div>
                          </div>
                        </div>
                        <button type="button" class="btn btn-dark btn-sm pull-right" data-toggle="modal" data-target="#reupload">Re-upload</button>
                        <div id="reupload" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                          <div class="modal-dialog modal-sm">
                            <div class="modal-content">

                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel2">Re-upload corrected file entries</h4>
                              </div>
                              <div class="modal-body">
                                  <form method="post" id="reuploadblk" enctype="multipart/form-data">

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <label>Select Transacting Currency:</label>
                                        <select id="TXN_CRNCY" name="TXN_CRNCY" class="form-control" required="">
                                          <option value="">-----</option>
                                          <option value="UGX">UGX</option>
                                        </select>
                                      </div>

                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <label>Select Upload Files:</label><small><em>(*Only .xlsx is accepted)</em></small>
                                        <input type="file" id="BANK_RECEIPT_ATTCHMT" name="BANK_RECEIPT_ATTCHMT" class="form-control" required="">
                                      </div>
                                  
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                      <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <input type="hidden" id="FILE_ID" name="FILE_ID" value="<?php echo $FILE_ID; ?>">
                                        <button type="submit" class="btn btn-primary pull-right" name="btn_reupload_file">Upload File</button>
                                      </div>
                                    </div>

                                  </form>
                              </div>
                             

                            </div>
                          </div>
                        </div>
                        <button type="submit" class="btn btn-info btn-sm pull-right" disabled="">To verify file, all entries must pass verification</button>
                      </td>
                      <?php
                    }
                    ?>
                    
                  </tr>
                </table>        

                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">  

                <table class="table table-striped table-bordered" style="font-size: 12px;">
                  <thead>
                    <tr valign="top" bgcolor="#EEE">
                      <th>File Id</th>
                      <th>File Name</th>
                      <th>Description</th>
                      <th>Upload Date</th>
                      <th>Uploaded By</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                      <td><?php echo $FILE_ID; ?></td>
                      <td><?php echo $FILE_NAME; ?></td>
                      <td><?php echo $UPLOAD_REASON; ?></td>
                      <td><?php echo $UPLOADED_ON; ?></td>
                      <td><?php echo $UPLOADED_BY_NAME; ?></td>
                    </tr>
                  </tbody>
                </table>

                <table class="table table-striped table-bordered" style="font-size: 12px;">
                  <thead>
                    <tr valign="top" bgcolor="#EEE">
                      <th width="15%">CATEGORY</th>
                      <th width="10%">COUNT</th>
                      <th width="20%">VOLUME (UGX)</th>
                      <th>PASSED ENTRIES</th>
                      <th>FAILED ENTRIES</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr valign="top">
                        <th>Debit Entries</th>
                        <td><?php echo number_format($CNT_TOTAL_DEBITS); ?></td>
                        <td><?php echo number_format($SUM_TOTAL_DEBITS); ?></td>
                        <td><?php echo ($CNT_TOTAL_FILE_PASS_DR)." (".($SUM_TOTAL_FILE_PASS_DR).")"; ?></td>
                        <td><?php echo ($CNT_TOTAL_FILE_FAIL_DR)." (".($SUM_TOTAL_FILE_FAIL_DR).")"; ?></td>
                    </tr>
                    <tr valign="top">
                        <th>Credit Entries</th>
                        <td><?php echo number_format($CNT_TOTAL_CREDITS); ?></td>
                        <td><?php echo number_format($SUM_TOTAL_CREDITS); ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_PASS_CR." (".$SUM_TOTAL_FILE_PASS_CR.")"; ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_FAIL_CR." (".$SUM_TOTAL_FILE_FAIL_CR.")"; ?></td>
                    </tr>
                    <tr valign="top">
                        <th>Totals</th>
                        <td><?php echo number_format($CNT_TOTAL_FILE); ?></td>
                        <td><?php echo number_format($SUM_TOTAL_FILE); ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_PASS." (".$SUM_TOTAL_FILE_PASS.")"; ?></td>
                        <td><?php echo $CNT_TOTAL_FILE_FAIL." (".$SUM_TOTAL_FILE_FAIL.")"; ?></td>
                    </tr>
                  </tbody>
                </table>

                <div style="overflow-y: auto; height: 490px;">
                  <table id="datatable3" class="table table-striped table-bordered" style="font-size: 11px;">
                    <thead>
                      <tr valign="top">
                        <th colspan="8" bgcolor="#EEE">
                          
                          <table width="100%">
                            <tr>
                              <td><span>List of File Transaction Entries</span></td>
                              <td>
                                <form method="post" id="dgdgd28sjhs">
                                    <input type="hidden" id="FILE_ID" name="FILE_ID" value="<?php echo $FILE_ID; ?>">
                                  <button type="submit" class="btn btn-warning btn-xs pull-right" name="btn_proc_txn_ent">Validate Entries</button>
                                </form>
                              </td>
                              <td width="10%"><a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download</a></td>
                            </tr>
                          </table>                                          
                        </th>

                      </tr>
                      <tr valign="top">
                        <th>#</th>
                        <th>Acct No</th>
                        <th>Acct Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Nrrtn</th>
                        <th>Pass/Fail</th>
                        <th>Pass/Fail Rmks</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $excel_table_list = array();
                      $txn_list_debits = array();
                      $txn_list_debits = FetchBulkTxnListDebits($FILE_ID);
                      $txn_list_credits = array();
                      $txn_list_credits = FetchBulkTxnListCredits($FILE_ID);
                      $txn_list = array();
                      $txn_list = array_merge($txn_list_debits, $txn_list_credits);

                      for ($i=0; $i < sizeof($txn_list); $i++) { 
                        //$excel_table_row = array();
                        $txn = array();
                        $txn = $txn_list[$i];
                        $RECORD_ID = $txn['RECORD_ID'];
                        $TRAN_ID = $txn['TRAN_ID'];
                        $FILE_ID = $txn['FILE_ID'];
                        $SAVINGS_CUST_ID = $txn['SAVINGS_CUST_ID'];
                        $SAVINGS_ACCT_ID = $txn['SAVINGS_ACCT_ID'];
                        $SAVINGS_ACCT_NUM = $txn['SAVINGS_ACCT_NUM'];
                        $SAVINGS_ACCT_NAME = $txn['SAVINGS_ACCT_NAME'];
                        $CURRENCY = $txn['CURRENCY'];
                        $TRAN_TYPE = $txn['TRAN_TYPE'];
                        $TRAN_AMT = $txn['TRAN_AMT'];
                        $TRAN_NARRATION = $txn['TRAN_NARRATION'];
                        $PASS_FAIL_FLG = $txn['PASS_FAIL_FLG'];
                        $PASS_FAIL_RMKS = $txn['PASS_FAIL_RMKS'];
                        $CORE_REF_ID = $txn['CORE_REF_ID'];
                        $TRAN_STATUS = $txn['TRAN_STATUS'];

                        # ... Building the excel table row
                        //$_SESSION["EXCEL_HEADER"] = array("CCID","ACID","ACCT_NO","ACCT_NAME","TRAN_TYPE","TRAN_AMT","TRAN_NARRATION","RESULT","REMARKS");
                        $excel_table_row[0] = $SAVINGS_CUST_ID;
                        $excel_table_row[1] = $SAVINGS_ACCT_ID;
                        $excel_table_row[2] = $SAVINGS_ACCT_NUM;
                        $excel_table_row[3] = $SAVINGS_ACCT_NAME;
                        $excel_table_row[4] = $TRAN_TYPE;
                        $excel_table_row[5] = $TRAN_AMT;
                        $excel_table_row[6] = $TRAN_NARRATION;
                        $excel_table_row[7] = $PASS_FAIL_FLG;
                        $excel_table_row[8] = $PASS_FAIL_RMKS;
                        $excel_table_list[$i] = $excel_table_row;

                        ?>
                         <tr valign="top">
                          <td><?php echo ($i+1); ?>. </td>
                          <td><?php echo $SAVINGS_ACCT_NUM; ?></td>
                          <td><?php echo $SAVINGS_ACCT_NAME; ?></td>
                          <td><?php echo $TRAN_TYPE; ?></td>
                          <td><?php echo number_format($TRAN_AMT); ?></td>
                          <td><?php echo $TRAN_NARRATION; ?></td>
                          <td><?php echo $PASS_FAIL_FLG; ?></td>
                          <td><?php echo $PASS_FAIL_RMKS; ?></td>
                        </tr>
                        <?php
                      } # .. END..LOOP

                      # ... Excel Data Preparation
                      $_SESSION["EXCEL_HEADER"] = array("CCID","ACID","ACCT_NO","ACCT_NAME","TRAN_TYPE","TRAN_AMT","TRAN_NARRATION","RESULT","REMARKS");
                      $_SESSION["EXCEL_DATA"] = $excel_table_list;
                      $_SESSION["EXCEL_FILE"] = $FILE_ID."_".date('dFY', strtotime(GetCurrentDateTime())).".xlsx";


                      ?>
                    </tbody>
                  </table>
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
  
  </body>
</html>
