<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

# ... Receiving Details
$SVG_ID = mysql_real_escape_string(trim($_GET['k']));
$SVG_ACCT_NUM = mysql_real_escape_string(trim($_GET['l']));
$SVG_ACCT_PDT = mysql_real_escape_string(trim($_GET['m']));

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("My Accounts", $APP_SMALL_LOGO); 

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
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <a href="my-accounts" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Saving Account Statement</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         

                <form method="post" action="my-accounts-svg-stmt2">
                  <input type="hidden" id="k" name="k" value="<?php echo $SVG_ID; ?>">
                  <input type="hidden" id="l" name="l" value="<?php echo $SVG_ACCT_NUM; ?>">
                  <input type="hidden" id="m" name="m" value="<?php echo $SVG_ACCT_PDT; ?>">

                  <label>Acct Number :</label><br>
                  <input type="text" disabled="" value="<?php echo $SVG_ACCT_NUM; ?>"><br><br>
                  <label>Acct Product:</label><br>
                  <input type="text" disabled="" value="<?php echo $SVG_ACCT_PDT; ?>"><br><br>

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

                  <button type="submit" class="btn btn-primary btn-sm">Submit</button>
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
