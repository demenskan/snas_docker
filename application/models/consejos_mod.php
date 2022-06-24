<?php

	class consejos_mod extends CI_Model {
	
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}
		
		function RegresaNombre($psClave) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT iniciales FROM cat_consejos WHERE id_unico=".$psClave,
						'UNICA_FILA' => true)
			);
			if ($asResult['ESTATUS']==1) 
				return ($asResult['DATOS']['iniciales']);
			else
				return ($asResult['ERROR']);
		}

		function RegresaDatosPorURL($psClave) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT * "
								."FROM cat_consejos "
								."WHERE iniciales='".$psClave."'",
						'UNICA_FILA' => true)
			);
			return ($asResult);
		}
		
		function getDatosPorIniciales($psSearch) {
			$asResult=$this->tools_lib->consulta(array ('QUERY' => "SELECT id_unico, iniciales FROM cat_consejos WHERE iniciales LIKE '%".$psSearch."%'"));
			return ($asResult);
		}
		
		function getDatosPorOperador($psOperador) {
			$asResult=$this->tools_lib->consulta(array ('QUERY' => "SELECT cc.* "
				." FROM cat_consejos cc "
				." INNER JOIN operadores op ON cc.id_unico=op.consejo "
				." WHERE op.login='".$psOperador."'",
				'UNICA_FILA' => true));
			return ($asResult);
		}
		
		function getLista() {
			$asResult=$this->tools_lib->consulta(array (
				'QUERY' => "SELECT cc.* "
				." FROM cat_consejos cc "));
			return ($asResult);
		}
		
		function getListaClubes($piConsejo, $pbApadrinadosTambien=false) {
			$sQyApadrinados= ($pbApadrinadosTambien==true) ? " OR administrado_por=".$piConsejo : "";
			$sQyConsejo=($piConsejo<>-1) ? " WHERE id_consejo=".$piConsejo : "" ;
			$asResult=$this->tools_lib->consulta(array (
				'QUERY' => "SELECT * "
				." FROM clubes "
				.$sQyConsejo
				.$sQyApadrinados));
			return ($asResult);
		}
		
		function Noticias_lista ($piConsejo) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT nt.id_unico, nt.titulo, nt.resumen, nt.imagen "
								."FROM noticias nt "
								."INNER JOIN tags_noticias tag ON nt.id_unico=tag.id_docto "
								."WHERE tag.tipo_docto=1 AND tag.campo='consejo' AND tag.valor='".$piConsejo."'"
								."ORDER BY fecha DESC LIMIT 0,12")
			);
			return ($asResult);
		}


	}
?>
