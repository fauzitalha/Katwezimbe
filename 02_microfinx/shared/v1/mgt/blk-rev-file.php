<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... SEARCH FILE  ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...#
if (isset($_POST["btn_search_file"])) {
  $FILE_ID =mysql_real_escape_string(trim($_POST["FILE_ID"]));

  $file = array();
  $file = FetchBulkFileById($FILE_ID);

  if (isset($file['FILE_ID'])) {
    $_SESSION['REV_FILE_ID'] = $file['FILE_ID']; 
    $next_page = "blk-rev-file-ind";
    NavigateToNextPage($next_page);
  } else {
    $alert_type = "ERROR";
    $alert_msg = "ALERT: Unknown file id";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }

}

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Reverse File", $APP_SMALL_LOGO); 

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
                <h2>Reverse File</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <form method="post" enctype="multipart/form-data">

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                      <label>Enter File Id:</label>
                      <input type="text" id="FILE_ID" name="FILE_ID" class="form-control" required="">
                    </div>
                  </div>

   
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <button type="submit" class="btn btn-primary" name="btn_search_file">Search File</button>
                    </div>
                  </div>
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
