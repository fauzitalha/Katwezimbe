<?php
session_start();
include("conf/no-session.php");

# ... 01: Handlig the Form submission
$_SESSION['ALERT_MSG'] = "";

# ... 02: Processing Received Data
$USER_ID = $_SESSION['UPR_USER_ID'];
$USER_CORE_ID = $_SESSION['UPR_USER_CORE_ID'];
$response_msg = FetchUserDetailsFromCore($USER_CORE_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
//$RESP_FLG = $response_msg["RESP_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$sys_usr = $response_msg["CORE_RESP"];
$CORE_username = $sys_usr["username"];
$firstname = $sys_usr["firstname"];
$lastname = $sys_usr["lastname"];

$full_name = $firstname." ".$lastname;


# ... Verify Token
if (isset($_POST['btn_continue'])) {
	
	$USER_ID = $_SESSION['UPR_USER_ID'];
	$upr_token = trim($_POST['upr_token']);
	$DEVICE_ID = GetActiveUserTFADevice($USER_ID);

	# ... Get Configured Expiry Window
	$TOKEN_EXPIRY_TIME = GetSystemParameter("TOKEN_EXPIRY");
	$is_valid = VerifyAuthToken($USER_ID, $upr_token, $TOKEN_EXPIRY_TIME); 

	# ... Processing Token results
	if ($is_valid=="INVALID") {
		$alert_type = "ERROR";
		$alert_msg = "Token is invalid.";
		$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	} else if ($is_valid=="EXPIRED") {
		$alert_type = "WARNING";
		$alert_msg = "Token is expired. Regenerate a new one.";
		$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	}
	else if ($is_valid=="VALID") {
		
		# ... Flag token as used successfully
		$q = "UPDATE tfa_tokens SET TOQEN_STATUS='USED_SUCCESSFULLY' WHERE ENTITY_ID='$USER_ID' AND TOQEN_STATUS='ACTIVE' AND DEVICE_ID='$DEVICE_ID'";
	  $update_response = ExecuteEntityUpdate($q);
	  if ($update_response=="EXECUTED") {
	  	# .. Log User Access Login
	    $LOG_TYPE = "LOGIN_TFA";
	    $LOG_DATE = date("Y-m-d H:i:s", time());
	    $LOG_DETAILS = "";
	    $SRC_IP_ADDRESS = $_SERVER['REMOTE_ADDR'];	
	    LogUserAccessLog($USER_ID,$LOG_TYPE,$LOG_DATE,$LOG_DETAILS,$SRC_IP_ADDRESS);  
			FlagUserLogInStatus($USER_ID, "YES"); 

			# ... 06: Navigate to Landing Page
			$_SESSION['USR_GRNTD_ACCSS'] = "YES";	
	    $next_page = "main-dashboard";

	    NavigateToNextPage($next_page);
	  }
		
	}
}


# ... Regenerate Token
if (isset($_POST['btn_resend_token'])) {

	$ENTITY_ID = $_SESSION['UPR_USER_ID'];
	$DEVICE_ID = GetActiveUserTFADevice($ENTITY_ID);
	$EVENT_TYPE_ID = "USR_LOGIN";
	$TOQEN = AES256::encrypt(GenerateRandomAccessPin(7));
	$is_token_gen = ReGenerateTFAToken($DEVICE_ID,$ENTITY_ID,$EVENT_TYPE_ID,$TOQEN);

	if($is_token_gen == "GENERATED"){
		$alert_type = "SUCCESS";
		$alert_msg = "New token re-generated. Check you device for token.";
		$_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	}


}






?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php     
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("TFA Login", $APP_SMALL_LOGO); 
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
	            <tr><td align="center"><h2 style="font-size: 32px;"><?=$APP_NAME;?></h2>
	            											 --- Enter Received Token ---</td></tr>
	            <tr><td><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></td></tr>
	            <tr><td>
	            			<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
	            				<label class="control-label">UserName</label>
	            			</div>
	            </td></tr>
	            <tr><td>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                      <input type="text" class="form-control" id="upr_usr" name="upr_usr" required="required" disabled="" value="<?php echo $CORE_username; ?>" />
                      <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                    </div>
	            </td></tr>
	            <tr><td>
	            			<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
	            				<label class="control-label">Full Names</label>
	            			</div>
	            </td></tr>
	            <tr><td>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                      <input type="text" class="form-control" id="upr_usr" name="upr_usr" required="required" disabled="" value="<?php echo $full_name; ?>" />
                      <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                    </div>
	            </td></tr>
	            <tr><td>
	            			<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
	            				<label class="control-label">Token / PassPhrase</label>
	            			</div>
                    
	            </td></tr>
	            <tr><td>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                      <input type="number" class="form-control" id="upr_token" name="upr_token" />
                      <span class="fa fa-lock form-control-feedback right" aria-hidden="true"></span>
                    </div>
	            </td></tr>

	            <tr><td>
	                    <div class="form-group">
	                      <div class="col-md-9 col-sm-9 col-xs-12">
	                        <button type="submit" class="btn btn-success btn-sm" name="btn_continue">Continue</button>
	                        <button type="submit" class="btn btn-primary btn-sm" name="btn_resend_token">Resend Token</button>
	                        <a href="index" class="btn btn-dark btn-sm" >Login</a>
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


