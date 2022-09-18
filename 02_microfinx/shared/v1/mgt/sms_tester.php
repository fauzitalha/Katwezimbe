<?php

# ... Initializing Payload
$msisdns = array();
$msisdns[0] = "256702445779";
$username = "naggayi.invest";
$password = "Ayi#82gaF7";
$message = "This is a test message from TA. May Allah bless our efforts";

# ... Assembling Payload
$URL = "http://mysms.trueafrican.com/v1/api/esme/send";
$METHOD = "POST";

$PL = array();
$PL["msisdn"] = $msisdns;
$PL["username"] = $username;
$PL["password"] = $password;
$PL["message"] = $message;
$PAYLOAD = json_encode($PL);
echo "<pre>".print_r($PAYLOAD, true)."</pre>";




$response_details = send_sms_TA($URL, $METHOD, $PAYLOAD);
echo "<pre>".print_r($response_details, true)."</pre>";



// ... Document Request Forwarding
function send_sms_TA($URL, $METHOD, $PAYLOAD)
{     
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$URL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $METHOD);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $PAYLOAD);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=UTF-8')
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    $result=curl_exec ($ch);
    curl_close ($ch);

    $response_details = array();
    $response_details = json_decode(json_encode(json_decode($result,true)), true);
    return $response_details;
}




?>