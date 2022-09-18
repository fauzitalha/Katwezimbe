<?php
# ... Important Data
include("conf/session-checker.php");

# ### ###  ###  ###  ###  Core Products
if(isset($_POST['fin_inst_id']))
{
	$FIN_INST_ID = $_POST['fin_inst_id'];
	$response_details = array();

	$fin = array();
  $fin = FetchFinInstitutionsById($FIN_INST_ID);

  $response_details["ORG_ACCT_NUM"] = $fin['ORG_ACCT_NUM'];
  $response_details["FIN_INST_NAME"] = $fin['FIN_INST_NAME'];

	$responseJSON = json_encode($response_details);
 	echo $responseJSON;
 	exit();
}

?>

