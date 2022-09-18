<?php
# ... Important Data
include("conf/session-checker.php");

# ### ###  ###  ###  ###  Core Products
if(isset($_POST['account_no']))
{
	$account_no = $_POST['account_no'];
	$response_details = array();

  $response_msg = InquireSvgsAcctDetails($account_no, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $ACCTS_DATA = array();
  $ACCTS_DATA = $CORE_RESP;

  if (sizeof($ACCTS_DATA["data"])>0) {
    $DATA_ROW = $ACCTS_DATA["data"][0]["row"];
    $response_details["STATUS_CODE"] = "OK";
    $response_details["STATUS_MESSAGE"] = "SUCCESS";
    $response_details["ACCT_ID"] = $DATA_ROW[0];
    $response_details["ACCT_NUMBER"] = $DATA_ROW[1];
    $response_details["CRNCY"] = $DATA_ROW[2];
    $response_details["CLIENT_ID"] = $DATA_ROW[3];
    $response_details["CLIENT_NAME"] = $DATA_ROW[4];
    $response_details["ACCT_PDT_ID"] = $DATA_ROW[5];
    $response_details["ACCT_PDT_NAME"] = $DATA_ROW[6];
    $response_details["ACCT_PDT_SHORT_NAME"] = $DATA_ROW[7];
    $response_details["GROUP_ID"] = $DATA_ROW[8];
    $response_details["STATUS_ENUM"] = $DATA_ROW[9];
  } else {
    $response_details["STATUS_CODE"] = "ERROR";
    $response_details["STATUS_MESSAGE"] = "UNKNOWN ACCOUNT NUMBER";
  }


	$responseJSON = json_encode($response_details);
 	echo $responseJSON;
 	exit();
}

?>

