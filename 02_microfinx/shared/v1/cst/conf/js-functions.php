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
	    var inbox;

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
   				console.log(response.inbox);

    			response = JSON.parse(response)
			    inbox = response.inbox;
			    $('#inbox').text(inbox);
			    $('#inbox_top').text(inbox);
					    
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
	    var inbox;

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
    			response = JSON.parse(response);
			    inbox = response.inbox;
			    $('#inbox').text(inbox);
			    $('#inbox_top').text(inbox);

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
