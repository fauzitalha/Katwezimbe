<?php
session_start();
include("conf/no-session.php");
$_SESSION['ALERT_MSG'] = "";


# ... Button Click
if (isset($_POST['btn_send_email'])) {
  
  $cst_usr = trim(mysql_real_escape_string($_POST['cst_usr']));
  $CUST_USR = md5($cst_usr);

  $Ref_Count = "SELECT count(*) as RTN_VALUE FROM cstmrs WHERE CUST_USR='$CUST_USR' AND CUST_STATUS='ACTIVE'";
  $cnt = ReturnOneEntryFromDB($Ref_Count);

  if ($cnt>0) {
    $cstmr = array();
    $cstmr = FetchCustomerLoginDataByUsr($CUST_USR);
    $CUST_ID = $cstmr['CUST_ID'];
    $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
    $CUST_EMAIL = $cstmr['CUST_EMAIL'];
    $CUST_PHONE = $cstmr['CUST_PHONE'];

    # ... Get Customer Name From Core
    $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];
    $displayName = strtoupper($CORE_RESP["displayName"]);

    # ... Decrypt Email & Phone
    $EMAIL = AES256::decrypt($CUST_EMAIL);
    $PHONE = AES256::decrypt($CUST_PHONE);

    # ... Navigate to Forgot Password Options
    $_SESSION['FP_CUST_ID'] = $CUST_ID;
    $_SESSION['FP_NAME'] = $displayName;
    $_SESSION['FP_EMAIL'] = $EMAIL;
    $_SESSION['FP_PHONE'] = $PHONE;
    $next_page = "cst-frgt-pd2";
    NavigateToNextPage($next_page);


  }
  else{
    $alert_type = "ERROR";
    $alert_msg = "ALERT: UserAccount is unknown";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
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
                    <tr><td align="center">Forgot Password</td></tr>
                    <tr><td>
                          <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                            <label class="control-label">Enter UserName</label>
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
                                <button type="submit" class="btn btn-primary" name="btn_send_email">Continue</button>
                              </div>
                            </div>
                    </td></tr>
                    <tr><td><br><br><br><br></td></tr>
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


