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


# ... Handling Form Data
if (isset($_POST['btn_save_modifications'])) {
  
  $ROLE_ID = $_POST['ROLE_ID'];
  $FTR_TYPE = $_POST['FTR_TYPE']; 
  $CHNG_INIT_DATE = GetCurrentDateTime();
  $CHNG_INIT_BY = $_SESSION['UPR_USER_ID'];

  $COLUMN_FIELDS = "ROLE_ID";
  $VALUE_FIELDS = "'".$ROLE_ID;

  $OLD_COLS = "";
  $OLD_COL_VALS = "";
  $NEW_COLS = "";
  $NEW_COL_VALS = "";


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
        $OLD_F_COL = "OLD_".$BTM_FTR_ID;
        $OLD_F_COL_VAL = $_POST["OLD_".$BTM_FTR_ID];
        $NEW_F_COL = "NEW_".$BTM_FTR_ID;
        $NEW_F_COL_VAL = $_POST["NEW_".$BTM_FTR_ID];

        if ($OLD_COLS=="") {
          $OLD_COLS = $OLD_F_COL;
          $OLD_COL_VALS = $OLD_F_COL_VAL;

          $NEW_COLS = $NEW_F_COL;
          $NEW_COL_VALS = $NEW_F_COL_VAL;
        }
        else{
          $OLD_COLS = $OLD_COLS.",".$OLD_F_COL;
          $OLD_COL_VALS = $OLD_COL_VALS."','".$OLD_F_COL_VAL;

          $NEW_COLS = $NEW_COLS.",".$NEW_F_COL;
          $NEW_COL_VALS = $NEW_COL_VALS."','".$NEW_F_COL_VAL;          
        }

      } # ... End loop 03
    }   # ... End loop 02
  } # ... End loop 01


  $COLUMN_FIELDS = $COLUMN_FIELDS.",".$OLD_COLS.",".$NEW_COLS.",CHNG_INIT_DATE,CHNG_INIT_BY";
  $VALUE_FIELDS = $VALUE_FIELDS."','".$OLD_COL_VALS."','".$NEW_COL_VALS."','".$CHNG_INIT_DATE."','".$CHNG_INIT_BY."'";

  # ... Performing Change Detect before submission
  if ( $OLD_COL_VALS==$NEW_COL_VALS ) {
    $alert_type = "WARNING";
    $alert_msg = "Role change not submitted. No change in features detected.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
  else {
    # ... Checking for Pending Roles for this Role ID
    $qs = mysql_query("SELECT * FROM sys_roles_chng_log WHERE ROLE_ID='$ROLE_ID' AND CHNG_STATUS='PENDING'") or die("ERR G: ".mysql_error());
    if (mysql_num_rows($qs)>0) {
      $alert_type = "WARNING";
      $alert_msg = "Role change not submitted. Role has modifications pending approval. Contact Approver.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
    }
    else{
      # ... Add Role Changes to the System
      $q = "INSERT INTO sys_roles_chng_log(".$COLUMN_FIELDS.") VALUES(".$VALUE_FIELDS.")";
      $exec_response = array();
      $exec_response = ExecuteEntityInsert($q);
      $RESP = $exec_response["RESP"]; 
      if ($RESP=="EXECUTED") {
        $alert_type = "INFO";
        $alert_msg = "Role change submitted. Pending Approval for changes to take effect.";
        $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
      }
    }
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

            <form method="post">
              <input type="hidden" name="ROLE_ID" value="<?php echo $ROLE_ID; ?>">
              <input type="hidden" name="FTR_TYPE" value="<?php echo $FTR_TYPE; ?>">

              <div class="x_panel">
                <div class="x_title">
                  <div class="col-md-1 col-sm-1 col-xs-12">
                    <a href="role-details?k=<?php echo $ROLE_ID; ?>" class="btn btn-dark btn-sm">Back</a>
                  </div>
                  <h2>Modify Role</h2>
                  <div class="nav navbar-right panel_toolbox">
                    <button type="submit" class="btn btn-success btn-sm" name="btn_save_modifications">Save Mofications</button>
                  </div>
                  <div class="clearfix"></div>
                </div>

                <!-- <div class="x_content" style="overflow-y: auto; height: 490px;">-->
                <div class="x_content">    
                  <table class="table table-bordered">
                      <tr valign="top"><th bgcolor="#EEE">ROLE NAME</th><td colspan="5"><?php echo $ROLE_NAME; ?></td></tr>
                      <tr valign="top"><th bgcolor="#EEE">ROLE TYPE</th><td colspan="5"><?php echo $ROLE_CAT_NAME; ?></td></tr>
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
                        <tr valign="top"><th colspan="6" bgcolor="#EEE">SECTION <?php echo ($x+1).": ".$FTR_NAME; ?></th></tr>
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
                            <td align="left" colspan="5" bgcolor="#EEE"><?php echo $SUB_FTR_NAME; ?></td>
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
                              <th bgcolor="#EEE">Current Status</th>
                              <th bgcolor="#EEE">New Status</th>
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

                            # ... Old and New Fields
                            $OLD_FIELD = "OLD_".$BTM_FTR_ID;
                            $OLD_FIELD_VAL = $F_VAL;
                            $NEW_FIELD = "NEW_".$BTM_FTR_ID;
                            ?>
                            <tr valign="top">
                              <td>&nbsp;</td>
                              <td>&nbsp;</td>
                              <td><?php echo ($z+1); ?></td>
                              <td align="left"><?php echo $BTM_FTR_NAME; ?></td>
                              <td bgcolor="<?php echo $R_BGCOLOR; ?>"><?php echo $F_DISP_VAL; ?></td>
                              <td>
                                <input type="hidden" name="<?php echo $OLD_FIELD; ?>" value="<?php echo $OLD_FIELD_VAL; ?>">

                                <?php
                                if ( $OLD_FIELD_VAL=="YES" ) {
                                  ?>
                                  <select id="<?php echo $NEW_FIELD; ?>" name="<?php echo $NEW_FIELD; ?>">
                                    <option value="NO">DISABLE</option>
                                    <option value="YES" selected="selected">ENABLE</option>
                                  </select>
                                  <?php
                                }
                                if ( $OLD_FIELD_VAL=="NO" ) {
                                  ?>
                                   <select id="<?php echo $NEW_FIELD; ?>" name="<?php echo $NEW_FIELD; ?>">
                                    <option value="NO" selected="selected">DISABLE</option>
                                    <option value="YES">ENABLE</option>
                                  </select>
                                  <?php
                                }
                                ?>
                                
                              </td>
                            </tr>

                            <?php
                          } # ... End loop 03
                        }   # ... End loop 02
                      } # ... End loop 01
                      ?>
                    </table>

                    <div class="nav navbar-left panel_toolbox">
                      <a href="role-details?k=<?php echo $ROLE_ID; ?>" class="btn btn-dark btn-sm">Back</a>
                    </div>

                    <div class="nav navbar-right panel_toolbox">
                      <button type="submit" class="btn btn-success btn-sm" name="btn_save_modifications">Save Modification</button>
                    </div>

                    <script type="text/javascript">
                      $('select').change(function(){
                        if($(this).val() === 'NO') {
                          //$(this).parent().css({
                          //  'background-color': '#fff'
                          //});

                          $(this).closest("tr").css("background-color","lightcoral")
                        }else{
                         /// $(this).parent().css({
                         //   'background-color': 'green'
                         // });

                          $(this).closest("tr").css("background-color","lightgreen")
                        }
                      })
                    </script>
                </div>

              </div>
            </form>
           
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
