<?php
// ... include("../aes/AES256.php");

# ... Debugging Utilities
//echo "<pre>".print_r($user_role_details,true)."</pre>";

# **..** **..** **..** **..** **..** **..** **..** SECTION 01: Account Activation **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 01: Account Activation **..** **..** **..** **..** **..** **..** **..**  **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 01: Account Activation **..** **..** **..** **..** **..** **..** **..**  **..** 

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

# ... ... ... F2: Return one Entry from DB ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function ReturnOneEntryFromDB($DB_QUERY){
	$RTN_VALUE = "";

	$q = mysql_query($DB_QUERY) or die("ERROR 1: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {
		$RTN_VALUE = trim($row['RTN_VALUE']);
	}

	return $RTN_VALUE;
}

# ... ... ... F3: Execute Entity Insert ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function ExecuteEntityInsert($QUERY){
	$exec_response = array();
	$q = mysql_query($QUERY) or die("ERROR INS: ".mysql_error());
	if ($q) {
		$exec_response["RESP"] = "EXECUTED";
		$exec_response["RECORD_ID"] = mysql_insert_id();
	}

	return $exec_response;
}

# ... ... ... F4: Execute Entity Update ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function ExecuteEntityUpdate($QUERY){
	$update_response = "";
	$q = mysql_query($QUERY) or die("ERROR 1: ".mysql_error());
	if ($q) {
		$update_response = "EXECUTED";
	}
	return $update_response;
}

# ... ... ... F5: Execute Entity Delete ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
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

# ... ... ... F6: Perform DataChecks On Request ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ..
function PerformDataChecksOnRequest($EMAIL, $MOBILE_NO, $WORK_ID, $NATIONAL_ID){
	$data_check_results = array();
	$data_chk_res = "";

	# ... Email
	$q1 = "SELECT COUNT(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE EMAIL='$EMAIL' AND ACTIVATION_STATUS!='REJECTED'";
	$q1_cnt = ReturnOneEntryFromDB($q1);
	if ($q1_cnt>0) { 
		$data_check_results["EMAIL_CHK"] = false;  
		$data_chk_res = $data_chk_res." Email supplied is already registered to another request.<br>"; 
	} else { 
		$data_check_results["EMAIL_CHK"] = true; 
	}

	# ... Mobile Phone Number
	$q2 = "SELECT COUNT(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE MOBILE_NO='$MOBILE_NO' AND ACTIVATION_STATUS!='REJECTED'";
	$q2_cnt = ReturnOneEntryFromDB($q2);
	if ($q2_cnt>0) { 
		$data_check_results["MOBILENO_CHK"] = false; 
		$data_chk_res = $data_chk_res." Mobile Phone supplied is already registered to another request.<br>"; 
	} else { 
		$data_check_results["MOBILENO_CHK"] = true; 
	}

	# ... Work ID
	$q3 = "SELECT COUNT(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE WORK_ID='$WORK_ID' AND ACTIVATION_STATUS!='REJECTED'";
	$q3_cnt = ReturnOneEntryFromDB($q3);
	if ($q3_cnt>0) { 
		$data_check_results["WORKID_CHK"] = false; 
		$data_chk_res = $data_chk_res." WorkID/StaffID supplied is already registered to another request.<br>"; 
	} else { 
		$data_check_results["WORKID_CHK"] = true; 
	}

	# ... National ID
	$q4 = "SELECT COUNT(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE NATIONAL_ID='$NATIONAL_ID' AND ACTIVATION_STATUS!='REJECTED'";
	$q4_cnt = ReturnOneEntryFromDB($q4);
	if ($q4_cnt>0) {
		$data_check_results["NATIONALID_CHK"] = false; 
		$data_chk_res = $data_chk_res." National_ID supplied is already registered to another request.<br>"; 
	} else { 
		$data_check_results["NATIONALID_CHK"] = true; 
	}


		$data_check_results["RESULT_RMKS"] = $data_chk_res; 
	return $data_check_results;
}

# ... ... ... F7: Fetch Activation Request By Id ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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
		$cstmr_actvn['ACTIVATION_STATUS'] = trim($row['ACTIVATION_STATUS']);

	}

	return $cstmr_actvn;
}

# ... ... ... F8: Fetch Customer Login Data By ApplnRef ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustomerLoginDataByApplnRef($APPLN_REF){
	$cstmr = array();
	$q = mysql_query("SELECT * FROM cstmrs WHERE APPLN_REF='$APPLN_REF' AND CUST_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cstmr['RECORD_ID'] = trim($row['RECORD_ID']);
		$cstmr['CUST_ID'] = trim($row['CUST_ID']);
		$cstmr['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$cstmr['APPLN_REF'] = trim($row['APPLN_REF']);
		$cstmr['ACTVN_TOKEN'] = trim($row['ACTVN_TOKEN']);
		$cstmr['CUST_EMAIL'] = trim($row['CUST_EMAIL']);
		$cstmr['CUST_PHONE'] = trim($row['CUST_PHONE']);
		$cstmr['WEB_CHANNEL_ACCESS_FLG'] = trim($row['WEB_CHANNEL_ACCESS_FLG']);
		$cstmr['MOB_CHANNEL_ACCESS_FLG'] = trim($row['MOB_CHANNEL_ACCESS_FLG']);
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

# ... ... ... F9: Fetch Customer Login Data By Usr ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustomerLoginDataByUsr($CUST_USR){
	$cstmr = array();
	$q = mysql_query("SELECT * FROM cstmrs WHERE CUST_USR='$CUST_USR' AND CUST_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cstmr['RECORD_ID'] = trim($row['RECORD_ID']);
		$cstmr['CUST_ID'] = trim($row['CUST_ID']);
		$cstmr['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$cstmr['APPLN_REF'] = trim($row['APPLN_REF']);
		$cstmr['ACTVN_TOKEN'] = trim($row['ACTVN_TOKEN']);
		$cstmr['CUST_EMAIL'] = trim($row['CUST_EMAIL']);
		$cstmr['CUST_PHONE'] = trim($row['CUST_PHONE']);
		$cstmr['WEB_CHANNEL_LOGIN_ATTEMPTS'] = trim($row['WEB_CHANNEL_LOGIN_ATTEMPTS']);
		$cstmr['WEB_CHANNEL_ACCESS_FLG'] = trim($row['WEB_CHANNEL_ACCESS_FLG']);
		$cstmr['MOB_CHANNEL_LOGIN_ATTEMPTS'] = trim($row['MOB_CHANNEL_LOGIN_ATTEMPTS']);
		$cstmr['MOB_CHANNEL_ACCESS_FLG'] = trim($row['MOB_CHANNEL_ACCESS_FLG']);
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

# ... ... ... F10: Fetch Customer Login Data By CustId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustomerLoginDataByCustId($CUST_ID){
	$cstmr = array();
	$q = mysql_query("SELECT * FROM cstmrs WHERE CUST_ID='$CUST_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$cstmr['RECORD_ID'] = trim($row['RECORD_ID']);
		$cstmr['CUST_ID'] = trim($row['CUST_ID']);
		$cstmr['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$cstmr['APPLN_REF'] = trim($row['APPLN_REF']);
		$cstmr['ACTVN_TOKEN'] = trim($row['ACTVN_TOKEN']);
		$cstmr['CUST_EMAIL'] = trim($row['CUST_EMAIL']);
		$cstmr['CUST_PHONE'] = trim($row['CUST_PHONE']);
		$cstmr['WEB_CHANNEL_LOGIN_ATTEMPTS'] = trim($row['WEB_CHANNEL_LOGIN_ATTEMPTS']);
		$cstmr['WEB_CHANNEL_ACCESS_FLG'] = trim($row['WEB_CHANNEL_ACCESS_FLG']);
		$cstmr['WEB_CHANNEL_ACTVN_FLG'] = trim($row['WEB_CHANNEL_ACTVN_FLG']);
		$cstmr['WEB_CHANNEL_ACTVN_DATE'] = trim($row['WEB_CHANNEL_ACTVN_DATE']);
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

# ... ... ... F11: FETCH THE LAST FIVE PASSWORDS
function FetchCustomersLastPasswords($CUST_ID){
	$CUST_PASSWORDS = array();
	$i = 0;
	$pswd = "";

	$q = mysql_query("SELECT * FROM cstmrs_pwsd_pin_chng_log WHERE CUST_ID='$CUST_ID' AND CRED_TYPE='PASSWORD' ORDER BY	DATE_OF_CHNG DESC LIMIT 5") or  die("ERROR 1: ".mysql_error());
	while ( $row = mysql_fetch_array($q) ) {
		$pswd = $row['PWSD_PIN'];
		$CUST_PASSWORDS[$i] = AES256::decrypt($pswd);
		$i++;
	}	// ... END LOOP


	return $CUST_PASSWORDS;
}


# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Notifications Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Notifications Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Notifications Management **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 02: Notifications Management **..** **..** **..** **..** **..**  **..** **..** 

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





# **..** **..** **..** **..** **..** **..** **..** SECTION 03: Loan Application Mgt **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 03: Loan Application Mgt **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 03: Loan Application Mgt **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 03: Loan Application Mgt **..** **..** **..** **..** **..**  **..** **..** 

# ... ... ... 3.01: FetchLoanApplnConfigByProductId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... 3.02: FetchApplnTypeMenu ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... 3.03: FetchCustFinInstAccts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustFinInstAccts($CUST_ID){
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

# ... ... ... 3.04: FetchCustFinInstAcctsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustFinInstAcctsById($CUST_ID, $BANK_ACCOUNT){
	$bank_acct = array();
	$q = mysql_query("SELECT * FROM cstmrs_bank_details WHERE CUST_ID='$CUST_ID' AND BANK_ACCOUNT='$BANK_ACCOUNT'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$bank_acct['RECORD_ID'] = trim($row['RECORD_ID']);
		$bank_acct['CUST_ID'] = trim($row['CUST_ID']);
		$bank_acct['BANK_ID'] = trim($row['BANK_ID']);
		$bank_acct['BANK_ACCOUNT'] = trim($row['BANK_ACCOUNT']);
		$bank_acct['BANK_ACCOUNT_NAME'] = trim($row['BANK_ACCOUNT_NAME']);
		$bank_acct['DATE_ADDED'] = trim($row['DATE_ADDED']);
		$bank_acct['ACCT_STATUS'] = trim($row['ACCT_STATUS']);
	}
	return $bank_acct;
}

# ... ... ... 3.05: FetchCustFinInstAcctsByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustFinInstAcctsByRecordId($RECORD_ID){
	$bank_acct = array();
	$q = mysql_query("SELECT * FROM cstmrs_bank_details WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());
	while ($row = mysql_fetch_array($q)) {

		$bank_acct['RECORD_ID'] = trim($row['RECORD_ID']);
		$bank_acct['CUST_ID'] = trim($row['CUST_ID']);
		$bank_acct['BANK_ID'] = trim($row['BANK_ID']);
		$bank_acct['BANK_ACCOUNT'] = trim($row['BANK_ACCOUNT']);
		$bank_acct['DATE_ADDED'] = trim($row['DATE_ADDED']);
		$bank_acct['ACCT_STATUS'] = trim($row['ACCT_STATUS']);
	}
	return $bank_acct;
}

# ... ... ... 3.06: FetchCustBankAccts ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... 3.07: FetchFinInstitutionsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... 3.08: FetchFinInstitutionsByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchFinInstitutionsByRecordId($RECORD_ID){
	$fin = array();
	$q = mysql_query("SELECT * FROM fin_instns WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$fin['RECORD_ID'] = trim($row['RECORD_ID']);
		$fin['FIN_INST_ID'] = trim($row['FIN_INST_ID']);
		$fin['FIN_INST_NAME'] = trim($row['FIN_INST_NAME']);
		$fin['FIELD01'] = trim($row['FIELD01']);
		$fin['FIELD02'] = trim($row['FIELD02']);
		$fin['FIELD03'] = trim($row['FIELD03']);
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

# ... ... ... 3.09: FetchLoanApplnDetailsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnDetailsById($LN_APPLN_NO){
	$la = array();
	$q = mysql_query("SELECT * FROM loan_applns WHERE LN_APPLN_NO='$LN_APPLN_NO'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {
		$la['RECORD_ID'] = trim($row['RECORD_ID']);
		$la['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
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

# ... ... ... 3.10: FetchLoanApplnDetailsByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnDetailsByRecordId($RECORD_ID){
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

# ... ... ... 3.11: FetchLoanApplnsByStatus ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnsByStatus($LN_APPLN_STATUS, $CST_ID){
	$loan_appln_list = array();
	$x = 0;

	$db_query = "";
	if ($LN_APPLN_STATUS=="") {
		$db_query = "SELECT * FROM loan_applns ORDER BY RECORD_ID ASC";
	}
	elseif ($LN_APPLN_STATUS!="") {
		$db_query = "SELECT * FROM loan_applns WHERE LN_APPLN_STATUS='$LN_APPLN_STATUS' AND CUST_ID='$CST_ID' ORDER BY RECORD_ID ASC";
	}

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
function FetchLoanApplnProgress($LN_PROGRESS_ID){
	$lnprog = array();
	$q = mysql_query("SELECT * FROM loan_appln_progress WHERE LN_PROGRESS_ID='$LN_PROGRESS_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$lnprog['LN_PROGRESS_ID'] = trim($row['LN_PROGRESS_ID']);
		$lnprog['PROGRESS_STATUS_NAME'] = trim($row['PROGRESS_STATUS_NAME']);

	}

	return $lnprog;
}

# ... ... ... 3.13: FetchLoanApplnFiles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... 3.14: FetchLoanApplnFiles ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnFileByRecordId($RECORD_ID){
	$ln_file = array();

	$q = mysql_query("SELECT * FROM loan_appln_files WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$ln_file['RECORD_ID'] = trim($row['RECORD_ID']);
		$ln_file['LN_APPLN_NO'] = trim($row['LN_APPLN_NO']);
		$ln_file['F_CODE'] = trim($row['F_CODE']);
		$ln_file['F_NAME'] = trim($row['F_NAME']);
		$ln_file['DATE_UPLOADED'] = trim($row['DATE_UPLOADED']);
		$ln_file['F_STATUS'] = trim($row['F_STATUS']);
		/*

		$RECORD_ID = $ln_file['RECORD_ID'];
		$LN_APPLN_NO = $ln_file['LN_APPLN_NO'];
		$F_CODE = $ln_file['F_CODE'];
		$F_NAME = $ln_file['F_NAME'];
		$DATE_UPLOADED = $ln_file['DATE_UPLOADED'];
		$F_STATUS = $ln_file['F_STATUS'];

		*/
	}
	return $ln_file;
}

# ... ... ... 3.15: FetchLoanApplnGuarantors ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... 3.16: FetchLoanApplnGuarantorsByRecordId ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnGuarantorsByRecordId($RECORD_ID){
	$g = array();

	$q = mysql_query("SELECT * FROM loan_appln_guarantors WHERE RECORD_ID='$RECORD_ID'") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

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
		$G_NAME = $g['G_NAME'];
		$G_PHONE = $g['G_PHONE'];
		$G_EMAIL = $g['G_EMAIL'];
		$DATE_GENERATED = $g['DATE_GENERATED'];
		$GUARANTORSHIP_STATUS = $g['GUARANTORSHIP_STATUS'];
		$RMKS = $g['RMKS'];
		$USED_FLG = $g['USED_FLG'];
		$DATE_USED = $g['DATE_USED'];
		$MIFOS_RESOURCE_ID = $g['MIFOS_RESOURCE_ID'];
		*/

	}
	return $g;
}

# ... ... ... 3.15: FetchLoanApplnGuarantors ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchGuarantorPool($LN_APPLN_NO, $CURRENT_CST_ID){
	$g_list = array();
	$x=0;

	$q = mysql_query("SELECT CUST_ID,CUST_CORE_ID,CUST_EMAIL,CUST_PHONE 
		                FROM cstmrs 
		                WHERE CUST_STATUS='ACTIVE'
		                  AND CUST_ID!='$CURRENT_CST_ID'
		                  AND CUST_ID not in (SELECT G_CUST_ID FROM loan_appln_guarantors
		                                      WHERE LN_APPLN_NO='$LN_APPLN_NO'
		                                        AND GUARANTORSHIP_STATUS in ('PENDING', 'APPROVED')
		                                     )") or die("ERR_UPR_LOG: ".mysql_error());

	while ($row = mysql_fetch_array($q)) {

		$g = array();
		$g['CUST_ID'] = trim($row['CUST_ID']);
		$g['CUST_CORE_ID'] = trim($row['CUST_CORE_ID']);
		$g['CUST_EMAIL'] = trim($row['CUST_EMAIL']);
		$g['CUST_PHONE'] = trim($row['CUST_PHONE']);

		/*
		$CUST_ID = $g['CUST_ID'];
		$CUST_CORE_ID = $g['CUST_CORE_ID'];
		$CUST_EMAIL = $g['CUST_EMAIL'];
		$CUST_PHONE = $g['CUST_PHONE'];*/

		$g_list[$x] = $g;
		$x++;
	}
	return $g_list;
}

# ... ... ... 3.15: FetchLoanApplnGuarantors ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchGrrtrshipRequests($G_CUST_ID){
	$g_list = array();
	$x=0;

	$q = mysql_query("SELECT * FROM loan_appln_guarantors WHERE G_CUST_ID='$G_CUST_ID' AND GUARANTORSHIP_STATUS='PENDING'") or die("ERR_UPR_LOG: ".mysql_error());

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

		$g_list[$x] = $g;
		$x++;
	}
	return $g_list;
}

# ... ... ... 3.16: FetchPendingLoanApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchPendingLoanApplns($CUST_ID){
	$loan_appln_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM loan_applns WHERE CUST_ID='$CUST_ID' AND LN_APPLN_STATUS in ('PENDING','RETURNED_TO_CUSTOMER') ORDER BY RECORD_ID ASC") or die("ERR_UPR_LOG: ".mysql_error());
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

# ... ... ... 3.16: FetchLoanApplnsByStatus ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchPrevoiusLoanApplns($CUST_ID, $START_DATE, $END_DATE){
	$loan_appln_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM loan_applns 
		                WHERE CUST_ID='$CUST_ID' 
 											AND LN_APPLN_SUBMISSION_DATE>='$START_DATE' AND LN_APPLN_SUBMISSION_DATE<'$END_DATE'
 										ORDER BY RECORD_ID ASC") or die("ERR_UPR_LOG: ".mysql_error());
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

# ... ... ... 3.17: FetchLoanApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchLoanApplnsById($LN_APPLN_NO){
	$la = array();

	$q = mysql_query("SELECT * FROM loan_applns WHERE LN_APPLN_NO='$LN_APPLN_NO'") or die("ERR_UPR_LOG: ".mysql_error());

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

# ... ... ... 3.18 GetCustBankFromBankAcct ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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


# **..** **..** **..** **..** **..** **..** **..** SECTION 04: Savings Application Mgt **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 04: Savings Application Mgt **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 04: Savings Application Mgt **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 04: Savings Application Mgt **..** **..** **..** **..** **..**  **..** **..** 

# ... ... ... 4.01: FetchActiveTAN ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... 4.02: ValidateTranTAN ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... 4.03: FetchSavingsWithdrawApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustPendingSavingsWithdrawAppln($CUST_ID){
	$sw_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM svgs_withdraw_requests WHERE CUST_ID='$CUST_ID' AND SVGS_APPLN_STATUS='PENDING'") or die("ERR_UPR_LOG: ".mysql_error());

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

# ... ... ... 4.04: FetchSavingsWithdrawApplnById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchSavingsWithdrawApplnById($WITHDRAW_REF){
	$sw = array();
	$q = mysql_query("SELECT * FROM svgs_withdraw_requests WHERE WITHDRAW_REF='$WITHDRAW_REF'") or die("ERR_UPR_LOG: ".mysql_error());

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

# ... ... ... 4.05: FetchCustPendingSavingsDepositApplns ... ...... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustPendingSavingsDepositApplns($CUST_ID){
	$sd_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM svgs_deposit_requests WHERE CUST_ID='$CUST_ID' AND RQST_STATUS='PENDING'") or die("ERR_UPR_LOG: ".mysql_error());

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

# ... ... ... 4.06: FetchSavingsDepositApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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

# ... ... ... 4.07: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustPendingSavingsTransferApplns($CUST_ID){
	$st_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM svgs_transfer_requests WHERE CUST_ID='$CUST_ID' AND TRANSFER_APPLN_STATUS='PENDING'") or die("ERR_UPR_LOG: ".mysql_error());

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

# ... ... ... 4.08: FetchSavingsTransferApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
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


# ... ... ... 4.03: FetchSavingsWithdrawApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustPrevSavingsWithdrawAppln($CUST_ID, $START_DATE, $END_DATE){
	$sw_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM svgs_withdraw_requests 
		                WHERE CUST_ID='$CUST_ID' 
		                  AND APPLN_SUBMISSION_DATE>='$START_DATE' AND APPLN_SUBMISSION_DATE<'$END_DATE'
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

# ... ... ... 4.05: FetchCustPendingSavingsDepositApplns ... ...... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustPrevoiusSavingsDepositApplns($CUST_ID, $START_DATE, $END_DATE){
	$sd_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM svgs_deposit_requests 
		                WHERE CUST_ID='$CUST_ID'
		                  AND RQST_DATE>='$START_DATE' AND RQST_DATE<'$END_DATE'
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

# ... ... ... 4.07: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustPreviousSavingsTransferApplns($CUST_ID, $START_DATE, $END_DATE){
	$st_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM svgs_transfer_requests 
		                WHERE CUST_ID='$CUST_ID' 
		                  AND APPLN_SUBMISSION_DATE>='$START_DATE' AND APPLN_SUBMISSION_DATE<'$END_DATE'
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



# **..** **..** **..** **..** **..** **..** **..** SECTION 05: Shares Application Mgt **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 05: Shares Application Mgt **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 05: Shares Application Mgt **..** **..** **..** **..** **..**  **..** **..** 
# **..** **..** **..** **..** **..** **..** **..** SECTION 05: Shares Application Mgt **..** **..** **..** **..** **..**  **..** **..** 

# ... ... ... 5.01: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustPendingShareRequestApplns($CUST_ID){
	$shr_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM shares_appln_requests WHERE CUST_ID='$CUST_ID' AND SHARES_APPLN_STATUS='PENDING'") or die("ERR_UPR_LOG: ".mysql_error());

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

# ... ... ... 5.02: FetchShareRequestApplnsById ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchShareRequestApplnsById($SHARES_APPLN_REF){
	$shr = array();

	$q = mysql_query("SELECT * FROM shares_appln_requests WHERE SHARES_APPLN_REF='$SHARES_APPLN_REF'") or die("ERR_UPR_LOG: ".mysql_error());

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

# ... ... ... 5.03: FetchSavingsTransferApplns ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function FetchCustPreviousShareRequestApplns($CUST_ID, $START_DATE, $END_DATE){
	$shr_list = array();
	$x = 0;

	$q = mysql_query("SELECT * FROM shares_appln_requests 
		                WHERE CUST_ID='$CUST_ID' 
		                  AND APPLN_SUBMISSION_DATE>='$START_DATE' AND APPLN_SUBMISSION_DATE<'$END_DATE'
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


?>

