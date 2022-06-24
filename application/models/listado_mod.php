<?php

	class listado_mod extends CI_Model {
	
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}

		function ListaClubes () {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT * From clubes WHERE id_unico<>0 ORDER BY id_unico")
			);
			return ($asResult);
		}

				
		function RegresaNombre($psClave) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT nombre_corto FROM equipos WHERE id_unico=".$psClave,
						'UNICA_FILA' => true)
			);
			if ($asResult['ESTATUS']==1) 
				return ($asResult['DATOS']['nombre_corto']);
			else
				return ($asResult['ERROR']);
		}
	
	}
?>