<?php
# ... F1: Top Admin Navigation menu ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function TopNavBar($user_id, $user_full_names, $user_role_name)
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
              <?php echo strtoupper($user_full_names); ?>
              <span class=" fa fa-angle-down"></span>
            </a>
            <ul class="dropdown-menu dropdown-usermenu pull-right">
              <li><a href="javascript:;">Profile</a></li>
              <li><a href="logout-user"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
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
function SideNavBar($user_id, $user_roles)
{
	?>
	<div class="menu_section">
    <ul class="nav side-menu">
    	<li><a href="main-dashboard"><i class="fa fa-home"></i> Home </a></li>

    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- CLIENT MGT  --- --- --- --- --- --- --- --- --- --- --- --- --- -->
    	<?php
    	if ( ($user_roles['F1']=="YES")||($user_roles['F2']=="YES")||($user_roles['F3']=="YES") ) {
    		?>
    		<li><a><i class="fa fa-users"></i> Client Mgt<span class="fa fa-chevron-down"></span></a>
	      	<ul class="nav child_menu">

      			<?php
	      			# ... Withdraw Applications
			    		if ( ($user_roles['F1']=="YES")||($user_roles['F54']=="YES")||($user_roles['F55']=="YES") ) {
			    			?>
			    			<li><a> Enrollments <span class="fa fa-chevron-down"></span></a>
		      		  	<ul class="nav child_menu">
		      		  		<?php if ($user_roles['F1']=="YES") {?><li><a href="cm-new-self-enrollments">New Applns <span id="New_Self_Enrollments" class="badge bg-blue pull-right"></span></a></li><?php } ?>
				          	<?php if ($user_roles['F54']=="YES") {?><li><a href="cm-applns-for-review">Applns 4 Review <span id="Applns_4_Review" class="badge bg-red pull-right"></span></a></li><?php } ?>
				          	<?php if ($user_roles['F55']=="YES") {?><li><a href="cm-apprv-applns">Approve Applns <span id="Approve_Applns" class="badge bg-purple pull-right"></span></a></li><?php } ?>
				          	<?php if ($user_roles['F56']=="YES") {?><li><a href="cm-finalize-enrollment">Finalize Enrollment <span id="Finalize_Enrollment" class="badge bg-green pull-right"></span></a></li><?php } ?>
				          	<?php if ($user_roles['F57']=="YES") {?><li><a href="cm-appln-reports">Appln Report</a></li><?php } ?>
		      		  	</ul>
		      		  </li>
			    			<?php
			    		}
			    	?>

	          <?php if ($user_roles['F2']=="YES") {?><li><a href="cm-client-list">Client List</a></li> <?php } ?>
            <?php if ($user_roles['F3']=="YES") {?><li><a href="cm-customer-updates">Customer Updates <span id="Customer_Updates" class="badge bg-amber pull-right"></span></a></li><?php } ?>
            <?php if ($user_roles['F64']=="YES") {?><li><a href="cm-customer-updates-apprv">Approve Client Updates</a></li><?php } ?>

	      	</ul>
	    	</li>
    		<?php
    	}
    	?>
	    
    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- LOAN APPLICATIONS -- --- --- --- --- --- --- --- --- --- --- --- -->
    	<?php
    	if ( ($user_roles['F4']=="YES")||($user_roles['F5']=="YES")||($user_roles['F6']=="YES") || 
    			 ($user_roles['F7']=="YES")||($user_roles['F7']=="YES")||($user_roles['F9']=="YES") ) {
			 	?>
			 	<li><a><i class="fa fa-bank"></i> Loan Applns Mgt<span class="fa fa-chevron-down"></span></a>
	      	<ul class="nav child_menu">
	      			<?php if ($user_roles['F4']=="YES") {?><li><a>Walk In<span class="fa fa-chevron-down"></span></a>
	      				<ul class="nav child_menu">
	      					<?php if ($user_roles['F4']=="YES") {?><li><a href="la-new-appln-walkin0">Make Loan Appln</a></li><?php } ?>
	      					<?php if ($user_roles['F4']=="YES") {?><li><a href="la-res-appln-walkin">Resume Loan Appln</a></li><?php } ?>
	      				</ul>
	      			</li><?php } ?>
	          	<?php if ($user_roles['F4']=="YES") {?><li><a href="la-queue">Loan Applns Queue <span id="Loan_Applns_Queue" class="badge bg-blue pull-right"></span></a></li><?php } ?>
	          	<?php if ($user_roles['F5']=="YES") {?><li><a href="la-cc">Credit Committee <span id="Credit_Committee" class="badge bg-purple pull-right"></span></a></li><?php } ?>
	          	<?php if ($user_roles['F6']=="YES") {?><li><a href="la-review">Review Applns <span id="Review_Applns" class="badge bg-yellow pull-right"></span></a></li><?php } ?>
	          	<?php if ($user_roles['F7']=="YES") {?><li><a href="la-disburse">Loan Disbursement <span id="Loan_Disbursement" class="badge bg-green pull-right"></span></a></li><?php } ?>
	          	<?php if ($user_roles['F8']=="YES") {?><li><a href="la-get-disbursed-list">Get Disbursed Loan List</a></li><?php } ?>
	          	<?php if ($user_roles['F9']=="YES") {?><li><a href="la-prev-applns">Previous Applns</a></li><?php } ?>
	      	</ul>
	    	</li>
			 <?php
    	}
    	?>


    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- SAVINGS APPLNS MGT -- --- --- --- --- --- --- --- --- --- --- -->
    	<?php
    	if ( ($user_roles['F10']=="YES")||($user_roles['F11']=="YES")||($user_roles['F12']=="YES") || 
    			 ($user_roles['F13']=="YES")||($user_roles['F14']=="YES")||($user_roles['F15']=="YES") ||
    			 ($user_roles['F16']=="YES") ) {
			 	?>
			 	<li><a><i class="fa fa-money"></i> Savings Applns Mgt<span class="fa fa-chevron-down"></span></a>
	      	<ul class="nav child_menu">

	      			<?php
	      			# ... Withdraw Applications
			    		if ( ($user_roles['F10']=="YES")||($user_roles['F11']=="YES")||($user_roles['F12']=="YES") || 
			    			 ($user_roles['F13']=="YES") ) {
			    			?>
			    			<li><a><i class="fa fa-plus-square-o"></i> Withdraws <span class="fa fa-chevron-down"></span></a>
		      		  	<ul class="nav child_menu">
		      		  		<?php if ($user_roles['F10']=="YES") {?><li><a href="sw-queue">Pending Applns <span id="Pending_Applns_Withdraws" class="badge bg-blue pull-right"></span></a></li><?php } ?>
				          	<?php if ($user_roles['F11']=="YES") {?><li><a href="sw-apprv">Approve Withdraw <span id="Approve_Withdraw" class="badge bg-green pull-right"></span></a></li><?php } ?>
				          	<?php if ($user_roles['F12']=="YES") {?><li><a href="sw-apprvd-list">Approved Withdraw List</a></li><?php } ?>
                    <?php if ($user_roles['F13']=="YES") {?><li><a href="sw-prev-list">Previous Applns</a></li><?php } ?>
		      		  	</ul>
		      		  </li>
			    			<?php
			    		}
			    
	      		  # ... Deposit Applications
			    		if ( ($user_roles['F14']=="YES")||($user_roles['F15']=="YES") || ($user_roles['F16']=="YES") ) {
			    			?>
			    			<li><a><i class="fa fa-plus-square-o"></i> Deposits <span class="fa fa-chevron-down"></span></a>
		      		  	<ul class="nav child_menu">
		      		  		<?php if ($user_roles['F14']=="YES") {?><li><a href="sd-queue">Pending Applns <span id="Pending_Applns_Deposits" class="badge bg-blue pull-right"></span></a></li><?php } ?>
				          	<?php if ($user_roles['F15']=="YES") {?><li><a href="sd-apprv">Approve Deposit <span id="Approve_Deposit" class="badge bg-green pull-right"></span></a></li><?php } ?>
                    <?php if ($user_roles['F16']=="YES") {?><li><a href="sd-prev-applns">Previous Applns</a></li><?php } ?>
		      		  	</ul>
		      		  </li>
			    			<?php
			    		}

			    		# ... Transfer Applications
			    		if ( ($user_roles['F61']=="YES")||($user_roles['F62']=="YES") || ($user_roles['F63']=="YES") ) {
			    			?>
			    			<li><a><i class="fa fa-plus-square-o"></i> Transfers <span class="fa fa-chevron-down"></span></a>
		      		  	<ul class="nav child_menu">
		      		  		<?php if ($user_roles['F61']=="YES") {?><li><a href="st-queue">Pending Applns <span id="Pending_Applns_Deposits" class="badge bg-blue pull-right"></span></a></li><?php } ?>
				          	<?php if ($user_roles['F62']=="YES") {?><li><a href="st-apprv">Approve Transfer <span id="Approve_Deposit" class="badge bg-green pull-right"></span></a></li><?php } ?>
				          	<?php if ($user_roles['F63']=="YES") {?><li><a href="st-prev-applns">Previous Applns</a></li><?php } ?>
		      		  	</ul>
		      		  </li>
			    			<?php
			    		}
	      			?>
	      	</ul>
	    	</li>
			  <?php
    	}
    	?>
	    

    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- SHARES MANAGEMENT -- --- --- --- --- --- --- --- --- --- --- --- -->
    	<?php
    	if ( ($user_roles['F17']=="YES")||($user_roles['F18']=="YES")||($user_roles['F19']=="YES")||($user_roles['F20']=="YES") ) {
    		?>
    		<li><a><i class="fa fa-line-chart"></i> Shares Mgt<span class="fa fa-chevron-down"></span></a>
	      	<ul class="nav child_menu">
	          	<!--<?php //if ($user_roles['F17']=="YES") {?><li><a href="#">Shares Market</a></li><?php //} ?>-->
	          	<?php if ($user_roles['F18']=="YES") {?><li><a href="shr-queue">Pending Shares Request <span id="Pending_Shares_Request" class="badge bg-blue pull-right"></span></a></li><?php } ?>
	          	<?php if ($user_roles['F19']=="YES") {?><li><a href="shr-apprv">Approve Shares Request <span id="Approve_Shares_Request" class="badge bg-green pull-right"></span></a></li><?php } ?>
	          	<?php if ($user_roles['F20']=="YES") {?><li><a href="shr-prev">Previous Requests</a></li><?php } ?>
	      	</ul>
	    	</li>
    		<?php
    	}
    	?>
			    

    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- TRANSACTIONS MGT -- --- --- --- --- --- --- --- --- --- --- -->
    	<?php
    	if ( ($user_roles['F21']=="YES")||($user_roles['F22']=="YES")||($user_roles['F23']=="YES") || 
    			 ($user_roles['F24']=="YES")||($user_roles['F25']=="YES")||($user_roles['F26']=="YES") || 
    			 ($user_roles['F27']=="YES")||($user_roles['F28']=="YES")||($user_roles['F29']=="YES") ) {

  			?>
  			<li><a><i class="fa fa-gbp"></i> Bulk Txns Mgt<span class="fa fa-chevron-down"></span></a>
	      	<ul class="nav child_menu">
  		  		<?php if ($user_roles['F21']=="YES") {?><li><a href="blk-file-templates">Pymt File Templates</a></li><?php } ?>
          	<?php if ($user_roles['F22']=="YES") {?><li><a href="blk-upld-file">Upload Pymt File</a></li><?php } ?>
          	<?php if ($user_roles['F23']=="YES") {?><li><a href="blk-vrff-file">Verify Pymt File<span id="Verify_Pymt_Schedule" class="badge bg-blue pull-right"></span></a></li><?php } ?>
            <?php if ($user_roles['F24']=="YES") {?><li><a href="blk-apprv-file">Approve Pymt File<span id="Approve_Pymt_Schedule" class="badge bg-green pull-right"></span></a></li><?php } ?>
            <?php if ($user_roles['F25']=="YES") {?><li><a href="blk-rev-file">Reverse Pymt File</a></li><?php } ?>
          	<?php if ($user_roles['F26']=="YES") {?><li><a href="blk-prev-uplds">View Prevoius Uploads</a></li><?php } ?>
	      	</ul>
	    	</li>
  			<?php
    	}
    	?>
	    
    	
    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- NOTIFICATIONS -- --- --- --- --- --- --- --- --- --- --- --- -->
    	<?php
    	if ( ($user_roles['F30']=="YES")||($user_roles['F31']=="YES")||($user_roles['F32']=="YES")||($user_roles['F33']=="YES") ) {
    		?>
    		<li><a><i class="fa fa-envelope"></i> Notifications<span class="fa fa-chevron-down"></span></a>
	      	<ul class="nav child_menu">
	          	<?php if ($user_roles['F30']=="YES") {?><li><a href="nt-send-msg">Send Message</a></li><?php } ?>
	          	<?php if ($user_roles['F31']=="YES") {?><li><a href="nt-inbox">Inbox <span id="inbox" class="badge bg-blue pull-right"></span></a></li><?php } ?>
	          	<?php if ($user_roles['F33']=="YES") {?><li><a href="nt-sent-messages">Sent Messages</a></li><?php } ?>
	          	<?php if ($user_roles['F32']=="YES") {?><li><a href="nt-trash">Trash</a></li><?php } ?>
	          	<?php// if ($user_roles['F52']=="YES") {?><!--<li><a href="feed-sms">SMS Feed</a></li>--><?php// } ?>
	          	<?php //if ($user_roles['F53']=="YES") {?><!--<li><a href="feed-email">Email Feed</a></li>--><?php //} ?>
	      	</ul>
	    	</li>
    		<?php
    	}
    	?>
	    

    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- USER MGT -- --- --- --- --- --- --- --- --- --- --- --- -->
    	<?php
    	if ( ($user_roles['F34']=="YES")||($user_roles['F35']=="YES")||($user_roles['F36']=="YES")||($user_roles['F37']=="YES") ) {
    		?>
    		<li><a><i class="fa fa-user"></i> User Mgt<span class="fa fa-chevron-down"></span></a>
	      	<ul class="nav child_menu">
	          	<?php if ($user_roles['F34']=="YES") {?><li><a href="usr-create">Create New User</a></li><?php } ?>
	          	<?php if ($user_roles['F35']=="YES") {?><li><a href="usr-verify">Verify New User <span id="Verify_New_User" class="badge bg-green pull-right"></span></a></li><?php } ?>
	          	<?php if ($user_roles['F36']=="YES") {?><li><a href="usr-view">View all Users</a></li><?php } ?>
	          	<?php if ($user_roles['F37']=="YES") {?><li><a href="usr-verify-update">Verify User Updates <span id="Verify_User_Updates" class="badge bg-white pull-right"></span></a></li><?php } ?>
	      	</ul>
	    	</li>
    		<?php
    	}
    	?>			    

    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- SYSTEM SETTINGS -- --- --- --- --- --- --- --- --- --- --- -->
    	<?php
    	if ( ($user_roles['F38']=="YES")||($user_roles['F39']=="YES")||($user_roles['F40']=="YES") || 
    			 ($user_roles['F41']=="YES")||($user_roles['F42']=="YES")||($user_roles['F43']=="YES") || 
    			 ($user_roles['F44']=="YES")||($user_roles['F45']=="YES")||($user_roles['F46']=="YES") || 
    			 ($user_roles['F47']=="YES")||($user_roles['F48']=="YES")||($user_roles['F49']=="YES") || 
    			 ($user_roles['F50']=="YES")||($user_roles['F51']=="YES") ) {
    			?>
    			<li><a><i class="fa fa-cog"></i> System Settings<span class="fa fa-chevron-down"></span></a>
		      	<ul class="nav child_menu">

		      			<?php
		      			# ... Cust Applns
		      			if ( ($user_roles['F38']=="YES")||($user_roles['F39']=="YES")||($user_roles['F40']=="YES")||($user_roles['F51']=="YES") ) {
		      				?>
		      				<li><a><i class="fa fa-plus-square-o"></i> Cust Applns<span class="fa fa-chevron-down"></span></a>
			      		  	<ul class="nav child_menu">
			      		  		<?php if ($user_roles['F38']=="YES") {?><li><a href="applns-configs-new">Configure Applns</a></li><?php } ?>
			      		  		<!--<?php //if ($user_roles['F39']=="YES") {?><li><a href="applns-configs-apprv">Apprv Appln Configs <span id="Apprv_Appln_Configs" class="badge bg-blue pull-right"></span></a></li><?php //} ?>-->
			      		  		<?php if ($user_roles['F59']=="YES") {?><li><a href="applns-configs-view">View Appln Configs</a></li><?php } ?>
			      		  		<!--<?php //if ($user_roles['F40']=="YES") {?><li><a href="applns-configs-apprv-update">Apprv Config Update <span id="Apprv_Config_Update" class="badge bg-white pull-right"></span></a></li><?php //} ?>-->
			      		  		<?php if ($user_roles['F51']=="YES") {?><li><a href="appln-mgt-grps">Applns Mgt Groups</a></li><?php } ?>
			      		  		<!--<?php //if ($user_roles['F60']=="YES") {?><li><a href="appln-prcss-wrkflws">Process Workflows</a></li><?php //} ?>-->
			      		  	</ul>
			      		  </li>
		      				<?php
		      			}
			     
		      			# ... Transactions
		      			if ( ($user_roles['F41']=="YES")||($user_roles['F42']=="YES")||($user_roles['F43']=="YES") ) {
		      				?>
		      				<li><a><i class="fa fa-plus-square-o"></i> Transactions <span class="fa fa-chevron-down"></span></a>
			      		  	<ul class="nav child_menu">
			      		  		<?php if ($user_roles['F41']=="YES") {?><li><a href="tt-types">Tran Types</a></li><?php } ?>
			      		  		<?php if ($user_roles['F42']=="YES") {?><li><a href="tt-charges">Tran Charges</a></li><?php } ?>
			      		  		<?php if ($user_roles['F43']=="YES") {?><li><a href="tt-charge-events">Tran Charge Events</a></li><?php } ?>
			      		  	</ul>
			      		  </li> 
		      				<?php
		      			}
			    
		      			# ... Notifications
		      			if ( ($user_roles['F44']=="YES") ) {
		      				?>
		      				<li><a><i class="fa fa-plus-square-o"></i> Notifications <span class="fa fa-chevron-down"></span></a>
			      		  	<ul class="nav child_menu">
			      		  		<?php if ($user_roles['F44']=="YES") {?><li><a href="nts-mng-grps">Manage Groups</a></li><?php } ?>
			      		  		<?php if ($user_roles['F58']=="YES") {?><li><a href="nts-addrss-bk">Address Book</a></li><?php } ?>
			      		  	</ul>
			      		  </li> 
		      				<?php
		      			}

		      			# ... Role Mgt
		      			if ( ($user_roles['F45']=="YES")||($user_roles['F46']=="YES")||($user_roles['F47']=="YES")||($user_roles['F48']=="YES") ) {
		      				?>
		      				<li><a><i class="fa fa-plus-square-o"></i> Role Mgt <span class="fa fa-chevron-down"></span></a>
			      		  	<ul class="nav child_menu">
			      		  		<?php if ($user_roles['F45']=="YES") {?><li><a href="roles-create">Create New Role</a></li><?php } ?>
			      		  		<?php if ($user_roles['F46']=="YES") {?><li><a href="roles-apprv">Approve New Role <span id="Approve_New_Role" class="badge bg-white pull-right"></span></a></li><?php } ?>
			      		  		<?php if ($user_roles['F47']=="YES") {?><li><a href="roles-view">View Roles</a></li><?php } ?>
			      		  		<?php if ($user_roles['F48']=="YES") {?><li><a href="roles-apprv-update">Apprv Role Update <span id="Apprv_Role_Update" class="badge bg-blue pull-right"></span></a></li><?php } ?>
			      		  	</ul>
			      		  </li>
		      				<?php
		      			}

		      			# ... General Settings
		      			//if ( ($user_roles['F49']=="YES")||($user_roles['F50']=="YES") ) {
		      				?>
		      				 <!--<li><a><i class="fa fa-plus-square-o"></i> General Settings <span class="fa fa-chevron-down"></span></a>
			      		  	<ul class="nav child_menu">
			      		  		<?php // if ($user_roles['F49']=="YES") {?><li><a href="#">View Settings</a></li><?php //} ?>
			      		  		<?php //if ($user_roles['F50']=="YES") {?><li><a href="#">Apprv Settings Changes <span id="Apprv_Settings_Changes" class="badge bg-blue pull-right"></span></a></li><?php //} ?>
			      		  	</ul>
			      		  </li> -->
		      				<?php
		      			//}
		      			?>
		      	</ul>
		    	</li>
    			<?php
    	}
    	?>

	      	
    	<!-- --- --- --- --- --- --- --- --- --- --- --- --- LOG OUT -- --- --- --- --- --- --- --- --- --- --- -->
    	<li><a href="logout-user.php"><i class="fa fa-sign-out"></i> Log Out</a></li>
    </ul>
	</div>
	<?php
}

?>