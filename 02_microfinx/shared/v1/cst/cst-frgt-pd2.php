<?php
session_start();
include("conf/no-session.php");
$_SESSION['ALERT_MSG'] = "";


# ... Receving Data
$CUST_ID = $_SESSION['FP_CUST_ID'];
$displayName = $_SESSION['FP_NAME'];
$EMAIL = $_SESSION['FP_EMAIL'];
$PHONE = $_SESSION['FP_PHONE'];

# ... Send SMS
if (isset($_POST['btn_send_sms'])) {

  $c_phone = trim(mysql_real_escape_string($_POST['c_phone']));
  $temp_pwd = GeneratePassKey(10);

  if ($c_phone!=$_SESSION['FP_PHONE']) {
    $alert_type = "ERROR";
    $alert_msg = "ALERT: Phone number supplied not matching with Phone number registered. SMS will not be sent";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
  else{

    # ... DB INSERT
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "TEMP_PSWD";
    $RECIPIENT_NO = $_SESSION['FP_PHONE'];
    $SMS_MESSAGE = "This is your temporary Password: ".$temp_pwd;
    $RECORD_DATE = GetCurrentDateTime();
    $SMS_STATUS = "NN";

    $q = "INSERT INTO outbox_sms(INIT_CHANNEL, MSG_TYPE, RECIPIENT_NO, SMS_MESSAGE, RECORD_DATE, SMS_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_NO', '$SMS_MESSAGE', '$RECORD_DATE', '$SMS_STATUS')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP=="EXECUTED") {

      # ... Updating the Password
      $CUST_ID = $_SESSION['FP_CUST_ID'];
      $CUST_PWSD_STATUS = "RR";
      $CUST_PWSD = AES256::encrypt($temp_pwd);
      $CUST_PWSD_LST_CHNG_DATE = GetCurrentDateTime();

      $q2 = "UPDATE cstmrs SET CUST_PWSD_STATUS='$CUST_PWSD_STATUS', CUST_PWSD='$CUST_PWSD', CUST_PWSD_LST_CHNG_DATE='$CUST_PWSD_LST_CHNG_DATE'  WHERE CUST_ID='$CUST_ID'";
      $update_response = ExecuteEntityUpdate($q2);
      if($update_response == "EXECUTED"){

        # ... Log System Audit Log
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "CUSTOMER";
        $ENTITY_ID_AFFECTED = $_SESSION['FP_CUST_ID'];
        $EVENT = "FORGOT_PASSWORD";
        $EVENT_OPERATION = "FORGOT_PASSWORD_VIA_SMS";
        $EVENT_RELATION = "outbox_sms";
        $EVENT_RELATION_NO = $_SESSION['FP_CUST_ID'];
        $OTHER_DETAILS = $SMS_MESSAGE;
        $INVOKER_ID = $_SESSION['FP_CUST_ID'];
        LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                       $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


        $alert_type = "INFO";
        $alert_msg = "SUCCESS: Check your mobile phone. You are about to receive the temporary password. Re-directing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

        header("Refresh:5; url=cst-lgin");

      }

    }
  }
}


# ... Send Email
if (isset($_POST['btn_send_email'])) {

  $c_emm = trim(mysql_real_escape_string($_POST['c_emm']));
  $temp_pwd = GeneratePassKey(10);

  if ($c_emm!=$_SESSION['FP_EMAIL']) {
    $alert_type = "ERROR";
    $alert_msg = "ALERT: Email supplied not matching with Email registered. EMAIL will not be sent";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
  else{

    # ... DB INSERT
    $INIT_CHANNEL = "WEB";
    $MSG_TYPE = "TEMP_PSWD";
    $RECIPIENT_EMAILS = $_SESSION['FP_EMAIL'];
    $EMAIL_MESSAGE = "Dear ".$_SESSION['FP_NAME']."<br>"
                    ."This is your temporary Password: ".$temp_pwd;
    $EMAIL_ATTACHMENT_PATH = "";
    $RECORD_DATE = GetCurrentDateTime();
    $EMAIL_STATUS = "NN";

     $q = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    if ($RESP=="EXECUTED") {

      # ... Updating the Password
      $CUST_ID = $_SESSION['FP_CUST_ID'];
      $CUST_PWSD_STATUS = "RR";
      $CUST_PWSD = AES256::encrypt($temp_pwd);
      $CUST_PWSD_LST_CHNG_DATE = GetCurrentDateTime();

      $q2 = "UPDATE cstmrs SET CUST_PWSD_STATUS='$CUST_PWSD_STATUS', CUST_PWSD='$CUST_PWSD', CUST_PWSD_LST_CHNG_DATE='$CUST_PWSD_LST_CHNG_DATE'  WHERE CUST_ID='$CUST_ID'";
      $update_response = ExecuteEntityUpdate($q2);

      if($update_response == "EXECUTED"){
        # ... Log System Audit Log
        $AUDIT_DATE = GetCurrentDateTime();
        $ENTITY_TYPE = "CUSTOMER";
        $ENTITY_ID_AFFECTED = $_SESSION['FP_CUST_ID'];
        $EVENT = "FORGOT_PASSWORD";
        $EVENT_OPERATION = "FORGOT_PASSWORD_VIA_EMAIL";
        $EVENT_RELATION = "outbox_email";
        $EVENT_RELATION_NO = $_SESSION['FP_CUST_ID'];
        $OTHER_DETAILS = $EMAIL_MESSAGE;
        $INVOKER_ID = $_SESSION['FP_CUST_ID'];
        LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                       $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


        $alert_type = "SUCCESS";
        $alert_msg = "SUCCESS: Check your email inbox. A temporary password has been sent out to it. Re-directing in 5 seconds.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

        header("Refresh:5; url=cst-lgin");
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
    LoadDefaultCSSConfigurations("Forgot Password", $APP_SMALL_LOGO); 
    ?>   
    
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
          <table align="center" width="320px">
            <tr><td align="center">

              <fieldset style="background-color: #EEE; border: solid; size: 1px; border-top-left-radius: 61px; border-bottom-right-radius: 61px;">
                <form method="post" class="form-horizontal form-label-left input_mask">

                  <table width="90%">
                    <tr><td>&nbsp;</td></tr>
                    <tr><td align="center"><h2 style="font-size: 32px;"><?=$APP_NAME;?></h2></td></tr>
                    <tr><td><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></td></tr>
                    <tr><td>To recover password, select one of the options below;<br><br></td></tr>
                    <tr><td>
                          <!--
                          <form method="post" id="sssswuwuw">
                            <div class="alert alert-info" role="alert">
                              <strong><u>Recover through SMS</u></strong><br> 
                              Send temporary password to the registered number that ends with
                              <strong style="color: yellow;"><?php echo MaskPhone($PHONE, 2, "*"); ?>;</strong><br><br>

                              <label>Enter Phone (<strong style="color: yellow;"><?php echo MaskPhone($PHONE, 2, "*"); ?></strong>):</label>
                              <div class="input-group">
                                <input type="number" class="form-control" id="c_phone" name="c_phone" required="" placeholder="e.g. 0701000111">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-success" name="btn_send_sms">Send</button>
                                </span>
                              </div>
                            </div>
                          </form>
                          -->

                          <form method="post" id="sswswwww233">
                            <div class="alert alert-success" role="alert">
                              <strong><u>Recover through Email</u></strong><br> 
                              Send temporary password to the registered email that matches with;<br>
                              <strong style="color: yellow;"><?php echo MaskEmail($EMAIL); ?></strong><br><br>

                              <label>Enter Email:</label>
                              <div class="input-group">
                                <input type="email" class="form-control" id="c_emm" name="c_emm" required="" placeholder="e.g. john@gmail.com">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary" name="btn_send_email">Send</button>
                                </span>
                              </div>
                            </div>
                          </div>
                          </form>

                            

                            
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
              <br>
            </td></tr>
          </table>
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
        </div>
      </div>
      <!-- /Bottom Link -->



      <!-- Copy right Statement -->
      <div>
        
      </div>
      <!-- /Copy right Statement -->



    </div>



  </body>

  <?php
  LoadDefaultJavaScriptConfigurations();
  ?>
</html>


