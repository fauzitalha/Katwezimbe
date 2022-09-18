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
    LoadDefaultCSSConfigurations("Execute Bulk File", $APP_SMALL_LOGO); 

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
                <h2>Execute Bulk File</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">         

                <table id="datatable" class="table table-striped table-bordered" style="font-size: 12px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">
                        Bulk Files Pending Verification
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>File Id</th>
                      <th>File Name</th>
                      <th>Description</th>
                      <th>Upload Date</th>
                      <th>Entries</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $file_list = array();
                    $file_list = FetchApprovedBulkFiles();
                    for ($i=0; $i < sizeof($file_list); $i++) { 
                      $file = array();
                      $file = $file_list[$i];
                      $RECORD_ID = $file['RECORD_ID'];
                      $FILE_ID = $file['FILE_ID'];
                      $FILE_NAME = $file['FILE_NAME'];
                      $UPLOAD_REASON = $file['UPLOAD_REASON'];
                      $UPLOADED_BY = $file['UPLOADED_BY'];
                      $UPLOADED_ON = $file['UPLOADED_ON'];
                      $VERIFIED_RMKS = $file['VERIFIED_RMKS'];
                      $VERIFIED_BY = $file['VERIFIED_BY'];
                      $VERIFIED_ON = $file['VERIFIED_ON'];
                      $APPROVED_RMKS = $file['APPROVED_RMKS'];
                      $APPROVED_BY = $file['APPROVED_BY'];
                      $APPROVED_ON = $file['APPROVED_ON'];
                      $REVERSAL_FLG = $file['REVERSAL_FLG'];
                      $REV_INIT_RMKS = $file['REV_INIT_RMKS'];
                      $REV_INIT_BY = $file['REV_INIT_BY'];
                      $REV_INIT_ON = $file['REV_INIT_ON'];
                      $REV_APPROVED_RMKS = $file['REV_APPROVED_RMKS'];
                      $REV_APPROVED_BY = $file['REV_APPROVED_BY'];
                      $REV_APPROVED_ON = $file['REV_APPROVED_ON'];
                      $FILE_STATUS = $file['FILE_STATUS'];

                      # ... Count of Entries
                      $Q_ENT = "SELECT count(*) as RTN_VALUE FROM blk_pymt_txns WHERE FILE_ID='$FILE_ID'";
                      $CNT_ENT = ReturnOneEntryFromDB($Q_ENT);
                     


                      $datatotransfer = $FILE_ID;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $FILE_ID; ?></td>
                        <td><?php echo $FILE_NAME; ?></td>
                        <td><?php echo $UPLOAD_REASON; ?></td>
                        <td><?php echo $UPLOADED_ON; ?></td>
                        <td><?php echo $CNT_ENT; ?></td>
                        <td>
                          <a href="blk-exec-file-ind?k=<?php echo $datatotransfer; ?>" class="btn btn-xs btn-primary">Manage</a>
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
