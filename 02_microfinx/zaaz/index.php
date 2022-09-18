<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ... 001: Utility Files
include_once "conf/conf.php";
include_once "core/core.php";

// ... 002: Interface Headers
header("Access-Control-Allow-Origin: ".$APP_CONF["ALLOWED_IPADDRESSES"]);
header("Content-Type: application/json;");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Content-Length");
  
// ... 003: Get posted data from client
$data = json_decode(file_get_contents("php://input"));
$domain_name = isset($data->domain_name)? $data->domain_name : "";

// ... 004: start the router & Database
$router = new SaasRouter($APP_CONF);

// ... 005: get url
$ptxl = trim($domain_name);


$api_response = $router->RouteTraffic($ptxl);
echo $api_response;

?>
