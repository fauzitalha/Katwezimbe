<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Data Received
$APPLN_CONFIG_ID = mysql_real_escape_string($_GET['k']); 

$appln_config = array();
$appln_config = FetchApplnConfigById($APPLN_CONFIG_ID);
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


# ... Reject Changes Made
if (isset($_POST['btn_delete_conf'])) {
  
  $APPLN_CONFIG_ID = $_POST['APPLN_CONFIG_ID'];
  $APPLN_CONFIG_STATUS = "DELETED";
  $q2 = "UPDATE appln_configs SET APPLN_CONFIG_STATUS='$APPLN_CONFIG_STATUS' WHERE APPLN_CONFIG_ID='$APPLN_CONFIG_ID'";

  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {

    # ... Log System Audit Log
    $AUDIT_DATE = GetCurrentDateTime();
    $ENTITY_TYPE = "APPLN_CONFIG";
    $ENTITY_ID_AFFECTED = $APPLN_CONFIG_ID;
    $EVENT = "DELETE";
    $EVENT_OPERATION = "DELETE_NEW_APPLN_CONFIG";
    $EVENT_RELATION = "appln_configs";
    $EVENT_RELATION_NO = $APPLN_CONFIG_ID;
    $OTHER_DETAILS = "";
    $INVOKER_ID = $_SESSION['UPR_USER_ID'];
    LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                   $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);

    $alert_type = "INFO";
    $alert_msg = "MESSAGE: application config parameter has been deleted.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    header("Refresh:5; url=applns-configs-view");

  }

}



?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Modify Configs", $APP_SMALL_LOGO); 

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
                <a href="applns-configs-view" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Appln Config Details</h2>
                <div class="nav navbar-right panel_toolbox">
                  <a href="applns-configs-modify-ind-details?k=<?php echo $APPLN_CONFIG_ID; ?>" class="btn btn-default btn-sm">Modify</a>
                  <button type="button" class="btn btn-sm btn-danger btn-sm" data-toggle="modal" data-target="#delete_role">Delete</button>
                  <div class="modal fade" id="delete_role" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel">Delete Appln Config</h4>
                        </div>
                        <div class="modal-body">
                          <p>
                            Do you want to delete this role?<br /><br />
                            <b>APPLN_CONFIG_ID:</b><br />
                            <?php echo $APPLN_CONFIG_ID; ?><br /><br />

                            <b>APPLN_CONFIG_NAME:</b><br />
                            <?php echo $APPLN_CONFIG_NAME; ?><br /><br />

                            <b>PRODUCT_NAME:</b><br />
                            <?php echo $C_PDT_NAME; ?><br /><br />
                           
                            <b>PRODUCT_TYPE:</b><br />
                            <?php echo $PDT_TYPE_ID; ?><br /><br />

                            <b>NOTE*</b><br />
                            Once the config is deleted, it cannot be undone.<br />
                            

                          </p>
                        </div>
                        <div class="modal-footer">
                          <table align="right">
                            <tr>
                              <td>
                                <form method="post" id="form_delete_role">
                                  <input type="hidden" name="APPLN_CONFIG_ID" value="<?php echo $APPLN_CONFIG_ID; ?>">
                                  <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_conf">Delete</button>
                                  
                                </form>
                              </td>
                              <td>
                                <button type="submit" class="btn btn-default btn-sm" data-dismiss="modal">No</button>
                              </td>
                              
                            </tr>
                          </table>                         
                        </div>

                      </div>
                    </div>
                  </div>


                </div>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <table class="table table-bordered">
                  <tr valign="top"><th bgcolor="#EEE">CONFIG NAME</th><td colspan="2"><?php echo $APPLN_CONFIG_NAME; ?></td></tr>
                  <tr valign="top"><th bgcolor="#EEE">PRODUCT NAME</th><td colspan="2"><?php echo $C_PDT_NAME; ?></td></tr>
                  <tr valign="top"><th bgcolor="#EEE">PRODUCT TYPE</th><td colspan="2"><?php echo $PDT_TYPE_ID; ?></td></tr>
                  <tr valign="top">
                    <th bgcolor="#EEE">#</th>
                    <th bgcolor="#EEE">Feature</th>
                    <th bgcolor="#EEE">Feature Value</th>
                  </tr>

                  <?php
                  $config_param_list = array();
                  $config_param_list = FetchApplnTypeMenu($APPLN_TYPE_ID);
                  for ($x=0; $x < sizeof($config_param_list); $x++) { 
                    
                    $config_param = array();
                    $config_param = $config_param_list[$x];
                    $PP_RECORD_ID = $config_param['RECORD_ID'];
                    $PP_APPLN_TYPE_ID = $config_param['APPLN_TYPE_ID'];
                    $PP_PRM_FEATURE_ID = $config_param['PRM_FEATURE_ID'];
                    $PP_PRM_FEATURE_VALUE = $config_param['PRM_FEATURE_VALUE'];
                    $PP_PRM_INPUT_TYPE = $config_param['PRM_INPUT_TYPE'];
                    $PP_PRM_STATUS = $config_param['PRM_STATUS'];

                    # ... Get Feature Values
                    $F_VAL = $appln_config[$PP_PRM_FEATURE_ID];
                    $R_BGCOLOR = "";
                    if($F_VAL=="YES"){
                      $R_BGCOLOR = "#C1F5C3";
                    } elseif ($F_VAL=="NO") {
                      $R_BGCOLOR = "#FCC8C8";
                    } else {
                      $R_BGCOLOR = "white";
                    } 

                    ?>
                    <tr valign="top">
                      <td><?php echo ($x+1); ?></td>
                      <td align="left" width="70%"><?php echo $PP_PRM_FEATURE_VALUE; ?></td>
                      <td bgcolor="<?php echo $R_BGCOLOR; ?>"><?php echo $appln_config[$PP_PRM_FEATURE_ID]; ?></td>
                    </tr>
                    <?php
                  }

                  ?>


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
