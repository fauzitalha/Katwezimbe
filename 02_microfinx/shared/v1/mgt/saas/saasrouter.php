<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// ... Execute SAAS Router
function ExecuteSAASRouter($domain_name)
{

    // ... Prepare JSON data request message
    $PAYLOAD = array();
    $PAYLOAD["domain_name"] = $domain_name;
    $VR_PAYLOAD = json_encode($PAYLOAD);
 
    // ... Microfinx Properties
    $VR_URL = "http://microfinx.me";
    $VR_METHOD = "POST";

    // ... echo "<pre>".print_r($VR_PAYLOAD,true)."</pre>";
   
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$VR_URL);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $VR_METHOD);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $VR_PAYLOAD);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json; charset=UTF-8')
	);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
	$result=curl_exec ($ch);
	curl_close ($ch);

	$response_details = array();
	$response_details = json_decode(json_encode(json_decode($result,true)), true);

	// ... Unpacking the Response
	//echo "<pre>".print_r($response_details,true)."</pre>";
	/*
	Array
	(
	    [code] => 200
	    [message] => Success
	    [domain] => muntuyeraclient.microfinx.me
	    [condetails] => Array
	        (
	            [HOST] => U2FsdGVkX1/dpYcuXCr//XBp4YOu/kiaditAkduQD4c=
	            [RSSU] => U2FsdGVkX19fEQMlCcjocapdZD2Ili1BmnGKBHetRAU=
	            [DWWP] => U2FsdGVkX18aXOZqY8UnnFxWtlICDtzBU2shxJQLgWA=
	            [BANK] => U2FsdGVkX1+KAL79mRtj74U4wluCly/XVpbMD+sZeUdEEuKBLr4kh3awzBSJVgEn
	        )

	)
	*/

	$host = $response_details["condetails"]["HOST"];
	$rssu = $response_details["condetails"]["RSSU"];
	$dwwp = $response_details["condetails"]["DWWP"];
	$bank = $response_details["condetails"]["BANK"];

	$_SESSION["mgtzaazhost"] = $host;
	$_SESSION["mgtzaazrssu"] = $rssu;
	$_SESSION["mgtzaazdwwp"] = $dwwp;
	$_SESSION["mgtzaazbank"] = $bank;
}

?>
