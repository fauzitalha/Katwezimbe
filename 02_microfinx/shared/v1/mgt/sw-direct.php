<?php
# ... Important Data
include("conf/session-checker.php");

# ... User-Data
include("usr-mgt.php");

# ... Handle Form Data
if (isset($_POST['btn_submit_appln'])) {
  
  $_SESSION['TXT_ACCT'] = mysql_real_escape_string(trim($_POST['TXT_ACCT']));
  $_SESSION['TO_CLIENT_ID'] = mysql_real_escape_string(trim($_POST['TO_CLIENT_ID']));
  $_SESSION['TO_ACCT_ID'] = mysql_real_escape_string(trim($_POST['TO_ACCT_ID']));
  $_SESSION['TO_ACCT_NUM'] = mysql_real_escape_string(trim($_POST['TO_ACCT_NUM']));
  $_SESSION['TO_ACCT_CRNCY'] = mysql_real_escape_string(trim($_POST['TO_ACCT_CRNCY']));
  $_SESSION['TO_ACCT_NAME'] = mysql_real_escape_string(trim($_POST['TO_ACCT_NAME']));
  $_SESSION['SVNGS_ACCT_BAL'] = mysql_real_escape_string(trim($_POST['SVNGS_ACCT_BAL']));
  $_SESSION['TRANSFER_AMT'] = mysql_real_escape_string(trim($_POST['TRANSFER_AMT']));
  $_SESSION['REASON'] = mysql_real_escape_string(trim($_POST['REASON']));




  $next_page = "sw-direct2";
  NavigateToNextPage($next_page); 
}

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Make Direct Withdraw", $APP_SMALL_LOGO); 

    # ... Javascript
    LoadPriorityJS();
    OnLoadExecutions();
    StartTimeoutCountdown();
    ExecuteProcessStatistics();
    ?>
    <script type="text/javascript">

      // ... 01:  InquireSvgsAcctDetails
      function InquireSvgsAcctDetails(){

        document.getElementById('MSG_AREA').innerHTML = '';
        $('#TO_CLIENT_ID').val("");
        $('#TO_ACCT_ID').val("");
        $('#TO_ACCT_NUM').val("");
        $('#TO_ACCT_NO_DISP').val("");
        $('#TO_ACCT_CRNCY').val("");
        $('#TO_ACCT_CRNCY_DISP').val("");
        $('#TO_ACCT_NAME').val("");
        $('#TO_ACCT_NAME_DISP').val("");

        var selected_val = document.getElementById('TXT_ACCT').value;

        //alert(selected_val);

        // ... Ajax
        $.ajax
        ({
          type:'post',
          url:'ajax-inquiry-svngs-acct-details.php',
          data:{
            account_no: selected_val
          },
          success:function(response) 
          {
            //console.log(response);

            // ... Handling of Db responses
            response = JSON.parse(response);
            var STATUS_CODE = response.STATUS_CODE;
            var STATUS_MESSAGE = response.STATUS_MESSAGE;

            if (STATUS_CODE=="OK") {

              var ACCT_ID = response.ACCT_ID;
              var ACCT_NUMBER = response.ACCT_NUMBER;
              var CRNCY = response.CRNCY;
              var CLIENT_ID = response.CLIENT_ID;
              var CLIENT_NAME = response.CLIENT_NAME;
              var ACCT_PDT_ID = response.ACCT_PDT_ID;
              var ACCT_PDT_NAME = response.ACCT_PDT_NAME;
              var ACCT_PDT_SHORT_NAME = response.ACCT_PDT_SHORT_NAME;
              var GROUP_ID = response.GROUP_ID;
              var STATUS_ENUM = response.STATUS_ENUM;


              $('#TO_CLIENT_ID').val(CLIENT_ID);
              $('#TO_ACCT_ID').val(ACCT_ID);
              $('#TO_ACCT_NUM').val(ACCT_NUMBER);
              $('#TO_ACCT_NO_DISP').val(ACCT_NUMBER);
              $('#TO_ACCT_CRNCY').val(CRNCY);
              $('#TO_ACCT_CRNCY_DISP').val(CRNCY);
              $('#TO_ACCT_NAME').val(CLIENT_NAME);
              $('#TO_ACCT_NAME_DISP').val(CLIENT_NAME);

              // ... Fetch Account Balance of the Account
              FetchSavingsAcctDetailsWithAcctID(ACCT_ID);

            }

            if (STATUS_CODE=="ERROR") {
              document.getElementById('MSG_AREA').innerHTML = "<span style='color: red;'>*** "+STATUS_MESSAGE+" ***</span>";
            }



          }

        });
      }

      // ... 02: Get Savings Acct Details
      function FetchSavingsAcctDetails() {
        
        $('#SVNGS_ACCT_BAL').val("");
        $('#SVNGS_ACCT_BAL_DISP').val("");
        var selected_val = document.getElementById('SVGS_ACCT_ID_TO_DEBIT').value;

        //alert(selected_val);

        // ... Ajax
        $.ajax
        ({
          type:'post',
          url:'ajax-fetch-savings_acct_details.php',
          data:{
            svngs_id: selected_val
          },
          success:function(response) 
          {
            //console.log(response);

            // ... Handling of Db responses
            response = JSON.parse(response)
            var SVNGS_ACCT_BAL = response.SVNGS_ACCT_BAL;
            var SVNGS_ACCT_BAL_JS = response.SVNGS_ACCT_BAL;
            var num= parseFloat(SVNGS_ACCT_BAL).toLocaleString('en');
            //console.log(num);
            //console.log(response);
            $('#SVNGS_ACCT_BAL').val(SVNGS_ACCT_BAL);
            $('#SVNGS_ACCT_BAL_DISP').val(num);
          }
        });
      }

      // ... 03: Validate Data
      function Validate() {
        var SVNGS_ACCT_BAL = document.getElementById('SVNGS_ACCT_BAL').value;
        var TRANSFER_AMT = document.getElementById('TRANSFER_AMT').value;
        
        var TRANS = parseFloat(TRANSFER_AMT);
        var SVSS = parseFloat(SVNGS_ACCT_BAL);

        if (TRANS==0) {
            alert("Zero values not allowed");
            return false;
        } else {
          if (TRANS>SVSS) {
              alert("Withdraw amount is greater than available balance");
              return false;
          } else {
              return true;
          }

        }       
      }

      // ... 04: FetchSavingsAcctDetailsWithAcctID
      function FetchSavingsAcctDetailsWithAcctID(ACCT_ID) {
        
        $('#SVNGS_ACCT_BAL').val("");
        $('#SVNGS_ACCT_BAL_DISP').val("");

        var selected_val = ACCT_ID;
        //var selected_val = document.getElementById('SVGS_ACCT_ID_TO_DEBIT').value;

        //alert(selected_val);

        // ... Ajax
        $.ajax
        ({
          type:'post',
          url:'ajax-fetch-savings_acct_details.php',
          data:{
            svngs_id: selected_val
          },
          success:function(response) 
          {
            //console.log(response);

            // ... Handling of Db responses
            response = JSON.parse(response)
            var SVNGS_ACCT_BAL = response.SVNGS_ACCT_BAL;
            var SVNGS_ACCT_BAL_JS = response.SVNGS_ACCT_BAL;
            var num= parseFloat(SVNGS_ACCT_BAL).toLocaleString('en');
            //console.log(num);
            //console.log(response);
            $('#SVNGS_ACCT_BAL').val(SVNGS_ACCT_BAL);
            $('#SVNGS_ACCT_BAL_DISP').val(num);
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
            <div id="MSG_AREA" align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <h2>Make Direct Withdraw of Funds from Customer</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         

                <form method="post" id="dmsEEAjj" onsubmit="return Validate(this)">
                  
                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Enter Account Number to Withdraw From:</label>
                    <div class="input-group">
                      <input type="number" id="TXT_ACCT" name="TXT_ACCT" class="form-control" required="">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" onclick="InquireSvgsAcctDetails()">Inquire Acct</button>
                      </span>
                    </div>
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Account No:</label>
                    <input type="hidden" id="TO_CLIENT_ID" name="TO_CLIENT_ID">
                    <input type="hidden" id="TO_ACCT_ID" name="TO_ACCT_ID">
                    <input type="hidden" id="TO_ACCT_NUM" name="TO_ACCT_NUM">
                    <input type="text" id="TO_ACCT_NO_DISP" name="TO_ACCT_NO_DISP" class="form-control" disabled="">
                  </div>

                  <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                    <label>Currency:</label>
                    <input type="hidden" id="TO_ACCT_CRNCY" name="TO_ACCT_CRNCY">
                    <input type="text" id="TO_ACCT_CRNCY_DISP" name="TO_ACCT_CRNCY_DISP" class="form-control" disabled="">
                  </div>

                  <div class="col-md-5 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Account Name:</label>
                    <input type="hidden" id="TO_ACCT_NAME" name="TO_ACCT_NAME">
                    <input type="text" id="TO_ACCT_NAME_DISP" name="TO_ACCT_NAME_DISP" class="form-control" disabled="">
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Account Balance:</label>
                    <input type="hidden" id="SVNGS_ACCT_BAL" name="SVNGS_ACCT_BAL">
                    <input type="text" id="SVNGS_ACCT_BAL_DISP" name="SVNGS_ACCT_BAL_DISP" class="form-control" disabled="">
                  </div>


                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Amount to Withdraw:</label>
                    <input type="number" id="TRANSFER_AMT" name="TRANSFER_AMT" class="form-control" required="">
                  </div>


                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Withdraw Narration</label>
                    <textarea class="form-control" rows="3" name="REASON" id="REASON" required=""></textarea>
                  </div>

                  
                   <div class="col-md-12 col-sm-12 col-xs-12 form-group">

                    
                    <button type="submit" class="btn btn-success" name="btn_submit_appln">Submit Appln Details</button>
                     
                  </div>
                </form>




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
