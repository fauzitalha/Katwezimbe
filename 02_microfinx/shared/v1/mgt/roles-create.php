<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Handle Button Click
if (isset($_POST['btn_ctntn'])) {
  $ROLE_NAME = trim($_POST['ROLE_NAME']);
  $ROLE_TYPE = trim($_POST['ROLE_TYPE']);

  $next_page = "roles-create-type";
  NavigateToNextPage($next_page);
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Create New Role", $APP_SMALL_LOGO); 

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
                <h2>Create New Role</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         


               <div class="col-md-8 center-margin">
                  <form class="form-horizontal form-label-left" action="roles-create-type" method="post">
                    <div class="form-group">
                      <label>Role Name</label>
                      <input type="text" class="form-control" id="ROLE_NAME" name="ROLE_NAME" placeholder="Enter Role Name" required="">
                    </div>
                    <div class="form-group">
                      <label>Select Role Type</label>
                      <select id="ROLE_TYPE" name="ROLE_TYPE" class="form-control" required="">
                        <option value="" selected="selected">-----------</option>
                        <option value="USR_MGT" selected="selected">User Role</option>
                        <option value="CST_MGT">Customer Role</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <br />
                      <button type="submit" class="btn btn-primary" name="btn_ctn">Continue</button>
                    </div>

                  </form>
                </div>



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
