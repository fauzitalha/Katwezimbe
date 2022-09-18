<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("saas/saasrouter.php");

// ... SAAS logic Here
$domain_name = trim($_SERVER['SERVER_NAME']);
ExecuteSAASRouter($domain_name);


include("conf/no-session.php");

# ... 01: Handlig the Form submission
$_SESSION['ALERT_MSG'] = "";
if (isset($_POST['btn_continue'])) {
	$digit = isset($_SESSION['digit'])? trim($_SESSION['digit']) : "";
	$captcha_code_input = trim($_POST['captcha_code_input']);

	if ($captcha_code_input!=$digit) {
		# ... Invalid captcha code
		$alert_type = "ERROR";
		$alert_msg = "INVALID CAPTCHA NUMBER.";
		$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	}
	else{
		$upr_usr = trim($_POST['upr_usr']);
		$upr_pwd = $_POST['upr_pwd'];

		# ... Core Authentication
		$response_msg = AuthenticateUserCredentials($upr_usr, $upr_pwd, $MIFOS_CONN_DETAILS);
		$CONN_FLG = $response_msg["CONN_FLG"];
		$RESP_FLG = isset($response_msg["RESP_FLG"])? $response_msg["RESP_FLG"] : "";
		$CORE_RESP = $response_msg["CORE_RESP"];

		# ... 01: Track Connection to Core
		if ($CONN_FLG=="NOT_CONNECTED") {
			# ... No connection to core
			$alert_type = "ERROR";
			$alert_msg = "NO CONNECTION TO CORE.";
			$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
		}
		else {

			# ... 02: Track Response Message  (WRONG_CREDENTIALS)
			if ($RESP_FLG=="FAIL") {
				# ... No connection to core
				$alert_type = "ERROR";
				$alert_msg = trim($CORE_RESP["defaultUserMessage"]);
				$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
			}
			else{
		    # ... 02: Check if user is allowed to access Management Portal
		    $core_user_id = $CORE_RESP["userId"];
		    $ACCESS_DETAILS = CheckUserAccessClearanceOnMgtPortal($core_user_id);
		    $ACCESS_FLG = $ACCESS_DETAILS['ACCESS_FLG'];
				$USER_STATUS = $ACCESS_DETAILS['USER_STATUS'];

				if ($ACCESS_FLG=="NO") {
					# ... Not yet added to Mgt-Portal
					$alert_type = "WARNING";
					$alert_msg = "USER ACCOUNT NOT YET ADDED TO MANAGEMENT PORTAL. CONTACT MANAGEMENT";
					$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
				} else if ($ACCESS_FLG=="YES"){

					# ... Track user status on Mgt-Portal
					if ($USER_STATUS=="PENDING") 					// ... Pending Approval
					{				
						$alert_type = "INFO";
						$alert_msg = "USER ACCOUNT PENDING VERIFICATION";
						$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
					} 
					else if($USER_STATUS=="DEACTIVATED")  					// ... Account Deactivated
					{
						$alert_type = "ERROR";
						$alert_msg = "USER ACCOUNT WAS DEACTIVATED";
						$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

					} else if($USER_STATUS=="ACTIVE") {

						# ... Customer Core User Details
						$_SESSION['core_username'] = $CORE_RESP["username"];
				    $_SESSION['core_userId'] = $CORE_RESP["userId"];
				    $_SESSION['core_base64EncodedAuthenticationKey'] = $CORE_RESP["base64EncodedAuthenticationKey"];
				    $_SESSION['core_authenticated'] = $CORE_RESP["authenticated"];
				    $_SESSION['core_officeId'] = $CORE_RESP["officeId"];
				    $_SESSION['core_officeName'] = $CORE_RESP["officeName"];
				    $_SESSION['core_roles'] = $CORE_RESP["roles"];
				    $_SESSION['core_permissions'] = $CORE_RESP["permissions"];
				    $_SESSION['core_shouldRenewPassword'] = $CORE_RESP["shouldRenewPassword"];

				    # ... Customer App User Details
				    $USER_DETAILS = GetCustDetailsFromPortal($CORE_RESP["userId"]);
						$_SESSION['UPR_RECORD_ID'] = $USER_DETAILS['RECORD_ID'];
						$_SESSION['UPR_USER_ID'] = $USER_DETAILS['USER_ID'];
						$_SESSION['UPR_USER_CORE_ID'] = $USER_DETAILS['USER_CORE_ID'];
						$_SESSION['UPR_GENDER'] = $USER_DETAILS['GENDER'];
						$_SESSION['UPR_PHONE'] = $USER_DETAILS['PHONE'];
						$_SESSION['UPR_EMAIL_ADDRESS'] = $USER_DETAILS['EMAIL_ADDRESS'];
						$_SESSION['UPR_TFA_FLG'] = $USER_DETAILS['TFA_FLG'];
						$_SESSION['UPR_LOGGED_IN'] = $USER_DETAILS['LOGGED_IN'];
						$_SESSION['UPR_USER_ROLE_DETAILS'] = $USER_DETAILS['USER_ROLE_DETAILS'];	
						$_SESSION['ORG_CODE'] = GetSystemParameter("ORGCODE");

						# ... 04: Log User Access Login
				    $USER_ID = $USER_DETAILS['USER_ID'];
				    $LOG_TYPE = "LOGIN";
				    $LOG_DATE = date("Y-m-d H:i:s", time());
				    $LOG_DETAILS = "";
				    $SRC_IP_ADDRESS = $_SERVER['REMOTE_ADDR'];	 

				    # ... 05: Check if user is logged in
				    if ( $USER_DETAILS['LOGGED_IN']=="YES" ) {
				    	$alert_type = "WARNING";
							$alert_msg = "USER ACCOUNT IS ALREADY LOGGED IN. CONTACT MANAGEMENT";
							$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
				    }
				    else {
				    	 
				    	# ... Checking for 2FA user config
				    	if ($_SESSION['UPR_TFA_FLG']=="YES") {

				    		$ENTITY_ID = $_SESSION['UPR_USER_ID'];
				    		$DEVICE_ID = GetActiveUserTFADevice($ENTITY_ID);
				    		if ($DEVICE_ID=="") {
				    			$alert_type = "WARNING";
									$alert_msg = "You do not have an active Two factor authentication device. Contact Management.";
									$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
				    		} else {
				    			$EVENT_TYPE_ID = "USR_LOGIN";
					    		$TOQEN = AES256::encrypt(GenerateRandomAccessPin(7));
					    		$is_token_gen = GenerateTFAToken($DEVICE_ID,$ENTITY_ID,$EVENT_TYPE_ID,$TOQEN);

					    		# ... 06: Navigate to Landing Page
									$_SESSION['USR_GRNTD_ACCSS'] = "TFA_LEVEL";	
							    $next_page = "usr-tfa-login";
							    NavigateToNextPage($next_page);
				    		}

				    		
				    	} else {

				    		LogUserAccessLog($USER_ID,$LOG_TYPE,$LOG_DATE,$LOG_DETAILS,$SRC_IP_ADDRESS);  
				    		FlagUserLogInStatus($USER_ID, "YES"); 

				    		# ... 06: Navigate to Landing Page
								$_SESSION['USR_GRNTD_ACCSS'] = "YES";	
						    $next_page = "main-dashboard";
						    NavigateToNextPage($next_page);
				    	}

				    	
					    				    	
				    }

				    


					}	// ... END..IFF..ELSE
				}	
			}
		}
	}
}



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php     
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations($APP_NAME, $APP_SMALL_LOGO); 
    ?>    
  </head>

  <body>

  <div class="x_panel">
    <br>
  
    <div class="x_content">
    <table align="center" width="450px">
      <tr><td align="center">

        <fieldset style="background-color: #EEE; border: solid; size: 1px; border-top-left-radius: 61px; border-bottom-right-radius: 61px;">
        	<form method="post" class="form-horizontal form-label-left input_mask">

	          <table width="90%">
	            <tr><td>&nbsp;</td></tr>
	            <tr><td align="center"><h2 style="font-size: 32px;"><?php echo $APP_NAME_MGT; ?></h2></td></tr>
	            <tr><td><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></td></tr>
	            <tr><td>
	            			<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
	            				<label class="control-label">UserName</label>
	            			</div>
	            </td></tr>
	            <tr><td>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                      <input type="text" class="form-control" id="upr_usr" name="upr_usr" required="required" />
                      <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                    </div>
	            </td></tr>
	            <tr><td>
	            			<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
	            				<label class="control-label">Password</label>
	            			</div>
                    
	            </td></tr>
	            <tr><td>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                      <input type="password" class="form-control" id="upr_pwd" name="upr_pwd" required="required" />
                      <span class="fa fa-lock form-control-feedback right" aria-hidden="true"></span>
                    </div>
	            </td></tr>
	            <tr><td>
	            			<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
	            				<label class="control-label">Captcha Number</label>
	            			</div>
	            </td></tr>
	            <tr><td>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                      <p><img id="captcha" src="captcha/captcha.php" width="160" height="45" border="1" alt="CAPTCHA">
												 <small><a href="#" onclick="document.getElementById('captcha').src = 'captcha/captcha.php?' + Math.random();
																										 document.getElementById('captcha_code_input').value = '';
																					  				 return false;
											"> <span class="fa fa-refresh fa-lg" aria-hidden="true"></span> Refresh
											</a></small></p>
											<p><input id="captcha_code_input" type="text" name="captcha_code_input" size="10" maxlength="5" required="" onkeyup="this.value = this.value.replace(/[^\d]+/g, '');"> <small>copy the digits from the image into this box</small></p>
                    </div>
	            </td></tr>
	            <tr><td>
	                    <div class="form-group">
	                      <div class="col-md-9 col-sm-9 col-xs-12">
	                        <button type="submit" class="btn btn-success" name="btn_continue">Continue</button>
	                      </div>
	                    </div>
	            </td></tr>
	            <tr><td>
	                    <div class="ln_solid"></div>
	                      <?php echo $COPY_RIGHT_STMT; ?>  
	                    </div>
	            </td></tr>
	            <tr><td></td></tr>
	          </table>

        	</form>

        <br>
        <br>
        </fieldset>
      </td></tr>
    </table>

      

    
  </div>









  </body>

  <?php
  LoadDefaultJavaScriptConfigurations();
  ?>
</html>


