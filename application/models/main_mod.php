<?php

	class main_mod extends CI_Model {
	
		function __construct() {
			parent::__construct();
			$this->load->library('tools_lib');
		}
	
		function menu_navegacion_principales($psSeccion) {
			$asArgs=array ('QUERY' => "SELECT id_unico, texto, url FROM menu_navegacion WHERE id_padre=0 AND seccion=".$psSeccion);
			$asResultado=$this->tools_lib->consulta($asArgs);
			return ($asResultado);
		}	
		
		function menu_navegacion_hijos($piIDPadre) {
			$asArgs=array ('QUERY' => "SELECT texto, url FROM menu_navegacion WHERE id_padre=".$piIDPadre);
			$asResultadoHijos=$this->tools_lib->consulta($asArgs);
			return ($asResultadoHijos);
		}
		
		function insertaBitacoraEventos ($psOperador, $psSeccion, $psObservaciones) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "INSERT INTO bitacora (id_unico, operador, fecha, seccion, observaciones) VALUES"
								." (UUID(), '".$psOperador."', CURRENT_TIMESTAMP(), '".$psSeccion."','".$psObservaciones."')"));
			return ($asResult);
		}

		function bitacora_eventos_lista ($psFechaInicio, $psFechaFin, $piPagina, $piElementosxPagina) {
			$iOffset=($piPagina-1) * $piElementosxPagina;
			$sFiltroFecha= (($psFechaInicio!="ALL") && ($psFechaFin!="ALL")) ? " AND fecha between '".$psFechaInicio."' AND '".$psFechaFin."'" : "";
			$asArgs=array ('QUERY' => "SELECT fecha, observaciones FROM bitacora "
						   ." WHERE seccion='robot-draft' "
						   .$sFiltroFecha
						   ." ORDER BY fecha"
						   ." LIMIT ".$iOffset.",".$piElementosxPagina);
			$asResultado=$this->tools_lib->consulta($asArgs);
			return ($asResultado);
		}

		function bitacora_eventos_conteo ($psFechaInicio, $psFechaFin, $piPagina, $piElementosxPagina) {
			$iOffset=($piPagina-1) * $piElementosxPagina;
			$sFiltroFecha= (($psFechaInicio!="ALL") && ($psFechaFin!="ALL")) ? " AND fecha between '".$psFechaInicio."' AND '".$psFechaFin."'" : "";
			$asArgs=array ('QUERY' => "SELECT count(*) as conteo FROM bitacora "
						   ." WHERE seccion='robot-draft' "
						   .$sFiltroFecha
						   ." ORDER BY fecha", 'UNICA_FILA' => true);
			$asResultado=$this->tools_lib->consulta($asArgs);
			return ($asResultado['DATOS']['conteo']);
		}
	
	}


?>