<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");


# .... Receiving Data
$DEPOSIT_REF = $_SESSION['DEPOSIT_REF'];
$SVGS_ACCT_ID_TO_CREDIT = $_SESSION['SVGS_ACCT_ID_TO_CREDIT'];
$SVGS_ACCT_NUM_TO_CREDIT = $_SESSION['SVGS_ACCT_NUM_TO_CREDIT'];
$AMOUNT_BANKED = $_SESSION['AMOUNT_BANKED'];
$REASON = $_SESSION['REASON'];
$BANK_ID = $_SESSION['BANK_ID'];
$FIN_INST_NAME = $_SESSION['FIN_INST_NAME'];
$BANK_INST_ACCT_NO = $_SESSION['BANK_INST_ACCT_NO'];
$BANK_INST_ACCT_NAME = $_SESSION['BANK_INST_ACCT_NAME'];
$BANK_RECEIPT_REF = $_SESSION['BANK_RECEIPT_REF'];
$RQST_DATE = $_SESSION['RQST_DATE'];

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Savings Deposit", $APP_SMALL_LOGO); 

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
              <?php SideNavBar($CUST_ID); ?>
            </div>
            <!-- /sidebar menu -->


          </div>
        </div>

        <!-- top navigation -->
        <?php TopNavBar($firstname); ?>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <a href="svg-appln-deposit" class="btn btn-dark btn-sm pull-left">Back</a>
                <h2>Savings Deposit Applns</h2>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">         


                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Appln Deposit Reference:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $DEPOSIT_REF; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Appln Date:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $RQST_DATE; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Savings Account to Credit:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $SVGS_ACCT_NUM_TO_CREDIT; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Amount to be deposited:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo number_format($AMOUNT_BANKED); ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Funds were deposited from:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $FIN_INST_NAME; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Bank Account No:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $BANK_INST_ACCT_NO; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Bank Account Name:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $BANK_INST_ACCT_NAME; ?>">
                  </div>

                  <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                    <label>Receipt Reference Number:</label>
                    <input type="text" class="form-control" disabled="" value="<?php echo $BANK_RECEIPT_REF; ?>">
                  </div>


                  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <label>Deposit Narration</label>
                    <textarea class="form-control" rows="3" disabled=""><?php echo $REASON; ?></textarea>
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
