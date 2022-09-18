<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Previous Withdraw Applns", $APP_SMALL_LOGO); 

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
              <?php SideNavBar($UPR_USER_ID, $UPR_USER_ROLE_DETAILS); ?>
            </div>
            <!-- /sidebar menu -->


          </div>
        </div>

        <!-- top navigation -->
        <?php TopNavBar($UPR_USER_ID, $core_username, $core_role_name); ?>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <h2>Previous Withdraw Applns</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <form method="post" action="sw-prev-list2">
                  <label for="dob">Start Date :</label><br>
                  <select id="dob_dd" name="dd1" required="">
                    <option value="">Day</option>
                    <?php
                    for ($i=1; $i < 32; $i++) { 
                      ?>
                      <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                  <select id="dob_mm" name="mm1" required="">
                    <option value="">Month</option>
                    <?php
                    $months = array("Jan","Feb","March","April","May","June","July","Aug","Sep","Oct","Nov","Dec");
                    for ($i=0; $i < 12; $i++) { 
                      ?>
                      <option value="<?php echo ($i+1); ?>"><?php echo $months[$i]; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                  <select id="dob_yy" name="yy1" required="">
                    <option value="">Year</option>
                    <?php
                    $current_year = date("Y", time());
                    for ($i=2016; $i < ($current_year+3); $i++) { 
                      ?>
                      <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                      <?php
                    }
                    ?>
                  </select><br><br>

                  <label for="dob">End Date :</label><br>
                  <select id="dob_dd" name="dd2" required="">
                    <option value="">Day</option>
                    <?php
                    for ($i=1; $i < 32; $i++) { 
                      ?>
                      <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                  <select id="dob_mm" name="mm2" required="">
                    <option value="">Month</option>
                    <?php
                    $months = array("Jan","Feb","March","April","May","June","July","Aug","Sep","Oct","Nov","Dec");
                    for ($i=0; $i < 12; $i++) { 
                      ?>
                      <option value="<?php echo ($i+1); ?>"><?php echo $months[$i]; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                  <select id="dob_yy" name="yy2" required="">
                    <option value="">Year</option>
                    <?php
                    $current_year = date("Y", time());
                    for ($i=2016; $i < ($current_year+3); $i++) { 
                      ?>
                      <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                      <?php
                    }
                    ?>
                  </select><br><br>

                  <button type="submit" class="btn btn-primary btn-sm" name="btn_submit_appln">Submit</button>
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
