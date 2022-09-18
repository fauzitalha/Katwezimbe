<?php
# ... Important Data
include("conf/session-checker.php");

# ### ###  ###  ###  ###  Core Products
if(isset($_POST['shares_acct_id']))
{
	$shares_acct_id = $_POST['shares_acct_id'];
	$response_details = array();

  $response_msg = FetchShareAcctById($shares_acct_id, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $ACCTS_DATA = array();
  $ACCTS_DATA = $CORE_RESP;

  $totalApprovedShares = $ACCTS_DATA["summary"]["totalApprovedShares"];
  $productId = $ACCTS_DATA["summary"]["productId"];


  // ... Getting the shares product values
  $response_msg = FetchShareProductById($productId, $MIFOS_CONN_DETAILS);
  $CONN_FLG = $response_msg["CONN_FLG"];
  $CORE_RESP = $response_msg["CORE_RESP"];
  $PRODUCTS_DATA = array();
  $PRODUCTS_DATA = $CORE_RESP;
  $unitPrice = $PRODUCTS_DATA["unitPrice"];
  $SHARE_PDT_ID = $PRODUCTS_DATA["id"];
  $SHARE_PDT_NAME = $PRODUCTS_DATA["name"];
  $SHARE_PDT_SHORT_NAME = $PRODUCTS_DATA["shortName"];
  $SHARE_PDT_DESC = $PRODUCTS_DATA["description"];
  $SHARES_MAXIMUM = $PRODUCTS_DATA["maximumShares"];

  $response_details["TT_APPRVD_SHARES"] = $totalApprovedShares;
  $response_details["SHARES_UNIT_PRICE"] = $unitPrice;
  $response_details["SHARE_PDT_ID"] = $SHARE_PDT_ID;
  $response_details["SHARE_PDT_NAME"] = $SHARE_PDT_NAME;
  $response_details["SHARE_PDT_SHORT_NAME"] = $SHARE_PDT_SHORT_NAME;
  $response_details["SHARE_PDT_DESC"] = $SHARE_PDT_DESC;
  $response_details["SHARES_MAXIMUM"] = $SHARES_MAXIMUM;

	$responseJSON = json_encode($response_details);
 	echo $responseJSON;
 	exit();
}

?>

