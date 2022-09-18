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
    LoadDefaultCSSConfigurations("Client List", $APP_SMALL_LOGO); 

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
                <h2>Select Client from List</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">  

                <!-- -- -- -- -- -- -- -- -- -- -- SMART WIZARD -- -- -- -- -- -- -- -- -- -- -- -->       
                <!-- -- -- -- -- -- -- -- -- -- -- SMART WIZARD -- -- -- -- -- -- -- -- -- -- -- -->       
                <div id="wizard" class="form_wizard wizard_horizontal">
                  <ul class="wizard_steps">
                    <li>
                      <a href="#step-1">
                        <span class="step_no" style="background-color: #006DAE;">1</span>
                        <span class="step_descr">
                          Step 1<br />
                          <small>Select Client</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-2" >
                        <span class="step_no" style="background-color: #D1F2F2;">2</span>
                        <span class="step_descr">
                          Step 2<br />
                          <small>Review Personal Info.</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-3">
                        <span class="step_no" style="background-color: #D1F2F2;">3</span>
                        <span class="step_descr">
                            Step 3<br />
                            <small>Enter Loan Details</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-4">
                        <span class="step_no" style="background-color: #D1F2F2;">4</span>
                        <span class="step_descr">
                            Step 4<br />
                            <small>Loan Documents & Guarantors</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-5">
                        <span class="step_no" style="background-color: #D1F2F2;">5</span>
                        <span class="step_descr">
                            Step 5<br />
                            <small>Terms & Conditions</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-6">
                        <span class="step_no" style="background-color: #D1F2F2;">6</span>
                        <span class="step_descr">
                            Step 6<br />
                            <small>Signing & Submission</small>
                        </span>
                      </a>
                    </li>
                  </ul>
                </div>


                <!-- -- -- -- -- -- -- -- -- -- -- CLIENT LIST -- -- -- -- -- -- -- -- -- -- -- -->       
                <!-- -- -- -- -- -- -- -- -- -- -- CLIENT LIST -- -- -- -- -- -- -- -- -- -- -- -->       
              	<table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr valign="top">
                      <th colspan="7" bgcolor="#EEE">Client List</th>
                    </tr>
                    <tr valign="top">
                      <th>#</th>
                      <th>Client Name</th>
                      <th>Client Id</th>
                      <th>External Id</th>
                      <th>Activation Date</th>
                      <th>e-Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  	<?php
                  	$client_list = array();
                  	$response_msg = FetchAllClients($MIFOS_CONN_DETAILS);
                    $CONN_FLG = $response_msg["CONN_FLG"];
                    $CORE_RESP = $response_msg["CORE_RESP"];
                    $client_list = $CORE_RESP["data"];

                    for ($i=0; $i < sizeof($client_list); $i++) { 
                      
                      $client = array();
                      $client = $client_list[$i]["row"];
                      $CLIENT_CORE_ID = $client[0];
                      $CLIENT_CORE_ID_NUM = $client[1];
                      $CLIENT_STATUS_ENUM = $client[2];
                      $CLIENT_CORE_NAME = $client[3];
                      $CLIENT_EXTERN_ID = $client[4];
                      $CLIENT_ACTVN_DATE = $client[5];
                      $E_STATUS = "NOT_ENROLLED";
                      $Q_CHK = "SELECT count(*) as RTN_VALUE FROM cstmrs WHERE CUST_CORE_ID='$CLIENT_CORE_ID' AND CUST_STATUS not in ('DELETED','REJECTED')";
											$C_CHK = ReturnOneEntryFromDB($Q_CHK);
											if ($C_CHK>0) {
											  $E_STATUS = "ENROLLED";
											}

                      $data_transfer = $CLIENT_CORE_ID;
                      ?>
                      <tr valign="top">
                        <td><?php echo ($i+1); ?>. </td>
                        <td><?php echo $CLIENT_CORE_NAME; ?></td>
                        <td><?php echo $CLIENT_CORE_ID_NUM; ?></td>
                        <td><?php echo $CLIENT_EXTERN_ID; ?></td>
                        <td><?php echo $CLIENT_ACTVN_DATE; ?></td>
                        <td><?php echo $E_STATUS; ?></td>
                        <td>
                          <a href="la-new-appln-walkin1?k=<?php echo $data_transfer; ?>" class="btn btn-primary btn-xs">Select</a>
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
