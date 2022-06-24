<?php

	class bitacora_draft_mod extends CI_Model {
	
		function __construct() {
			parent::__construct();
			$this->load->library('tools_lib');
		}
	
		function inserta ($pasDatos) {
			$sClub=(isset($pasDatos['id_club'])) ? $pasDatos['id_club'] : "NULL";
			$sConsejo=(isset($pasDatos['id_consejo'])) ? $pasDatos['id_consejo'] : "NULL";
			$sJugador=(isset($pasDatos['id_jugador'])) ? $pasDatos['id_jugador'] : "NULL";
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "INSERT INTO bitacora_draft (id_unico, operador, fecha, tipo, id_club, id_jugador, id_consejo, id_temporada, observaciones) VALUES"
								." (UUID(), '".$pasDatos['operador']."', CURRENT_TIMESTAMP(), '".$pasDatos['tipo']."',".$sClub.",".$sJugador.",".$sConsejo.", "
								.$pasDatos['temporada'].",'".$pasDatos['observaciones']."')"));
			return ($asResult);
		}

		function lista ($psFechaInicio, $psFechaFin, $piPagina, $piElementosxPagina) {
			$iOffset=($piPagina-1) * $piElementosxPagina;
			$sFiltroFecha= (($psFechaInicio!="ALL") && ($psFechaFin!="ALL")) ? " AND fecha between '".$psFechaInicio."' AND '".$psFechaFin."'" : "";
			$iTemporada=$this->config->item('temporada_actual');
			$asArgs=array ('QUERY' => "SELECT bd.fecha, bd.tipo, cl.nombre_corto as club, jug.nombre as jugador, con.iniciales as consejo, "
									."bd.id_temporada, observaciones "
									."FROM bitacora_draft bd "
									." LEFT JOIN 	clubes cl ON bd.id_club=cl.id_unico "
									." LEFT JOIN jugadores jug ON bd.id_jugador=jug.id_unico "
									." LEFT JOIN cat_consejos con ON bd.id_consejo=con.id_unico "
						   ." WHERE id_temporada=".$iTemporada
						   .$sFiltroFecha
						   ." ORDER BY fecha"
						   ." LIMIT ".$iOffset.",".$piElementosxPagina);
			$asResultado=$this->tools_lib->consulta($asArgs);
			return ($asResultado);
		}

		function conteo ($psFechaInicio, $psFechaFin, $piPagina, $piElementosxPagina) {
			$iOffset=($piPagina-1) * $piElementosxPagina;
			$sFiltroFecha= (($psFechaInicio!="ALL") && ($psFechaFin!="ALL")) ? " AND fecha between '".$psFechaInicio."' AND '".$psFechaFin."'" : "";
			$iTemporada=$this->config->item('temporada_actual');
			$asArgs=array ('QUERY' => "SELECT count(*) as conteo FROM bitacora_draft "
						   ." WHERE id_temporada=".$iTemporada
						   .$sFiltroFecha
						   ." ORDER BY fecha", 'UNICA_FILA' => true);
			$asResultado=$this->tools_lib->consulta($asArgs);
			return ($asResultado['DATOS']['conteo']);
		}
	
	}


?>