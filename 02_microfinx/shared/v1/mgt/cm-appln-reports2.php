<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Variables
$filter = mysql_real_escape_string(trim($_POST['filter']));

$dd1 = mysql_real_escape_string(trim($_POST['dd1']));
$mm1 = mysql_real_escape_string(trim($_POST['mm1']));
$yy1 = mysql_real_escape_string(trim($_POST['yy1']));
$date11 = $yy1."-".$mm1."-".$dd1;
$date111 = date('Y-m-d', strtotime($date11));

$dd2 = mysql_real_escape_string(trim($_POST['dd2']));
$mm2 = mysql_real_escape_string(trim($_POST['mm2']));
$yy2 = mysql_real_escape_string(trim($_POST['yy2']));
$date22 = $yy2."-".$mm2."-".$dd2;


$time22 = strtotime($date22."+1 days");
$date33 = date('Y-m-d', $time22);

$START_DATE = $date111;
$END_DATE = $date33;
# ............................................................................................


# ... Processing filter
$DB_QUERY = "";
$TITLE = "";
if ($filter=="ALL") {
  $TITLE = "All Applications";
  $DB_QUERY = "SELECT * FROM cstmrs_actvn_rqsts 
               WHERE REQST_RECORD_DATE>='$START_DATE' AND REQST_RECORD_DATE<'$END_DATE' 
               ORDER BY REQST_RECORD_DATE ASC";
}

if ($filter=="NEW") {
  $TITLE = "New & Resubmitted Applications";
  $DB_QUERY = "SELECT * FROM cstmrs_actvn_rqsts 
               WHERE REQST_RECORD_DATE>='$START_DATE' AND REQST_RECORD_DATE<'$END_DATE' 
                 AND ACTIVATION_STATUS in ('PENDING','RESUBMITTED')
               ORDER BY REQST_RECORD_DATE ASC";
}

if ($filter=="REVIEW") {
  $TITLE = "Applications resent to customers for correction.";
  $DB_QUERY = "SELECT * FROM cstmrs_actvn_rqsts 
               WHERE REQST_RECORD_DATE>='$START_DATE' AND REQST_RECORD_DATE<'$END_DATE' 
                 AND ACTIVATION_STATUS in ('NEEDS_CUSTOMER_REVIEW')
               ORDER BY REQST_RECORD_DATE ASC";
}

if ($filter=="P_APPROVAL") {
  $TITLE = "Applications that have been verified but pending approval.";
  $DB_QUERY = "SELECT * FROM cstmrs_actvn_rqsts 
               WHERE REQST_RECORD_DATE>='$START_DATE' AND REQST_RECORD_DATE<'$END_DATE' 
                 AND ACTIVATION_STATUS in ('VERIFIED')
               ORDER BY REQST_RECORD_DATE ASC";
}

if ($filter=="P_FINAL") {
  $TITLE = "Applications that have been approved but pending set up in system.";
  $DB_QUERY = "SELECT * FROM cstmrs_actvn_rqsts 
               WHERE REQST_RECORD_DATE>='$START_DATE' AND REQST_RECORD_DATE<'$END_DATE' 
                 AND ACTIVATION_STATUS in ('APPROVED')
               ORDER BY REQST_RECORD_DATE ASC";
}

if ($filter=="COMPLETE") {
  $TITLE = "Applications completed successfully.";
  $DB_QUERY = "SELECT * FROM cstmrs_actvn_rqsts 
               WHERE REQST_RECORD_DATE>='$START_DATE' AND REQST_RECORD_DATE<'$END_DATE' 
                 AND ACTIVATION_STATUS in ('COMPLETE')
               ORDER BY REQST_RECORD_DATE ASC";
}



if ($filter=="REJECTED") {
  $TITLE = "Rejected Applications.";
  $DB_QUERY = "SELECT * FROM cstmrs_actvn_rqsts 
               WHERE REQST_RECORD_DATE>='$START_DATE' AND REQST_RECORD_DATE<'$END_DATE' 
                 AND ACTIVATION_STATUS in ('REJECTED')
               ORDER BY REQST_RECORD_DATE ASC";
}


?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Appln Report", $APP_SMALL_LOGO); 

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
                <a href="cm-appln-reports" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Appln Report</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         

                <table class="table table-striped table-bordered" style="font-size: 11px;">
                  <tr><th width="20%">TITLE</th><td><?php echo $TITLE; ?></td></tr>
                  <tr><th>START_DATE</th><td><?php echo date('d-M-Y', strtotime($START_DATE)); ?></td></tr>
                  <tr><th>END_DATE</th><td><?php echo date('d-M-Y', strtotime($END_DATE)); ?></td></tr>
                </table>

                <table id="datatable" class="table table-striped table-bordered" style="font-size: 11px;">
                  <thead>
                    <tr valign="top">
                      <th colspan="9" bgcolor="#EEE">
                        <?php echo $TITLE; ?>
                        <a href="export-excel-xlsx" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i> Download</a>  
                      </th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Regn. Date</th>
                      <th>Regn. Type</th>
                      <th>Actvn Ref</th>
                      <th>Cust_Name</th>
                      <th>Sex</th>
                      <th>Phone</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    $cstmr_actvn_list = array();
                    $cstmr_actvn_list = FetchAllActivationRequestsByQuery($DB_QUERY);
                    $excel_table_list = array();



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

                      # ... Building the excel table row
                      $excel_table_row[0] = ($i+1);
                      $excel_table_row[1] = $REQST_RECORD_DATE;
                      $excel_table_row[2] = $MMBSHP_TYPE;
                      $excel_table_row[3] = $ACTIVATION_REF;
                      $excel_table_row[4] = $Cust_Name;
                      $excel_table_row[5] = $GENDER;
                      $excel_table_row[6] = $MOBILE_NO;
                      $excel_table_row[7] = $EMAIL;
                      $excel_table_row[8] = $ACTIVATION_STATUS;
                      $excel_table_list[$i] = $excel_table_row;

                      

                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $REQST_RECORD_DATE; ?></td>
                        <td><?php echo $MMBSHP_TYPE; ?></td>
                        <td><?php echo $ACTIVATION_REF; ?></td>
                        <td><?php echo $Cust_Name; ?></td>
                        <td><?php echo $GENDER; ?></td>
                        <td><?php echo $MOBILE_NO; ?></td>
                        <td><?php echo $ACTIVATION_STATUS; ?></td>
                        <td>
                          <a href="cm-appln-reports-ind-details?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">View</a>
                        </td>
                      </tr>


                      <?php

                    }
                    # ... Excel Data Preparation
                    $_SESSION["EXCEL_HEADER"] = array("#","Regn. Date","Regn. Type","Actvn Ref","Cust_Name","Sex","Cust_Name","Phone","Email","Status");
                    $_SESSION["EXCEL_DATA"] = $excel_table_list;
                    $_SESSION["EXCEL_FILE"] = "ApplicationReport_".date('dFY', strtotime(GetCurrentDateTime())).".xlsx";
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


