<?php
include("aes/AES256_AES256.php");

$key = AES256AES256::$KEY;
$zaazhost = AES256AES256::decrypt($_SESSION["mgtzaazhost"], $key);
$zaazrssu = AES256AES256::decrypt($_SESSION["mgtzaazrssu"], $key);
$zaazdwwp = AES256AES256::decrypt($_SESSION["mgtzaazdwwp"], $key);
$zaazbank = AES256AES256::decrypt($_SESSION["mgtzaazbank"], $key);


//creation of link to database server
$link = @mysql_connect($zaazhost, $zaazrssu, $zaazdwwp) or die("error connecting to database server ".mysql_error());

//selecting database
$db = mysql_select_db($zaazbank, $link) or die("error selecting database ".mysql_error());

?>