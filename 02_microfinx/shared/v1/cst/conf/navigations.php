<?php
# ... F1: Top Admin Navigation menu ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function TopNavBar($cust_first_name)
{
	?>
	<div class="top_nav">
    <div class="nav_menu">
      <nav>
        <div class="nav toggle">
          <a id="menu_toggle"><i class="fa fa-bars"></i></a>
        </div>


        <ul class="nav navbar-nav navbar-right">
          <li class="">
            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              <?php echo strtoupper($cust_first_name); ?>
              <span class=" fa fa-angle-down"></span>
            </a>
            <ul class="dropdown-menu dropdown-usermenu pull-right">
              <li><a href="javascript:;">Profile</a></li>
              <li><a href="logout-cst"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
            </ul>
          </li>
          <li role="presentation" class="dropdown">
            <a href="nt-inbox" class="dropdown-toggle info-number">
              <i class="fa fa-envelope-o"></i>
              <span id="inbox_top" class="badge bg-green"></span>
            </a>
            <!--<ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
              <li>
                <a>
                  <span class="image"><img src="images/img.jpg" alt="Profile Image"></span>
                  <span>
                    <span>John Smith</span>
                    <span class="time">3 mins ago</span>
                  </span>
                  <span class="message">
                    Film festivals used to be do-or-die moments for movie makers. They were where...
                  </span>
                </a>
              </li>
              <li>
                <a>
                  <span class="image"><img src="images/img.jpg" alt="Profile Image"></span>
                  <span>
                    <span>John Smith</span>
                    <span class="time">3 mins ago</span>
                  </span>
                  <span class="message">
                    Film festivals used to be do-or-die moments for movie makers. They were where...
                  </span>
                </a>
              </li>
              <li>
                <div class="text-center">
                  <a>
                    <strong>See All Messages</strong>
                    <i class="fa fa-angle-right"></i>
                  </a>
                </div>
              </li>
            </ul>-->
          </li>
          <li class="">
            <a href="#" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              TIMER: <label id="countdown"></label>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
	<?php
}


# ... F1: Top Admin Navigation menu ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function SideNavBar($user_id)
{
	?>
	<div class="menu_section">
    <ul class="nav side-menu">
    	<li><a href="control-centre"><i class="fa fa-home"></i> Home</a></li>
    	<li><a href="my-accounts"><i class="fa fa-calculator"></i> My Accounts</a></li>

    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- Loan Applications  --- --- --- --- --- --- --- --- --- --- --- --- --- -->
  		<li><a><i class="fa fa-users"></i> Loan Applications<span class="fa fa-chevron-down"></span></a>
      	<ul class="nav child_menu">
          <li><a href="la-new-appln">Make New Appln</a></li>
          <li><a href="la-res-appln">Resume Appln</a></li>
          <li><a href="la-pending-appln">Pending Applns</a></li>
          <li><a href="la-loan-recom">Loan Recommendations</a></li>
          <li><a href="la-loan-grrt">Loan Guaranting</a></li>
          <li><a href="la-prv-appln">Previous Applns</a></li>
      	</ul>
    	</li>
    	

    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- SAVINGS APPLNS -- --- --- --- --- --- --- --- --- --- --- -->
		 	<li><a><i class="fa fa-money"></i> Savings Applications<span class="fa fa-chevron-down"></span></a>
      	<ul class="nav child_menu">
          <li><a href="svg-appln-withdraw">Withdraw Appln</a></li>
          <li><a href="svg-appln-deposit">Savings Deposit</a></li>
          <li><a href="svg-appln-transfer">Transfer Appln</a></li>

          <li><a href="#">Pending Applns <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="svg-appln-pending-with">Withdraw</a>
              <li><a href="svg-appln-pending-dep">Deposit</a>
              <li><a href="svg-appln-pending-trf">Transfer</a>
            </ul>
          </li>
          <li><a href="#">Previous Applns <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="svg-appln-prev-with">Withdraw</a>
              <li><a href="svg-appln-prev-dep">Deposit</a>
              <li><a href="svg-appln-prev-trf">Transfer</a>
            </ul>
          </li>
      	</ul>
    	</li>
			 

    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- SHARES MANAGEMENT -- --- --- --- --- --- --- --- --- --- --- --- -->
  		<li><a><i class="fa fa-line-chart"></i> Shares<span class="fa fa-chevron-down"></span></a>
      	<ul class="nav child_menu">
          <li><a href="shares-appln-buy">Buy Shares Appln</a></li>
          <li><a href="shares-appln-pending">Pending Applns</a></li>
          <li><a href="shares-appln-previous">Previous Applns</a></li>
      	</ul>
    	</li>
    	
  			
    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- NOTIFICATIONS -- --- --- --- --- --- --- --- --- --- --- --- -->
  		<li><a><i class="fa fa-envelope"></i> Notifications<span class="fa fa-chevron-down"></span></a>
      	<ul class="nav child_menu">
          	<li><a href="nt-send-msg">Send Message</a></li>
          	<li><a href="nt-inbox">Inbox <span id="inbox" class="badge bg-blue pull-right"></span></a></li>
            <li><a href="nt-sent-messages">Sent Messages</a></li>
          	<li><a href="nt-trash">Trash</a></li>
      	</ul>
    	</li>

    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- MY PROFILE -- --- --- --- --- --- --- --- --- --- --- --- -->
  		<li><a><i class="fa fa-gear"></i> My Profile<span class="fa fa-chevron-down"></span></a>
      	<ul class="nav child_menu">
          	<li><a href="prof-bio-data">Bio Data</a></li>
          	<li><a href="prof-bank-accts">Bank Accounts</a></li>
      	</ul>
    	</li>
   	
    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- LOG OUT -- --- --- --- --- --- --- --- --- --- --- -->
    	<li><a href="logout-cst"><i class="fa fa-sign-out"></i> Log Out</a></li>
    </ul>
	</div>
	<?php
}






?>