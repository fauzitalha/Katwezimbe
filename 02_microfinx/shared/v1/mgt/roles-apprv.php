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
    LoadDefaultCSSConfigurations("Approve Role", $APP_SMALL_LOGO); 

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
                <h2>Approve Created Role</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         

                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th>#</th>
                      <th>Role Name</th>
                      <th>Role Category</th>
                      <th>Created By</th>
                      <th>Date Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $ROLE_STATUS = "PENDING";
                    $sys_roles_list = GetAllUserSystemRolesWithStatus($ROLE_STATUS);
                    for ($i=0; $i < sizeof($sys_roles_list); $i++) { 
                      
                      # ... 01: Getting the Data
                      $sys_role = array();
                      $sys_role = $sys_roles_list[$i];
                      $ROLE_ID = $sys_role['ROLE_ID'];
                      $ROLE_CAT_ID = $sys_role['ROLE_CAT_ID'];
                      $ROLE_NAME = $sys_role['ROLE_NAME'];

                      $ROLE_CAT_DETAILS = GetRoleCategoryDetails($ROLE_CAT_ID);
                      $ROLE_CAT_NAME = $ROLE_CAT_DETAILS['ROLE_CAT_NAME'];

                      # ... Get role creators name
                      $ROLE_CREATOR_ID = $sys_role['ROLE_CREATOR'];
                      $ROLE_CREATOR_CORE_ID = GetUserCoreIdFromWebApp($ROLE_CREATOR_ID);
                      $response_msg = FetchUserDetailsFromCore($ROLE_CREATOR_CORE_ID, $MIFOS_CONN_DETAILS);
                      //$CONN_FLG = $response_msg["CONN_FLG"];
                      //$RESP_FLG = $response_msg["RESP_FLG"];
                      $CORE_RESP = $response_msg["CORE_RESP"];
                      $ROLE_CREATOR_NAME = $CORE_RESP["username"]." (".$CORE_RESP["firstname"]." ".$CORE_RESP["lastname"].")";

                      $ROLE_CREATION_DATE = $sys_role['ROLE_CREATION_DATE'];

                      # ... 02: Displaying the Data
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $ROLE_NAME; ?></td>
                        <td><?php echo $ROLE_CAT_NAME; ?></td>
                        <td><?php echo $ROLE_CREATOR_NAME; ?></td>
                        <td><?php echo $ROLE_CREATION_DATE; ?></td>
                        <td>
                          <a href="role-apprv-ind-details?k=<?php echo $ROLE_ID; ?>" class="btn btn-primary btn-xs">View </a>
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
