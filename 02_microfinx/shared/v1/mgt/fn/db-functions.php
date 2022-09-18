<?php
# ... Debugging Utilities
//echo "<pre>".print_r($user_role_details,true)."</pre>";

# **..** **..** **..** **..** **..** **..** **..** SECTION 01: System Access Functions **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 01: System Access Functions **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 01: System Access Functions **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 01: System Access Functions **..** **..** **..** **..** **..** **..** **..**  **..** 

# ... ... ... F0: Log System Event ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID){

	$q = "INSERT INTO sys_gen_activities(AUDIT_DATE, ENTITY_TYPE, ENTITY_ID_AFFECTED, EVENT, EVENT_OPERATION, EVENT_RELATION, 
																			 EVENT_RELATION_NO, OTHER_DETAILS, INVOKER_ID) 
				VALUES('$AUDIT_DATE', '$ENTITY_TYPE', '$ENTITY_ID_AFFECTED', '$EVENT', '$EVENT_OPERATION', '$EVENT_RELATION', 
				       '$EVENT_RELATION_NO', '$OTHER_DETAILS', '$INVOKER_ID')";

	ExecuteEntityInsert($q);
}

# ... ... ... F1: Get System Parameter ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetSystemParameter($PARAM_CODE){
	$PARAM_VALUE = "";

	$q = mysql_query("SELECT * FROM sys_gen_params WHERE PARAM_CODE='$PARAM_CODE' AND PARAM_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$PARAM_CODE = trim($row['PARAM_CODE']);
		$PARAM_VALUE = trim($row['PARAM_VALUE']);
	}

	return $PARAM_VALUE;
}

# ... ... ... F2: Log User Access Log ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function LogUserAccessLog($USER_ID,$LOG_TYPE,$LOG_DATE,$LOG_DETAILS,$SRC_IP_ADDRESS){
	$is_logged= "NO";
	$q = mysql_query("INSERT INTO upr_lgn_log(USER_ID,LOG_TYPE,LOG_DATE,LOG_DETAILS,SRC_IP_ADDRESS) VALUES('$USER_ID','$LOG_TYPE','$LOG_DATE','$LOG_DETAILS','$SRC_IP_ADDRESS')") or die("ERR_UPR_LOG: ".mysql_error());
	if($q)
	{
		$is_logged = "YES";
	}

	return $is_logged;
}

# ... ... ... F3: Check User Access Clearance On Mgt Portal ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function CheckUserAccessClearanceOnMgtPortal($core_user_id){
	$ACCESS_DETAILS = array();
	$ACCESS_FLG = "NO";
	$USER_STATUS = "";

	$q = mysql_query("SELECT * FROM upr WHERE USER_CORE_ID='$core_user_id' AND USER_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$USER_STATUS = trim($row['USER_STATUS']);
	}

	# ... Check if User Exists
	if (mysql_num_rows($q)>0) {
		$ACCESS_FLG = "YES";
	}
	else{
		$ACCESS_FLG = "NO";
	}

	$ACCESS_DETAILS['ACCESS_FLG'] = $ACCESS_FLG;
	$ACCESS_DETAILS['USER_STATUS'] = $USER_STATUS;


	return $ACCESS_DETAILS;
}

# ... ... ... F4: Get Cust Details From Portal ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function GetCustDetailsFromPortal($core_user_id){
	$USER_DETAILS = array();

	$q = mysql_query("SELECT * FROM upr WHERE USER_CORE_ID='$core_user_id' AND USER_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$USER_DETAILS['RECORD_ID'] = trim($row['RECORD_ID']);
		$USER_DETAILS['USER_ID'] = trim($row['USER_ID']);
		$USER_DETAILS['USER_CORE_ID'] = trim($row['USER_CORE_ID']);
		$USER_DETAILS['GENDER'] = trim($row['GENDER']);
		$USER_DETAILS['PHONE'] = trim($row['PHONE']);
		$USER_DETAILS['EMAIL_ADDRESS'] = trim($row['EMAIL_ADDRESS']);
		$USER_DETAILS['TFA_FLG'] = trim($row['TFA_FLG']);
		$USER_DETAILS['LOGGED_IN'] = trim($row['LOGGED_IN']);

		$user_id = $row['USER_ID'];
		$user_roles = GetUserDefinedRoles($user_id);
		$user_role_details = ProcessUserRoleDetails($user_roles);

		$USER_DETAILS['USER_ROLE_DETAILS'] = $user_role_details;

	}

	return $USER_DETAILS;
}

# ... ... ... F5: GetRoleCategoryDetails ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function GetUserDefinedRoles($user_id){
	$user_roles = array();
	$x = 0;
	
	$q = mysql_query("SELECT * FROM upr_usr_roles WHERE USER_ID='$user_id' AND USER_ROLE_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {
			$ROLE_ID = trim($row['ROLE_ID']);
			$user_roles[$x] = $ROLE_ID;
			$x++;
		}

	return $user_roles;
}

# ... ... ... F6: Get Role Details ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetRoleDetails($role_id){
	$ROLE_DETAILS = array();
		$q = mysql_query("SELECT * FROM sys_roles WHERE ROLE_ID='$role_id' AND ROLE_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {
			$ROLE_DETAILS['RECORD_ID'] = trim($row['RECORD_ID']);
			$ROLE_DETAILS['ROLE_ID'] = trim($row['ROLE_ID']);
			$ROLE_DETAILS['ROLE_CAT_ID'] = trim($row['ROLE_CAT_ID']);
			$ROLE_CAT_DETAILS = GetRoleCategoryDetails($row['ROLE_CAT_ID']);		
			$ROLE_DETAILS['ROLE_CAT_NAME'] = $ROLE_CAT_DETAILS['ROLE_CAT_NAME'];
			$ROLE_DETAILS['ROLE_NAME'] = trim($row['ROLE_NAME']);
			$ROLE_DETAILS['F1'] = trim($row['F1']);
			$ROLE_DETAILS['F2'] = trim($row['F2']);
			$ROLE_DETAILS['F3'] = trim($row['F3']);
			$ROLE_DETAILS['F4'] = trim($row['F4']);
			$ROLE_DETAILS['F5'] = trim($row['F5']);
			$ROLE_DETAILS['F6'] = trim($row['F6']);
			$ROLE_DETAILS['F7'] = trim($row['F7']);
			$ROLE_DETAILS['F8'] = trim($row['F8']);
			$ROLE_DETAILS['F9'] = trim($row['F9']);
			$ROLE_DETAILS['F10'] = trim($row['F10']);
			$ROLE_DETAILS['F11'] = trim($row['F11']);
			$ROLE_DETAILS['F12'] = trim($row['F12']);
			$ROLE_DETAILS['F13'] = trim($row['F13']);
			$ROLE_DETAILS['F14'] = trim($row['F14']);
			$ROLE_DETAILS['F15'] = trim($row['F15']);
			$ROLE_DETAILS['F16'] = trim($row['F16']);
			$ROLE_DETAILS['F17'] = trim($row['F17']);
			$ROLE_DETAILS['F18'] = trim($row['F18']);
			$ROLE_DETAILS['F19'] = trim($row['F19']);
			$ROLE_DETAILS['F20'] = trim($row['F20']);
			$ROLE_DETAILS['F21'] = trim($row['F21']);
			$ROLE_DETAILS['F22'] = trim($row['F22']);
			$ROLE_DETAILS['F23'] = trim($row['F23']);
			$ROLE_DETAILS['F24'] = trim($row['F24']);
			$ROLE_DETAILS['F25'] = trim($row['F25']);
			$ROLE_DETAILS['F26'] = trim($row['F26']);
			$ROLE_DETAILS['F27'] = trim($row['F27']);
			$ROLE_DETAILS['F28'] = trim($row['F28']);
			$ROLE_DETAILS['F29'] = trim($row['F29']);
			$ROLE_DETAILS['F30'] = trim($row['F30']);		
			$ROLE_DETAILS['F31'] = trim($row['F31']);
			$ROLE_DETAILS['F32'] = trim($row['F32']);
			$ROLE_DETAILS['F33'] = trim($row['F33']);
			$ROLE_DETAILS['F34'] = trim($row['F34']);
			$ROLE_DETAILS['F35'] = trim($row['F35']);
			$ROLE_DETAILS['F36'] = trim($row['F36']);
			$ROLE_DETAILS['F37'] = trim($row['F37']);
			$ROLE_DETAILS['F38'] = trim($row['F38']);
			$ROLE_DETAILS['F39'] = trim($row['F39']);
			$ROLE_DETAILS['F40'] = trim($row['F40']);
			$ROLE_DETAILS['F41'] = trim($row['F41']);
			$ROLE_DETAILS['F42'] = trim($row['F42']);
			$ROLE_DETAILS['F43'] = trim($row['F43']);
			$ROLE_DETAILS['F44'] = trim($row['F44']);
			$ROLE_DETAILS['F45'] = trim($row['F45']);
			$ROLE_DETAILS['F46'] = trim($row['F46']);
			$ROLE_DETAILS['F47'] = trim($row['F47']);
			$ROLE_DETAILS['F48'] = trim($row['F48']);
			$ROLE_DETAILS['F49'] = trim($row['F49']);
			$ROLE_DETAILS['F50'] = trim($row['F50']);
			$ROLE_DETAILS['F51'] = trim($row['F51']);
			$ROLE_DETAILS['F52'] = trim($row['F52']);
			$ROLE_DETAILS['F53'] = trim($row['F53']);
			$ROLE_DETAILS['F54'] = trim($row['F54']);
			$ROLE_DETAILS['F55'] = trim($row['F55']);
			$ROLE_DETAILS['F56'] = trim($row['F56']);
			$ROLE_DETAILS['F57'] = trim($row['F57']);
			$ROLE_DETAILS['F58'] = trim($row['F58']);
			$ROLE_DETAILS['F59'] = trim($row['F59']);
			$ROLE_DETAILS['F60'] = trim($row['F60']);
			$ROLE_DETAILS['F61'] = trim($row['F61']);
			$ROLE_DETAILS['F62'] = trim($row['F62']);
			$ROLE_DETAILS['F63'] = trim($row['F63']);
			$ROLE_DETAILS['F64'] = trim($row['F64']);
			$ROLE_DETAILS['F65'] = trim($row['F65']);
			$ROLE_DETAILS['F66'] = trim($row['F66']);
			$ROLE_DETAILS['F67'] = trim($row['F67']);
			$ROLE_DETAILS['F68'] = trim($row['F68']);
			$ROLE_DETAILS['F69'] = trim($row['F69']);
			$ROLE_DETAILS['F70'] = trim($row['F70']);
			$ROLE_DETAILS['ROLE_CREATOR'] = trim($row['ROLE_CREATOR']);
			$ROLE_DETAILS['ROLE_CREATION_DATE'] = trim($row['ROLE_CREATION_DATE']);
			$ROLE_DETAILS['ROLE_APPROVER'] = trim($row['ROLE_APPROVER']);
			$ROLE_DETAILS['ROLE_APPROVAL_DATE'] = trim($row['ROLE_APPROVAL_DATE']);
			$ROLE_DETAILS['ROLE_LST_CHNG_BY'] = trim($row['ROLE_LST_CHNG_BY']);
			$ROLE_DETAILS['ROLE_LST_CHNG_ONT'] = trim($row['ROLE_LST_CHNG_ON']);
			$ROLE_DETAILS['ROLE_STATUS'] = trim($row['ROLE_STATUS']);

			


		}

	return $ROLE_DETAILS;
}

# ... ... ... F7: GetRoleCategoryDetails ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetRoleCategoryDetails($role_cat_id){
	$ROLE_CAT_DETAILS = array();
	
	$q = mysql_query("SELECT * FROM sys_roles_categories WHERE ROLE_CAT_ID='$role_cat_id' AND ROLE_CAT_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {
			$ROLE_CAT_DETAILS['ROLE_CAT_ID'] = trim($row['ROLE_CAT_ID']);
			$ROLE_CAT_DETAILS['ROLE_CAT_NAME'] = trim($row['ROLE_CAT_NAME']);
		}

	return $ROLE_CAT_DETAILS;
}

# ... ... ... F8: ProcessUserRoleDetails ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ProcessUserRoleDetails($user_roles){
	$user_role_details = array();
	$mother_array = array();
	$proc_roles = array();

	# ... 01: Get all Role features for give role ids
	for ($i=0; $i < sizeof($user_roles); $i++) { 
		$user_role_id = $user_roles[$i];

    $role_details = GetRoleDetails($user_role_id);
    $mother_array[$i] = $role_details;
	}

	# ... 02: Extracting Roles per feature
	$roles_assigned = sizeof($mother_array);			# ... No. of roles assigned to a given user
	$sys_features_designed = 70; 									# ... No. of system designed features
  for ($x=0; $x < $sys_features_designed; $x++) { 
  	
  	$feature_id = "F".($x+1);
  	$feature_comb = "";
  	for ($y=0; $y < $roles_assigned; $y++) {
  		$feature_set = $mother_array[$y];
  		$feature_value = $feature_set[$feature_id];

  		if ($feature_comb=="") {
  			$feature_comb = $feature_value;
  		}
  		else{
  			$feature_comb = $feature_comb."#".$feature_value;
  		}
  	}

  	$proc_roles[$feature_id] = $feature_comb;
  }


  # ... 03: Compute final feature values
  for ($z=0; $z < sizeof($proc_roles); $z++) { 
  	$feature_id = "F".($z+1);
  	$feature_val = $proc_roles[$feature_id];

  	$data_ref = explode('#', $feature_val);
  	$lookup_value="YES";
  	$val = CheckForStringValue($data_ref, $lookup_value);

  	if ($val=="TRUE") {
  		$user_role_details[$feature_id] = "YES";
  	}
  	else {
  		$user_role_details[$feature_id] = "NO";
  	}

  }

	return $user_role_details;
}

# ... ... ... F9: CheckForStringValue ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function CheckForStringValue($data_ref, $lookup_value){
	$RTN_VALUE = "FALSE";

	if ( in_array($lookup_value, $data_ref) )
  {
  	$RTN_VALUE = "TRUE";
  }
	else
  {
  	$RTN_VALUE = "FALSE";
  }
	return $RTN_VALUE;
}

# ... ... ... F10: Flag User LogIn Status ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FlagUserLogInStatus($USER_ID, $LOGGED_IN_FLG){
	$is_logged= "NO";
	$q = mysql_query("UPDATE upr SET LOGGED_IN='$LOGGED_IN_FLG' WHERE USER_ID='$USER_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	if($q)
	{
		$is_logged = "YES";
	}

	return $is_logged;
} 

# ... ... ... F11: Return one Entry from DB ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ReturnOneEntryFromDB($DB_QUERY){
	$RTN_VALUE = "";

	$q = mysql_query($DB_QUERY) or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$RTN_VALUE = trim($row['RTN_VALUE']);
	}

	return $RTN_VALUE;
}

# ... ... ... F12: Perform DataChecks On Request ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function PerformDataChecksOnRequest($EMAIL, $MOBILE_NO, $WORK_ID, $NATIONAL_ID)
{
	$data_check_results = array();
	$data_chk_res = "";

	# ... Email
	$q1 = "SELECT COUNT(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE EMAIL='$EMAIL' AND ACTIVATION_STATUS!='REJECTED'";
	$q1_cnt = ReturnOneEntryFromDB($q1);
	if ($q1_cnt > 0) {
		$data_check_results["EMAIL_CHK"] = false;
		$data_chk_res = $data_chk_res . " Email supplied is already registered to another request.<br>";
	} else {
		$data_check_results["EMAIL_CHK"] = true;
	}

	# ... Mobile Phone Number
	$q2 = "SELECT COUNT(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE MOBILE_NO='$MOBILE_NO' AND ACTIVATION_STATUS!='REJECTED'";
	$q2_cnt = ReturnOneEntryFromDB($q2);
	if ($q2_cnt > 0) {
		$data_check_results["MOBILENO_CHK"] = false;
		$data_chk_res = $data_chk_res . " Mobile Phone supplied is already registered to another request.<br>";
	} else {
		$data_check_results["MOBILENO_CHK"] = true;
	}

	# ... Work ID
	$q3 = "SELECT COUNT(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE WORK_ID='$WORK_ID' AND ACTIVATION_STATUS!='REJECTED'";
	$q3_cnt = ReturnOneEntryFromDB($q3);
	if ($q3_cnt > 0) {
		$data_check_results["WORKID_CHK"] = false;
		$data_chk_res = $data_chk_res . " WorkID/StaffID supplied is already registered to another request.<br>";
	} else {
		$data_check_results["WORKID_CHK"] = true;
	}

	# ... National ID
	$q4 = "SELECT COUNT(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE NATIONAL_ID='$NATIONAL_ID' AND ACTIVATION_STATUS!='REJECTED'";
	$q4_cnt = ReturnOneEntryFromDB($q4);
	if ($q4_cnt > 0) {
		$data_check_results["NATIONALID_CHK"] = false;
		$data_chk_res = $data_chk_res . " National_ID supplied is already registered to another request.<br>";
	} else {
		$data_check_results["NATIONALID_CHK"] = true;
	}


	$data_check_results["RESULT_RMKS"] = $data_chk_res;
	return $data_check_results;
}


# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Role Maintenance **..** **..** **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Role Maintenance **..** **..** **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Role Maintenance **..** **..** **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Role Maintenance **..** **..** **..** **..** **..** **..** **..**  **..** **..** 


# ... ... ... F12: Fetch All System Roles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetAllUserSystemRoles($ROLE_CAT_ID){
	$sys_roles_list = array();	
	$x = 0;
	
	$q = mysql_query("SELECT * FROM sys_roles WHERE ROLE_CAT_ID='$ROLE_CAT_ID' AND ROLE_STATUS='ACTIVE' ORDER BY RECORD_ID ASC") or die("ERROR ROLES: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {

			$sys_role = array();
			$sys_role['RECORD_ID'] = trim($row['RECORD_ID']);
			$sys_role['ROLE_ID'] = trim($row['ROLE_ID']);
			$sys_role['ROLE_CAT_ID'] = trim($row['ROLE_CAT_ID']);
			$sys_role['ROLE_NAME'] = trim($row['ROLE_NAME']);
			$sys_role['F1'] = trim($row['F1']);
			$sys_role['F2'] = trim($row['F2']);
			$sys_role['F3'] = trim($row['F3']);
			$sys_role['F4'] = trim($row['F4']);
			$sys_role['F5'] = trim($row['F5']);
			$sys_role['F6'] = trim($row['F6']);
			$sys_role['F7'] = trim($row['F7']);
			$sys_role['F8'] = trim($row['F8']);
			$sys_role['F9'] = trim($row['F9']);
			$sys_role['F10'] = trim($row['F10']);
			$sys_role['F11'] = trim($row['F11']);
			$sys_role['F12'] = trim($row['F12']);
			$sys_role['F13'] = trim($row['F13']);
			$sys_role['F14'] = trim($row['F14']);
			$sys_role['F15'] = trim($row['F15']);
			$sys_role['F16'] = trim($row['F16']);
			$sys_role['F17'] = trim($row['F17']);
			$sys_role['F18'] = trim($row['F18']);
			$sys_role['F19'] = trim($row['F19']);
			$sys_role['F20'] = trim($row['F20']);
			$sys_role['F21'] = trim($row['F21']);
			$sys_role['F22'] = trim($row['F22']);
			$sys_role['F23'] = trim($row['F23']);
			$sys_role['F24'] = trim($row['F24']);
			$sys_role['F25'] = trim($row['F25']);
			$sys_role['F26'] = trim($row['F26']);
			$sys_role['F27'] = trim($row['F27']);
			$sys_role['F28'] = trim($row['F28']);
			$sys_role['F29'] = trim($row['F29']);
			$sys_role['F30'] = trim($row['F30']);
			$sys_role['F31'] = trim($row['F31']);
			$sys_role['F32'] = trim($row['F32']);
			$sys_role['F33'] = trim($row['F33']);
			$sys_role['F34'] = trim($row['F34']);
			$sys_role['F35'] = trim($row['F35']);
			$sys_role['F36'] = trim($row['F36']);
			$sys_role['F37'] = trim($row['F37']);
			$sys_role['F38'] = trim($row['F38']);
			$sys_role['F39'] = trim($row['F39']);
			$sys_role['F40'] = trim($row['F40']);
			$sys_role['F41'] = trim($row['F41']);
			$sys_role['F42'] = trim($row['F42']);
			$sys_role['F43'] = trim($row['F43']);
			$sys_role['F44'] = trim($row['F44']);
			$sys_role['F45'] = trim($row['F45']);
			$sys_role['F46'] = trim($row['F46']);
			$sys_role['F47'] = trim($row['F47']);
			$sys_role['F48'] = trim($row['F48']);
			$sys_role['F49'] = trim($row['F49']);
			$sys_role['F50'] = trim($row['F50']);
			$sys_role['F51'] = trim($row['F51']);
			$sys_role['F52'] = trim($row['F52']);
			$sys_role['F53'] = trim($row['F53']);
			$sys_role['F54'] = trim($row['F54']);
			$sys_role['F55'] = trim($row['F55']);
			$sys_role['F56'] = trim($row['F56']);
			$sys_role['F57'] = trim($row['F57']);
			$sys_role['F58'] = trim($row['F58']);
			$sys_role['F59'] = trim($row['F59']);
			$sys_role['F60'] = trim($row['F60']);
			$sys_role['F61'] = trim($row['F61']);
			$sys_role['F62'] = trim($row['F62']);
			$sys_role['F63'] = trim($row['F63']);
			$sys_role['F64'] = trim($row['F64']);
			$sys_role['F65'] = trim($row['F65']);
			$sys_role['F66'] = trim($row['F66']);
			$sys_role['F67'] = trim($row['F67']);
			$sys_role['F68'] = trim($row['F68']);
			$sys_role['F69'] = trim($row['F69']);
			$sys_role['F70'] = trim($row['F70']);
			$sys_role['ROLE_CREATOR'] = trim($row['ROLE_CREATOR']);
			$sys_role['ROLE_CREATION_DATE'] = trim($row['ROLE_CREATION_DATE']);
			$sys_role['ROLE_APPROVER'] = trim($row['ROLE_APPROVER']);
			$sys_role['ROLE_APPROVAL_DATE'] = trim($row['ROLE_APPROVAL_DATE']);
			$sys_role['ROLE_LST_CHNG_BY'] = trim($row['ROLE_LST_CHNG_BY']);
			$sys_role['ROLE_LST_CHNG_ON'] = trim($row['ROLE_LST_CHNG_ON']);
			$sys_role['ROLE_STATUS'] = trim($row['ROLE_STATUS']);


			$sys_roles_list[$x] = $sys_role;
			$x++;
		}

	return $sys_roles_list;
}

# ... ... ... F13: Get Menu Level 01 ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetMenuLevel_01($FTR_TYPE){
	$MENU_LEVEL_01_LIST = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM sys_menu_level1 WHERE FTR_TYPE='$FTR_TYPE' AND FTR_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {	
		$MENU_LEVEL_01 = array();
		$MENU_LEVEL_01['RECORD_ID'] = trim($row['RECORD_ID']);
		$MENU_LEVEL_01['FTR_ID'] = trim($row['FTR_ID']);
		$MENU_LEVEL_01['FTR_NAME'] = trim($row['FTR_NAME']);
		$MENU_LEVEL_01['FTR_TYPE'] = trim($row['FTR_TYPE']);
		$MENU_LEVEL_01['FTR_STATUS'] = trim($row['FTR_STATUS']);

		$MENU_LEVEL_01_LIST[$x] = $MENU_LEVEL_01;
		$x++;
	}

	return $MENU_LEVEL_01_LIST;
}

# ... ... ... F14: Get Menu Level 02 ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetMenuLevel_02($FTR_ID){
	$MENU_LEVEL_02_LIST = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM sys_menu_level2 WHERE FTR_ID='$FTR_ID' AND SUB_FTR_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {	
		$MENU_LEVEL_02 = array();
		$MENU_LEVEL_02['RECORD_ID'] = trim($row['RECORD_ID']);
		$MENU_LEVEL_02['SUB_FTR_ID'] = trim($row['SUB_FTR_ID']);
		$MENU_LEVEL_02['FTR_ID'] = trim($row['FTR_ID']);
		$MENU_LEVEL_02['SUB_FTR_NAME'] = trim($row['SUB_FTR_NAME']);
		$MENU_LEVEL_02['SUB_FTR_STATUS'] = trim($row['SUB_FTR_STATUS']);

		$MENU_LEVEL_02_LIST[$x] = $MENU_LEVEL_02;
		$x++;
	}

	return $MENU_LEVEL_02_LIST;
}

# ... ... ... F15: Get Menu Level 03 ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetMenuLevel_03($SUB_FTR_ID){
	$MENU_LEVEL_03_LIST = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM sys_menu_level3 WHERE SUB_FTR_ID='$SUB_FTR_ID' AND BTM_FTR_STATUS='ACTIVE'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {	
		$MENU_LEVEL_03 = array();
		$MENU_LEVEL_03['RECORD_ID'] = trim($row['RECORD_ID']);
		$MENU_LEVEL_03['BTM_FTR_ID'] = trim($row['BTM_FTR_ID']);
		$MENU_LEVEL_03['SUB_FTR_ID'] = trim($row['SUB_FTR_ID']);
		$MENU_LEVEL_03['BTM_FTR_NAME'] = trim($row['BTM_FTR_NAME']);
		$MENU_LEVEL_03['BTM_FTR_STATUS'] = trim($row['BTM_FTR_STATUS']);

		$MENU_LEVEL_03_LIST[$x] = $MENU_LEVEL_03;
		$x++;
	}

	return $MENU_LEVEL_03_LIST;
}

# ... ... ... F16: Execute Entity Insert ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function ExecuteEntityInsert($QUERY){
	$exec_response = array();
	$q = mysql_query($QUERY) or die("ERROR 1: ".mysql_error());
	if ($q) {
		$exec_response["RESP"] = "EXECUTED";
		$exec_response["RECORD_ID"] = mysql_insert_id();
	}

	return $exec_response;
}

# ... ... ... F17: Execute Entity Update ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function ExecuteEntityUpdate($QUERY){
	$update_response = "";
	$q = mysql_query($QUERY) or die("ERROR 1: ".mysql_error());
	if ($q) {
		$update_response = "EXECUTED";
	}
	return $update_response;
}

# ... ... ... F18: Execute Entity Delete ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function ExecuteEntityDelete($TABLE, $TABLE_RECORD_ID){
	$delete_response = array();
	$TABLE_ROW = "";

	# ... Get the row records to be deleted
	$q_sel = mysql_query("SELECT * FROM $TABLE WHERE RECORD_ID='$TABLE_RECORD_ID'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q_sel)) {	
		
		$num_fields = mysql_num_fields($q_sel);

		for ($i=0; $i < $num_fields; $i++) { 
			if ($TABLE_ROW=="") {
				$TABLE_ROW = trim($row[$i]);
			}
			else{
				$TABLE_ROW = $TABLE_ROW."|".trim($row[$i]);
			}
		}
		

	}

	# ... Delete the record from Table
	$q_del = mysql_query("DELETE FROM $TABLE WHERE RECORD_ID='$TABLE_RECORD_ID'") or die("ERROR 1: ".mysql_error());
	if ($q_del) {
		$delete_response["DEL_FLG"] = "Y";
	}

	# ... Packaging the delete response
	$delete_response["DEL_ROW"] = $TABLE_ROW;

	return $delete_response;
}

# ... ... ... F19: Fetch All System Roles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetAllUserSystemRolesWithStatus($ROLE_STATUS){
	$sys_roles_list = array();	
	$x = 0;
	
	$q = mysql_query("SELECT * FROM sys_roles WHERE ROLE_STATUS='$ROLE_STATUS'") or die("ERROR ROLES: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {

			$sys_role = array();
			$sys_role['RECORD_ID'] = trim($row['RECORD_ID']);
			$sys_role['ROLE_ID'] = trim($row['ROLE_ID']);
			$sys_role['ROLE_CAT_ID'] = trim($row['ROLE_CAT_ID']);
			$sys_role['ROLE_NAME'] = trim($row['ROLE_NAME']);
			$sys_role['F1'] = trim($row['F1']);
			$sys_role['F2'] = trim($row['F2']);
			$sys_role['F3'] = trim($row['F3']);
			$sys_role['F4'] = trim($row['F4']);
			$sys_role['F5'] = trim($row['F5']);
			$sys_role['F6'] = trim($row['F6']);
			$sys_role['F7'] = trim($row['F7']);
			$sys_role['F8'] = trim($row['F8']);
			$sys_role['F9'] = trim($row['F9']);
			$sys_role['F10'] = trim($row['F10']);
			$sys_role['F11'] = trim($row['F11']);
			$sys_role['F12'] = trim($row['F12']);
			$sys_role['F13'] = trim($row['F13']);
			$sys_role['F14'] = trim($row['F14']);
			$sys_role['F15'] = trim($row['F15']);
			$sys_role['F16'] = trim($row['F16']);
			$sys_role['F17'] = trim($row['F17']);
			$sys_role['F18'] = trim($row['F18']);
			$sys_role['F19'] = trim($row['F19']);
			$sys_role['F20'] = trim($row['F20']);
			$sys_role['F21'] = trim($row['F21']);
			$sys_role['F22'] = trim($row['F22']);
			$sys_role['F23'] = trim($row['F23']);
			$sys_role['F24'] = trim($row['F24']);
			$sys_role['F25'] = trim($row['F25']);
			$sys_role['F26'] = trim($row['F26']);
			$sys_role['F27'] = trim($row['F27']);
			$sys_role['F28'] = trim($row['F28']);
			$sys_role['F29'] = trim($row['F29']);
			$sys_role['F30'] = trim($row['F30']);
			$sys_role['F31'] = trim($row['F31']);
			$sys_role['F32'] = trim($row['F32']);
			$sys_role['F33'] = trim($row['F33']);
			$sys_role['F34'] = trim($row['F34']);
			$sys_role['F35'] = trim($row['F35']);
			$sys_role['F36'] = trim($row['F36']);
			$sys_role['F37'] = trim($row['F37']);
			$sys_role['F38'] = trim($row['F38']);
			$sys_role['F39'] = trim($row['F39']);
			$sys_role['F40'] = trim($row['F40']);
			$sys_role['F41'] = trim($row['F41']);
			$sys_role['F42'] = trim($row['F42']);
			$sys_role['F43'] = trim($row['F43']);
			$sys_role['F44'] = trim($row['F44']);
			$sys_role['F45'] = trim($row['F45']);
			$sys_role['F46'] = trim($row['F46']);
			$sys_role['F47'] = trim($row['F47']);
			$sys_role['F48'] = trim($row['F48']);
			$sys_role['F49'] = trim($row['F49']);
			$sys_role['F50'] = trim($row['F50']);
			$sys_role['F51'] = trim($row['F51']);
			$sys_role['F52'] = trim($row['F52']);
			$sys_role['F53'] = trim($row['F53']);
			$sys_role['F54'] = trim($row['F54']);
			$sys_role['F55'] = trim($row['F55']);
			$sys_role['F56'] = trim($row['F56']);
			$sys_role['F57'] = trim($row['F57']);
			$sys_role['F58'] = trim($row['F58']);
			$sys_role['F59'] = trim($row['F59']);
			$sys_role['F60'] = trim($row['F60']);
			$sys_role['F61'] = trim($row['F61']);
			$sys_role['F62'] = trim($row['F62']);
			$sys_role['F63'] = trim($row['F63']);
			$sys_role['F64'] = trim($row['F64']);
			$sys_role['F65'] = trim($row['F65']);
			$sys_role['F66'] = trim($row['F66']);
			$sys_role['F67'] = trim($row['F67']);
			$sys_role['F68'] = trim($row['F68']);
			$sys_role['F69'] = trim($row['F69']);
			$sys_role['F70'] = trim($row['F70']);
			$sys_role['ROLE_CREATOR'] = trim($row['ROLE_CREATOR']);
			$sys_role['ROLE_CREATION_DATE'] = trim($row['ROLE_CREATION_DATE']);
			$sys_role['ROLE_APPROVER'] = trim($row['ROLE_APPROVER']);
			$sys_role['ROLE_APPROVAL_DATE'] = trim($row['ROLE_APPROVAL_DATE']);
			$sys_role['ROLE_LST_CHNG_BY'] = trim($row['ROLE_LST_CHNG_BY']);
			$sys_role['ROLE_LST_CHNG_ON'] = trim($row['ROLE_LST_CHNG_ON']);
			$sys_role['ROLE_STATUS'] = trim($row['ROLE_STATUS']);


			$sys_roles_list[$x] = $sys_role;
			$x++;
		}

	return $sys_roles_list;
}

# ... ... ... F20: Get Cust Details From Portal ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function GetUserCoreIdFromWebApp($USER_ID){
	$CORE_USER_ID = "";

	$q = mysql_query("SELECT * FROM upr WHERE USER_ID='$USER_ID'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$CORE_USER_ID = trim($row['USER_CORE_ID']);
	}

	return $CORE_USER_ID;
}

# ... ... ... F21: Get Role Details Ignore Status ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... .... ... ...
function GetRoleDetailsIgnoreStatus($role_id){
	$ROLE_DETAILS = array();
		$q = mysql_query("SELECT * FROM sys_roles WHERE ROLE_ID='$role_id'") or die("ERROR 1: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {
			$ROLE_DETAILS['RECORD_ID'] = trim($row['RECORD_ID']);
			$ROLE_DETAILS['ROLE_ID'] = trim($row['ROLE_ID']);
			$ROLE_DETAILS['ROLE_CAT_ID'] = trim($row['ROLE_CAT_ID']);
			$ROLE_CAT_DETAILS = GetRoleCategoryDetails($row['ROLE_CAT_ID']);		
			$ROLE_DETAILS['ROLE_CAT_NAME'] = $ROLE_CAT_DETAILS['ROLE_CAT_NAME'];
			$ROLE_DETAILS['ROLE_NAME'] = trim($row['ROLE_NAME']);
			$ROLE_DETAILS['F1'] = trim($row['F1']);
			$ROLE_DETAILS['F2'] = trim($row['F2']);
			$ROLE_DETAILS['F3'] = trim($row['F3']);
			$ROLE_DETAILS['F4'] = trim($row['F4']);
			$ROLE_DETAILS['F5'] = trim($row['F5']);
			$ROLE_DETAILS['F6'] = trim($row['F6']);
			$ROLE_DETAILS['F7'] = trim($row['F7']);
			$ROLE_DETAILS['F8'] = trim($row['F8']);
			$ROLE_DETAILS['F9'] = trim($row['F9']);
			$ROLE_DETAILS['F10'] = trim($row['F10']);
			$ROLE_DETAILS['F11'] = trim($row['F11']);
			$ROLE_DETAILS['F12'] = trim($row['F12']);
			$ROLE_DETAILS['F13'] = trim($row['F13']);
			$ROLE_DETAILS['F14'] = trim($row['F14']);
			$ROLE_DETAILS['F15'] = trim($row['F15']);
			$ROLE_DETAILS['F16'] = trim($row['F16']);
			$ROLE_DETAILS['F17'] = trim($row['F17']);
			$ROLE_DETAILS['F18'] = trim($row['F18']);
			$ROLE_DETAILS['F19'] = trim($row['F19']);
			$ROLE_DETAILS['F20'] = trim($row['F20']);
			$ROLE_DETAILS['F21'] = trim($row['F21']);
			$ROLE_DETAILS['F22'] = trim($row['F22']);
			$ROLE_DETAILS['F23'] = trim($row['F23']);
			$ROLE_DETAILS['F24'] = trim($row['F24']);
			$ROLE_DETAILS['F25'] = trim($row['F25']);
			$ROLE_DETAILS['F26'] = trim($row['F26']);
			$ROLE_DETAILS['F27'] = trim($row['F27']);
			$ROLE_DETAILS['F28'] = trim($row['F28']);
			$ROLE_DETAILS['F29'] = trim($row['F29']);
			$ROLE_DETAILS['F30'] = trim($row['F30']);		
			$ROLE_DETAILS['F31'] = trim($row['F31']);
			$ROLE_DETAILS['F32'] = trim($row['F32']);
			$ROLE_DETAILS['F33'] = trim($row['F33']);
			$ROLE_DETAILS['F34'] = trim($row['F34']);
			$ROLE_DETAILS['F35'] = trim($row['F35']);
			$ROLE_DETAILS['F36'] = trim($row['F36']);
			$ROLE_DETAILS['F37'] = trim($row['F37']);
			$ROLE_DETAILS['F38'] = trim($row['F38']);
			$ROLE_DETAILS['F39'] = trim($row['F39']);
			$ROLE_DETAILS['F40'] = trim($row['F40']);
			$ROLE_DETAILS['F41'] = trim($row['F41']);
			$ROLE_DETAILS['F42'] = trim($row['F42']);
			$ROLE_DETAILS['F43'] = trim($row['F43']);
			$ROLE_DETAILS['F44'] = trim($row['F44']);
			$ROLE_DETAILS['F45'] = trim($row['F45']);
			$ROLE_DETAILS['F46'] = trim($row['F46']);
			$ROLE_DETAILS['F47'] = trim($row['F47']);
			$ROLE_DETAILS['F48'] = trim($row['F48']);
			$ROLE_DETAILS['F49'] = trim($row['F49']);
			$ROLE_DETAILS['F50'] = trim($row['F50']);
			$ROLE_DETAILS['F51'] = trim($row['F51']);
			$ROLE_DETAILS['F52'] = trim($row['F52']);
			$ROLE_DETAILS['F53'] = trim($row['F53']);
			$ROLE_DETAILS['F54'] = trim($row['F54']);
			$ROLE_DETAILS['F55'] = trim($row['F55']);
			$ROLE_DETAILS['F56'] = trim($row['F56']);
			$ROLE_DETAILS['F57'] = trim($row['F57']);
			$ROLE_DETAILS['F58'] = trim($row['F58']);
			$ROLE_DETAILS['F59'] = trim($row['F59']);
			$ROLE_DETAILS['F60'] = trim($row['F60']);
			$ROLE_DETAILS['F61'] = trim($row['F61']);
			$ROLE_DETAILS['F62'] = trim($row['F62']);
			$ROLE_DETAILS['F63'] = trim($row['F63']);
			$ROLE_DETAILS['F64'] = trim($row['F64']);
			$ROLE_DETAILS['F65'] = trim($row['F65']);
			$ROLE_DETAILS['F66'] = trim($row['F66']);
			$ROLE_DETAILS['F67'] = trim($row['F67']);
			$ROLE_DETAILS['F68'] = trim($row['F68']);
			$ROLE_DETAILS['F69'] = trim($row['F69']);
			$ROLE_DETAILS['F70'] = trim($row['F70']);
			$ROLE_DETAILS['ROLE_CREATOR'] = trim($row['ROLE_CREATOR']);
			$ROLE_DETAILS['ROLE_CREATION_DATE'] = trim($row['ROLE_CREATION_DATE']);
			$ROLE_DETAILS['ROLE_APPROVER'] = trim($row['ROLE_APPROVER']);
			$ROLE_DETAILS['ROLE_APPROVAL_DATE'] = trim($row['ROLE_APPROVAL_DATE']);
			$ROLE_DETAILS['ROLE_LST_CHNG_BY'] = trim($row['ROLE_LST_CHNG_BY']);
			$ROLE_DETAILS['ROLE_LST_CHNG_ONT'] = trim($row['ROLE_LST_CHNG_ON']);
			$ROLE_DETAILS['ROLE_STATUS'] = trim($row['ROLE_STATUS']);

			


		}

	return $ROLE_DETAILS;
}

# ... ... ... F22: Get Pending System Roles Changes ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function GetPendingSystemRolesChanges(){
	$sys_roles_chng_list = array();	
	$x = 0;
	
	$q = mysql_query("SELECT * FROM sys_roles_chng_log WHERE CHNG_STATUS='PENDING' ORDER BY RECORD_ID ASC") or die("ERROR ROLES: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {

			$sys_role_chng = array();
			$sys_role_chng['RECORD_ID'] = trim($row['RECORD_ID']);
			$sys_role_chng['ROLE_ID'] = trim($row['ROLE_ID']);			
			$sys_role_chng['CHNG_INIT_DATE'] = trim($row['CHNG_INIT_DATE']);
			$sys_role_chng['CHNG_INIT_BY'] = trim($row['CHNG_INIT_BY']);
			$sys_role_chng['CHNG_VERIF_DATE'] = trim($row['CHNG_VERIF_DATE']);
			$sys_role_chng['CHNG_VERIF_BY'] = trim($row['CHNG_VERIF_BY']);
			$sys_role_chng['CHNG_STATUS'] = trim($row['CHNG_STATUS']);

			$sys_roles_chng_list[$x] = $sys_role_chng;
			$x++;
		}

	return $sys_roles_chng_list;
}

# ... ... ... F23: Get Sys Role Change Details ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetSysRoleChangeDetails($RECORD_ID){
	$sys_role_chng = array();	
	
	$q = mysql_query("SELECT * FROM sys_roles_chng_log WHERE RECORD_ID='$RECORD_ID'") or die("ERROR ROLES: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {

			$sys_role_chng['RECORD_ID'] = trim($row['RECORD_ID']);
			$sys_role_chng['ROLE_ID'] = trim($row['ROLE_ID']);
			$sys_role_chng['OLD_F1'] = trim($row['OLD_F1']);
			$sys_role_chng['OLD_F2'] = trim($row['OLD_F2']);
			$sys_role_chng['OLD_F3'] = trim($row['OLD_F3']);
			$sys_role_chng['OLD_F4'] = trim($row['OLD_F4']);
			$sys_role_chng['OLD_F5'] = trim($row['OLD_F5']);
			$sys_role_chng['OLD_F6'] = trim($row['OLD_F6']);
			$sys_role_chng['OLD_F7'] = trim($row['OLD_F7']);
			$sys_role_chng['OLD_F8'] = trim($row['OLD_F8']);
			$sys_role_chng['OLD_F9'] = trim($row['OLD_F9']);
			$sys_role_chng['OLD_F10'] = trim($row['OLD_F10']);
			$sys_role_chng['OLD_F11'] = trim($row['OLD_F11']);
			$sys_role_chng['OLD_F12'] = trim($row['OLD_F12']);
			$sys_role_chng['OLD_F13'] = trim($row['OLD_F13']);
			$sys_role_chng['OLD_F14'] = trim($row['OLD_F14']);
			$sys_role_chng['OLD_F15'] = trim($row['OLD_F15']);
			$sys_role_chng['OLD_F16'] = trim($row['OLD_F16']);
			$sys_role_chng['OLD_F17'] = trim($row['OLD_F17']);
			$sys_role_chng['OLD_F18'] = trim($row['OLD_F18']);
			$sys_role_chng['OLD_F19'] = trim($row['OLD_F19']);
			$sys_role_chng['OLD_F20'] = trim($row['OLD_F20']);
			$sys_role_chng['OLD_F21'] = trim($row['OLD_F21']);
			$sys_role_chng['OLD_F22'] = trim($row['OLD_F22']);
			$sys_role_chng['OLD_F23'] = trim($row['OLD_F23']);
			$sys_role_chng['OLD_F24'] = trim($row['OLD_F24']);
			$sys_role_chng['OLD_F25'] = trim($row['OLD_F25']);
			$sys_role_chng['OLD_F26'] = trim($row['OLD_F26']);
			$sys_role_chng['OLD_F27'] = trim($row['OLD_F27']);
			$sys_role_chng['OLD_F28'] = trim($row['OLD_F28']);
			$sys_role_chng['OLD_F29'] = trim($row['OLD_F29']);
			$sys_role_chng['OLD_F30'] = trim($row['OLD_F30']);
			$sys_role_chng['OLD_F31'] = trim($row['OLD_F31']);
			$sys_role_chng['OLD_F32'] = trim($row['OLD_F32']);
			$sys_role_chng['OLD_F33'] = trim($row['OLD_F33']);
			$sys_role_chng['OLD_F34'] = trim($row['OLD_F34']);
			$sys_role_chng['OLD_F35'] = trim($row['OLD_F35']);
			$sys_role_chng['OLD_F36'] = trim($row['OLD_F36']);
			$sys_role_chng['OLD_F37'] = trim($row['OLD_F37']);
			$sys_role_chng['OLD_F38'] = trim($row['OLD_F38']);
			$sys_role_chng['OLD_F39'] = trim($row['OLD_F39']);
			$sys_role_chng['OLD_F40'] = trim($row['OLD_F40']);
			$sys_role_chng['OLD_F41'] = trim($row['OLD_F41']);
			$sys_role_chng['OLD_F42'] = trim($row['OLD_F42']);
			$sys_role_chng['OLD_F43'] = trim($row['OLD_F43']);
			$sys_role_chng['OLD_F44'] = trim($row['OLD_F44']);
			$sys_role_chng['OLD_F45'] = trim($row['OLD_F45']);
			$sys_role_chng['OLD_F46'] = trim($row['OLD_F46']);
			$sys_role_chng['OLD_F47'] = trim($row['OLD_F47']);
			$sys_role_chng['OLD_F48'] = trim($row['OLD_F48']);
			$sys_role_chng['OLD_F49'] = trim($row['OLD_F49']);
			$sys_role_chng['OLD_F50'] = trim($row['OLD_F50']);
			$sys_role_chng['OLD_F51'] = trim($row['OLD_F51']);
			$sys_role_chng['OLD_F52'] = trim($row['OLD_F52']);
			$sys_role_chng['OLD_F53'] = trim($row['OLD_F53']);
			$sys_role_chng['OLD_F54'] = trim($row['OLD_F54']);
			$sys_role_chng['OLD_F55'] = trim($row['OLD_F55']);
			$sys_role_chng['OLD_F56'] = trim($row['OLD_F56']);
			$sys_role_chng['OLD_F57'] = trim($row['OLD_F57']);
			$sys_role_chng['OLD_F58'] = trim($row['OLD_F58']);
			$sys_role_chng['OLD_F59'] = trim($row['OLD_F59']);
			$sys_role_chng['OLD_F60'] = trim($row['OLD_F60']);
			$sys_role_chng['OLD_F61'] = trim($row['OLD_F61']);
			$sys_role_chng['OLD_F62'] = trim($row['OLD_F62']);
			$sys_role_chng['OLD_F63'] = trim($row['OLD_F63']);
			$sys_role_chng['OLD_F64'] = trim($row['OLD_F64']);
			$sys_role_chng['OLD_F65'] = trim($row['OLD_F65']);
			$sys_role_chng['OLD_F66'] = trim($row['OLD_F66']);
			$sys_role_chng['OLD_F67'] = trim($row['OLD_F67']);
			$sys_role_chng['OLD_F68'] = trim($row['OLD_F68']);
			$sys_role_chng['OLD_F69'] = trim($row['OLD_F69']);
			$sys_role_chng['OLD_F70'] = trim($row['OLD_F70']);
			$sys_role_chng['NEW_F1'] = trim($row['NEW_F1']);
			$sys_role_chng['NEW_F2'] = trim($row['NEW_F2']);
			$sys_role_chng['NEW_F3'] = trim($row['NEW_F3']);
			$sys_role_chng['NEW_F4'] = trim($row['NEW_F4']);
			$sys_role_chng['NEW_F5'] = trim($row['NEW_F5']);
			$sys_role_chng['NEW_F6'] = trim($row['NEW_F6']);
			$sys_role_chng['NEW_F7'] = trim($row['NEW_F7']);
			$sys_role_chng['NEW_F8'] = trim($row['NEW_F8']);
			$sys_role_chng['NEW_F9'] = trim($row['NEW_F9']);
			$sys_role_chng['NEW_F10'] = trim($row['NEW_F10']);
			$sys_role_chng['NEW_F11'] = trim($row['NEW_F11']);
			$sys_role_chng['NEW_F12'] = trim($row['NEW_F12']);
			$sys_role_chng['NEW_F13'] = trim($row['NEW_F13']);
			$sys_role_chng['NEW_F14'] = trim($row['NEW_F14']);
			$sys_role_chng['NEW_F15'] = trim($row['NEW_F15']);
			$sys_role_chng['NEW_F16'] = trim($row['NEW_F16']);
			$sys_role_chng['NEW_F17'] = trim($row['NEW_F17']);
			$sys_role_chng['NEW_F18'] = trim($row['NEW_F18']);
			$sys_role_chng['NEW_F19'] = trim($row['NEW_F19']);
			$sys_role_chng['NEW_F20'] = trim($row['NEW_F20']);
			$sys_role_chng['NEW_F21'] = trim($row['NEW_F21']);
			$sys_role_chng['NEW_F22'] = trim($row['NEW_F22']);
			$sys_role_chng['NEW_F23'] = trim($row['NEW_F23']);
			$sys_role_chng['NEW_F24'] = trim($row['NEW_F24']);
			$sys_role_chng['NEW_F25'] = trim($row['NEW_F25']);
			$sys_role_chng['NEW_F26'] = trim($row['NEW_F26']);
			$sys_role_chng['NEW_F27'] = trim($row['NEW_F27']);
			$sys_role_chng['NEW_F28'] = trim($row['NEW_F28']);
			$sys_role_chng['NEW_F29'] = trim($row['NEW_F29']);
			$sys_role_chng['NEW_F30'] = trim($row['NEW_F30']);
			$sys_role_chng['NEW_F31'] = trim($row['NEW_F31']);
			$sys_role_chng['NEW_F32'] = trim($row['NEW_F32']);
			$sys_role_chng['NEW_F33'] = trim($row['NEW_F33']);
			$sys_role_chng['NEW_F34'] = trim($row['NEW_F34']);
			$sys_role_chng['NEW_F35'] = trim($row['NEW_F35']);
			$sys_role_chng['NEW_F36'] = trim($row['NEW_F36']);
			$sys_role_chng['NEW_F37'] = trim($row['NEW_F37']);
			$sys_role_chng['NEW_F38'] = trim($row['NEW_F38']);
			$sys_role_chng['NEW_F39'] = trim($row['NEW_F39']);
			$sys_role_chng['NEW_F40'] = trim($row['NEW_F40']);
			$sys_role_chng['NEW_F41'] = trim($row['NEW_F41']);
			$sys_role_chng['NEW_F42'] = trim($row['NEW_F42']);
			$sys_role_chng['NEW_F43'] = trim($row['NEW_F43']);
			$sys_role_chng['NEW_F44'] = trim($row['NEW_F44']);
			$sys_role_chng['NEW_F45'] = trim($row['NEW_F45']);
			$sys_role_chng['NEW_F46'] = trim($row['NEW_F46']);
			$sys_role_chng['NEW_F47'] = trim($row['NEW_F47']);
			$sys_role_chng['NEW_F48'] = trim($row['NEW_F48']);
			$sys_role_chng['NEW_F49'] = trim($row['NEW_F49']);
			$sys_role_chng['NEW_F50'] = trim($row['NEW_F50']);
			$sys_role_chng['NEW_F51'] = trim($row['NEW_F51']);
			$sys_role_chng['NEW_F52'] = trim($row['NEW_F52']);
			$sys_role_chng['NEW_F53'] = trim($row['NEW_F53']);
			$sys_role_chng['NEW_F54'] = trim($row['NEW_F54']);
			$sys_role_chng['NEW_F55'] = trim($row['NEW_F55']);
			$sys_role_chng['NEW_F56'] = trim($row['NEW_F56']);
			$sys_role_chng['NEW_F57'] = trim($row['NEW_F57']);
			$sys_role_chng['NEW_F58'] = trim($row['NEW_F58']);
			$sys_role_chng['NEW_F59'] = trim($row['NEW_F59']);
			$sys_role_chng['NEW_F60'] = trim($row['NEW_F60']);
			$sys_role_chng['NEW_F61'] = trim($row['NEW_F61']);
			$sys_role_chng['NEW_F62'] = trim($row['NEW_F62']);
			$sys_role_chng['NEW_F63'] = trim($row['NEW_F63']);
			$sys_role_chng['NEW_F64'] = trim($row['NEW_F64']);
			$sys_role_chng['NEW_F65'] = trim($row['NEW_F65']);
			$sys_role_chng['NEW_F66'] = trim($row['NEW_F66']);
			$sys_role_chng['NEW_F67'] = trim($row['NEW_F67']);
			$sys_role_chng['NEW_F68'] = trim($row['NEW_F68']);
			$sys_role_chng['NEW_F69'] = trim($row['NEW_F69']);
			$sys_role_chng['NEW_F70'] = trim($row['NEW_F70']);
			$sys_role_chng['CHNG_INIT_DATE'] = trim($row['CHNG_INIT_DATE']);
			$sys_role_chng['CHNG_INIT_BY'] = trim($row['CHNG_INIT_BY']);
			$sys_role_chng['CHNG_VERIF_DATE'] = trim($row['CHNG_VERIF_DATE']);
			$sys_role_chng['CHNG_VERIF_BY'] = trim($row['CHNG_VERIF_BY']);
			$sys_role_chng['CHNG_STATUS'] = trim($row['CHNG_STATUS']);

		}

	return $sys_role_chng;
}


# **..** **..** **..** **..** **..** **..** **..** SECTION 03: User Management **..** **..** **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 03: User Management **..** **..** **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 03: User Management **..** **..** **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 03: User Management **..** **..** **..** **..** **..** **..** **..**  **..** **..** 


# ... ... ... F24: Flag User LogIn Status ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function CheckIfUserExists($USER_CORE_ID){
	$user_exists= "NO";
	$q = mysql_query("SELECT * FROM upr WHERE USER_CORE_ID='$USER_CORE_ID' AND USER_STATUS not in ('REJECTED')") or die("ERR_UPR_LOG: ".mysql_error());
	if(mysql_num_rows($q)>0)
	{
		$user_exists = "YES";
	}

	return $user_exists;
} 

# ... ... ... F25: Fetch System User List... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSysUserList($USER_STATUS){
	$usr_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM upr WHERE USER_STATUS='$USER_STATUS'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$usr = array();
		$usr['RECORD_ID'] = trim($row['RECORD_ID']);
		$usr['USER_ID'] = trim($row['USER_ID']);
		$usr['USER_CORE_ID'] = trim($row['USER_CORE_ID']);
		$usr['GENDER'] = trim($row['GENDER']);
		$usr['PHONE'] = trim($row['PHONE']);
		$usr['EMAIL_ADDRESS'] = trim($row['EMAIL_ADDRESS']);
		$usr['LOGGED_IN'] = trim($row['LOGGED_IN']);
		$usr['ADDED_ON'] = trim($row['ADDED_ON']);
		$usr['ADDED_BY'] = trim($row['ADDED_BY']);
		$usr['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$usr['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$usr['LAST_CHNGD_BY'] = trim($row['LAST_CHNGD_BY']);
		$usr['LAST_CHNGD_ON'] = trim($row['LAST_CHNGD_ON']);
		$usr['USER_STATUS'] = trim($row['USER_STATUS']);

		$usr_list[$x] = $usr;

		$x++;
	}

	return $usr_list;
}

# ... ... ... F26: Get Cust Details From Portal ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function GetUserDetailsFromPortal($USER_ID){
	$USER_DETAILS = array();

	$q = mysql_query("SELECT * FROM upr WHERE USER_ID='$USER_ID'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$USER_DETAILS['RECORD_ID'] = trim($row['RECORD_ID']);
		$USER_DETAILS['USER_ID'] = trim($row['USER_ID']);
		$USER_DETAILS['USER_CORE_ID'] = trim($row['USER_CORE_ID']);
		$USER_DETAILS['GENDER'] = trim($row['GENDER']);
		$USER_DETAILS['PHONE'] = trim($row['PHONE']);
		$USER_DETAILS['EMAIL_ADDRESS'] = trim($row['EMAIL_ADDRESS']);
		$USER_DETAILS['TFA_FLG'] = trim($row['TFA_FLG']);
		$USER_DETAILS['LOGGED_IN'] = trim($row['LOGGED_IN']);
		$USER_DETAILS['USER_STATUS'] = trim($row['USER_STATUS']);

		$user_id = $row['USER_ID'];
		$user_roles = GetUserDefinedRoles($user_id);
		$user_role_details = ProcessUserRoleDetails($user_roles);

		$USER_DETAILS['USER_ROLE_DETAILS'] = $user_role_details;

	}

	return $USER_DETAILS;
}

# ... ... ... F27: Fetch User Change Requests Count ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function FetchUserChangeRequestsCount(){
	# ... 01: Info Change Request
	$info_ids = array();
	$x = 0;
	$q = mysql_query("SELECT USER_ID FROM upr_info_chng_log WHERE CHNG_STATUS='PENDING'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$info_ids[$x] = trim($row['USER_ID']);
		$x++;
	}

	# ... 02: Roles Change Request
	$roles_users_id = array();
	$y = 0;
	$q2 = mysql_query("SELECT USER_ID FROM upr_usr_roles WHERE USER_ROLE_STATUS='PENDING'") or die("ERROR 1: ".mysql_error());;
	while ($row2 = mysql_fetch_array($q2)) {
		$roles_users_id[$y] = trim($row2['USER_ID']);
		$y++;
	}

	# ... 03: 2FA Change Request
	$fa_users = array();
	$z = 0;
	$q3 = mysql_query("SELECT ENTITY_ID FROM tfa_devices WHERE ENTITY_TYPE='SYS_USR' AND DEVICE_STATUS='PENDING'") or die("ERROR 1: ".mysql_error());;
	while ($row3 = mysql_fetch_array($q3)) {
		$fa_users[$z] = trim($row3['ENTITY_ID']);
		$z++;
	}

	# ... 04: Merge the arrays into one with unique Ids
	$merger = array();
	$merger = array_merge($info_ids, $roles_users_id, $fa_users);

	# ... 05: Filter it to have distinct Ids
	$final_user_ids = array();
	$final_user_ids = array_unique($merger);

	return sizeof($final_user_ids);
}

# ... ... ... F27: FetchUserChangeRequests ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function FetchUserChangeRequests(){

	# ... 01: Info Change Request
	$info_ids = array();
	$x = 0;
	$q = mysql_query("SELECT USER_ID FROM upr_info_chng_log WHERE CHNG_STATUS='PENDING'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$info_ids[$x] = trim($row['USER_ID']);
		$x++;
	}

	# ... 02: Roles Change Request
	$roles_users_id = array();
	$y = 0;
	$q2 = mysql_query("SELECT USER_ID FROM upr_usr_roles WHERE USER_ROLE_STATUS='PENDING'") or die("ERROR 1: ".mysql_error());;
	while ($row2 = mysql_fetch_array($q2)) {
		$roles_users_id[$y] = trim($row2['USER_ID']);
		$y++;
	}

	# ... 03: 2FA Change Request
	$fa_users = array();
	$z = 0;
	$q3 = mysql_query("SELECT ENTITY_ID FROM tfa_devices WHERE ENTITY_TYPE='SYS_USR' AND DEVICE_STATUS='PENDING'") or die("ERROR 1: ".mysql_error());;
	while ($row3 = mysql_fetch_array($q3)) {
		$fa_users[$z] = trim($row3['ENTITY_ID']);
		$z++;
	}

	# ... 04: Merge the arrays into one with unique Ids
	$merger = array();
	$merger = array_merge($info_ids, $roles_users_id, $fa_users);

	# ... 05: Filter it to have distinct Ids
	$final_user_ids = array();
	$final_user_ids = array_unique($merger);

	# ... 06: Process change statistics
	$usr_pending_updates = array();
	$m = 0;
	foreach($final_user_ids as $USER_ID) { 
		
		# ... ~: Fetch User's Names and Details
		$USER_DETAILS = array();
		$USER_DETAILS = GetUserDetailsFromPortal($USER_ID);
		$USR_RECORD_ID = $USER_DETAILS['RECORD_ID'];
		$USR_USER_CORE_ID = $USER_DETAILS['USER_CORE_ID'];
		$USR_GENDER = $USER_DETAILS['GENDER'];
		$USR_PHONE = $USER_DETAILS['PHONE'];
		$USR_EMAIL_ADDRESS = $USER_DETAILS['EMAIL_ADDRESS'];
		$USR_LOGGED_IN = $USER_DETAILS['LOGGED_IN'];
		$USR_USER_ROLE_DETAILS = $USER_DETAILS['USER_ROLE_DETAILS'];

		# ... A: Count Info Changes
		$q_info = "SELECT COUNT(*) AS RTN_VALUE FROM upr_info_chng_log WHERE USER_ID='$USER_ID' AND CHNG_STATUS='PENDING'";	
		$cnt_q_info = ReturnOneEntryFromDB($q_info);

		# ... B: Count User Role Changes
		$q_role = "SELECT COUNT(*) AS RTN_VALUE FROM upr_usr_roles WHERE USER_ID='$USER_ID' AND USER_ROLE_STATUS='PENDING'";
		$cnt_q_role = ReturnOneEntryFromDB($q_role);

		# ... C: Count 2FA Changes
		$q_faaa = "SELECT COUNT(*) AS RTN_VALUE FROM tfa_devices WHERE ENTITY_TYPE='SYS_USR' AND ENTITY_ID='$USER_ID' AND DEVICE_STATUS='PENDING'";
		$cnt_q_faaa = ReturnOneEntryFromDB($q_faaa);


		# ... Z: packaking the array
		$usr_chng = array();
		$usr_chng["USER_ID"] = $USER_ID;
		$usr_chng["USR_USER_CORE_ID"] = $USR_USER_CORE_ID;
		$usr_chng["cnt_q_info"] = $cnt_q_info;
		$usr_chng["cnt_q_role"] = $cnt_q_role;
		$usr_chng["cnt_q_faaa"] = $cnt_q_faaa;

		$usr_pending_updates[$m] = $usr_chng;
		$m++;
	}

	return $usr_pending_updates;
}

# ... ... ... F28: GetUserInfoChange ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function GetUserInfoChange($USER_ID){
	$user_info_chng = array();
	$q = mysql_query("SELECT * FROM upr_info_chng_log WHERE USER_ID='$USER_ID' AND CHNG_STATUS='PENDING'") or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$user_info_chng['RECORD_ID'] = trim($row['RECORD_ID']);
		$user_info_chng['USER_ID'] = trim($row['USER_ID']);
		$user_info_chng['OLD_GENDER'] = trim($row['OLD_GENDER']);
		$user_info_chng['OLD_PHONE'] = trim($row['OLD_PHONE']);
		$user_info_chng['NEW_GENDER'] = trim($row['NEW_GENDER']);
		$user_info_chng['NEW_PHONE'] = trim($row['NEW_PHONE']);
		$user_info_chng['CHNG_INIT_DATE'] = trim($row['CHNG_INIT_DATE']);
		$user_info_chng['CHNG_INIT_BY'] = trim($row['CHNG_INIT_BY']);
		$user_info_chng['CHNG_VERIF_DATE'] = trim($row['CHNG_VERIF_DATE']);
		$user_info_chng['CHNG_VERIF_BY'] = trim($row['CHNG_VERIF_BY']);
		$user_info_chng['CHNG_VERIF_RMKS'] = trim($row['CHNG_VERIF_RMKS']);
		$user_info_chng['CHNG_STATUS'] = trim($row['CHNG_STATUS']);		
	}

	return $user_info_chng;
}

# ... ... ... F29: GetUserRolesRequested ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function GetUserRolesRequested($user_id){
	$user_roles = array();
	$x = 0;
	
	$q = mysql_query("SELECT * FROM upr_usr_roles WHERE USER_ID='$user_id' AND USER_ROLE_STATUS='PENDING'") or die("ERROR 1: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {
			$ROLE_ID = trim($row['ROLE_ID']);
			$user_roles[$x] = $ROLE_ID;
			$x++;
		}

	return $user_roles;
}

# ... ... ... 30: Fetch System User List... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTFADeviceTypes(){
	$dev_type_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM tfa_device_types WHERE DEVICE_TYPE_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$dev_type = array();
		$dev_type['RECORD_ID'] = trim($row['RECORD_ID']);
		$dev_type['DEVICE_TYPE_ID'] = trim($row['DEVICE_TYPE_ID']);
		$dev_type['DEVICE_TYPE_NAME'] = trim($row['DEVICE_TYPE_NAME']);
		$dev_type['DEVICE_TYPE_STATUS'] = trim($row['DEVICE_TYPE_STATUS']);
		$dev_type_list[$x] = $dev_type;

		$x++;
	}

	return $dev_type_list;
}

# ... ... ... 31: Fetch User 2FA devices ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function Fetch2FADevicesForEntityId($ENTITY_ID){
	$tfa_device_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM tfa_devices WHERE ENTITY_ID='$ENTITY_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$tfa_device = array();
		$tfa_device['RECORD_ID'] = trim($row['RECORD_ID']);
		$tfa_device['DEVICE_ID'] = trim($row['DEVICE_ID']);
		$tfa_device['DEVICE_TYPE_ID'] = trim($row['DEVICE_TYPE_ID']);

		$dev_type = array();
		$dev_type = GetTFADeviceType($tfa_device['DEVICE_TYPE_ID']);
		//$DEVICE_TYPE_NAME = $dev_type['DEVICE_TYPE_NAME'];
		//$tfa_device['DEVICE_TYPE_NAME'] = $DEVICE_TYPE_NAME;

		$tfa_device['ENTITY_TYPE'] = trim($row['ENTITY_TYPE']);
		$tfa_device['ENTITY_ID'] = trim($row['ENTITY_ID']);
		//$tfa_device['TEMP_ACCESS_PIN'] = trim($row['TEMP_ACCESS_PIN']);
		//$tfa_device['ACCESS_PIN_RESET_FLG'] = trim($row['ACCESS_PIN_RESET_FLG']);
		$tfa_device['KEY_1'] = trim($row['KEY_1']);
		$tfa_device['KEY_2'] = trim($row['KEY_2']);
		$tfa_device['KEY_3'] = trim($row['KEY_3']);
		$tfa_device['ADDED_ON'] = trim($row['ADDED_ON']);
		$tfa_device['ADDED_BY'] = trim($row['ADDED_BY']);
		$tfa_device['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$tfa_device['APPROVED_BY'] = trim($row['APPROVED_BY']);
		//$tfa_device['LAST_ACCESS_PIN_RESET_DATE'] = trim($row['LAST_ACCESS_PIN_RESET_DATE']);
		//$tfa_device['LAST_ACCESS_PIN_RESET_DONEBY'] = trim($row['LAST_ACCESS_PIN_RESET_DONEBY']);
		$tfa_device['DEVICE_STATUS'] = trim($row['DEVICE_STATUS']);

		$tfa_device_list[$x] = $tfa_device;

		$x++;
	}

	return $tfa_device_list;
}

# ... ... ... 32: Get TFA Device Type ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetTFADeviceType($DEVICE_TYPE_ID){
	$dev_type = array();
	$q = mysql_query("SELECT * FROM tfa_device_types WHERE DEVICE_TYPE_ID='$DEVICE_TYPE_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$dev_type['RECORD_ID'] = trim($row['RECORD_ID']);
		$dev_type['DEVICE_TYPE_ID'] = trim($row['DEVICE_TYPE_ID']);
		$dev_type['DEVICE_TYPE_NAME'] = trim($row['DEVICE_TYPE_NAME']);
		$dev_type['DEVICE_TYPE_STATUS'] = trim($row['DEVICE_TYPE_STATUS']);

	}

	return $dev_type;
}


# **..** **..** **..** **..** **..** **..** **..** SECTION 04: Customer Management **..** **..** **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 04: Customer Management **..** **..** **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 04: Customer Management **..** **..** **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 04: Customer Management **..** **..** **..** **..** **..** **..** **..**  **..** **..** 

# ... ... ... 33: Fetch Activation Requests By Status ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchActivationRequestsByStatus($ACTIVATION_STATUS){
	$cstmr_actvn_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM cstmrs_actvn_rqsts WHERE ACTIVATION_STATUS='$ACTIVATION_STATUS' ORDER BY REQST_RECORD_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cstmr_actvn = array();
		$cstmr_actvn['RECORD_ID'] = trim($row['RECORD_ID']);
		$cstmr_actvn['ACTIVATION_REF'] = trim($row['ACTIVATION_REF']);
		$cstmr_actvn['MMBSHP_TYPE'] = trim($row['MMBSHP_TYPE']);
		$cstmr_actvn['CHANNEL_ID'] = trim($row['CHANNEL_ID']);
		$cstmr_actvn['FIRST_NAME'] = trim($row['FIRST_NAME']);
		$cstmr_actvn['MIDDLE_NAME'] = trim($row['MIDDLE_NAME']);
		$cstmr_actvn['LAST_NAME'] = trim($row['LAST_NAME']);
		$cstmr_actvn['GENDER'] = trim($row['GENDER']);
		$cstmr_actvn['DOB'] = trim($row['DOB']);
		$cstmr_actvn['BIO_DATA_VERIF_FLG'] = trim($row['BIO_DATA_VERIF_FLG']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS'] = trim($row['BIO_DATA_VERIF_RMKS']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS_BY'] = trim($row['BIO_DATA_VERIF_RMKS_BY']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS_DATE'] = trim($row['BIO_DATA_VERIF_RMKS_DATE']);
		$cstmr_actvn['EMAIL'] = trim($row['EMAIL']);
		$cstmr_actvn['MOBILE_NO'] = trim($row['MOBILE_NO']);
		$cstmr_actvn['PHYSICAL_ADDRESS'] = trim($row['PHYSICAL_ADDRESS']);
		$cstmr_actvn['CONTACT_DATA_VERIF_FLG'] = trim($row['CONTACT_DATA_VERIF_FLG']);
		$cstmr_actvn['CONTACT_DATA_VERIF_RMKS'] = trim($row['CONTACT_DATA_VERIF_RMKS']);
		$cstmr_actvn['CONTACT_DATA_VERIF_BY'] = trim($row['CONTACT_DATA_VERIF_BY']);
		$cstmr_actvn['CONTACT_DATA_VERIF_DATE'] = trim($row['CONTACT_DATA_VERIF_DATE']);
		$cstmr_actvn['WORK_ID'] = trim($row['WORK_ID']);
		$cstmr_actvn['WORK_ID_ATTCHMNT_FLG'] = trim($row['WORK_ID_ATTCHMNT_FLG']);
		$cstmr_actvn['WORK_ID_FILE_NAME'] = trim($row['WORK_ID_FILE_NAME']);
		$cstmr_actvn['NATIONAL_ID'] = trim($row['NATIONAL_ID']);
		$cstmr_actvn['NATIONAL_ID_ATTCHMNT_FLG'] = trim($row['NATIONAL_ID_ATTCHMNT_FLG']);
		$cstmr_actvn['NATIONAL_ID_FILE_NAME'] = trim($row['NATIONAL_ID_FILE_NAME']);
		$cstmr_actvn['MAF_UPLOAD_FLG'] = trim($row['MAF_UPLOAD_FLG']);
		$cstmr_actvn['MAF_UPLOAD_FILE_NAME'] = trim($row['MAF_UPLOAD_FILE_NAME']);
		$cstmr_actvn['PASSPORT_PHOTO_UPLOAD_FLG'] = trim($row['PASSPORT_PHOTO_UPLOAD_FLG']);
		$cstmr_actvn['PASSPORT_PHOTO_FILE_NAME'] = trim($row['PASSPORT_PHOTO_FILE_NAME']);
		$cstmr_actvn['FILE_DATA_VERIF_FLG'] = trim($row['FILE_DATA_VERIF_FLG']);
		$cstmr_actvn['FILE_DATA_VERIF_RMKS'] = trim($row['FILE_DATA_VERIF_RMKS']);
		$cstmr_actvn['FILE_DATA_VERIF_BY'] = trim($row['FILE_DATA_VERIF_BY']);
		$cstmr_actvn['FILE_DATA_VERIF_DATE'] = trim($row['FILE_DATA_VERIF_DATE']);
		$cstmr_actvn['REQST_RECORD_DATE'] = trim($row['REQST_RECORD_DATE']);
		$cstmr_actvn['VERIF_RMKS'] = trim($row['VERIF_RMKS']);
		$cstmr_actvn['VERIF_DATE'] = trim($row['VERIF_DATE']);
		$cstmr_actvn['VERIF_BY'] = trim($row['VERIF_BY']);
		$cstmr_actvn['APPRVL_RMKS'] = trim($row['APPRVL_RMKS']);
		$cstmr_actvn['APPRVL_DATE'] = trim($row['APPRVL_DATE']);
		$cstmr_actvn['APPRVD_BY'] = trim($row['APPRVD_BY']);
		$cstmr_actvn['CST_CORE_CRTN_FLG'] = trim($row['CST_CORE_CRTN_FLG']);
		$cstmr_actvn['CST_CORE_ID'] = trim($row['CST_CORE_ID']);
		$cstmr_actvn['CORE_IMG_UPLD_FLG'] = trim($row['CORE_IMG_UPLD_FLG']);
		$cstmr_actvn['WRKID_UPLD_FLG'] = trim($row['WRKID_UPLD_FLG']);
		$cstmr_actvn['NIN_UPLD_FLG'] = trim($row['NIN_UPLD_FLG']);
		$cstmr_actvn['MAF_UPLD_FLG'] = trim($row['MAF_UPLD_FLG']);
		$cstmr_actvn['ACTIVATION_STATUS'] = trim($row['ACTIVATION_STATUS']);

		$cstmr_actvn_list[$x] = $cstmr_actvn;
		$x++;

	}

	return $cstmr_actvn_list;
}

# ... ... ... 34: Fetch Activation Request By Id ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchActivationRequestById($ACTIVATION_REF){
	$cstmr_actvn = array();
	$q = mysql_query("SELECT * FROM cstmrs_actvn_rqsts WHERE ACTIVATION_REF='$ACTIVATION_REF'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cstmr_actvn['RECORD_ID'] = trim($row['RECORD_ID']);
		$cstmr_actvn['ACTIVATION_REF'] = trim($row['ACTIVATION_REF']);
		$cstmr_actvn['MMBSHP_TYPE'] = trim($row['MMBSHP_TYPE']);
		$cstmr_actvn['CHANNEL_ID'] = trim($row['CHANNEL_ID']);
		$cstmr_actvn['FIRST_NAME'] = trim($row['FIRST_NAME']);
		$cstmr_actvn['MIDDLE_NAME'] = trim($row['MIDDLE_NAME']);
		$cstmr_actvn['LAST_NAME'] = trim($row['LAST_NAME']);
		$cstmr_actvn['GENDER'] = trim($row['GENDER']);
		$cstmr_actvn['DOB'] = trim($row['DOB']);
		$cstmr_actvn['BIO_DATA_VERIF_FLG'] = trim($row['BIO_DATA_VERIF_FLG']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS'] = trim($row['BIO_DATA_VERIF_RMKS']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS_BY'] = trim($row['BIO_DATA_VERIF_RMKS_BY']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS_DATE'] = trim($row['BIO_DATA_VERIF_RMKS_DATE']);
		$cstmr_actvn['EMAIL'] = trim($row['EMAIL']);
		$cstmr_actvn['MOBILE_NO'] = trim($row['MOBILE_NO']);
		$cstmr_actvn['PHYSICAL_ADDRESS'] = trim($row['PHYSICAL_ADDRESS']);
		$cstmr_actvn['CONTACT_DATA_VERIF_FLG'] = trim($row['CONTACT_DATA_VERIF_FLG']);
		$cstmr_actvn['CONTACT_DATA_VERIF_RMKS'] = trim($row['CONTACT_DATA_VERIF_RMKS']);
		$cstmr_actvn['CONTACT_DATA_VERIF_BY'] = trim($row['CONTACT_DATA_VERIF_BY']);
		$cstmr_actvn['CONTACT_DATA_VERIF_DATE'] = trim($row['CONTACT_DATA_VERIF_DATE']);
		$cstmr_actvn['WORK_ID'] = trim($row['WORK_ID']);
		$cstmr_actvn['WORK_ID_ATTCHMNT_FLG'] = trim($row['WORK_ID_ATTCHMNT_FLG']);
		$cstmr_actvn['WORK_ID_FILE_NAME'] = trim($row['WORK_ID_FILE_NAME']);
		$cstmr_actvn['NATIONAL_ID'] = trim($row['NATIONAL_ID']);
		$cstmr_actvn['NATIONAL_ID_ATTCHMNT_FLG'] = trim($row['NATIONAL_ID_ATTCHMNT_FLG']);
		$cstmr_actvn['NATIONAL_ID_FILE_NAME'] = trim($row['NATIONAL_ID_FILE_NAME']);
		$cstmr_actvn['MAF_UPLOAD_FLG'] = trim($row['MAF_UPLOAD_FLG']);
		$cstmr_actvn['MAF_UPLOAD_FILE_NAME'] = trim($row['MAF_UPLOAD_FILE_NAME']);
		$cstmr_actvn['PASSPORT_PHOTO_UPLOAD_FLG'] = trim($row['PASSPORT_PHOTO_UPLOAD_FLG']);
		$cstmr_actvn['PASSPORT_PHOTO_FILE_NAME'] = trim($row['PASSPORT_PHOTO_FILE_NAME']);
		$cstmr_actvn['FILE_DATA_VERIF_FLG'] = trim($row['FILE_DATA_VERIF_FLG']);
		$cstmr_actvn['FILE_DATA_VERIF_RMKS'] = trim($row['FILE_DATA_VERIF_RMKS']);
		$cstmr_actvn['FILE_DATA_VERIF_BY'] = trim($row['FILE_DATA_VERIF_BY']);
		$cstmr_actvn['FILE_DATA_VERIF_DATE'] = trim($row['FILE_DATA_VERIF_DATE']);
		$cstmr_actvn['REQST_RECORD_DATE'] = trim($row['REQST_RECORD_DATE']);
		$cstmr_actvn['VERIF_RMKS'] = trim($row['VERIF_RMKS']);
		$cstmr_actvn['VERIF_DATE'] = trim($row['VERIF_DATE']);
		$cstmr_actvn['VERIF_BY'] = trim($row['VERIF_BY']);
		$cstmr_actvn['APPRVL_RMKS'] = trim($row['APPRVL_RMKS']);
		$cstmr_actvn['APPRVL_DATE'] = trim($row['APPRVL_DATE']);
		$cstmr_actvn['APPRVD_BY'] = trim($row['APPRVD_BY']);
		$cstmr_actvn['CST_CORE_CRTN_FLG'] = trim($row['CST_CORE_CRTN_FLG']);
		$cstmr_actvn['CST_CORE_ID'] = trim($row['CST_CORE_ID']);
		$cstmr_actvn['CORE_IMG_UPLD_FLG'] = trim($row['CORE_IMG_UPLD_FLG']);
		$cstmr_actvn['CORE_IMG_UPLD_USER_ID'] = trim($row['CORE_IMG_UPLD_USER_ID']);
		$cstmr_actvn['CORE_IMG_UPLD_DATE'] = trim($row['CORE_IMG_UPLD_DATE']);
		$cstmr_actvn['WRKID_UPLD_FLG'] = trim($row['WRKID_UPLD_FLG']);
		$cstmr_actvn['WRKID_UPLD_USER_ID'] = trim($row['WRKID_UPLD_USER_ID']);
		$cstmr_actvn['WRKID_UPLD_DATE'] = trim($row['WRKID_UPLD_DATE']);
		$cstmr_actvn['NIN_UPLD_FLG'] = trim($row['NIN_UPLD_FLG']);
		$cstmr_actvn['NIN_UPLD_USER_ID'] = trim($row['NIN_UPLD_USER_ID']);
		$cstmr_actvn['NIN_UPLD_DATE'] = trim($row['NIN_UPLD_DATE']);
		$cstmr_actvn['MAF_UPLD_FLG'] = trim($row['MAF_UPLD_FLG']);
		$cstmr_actvn['MAF_UPLD_USER_ID'] = trim($row['MAF_UPLD_USER_ID']);
		$cstmr_actvn['MAF_UPLD_DATE'] = trim($row['MAF_UPLD_DATE']);
		$cstmr_actvn['SVNGS_ACCT_CRTN_FLG'] = trim($row['SVNGS_ACCT_CRTN_FLG']);
		$cstmr_actvn['SVNGS_ACCT_CRTN_USER_ID'] = trim($row['SVNGS_ACCT_CRTN_USER_ID']);
		$cstmr_actvn['SVNGS_ACCT_CRTN_DATE'] = trim($row['SVNGS_ACCT_CRTN_DATE']);
		$cstmr_actvn['OAA_FLG'] = trim($row['OAA_FLG']);
		$cstmr_actvn['OAA_USER_ID'] = trim($row['OAA_USER_ID']);
		$cstmr_actvn['OAA_DATE'] = trim($row['OAA_DATE']);

		$cstmr_actvn['ACTIVATION_STATUS'] = trim($row['ACTIVATION_STATUS']);

	}

	return $cstmr_actvn;
}

# ... ... ... 35: Fetch Activation Requests By Status ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAllActivationRequests(){
	$cstmr_actvn_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM cstmrs_actvn_rqsts ORDER BY REQST_RECORD_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cstmr_actvn = array();
		$cstmr_actvn['RECORD_ID'] = trim($row['RECORD_ID']);
		$cstmr_actvn['ACTIVATION_REF'] = trim($row['ACTIVATION_REF']);
		$cstmr_actvn['MMBSHP_TYPE'] = trim($row['MMBSHP_TYPE']);
		$cstmr_actvn['CHANNEL_ID'] = trim($row['CHANNEL_ID']);
		$cstmr_actvn['FIRST_NAME'] = trim($row['FIRST_NAME']);
		$cstmr_actvn['MIDDLE_NAME'] = trim($row['MIDDLE_NAME']);
		$cstmr_actvn['LAST_NAME'] = trim($row['LAST_NAME']);
		$cstmr_actvn['GENDER'] = trim($row['GENDER']);
		$cstmr_actvn['DOB'] = trim($row['DOB']);
		$cstmr_actvn['BIO_DATA_VERIF_FLG'] = trim($row['BIO_DATA_VERIF_FLG']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS'] = trim($row['BIO_DATA_VERIF_RMKS']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS_BY'] = trim($row['BIO_DATA_VERIF_RMKS_BY']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS_DATE'] = trim($row['BIO_DATA_VERIF_RMKS_DATE']);
		$cstmr_actvn['EMAIL'] = trim($row['EMAIL']);
		$cstmr_actvn['MOBILE_NO'] = trim($row['MOBILE_NO']);
		$cstmr_actvn['PHYSICAL_ADDRESS'] = trim($row['PHYSICAL_ADDRESS']);
		$cstmr_actvn['CONTACT_DATA_VERIF_FLG'] = trim($row['CONTACT_DATA_VERIF_FLG']);
		$cstmr_actvn['CONTACT_DATA_VERIF_RMKS'] = trim($row['CONTACT_DATA_VERIF_RMKS']);
		$cstmr_actvn['CONTACT_DATA_VERIF_BY'] = trim($row['CONTACT_DATA_VERIF_BY']);
		$cstmr_actvn['CONTACT_DATA_VERIF_DATE'] = trim($row['CONTACT_DATA_VERIF_DATE']);
		$cstmr_actvn['WORK_ID'] = trim($row['WORK_ID']);
		$cstmr_actvn['WORK_ID_ATTCHMNT_FLG'] = trim($row['WORK_ID_ATTCHMNT_FLG']);
		$cstmr_actvn['WORK_ID_FILE_NAME'] = trim($row['WORK_ID_FILE_NAME']);
		$cstmr_actvn['NATIONAL_ID'] = trim($row['NATIONAL_ID']);
		$cstmr_actvn['NATIONAL_ID_ATTCHMNT_FLG'] = trim($row['NATIONAL_ID_ATTCHMNT_FLG']);
		$cstmr_actvn['NATIONAL_ID_FILE_NAME'] = trim($row['NATIONAL_ID_FILE_NAME']);
		$cstmr_actvn['MAF_UPLOAD_FLG'] = trim($row['MAF_UPLOAD_FLG']);
		$cstmr_actvn['MAF_UPLOAD_FILE_NAME'] = trim($row['MAF_UPLOAD_FILE_NAME']);
		$cstmr_actvn['PASSPORT_PHOTO_UPLOAD_FLG'] = trim($row['PASSPORT_PHOTO_UPLOAD_FLG']);
		$cstmr_actvn['PASSPORT_PHOTO_FILE_NAME'] = trim($row['PASSPORT_PHOTO_FILE_NAME']);
		$cstmr_actvn['FILE_DATA_VERIF_FLG'] = trim($row['FILE_DATA_VERIF_FLG']);
		$cstmr_actvn['FILE_DATA_VERIF_RMKS'] = trim($row['FILE_DATA_VERIF_RMKS']);
		$cstmr_actvn['FILE_DATA_VERIF_BY'] = trim($row['FILE_DATA_VERIF_BY']);
		$cstmr_actvn['FILE_DATA_VERIF_DATE'] = trim($row['FILE_DATA_VERIF_DATE']);
		$cstmr_actvn['REQST_RECORD_DATE'] = trim($row['REQST_RECORD_DATE']);
		$cstmr_actvn['VERIF_RMKS'] = trim($row['VERIF_RMKS']);
		$cstmr_actvn['VERIF_DATE'] = trim($row['VERIF_DATE']);
		$cstmr_actvn['VERIF_BY'] = trim($row['VERIF_BY']);
		$cstmr_actvn['APPRVL_RMKS'] = trim($row['APPRVL_RMKS']);
		$cstmr_actvn['APPRVL_DATE'] = trim($row['APPRVL_DATE']);
		$cstmr_actvn['APPRVD_BY'] = trim($row['APPRVD_BY']);
		$cstmr_actvn['CST_CORE_CRTN_FLG'] = trim($row['CST_CORE_CRTN_FLG']);
		$cstmr_actvn['CST_CORE_ID'] = trim($row['CST_CORE_ID']);
		$cstmr_actvn['CORE_IMG_UPLD_FLG'] = trim($row['CORE_IMG_UPLD_FLG']);
		$cstmr_actvn['WRKID_UPLD_FLG'] = trim($row['WRKID_UPLD_FLG']);
		$cstmr_actvn['NIN_UPLD_FLG'] = trim($row['NIN_UPLD_FLG']);
		$cstmr_actvn['MAF_UPLD_FLG'] = trim($row['MAF_UPLD_FLG']);
		$cstmr_actvn['ACTIVATION_STATUS'] = trim($row['ACTIVATION_STATUS']);

		$cstmr_actvn_list[$x] = $cstmr_actvn;
		$x++;

	}

	return $cstmr_actvn_list;
}

# ... ... ... 36: FetchCustList ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustList($CUST_STATUS){
	$cust_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM cstmrs WHERE CUST_STATUS='$CUST_STATUS'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cust = array();
		$cust['RECORD_ID'] = trim($row['RECORD_ID']);
		$cust['CUST_ID'] = trim($row['CUST_ID']);
		$cust['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$cust['APPLN_REF'] = trim($row['APPLN_REF']);
		$cust['ACTVN_TOKEN'] = trim($row['ACTVN_TOKEN']);
		$cust['CUST_EMAIL'] = trim($row['CUST_EMAIL']);
		$cust['CUST_PHONE'] = trim($row['CUST_PHONE']);
		$cust['WEB_CHANNEL_LOGIN_ATTEMPTS'] = trim($row['WEB_CHANNEL_LOGIN_ATTEMPTS']);
		$cust['WEB_CHANNEL_ACCESS_FLG'] = trim($row['WEB_CHANNEL_ACCESS_FLG']);
		$cust['WEB_CHANNEL_ACTVN_FLG'] = trim($row['WEB_CHANNEL_ACTVN_FLG']);
		$cust['WEB_CHANNEL_ACTVN_DATE'] = trim($row['WEB_CHANNEL_ACTVN_DATE']);
		$cust['MOB_CHANNEL_LOGIN_ATTEMPTS'] = trim($row['MOB_CHANNEL_LOGIN_ATTEMPTS']);
		$cust['MOB_CHANNEL_ACCESS_FLG'] = trim($row['MOB_CHANNEL_ACCESS_FLG']);
		$cust['MOB_CHANNEL_ACTVN_FLG'] = trim($row['MOB_CHANNEL_ACTVN_FLG']);
		$cust['MOB_CHANNEL_ACTVN_DATE'] = trim($row['MOB_CHANNEL_ACTVN_DATE']);
		$cust['CUST_USR'] = trim($row['CUST_USR']);
		$cust['CUST_PWSD_STATUS'] = trim($row['CUST_PWSD_STATUS']);
		$cust['CUST_PWSD'] = trim($row['CUST_PWSD']);
		$cust['CUST_PWSD_LST_CHNG_DATE'] = trim($row['CUST_PWSD_LST_CHNG_DATE']);
		$cust['CUST_PIN_STATUS'] = trim($row['CUST_PIN_STATUS']);
		$cust['CUST_PIN'] = trim($row['CUST_PIN']);
		$cust['CUST_PIN_LST_CHNG_DATE'] = trim($row['CUST_PIN_LST_CHNG_DATE']);
		$cust['CUST_DEVICE_ID'] = trim($row['CUST_DEVICE_ID']);
		$cust['CUST_SIM_IMEI'] = trim($row['CUST_SIM_IMEI']);
		$cust['CUST_STATUS'] = trim($row['CUST_STATUS']);

		$cust_list[$x] = $cust;
		$x++;
	}

	return $cust_list;
}

# ... ... ... 37: FetchClientUpdates ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchClientUpdates($CHNG_STATUS){
	$chng_list = array();
	$x = 0;

	$db_query = "";
	if ($CHNG_STATUS=="") {
		$db_query = "SELECT * FROM cstmrs_info_chng_log ORDER BY CHNG_INIT_DATE ASC";
	}
	elseif ($CHNG_STATUS!="") {
		$db_query = "SELECT * FROM cstmrs_info_chng_log WHERE CHNG_STATUS='$CHNG_STATUS' ORDER BY CHNG_INIT_DATE ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$chng = array();
		$chng['RECORD_ID'] = trim($row['RECORD_ID']);
		$chng['CUST_ID'] = trim($row['CUST_ID']);
		$chng['CHANGE_TYPE'] = trim($row['CHANGE_TYPE']);
		$chng['OLD_VALUE'] = trim($row['OLD_VALUE']);
		$chng['NEW_VALUE'] = trim($row['NEW_VALUE']);
		$chng['CHNG_INIT_DATE'] = trim($row['CHNG_INIT_DATE']);
		$chng['CHNG_INIT_BY'] = trim($row['CHNG_INIT_BY']);
		$chng['CHNG_VERIF_RMKS'] = trim($row['CHNG_VERIF_RMKS']);
		$chng['CHNG_VERIF_DATE'] = trim($row['CHNG_VERIF_DATE']);
		$chng['CHNG_VERIF_BY'] = trim($row['CHNG_VERIF_BY']);
		$chng['CHNG_APPRVL_RMKS'] = trim($row['CHNG_APPRVL_RMKS']);
		$chng['CHNG_APPRVL_DATE'] = trim($row['CHNG_APPRVL_DATE']);
		$chng['CHNG_APPRVL_BY'] = trim($row['CHNG_APPRVL_BY']);
		$chng['CHNG_STATUS'] = trim($row['CHNG_STATUS']);
		$chng_list[$x] = $chng;
		$x++;
	}

	return $chng_list;
}

# ... ... ... 35: FetchAllActivationRequestsByQuery ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAllActivationRequestsByQuery($DB_QUERY){
	$cstmr_actvn_list = array();
	$x = 0;
	$q = mysql_query($DB_QUERY) or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cstmr_actvn = array();
		$cstmr_actvn['RECORD_ID'] = trim($row['RECORD_ID']);
		$cstmr_actvn['ACTIVATION_REF'] = trim($row['ACTIVATION_REF']);
		$cstmr_actvn['MMBSHP_TYPE'] = trim($row['MMBSHP_TYPE']);
		$cstmr_actvn['CHANNEL_ID'] = trim($row['CHANNEL_ID']);
		$cstmr_actvn['FIRST_NAME'] = trim($row['FIRST_NAME']);
		$cstmr_actvn['MIDDLE_NAME'] = trim($row['MIDDLE_NAME']);
		$cstmr_actvn['LAST_NAME'] = trim($row['LAST_NAME']);
		$cstmr_actvn['GENDER'] = trim($row['GENDER']);
		$cstmr_actvn['DOB'] = trim($row['DOB']);
		$cstmr_actvn['BIO_DATA_VERIF_FLG'] = trim($row['BIO_DATA_VERIF_FLG']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS'] = trim($row['BIO_DATA_VERIF_RMKS']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS_BY'] = trim($row['BIO_DATA_VERIF_RMKS_BY']);
		$cstmr_actvn['BIO_DATA_VERIF_RMKS_DATE'] = trim($row['BIO_DATA_VERIF_RMKS_DATE']);
		$cstmr_actvn['EMAIL'] = trim($row['EMAIL']);
		$cstmr_actvn['MOBILE_NO'] = trim($row['MOBILE_NO']);
		$cstmr_actvn['PHYSICAL_ADDRESS'] = trim($row['PHYSICAL_ADDRESS']);
		$cstmr_actvn['CONTACT_DATA_VERIF_FLG'] = trim($row['CONTACT_DATA_VERIF_FLG']);
		$cstmr_actvn['CONTACT_DATA_VERIF_RMKS'] = trim($row['CONTACT_DATA_VERIF_RMKS']);
		$cstmr_actvn['CONTACT_DATA_VERIF_BY'] = trim($row['CONTACT_DATA_VERIF_BY']);
		$cstmr_actvn['CONTACT_DATA_VERIF_DATE'] = trim($row['CONTACT_DATA_VERIF_DATE']);
		$cstmr_actvn['WORK_ID'] = trim($row['WORK_ID']);
		$cstmr_actvn['WORK_ID_ATTCHMNT_FLG'] = trim($row['WORK_ID_ATTCHMNT_FLG']);
		$cstmr_actvn['WORK_ID_FILE_NAME'] = trim($row['WORK_ID_FILE_NAME']);
		$cstmr_actvn['NATIONAL_ID'] = trim($row['NATIONAL_ID']);
		$cstmr_actvn['NATIONAL_ID_ATTCHMNT_FLG'] = trim($row['NATIONAL_ID_ATTCHMNT_FLG']);
		$cstmr_actvn['NATIONAL_ID_FILE_NAME'] = trim($row['NATIONAL_ID_FILE_NAME']);
		$cstmr_actvn['MAF_UPLOAD_FLG'] = trim($row['MAF_UPLOAD_FLG']);
		$cstmr_actvn['MAF_UPLOAD_FILE_NAME'] = trim($row['MAF_UPLOAD_FILE_NAME']);
		$cstmr_actvn['PASSPORT_PHOTO_UPLOAD_FLG'] = trim($row['PASSPORT_PHOTO_UPLOAD_FLG']);
		$cstmr_actvn['PASSPORT_PHOTO_FILE_NAME'] = trim($row['PASSPORT_PHOTO_FILE_NAME']);
		$cstmr_actvn['FILE_DATA_VERIF_FLG'] = trim($row['FILE_DATA_VERIF_FLG']);
		$cstmr_actvn['FILE_DATA_VERIF_RMKS'] = trim($row['FILE_DATA_VERIF_RMKS']);
		$cstmr_actvn['FILE_DATA_VERIF_BY'] = trim($row['FILE_DATA_VERIF_BY']);
		$cstmr_actvn['FILE_DATA_VERIF_DATE'] = trim($row['FILE_DATA_VERIF_DATE']);
		$cstmr_actvn['REQST_RECORD_DATE'] = trim($row['REQST_RECORD_DATE']);
		$cstmr_actvn['VERIF_RMKS'] = trim($row['VERIF_RMKS']);
		$cstmr_actvn['VERIF_DATE'] = trim($row['VERIF_DATE']);
		$cstmr_actvn['VERIF_BY'] = trim($row['VERIF_BY']);
		$cstmr_actvn['APPRVL_RMKS'] = trim($row['APPRVL_RMKS']);
		$cstmr_actvn['APPRVL_DATE'] = trim($row['APPRVL_DATE']);
		$cstmr_actvn['APPRVD_BY'] = trim($row['APPRVD_BY']);
		$cstmr_actvn['CST_CORE_CRTN_FLG'] = trim($row['CST_CORE_CRTN_FLG']);
		$cstmr_actvn['CST_CORE_ID'] = trim($row['CST_CORE_ID']);
		$cstmr_actvn['CORE_IMG_UPLD_FLG'] = trim($row['CORE_IMG_UPLD_FLG']);
		$cstmr_actvn['WRKID_UPLD_FLG'] = trim($row['WRKID_UPLD_FLG']);
		$cstmr_actvn['NIN_UPLD_FLG'] = trim($row['NIN_UPLD_FLG']);
		$cstmr_actvn['MAF_UPLD_FLG'] = trim($row['MAF_UPLD_FLG']);
		$cstmr_actvn['ACTIVATION_STATUS'] = trim($row['ACTIVATION_STATUS']);

		$cstmr_actvn_list[$x] = $cstmr_actvn;
		$x++;

	}

	return $cstmr_actvn_list;
}



# **..** **..** **..** **..** **..** **..** **..** SECTION 05: Notifications Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 05: Notifications Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 05: Notifications Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 05: Notifications Management **..** **..** **..** **..** **..**  **..** **..** 

# ... ... ... 37: FetchSysUserListAddressBook ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSysUserListAddressBook($USER_STATUS){
	$usr_list = array();
	$x = 0;
	$q = mysql_query("SELECT * 
									  FROM upr 
									  WHERE USER_STATUS='$USER_STATUS' 
									    AND USER_ID not in (SELECT ADDRESS_ENTITY_ID 
									                        FROM notification_addressbook 
									                        WHERE ADDRESS_ENTITY_TYPE='USER')
									") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$usr = array();
		$usr['RECORD_ID'] = trim($row['RECORD_ID']);
		$usr['USER_ID'] = trim($row['USER_ID']);
		$usr['USER_CORE_ID'] = trim($row['USER_CORE_ID']);
		$usr['GENDER'] = trim($row['GENDER']);
		$usr['PHONE'] = trim($row['PHONE']);
		$usr['EMAIL_ADDRESS'] = trim($row['EMAIL_ADDRESS']);
		$usr['LOGGED_IN'] = trim($row['LOGGED_IN']);
		$usr['ADDED_ON'] = trim($row['ADDED_ON']);
		$usr['ADDED_BY'] = trim($row['ADDED_BY']);
		$usr['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$usr['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$usr['LAST_CHNGD_BY'] = trim($row['LAST_CHNGD_BY']);
		$usr['LAST_CHNGD_ON'] = trim($row['LAST_CHNGD_ON']);
		$usr['USER_STATUS'] = trim($row['USER_STATUS']);

		$usr_list[$x] = $usr;

		$x++;
	}
	return $usr_list;
}

# ... ... ... 38: FetchCustListAddressBook ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustListAddressBook($CUST_STATUS){
	$cust_list = array();
	$x = 0;
	$q = mysql_query("SELECT * 
		                FROM cstmrs 
		                WHERE CUST_STATUS='$CUST_STATUS'
		                  AND CUST_ID not in (SELECT ADDRESS_ENTITY_ID 
									                        FROM notification_addressbook 
									                        WHERE ADDRESS_ENTITY_TYPE='CUSTOMER')") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cust = array();
		$cust['RECORD_ID'] = trim($row['RECORD_ID']);
		$cust['CUST_ID'] = trim($row['CUST_ID']);
		$cust['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$cust['APPLN_REF'] = trim($row['APPLN_REF']);
		$cust['ACTVN_TOKEN'] = trim($row['ACTVN_TOKEN']);
		$cust['CUST_EMAIL'] = trim($row['CUST_EMAIL']);
		$cust['CUST_PHONE'] = trim($row['CUST_PHONE']);
		$cust['WEB_CHANNEL_LOGIN_ATTEMPTS'] = trim($row['WEB_CHANNEL_LOGIN_ATTEMPTS']);
		$cust['WEB_CHANNEL_ACCESS_FLG'] = trim($row['WEB_CHANNEL_ACCESS_FLG']);
		$cust['WEB_CHANNEL_ACTVN_FLG'] = trim($row['WEB_CHANNEL_ACTVN_FLG']);
		$cust['WEB_CHANNEL_ACTVN_DATE'] = trim($row['WEB_CHANNEL_ACTVN_DATE']);
		$cust['MOB_CHANNEL_LOGIN_ATTEMPTS'] = trim($row['MOB_CHANNEL_LOGIN_ATTEMPTS']);
		$cust['MOB_CHANNEL_ACCESS_FLG'] = trim($row['MOB_CHANNEL_ACCESS_FLG']);
		$cust['MOB_CHANNEL_ACTVN_FLG'] = trim($row['MOB_CHANNEL_ACTVN_FLG']);
		$cust['MOB_CHANNEL_ACTVN_DATE'] = trim($row['MOB_CHANNEL_ACTVN_DATE']);
		$cust['CUST_USR'] = trim($row['CUST_USR']);
		$cust['CUST_PWSD_STATUS'] = trim($row['CUST_PWSD_STATUS']);
		$cust['CUST_PWSD'] = trim($row['CUST_PWSD']);
		$cust['CUST_PWSD_LST_CHNG_DATE'] = trim($row['CUST_PWSD_LST_CHNG_DATE']);
		$cust['CUST_PIN_STATUS'] = trim($row['CUST_PIN_STATUS']);
		$cust['CUST_PIN'] = trim($row['CUST_PIN']);
		$cust['CUST_PIN_LST_CHNG_DATE'] = trim($row['CUST_PIN_LST_CHNG_DATE']);
		$cust['CUST_DEVICE_ID'] = trim($row['CUST_DEVICE_ID']);
		$cust['CUST_SIM_IMEI'] = trim($row['CUST_SIM_IMEI']);
		$cust['CUST_STATUS'] = trim($row['CUST_STATUS']);

		$cust_list[$x] = $cust;
		$x++;
	}

	return $cust_list;
}

# ... ... ... 39: FetchGroupListAddressBook ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchGroupListAddressBook($GRP_STATUS){
	$grp_list = array();
	$x = 0;
	$q = mysql_query("SELECT * 
		                FROM notification_groups 
		                WHERE GRP_STATUS='$GRP_STATUS'
		                	AND GRP_ID not in (SELECT ADDRESS_ENTITY_ID 
									                        FROM notification_addressbook 
									                        WHERE ADDRESS_ENTITY_TYPE='GROUP')") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$grp = array();
		$grp['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp['GRP_ID'] = trim($row['GRP_ID']);
		$grp['GRP_TYPE_ID'] = trim($row['GRP_TYPE_ID']);
		$grp['GRP_NAME'] = trim($row['GRP_NAME']);
		$grp['DATE_CREATED'] = trim($row['DATE_CREATED']);
		$grp['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp['GRP_STATUS'] = trim($row['GRP_STATUS']);
		$grp_list[$x] = $grp;
		$x++;
	}

	return $grp_list;
}

# ... ... ... 40: GetAddressBook ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAddressBook($ADDRESS_STATUS){
	$address_book = array();
	$x = 0;

	$db_query = "";
	if ($ADDRESS_STATUS=="") {
		$db_query = "SELECT * FROM notification_addressbook ORDER BY ADDRESS_ENTITY_NAME ASC";
	}
	elseif ($ADDRESS_STATUS!="") {
		$db_query = "SELECT * FROM notification_addressbook WHERE ADDRESS_STATUS='$ADDRESS_STATUS' ORDER BY ADDRESS_ENTITY_NAME ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$address = array();
		$address['RECORD_ID'] = trim($row['RECORD_ID']);
		$address['ADDRESS_ENTITY_TYPE'] = trim($row['ADDRESS_ENTITY_TYPE']);
		$address['ADDRESS_ENTITY_ID'] = trim($row['ADDRESS_ENTITY_ID']);
		$address['ADDRESS_ENTITY_NAME'] = trim($row['ADDRESS_ENTITY_NAME']);
		$address['ADDRESS_ADDED_DATE'] = trim($row['ADDRESS_ADDED_DATE']);
		$address['ADDRESS_STATUS'] = trim($row['ADDRESS_STATUS']);
		$address_book[$x] = $address;
		$x++;
	}
	return $address_book;
}

# ... ... ... 41: GetAddressBook ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAddressFromAddressBook($RECORD_ID){
	$address = array();
	$q = mysql_query("SELECT * FROM notification_addressbook WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		
		$address['RECORD_ID'] = trim($row['RECORD_ID']);
		$address['ADDRESS_ENTITY_TYPE'] = trim($row['ADDRESS_ENTITY_TYPE']);
		$address['ADDRESS_ENTITY_ID'] = trim($row['ADDRESS_ENTITY_ID']);
		$address['ADDRESS_ENTITY_NAME'] = trim($row['ADDRESS_ENTITY_NAME']);
		$address['ADDRESS_ADDED_DATE'] = trim($row['ADDRESS_ADDED_DATE']);
		$address['ADDRESS_STATUS'] = trim($row['ADDRESS_STATUS']);
	}
	return $address;
}
function FetchAddressFromAddressBookById($ADDRESS_ENTITY_ID){
	$address = array();
	$q = mysql_query("SELECT * FROM notification_addressbook WHERE ADDRESS_ENTITY_ID='$ADDRESS_ENTITY_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		
		$address['RECORD_ID'] = trim($row['RECORD_ID']);
		$address['ADDRESS_ENTITY_TYPE'] = trim($row['ADDRESS_ENTITY_TYPE']);
		$address['ADDRESS_ENTITY_ID'] = trim($row['ADDRESS_ENTITY_ID']);
		$address['ADDRESS_ENTITY_NAME'] = trim($row['ADDRESS_ENTITY_NAME']);
		$address['ADDRESS_ADDED_DATE'] = trim($row['ADDRESS_ADDED_DATE']);
		$address['ADDRESS_STATUS'] = trim($row['ADDRESS_STATUS']);
	}
	return $address;
}

# ... ... ... 42: Fetch Group List ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchGroupList($GRP_STATUS){
	$grp_list = array();
	$x = 0;

	$db_query = "";
	if ($GRP_STATUS=="") {
		$db_query = "SELECT * FROM notification_groups ORDER BY GRP_NAME ASC";
	}
	elseif ($GRP_STATUS!="") {
		$db_query = "SELECT * FROM notification_groups WHERE GRP_STATUS='$GRP_STATUS' ORDER BY GRP_NAME ASC";
	}

		$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$grp = array();
		$grp['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp['GRP_ID'] = trim($row['GRP_ID']);
		$grp['GRP_TYPE_ID'] = trim($row['GRP_TYPE_ID']);
		$grp['GRP_NAME'] = trim($row['GRP_NAME']);
		$grp['DATE_CREATED'] = trim($row['DATE_CREATED']);
		$grp['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp['GRP_STATUS'] = trim($row['GRP_STATUS']);
		$grp_list[$x] = $grp;
		$x++;
	}

	return $grp_list;
}


# ... ... ... 43: FetchGroupById... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchGroupById($GRP_ID){
	$grp = array();

	$q = mysql_query("SELECT * FROM notification_groups WHERE GRP_ID='$GRP_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {	
		$grp['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp['GRP_ID'] = trim($row['GRP_ID']);
		$grp['GRP_TYPE_ID'] = trim($row['GRP_TYPE_ID']);
		$grp['GRP_NAME'] = trim($row['GRP_NAME']);
		$grp['DATE_CREATED'] = trim($row['DATE_CREATED']);
		$grp['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp['GRP_STATUS'] = trim($row['GRP_STATUS']);
	}
	return $grp;
}
function FetchGroupByRecordId($RECORD_ID){
	$grp = array();

	$q = mysql_query("SELECT * FROM notification_groups WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {	
		$grp['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp['GRP_ID'] = trim($row['GRP_ID']);
		$grp['GRP_TYPE_ID'] = trim($row['GRP_TYPE_ID']);
		$grp['GRP_NAME'] = trim($row['GRP_NAME']);
		$grp['DATE_CREATED'] = trim($row['DATE_CREATED']);
		$grp['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp['GRP_STATUS'] = trim($row['GRP_STATUS']);
	}
	return $grp;
}

# ... ... ... 44: Fetch Group Type List ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchGroupTypeList($GRP_TYPE_STATUS){
	$grp_type_list = array();
	$x = 0;

	$db_query = "";
	if ($GRP_TYPE_STATUS=="") {
		$db_query = "SELECT * FROM notification_groups_types ORDER BY GRP_TYPE_NAME ASC";
	}
	elseif ($GRP_TYPE_STATUS!="") {
		$db_query = "SELECT * FROM notification_groups_types WHERE GRP_TYPE_STATUS='$GRP_TYPE_STATUS' ORDER BY GRP_TYPE_NAME ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$grp_type = array();
		$grp_type['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp_type['GRP_TYPE_ID'] = trim($row['GRP_TYPE_ID']);
		$grp_type['GRP_TYPE_NAME'] = trim($row['GRP_TYPE_NAME']);
		$grp_type['DATE_CREATED'] = trim($row['DATE_CREATED']);
		$grp_type['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp_type['GRP_TYPE_STATUS'] = trim($row['GRP_TYPE_STATUS']);
		$grp_type_list[$x] = $grp_type;
		$x++;
	}

	return $grp_type_list;
}

# ... ... ... 44: FetchGroupTypeById... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchGroupTypeById($GRP_TYPE_ID){
	$grp_type = array();

	$q = mysql_query("SELECT * FROM notification_groups_types WHERE GRP_TYPE_ID='$GRP_TYPE_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {	
		$grp_type['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp_type['GRP_TYPE_ID'] = trim($row['GRP_TYPE_ID']);
		$grp_type['GRP_TYPE_NAME'] = trim($row['GRP_TYPE_NAME']);
		$grp_type['DATE_CREATED'] = trim($row['DATE_CREATED']);
		$grp_type['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp_type['GRP_TYPE_STATUS'] = trim($row['GRP_TYPE_STATUS']);
	}
	return $grp_type;
}

# ... ... ... 44: FetchGroupMembers ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchGroupMembers($GRP_ID){
	$grp_member_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM notification_group_members WHERE GRP_ID='$GRP_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$grp_membr = array();
		$grp_membr['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp_membr['MEMBER_ID'] = trim($row['MEMBER_ID']);
		$grp_membr['GRP_ID'] = trim($row['GRP_ID']);
		$grp_membr['DATE_CREATED'] = trim($row['DATE_CREATED']);
		$grp_membr['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp_membr['GRP_MEMBER_STATUS'] = trim($row['GRP_MEMBER_STATUS']);
		$grp_member_list[$x] = $grp_membr;
		$x++;
	}

	return $grp_member_list;
}

# ... ... ... 45: FetchMmbrsToAddGroup ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchMmbrsToAddGroup($ADDRESS_ENTITY_TYPE, $ADDRESS_STATUS, $GRP_ID){
	$address_book = array();
	$x = 0;
	$q = mysql_query("SELECT * 
									  FROM notification_addressbook 
									  WHERE ADDRESS_ENTITY_TYPE='$ADDRESS_ENTITY_TYPE'
									    AND ADDRESS_STATUS='$ADDRESS_STATUS' 
									    AND ADDRESS_ENTITY_ID not in (SELECT MEMBER_ID 
									                        				  FROM notification_group_members 
									                        					WHERE GRP_ID='$GRP_ID')
									  ORDER BY ADDRESS_ENTITY_NAME ASC
									") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$address = array();
		$address['RECORD_ID'] = trim($row['RECORD_ID']);
		$address['ADDRESS_ENTITY_TYPE'] = trim($row['ADDRESS_ENTITY_TYPE']);
		$address['ADDRESS_ENTITY_ID'] = trim($row['ADDRESS_ENTITY_ID']);
		$address['ADDRESS_ENTITY_NAME'] = trim($row['ADDRESS_ENTITY_NAME']);
		$address['ADDRESS_ADDED_DATE'] = trim($row['ADDRESS_ADDED_DATE']);
		$address['ADDRESS_STATUS'] = trim($row['ADDRESS_STATUS']);
		$address_book[$x] = $address;
		$x++;
	}
	return $address_book;
}

# ... ... ... 44: FetchGroupMembers ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchInboxMessages($RECIPIENT_ID){
	$notif_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM notifications 
		                WHERE NTFCN_ID not in (SELECT NTFCN_ID
		                                       FROM notification_read_receipt
		                                       WHERE DEL_FLG='Y' AND RECIPIENT_ID='$RECIPIENT_ID')
						  				AND NTFCN_ID in (SELECT DISTINCT(NTFCN_ID)
										   								 FROM notification_recipients
										                   WHERE RECIPIENT_ID = '$RECIPIENT_ID'
																					OR RECIPIENT_ID in (SELECT DISTINCT(GRP_ID) 
																			 FROM notification_group_members 
																			 WHERE MEMBER_ID='$RECIPIENT_ID'))
										ORDER BY SEND_DATE DESC") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$notif_msg = array();
		$notif_msg['RECORD_ID'] = trim($row['RECORD_ID']);
		$notif_msg['NTFCN_ID'] = trim($row['NTFCN_ID']);
		$notif_msg['SENDER_ID'] = trim($row['SENDER_ID']);
		$notif_msg['HAS_ATTCHMT_FLG'] = trim($row['HAS_ATTCHMT_FLG']);
		$notif_msg['RECALL_FLG'] = trim($row['RECALL_FLG']);
		$notif_msg['SEND_DATE'] = trim($row['SEND_DATE']);
		$notif_msg['NTFCN_SUBJECT'] = trim($row['NTFCN_SUBJECT']);
		$notif_msg['NTFCN_MSG'] = trim($row['NTFCN_MSG']);
		$notif_msg['NTFCN_THREAD_FLG'] = trim($row['NTFCN_THREAD_FLG']);
		$notif_msg['NTFCN_THREAD_ID'] = trim($row['NTFCN_THREAD_ID']);
		$notif_msg['NTFCN_MSG_STATUS'] = trim($row['NTFCN_MSG_STATUS']);

		$notif_list[$x] = $notif_msg;
		$x++;
	}

	return $notif_list;
}

# ... ... ... 48: FetchGroupMembers ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchNotifcationMessageById($NTFCN_ID){
	$notif_msg = array();

	$q = mysql_query("SELECT * FROM notifications WHERE NTFCN_ID='$NTFCN_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$notif_msg['RECORD_ID'] = trim($row['RECORD_ID']);
		$notif_msg['NTFCN_ID'] = trim($row['NTFCN_ID']);
		$notif_msg['SENDER_ID'] = trim($row['SENDER_ID']);
		$notif_msg['HAS_ATTCHMT_FLG'] = trim($row['HAS_ATTCHMT_FLG']);
		$notif_msg['RECALL_FLG'] = trim($row['RECALL_FLG']);
		$notif_msg['SEND_DATE'] = trim($row['SEND_DATE']);
		$notif_msg['NTFCN_SUBJECT'] = trim($row['NTFCN_SUBJECT']);
		$notif_msg['NTFCN_MSG'] = trim($row['NTFCN_MSG']);
		$notif_msg['NTFCN_THREAD_FLG'] = trim($row['NTFCN_THREAD_FLG']);
		$notif_msg['NTFCN_THREAD_ID'] = trim($row['NTFCN_THREAD_ID']);
		$notif_msg['NTFCN_MSG_STATUS'] = trim($row['NTFCN_MSG_STATUS']);
	}

	return $notif_msg;
}

function DetermineIffRead($CURRENT_USER, $M_NTFCN_ID){

	$has_been_read = "";
	$q = "SELECT count(*) as RTN_VALUE FROM notification_read_receipt 
	                        WHERE NTFCN_ID='$M_NTFCN_ID' AND RECIPIENT_ID='$CURRENT_USER'";
	$cnt = ReturnOneEntryFromDB($q);
	if ($cnt>0) {
		$has_been_read = "YY";
	}
	else{
		$has_been_read = "NN";
	}
	return $has_been_read;
}

# ... ... ... 48: FetchNotificationRecipientsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchNotificationRecipientsById($NTFCN_ID){
	$rcp_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM notification_recipients WHERE NTFCN_ID='$NTFCN_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		
		$rcp = array();
		$rcp['RECORD_ID'] = trim($row['RECORD_ID']);
		$rcp['NTFCN_ID'] = trim($row['NTFCN_ID']);
		$rcp['RECIPIENT_TYPE'] = trim($row['RECIPIENT_TYPE']);
		$rcp['RECIPIENT_ID'] = trim($row['RECIPIENT_ID']);
		$rcp['RECEIVED_FLG'] = trim($row['RECEIVED_FLG']);
		$rcp['RECEIVED_DATE'] = trim($row['RECEIVED_DATE']);

		$rcp_list[$x] = $rcp;
		$x++;
	}

	return $rcp_list;
}

# ... ... ... 48: FetchNotificationRecipientsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchNotificationThread($NTFCN_THREAD_ID){
	$thread_list = array();
	$x = 0;

	/*$q = mysql_query("SELECT * FROM notification_thread 
		                WHERE NTFCN_THREAD_ID='$NTFCN_THREAD_ID'
		                  AND THREAD_DATE<(select t.THREAD_DATE from notification_thread t where t.NTFCN_ID='$NTFCN_ID')
		                ORDER BY THREAD_DATE DESC") or die("ERR_UPR_LOG: ".mysql_error());*/

	$q = mysql_query("SELECT * FROM notification_thread 
		                WHERE NTFCN_THREAD_ID='$NTFCN_THREAD_ID'
		                ORDER BY THREAD_DATE DESC") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		
		$thread = array();
		$thread['RECORD_ID'] = trim($row['RECORD_ID']);
		$thread['NTFCN_THREAD_ID'] = trim($row['NTFCN_THREAD_ID']);
		$thread['NTFCN_ID'] = trim($row['NTFCN_ID']);
		$thread['THREAD_TYPE'] = trim($row['THREAD_TYPE']);
		$thread['THREAD_DATE'] = trim($row['THREAD_DATE']);

		$thread_list[$x] = $thread;
		$x++;
	}

	return $thread_list;
}

# ... ... ... 48: FetchNotifcationAttachmentsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchNotifcationAttachmentsById($NTFCN_ID){
	$attmt_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM notification_attachments WHERE NTFCN_ID='$NTFCN_ID' AND ATTCHMT_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$attmt = array();
		$attmt['RECORD_ID'] = trim($row['RECORD_ID']);
		$attmt['NTFCN_ID'] = trim($row['NTFCN_ID']);
		$attmt['ATTCHMT_NAME'] = trim($row['ATTCHMT_NAME']);
		$attmt['ATTCHMT_STATUS'] = trim($row['ATTCHMT_STATUS']);

		$attmt_list[$x] = $attmt;
		$x++;
	}

	return $attmt_list;
}

# ... ... ... 44: FetchSentMessages ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSentMessages($SENDER_ID){
	$notif_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM notifications WHERE SENDER_ID='$SENDER_ID' AND SENDER_DEL_FLG='NN'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$notif_msg = array();
		$notif_msg['RECORD_ID'] = trim($row['RECORD_ID']);
		$notif_msg['NTFCN_ID'] = trim($row['NTFCN_ID']);
		$notif_msg['SENDER_ID'] = trim($row['SENDER_ID']);
		$notif_msg['HAS_ATTCHMT_FLG'] = trim($row['HAS_ATTCHMT_FLG']);
		$notif_msg['RECALL_FLG'] = trim($row['RECALL_FLG']);
		$notif_msg['SEND_DATE'] = trim($row['SEND_DATE']);
		$notif_msg['NTFCN_SUBJECT'] = trim($row['NTFCN_SUBJECT']);
		$notif_msg['NTFCN_MSG'] = trim($row['NTFCN_MSG']);
		$notif_msg['NTFCN_THREAD_ID'] = trim($row['NTFCN_THREAD_ID']);		
		$notif_msg['NTFCN_MSG_STATUS'] = trim($row['NTFCN_MSG_STATUS']);

		$notif_list[$x] = $notif_msg;
		$x++;
	}

	return $notif_list;
}

# ... ... ... 44: FetchThreadCount ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchThreadCount($NTFCN_THREAD_ID, $NTFCN_ID){
	$thread_count = "";
	$thrd_list = array();
	$x = 1;

	$q = mysql_query("SELECT * FROM notification_thread WHERE NTFCN_THREAD_ID='$NTFCN_THREAD_ID' ORDER BY RECORD_ID ASC") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$notif_msg = array();
		$DB_NTFCN_ID = trim($row['NTFCN_ID']);
		
		$thrd_list[$DB_NTFCN_ID] = $x;
		$x++;
	}
	
	$thread_count = $thrd_list[$NTFCN_ID];
	

	return $thread_count;
}

# ... ... ... 44: FetchTrash ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTrash($RECIPIENT_ID){
	$trash_list = array();
	$inbox_trash_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM notification_read_receipt WHERE RECIPIENT_ID='$RECIPIENT_ID' AND DEL_FLG='Y'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$trash = array();
		$trash['NTFCN_ID'] = trim($row['NTFCN_ID']);
		$inbox_trash_list[$x] = $trash;
		$x++;
	}

	# ... Sent Messages
	$sent_trash_list = array();
	$y = 0;
	$q = mysql_query("SELECT * FROM notifications WHERE SENDER_ID='$RECIPIENT_ID' AND SENDER_DEL_FLG='YY'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$trash = array();
		$trash['NTFCN_ID'] = trim($row['NTFCN_ID']);
		$sent_trash_list[$y] = $trash;
		$y++;
	}

	$trash_list = array_merge($inbox_trash_list, $sent_trash_list);
	return $trash_list;
}

# ... ... ... 44: FeCheckIFFThreadIsDeletedtchTrash ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function CheckIFFThreadIsDeleted($THREAD_ID, $NTFCN_ID, $ENTITY_ID){
	$del = "";
	$q = mysql_query("SELECT * FROM notification_thread_delete WHERE THREAD_ID='$THREAD_ID' AND NTFCN_ID='$NTFCN_ID' AND ENTITY_ID='$ENTITY_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	if (mysql_num_rows($q)>0) {
		$del = "YY";
	} else {
		$del = "NN";
	}

	return $del;
}


# ... ... ... 44: FetchGroupMembers ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchNftcnsByScope($NFTCN_LIST){
	$notif_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM notifications WHERE NTFCN_ID in ($NFTCN_LIST)") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$notif_msg = array();
		$notif_msg['RECORD_ID'] = trim($row['RECORD_ID']);
		$notif_msg['NTFCN_ID'] = trim($row['NTFCN_ID']);
		$notif_msg['SENDER_ID'] = trim($row['SENDER_ID']);
		$notif_msg['HAS_ATTCHMT_FLG'] = trim($row['HAS_ATTCHMT_FLG']);
		$notif_msg['RECALL_FLG'] = trim($row['RECALL_FLG']);
		$notif_msg['SEND_DATE'] = trim($row['SEND_DATE']);
		$notif_msg['NTFCN_SUBJECT'] = trim($row['NTFCN_SUBJECT']);
		$notif_msg['NTFCN_MSG'] = trim($row['NTFCN_MSG']);
		$notif_msg['NTFCN_THREAD_FLG'] = trim($row['NTFCN_THREAD_FLG']);
		$notif_msg['NTFCN_THREAD_ID'] = trim($row['NTFCN_THREAD_ID']);
		$notif_msg['NTFCN_MSG_STATUS'] = trim($row['NTFCN_MSG_STATUS']);

		$notif_list[$x] = $notif_msg;
		$x++;
	}

	return $notif_list;
}


# **..** **..** **..** **..** **..** SECTION 06: Application Management Groups **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 06: Application Management Groups **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 06: Application Management Groups **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 06: Application Management Groups **..** **..** **..** **..** **..**  **..** **..** 


# ... ... ... : Fetch Group List ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAppMgtGroupList($GRP_STATUS){
	$grp_list = array();
	$x = 0;

	$db_query = "";
	if ($GRP_STATUS=="") {
		$db_query = "SELECT * FROM appln_mgt_group ORDER BY GRP_NAME ASC";
	}
	elseif ($GRP_STATUS!="") {
		$db_query = "SELECT * FROM appln_mgt_group WHERE GRP_STATUS='$GRP_STATUS' ORDER BY GRP_NAME ASC";
	}

		$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$grp = array();
		$grp['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp['GRP_ID'] = trim($row['GRP_ID']);
		$grp['GRP_NAME'] = trim($row['GRP_NAME']);
		$grp['CREATED_ON'] = trim($row['CREATED_ON']);
		$grp['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp['GRP_STATUS'] = trim($row['GRP_STATUS']);
		$grp_list[$x] = $grp;
		$x++;
	}

	return $grp_list;
}

# ... ... ... : FetchGroupById... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAppMgtGroupById($GRP_ID){
	$grp = array();

	$q = mysql_query("SELECT * FROM appln_mgt_group WHERE GRP_ID='$GRP_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {	
		$grp['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp['GRP_ID'] = trim($row['GRP_ID']);
		$grp['GRP_NAME'] = trim($row['GRP_NAME']);
		$grp['CREATED_ON'] = trim($row['CREATED_ON']);
		$grp['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp['GRP_STATUS'] = trim($row['GRP_STATUS']);
	}
	return $grp;
}
function FetchAppMgtGroupByRecordId($RECORD_ID){
	$grp = array();

	$q = mysql_query("SELECT * FROM appln_mgt_group WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {	
		$grp['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp['GRP_ID'] = trim($row['GRP_ID']);
		$grp['GRP_NAME'] = trim($row['GRP_NAME']);
		$grp['CREATED_ON'] = trim($row['CREATED_ON']);
		$grp['CREATED_BY'] = trim($row['CREATED_BY']);
		$grp['GRP_STATUS'] = trim($row['GRP_STATUS']);
	}
	return $grp;
}

# ... ... ... : FetchAppMgtGroupMembers... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAppMgtGroupMembers($GRP_ID){
	$grp_member_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM appln_mgt_group_members WHERE GRP_ID='$GRP_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$grp_membr = array();
		$grp_membr['RECORD_ID'] = trim($row['RECORD_ID']);
		$grp_membr['GRP_ID'] = trim($row['GRP_ID']);
		$grp_membr['GRP_MEMBER_ID'] = trim($row['GRP_MEMBER_ID']);
		$grp_membr['ADDED_BY'] = trim($row['ADDED_BY']);
		$grp_membr['CREATED_ON'] = trim($row['CREATED_ON']);
		$grp_membr['GRP_MEMBER_STATUS'] = trim($row['GRP_MEMBER_STATUS']);
		$grp_member_list[$x] = $grp_membr;
		$x++;
	}

	return $grp_member_list;
}

# ... ... ... F25: Fetch System User List... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchAppMgtSysUserList($GRP_ID){
	$usr_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM upr 
		                WHERE USER_STATUS='ACTIVE'
		                  AND USER_ID not in (
		                  	SELECT GRP_MEMBER_ID FROM appln_mgt_group_members
		                  	WHERE GRP_ID='$GRP_ID'
		                  		AND GRP_MEMBER_STATUS='ACTIVE'
		                	)") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$usr = array();
		$usr['RECORD_ID'] = trim($row['RECORD_ID']);
		$usr['USER_ID'] = trim($row['USER_ID']);
		$usr['USER_CORE_ID'] = trim($row['USER_CORE_ID']);
		$usr['GENDER'] = trim($row['GENDER']);
		$usr['PHONE'] = trim($row['PHONE']);
		$usr['EMAIL_ADDRESS'] = trim($row['EMAIL_ADDRESS']);
		$usr['LOGGED_IN'] = trim($row['LOGGED_IN']);
		$usr['ADDED_ON'] = trim($row['ADDED_ON']);
		$usr['ADDED_BY'] = trim($row['ADDED_BY']);
		$usr['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$usr['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$usr['LAST_CHNGD_BY'] = trim($row['LAST_CHNGD_BY']);
		$usr['LAST_CHNGD_ON'] = trim($row['LAST_CHNGD_ON']);
		$usr['USER_STATUS'] = trim($row['USER_STATUS']);

		$usr_list[$x] = $usr;

		$x++;
	}

	return $usr_list;
}

# ... ... ... : Fetch Group List ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchApplnTypes($APPLN_TYPE_STATUS){
	$appln_type_list = array();
	$x = 0;

	$db_query = "";
	if ($GRP_STATUS=="") {
		$db_query = "SELECT * FROM appln_types ORDER BY APPLN_TYPE_NAME ASC";
	}
	elseif ($GRP_STATUS!="") {
		$db_query = "SELECT * FROM appln_types WHERE APPLN_TYPE_STATUS='$APPLN_TYPE_STATUS' ORDER BY APPLN_TYPE_NAME ASC";
	}

		$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$appln_type = array();
		$appln_type['RECORD_ID'] = trim($row['RECORD_ID']);
		$appln_type['APPLN_TYPE_ID'] = trim($row['APPLN_TYPE_ID']);
		$appln_type['APPLN_TYPE_NAME'] = trim($row['APPLN_TYPE_NAME']);
		$appln_type['APPLN_TYPE_STATUS'] = trim($row['APPLN_TYPE_STATUS']);
		$appln_type_list[$x] = $appln_type;
		$x++;
	}

	return $appln_type_list;
}

# ... ... ... : Fetch Group List ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchApplnConfigs($APPLN_CONFIG_STATUS){
	$appln_configs_list = array();
	$x = 0;

	$db_query = "";
	if ($APPLN_CONFIG_STATUS=="") {
		$db_query = "SELECT * FROM appln_configs ORDER BY APPLN_CONFIG_NAME ASC";
	}
	elseif ($APPLN_CONFIG_STATUS!="") {
		$db_query = "SELECT * FROM appln_configs WHERE APPLN_CONFIG_STATUS='$APPLN_CONFIG_STATUS' ORDER BY APPLN_CONFIG_NAME ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$appln_config = array();
		$appln_config['RECORD_ID'] = trim($row['RECORD_ID']);
		$appln_config['APPLN_CONFIG_ID'] = trim($row['APPLN_CONFIG_ID']);
		$appln_config['APPLN_CONFIG_NAME'] = trim($row['APPLN_CONFIG_NAME']);
		$appln_config['APPLN_TYPE_ID'] = trim($row['APPLN_TYPE_ID']);
		$appln_config['PDT_ID'] = trim($row['PDT_ID']);
		$appln_config['PDT_TYPE_ID'] = trim($row['PDT_TYPE_ID']);
		$appln_config['PRM_01'] = trim($row['PRM_01']);
		$appln_config['PRM_02'] = trim($row['PRM_02']);
		$appln_config['PRM_03'] = trim($row['PRM_03']);
		$appln_config['PRM_04'] = trim($row['PRM_04']);
		$appln_config['PRM_05'] = trim($row['PRM_05']);
		$appln_config['PRM_06'] = trim($row['PRM_06']);
		$appln_config['PRM_07'] = trim($row['PRM_07']);
		$appln_config['PRM_08'] = trim($row['PRM_08']);
		$appln_config['PRM_09'] = trim($row['PRM_09']);
		$appln_config['PRM_10'] = trim($row['PRM_10']);
		$appln_config['PRM_11'] = trim($row['PRM_11']);
		$appln_config['PRM_12'] = trim($row['PRM_12']);
		$appln_config['PRM_13'] = trim($row['PRM_13']);
		$appln_config['PRM_14'] = trim($row['PRM_14']);
		$appln_config['PRM_15'] = trim($row['PRM_15']);
		$appln_config['PRM_16'] = trim($row['PRM_16']);
		$appln_config['PRM_17'] = trim($row['PRM_17']);
		$appln_config['PRM_18'] = trim($row['PRM_18']);
		$appln_config['PRM_19'] = trim($row['PRM_19']);
		$appln_config['PRM_20'] = trim($row['PRM_20']);
		$appln_config['PRM_21'] = trim($row['PRM_21']);
		$appln_config['PRM_22'] = trim($row['PRM_22']);
		$appln_config['PRM_23'] = trim($row['PRM_23']);
		$appln_config['PRM_24'] = trim($row['PRM_24']);
		$appln_config['PRM_25'] = trim($row['PRM_25']);
		$appln_config['PRM_26'] = trim($row['PRM_26']);
		$appln_config['PRM_27'] = trim($row['PRM_27']);
		$appln_config['PRM_28'] = trim($row['PRM_28']);
		$appln_config['PRM_29'] = trim($row['PRM_29']);
		$appln_config['PRM_30'] = trim($row['PRM_30']);
		$appln_config['CREATED_BY'] = trim($row['CREATED_BY']);
		$appln_config['CREATED_ON'] = trim($row['CREATED_ON']);
		$appln_config['APPLN_CONFIG_STATUS'] = trim($row['APPLN_CONFIG_STATUS']);
		$appln_configs_list[$x] = $appln_config;
		$x++;

	}

	return $appln_configs_list;
}

# ... ... ... : Fetch Group List ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchApplnConfigById($APPLN_CONFIG_ID){
	$appln_config = array();
	$q = mysql_query("SELECT * FROM appln_configs WHERE APPLN_CONFIG_ID='$APPLN_CONFIG_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$appln_config['RECORD_ID'] = trim($row['RECORD_ID']);
		$appln_config['APPLN_CONFIG_ID'] = trim($row['APPLN_CONFIG_ID']);
		$appln_config['APPLN_CONFIG_NAME'] = trim($row['APPLN_CONFIG_NAME']);
		$appln_config['APPLN_TYPE_ID'] = trim($row['APPLN_TYPE_ID']);
		$appln_config['PDT_ID'] = trim($row['PDT_ID']);
		$appln_config['PDT_TYPE_ID'] = trim($row['PDT_TYPE_ID']);
		$appln_config['PRM_01'] = trim($row['PRM_01']);
		$appln_config['PRM_02'] = trim($row['PRM_02']);
		$appln_config['PRM_03'] = trim($row['PRM_03']);
		$appln_config['PRM_04'] = trim($row['PRM_04']);
		$appln_config['PRM_05'] = trim($row['PRM_05']);
		$appln_config['PRM_06'] = trim($row['PRM_06']);
		$appln_config['PRM_07'] = trim($row['PRM_07']);
		$appln_config['PRM_08'] = trim($row['PRM_08']);
		$appln_config['PRM_09'] = trim($row['PRM_09']);
		$appln_config['PRM_10'] = trim($row['PRM_10']);
		$appln_config['PRM_11'] = trim($row['PRM_11']);
		$appln_config['PRM_12'] = trim($row['PRM_12']);
		$appln_config['PRM_13'] = trim($row['PRM_13']);
		$appln_config['PRM_14'] = trim($row['PRM_14']);
		$appln_config['PRM_15'] = trim($row['PRM_15']);
		$appln_config['PRM_16'] = trim($row['PRM_16']);
		$appln_config['PRM_17'] = trim($row['PRM_17']);
		$appln_config['PRM_18'] = trim($row['PRM_18']);
		$appln_config['PRM_19'] = trim($row['PRM_19']);
		$appln_config['PRM_20'] = trim($row['PRM_20']);
		$appln_config['PRM_21'] = trim($row['PRM_21']);
		$appln_config['PRM_22'] = trim($row['PRM_22']);
		$appln_config['PRM_23'] = trim($row['PRM_23']);
		$appln_config['PRM_24'] = trim($row['PRM_24']);
		$appln_config['PRM_25'] = trim($row['PRM_25']);
		$appln_config['PRM_26'] = trim($row['PRM_26']);
		$appln_config['PRM_27'] = trim($row['PRM_27']);
		$appln_config['PRM_28'] = trim($row['PRM_28']);
		$appln_config['PRM_29'] = trim($row['PRM_29']);
		$appln_config['PRM_30'] = trim($row['PRM_30']);
		$appln_config['CREATED_BY'] = trim($row['CREATED_BY']);
		$appln_config['CREATED_ON'] = trim($row['CREATED_ON']);
		$appln_config['APPLN_CONFIG_STATUS'] = trim($row['APPLN_CONFIG_STATUS']);
	}

	return $appln_config;
}

# ... ... ... : Fetch Group List ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchApplnTypeMenu($APPLN_TYPE_ID){
	$config_param_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM appln_config_param_menu WHERE APPLN_TYPE_ID='".$APPLN_TYPE_ID."' AND PRM_STATUS='ACTIVE' ORDER BY PRM_FEATURE_ID ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$config_param = array();
		$config_param['RECORD_ID'] = trim($row['RECORD_ID']);
		$config_param['APPLN_TYPE_ID'] = trim($row['APPLN_TYPE_ID']);
		$config_param['PRM_FEATURE_ID'] = trim($row['PRM_FEATURE_ID']);
		$config_param['PRM_FEATURE_VALUE'] = trim($row['PRM_FEATURE_VALUE']);
		$config_param['PRM_INPUT_TYPE'] = trim($row['PRM_INPUT_TYPE']);
		$config_param['PRM_STATUS'] = trim($row['PRM_STATUS']);
		$config_param_list[$x] = $config_param;

		$x++;
	}

	return $config_param_list;
}





# **..** **..** **..** **..** **..** SECTION 07: Transaction Maintenace **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 07: Transaction Maintenace **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 07: Transaction Maintenace **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 07: Transaction Maintenace **..** **..** **..** **..** **..**  **..** **..** 


# ... ... ... 7.1: FetchTransactionTypes ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionTypes($TRAN_TYPE_STATUS){
	$tt_list = array();
	$x = 0;

	$db_query = "";
	if ($TRAN_TYPE_STATUS=="") {
		$db_query = "SELECT * FROM txn_types ORDER BY TRAN_TYPE_NAME ASC";
	}
	elseif ($TRAN_TYPE_STATUS!="") {
		$db_query = "SELECT * FROM txn_types WHERE TRAN_TYPE_STATUS='$TRAN_TYPE_STATUS' ORDER BY TRAN_TYPE_NAME ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$tt = array();
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_TYPE_ID'] = trim($row['TRAN_TYPE_ID']);
		$tt['TRAN_TYPE_NAME'] = trim($row['TRAN_TYPE_NAME']);
		$tt['TRAN_DESC'] = trim($row['TRAN_DESC']);
		$tt['CHRG_FLG'] = trim($row['CHRG_FLG']);
		$tt['CHRG_EVENT_ID'] = trim($row['CHRG_EVENT_ID']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_TYPE_STATUS'] = trim($row['TRAN_TYPE_STATUS']);
		$tt_list[$x] = $tt;
		$x++;

	}

	return $tt_list;
}

# ... ... ... 7.2: FetchTransactionTypeByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionTypeByRecordId($RECORD_ID){
	$tt = array();
	$q = mysql_query("SELECT * FROM txn_types WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_TYPE_ID'] = trim($row['TRAN_TYPE_ID']);
		$tt['TRAN_TYPE_NAME'] = trim($row['TRAN_TYPE_NAME']);
		$tt['TRAN_DESC'] = trim($row['TRAN_DESC']);
		$tt['CHRG_FLG'] = trim($row['CHRG_FLG']);
		$tt['CHRG_EVENT_ID'] = trim($row['CHRG_EVENT_ID']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_TYPE_STATUS'] = trim($row['TRAN_TYPE_STATUS']);
	}
	return $tt;
}

# ... ... ... 7.3: FetchTransactionTypeById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionTypeById($TRAN_TYPE_ID){
	$tt = array();
	$q = mysql_query("SELECT * FROM txn_types WHERE TRAN_TYPE_ID='$TRAN_TYPE_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_TYPE_ID'] = trim($row['TRAN_TYPE_ID']);
		$tt['TRAN_TYPE_NAME'] = trim($row['TRAN_TYPE_NAME']);
		$tt['TRAN_DESC'] = trim($row['TRAN_DESC']);
		$tt['CHRG_FLG'] = trim($row['CHRG_FLG']);
		$tt['CHRG_EVENT_ID'] = trim($row['CHRG_EVENT_ID']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_TYPE_STATUS'] = trim($row['TRAN_TYPE_STATUS']);
	}
	return $tt;
}

# ... ... ... 7.4: FetchTransactionTypes ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionCharges($TRAN_CHRG_STATUS){
	$tt_list = array();
	$x = 0;

	$db_query = "";
	if ($TRAN_CHRG_STATUS=="") {
		$db_query = "SELECT * FROM txn_charges ORDER BY TRAN_CHRG_NAME ASC";
	}
	elseif ($TRAN_CHRG_STATUS!="") {
		$db_query = "SELECT * FROM txn_charges WHERE TRAN_CHRG_STATUS='$TRAN_CHRG_STATUS' ORDER BY TRAN_CHRG_NAME ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$tt = array();
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_CHRG_ID'] = trim($row['TRAN_CHRG_ID']);
		$tt['TRAN_CHRG_NAME'] = trim($row['TRAN_CHRG_NAME']);
		$tt['TRAN_CHRG_DESC'] = trim($row['TRAN_CHRG_DESC']);
		$tt['TRAN_CHRG_TYPE'] = trim($row['TRAN_CHRG_TYPE']);
		$tt['CORE_CR_ACCT_ID'] = trim($row['CORE_CR_ACCT_ID']);
		$tt['TRAN_NRRTN_PREFIX'] = trim($row['TRAN_NRRTN_PREFIX']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_CHRG_STATUS'] = trim($row['TRAN_CHRG_STATUS']);
		$tt_list[$x] = $tt;
		$x++;

	}

	return $tt_list;
}

# ... ... ... 7.5: FetchTransactionTypes ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionChargesByRecordId($RECORD_ID){
	$tt = array();
	$q = mysql_query("SELECT * FROM txn_charges WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_CHRG_ID'] = trim($row['TRAN_CHRG_ID']);
		$tt['TRAN_CHRG_NAME'] = trim($row['TRAN_CHRG_NAME']);
		$tt['TRAN_CHRG_DESC'] = trim($row['TRAN_CHRG_DESC']);
		$tt['TRAN_CHRG_TYPE'] = trim($row['TRAN_CHRG_TYPE']);
		$tt['CORE_CR_ACCT_ID'] = trim($row['CORE_CR_ACCT_ID']);
		$tt['TRAN_NRRTN_PREFIX'] = trim($row['TRAN_NRRTN_PREFIX']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_CHRG_STATUS'] = trim($row['TRAN_CHRG_STATUS']);
	}
	return $tt;
}

# ... ... ... 7.555: FetchTransactionTypes ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionChargesByTranTypeId($TRAN_TYPE_ID){
	$tt_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM txn_charges
										WHERE TRAN_CHRG_STATUS='ACTIVE' 
										  AND TRAN_CHRG_ID!='HJK0001'
										  AND TRAN_CHRG_ID in ( 
										     SELECT TRAN_CHRG_ID FROM txn_charge_event_items
											   WHERE CHRG_EVENT_ITEM_STATUS='ACTIVE' 
										       AND CHRG_EVNT_ID=(
													SELECT CHRG_EVNT_ID FROM txn_charge_events
													WHERE TRAN_CHRG_STATUS='ACTIVE'
											      AND CHRG_EVNT_ID=(select CHRG_EVENT_ID from txn_types where TRAN_TYPE_ID='$TRAN_TYPE_ID')
										      )
										)") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$tt = array();
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_CHRG_ID'] = trim($row['TRAN_CHRG_ID']);
		$tt['TRAN_CHRG_NAME'] = trim($row['TRAN_CHRG_NAME']);
		$tt['TRAN_CHRG_DESC'] = trim($row['TRAN_CHRG_DESC']);
		$tt['TRAN_CHRG_TYPE'] = trim($row['TRAN_CHRG_TYPE']);
		$tt['CORE_CR_ACCT_ID'] = trim($row['CORE_CR_ACCT_ID']);
		$tt['TRAN_NRRTN_PREFIX'] = trim($row['TRAN_NRRTN_PREFIX']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_CHRG_STATUS'] = trim($row['TRAN_CHRG_STATUS']);
		$tt_list[$x] = $tt;
		$x++;

	}

	return $tt_list;
}

# ... ... ... 7.6: FetchTransactionChargesById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionChargesById($TRAN_CHRG_ID){
	$tt = array();
	$q = mysql_query("SELECT * FROM txn_charges WHERE TRAN_CHRG_ID='$TRAN_CHRG_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_CHRG_ID'] = trim($row['TRAN_CHRG_ID']);
		$tt['TRAN_CHRG_NAME'] = trim($row['TRAN_CHRG_NAME']);
		$tt['TRAN_CHRG_DESC'] = trim($row['TRAN_CHRG_DESC']);
		$tt['TRAN_CHRG_TYPE'] = trim($row['TRAN_CHRG_TYPE']);
		$tt['CORE_CR_ACCT_ID'] = trim($row['CORE_CR_ACCT_ID']);
		$tt['TRAN_NRRTN_PREFIX'] = trim($row['TRAN_NRRTN_PREFIX']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_CHRG_STATUS'] = trim($row['TRAN_CHRG_STATUS']);
	}
	return $tt;
}

# ... ... ... 7.7: FetchTransactionChargeEvents ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionChargeEvents($TRAN_CHRG_STATUS){
	$tt_list = array();
	$x = 0;

	$db_query = "";
	if ($TRAN_CHRG_STATUS=="") {
		$db_query = "SELECT * FROM txn_charge_events ORDER BY CHRG_EVNT_NAME ASC";
	}
	elseif ($TRAN_CHRG_STATUS!="") {
		$db_query = "SELECT * FROM txn_charge_events WHERE TRAN_CHRG_STATUS='$TRAN_CHRG_STATUS' ORDER BY CHRG_EVNT_NAME ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tt = array();
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['CHRG_EVNT_ID'] = trim($row['CHRG_EVNT_ID']);
		$tt['CHRG_EVNT_NAME'] = trim($row['CHRG_EVNT_NAME']);
		$tt['CHRG_EVNT_DESC'] = trim($row['CHRG_EVNT_DESC']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_CHRG_STATUS'] = trim($row['TRAN_CHRG_STATUS']);
		$tt_list[$x] = $tt;
		$x++;
	}
	return $tt_list;
}

# ... ... ... 7.8: FetchTransactionChargeEventsByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionChargeEventsByRecordId($RECORD_ID){
	$tt = array();
	$q = mysql_query("SELECT * FROM txn_charge_events WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['CHRG_EVNT_ID'] = trim($row['CHRG_EVNT_ID']);
		$tt['CHRG_EVNT_NAME'] = trim($row['CHRG_EVNT_NAME']);
		$tt['CHRG_EVNT_DESC'] = trim($row['CHRG_EVNT_DESC']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_CHRG_STATUS'] = trim($row['TRAN_CHRG_STATUS']);
	}
	return $tt;
}

# ... ... ... 7.9: FetchTransactionChargeEventsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTransactionChargeEventsById($CHRG_EVNT_ID){
	$tt = array();
	$q = mysql_query("SELECT * FROM txn_charge_events WHERE CHRG_EVNT_ID='$CHRG_EVNT_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['CHRG_EVNT_ID'] = trim($row['CHRG_EVNT_ID']);
		$tt['CHRG_EVNT_NAME'] = trim($row['CHRG_EVNT_NAME']);
		$tt['CHRG_EVNT_DESC'] = trim($row['CHRG_EVNT_DESC']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_CHRG_STATUS'] = trim($row['TRAN_CHRG_STATUS']);
	}
	return $tt;
}

# ... ... ... 7.10: FetchTransactionChargeEvents ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTranChargeAmountsForChargeId($TRAN_CHRG_ID){
	$tt_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM txn_charge_amounts WHERE TRAN_CHRG_ID='$TRAN_CHRG_ID' AND TRAN_CHRG_AMT_STATUS='ACTIVE' ORDER BY CHRG_LOW ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tt = array();
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_CHRG_AMT_ID'] = trim($row['TRAN_CHRG_AMT_ID']);
		$tt['TRAN_CHRG_ID'] = trim($row['TRAN_CHRG_ID']);
		$tt['CHRG_LOW'] = trim($row['CHRG_LOW']);
		$tt['CHRG_HIGH'] = trim($row['CHRG_HIGH']);
		$tt['CHRG_AMT'] = trim($row['CHRG_AMT']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['TRAN_CHRG_AMT_STATUS'] = trim($row['TRAN_CHRG_AMT_STATUS']);
		$tt_list[$x] = $tt;
		$x++;
	}
	return $tt_list;
}

# ... ... ... 7.11: FetchTransactionChargeEvents ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ValidateChargeBlock($TRAN_CHRG_ID, $CHRG_LOW, $CHRG_HIGH){
	# ... Response
	$resp = array();
	$resp["CODE"] = "OKAY";
	$resp["MSSG"] = "Tier block is okay";


	# ... Validate Charge Block
	$tt_list = array();
	$tt_list = FetchTranChargeAmountsForChargeId($TRAN_CHRG_ID);
	for ($i=0; $i < sizeof($tt_list); $i++) { 
		$tt = array();
		$tt = $tt_list[$i];
		$CC_RECORD_ID = $tt['RECORD_ID'];
		$CC_TRAN_CHRG_AMT_ID = $tt['TRAN_CHRG_AMT_ID'];
		$CC_TRAN_CHRG_ID = $tt['TRAN_CHRG_ID'];
		$CC_CHRG_LOW = $tt['CHRG_LOW'];
		$CC_CHRG_HIGH = $tt['CHRG_HIGH'];
		$CC_CHRG_AMT = $tt['CHRG_AMT'];
		$CC_CREATED_BY = $tt['CREATED_BY'];
		$CC_CREATED_ON = $tt['CREATED_ON'];
		$CC_TRAN_CHRG_AMT_STATUS = $tt['TRAN_CHRG_AMT_STATUS'];

		# ... Validate Low Limit
		if(in_array($CHRG_LOW,range($CC_CHRG_LOW, $CC_CHRG_HIGH))  ){
    	$resp["CODE"] = "ERRR";
			$resp["MSSG"] = "Lower tier limit is already defined for this charge. Cannot Continue";
    	break;
		} 
		
		# ... Validate Upper Limit
		if(in_array($CHRG_HIGH,range($CC_CHRG_LOW, $CC_CHRG_HIGH))  ){
    	$resp["CODE"] = "ERRR";
			$resp["MSSG"] = "Upper tier limit is already defined for this charge. Cannot Continue";
    	break;
		} 


	}



	return $resp;
}

# ... ... ... 7.12: FetchTransactionChargeEvents ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTranChargeAmountsForRecordId($RECORD_ID){
	$tt = array();
	$q = mysql_query("SELECT * FROM txn_charge_amounts WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_CHRG_AMT_ID'] = trim($row['TRAN_CHRG_AMT_ID']);
		$tt['TRAN_CHRG_ID'] = trim($row['TRAN_CHRG_ID']);
		$tt['CHRG_LOW'] = trim($row['CHRG_LOW']);
		$tt['CHRG_HIGH'] = trim($row['CHRG_HIGH']);
		$tt['CHRG_AMT'] = trim($row['CHRG_AMT']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['TRAN_CHRG_AMT_STATUS'] = trim($row['TRAN_CHRG_AMT_STATUS']);
	}
	return $tt;
}

# ... ... ... 7.13: FetchChargesToAddToChargeEvent ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchChargesToAddToChargeEvent($CHRG_EVNT_ID){
	$tt_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM txn_charges
		                WHERE TRAN_CHRG_ID not in (
		                  			SELECT TRAN_CHRG_ID FROM txn_charge_event_items
		                  			WHERE CHRG_EVNT_ID='$CHRG_EVNT_ID'
		                  				AND CHRG_EVENT_ITEM_STATUS='ACTIVE'
		                  		)
		                ORDER BY TRAN_CHRG_NAME ASC") or die("ERR_UPR_LOG: ".mysql_error());


	while ($row = mysql_fetch_array($q)) {
		$tt = array();
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_CHRG_ID'] = trim($row['TRAN_CHRG_ID']);
		$tt['TRAN_CHRG_NAME'] = trim($row['TRAN_CHRG_NAME']);
		$tt['TRAN_CHRG_DESC'] = trim($row['TRAN_CHRG_DESC']);
		$tt['TRAN_CHRG_TYPE'] = trim($row['TRAN_CHRG_TYPE']);
		$tt['CORE_CR_ACCT_ID'] = trim($row['CORE_CR_ACCT_ID']);
		$tt['TRAN_NRRTN_PREFIX'] = trim($row['TRAN_NRRTN_PREFIX']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_CHRG_STATUS'] = trim($row['TRAN_CHRG_STATUS']);
		$tt_list[$x] = $tt;
		$x++;
	}
	return $tt_list;
}

# ... ... ... 7.14: FetchChargesToAddToChargeEvent ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchChargesRelatedToChrgEventId($CHRG_EVNT_ID){
	$tt_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM txn_charges
		                WHERE TRAN_CHRG_ID in (
		                  			SELECT TRAN_CHRG_ID FROM txn_charge_event_items
		                  			WHERE CHRG_EVNT_ID='$CHRG_EVNT_ID'
		                  				AND CHRG_EVENT_ITEM_STATUS='ACTIVE'
		                  		)
		                ORDER BY TRAN_CHRG_NAME ASC") or die("ERR_UPR_LOG: ".mysql_error());


	while ($row = mysql_fetch_array($q)) {
		$tt = array();
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['TRAN_CHRG_ID'] = trim($row['TRAN_CHRG_ID']);
		$tt['TRAN_CHRG_NAME'] = trim($row['TRAN_CHRG_NAME']);
		$tt['TRAN_CHRG_DESC'] = trim($row['TRAN_CHRG_DESC']);
		$tt['TRAN_CHRG_TYPE'] = trim($row['TRAN_CHRG_TYPE']);
		$tt['CORE_CR_ACCT_ID'] = trim($row['CORE_CR_ACCT_ID']);
		$tt['TRAN_NRRTN_PREFIX'] = trim($row['TRAN_NRRTN_PREFIX']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['LST_CHNG_BY'] = trim($row['LST_CHNG_BY']);
		$tt['LST_CHNG_ON'] = trim($row['LST_CHNG_ON']);
		$tt['TRAN_CHRG_STATUS'] = trim($row['TRAN_CHRG_STATUS']);
		$tt_list[$x] = $tt;
		$x++;
	}
	return $tt_list;
}

# ... ... ... 7.15: FetchChargesEventRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchChargesEventItemRecordId($CHRG_EVNT_ID, $TRAN_CHRG_ID){
	$RECORD_ID = "";
	$q = mysql_query("SELECT RECORD_ID FROM txn_charge_event_items WHERE CHRG_EVNT_ID='$CHRG_EVNT_ID' AND TRAN_CHRG_ID='$TRAN_CHRG_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$RECORD_ID = trim($row['RECORD_ID']);
	}
	return $RECORD_ID;
}

# ... ... ... 7.16: FetchTransactionChargeEvents ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchChargeEventItemByRecordId($RECORD_ID){
	$tt = array();
	$q = mysql_query("SELECT * FROM txn_charge_event_items WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tt['RECORD_ID'] = trim($row['RECORD_ID']);
		$tt['CHRG_EVNT_ID'] = trim($row['CHRG_EVNT_ID']);
		$tt['TRAN_CHRG_ID'] = trim($row['TRAN_CHRG_ID']);
		$tt['CREATED_BY'] = trim($row['CREATED_BY']);
		$tt['CREATED_ON'] = trim($row['CREATED_ON']);
		$tt['CHRG_EVENT_ITEM_STATUS'] = trim($row['CHRG_EVENT_ITEM_STATUS']);
	}
	return $tt;
}


# **..** **..** **..** **..** **..** SECTION 08: Loan Application Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 08: Loan Application Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 08: Loan Application Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 08: Loan Application Management **..** **..** **..** **..** **..**  **..** **..** 

# ... ... ... 8.01: FetchNewLoanApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplns($LN_APPLN_STATUS){
	$la_list = array();
	$x = 0;

	$db_query = "";
	if ($LN_APPLN_STATUS=="") {
		$db_query = "SELECT * FROM loan_applns ORDER BY LN_APPLN_SUBMISSION_DATE ASC";
	}
	elseif ($LN_APPLN_STATUS!="") {
		$db_query = "SELECT * FROM loan_applns WHERE LN_APPLN_STATUS='$LN_APPLN_STATUS' ORDER BY LN_APPLN_SUBMISSION_DATE ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$la = array();
		$la['RECORD_ID'] = trim($row['RECORD_ID']);
		$la['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$la['IS_WALK_IN'] = trim($row['IS_WALK_IN']);
		$la['IS_TOP_UP'] = trim($row['IS_TOP_UP']);
		$la['CUST_ID'] = trim($row['CUST_ID']);
		$la['LN_PDT_ID'] = trim($row['LN_PDT_ID']);
		$la['LN_APPLN_CREATION_DATE'] = trim($row['LN_APPLN_CREATION_DATE']);
		$la['LN_APPLN_PROGRESS_STATUS'] = trim($row['LN_APPLN_PROGRESS_STATUS']);
		$la['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$la['RQSTD_RPYMT_PRD'] = trim($row['RQSTD_RPYMT_PRD']);
		$la['PURPOSE'] = trim($row['PURPOSE']);
		$la['LN_APPLN_SUBMISSION_DATE'] = trim($row['LN_APPLN_SUBMISSION_DATE']);
		$la['LN_APPLN_ASSMT_STATUS'] = trim($row['LN_APPLN_ASSMT_STATUS']);
		$la['LN_APPLN_ASSMT_RMKS'] = trim($row['LN_APPLN_ASSMT_RMKS']);
		$la['LN_APPLN_ASSMT_DATE'] = trim($row['LN_APPLN_ASSMT_DATE']);
		$la['LN_APPLN_ASSMT_USER_ID'] = trim($row['LN_APPLN_ASSMT_USER_ID']);
		$la['LN_APPLN_DOC_STATUS'] = trim($row['LN_APPLN_DOC_STATUS']);
		$la['LN_APPLN_DOC_RMKS'] = trim($row['LN_APPLN_DOC_RMKS']);
		$la['LN_APPLN_DOC_DATE'] = trim($row['LN_APPLN_DOC_DATE']);
		$la['LN_APPLN_DOC_USER_ID'] = trim($row['LN_APPLN_DOC_USER_ID']);
		$la['LN_APPLN_GRRTR_STATUS'] = trim($row['LN_APPLN_GRRTR_STATUS']);
		$la['LN_APPLN_GRRTR_RMKS'] = trim($row['LN_APPLN_GRRTR_RMKS']);
		$la['LN_APPLN_GRRTR_DATE'] = trim($row['LN_APPLN_GRRTR_DATE']);
		$la['LN_APPLN_GRRTR_USER_ID'] = trim($row['LN_APPLN_GRRTR_USER_ID']);
		$la['CC_FLG'] = trim($row['CC_FLG']);
		$la['CC_RECEIVE_DATE'] = trim($row['CC_RECEIVE_DATE']);
		$la['CC_HANDLER_WKFLW_ID'] = trim($row['CC_HANDLER_WKFLW_ID']);
		$la['CC_STATUS'] = trim($row['CC_STATUS']);
		$la['CC_STATUS_DATE'] = trim($row['CC_STATUS_DATE']);
		$la['CC_RMKS'] = trim($row['CC_RMKS']);
		$la['CREDIT_OFFICER_RCMNDTN_USER_ID'] = trim($row['CREDIT_OFFICER_RCMNDTN_USER_ID']);
		$la['RCMNDTN_REQUEST_SEND_DATE'] = trim($row['RCMNDTN_REQUEST_SEND_DATE']);
		$la['RCMNDD_APPLN_AMT'] = trim($row['RCMNDD_APPLN_AMT']);
		$la['RCMNDTN_CUST_RESPONSE_DATE'] = trim($row['RCMNDTN_CUST_RESPONSE_DATE']);
		$la['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$la['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$la['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$la['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$la['CORE_LOAN_ACCT_ID'] = trim($row['CORE_LOAN_ACCT_ID']);
		$la['CORE_SVGS_ACCT_ID'] = trim($row['CORE_SVGS_ACCT_ID']);
		$la['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$la['PROC_MODE'] = trim($row['PROC_MODE']);
		$la['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$la['CORE_RESOURCE_ID'] = trim($row['CORE_RESOURCE_ID']);
		$la['LN_APPLN_STATUS'] = trim($row['LN_APPLN_STATUS']);
		$la_list[$x] = $la;
		$x++;

	}

	return $la_list;
}

# ... ... ... 8.02: FetchLoanApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnsById($LN_APPLN_NO){
	$la = array();

	$q = mysql_query("SELECT * FROM loan_applns WHERE LN_APPLN_NO='$LN_APPLN_NO'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$la['RECORD_ID'] = trim($row['RECORD_ID']);
		$la['CHANNEL'] = trim($row['CHANNEL']);
		$la['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$la['IS_WALK_IN'] = trim($row['IS_WALK_IN']);
		$la['IS_TOP_UP'] = trim($row['IS_TOP_UP']);
		$la['TOP_UP_LOAN_ID'] = trim($row['TOP_UP_LOAN_ID']);		
		$la['CUST_ID'] = trim($row['CUST_ID']);
		$la['LN_PDT_ID'] = trim($row['LN_PDT_ID']);
		$la['LN_APPLN_CREATION_DATE'] = trim($row['LN_APPLN_CREATION_DATE']);
		$la['LN_APPLN_PROGRESS_STATUS'] = trim($row['LN_APPLN_PROGRESS_STATUS']);
		$la['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$la['RQSTD_RPYMT_PRD'] = trim($row['RQSTD_RPYMT_PRD']);
		$la['PURPOSE'] = trim($row['PURPOSE']);
		$la['LN_APPLN_SUBMISSION_DATE'] = trim($row['LN_APPLN_SUBMISSION_DATE']);
		$la['LN_APPLN_ASSMT_STATUS'] = trim($row['LN_APPLN_ASSMT_STATUS']);
		$la['LN_APPLN_ASSMT_RMKS'] = trim($row['LN_APPLN_ASSMT_RMKS']);
		$la['LN_APPLN_ASSMT_DATE'] = trim($row['LN_APPLN_ASSMT_DATE']);
		$la['LN_APPLN_ASSMT_USER_ID'] = trim($row['LN_APPLN_ASSMT_USER_ID']);
		$la['LN_APPLN_DOC_STATUS'] = trim($row['LN_APPLN_DOC_STATUS']);
		$la['LN_APPLN_DOC_RMKS'] = trim($row['LN_APPLN_DOC_RMKS']);
		$la['LN_APPLN_DOC_DATE'] = trim($row['LN_APPLN_DOC_DATE']);
		$la['LN_APPLN_DOC_USER_ID'] = trim($row['LN_APPLN_DOC_USER_ID']);
		$la['LN_APPLN_GRRTR_STATUS'] = trim($row['LN_APPLN_GRRTR_STATUS']);
		$la['LN_APPLN_GRRTR_RMKS'] = trim($row['LN_APPLN_GRRTR_RMKS']);
		$la['LN_APPLN_GRRTR_DATE'] = trim($row['LN_APPLN_GRRTR_DATE']);
		$la['LN_APPLN_GRRTR_USER_ID'] = trim($row['LN_APPLN_GRRTR_USER_ID']);
		$la['VERIF_STATUS'] = trim($row['VERIF_STATUS']);
		$la['VERIF_DATE'] = trim($row['VERIF_DATE']);
		$la['VERIF_RMKS'] = trim($row['VERIF_RMKS']);
		$la['VERIF_USER_ID'] = trim($row['VERIF_USER_ID']);
		$la['CC_FLG'] = trim($row['CC_FLG']);
		$la['CC_RECEIVE_DATE'] = trim($row['CC_RECEIVE_DATE']);
		$la['CC_HANDLER_WKFLW_ID'] = trim($row['CC_HANDLER_WKFLW_ID']);
		$la['CC_STATUS'] = trim($row['CC_STATUS']);
		$la['CC_STATUS_DATE'] = trim($row['CC_STATUS_DATE']);
		$la['CC_RMKS'] = trim($row['CC_RMKS']);
		$la['CREDIT_OFFICER_RCMNDTN_USER_ID'] = trim($row['CREDIT_OFFICER_RCMNDTN_USER_ID']);
		$la['RCMNDTN_REQUEST_SEND_DATE'] = trim($row['RCMNDTN_REQUEST_SEND_DATE']);
		$la['RCMNDD_APPLN_AMT'] = trim($row['RCMNDD_APPLN_AMT']);
		$la['RCMNDTN_CUST_RESPONSE_DATE'] = trim($row['RCMNDTN_CUST_RESPONSE_DATE']);
		$la['APPROVAL_STATUS'] = trim($row['APPROVAL_STATUS']);
		$la['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$la['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$la['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$la['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$la['FLG_OPEN_LOAN_ACCT'] = trim($row['FLG_OPEN_LOAN_ACCT']);
		$la['FLG_APPRV_LOAN_ACCT'] = trim($row['FLG_APPRV_LOAN_ACCT']);
		$la['FLG_UPLOAD_LOAN_DOCS'] = trim($row['FLG_UPLOAD_LOAN_DOCS']);
		$la['FLG_ADD_GRRTRS'] = trim($row['FLG_ADD_GRRTRS']);
		$la['FLG_DISB_TO_SVNGS'] = trim($row['FLG_DISB_TO_SVNGS']);
		$la['DISB_DATE'] = trim($row['DISB_DATE']);
		$la['DISB_USER_ID'] = trim($row['DISB_USER_ID']);
		$la['CORE_LOAN_ACCT_ID'] = trim($row['CORE_LOAN_ACCT_ID']);
		$la['CORE_SVGS_ACCT_ID'] = trim($row['CORE_SVGS_ACCT_ID']);
		$la['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$la['PROC_MODE'] = trim($row['PROC_MODE']);
		$la['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$la['CORE_RESOURCE_ID'] = trim($row['CORE_RESOURCE_ID']);
		$la['LN_APPLN_STATUS'] = trim($row['LN_APPLN_STATUS']);

	}

	return $la;
}

# ... ... ... 8.03: FetchLoanApplnsByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnsByRecordId($RECORD_ID){
	$la = array();

	$q = mysql_query("SELECT * FROM loan_applns WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

				$la['RECORD_ID'] = trim($row['RECORD_ID']);
		$la['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$la['CUST_ID'] = trim($row['CUST_ID']);
		$la['LN_PDT_ID'] = trim($row['LN_PDT_ID']);
		$la['LN_APPLN_CREATION_DATE'] = trim($row['LN_APPLN_CREATION_DATE']);
		$la['LN_APPLN_PROGRESS_STATUS'] = trim($row['LN_APPLN_PROGRESS_STATUS']);
		$la['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$la['RQSTD_RPYMT_PRD'] = trim($row['RQSTD_RPYMT_PRD']);
		$la['PURPOSE'] = trim($row['PURPOSE']);
		$la['LN_APPLN_SUBMISSION_DATE'] = trim($row['LN_APPLN_SUBMISSION_DATE']);
		$la['LN_APPLN_ASSMT_STATUS'] = trim($row['LN_APPLN_ASSMT_STATUS']);
		$la['LN_APPLN_ASSMT_RMKS'] = trim($row['LN_APPLN_ASSMT_RMKS']);
		$la['LN_APPLN_ASSMT_DATE'] = trim($row['LN_APPLN_ASSMT_DATE']);
		$la['LN_APPLN_ASSMT_USER_ID'] = trim($row['LN_APPLN_ASSMT_USER_ID']);
		$la['LN_APPLN_DOC_STATUS'] = trim($row['LN_APPLN_DOC_STATUS']);
		$la['LN_APPLN_DOC_RMKS'] = trim($row['LN_APPLN_DOC_RMKS']);
		$la['LN_APPLN_DOC_DATE'] = trim($row['LN_APPLN_DOC_DATE']);
		$la['LN_APPLN_DOC_USER_ID'] = trim($row['LN_APPLN_DOC_USER_ID']);
		$la['LN_APPLN_GRRTR_STATUS'] = trim($row['LN_APPLN_GRRTR_STATUS']);
		$la['LN_APPLN_GRRTR_RMKS'] = trim($row['LN_APPLN_GRRTR_RMKS']);
		$la['LN_APPLN_GRRTR_DATE'] = trim($row['LN_APPLN_GRRTR_DATE']);
		$la['LN_APPLN_GRRTR_USER_ID'] = trim($row['LN_APPLN_GRRTR_USER_ID']);
		$la['CC_FLG'] = trim($row['CC_FLG']);
		$la['CC_RECEIVE_DATE'] = trim($row['CC_RECEIVE_DATE']);
		$la['CC_HANDLER_WKFLW_ID'] = trim($row['CC_HANDLER_WKFLW_ID']);
		$la['CC_STATUS'] = trim($row['CC_STATUS']);
		$la['CC_STATUS_DATE'] = trim($row['CC_STATUS_DATE']);
		$la['CC_RMKS'] = trim($row['CC_RMKS']);
		$la['CREDIT_OFFICER_RCMNDTN_USER_ID'] = trim($row['CREDIT_OFFICER_RCMNDTN_USER_ID']);
		$la['RCMNDTN_REQUEST_SEND_DATE'] = trim($row['RCMNDTN_REQUEST_SEND_DATE']);
		$la['RCMNDD_APPLN_AMT'] = trim($row['RCMNDD_APPLN_AMT']);
		$la['RCMNDTN_CUST_RESPONSE_DATE'] = trim($row['RCMNDTN_CUST_RESPONSE_DATE']);
		$la['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$la['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$la['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$la['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$la['CORE_LOAN_ACCT_ID'] = trim($row['CORE_LOAN_ACCT_ID']);
		$la['CORE_SVGS_ACCT_ID'] = trim($row['CORE_SVGS_ACCT_ID']);
		$la['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$la['PROC_MODE'] = trim($row['PROC_MODE']);
		$la['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$la['CORE_RESOURCE_ID'] = trim($row['CORE_RESOURCE_ID']);
		$la['LN_APPLN_STATUS'] = trim($row['LN_APPLN_STATUS']);

	}

	return $la;
}

# ... ... ... 8.04: FetchLoanApplnsByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustomerLoginDataByCustId($CUST_ID){
	$cstmr = array();
	$q = mysql_query("SELECT * FROM cstmrs WHERE CUST_ID='$CUST_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cstmr['RECORD_ID'] = trim($row['RECORD_ID']);
		$cstmr['CUST_ID'] = trim($row['CUST_ID']);
		$cstmr['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$cstmr['APPLN_REF'] = trim($row['APPLN_REF']);
		$cstmr['APPLN_REF_MOB'] = trim($row['APPLN_REF_MOB']);
		$cstmr['ACTVN_TOKEN'] = trim($row['ACTVN_TOKEN']);
		$cstmr['ACTVN_TOKEN_MOB'] = trim($row['ACTVN_TOKEN_MOB']);
		$cstmr['CUST_EMAIL'] = trim($row['CUST_EMAIL']);
		$cstmr['CUST_PHONE'] = trim($row['CUST_PHONE']);
		$cstmr['WEB_CHANNEL_LOGIN_ATTEMPTS'] = trim($row['WEB_CHANNEL_LOGIN_ATTEMPTS']);
		$cstmr['WEB_CHANNEL_ACCESS_FLG'] = trim($row['WEB_CHANNEL_ACCESS_FLG']);
		$cstmr['WEB_CHANNEL_ACTVN_FLG'] = trim($row['WEB_CHANNEL_ACTVN_FLG']);
		$cstmr['WEB_CHANNEL_ACTVN_DATE'] = trim($row['WEB_CHANNEL_ACTVN_DATE']);
		$cstmr['MOB_WALLET'] = trim($row['MOB_WALLET']);
		$cstmr['MOB_CHANNEL_LOGIN_ATTEMPTS'] = trim($row['MOB_CHANNEL_LOGIN_ATTEMPTS']);
		$cstmr['MOB_CHANNEL_ACCESS_FLG'] = trim($row['MOB_CHANNEL_ACCESS_FLG']);
		$cstmr['MOB_CHANNEL_ACTVN_FLG'] = trim($row['MOB_CHANNEL_ACTVN_FLG']);
		$cstmr['MOB_CHANNEL_ACTVN_DATE'] = trim($row['MOB_CHANNEL_ACTVN_DATE']);
		$cstmr['CUST_USR'] = trim($row['CUST_USR']);
		$cstmr['CUST_PWSD_STATUS'] = trim($row['CUST_PWSD_STATUS']);
		$cstmr['CUST_PWSD'] = trim($row['CUST_PWSD']);
		$cstmr['CUST_PWSD_LST_CHNG_DATE'] = trim($row['CUST_PWSD_LST_CHNG_DATE']);
		$cstmr['CUST_PIN_STATUS'] = trim($row['CUST_PIN_STATUS']);
		$cstmr['CUST_PIN'] = trim($row['CUST_PIN']);
		$cstmr['CUST_PIN_LST_CHNG_DATE'] = trim($row['CUST_PIN_LST_CHNG_DATE']);
		$cstmr['CUST_DEVICE_ID'] = trim($row['CUST_DEVICE_ID']);
		$cstmr['CUST_SIM_IMEI'] = trim($row['CUST_SIM_IMEI']);
		$cstmr['CUST_STATUS'] = trim($row['CUST_STATUS']);
	}

	return $cstmr;
}

# ... ... ... 8.05: FetchLoanApplnConfigByProductId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnConfigByProductId($PDT_ID){
	$appln_config = array();
	$q = mysql_query("SELECT * FROM appln_configs WHERE PDT_ID='$PDT_ID' AND PDT_TYPE_ID='LOAN' AND APPLN_CONFIG_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$appln_config['RECORD_ID'] = trim($row['RECORD_ID']);
		$appln_config['APPLN_CONFIG_ID'] = trim($row['APPLN_CONFIG_ID']);
		$appln_config['APPLN_CONFIG_NAME'] = trim($row['APPLN_CONFIG_NAME']);
		$appln_config['APPLN_TYPE_ID'] = trim($row['APPLN_TYPE_ID']);
		$appln_config['PDT_ID'] = trim($row['PDT_ID']);
		$appln_config['PDT_TYPE_ID'] = trim($row['PDT_TYPE_ID']);
		$appln_config['PRM_01'] = trim($row['PRM_01']);
		$appln_config['PRM_02'] = trim($row['PRM_02']);
		$appln_config['PRM_03'] = trim($row['PRM_03']);
		$appln_config['PRM_04'] = trim($row['PRM_04']);
		$appln_config['PRM_05'] = trim($row['PRM_05']);
		$appln_config['PRM_06'] = trim($row['PRM_06']);
		$appln_config['PRM_07'] = trim($row['PRM_07']);
		$appln_config['PRM_08'] = trim($row['PRM_08']);
		$appln_config['PRM_09'] = trim($row['PRM_09']);
		$appln_config['PRM_10'] = trim($row['PRM_10']);
		$appln_config['PRM_11'] = trim($row['PRM_11']);
		$appln_config['PRM_12'] = trim($row['PRM_12']);
		$appln_config['PRM_13'] = trim($row['PRM_13']);
		$appln_config['PRM_14'] = trim($row['PRM_14']);
		$appln_config['PRM_15'] = trim($row['PRM_15']);
		$appln_config['PRM_16'] = trim($row['PRM_16']);
		$appln_config['PRM_17'] = trim($row['PRM_17']);
		$appln_config['PRM_18'] = trim($row['PRM_18']);
		$appln_config['PRM_19'] = trim($row['PRM_19']);
		$appln_config['PRM_20'] = trim($row['PRM_20']);
		$appln_config['PRM_21'] = trim($row['PRM_21']);
		$appln_config['PRM_22'] = trim($row['PRM_22']);
		$appln_config['PRM_23'] = trim($row['PRM_23']);
		$appln_config['PRM_24'] = trim($row['PRM_24']);
		$appln_config['PRM_25'] = trim($row['PRM_25']);
		$appln_config['PRM_26'] = trim($row['PRM_26']);
		$appln_config['PRM_27'] = trim($row['PRM_27']);
		$appln_config['PRM_28'] = trim($row['PRM_28']);
		$appln_config['PRM_29'] = trim($row['PRM_29']);
		$appln_config['PRM_30'] = trim($row['PRM_30']);
		$appln_config['CREATED_BY'] = trim($row['CREATED_BY']);
		$appln_config['CREATED_ON'] = trim($row['CREATED_ON']);
		$appln_config['APPLN_CONFIG_STATUS'] = trim($row['APPLN_CONFIG_STATUS']);
	}

	return $appln_config;
}

# ... ... ... 8.06 GetCustBankFromBankAcct ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCustBankFromBankAcct($CUST_ID, $CUST_FIN_INST_ID){

	$BANK_NAME = "";
	$q = mysql_query("SELECT FIN_INST_NAME FROM fin_instns WHERE FIN_INST_ID in (
														SELECT BANK_ID 
														FROM cstmrs_bank_details 
														WHERE CUST_ID='$CUST_ID' AND BANK_ACCOUNT='$CUST_FIN_INST_ID')") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$BANK_NAME = trim($row['FIN_INST_NAME']);
	}

	return $BANK_NAME;
}

# ... ... ... 8.06 FetchLoanApplnFiles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnFiles($LN_APPLN_NO){
	$ln_file_list = array();
	$x=0;

	$q = mysql_query("SELECT * FROM loan_appln_files WHERE LN_APPLN_NO='$LN_APPLN_NO' AND F_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$ln_file = array();
		$ln_file['RECORD_ID'] = trim($row['RECORD_ID']);
		$ln_file['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$ln_file['F_CODE'] = trim($row['F_CODE']);
		$ln_file['F_NAME'] = trim($row['F_NAME']);
		$ln_file['DATE_UPLOADED'] = trim($row['DATE_UPLOADED']);
		$ln_file['F_STATUS'] = trim($row['F_STATUS']);

		$ln_file_list[$x] = $ln_file;
		$x++;
	}
	return $ln_file_list;
}

# ... ... ... 8.07 FetchLoanApplnGuarantors ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnGuarantors($LN_APPLN_NO){
	$g_list = array();
	$x=0;

	$q = mysql_query("SELECT * FROM loan_appln_guarantors WHERE LN_APPLN_NO='$LN_APPLN_NO' AND GUARANTORSHIP_STATUS!='REMOVED'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$g = array();
		$g['RECORD_ID'] = trim($row['RECORD_ID']);
		$g['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$g['G_CUST_ID'] = trim($row['G_CUST_ID']);
		$g['G_NAME'] = trim($row['G_NAME']);
		$g['G_PHONE'] = trim($row['G_PHONE']);
		$g['G_EMAIL'] = trim($row['G_EMAIL']);
		$g['DATE_GENERATED'] = trim($row['DATE_GENERATED']);
		$g['GUARANTORSHIP_STATUS'] = trim($row['GUARANTORSHIP_STATUS']);
		$g['RMKS'] = trim($row['RMKS']);
		$g['USED_FLG'] = trim($row['USED_FLG']);
		$g['DATE_USED'] = trim($row['DATE_USED']);
		$g['MIFOS_RESOURCE_ID'] = trim($row['MIFOS_RESOURCE_ID']);

		/*
		$RECORD_ID = $g['RECORD_ID'];
		$LN_APPLN_NO = $g['LN_APPLN_NO'];
		$G_CUST_ID = $g['G_CUST_ID'];
		$G_EXT_ID = $g['G_EXT_ID'];
		$G_NAME = $g['G_NAME'];
		$G_ADDRESS = $g['G_ADDRESS'];
		$G_PHONE = $g['G_PHONE'];
		$G_EMAIL = $g['G_EMAIL'];
		$AUTH_TOKEN = $g['AUTH_TOKEN'];
		$DATE_GENERATED = $g['DATE_GENERATED'];
		$GUARANTORSHIP_STATUS = $g['GUARANTORSHIP_STATUS'];
		$RMKS = $g['RMKS'];
		$USED_FLG = $g['USED_FLG'];
		$DATE_USED = $g['DATE_USED'];
		$MIFOS_RESOURCE_ID = $g['MIFOS_RESOURCE_ID'];
		*/

		$g_list[$x] = $g;
		$x++;
	}
	return $g_list;
}

# ... ... ... 8.08 GetCCThresholdValue ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetCCThresholdValue($LN_PDT_ID){
	$CC_THRESHOLD = "";
	$q = mysql_query("SELECT PRM_10 FROM appln_configs WHERE PDT_TYPE_ID='LOAN' AND PDT_ID='$LN_PDT_ID' AND APPLN_CONFIG_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$CC_THRESHOLD = trim($row['PRM_10']);
	}

	return $CC_THRESHOLD;
}

# ... ... ... 8.09: FetchCreditCommitteeLoanApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCreditCommitteeLoanApplns(){
	$la_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM loan_applns WHERE CC_FLG='YY' AND LN_APPLN_STATUS='VERIFIED' ORDER BY LN_APPLN_SUBMISSION_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$la = array();
		$la['RECORD_ID'] = trim($row['RECORD_ID']);
		$la['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$la['IS_WALK_IN'] = trim($row['IS_WALK_IN']);
		$la['IS_TOP_UP'] = trim($row['IS_TOP_UP']);
		$la['CUST_ID'] = trim($row['CUST_ID']);
		$la['LN_PDT_ID'] = trim($row['LN_PDT_ID']);
		$la['LN_APPLN_CREATION_DATE'] = trim($row['LN_APPLN_CREATION_DATE']);
		$la['LN_APPLN_PROGRESS_STATUS'] = trim($row['LN_APPLN_PROGRESS_STATUS']);
		$la['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$la['RQSTD_RPYMT_PRD'] = trim($row['RQSTD_RPYMT_PRD']);
		$la['PURPOSE'] = trim($row['PURPOSE']);
		$la['LN_APPLN_SUBMISSION_DATE'] = trim($row['LN_APPLN_SUBMISSION_DATE']);
		$la['LN_APPLN_ASSMT_STATUS'] = trim($row['LN_APPLN_ASSMT_STATUS']);
		$la['LN_APPLN_ASSMT_RMKS'] = trim($row['LN_APPLN_ASSMT_RMKS']);
		$la['LN_APPLN_ASSMT_DATE'] = trim($row['LN_APPLN_ASSMT_DATE']);
		$la['LN_APPLN_ASSMT_USER_ID'] = trim($row['LN_APPLN_ASSMT_USER_ID']);
		$la['LN_APPLN_DOC_STATUS'] = trim($row['LN_APPLN_DOC_STATUS']);
		$la['LN_APPLN_DOC_RMKS'] = trim($row['LN_APPLN_DOC_RMKS']);
		$la['LN_APPLN_DOC_DATE'] = trim($row['LN_APPLN_DOC_DATE']);
		$la['LN_APPLN_DOC_USER_ID'] = trim($row['LN_APPLN_DOC_USER_ID']);
		$la['LN_APPLN_GRRTR_STATUS'] = trim($row['LN_APPLN_GRRTR_STATUS']);
		$la['LN_APPLN_GRRTR_RMKS'] = trim($row['LN_APPLN_GRRTR_RMKS']);
		$la['LN_APPLN_GRRTR_DATE'] = trim($row['LN_APPLN_GRRTR_DATE']);
		$la['LN_APPLN_GRRTR_USER_ID'] = trim($row['LN_APPLN_GRRTR_USER_ID']);
		$la['CC_FLG'] = trim($row['CC_FLG']);
		$la['CC_RECEIVE_DATE'] = trim($row['CC_RECEIVE_DATE']);
		$la['CC_HANDLER_WKFLW_ID'] = trim($row['CC_HANDLER_WKFLW_ID']);
		$la['CC_STATUS'] = trim($row['CC_STATUS']);
		$la['CC_STATUS_DATE'] = trim($row['CC_STATUS_DATE']);
		$la['CC_RMKS'] = trim($row['CC_RMKS']);
		$la['CREDIT_OFFICER_RCMNDTN_USER_ID'] = trim($row['CREDIT_OFFICER_RCMNDTN_USER_ID']);
		$la['RCMNDTN_REQUEST_SEND_DATE'] = trim($row['RCMNDTN_REQUEST_SEND_DATE']);
		$la['RCMNDD_APPLN_AMT'] = trim($row['RCMNDD_APPLN_AMT']);
		$la['RCMNDTN_CUST_RESPONSE_DATE'] = trim($row['RCMNDTN_CUST_RESPONSE_DATE']);
		$la['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$la['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$la['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$la['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$la['CORE_LOAN_ACCT_ID'] = trim($row['CORE_LOAN_ACCT_ID']);
		$la['CORE_SVGS_ACCT_ID'] = trim($row['CORE_SVGS_ACCT_ID']);
		$la['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$la['PROC_MODE'] = trim($row['PROC_MODE']);
		$la['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$la['CORE_RESOURCE_ID'] = trim($row['CORE_RESOURCE_ID']);
		$la['LN_APPLN_STATUS'] = trim($row['LN_APPLN_STATUS']);
		$la_list[$x] = $la;
		$x++;

	}

	return $la_list;
}

# ... ... ... 8.10: FetchCreditCommitteeLoanApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchActiveTAN($ENTITY_ID){
	$tan = array();

	$q = mysql_query("SELECT * FROM txn_tans WHERE ENTITY_ID='$ENTITY_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tan['RECORD_ID'] = trim($row['RECORD_ID']);
		$tan['ENTITY_TYPE'] = trim($row['ENTITY_TYPE']);
		$tan['ENTITY_ID'] = trim($row['ENTITY_ID']);
		$tan['EVENT_TYPE'] = trim($row['EVENT_TYPE']);
		$tan['TAN'] = trim($row['TAN']);
		$tan['TAN_GEN_DATE'] = trim($row['TAN_GEN_DATE']);
		$tan['TAN_STATUS'] = trim($row['TAN_STATUS']);
	}

	return $tan;
}

# ... ... ... 8.11: ValidateTranTAN ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ValidateTranTAN($ENTITY_ID, $TRAN_TAN){
	# ... 00: Variablrd
	$val_results = array();

	# ... 01: Get Active Tan Details
	$tan = array();
	$tan = FetchActiveTAN($ENTITY_ID);

	$RECORD_ID = "";
	$ENTITY_TYPE = "";
	$ENTITY_ID = "";
	$EVENT_TYPE = "";
	$TAN = "";
	$TAN_GEN_DATE = "";
	$TAN_STATUS = "";

	if (sizeof(isset($tan['RECORD_ID']))) {
		$RECORD_ID = $tan['RECORD_ID'];
		$ENTITY_TYPE = $tan['ENTITY_TYPE'];
		$ENTITY_ID = $tan['ENTITY_ID'];
		$EVENT_TYPE = $tan['EVENT_TYPE'];
		$TAN = $tan['TAN'];
		$TAN_GEN_DATE = $tan['TAN_GEN_DATE'];
		$TAN_STATUS = $tan['TAN_STATUS'];
	}

	# ... 02: DECRYPT DB ACTIVE TAN
	$DB_TAN = AES256::decrypt($TAN);
	if ($TRAN_TAN==$DB_TAN) {
		
		$CUR_DATE = GetCurrentDateTime();
		$CUR_TIME = strtotime($CUR_DATE);
		$TAN_TIME = strtotime($TAN_GEN_DATE);
		$TIME_DIFF = (($CUR_TIME - $TAN_TIME)/60);

		if ($TIME_DIFF>5) {
			$val_results["TAN_MSG_CODE"] = "FALSE";
			$val_results["TAN_MSG_MSG"] = "TAN IS EXPIRED.";

			# ... Expire TAN
			$q = "UPDATE txn_tans SET TAN_STATUS='EXPIRED' WHERE ENTITY_ID='$ENTITY_ID' AND TAN_STATUS='ACTIVE'";
  		ExecuteEntityUpdate($q);

		} else if($TIME_DIFF<=5){
			$val_results["TAN_MSG_CODE"] = "TRUE";
			$val_results["TAN_MSG_MSG"] = "TAN IS VALID.";
		}
	} else
	{
		$val_results["TAN_MSG_CODE"] = "FALSE";
		$val_results["TAN_MSG_MSG"] = "Wrong transaction TAN supplied.";
	}
	
	return $val_results;
}

# ... ... ... 8.12 FetchCreditCommitteeForLoanProduct ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCreditCommitteeForLoanProduct($LN_PDT_ID){
	$CC_COMMITTEE_ID = "";
	$q = mysql_query("SELECT PRM_11 FROM appln_configs WHERE PDT_TYPE_ID='LOAN' AND PDT_ID='$LN_PDT_ID' AND APPLN_CONFIG_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$CC_COMMITTEE_ID = trim($row['PRM_11']);
	}

	return $CC_COMMITTEE_ID;
}

# ... ... ... 8.13 FetchApplnGroupActionTakenByIndMember ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchApplnGroupActionTakenByIndMember($ACTION_TYPE, $APPLN_NO, $GRP_ID, $GRP_MEMBER_ID){
	$appln_grp_action_details = array();

	$q = mysql_query("SELECT * FROM appln_mgt_group_actions 
                    WHERE ACTION_TYPE='$ACTION_TYPE'
                      AND APPLN_NO='$APPLN_NO'
                      AND GRP_ID='$GRP_ID'
                      AND GRP_MEMBER_ID='$GRP_MEMBER_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$appln_grp_action_details['RECORD_ID'] = trim($row['RECORD_ID']);
		$appln_grp_action_details['ACTION_ID'] = trim($row['ACTION_ID']);
		$appln_grp_action_details['ACTION_TYPE'] = trim($row['ACTION_TYPE']);
		$appln_grp_action_details['APPLN_NO'] = trim($row['APPLN_NO']);
		$appln_grp_action_details['GRP_ID'] = trim($row['GRP_ID']);
		$appln_grp_action_details['GRP_MEMBER_ID'] = trim($row['GRP_MEMBER_ID']);
		$appln_grp_action_details['ACTION_TAKEN'] = trim($row['ACTION_TAKEN']);
		$appln_grp_action_details['ACTION_REMARKS'] = trim($row['ACTION_REMARKS']);
		$appln_grp_action_details['DATE_ACTION_TAKEN'] = trim($row['DATE_ACTION_TAKEN']);
		$appln_grp_action_details['ACTION_RETRY_FLG'] = trim($row['ACTION_RETRY_FLG']);
		$appln_grp_action_details['CNT_RETRIED'] = trim($row['CNT_RETRIED']);
		$appln_grp_action_details['DATE_LST_RETRIED'] = trim($row['DATE_LST_RETRIED']);
	}


	return $appln_grp_action_details;
}

# ... ... ... 8.14 FetchApplnGroupActionTakenByGroup ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchApplnGroupActionTakenByGroup($ACTION_TYPE, $APPLN_NO, $GRP_ID){
	$appln_grp_action_details_list = array();
	$x=0;

	$q = mysql_query("SELECT * FROM appln_mgt_group_actions 
                    WHERE ACTION_TYPE='$ACTION_TYPE'
                      AND APPLN_NO='$APPLN_NO'
                      AND GRP_ID='$GRP_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$appln_grp_action_details = array();
		$appln_grp_action_details['RECORD_ID'] = trim($row['RECORD_ID']);
		$appln_grp_action_details['ACTION_ID'] = trim($row['ACTION_ID']);
		$appln_grp_action_details['ACTION_TYPE'] = trim($row['ACTION_TYPE']);
		$appln_grp_action_details['APPLN_NO'] = trim($row['APPLN_NO']);
		$appln_grp_action_details['GRP_ID'] = trim($row['GRP_ID']);
		$appln_grp_action_details['GRP_MEMBER_ID'] = trim($row['GRP_MEMBER_ID']);
		$appln_grp_action_details['ACTION_TAKEN'] = trim($row['ACTION_TAKEN']);
		$appln_grp_action_details['ACTION_REMARKS'] = trim($row['ACTION_REMARKS']);
		$appln_grp_action_details['DATE_ACTION_TAKEN'] = trim($row['DATE_ACTION_TAKEN']);
		$appln_grp_action_details['ACTION_RETRY_FLG'] = trim($row['ACTION_RETRY_FLG']);
		$appln_grp_action_details['CNT_RETRIED'] = trim($row['CNT_RETRIED']);
		$appln_grp_action_details['DATE_LST_RETRIED'] = trim($row['DATE_LST_RETRIED']);

		$appln_grp_action_details_list[$x] = $appln_grp_action_details;
		$x++;
	}
	return $appln_grp_action_details_list;
}

# ... ... ... 8.15 ProcessCCApproval ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ProcessCCApproval($ACTION_TYPE, $GRP_ID, $APPLN_NO){

	$resp = array();

	# ... Member Count
	$Q_CCNNTT ="SELECT count(*) as RTN_VALUE FROM appln_mgt_group_members WHERE GRP_ID='$GRP_ID'AND GRP_MEMBER_STATUS='ACTIVE'";
  $CNNT_CC = ReturnOneEntryFromDB($Q_CCNNTT);
  $resp["MMBR_CNT"] = $CNNT_CC;

	# ... Member Approvals
	$Q111 = "SELECT count(*) as RTN_VALUE FROM appln_mgt_group_actions WHERE ACTION_TYPE='$ACTION_TYPE' AND GRP_ID='$GRP_ID' AND APPLN_NO='$APPLN_NO' AND ACTION_TAKEN='APPROVE'";
  $C111 = ReturnOneEntryFromDB($Q111);
  $resp["MMBR_CNT_APPRV"] = $C111;

	# ... Member Rejects
	$Q222 = "SELECT count(*) as RTN_VALUE FROM appln_mgt_group_actions WHERE ACTION_TYPE='$ACTION_TYPE' AND GRP_ID='$GRP_ID' AND APPLN_NO='$APPLN_NO' AND ACTION_TAKEN='REJECT'";
  $C222 = ReturnOneEntryFromDB($Q222);
  $resp["MMBR_CNT_REJN"] = $C222;


  return $resp;
}

# ... ... ... 8.16 FetchDisbursedLoanApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchDisbursedLoanApplns($START_DATE, $END_DATE){
	$la_list = array();
	$x = 0;

	$q = mysql_query("SELECT * 
		                FROM loan_applns 
		                WHERE DISB_DATE>='$START_DATE' AND DISB_DATE<'$END_DATE' 
		                  AND LN_APPLN_STATUS='APPLN_DISBURSED' 
		                ORDER BY LN_APPLN_SUBMISSION_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$la = array();
		$la['RECORD_ID'] = trim($row['RECORD_ID']);
		$la['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$la['CUST_ID'] = trim($row['CUST_ID']);
		$la['LN_PDT_ID'] = trim($row['LN_PDT_ID']);
		$la['LN_APPLN_CREATION_DATE'] = trim($row['LN_APPLN_CREATION_DATE']);
		$la['LN_APPLN_PROGRESS_STATUS'] = trim($row['LN_APPLN_PROGRESS_STATUS']);
		$la['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$la['RQSTD_RPYMT_PRD'] = trim($row['RQSTD_RPYMT_PRD']);
		$la['PURPOSE'] = trim($row['PURPOSE']);
		$la['LN_APPLN_SUBMISSION_DATE'] = trim($row['LN_APPLN_SUBMISSION_DATE']);
		$la['LN_APPLN_ASSMT_STATUS'] = trim($row['LN_APPLN_ASSMT_STATUS']);
		$la['LN_APPLN_ASSMT_RMKS'] = trim($row['LN_APPLN_ASSMT_RMKS']);
		$la['LN_APPLN_ASSMT_DATE'] = trim($row['LN_APPLN_ASSMT_DATE']);
		$la['LN_APPLN_ASSMT_USER_ID'] = trim($row['LN_APPLN_ASSMT_USER_ID']);
		$la['LN_APPLN_DOC_STATUS'] = trim($row['LN_APPLN_DOC_STATUS']);
		$la['LN_APPLN_DOC_RMKS'] = trim($row['LN_APPLN_DOC_RMKS']);
		$la['LN_APPLN_DOC_DATE'] = trim($row['LN_APPLN_DOC_DATE']);
		$la['LN_APPLN_DOC_USER_ID'] = trim($row['LN_APPLN_DOC_USER_ID']);
		$la['LN_APPLN_GRRTR_STATUS'] = trim($row['LN_APPLN_GRRTR_STATUS']);
		$la['LN_APPLN_GRRTR_RMKS'] = trim($row['LN_APPLN_GRRTR_RMKS']);
		$la['LN_APPLN_GRRTR_DATE'] = trim($row['LN_APPLN_GRRTR_DATE']);
		$la['LN_APPLN_GRRTR_USER_ID'] = trim($row['LN_APPLN_GRRTR_USER_ID']);
		$la['VERIF_STATUS'] = trim($row['VERIF_STATUS']);
		$la['VERIF_DATE'] = trim($row['VERIF_DATE']);
		$la['VERIF_RMKS'] = trim($row['VERIF_RMKS']);
		$la['VERIF_USER_ID'] = trim($row['VERIF_USER_ID']);
		$la['CC_FLG'] = trim($row['CC_FLG']);
		$la['CC_RECEIVE_DATE'] = trim($row['CC_RECEIVE_DATE']);
		$la['CC_HANDLER_WKFLW_ID'] = trim($row['CC_HANDLER_WKFLW_ID']);
		$la['CC_STATUS'] = trim($row['CC_STATUS']);
		$la['CC_STATUS_DATE'] = trim($row['CC_STATUS_DATE']);
		$la['CC_RMKS'] = trim($row['CC_RMKS']);
		$la['CREDIT_OFFICER_RCMNDTN_USER_ID'] = trim($row['CREDIT_OFFICER_RCMNDTN_USER_ID']);
		$la['RCMNDTN_REQUEST_SEND_DATE'] = trim($row['RCMNDTN_REQUEST_SEND_DATE']);
		$la['RCMNDD_APPLN_AMT'] = trim($row['RCMNDD_APPLN_AMT']);
		$la['RCMNDTN_CUST_RESPONSE_DATE'] = trim($row['RCMNDTN_CUST_RESPONSE_DATE']);
		$la['APPROVAL_STATUS'] = trim($row['APPROVAL_STATUS']);
		$la['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$la['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$la['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$la['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$la['FLG_OPEN_LOAN_ACCT'] = trim($row['FLG_OPEN_LOAN_ACCT']);
		$la['FLG_OPEN_LOAN_ACCT_USER_ID'] = trim($row['FLG_OPEN_LOAN_ACCT_USER_ID']);
		$la['FLG_APPRV_LOAN_ACCT'] = trim($row['FLG_APPRV_LOAN_ACCT']);
		$la['FLG_APPRV_LOAN_ACCT_USER_ID'] = trim($row['FLG_APPRV_LOAN_ACCT_USER_ID']);
		$la['FLG_UPLOAD_LOAN_DOCS'] = trim($row['FLG_UPLOAD_LOAN_DOCS']);
		$la['FLG_UPLOAD_LOAN_DOCS_USER_ID'] = trim($row['FLG_UPLOAD_LOAN_DOCS_USER_ID']);
		$la['FLG_ADD_GRRTRS'] = trim($row['FLG_ADD_GRRTRS']);
		$la['FLG_ADD_GRRTRS_USER_ID'] = trim($row['FLG_ADD_GRRTRS_USER_ID']);
		$la['FLG_DISB_TO_SVNGS'] = trim($row['FLG_DISB_TO_SVNGS']);
		$la['FLG_DISB_TO_SVNGS_USER_ID'] = trim($row['FLG_DISB_TO_SVNGS_USER_ID']);
		$la['DISB_DATE'] = trim($row['DISB_DATE']);
		$la['DISB_USER_ID'] = trim($row['DISB_USER_ID']);
		$la['CORE_LOAN_ACCT_ID'] = trim($row['CORE_LOAN_ACCT_ID']);
		$la['CORE_SVGS_ACCT_ID'] = trim($row['CORE_SVGS_ACCT_ID']);
		$la['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$la['PROC_MODE'] = trim($row['PROC_MODE']);
		$la['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$la['CORE_RESOURCE_ID'] = trim($row['CORE_RESOURCE_ID']);
		$la['LN_APPLN_STATUS'] = trim($row['LN_APPLN_STATUS']);
		$la_list[$x] = $la;
		$x++;
	}

	return $la_list;
}


# ... ... ... 8.17: FetchLoanApplnsBetweenPeriods ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnsBetweenPeriods($START_DATE, $END_DATE){
	$la_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM loan_applns 
		                WHERE LN_APPLN_SUBMISSION_DATE>='$START_DATE' AND LN_APPLN_SUBMISSION_DATE<'$END_DATE' 
		                ORDER BY LN_APPLN_SUBMISSION_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$la = array();
		$la['RECORD_ID'] = trim($row['RECORD_ID']);
		$la['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$la['CUST_ID'] = trim($row['CUST_ID']);
		$la['LN_PDT_ID'] = trim($row['LN_PDT_ID']);
		$la['LN_APPLN_CREATION_DATE'] = trim($row['LN_APPLN_CREATION_DATE']);
		$la['LN_APPLN_PROGRESS_STATUS'] = trim($row['LN_APPLN_PROGRESS_STATUS']);
		$la['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$la['RQSTD_RPYMT_PRD'] = trim($row['RQSTD_RPYMT_PRD']);
		$la['PURPOSE'] = trim($row['PURPOSE']);
		$la['LN_APPLN_SUBMISSION_DATE'] = trim($row['LN_APPLN_SUBMISSION_DATE']);
		$la['LN_APPLN_ASSMT_STATUS'] = trim($row['LN_APPLN_ASSMT_STATUS']);
		$la['LN_APPLN_ASSMT_RMKS'] = trim($row['LN_APPLN_ASSMT_RMKS']);
		$la['LN_APPLN_ASSMT_DATE'] = trim($row['LN_APPLN_ASSMT_DATE']);
		$la['LN_APPLN_ASSMT_USER_ID'] = trim($row['LN_APPLN_ASSMT_USER_ID']);
		$la['LN_APPLN_DOC_STATUS'] = trim($row['LN_APPLN_DOC_STATUS']);
		$la['LN_APPLN_DOC_RMKS'] = trim($row['LN_APPLN_DOC_RMKS']);
		$la['LN_APPLN_DOC_DATE'] = trim($row['LN_APPLN_DOC_DATE']);
		$la['LN_APPLN_DOC_USER_ID'] = trim($row['LN_APPLN_DOC_USER_ID']);
		$la['LN_APPLN_GRRTR_STATUS'] = trim($row['LN_APPLN_GRRTR_STATUS']);
		$la['LN_APPLN_GRRTR_RMKS'] = trim($row['LN_APPLN_GRRTR_RMKS']);
		$la['LN_APPLN_GRRTR_DATE'] = trim($row['LN_APPLN_GRRTR_DATE']);
		$la['LN_APPLN_GRRTR_USER_ID'] = trim($row['LN_APPLN_GRRTR_USER_ID']);
		$la['CC_FLG'] = trim($row['CC_FLG']);
		$la['CC_RECEIVE_DATE'] = trim($row['CC_RECEIVE_DATE']);
		$la['CC_HANDLER_WKFLW_ID'] = trim($row['CC_HANDLER_WKFLW_ID']);
		$la['CC_STATUS'] = trim($row['CC_STATUS']);
		$la['CC_STATUS_DATE'] = trim($row['CC_STATUS_DATE']);
		$la['CC_RMKS'] = trim($row['CC_RMKS']);
		$la['CREDIT_OFFICER_RCMNDTN_USER_ID'] = trim($row['CREDIT_OFFICER_RCMNDTN_USER_ID']);
		$la['RCMNDTN_REQUEST_SEND_DATE'] = trim($row['RCMNDTN_REQUEST_SEND_DATE']);
		$la['RCMNDD_APPLN_AMT'] = trim($row['RCMNDD_APPLN_AMT']);
		$la['RCMNDTN_CUST_RESPONSE_DATE'] = trim($row['RCMNDTN_CUST_RESPONSE_DATE']);
		$la['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$la['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$la['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$la['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$la['CORE_LOAN_ACCT_ID'] = trim($row['CORE_LOAN_ACCT_ID']);
		$la['CORE_SVGS_ACCT_ID'] = trim($row['CORE_SVGS_ACCT_ID']);
		$la['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$la['PROC_MODE'] = trim($row['PROC_MODE']);
		$la['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$la['CORE_RESOURCE_ID'] = trim($row['CORE_RESOURCE_ID']);
		$la['LN_APPLN_STATUS'] = trim($row['LN_APPLN_STATUS']);
		$la_list[$x] = $la;
		$x++;

	}

	return $la_list;
}

# ... ... ... 8.18: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanDisbursmentChargeList($CHANNEL, $CHRG_CRNCY, $CHRG_PDT_ID)
{
	$chrg_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM loan_appln_mifos_chrg_mappings 
									  WHERE CHRG_EVENT='DISBURSEMENT' 
										  AND CHANNEL='$CHANNEL'
										  AND CHRG_CRNCY='$CHRG_CRNCY'
										  AND CHRG_PDT_ID='$CHRG_PDT_ID'
										  AND STATUS='ACTIVE'
									  ORDER BY CHRG_EXEC_ORDER ASC") or die("ERR_FETCH_CHRG: " . mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$chrg = array();
		$chrg['RECORDID'] = trim($row['RECORDID']);
		$chrg['CHANNEL'] = trim($row['CHANNEL']);
		$chrg['CHRG_CRNCY'] = trim($row['CHRG_CRNCY']);
		$chrg['CHRG_PDT_ID'] = trim($row['CHRG_PDT_ID']);
		$chrg['CHRG_PDT_NAME'] = trim($row['CHRG_PDT_NAME']);
		$chrg['CHRG_EVENT'] = trim($row['CHRG_EVENT']);
		$chrg['CHRG_EXEC_ORDER'] = trim($row['CHRG_EXEC_ORDER']);
		$chrg['CHRG_TYPE'] = trim($row['CHRG_TYPE']);
		$chrg['CHRG_AMT'] = trim($row['CHRG_AMT']);
		$chrg['MIFOS_CHRG_ID'] = trim($row['MIFOS_CHRG_ID']);
		$chrg['MIFOS_CHRG_NAME'] = trim($row['MIFOS_CHRG_NAME']);
		$chrg['STATUS'] = trim($row['STATUS']);
		$chrg_list[$x] = $chrg;
		$x++;
	}

	return $chrg_list;
}




# **..** **..** **..** **..** **..** SECTION 10: Savings Application Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 10: Savings Application Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 10: Savings Application Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 10: Savings Application Management **..** **..** **..** **..** **..**  **..** **..** 

# ... ... ... 9.01: FetchSavingsWithdrawApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsWithdrawApplns($SVGS_APPLN_STATUS){
	$sw_list = array();
	$x = 0;

	$db_query = "";
	if ($SVGS_APPLN_STATUS=="") {
		$db_query = "SELECT * FROM svgs_withdraw_requests ORDER BY APPLN_SUBMISSION_DATE ASC";
	}
	elseif ($SVGS_APPLN_STATUS!="") {
		$db_query = "SELECT * FROM svgs_withdraw_requests WHERE SVGS_APPLN_STATUS='$SVGS_APPLN_STATUS' ORDER BY APPLN_SUBMISSION_DATE ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$sw = array();
		$sw['RECORD_ID'] = trim($row['RECORD_ID']);
		$sw['WITHDRAW_REF'] = trim($row['WITHDRAW_REF']);
		$sw['CUST_ID'] = trim($row['CUST_ID']);
		$sw['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$sw['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$sw['REASON'] = trim($row['REASON']);
		$sw['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$sw['SVGS_HANDLER_USER_ID'] = trim($row['SVGS_HANDLER_USER_ID']);
		$sw['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$sw['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$sw['COMMITTEE_FLG'] = trim($row['COMMITTEE_FLG']);
		$sw['COMMITTEE_HANDLER_USER_ID'] = trim($row['COMMITTEE_HANDLER_USER_ID']);
		$sw['COMMITTEE_STATUS'] = trim($row['COMMITTEE_STATUS']);
		$sw['COMMITTEE_STATUS_DATE'] = trim($row['COMMITTEE_STATUS_DATE']);
		$sw['COMMITTEE_RMKS'] = trim($row['COMMITTEE_RMKS']);
		$sw['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$sw['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$sw['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$sw['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$sw['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$sw['PROC_MODE'] = trim($row['PROC_MODE']);
		$sw['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$sw['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$sw['SVGS_APPLN_STATUS'] = trim($row['SVGS_APPLN_STATUS']);
		$sw_list[$x] = $sw;
		$x++;
	}

	return $sw_list;
}

# ... ... ... 9.02: FetchSavingsWithdrawApplnById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsWithdrawApplnById($WITHDRAW_REF){
	$sw = array();
	$q = mysql_query("SELECT * FROM svgs_withdraw_requests WHERE WITHDRAW_REF='$WITHDRAW_REF'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$sw['RECORD_ID'] = trim($row['RECORD_ID']);
		$sw['CHANNEL'] = trim($row['CHANNEL']);
		$sw['WITHDRAW_REF'] = trim($row['WITHDRAW_REF']);
		$sw['CUST_ID'] = trim($row['CUST_ID']);
		$sw['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$sw['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$sw['REASON'] = trim($row['REASON']);
		$sw['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$sw['SVGS_HANDLER_USER_ID'] = trim($row['SVGS_HANDLER_USER_ID']);
		$sw['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$sw['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$sw['COMMITTEE_FLG'] = trim($row['COMMITTEE_FLG']);
		$sw['COMMITTEE_HANDLER_USER_ID'] = trim($row['COMMITTEE_HANDLER_USER_ID']);
		$sw['COMMITTEE_STATUS'] = trim($row['COMMITTEE_STATUS']);
		$sw['COMMITTEE_STATUS_DATE'] = trim($row['COMMITTEE_STATUS_DATE']);
		$sw['COMMITTEE_RMKS'] = trim($row['COMMITTEE_RMKS']);
		$sw['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$sw['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$sw['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$sw['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$sw['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$sw['PROC_MODE'] = trim($row['PROC_MODE']);
		$sw['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$sw['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$sw['SVGS_APPLN_STATUS'] = trim($row['SVGS_APPLN_STATUS']);
	}
	return $sw;
}

# ... ... ... 9.03: FetchSavingsWithdrawApplnByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsWithdrawApplnByRecordId($RECORD_ID){
	$sw = array();
	$q = mysql_query("SELECT * FROM svgs_withdraw_requests WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$sw['RECORD_ID'] = trim($row['RECORD_ID']);
		$sw['WITHDRAW_REF'] = trim($row['WITHDRAW_REF']);
		$sw['CUST_ID'] = trim($row['CUST_ID']);
		$sw['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$sw['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$sw['REASON'] = trim($row['REASON']);
		$sw['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$sw['SVGS_HANDLER_USER_ID'] = trim($row['SVGS_HANDLER_USER_ID']);
		$sw['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$sw['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$sw['COMMITTEE_FLG'] = trim($row['COMMITTEE_FLG']);
		$sw['COMMITTEE_HANDLER_USER_ID'] = trim($row['COMMITTEE_HANDLER_USER_ID']);
		$sw['COMMITTEE_STATUS'] = trim($row['COMMITTEE_STATUS']);
		$sw['COMMITTEE_STATUS_DATE'] = trim($row['COMMITTEE_STATUS_DATE']);
		$sw['COMMITTEE_RMKS'] = trim($row['COMMITTEE_RMKS']);
		$sw['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$sw['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$sw['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$sw['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$sw['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$sw['PROC_MODE'] = trim($row['PROC_MODE']);
		$sw['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$sw['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$sw['SVGS_APPLN_STATUS'] = trim($row['SVGS_APPLN_STATUS']);
	}
	return $sw;
}

# ... ... ... 9.04: FetchSavingsApplnParamConfigCode ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsApplnParamConfigCode($SVGS_PDT_ID){
	$appln_config = array();

	$q = mysql_query("SELECT * FROM appln_configs WHERE PDT_TYPE_ID='SVNG' AND PDT_ID='$SVGS_PDT_ID' AND APPLN_CONFIG_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$appln_config['RECORD_ID'] = trim($row['RECORD_ID']);
		$appln_config['APPLN_CONFIG_ID'] = trim($row['APPLN_CONFIG_ID']);
		$appln_config['APPLN_CONFIG_NAME'] = trim($row['APPLN_CONFIG_NAME']);
		$appln_config['APPLN_TYPE_ID'] = trim($row['APPLN_TYPE_ID']);
		$appln_config['PDT_ID'] = trim($row['PDT_ID']);
		$appln_config['PDT_TYPE_ID'] = trim($row['PDT_TYPE_ID']);
		$appln_config['PRM_01'] = trim($row['PRM_01']);
		$appln_config['PRM_02'] = trim($row['PRM_02']);
		$appln_config['PRM_03'] = trim($row['PRM_03']);
		$appln_config['PRM_04'] = trim($row['PRM_04']);
		$appln_config['PRM_05'] = trim($row['PRM_05']);
		$appln_config['PRM_06'] = trim($row['PRM_06']);
		$appln_config['PRM_07'] = trim($row['PRM_07']);
		$appln_config['PRM_08'] = trim($row['PRM_08']);
		$appln_config['PRM_09'] = trim($row['PRM_09']);
		$appln_config['PRM_10'] = trim($row['PRM_10']);
		$appln_config['PRM_11'] = trim($row['PRM_11']);
		$appln_config['PRM_12'] = trim($row['PRM_12']);
		$appln_config['PRM_13'] = trim($row['PRM_13']);
		$appln_config['PRM_14'] = trim($row['PRM_14']);
		$appln_config['PRM_15'] = trim($row['PRM_15']);
		$appln_config['PRM_16'] = trim($row['PRM_16']);
		$appln_config['PRM_17'] = trim($row['PRM_17']);
		$appln_config['PRM_18'] = trim($row['PRM_18']);
		$appln_config['PRM_19'] = trim($row['PRM_19']);
		$appln_config['PRM_20'] = trim($row['PRM_20']);
		$appln_config['PRM_21'] = trim($row['PRM_21']);
		$appln_config['PRM_22'] = trim($row['PRM_22']);
		$appln_config['PRM_23'] = trim($row['PRM_23']);
		$appln_config['PRM_24'] = trim($row['PRM_24']);
		$appln_config['PRM_25'] = trim($row['PRM_25']);
		$appln_config['PRM_26'] = trim($row['PRM_26']);
		$appln_config['PRM_27'] = trim($row['PRM_27']);
		$appln_config['PRM_28'] = trim($row['PRM_28']);
		$appln_config['PRM_29'] = trim($row['PRM_29']);
		$appln_config['PRM_30'] = trim($row['PRM_30']);
		$appln_config['CREATED_BY'] = trim($row['CREATED_BY']);
		$appln_config['CREATED_ON'] = trim($row['CREATED_ON']);
		$appln_config['APPLN_CONFIG_STATUS'] = trim($row['APPLN_CONFIG_STATUS']);
	}

	return $appln_config;
}

# ... ... ... 9.05: DeduceChargeFee ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function DeduceChargeFee($TXN_AMT, $TRAN_CHRG_ID, $TRAN_CHRG_TYPE){
	$CHRG_TXN_FEE = "";

	# ... 01: Determine Charge Type
	if ($TRAN_CHRG_TYPE=="PP") {   
		$tt_list = array();
		$tt = array();
		$tt_list = FetchTranChargeAmountsForChargeId($TRAN_CHRG_ID);
		$tt = $tt_list[0];
		$CHRG_AMT = $tt['CHRG_AMT'];
		$CHRG_TXN_FEE = (($CHRG_AMT/100)*$TXN_AMT);
	} else if ($TRAN_CHRG_TYPE=="FF") {
		$tt_list = array();
		$tt_list = FetchTranChargeAmountsForChargeId($TRAN_CHRG_ID);
		for ($x=0; $x < sizeof($tt_list); $x++) { 
			$tt = array();
			$tt = $tt_list[$x];
			$CHRG_LOW = $tt['CHRG_LOW'];
			$CHRG_HIGH = $tt['CHRG_HIGH'];
			$CHRG_AMT = $tt['CHRG_AMT'];

			if ( ($TXN_AMT>=$CHRG_LOW)&&($TXN_AMT<=$CHRG_HIGH) ) {
				$CHRG_TXN_FEE = $CHRG_AMT;
				break;
			}

		}
	}
	return $CHRG_TXN_FEE;
}

# ... ... ... 9.06: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsTransferApplns($TRANSFER_APPLN_STATUS){
	$st_list = array();
	$x = 0;

	$db_query = "";
	if ($TRANSFER_APPLN_STATUS=="") {
		$db_query = "SELECT * FROM svgs_transfer_requests ORDER BY APPLN_SUBMISSION_DATE ASC";
	}
	elseif ($TRANSFER_APPLN_STATUS!="") {
		$db_query = "SELECT * FROM svgs_transfer_requests WHERE TRANSFER_APPLN_STATUS='$TRANSFER_APPLN_STATUS' ORDER BY APPLN_SUBMISSION_DATE ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$st = array();
		$st['RECORD_ID'] = trim($row['RECORD_ID']);
		$st['TRANSFER_REF'] = trim($row['TRANSFER_REF']);
		$st['CUST_ID'] = trim($row['CUST_ID']);
		$st['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$st['TRANSFER_AMT'] = trim($row['TRANSFER_AMT']);
		$st['SVGS_ACCT_ID_TO_CREDIT'] = trim($row['SVGS_ACCT_ID_TO_CREDIT']);
		$st['REASON'] = trim($row['REASON']);
		$st['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$st['SVGS_HANDLER_USER_ID'] = trim($row['SVGS_HANDLER_USER_ID']);
		$st['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$st['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$st['COMMITTEE_FLG'] = trim($row['COMMITTEE_FLG']);
		$st['COMMITTEE_HANDLER_USER_ID'] = trim($row['COMMITTEE_HANDLER_USER_ID']);
		$st['COMMITTEE_STATUS'] = trim($row['COMMITTEE_STATUS']);
		$st['COMMITTEE_STATUS_DATE'] = trim($row['COMMITTEE_STATUS_DATE']);
		$st['COMMITTEE_RMKS'] = trim($row['COMMITTEE_RMKS']);
		$st['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$st['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$st['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$st['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$st['PROC_MODE'] = trim($row['PROC_MODE']);
		$st['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$st['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$st['TRANSFER_APPLN_STATUS'] = trim($row['TRANSFER_APPLN_STATUS']);

		$st_list[$x] = $st;
		$x++;
	}

	return $st_list;
}

# ... ... ... 9.07: FetchSavingsTransferApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsTransferApplnsById($TRANSFER_REF){
	$st = array();

	$q = mysql_query("SELECT * FROM svgs_transfer_requests WHERE TRANSFER_REF='$TRANSFER_REF'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$st['RECORD_ID'] = trim($row['RECORD_ID']);
		$st['TRANSFER_REF'] = trim($row['TRANSFER_REF']);
		$st['CUST_ID'] = trim($row['CUST_ID']);
		$st['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$st['TRANSFER_AMT'] = trim($row['TRANSFER_AMT']);
		$st['SVGS_ACCT_ID_TO_CREDIT'] = trim($row['SVGS_ACCT_ID_TO_CREDIT']);
		$st['REASON'] = trim($row['REASON']);
		$st['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$st['SVGS_HANDLER_USER_ID'] = trim($row['SVGS_HANDLER_USER_ID']);
		$st['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$st['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$st['COMMITTEE_FLG'] = trim($row['COMMITTEE_FLG']);
		$st['COMMITTEE_HANDLER_USER_ID'] = trim($row['COMMITTEE_HANDLER_USER_ID']);
		$st['COMMITTEE_STATUS'] = trim($row['COMMITTEE_STATUS']);
		$st['COMMITTEE_STATUS_DATE'] = trim($row['COMMITTEE_STATUS_DATE']);
		$st['COMMITTEE_RMKS'] = trim($row['COMMITTEE_RMKS']);
		$st['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$st['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$st['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$st['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$st['PROC_MODE'] = trim($row['PROC_MODE']);
		$st['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$st['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$st['TRANSFER_APPLN_STATUS'] = trim($row['TRANSFER_APPLN_STATUS']);
	}

	return $st;
}

# ... ... ... 9.08: FetchSavingsTransferApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsTransferApplnsByRecordId($RECORD_ID){
	$st = array();

	$q = mysql_query("SELECT * FROM svgs_transfer_requests WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$st['RECORD_ID'] = trim($row['RECORD_ID']);
		$st['TRANSFER_REF'] = trim($row['TRANSFER_REF']);
		$st['CUST_ID'] = trim($row['CUST_ID']);
		$st['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$st['TRANSFER_AMT'] = trim($row['TRANSFER_AMT']);
		$st['SVGS_ACCT_ID_TO_CREDIT'] = trim($row['SVGS_ACCT_ID_TO_CREDIT']);
		$st['REASON'] = trim($row['REASON']);
		$st['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$st['SVGS_HANDLER_USER_ID'] = trim($row['SVGS_HANDLER_USER_ID']);
		$st['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$st['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$st['COMMITTEE_FLG'] = trim($row['COMMITTEE_FLG']);
		$st['COMMITTEE_HANDLER_USER_ID'] = trim($row['COMMITTEE_HANDLER_USER_ID']);
		$st['COMMITTEE_STATUS'] = trim($row['COMMITTEE_STATUS']);
		$st['COMMITTEE_STATUS_DATE'] = trim($row['COMMITTEE_STATUS_DATE']);
		$st['COMMITTEE_RMKS'] = trim($row['COMMITTEE_RMKS']);
		$st['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$st['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$st['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$st['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$st['PROC_MODE'] = trim($row['PROC_MODE']);
		$st['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$st['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$st['TRANSFER_APPLN_STATUS'] = trim($row['TRANSFER_APPLN_STATUS']);
	}

	return $st;
}

# ... ... ... 9.09: FetchSavingsDepositApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsDepositApplns($RQST_STATUS){
	$sd_list = array();
	$x = 0;

	$db_query = "";
	if ($RQST_STATUS=="") {
		$db_query = "SELECT * FROM svgs_deposit_requests ORDER BY RQST_DATE ASC";
	}
	elseif ($RQST_STATUS!="") {
		$db_query = "SELECT * FROM svgs_deposit_requests WHERE RQST_STATUS='$RQST_STATUS' ORDER BY RQST_DATE ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$sd = array();
		$sd['RECORD_ID'] = trim($row['RECORD_ID']);
		$sd['DEPOSIT_REF'] = trim($row['DEPOSIT_REF']);
		$sd['CUST_ID'] = trim($row['CUST_ID']);
		$sd['SVGS_ACCT_ID_TO_CREDIT'] = trim($row['SVGS_ACCT_ID_TO_CREDIT']);
		$sd['AMOUNT_BANKED'] = trim($row['AMOUNT_BANKED']);
		$sd['REASON'] = trim($row['REASON']);
		$sd['BANK_ID'] = trim($row['BANK_ID']);
		$sd['BANK_INST_ACCT_NO'] = trim($row['BANK_INST_ACCT_NO']);
		$sd['BANK_INST_ACCT_NAME'] = trim($row['BANK_INST_ACCT_NAME']);
		$sd['BANK_RECEIPT_REF'] = trim($row['BANK_RECEIPT_REF']);
		$sd['BANK_RECEIPT_ATTCHMT'] = trim($row['BANK_RECEIPT_ATTCHMT']);
		$sd['RQST_DATE'] = trim($row['RQST_DATE']);
		$sd['HANDLED_BY'] = trim($row['HANDLED_BY']);
		$sd['HANDLED_ON'] = trim($row['HANDLED_ON']);
		$sd['HANDLER_RMKS'] = trim($row['HANDLER_RMKS']);
		$sd['APPRVD_BY'] = trim($row['APPRVD_BY']);
		$sd['APPRVL_DATE'] = trim($row['APPRVL_DATE']);
		$sd['APPRVL_RMKS'] = trim($row['APPRVL_RMKS']);
		$sd['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$sd['RQST_STATUS'] = trim($row['RQST_STATUS']);
		$sd_list[$x] = $sd;
		$x++;
	}

	return $sd_list;
}

# ... ... ... 9.10: FetchSavingsDepositApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsDepositApplnsById($DEPOSIT_REF){
	$sd = array();

	$q = mysql_query("SELECT * FROM svgs_deposit_requests WHERE DEPOSIT_REF='$DEPOSIT_REF'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$sd['RECORD_ID'] = trim($row['RECORD_ID']);
		$sd['DEPOSIT_REF'] = trim($row['DEPOSIT_REF']);
		$sd['CUST_ID'] = trim($row['CUST_ID']);
		$sd['SVGS_ACCT_ID_TO_CREDIT'] = trim($row['SVGS_ACCT_ID_TO_CREDIT']);
		$sd['AMOUNT_BANKED'] = trim($row['AMOUNT_BANKED']);
		$sd['REASON'] = trim($row['REASON']);
		$sd['BANK_ID'] = trim($row['BANK_ID']);
		$sd['BANK_INST_ACCT_NO'] = trim($row['BANK_INST_ACCT_NO']);
		$sd['BANK_INST_ACCT_NAME'] = trim($row['BANK_INST_ACCT_NAME']);
		$sd['BANK_RECEIPT_REF'] = trim($row['BANK_RECEIPT_REF']);
		$sd['BANK_RECEIPT_ATTCHMT'] = trim($row['BANK_RECEIPT_ATTCHMT']);
		$sd['RQST_DATE'] = trim($row['RQST_DATE']);
		$sd['HANDLED_BY'] = trim($row['HANDLED_BY']);
		$sd['HANDLED_ON'] = trim($row['HANDLED_ON']);
		$sd['HANDLER_RMKS'] = trim($row['HANDLER_RMKS']);
		$sd['APPRVD_BY'] = trim($row['APPRVD_BY']);
		$sd['APPRVL_DATE'] = trim($row['APPRVL_DATE']);
		$sd['APPRVL_RMKS'] = trim($row['APPRVL_RMKS']);
		$sd['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$sd['RQST_STATUS'] = trim($row['RQST_STATUS']);
	}

	return $sd;
}

# ... ... ... 9.11: FetchSavingsDepositApplnsByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsDepositApplnsByRecordId($RECORD_ID){
	$sd = array();

	$q = mysql_query("SELECT * FROM svgs_deposit_requests WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$sd['RECORD_ID'] = trim($row['RECORD_ID']);
		$sd['DEPOSIT_REF'] = trim($row['DEPOSIT_REF']);
		$sd['CUST_ID'] = trim($row['CUST_ID']);
		$sd['SVGS_ACCT_ID_TO_CREDIT'] = trim($row['SVGS_ACCT_ID_TO_CREDIT']);
		$sd['AMOUNT_BANKED'] = trim($row['AMOUNT_BANKED']);
		$sd['REASON'] = trim($row['REASON']);
		$sd['BANK_ID'] = trim($row['BANK_ID']);
		$sd['BANK_INST_ACCT_NO'] = trim($row['BANK_INST_ACCT_NO']);
		$sd['BANK_INST_ACCT_NAME'] = trim($row['BANK_INST_ACCT_NAME']);
		$sd['BANK_RECEIPT_REF'] = trim($row['BANK_RECEIPT_REF']);
		$sd['BANK_RECEIPT_ATTCHMT'] = trim($row['BANK_RECEIPT_ATTCHMT']);
		$sd['RQST_DATE'] = trim($row['RQST_DATE']);
		$sd['HANDLED_BY'] = trim($row['HANDLED_BY']);
		$sd['HANDLED_ON'] = trim($row['HANDLED_ON']);
		$sd['HANDLER_RMKS'] = trim($row['HANDLER_RMKS']);
		$sd['APPRVD_BY'] = trim($row['APPRVD_BY']);
		$sd['APPRVL_DATE'] = trim($row['APPRVL_DATE']);
		$sd['APPRVL_RMKS'] = trim($row['APPRVL_RMKS']);
		$sd['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$sd['RQST_STATUS'] = trim($row['RQST_STATUS']);
	}

	return $sd;
}

# ... ... ... 9.12: FetchFinInstitutions ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchFinInstitutions(){
	$fin_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM fin_instns WHERE BANK_STATUS='ACTIVE' ORDER BY FIN_INST_NAME ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$fin = array();
		$fin['RECORD_ID'] = trim($row['RECORD_ID']);	
		$fin['FIN_INST_ID'] = trim($row['FIN_INST_ID']);	
		$fin['FIN_INST_NAME'] = trim($row['FIN_INST_NAME']);	
		$fin['SORTCODE'] = trim($row['SORTCODE']);	
		$fin['SWIFT_CODE'] = trim($row['SWIFT_CODE']);	
		$fin['BANK_CODE'] = trim($row['BANK_CODE']);	
		$fin['ORG_HAS_ACCT'] = trim($row['ORG_HAS_ACCT']);	
		$fin['ORG_ACCT_NUM'] = trim($row['ORG_ACCT_NUM']);	
		$fin['MIFOS_PYMT_TYPE_ID'] = trim($row['MIFOS_PYMT_TYPE_ID']);	
		$fin['MIFOS_GL_ACCT_ID'] = trim($row['MIFOS_GL_ACCT_ID']);	
		$fin['DATE_ADDED'] = trim($row['DATE_ADDED']);	
		$fin['ADDED_BY'] = trim($row['ADDED_BY']);	
		$fin['DATE_APPROVED'] = trim($row['DATE_APPROVED']);	
		$fin['APPROVED_BY'] = trim($row['APPROVED_BY']);	
		$fin['DATE_LST_CHNGD'] = trim($row['DATE_LST_CHNGD']);	
		$fin['LST_CHNGD_BY'] = trim($row['LST_CHNGD_BY']);	
		$fin['BANK_STATUS'] = trim($row['BANK_STATUS']);	

		$fin_list[$x] = $fin;
		$x++;
	}

	return $fin_list;
}

# ... ... ... 9.13: FetchFinInstitutionsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchFinInstitutionsById($FIN_INST_ID){
	$fin = array();
	$q = mysql_query("SELECT * FROM fin_instns WHERE FIN_INST_ID='$FIN_INST_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$fin['RECORD_ID'] = trim($row['RECORD_ID']);	
		$fin['FIN_INST_ID'] = trim($row['FIN_INST_ID']);	
		$fin['FIN_INST_NAME'] = trim($row['FIN_INST_NAME']);	
		$fin['SORTCODE'] = trim($row['SORTCODE']);	
		$fin['SWIFT_CODE'] = trim($row['SWIFT_CODE']);	
		$fin['BANK_CODE'] = trim($row['BANK_CODE']);	
		$fin['ORG_HAS_ACCT'] = trim($row['ORG_HAS_ACCT']);	
		$fin['ORG_ACCT_NUM'] = trim($row['ORG_ACCT_NUM']);	
		$fin['MIFOS_PYMT_TYPE_ID'] = trim($row['MIFOS_PYMT_TYPE_ID']);	
		$fin['MIFOS_GL_ACCT_ID'] = trim($row['MIFOS_GL_ACCT_ID']);	
		$fin['DATE_ADDED'] = trim($row['DATE_ADDED']);	
		$fin['ADDED_BY'] = trim($row['ADDED_BY']);	
		$fin['DATE_APPROVED'] = trim($row['DATE_APPROVED']);	
		$fin['APPROVED_BY'] = trim($row['APPROVED_BY']);	
		$fin['DATE_LST_CHNGD'] = trim($row['DATE_LST_CHNGD']);	
		$fin['LST_CHNGD_BY'] = trim($row['LST_CHNGD_BY']);	
		$fin['BANK_STATUS'] = trim($row['BANK_STATUS']);	
	}

	return $fin;
}

# ... ... ... 9.14: FetchSavingsWithdrawApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsWithdrawApplnsBtnPeriods($START_DATE, $END_DATE){
	$sw_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM svgs_withdraw_requests 
		                WHERE APPROVAL_DATE>='$START_DATE' AND APPROVAL_DATE<'$END_DATE' 
		                ORDER BY APPROVAL_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$sw = array();
		$sw['RECORD_ID'] = trim($row['RECORD_ID']);
		$sw['WITHDRAW_REF'] = trim($row['WITHDRAW_REF']);
		$sw['CUST_ID'] = trim($row['CUST_ID']);
		$sw['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$sw['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$sw['REASON'] = trim($row['REASON']);
		$sw['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$sw['SVGS_HANDLER_USER_ID'] = trim($row['SVGS_HANDLER_USER_ID']);
		$sw['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$sw['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$sw['COMMITTEE_FLG'] = trim($row['COMMITTEE_FLG']);
		$sw['COMMITTEE_HANDLER_USER_ID'] = trim($row['COMMITTEE_HANDLER_USER_ID']);
		$sw['COMMITTEE_STATUS'] = trim($row['COMMITTEE_STATUS']);
		$sw['COMMITTEE_STATUS_DATE'] = trim($row['COMMITTEE_STATUS_DATE']);
		$sw['COMMITTEE_RMKS'] = trim($row['COMMITTEE_RMKS']);
		$sw['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$sw['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$sw['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$sw['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$sw['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$sw['PROC_MODE'] = trim($row['PROC_MODE']);
		$sw['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$sw['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$sw['SVGS_APPLN_STATUS'] = trim($row['SVGS_APPLN_STATUS']);
		$sw_list[$x] = $sw;
		$x++;
	}

	return $sw_list;
}

# ... ... ... 9.14: FetchSavingsWithdrawApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsWithdrawApplnsBtnPeriodsAppDate($START_DATE, $END_DATE){
	$sw_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM svgs_withdraw_requests 
		                WHERE APPLN_SUBMISSION_DATE>='$START_DATE' AND APPLN_SUBMISSION_DATE<'$END_DATE' 
		                ORDER BY APPLN_SUBMISSION_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$sw = array();
		$sw['RECORD_ID'] = trim($row['RECORD_ID']);
		$sw['WITHDRAW_REF'] = trim($row['WITHDRAW_REF']);
		$sw['CUST_ID'] = trim($row['CUST_ID']);
		$sw['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$sw['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$sw['REASON'] = trim($row['REASON']);
		$sw['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$sw['SVGS_HANDLER_USER_ID'] = trim($row['SVGS_HANDLER_USER_ID']);
		$sw['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$sw['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$sw['COMMITTEE_FLG'] = trim($row['COMMITTEE_FLG']);
		$sw['COMMITTEE_HANDLER_USER_ID'] = trim($row['COMMITTEE_HANDLER_USER_ID']);
		$sw['COMMITTEE_STATUS'] = trim($row['COMMITTEE_STATUS']);
		$sw['COMMITTEE_STATUS_DATE'] = trim($row['COMMITTEE_STATUS_DATE']);
		$sw['COMMITTEE_RMKS'] = trim($row['COMMITTEE_RMKS']);
		$sw['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$sw['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$sw['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$sw['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$sw['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$sw['PROC_MODE'] = trim($row['PROC_MODE']);
		$sw['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$sw['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$sw['SVGS_APPLN_STATUS'] = trim($row['SVGS_APPLN_STATUS']);
		$sw_list[$x] = $sw;
		$x++;
	}

	return $sw_list;
}

# ... ... ... 9.15: FetchSavingsDepositApplnsPerPeriod ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsDepositApplnsPerPeriod($START_DATE, $END_DATE){
	$sd_list = array();
	$x = 0;
	$q = mysql_query("SELECT * FROM svgs_deposit_requests 
		                WHERE RQST_DATE>='$START_DATE' AND RQST_DATE<'$END_DATE'
		                ORDER BY RQST_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$sd = array();
		$sd['RECORD_ID'] = trim($row['RECORD_ID']);
		$sd['DEPOSIT_REF'] = trim($row['DEPOSIT_REF']);
		$sd['CUST_ID'] = trim($row['CUST_ID']);
		$sd['SVGS_ACCT_ID_TO_CREDIT'] = trim($row['SVGS_ACCT_ID_TO_CREDIT']);
		$sd['AMOUNT_BANKED'] = trim($row['AMOUNT_BANKED']);
		$sd['REASON'] = trim($row['REASON']);
		$sd['BANK_ID'] = trim($row['BANK_ID']);
		$sd['BANK_INST_ACCT_NO'] = trim($row['BANK_INST_ACCT_NO']);
		$sd['BANK_INST_ACCT_NAME'] = trim($row['BANK_INST_ACCT_NAME']);
		$sd['BANK_RECEIPT_REF'] = trim($row['BANK_RECEIPT_REF']);
		$sd['BANK_RECEIPT_ATTCHMT'] = trim($row['BANK_RECEIPT_ATTCHMT']);
		$sd['RQST_DATE'] = trim($row['RQST_DATE']);
		$sd['HANDLED_BY'] = trim($row['HANDLED_BY']);
		$sd['HANDLED_ON'] = trim($row['HANDLED_ON']);
		$sd['HANDLER_RMKS'] = trim($row['HANDLER_RMKS']);
		$sd['APPRVD_BY'] = trim($row['APPRVD_BY']);
		$sd['APPRVL_DATE'] = trim($row['APPRVL_DATE']);
		$sd['APPRVL_RMKS'] = trim($row['APPRVL_RMKS']);
		$sd['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$sd['RQST_STATUS'] = trim($row['RQST_STATUS']);
		$sd_list[$x] = $sd;
		$x++;
	}

	return $sd_list;
}

# ... ... ... 9.16: FetchSavingsTransferApplnsPerPeriod ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsTransferApplnsPerPeriod($START_DATE, $END_DATE){
	$st_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM svgs_transfer_requests 
										WHERE APPLN_SUBMISSION_DATE>='$START_DATE' AND APPLN_SUBMISSION_DATE<'$END_DATE' 
										ORDER BY APPLN_SUBMISSION_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$st = array();
		$st['RECORD_ID'] = trim($row['RECORD_ID']);
		$st['TRANSFER_REF'] = trim($row['TRANSFER_REF']);
		$st['CUST_ID'] = trim($row['CUST_ID']);
		$st['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$st['TRANSFER_AMT'] = trim($row['TRANSFER_AMT']);
		$st['SVGS_ACCT_ID_TO_CREDIT'] = trim($row['SVGS_ACCT_ID_TO_CREDIT']);
		$st['REASON'] = trim($row['REASON']);
		$st['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$st['SVGS_HANDLER_USER_ID'] = trim($row['SVGS_HANDLER_USER_ID']);
		$st['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$st['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$st['COMMITTEE_FLG'] = trim($row['COMMITTEE_FLG']);
		$st['COMMITTEE_HANDLER_USER_ID'] = trim($row['COMMITTEE_HANDLER_USER_ID']);
		$st['COMMITTEE_STATUS'] = trim($row['COMMITTEE_STATUS']);
		$st['COMMITTEE_STATUS_DATE'] = trim($row['COMMITTEE_STATUS_DATE']);
		$st['COMMITTEE_RMKS'] = trim($row['COMMITTEE_RMKS']);
		$st['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$st['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$st['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$st['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$st['PROC_MODE'] = trim($row['PROC_MODE']);
		$st['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$st['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$st['TRANSFER_APPLN_STATUS'] = trim($row['TRANSFER_APPLN_STATUS']);

		$st_list[$x] = $st;
		$x++;
	}

	return $st_list;
}

# ... ... ... 9.17: FetchTranProcBanks ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchTranProcBanks(){
	$proc_bank_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM fin_instns 
										WHERE ORG_HAS_ACCT>='YY' 
										  AND BANK_STATUS='ACTIVE' 
										ORDER BY FIN_INST_NAME ASC") or die("ERR_FETCH_BNK: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$proc_bank = array();
		$proc_bank['RECORD_ID'] = trim($row['RECORD_ID']);	
		$proc_bank['FIN_INST_ID'] = trim($row['FIN_INST_ID']);	
		$proc_bank['FIN_INST_NAME'] = trim($row['FIN_INST_NAME']);	
		$proc_bank['SORTCODE'] = trim($row['SORTCODE']);	
		$proc_bank['SWIFT_CODE'] = trim($row['SWIFT_CODE']);	
		$proc_bank['BANK_CODE'] = trim($row['BANK_CODE']);	
		$proc_bank['ORG_HAS_ACCT'] = trim($row['ORG_HAS_ACCT']);	
		$proc_bank['ORG_ACCT_NUM'] = trim($row['ORG_ACCT_NUM']);	
		$proc_bank['MIFOS_PYMT_TYPE_ID'] = trim($row['MIFOS_PYMT_TYPE_ID']);	
		$proc_bank['MIFOS_GL_ACCT_ID'] = trim($row['MIFOS_GL_ACCT_ID']);	
		$proc_bank['DATE_ADDED'] = trim($row['DATE_ADDED']);	
		$proc_bank['ADDED_BY'] = trim($row['ADDED_BY']);	
		$proc_bank['DATE_APPROVED'] = trim($row['DATE_APPROVED']);	
		$proc_bank['APPROVED_BY'] = trim($row['APPROVED_BY']);	
		$proc_bank['DATE_LST_CHNGD'] = trim($row['DATE_LST_CHNGD']);	
		$proc_bank['LST_CHNGD_BY'] = trim($row['LST_CHNGD_BY']);	
		$proc_bank['BANK_STATUS'] = trim($row['BANK_STATUS']);	

		$proc_bank_list[$x] = $proc_bank;
		$x++;
	}

	return $proc_bank_list;
}

# ... ... ... 9.18: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsWithdrawChargeList($CHANNEL, $CHRG_CRNCY, $CHRG_PDT_ID)
{
	$chrg_list = array();
	$x = 0;

	$q = mysql_query("SELECT DISTINCT(CHRG_EXEC_ORDER), CHRG_TYPE  
	                  FROM svgs_appln_mifos_chrg_mappings 
									  WHERE CHRG_EVENT='WITHDRAW' 
										  AND CHANNEL='$CHANNEL'
										  AND CHRG_CRNCY='$CHRG_CRNCY'
										  AND CHRG_PDT_ID='$CHRG_PDT_ID'
										  AND STATUS='ACTIVE'
										GROUP BY CHRG_EXEC_ORDER, CHRG_TYPE
										ORDER BY CHRG_EXEC_ORDER ASC") or die("ERR_FETCH_CHRG: " . mysql_error());									

	while ($row = mysql_fetch_array($q)) {

		$CHRG_EXEC_ORDER = trim($row['CHRG_EXEC_ORDER']);
		$CHRG_TYPE = trim($row['CHRG_TYPE']);

		// ... get the charge type details
		$chrg_block = array();
		$range_chrg_list = array();
		if ($CHRG_TYPE=="RANGE") {
			$qr = mysql_query("SELECT * FROM svgs_appln_mifos_chrg_mappings 
									  WHERE CHRG_EVENT='WITHDRAW' 
										  AND CHANNEL='$CHANNEL'
										  AND CHRG_CRNCY='$CHRG_CRNCY'
										  AND CHRG_PDT_ID='$CHRG_PDT_ID'
											AND CHRG_EXEC_ORDER='$CHRG_EXEC_ORDER'
											AND CHRG_TYPE='$CHRG_TYPE'
										  AND STATUS='ACTIVE'
										ORDER BY TIER_ORDER ASC") or die("ERR_FETCH_CHRG: " . mysql_error());

			$y = 0;
			while ($rowr = mysql_fetch_array($qr)) {
				$chrg = array();
				$chrg['RECORDID'] = trim($rowr['RECORDID']);
				$chrg['CHANNEL'] = trim($rowr['CHANNEL']);
				$chrg['CHRG_CRNCY'] = trim($rowr['CHRG_CRNCY']);
				$chrg['CHRG_PDT_ID'] = trim($rowr['CHRG_PDT_ID']);
				$chrg['CHRG_PDT_NAME'] = trim($rowr['CHRG_PDT_NAME']);
				$chrg['CHRG_EVENT'] = trim($rowr['CHRG_EVENT']);
				$chrg['CHRG_EXEC_ORDER'] = trim($rowr['CHRG_EXEC_ORDER']);
				$chrg['CHRG_TYPE'] = trim($rowr['CHRG_TYPE']);
				$chrg['TIER_ORDER'] = trim($rowr['TIER_ORDER']);
				$chrg['MIN_TIER'] = trim($rowr['MIN_TIER']);
				$chrg['MAX_TIER'] = trim($rowr['MAX_TIER']);
				$chrg['CHRG_AMT'] = trim($rowr['CHRG_AMT']);
				$chrg['MIFOS_CHRG_ID'] = trim($rowr['MIFOS_CHRG_ID']);
				$chrg['MIFOS_CHRG_NAME'] = trim($rowr['MIFOS_CHRG_NAME']);
				$chrg['STATUS'] = trim($rowr['STATUS']);
				$range_chrg_list[$y] = $chrg;
				$y++;
			}						
			
		} else {

			$qr = mysql_query("SELECT * FROM svgs_appln_mifos_chrg_mappings 
									  WHERE CHRG_EVENT='WITHDRAW' 
										  AND CHANNEL='$CHANNEL'
										  AND CHRG_CRNCY='$CHRG_CRNCY'
										  AND CHRG_PDT_ID='$CHRG_PDT_ID'
											AND CHRG_EXEC_ORDER='$CHRG_EXEC_ORDER'
											AND CHRG_TYPE='$CHRG_TYPE'
										  AND STATUS='ACTIVE'
										ORDER BY CHRG_EXEC_ORDER ASC") or die("ERR_FETCH_CHRG: " . mysql_error());
			$p = 0;
			while ($rowr = mysql_fetch_array($qr)) {
				$chrg = array();
				$chrg['RECORDID'] = trim($rowr['RECORDID']);
				$chrg['CHANNEL'] = trim($rowr['CHANNEL']);
				$chrg['CHRG_CRNCY'] = trim($rowr['CHRG_CRNCY']);
				$chrg['CHRG_PDT_ID'] = trim($rowr['CHRG_PDT_ID']);
				$chrg['CHRG_PDT_NAME'] = trim($rowr['CHRG_PDT_NAME']);
				$chrg['CHRG_EVENT'] = trim($rowr['CHRG_EVENT']);
				$chrg['CHRG_EXEC_ORDER'] = trim($rowr['CHRG_EXEC_ORDER']);
				$chrg['CHRG_TYPE'] = trim($rowr['CHRG_TYPE']);
				$chrg['TIER_ORDER'] = trim($rowr['TIER_ORDER']);
				$chrg['MIN_TIER'] = trim($rowr['MIN_TIER']);
				$chrg['MAX_TIER'] = trim($rowr['MAX_TIER']);
				$chrg['CHRG_AMT'] = trim($rowr['CHRG_AMT']);
				$chrg['MIFOS_CHRG_ID'] = trim($rowr['MIFOS_CHRG_ID']);
				$chrg['MIFOS_CHRG_NAME'] = trim($rowr['MIFOS_CHRG_NAME']);
				$chrg['STATUS'] = trim($rowr['STATUS']);
				$range_chrg_list[$p] = $chrg;
				$p++;
			}									
		}

		// ... assemble final message
		$chrg_block["TT_CHRG_TYPE"] = $CHRG_TYPE;
		$chrg_block["TT_CHRG_LIST"] = $range_chrg_list;
		$chrg_list[$x] = $chrg_block;
		$x++;
	}

	return $chrg_list;
}

# ... ... ... 9.19: GetChargeFeeFromRange ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function GetChargeFeeFromRange($RANGE_MATRIX, $TRAN_AMOUNT)
{
	$chrg_tarrif = array();
	$CHRG_TXN_FEE = 0;
	$CHRG_MIFOS_CHRG_ID = 0;

	for ($x = 0; $x < sizeof($RANGE_MATRIX); $x++) {
		$tt = array();
		$tt = $RANGE_MATRIX[$x];
		$MIN_TIER = $tt['MIN_TIER'];
		$MAX_TIER = $tt['MAX_TIER'];
		$CHRG_AMT = $tt['CHRG_AMT'];
		$MIFOS_CHRG_ID = $tt['MIFOS_CHRG_ID'];

		if (($TRAN_AMOUNT >= $MIN_TIER) && ($TRAN_AMOUNT <= $MAX_TIER)) {
			$CHRG_TXN_FEE = $CHRG_AMT;
			$CHRG_MIFOS_CHRG_ID = $MIFOS_CHRG_ID;
			break;
		}
	}

	$chrg_tarrif["CHRG_TXN_FEE"] = $CHRG_TXN_FEE;
	$chrg_tarrif["CHRG_MIFOS_CHRG_ID"] = $CHRG_MIFOS_CHRG_ID;
	
	return $chrg_tarrif;
}

# **..** **..** **..** **..** **..** SECTION 11: Shares Application Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 11: Shares Application Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 11: Shares Application Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 11: Shares Application Management **..** **..** **..** **..** **..**  **..** **..** 

# ... ... ... 11.01: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchShareRequestApplns($SHARES_APPLN_STATUS){
	$shr_list = array();
	$x = 0;

	$db_query = "";
	if ($SHARES_APPLN_STATUS=="") {
		$db_query = "SELECT * FROM shares_appln_requests ORDER BY APPLN_SUBMISSION_DATE ASC";
	}
	elseif ($SHARES_APPLN_STATUS!="") {
		$db_query = "SELECT * FROM shares_appln_requests WHERE SHARES_APPLN_STATUS='$SHARES_APPLN_STATUS' ORDER BY APPLN_SUBMISSION_DATE ASC";
	}

	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$shr = array();
		$shr['RECORD_ID'] = trim($row['RECORD_ID']);
		$shr['SHARES_APPLN_REF'] = trim($row['SHARES_APPLN_REF']);
		$shr['CUST_ID'] = trim($row['CUST_ID']);
		$shr['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$shr['SHARES_REQUESTED'] = trim($row['SHARES_REQUESTED']);
		$shr['SHARES_ACCT_ID_TO_CREDIT'] = trim($row['SHARES_ACCT_ID_TO_CREDIT']);
		$shr['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$shr['SHARES_HANDLER_USER_ID'] = trim($row['SHARES_HANDLER_USER_ID']);
		$shr['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$shr['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$shr['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$shr['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$shr['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$shr['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$shr['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$shr['SHARES_APPLN_STATUS'] = trim($row['SHARES_APPLN_STATUS']);
		$shr_list[$x] = $shr;
		$x++;
	}

	return $shr_list;
}

# ... ... ... 11.02: FetchShareRequestApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchShareRequestApplnsById($SHARES_APPLN_REF){
	$shr = array();

	$q = mysql_query("SELECT * FROM shares_appln_requests WHERE SHARES_APPLN_REF='$SHARES_APPLN_REF'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$shr = array();
		$shr['RECORD_ID'] = trim($row['RECORD_ID']);
		$shr['CHANNEL'] = trim($row['CHANNEL']);
		$shr['SHARES_APPLN_REF'] = trim($row['SHARES_APPLN_REF']);
		$shr['CUST_ID'] = trim($row['CUST_ID']);
		$shr['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$shr['SHARES_REQUESTED'] = trim($row['SHARES_REQUESTED']);
		$shr['SHARES_ACCT_ID_TO_CREDIT'] = trim($row['SHARES_ACCT_ID_TO_CREDIT']);
		$shr['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$shr['SHARES_HANDLER_USER_ID'] = trim($row['SHARES_HANDLER_USER_ID']);
		$shr['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$shr['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$shr['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$shr['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$shr['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$shr['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$shr['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$shr['SHARES_APPLN_STATUS'] = trim($row['SHARES_APPLN_STATUS']);
	}

	return $shr;
}

# ... ... ... 11.03: FetchShareRequestApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchShareRequestApplnsByRecordId($RECORD_ID){
	$shr = array();

	$q = mysql_query("SELECT * FROM shares_appln_requests WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$shr = array();
		$shr['RECORD_ID'] = trim($row['RECORD_ID']);
		$shr['SHARES_APPLN_REF'] = trim($row['SHARES_APPLN_REF']);
		$shr['CUST_ID'] = trim($row['CUST_ID']);
		$shr['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$shr['SHARES_REQUESTED'] = trim($row['SHARES_REQUESTED']);
		$shr['SHARES_ACCT_ID_TO_CREDIT'] = trim($row['SHARES_ACCT_ID_TO_CREDIT']);
		$shr['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$shr['SHARES_HANDLER_USER_ID'] = trim($row['SHARES_HANDLER_USER_ID']);
		$shr['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$shr['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$shr['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$shr['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$shr['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$shr['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$shr['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$shr['SHARES_APPLN_STATUS'] = trim($row['SHARES_APPLN_STATUS']);
	}

	return $shr;
}

# ... ... ... 11.04: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchShareRequestApplnsPerPeriod($START_DATE, $END_DATE){
	$shr_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM shares_appln_requests 
									  WHERE APPLN_SUBMISSION_DATE>='$START_DATE' AND APPLN_SUBMISSION_DATE<'$END_DATE'
									  ORDER BY APPLN_SUBMISSION_DATE ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$shr = array();
		$shr['RECORD_ID'] = trim($row['RECORD_ID']);
		$shr['SHARES_APPLN_REF'] = trim($row['SHARES_APPLN_REF']);
		$shr['CUST_ID'] = trim($row['CUST_ID']);
		$shr['SVGS_ACCT_ID_TO_DEBIT'] = trim($row['SVGS_ACCT_ID_TO_DEBIT']);
		$shr['SHARES_REQUESTED'] = trim($row['SHARES_REQUESTED']);
		$shr['SHARES_ACCT_ID_TO_CREDIT'] = trim($row['SHARES_ACCT_ID_TO_CREDIT']);
		$shr['APPLN_SUBMISSION_DATE'] = trim($row['APPLN_SUBMISSION_DATE']);
		$shr['SHARES_HANDLER_USER_ID'] = trim($row['SHARES_HANDLER_USER_ID']);
		$shr['FIRST_HANDLED_ON'] = trim($row['FIRST_HANDLED_ON']);
		$shr['FIRST_HANDLE_RMKS'] = trim($row['FIRST_HANDLE_RMKS']);
		$shr['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$shr['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$shr['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$shr['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$shr['CORE_TXN_ID'] = trim($row['CORE_TXN_ID']);
		$shr['SHARES_APPLN_STATUS'] = trim($row['SHARES_APPLN_STATUS']);
		$shr_list[$x] = $shr;
		$x++;
	}

	return $shr_list;
}

# ... ... ... 11.05: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSharesPurchaseChargeList($CHANNEL, $CHRG_CRNCY, $CHRG_PDT_ID){
	$chrg_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM shares_appln_mifos_chrg_mappings 
									  WHERE CHRG_EVENT='PURCHASE_SHARES' 
										  AND CHANNEL='$CHANNEL'
										  AND CHRG_CRNCY='$CHRG_CRNCY'
										  AND CHRG_PDT_ID='$CHRG_PDT_ID'
										  AND STATUS='ACTIVE'
									  ORDER BY CHRG_EXEC_ORDER ASC") or die("ERR_FETCH_CHRG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$chrg = array();
		$chrg['RECORDID'] = trim($row['RECORDID']);
		$chrg['CHANNEL'] = trim($row['CHANNEL']);
		$chrg['CHRG_CRNCY'] = trim($row['CHRG_CRNCY']);
		$chrg['CHRG_PDT_ID'] = trim($row['CHRG_PDT_ID']);
		$chrg['CHRG_PDT_NAME'] = trim($row['CHRG_PDT_NAME']);
		$chrg['CHRG_EVENT'] = trim($row['CHRG_EVENT']);
		$chrg['CHRG_EXEC_ORDER'] = trim($row['CHRG_EXEC_ORDER']);
		$chrg['CHRG_TYPE'] = trim($row['CHRG_TYPE']);
		$chrg['MIFOS_CHRG_ID'] = trim($row['MIFOS_CHRG_ID']);
		$chrg['MIFOS_CHRG_NAME'] = trim($row['MIFOS_CHRG_NAME']);
		$chrg['STATUS'] = trim($row['STATUS']);
		$chrg_list[$x] = $chrg;
		$x++;
	}

	return $chrg_list;
}


# **..** **..** **..** **..** **..** SECTION 12: Bulk Transaction Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 12: Bulk Transaction Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 12: Bulk Transaction Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** SECTION 12: Bulk Transaction Management **..** **..** **..** **..** **..**  **..** **..** 

# ... ... ... 12.01: FetchBulkTemplateList ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkTemplateList($TEMPLATE_STATUS){
	$tmp_list = array();
	$x = 0;

	$db_query = "";
	if ($TEMPLATE_STATUS=="") {
		$db_query = "SELECT * FROM blk_pymt_template ORDER BY TEMPLATE_NAME ASC";
	}
	elseif ($TEMPLATE_STATUS!="") {
		$db_query = "SELECT * FROM blk_pymt_template WHERE TEMPLATE_STATUS='$TEMPLATE_STATUS' ORDER BY TEMPLATE_NAME ASC";
	}

		$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$tmp = array();
		$tmp['RECORD_ID'] = trim($row['RECORD_ID']);
		$tmp['TEMPLATE_ID'] = trim($row['TEMPLATE_ID']);
		$tmp['TEMPLATE_NAME'] = trim($row['TEMPLATE_NAME']);
		$tmp['CREATED_BY'] = trim($row['CREATED_BY']);
		$tmp['CREATED_ON'] = trim($row['CREATED_ON']);
		$tmp['DATE_LST_CHNGD'] = trim($row['DATE_LST_CHNGD']);
		$tmp['LST_CHNGD_BY'] = trim($row['LST_CHNGD_BY']);
		$tmp['TEMPLATE_STATUS'] = trim($row['TEMPLATE_STATUS']);

		$tmp_list[$x] = $tmp;
		$x++;
	}

	return $tmp_list;
}

# ... ... ... 12.02: FetchBulkTemplateById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkTemplateById($TEMPLATE_ID){
	$tmp = array();

	$q = mysql_query("SELECT * FROM blk_pymt_template WHERE TEMPLATE_ID='$TEMPLATE_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tmp['RECORD_ID'] = trim($row['RECORD_ID']);
		$tmp['TEMPLATE_ID'] = trim($row['TEMPLATE_ID']);
		$tmp['TEMPLATE_NAME'] = trim($row['TEMPLATE_NAME']);
		$tmp['CREATED_BY'] = trim($row['CREATED_BY']);
		$tmp['CREATED_ON'] = trim($row['CREATED_ON']);
		$tmp['DATE_LST_CHNGD'] = trim($row['DATE_LST_CHNGD']);
		$tmp['LST_CHNGD_BY'] = trim($row['LST_CHNGD_BY']);
		$tmp['TEMPLATE_STATUS'] = trim($row['TEMPLATE_STATUS']);
	}

	return $tmp;
}

# ... ... ... 12.03: FetchBulkTemplateByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkTemplateByRecordId($RECORD_ID){
	$tmp = array();

	$q = mysql_query("SELECT * FROM blk_pymt_template WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$tmp['RECORD_ID'] = trim($row['RECORD_ID']);
		$tmp['TEMPLATE_ID'] = trim($row['TEMPLATE_ID']);
		$tmp['TEMPLATE_NAME'] = trim($row['TEMPLATE_NAME']);
		$tmp['CREATED_BY'] = trim($row['CREATED_BY']);
		$tmp['CREATED_ON'] = trim($row['CREATED_ON']);
		$tmp['DATE_LST_CHNGD'] = trim($row['DATE_LST_CHNGD']);
		$tmp['LST_CHNGD_BY'] = trim($row['LST_CHNGD_BY']);
		$tmp['TEMPLATE_STATUS'] = trim($row['TEMPLATE_STATUS']);
	}
	return $tmp;
}

# ... ... ... 12.04: FetchBulkTemplateDetailsListDebits ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkTemplateDetailsListDebits($TEMPLATE_ID){
	$tmp_dtl_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM blk_pymt_template_details WHERE TEMPLATE_ID='$TEMPLATE_ID' AND TRAN_TYPE='D' ORDER BY SVGS_ACCT_NAME ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$tmp_dtl = array();
		$tmp_dtl['RECORD_ID'] = trim($row['RECORD_ID']);
		$tmp_dtl['TEMPLATE_ID'] = trim($row['TEMPLATE_ID']);
		$tmp_dtl['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$tmp_dtl['SVGS_ACCT_ID'] = trim($row['SVGS_ACCT_ID']);
		$tmp_dtl['SVGS_ACCT_NUM'] = trim($row['SVGS_ACCT_NUM']);
		$tmp_dtl['PDT_NAME'] = trim($row['PDT_NAME']);
		$tmp_dtl['SVGS_ACCT_NAME'] = trim($row['SVGS_ACCT_NAME']);
		$tmp_dtl['CURRENCY'] = trim($row['CURRENCY']);
		$tmp_dtl['TRAN_TYPE'] = trim($row['TRAN_TYPE']);
		$tmp_dtl['TRAN_AMT'] = trim($row['TRAN_AMT']);
		$tmp_dtl['TRAN_NARRATION'] = trim($row['TRAN_NARRATION']);
		$tmp_dtl['ADDED_BY'] = trim($row['ADDED_BY']);
		$tmp_dtl['ADDED_ON'] = trim($row['ADDED_ON']);
		$tmp_dtl['TEMPLATE_STATUS'] = trim($row['TEMPLATE_STATUS']);


		$tmp_dtl_list[$x] = $tmp_dtl;
		$x++;
	}

	return $tmp_dtl_list;
}

# ... ... ... 12.05: FetchBulkTemplateDetailsListCredits ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkTemplateDetailsListCredits($TEMPLATE_ID){
	$tmp_dtl_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM blk_pymt_template_details WHERE TEMPLATE_ID='$TEMPLATE_ID' AND TRAN_TYPE='C' ORDER BY SVGS_ACCT_NAME ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$tmp_dtl = array();
		$tmp_dtl['RECORD_ID'] = trim($row['RECORD_ID']);
		$tmp_dtl['TEMPLATE_ID'] = trim($row['TEMPLATE_ID']);
		$tmp_dtl['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$tmp_dtl['SVGS_ACCT_ID'] = trim($row['SVGS_ACCT_ID']);
		$tmp_dtl['SVGS_ACCT_NUM'] = trim($row['SVGS_ACCT_NUM']);
		$tmp_dtl['PDT_NAME'] = trim($row['PDT_NAME']);
		$tmp_dtl['SVGS_ACCT_NAME'] = trim($row['SVGS_ACCT_NAME']);
		$tmp_dtl['CURRENCY'] = trim($row['CURRENCY']);
		$tmp_dtl['TRAN_TYPE'] = trim($row['TRAN_TYPE']);
		$tmp_dtl['TRAN_AMT'] = trim($row['TRAN_AMT']);
		$tmp_dtl['TRAN_NARRATION'] = trim($row['TRAN_NARRATION']);
		$tmp_dtl['ADDED_BY'] = trim($row['ADDED_BY']);
		$tmp_dtl['ADDED_ON'] = trim($row['ADDED_ON']);
		$tmp_dtl['TEMPLATE_STATUS'] = trim($row['TEMPLATE_STATUS']);


		$tmp_dtl_list[$x] = $tmp_dtl;
		$x++;
	}

	return $tmp_dtl_list;
}

# ... ... ... 12.06: FetchBulkTemplateDetailsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkTemplateDetailsById($TEMPLATE_ID){
	$tmp_dtl = array();

	$q = mysql_query("SELECT * FROM blk_pymt_template_details WHERE TEMPLATE_ID='$TEMPLATE_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$tmp_dtl['RECORD_ID'] = trim($row['RECORD_ID']);
		$tmp_dtl['TEMPLATE_ID'] = trim($row['TEMPLATE_ID']);
		$tmp_dtl['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$tmp_dtl['SVGS_ACCT_ID'] = trim($row['SVGS_ACCT_ID']);
		$tmp_dtl['SVGS_ACCT_NUM'] = trim($row['SVGS_ACCT_NUM']);
		$tmp_dtl['SVGS_ACCT_NAME'] = trim($row['SVGS_ACCT_NAME']);
		$tmp_dtl['CURRENCY'] = trim($row['CURRENCY']);
		$tmp_dtl['TRAN_TYPE'] = trim($row['TRAN_TYPE']);
		$tmp_dtl['TRAN_AMT'] = trim($row['TRAN_AMT']);
		$tmp_dtl['TRAN_NARRATION'] = trim($row['TRAN_NARRATION']);
		$tmp_dtl['ADDED_BY'] = trim($row['ADDED_BY']);
		$tmp_dtl['ADDED_ON'] = trim($row['ADDED_ON']);
		$tmp_dtl['TEMPLATE_STATUS'] = trim($row['TEMPLATE_STATUS']);

		$tmp_dtl_list[$x] = $tmp_dtl;
		$x++;
	}

	return $tmp_dtl;
}

# ... ... ... 12.07: FetchBulkTemplateDetailsByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkTemplateDetailsByRecordId($RECORD_ID){
	$tmp_dtl = array();

	$q = mysql_query("SELECT * FROM blk_pymt_template_details WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$tmp_dtl['RECORD_ID'] = trim($row['RECORD_ID']);
		$tmp_dtl['TEMPLATE_ID'] = trim($row['TEMPLATE_ID']);
		$tmp_dtl['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$tmp_dtl['SVGS_ACCT_ID'] = trim($row['SVGS_ACCT_ID']);
		$tmp_dtl['SVGS_ACCT_NUM'] = trim($row['SVGS_ACCT_NUM']);
		$tmp_dtl['SVGS_ACCT_NAME'] = trim($row['SVGS_ACCT_NAME']);
		$tmp_dtl['CURRENCY'] = trim($row['CURRENCY']);
		$tmp_dtl['TRAN_TYPE'] = trim($row['TRAN_TYPE']);
		$tmp_dtl['TRAN_AMT'] = trim($row['TRAN_AMT']);
		$tmp_dtl['TRAN_NARRATION'] = trim($row['TRAN_NARRATION']);
		$tmp_dtl['ADDED_BY'] = trim($row['ADDED_BY']);
		$tmp_dtl['ADDED_ON'] = trim($row['ADDED_ON']);
		$tmp_dtl['TEMPLATE_STATUS'] = trim($row['TEMPLATE_STATUS']);

		$tmp_dtl_list[$x] = $tmp_dtl;
		$x++;
	}

	return $tmp_dtl;
}

# ... ... ... 12.08: FetchPendingBulkFiles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchPendingBulkFiles(){
	$file_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM blk_pymt_file 
		                WHERE FILE_ID in (select distinct(b.FILE_ID) from blk_pymt_txns b WHERE b.TRAN_STATUS='PENDING')
		                ORDER BY UPLOADED_ON ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$file = array();
		$file['RECORD_ID'] = trim($row['RECORD_ID']);
		$file['FILE_ID'] = trim($row['FILE_ID']);
		$file['FILE_NAME'] = trim($row['FILE_NAME']);
		$file['UPLOAD_REASON'] = trim($row['UPLOAD_REASON']);
		$file['UPLOADED_BY'] = trim($row['UPLOADED_BY']);
		$file['UPLOADED_ON'] = trim($row['UPLOADED_ON']);
		$file['VERIFIED_RMKS'] = trim($row['VERIFIED_RMKS']);
		$file['VERIFIED_BY'] = trim($row['VERIFIED_BY']);
		$file['VERIFIED_ON'] = trim($row['VERIFIED_ON']);
		$file['APPROVED_RMKS'] = trim($row['APPROVED_RMKS']);
		$file['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$file['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$file['REVERSAL_FLG'] = trim($row['REVERSAL_FLG']);
		$file['REV_INIT_RMKS'] = trim($row['REV_INIT_RMKS']);
		$file['REV_INIT_BY'] = trim($row['REV_INIT_BY']);
		$file['REV_INIT_ON'] = trim($row['REV_INIT_ON']);
		$file['REV_APPROVED_RMKS'] = trim($row['REV_APPROVED_RMKS']);
		$file['REV_APPROVED_BY'] = trim($row['REV_APPROVED_BY']);
		$file['REV_APPROVED_ON'] = trim($row['REV_APPROVED_ON']);
		$file['FILE_STATUS'] = trim($row['FILE_STATUS']);

		$file_list[$x] = $file;
		$x++;
	}
	return $file_list;
}

# ... ... ... 12.09: FetchPendingBulkFileById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkFileById($FILE_ID){
	$file = array();

	$q = mysql_query("SELECT * FROM blk_pymt_file WHERE FILE_ID='$FILE_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$file['RECORD_ID'] = trim($row['RECORD_ID']);
		$file['FILE_ID'] = trim($row['FILE_ID']);
		$file['FILE_NAME'] = trim($row['FILE_NAME']);
		$file['UPLOAD_REASON'] = trim($row['UPLOAD_REASON']);
		$file['UPLOADED_BY'] = trim($row['UPLOADED_BY']);
		$file['UPLOADED_ON'] = trim($row['UPLOADED_ON']);
		$file['VERIFIED_RMKS'] = trim($row['VERIFIED_RMKS']);
		$file['VERIFIED_BY'] = trim($row['VERIFIED_BY']);
		$file['VERIFIED_ON'] = trim($row['VERIFIED_ON']);
		$file['APPROVED_RMKS'] = trim($row['APPROVED_RMKS']);
		$file['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$file['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$file['REVERSAL_FLG'] = trim($row['REVERSAL_FLG']);
		$file['REV_INIT_RMKS'] = trim($row['REV_INIT_RMKS']);
		$file['REV_INIT_BY'] = trim($row['REV_INIT_BY']);
		$file['REV_INIT_ON'] = trim($row['REV_INIT_ON']);
		$file['REV_APPROVED_RMKS'] = trim($row['REV_APPROVED_RMKS']);
		$file['REV_APPROVED_BY'] = trim($row['REV_APPROVED_BY']);
		$file['REV_APPROVED_ON'] = trim($row['REV_APPROVED_ON']);
		$file['FILE_STATUS'] = trim($row['FILE_STATUS']);

	}
	return $file;
}

# ... ... ... 12.10: FetchPendingBulkFileByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkFileByRecordId($RECORD_ID){
	$file_list = array();

	$q = mysql_query("SELECT * FROM blk_pymt_file WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$file = array();
		$file['RECORD_ID'] = trim($row['RECORD_ID']);
		$file['FILE_ID'] = trim($row['FILE_ID']);
		$file['FILE_NAME'] = trim($row['FILE_NAME']);
		$file['UPLOAD_REASON'] = trim($row['UPLOAD_REASON']);
		$file['UPLOADED_BY'] = trim($row['UPLOADED_BY']);
		$file['UPLOADED_ON'] = trim($row['UPLOADED_ON']);
		$file['VERIFIED_RMKS'] = trim($row['VERIFIED_RMKS']);
		$file['VERIFIED_BY'] = trim($row['VERIFIED_BY']);
		$file['VERIFIED_ON'] = trim($row['VERIFIED_ON']);
		$file['APPROVED_RMKS'] = trim($row['APPROVED_RMKS']);
		$file['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$file['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$file['REVERSAL_FLG'] = trim($row['REVERSAL_FLG']);
		$file['REV_INIT_RMKS'] = trim($row['REV_INIT_RMKS']);
		$file['REV_INIT_BY'] = trim($row['REV_INIT_BY']);
		$file['REV_INIT_ON'] = trim($row['REV_INIT_ON']);
		$file['REV_APPROVED_RMKS'] = trim($row['REV_APPROVED_RMKS']);
		$file['REV_APPROVED_BY'] = trim($row['REV_APPROVED_BY']);
		$file['REV_APPROVED_ON'] = trim($row['REV_APPROVED_ON']);
		$file['FILE_STATUS'] = trim($row['FILE_STATUS']);

	}
	return $file;
}

# ... ... ... 12.11: FetchBulkTxnListDebits ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkTxnListDebits($FILE_ID){
	$txn_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='D' ORDER BY SAVINGS_ACCT_NAME ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$txn = array();
		$txn['RECORD_ID'] = trim($row['RECORD_ID']);
		$txn['TRAN_ID'] = trim($row['TRAN_ID']);
		$txn['FILE_ID'] = trim($row['FILE_ID']);
		$txn['SAVINGS_CUST_ID'] = trim($row['SAVINGS_CUST_ID']);
		$txn['SAVINGS_ACCT_ID'] = trim($row['SAVINGS_ACCT_ID']);
		$txn['SAVINGS_ACCT_NUM'] = trim($row['SAVINGS_ACCT_NUM']);
		$txn['SAVINGS_ACCT_NAME'] = trim($row['SAVINGS_ACCT_NAME']);
		$txn['CURRENCY'] = trim($row['CURRENCY']);
		$txn['TRAN_TYPE'] = trim($row['TRAN_TYPE']);
		$txn['TRAN_AMT'] = trim($row['TRAN_AMT']);
		$txn['TRAN_NARRATION'] = trim($row['TRAN_NARRATION']);
		$txn['PASS_FAIL_FLG'] = trim($row['PASS_FAIL_FLG']);
		$txn['PASS_FAIL_RMKS'] = trim($row['PASS_FAIL_RMKS']);
		$txn['EXEC_FLG'] = trim($row['EXEC_FLG']);
		$txn['EXEC_MSG'] = trim($row['EXEC_MSG']);
		$txn['CORE_REF_ID'] = trim($row['CORE_REF_ID']);
		$txn['TRAN_STATUS'] = trim($row['TRAN_STATUS']);

		$txn_list[$x] = $txn;
		$x++;
	}
	return $txn_list;
}

# ... ... ... 12.12: FetchBulkTxnListCredits ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkTxnListCredits($FILE_ID){
	$txn_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID' AND TRAN_TYPE='C' ORDER BY SAVINGS_ACCT_NAME ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$txn = array();
		$txn['RECORD_ID'] = trim($row['RECORD_ID']);
		$txn['TRAN_ID'] = trim($row['TRAN_ID']);
		$txn['FILE_ID'] = trim($row['FILE_ID']);
		$txn['SAVINGS_CUST_ID'] = trim($row['SAVINGS_CUST_ID']);
		$txn['SAVINGS_ACCT_ID'] = trim($row['SAVINGS_ACCT_ID']);
		$txn['SAVINGS_ACCT_NUM'] = trim($row['SAVINGS_ACCT_NUM']);
		$txn['SAVINGS_ACCT_NAME'] = trim($row['SAVINGS_ACCT_NAME']);
		$txn['CURRENCY'] = trim($row['CURRENCY']);
		$txn['TRAN_TYPE'] = trim($row['TRAN_TYPE']);
		$txn['TRAN_AMT'] = trim($row['TRAN_AMT']);
		$txn['TRAN_NARRATION'] = trim($row['TRAN_NARRATION']);
		$txn['PASS_FAIL_FLG'] = trim($row['PASS_FAIL_FLG']);
		$txn['PASS_FAIL_RMKS'] = trim($row['PASS_FAIL_RMKS']);
		$txn['EXEC_FLG'] = trim($row['EXEC_FLG']);
		$txn['EXEC_MSG'] = trim($row['EXEC_MSG']);
		$txn['CORE_REF_ID'] = trim($row['CORE_REF_ID']);
		$txn['TRAN_STATUS'] = trim($row['TRAN_STATUS']);

		$txn_list[$x] = $txn;
		$x++;
	}
	return $txn_list;
}

# ... ... ... 12.13: FetchPendingBulkFiles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchVerifiedBulkFiles(){
	$file_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM blk_pymt_file 
		                WHERE FILE_ID in (select distinct(b.FILE_ID) from blk_pymt_txns b WHERE b.TRAN_STATUS='VERIFIED')
		                ORDER BY UPLOADED_ON ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$file = array();
		$file['RECORD_ID'] = trim($row['RECORD_ID']);
		$file['FILE_ID'] = trim($row['FILE_ID']);
		$file['FILE_NAME'] = trim($row['FILE_NAME']);
		$file['UPLOAD_REASON'] = trim($row['UPLOAD_REASON']);
		$file['UPLOADED_BY'] = trim($row['UPLOADED_BY']);
		$file['UPLOADED_ON'] = trim($row['UPLOADED_ON']);
		$file['VERIFIED_RMKS'] = trim($row['VERIFIED_RMKS']);
		$file['VERIFIED_BY'] = trim($row['VERIFIED_BY']);
		$file['VERIFIED_ON'] = trim($row['VERIFIED_ON']);
		$file['APPROVED_RMKS'] = trim($row['APPROVED_RMKS']);
		$file['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$file['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$file['REVERSAL_FLG'] = trim($row['REVERSAL_FLG']);
		$file['REV_INIT_RMKS'] = trim($row['REV_INIT_RMKS']);
		$file['REV_INIT_BY'] = trim($row['REV_INIT_BY']);
		$file['REV_INIT_ON'] = trim($row['REV_INIT_ON']);
		$file['REV_APPROVED_RMKS'] = trim($row['REV_APPROVED_RMKS']);
		$file['REV_APPROVED_BY'] = trim($row['REV_APPROVED_BY']);
		$file['REV_APPROVED_ON'] = trim($row['REV_APPROVED_ON']);
		$file['FILE_STATUS'] = trim($row['FILE_STATUS']);

		$file_list[$x] = $file;
		$x++;
	}
	return $file_list;
}

# ... ... ... 12.14: FetchPendingBulkFiles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchApprovedBulkFiles(){
	$file_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM blk_pymt_file 
		                WHERE FILE_ID in (select distinct(b.FILE_ID) from blk_pymt_txns b WHERE b.TRAN_STATUS='APPROVED')
		                ORDER BY UPLOADED_ON ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$file = array();
		$file['RECORD_ID'] = trim($row['RECORD_ID']);
		$file['FILE_ID'] = trim($row['FILE_ID']);
		$file['FILE_NAME'] = trim($row['FILE_NAME']);
		$file['UPLOAD_REASON'] = trim($row['UPLOAD_REASON']);
		$file['UPLOADED_BY'] = trim($row['UPLOADED_BY']);
		$file['UPLOADED_ON'] = trim($row['UPLOADED_ON']);
		$file['VERIFIED_RMKS'] = trim($row['VERIFIED_RMKS']);
		$file['VERIFIED_BY'] = trim($row['VERIFIED_BY']);
		$file['VERIFIED_ON'] = trim($row['VERIFIED_ON']);
		$file['APPROVED_RMKS'] = trim($row['APPROVED_RMKS']);
		$file['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$file['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$file['REVERSAL_FLG'] = trim($row['REVERSAL_FLG']);
		$file['REV_INIT_RMKS'] = trim($row['REV_INIT_RMKS']);
		$file['REV_INIT_BY'] = trim($row['REV_INIT_BY']);
		$file['REV_INIT_ON'] = trim($row['REV_INIT_ON']);
		$file['REV_APPROVED_RMKS'] = trim($row['REV_APPROVED_RMKS']);
		$file['REV_APPROVED_BY'] = trim($row['REV_APPROVED_BY']);
		$file['REV_APPROVED_ON'] = trim($row['REV_APPROVED_ON']);
		$file['FILE_STATUS'] = trim($row['FILE_STATUS']);

		$file_list[$x] = $file;
		$x++;
	}
	return $file_list;
}


# ... ... ... 12.15: FetchBulkFilesPerPeriod ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchBulkFilesPerPeriod($START_DATE, $END_DATE){
	$file_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM blk_pymt_file 
		                WHERE FILE_ID in (select distinct(b.FILE_ID) from blk_pymt_txns b)
		                	AND UPLOADED_ON>='$START_DATE' AND UPLOADED_ON<'$END_DATE'
		                ORDER BY UPLOADED_ON ASC") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$file = array();
		$file['RECORD_ID'] = trim($row['RECORD_ID']);
		$file['FILE_ID'] = trim($row['FILE_ID']);
		$file['FILE_NAME'] = trim($row['FILE_NAME']);
		$file['UPLOAD_REASON'] = trim($row['UPLOAD_REASON']);
		$file['UPLOADED_BY'] = trim($row['UPLOADED_BY']);
		$file['UPLOADED_ON'] = trim($row['UPLOADED_ON']);
		$file['VERIFIED_RMKS'] = trim($row['VERIFIED_RMKS']);
		$file['VERIFIED_BY'] = trim($row['VERIFIED_BY']);
		$file['VERIFIED_ON'] = trim($row['VERIFIED_ON']);
		$file['APPROVED_RMKS'] = trim($row['APPROVED_RMKS']);
		$file['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$file['APPROVED_ON'] = trim($row['APPROVED_ON']);
		$file['REVERSAL_FLG'] = trim($row['REVERSAL_FLG']);
		$file['REV_INIT_RMKS'] = trim($row['REV_INIT_RMKS']);
		$file['REV_INIT_BY'] = trim($row['REV_INIT_BY']);
		$file['REV_INIT_ON'] = trim($row['REV_INIT_ON']);
		$file['REV_APPROVED_RMKS'] = trim($row['REV_APPROVED_RMKS']);
		$file['REV_APPROVED_BY'] = trim($row['REV_APPROVED_BY']);
		$file['REV_APPROVED_ON'] = trim($row['REV_APPROVED_ON']);
		$file['FILE_STATUS'] = trim($row['FILE_STATUS']);

		$file_list[$x] = $file;
		$x++;
	}
	return $file_list;
}



# ... ... ... 3.03: FetchCustFinInstAccts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustFinInstAccts_walkin($CUST_ID){
	$bank_acct_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM cstmrs_bank_details WHERE CUST_ID='$CUST_ID' AND ACCT_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$bank_acct = array();
		$bank_acct['RECORD_ID'] = trim($row['RECORD_ID']);
		$bank_acct['CUST_ID'] = trim($row['CUST_ID']);
		$bank_acct['BANK_ID'] = trim($row['BANK_ID']);
		$bank_acct['BANK_ACCOUNT'] = trim($row['BANK_ACCOUNT']);
		$bank_acct['BANK_ACCOUNT_NAME'] = trim($row['BANK_ACCOUNT_NAME']);
		$bank_acct['DATE_ADDED'] = trim($row['DATE_ADDED']);
		$bank_acct['ACCT_STATUS'] = trim($row['ACCT_STATUS']);

		$bank_acct_list[$x] = $bank_acct;
		$x++;
	}

	return $bank_acct_list;
}


# ... ... ... 9.13: FetchFinInstitutionsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchFinInstitutionsById_walkin($FIN_INST_ID){
	$fin = array();
	$q = mysql_query("SELECT * FROM fin_instns WHERE FIN_INST_ID='$FIN_INST_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$fin['RECORD_ID'] = trim($row['RECORD_ID']);	
		$fin['FIN_INST_ID'] = trim($row['FIN_INST_ID']);	
		$fin['FIN_INST_NAME'] = trim($row['FIN_INST_NAME']);	
		$fin['SORTCODE'] = trim($row['SORTCODE']);	
		$fin['SWIFT_CODE'] = trim($row['SWIFT_CODE']);	
		$fin['BANK_CODE'] = trim($row['BANK_CODE']);	
		$fin['ORG_HAS_ACCT'] = trim($row['ORG_HAS_ACCT']);	
		$fin['ORG_ACCT_NUM'] = trim($row['ORG_ACCT_NUM']);	
		$fin['MIFOS_PYMT_TYPE_ID'] = trim($row['MIFOS_PYMT_TYPE_ID']);	
		$fin['MIFOS_GL_ACCT_ID'] = trim($row['MIFOS_GL_ACCT_ID']);	
		$fin['DATE_ADDED'] = trim($row['DATE_ADDED']);	
		$fin['ADDED_BY'] = trim($row['ADDED_BY']);	
		$fin['DATE_APPROVED'] = trim($row['DATE_APPROVED']);	
		$fin['APPROVED_BY'] = trim($row['APPROVED_BY']);	
		$fin['DATE_LST_CHNGD'] = trim($row['DATE_LST_CHNGD']);	
		$fin['LST_CHNGD_BY'] = trim($row['LST_CHNGD_BY']);	
		$fin['BANK_STATUS'] = trim($row['BANK_STATUS']);	
	}

	return $fin;
}


# ... ... ... 3.09: FetchLoanApplnDetailsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnDetailsById_walkin($LN_APPLN_NO){
	$la = array();
	$q = mysql_query("SELECT * FROM loan_applns WHERE LN_APPLN_NO='$LN_APPLN_NO'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$la['RECORD_ID'] = trim($row['RECORD_ID']);
		$la['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$la['IS_WALK_IN'] = trim($row['IS_WALK_IN']);
		$la['IS_TOP_UP'] = trim($row['IS_TOP_UP']);
		$la['TOP_UP_LOAN_ID'] = trim($row['TOP_UP_LOAN_ID']);
		$la['CUST_ID'] = trim($row['CUST_ID']);
		$la['LN_PDT_ID'] = trim($row['LN_PDT_ID']);
		$la['LN_APPLN_CREATION_DATE'] = trim($row['LN_APPLN_CREATION_DATE']);
		$la['LN_APPLN_PROGRESS_STATUS'] = trim($row['LN_APPLN_PROGRESS_STATUS']);
		$la['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$la['RQSTD_RPYMT_PRD'] = trim($row['RQSTD_RPYMT_PRD']);
		$la['PURPOSE'] = trim($row['PURPOSE']);
		$la['LN_APPLN_SUBMISSION_DATE'] = trim($row['LN_APPLN_SUBMISSION_DATE']);
		$la['LN_APPLN_ASSMT_STATUS'] = trim($row['LN_APPLN_ASSMT_STATUS']);
		$la['LN_APPLN_ASSMT_RMKS'] = trim($row['LN_APPLN_ASSMT_RMKS']);
		$la['LN_APPLN_ASSMT_DATE'] = trim($row['LN_APPLN_ASSMT_DATE']);
		$la['LN_APPLN_ASSMT_USER_ID'] = trim($row['LN_APPLN_ASSMT_USER_ID']);
		$la['LN_APPLN_DOC_STATUS'] = trim($row['LN_APPLN_DOC_STATUS']);
		$la['LN_APPLN_DOC_RMKS'] = trim($row['LN_APPLN_DOC_RMKS']);
		$la['LN_APPLN_DOC_DATE'] = trim($row['LN_APPLN_DOC_DATE']);
		$la['LN_APPLN_DOC_USER_ID'] = trim($row['LN_APPLN_DOC_USER_ID']);
		$la['LN_APPLN_GRRTR_STATUS'] = trim($row['LN_APPLN_GRRTR_STATUS']);
		$la['LN_APPLN_GRRTR_RMKS'] = trim($row['LN_APPLN_GRRTR_RMKS']);
		$la['LN_APPLN_GRRTR_DATE'] = trim($row['LN_APPLN_GRRTR_DATE']);
		$la['LN_APPLN_GRRTR_USER_ID'] = trim($row['LN_APPLN_GRRTR_USER_ID']);
		$la['CC_FLG'] = trim($row['CC_FLG']);
		$la['CC_RECEIVE_DATE'] = trim($row['CC_RECEIVE_DATE']);
		$la['CC_HANDLER_WKFLW_ID'] = trim($row['CC_HANDLER_WKFLW_ID']);
		$la['CC_STATUS'] = trim($row['CC_STATUS']);
		$la['CC_STATUS_DATE'] = trim($row['CC_STATUS_DATE']);
		$la['CC_RMKS'] = trim($row['CC_RMKS']);
		$la['CREDIT_OFFICER_RCMNDTN_USER_ID'] = trim($row['CREDIT_OFFICER_RCMNDTN_USER_ID']);
		$la['RCMNDTN_REQUEST_SEND_DATE'] = trim($row['RCMNDTN_REQUEST_SEND_DATE']);
		$la['RCMNDD_APPLN_AMT'] = trim($row['RCMNDD_APPLN_AMT']);
		$la['RCMNDTN_CUST_RESPONSE_DATE'] = trim($row['RCMNDTN_CUST_RESPONSE_DATE']);
		$la['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$la['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$la['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$la['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$la['CORE_LOAN_ACCT_ID'] = trim($row['CORE_LOAN_ACCT_ID']);
		$la['CORE_SVGS_ACCT_ID'] = trim($row['CORE_SVGS_ACCT_ID']);
		$la['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$la['PROC_MODE'] = trim($row['PROC_MODE']);
		$la['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$la['CORE_RESOURCE_ID'] = trim($row['CORE_RESOURCE_ID']);
		$la['LN_APPLN_STATUS'] = trim($row['LN_APPLN_STATUS']);

	}

	return $la;
}


# ... ... ... 3.11: FetchLoanApplnsByStatus ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnsByStatus_walkin($LN_APPLN_STATUS){
	$loan_appln_list = array();
	$x = 0;

	$db_query = "SELECT * FROM loan_applns WHERE IS_WALK_IN='YES' AND LN_APPLN_STATUS='$LN_APPLN_STATUS' ORDER BY RECORD_ID ASC";
	
	$q = mysql_query($db_query) or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$la = array();
		$la['RECORD_ID'] = trim($row['RECORD_ID']);
		$la['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$la['CUST_ID'] = trim($row['CUST_ID']);
		$la['LN_PDT_ID'] = trim($row['LN_PDT_ID']);
		$la['LN_APPLN_CREATION_DATE'] = trim($row['LN_APPLN_CREATION_DATE']);
		$la['LN_APPLN_PROGRESS_STATUS'] = trim($row['LN_APPLN_PROGRESS_STATUS']);
		$la['RQSTD_AMT'] = trim($row['RQSTD_AMT']);
		$la['RQSTD_RPYMT_PRD'] = trim($row['RQSTD_RPYMT_PRD']);
		$la['PURPOSE'] = trim($row['PURPOSE']);
		$la['LN_APPLN_SUBMISSION_DATE'] = trim($row['LN_APPLN_SUBMISSION_DATE']);
		$la['LN_APPLN_ASSMT_STATUS'] = trim($row['LN_APPLN_ASSMT_STATUS']);
		$la['LN_APPLN_ASSMT_RMKS'] = trim($row['LN_APPLN_ASSMT_RMKS']);
		$la['LN_APPLN_ASSMT_DATE'] = trim($row['LN_APPLN_ASSMT_DATE']);
		$la['LN_APPLN_ASSMT_USER_ID'] = trim($row['LN_APPLN_ASSMT_USER_ID']);
		$la['LN_APPLN_DOC_STATUS'] = trim($row['LN_APPLN_DOC_STATUS']);
		$la['LN_APPLN_DOC_RMKS'] = trim($row['LN_APPLN_DOC_RMKS']);
		$la['LN_APPLN_DOC_DATE'] = trim($row['LN_APPLN_DOC_DATE']);
		$la['LN_APPLN_DOC_USER_ID'] = trim($row['LN_APPLN_DOC_USER_ID']);
		$la['LN_APPLN_GRRTR_STATUS'] = trim($row['LN_APPLN_GRRTR_STATUS']);
		$la['LN_APPLN_GRRTR_RMKS'] = trim($row['LN_APPLN_GRRTR_RMKS']);
		$la['LN_APPLN_GRRTR_DATE'] = trim($row['LN_APPLN_GRRTR_DATE']);
		$la['LN_APPLN_GRRTR_USER_ID'] = trim($row['LN_APPLN_GRRTR_USER_ID']);
		$la['CC_FLG'] = trim($row['CC_FLG']);
		$la['CC_RECEIVE_DATE'] = trim($row['CC_RECEIVE_DATE']);
		$la['CC_HANDLER_WKFLW_ID'] = trim($row['CC_HANDLER_WKFLW_ID']);
		$la['CC_STATUS'] = trim($row['CC_STATUS']);
		$la['CC_STATUS_DATE'] = trim($row['CC_STATUS_DATE']);
		$la['CC_RMKS'] = trim($row['CC_RMKS']);
		$la['CREDIT_OFFICER_RCMNDTN_USER_ID'] = trim($row['CREDIT_OFFICER_RCMNDTN_USER_ID']);
		$la['RCMNDTN_REQUEST_SEND_DATE'] = trim($row['RCMNDTN_REQUEST_SEND_DATE']);
		$la['RCMNDD_APPLN_AMT'] = trim($row['RCMNDD_APPLN_AMT']);
		$la['RCMNDTN_CUST_RESPONSE_DATE'] = trim($row['RCMNDTN_CUST_RESPONSE_DATE']);
		$la['APPROVED_AMT'] = trim($row['APPROVED_AMT']);
		$la['APPROVED_BY'] = trim($row['APPROVED_BY']);
		$la['APPROVAL_DATE'] = trim($row['APPROVAL_DATE']);
		$la['APPROVAL_RMKS'] = trim($row['APPROVAL_RMKS']);
		$la['CORE_LOAN_ACCT_ID'] = trim($row['CORE_LOAN_ACCT_ID']);
		$la['CORE_SVGS_ACCT_ID'] = trim($row['CORE_SVGS_ACCT_ID']);
		$la['CUST_FIN_INST_ID'] = trim($row['CUST_FIN_INST_ID']);
		$la['PROC_MODE'] = trim($row['PROC_MODE']);
		$la['PROC_BATCH_NO'] = trim($row['PROC_BATCH_NO']);
		$la['CORE_RESOURCE_ID'] = trim($row['CORE_RESOURCE_ID']);
		$la['LN_APPLN_STATUS'] = trim($row['LN_APPLN_STATUS']);


		$loan_appln_list[$x] = $la;
		$x++;
	}

	return $loan_appln_list;
}



# ... ... ... 3.12: FetchLoanApplnProgress ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnProgress_walkin($LN_PROGRESS_ID){
	$lnprog = array();
	$q = mysql_query("SELECT * FROM loan_appln_progress WHERE LN_PROGRESS_ID='$LN_PROGRESS_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$lnprog['LN_PROGRESS_ID'] = trim($row['LN_PROGRESS_ID']);
		$lnprog['PROGRESS_STATUS_NAME'] = trim($row['PROGRESS_STATUS_NAME']);

	}

	return $lnprog;
}


# ... ... ... 3.01: FetchLoanApplnConfigByProductId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnConfigByProductId_walkin($PDT_ID){
	$appln_config = array();
	$q = mysql_query("SELECT * FROM appln_configs WHERE PDT_ID='$PDT_ID' AND PDT_TYPE_ID='LOAN' AND APPLN_CONFIG_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$appln_config['RECORD_ID'] = trim($row['RECORD_ID']);
		$appln_config['APPLN_CONFIG_ID'] = trim($row['APPLN_CONFIG_ID']);
		$appln_config['APPLN_CONFIG_NAME'] = trim($row['APPLN_CONFIG_NAME']);
		$appln_config['APPLN_TYPE_ID'] = trim($row['APPLN_TYPE_ID']);
		$appln_config['PDT_ID'] = trim($row['PDT_ID']);
		$appln_config['PDT_TYPE_ID'] = trim($row['PDT_TYPE_ID']);
		$appln_config['PRM_01'] = trim($row['PRM_01']);
		$appln_config['PRM_02'] = trim($row['PRM_02']);
		$appln_config['PRM_03'] = trim($row['PRM_03']);
		$appln_config['PRM_04'] = trim($row['PRM_04']);
		$appln_config['PRM_05'] = trim($row['PRM_05']);
		$appln_config['PRM_06'] = trim($row['PRM_06']);
		$appln_config['PRM_07'] = trim($row['PRM_07']);
		$appln_config['PRM_08'] = trim($row['PRM_08']);
		$appln_config['PRM_09'] = trim($row['PRM_09']);
		$appln_config['PRM_10'] = trim($row['PRM_10']);
		$appln_config['PRM_11'] = trim($row['PRM_11']);
		$appln_config['PRM_12'] = trim($row['PRM_12']);
		$appln_config['PRM_13'] = trim($row['PRM_13']);
		$appln_config['PRM_14'] = trim($row['PRM_14']);
		$appln_config['PRM_15'] = trim($row['PRM_15']);
		$appln_config['PRM_16'] = trim($row['PRM_16']);
		$appln_config['PRM_17'] = trim($row['PRM_17']);
		$appln_config['PRM_18'] = trim($row['PRM_18']);
		$appln_config['PRM_19'] = trim($row['PRM_19']);
		$appln_config['PRM_20'] = trim($row['PRM_20']);
		$appln_config['PRM_21'] = trim($row['PRM_21']);
		$appln_config['PRM_22'] = trim($row['PRM_22']);
		$appln_config['PRM_23'] = trim($row['PRM_23']);
		$appln_config['PRM_24'] = trim($row['PRM_24']);
		$appln_config['PRM_25'] = trim($row['PRM_25']);
		$appln_config['PRM_26'] = trim($row['PRM_26']);
		$appln_config['PRM_27'] = trim($row['PRM_27']);
		$appln_config['PRM_28'] = trim($row['PRM_28']);
		$appln_config['PRM_29'] = trim($row['PRM_29']);
		$appln_config['PRM_30'] = trim($row['PRM_30']);
		$appln_config['CREATED_BY'] = trim($row['CREATED_BY']);
		$appln_config['CREATED_ON'] = trim($row['CREATED_ON']);
		$appln_config['APPLN_CONFIG_STATUS'] = trim($row['APPLN_CONFIG_STATUS']);
	}

	return $appln_config;
}

# ... ... ... 3.15: FetchLoanApplnGuarantors ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchGuarantorPool_walkin($LN_APPLN_NO){
	$g_list = array();
	$x=0;

	$q = mysql_query("SELECT * FROM loan_appln_guarantors_walkin WHERE GUARANTORSHIP_STATUS='ACTIVE' AND LN_APPLN_NO='$LN_APPLN_NO'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$g = array();
		$g['RECORD_ID'] = trim($row['RECORD_ID']);
		$g['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$g['G_CUST_CORE_ID'] = trim($row['G_CUST_CORE_ID']);
		$g['G_NAME'] = trim($row['G_NAME']);
		$g['DATE_ADDED'] = trim($row['DATE_ADDED']);
		$g['GUARANTORSHIP_STATUS'] = trim($row['GUARANTORSHIP_STATUS']);

		$g_list[$x] = $g;
		$x++;
	}
	return $g_list;
}


# ... ... ... 3.15: FetchLoanApplnGuarantors ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function CheckIfGuarantor_Exists_LoanAppln_walkin($LN_APPLN_NO, $G_CUST_CORE_ID){

	$exists = "no";
	$q = mysql_query("SELECT * FROM loan_appln_guarantors_walkin WHERE GUARANTORSHIP_STATUS='ACTIVE' AND LN_APPLN_NO='$LN_APPLN_NO' AND G_CUST_CORE_ID='$G_CUST_CORE_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$exists = "yes";
	}
	return $exists;
}

?>
