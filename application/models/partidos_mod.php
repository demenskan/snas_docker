<?php

	class partidos_mod extends CI_Model {
		
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}

		function actualizaMarcador ($piTemporada, $piTorneo, $piClave, $piMarcadorLocal, $piMarcadorVisita) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE partidos SET marcador_local=".$piMarcadorLocal.", marcador_visitante=".$piMarcadorVisita
						." WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo." AND clave=".$piClave));
			return ($asResult);
		}

		
		function getCampo($psNombreCampo, $piTemporada,$piTorneo, $piClave) {
			$asResult=$this->tools_lib->consulta(
				array 	('QUERY' => "SELECT ".$psNombreCampo
									." FROM partidos "
									." WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo
									."  AND clave=".$piClave,
					'UNICA_FILA' => true		
					));
			switch ($asResult['ESTATUS']) {
				case 1:
					$sSalida=$asResult['DATOS'][$psNombreCampo];
					break;
				case 0:
					$sSalida="Vacio";
					break;
				case -1:
					$sSalida="Error!";
					break;
			}
			return ($sSalida);
		}
		
		function inserta ($piTemporada, $piClaveTorneo, $piJornada, $piClubLocal, $piClubVisita, $piTipo) {
			$iClaveEstadio=$this->tools_lib->consulta_rapida(array ('tabla' => "clubes",'campo_valor' => "id_estadio",
																	'campo_clave' => "id_unico",'valor' => $piClubLocal));
			$iNuevaClave=$this->tools_lib->trae_ultimo_indice(array (
				'nombre_campo' => "clave", 'tabla' => "partidos", 'condiciones' => "id_temporada=".$piTemporada." AND id_torneo=".$piClaveTorneo
			)); 
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "INSERT IGNORE INTO partidos (id_equipo_local, id_equipo_visitante, marcador_local, marcador_visitante, jornada, clave, id_torneo, id_temporada, jugado, "
					." fecha_programado, fecha_jugado, tipo, id_estadio) "
					." VALUES (".$piClubLocal.",".$piClubVisita.",0,0,".$piJornada.",".$iNuevaClave.",".$piClaveTorneo.", "
					.$piTemporada.", 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00',".$piTipo.",".$iClaveEstadio.")")
			);
			return ($asResult);
		}

		function elimina ($piTemporada, $piTorneo, $piPartido) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "DELETE FROM partidos "
					." WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo
					." AND clave=".$piPartido)
			);
			return ($asResult);
		}

		function inserta_evento ($piTemporada, $piTorneo, $piPartido, $piTipo, $piJugador, $piMinuto) {
			$iNuevaClave=$this->getNuevoIdRelativo($piTemporada,$piTorneo, $piPartido);
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "INSERT INTO eventos (id_temporada, id_torneo, id_partido, id_tipo, id_jugador, minuto, "
					." id_relativo) "
					." VALUES (".$piTemporada.",".$piTorneo.",".$piPartido.",".$piTipo.",".$piJugador.", "
					.$piMinuto.",".$iNuevaClave.")")
			);
			return ($asResult);
		}

		function elimina_evento ($piTemporada, $piTorneo, $piPartido, $piRelativo) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "DELETE FROM eventos "
					." WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo
					." AND id_partido=".$piPartido." AND id_relativo=".$piRelativo)
			);
			return ($asResult);
		}

		function cierra ($psComentarios, $piTemporada, $piTorneo, $piPartido, $psFechaJuego) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE partidos SET jugado=1, fecha_captura=Now(), operador='".$this->session->userdata('sUsuario')."',  "
			  ." comentarios=".$psComentarios.", fecha_jugado='".$psFechaJuego."'"
			  ." WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo." AND clave=".$piPartido)
			);
			return ($asResult);
		}

		
		function designados_update($piTemporada, $piTorneo, $piPartido, $psDesignadoLocal, $psDesignadoVisitante) {
			/* Actualiza la tabla partidos con los designados para 	*/
			$asArgumento=	array 	('QUERY' => "UPDATE  partidos "
					." SET designado_local=".$psDesignadoLocal.", designado_visita=".$psDesignadoVisitante
					." WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo
					."  AND clave=".$piPartido
				);
			//var_dump ($asArgumento);
			$asResult=$this->tools_lib->ejecutar_query($asArgumento);
			return ($asResult);
		}
		
		
		
		function getDatos ($piTemporada, $piTorneo, $piClave) {
			$asResult=$this->tools_lib->consulta(
				array 	('QUERY' => "SELECT cl1.id_unico as 'IDLocal', cl2.id_unico as 'IDVisitante', \n"
					." cl1.ruta_logo as 'LogoLocal', cl2.ruta_logo as 'LogoVisitante', par.id_temporada, \n"
					." par.tipo as 'TipoPartido', par.id_estadio, par.jornada, tor.nombre as 'Torneo', "
					." par.marcador_local, par.marcador_visitante "
					." FROM partidos par \n"
					." INNER JOIN clubes cl1 On cl1.id_unico=par.id_equipo_local \n"
					." INNER JOIN clubes cl2 On cl2.id_unico=par.id_equipo_visitante \n"
					." INNER JOIN cat_tipos_partido ctp On par.tipo=ctp.id_unico \n"
					." INNER JOIN torneos tor On par.id_torneo=tor.clave AND par.id_temporada=tor.id_temporada \n"
					." WHERE par.id_temporada=".$piTemporada." AND par.id_torneo=".$piTorneo
					."  AND par.clave=".$piClave,
					'UNICA_FILA' => true
				)
			);
			return ($asResult);
		}

		function getJornadas ($piTemporada, $piTorneo) {
			$asResult=$this->tools_lib->consulta(
				array 	(
					'QUERY' => "SELECT DISTINCT jornada as 'jornada A', jornada as 'jornada B' FROM partidos WHERE id_torneo=".$piTorneo." AND id_temporada=".$piTemporada
				)
			);
			return ($asResult);
		}

		function getDatosExtendido ($piTemporada, $piTorneo, $piClave) {
			$asResult=$this->tools_lib->consulta(
				array 	('QUERY' => "Select eqa.nombre as nombre_local, eqb.nombre as nombre_visita, "
					."eqa.ruta_logo as logo_local, eqb.ruta_logo as logo_visita, "
					."marcador_local, marcador_visitante, jornada, pa.clave, ces.nombre as nombreestadio, pa.jugado, "
					." pa.id_equipo_local, pa.id_equipo_visitante, eqa.iniciales as 'iniciales_local', eqb.iniciales as 'iniciales_visita', "
					." tor.nombre as 'nombre_torneo', pa.duracion, pa.designado_local, pa.designado_visita "
					."From partidos pa \n"
					."Inner Join equipos eqa On pa.id_equipo_local=eqa.id_unico \n"
					."Inner Join equipos eqb On pa.id_equipo_visitante=eqb.id_unico \n"
					."Inner Join cat_estadios ces On pa.id_estadio=ces.id_unico \n"
					."Inner Join torneos tor On tor.id_temporada=pa.id_temporada and tor.clave=pa.id_torneo \n"
					."Where pa.id_temporada=".$piTemporada." and pa.id_torneo=".$piTorneo
					."		and pa.clave=".$piClave,
					'UNICA_FILA' => true
				)
			);
			return ($asResult);
		}

		function actualizaDatosGenerales ($pasInput) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE partidos SET id_estadio=".$pasInput['ESTADIO'].", id_equipo_local=".$pasInput['EQUIPO_LOCAL']
						.", id_equipo_visitante=".$pasInput['EQUIPO_VISITA'].", tipo=".$pasInput['TIPO'].", jornada=".$pasInput['JORNADA']
			." WHERE id_temporada=".$pasInput['ID_TEMPORADA']." AND id_torneo=".$pasInput['ID_TORNEO']." AND clave=".$pasInput['ID_PARTIDO']));
			return ($asResult);
		}

		function getEventos ($piTemporada, $piTorneo, $piClave) {
			$asResult=$this->tools_lib->consulta(
				array 	('QUERY' => "SELECT jug.nombre, ev.minuto, ctev.imagen, rpt.id_equipo, ctev.descripcion, ev.id_relativo,  \n"
						." ev.id_temporada, ev.id_torneo, ev.id_partido, ev.id_tipo "
						." FROM eventos ev \n"
						." 		INNER JOIN jugadores jug ON ev.id_jugador=jug.id_unico \n"
						." 		INNER JOIN cat_tipos_evento ctev ON ev.id_tipo=ctev.id_unico \n"
						." 		INNER JOIN rostersportemporada rpt ON ev.id_jugador=rpt.id_jugador AND rpt.id_temporada=ev.id_temporada \n"
						."	WHERE \n"
						."		ev.id_temporada=".$piTemporada." AND ev.id_torneo=".$piTorneo."\n"
						."		AND ev.id_partido=".$piClave."\n"
						." ORDER BY minuto \n"));
			return ($asResult);
		}
		
		function getListaJugadores ($piTemporada, $piTorneo, $piPartido) {
			$asResult=$this->tools_lib->consulta(
				array 	('QUERY' => "SELECT cl1.id_unico as 'IDLocal', cl2.id_unico as 'IDVisitante', \n"
					." cl1.ruta_logo as 'LogoLocal', cl2.ruta_logo as 'LogoVisitante', par.id_temporada, \n"
					." par.tipo as 'TipoPartido', par.id_estadio, par.jornada, tor.nombre as 'Torneo' "
					." FROM partidos par \n"
					." INNER JOIN clubes cl1 On cl1.id_unico=par.id_equipo_local \n"
					." INNER JOIN clubes cl2 On cl2.id_unico=par.id_equipo_visitante \n"
					." INNER JOIN cat_tipos_partido ctp On par.tipo=ctp.id_unico \n"
					." INNER JOIN torneos tor On par.id_torneo=tor.clave AND par.id_temporada=tor.id_temporada \n"
					." WHERE par.id_temporada=".$piTemporada." AND par.id_torneo=".$piTorneo
					."  AND par.clave=".$piClave,
					'UNICA_FILA' => true
				)
			);
			return ($asResult);
		}

		function getNuevoIdRelativo ($piTemporada, $piTorneo, $piPartido) {
			$asResult=$this->tools_lib->consulta(
				array 	('QUERY' => "SELECT id_relativo "
						."FROM eventos "
						."WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo
						." AND id_partido=".$piPartido
						." ORDER BY id_relativo DESC  LIMIT 0,1",
						'UNICA_FILA' => true));
			switch ($asResult['ESTATUS']) {
				case 1:
					$iResultado=$asResult['DATOS']['id_relativo']+1;
					break;
				case 0:
					$iResultado=1;
					break;
				case -1:
					$iResultado=-1;
					break;
			}
			return($iResultado);
		}

		function calculaMarcador ($piTemporada, $piTorneo, $piPartido) {
			$asMarcador=array('LOCAL' => 0, 'VISITA' => 0);
			$asPartido=$this->getDatos($piTemporada, $piTorneo, $piPartido);
			$iLocal=$asPartido['DATOS']['IDLocal'];
			$iVisita=$asPartido['DATOS']['IDVisitante'];
			$asEventos=$this->getEventos($piTemporada,$piTorneo, $piPartido);
			if ($asEventos['ESTATUS']==1) {
				for ($i=0;$i<count($asEventos['DATOS']);$i++) {
					if (($asEventos['DATOS'][$i]['id_tipo']=="1")||($asEventos['DATOS'][$i]['id_tipo']=="6")||($asEventos['DATOS'][$i]['id_tipo']=="5")) {
						if ($asEventos['DATOS'][$i]['id_equipo']==$iLocal)
							if (($asEventos['DATOS'][$i]['id_tipo']=="1")||($asEventos['DATOS'][$i]['id_tipo']=="6"))		//gol local
								$asMarcador['LOCAL']++;
							else
								$asMarcador['VISITA']++;						//Autogol local
						else
							if (($asEventos['DATOS'][$i]['id_tipo']=="1")||($asEventos['DATOS'][$i]['id_tipo']=="6"))		//gol visita
								$asMarcador['VISITA']++;
							else
								$asMarcador['LOCAL']++;						//Autogol visita
						//echo $iMarcadorLocal."-".$iMarcadorVisita;		
						//$sResultMarcador=$this->partidos_mod->actualizaMarcador($piTemporada, $piTorneo, $piPartido, $iMarcadorLocal, $iMarcadorVisita);
					}
				}
			}
			return($asMarcador);
		}
		function modifica_duracion ($piTemporada, $piTorneo, $piPartido, $piDuracion) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE  `partidos` SET `duracion`=".$piDuracion
							." WHERE  `id_temporada`=".$piTemporada." AND  `id_torneo`=".$piTorneo
							." AND `clave`=".$piPartido
				));
			return ($asResult);
		}

		function getAlineacionesTitulares ($piTemporada, $piTorneo, $piPartido) {
			$asResult=$this->tools_lib->consulta(
				array 	('QUERY' => "SELECT * "
						."FROM alineaciones "
						."WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo
						." AND id_partido=".$piPartido." AND min_entra=0"
						)
			);
			return($asResult);
		}

		function getAlineacionesCambios ($piTemporada, $piTorneo, $piPartido) {
			$asResult=$this->tools_lib->consulta(
				array 	('QUERY' => "SELECT ali.*, jugA.nombre as 'nombre_sale', jugB.nombre as 'nombre_entra', "
						 ." cl.nombre_corto, cl.ruta_logo"
						." FROM alineaciones ali "
						." INNER JOIN jugadores jugA ON ali.entra_por=jugA.id_unico"
						." INNER JOIN jugadores jugB ON ali.id_jugador=jugB.id_unico"
						." INNER JOIN rostersportemporada rpt ON ali.id_temporada=rpt.id_temporada AND ali.id_jugador=rpt.id_jugador"
						." INNER JOIN clubes cl ON rpt.id_equipo=cl.id_unico "
						."WHERE ali.id_temporada=".$piTemporada." AND ali.id_torneo=".$piTorneo
						." AND ali.id_partido=".$piPartido." AND ali.min_entra>0"
						)
			);
			return($asResult);
		}
		
		function inserta_alineacion ($pasInput) {
			if (isset($pasInput['ENTRA_POR'])) {
				$sCampoEntraPor = ", `entra_por`";
				$sValorEntraPor = ", ".$pasInput['ENTRA_POR'];
			}
			else {
				$sCampoEntraPor = "";
				$sValorEntraPor = "";
			}
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "INSERT INTO `alineaciones` (`id_unico`, `id_temporada`, `id_torneo`, `id_partido`, `id_jugador`, `min_entra`, `min_sale`".$sCampoEntraPor.")"
				    ." VALUES (UUID(), ".$pasInput['TEMPORADA'].", ".$pasInput['TORNEO'].", ".$pasInput['PARTIDO'].","
					.$pasInput['JUGADOR'].", ".$pasInput['MIN_ENTRA'].", ".$pasInput['MIN_SALE'].$sValorEntraPor.")"
				));
			return ($asResult);
		}
		
		function modifica_alineacion ($pasInput) {
			$sSalePor = (isset($pasInput['SALE_POR'])) ? ", `sale_por`=".$pasInput['SALE_POR'] : "";
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE  `alineaciones` SET `min_sale`=".$pasInput['MIN_SALE'].$sSalePor
							." WHERE  `id_temporada`=".$pasInput['TEMPORADA']." AND  `id_torneo`=".$pasInput['TORNEO']
							." AND `id_partido`=".$pasInput['PARTIDO']." AND `id_jugador`= ".$pasInput['JUGADOR']
				));
			return ($asResult);
		}

		function elimina_alineacion ($pasInput) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "DELETE FROM  `alineaciones` "
							." WHERE  `id_temporada`=".$pasInput['TEMPORADA']." AND  `id_torneo`=".$pasInput['TORNEO']
							." AND `id_partido`=".$pasInput['PARTIDO']
				));
			return ($asResult);
		}
	}
?>
