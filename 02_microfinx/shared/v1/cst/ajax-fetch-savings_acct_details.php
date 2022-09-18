<?php
# ... Important Data
include("conf/session-checker.php");

# ### ###  ###  ###  ###  Core Products
if(isset($_POST['svngs_id']))
{
	$svngs_id = $_POST['svngs_id'];
	$response_details = array();

  $response_msg = FetchSavingsAcctById($svngs_id, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $ACCTS_DATA = array();
  $ACCTS_DATA = $CORE_RESP;

  $accountBalance = $ACCTS_DATA["summary"]["accountBalance"];
  $response_details["SVNGS_ACCT_BAL"] = $accountBalance;

	$responseJSON = json_encode($response_details);
 	echo $responseJSON;
 	exit();
}

?>

