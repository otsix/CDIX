<?php
/**
 *
 */
class SINAException extends Exception
{
	var $operation;
	var $tabla;
	
    public function __construct($operation, $tabla, $message, $code = 0, Exception $previous = null) {
    	
		$this->operation = $operation;
		$this->tabla = $tabla;
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getOperation() {
        return $this->operation;
    }
	
	public function getTable() {
        return $this->tabla;
    }
}

class AccessBBDD {
	
	protected $connectionstring_cdix;
	protected $conn;

	protected $connectionstring_carto;
	protected $conn_carto;
	
	function __construct($conn_string_cdix, $conn_string_carto) {
		$this -> connectionstring_cdix = $conn_string_cdix;
		$this -> connectionstring_carto = $conn_string_carto;
		//debug("cdix_conn_str: ".$this -> connectionstring_cdix);
		//debug("carto_conn_str: ".$this -> connectionstring_carto);
	}

	public function connect() {
		$this -> conn = pg_connect($this -> connectionstring_cdix);
		pg_set_client_encoding($this -> conn, "utf-8");
		if ($this -> conn != NULL) {
			return true;
		} else {
			return false;
		}
	}
	
	public function connect_carto() {
		//debug("cdix_conn_str: ".$this -> connectionstring_carto);
		//debug("carto_conn_str: ".$this -> connectionstring_carto);
		$this -> conn_carto = pg_connect($this -> connectionstring_carto);
		pg_set_client_encoding($this -> conn_carto, "utf-8");
		if ($this -> conn_carto != NULL) {
			return true;
		} else {
			return false;
		}
	}
	
	private function extractcoordinates($valuesarray) {
		$coordinates = "'SRID=23029;POINT (" . $valuesarray['coordinates']['lat'] . " " . $valuesarray['coordinates']['lon'] . ")'";
		return $coordinates;			
	}

	private function extractissuevalues($valuesarray) {
		$issuearray = array();
		array_push($issuearray, "issue", implode(",", array_keys($valuesarray['issue'])), implode(",", array_values($valuesarray['issue'])));

		return $issuearray;
	}

	public function insert($valuesarray) {
		$coordinates = $this->extractcoordinates($valuesarray);
		$issuearray = $this -> extractissuevalues($valuesarray);
		$sqlstring = "INSERT into " . $issuearray[0] . " ( the_geom, " . $issuearray[1] . ") VALUES (" . $coordinates . "," . $issuearray[2] . ") RETURNING id_issue";
		if ($DEBUG_MODE) {
			debug($sqlstring);
		}
		try {
			$result = $this -> execquery($sqlstring);
		} catch (Exception $e) {
			throw new SINAException('insert', 'issue', 'Error al insertar la issue con id=' . $id);
		};
		$id_issue = $result['id_issue'];

		if ($result != 0) {
			foreach ($valuesarray as $key => $value) {
				$valuesarray[$key] = $this->extractBreakLines($valuesarray[$key]);
				if ($key != 'issue' && $key != 'coordinates') {
					$sqlstring = "INSERT into " . $key . " (id_issue, " . implode(",", array_keys($valuesarray[$key])) . 
					") VALUES (" . $id_issue . "," . implode(",", array_values($valuesarray[$key])) . ")";
					if ($DEBUG_MODE) {
						debug($sqlstring);
					}
					try {
						$result = $this -> execquery($sqlstring);
					} catch (Exception $e) {
						throw new SINAException('insert', 'issue', 'Error al insertar la '. $tablaupdate . ' con id_issue=' . $id);
					};
				}
			}
		}
		
		return $id_issue;
	}

	private function extractBreakLines($arr) {
		$arrvalores = array();
		foreach ($arr as $clave => $valor) {
			$arrvalores[$clave] = preg_replace("[\n|\r|\n\r]", '@#!', $valor);
		}
		return $arrvalores;
	}
	
	public function remove($id)
	{
		$sqlstring = "UPDATE issue set id_status=0, active=false where id_issue = " . $id;
		if ($DEBUG_MODE) {
			debug($sqlstring);
		}
		try {
			$result = $this -> execquery($sqlstring);
		} catch (Exception $e) {
			throw new SINAException('delete', 'issue', 'Error al eliminar la issue con id=' . $id);
		};
	}
	
	public function update($id, $valuearray)
	{
		$issueupdate = '';
		foreach ($valuearray['issue'] as $key => $value) {
			$issueupdate = $issueupdate . $key . '=' . $value . ',';
		}
		$issueupdate = substr($issueupdate, 0, -1);
		$sqlstring = 'UPDATE issue set ' .  $issueupdate . ' where id_issue=' . $id . ' RETURNING id_issue';
		debug($sqlstring);
		try {
			$result = $this -> execquery($sqlstring);
			$id_issue = $result['id_issue'];
		} catch (Exception $e) {
			throw new SINAException('update', 'issue', 'Error al actualizar la issue con id=' . $id);
		};
		

		if ($result != 0) {
			foreach ($valuearray as $tabla => $datos) {
				if ($tabla != 'issue' && $tabla != 'coordinates') {
					$tablaupdate = '';
					foreach ($valuearray[$tabla] as $key => $value) {
						$tablaupdate = $tablaupdate . $key . '=' . preg_replace("[\n|\r|\n\r]", '@#!', $value) . ',';
					}
					$tablaupdate = substr($tablaupdate, 0, -1);
					$sqlstring = 'UPDATE ' . $tabla . ' set ' . $tablaupdate . ' where id_issue=' . $id;
					if ($DEBUG_MODE) {
						debug($sqlstring);
					}
					try {
						$this -> execquery($sqlstring);
					} catch (Exception $e) {
						throw new SINAException('update', $tablaupdate, 'Error al actualizar la '. $tablaupdate . ' con id_issue=' . $id);
					};				
				}
			}
		}
		return $id_issue;
	}

	public function execquery($sql) {
		$result = pg_query($this -> conn, $sql);
		$resultados = pg_fetch_assoc($result);

		return $resultados;
	}
	
	public function closeconnection() {
		return pg_close($this -> conn);
	}

}
?>