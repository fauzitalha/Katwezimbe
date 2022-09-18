<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

$LN_APPLN_NO = mysql_real_escape_string(trim($_GET['k']));

# ... Proceed to signing rules
if (isset($_POST['btn_submit_request'])) {

  $LN_APPLN_NO = trim(mysql_real_escape_string($_POST['LN_APPLN_NO']));
  $LN_APPLN_PROGRESS_STATUS = "4";
  $LN_APPLN_SUBMISSION_DATE = GetCurrentDateTime(); 
  $LN_APPLN_STATUS = "NEW_SUBMISSION";

  # ... SQL
  $q2 = "UPDATE loan_applns SET LN_APPLN_PROGRESS_STATUS='$LN_APPLN_PROGRESS_STATUS', LN_APPLN_SUBMISSION_DATE='$LN_APPLN_SUBMISSION_DATE', LN_APPLN_STATUS='$LN_APPLN_STATUS' WHERE LN_APPLN_NO='$LN_APPLN_NO'";
  $update_response = ExecuteEntityUpdate($q2);

  # ... Log System Audit Log
  $AUDIT_DATE = GetCurrentDateTime();
  $ENTITY_TYPE = "LOAN_APPLN";
  $ENTITY_ID_AFFECTED = $LN_APPLN_NO;
  $EVENT = "LOAN_APPLN_SUBMISSION";
  $EVENT_OPERATION = "LOAN_APPLN_SUBMISSION";
  $EVENT_RELATION = "loan_applns";
  $EVENT_RELATION_NO = $LN_APPLN_NO;
  $OTHER_DETAILS = "";
  $INVOKER_ID = $_SESSION['CST_USR_ID'];
  LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                 $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

  $alert_type = "SUCCESS";
  $alert_msg = "MESSAGE: You have successfully submitted the loan application. Management will take it on from here. Refreshing in 5 seconds.";
  $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  header("Refresh:5; url='control-centre'");

}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("New Loan Appln", $APP_SMALL_LOGO); 

    # ... Javascript
    LoadPriorityJS();
    OnLoadExecutions();
    StartTimeoutCountdown();
    ExecuteProcessStatistics();
    ?>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">

            <div class="navbar nav_title" style="border: 0;">
              <a href="main-dashboard" class="site_title"> <span><?php echo $APP_NAME; ?></span></a>
            </div>

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <?php SideNavBar($CUST_ID); ?>
            </div>
            <!-- /sidebar menu -->


          </div>
        </div>

        <!-- top navigation -->
        <?php TopNavBar($firstname); ?>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">

          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <!-- -- -- -- -- -- -- -- -- -- -- HEADER DETAILS -- -- -- -- -- -- -- -- -- -- -- -->       
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>
            <div class="x_panel">
              <div class="x_title">
                <a href="la-res-appln" class="btn btn-dark btn-xs pull-left">Back</a>
                Loan Application Terms & Conditions (T&Cs)
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <div id="wizard" class="form_wizard wizard_horizontal">
                  <ul class="wizard_steps">
                      <li>
                        <a href="#step-1">
                          <span class="step_no" style="background-color: #1ABB9C;">1</span>
                          <span class="step_descr">
                            Step 1<br />
                            <small>Select Loan Product</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-2" >
                          <span class="step_no" style="background-color: #1ABB9C;">2</span>
                          <span class="step_descr">
                            Step 2<br />
                            <small>Review Personal Info.</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-3">
                          <span class="step_no" style="background-color: #1ABB9C;">3</span>
                          <span class="step_descr">
                              Step 3<br />
                              <small>Enter Loan Details</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-4">
                          <span class="step_no" style="background-color: #1ABB9C;">4</span>
                          <span class="step_descr">
                              Step 4<br />
                              <small>Loan Docs. & Guarantors</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-5">
                          <span class="step_no" style="background-color: #006DAE;">5</span>
                          <span class="step_descr">
                              Step 5<br />
                              <small>Terms & Conditions</small>
                          </span>
                        </a>
                      </li>
                      <li>
                        <a href="#step-6">
                          <span class="step_no" style="background-color: #D1F2F2;">6</span>
                          <span class="step_descr">
                              Step 6<br />
                              <small>Signing & Submission</small>
                          </span>
                        </a>
                      </li>
                    </ul>
                </div>

                <div style="overflow-y: auto; height: 300px;">      
                  <p align="justified">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                  </p>
                  <p align="justified">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                  </p>
                  <p align="justified">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                  </p>
                  <p align="justified">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                  </p>

                  <p align="justified">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                  </p>
                  <p align="justified">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                  </p>
                  <p align="justified">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                  </p>
                  <p align="justified">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.
                  </p>
                </div>

                

                <div class="ln_solid"></div>

                <form method="post" id="efdgwsty">
                  <input type="hidden" id="LN_APPLN_NO" name="LN_APPLN_NO" value="<?php echo $LN_APPLN_NO; ?>">
                  <input type="checkbox" id="tcs" name="tcs" required=""> I agree with the Terms & Conditions. <br>
                  <button type="submit" class="btn btn-lg btn-success" name="btn_submit_request">Submit for Processing</button>
                </form>
                
          
              </div>

            </div>
          </div>   

                  
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            <?php echo $COPY_RIGHT_STMT; ?> 
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>



    <?php LoadDefaultJavaScriptConfigurations(); ?>
  
  </body>
</html>
