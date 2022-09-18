<?php
session_start();
include("conf/no-session.php");
$_SESSION['ALERT_MSG'] = "";

# ... Trackig the activation reference
if (isset($_POST['btn_track_actvn_ref'])) {
	
  $ACTIVATION_REF = trim(mysql_real_escape_string($_POST['xyz']));

  $Ref_Count = "SELECT count(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE ACTIVATION_REF='$ACTIVATION_REF'";
	$cnt = ReturnOneEntryFromDB($Ref_Count);

	if ($cnt>0) {
		$_SESSION['ACTIVATION_REF'] = $ACTIVATION_REF;
		$next_page = "cst-track-actvn-rqst-details";
		NavigateToNextPage($next_page);
		
	} else {
		$alert_type = "ERROR";
    $alert_msg = "Invalid Activation Reference";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
	}

}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php     
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Track Request", $APP_SMALL_LOGO); 
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
          <br>
          <br>
          <!-- System Message Area -->
          <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>

          <div class="x_panel">
            <a href="cst-acct-actvn" class="btn btn-sm btn-dark pull-left">Back</a>
            <div class="x_title">
              <h2>Track Activation Request</h2>
              
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <form id="tf78" class="form-horizontal form-label-left" method="post">
                <div class="form-group">


                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Activation Reference <span class="required">*</span>
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" id="xyz" name="xyz" required="required" class="form-control col-md-7 col-xs-12">
                    <span class="fa fa-code-fork form-control-feedback right" aria-hidden="true"></span>
                  </div>
                </div>


                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <button type="submit" class="btn btn-primary" name="btn_track_actvn_ref">Inquire Status</button>
                  </div>
                </div>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
              </form>
            </div>
          </div>
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


