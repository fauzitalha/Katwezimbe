<?php
# ... ... ... F1: System Alert Message ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function SystemAlertMessage($alert_type, $alert_msg){
	$final_alert_msg = "";

	if ($alert_type=="SUCCESS") {
		$final_alert_msg = "<div class='alert alert-success alert-dismissible fade in' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>".$alert_msg."</div>";
	}
	else if ($alert_type=="INFO"){
		$final_alert_msg = "<div class='alert alert-info alert-dismissible fade in' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' title='Close'>×</span></button>".$alert_msg."</div>";
	}
	else if ($alert_type=="WARNING"){
		$final_alert_msg = "<div class='alert alert-warning alert-dismissible fade in' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' title='Close'>×</span></button>".$alert_msg."</div>";
	}
	else if ($alert_type=="ERROR"){
		$final_alert_msg = "<div class='alert alert-danger alert-dismissible fade in' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' title='Close'>×</span></button>".$alert_msg."</div>";
	}

	return $final_alert_msg;
}


# ... ... ... F2: System Alert Message ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function NavigateToNextPage($next_page){

	echo '<meta http-equiv="refresh" content="0; URL='.$next_page.'" />';
}


# ... ... ... F3: Get Current DateTime ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCurrentDateTime(){
	$DATE = date("Y-m-d H:i:s", time());
	return $DATE;
}


# ... ... ... F4: Get Current DateTime ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ProcessEntityID($id_prefix, $id_len, $id_record_id){
	$len_pref = strlen($id_prefix);
	$len_recid = strlen($id_record_id);
	$len_middle = $id_len - ($len_pref + $len_recid);

	$zeros_list = "";
	for ($i=0; $i < $len_middle; $i++) { 
		
		$zero = "0";
		$zeros_list = $zeros_list.$zero;
	}

	$ENTITY_ID = $id_prefix.$zeros_list.$id_record_id;
	return $ENTITY_ID;
}




# **..** **..** **..** **..** **..** **..** **..** SECTION 02: 2-FACTOR AUTHENTICATION **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: 2-FACTOR AUTHENTICATION **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: 2-FACTOR AUTHENTICATION **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: 2-FACTOR AUTHENTICATION **..** **..** **..** **..** **..** **..** **..**  **..** 

# ... ... ... F5: GetConnStatus ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetConnStatus($request_msg){
	$sys_response = array();

	# ... Process Request Message
	$request_msg_details = array();
	$request_msg_details = explode('#', $request_msg);
	$operation_type = $request_msg_details[0];
	$DEVICE_ID = $request_msg_details[1];
	//$ENTITY_ID = $request_msg_details[2];

	$sys_response["RESP_CODE"] = "401";
	$sys_response["RESP_CODE_DESCRIPTION"] = "UNKNOWN DEVICE";

	$q = mysql_query("SELECT * FROM tfa_devices WHERE DEVICE_ID='$DEVICE_ID'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$sys_response["RESP_CODE"] = "000";
		$sys_response["RESP_CODE_DESCRIPTION"] = "SUCCESS";
		$sys_response['TEMP_ACCESS_PIN'] = trim($row['TEMP_ACCESS_PIN']);
		$sys_response['ACCESS_PIN_RESET_FLG'] = trim($row['ACCESS_PIN_RESET_FLG']);
		$sys_response['DEVICE_STATUS'] = trim($row['DEVICE_STATUS']);
	}


	return $sys_response;
}


# ... ... ... F6: GetDevConfigs ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetDevConfigs($request_msg){
	$sys_response = array();

	# ... Process Request Message
	$request_msg_details = array();
	$request_msg_details = explode('#', $request_msg);
	$operation_type = $request_msg_details[0];
	$DEVICE_ID = $request_msg_details[1];
	$ENTITY_ID = $request_msg_details[2];

	$sys_response["RESP_CODE"] = "401";
	$sys_response["RESP_CODE_DESCRIPTION"] = "UNKNOWN DEVICE";
	
	$q = mysql_query("SELECT * FROM tfa_devices WHERE DEVICE_ID='$DEVICE_ID' AND ENTITY_ID='$ENTITY_ID'") or die("ERROR 1: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {

			$sys_response["RESP_CODE"] = "000";
			$sys_response["RESP_CODE_DESCRIPTION"] = "SUCCESS";
			$sys_response['RECORD_ID'] = trim($row['RECORD_ID']);
			$sys_response['DEVICE_ID'] = trim($row['DEVICE_ID']);
			$sys_response['DEVICE_TYPE_ID'] = trim($row['DEVICE_TYPE_ID']);
			$sys_response['ENTITY_TYPE'] = trim($row['ENTITY_TYPE']);
			$sys_response['ENTITY_ID'] = trim($row['ENTITY_ID']);
			$sys_response['TEMP_ACCESS_PIN'] = trim($row['TEMP_ACCESS_PIN']);
			$sys_response['ACCESS_PIN_RESET_FLG'] = trim($row['ACCESS_PIN_RESET_FLG']);
			$sys_response['KEY_1'] = trim($row['KEY_1']);
			$sys_response['KEY_2'] = trim($row['KEY_2']);
			$sys_response['KEY_3'] = trim($row['KEY_3']);
			$sys_response['ADDED_ON'] = trim($row['ADDED_ON']);
			$sys_response['ADDED_BY'] = trim($row['ADDED_BY']);
			$sys_response['APPROVED_ON'] = trim($row['APPROVED_ON']);
			$sys_response['APPROVED_BY'] = trim($row['APPROVED_BY']);
			$sys_response['LAST_ACCESS_PIN_RESET_DATE'] = trim($row['LAST_ACCESS_PIN_RESET_DATE']);
			$sys_response['LAST_ACCESS_PIN_RESET_DONEBY'] = trim($row['LAST_ACCESS_PIN_RESET_DONEBY']);
			$sys_response['DEVICE_STATUS'] = trim($row['DEVICE_STATUS']);

		}

	return $sys_response;
}


# ... ... ... F7: GetToken ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetToken($request_msg){
	$sys_response = array();

	# ... Process Request Message
	$request_msg_details = array();
	$request_msg_details = explode('#', $request_msg);
	$operation_type = $request_msg_details[0];
	$DEVICE_ID = $request_msg_details[1];

	$sys_response["RESP_CODE"] = "401";
	$sys_response["RESP_CODE_DESCRIPTION"] = "No active token found.";
	
	$q = mysql_query("SELECT * FROM tfa_tokens WHERE DEVICE_ID='$DEVICE_ID' AND TOQEN_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$sys_response["RESP_CODE"] = "000";
		$sys_response["RESP_CODE_DESCRIPTION"] = "SUCCESS";
		$sys_response['RECORD_ID'] = trim($row['RECORD_ID']);
		$sys_response['DEVICE_ID'] = trim($row['DEVICE_ID']);
		$sys_response['ENTITY_ID'] = trim($row['ENTITY_ID']);
		$sys_response['EVENT_TYPE_ID'] = trim($row['EVENT_TYPE_ID']);
		$sys_response['TOQEN'] = trim($row['TOQEN']);
		$sys_response['TOQEN_STATUS'] = trim($row['TOQEN_STATUS']);

	}

	return $sys_response;
}


# ... ... ... F8: GenerateRandomAccessPin ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GenerateRandomAccessPin($length) {
  $result = '';

  for($i = 0; $i < $length; $i++) {
      $result .= mt_rand(0, 9);
  }

  return $result;
}


# ... ... ... F9: GenerateSecurityKey ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GenerateSecurityKey($len) {

  //define character libraries - remove ambiguous characters like iIl|1 0oO
  $sets = array();
  $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
  $sets[] = 'abcdefghjkmnpqrstuvwxyz';
  $sets[] = '23456789';
  $sets[]  = '~!@#$%^&*(){}[],./?';

  $password = '';
  
  //append a character from each set - gets first 4 characters
  foreach ($sets as $set) {
      $password .= $set[array_rand(str_split($set))];
  }

  //use all characters to fill up to $len
  while(strlen($password) < $len) {
      //get a random set
      $randomSet = $sets[array_rand($sets)];
      
      //add a random char from the random set
      $password .= $randomSet[array_rand(str_split($randomSet))]; 
  }
  
  //shuffle the password string before returning!
  return str_shuffle($password);
}

# ... ... ... F9: GenerateSecurityKey ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GeneratePassKey($len) {

  //define character libraries - remove ambiguous characters like iIl|1 0oO
  $sets = array();
  $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
  $sets[] = '23456789';

  $password = '';
  
  //append a character from each set - gets first 4 characters
  foreach ($sets as $set) {
      $password .= $set[array_rand(str_split($set))];
  }

  //use all characters to fill up to $len
  while(strlen($password) < $len) {
      //get a random set
      $randomSet = $sets[array_rand($sets)];
      
      //add a random char from the random set
      $password .= $randomSet[array_rand(str_split($randomSet))]; 
  }
  
  //shuffle the password string before returning!
  return str_shuffle($password);
}


# ... ... ... F10: GetDevConfigs ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function UpdateDevPinFromDevice($request_msg){
	$sys_response = array();

	# ... Process Request Message
	$request_msg_details = array();
	$request_msg_details = explode('#', $request_msg);
	$operation_type = $request_msg_details[0];
	$ACCESS_PIN_RESET_FLG = $request_msg_details[1];
	$pin_encrypted = $request_msg_details[2];
	$DEVICE_ID = $request_msg_details[3];

	$sys_response["RESP_CODE"] = "401";
	$sys_response["RESP_CODE_DESCRIPTION"] = "UNKNOWN OPERATION";

	$q = mysql_query("UPDATE tfa_devices SET ACCESS_PIN_RESET_FLG='$ACCESS_PIN_RESET_FLG' WHERE DEVICE_ID='$DEVICE_ID'") or die("ERROR 1: ".mysql_error());

	if ($q) {
		$sys_response["RESP_CODE"] = "000";
		$sys_response["RESP_CODE_DESCRIPTION"] = "SUCCESS";
		$sys_response["MESSAGE"] = "PIN_UPDATED";
	}



	return $sys_response;
}

# ... ... ... F11: GetDevConfigs ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetActiveUserTFADevice($ENTITY_ID){
	$DEVICE_ID = "";

	$q = mysql_query("SELECT * FROM tfa_devices WHERE ENTITY_ID='$ENTITY_ID' AND DEVICE_STATUS='COMPLETE'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$DEVICE_ID = trim($row['DEVICE_ID']);
	}

	return $DEVICE_ID;
}

# ... ... ... F12: GenerateTFAToken ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GenerateTFAToken($DEVICE_ID,$ENTITY_ID,$EVENT_TYPE_ID,$TOQEN){
	$is_token_gen = "NOT_GENERATED";
	$TOQEN_GEN_DATE = GetCurrentDateTime();

	# ... 03: Kill Unused tokens
	mysql_query("UPDATE tfa_tokens SET TOQEN_STATUS='KILLED (NOT_USED)' WHERE DEVICE_ID='$DEVICE_ID' AND ENTITY_ID='$ENTITY_ID' AND TOQEN_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());

	$q = mysql_query("INSERT INTO tfa_tokens(DEVICE_ID,ENTITY_ID,EVENT_TYPE_ID,TOQEN,TOQEN_GEN_DATE) VALUES('$DEVICE_ID','$ENTITY_ID','$EVENT_TYPE_ID','$TOQEN','$TOQEN_GEN_DATE')") or die("ERROR 1: ".mysql_error());

	if ($q) {
		$is_token_gen = "GENERATED";
	}

	return $is_token_gen;
}

# ... ... ... F13: VerifyAuthToken ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function VerifyAuthToken($USER_ID, $upr_token, $TOKEN_EXPIRY_TIME){
	$is_valid = "";
	$TOQEN_GEN_DATE = "";
	$DB_TOQEN = "";

	$q = mysql_query("SELECT * FROM tfa_tokens WHERE ENTITY_ID='$USER_ID' AND TOQEN_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$TOQEN_GEN_DATE = trim($row['TOQEN_GEN_DATE']);
		$DB_TOQEN = trim($row['TOQEN']);
	}

	$DB_TOQEN_DEC = AES256::decrypt($DB_TOQEN);


	# ... Check for validity
	if ($upr_token==$DB_TOQEN_DEC) {
		
		# ... Check for expiry
		$curr_date_time = GetCurrentDateTime();
		$cur_time = strtotime($curr_date_time);
		$gen_time = strtotime($TOQEN_GEN_DATE);
		$time_interval = $cur_time - $gen_time;

		if ($time_interval > $TOKEN_EXPIRY_TIME) {
			$is_valid = "EXPIRED";
		} else {
			$is_valid = "VALID";
		}
	} else {
		$is_valid = "INVALID";
	}

	return $is_valid;
}

# ... ... ... F12: GenerateTFAToken ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ReGenerateTFAToken($DEVICE_ID,$ENTITY_ID,$EVENT_TYPE_ID,$TOQEN){
	$is_token_gen = "NOT_GENERATED";
	$TOQEN_GEN_DATE = GetCurrentDateTime();

	# ... 03: Kill Unused tokens
	mysql_query("UPDATE tfa_tokens SET TOQEN_STATUS='KILLED (NEW_TOKEN_CREATED)' WHERE DEVICE_ID='$DEVICE_ID' AND ENTITY_ID='$ENTITY_ID' AND TOQEN_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());

	$q = mysql_query("INSERT INTO tfa_tokens(DEVICE_ID,ENTITY_ID,EVENT_TYPE_ID,TOQEN,TOQEN_GEN_DATE) VALUES('$DEVICE_ID','$ENTITY_ID','$EVENT_TYPE_ID','$TOQEN','$TOQEN_GEN_DATE')") or die("ERROR 1: ".mysql_error());

	if ($q) {
		$is_token_gen = "GENERATED";
	}

	return $is_token_gen;
}


# **..** **..** **..** **..** **..** **..** **..** SECTION 999: DATA VALIDATION FUNCTIONS **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 999: DATA VALIDATION FUNCTIONS **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 999: DATA VALIDATION FUNCTIONS **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 999: DATA VALIDATION FUNCTIONS **..** **..** **..** **..** **..** **..** **..**  **..** 

# ... ... ... F991: ProcessVerifButtonDisplay ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ProcessVerifButtonDisplay($BIO_DATA_VERIF_FLG, $CONTACT_DATA_VERIF_FLG, $FILE_DATA_VERIF_FLG){
	$display_buttons_details = array();
	$disp_buttons = "";
	$disp_response = "";

	# ... 01: Bio Data
  # ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
	$BIO_FLG = $BIO_DATA_VERIF_FLG;
	if ($BIO_FLG=="YY") {
		$disp_response = "<span style='color: green; font-weight: bold;'>[BIO_DATA_OKAY]: </span>Bio Data is approved.<br>";
	} 
	else if($BIO_FLG=="NN"){
		$disp_response = "<span style='color: red; font-weight: bold;'>[BIO_DATA_ERROR]: </span>Bio Data is not approved.<br>";
	}
  # ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...

	# ... 02: Contact Data
	$addd = explode('|', $CONTACT_DATA_VERIF_FLG);
  $EMAIL_FLG = $addd[0];
  $PHONE_FLG = $addd[1];

  # ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
  if ($EMAIL_FLG=="YY") {
		$disp_response = $disp_response."<span style='color: green; font-weight: bold;'>[EMAIL_OKAY]: </span>Email is approved.<br>";
	} 
	else if($EMAIL_FLG=="NN"){
		$disp_response = $disp_response."<span style='color: red; font-weight: bold;'>[EMAIL_ERROR]: </span>Email is not approved.<br>";
	}

	if ($PHONE_FLG=="YY") {
		$disp_response = $disp_response."<span style='color: green; font-weight: bold;'>[PHONE_OKAY]: </span>Phone is approved.<br>";
	} 
	else if($PHONE_FLG=="NN"){
		$disp_response = $disp_response."<span style='color: red; font-weight: bold;'>[PHONE_ERROR]: </span>Phone is not approved.<br>";
	}
  # ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...


	# ... 03: File Data
	$addd = explode('|', $FILE_DATA_VERIF_FLG);
  $wkid_flg = $addd[0];
  $nin_flg = $addd[1];
  $maf_flg = $addd[2];
  $php_flg = $addd[3];

  # ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
  if ($wkid_flg=="YY") {
		$disp_response = $disp_response."<span style='color: green; font-weight: bold;'>[WORK_ID_OKAY]: </span>Work_id is approved.<br>";
	} 
	else if($wkid_flg=="NN"){
		$disp_response = $disp_response."<span style='color: red; font-weight: bold;'>[WORK_ID_ERROR]: </span>Work_id is not approved.<br>";
	}

	if ($nin_flg=="YY") {
		$disp_response = $disp_response."<span style='color: green; font-weight: bold;'>[NATIONAL_ID_OKAY]: </span>National_id is approved.<br>";
	} 
	else if($nin_flg=="NN"){
		$disp_response = $disp_response."<span style='color: red; font-weight: bold;'>[NATIONAL_ID_ERROR]: </span>National_id is not approved.<br>";
	}

	if ($maf_flg=="YY") {
		$disp_response = $disp_response."<span style='color: green; font-weight: bold;'>[APPLICATION_FORM_OKAY]: </span>Application Form is approved.<br>";
	} 
	else if($maf_flg=="NN"){
		$disp_response = $disp_response."<span style='color: red; font-weight: bold;'>[APPLICATION_FORM_ERROR]: </span>Application Form is not approved.<br>";
	}

	if ($php_flg=="YY") {
		$disp_response = $disp_response."<span style='color: green; font-weight: bold;'>[PASSPORT_PHOTO_OKAY]: </span>Passport photo is approved.<br>";
	} 
	else if($php_flg=="NN"){
		$disp_response = $disp_response."<span style='color: red; font-weight: bold;'>[PASSPORT_PHOTO_ERROR]: </span>Passport photo is not approved.<br>";
	}
  # ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...


	# ... 05:  Display respise
	if ( ($BIO_FLG=="YY")&&($EMAIL_FLG=="YY")&&($PHONE_FLG=="YY")&&($wkid_flg=="YY")&&($nin_flg=="YY")&&($maf_flg=="YY")&&($php_flg=="YY") ) {
		$disp_buttons = "DISPLAY_VERIFY";	
	}
	else{
		$disp_buttons = "DISPLAY_DONT_VERIFY";	
	}


	# ... 06: Package Response
	$display_buttons_details["BTN_DISP_FLG"]=$disp_buttons;
	$display_buttons_details["BTN_DISP_MSG"]=$disp_response;


	return $display_buttons_details;
}

# ... ... ... F992: Color By Status YES or NO ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ColorByStatusFlg($STATUS_FLG){
	$bg_color = "";

	if ($STATUS_FLG=="YY") {
		$bg_color = "background: #C8F7C8;";
	}

	if ($STATUS_FLG=="NN") {
		$bg_color = "background: #F7CAC8;";
	}
	return $bg_color;
}


# ... ... ... F993: Disable By Status Flg ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function DisableByStatusFlg($STATUS_FLG){
	$disabled_flg = "";

	if ($STATUS_FLG=="YY") {
		$disabled_flg = "";
	}

	if ($STATUS_FLG=="NN") {
		$disabled_flg = "disabled=''";
	}
	return $disabled_flg;
}


# ... ... ... F994: Color By Status YES or NO ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BackgrounfColorByStatusFlg($STATUS_FLG){
	$bg_color = "";

	if ($STATUS_FLG=="YY") {
		$bg_color = "background: #C8F7C8;";
	}

	if ($STATUS_FLG=="NN") {
		$bg_color = "background: #EEE;";
	}
	return $bg_color;
}

# ... ... ... F995: Color By Status YES or NO ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function PendingOrDone($STATUS_FLG){
	$bg_color = "";

	if ($STATUS_FLG=="YY") {
		$bg_color = "done";
	}

	if ($STATUS_FLG=="NN") {
		$bg_color = "pending";
	}
	return $bg_color;
}

# ... ... ... F996: ValidateFileAttachment YES or NO ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ValidateFileAttachment($required_specs, $file_specs){

	$file_results = array();
	$file_rmks = "";

	# ... Validate File Size
	$file_size = $file_specs["FILE_SIZE"];
	$required_file_size = $required_specs["FILE_SIZE"];
	if ($file_size>$required_file_size) {
		$file_results["FILE_SIZE_CHK"] = false;
		$file_rmks = $file_rmks." File size exceeds acceptable limit of 700KBs.<br>";
	} else {
		$file_results["FILE_SIZE_CHK"] = true;
	}

	# ... Validate Mime Type
	$valid_file_types = array();
	$valid_file_types = $required_specs["FILE_TYPES"]; 
	$file_type = $file_specs["FILE_TYPE"]; 
	if (in_array($file_type, $valid_file_types)) {
		$file_results["FILE_TYPE_CHK"] = true;
	} else {
		$file_results["FILE_TYPE_CHK"] = false;
		$file_rmks = $file_rmks." Invalid File Type (".$file_type.").<br>";
	}


	# ... Validate File Ext
	$valid_file_extensions = array();
	$valid_file_extensions = $required_specs["FILE_EXTENSIONS"]; 
	$file_ext = $file_specs["FILE_EXTENSION"];
	if (in_array($file_ext, $valid_file_extensions)) {
		$file_results["FILE_EXTSN_CHK"] = true;
	} else {
		$file_results["FILE_EXTSN_CHK"] = false;
		$file_rmks = $file_rmks." Invalid File Extension (.".$file_ext.").<br>";
	}

	$file_results["FILE_RMKS"] = $file_rmks;
	return $file_results;
}

# ... ... ... F3: Get Current DateTime ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function NotificationId(){
	$DATE = date("Ymd", time());
	return $DATE;
}

# ... ... ... F3: Get Current DateTime ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function TimeStamp(){
	$DATE = date("YmdHis", time());
	return $DATE;
}


# ... ... ... F3: BuildUiTool ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function BuildUiTool($UI_ID_NAME, $UI_DES_SPEC, $UI_CUR_VAL){

	/*
	DD#YES_YES|NO_NO
	TB#NUM --- TB#TEXT
	DDDB#SQLAPPLNMGTCOM
	*/

	$html_ui_tool = "";

	$split01 = explode('#', $UI_DES_SPEC);
	$ui_type = $split01[0];
	$ui_data = $split01[1];

	# ... ... IFF SIMPLE DROPDOWNLIST
	if ($ui_type=="DD") {
		
		$html_ui_tool = "<select id='$UI_ID_NAME' name='$UI_ID_NAME'>";
		$split02 = explode('|', $ui_data);
		for ($i=0; $i < sizeof($split02); $i++) { 
			
			$ui_data_val = $split02[$i];
			$split03 = explode('_', $ui_data_val);
			$opt_val = $split03[0];
			$opt_disp = $split03[1];

			if ($opt_val==$UI_CUR_VAL) {
				$html_ui_tool = $html_ui_tool."<option value='$opt_val' selected='selected'>$opt_disp</option>";
			}
			else{
				$html_ui_tool = $html_ui_tool."<option value='$opt_val'>$opt_disp</option>";
			}
		}	// ... end..loop
		$html_ui_tool = $html_ui_tool."</select>";
	}

	# ... ... IFF TEXTBOX
	elseif ($ui_type=="TB") {

		if ($ui_data=="NUM") {
			$html_ui_tool = "<input type='number' id='$UI_ID_NAME' name='$UI_ID_NAME' value='$UI_CUR_VAL' />";
		}
		else if ($ui_data=="TEXT") {
			$html_ui_tool = "<input type='text' id='$UI_ID_NAME' name='$UI_ID_NAME' value='$UI_CUR_VAL' />";
		}
	}

	# ... ... IFF DATABASE DROPDOWNLIST
	elseif ($ui_type=="DDDB") {

		if ($ui_data=="SQLAPPLNMGTCOM") {
			$GRP_STATUS = "ACTIVE";
      $grp_list = array();
      $grp_list = FetchAppMgtGroupList($GRP_STATUS);

      $html_ui_tool = "<select id='$UI_ID_NAME' name='$UI_ID_NAME'>";
      $html_ui_tool = $html_ui_tool."<option value=''>Select Value</option>";
      for ($i=0; $i < sizeof($grp_list); $i++) { 
        $grp = array();
        $grp = $grp_list[$i];
        $RECORD_ID = $grp['RECORD_ID'];
        $GRP_ID = $grp['GRP_ID'];
        $GRP_NAME = $grp['GRP_NAME'];
        
        if ($GRP_ID==$UI_CUR_VAL) {
					$html_ui_tool = $html_ui_tool."<option value='$GRP_ID' selected='selected'>$GRP_NAME</option>";
				}
				else{
					$html_ui_tool = $html_ui_tool."<option value='$GRP_ID'>$GRP_NAME</option>";
				}

      } // ... END..LOOP
			$html_ui_tool = $html_ui_tool."</select>";
		}
		else if ($ui_data=="SQLCHRGEVNTTYPE") {
			
			$TRAN_TYPE_STATUS = "ACTIVE";
      $tt_list = array();
      $tt_list = FetchTransactionTypes($TRAN_TYPE_STATUS);

      $html_ui_tool = "<select id='$UI_ID_NAME' name='$UI_ID_NAME'>";
      $html_ui_tool = $html_ui_tool."<option value=''>Select Value</option>";
      for ($i=0; $i < sizeof($tt_list); $i++) { 
        $tt = array();
        $tt = $tt_list[$i];
        $RECORD_ID = $tt['RECORD_ID'];
        $TRAN_TYPE_ID = $tt['TRAN_TYPE_ID'];
        $TRAN_TYPE_NAME = $tt['TRAN_TYPE_NAME'];
        
        if ($TRAN_TYPE_ID==$UI_CUR_VAL) {
					$html_ui_tool = $html_ui_tool."<option value='$TRAN_TYPE_ID' selected='selected'>$TRAN_TYPE_NAME</option>";
				}
				else{
					$html_ui_tool = $html_ui_tool."<option value='$TRAN_TYPE_ID'>$TRAN_TYPE_NAME</option>";
				}

      } // ... END..LOOP
			$html_ui_tool = $html_ui_tool."</select>";

		}
	}


	return $html_ui_tool;
}



?>

