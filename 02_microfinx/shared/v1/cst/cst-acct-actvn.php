<?php
session_start();
include("conf/no-session.php");


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
              <h2>Account Activations</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">

              <div class="x_content bs-example-popovers">

                  <div class="alert alert-info" role="alert">
                    <strong><u>Make Activation Request</u></strong><br> 
                    This is for Members who are new to this platform. Click the <strong>PROCEED</strong> button<br>

                    <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#seeee">PROCEED</button>
                    <div id="seeee" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content" style="color: #333;">

                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel2">Select Membership Type</h4>
                          </div>
                          <div class="modal-body">
                              <div class="alert alert-default" role="alert">
                                <strong><u>New Membership</u></strong><br>
                                Select this option if;<br> 
                                -> You have never been a member of the SACCO.<br>
                                -> You exited the SACCO but wish to re-join the SACCO.<br>
                                <a href="cst-make-actvn-rqst-new-mmbrshp" class="btn btn-sm btn-primary">New Membership</a>
                              </div>

                              <div class="alert alert-info" role="alert">
                                <strong><u>Existing Membership</u></strong><br> 
                                Select this option if;<br> 
                                -> You are current member of the SACCO.<br>
                                <a href="cst-make-actvn-rqst-exst-mmbrshp" class="btn btn-sm btn-default">Existing Membership</a>
                              </div>
                          </div>
                         

                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="alert alert-warning" role="alert">
                    <strong><u>Track Activation Request</u></strong><br> 
                    Track the stage of your activation request. Click the <strong>TRACK</strong> button<br>
                    <a href="cst-track-actvn-rqst" class="btn btn-sm btn-default">TRACK</a>
                  </div>

                  <div class="alert alert-success" role="alert">
                    <strong><u>Activate Account</u></strong><br> 
                    Enjoy this platform my activating your account. Click the <strong>ACTIVATE</strong> button<br>
                    <a href="cst-activate-acct" class="btn btn-sm btn-default">ACTIVATE</a>
                  </div>


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


