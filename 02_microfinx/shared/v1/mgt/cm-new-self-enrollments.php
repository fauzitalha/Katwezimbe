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
    LoadDefaultCSSConfigurations("New Enrollments", $APP_SMALL_LOGO); 

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
                <h2>New Enrollments</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         

                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="11" bgcolor="#EEE">List of New Self Enrollments</th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Regn. Date</th>
                      <th>Regn. Type</th>
                      <th>Actvn Ref</th>
                      <th>Cust_Name</th>
                      <th>Sex</th>
                      <th>WorkID</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php

                    $ACTIVATION_STATUS_RESUBMITTED = "RESUBMITTED";
                    $ACTIVATION_STATUS_BOUNCED = "BOUNCED";
                    $ACTIVATION_STATUS_PENDING = "PENDING";
                    $cstmr_actvn_list_resubmitted = array();
                    $cstmr_actvn_list_bounced = array();
                    $cstmr_actvn_list_pending = array();
                    $cstmr_actvn_list_resubmitted = FetchActivationRequestsByStatus($ACTIVATION_STATUS_RESUBMITTED);
                    $cstmr_actvn_list_bounced = FetchActivationRequestsByStatus($ACTIVATION_STATUS_BOUNCED);
                    $cstmr_actvn_list_pending = FetchActivationRequestsByStatus($ACTIVATION_STATUS_PENDING);

                    $cstmr_actvn_list = array();
                    $cstmr_actvn_list = array_merge($cstmr_actvn_list_resubmitted, $cstmr_actvn_list_bounced, $cstmr_actvn_list_pending);


                    for ($i=0; $i < sizeof($cstmr_actvn_list); $i++) {
                      $cstmr_actvn = array();
                      $cstmr_actvn = $cstmr_actvn_list[$i];
                      $RECORD_ID= $cstmr_actvn['RECORD_ID'];
                      $ACTIVATION_REF= $cstmr_actvn['ACTIVATION_REF'];
                      $MMBSHP_TYPE= $cstmr_actvn['MMBSHP_TYPE'];
                      $CHANNEL_ID= $cstmr_actvn['CHANNEL_ID'];
                      $FIRST_NAME= $cstmr_actvn['FIRST_NAME'];
                      $MIDDLE_NAME= $cstmr_actvn['MIDDLE_NAME'];
                      $LAST_NAME= $cstmr_actvn['LAST_NAME'];
                      $GENDER= $cstmr_actvn['GENDER'];
                      $DOB= $cstmr_actvn['DOB'];
                      $BIO_DATA_VERIF_FLG= $cstmr_actvn['BIO_DATA_VERIF_FLG'];
                      $BIO_DATA_VERIF_RMKS= $cstmr_actvn['BIO_DATA_VERIF_RMKS'];
                      $BIO_DATA_VERIF_RMKS_BY= $cstmr_actvn['BIO_DATA_VERIF_RMKS_BY'];
                      $BIO_DATA_VERIF_RMKS_DATE= $cstmr_actvn['BIO_DATA_VERIF_RMKS_DATE'];
                      $EMAIL= $cstmr_actvn['EMAIL'];
                      $MOBILE_NO= $cstmr_actvn['MOBILE_NO'];
                      $PHYSICAL_ADDRESS= $cstmr_actvn['PHYSICAL_ADDRESS'];
                      $CONTACT_DATA_VERIF_FLG= $cstmr_actvn['CONTACT_DATA_VERIF_FLG'];
                      $CONTACT_DATA_VERIF_RMKS= $cstmr_actvn['CONTACT_DATA_VERIF_RMKS'];
                      $CONTACT_DATA_VERIF_BY= $cstmr_actvn['CONTACT_DATA_VERIF_BY'];
                      $CONTACT_DATA_VERIF_DATE= $cstmr_actvn['CONTACT_DATA_VERIF_DATE'];
                      $WORK_ID= $cstmr_actvn['WORK_ID'];
                      $WORK_ID_ATTCHMNT_FLG= $cstmr_actvn['WORK_ID_ATTCHMNT_FLG'];
                      $WORK_ID_FILE_NAME= $cstmr_actvn['WORK_ID_FILE_NAME'];
                      $NATIONAL_ID= $cstmr_actvn['NATIONAL_ID'];
                      $NATIONAL_ID_ATTCHMNT_FLG= $cstmr_actvn['NATIONAL_ID_ATTCHMNT_FLG'];
                      $NATIONAL_ID_FILE_NAME= $cstmr_actvn['NATIONAL_ID_FILE_NAME'];
                      $MAF_UPLOAD_FLG= $cstmr_actvn['MAF_UPLOAD_FLG'];
                      $MAF_UPLOAD_FILE_NAME= $cstmr_actvn['MAF_UPLOAD_FILE_NAME'];
                      $PASSPORT_PHOTO_UPLOAD_FLG= $cstmr_actvn['PASSPORT_PHOTO_UPLOAD_FLG'];
                      $PASSPORT_PHOTO_FILE_NAME= $cstmr_actvn['PASSPORT_PHOTO_FILE_NAME'];
                      $FILE_DATA_VERIF_FLG= $cstmr_actvn['FILE_DATA_VERIF_FLG'];
                      $FILE_DATA_VERIF_RMKS= $cstmr_actvn['FILE_DATA_VERIF_RMKS'];
                      $FILE_DATA_VERIF_BY= $cstmr_actvn['FILE_DATA_VERIF_BY'];
                      $FILE_DATA_VERIF_DATE= $cstmr_actvn['FILE_DATA_VERIF_DATE'];
                      $REQST_RECORD_DATE= $cstmr_actvn['REQST_RECORD_DATE'];
                      $APPRVL_DATE= $cstmr_actvn['APPRVL_DATE'];
                      $APPRVD_BY= $cstmr_actvn['APPRVD_BY'];
                      $ACTIVATION_STATUS= $cstmr_actvn['ACTIVATION_STATUS'];
                      
                      $Cust_Name = $FIRST_NAME." ".$MIDDLE_NAME." ".$LAST_NAME;
                      $data_transfer = $ACTIVATION_REF;

                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $REQST_RECORD_DATE; ?></td>
                        <td><?php echo $MMBSHP_TYPE; ?></td>
                        <td><?php echo $ACTIVATION_REF; ?></td>
                        <td><?php echo $Cust_Name; ?></td>
                        <td><?php echo $GENDER; ?></td>
                        <td><?php echo $WORK_ID; ?></td>
                        <td><?php echo $EMAIL; ?></td>
                        <td><?php echo $MOBILE_NO; ?></td>
                        <td><?php echo $ACTIVATION_STATUS; ?></td>
                        <td>
                          <a href="cm-new-self-enrollments-ind-details?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
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
