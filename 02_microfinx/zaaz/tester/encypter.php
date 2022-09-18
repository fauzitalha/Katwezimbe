<?php
include "../conf/app-config.php";
include "../core/aes256.php";

$rawtext = "microfinx-bwebajja";
$enctest = EncryptText($rawtext, $APP_CONF["AESKEY"]);
$dectest = DecryptText($enctest, $APP_CONF["AESKEY"]);
echo "ENC: ".$enctest."<br>";
echo "DEC: ".$dectest."<br>";

// ... Encrypt Text
function EncryptText($rawtext, $passphrase){
    return AES256::encrypt($rawtext, $passphrase);
} 

// ... Decrypt Text
function DecryptText($rawtext, $passphrase){
    return AES256::decrypt($rawtext, $passphrase);
} 

?>