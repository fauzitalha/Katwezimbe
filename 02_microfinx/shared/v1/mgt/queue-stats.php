<?php
# ... Important Data
include("conf/session-checker.php");

# ### ###  ###  ###  ###  Statistical Counts
if(isset($_POST['get_count']))
{
	$response_stats = array();

	# ... Customers
	$DB_New_Self_Enrollments = "SELECT count(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE ACTIVATION_STATUS in ('PENDING','RESUBMITTED','BOUNCED')";
	$DB_Applns_4_Review = "SELECT count(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE ACTIVATION_STATUS='NEEDS_CUSTOMER_REVIEW'";
	$DB_Approve_Applns = "SELECT count(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE ACTIVATION_STATUS='VERIFIED'";
	$DB_Finalize_Enrollment = "SELECT count(*) as RTN_VALUE FROM cstmrs_actvn_rqsts WHERE ACTIVATION_STATUS='APPROVED'";
	$DB_Customer_Updates = "SELECT count(*) as RTN_VALUE FROM cstmrs_info_chng_log WHERE CHNG_STATUS='PENDING'";
	$New_Self_Enrollments = ReturnOneEntryFromDB($DB_New_Self_Enrollments);
	$Applns_4_Review = ReturnOneEntryFromDB($DB_Applns_4_Review);
	$Approve_Applns = ReturnOneEntryFromDB($DB_Approve_Applns);
	$Finalize_Enrollment = ReturnOneEntryFromDB($DB_Finalize_Enrollment);
	$Customer_Updates = ReturnOneEntryFromDB($DB_Customer_Updates);

	$response_stats["New_Self_Enrollments"] = $New_Self_Enrollments;
	$response_stats["Applns_4_Review"] = $Applns_4_Review;
	$response_stats["Approve_Applns"] = $Approve_Applns;
	$response_stats["Finalize_Enrollment"] = $Finalize_Enrollment;
	$response_stats["Customer_Updates"] = $Customer_Updates;

	# ... Loan Applications
	$DB_Loan_Applns_Queue = "SELECT count(*) as RTN_VALUE FROM loan_applns WHERE LN_APPLN_STATUS in ('NEW_SUBMISSION','AA_BOUNCED_BACK','CC_BOUNCED_BACK')";
	$DB_Credit_Committee = "SELECT count(*) as RTN_VALUE FROM loan_applns WHERE CC_FLG='YY' AND LN_APPLN_STATUS='VERIFIED'";
	$DB_Review_Applns = "SELECT count(*) as RTN_VALUE FROM loan_applns WHERE LN_APPLN_STATUS='READY_4_REVIEW'";
	$DB_Loan_Disbursement = "SELECT count(*) as RTN_VALUE FROM loan_applns WHERE LN_APPLN_STATUS='READY_4_DISBURSAL'";
	$Loan_Applns_Queue = ReturnOneEntryFromDB($DB_Loan_Applns_Queue);
	$Credit_Committee = ReturnOneEntryFromDB($DB_Credit_Committee);
	$Review_Applns = ReturnOneEntryFromDB($DB_Review_Applns);
	$Loan_Disbursement = ReturnOneEntryFromDB($DB_Loan_Disbursement);

	$response_stats["Loan_Applns_Queue"] = $Loan_Applns_Queue;
	$response_stats["Credit_Committee"] = $Credit_Committee;
	$response_stats["Review_Applns"] = $Review_Applns;
	$response_stats["Loan_Disbursement"] = $Loan_Disbursement;

	# ... Savings Withdraws
	$DB_Pending_Applns_Withdraws = "SELECT count(*) as RTN_VALUE FROM svgs_withdraw_requests WHERE SVGS_APPLN_STATUS='PENDING'";
	$DB_Approve_Withdraw = "SELECT count(*) as RTN_VALUE FROM svgs_withdraw_requests WHERE SVGS_APPLN_STATUS='VERIFIED'";
	$Pending_Applns_Withdraws = ReturnOneEntryFromDB($DB_Pending_Applns_Withdraws);
	$Approve_Withdraw = ReturnOneEntryFromDB($DB_Approve_Withdraw);

	$response_stats["Pending_Applns_Withdraws"] = $Pending_Applns_Withdraws;
	$response_stats["Approve_Withdraw"] = $Approve_Withdraw;

	# ... Savings Deposits
	$DB_Pending_Applns_Deposits = "SELECT count(*) as RTN_VALUE FROM svgs_deposit_requests WHERE RQST_STATUS='PENDING'";
	$DB_Approve_Deposit = "SELECT count(*) as RTN_VALUE FROM svgs_deposit_requests WHERE RQST_STATUS='VERIFIED'";
	$Pending_Applns_Deposits = ReturnOneEntryFromDB($DB_Pending_Applns_Deposits);
	$Approve_Deposit = ReturnOneEntryFromDB($DB_Approve_Deposit);

	$response_stats["Pending_Applns_Deposits"] = $Pending_Applns_Deposits;
	$response_stats["Approve_Deposit"] = $Approve_Deposit;

	# ... Shares
 	/*$DB_Pending_Shares_Request = "SELECT count(*) as RTN_VALUE FROM shares_appln_requests WHERE FINAL_TRAN_STATUS='PENDING'";
	$DB_Approve_Shares_Request = "SELECT count(*) as RTN_VALUE FROM shares_appln_requests WHERE FINAL_TRAN_STATUS='VERIFIED'";
	$Pending_Shares_Request = ReturnOneEntryFromDB($DB_Pending_Shares_Request);
	$Approve_Shares_Request = ReturnOneEntryFromDB($DB_Approve_Shares_Request);

	$response_stats["Pending_Shares_Request"] = $Pending_Shares_Request;
	$response_stats["Approve_Shares_Request"] = $Approve_Shares_Request;*/

	# ... Bulk Transactions
	$DB_Verify_Pymt_Schedule = "SELECT COUNT(*) RTN_VALUE 
	                            FROM blk_pymt_file 
	                            WHERE FILE_ID in (select distinct(b.FILE_ID) from blk_pymt_txns b WHERE b.TRAN_STATUS='PENDING')";
	$DB_Approve_Pymt_Schedule = "SELECT COUNT(*) RTN_VALUE 
	                            FROM blk_pymt_file 
	                            WHERE FILE_ID in (select distinct(b.FILE_ID) from blk_pymt_txns b WHERE b.TRAN_STATUS='VERIFIED')";
	$Verify_Pymt_Schedule = ReturnOneEntryFromDB($DB_Verify_Pymt_Schedule);
	$Approve_Pymt_Schedule = ReturnOneEntryFromDB($DB_Approve_Pymt_Schedule);

	$response_stats["Verify_Pymt_Schedule"] = $Verify_Pymt_Schedule;
	$response_stats["Approve_Pymt_Schedule"] = $Approve_Pymt_Schedule;

	# ... Transaction Posting
	$DB_Transaction_Queue = "SELECT count(distinct(TRAN_ID)) as RTN_VALUE FROM txn_details WHERE DEL_FLG='N' and PSTD_FLG='N' and VRF_FLG='N'";
	$DB_Verify_Transaction = "SELECT count(distinct(TRAN_ID)) as RTN_VALUE FROM txn_details WHERE DEL_FLG='N' and PSTD_FLG='Y' and VRF_FLG='N'";
	$DB_Approve_Reversal = "SELECT count(*) as RTN_VALUE FROM txn_reversals WHERE RVRSL_STATUS='PENDING'";
	$Transaction_Queue = ReturnOneEntryFromDB($DB_Transaction_Queue);
	$Verify_Transaction = ReturnOneEntryFromDB($DB_Verify_Transaction);
	$Approve_Reversal = ReturnOneEntryFromDB($DB_Approve_Reversal);

	$response_stats["Transaction_Queue"] = $Transaction_Queue;
	$response_stats["Verify_Transaction"] = $Verify_Transaction;
	$response_stats["Approve_Reversal"] = $Approve_Reversal;


	# ... Notifications
	$RECIPIENT_ID = $_SESSION['UPR_USER_ID'];
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


	# ... User Management
	$DB_Verify_New_User = "SELECT count(*) as RTN_VALUE FROM upr WHERE USER_STATUS='PENDING'";
	$Verify_New_User = ReturnOneEntryFromDB($DB_Verify_New_User);	
	$Verify_User_Updates = FetchUserChangeRequestsCount();

	$response_stats["Verify_New_User"] = $Verify_New_User;
	$response_stats["Verify_User_Updates"] = $Verify_User_Updates;

	# ... System Settings;
	$DB_Apprv_Appln_Configs = "SELECT count(*) as RTN_VALUE FROM appln_configs WHERE APPLN_CONFIG_STATUS ='PENDING'";
	//$DB_Apprv_Config_Update = "SELECT count(*) as RTN_VALUE FROM appln_configs_chng_log WHERE CHNG_STATUS ='PENDING'";
	$Apprv_Appln_Configs = ReturnOneEntryFromDB($DB_Apprv_Appln_Configs);	
	//$Apprv_Config_Update = ReturnOneEntryFromDB($DB_Apprv_Config_Update);	

	$response_stats["Apprv_Appln_Configs"] = $Apprv_Appln_Configs;
	//$response_stats["Apprv_Config_Update"] = $Apprv_Config_Update;


	$DB_Apprv_Tran_Type = "SELECT count(*) as RTN_VALUE FROM txn_types WHERE TRAN_TYPE_STATUS ='PENDING'";
	$DB_Apprv_TranType_Update = "SELECT count(*) as RTN_VALUE FROM txn_types_chng_log WHERE CHNG_STATUS ='PENDING'";
	$Apprv_Tran_Type = ReturnOneEntryFromDB($DB_Apprv_Tran_Type);	
	$Apprv_TranType_Update = ReturnOneEntryFromDB($DB_Apprv_TranType_Update);	

	$response_stats["Apprv_Tran_Type"] = $Apprv_Tran_Type;
	$response_stats["Apprv_TranType_Update"] = $Apprv_TranType_Update;


	$DB_Approve_New_Role = "SELECT count(*) as RTN_VALUE FROM sys_roles WHERE ROLE_STATUS ='PENDING'";
	$DB_Apprv_Role_Update = "SELECT count(*) as RTN_VALUE FROM sys_roles_chng_log WHERE CHNG_STATUS ='PENDING'";
	$Approve_New_Role = ReturnOneEntryFromDB($DB_Approve_New_Role);	
	$Apprv_Role_Update = ReturnOneEntryFromDB($DB_Apprv_Role_Update);	

	$response_stats["Approve_New_Role"] = $Approve_New_Role;
	$response_stats["Apprv_Role_Update"] = $Apprv_Role_Update;


	$DB_Apprv_Settings_Changes = "SELECT count(*) as RTN_VALUE FROM sys_gen_params_chng_log WHERE CHNG_STATUS ='PENDING'";
	$Apprv_Settings_Changes = ReturnOneEntryFromDB($DB_Apprv_Settings_Changes);	

	$response_stats["Apprv_Settings_Changes"] = $Apprv_Settings_Changes;



	$responseJSON = json_encode($response_stats);
 	echo $responseJSON;
 	exit();
}

?>

