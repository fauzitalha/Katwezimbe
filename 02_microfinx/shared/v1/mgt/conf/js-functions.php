<?php
# ... ... ... F1: LoadPriorityJS ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function LoadPriorityJS(){
	?>
	<script src="vendors/jquery.min.js"></script>
	<?php
}


# ... ... ... F2: Load Default JavaScript Configurations ... ... ... ... ... ... ... ... ... ... ... ...
function LoadDefaultJavaScriptConfigurations(){
	?>
	<!-- jQuery -->
  <script src="vendors/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- FastClick -->
  <script src="vendors/fastclick/lib/fastclick.js"></script>
  <!-- NProgress -->
  <script src="vendors/nprogress/nprogress.js"></script>
  <!-- iCheck -->
  <script src="vendors/iCheck/icheck.min.js"></script>
  <!-- Datatables -->
  <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
  <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
  <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
  <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
  <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
  <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
  <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
  <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
  <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
  <script src="vendors/datatables.net-scroller/js/datatables.scroller.min.js"></script>
  <script src="vendors/jszip/dist/jszip.min.js"></script>
  <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
  <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

  <!-- Custom Theme Scripts -->
  <script src="build/js/custom.min.js"></script>

  <!-- Datatables -->
  <script>
    $(document).ready(function() {
      var handleDataTableButtons = function() {
        if ($("#datatable-buttons").length) {
          $("#datatable-buttons").DataTable({
            dom: "Bfrtip",
            buttons: [
              {
                extend: "copy",
                className: "btn-sm"
              },
              {
                extend: "csv",
                className: "btn-sm"
              },
              {
                extend: "excel",
                className: "btn-sm"
              },
              {
                extend: "pdfHtml5",
                className: "btn-sm"
              },
              {
                extend: "print",
                className: "btn-sm"
              },
            ],
            responsive: true
          });
        }
      };

      TableManageButtons = function() {
        "use strict";
        return {
          init: function() {
            handleDataTableButtons();
          }
        };
      }();

      $('#datatable').dataTable();
      $('#datatable2').dataTable();
      $('#datatable3').dataTable();

      $('#blk_datatable_debits').DataTable( {
	        "paging":   false
	   } );

      $('#blk_datatable_credits').DataTable( {
	        "paging":   false
	   } );

      $('#blk_datatable_entry_list').DataTable( {
	        "paging":   false
	   } );

      

      $('#datatable-keytable').DataTable({
        keys: true
      });

      $('#datatable-responsive').DataTable();

      $('#datatable-scroller').DataTable({
        ajax: "js/datatables/json/scroller-demo.json",
        deferRender: true,
        scrollY: 380,
        scrollCollapse: true,
        scroller: true
      });

      $('#datatable-fixed-header').DataTable({
        fixedHeader: true
      });

      var $datatable = $('#datatable-checkbox');

      $datatable.dataTable({
        'order': [[ 1, 'asc' ]],
        'columnDefs': [
          { orderable: false, targets: [0] }
        ]
      });
      $datatable.on('draw.dt', function() {
        $('input').iCheck({
          checkboxClass: 'icheckbox_flat-green'
        });
      });

      TableManageButtons.init();
    });
  </script>
    <!-- /Datatables -->	
	<?php
}


# ... ... ... F3: Start Timeout Countdown ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
function StartTimeoutCountdown(){
	?>
	<script type="text/javascript">
			var timer2 = "15:02";
			var interval = setInterval(function() 
			{
			  var timer = timer2.split(':');		  
			  var minutes = parseInt(timer[0], 10);
			  var seconds = parseInt(timer[1], 10);

			  --seconds;
			  minutes = (seconds < 0) ? --minutes : minutes;

			  if (minutes < 0) {
			  	$.ajax
	   			({
	     			type:'post',
	     			url:'check-idle-session.php',
	     			data:{
	      			logout:"logout"
	     			},
	     			success:function(response) 
	     			{
	      			window.location="timeout-user.php";
	     			}
	   			});
			  }


			  seconds = (seconds < 0) ? 59 : seconds;
			  seconds = (seconds < 10) ? '0' + seconds : seconds;
			  $('#countdown').html(minutes + ':' + seconds);		
			  timer2 = minutes + ':' + seconds;
			}, 1000);


	</script>	
	<?php
}


# ... ... ... F4: Execute Process Statistics ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function ExecuteProcessStatistics(){
	?>
	<script type="text/javascript">

		// ... Interval
    var minutes = 1;
		var interval = 1000 * 60 * minutes;

		// ... Function Call
		var ajax_call = function() {
		  // ... Data Transfer Logic
	    var New_Self_Enrollments;
	    var Applns_4_Review;
	    var Approve_Applns;
	    var Finalize_Enrollment;
	    var Customer_Updates;
	    var Loan_Applns_Queue;
	    var Credit_Committee;
	    var Review_Applns;
	    var Loan_Disbursement;
	    var Pending_Applns_Withdraws;
	    var Approve_Withdraw;
	    var Pending_Applns_Deposits;
	    var Approve_Deposit;
	    var Pending_Shares_Request;
	    var Approve_Shares_Request;
	    var Verify_Pymt_Schedule;
	    var Approve_Pymt_Schedule;
	    var Transaction_Queue;
	    var Verify_Transaction;
	    var Approve_Reversal;
	    var inbox;
	    var Verify_New_User;
	    var Verify_User_Updates;
	    var Apprv_Appln_Configs;
	    //var Apprv_Config_Update;
	    var Apprv_Tran_Type;
	    var Apprv_TranType_Update;
	    var Approve_New_Role;
	    var Apprv_Role_Update;
	    var Apprv_Settings_Changes;

	    // ... Ajax Call
	  	$.ajax
 			({
   			type:'post',
   			url:'queue-stats.php',
   			data:{
    			get_count:"get_count"
   			},
   			success:function(response) 
   			{
   				//console.log(response);

    			// ... Handling of Db responses
    			response = JSON.parse(response)

    			New_Self_Enrollments = response.New_Self_Enrollments;
    			Applns_4_Review = response.Applns_4_Review;
    			Approve_Applns = response.Approve_Applns;
    			Finalize_Enrollment = response.Finalize_Enrollment;
			    Customer_Updates = response.Customer_Updates; 
			    Loan_Applns_Queue = response.Loan_Applns_Queue;
			    Credit_Committee = response.Credit_Committee;
			    Review_Applns = response.Review_Applns;
			    Loan_Disbursement = response.Loan_Disbursement;
			    Pending_Applns_Withdraws = response.Pending_Applns_Withdraws;
			    Approve_Withdraw = response.Approve_Withdraw;
			    Pending_Applns_Deposits = response.Pending_Applns_Deposits;
			    Approve_Deposit = response.Approve_Deposit;
			    Pending_Shares_Request = response.Pending_Shares_Request;
			    Approve_Shares_Request = response.Approve_Shares_Request;
			    Verify_Pymt_Schedule = response.Verify_Pymt_Schedule;
			    Approve_Pymt_Schedule = response.Approve_Pymt_Schedule;
			    Transaction_Queue = response.Transaction_Queue;
			    Verify_Transaction = response.Verify_Transaction;
			    Approve_Reversal = response.Approve_Reversal;
			    inbox = response.inbox;
			    Verify_New_User = response.Verify_New_User;
			    Verify_User_Updates = response.Verify_User_Updates;
			    Apprv_Appln_Configs = response.Apprv_Appln_Configs;
			    //Apprv_Config_Update = response.Apprv_Config_Update;
			    Apprv_Tran_Type = response.Apprv_Tran_Type;
			    Apprv_TranType_Update = response.Apprv_TranType_Update;
			    Approve_New_Role = response.Approve_New_Role;
			    Apprv_Role_Update = response.Apprv_Role_Update;
			    Apprv_Settings_Changes = response.Apprv_Settings_Changes;

			    // ... ... ... ... ... ... ... ... ... Displaying the Records to the User
	      	$('#New_Self_Enrollments').text(New_Self_Enrollments);
	      	$('#Applns_4_Review').text(Applns_4_Review);
	      	$('#Approve_Applns').text(Approve_Applns);
	      	$('#Finalize_Enrollment').text(Finalize_Enrollment);
			    $('#Customer_Updates').text(Customer_Updates);
			    $('#Loan_Applns_Queue').text(Loan_Applns_Queue);
			    $('#Credit_Committee').text(Credit_Committee);
			    $('#Review_Applns').text(Review_Applns);
			    $('#Loan_Disbursement').text(Loan_Disbursement);
			    $('#Pending_Applns_Withdraws').text(Pending_Applns_Withdraws);
			    $('#Approve_Withdraw').text(Approve_Withdraw);
			    $('#Pending_Applns_Deposits').text(Pending_Applns_Deposits);
			    $('#Approve_Deposit').text(Approve_Deposit);
			    $('#Pending_Shares_Request').text(Pending_Shares_Request);
			    $('#Approve_Shares_Request').text(Approve_Shares_Request);
			    $('#Verify_Pymt_Schedule').text(Verify_Pymt_Schedule);
			    $('#Approve_Pymt_Schedule').text(Approve_Pymt_Schedule);
			    $('#Transaction_Queue').text(Transaction_Queue);
			    $('#Verify_Transaction').text(Verify_Transaction);
			    $('#Approve_Reversal').text(Approve_Reversal);
			    $('#inbox').text(inbox);
			    $('#inbox_top').text(inbox);
			    $('#Verify_New_User').text(Verify_New_User);
			    $('#Verify_User_Updates').text(Verify_User_Updates);
			    $('#Apprv_Appln_Configs').text(Apprv_Appln_Configs);
			    //$('#Apprv_Config_Update').text(Apprv_Config_Update);
			    $('#Apprv_Tran_Type').text(Apprv_Tran_Type);
			    $('#Apprv_TranType_Update').text(Apprv_TranType_Update);
			    $('#Approve_New_Role').text(Approve_New_Role);
			    $('#Apprv_Role_Update').text(Apprv_Role_Update);
			    $('#Apprv_Settings_Changes').text(Apprv_Settings_Changes);
					    
   			}
 			});
		  
 			
		};

		// ... Executing the Interval
		setInterval(ajax_call, interval);
		
	</script>
	<?php
}


# ... ... ... F5: On Load Event ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... 
function OnLoadExecutions(){
	?>
	<script type="text/javascript">

		//$(doc).on("load", function () {
		$( document ).ready(function() {
			// ... ... ... ... ... ... ... ... ... ... ... ... 01: Menu Statistics ... ... ... ... ... ... ... ... ... ... ... ... //
			// ... ... ... ... ... ... ... ... ... ... ... ... 01: Menu Statistics ... ... ... ... ... ... ... ... ... ... ... ... //
			// ... ... ... ... ... ... ... ... ... ... ... ... 01: Menu Statistics ... ... ... ... ... ... ... ... ... ... ... ... //

			// ... Data Transfer Logic
	    var New_Self_Enrollments;
	    var Applns_4_Review;
	    var Approve_Applns;
	    var Finalize_Enrollment;
	    var Customer_Updates;
	    var Loan_Applns_Queue;
	    var Credit_Committee;
	    var Review_Applns;
	    var Loan_Disbursement;
	    var Pending_Applns_Withdraws;
	    var Approve_Withdraw;
	    var Pending_Applns_Deposits;
	    var Approve_Deposit;
	    var Pending_Shares_Request;
	    var Approve_Shares_Request;
	    var Verify_Pymt_Schedule;
	    var Approve_Pymt_Schedule;
	    var Transaction_Queue;
	    var Verify_Transaction;
	    var Approve_Reversal;
	    var inbox;
	    var Verify_New_User;
	    var Verify_User_Updates;
	    var Apprv_Appln_Configs;
	    //var Apprv_Config_Update;
	    var Apprv_Tran_Type;
	    var Apprv_TranType_Update;
	    var Approve_New_Role;
	    var Apprv_Role_Update;
	    var Apprv_Settings_Changes;

			// ... Ajax Call
	  	$.ajax
 			({
   			type:'post',
   			url:'queue-stats.php',
   			data:{
    			get_count:"get_count"
   			},
   			success:function(response) 
   			{
   				console.log(response);

    			// ... Handling of Db responses
    			response = JSON.parse(response)

    			New_Self_Enrollments = response.New_Self_Enrollments;
    			Applns_4_Review = response.Applns_4_Review;
    			Approve_Applns = response.Approve_Applns;
    			Finalize_Enrollment = response.Finalize_Enrollment;
			    Customer_Updates = response.Customer_Updates; 
			    Loan_Applns_Queue = response.Loan_Applns_Queue;
			    Credit_Committee = response.Credit_Committee;
			    Review_Applns = response.Review_Applns;
			    Loan_Disbursement = response.Loan_Disbursement;
			    Pending_Applns_Withdraws = response.Pending_Applns_Withdraws;
			    Approve_Withdraw = response.Approve_Withdraw;
			    Pending_Applns_Deposits = response.Pending_Applns_Deposits;
			    Approve_Deposit = response.Approve_Deposit;
			    Pending_Shares_Request = response.Pending_Shares_Request;
			    Approve_Shares_Request = response.Approve_Shares_Request;
			    Verify_Pymt_Schedule = response.Verify_Pymt_Schedule;
			    Approve_Pymt_Schedule = response.Approve_Pymt_Schedule;
			    Transaction_Queue = response.Transaction_Queue;
			    Verify_Transaction = response.Verify_Transaction;
			    Approve_Reversal = response.Approve_Reversal;
			    inbox = response.inbox;
			    Verify_New_User = response.Verify_New_User;
			    Verify_User_Updates = response.Verify_User_Updates;
			    Apprv_Appln_Configs = response.Apprv_Appln_Configs;
			   // Apprv_Config_Update = response.Apprv_Config_Update;
			    Apprv_Tran_Type = response.Apprv_Tran_Type;
			    Apprv_TranType_Update = response.Apprv_TranType_Update;
			    Approve_New_Role = response.Approve_New_Role;
			    Apprv_Role_Update = response.Apprv_Role_Update;
			    Apprv_Settings_Changes = response.Apprv_Settings_Changes;

			    // ... ... ... ... ... ... ... ... ... Displaying the Records to the User
		      $('#New_Self_Enrollments').text(New_Self_Enrollments);
		      $('#Applns_4_Review').text(Applns_4_Review);
	      	$('#Approve_Applns').text(Approve_Applns);
	      	$('#Finalize_Enrollment').text(Finalize_Enrollment);
			    $('#Customer_Updates').text(Customer_Updates);
			    $('#Loan_Applns_Queue').text(Loan_Applns_Queue);
			    $('#Credit_Committee').text(Credit_Committee);
			    $('#Review_Applns').text(Review_Applns);
			    $('#Loan_Disbursement').text(Loan_Disbursement);
			    $('#Pending_Applns_Withdraws').text(Pending_Applns_Withdraws);
			    $('#Approve_Withdraw').text(Approve_Withdraw);
			    $('#Pending_Applns_Deposits').text(Pending_Applns_Deposits);
			    $('#Approve_Deposit').text(Approve_Deposit);
			    $('#Pending_Shares_Request').text(Pending_Shares_Request);
			    $('#Approve_Shares_Request').text(Approve_Shares_Request);
			    $('#Verify_Pymt_Schedule').text(Verify_Pymt_Schedule);
			    $('#Approve_Pymt_Schedule').text(Approve_Pymt_Schedule);
			    $('#Transaction_Queue').text(Transaction_Queue);
			    $('#Verify_Transaction').text(Verify_Transaction);
			    $('#Approve_Reversal').text(Approve_Reversal);
			    $('#inbox').text(inbox);
			    $('#inbox_top').text(inbox);
			    $('#Verify_New_User').text(Verify_New_User);
			    $('#Verify_User_Updates').text(Verify_User_Updates);
			    $('#Apprv_Appln_Configs').text(Apprv_Appln_Configs);
			    //$('#Apprv_Config_Update').text(Apprv_Config_Update);
			    $('#Apprv_Tran_Type').text(Apprv_Tran_Type);
			    $('#Apprv_TranType_Update').text(Apprv_TranType_Update);
			    $('#Approve_New_Role').text(Approve_New_Role);
			    $('#Apprv_Role_Update').text(Apprv_Role_Update);
			    $('#Apprv_Settings_Changes').text(Apprv_Settings_Changes);
					    
   			}
 			});
 			// ... ... ... ... ... ... ... ... ... ... ... ... END 01: Menu Statistics ... ... ... ... ... ... ... ... ... ... ... ... //
			// ... ... ... ... ... ... ... ... ... ... ... ... END 01: Menu Statistics ... ... ... ... ... ... ... ... ... ... ... ... //
			// ... ... ... ... ... ... ... ... ... ... ... ... END 01: Menu Statistics ... ... ... ... ... ... ... ... ... ... ... ... //


		});

		
	</script>
	<?php
}

?>
