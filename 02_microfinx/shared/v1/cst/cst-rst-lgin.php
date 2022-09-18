<?php
session_start();
include("conf/no-session.php");
$_SESSION['ALERT_MSG'] = "";

# ... Receiving Information
$CST_USR_ID = $_SESSION['CST_USR_ID'];
$CST_USR_TXT = $_SESSION['CST_USR_TXT'];

# ... Button Click
if (isset($_POST['btn_continue'])) {
  
  $CUST_ID = $_SESSION['CST_USR_ID'];
  $psw = trim(mysql_real_escape_string($_POST['psw']));

  // ... STEP 01: Fetch last 5 Five Passwords
  $CUST_PASSWORDS = array();
  $CUST_PASSWORDS = FetchCustomersLastPasswords($CUST_ID);
  $pswd_is_recent = CheckIfPasswordIsRecent($psw, $CUST_PASSWORDS);

  if ( $pswd_is_recent=="YES" ) {
    $alert_type = "ERROR";
    $alert_msg = "ALERT: This password has been used recently. Enter a completely new password";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
  else
  {
    $CUST_PWSD_STATUS = "YY";
    $CUST_PWSD = AES256::encrypt($psw);
    $CUST_PWSD_LST_CHNG_DATE = GetCurrentDateTime();

    $CRED_TYPE = "PASSWORD";
    $PWSD_PIN = $CUST_PWSD;
    $DATE_OF_CHNG = GetCurrentDateTime();


    # ... INSERT SQL
    $q = "INSERT INTO cstmrs_pwsd_pin_chng_log(CUST_ID, CRED_TYPE, PWSD_PIN, DATE_OF_CHNG) VALUES('$CUST_ID', '$CRED_TYPE', '$PWSD_PIN', '$DATE_OF_CHNG')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);

    $q = "UPDATE cstmrs SET WEB_CHANNEL_LOGIN_ATTEMPTS='0', CUST_PWSD_STATUS='$CUST_PWSD_STATUS', CUST_PWSD='$CUST_PWSD', CUST_PWSD_LST_CHNG_DATE='$CUST_PWSD_LST_CHNG_DATE' WHERE CUST_ID='$CUST_ID'";
    $update_response = ExecuteEntityUpdate($q);
    if ($update_response=="EXECUTED") {

      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "CUSTOMER";
      $ENTITY_ID_AFFECTED = $CUST_ID;
      $EVENT = "PASSWORD_RESET";
      $EVENT_OPERATION = "PASSWORD_RESET_BY_CUSTOMER";
      $EVENT_RELATION = "cstmrs";
      $EVENT_RELATION_NO = $CUST_ID;
      $OTHER_DETAILS = $CUST_PWSD;
      $INVOKER_ID = $CUST_ID;
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "SUCCESS: Password has been changed. Re-directing to login page in 5 seconds.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);

      header("Refresh:5; url=cst-lgin");
    }
  } 
}



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php     
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Reset Password", $APP_SMALL_LOGO); 
    ?>   
    <style>
      /* Add a green text color and a checkmark when the requirements are right */
      .valid {
        color: green;
      }

      .valid:before {
        position: relative;
        content: "✔";
      }

      /* Add a red text color and an "x" when the requirements are wrong */
      .invalid {
        color: red;
      }

      .invalid:before {
        position: relative;
        content: "✖";
      }
    </style>

    <script type="text/javascript">
      function validate(){
        var psw = document.getElementById("psw").value;
        var psw2 = document.getElementById("psw2").value;
        var psw_ele = document.getElementById("psw2");

        if (psw==psw2) {
          return true;
        }
        else
        {
          alert("Passwords are not matching");
          return false;
        }
      }
    </script>
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
          <table align="center" width="100%">
            <tr><td align="center">

              <fieldset style="background-color: #EEE; border: solid; size: 1px; border-top-left-radius: 61px; border-bottom-right-radius: 61px;">
                
                <table>
                  <tr><td colspan="2">&nbsp;</td></tr>
                  <tr><td colspan="2" align="center"><h2 style="font-size: 32px;"><?=$APP_NAME;?></h2></td></tr>
                  <tr><td colspan="2"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></td></tr>
                  <tr><td colspan="2" align="center">PASSWORD RESET</td></tr>
                  <tr><td colspan="2" align="center"><?php echo $_SESSION['RESET_MSG']; ?></td></tr>
                  <tr><td colspan="2" align="center">&nbsp;</td></tr>
                  <tr>
                    <td>
                      <div class="col-md-6 col-sm-6 col-xs-6">
                        <form method="post" class="form-horizontal form-label-left input_mask" onsubmit="return validate(this)">
                          <table width="90%">
                            
                            <tr><td>
                                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                    <label class="control-label">UserName</label>
                                  </div>
                            </td></tr>
                            <tr><td>
                                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                    <input type="text" class="form-control" id="cst_usr" name="cst_usr" disabled="" value="<?php echo $CST_USR_TXT; ?>" />
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
                                    <input type="password" class="form-control" id="psw" name="psw" required="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" />
                                    <span class="fa fa-lock form-control-feedback right" aria-hidden="true"></span>
                                  </div>
                            </td></tr>
                            <tr><td>
                                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                    <label class="control-label">Confirm Password</label>
                                  </div>
                            </td></tr>
                            <tr><td>
                                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                    <input type="password" class="form-control" id="psw2" name="psw2" required="required" />
                                    <span class="fa fa-lock form-control-feedback right" aria-hidden="true"></span>
                                  </div>
                            </td></tr>
                            <tr><td colspan="2">
                                    <div class="form-group">
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <button type="submit" class="btn btn-success" name="btn_continue">Continue</button>
                                      </div>
                                    </div>
                            </td></tr>
                          </table>
                        </form>
                      </div>

                      <div class="col-md-6 col-sm-6 col-xs-6">
                        <p>
                          <strong>Password must contain the following:</strong>
                          <p id="letter" class="invalid">  A <b>lowercase</b> letter</p>
                          <p id="capital" class="invalid">  A <b>capital (uppercase)</b> letter</p>
                          <p id="number" class="invalid">  A <b>number</b></p>
                          <p id="length" class="invalid">  Minimum <b>8 characters</b></p>
                        </p>
                      </div>

                      <script>
                        var myInput = document.getElementById("psw");
                        var myInput2 = document.getElementById("psw2");
                        var letter = document.getElementById("letter");
                        var capital = document.getElementById("capital");
                        var number = document.getElementById("number");
                        var length = document.getElementById("length");
                        var matching = document.getElementById("matching");


                        // When the user starts to type something inside the password field
                        myInput.onkeyup = function() {
                          // Validate lowercase letters
                          var lowerCaseLetters = /[a-z]/g;
                          if(myInput.value.match(lowerCaseLetters)) {  
                            letter.classList.remove("invalid");
                            letter.classList.add("valid");
                          } else {
                            letter.classList.remove("valid");
                            letter.classList.add("invalid");
                          }
                          
                          // Validate capital letters
                          var upperCaseLetters = /[A-Z]/g;
                          if(myInput.value.match(upperCaseLetters)) {  
                            capital.classList.remove("invalid");
                            capital.classList.add("valid");
                          } else {
                            capital.classList.remove("valid");
                            capital.classList.add("invalid");
                          }

                          // Validate numbers
                          var numbers = /[0-9]/g;
                          if(myInput.value.match(numbers)) {  
                            number.classList.remove("invalid");
                            number.classList.add("valid");
                          } else {
                            number.classList.remove("valid");
                            number.classList.add("invalid");
                          }
                          
                          // Validate length
                          if(myInput.value.length >= 8) {
                            length.classList.remove("invalid");
                            length.classList.add("valid");
                          } else {
                            length.classList.remove("valid");
                            length.classList.add("invalid");
                          }

                        }
                      </script>
                    </td>
                   
                  </tr>
                  
                  <tr><td colspan="2">
                          <div class="ln_solid"></div>
                            <?php echo $COPY_RIGHT_STMT; ?>  
                          </div>
                  </td></tr>
                  <tr><td colspan="2">&nbsp;</td></tr>
                </table>
                

       
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


