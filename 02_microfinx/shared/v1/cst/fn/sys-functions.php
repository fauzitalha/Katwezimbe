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

// ... F5: CHECK FOR PASSWORD EXPIRY
function CheckForPasswordExpiry($CURRENT_DATE, $LST_PSWD_CHNG){
	$is_pswd_expired = "NO";

	$start = strtotime($CURRENT_DATE);
    $end = strtotime($LST_PSWD_CHNG);

	$datediff = abs($start - $end);
	$days = floor($datediff / (60*60*24));

	if ( $days > 30 ) { // ... PASSWORD IS EXPIRED
		$is_pswd_expired = "YES";
	}

	return $is_pswd_expired;
}

// ... F6: CHECKING FOR RECENT PASSWORDS
function CheckIfPasswordIsRecent($new_password, $CUST_PASSWORDS){
	$pswd_is_recent = "NO";
	$pswd_list = array();
	$pswd_list = $CUST_PASSWORDS;
	$p = "";

	if (sizeof($pswd_list)>0) {
		for ($i=0; $i < sizeof($pswd_list) ; $i++) { 
			$p = $pswd_list[$i];

			if ( $p==$new_password ) {
				$pswd_is_recent = "YES";
				break;
			}
		}
	}
	else
	{
		$pswd_is_recent = "NO";
	}
	return $pswd_is_recent;
}

// ... F7: Function to get the client IP address
function GetClientIp() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// ... F8: Mask Phone
function MaskPhone($phonenumber, $trim, $maskCharacter){
	$suffixNumber = substr($phonenumber, strlen($phonenumber)-$trim,$trim); 
	$prefixNumber = substr($phonenumber, 0, -$trim); 
	$str = "";

	for ($x = 0; $x < strlen($prefixNumber); $x++){
	    $str.= ( is_numeric($prefixNumber[$x]) )? str_replace($prefixNumber[$x], $maskCharacter, $prefixNumber[$x]) : $prefixNumber[$x];
	}

	return  $str.$suffixNumber;    
}

// ... F9: Mask Email
function MaskEmail($email){
	$prop=2;
  $domain = substr(strrchr($email, "@"), 1);
  $mailname=str_replace($domain,'',$email);
  $name_l=strlen($mailname);
  $domain_l=strlen($domain);
  $start = "";
  $end = "";
  for($i=0;$i<=$name_l/$prop-1;$i++)
  {
  	$start.='#';
  }

  for($i=0;$i<=$domain_l/$prop-1;$i++)
  {
  	$end.='#';
  }

  return substr_replace($mailname, $start, 2, $name_l/$prop).substr_replace($domain, $end, 2, $domain_l/$prop);
}


# **..** **..** **..** **..** **..** **..** **..** SECTION 03: ACTIVATION REQUESTES **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 03: ACTIVATION REQUESTES **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 03: ACTIVATION REQUESTES **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 03: ACTIVATION REQUESTES **..** **..** **..** **..** **..** **..** **..**  **..** 

function ValidateFileAttachment($required_specs, $file_specs){

	$file_results = array();
	$file_rmks = "";

	# ... Validate File Size
	$file_size = $file_specs["FILE_SIZE"];
	$required_file_size = $required_specs["FILE_SIZE"];
	if ($file_size>$required_file_size) {
		$file_results["FILE_SIZE_CHK"] = false;
		$file_rmks = $file_rmks." File size exceeds acceptable limit.<br>";
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
	$EMAIL_FLG = "";
  $PHONE_FLG = "";
	$addd = explode('|', $CONTACT_DATA_VERIF_FLG);
	if (sizeof($addd)>0) {
		$EMAIL_FLG = $addd[0];
  	$PHONE_FLG = $addd[1];
	}

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

# ... ... ... F993: Color By Status YES or NO ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function PassOrFail($STATUS_FLG){
	$res = "";

	if ($STATUS_FLG=="YY") {
		$res = "PASS";
	}

	if ($STATUS_FLG=="NN") {
		$res = "FAIL";
	}
	return $res;
}

# ... ... ... F994: ProcessVerifButtonDisplay ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ReflagActivationStatus($BIO_DATA_VERIF_FLG, $CONTACT_DATA_VERIF_FLG, $FILE_DATA_VERIF_FLG){
	$ACTIVATION_STATUS = "";

  # ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
  if ( ($BIO_DATA_VERIF_FLG==""||$BIO_DATA_VERIF_FLG=="YY") && 
  	   ($CONTACT_DATA_VERIF_FLG==""||$CONTACT_DATA_VERIF_FLG=="YY|YY") && 
  	   ($FILE_DATA_VERIF_FLG==""||$FILE_DATA_VERIF_FLG=="YY|YY|YY|YY")) {
		$ACTIVATION_STATUS = "RESUBMITTED";
	} 
	else {
		$ACTIVATION_STATUS = "NEEDS_CUSTOMER_REVIEW";
	}

	return $ACTIVATION_STATUS;
}

# ... ... ... F995: GenerateSecurityKey ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... F996: EmailMessageContainer ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function EmailMessageContainer($TITLE, $MESSAGE){
  $html = "<table bgcolor='#EEE' width='100%' height='100%' style=''>".
              "<tr><td>".
                  "<br>".
                  "<table bgcolor='#FFF' align='center' width='55%' style='border: solid 1px #EEE; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 15px; padding: 4px; border-radius: 7px; -webkit-border-radius: 7px; -moz-border-radius: 7px;'>".
                      "<tr valign='top'>".
                          "<td colspan='2' align='right'><img class='scale-with-grid' src='https://web.postbank.co.ug/images/PostBankLogoStacked.jpg' style='width:130px;height:111px' alt='Post Bank Uganda'></td>".
                      "</tr>".
                      "<tr><td colspan='2'><strong>RE: ".$TITLE."</strong></td></tr>".
                      "<tr><td colspan='2'><hr style='border: solid 1px #EEE; '></td></tr>".
                      "<tr>".
                          "<td colspan='2'>".$MESSAGE. "</td>".
                      "</tr>".
                      "<tr><td colspan='2'>Thank you for banking with us. <br><br><br><br></td></tr>".
                  "</table>".
                      "<br>".
                      "<br>".
          "</td></tr>".
          "</table>";

  return $html;
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

?>

