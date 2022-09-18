<?php
# ... Important Data
include("conf/session-checker.php");

# ### ###  ###  ###  ###  Core Products
if(isset($_POST['pdt_type']))
{
	$p_type = $_POST['pdt_type'];
	$response_details = array();

  /*
	Y0001 - Loan Application
	Y0002 - Savings Withdraw Application
	Y0003 - Savings Deposit Application

	*/

	# ... 01: if Loan Products
	if ($p_type=="Y0001") {
		$response_msg = array();
		$response_msg = FetchLoanProducts($MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];
    $loans_pdt_list = $CORE_RESP;
    for ($i=0; $i < sizeof($loans_pdt_list); $i++) { 
      
      $loan_pdt = $loans_pdt_list[$i];
      $id = $loan_pdt["id"];
      $name = $loan_pdt["name"];
      $shortName = $loan_pdt["shortName"];

      $pdt = array();
      $pdt["PDT_ID"] = $id;
      $pdt["PDT_NAME"] = $name;
      $pdt["PDT_SORT_NAME"] = $shortName;

      $response_details[$i] = $pdt;
    }
	}

	# ... 02: if Savings or WithDraw
	if ( ($p_type=="Y0002")||($p_type=="Y0003") ) {
		$response_msg = array();
		$response_msg = FetchSavingsProducts($MIFOS_CONN_DETAILS);
    $CONN_FLG = $response_msg["CONN_FLG"];
    $CORE_RESP = $response_msg["CORE_RESP"];
    $svngs_pdt_list = $CORE_RESP;
    for ($i=0; $i < sizeof($svngs_pdt_list); $i++) { 
      
      $savings_pdt = $svngs_pdt_list[$i];
      $id = $savings_pdt["id"];
      $name = $savings_pdt["name"];
      $shortName = $savings_pdt["shortName"];

      $pdt = array();
      $pdt["PDT_ID"] = $id;
      $pdt["PDT_NAME"] = $name;
      $pdt["PDT_SORT_NAME"] = $shortName;

      $response_details[$i] = $pdt;
    }
	}



	$responseJSON = json_encode($response_details);
 	echo $responseJSON;
 	exit();
}

?>

