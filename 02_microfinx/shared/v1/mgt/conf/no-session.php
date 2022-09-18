<?php
# ... Display application errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

# ... Load Configurations
include("conf/db-config.php");
include("fn/db-functions.php");
include("fn/sys-functions.php");
include("fn/core-api-client-rqst-msgs.php");
include("fn/core-api-client.php");

include("conf/app-config.php");
include("conf/css-functions.php");
include("conf/js-functions.php");
include("conf/navigations.php");

# ... Load Other Configs
include("aes/AES256.php");
require 'fezzmail/r/PHPMailerAutoload.php';


?>

