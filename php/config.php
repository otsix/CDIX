<?php

require_once ('PhpConsole.php');
PhpConsole::start();

//DEBUG_MODE=1 when devel, else set to "0"
$DEBUG_MODE=1;

$host_carto="localhost"; // DB hostname
$port_carto="5432"; // DB Port
$db_name_carto="cartobase"; // DB Name 
$username_carto="cartobase_reader"; //Username 
$password_carto="20webcarto12"; //Password to access 
$connectionstring_carto = "host=$host_carto dbname=$db_name_carto user=$username_carto password=$password_carto";
$connectionstring = $connectionstring_carto;

////////TODO change to CartoBASE db (it must contain all base layers admin-limits, etc.)
$host_cdix="localhost"; // DB hostname
$port_cdix="5432"; // DB Port
$db_name_cdix="cdix"; // DB Name 
$username_cdix="cdix_reader"; //Username 
$password_cdix="cdix"; //Password to access 
$connectionstring_cdix = "host=$host_cdix dbname=$db_name_cdix user=$username_cdix password=$password_cdix";

?>