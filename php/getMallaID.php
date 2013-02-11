<?php

   include ('config.php');

   function processSQLResult($result){
   	  $tb = '';
      if(pg_num_rows($result)>0){         
         while ($row = pg_fetch_row($result)){
             $tb .= $row[0];
         }
      }
      return $tb;
   }

$dbconn_carto = pg_connect($connectionstring_carto);
$dbconn = pg_connect($connectionstring_cdix);

$lon = $_GET['lon'];
$lat = $_GET['lat'];
$mallas_string = $_GET['mallas'];

#$mallas="Planeamento,";
#$lat=4800495.9186059;
#$lon=516647.4;

#$lon = 533650;
#$lat = 4723250;
#$mallas_string = "Malla 5000,";

/**
 * Los pasos para recuperar los recursos son:
 *    1.- Obtener el "db_tablename" (tabla asociada a esa malla) en la tabla "_resurce_grids" 
 *    2.- Consultar el "db_codecolumn" usado como link entre las tablas "resources" y los elementos
 *              de la malla. 
 * 	  3.- Obtener el/los code/s de elemento/s en ese punto
 *    4.- Obtener todos los recursos asociados
 *    NOTA: Puede haber multimalla 
 */

if (!isset($mallas_string)){
	echo "No layer selected.";
	return;
}

$malla_array = explode(',',$mallas_string, -1);

$msg='';

$resources = array();

$count = count($malla_array);
for($i=0;$i<$count;$i++){
	$malla_name = trim($malla_array[$i]);
	if (!$malla_name || count_chars($malla_name)==0){
		break;	
	}
	
	/////////////////////////
	//  1)  Get tablename
	/////////////////////////
	$sql = "SELECT a.db_tablename as \"table_name\" 
			FROM resource_grids a, _wms_layers b
			WHERE a.wms_layer = b.wms_layer AND b.layer_name = '".$malla_name."'";
	if ($DEBUG_MODE) {debug($sql);}
	$res = pg_query($dbconn, $sql);
	$count_res = pg_num_rows($res);
	$row = '';
	if ($count_res == 1) {
		$row =  pg_fetch_array($res, 0, PGSQL_ASSOC); 
	}
	$table_name = $row["table_name"];
	if ($DEBUG_MODE) {debug($table_name);}
	/////////////////////////
	//  2)  Get resource layer info and code_column 
	/////////////////////////
	$sql = "SELECT id_layer, db_codecolumn 
			FROM resource_grids
			WHERE db_tablename = '".$table_name."'";
	if ($DEBUG_MODE) {debug($sql);}
	$res = pg_query($dbconn, $sql);
	$count_res = pg_num_rows($res);
	$row = '';
	if ($count_res == 1) {
		$row =  pg_fetch_array($res, 0, PGSQL_ASSOC); 
	}
	$code_column = $row["db_codecolumn"];
	$id_layer = $row["id_layer"];
	
	/////////////////////////
	//  3)  Get code of this resource 
	/////////////////////////
	$sql = "SELECT ".$code_column.' as "code"  
			FROM '.$table_name."
			WHERE st_intersects(the_geom,
			st_setsrid(st_makepoint(". $lon . ", ". $lat ."), st_srid(the_geom)))";		
	if ($DEBUG_MODE) {debug($sql);}
	$res = pg_query($dbconn, $sql);
	
	//if ($DEBUG_MODE) {debug($res);}
	
	//TODO When more than one
	$count_codes = pg_num_rows($res);
	for($j=0;$j<$count_codes;$j++){
		$row = '';
		$row =  pg_fetch_row($res, $j);
		$code = $row[0];
	
		//Get resources path and title
		$sql = "SELECT title_serie, filepath
			FROM resources_view
			WHERE code = '".$code."' 
			AND id_layer = '".$id_layer."'";

		if ($DEBUG_MODE) {
			debug($sql);
		}
		$res2 = pg_query($dbconn, $sql);
		$count_codes2 = pg_num_rows($res2);
		// Identifica de qué MALLA se trata en la respuesta
		array_push($resources, (string)$malla_name . ' ['.$code.']');
		for($k=0;$k<$count_codes2;$k++){
			// Enlace al fichero y el nombre en el listado		
			array_push($resources, pg_fetch_row($res2,$k));
		}
	}	
}

echo json_encode($resources);

?>

