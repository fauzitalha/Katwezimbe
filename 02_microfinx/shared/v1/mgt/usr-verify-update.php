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
    LoadDefaultCSSConfigurations("Verify User Update", $APP_SMALL_LOGO); 

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
                <h2>Verify User Update</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
               
                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">Approve Created Users</th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Core UserName</th>
                      <th>Core Full Name</th>
                      <th>Info Change</th>
                      <th>Roles Change</th>
                      <th>2FA Change</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    $usr_pending_updates = array();
                    $usr_pending_updates = FetchUserChangeRequests();
                    for ($i=0; $i < sizeof($usr_pending_updates); $i++) { 
                      
                      $usr_chng = array();
                      $usr_chng = $usr_pending_updates[$i];
                      $USER_ID = $usr_chng["USER_ID"];
                      $USR_USER_CORE_ID = $usr_chng["USR_USER_CORE_ID"];
                      $cnt_q_info = $usr_chng["cnt_q_info"];
                      $cnt_q_role = $usr_chng["cnt_q_role"];
                      $cnt_q_faaa = $usr_chng["cnt_q_faaa"];

                      # ... Get Core Details
                      $response_msg = FetchUserDetailsFromCore($USR_USER_CORE_ID, $MIFOS_CONN_DETAILS);
                      $CONN_FLG = $response_msg["CONN_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $sys_usr = $response_msg["CORE_RESP"];
                      $CORE_username = $sys_usr["username"];
                      $firstname = $sys_usr["firstname"];
                      $lastname = $sys_usr["lastname"];
                      $full_name = $firstname." ".$lastname;

                      $data_transfer = $USER_ID."_".$USR_USER_CORE_ID."_".$cnt_q_info."_".$cnt_q_role."_".$cnt_q_faaa;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $CORE_username; ?></td>
                        <td><?php echo $full_name; ?></td>
                        <td><?php echo $cnt_q_info; ?></td>
                        <td><?php echo $cnt_q_role; ?></td>
                        <td><?php echo $cnt_q_faaa; ?></td>
                        <td>
                          <a href="usr-verify-update-ind-details?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
                        </td>
                      </tr>
                      <?php

                    }

                    ?>

                  </tbody>

                </table>
                


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
