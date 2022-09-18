<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Handle Form Data
if (isset($_POST['btn_submit_appln'])) {

  # ... VARIABLES ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
  $CC_SVGS_ACCT_ID_TO_CREDIT = mysql_real_escape_string(trim($_POST['SVGS_ACCT_ID_TO_CREDIT']));
  $SVNGS_ACCT_BAL = mysql_real_escape_string(trim($_POST['SVNGS_ACCT_BAL']));
  $DEPOSIT_AMT = mysql_real_escape_string(trim($_POST['DEPOSIT_AMT']));
  $CC_BANK_ID = mysql_real_escape_string(trim($_POST['BANK_ID']));
  $CC_BANK_INST_ACCT_NO = mysql_real_escape_string(trim($_POST['BANK_INST_ACCT_NO']));
  $CC_BANK_INST_ACCT_NAME = mysql_real_escape_string(trim($_POST['BANK_INST_ACCT_NAME']));
  $CC_BANK_RECEIPT_REF = mysql_real_escape_string(trim($_POST['BANK_RECEIPT_REF']));
  $DEP_NRRTN = mysql_real_escape_string(trim($_POST['DEP_NRRTN']));

  $CUST_ID = $_SESSION['CST_USR_ID'];
  $SVGS_ACCT_ID_TO_CREDIT = $CC_SVGS_ACCT_ID_TO_CREDIT;
  $AMOUNT_BANKED = $DEPOSIT_AMT;
  $REASON = $DEP_NRRTN;
  $BANK_ID = $CC_BANK_ID;
  $BANK_INST_ACCT_NO = $CC_BANK_INST_ACCT_NO;
  $BANK_INST_ACCT_NAME = $CC_BANK_INST_ACCT_NAME;
  $BANK_RECEIPT_REF = $CC_BANK_RECEIPT_REF;
  $RQST_DATE = GetCurrentDateTime();

  # ... SQL INSERT ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
  $q = "INSERT INTO svgs_deposit_requests(CUST_ID,SVGS_ACCT_ID_TO_CREDIT,AMOUNT_BANKED,REASON,BANK_ID,BANK_INST_ACCT_NO,BANK_INST_ACCT_NAME
	                                       ,BANK_RECEIPT_REF,RQST_DATE) 
	      VALUES('$CUST_ID','$SVGS_ACCT_ID_TO_CREDIT','$AMOUNT_BANKED','$REASON','$BANK_ID','$BANK_INST_ACCT_NO','$BANK_INST_ACCT_NAME','$BANK_RECEIPT_REF','$RQST_DATE')";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"];
  $RECORD_ID = $exec_response["RECORD_ID"];

  # ... PROCESS DEPOSIT (DEPOSIT_REF) ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... #
  $id_prefix = "SDA";
  $id_len = 20;
  $id_record_id = $RECORD_ID;
  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
  $DEPOSIT_REF = $ENTITY_ID;

  $qeeee2 = "UPDATE svgs_deposit_requests SET DEPOSIT_REF='$DEPOSIT_REF' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($qeeee2);

  # ... UPLOADING FILE RECEIPT ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
  if (isset($_FILES['BANK_RECEIPT_ATTCHMT']['size'])) {

    // ... SAVINGS DEPOSIT APPLN
    $SVNGS_DEPOSIT_APPLN_FILES_BASEPATH = GetSystemParameter("SVNGS_DEPOSIT_APPLN_FILES_BASEPATH") . "/" . $_SESSION['ORG_CODE'];
    if (!is_dir($SVNGS_DEPOSIT_APPLN_FILES_BASEPATH)) {
      mkdir($SVNGS_DEPOSIT_APPLN_FILES_BASEPATH);
    }
    $SVNGS_DIR = $SVNGS_DEPOSIT_APPLN_FILES_BASEPATH . "/" . $DEPOSIT_REF;
    $dir = $SVNGS_DIR;
    if (!is_dir($SVNGS_DIR)) {
      mkdir($SVNGS_DIR);
    }

    $file_size = $_FILES['BANK_RECEIPT_ATTCHMT']['size'];
    $ext = strtolower(substr(strrchr($_FILES['BANK_RECEIPT_ATTCHMT']['name'], "."), 1));
    $file_name = $DEPOSIT_REF . "." . $ext;

    if (is_uploaded_file($_FILES['BANK_RECEIPT_ATTCHMT']['tmp_name'])) {
      if ($file_size >= 700000) { // file size (700KB)
        $alert_type = "ERROR";
        $alert_msg = "ERROR: Files exceeds 700KB. Upload file of a smaller size";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      } else {
        if (($_FILES['BANK_RECEIPT_ATTCHMT']['type'] == "image/gif") // gif
          || ($_FILES['BANK_RECEIPT_ATTCHMT']['type'] == "image/jpeg") // jpeg
          || ($_FILES['BANK_RECEIPT_ATTCHMT']['type'] == "image/png") // png
          || ($_FILES['BANK_RECEIPT_ATTCHMT']['type'] == "application/pdf") // pdf
        ) {
          $result = move_uploaded_file($_FILES['BANK_RECEIPT_ATTCHMT']['tmp_name'], $dir . "/" . $file_name);
          if ($result == 1) {

            $qwwwq = "UPDATE svgs_deposit_requests SET BANK_RECEIPT_ATTCHMT='$file_name' WHERE DEPOSIT_REF='$DEPOSIT_REF'";
            ExecuteEntityInsert($qwwwq);
          }
        } else {
          $alert_type = "ERROR";
          $alert_msg = "ERROR: Unacceptable file format. Acceptable formats include '.png', '.jpg', '.gif' and .'pdf'";
          $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
        }
      }
    }
  }  # ... END..IFF


  # ... GETTING CUSTOMER DETAILS ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ......  ...  ...  ... #
  $cstmr = array();
  $cstmr = FetchCustomerLoginDataByCustId($_SESSION['CST_USR_ID']);
  $CUST_ID = $cstmr['CUST_ID'];
  $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
  $CUST_EMAIL = $cstmr['CUST_EMAIL'];
  $CUST_PHONE = $cstmr['CUST_PHONE'];

  # ... Get Customer Name From Core
  $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $displayName = strtoupper($CORE_RESP["displayName"]);

  # ... Decrypt Email & Phone
  $FP_EMAIL = AES256::decrypt($CUST_EMAIL);
  $FP_PHONE = AES256::decrypt($CUST_PHONE);
  $FP_NAME = $displayName;


  # ... GET SAVINGS ACCT ID ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
  $response_msg = FetchSavingsAcctById($CC_SVGS_ACCT_ID_TO_CREDIT, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $SVGS_ACCT_NUM_TO_CREDIT = $CORE_RESP["accountNo"];

  # ... BANK NAME
  $fin = array();
  $fin = FetchFinInstitutionsById($BANK_ID);
  $FIN_INST_NAME = $fin['FIN_INST_NAME'];


  # ... SENDING EMAIL TO CUSTOMER ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
  $INIT_CHANNEL = "WEB";
  $MSG_TYPE = "SAVINGS APPLICATION DEPOSIT";
  $RECIPIENT_EMAILS = $FP_EMAIL;
  $EMAIL_MESSAGE = "Dear " . $FP_NAME . "<br>"
    . "Your savings deposit application has been received. Below are the details;<br>"
    . "-------------------------------------------------------------------------------------------------<br>"
    . "<b>APPLN SAVINGS REF:</b> <i>" . $DEPOSIT_REF . "</i><br>"
    . "<b>CR SAVINGS ACCOUNT:</b> <i>" . $SVGS_ACCT_NUM_TO_CREDIT . "</i><br>"
    . "<b>AMOUNT DEPOSITED:</b> <i>" . number_format($AMOUNT_BANKED) . "</i><br>"
    . "<b>DEPOSIT PURPOSE<i>" . $REASON . "</i><br>"
    . "<b>DEPOSIT LOCATION</b><i>" . $FIN_INST_NAME . "</i><br>"
    . "<b>DEPOSIT ACCT NAME</b><i>" . $CC_BANK_INST_ACCT_NAME . "</i><br>"
    . "<b>DEPOSIT ACCT NO</b><i>" . $CC_BANK_INST_ACCT_NO . "</i><br>"
    . "-------------------------------------------------------------------------------------------------<br>"
    . "<br/>"
    . "Upon procesing this request, we shall inform you of the progress.<br>"
    . "Regards<br>"
    . "Management<br>"
    . "<i></i>";
  $EMAIL_ATTACHMENT_PATH = "";
  $RECORD_DATE = GetCurrentDateTime();
  $EMAIL_STATUS = "NN";

  $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
  ExecuteEntityInsert($qqq);


  # ... LOGGING AND SENDING RESPONSE TO CUSTOMER ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "SAVINGS_DEPOSIT_APPLN";
  $ENTITY_ID_AFFECTED = $_SESSION['CST_USR_ID'];
  $EVENT = "SUBMIT";
  $EVENT_OPERATION = "SUBMIT_SAVINGS_DEPOSIT_APPLN";
  $EVENT_RELATION = "svgs_deposit_requests";
  $EVENT_RELATION_NO = $RECORD_ID;
  $OTHER_DETAILS = $DEPOSIT_REF;
  $INVOKER_ID = $_SESSION['CST_USR_ID'];
  LogSystemEvent(
    $AUDIT_DATE,
    $ENTITY_TYPE,
    $ENTITY_ID_AFFECTED,
    $EVENT,
    $EVENT_OPERATION,
    $EVENT_RELATION,
    $EVENT_RELATION_NO,
    $OTHER_DETAILS,
    $INVOKER_ID
  );



  # ... ASSEMBLING  DEPOSIT DATA ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
  $_SESSION['DEPOSIT_REF'] = $DEPOSIT_REF;
  $_SESSION['SVGS_ACCT_ID_TO_CREDIT'] = $SVGS_ACCT_ID_TO_CREDIT;
  $_SESSION['SVGS_ACCT_NUM_TO_CREDIT'] = $SVGS_ACCT_NUM_TO_CREDIT;
  $_SESSION['AMOUNT_BANKED'] = $AMOUNT_BANKED;
  $_SESSION['REASON'] = $REASON;
  $_SESSION['BANK_ID'] = $BANK_ID;
  $_SESSION['FIN_INST_NAME'] = $FIN_INST_NAME;
  $_SESSION['BANK_INST_ACCT_NO'] = $BANK_INST_ACCT_NO;
  $_SESSION['BANK_INST_ACCT_NAME'] = $BANK_INST_ACCT_NAME;
  $_SESSION['BANK_RECEIPT_REF'] = $BANK_RECEIPT_REF;
  $_SESSION['RQST_DATE'] = $RQST_DATE;

  $next_page = "svg-appln-deposit2";
  NavigateToNextPage($next_page);
}

?>

<!DOCTYPE html>
<html>

<head>
  <?php
  # ... Device Settings and Global CSS
  LoadDeviceSettings();
  LoadDefaultCSSConfigurations("Savings Deposit", $APP_SMALL_LOGO);

  # ... Javascript
  LoadPriorityJS();
  OnLoadExecutions();
  StartTimeoutCountdown();
  ExecuteProcessStatistics();
  ?>

  <script type="text/javascript">
    // ... 01: Get Savings Acct Details
    // ... 01: Get Savings Acct Details
    function FetchSavingsAcctDetails() {

      $('#SVNGS_ACCT_BAL').val("");
      $('#SVNGS_ACCT_BAL_DISP').val("");
      var selected_val = document.getElementById('SVGS_ACCT_ID_TO_CREDIT').value;

      //alert(selected_val);

      // ... Ajax
      $.ajax({
        type: 'post',
        url: 'ajax-fetch-savings_acct_details.php',
        data: {
          svngs_id: selected_val
        },
        success: function(response) {
          console.log(response);

          // ... Handling of Db responses
          response = JSON.parse(response)
          var SVNGS_ACCT_BAL = response.SVNGS_ACCT_BAL;
          var SVNGS_ACCT_BAL_JS = response.SVNGS_ACCT_BAL;
          var num = parseFloat(SVNGS_ACCT_BAL).toLocaleString('en');
          //console.log(num);
          //console.log(response);
          $('#SVNGS_ACCT_BAL').val(SVNGS_ACCT_BAL);
          $('#SVNGS_ACCT_BAL_DISP').val(num);
        }
      });
    }


    // ... 02: Get Deposit Bank Details
    // ... 02: Get Deposit Bank Details
    function FetchProcBankDetails() {

      $('#BANK_INST_ACCT_NO').val("");
      $('#BANK_INST_ACCT_NAME').val("");
      $('#disp_BANK_INST_ACCT_NO').val("");
      $('#disp_BANK_INST_ACCT_NAME').val("");
      var selected_val = document.getElementById('BANK_ID').value;

      //alert(selected_val);

      // ... Ajax
      $.ajax({
        type: 'post',
        url: 'ajax-fetch-proc-bank-details.php',
        data: {
          fin_inst_id: selected_val
        },
        success: function(response) {
          console.log(response);

          // ... Handling of Db responses
          response = JSON.parse(response)
          var ORG_ACCT_NUM = response.ORG_ACCT_NUM;
          var FIN_INST_NAME = response.FIN_INST_NAME;
          //console.log(num);
          //console.log(response);
          $('#BANK_INST_ACCT_NO').val(ORG_ACCT_NUM);
          $('#BANK_INST_ACCT_NAME').val(FIN_INST_NAME);
          $('#disp_BANK_INST_ACCT_NO').val(ORG_ACCT_NUM);
          $('#disp_BANK_INST_ACCT_NAME').val(FIN_INST_NAME);
        }
      });
    }
  </script>

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
          <div align="center" style="width: 100%;"><?php if (isset($_SESSION['ALERT_MSG'])) {
                                                      echo $_SESSION['ALERT_MSG'];
                                                    } ?></div>


          <div class="x_panel">
            <div class="x_title">
              <h2>Savings Deposit Applns</h2>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">

              <form method="post" id="ssdaaewq" enctype="multipart/form-data">

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Savings Account to Credit:</label>
                  <select id="SVGS_ACCT_ID_TO_CREDIT" name="SVGS_ACCT_ID_TO_CREDIT" class="form-control" required="" onchange="FetchSavingsAcctDetails()">
                    <option value="">--- Select Account ----</option>
                    <option value=""></option>
                    <option value="">------- Individual Accounts -------</option>
                    <?php
                    // ... individual
                    $response_msg = GetCustSavingsAccounts($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                    $CONN_FLG = $response_msg["CONN_FLG"];
                    $CORE_RESP = $response_msg["CORE_RESP"];
                    $ACCTS_DATA = array();
                    $ACCTS_DATA = $CORE_RESP["data"];

                    // ... group
                    $response_msg2 = GetCustSavingsAccountsGroup($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
                    $CONN_FLG2 = $response_msg2["CONN_FLG"];
                    $CORE_RESP2 = $response_msg2["CORE_RESP"];
                    $ACCTS_DATA2 = array();
                    $ACCTS_DATA2 = $CORE_RESP2["data"];

                    // ... populate drop down
                    for ($i = 0; $i < sizeof($ACCTS_DATA); $i++) {

                      $row = $ACCTS_DATA[$i]["row"];
                      $svgs_id = $row[0];
                      $svgs_account_no = $row[1];
                      $product = $row[5];
                    ?>
                      <option value="<?php echo $svgs_id; ?>"><?php echo $svgs_account_no . " - " . $product; ?></option>
                    <?php
                    }
                    ?>
                    <option value=""></option>
                    <option value="">------- Group Accounts -------</option>
                    <?php
                    for ($v = 0; $v < sizeof($ACCTS_DATA2); $v++) {

                      $row = $ACCTS_DATA2[$v]["row"];
                      $svgs_id = $row[0];
                      $svgs_account_no = $row[1];
                      $product = $row[5];
                    ?>
                      <option value="<?php echo $svgs_id; ?>"><?php echo $svgs_account_no . " - " . $product; ?></option>
                    <?php
                    }

                    ?>
                  </select>
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Savings Account Balance:</label>
                  <input type="hidden" id="SVNGS_ACCT_BAL" name="SVNGS_ACCT_BAL">
                  <input type="text" id="SVNGS_ACCT_BAL_DISP" name="SVNGS_ACCT_BAL_DISP" class="form-control" disabled="">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Amount to be deposited:</label>
                  <input type="number" id="DEPOSIT_AMT" name="DEPOSIT_AMT" class="form-control" required="">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Funds were deposited from:</label>
                  <select id="BANK_ID" name="BANK_ID" class="form-control" required="" onchange="FetchProcBankDetails()">
                    <option value="">-------</option>
                    <?php
                    $fin_list = array();
                    $fin_list = FetchTranProcBanks();

                    for ($i = 0; $i < sizeof($fin_list); $i++) {

                      $fin = array();
                      $fin = $fin_list[$i];
                      $FIN_INST_ID = $fin['FIN_INST_ID'];
                      $FIN_INST_NAME = $fin['FIN_INST_NAME'];

                    ?>
                      <option value="<?php echo $FIN_INST_ID; ?>"><?php echo $FIN_INST_NAME; ?></option>
                    <?php
                    }

                    ?>
                  </select>
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Organization Account No:</label>
                  <input type="hidden" id="BANK_INST_ACCT_NO" name="BANK_INST_ACCT_NO">
                  <input type="text" id="disp_BANK_INST_ACCT_NO" name="disp_BANK_INST_ACCT_NO" class="form-control" required="" disabled="">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Organization Account Name:</label>
                  <input type="hidden" id="BANK_INST_ACCT_NAME" name="BANK_INST_ACCT_NAME">
                  <input type="text" id="disp_BANK_INST_ACCT_NAME" name="disp_BANK_INST_ACCT_NAME" class="form-control" required="" disabled="">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Deposit Receipt Attachment:</label>
                  <!--input type="file" id="BANK_RECEIPT_ATTCHMT" name="BANK_RECEIPT_ATTCHMT" class="form-control" required=""-->
                  <input type="file" id="BANK_RECEIPT_ATTCHMT" name="BANK_RECEIPT_ATTCHMT" class="form-control">
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                  <label>Receipt Reference Number:</label>
                  <input type="text" id="BANK_RECEIPT_REF" name="BANK_RECEIPT_REF" class="form-control">
                </div>


                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <label>Deposit Narration</label>
                  <textarea class="form-control" rows="3" name="DEP_NRRTN" id="DEP_NRRTN" required=""></textarea>
                </div>


                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                  <?php
                  # ... Checking for Pending Applications
                  $CCCC_ID = $_SESSION['CST_USR_ID'];;
                  $Q = "SELECT count(*) as RTN_VALUE FROM svgs_deposit_requests WHERE CUST_ID='$CCCC_ID' AND RQST_STATUS='PENDING'";
                  $Q_CNT = ReturnOneEntryFromDB($Q);

                  if ($Q_CNT > 0) {
                  ?>
                    <button type="submit" class="btn btn-warning" disabled="">You have a pending savings deposit application. Request Management to action the application</button>
                  <?php
                  } else {
                  ?>
                    <button type="submit" class="btn btn-success" name="btn_submit_appln">Submit Appln Details</button>
                  <?php
                  }
                  ?>
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