<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Data Received
$RECORD_ID = mysql_real_escape_string($_GET['r']); 

# ... Get Change Details
$sys_role_chng = GetSysRoleChangeDetails($RECORD_ID);
$ROLE_ID = $sys_role_chng['ROLE_ID'];

# ... Get the Role Change Details
$ROLE_CAT_ID = "";
$ROLE_CAT_NAME = "";
$ROLE_NAME = "";
$CHNG_INIT_DATE = $sys_role_chng['CHNG_INIT_DATE'];
$CHNG_STATUS = $sys_role_chng['CHNG_STATUS'];
$ROLE_STATUS = "";
$role_details = GetRoleDetailsIgnoreStatus($ROLE_ID);
$ROLE_CAT_ID = $role_details['ROLE_CAT_ID'];
$ROLE_CAT_NAME = $role_details['ROLE_CAT_NAME'];
$ROLE_NAME = $role_details['ROLE_NAME'];

# ... Get role creators name
$CHNG_INIT_BY = $sys_role_chng['CHNG_INIT_BY'];
$CHNG_INIT_CORE_ID = GetUserCoreIdFromWebApp($CHNG_INIT_BY);
$response_msg = FetchUserDetailsFromCore($CHNG_INIT_CORE_ID, $MIFOS_CONN_DETAILS);
//$CONN_FLG = $response_msg["CONN_FLG"];
//$RESP_FLG = $response_msg["RESP_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$CHNG_CREATOR_NAME = $CORE_RESP["username"]." (".$CORE_RESP["firstname"]." ".$CORE_RESP["lastname"].")";

$ROLE_CREATION_DATE = $role_details['ROLE_CREATION_DATE'];
$ROLE_STATUS = $role_details['ROLE_STATUS'];

$FTR_TYPE = ($ROLE_CAT_ID=="RC00001")? "USR_MGT" : "CST_MGT";


# ... Approve Changes Made
if ( isset($_POST['btn_apprv_modifications']) ) {
  
  $RECORD_ID = $_POST['RECORD_ID'];
  $ROLE_ID = $_POST['ROLE_ID'];
  $FTR_TYPE = $_POST['FTR_TYPE']; 
  $CHNG_VERIF_DATE = GetCurrentDateTime();
  $CHNG_VERIF_BY = $_SESSION['UPR_USER_ID'];
  $CHNG_VERIF_RMKS = "APPROVED";
  $CHNG_STATUS = "APPROVED";

  $UPDATE_SET_SECTION = "";

  # ... Get Changes Made
  $sys_role_chng = GetSysRoleChangeDetails($RECORD_ID);

  # ... Menu Level 01
  $MENU_LEVEL_01_LIST = array();
  $MENU_LEVEL_01_LIST = GetMenuLevel_01($FTR_TYPE);
  for ($x=0; $x < sizeof($MENU_LEVEL_01_LIST); $x++) { 
    
    $MENU_LEVEL_01 = array();
    $MENU_LEVEL_01 = $MENU_LEVEL_01_LIST[$x];
    $FTR_ID = $MENU_LEVEL_01['FTR_ID'];
    $FTR_NAME = $MENU_LEVEL_01['FTR_NAME'];

    # ... Menu Level 02
    $MENU_LEVEL_02_LIST = array();
    $MENU_LEVEL_02_LIST = GetMenuLevel_02($FTR_ID);
    for ($y=0; $y < sizeof($MENU_LEVEL_02_LIST); $y++) { 
      
      $MENU_LEVEL_02 = array();
      $MENU_LEVEL_02 = $MENU_LEVEL_02_LIST[$y];
      $SUB_FTR_ID = $MENU_LEVEL_02['SUB_FTR_ID'];
      $SUB_FTR_NAME = $MENU_LEVEL_02['SUB_FTR_NAME'];

      # ... Menu Level 03
      $MENU_LEVEL_03_LIST = array();
      $MENU_LEVEL_03_LIST = GetMenuLevel_03($SUB_FTR_ID);
      for ($z=0; $z < sizeof($MENU_LEVEL_03_LIST); $z++) { 
        
        $MENU_LEVEL_03 = array();
        $MENU_LEVEL_03 = $MENU_LEVEL_03_LIST[$z];
        $BTM_FTR_ID = $MENU_LEVEL_03['BTM_FTR_ID'];
        $BTM_FTR_NAME = $MENU_LEVEL_03['BTM_FTR_NAME'];

        # ... Checking for form data
        $F_COL = $BTM_FTR_ID;
        $F_COL_VAL = $sys_role_chng["NEW_".$BTM_FTR_ID];

        $UP_SEC = $F_COL."='".$F_COL_VAL."'";
        if ($UPDATE_SET_SECTION=="") {
          $UPDATE_SET_SECTION = $UP_SEC;
        }
        else{
          $UPDATE_SET_SECTION = $UPDATE_SET_SECTION.",".$UP_SEC;
        }

      } # ... End loop 03
    }   # ... End loop 02
  } # ... End loop 01

  # ... Update Change Request
  $q2 = "UPDATE sys_roles_chng_log SET CHNG_VERIF_DATE='$CHNG_VERIF_DATE',CHNG_VERIF_BY='$CHNG_VERIF_BY',CHNG_VERIF_RMKS='$CHNG_VERIF_RMKS',CHNG_STATUS='$CHNG_STATUS' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);

  # ... Update the new roles
  $ROLE_LST_CHNG_ON = GetCurrentDateTime();
  $ROLE_LST_CHNG_BY = $_SESSION['UPR_USER_ID'];
  $q = "UPDATE sys_roles SET $UPDATE_SET_SECTION,ROLE_LST_CHNG_ON='$ROLE_LST_CHNG_ON',ROLE_LST_CHNG_BY='$ROLE_LST_CHNG_BY' WHERE ROLE_ID='$ROLE_ID'";
  $update_response = ExecuteEntityUpdate($q);
  if ($update_response=="EXECUTED") {
    $alert_type = "SUCCESS";
    $alert_msg = "Role changes has been approved successfully. Changes are now active.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }


}


# ... Reject Changes Made
if (isset($_POST['btn_rej_modifications'])) {
  
  $RECORD_ID = $_POST['RECORD_ID'];
  $ROLE_ID = $_POST['ROLE_ID'];
  $FTR_TYPE = $_POST['FTR_TYPE']; 
  $CHNG_VERIF_DATE = GetCurrentDateTime();
  $CHNG_VERIF_BY = $_SESSION['UPR_USER_ID'];
  $CHNG_VERIF_RMKS = trim($_POST['CHNG_VERIF_RMKS']);
  $CHNG_STATUS = "REJECTED";
  $q2 = "UPDATE sys_roles_chng_log SET CHNG_VERIF_DATE='$CHNG_VERIF_DATE',CHNG_VERIF_BY='$CHNG_VERIF_BY',CHNG_VERIF_RMKS='$CHNG_VERIF_RMKS',CHNG_STATUS='$CHNG_STATUS' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {
    $alert_type = "ERROR";
    $alert_msg = "Changes to the role have bee rejected.";
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
    LoadDefaultCSSConfigurations("Modify Role", $APP_SMALL_LOGO); 

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
                    <a href="roles-apprv-update" class="btn btn-dark btn-sm">Back</a>
                  </div>
                  <h2>Modify Role</h2>
                  <div class="nav navbar-right panel_toolbox">
                    <?php
                    if ($CHNG_STATUS=="PENDING" || $CHNG_STATUS=="") {
                      ?>
                      <button type="submit" class="btn btn-sm btn-success" data-toggle="modal" data-target="#apprv_role">Approve Modifications</button>
                      <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rej_role">Reject Modifications</button>

                      <?php
                    } else {
                      ?>
                      <button type="submit" class="btn btn-sm btn-success" disabled="">Approve Modifications</button>
                      <button type="button" class="btn btn-sm btn-danger" disabled="">Reject Modifications</button>
                      <?php
                    }

                    ?>
                    
                    <div class="modal fade" id="apprv_role" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">

                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel">Approve Role Modifications</h4>
                          </div>
                          <div class="modal-body">
                            <p>
                              Do you want to approve changes made to this role?<br />
                              <b>ROLE_ID:</b><br />
                              <?php echo $ROLE_ID; ?><br /><br />

                              <b>ROLE_NAME:</b><br />
                              <?php echo $ROLE_NAME; ?><br /><br />

                              <b>ROLE_CATEGORY:</b><br />
                              <?php echo $ROLE_CAT_NAME; ?><br /><br />
                             
                              <b>CHANGE INIT DATE:</b><br />
                              <?php echo $CHNG_INIT_DATE; ?><br /><br />
                              
                              <b>CHANGE INIT BY:</b><br />
                              <?php echo $CHNG_CREATOR_NAME; ?><br />

                            </p>
                          </div>
                          <div class="modal-footer">
                            <table align="right">
                              <tr>
                                <td>
                                  <form method="post">
                                      <input type="hidden" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                      <input type="hidden" name="ROLE_ID" value="<?php echo $ROLE_ID; ?>">
                                      <input type="hidden" name="FTR_TYPE" value="<?php echo $FTR_TYPE; ?>">
                                    <button type="submit" class="btn btn-success btn-sm" name="btn_apprv_modifications">Approve</button>       
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

                    <form method="post" id="dddddd">
                      <div class="modal fade" id="rej_role" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                          <div class="modal-content">

                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                              </button>
                              <h4 class="modal-title" id="myModalLabel">Reject Role Modifications</h4>
                            </div>
                            <div class="modal-body">
                              <p>
                                Do you want to reject changes made to this role?<br />
                                <b>ROLE_ID:</b><br />
                                <?php echo $ROLE_ID; ?><br /><br />

                                <b>ROLE_NAME:</b><br />
                                <?php echo $ROLE_NAME; ?><br /><br />

                                <b>ROLE_CATEGORY:</b><br />
                                <?php echo $ROLE_CAT_NAME; ?><br /><br />
                               
                                <b>CHANGE INIT DATE:</b><br />
                                <?php echo $CHNG_INIT_DATE; ?><br /><br />
                                
                                <b>CHANGE INIT BY:</b><br />
                                <?php echo $CHNG_CREATOR_NAME; ?><br /><br />

                                <b>REJECTION REASON:</b><br />
                                <textarea name="CHNG_VERIF_RMKS" id="CHNG_VERIF_RMKS" required=""></textarea><br />
                                

                              </p>
                            </div>
                            <div class="modal-footer">
                              <table align="right">
                                <tr>
                                  <td>
                                        <input type="hidden" name="RECORD_ID" value="<?php echo $RECORD_ID; ?>">
                                        <input type="hidden" name="ROLE_ID" value="<?php echo $ROLE_ID; ?>">
                                        <input type="hidden" name="FTR_TYPE" value="<?php echo $FTR_TYPE; ?>">
                                      <button type="submit" class="btn btn-danger btn-sm" name="btn_rej_modifications">Reject</button>  
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
                    </form>
                    


                  </div>
                  <div class="clearfix"></div>
                </div>

                <!-- <div class="x_content" style="overflow-y: auto; height: 490px;">-->
                <div class="x_content">    
                  <table class="table table-bordered">
                      <tr valign="top"><th bgcolor="#EEE">ROLE NAME</th><td colspan="7"><?php echo $ROLE_NAME; ?></td></tr>
                      <tr valign="top"><th bgcolor="#EEE">ROLE TYPE</th><td colspan="7"><?php echo $ROLE_CAT_NAME; ?></td></tr>
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
                        <tr valign="top"><th colspan="8" bgcolor="#EEE">SECTION <?php echo ($x+1).": ".$FTR_NAME; ?></th></tr>
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
                            <td align="left" colspan="7" bgcolor="#EEE"><?php echo $SUB_FTR_NAME; ?></td>
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
                              <th bgcolor="#EEE">Old Status</th>
                              <th bgcolor="#EEE">New Status</th>
                              <th bgcolor="#EEE">Change Status</th>
                              <th bgcolor="#EEE">Confirm</th>
                            </tr>
                          <?php
                          for ($z=0; $z < sizeof($MENU_LEVEL_03_LIST); $z++) { 
                            
                            $MENU_LEVEL_03 = array();
                            $MENU_LEVEL_03 = $MENU_LEVEL_03_LIST[$z];
                            $BTM_FTR_ID = $MENU_LEVEL_03['BTM_FTR_ID'];
                            $BTM_FTR_NAME = $MENU_LEVEL_03['BTM_FTR_NAME'];

                            # ... Get Feature Values
                            $OLD_F_VAL = $sys_role_chng["OLD_".$BTM_FTR_ID];
                            $NEW_F_VAL = $sys_role_chng["NEW_".$BTM_FTR_ID];

                            $OLD_F_DISP_VAL = ($OLD_F_VAL=="YES")? "ENABLED" : "DISABLED";
                            $NEW_F_DISP_VAL = ($NEW_F_VAL=="YES")? "ENABLED" : "DISABLED";

                            $OLD_R_BGCOLOR = ($OLD_F_DISP_VAL=="ENABLED")? "lightgreen" : "lightcoral";
                            $NEW_R_BGCOLOR = ($NEW_F_DISP_VAL=="ENABLED")? "lightgreen" : "lightcoral";

                            $CHK_BX = "NEW_".$BTM_FTR_ID;

                            # ... Change Detection
                            $CHNG_DTCT_VAL = "no change";
                            $CHNG_DTCT_BGCOLOR = "#EEEEEE";
                            if ($OLD_F_VAL!=$NEW_F_VAL) {
                              $CHNG_DTCT_VAL = "change detected";
                              $CHNG_DTCT_BGCOLOR = "lightgreen";
                              ?>
                              <tr valign="top">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php echo ($z+1); ?></td>
                                <td align="left"><?php echo $BTM_FTR_NAME; ?></td>
                                <td bgcolor="<?php echo $OLD_R_BGCOLOR; ?>"><?php echo $OLD_F_DISP_VAL; ?></td>
                                <td bgcolor="<?php echo $NEW_R_BGCOLOR; ?>"><?php echo $NEW_F_DISP_VAL; ?></td>
                                <td bgcolor="<?php echo $CHNG_DTCT_BGCOLOR; ?>"><?php echo $CHNG_DTCT_VAL; ?></td>
                                <td><input type="checkbox" name="<?php echo $CHK_BX; ?>" id="<?php echo $CHK_BX; ?>" required=""></td>
                              </tr>
                              <?php
                            }else{
                              ?>
                              <tr valign="top">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php echo ($z+1); ?></td>
                                <td align="left"><?php echo $BTM_FTR_NAME; ?></td>
                                <td><?php echo $OLD_F_DISP_VAL; ?></td>
                                <td><?php echo $NEW_F_DISP_VAL; ?></td>
                                <td><?php echo $CHNG_DTCT_VAL; ?></td>
                                <td>&nbsp;</td>
                              </tr>
                              <?php
                            }
                            
                            ?>
                            

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
