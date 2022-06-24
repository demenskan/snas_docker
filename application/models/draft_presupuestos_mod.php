<?php

	class draft_presupuestos_mod extends CI_Model {
		
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}
		
		function clubes_lista($psGenero, $psClave, $piTemporada) {
			switch ($psGenero) {
				case "__EXTRA__":
					$sCondicion=" AND of.temporada=".$piTemporada;
					break;
				case "consejo":
					$sCondicion=" AND  cl.id_consejo=".$psClave." AND of.temporada=".$piTemporada;
					break;
				case "liga":
					$sCondicion=" AND cl.id_unico IN (Select aco.id_equipo From acomodo_grupos aco where  aco.id_torneo=".$psClave." AND aco.id_temporada=".$piTemporada.")";
					break;
			}
			$asResultado=$this->tools_lib->consulta (array
				('QUERY' => "SELECT cl.nombre_corto as 'nombre_club', "
							." cantidad as presupuesto "
							." FROM operaciones_financieras of "
							." INNER JOIN clubes cl ON of.codigo_entidad_receptora=cl.id_unico "
							." WHERE "
							."  (of.tipo_entidad_emisora=2 OR of.tipo_entidad_emisora=1) "
							." AND of.tipo_entidad_receptora=3 "
							.$sCondicion
							." ORDER BY presupuesto DESC"
				 ));
			return ($asResultado);
		}

	}
?>