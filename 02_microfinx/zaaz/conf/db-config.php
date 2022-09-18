<?php

//creation of link to database server
$link = @mysql_connect("localhost", "wvi_db_user", "wvi_db_user@123") or die("error connecting to database server ".mysql_error());

//selecting database
$db = mysql_select_db("microfinx", $link) or die("error selecting database ".mysql_error());

?>
