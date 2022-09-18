<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Reeceiving Data
$SUMMARY = $_SESSION["ACTN_RQST_SUMMARY"];
$ACTIVATION_REF = $SUMMARY["ACTIVATION_REF"];
$CUST_NAME = $SUMMARY["CUST_NAME"];
$EMAIL = $SUMMARY["EMAIL"];
$PHONE = $SUMMARY["PHONE"];

$FILE_UPLOAD_RMKS = $SUMMARY["FILE_UPLOAD_RMKS"];
$WORKID_RMKS = $FILE_UPLOAD_RMKS["WORKID_RMKS"];
$NATIONALID_RMKS = $FILE_UPLOAD_RMKS["NATIONALID_RMKS"];
$MAF_RMKS = $FILE_UPLOAD_RMKS["MAF_RMKS"];
$PP_RMKS = $FILE_UPLOAD_RMKS["PP_RMKS"];

?>
<!DOCTYPE html>
<html>

<head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings();
    LoadDefaultCSSConfigurations("Main Control", $APP_SMALL_LOGO);

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
                    <div align="center" style="width: 100%;"><?php if (isset($_SESSION['ALERT_MSG'])) {
                                                                    echo $_SESSION['ALERT_MSG'];
                                                                } ?></div>
                    <div class="x_panel">
                        <div class="">
                            <a href="cm-client-list-ind?k=<?php echo $CUST_CORE_ID; ?>" class="btn btn-dark btn-sm pull-left">Back</a>
                            <h2>Enroll Client on e-Platform (Internet and Mobile)</h2>
                            <div class="clearfix"></div>
												</div>
												
												<div class="x_content">
													<table class="table table-striped table-bordered">
														<tr valign="top"><th colspan="3">GENERAL DETAILS</th></tr>
														<tr valign="top"><th width="20%">ACTIVATION_REF</th><th width="3%">:</th><td style="color: green; font-weight: bolder; font-size: 20px;"><?php echo $ACTIVATION_REF; ?></td></tr>
														<tr valign="top"><th>CUST_NAME</th><th>:</th><td><?php echo $CUST_NAME; ?></td></tr>
														<tr valign="top"><th>EMAIL</th><th>:</th><td><?php echo $EMAIL; ?></td></tr>
														<tr valign="top"><th>PHONE</th><th>:</th><td><?php echo $PHONE; ?></td></tr>
														<tr valign="top"><th colspan="3">&nbsp;</th></tr>
														<tr valign="top"><th colspan="3">FILE UPLOAD REMARKS</th></tr>
														<tr valign="top"><th>WORKID</th><th>:</th><td><?php echo $WORKID_RMKS; ?></td></tr>
														<tr valign="top"><th>NATIONAL_ID</th><th>:</th><td><?php echo $NATIONALID_RMKS; ?></td></tr>
														<tr valign="top"><th>APPLICATION FORM</th><th>:</th><td><?php echo $MAF_RMKS; ?></td></tr>
														<tr valign="top"><th>PASSPORT PHOTO</th><th>:</th><td><?php echo $PP_RMKS; ?></td></tr>
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