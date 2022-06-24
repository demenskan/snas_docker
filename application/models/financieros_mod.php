<?php

	class financieros_mod extends CI_Model {
		
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}

		
		function getOperacion($piTemporada, $piTipoEmisora, $piCodigoEmisora, $piTipoReceptora, $piCodigoReceptora) {
				/*function getExistenciaAsignacion($piClub, $piTemporada, $piConsejo) {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT referencia "
				." FROM operaciones_financieras "
				." WHERE temporada=".$piTemporada
				." AND tipo_entidad_receptora=3 "
				." AND codigo_entidad_receptora=".$piClub
				." AND tipo_entidad_emisora=2 "
				." AND codigo_entidad_emisora=".$piConsejo,
				'UNICA_FILA' => true
				));*/
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT referencia, cantidad "
				." FROM operaciones_financieras "
				." WHERE temporada=".$piTemporada
				." AND tipo_entidad_receptora=".$piTipoReceptora
				." AND codigo_entidad_receptora=".$piCodigoReceptora
				." AND tipo_entidad_emisora=".$piTipoEmisora
				." AND codigo_entidad_emisora=".$piCodigoEmisora, 
				'UNICA_FILA' => true));
			return ($asResult);
		}
		
		function insertaOperacion($piTemporada, $pfCantidad,  $piTipoEmisora, $piCodigoEmisora, $piTipoReceptora, $piCodigoReceptora) {
			/*function insertaAsignacion($piClub, $piTemporada, $piConsejo, $pfCantidad) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "INSERT INTO operaciones_financieras "
				 ."(referencia, tipo_entidad_emisora, codigo_entidad_emisora, tipo_entidad_receptora, codigo_entidad_receptora, cantidad, temporada)"
				 ." VALUES (UUID_SHORT(), 2, ".$piConsejo.", 3,".$piClub.",".$pfCantidad.",".$piTemporada.")"
				 ));*/
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "INSERT INTO operaciones_financieras "
				 ."(referencia, tipo_entidad_emisora, codigo_entidad_emisora, tipo_entidad_receptora, codigo_entidad_receptora, cantidad, temporada)"
				 ." VALUES (UUID_SHORT(), ".$piTipoEmisora.", ".$piCodigoEmisora.", ".$piTipoReceptora.", ".$piCodigoReceptora.", ".$pfCantidad.",".$piTemporada.")"
				));
			return ($asResult);		
		}

		function actualizaOperacion($psReferencia, $pfCantidad) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE operaciones_financieras "
				 ." SET cantidad=".$pfCantidad
				 ." WHERE referencia='".$psReferencia."'"
				));
			return ($asResult);		
		}
		
		function getContrato($piClub, $piJugador) {
			$sQuery="SELECT * "
			." From contratos_jugadores "
			." WHERE id_club=".$piClub." AND id_jugador=".$piJugador."";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery, 'UNICA_FILA' => true));
			return ($asResult);
		}
		
		function borraContrato($piClave) {
			$sQuery="DELETE FROM contratos_jugadores "
					." WHERE id_unico='".$piClave."'";
			$asResult=$this->tools_lib->ejecutar_query (array ('QUERY' => $sQuery));
			return ($asResult);
		}
		
		function insertaContrato ($piTemporada, $piClub, $pasDatos) {
			switch ($pasDatos[1]) {
				case 'B':
					$sTipo='BA';
					break;
				case 'F':
					$sTipo='FR';
					break;
				case 'S':
					$sTipo='SU';
					break;
			}
			$sQuery="INSERT INTO contratos_jugadores (id_unico, temporada_inicio, id_jugador, id_club, precio_base, duracion, tipo, estatus, posicion_temporal) "
					." VALUES (UUID(),".$piTemporada.",".$pasDatos[0].",".$piClub.",".$pasDatos[3].",".$pasDatos[2].",'".$sTipo."',1, 99)";
			$asResult=$this->tools_lib->ejecutar_query (array ('QUERY' => $sQuery));
			return ($asResult);
		}
		

	
	}
?>