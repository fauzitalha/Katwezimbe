<?php


substr($_SERVER['SERVER_NAME'], 0, 3) == "www" ? $WWW = true : $WWW = false;

if ($WWW) {
    echo $_SERVER['SERVER_NAME']."<br>";
    echo $WWW."<br>";
} else {
    echo $_SERVER['SERVER_NAME']."<br>";
    echo $WWW."<br>";
}



?>