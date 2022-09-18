<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Details
$CUST_CORE_ID = mysql_real_escape_string(trim($_GET['k']));

# ... ... ... ... ... ... ... ... Get Customer Main Details ... ... ... ... ... ... ... ... ... ... ...#
$cust_id = $CUST_CORE_ID;
$response_msg = FetchCustomerDetailsFromCore($cust_id, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$CST_accountNo = isset($CORE_RESP["accountNo"]) ? $CORE_RESP["accountNo"] : "";
$CST_firstname = isset($CORE_RESP["firstname"]) ? $CORE_RESP["firstname"] : "";
$CST_middlename = isset($CORE_RESP["middlename"]) ? $CORE_RESP["middlename"] : "";
$CST_lastname = isset($CORE_RESP["lastname"]) ? $CORE_RESP["lastname"] : "";
$CST_displayName = isset($CORE_RESP["displayName"]) ? $CORE_RESP["displayName"] : "";
$CST_mobileNo = isset($CORE_RESP["mobileNo"]) ? $CORE_RESP["mobileNo"] : "";

# ... ... ... ... ... ... ... ... Get Customer Other Details ... ... ... ... ... ... ... ... ... ... ...#
$CST_client_id = "";
$CST_Email = "";
$CST_WorkID = "";
$CST_NationalId = "";
$CST_Physical_Address = "";
$CST_Date_of_Birth = "";

$CLIENT_ID = $CUST_CORE_ID;
$response_msg = FetchClientOtherDetails_Walkin($CLIENT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
if (isset($CORE_RESP[0]["client_id"])) {
	$CST_client_id = $CORE_RESP[0]["client_id"];
	$CST_Email = $CORE_RESP[0]["Email"];
	$CST_WorkID = $CORE_RESP[0]["WorkID"];
	$CST_NationalId = $CORE_RESP[0]["NationalId"];
	$CST_Physical_Address = $CORE_RESP[0]["Physical_Address"];
	$CST_Date_of_Birth = $CORE_RESP[0]["Date_of_Birth"];
}


if (isset($_POST['btn_submit_request'])) {

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
	$MMBSHP_TYPE = "EXST";
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
			$id_prefix = "EH";
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
				$required_specs["FILE_SIZE"] = 5000000;     // ... 3MB                      
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
				$MSG_TYPE = "e-PLATFORM Registration";
				$RECIPIENT_EMAILS = $EMAIL;
				$EMAIL_MESSAGE = "Dear " . $SUMMARY["CUST_NAME"] . "<br>"
				. "Thank you for registering on the <b>i-KHASAKH platform</b>. Your activation/registration reference is: <b>" . $ACTIVATION_REF . "</b>.<br>"
				. "You will use this reference for; "
				. "<br>1. Tracking your registration progress."
				. "<br>2. Submitting additional information requirements incase management requests for more data.<br>"
				. "<br>3. Activating your <b>i-KHASAKH Account</b>.<br>"
				. "Regards<br>"
				. "Management<br>"
				. "<i></i>";
				$EMAIL_ATTACHMENT_PATH = "";
				$RECORD_DATE = GetCurrentDateTime();
				$EMAIL_STATUS = "NN";

				$qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
				ExecuteEntityInsert($qqq);


				$next_page = "cm-client-enroll-summ";
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
					<div align="center" style="width: 100%;"><?php if (isset($_SESSION['ALERT_MSG'])) {
																											echo $_SESSION['ALERT_MSG'];
																										} ?></div>
					<div class="x_panel">
						<div class="">
							<a href="cm-client-list-ind?k=<?php echo $CUST_CORE_ID; ?>" class="btn btn-dark btn-sm pull-left">Back</a>
							<h2>Enroll Client on e-Platform (Internet and Mobile)</h2>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>

				<form method="post" id="make_form" enctype="multipart/form-data" onsubmit="return validate(this)">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="x_panel">
							<div class="x_title">
								<strong>SECTION A: </strong> Bio Data
								<div class="clearfix"></div>
							</div>
							<div class="x_content">

								<label>Core Id:</label>
								<input type="text" class="form-control" disabled="" value="<?php echo $CST_accountNo; ?>">

								<label for="fn">First Name * :</label>
								<input type="text" id="fn" name="fn" class="form-control" value="<?php echo $CST_firstname; ?>" required="">

								<label for="mn">Middle Name :</label>
								<input type="text" id="mn" name="mn" class="form-control" value="<?php echo $CST_middlename; ?>">

								<label for="ln">Last Name / Surname * :</label>
								<input type="text" id="ln" name="ln" class="form-control" value="<?php echo $CST_lastname; ?>" required="">

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
								<br><br><br>

								<label for="email1">Email * :</label>
								<input type="email" id="email1" name="email1" class="form-control" value="<?php echo $CST_Email; ?>" required="">

								<label for="phone1">Mobile Number * :</label>
								<input type="number" id="phone1" name="phone1" class="form-control" value="<?php echo $CST_mobileNo; ?>" required="">

								<label for="phy_address">Physical Address * :</label>
								<textarea id="phy_address" name="phy_address" class="form-control" required="required"><?php echo $CST_Physical_Address; ?></textarea>

							</div>
						</div>
					</div>

					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel">
							<div class="x_title">
								<strong>SECTION C: </strong> File Attachments
								<div class="clearfix"></div>
							</div>
							<div class="x_content">

								<div class="col-md-4 col-sm-6 col-xs-12">
									<label for="personal_id_doc_no">Work ID/Staff ID/Personal ID * :</label>
									<input type="text" id="personal_id_doc_no" name="personal_id_doc_no" value="<?php echo $CST_WorkID; ?>" class="form-control">
									<label for="personal_id_doc_attcnt">Work ID attachment <em style="color: maroon; font-size: 11px;">(pdf, png or jpeg)</em> :</label>
									<input type="file" id="personal_id_doc_attcnt" name="personal_id_doc_attcnt" class="form-control"><br>

								</div>

								<div class="col-md-4 col-sm-6 col-xs-12">
									<label for="nat_id_nin">National ID (NIN) *:</label>
									<input type="text" id="nat_id_nin" name="nat_id_nin" value="<?php echo $CST_NationalId; ?>" class="form-control">
									<label for="nat_id_attchmnt">National ID attachment <em style="color: maroon; font-size: 11px;">(pdf, png or jpeg)</em> :</label>
									<input type="file" id="nat_id_attchmnt" name="nat_id_attchmnt" class="form-control"><br>
								</div>

								<div class="col-md-4 col-sm-6 col-xs-12">
									<label for="maf_attchmnt">Membership Application Form <em style="color: maroon; font-size: 11px;">(pdf, png or jpeg)</em> *:<br>
										<small>(The hard copy of the filled in Membership Application Form. Scan and upload)</small></label>
									<input type="file" id="maf_attchmnt" name="maf_attchmnt" class="form-control"><br>


									<label for="passport_photo">Passport Photo <em style="color: maroon; font-size: 11px;">(png or jpeg)</em> *:<br>
										<small>(soft copy taken from studio on a white background)</small></label>
									<input type="file" id="passport_photo" name="passport_photo" accept=".png, .jpg, .jpeg, .gif" class="form-control">
								</div>

							</div>
						</div>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel">
							<div class="x_content">

								<button type="submit" class="btn btn-lg btn-success pull-right" name="btn_submit_request">Submit Request</button>
							</div>
						</div>
					</div>
				</form>

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