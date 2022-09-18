<?php
# ... Important Data
include("conf/session-checker.php");

# ### ###  ###  ###  ###  Statistical Counts
if(isset($_POST['get_count']))
{
	$response_stats = array();

	# ... Notifications
	$RECIPIENT_ID = $_SESSION['CST_USR_ID'];
	$DB_inbox = "SELECT (
	                     	SELECT COUNT(*) FROM notifications 
		                		WHERE NTFCN_ID not in (SELECT NTFCN_ID
		                                       		FROM notification_read_receipt
		                                          WHERE DEL_FLG='Y' AND RECIPIENT_ID='$RECIPIENT_ID')
						  				    AND NTFCN_ID in (SELECT DISTINCT(NTFCN_ID)
											   								  FROM notification_recipients
											                    WHERE RECIPIENT_ID = '$RECIPIENT_ID'
																						OR RECIPIENT_ID in (SELECT DISTINCT(GRP_ID) 
																				  FROM notification_group_members 
																				  WHERE MEMBER_ID='$RECIPIENT_ID'))
											) - 
											(
												SELECT count(*) as RTN_VALUE FROM notification_read_receipt WHERE RECIPIENT_ID='$RECIPIENT_ID'
											) as RTN_VALUE";
	$inbox = ReturnOneEntryFromDB($DB_inbox);	

	$response_stats["inbox"] = $inbox;

	$responseJSON = json_encode($response_stats);
 	echo $responseJSON;
 	exit();
}

?>

