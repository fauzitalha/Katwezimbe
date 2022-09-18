<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Receiving Details
$data = mysql_real_escape_string(trim($_GET['k']));
$data_details = explode('-', $data);
$pdt_id = $data_details[0];
$pdt_name = $data_details[1];
$CUST_CORE_ID = $data_details[2];

# ... ... ... ... ... ... ... ... Get Customer Main Details ... ... ... ... ... ... ... ... ... ... ...#
$cust_id = $CUST_CORE_ID;
$response_msg = FetchCustomerDetailsFromCore($cust_id, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$CST_accountNo = isset($CORE_RESP["accountNo"])? $CORE_RESP["accountNo"] : "";
$CST_displayName = isset($CORE_RESP["displayName"])? $CORE_RESP["displayName"] : "";
$CST_mobileNo = isset($CORE_RESP["mobileNo"])? $CORE_RESP["mobileNo"] : "";

# ... ... ... ... ... ... ... ... Get Customer Other Details ... ... ... ... ... ... ... ... ... ... ...#
$CST_client_id = "";
$CST_Email = "";
$CST_WorkID = "";
$CST_NationalId = "";
$CST_Physical_Address = "";
$CST_Date_of_Birth = "";

$CLIENT_ID = $CUST_CORE_ID;
$response_msg = FetchClientOtherDetails_Walkin($CLIENT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
if (isset($CORE_RESP[0]["client_id"])) {
  $CST_client_id = $CORE_RESP[0]["client_id"];
  $CST_Email = $CORE_RESP[0]["Email"];
  $CST_WorkID = $CORE_RESP[0]["WorkID"];
  $CST_NationalId = $CORE_RESP[0]["NationalId"];
  $CST_Physical_Address = $CORE_RESP[0]["Physical_Address"];
  $CST_Date_of_Birth = $CORE_RESP[0]["Date_of_Birth"];
}

# ... ... ... ... ... ... ... ... Get Customer Photo ... ... ... ... ... ... ... ... ... ... ...#
$img_response_msg = FetchClientImage($CUST_CORE_ID, $MIFOS_CONN_DETAILS);


# ... ... ... ... ... ... ... ... Get Customer Documents ... ... ... ... ... ... ... ... ... ... ...#
$client_doc_list = array();
$response_msg = FetchClientDocuments_Walkin($CLIENT_ID, $MIFOS_CONN_DETAILS);
$CONN_FLG = $response_msg["CONN_FLG"];
$CORE_RESP = $response_msg["CORE_RESP"];
$client_doc_list = $CORE_RESP;



# ... Process System Remarks about client account.. ... ... ... ... ... ... ... ... ... ... ... ... ... #
$SYS_RMKS = array();
$gen_msg = "";

$mob_gen_flg = "";
if ($CST_mobileNo!="") {
  $SYS_RMKS["MOB"] = "<span style='color: green; font-weight: bolder;'>YES</span>";
  $mob_gen_flg = true;
} else {
  $SYS_RMKS["MOB"] = "<span style='color: red; font-weight: bolder;'>NO</span>";
  $mob_gen_flg = false;
  $gen_msg .= "-> Mobile number not available.<br>";
}

$emm_gen_flg = "";
if ($CST_Email!="") {
  $SYS_RMKS["EMM"] = "<span style='color: green; font-weight: bolder;'>YES</span>";
  $emm_gen_flg = true;
} else {
  $SYS_RMKS["EMM"] = "<span style='color: red; font-weight: bolder;'>NO</span>";
  $emm_gen_flg = false;
  $gen_msg .= "-> Email address not available.<br>";
}

$phy_gen_flg = "";
if ($CST_Physical_Address!="") {
  $SYS_RMKS["PHY"] = "<span style='color: green; font-weight: bolder;'>YES</span>";
  $phy_gen_flg = true;
} else {
  $SYS_RMKS["PHY"] = "<span style='color: red; font-weight: bolder;'>NO</span>";
  $phy_gen_flg = false;
  $gen_msg .= "-> Physical address not available.<br>";
}

$img_gen_flg = "";
if ($img_response_msg!="") {
  $SYS_RMKS["IMG"] = "<span style='color: green; font-weight: bolder;'>YES</span>";
  $img_gen_flg = true;
} else {
  $SYS_RMKS["IMG"] = "<span style='color: red; font-weight: bolder;'>NO</span>";
  $img_gen_flg = false;
  $gen_msg .= "-> Customer image not available.<br>";
}

$doc_gen_flg = "";
if (sizeof($client_doc_list)>0) {
  $SYS_RMKS["DOC"] = "<span style='color: green; font-weight: bolder;'>YES</span>";
  $doc_gen_flg = true;
} else {
  $SYS_RMKS["DOC"] = "<span style='color: red; font-weight: bolder;'>NO</span>";
  $doc_gen_flg = false;
  $gen_msg .= "-> Customer support documents not available.<br>";
}

if ($mob_gen_flg && $emm_gen_flg && $phy_gen_flg && $img_gen_flg && $doc_gen_flg) {
  $SYS_RMKS["PROC"] = "OK";
  $SYS_RMKS["GEN_MSG"] = "<span style='color: green; font-weight: bolder;'>All is good</span>";
} else {
  $SYS_RMKS["PROC"] = "ERR";
  $SYS_RMKS["GEN_MSG"] = "<span style='color: red; font-weight: bolder;'>".$gen_msg."</span>";
}


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
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <a href="la-new-appln-walkin1?k=<?php echo $CUST_CORE_ID; ?>" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>STEP 02: New Loan Appln</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         
                <!-- -- -- -- -- -- -- -- -- -- -- SMART WIZARD -- -- -- -- -- -- -- -- -- -- -- -->       
                <!-- -- -- -- -- -- -- -- -- -- -- SMART WIZARD -- -- -- -- -- -- -- -- -- -- -- -->       
                <div id="wizard" class="form_wizard wizard_horizontal">
                  <ul class="wizard_steps">
                    <li>
                      <a href="#step-1">
                        <span class="step_no" style="background-color: #1ABB9C;">1</span>
                        <span class="step_descr">
                          Step 1<br />
                          <small>Select Loan Product</small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#step-2" >
                        <span class="step_no" style="background-color: #006DAE;">2</span>
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
              
              </div>
            </div>           
          </div>   

          <div class="col-md-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION A: </strong> Client Details
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

                <div class="col-md-5 col-sm-5 col-xs-12">
                  <img src="<?php  echo $img_response_msg; ?>" width="100%">
                </div>

                <div class="col-md-7 col-sm-7 col-xs-12">
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                    <label>Customer ID</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CST_accountNo; ?>">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                    <label>Customer Full Name</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CST_displayName; ?>">
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                    <label>Date of Birth</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CST_Date_of_Birth; ?>">
                  </div> 
                </div>   

                <div class="col-md-5 col-sm-5 col-xs-12">
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                    <label>Work ID/Staff ID/Personal ID</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CST_WorkID; ?>">
                  </div> 

                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                    <label>National Id</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CST_NationalId; ?>">
                  </div>  
                </div>

                <div class="col-md-7 col-sm-7 col-xs-12">
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                    <label>Email Address</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CST_Email; ?>">
                  </div>


                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                    <label>Phone Number</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $CST_mobileNo; ?>">
                  </div>
                </div>


                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                    <label>Physical Address</label>
                    <textarea class="form-control" disabled=""><?php echo $CST_Physical_Address; ?></textarea>
                  </div>
                </div>
                  
              </div>
            </div>
          </div>
         
          <div class="col-md-6 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION C: </strong> Customer Documents
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

                <?php
                if (sizeof($client_doc_list)>0) {
                  ?>
                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr valign="top">
                        <th>#</th>
                        <th>Document</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      for ($i=0; $i < sizeof($client_doc_list); $i++) { 
                        $doc = array();
                        $doc = $client_doc_list[$i];
                        $doc_id = $doc["id"];
                        $doc_parentEntityType = $doc["parentEntityType"];
                        $doc_parentEntityId = $doc["parentEntityId"];
                        $doc_name = $doc["name"];
                        $doc_fileName = $doc["fileName"];
                        $doc_size = $doc["size"];
                        $doc_type = $doc["type"];
                        $doc_description = $doc["description"];

                        $CLIENT_ID = $doc_parentEntityId;
                        $DOCUMENT_ID = $doc_id;
                        $doc_url = FetchDownloadClientDocument_Walkin($CLIENT_ID, $DOCUMENT_ID, $MIFOS_CONN_DETAILS)
                        ?>
                        <tr valign="top">
                          <td><?php echo ($i+1); ?>. </td>
                          <td><?php echo $doc_description; ?></td>
                          <td>
                            <a href="<?php echo $doc_url; ?>" class="btn btn-primary btn-xs">View</a>
                          </td>
                        </tr>
                        <?php
                      }
                     ?>
                   </tbody>
                  </table>
                  <?php
                } else {
                  ?>
                  <br>
                  No client documents
                  <?php
                }
                ?>
                               
              </div>
            </div>
          </div>

          <div class="col-md-6 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <strong>SECTION D: </strong> System Remarks
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

               <table class="table table-striped table-bordered">
                 <tr><th width="65%">Loan Product Selected</th><td><?php echo $pdt_name; ?></td></tr>
                 <tr><th>Is Phone Available?</th><td><?php echo $SYS_RMKS["MOB"]; ?></td></tr>
                 <tr><th>Is Email Available?</th><td><?php echo $SYS_RMKS["EMM"]; ?></td></tr>
                 <tr><th>Is Physical Address Supplied?</th><td><?php echo $SYS_RMKS["PHY"]; ?></td></tr>
                 <tr><th>Does client have photo?</th><td><?php echo $SYS_RMKS["IMG"]; ?></td></tr>
                 <tr><th>Does client have supportive documents?</th><td><?php echo $SYS_RMKS["DOC"]; ?></td></tr>
                 <tr><th colspan="2"></th></tr>
                 <tr><th colspan="2"><span style="text-align: center;">GENERAL REMARK</span> </th></tr>
                 <tr><td colspan="2"><?php echo $SYS_RMKS["GEN_MSG"]; ?></td></tr>
                 <tr><td colspan="2">
                  <?php
                  if ($SYS_RMKS["PROC"]=="OK") {
                    ?>

                    <a href="la-new-appln-walkin3?k=<?php echo $pdt_id."-".$pdt_name."-".$CUST_CORE_ID; ?>" class="btn btn-success btn-sm pull-right">Continue</a>
                    <?php
                  } elseif ($SYS_RMKS["PROC"]=="ERR") {
                    echo "Cannot proceed with loan application until all items above are available. Seek assistance from support";
                  }
                  ?>
                 </td></tr>
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
