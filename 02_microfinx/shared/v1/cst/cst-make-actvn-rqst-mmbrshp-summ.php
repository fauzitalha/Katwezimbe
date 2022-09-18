<?php
session_start();
include("conf/no-session.php");

# ... Reeceiving Data
$SUMMARY = $_SESSION["ACTN_RQST_SUMMARY"];
$ACTIVATION_REF = $SUMMARY["ACTIVATION_REF"];
$CUST_NAME = $SUMMARY["CUST_NAME"];
$EMAIL = $SUMMARY["EMAIL"];   
$PHONE = $SUMMARY["PHONE"];

$FILE_UPLOAD_RMKS = $SUMMARY["FILE_UPLOAD_RMKS"];
$WORKID_RMKS = $FILE_UPLOAD_RMKS["WORKID_RMKS"];
$NATIONALID_RMKS = $FILE_UPLOAD_RMKS["NATIONALID_RMKS"];
$MAF_RMKS = $FILE_UPLOAD_RMKS["MAF_RMKS"];
$PP_RMKS = $FILE_UPLOAD_RMKS["PP_RMKS"];


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php     
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Summary", $APP_SMALL_LOGO); 
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
          <div class="x_panel">
            <div class="x_title">
            	<a href="cst-acct-actvn" class="btn btn-sm btn-dark pull-left">Back</a>
              <h2>Summary</h2>
            	<a href="index" class="btn btn-sm btn-primary pull-right">Continue</a>
              <button class="btn btn-default btn-sm pull-right" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">

              <div class="x_content bs-example-popovers">

                <table class="table table-striped table-bordered">
                	<tr valign="top"><th colspan="3">GENERAL DETAILS</th></tr>
                	<tr valign="top"><th width="20%">ACTIVATION_REF</th><th width="3%">:</th><td style="color: green; font-weight: bolder; font-size: 20px;"><?php echo $ACTIVATION_REF; ?></td></tr>
                	<tr valign="top"><th>CUST_NAME</th><th>:</th><td><?php echo $CUST_NAME; ?></td></tr>
                	<tr valign="top"><th>EMAIL</th><th>:</th><td><?php echo $EMAIL; ?></td></tr>
                	<tr valign="top"><th>PHONE</th><th>:</th><td><?php echo $PHONE; ?></td></tr>
                	<tr valign="top"><th colspan="3">&nbsp;</th></tr>
                	<tr valign="top"><th colspan="3">FILE UPLOAD REMARKS</th></tr>
                	<tr valign="top"><th>WORKID</th><th>:</th><td><?php echo $WORKID_RMKS; ?></td></tr>
                	<tr valign="top"><th>NATIONAL_ID</th><th>:</th><td><?php echo $NATIONALID_RMKS; ?></td></tr>
                	<tr valign="top"><th>APPLICATION FORM</th><th>:</th><td><?php echo $MAF_RMKS; ?></td></tr>
                	<tr valign="top"><th>PASSPORT PHOTO</th><th>:</th><td><?php echo $PP_RMKS; ?></td></tr>
                </table>


              </div>
     


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
        </div>
      </div>
      <!-- /Bottom Link -->


    </div>



  </body>

  <?php
  LoadDefaultJavaScriptConfigurations();
  ?>
</html>


