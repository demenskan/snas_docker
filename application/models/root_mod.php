<?php

	class root_mod extends CI_Model {
		
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}

		function ActualizaPuntos ($piPuntos, $piJugador) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE jugadores SET total_puntos=".$piPuntos." WHERE id_unico=".$piJugador));
			return ($asResult);
		}

		function getCamposHabilidades() {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT campo, valor_peso FROM graficasjugadores WHERE tipo=3 OR tipo=4 OR tipo=6"));
				return ($asResult);
		}

		function getHabilidadesJugadores($piOffset) {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT id_unico, precision_pie_malo, frecuencia_pie_malo, ataque, defensa, balance, \n"
						." estamina, velocidad_maxima, aceleracion, respuesta, agilidad, precision_dribble, velocidad_dribble, \n"
						." precision_pase_corto, velocidad_pase_corto, precision_pase_largo, velocidad_pase_largo, precision_tiro, potencia_tiro, \n"
						." tecnica_tiro, precision_tiro_libre, chanfle, cabezeo, salto, tecnica, agresividad, mentalidad, constancia, \n"
						." habilidad_portero, trabajo_equipo, condicion_fisica, dribble, antidribble, post_player, posicionamiento, \n"
						." reaccion, posicionamiento_linea, goleo, crea_jugadas, pasando, finta_ambos_pies, penalties, contundencia, \n"
						." saque_largo, pases_1ra_intencion, lado, centro, exterior, marcaje, control_linea_def, barrida, patada_directa_portero, \n"
						." para_penales, parador_1a1, tolerancia_lesiones, tipo_tiro_libre, nombre_club \n"
						." FROM jugadores jug \n"
						." ORDER BY id_unico LIMIT ".$piOffset.",100"
				));
			return ($asResult);
		}
		
		function roster_conteo($piClub, $piTemporada) {
			/* Devuelve un conteo de jugadores de un club y una temporada en particular
			*/
			$asResult=$this->tools_lib->consulta (array(
				'QUERY' => "SELECT count(*) as 'conteo'  "
					." FROM rostersportemporada rpt "
					." WHERE rpt.id_temporada=".$piTemporada
					."  AND rpt.id_equipo=".$piClub,
				'UNICA_FILA' => true
				));
			return ($asResult);
		}
		
		
		function roster_lista($piClub, $piTemporada) {
			/* Devuelve un conteo de jugadores de un club y una temporada en particular
			*/
			$asResult=$this->tools_lib->consulta (array(
				'QUERY' => "SELECT rpt.numero, jug.nombre, jug.id_unico  "
					." FROM rostersportemporada rpt "
					." INNER JOIN jugadores jug ON rpt.id_jugador=jug.id_unico "
					." WHERE rpt.id_temporada=".$piTemporada
					."  AND rpt.id_equipo=".$piClub
				));
			return ($asResult);
		}
		
		function roster_historial($piJugador) {
			/* Devuelve el historial de clubes por los que ha pasado un jugador
			*/
			$asResult=$this->tools_lib->consulta (array(
				'QUERY' => "SELECT rpt.id_temporada, cl.nombre_corto as 'club', cl.id_unico as 'id_club'  "
					." FROM rostersportemporada rpt "
					." INNER JOIN clubes cl ON rpt.id_equipo=cl.id_unico "
					." WHERE rpt.id_jugador=".$piJugador
					." ORDER BY rpt.id_temporada"
				));
			return ($asResult);
		}
		

	}
?>