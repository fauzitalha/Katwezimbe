<?php
include("aes/AES256_AES256.php");

$key = AES256AES256::$KEY;
$zaazhost = AES256AES256::decrypt($_SESSION["zaazhost"], $key);
$zaazrssu = AES256AES256::decrypt($_SESSION["zaazrssu"], $key);
$zaazdwwp = AES256AES256::decrypt($_SESSION["zaazdwwp"], $key);
$zaazbank = AES256AES256::decrypt($_SESSION["zaazbank"], $key);


//creation of link to database server
$link = @mysql_connect($zaazhost, $zaazrssu, $zaazdwwp) or die("error connecting to database server ".mysql_error());

//selecting database
$db = mysql_select_db($zaazbank, $link) or die("error selecting database ".mysql_error());

?>