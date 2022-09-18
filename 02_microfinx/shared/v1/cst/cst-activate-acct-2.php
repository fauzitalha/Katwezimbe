<?php
session_start();
include("conf/no-session.php");
$_SESSION['ALERT_MSG'] = "";

# ... Receiving Information
$ACTIVATION_REF = trim($_SESSION['APPLN_REF']);

# ... Get Activation Details Information
$cstmr_actvn = array();
$cstmr_actvn = FetchActivationRequestById($ACTIVATION_REF);
$FIRST_NAME= $cstmr_actvn['FIRST_NAME'];
$MIDDLE_NAME= $cstmr_actvn['MIDDLE_NAME'];
$LAST_NAME= $cstmr_actvn['LAST_NAME'];

# ... Form
if (isset($_POST['btn_save_usr'])) {

  $ACTIVATION_REF = trim($_POST['ACTIVATION_REF']);
  $cst_usr = trim(mysql_real_escape_string($_POST['cst_usr']));

  $cstmr = array();
  $cstmr = FetchCustomerLoginDataByApplnRef($ACTIVATION_REF);
  $CUST_ID = $cstmr['CUST_ID'];
  $CUST_EMAIL = $cstmr['CUST_EMAIL'];
  $CUST_PHONE = $cstmr['CUST_PHONE'];

  $WEB_CHANNEL_ACTVN_FLG = "YY";
  $WEB_CHANNEL_ACTVN_DATE = GetCurrentDateTime();
  $CUST_USR = md5($cst_usr);
  $CUST_PWSD_STATUS = "RR";
  $CUST_PWSD = GeneratePassKey(10);
  $CUST_PWSD_ENC = AES256::encrypt($CUST_PWSD);
  $DBB_CUST_EMAIL = AES256::decrypt($CUST_EMAIL);
  $DBB_CUST_PHONE = AES256::decrypt($CUST_PHONE);

  $q = "UPDATE cstmrs SET WEB_CHANNEL_ACTVN_FLG='$WEB_CHANNEL_ACTVN_FLG', WEB_CHANNEL_ACTVN_DATE='$WEB_CHANNEL_ACTVN_DATE', CUST_USR='$CUST_USR', CUST_PWSD_STATUS='$CUST_PWSD_STATUS', CUST_PWSD='$CUST_PWSD_ENC' WHERE  APPLN_REF='$ACTIVATION_REF'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {

     # ... Sending mail ... ... ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ...  ... #
      $INIT_CHANNEL = "WEB";
      $MSG_TYPE = "e-PLATFORM temp OTP";
      $RECIPIENT_EMAILS = $DBB_CUST_EMAIL;
      $EMAIL_MESSAGE = "Dear Esteemed Client;<br>"
                      ."Your your temporary One-Time-Password is: <b>".$CUST_PWSD."</b><br>"
                      ."Regards<br>"
                      ."Management<br>"
                      ."<i></i>";
      $EMAIL_ATTACHMENT_PATH = "";
      $RECORD_DATE = GetCurrentDateTime();
      $EMAIL_STATUS = "NN";

      $qqq = "INSERT INTO outbox_email(INIT_CHANNEL, MSG_TYPE, RECIPIENT_EMAILS, EMAIL_MESSAGE, RECORD_DATE, EMAIL_STATUS) VALUES('$INIT_CHANNEL', '$MSG_TYPE', '$RECIPIENT_EMAILS', '$EMAIL_MESSAGE', '$RECORD_DATE', '$EMAIL_STATUS')";
      ExecuteEntityInsert($qqq);




    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "CUSTOMER";
    $ENTITY_ID_AFFECTED = $ACTIVATION_REF;
    $EVENT = "ACTIVATION";
    $EVENT_OPERATION = "CREATE_USERNAME_BY_CUSTOMER";
    $EVENT_RELATION = "cstmrs";
    $EVENT_RELATION_NO = $CUST_ID;
    $OTHER_DETAILS = $CUST_ID;
    $INVOKER_ID = $CUST_ID;
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


    $alert_type = "SUCCESS";
    $alert_msg = "SUCCESS: UserName has been created. A <strong><i>ONE-TIME-PASSWORD</i></strong> has been sent out to you to be used for 
    logging in.<br>
    Click <a href='cst-lgin' class='btn btn-default btn-xs'>HERE</a> to proceed.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

  }
}




?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php     
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Sign In", $APP_SMALL_LOGO); 
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
                    <input type="hidden" id="ACTIVATION_REF" name="ACTIVATION_REF" value="<?php echo $ACTIVATION_REF; ?>">
                  <table width="90%">
                    <tr><td>&nbsp;</td></tr>
                    <tr><td><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></td></tr>
                    <tr><td>
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                          <?php
                          $welcome = "Hello <b>".$FIRST_NAME."</b>,<br>
                                      You are welcome to this platform.";
                          echo $welcome;            
                          ?>
                        </div>
                    </td></tr>
                    <tr><td>
                          <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                            
                            <div class="alert alert-info" role="alert">
                              <strong><u>Point 1</u></strong><br> 
                              On this stage, you will proceed by creating your preffered username. <br>
                            </div>

                            <div class="alert alert-success" role="alert">
                              <strong><u>Point 2</u></strong><br> 
                              This username will be used to access this Electronic Platform afterwards.
                            </div>
                          </div>
                    </td></tr>
                    <tr><td>
                          <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                            <label class="control-label">Enter Preferred UserName</label>
                          </div>
                    </td></tr>
                    <tr><td>
                          <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                            <input type="text" class="form-control" id="cst_usr" name="cst_usr" required="required" />
                            <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                          </div>
                    </td></tr>


                    <tr><td>
                            <div class="form-group">
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                <button type="submit" class="btn btn-primary" name="btn_save_usr">Save</button>
                              </div>
                            </div>
                    </td></tr>
                    <tr><td>
                            <div class="ln_solid"></div>
                              <?php echo $COPY_RIGHT_STMT; ?>  
                            </div>
                    </td></tr>
                  </table>

                </form>

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


