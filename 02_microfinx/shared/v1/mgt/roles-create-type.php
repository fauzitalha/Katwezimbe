<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Data
$ROLE_NAME = mysql_real_escape_string($_POST['ROLE_NAME']); 
$ROLE_TYPE = mysql_real_escape_string($_POST['ROLE_TYPE']); 

# ... Handling Form Data
if (isset($_POST['btn_save_role'])) {

  $ROLE_NAME = $_POST['ROLE_NAME']; 
  $ROLE_TYPE = $_POST['ROLE_TYPE']; 
  $FTR_TYPE = $_POST['FTR_TYPE']; 

  $ROLE_ID;
  $ROLE_CAT_ID = ($ROLE_TYPE=="USR_MGT")? "RC00001" : "RC00002";
  $ROLE_CREATOR = $_SESSION['UPR_USER_ID'];
  $ROLE_CREATION_DATE = GetCurrentDateTime();

  $COLUMN_FIELDS = "ROLE_CAT_ID,ROLE_NAME";
  $VALUE_FIELDS = "'".$ROLE_CAT_ID."','".$ROLE_NAME;

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
        $F_COL_VAL = $_POST[$BTM_FTR_ID];

        $COLUMN_FIELDS = $COLUMN_FIELDS.",".$F_COL;
        $VALUE_FIELDS = $VALUE_FIELDS."','".$F_COL_VAL;
      } # ... End loop 03
    }   # ... End loop 02
  } # ... End loop 01


  $COLUMN_FIELDS = $COLUMN_FIELDS.",ROLE_CREATOR,ROLE_CREATION_DATE";
  $VALUE_FIELDS = $VALUE_FIELDS."','".$ROLE_CREATOR."','".$ROLE_CREATION_DATE."'";

  # ... Add Role Details to the System
  $q = "INSERT INTO sys_roles(".$COLUMN_FIELDS.") VALUES(".$VALUE_FIELDS.")";
  $exec_response = array();
  $exec_response = ExecuteEntityInsert($q);
  $RESP = $exec_response["RESP"]; 
  $RECORD_ID = $exec_response["RECORD_ID"];

  # ... Process Entity System ID (Role ID)
  $id_prefix = "L";
  $id_len = 7;
  $id_record_id = $RECORD_ID;
  $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
  $ROLE_ID = $ENTITY_ID;


  # ... Updating the role id
  $q2 = "UPDATE sys_roles SET ROLE_ID='$ROLE_ID' WHERE RECORD_ID='$RECORD_ID'";
  $update_response = ExecuteEntityUpdate($q2);
  if ($update_response=="EXECUTED") {
    $alert_type = "SUCCESS";
    $alert_msg = "Role has been created successfully.";
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
          <div class="left_col scroll-view ">

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
              <input type="hidden" name="ROLE_NAME" value="<?php echo $ROLE_NAME; ?>">
              <input type="hidden" name="ROLE_TYPE" value="<?php echo $ROLE_TYPE; ?>">
              <input type="hidden" name="FTR_TYPE" value="<?php echo $ROLE_TYPE; ?>">
            
              <div class="x_panel">
                <div class="x_title">
                  <div class="col-md-1 col-sm-1 col-xs-12">
                    <a href="roles-create" class="btn btn-dark btn-sm">Back</a>
                  </div>
                  <h2>Create New Role</h2> <small>(Enable Features)</small>
                  <div class="nav navbar-right panel_toolbox">
                    <button type="submit" class="btn btn-success btn-sm" name="btn_save_role">Save Details</button>
                  </div>
                  <div class="clearfix"></div>
                </div>

                <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
                <div class="x_content">         
                  
                  
                  <table class="table table-bordered">
                    <tr valign="top"><th bgcolor="#EEE">ROLE NAME</th><td colspan="4"><?php echo $ROLE_NAME; ?></td></tr>
                    <tr valign="top"><th bgcolor="#EEE">ROLE TYPE</th><td colspan="4"><?php echo $ROLE_TYPE; ?></td></tr>
                    <?php
                    $FTR_TYPE = $ROLE_TYPE;

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
                            <th bgcolor="#EEE">Action</th>
                          </tr>
                        <?php
                        for ($z=0; $z < sizeof($MENU_LEVEL_03_LIST); $z++) { 
                          
                          $MENU_LEVEL_03 = array();
                          $MENU_LEVEL_03 = $MENU_LEVEL_03_LIST[$z];
                          $BTM_FTR_ID = $MENU_LEVEL_03['BTM_FTR_ID'];
                          $BTM_FTR_NAME = $MENU_LEVEL_03['BTM_FTR_NAME'];

                          ?>
                          <tr valign="top">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><?php echo ($z+1); ?></td>
                            <td align="left"><?php echo $BTM_FTR_NAME; ?></td>
                            <td>
                              <select id="<?php echo $BTM_FTR_ID; ?>" name="<?php echo $BTM_FTR_ID; ?>">
                                <option value="NO" selected="selected">DISABLE</option>
                                <option value="YES">ENABLE</option>
                              </select>
                            </td>
                          </tr>

                          <?php
                        } # ... End loop 03
                      }   # ... End loop 02
                    } # ... End loop 01
                    ?>
                  </table>
                  <div class="nav navbar-left panel_toolbox">
                    <a href="roles-create" class="btn btn-dark btn-sm">Back</a>
                  </div>

                  <div class="nav navbar-right panel_toolbox">
                    <button type="submit" class="btn btn-success btn-sm" name="btn_save_role">Save Details</button>
                  </div>

                  <script type="text/javascript">
                    $('select').change(function(){
                      if($(this).val() === 'NO') {
                        //$(this).parent().css({
                        //  'background-color': '#fff'
                        //});

                        $(this).closest("tr").css("background-color","white")
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
