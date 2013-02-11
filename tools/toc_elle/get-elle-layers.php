<?php
/**
 * This code connects to PostGIS _map table (defined by ELLE gvSIG-EIEL extension)
 * to get ToC layers.
 */

include '../../php/config.php';
include '../../php/access-bbdd.php';
include 'get-functions.php';

$getfeatures = new GetFeatures($connectionstring_cdix, $connectionstring_carto);
$connected = $getfeatures->connect();
if($DEBUG_MODE){
	$connected = (TRUE) ? debug("Connected...") : debug("ERROR: NOT CONNECTED!!") ;
}

//TODO Retrieve from a GET parameter
$MAP_NAME='default';


/**
 * To retrieve groups
 */
if (isset($_GET['getgroups'])){
	$list = $getfeatures->getGroups($MAP_NAME);
	echo json_encode($list);
	exit(1);
}

//TODO: ELLE is not used any more on CDIX project
// /**
 // * layers to retrieve "_map" (ELLE table) or "_wms_layers" table 
 // */
// if (!isset($_GET['layers'])){
	// $LAYERS = '_map'; 
// } else {
	// $LAYERS = '_wms_layers';
// }

try {
	// if ($LAYERS == '_map') {
		// if (isset($MAP_NAME)){
			// $list = $getfeatures->getELLELayers($MAP_NAME);
		// }
	// } else if ($LAYERS == '_wms_layers') {
		if (isset($MAP_NAME)){
			$list = $getfeatures->getWMSLayers($MAP_NAME);
		}		
	//}
	echo json_encode($list);
} catch (SINAException $ex) {
	echo $ex.getMessage();
}
?>

