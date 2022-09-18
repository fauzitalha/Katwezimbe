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
    LoadDefaultCSSConfigurations("View Roles", $APP_SMALL_LOGO); 

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
                <h2>View System Roles</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         

                <div class="" role="tabpanel" data-example-id="togglable-tabs">
                  <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Management/Staff Roles</a></li>
                    <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Clients/Members Roles</a></li>
                  </ul>
                  <div id="myTabContent" class="tab-content">

                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- Management/Staff Roles -- -- -- -- -- -- -- -- -- -- -- -- -->
                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- Management/Staff Roles -- -- -- -- -- -- -- -- -- -- -- -- -->
                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- Management/Staff Roles -- -- -- -- -- -- -- -- -- -- -- -- -->
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                      <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                          <tr valign="top">
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Role Category</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $ROLE_CAT_ID = "RC00001";
                          $sys_roles_list = GetAllUserSystemRoles($ROLE_CAT_ID);
                          for ($i=0; $i < sizeof($sys_roles_list); $i++) { 
                            
                            # ... 01: Getting the Data
                            $sys_role = array();
                            $sys_role = $sys_roles_list[$i];
                            $ROLE_ID = $sys_role['ROLE_ID'];
                            $ROLE_CAT_ID = $sys_role['ROLE_CAT_ID'];
                            $ROLE_NAME = $sys_role['ROLE_NAME'];

                            $ROLE_CAT_DETAILS = GetRoleCategoryDetails($ROLE_CAT_ID);
                            $ROLE_CAT_NAME = $ROLE_CAT_DETAILS['ROLE_CAT_NAME'];

                            # ... 02: Displaying the Data
                            ?>
                            <tr valign="top">
                              <td><?php echo ($i+1); ?>. </td>
                              <td><?php echo $ROLE_NAME; ?></td>
                              <td><?php echo $ROLE_CAT_NAME; ?></td>
                              <td>
                                  <a href="role-details?k=<?php echo $ROLE_ID; ?>" class="btn btn-primary btn-xs">View</a>
                              </td>
                            </tr>
                            <?php
                          }
                          ?>

                        </tbody>

                      </table> 
                    </div>

                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- Clients/Members Roles -- -- -- -- -- -- -- -- -- -- -- -- -->
                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- Clients/Members Roles -- -- -- -- -- -- -- -- -- -- -- -- -->
                    <!-- -- -- -- -- -- -- -- -- -- -- -- -- Clients/Members Roles -- -- -- -- -- -- -- -- -- -- -- -- -->
                    <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                      <table id="datatable2" class="table table-striped table-bordered">
                        <thead>
                          <tr valign="top">
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Role Category</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $ROLE_CAT_ID = "RC00002";
                          $sys_roles_list = GetAllUserSystemRoles($ROLE_CAT_ID);
                          for ($i=0; $i < sizeof($sys_roles_list); $i++) { 
                            
                            # ... 01: Getting the Data
                            $sys_role = array();
                            $sys_role = $sys_roles_list[$i];
                            $ROLE_ID = $sys_role['ROLE_ID'];
                            $ROLE_CAT_ID = $sys_role['ROLE_CAT_ID'];
                            $ROLE_NAME = $sys_role['ROLE_NAME'];

                            $ROLE_CAT_DETAILS = GetRoleCategoryDetails($ROLE_CAT_ID);
                            $ROLE_CAT_NAME = $ROLE_CAT_DETAILS['ROLE_CAT_NAME'];

                            # ... 02: Displaying the Data
                            ?>
                            <tr valign="top">
                              <td><?php echo ($i+1); ?>. </td>
                              <td><?php echo $ROLE_NAME; ?></td>
                              <td><?php echo $ROLE_CAT_NAME; ?></td>
                              <td>
                                  <a href="role-details?k=<?php echo $ROLE_ID; ?>" class="btn btn-primary btn-xs">View</a>
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
