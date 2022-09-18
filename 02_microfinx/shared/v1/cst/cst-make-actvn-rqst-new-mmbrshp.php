<?php
session_start();
include("conf/no-session.php");
$_SESSION["ALERT_MSG"] = "";

if (isset($_POST['btn_submit_request'])) {
  $digit = trim($_SESSION['digit']);
  $captcha_code_input = trim($_POST['captcha_code_input']);

  # ... 01: BioDate
  $fn = mysql_real_escape_string(trim($_POST['fn']));
  $mn = mysql_real_escape_string(trim($_POST['mn']));
  $ln = mysql_real_escape_string(trim($_POST['ln']));
  $gn = mysql_real_escape_string(trim($_POST['gender']));
  $dob_dd = mysql_real_escape_string(trim($_POST['dob_dd']));
  $dob_mm = mysql_real_escape_string(trim($_POST['dob_mm']));
  $dob_yy = mysql_real_escape_string(trim($_POST['dob_yy']));
  $dob_conc = $dob_yy . "-" . $dob_mm . "-" . $dob_dd;

  # ... 02: Contact_Details
  $email1 = mysql_real_escape_string(trim($_POST['email1']));
  $phone1 = mysql_real_escape_string(trim($_POST['phone1']));
  $phy_address = mysql_real_escape_string(trim($_POST['phy_address']));

  # ... 03: Files Variables
  $personal_id_doc_no = mysql_real_escape_string(trim($_POST['personal_id_doc_no']));
  $nat_id_nin = mysql_real_escape_string(trim($_POST['nat_id_nin']));

  # ... 04: Addding Data to DB
  $MMBSHP_TYPE = "NEW";
  $CHANNEL_ID = "WEB";
  $FIRST_NAME = $fn;
  $MIDDLE_NAME = $mn;
  $LAST_NAME = $ln;
  $GENDER = $gn;
  $DOB = $dob_conc;
  $EMAIL = $email1;
  $MOBILE_NO = $phone1;
  $PHYSICAL_ADDRESS = $phy_address;
  $WORK_ID = $personal_id_doc_no;
  $NATIONAL_ID = $nat_id_nin;
  $REQST_RECORD_DATE = GetCurrentDateTime();

  # ... Verify if their is pending request on the emails or phone
  $data_check_results = array();
  $data_check_results = PerformDataChecksOnRequest($EMAIL, $MOBILE_NO, $WORK_ID, $NATIONAL_ID);
  $EMAIL_CHK = $data_check_results["EMAIL_CHK"];
  $MOBILENO_CHK = $data_check_results["MOBILENO_CHK"];
  $WORKID_CHK = $data_check_results["WORKID_CHK"];
  $NATIONALID_CHK = $data_check_results["NATIONALID_CHK"];
  $RESULT_RMKS = $data_check_results["RESULT_RMKS"];

  if ($EMAIL_CHK && $MOBILENO_CHK && $WORKID_CHK && $NATIONALID_CHK) {

    # ... INSERT
    $q = "INSERT INTO cstmrs_actvn_rqsts(MMBSHP_TYPE, CHANNEL_ID, FIRST_NAME, MIDDLE_NAME, LAST_NAME, GENDER, DOB, EMAIL, MOBILE_NO, PHYSICAL_ADDRESS, WORK_ID, NATIONAL_ID, REQST_RECORD_DATE) VALUES('$MMBSHP_TYPE', '$CHANNEL_ID', '$FIRST_NAME', '$MIDDLE_NAME', '$LAST_NAME', '$GENDER', '$DOB', '$EMAIL', '$MOBILE_NO', '$PHYSICAL_ADDRESS', '$WORK_ID', '$NATIONAL_ID', '$REQST_RECORD_DATE')";

    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"];
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP == "EXECUTED") {

      # ... Process Entity System ID (ACTIVATION_REF)
      $id_prefix = "DF";
      $id_len = 10;
      $id_record_id = $RECORD_ID;
      $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
      $ACTIVATION_REF = $ENTITY_ID;

      # ... Updating the Activation Ref
      $q2 = "UPDATE cstmrs_actvn_rqsts SET ACTIVATION_REF='$ACTIVATION_REF' WHERE RECORD_ID='$RECORD_ID'";
      $update_response = ExecuteEntityUpdate($q2);
      if ($update_response == "EXECUTED") {

        $FILE_UPLOAD_REMARKS = array();
        $valid_file_types = array("image/gif", "image/jpeg", "image/png", "application/pdf");
        $valid_file_extensions = array("gif", "png", "jpg", "jpeg", "pdf");

        # ... 00: CREATING ACTIVATION DIRECTORY
        $ACTVN_RQSTS_BASE_FILEPATH = GetSystemParameter("ACTVN_RQSTS_BASE_FILEPATH") . "/" . $_SESSION['ORG_CODE'];
        if (!is_dir($ACTVN_RQSTS_BASE_FILEPATH)) {
          mkdir($ACTVN_RQSTS_BASE_FILEPATH);
        }

        //echo $ACTVN_RQSTS_BASE_FILEPATH;
        $AR_DIR = $ACTVN_RQSTS_BASE_FILEPATH . "/" . $ACTIVATION_REF;
        $dir = $AR_DIR;
        if (!is_dir($AR_DIR)) {
          mkdir($AR_DIR);
        }

        # ... FILE 01: WORK_ID ATTACHMENT
        // ----------------------------------------------------------------------------------------------------------------
        $file_size = $_FILES['personal_id_doc_attcnt']['size'];
        $file_type = $_FILES['personal_id_doc_attcnt']['type'];
        $file_ext = strtolower(substr(strrchr($_FILES['personal_id_doc_attcnt']['name'], "."), 1));
        $file_name = "WorkID_" . $ACTIVATION_REF . "." . $file_ext;

        $required_specs = array();
        $required_specs["FILE_SIZE"] = 5000000;        // ... 3MB                       
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

        if ($FILE_SIZE_CHK && $FILE_TYPE_CHK && $FILE_EXTSN_CHK) {
          $result = move_uploaded_file($_FILES['personal_id_doc_attcnt']['tmp_name'], $dir . "/" . $file_name);
          if ($result == 1) {
            $WORK_ID_ATTCHMNT_FLG = "YY";
            $WORK_ID_FILE_NAME =  $file_name;

            $q33 = "UPDATE cstmrs_actvn_rqsts SET WORK_ID_ATTCHMNT_FLG='$WORK_ID_ATTCHMNT_FLG', WORK_ID_FILE_NAME='$WORK_ID_FILE_NAME' 
                    WHERE ACTIVATION_REF='$ACTIVATION_REF'";
            $update_response33 = ExecuteEntityUpdate($q33);
            if ($update_response33 == "EXECUTED") {
              $FILE_UPLOAD_REMARKS["WORKID_RMKS"] = "WorkID Uploaded Successfully.";
            }
          }
        } else {
          $FILE_UPLOAD_REMARKS["WORKID_RMKS"] = "WorkID Not Uploaded. REASON: " . $FILE_RMKS;
        }
        // ----------------------------------------------------------------------------------------------------------------


        # ... FILE 02: NATIONAL_ID ATTACHMENT
        // ----------------------------------------------------------------------------------------------------------------
        $file_size = $_FILES['nat_id_attchmnt']['size'];
        $file_type = $_FILES['nat_id_attchmnt']['type'];
        $file_ext = strtolower(substr(strrchr($_FILES['nat_id_attchmnt']['name'], "."), 1));
        $file_name = "NATIONALID_" . $ACTIVATION_REF . "." . $file_ext;

        $required_specs = array();
        $required_specs["FILE_SIZE"] = 5000000;        // ... 3MB                         
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

        if ($FILE_SIZE_CHK && $FILE_TYPE_CHK && $FILE_EXTSN_CHK) {
          $result = move_uploaded_file($_FILES['nat_id_attchmnt']['tmp_name'], $dir . "/" . $file_name);
          if ($result == 1) {
            $NATIONAL_ID_ATTCHMNT_FLG = "YY";
            $NATIONAL_ID_FILE_NAME =  $file_name;


            $q33 = "UPDATE cstmrs_actvn_rqsts SET NATIONAL_ID_ATTCHMNT_FLG='$NATIONAL_ID_ATTCHMNT_FLG', 
                    NATIONAL_ID_FILE_NAME='$NATIONAL_ID_FILE_NAME' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
            $update_response33 = ExecuteEntityUpdate($q33);
            if ($update_response33 == "EXECUTED") {
              $FILE_UPLOAD_REMARKS["NATIONALID_RMKS"] = "National Id Uploaded Successfully.";
            }
          }
        } else {
          $FILE_UPLOAD_REMARKS["NATIONALID_RMKS"] = "National Id Not Uploaded. REASON: " . $FILE_RMKS;
        }
        // ----------------------------------------------------------------------------------------------------------------


        # ... FILE 03: MAF ATTACHMENT
        // ----------------------------------------------------------------------------------------------------------------
        $file_size = $_FILES['maf_attchmnt']['size'];
        $file_type = $_FILES['maf_attchmnt']['type'];
        $file_ext = strtolower(substr(strrchr($_FILES['maf_attchmnt']['name'], "."), 1));
        $file_name = "MAF_" . $ACTIVATION_REF . "." . $file_ext;

        $required_specs = array();
        $required_specs["FILE_SIZE"] = 5000000;        // ... 3MB
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

        if ($FILE_SIZE_CHK && $FILE_TYPE_CHK && $FILE_EXTSN_CHK) {
          $result = move_uploaded_file($_FILES['maf_attchmnt']['tmp_name'], $dir . "/" . $file_name);
          if ($result == 1) {
            $MAF_UPLOAD_FLG = "YY";
            $MAF_UPLOAD_FILE_NAME =  $file_name;

            $q33 = "UPDATE cstmrs_actvn_rqsts SET MAF_UPLOAD_FLG='$MAF_UPLOAD_FLG', 
                    MAF_UPLOAD_FILE_NAME='$MAF_UPLOAD_FILE_NAME' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
            $update_response33 = ExecuteEntityUpdate($q33);
            if ($update_response33 == "EXECUTED") {
              $FILE_UPLOAD_REMARKS["MAF_RMKS"] = "Membership Application Form Uploaded Successfully.";
            }
          }
        } else {
          $FILE_UPLOAD_REMARKS["MAF_RMKS"] = "Membership Application Form Not Uploaded. REASON: " . $FILE_RMKS;
        }
        // ----------------------------------------------------------------------------------------------------------------


        # ... FILE 04: PASSPORT PHOTO
        // ----------------------------------------------------------------------------------------------------------------
        $file_size = $_FILES['passport_photo']['size'];
        $file_type = $_FILES['passport_photo']['type'];
        $file_ext = strtolower(substr(strrchr($_FILES['passport_photo']['name'], "."), 1));
        $file_name = "PP_" . $ACTIVATION_REF . "." . $file_ext;

        $required_specs = array();
        $required_specs["FILE_SIZE"] = 5000000;        // ... 3MB                           
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

        if ($FILE_SIZE_CHK && $FILE_TYPE_CHK && $FILE_EXTSN_CHK) {
          $result = move_uploaded_file($_FILES['passport_photo']['tmp_name'], $dir . "/" . $file_name);
          if ($result == 1) {
            $PASSPORT_PHOTO_UPLOAD_FLG = "YY";
            $PASSPORT_PHOTO_FILE_NAME =  $file_name;

            $q33 = "UPDATE cstmrs_actvn_rqsts SET PASSPORT_PHOTO_UPLOAD_FLG='$PASSPORT_PHOTO_UPLOAD_FLG', 
                    PASSPORT_PHOTO_FILE_NAME='$PASSPORT_PHOTO_FILE_NAME' WHERE ACTIVATION_REF='$ACTIVATION_REF'";
            $update_response33 = ExecuteEntityUpdate($q33);
            if ($update_response33 == "EXECUTED") {
              $FILE_UPLOAD_REMARKS["PP_RMKS"] = "Passport Photo Uploaded Successfully.";
            }
          }
        } else {
          $FILE_UPLOAD_REMARKS["PP_RMKS"] = "Passport Photo Not Uploaded. REASON: " . $FILE_RMKS;
        }
        // ----------------------------------------------------------------------------------------------------------------


        # ... FILE 05: SUMMARY OF SUBMITTION
        // ----------------------------------------------------------------------------------------------------------------
        $SUMMARY = array();
        $SUMMARY["ACTIVATION_REF"] = $ACTIVATION_REF;
        $SUMMARY["CUST_NAME"] = $FIRST_NAME . " " . $MIDDLE_NAME . " " . $LAST_NAME;
        $SUMMARY["EMAIL"] = $EMAIL;
        $SUMMARY["PHONE"] = $MOBILE_NO;
        $SUMMARY["FILE_UPLOAD_RMKS"] = $FILE_UPLOAD_REMARKS;
        $_SESSION["ACTN_RQST_SUMMARY"] = $SUMMARY;

        # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
        $INIT_CHANNEL = "WEB";
        $MSG_TYPE = "i-KHASAKH Registration";
        $RECIPIENT_EMAILS = $EMAIL;
        $EMAIL_MESSAGE = "Dear " . $SUMMARY["CUST_NAME"] . "<br>"
          . "Thank you for registering on the <b>e-Platform</b>. Your activation/registration reference is: <b>" . $ACTIVATION_REF . "</b>.<br>"
          . "You will use this reference for; "
          . "<br>1. Tracking your registration progress."
          . "<br>2. Submitting additional information requirements incase management requests for more data.<br>"
          . "<br>3. Activating your <b>e-Platform Account</b>.<br>"
          . "Regards<br>"
          . "Management<br>"
          . "<i></i>";
        $EMAIL_ATTACHMENT_PATH = "";
        $RECORD_DATE = GetCurrentDateTime();
        $EMAIL_STATUS = "NN";

        $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
        ExecuteEntityInsert($qqq);

        $next_page = "cst-make-actvn-rqst-mmbrshp-summ";
        NavigateToNextPage($next_page);
      }
    }
  } else {
    # ... Invalid captcha code
    $alert_type = "ERROR";
    $alert_msg = $RESULT_RMKS;
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  LoadDeviceSettings();
  LoadDefaultCSSConfigurations("New Membership", $APP_SMALL_LOGO);
  ?>

  <script type="text/javascript">
    function validate() {
      var captcha_code_input = document.getElementById("captcha_code_input").value;
      var email1 = document.getElementById("email1").value;
      var email2 = document.getElementById("email2").value;
      var phone1 = document.getElementById("phone1").value;
      var phone2 = document.getElementById("phone2").value;

      if (email1 == email2) {

        if (phone1 == phone2) {

          // ... Ajax Call
          $.ajax({
            type: 'post',
            url: 'validate-captcha.php',
            data: {
              captcha_data: captcha_code_input
            },
            success: function(response) {
              //console.log(response);
              var resp = JSON.parse(response)
              var captcha_response_code = "" + resp.captcha_response_code;
              var captcha_response_msg = "" + resp.captcha_response_msg;

              sessionStorage.setItem("captcha_response_code", captcha_response_code);
              sessionStorage.setItem("captcha_response_msg", captcha_response_msg);
              //console.log(captcha_response_code);
              // console.log(captcha_response_msg);

            }
          });

          var captcha_response_code = sessionStorage.getItem("captcha_response_code");
          var captcha_response_msg = sessionStorage.getItem("captcha_response_msg");
          // console.log(captcha_response_code);
          // console.log(captcha_response_msg);

          var bool_bool = process_captcha(captcha_response_code, captcha_response_msg);
          return bool_bool;

        } else {
          alert("Mobile Phone numbers are not matching");
          return false;
        }
      } else {
        alert("Emails are not matching");
        return false;
      }
    }

    function process_captcha(captcha_response_code, captcha_response_msg) {
      if (captcha_response_code == 'OK') {
        return true;
      } else {
        $('#captcha_response_msg').text(captcha_response_msg);
        return false;
      }
    }

    function fileValidation() {
      var fileInput = document.getElementById('passport_photo');
      var filePath = fileInput.value;
      //var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
      var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
      if (!allowedExtensions.exec(filePath)) {
        //alert('Please upload file having extensions .jpeg/.jpg/.png/.gif only.');
        alert('Please upload file having extensions .jpeg/.jpg/.png only.');
        fileInput.value = '';
        return false;
      } else {
        return true;
      }
    }
  </script>
</head>

<body>

  <div style="background: #FFF;">

    <!-- top navigation -->
    <div class="top_nav">
      <div class="nav_menu">
        <ul class="nav navbar-nav navbar-right">
          <li class="list-group-item-success"><a href="cst-acct-actvn">Account Activation</a></li>
          <li class="list-group-item-danger"><a href="cst-lgin">Sign In</a></li>
          <li><a href="index"><?php echo $APP_NAME; ?></a></li>
        </ul>
      </div>
      <div class="clearfix"></div>
    </div>

    <!-- /top navigation -->


    <!-- article feed -->
    <div class="row">
      <div class="col-md-2 col-sm-0 col-xs-0">
      </div>

      <div class="col-md-8 col-sm-12 col-xs-12">
        <?php if (isset($_SESSION['ALERT_MSG'])) {
          echo $_SESSION['ALERT_MSG'];
        } ?>
        <div class="x_panel">
          <div class="x_title">
            <a href="cst-acct-actvn" class="btn btn-sm btn-dark pull-left">Back</a>
            <h2>New Request (New Membership)</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">

            <form method="post" id="make_form" enctype="multipart/form-data" onsubmit="return validate(this)">
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <strong>SECTION A: </strong> Bio Data
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <label for="fn">First Name * :</label>
                    <input type="text" id="fn" name="fn" class="form-control" required="">

                    <label for="mn">Middle Name :</label>
                    <input type="text" id="mn" name="mn" class="form-control">

                    <label for="ln">Last Name / Surname * :</label>
                    <input type="text" id="ln" name="ln" class="form-control" required="">

                    <label for="gender">Gender * :</label>
                    <select id="gender" name="gender" class="form-control" required="">
                      <option value="">Select Gender</option>
                      <?php
                      $gender_list = array();
                      $gender_list = explode('^', GetSystemParameter("CORE_GENDER_DEF"));
                      for ($i = 0; $i < sizeof($gender_list); $i++) {
                        $gender_map = array();
                        $gender_map = explode('-', $gender_list[$i]);
                        $gender_code = $gender_map[0];
                        $gender_name = $gender_map[1];

                      ?>
                        <option value="<?php echo $gender_code; ?>"><?php echo $gender_name; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                    

                    <label for="dob">Date of Birth * :</label><br>
                    <select id="dob_dd" name="dob_dd" required="">
                      <option value="">Day</option>
                      <?php
                      for ($i = 1; $i < 32; $i++) {
                      ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                    <select id="dob_mm" name="dob_mm" required="">
                      <option value="">Month</option>
                      <?php
                      $months = array("Jan", "Feb", "March", "April", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec");
                      for ($i = 0; $i < 12; $i++) {
                      ?>
                        <option value="<?php echo ($i + 1); ?>"><?php echo $months[$i]; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                    <select id="dob_yy" name="dob_yy" required="">
                      <option value="">Year</option>
                      <?php
                      $current_year = date("Y", time());
                      for ($i = 1900; $i < ($current_year + 1); $i++) {
                      ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                      <?php
                      }
                      ?>
                    </select>




                  </div>
                </div>
              </div>

              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <strong>SECTION B: </strong> Contact Details
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <label for="email1">Email * :</label>
                    <input type="email" id="email1" name="email1" class="form-control" required="">

                    <label for="email2">Re-type Email * :</label>
                    <input type="email" id="email2" name="email2" class="form-control" required="">


                    <label for="phone1">Mobile Number * :</label>
                    <input type="number" id="phone1" name="phone1" class="form-control" required="">

                    <label for="phone2">Re-type Mobile Number * :</label>
                    <input type="number" id="phone2" name="phone2" class="form-control" required="">

                    <label for="phy_address">Physical Address * :</label>
                    <textarea id="phy_address" name="phy_address" class="form-control" required="required"></textarea>



                  </div>
                </div>
              </div>

              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <strong>SECTION C: </strong> File Attachments
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <label for="personal_id_doc_no">Work ID/Staff ID/Personal ID * :</label>
                    <input type="text" id="personal_id_doc_no" name="personal_id_doc_no" class="form-control" required="">
                    <label for="personal_id_doc_attcnt">Work ID attachment <em style="color: maroon; font-size: 11px;">(pdf, png or jpeg)</em> :</label>
                    <input type="file" id="personal_id_doc_attcnt" name="personal_id_doc_attcnt" class="form-control" required=""><br>

                    <label for="nat_id_nin">National ID (NIN) *:</label>
                    <input type="text" id="nat_id_nin" name="nat_id_nin" class="form-control" required="">
                    <label for="nat_id_attchmnt">National ID attachment <em style="color: maroon; font-size: 11px;">(pdf, png or jpeg)</em> :</label>
                    <input type="file" id="nat_id_attchmnt" name="nat_id_attchmnt" class="form-control" required=""><br>

                    <label for="maf_attchmnt">Membership Application Form <em style="color: maroon; font-size: 11px;">(pdf, png or jpeg)</em> *:<br>
                      <small>(The hard copy of the filled in Membership Application Form. Scan and upload)</small></label>
                    <input type="file" id="maf_attchmnt" name="maf_attchmnt" class="form-control" required=""><br>

                    <label for="passport_photo">Passport Photo <em style="color: maroon; font-size: 11px;">(png or jpeg)</em> *:<br>
                      <small>(soft copy taken from studio on a white background)</small></label>
                    <input type="file" id="passport_photo" name="passport_photo" accept=".png, .jpg, .jpeg" onchange="return fileValidation()" class="form-control" required="">


                  </div>
                </div>
              </div>

              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <strong>SECTION D: </strong> Terms & Conditions (T&Cs)
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#seeee">View T&Cs from Here</button>
                    <div id="seeee" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content" style="color: #333;">

                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel2">Terms and Conditions</h4>
                          </div>
                          <div class="modal-body">
                            <p align="justified">
                              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                            </p>
                            <p align="justified">
                              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                            </p>
                            <p align="justified">
                              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                            </p>
                            <p align="justified">
                              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                            </p>
                          </div>


                        </div>
                      </div>
                    </div>

                    <br><input type="checkbox" id="tcs" name="tcs" required=""> I agree with the Terms & Conditions.
                    <div class="ln_solid"></div>

                    <p>
                      <label>Prove that you are human:</label><br>
                      <img id="captcha" src="captcha/captcha.php" width="160" height="45" border="1" alt="CAPTCHA">
                      <small><a href="#" onclick="document.getElementById('captcha').src = 'captcha/captcha.php?' + Math.random();
                                                     document.getElementById('captcha_code_input').value = '';
                                                     document.getElementById('captcha_response_msg').value = '';
                                                     return false;
                      "> <span class="fa fa-refresh fa-lg" aria-hidden="true"></span> Refresh
                        </a></small></p>
                    <p><input id="captcha_code_input" type="text" name="captcha_code_input" size="10" maxlength="5" required="" onkeyup="this.value = this.value.replace(/[^\d]+/g, '');"> <small>copy the digits from the image into this box</small></p>


                    <div class="ln_solid"></div>
                    Click <strong>Submit Request</strong> to submit application<br>
                    <span id="captcha_response_msg" style="color: red; font-size: 20px;"></span><br>
                    <button type="submit" class="btn btn-lg btn-success" name="btn_submit_request">Submit Request</button>


                  </div>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
      <div class="col-md-2 col-sm-0 col-xs-0">
      </div>


    </div>
    <!-- /article feed -->


    <!-- Bottom Link -->
    <div class="row" style="color: #FFF; background: #2f4357; padding-left: 25px; padding-right: 25px;">
      <span style="font-family: calibri; font-size: 35px;"><?php echo $APP_NAME; ?></span>
      <hr style="margin-top: 3px; margin-bottom: 10px;" />
      <div>
        <div class="pull-left" style="font-family: calibri; font-size: 14px;"><?php echo $COPY_RIGHT_STMT; ?></div>
        <br />
        <br />
      </div>
    </div>
    <!-- /Bottom Link -->
  </div>

</body>

<?php
LoadDefaultJavaScriptConfigurations();
?>

</html>