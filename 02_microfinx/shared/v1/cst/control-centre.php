<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

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

        	<!-- --- --- --- --- PANEL 01 -- --- --- --- -- -- -->
        	<!-- --- --- --- --- PANEL 01 -- --- --- --- -- -- -->
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Welcome, <?php echo $firstname; ?></h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">         
              	You can manage your account through the features provided below and aside.
              </div>
            </div>
          </div>

          <!-- --- --- --- --- PANEL 01 -- --- --- --- -- -- -->
        	<!-- --- --- --- --- PANEL 01 -- --- --- --- -- -- -->
          <div class="col-md-4 col-sm-4 col-xs-6">
          	<div class="x_panel">
              <div class="x_title">
                <h2>My Accounts</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">         
              	<ol>
              		<li><a href="my-accounts">Click Here</a></li>
              	</ol>
              </div>
            </div>

            <div class="x_panel">
              <div class="x_title">
                <h2>Loan Application</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">         
              	<ol>
              		<li><a href="la-new-appln">Make New Appln</a></li>
				          <li><a href="la-res-appln">Resume Appln</a></li>
				          <li><a href="la-pending-appln">Pending Applns</a></li>
				          <li><a href="la-loan-recom">Loan Recommendations</a></li>
				          <li><a href="la-loan-grrt">Loan Guaranting</a></li>
				          <li><a href="la-prv-appln">Previous Applns</a></li>
              	</ol>
              </div>
            </div>
          </div>   



          <!-- --- --- --- --- PANEL 02 -- --- --- --- -- -- -->
        	<!-- --- --- --- --- PANEL 02 -- --- --- --- -- -- -->
          <div class="col-md-4 col-sm-4 col-xs-6">
            <div class="x_panel">
              <div class="x_title">
                <h2>Savings Application</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">         
              	<ol>
              		<li><a href="svg-appln-withdraw">Withdraw Appln</a></li>
				          <li><a href="svg-appln-deposit">Deposit Appln</a></li>
				          <li><a href="svg-appln-transfer">Transfer Appln</a></li>
              	</ol>
              </div>
            </div>

            <div class="x_panel">
              <div class="x_title">
                <h2>Shares Application</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">         
              	<ol>
              		<li><a href="shares-appln-buy">Buy Shares Appln</a></li>
              	</ol>
              </div>
            </div>
          </div>  


          <!-- --- --- --- --- PANEL 03 -- --- --- --- -- -- -->
        	<!-- --- --- --- --- PANEL 03 -- --- --- --- -- -- -->
          <div class="col-md-4 col-sm-4 col-xs-6">
            <div class="x_panel">
              <div class="x_title">
                <h2>Internal Notifications</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">         
              	<ol>
			          	<li><a href="nt-send-msg">Send Message</a></li>
			          	<li><a href="nt-inbox">Inbox <span id="inbox" class="badge bg-blue pull-right"></span></a></li>
			            <li><a href="nt-sent-messages">Sent Messages</a></li>
			          	<li><a href="nt-trash">Trash</a></li>
              	</ol>
              </div>
            </div>

            <div class="x_panel">
              <div class="x_title">
                <h2>My Profile</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">         
              	<ol>
			          	<li><a href="prof-bio-data">Bio Data</a></li>
          				<li><a href="prof-bank-accts">Bank Accounts</a></li>
              	</ol>
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
