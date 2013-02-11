<?php
/******************************************************
// This is a mapscript to configure the access to DB on the mapserver map file.
// IMPORTANT: All layers will be set as a dblayer
//
// Steps to use this script:
//     1.- Set $INPUT_MAPFILE and $OUTPUT_MAPFILE variables.
//     2.- Ensure that "config.php" file (required on the php folder) has the connection variables defined properly.
//     3.- Execute command: "php5 -f ms_config.php".
//     4.- The result will be save on $OUTPUT_MAPFILE with the correct connection string.
//
// Author: Nacho Varela
// Date: May 2012 
// License: GPL3
******************************************************/

//dl("php_mapscript.so");
include('php/config.php');

$INPUT_MAPFILE 	= 'cdix_ORIG.map';
$OUTPUT_MAPFILE = 'cdix.map';

$orig_ms = new MapObj($INPUT_MAPFILE);
$ms = clone $orig_ms;
$layerNames = $ms->getAllLayerNames(); 

echo 'Configuring mapserver file... '."\n";
for($i=0; $i < sizeof($layerNames); $i++) { 
	echo '   Layer ['.($i+1).']: '.$layerNames[$i] . '... ';
	$lyr = $ms->getLayer($i);
	$lyr->set("connection","dbname='".$db_name_cdix."' host='".$host_cdix."' port=".$port_cdix." user='".$username_cdix."' password='".$password_cdix."' sslmode=disable");
    echo " changed.  \n";
}

$ms->save($OUTPUT_MAPFILE);
echo 'Done!!!!'."\n";