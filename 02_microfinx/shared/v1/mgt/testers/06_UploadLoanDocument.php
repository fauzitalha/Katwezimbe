<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

# ... File Parameter Information
$loan_account_no = "2";
$file_path = "D:/__#/wamp/www/wvi-cst/files/activation_requests/DF00000030/PP_20190419110546_DF00000030.jpg";
$file_type = mime_content_type($file_path);
$file_name = "PP_20190419110546_DF00000030.jpg";
$description = "This is an upload for the api.";


# ... Request Message
$data_string = array(
	'file' => new \CurLFile($file_path, $file_type, $file_name),
	'name' => $file_name,
	'description'=>$description
);


$endpoint = "https://localhost:8443/fineract-provider/api/v1/loans/".$loan_account_no."/documents?tenantIdentifier=default&pretty=true";
$username= "mifos";
$password= "password";
$tenant = "default";
$method = "POST";


$URL= $endpoint;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$URL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: multipart/form-data'
));
curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
$result=curl_exec ($ch);
curl_close ($ch);

$result = json_decode(json_encode(json_decode($result,true)), true);
echo "<pre>".print_r($result,true)."</pre>";


?>