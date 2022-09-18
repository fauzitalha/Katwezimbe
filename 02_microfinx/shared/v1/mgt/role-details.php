<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Data Received
$ROLE_ID = mysql_real_escape_string($_GET['k']); 

# ... Get the Role Details
$ROLE_CAT_ID = "";
$ROLE_CAT_NAME = "";
$ROLE_NAME = "";
$ROLE_CREATOR = "";
$ROLE_CREATION_DATE = "";
$ROLE_STATUS = "";
$role_details = GetRoleDetailsIgnoreStatus($ROLE_ID);
$ROLE_CAT_ID = $role_details['ROLE_CAT_ID'];
$ROLE_CAT_NAME = $role_details['ROLE_CAT_NAME'];
$ROLE_NAME = $role_details['ROLE_NAME'];

# ... Get role creators name
$ROLE_CREATOR_ID = $role_details['ROLE_CREATOR'];
$ROLE_CREATOR_CORE_ID = GetUserCoreIdFromWebApp($ROLE_CREATOR_ID);
$response_msg = FetchUserDetailsFromCore($ROLE_CREATOR_CORE_ID, $MIFOS_CONN_DETAILS);
//$CONN_FLG = $response_msg["CONN_FLG"];
//$RESP_FLG = $response_msg["RESP_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$ROLE_CREATOR_NAME = $CORE_RESP["username"]." (".$CORE_RESP["firstname"]." ".$CORE_RESP["lastname"].")";

$ROLE_CREATION_DATE = $role_details['ROLE_CREATION_DATE'];
$ROLE_STATUS = $role_details['ROLE_STATUS'];

$FTR_TYPE = ($ROLE_CAT_ID=="RC00001")? "USR_MGT" : "CST_MGT";


# ... Reject Changes Made
if (isset($_POST['btn_delete_role'])) {
  
  $ROLE_ID = $_POST['FM_ROLE_ID'];
  $ROLE_LST_CHNG_ON = GetCurrentDateTime();
  $ROLE_LST_CHNG_BY = $_SESSION['UPR_USER_ID'];
  $ROLE_STATUS = "DELETED";
  $q2 = "UPDATE sys_roles SET ROLE_LST_CHNG_ON='$ROLE_LST_CHNG_ON',ROLE_LST_CHNG_BY='$ROLE_LST_CHNG_BY',ROLE_STATUS='$ROLE_STATUS' WHERE ROLE_ID='$ROLE_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {
    ?>
    <script type="text/javascript">
      alert("Role has been deleted Successfully.");
    </script>
    <?php
    $next_page = "roles-view";
    NavigateToNextPage($next_page);

  }

}


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Role Details", $APP_SMALL_LOGO); 

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
                <div class="col-md-1 col-sm-1 col-xs-12">
                  <a href="roles-view" class="btn btn-dark btn-sm">Back</a>
                </div>
                <h2>Role Details</h2>
                <div class="nav navbar-right panel_toolbox">
                  <a href="roles-modify?k=<?php echo $ROLE_ID; ?>" class="btn btn-default btn-sm">Modify</a>
                  <button type="button" class="btn btn-sm btn-danger btn-sm" data-toggle="modal" data-target="#delete_role">Delete</button>
                  <div class="modal fade" id="delete_role" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">

                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel">Delete Role</h4>
                        </div>
                        <div class="modal-body">
                          <p>
                            Do you want to delete this role?<br /><br />
                            <b>ROLE_ID:</b><br />
                            <?php echo $ROLE_ID; ?><br /><br />

                            <b>ROLE_NAME:</b><br />
                            <?php echo $ROLE_NAME; ?><br /><br />

                            <b>ROLE_CATEGORY:</b><br />
                            <?php echo $ROLE_CAT_NAME; ?><br /><br />
                           
                            <b>CREATED BY:</b><br />
                            <?php echo $ROLE_CREATOR_NAME; ?><br /><br />
                            
                            <b>CREATED_ON:</b><br />
                            <?php echo $ROLE_CREATION_DATE; ?><br /><br />
                            
                            <b>ROLE_STATUS:</b><br />
                            <?php echo $ROLE_STATUS; ?><br /><br />

                            <b>NOTE*</b><br />
                            Once the role is deleted, it will affect a number of users/customers.<br />
                            

                          </p>
                        </div>
                        <div class="modal-footer">
                          <table align="right">
                            <tr>
                              <td>
                                <form method="post" id="form_delete_role">
                                  <input type="hidden" name="FM_ROLE_ID" value="<?php echo $ROLE_ID; ?>">
                                  <button type="submit" class="btn btn-danger btn-sm" name="btn_delete_role">Delete</button>
                                  
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

              <!-- <div class="x_content" style="overflow-y: auto; height: 490px;">-->
              <div class="x_content">    
                <table class="table table-bordered">
                    <tr valign="top"><th bgcolor="#EEE">ROLE NAME</th><td colspan="4"><?php echo $ROLE_NAME; ?></td></tr>
                    <tr valign="top"><th bgcolor="#EEE">ROLE TYPE</th><td colspan="4"><?php echo $ROLE_CAT_NAME; ?></td></tr>
                    <?php
                    # ... Menu Level 01
                    $MENU_LEVEL_01_LIST = array();
                    $MENU_LEVEL_01_LIST = GetMenuLevel_01($FTR_TYPE);
                    for ($x=0; $x < sizeof($MENU_LEVEL_01_LIST); $x++) { 
                      
                      $MENU_LEVEL_01 = array();
                      $MENU_LEVEL_01 = $MENU_LEVEL_01_LIST[$x];
                      $FTR_ID = $MENU_LEVEL_01['FTR_ID'];
                      $FTR_NAME = $MENU_LEVEL_01['FTR_NAME'];

                      ?>
                      <tr valign="top"><th colspan="5" bgcolor="#EEE">SECTION <?php echo ($x+1).": ".$FTR_NAME; ?></th></tr>
                      <?php
                      # ... Menu Level 02
                      $MENU_LEVEL_02_LIST = array();
                      $MENU_LEVEL_02_LIST = GetMenuLevel_02($FTR_ID);
                      for ($y=0; $y < sizeof($MENU_LEVEL_02_LIST); $y++) { 
                        
                        $MENU_LEVEL_02 = array();
                        $MENU_LEVEL_02 = $MENU_LEVEL_02_LIST[$y];
                        $SUB_FTR_ID = $MENU_LEVEL_02['SUB_FTR_ID'];
                        $SUB_FTR_NAME = $MENU_LEVEL_02['SUB_FTR_NAME'];

                        ?>
                        <tr valign="top">
                          <td>&nbsp;</td>
                          <td align="left" colspan="4" bgcolor="#EEE"><?php echo $SUB_FTR_NAME; ?></td>
                        </tr>
                        <?php
                        # ... Menu Level 03
                        $MENU_LEVEL_03_LIST = array();
                        $MENU_LEVEL_03_LIST = GetMenuLevel_03($SUB_FTR_ID);
                        ?>
                        <tr valign="top">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <th bgcolor="#EEE">#</th>
                            <th bgcolor="#EEE">Feature</th>
                            <th bgcolor="#EEE">Status</th>
                          </tr>
                        <?php
                        for ($z=0; $z < sizeof($MENU_LEVEL_03_LIST); $z++) { 
                          
                          $MENU_LEVEL_03 = array();
                          $MENU_LEVEL_03 = $MENU_LEVEL_03_LIST[$z];
                          $BTM_FTR_ID = $MENU_LEVEL_03['BTM_FTR_ID'];
                          $BTM_FTR_NAME = $MENU_LEVEL_03['BTM_FTR_NAME'];

                          # ... Get Feature Values
                          $F_VAL = $role_details[$BTM_FTR_ID];
                          $F_DISP_VAL = ($F_VAL=="YES")? "ENABLED" : "DISABLED";
                          $R_BGCOLOR = ($F_DISP_VAL=="ENABLED")? "lightgreen" : "white";
                          ?>
                          <tr valign="top" bgcolor="<?php echo $R_BGCOLOR; ?>">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><?php echo ($z+1); ?></td>
                            <td align="left"><?php echo $BTM_FTR_NAME; ?></td>
                            <td><?php echo $F_DISP_VAL; ?></td>
                          </tr>

                          <?php
                        } # ... End loop 03
                      }   # ... End loop 02
                    } # ... End loop 01
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
