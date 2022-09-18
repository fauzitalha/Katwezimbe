<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");


# ... Handle Button Click
if (isset($_POST['btn_create'])) {
  $APPLN_CONFIG_NAME = mysql_real_escape_string(trim($_POST['APPLN_CONFIG_NAME']));
  $APPLN_TYPE_ID = mysql_real_escape_string(trim($_POST['APPLN_TYPE_ID']));
  $PDT_ID = mysql_real_escape_string(trim($_POST['PDT_ID']));
  
  $PDT_TYPE_ID = ""; 
  if ($APPLN_TYPE_ID=="Y0001") { $PDT_TYPE_ID = "LOAN"; }
  if ( ($APPLN_TYPE_ID=="Y0002")||($APPLN_TYPE_ID=="Y0003") ) { $PDT_TYPE_ID = "SVNG"; }
  $CREATED_BY = $_SESSION['UPR_USER_ID'];
  $CREATED_ON = GetCurrentDateTime(); 

  $CHECK_Q = "SELECT count(*) as RTN_VALUE 
              FROM appln_configs 
              WHERE PDT_ID='$PDT_ID' AND APPLN_TYPE_ID='$APPLN_TYPE_ID' AND APPLN_CONFIG_STATUS='ACTIVE'";
  $CHECK_Q_CNT = ReturnOneEntryFromDB($CHECK_Q);
  if ($CHECK_Q_CNT>0) {
    $alert_type = "ERROR";
    $alert_msg = "MESSAGE: application config is already configured for this product.";
    $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
  }
  else{
    $q = "INSERT INTO appln_configs(APPLN_CONFIG_NAME,APPLN_TYPE_ID,PDT_ID,PDT_TYPE_ID,CREATED_BY,CREATED_ON) VALUES('$APPLN_CONFIG_NAME','$APPLN_TYPE_ID','$PDT_ID','$PDT_TYPE_ID','$CREATED_BY','$CREATED_ON')";
    $exec_response = array();
    $exec_response = ExecuteEntityInsert($q);
    $RESP = $exec_response["RESP"]; 
    $RECORD_ID = $exec_response["RECORD_ID"];

    # ... Process Entity System ID (Role ID)
    $id_prefix = "CF";
    $id_len = 7;
    $id_record_id = $RECORD_ID;
    $ENTITY_ID = ProcessEntityID($id_prefix, $id_len, $id_record_id);
    $APPLN_CONFIG_ID = $ENTITY_ID;

    # ... Updating the role id
    $q2 = "UPDATE appln_configs SET APPLN_CONFIG_ID='$APPLN_CONFIG_ID' WHERE RECORD_ID='$RECORD_ID'";
    $update_response = ExecuteEntityUpdate($q2);
    if ($update_response=="EXECUTED") {

      # ... Log System Audit Log
      $AUDIT_DATE = GetCurrentDateTime();
      $ENTITY_TYPE = "APPLN_CONFIG";
      $ENTITY_ID_AFFECTED = $APPLN_CONFIG_ID;
      $EVENT = "CREATE";
      $EVENT_OPERATION = "CREATE_NEW_APPLN_CONFIG";
      $EVENT_RELATION = "appln_configs";
      $EVENT_RELATION_NO = $RECORD_ID;
      $OTHER_DETAILS = "";
      $INVOKER_ID = $_SESSION['UPR_USER_ID'];
      LogSystemEvent($AUDIT_DATE, $ENTITY_TYPE, $ENTITY_ID_AFFECTED, $EVENT, $EVENT_OPERATION, 
                     $EVENT_RELATION, $EVENT_RELATION_NO, $OTHER_DETAILS, $INVOKER_ID);


      $alert_type = "SUCCESS";
      $alert_msg = "SUCCESS: application config has been created successfully.";
      $_SESSION['ALERT_MSG'] = SystemAlertMessage($alert_type, $alert_msg);
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
    LoadDefaultCSSConfigurations("Create New Config", $APP_SMALL_LOGO); 

    # ... Javascript
    LoadPriorityJS();
    OnLoadExecutions();
    StartTimeoutCountdown();
    ExecuteProcessStatistics();
    ?>

    <script type="text/javascript">
      function FetchAssocProducts() {
        
        $("#PDT_ID").empty();

        var selected_val = document.getElementById('APPLN_TYPE_ID').value;
        var select_PDT_ID = document.getElementById("PDT_ID");

        // ... Ajax
        $.ajax
        ({
          type:'post',
          url:'ajax-fetch-core-pdts.php',
          data:{
            pdt_type: selected_val
          },
          success:function(response) 
          {
            //console.log(response);

            // ... Handling of Db responses
            response = JSON.parse(response)
            console.log(response);

            select_PDT_ID.options[select_PDT_ID.options.length] = new Option('-----------', '');
            for(var x=0; x<response.length; x++){

              var arrJSON = response[x];
              var PDT_ID = arrJSON.PDT_ID;
              var PDT_NAME = arrJSON.PDT_NAME;
              var PDT_SORT_NAME = arrJSON.PDT_SORT_NAME;

              select_PDT_ID.options[select_PDT_ID.options.length] = new Option(PDT_NAME, PDT_ID);

            }


          }
        });
      }
    </script>
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
                <h2>Create New Config</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         

                <div class="col-md-8 center-margin">
                  <form class="form-horizontal form-label-left" method="post">
                    
                    <div class="form-group">
                      <label>Appln. Type</label>
                      <select id="APPLN_TYPE_ID" name="APPLN_TYPE_ID" class="form-control" required="" onchange="FetchAssocProducts()">
                        <option value="">-----------</option>
                        <?php
                        $APPLN_TYPE_STATUS = "ACTIVE";
                        $appln_type_list = array();
                        $appln_type_list = FetchApplnTypes($APPLN_TYPE_STATUS);
                        for ($i=0; $i < sizeof($appln_type_list); $i++) { 
                          
                          $appln_type = array();
                          $appln_type = $appln_type_list[$i];
                          $RECORD_ID = $appln_type['RECORD_ID'];
                          $APPLN_TYPE_ID = $appln_type['APPLN_TYPE_ID'];
                          $APPLN_TYPE_NAME = $appln_type['APPLN_TYPE_NAME'];
                          $APPLN_TYPE_STATUS = $appln_type['APPLN_TYPE_STATUS'];
                          ?>
                          <option value="<?php echo $APPLN_TYPE_ID; ?>"><?php echo $APPLN_TYPE_NAME; ?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Appln. Product</label>
                      <select id="PDT_ID" name="PDT_ID" class="form-control" required="">
                        <option value="" selected="selected">-----------</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Appln. Config Name</label>
                      <input type="text" class="form-control" id="APPLN_CONFIG_NAME" name="APPLN_CONFIG_NAME" placeholder="Enter Config Name" required="">
                    </div>
                    <div class="form-group">
                      <br />
                      <button type="submit" class="btn btn-primary" name="btn_create">Create</button>
                    </div>

                  </form>
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
