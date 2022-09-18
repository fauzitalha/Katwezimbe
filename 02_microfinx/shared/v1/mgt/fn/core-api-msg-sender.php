<?php
// ... utility files
include("conf/app-config.php");


// ... Request Forwarding
function send_to_mifos($url, $method, $username, $password, $data_string)
{
    // ... Generating Request Data
    $DB_ORG_CODE = GetSystemParameter("ORGCODE");
    $RQST_UNIQUE_RSQT_ID = uniqid();
    $RQST_TIMESTAMP = date("YmdHis", time());
    $RQST_EXT_ID = $DB_ORG_CODE."-".$RQST_TIMESTAMP."-".$RQST_UNIQUE_RSQT_ID;

    // ... Preparing Data
    $OrgCode = $DB_ORG_CODE;
    $OrgMifosUserName = $username;
    $OrgMifosPassword = $password;
    $RequestExtID = $RQST_EXT_ID;
    $RequestMethod = $method;
    $RequestUrl = $url;
    $RequestPayLoad = $data_string;
    $DataSignature = SignRequestData($RequestExtID, $RequestMethod, $RequestUrl, $RequestPayLoad, $OrgMifosUserName, $OrgMifosPassword, $OrgCode);

    // ... Prepare JSON data request message
    $PAYLOAD = array();
    $PAYLOAD["OrgCode"] = $OrgCode;
    $PAYLOAD["OrgMifosUserName"] = $OrgMifosUserName;
    $PAYLOAD["OrgMifosPassword"] = $OrgMifosPassword;
    $PAYLOAD["RequestExtID"] = $RequestExtID;
    $PAYLOAD["RequestMethod"] = $RequestMethod;
    $PAYLOAD["RequestUrl"] = $RequestUrl;
    $PAYLOAD["RequestPayLoad"] = $RequestPayLoad;
    $PAYLOAD["DataSignature"] = $DataSignature;
    $VR_PAYLOAD = json_encode($PAYLOAD);

    //echo json_encode($VR_PAYLOAD);
    
    // ... Mifos Connection
    $TRAN_INTERFACE = $_SESSION['TRAN_INTERFACE'];
    $VR_URL = $TRAN_INTERFACE;
    $VR_METHOD = "POST";

    // ... Saving the request to local database
    $RQSTPROCDATE = GetCurrentDateTime();
    $PROCREF = $RQST_EXT_ID;
    $DDD_ORGCODE = $DB_ORG_CODE;
    $DDD_REQUESTEXTID = $PROCREF;
    $DDD_REQUESTMETHOD = $RequestMethod; 
    $DDD_REQUESTURL = $RequestUrl;
    $DDD_REQUESTPAYLOAD = $RequestPayLoad;

    $InsertQuery = "INSERT INTO mifosxrqstproc (RQSTPROCDATE, PROCREF, ORGCODE, REQUESTEXTID, REQUESTMETHOD, REQUESTURL
    , REQUESTPAYLOAD) VALUES('$RQSTPROCDATE', '$PROCREF', '$DDD_ORGCODE', '$DDD_REQUESTEXTID', '$DDD_REQUESTMETHOD'
    ,'$DDD_REQUESTURL', '$DDD_REQUESTPAYLOAD')";

    ExecuteEntityInsert($InsertQuery);

    // ... forwarding the request to the Mifos
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

    // ... Update the response from Mifos
    $RESPPROCDATE = GetCurrentDateTime();
    $ClientRequestExtID = $response_details["ClientRequestExtID"];
    $RESPPROCCODE = $response_details["RoutingProcCode"];
    $RESPPROCMSSG = $response_details["RoutingProcMessage"];
    $RESPPROCREF = $response_details["RoutingProcRef"];
    $MIFOSHTTPRESPCODE = $response_details["MifosHttpRespcode"];
    $MIFOSRESPDETAILS = json_encode($response_details["MifosRespDetails"]);

    $httpc = substr($MIFOSHTTPRESPCODE,0,1);
    $RESP_M_DETAILS = ($httpc=="2")? "" : $MIFOSRESPDETAILS;

    $UpdateQuery = "UPDATE mifosxrqstproc 
                    SET RESPPROCDATE='$RESPPROCDATE'
                       ,RESPPROCCODE='$RESPPROCCODE'
                       ,RESPPROCMSSG='$RESPPROCMSSG'
                       ,RESPPROCREF='$RESPPROCREF'
                       ,MIFOSHTTPRESPCODE='$MIFOSHTTPRESPCODE'
                       ,MIFOSRESPDETAILS='$RESP_M_DETAILS'
                    WHERE PROCREF='$ClientRequestExtID'
                      AND REQUESTEXTID='$ClientRequestExtID'";
    ExecuteEntityUpdate($UpdateQuery);

    // ... displaying the actual response from Mifos
    return $response_details["MifosRespDetails"];
}

// ... Document Request Forwarding
function send_to_mifos_multipart($url, $method, $username, $password, $data_string)
{     
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
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

    $response_details = array();
    $response_details = json_decode(json_encode(json_decode($result,true)), true);
    return $response_details;
}

// ... Request Forwarding for Images
function send_to_mifos_images($url, $method, $username, $password, $data_string)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/plain',
        'Content-Length: ' . strlen($data_string)
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
    $result = curl_exec($ch);
    curl_close($ch);

    //$response_details = $result;
    //return $response_details;

    $response_details = array();
    $response_details = json_decode(json_encode(json_decode($result, true)), true);
    return $response_details;
}
function send_to_mifos_images_get($url, $method, $username, $password, $data_string)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/plain',
        'Content-Length: ' . strlen($data_string)
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
    $result = curl_exec($ch);
    curl_close($ch);

    $response_details = $result;
    return $response_details;
}

// ... Signing the Request Message
function SignRequestData($RequestExtID, $RequestMethod, $RequestUrl, $RequestPayLoad, $OrgMifosUserName, $OrgMifosPassword, $OrgCode){
    $BASEAESKEYFILEPATH = $_SESSION['BASEAESKEYFILEPATH'];
    $ORGKEYFILE = GetSystemParameter("ORGKEYFILE");
    $KEYFILEPATH = $BASEAESKEYFILEPATH."/".$ORGKEYFILE;
    $OrgDataKey = ReadOrgKeyFromFile($KEYFILEPATH);
    $RawData = $RequestExtID."^".$RequestMethod."^".$RequestUrl."^".$RequestPayLoad."^".$OrgMifosUserName."^".$OrgMifosPassword."^".$OrgCode;
    $DataSignature = AES256AES256::encrypt($RawData, $OrgDataKey);
    return $DataSignature;
}

// ... Read from File
function ReadOrgKeyFromFile($KEYFILEPATH)
{
    $DataKey = "";
    $KeyFile = fopen($KEYFILEPATH, "r") or die("Unable to open data file!");
    $DataKey = fgets($KeyFile);
    return $DataKey;
}

?>
