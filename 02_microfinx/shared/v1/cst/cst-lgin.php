<?php
session_start();

include("saas/saasrouter.php");

// ... SAAS logic Here
$domain_name = trim($_SERVER['SERVER_NAME']);
ExecuteSAASRouter($domain_name);



include("conf/no-session.php");
$_SESSION['ALERT_MSG'] = "";



# ... Button Click
if (isset($_POST['btn_continue'])) {
  
  $cst_usr = trim(mysql_real_escape_string($_POST['cst_usr']));
  $cst_pwd = trim(mysql_real_escape_string($_POST['cst_pwd']));
  $CUST_USR = md5($cst_usr);

  $Ref_Count = "SELECT count(*) as RTN_VALUE FROM cstmrs WHERE CUST_USR='$CUST_USR' AND CUST_STATUS='ACTIVE'";
  $cnt = ReturnOneEntryFromDB($Ref_Count);

  if ($cnt>0) {
    $cstmr = array();
    $cstmr = FetchCustomerLoginDataByUsr($CUST_USR);
    $CUST_ID = $cstmr['CUST_ID'];
    $CUST_CORE_ID = $cstmr['CUST_CORE_ID'];
    $DB_CUST_PWSD = AES256::decrypt($cstmr['CUST_PWSD']);
    $WEB_FLG = $cstmr['WEB_CHANNEL_ACCESS_FLG'];
    $DB_CNT_ATTEMPTS = $cstmr['WEB_CHANNEL_LOGIN_ATTEMPTS'];
    $CNT_ATTEMPTS = ($DB_CNT_ATTEMPTS=="")? 0 : $DB_CNT_ATTEMPTS;
    $WEB_ACTVN = $cstmr['WEB_CHANNEL_ACCESS_FLG'];
    $PWD_FLG = $cstmr['CUST_PWSD_STATUS'];
    $CUST_PWSD_LST_CHNG_DATE = $cstmr['CUST_PWSD_LST_CHNG_DATE'];

    # ... 00: CHECK ATTEMPTS
    if ($CNT_ATTEMPTS>2) {

      $qq = "UPDATE cstmrs SET WEB_CHANNEL_ACCESS_FLG='NN' WHERE CUST_ID='$CUST_ID'";
      $update_responseqq = ExecuteEntityUpdate($qq);
      if ($update_responseqq=="EXECUTED") {
        $alert_type = "ERROR";
        $alert_msg = "ALERT: This account has been locked out due to multiple failed log on attempts. Contact Support";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      }        
    }
    else{
      # ... 01: CHECK PASSWORD
      if ($DB_CUST_PWSD!=$cst_pwd) {

        # ... Increment Login Attempts
        $CNT_ATTEMPTS = ($CNT_ATTEMPTS + 1);
        $qq = "UPDATE cstmrs SET WEB_CHANNEL_LOGIN_ATTEMPTS='$CNT_ATTEMPTS' WHERE CUST_ID='$CUST_ID'";
        $update_responseqq = ExecuteEntityUpdate($qq);
        if ($update_responseqq=="EXECUTED") {
          $alert_type = "ERROR";
          $alert_msg = "ALERT: Wrong UserName or Password Provided";
          $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
        } 
      }
      else if ($DB_CUST_PWSD==$cst_pwd) {

        # ... 02: ACCOUNT ACCESS VIA WEB
        if ( ($WEB_FLG=="NN")||($WEB_FLG=="") ) {
          $alert_type = "WARNING";
          $alert_msg = "INFO: you are not authorized to access this account via <b>WEB</b>.";
          $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
        }
        else
        {
          # ... 03: CHECK PASSWORD RESET STATUS
          if ($PWD_FLG=="RR") {
            $_SESSION['CST_USR_ID'] = $CUST_ID;
            $_SESSION['CST_USR_TXT'] = $cst_usr;
            $_SESSION['RESET_MSG'] = "Your password was reset. Please change it.";
            $next_page = "cst-rst-lgin";
            NavigateToNextPage($next_page);
          }
          else{

            # ... 04: CHECK FOR PASSWORD EXPIRY
            $CURRENT_DATE = GetCurrentDateTime();
            $is_pswd_expired =  CheckForPasswordExpiry($CURRENT_DATE, $CUST_PWSD_LST_CHNG_DATE);
            if ( $is_pswd_expired=="YES" ) {
              $_SESSION['CST_USR_ID'] = $CUST_ID;
              $_SESSION['CST_USR_TXT'] = $cst_usr;
              $_SESSION['RESET_MSG'] = "Your password is expired. Please change it.";
              $next_page = "cst-rst-lgin";
              NavigateToNextPage($next_page);
            }
            else {

              # ... Log Accesd Log
              $CHANNEL_ID = "WEB";
              $LOG_DETAILS = "SIGN_IN";
              $LOG_DATE = GetCurrentDateTime(); 
              $SRC_IP_ADDRESS = GetClientIp();

              $q = "INSERT INTO cstmrs_lgn_log(CUST_ID, CHANNEL_ID, LOG_DETAILS, LOG_DATE, SRC_IP_ADDRESS) VALUES('$CUST_ID', '$CHANNEL_ID', '$LOG_DETAILS', '$LOG_DATE', '$SRC_IP_ADDRESS')";
              $exec_response = array();
              $exec_response = ExecuteEntityInsert($q);

              # ... Reset Login Attempts to 0
              $qq = "UPDATE cstmrs SET WEB_CHANNEL_LOGIN_ATTEMPTS='' WHERE CUST_ID='$CUST_ID'";
              $update_responseqq = ExecuteEntityUpdate($qq);
              
              # ... GetCoreDetails
              $response_msg = FetchCustomerDetailsFromCore($CUST_CORE_ID, $MIFOS_CONN_DETAILS);
              $CONN_FLG = $response_msg["CONN_FLG"];
              $CORE_RESP = $response_msg["CORE_RESP"];

              $_SESSION['CST_USR_ID'] = $CUST_ID;
              $_SESSION['CUST_CORE_ID'] = $CUST_CORE_ID;
              $_SESSION['accountNo'] = $CORE_RESP["accountNo"];
              $_SESSION['externalId'] = $CORE_RESP["externalId"];
              $_SESSION['firstname'] = $CORE_RESP["firstname"];
              $_SESSION['lastname'] = $CORE_RESP["lastname"];
              $_SESSION['displayName'] = $CORE_RESP["displayName"];
              $_SESSION['ORG_CODE'] = GetSystemParameter("ORGCODE");

              $next_page = "control-centre";
              NavigateToNextPage($next_page);
            }


            
          }

        }
      }
    }

        
  } else {
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

                  <table width="90%">
                    <tr><td>&nbsp;</td></tr>
                    <tr><td align="center"><h2 style="font-size: 32px;"><?=$APP_NAME;?></h2></td></tr>
                    <tr><td><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></td></tr>
                    <tr><td>
                          <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                            <label class="control-label">UserName</label>
                          </div>
                    </td></tr>
                    <tr><td>
                          <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                            <input type="text" class="form-control" id="cst_usr" name="cst_usr" required="required" />
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
                            <input type="password" class="form-control" id="cst_pwd" name="cst_pwd" required="required" />
                            <span class="fa fa-lock form-control-feedback right" aria-hidden="true"></span>
                          </div>
                    </td></tr>
                    <tr><td>
                            <div class="form-group">
                              <div class="col-md-9 col-sm-9 col-xs-12">
                                <button type="submit" class="btn btn-success" name="btn_continue">Sign In</button>
                              </div>
                            </div>
                    </td></tr>
                    <tr><td>
                            <div class="form-group">
                               <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                  <a href="cst-frgt-pd" style="text-decoration: underline; font-weight: bold; color: red;">Forgot Password</a><br>
                                  <a href="cst-acct-actvn" style="text-decoration: underline; font-weight: bold; color: green;">Request Account Activation from here</a>
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


