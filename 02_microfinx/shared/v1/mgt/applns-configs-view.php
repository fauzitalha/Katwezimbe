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
    LoadDefaultCSSConfigurations("Approve Configs", $APP_SMALL_LOGO); 

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
                <h2>View Application Configs</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         
                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th>#</th>
                      <th>Config ID</th>
                      <th>Config Name</th>
                      <th>Product Name</th>                      
                      <th>Product Type</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $APPLN_CONFIG_STATUS = "ACTIVE";
                    $appln_configs_list = array();
                    $appln_configs_list = FetchApplnConfigs($APPLN_CONFIG_STATUS);
                     for ($i=0; $i < sizeof($appln_configs_list); $i++) { 
                        
                        # ... 01: Getting the Data
                        $appln_config = array();
                        $appln_config = $appln_configs_list[$i];
                        $APPLN_CONFIG_ID = $appln_config['APPLN_CONFIG_ID'];
                        $APPLN_CONFIG_NAME = $appln_config['APPLN_CONFIG_NAME'];
                        $APPLN_TYPE_ID = $appln_config['APPLN_TYPE_ID'];
                        $PDT_ID = $appln_config['PDT_ID'];
                        $PDT_TYPE_ID = $appln_config['PDT_TYPE_ID'];

                        # ... 02: Get Product Details
                        $core_pdt_details = array();
                        $core_pdt_details = GetCorePdtDetails($PDT_TYPE_ID, $PDT_ID, $MIFOS_CONN_DETAILS);
                        $C_PDT_ID = $core_pdt_details["PDT_ID"];
                        $C_PDT_NAME = $core_pdt_details["PDT_NAME"];
                        $C_PDT_SHORT = $core_pdt_details["PDT_SHORT"];

                        $data_transfer = $APPLN_CONFIG_ID;
                        ?>
                        <tr valign="top">
                          <td><?php echo ($i+1); ?>. </td>
                          <td><?php echo $APPLN_CONFIG_ID; ?></td>
                          <td><?php echo $APPLN_CONFIG_NAME; ?></td>
                          <td><?php echo $C_PDT_NAME; ?></td>
                          <td><?php echo $PDT_TYPE_ID; ?></td>
                          <td>
                            <a href="applns-configs-view-ind-details?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
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
