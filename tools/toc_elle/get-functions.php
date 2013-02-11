<?php

include '../../php/config.php';

class GetFeatures extends AccessBBDD {
	
	function __construct($connectionstring_cdix, $connectionstring_carto) {
		parent::__construct($connectionstring_cdix, $connectionstring_carto);
	}
	
	public function getELLELayers($map_name){
		global $DEBUG_MODE;
		$sqlquery = 'SELECT nombre_capa, nombre_tabla, posicion, visible, grupo '.
		'FROM _map WHERE mapa = \''.$map_name.'\' order by posicion';
		if($DEBUG_MODE){debug($sqlquery);}
		$result = pg_query($this -> conn, $sqlquery);
		return pg_fetch_all($result);
	}
	
	public function getWMSLayers($map_name){
		global $DEBUG_MODE;
		$sqlquery = 'SELECT map_name, layer_name, wms_layer, wms_url, visible, 
		layer_position, layer_group, parameters, is_base '.
		'FROM _wms_layers WHERE map_name = \''.$map_name.'\' order by layer_position desc';
		if($DEBUG_MODE){debug($sqlquery);}
		$result = pg_query($this -> conn, $sqlquery);
		return pg_fetch_all($result);
	}
	
	public function getGroups($map_name){
		global $DEBUG_MODE;
		// TODO: Remove this commented old code 
		// $sqlquery = "SELECT grupo as \"group\", posicion as \"position\" 
			// FROM _map
			// UNION
			// SELECT layer_group, layer_position
			// from _wms_layers
			// ORDER BY position;";
		$sqlquery = "SELECT layer_group as \"group\", layer_position as \"position\"
			from _wms_layers
			ORDER BY position;";
		$result = pg_query($this -> conn, $sqlquery);
		return pg_fetch_all($result);
	}
}
?>