<?php

	class draft_mod extends CI_Model {
		
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}

		function ActualizaJugador ($piJugador, $piClubNuevo) {
			/*$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE rosters_draft SET id_club=Null, id_consejo=".$piConsejo." WHERE id_jugador=".$piJugador	));*/
			$iTemporada=$this->config->item('temporada_actual');
			$asContratoActual=$this->getContratoJugador($iTemporada,$piJugador);
			
			
			
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE rosters_draft SET id_club=Null, id_consejo=".$piConsejo." WHERE id_jugador=".$piJugador	));
			return ($asResult);
		}

		function ActualizaPosicion ($piPosicion, $piJugador) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE contratos_jugadores SET posicion_temporal=".$piPosicion." WHERE id_jugador=".$piJugador	));
			return ($asResult);
		}

		function AutorizaPropuesta ($piClave) {
			$qy="UPDATE propuestasdraft_titulo SET estatus=1, fecha_autorizacion=Now() WHERE id_unico='".$piClave."'";
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => $qy));
			return ($asResult);
		}

		function CancelaPropuestasInvolucradas ($piJugador, $piConsejo) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE propuestasdraft_titulo pdt "
						." INNER JOIN propuestasdraft_jugadores pdj On pdt.id_unico=pdj.id_propuesta "
						." SET pdt.estatus=4 "
						." WHERE pdj.id_jugador=".$piJugador
						." AND pdt.consejo_origen=".$piConsejo
						." AND pdt.estatus=0 "));
			return ($asResult);
		}

		function CancelaPropuesta($piClave) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE propuestasdraft_titulo SET estatus=3 WHERE id_unico=".$piClave
				));
			return ($asResult);
		}

		function Contrato_cancela($psIdContrato, $piRazon) {
			$asResult=$this->tools_lib->ejecutar_query (array
						('QUERY' => "UPDATE contratos_jugadores SET estatus=99, razon_cancelacion=".$piRazon
						." WHERE id_unico='".$psIdContrato."'")
				);
			return ($asResult);
		}
		
		function Contrato_update ($psId, $piClubDestino) {
			$asResult=$this->tools_lib->ejecutar_query (array(
				 'QUERY' => "UPDATE contratos_jugadores SET id_club=".$piClubDestino.", posicion_temporal=99 WHERE id_unico='".$psId."'"
				 ));
			return ($asResult);
		}

		
		function GeneraTituloPropuesta ($piOrigen, $piDestino, $piConsejoOrigen, $piConsejoDestino) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "INSERT INTO propuestasdraft_titulo (id_unico, temporada, fecha_creacion, estatus, club_origen, club_destino, "
				." consejo_origen, consejo_destino)"
				." VALUES (UUID_SHORT(),".$this->config->item('temporada_actual').",Now(),0,".$piOrigen.",".$piDestino.", "
				.$piConsejoOrigen.", ".$piConsejoDestino.")")
			);
			if ($asResult['ESTATUS']==1) {
				$asID=$this->tools_lib->consulta(array(
					'QUERY' => "SELECT id_unico FROM propuestasdraft_titulo ORDER BY fecha_creacion DESC LIMIT 0,1",
					'UNICA_FILA' => true
				));
				if ($asID['ESTATUS']==1)
					$asResult['NUEVO_ID']=$asID['DATOS']['id_unico'];
				else
					$asResult['NUEVO_ID']=$asID['ERROR'];
			}
			return ($asResult);
		}


		function getCamposHabilidades($piModo=0) {
			$sCondicion=($piModo==0) ? " WHERE tipo<>2 " : "";
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT campo, IF (boleano='T',Concat(leyenda, ' [0-1]'),leyenda) as leyenda FROM graficasjugadores \n"
							.$sCondicion." ORDER BY Orden"
				));
			return ($asResult);
		}

		function getClubesAdministrados($pbSoloPropios=false) {
			$sCondicionSoloPropios=($pbSoloPropios==false) ? "OR cl.administrado_por=op.consejo" : "" ;
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT cl.id_unico, cl.nombre_corto, cl.division "
				." FROM operadores op "
				." INNER JOIN clubes cl ON op.consejo=cl.id_consejo ".$sCondicionSoloPropios
				." WHERE op.login='".$this->session->userdata('sUsuario')."'"
				));
			return ($asResult);
		}

		function getConsejosDemas ($piConsejo) {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT cc.* "
				." FROM cat_consejos cc "
				." INNER JOIN operadores ope ON cc.id_unico=ope.consejo"
				." WHERE id_unico<>'".$piConsejo."'"
				));
			return ($asResult);
		}
		
		function getDisponiblesClub($piClub) {
			if ($piClub=="__EXTRA__") {
				$this->load->model('consejos_mod');
				$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
				$sCondicion="rd.id_club IS NULL AND rd.id_consejo=".$asConsejo['DATOS']['id_unico'];
			}
			else
				$sCondicion="rd.id_club=".$piClub;
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT jug.id_unico, jug.nombre, rd.disponible "
								." FROM jugadores jug "
								." INNER JOIN rosters_draft rd On jug.id_unico=rd.id_jugador"
								." WHERE ".$sCondicion)
			);
			return($asResult);
		}
		
		function getHabilidadesPorBloque ($piFiltro, $psOrden="") {
			if ($piFiltro==0) {
				$sComplemento="INNER JOIN clubes cl ON jug.nombre_club=cl.nombre_corto";
			}
			elseif (($piFiltro>=1)&&($piFiltro<=5)) {
				$sComplemento=" INNER JOIN rosters_draft rd on rd.id_jugador=jug.id_unico \n"
				." LEFT JOIN clubes cl on rd.id_club=cl.id_unico \n"
				." WHERE cl.id_consejo=".$piFiltro."  \n"
				." 	OR rd.id_consejo=".$piFiltro;
			}
			elseif ($piFiltro>=8) {
				$sComplemento=" INNER JOIN rosters_draft rd on rd.id_jugador=jug.id_unico \n"
				." LEFT JOIN clubes cl on rd.id_club=cl.id_unico \n"
				." WHERE cl.id_unico=".$piFiltro;
			}
			$sOrden=($psOrden!="") ? " ORDER BY ".$psOrden." DESC" : "";
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT jug.* "
				." FROM jugadores jug "
				.$sComplemento
				.$sOrden
				));
			return ($asResult);
		}

		function getHabilidadesJugadores ($piTemporada, $piClub) {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT jug.id_unico, jug.nombre, jug.posicion_registrada, cpo.iniciales_esp, jug.total_puntos, "
				 ." con.duracion, con.temporada_inicio, con.precio_base, con.tipo "
				." FROM jugadores jug "
				." INNER JOIN contratos_jugadores con On jug.id_unico=con.id_jugador "
				." INNER JOIN cat_posiciones cpo On jug.posicion_registrada=cpo.id_unico "
				." WHERE con.id_club=".$piClub." AND (con.temporada_inicio + con.duracion) > ".$piTemporada
				."  AND con.estatus=1 "
				." ORDER BY jug.posicion_registrada"
				));
			return ($asResult);
		}

		function getHabilidadesIndividuales ($piJugador) {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT * "
				." FROM jugadores jug "
				." WHERE jug.id_unico=".$piJugador, 'UNICA_FILA' => true
			));
			return ($asResult);
		}
		
		function getPosiciones() {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT id_unico, descripcion "
				." FROM cat_posiciones "
				));
			return ($asResult);
		}

		function getPosicionesTemporales ($piClub) {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT jug.id_unico, jug.nombre, jug.posicion_registrada, cpo.iniciales_esp, "
				." con.posicion_temporal as posicion, jug.total_puntos, cpo2.iniciales as 'iniciales_juego' "
				." FROM jugadores jug "
				." INNER JOIN contratos_jugadores con On jug.id_unico=con.id_jugador "
				." INNER JOIN cat_posiciones cpo On con.posicion_temporal=cpo.id_unico "
				." INNER JOIN cat_posiciones cpo2 On jug.posicion_registrada=cpo2.id_unico "
				." WHERE con.id_club=".$piClub." AND con.estatus=1"
				." ORDER BY posicion"
				));
			return ($asResult);
		}

		function getPresupuestoClub($piTemporada, $piClub) {
			$asIngresos=$this->tools_lib->consulta (array
				('QUERY' => "SELECT SUM(cantidad) as cantidad "
				." FROM operaciones_financieras "
				." WHERE temporada=".$piTemporada
				." AND tipo_entidad_receptora=3 "
				." AND codigo_entidad_receptora=".$piClub,
				'UNICA_FILA' => true
				));
			$asEgresos=$this->tools_lib->consulta (array
				('QUERY' => "SELECT SUM(cantidad) as cantidad "
				." FROM operaciones_financieras "
				." WHERE temporada=".$piTemporada
				." AND tipo_entidad_emisora=3 "
				." AND codigo_entidad_emisora=".$piClub,
				'UNICA_FILA' => true
				));
			$fIngresos=($asIngresos['ESTATUS']==1) ? $asIngresos['DATOS']['cantidad'] : 0;
			$fEgresos=($asEgresos['ESTATUS']==1) ? $asEgresos['DATOS']['cantidad'] : 0;
			$fSalida=$fIngresos-$fEgresos;
			return ($fSalida);
		}

		function getPresupuestoClubDesignado($piTemporada, $piClub) {
			$asIngresos=$this->tools_lib->consulta (array
				('QUERY' => "SELECT SUM(cantidad) as cantidad "
				." FROM operaciones_financieras "
				." WHERE temporada=".$piTemporada
				." AND (tipo_entidad_emisora=2 OR tipo_entidad_emisora=1) "
				." AND tipo_entidad_receptora=3 "
				." AND codigo_entidad_receptora=".$piClub,
				'UNICA_FILA' => true
				));
			$fIngresos=($asIngresos['ESTATUS']==1) ? $asIngresos['DATOS']['cantidad'] : 0;
			return ($fIngresos);
		}
		
		function getPresupuestoClubGastado ($piTemporada, $piClub) {
			$asEgresos=$this->tools_lib->consulta (array
				('QUERY' => "SELECT SUM(cantidad) as cantidad "
				." FROM operaciones_financieras "
				." WHERE temporada=".$piTemporada
				." AND tipo_entidad_emisora=3 "
				." AND codigo_entidad_emisora=".$piClub,
				'UNICA_FILA' => true
				));
			$fEgresos=($asEgresos['ESTATUS']==1) ? $asEgresos['DATOS']['cantidad'] : 0;
			return ($fEgresos);
		}

		function getPresupuestoClubPrometido ($piTemporada, $piClub) {
			$asEgresos=$this->tools_lib->consulta (array
				('QUERY' => "SELECT SUM(sueldo_base) as cantidad "
				." FROM draft_ofertas "
				." WHERE id_temporada=".$piTemporada
				." AND estatus=1 "
				." AND id_club=".$piClub,
				'UNICA_FILA' => true
				));
			$fEgresos=($asEgresos['ESTATUS']==1) ? $asEgresos['DATOS']['cantidad'] : 0;
			return ($fEgresos);
		}
		
		function OperacionesFinancieras_get($piTemporada, $piTipoEmisora, $piCodigoEmisora, $piTipoReceptora, $piCodigoReceptora) {
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
		
		function OperacionesFinancieras_insert($piTemporada, $pfCantidad,  $piTipoEmisora, $piCodigoEmisora, $piTipoReceptora, $piCodigoReceptora) {
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

		function actualizaAsignacion($psReferencia, $pfCantidad) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE operaciones_financieras "
				 ." SET cantidad=".$pfCantidad
				 ." WHERE referencia='".$psReferencia."'"
				));
			return ($asResult);		
		}
		
		function getPresupuestoConsejo($piTemporada, $piConsejo) {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT SUM(cantidad)+0 as cantidad "
				." FROM operaciones_financieras "
				." WHERE temporada=".$piTemporada
				." AND tipo_entidad_receptora=2 "
				." AND codigo_entidad_receptora=".$piConsejo,
				'UNICA_FILA' => true
				));
			return ($asResult);
		}

		
		function getJugadoresPropuesta ($psPropuesta) {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT pdj.id_jugador, pdj.tipo_propuesto, pdt.consejo_origen, pdt.consejo_destino, jug.nombre, "
							."  pdj.cantidad, pdt.club_origen, pdt.club_destino "
							." FROM propuestasdraft_titulo pdt "
							." INNER JOIN propuestasdraft_jugadores pdj On pdt.id_unico=pdj.id_propuesta "
							." LEFT JOIN jugadores jug On pdj.id_jugador=jug.id_unico "
							." LEFT JOIN rosters_draft rd ON rd.id_jugador=pdj.id_jugador "
							." LEFT JOIN clubes cl ON cl.id_unico=rd.id_club "
							." WHERE pdt.id_unico='".$psPropuesta."'"
				));
			return ($asResult);
		}

		function getJugadorConsejo ($piJugador, $piConsejo) {
			$asResult=$this->tools_lib->consulta (array
				('QUERY' => "SELECT * FROM rosters_draft rd "
							." INNER JOIN clubes cl On rd.id_club=cl.id_unico "
							." WHERE id_jugador=".$piJugador." AND cl.id_consejo=".$piConsejo
				));
			return ($asResult);
		}
		
		function InsertaJugadorPropuesta ($psUuid, $piJugador, $piTipo, $piCantidad=0) {
			if ($piCantidad==0) {
				$asResult=$this->tools_lib->ejecutar_query (array
					('QUERY' => "INSERT INTO propuestasdraft_jugadores(id_propuesta, id_jugador, tipo_propuesto) "
								." VALUES (".$psUuid.",".$piJugador.",".$piTipo.")")
				);
			}
			else {
				$asResult=$this->tools_lib->ejecutar_query (array
					('QUERY' => "INSERT INTO propuestasdraft_jugadores(id_propuesta, id_jugador, tipo_propuesto, cantidad) "
								." VALUES (".$psUuid.",".$piJugador.",".$piTipo.",".$piCantidad.")")
				);
			}
			return ($asResult);
		}

		function InsertaMensajePropuesta ($psUuid, $psMensaje, $piOrigen) {
			$asRelativo=$this->tools_lib->consulta (array
				('QUERY' => "SELECT id_relativo "
				." FROM propuestasdraft_mensajes "
				." WHERE id_propuesta='".$psUuid."'"
				." ORDER BY id_relativo DESC LIMIT 0,1",
				'UNICA_FILA' => true
				));
			$iRelativo=($asRelativo['ESTATUS']==1) ? $asRelativo['DATOS']['id_relativo']+1 : 1 ;
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "INSERT INTO propuestasdraft_mensajes (id_propuesta,id_relativo, mensaje, origen) "
								." VALUES (".$psUuid.",".$iRelativo.",'".$psMensaje."',".$piOrigen.")")
			);
			return ($asResult);
		}

		function ReiniciaTemporalDraft() {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "Delete from rosters_draft")
			);
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "Insert into rosters_draft (id_jugador, id_club, id_consejo, disponible)"
							."(Select id_jugador, id_equipo, Null, 0 From rostersportemporada where id_temporada=".$this->config->item('temporada_actual').")")
			);
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "Update `propuestasdraft_titulo` set `estatus`=0, `fecha_autorizacion`=NULL")
			);
			return ($asResult);
		}		
		
		function ReseteaDisponibles ($piClub) {
			if ($piClub=="__EXTRA__") {
				$this->load->model('consejos_mod');
				$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
				$sCondicion="id_club IS NULL AND id_consejo=".$asConsejo['DATOS']['id_unico'];
			}
			else
				$sCondicion="id_club=".$piClub;
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE rosters_draft SET disponible=0 WHERE ".$sCondicion)
			);
			return ($asResult);
		}

		function setDisponible ($piClaveJugador) {
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => "UPDATE rosters_draft SET disponible=1 WHERE id_jugador=".$piClaveJugador)
			);
			return ($asResult);
		}
		
		function getPropuestas ($psModo)	{
			//Para diferenciar entre las propuestas de otros y las propias
			$sCondicionModo=($psModo=="propias") ? "consejo_origen": "consejo_destino";
			$sCliente=($psModo=="propias") ? "consejo_destino": "consejo_origen";
			$sQuery="SELECT pdt.id_unico, clo.id_unico as 'club_id', cld.iniciales, jug.nombre, pdj.tipo_propuesto, pdj.id_jugador, \n"
				." pdt.fecha_creacion, cliente.login as 'cliente', pdt.consejo_origen, pdt.consejo_destino, pdt.estatus, pdm.mensaje,  \n"
				." pdj.cantidad, clo.iniciales as 'club_origen' , cld.iniciales as 'club_destino' "
				." FROM propuestasdraft_titulo pdt \n"
				." INNER JOIN propuestasdraft_jugadores pdj On pdt.id_unico=pdj.id_propuesta \n"
				." LEFT JOIN jugadores jug On pdj.id_jugador=jug.id_unico \n"
				." LEFT JOIN clubes clo On pdt.club_origen=clo.id_unico \n"
				." LEFT JOIN clubes cld On pdt.club_destino=cld.id_unico \n"
				." INNER JOIN operadores ope On pdt.".$sCondicionModo."=ope.consejo \n"
				." INNER JOIN operadores cliente On pdt.".$sCliente."=cliente.consejo \n"
				." LEFT JOIN propuestasdraft_mensajes pdm On pdt.id_unico=pdm.id_propuesta \n"
				." WHERE ope.login='".$this->session->userdata('sUsuario')."' \n"
				." ORDER BY pdt.fecha_creacion \n";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		/*
				." LEFT JOIN rosters_draft rd On pdj.id_jugador=rd.id_jugador \n"
				." LEFT JOIN clubes cl On rd.id_club=cl.id_unico \n"
		*/
		}
		
		function getPropuestasGeneral($psTemporada) {
			$sQuery="SELECT pdt.id_unico as 'clave_propuesta', cl.id_unico as 'clave_club', cl.iniciales, jug.nombre, pdj.tipo_propuesto, pdj.id_jugador, \n"
				." pdt.fecha_creacion, opOferta.Login as 'ofertante', ope.login as 'involucrado', pdt.consejo_origen, \n"
				." pdt.consejo_destino, pdt.estatus, pdm.mensaje \n"
				." FROM propuestasdraft_titulo pdt \n"
				." INNER JOIN propuestasdraft_jugadores pdj On pdt.id_unico=pdj.id_propuesta \n"
				." INNER JOIN jugadores jug On pdj.id_jugador=jug.id_unico \n"
				." INNER JOIN rostersportemporada rpt On pdj.id_jugador=rpt.id_jugador AND rpt.id_temporada=pdt.temporada \n"
				." INNER JOIN clubes cl On rpt.id_equipo=cl.id_unico \n"
				." INNER JOIN operadores ope On pdt.consejo_destino=ope.Consejo \n"
				." INNER JOIN operadores opOferta On pdt.consejo_origen=opOferta.Consejo \n"
				." LEFT JOIN propuestasdraft_mensajes pdm On pdt.id_unico=pdm.id_propuesta \n"
				." WHERE pdt.temporada=".$psTemporada
				." ORDER BY pdt.id_unico \n";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}

		function getMovimientosJugadores($psClub, $piModo) {
			/*
			if ($piModo=='altas') {
				$sCampoBusqueda="rd.id_club";
				$sNullCond="";
				$sClubOpuesto="rpt.id_equipo";
			}
			else {
				$sCampoBusqueda="rpt.id_equipo";
				$sNullCond=" OR rd.id_club is NULL";
				$sClubOpuesto="rd.id_club";
			}
			$sQuery="SELECT jug.nombre, cl.nombre_corto, con.iniciales as 'consejo'  \n"
					." FROM rosters_draft rd \n"
					." INNER JOIN jugadores jug On rd.id_jugador=jug.id_unico\n"
					." INNER JOIN rostersportemporada rpt On rd.id_jugador=rpt.id_jugador AND (rd.id_club<>rpt.id_equipo".$sNullCond.") AND rpt.id_temporada=".$this->config->item('temporada_actual')."\n"
					." LEFT JOIN clubes cl On ".$sClubOpuesto."=cl.id_unico \n"
					." LEFT JOIN cat_consejos con ON rd.id_consejo=con.id_unico \n"
					." WHERE ".$sCampoBusqueda."=".$psClub." \n";

			*/
			if ($piModo=='altas') {
				$sCampoBusqueda="con.id_club";
				$sNullCond="";
				$sClubOpuesto="rpt.id_equipo";
			}
			else {
				$sCampoBusqueda="rpt.id_equipo";
				$sNullCond=" OR con.id_jugador NOT IN (select id_jugador from contratos_jugadores where estatus=1)";
				$sClubOpuesto="con.id_club";
			}
			$sQuery="SELECT jug.nombre, cl.nombre_corto  \n"
					." FROM contratos_jugadores con \n"
					." INNER JOIN jugadores jug On con.id_jugador=jug.id_unico\n"
					." INNER JOIN rostersportemporada rpt On con.id_jugador=rpt.id_jugador AND (con.id_club<>rpt.id_equipo".$sNullCond.") AND rpt.id_temporada=(".$this->config->item('temporada_actual')."-1)\n"
					." LEFT JOIN clubes cl On ".$sClubOpuesto."=cl.id_unico \n"
					." WHERE ".$sCampoBusqueda."=".$psClub." \n"
					."  AND con.estatus=1";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}

		function getJugadoresLibresBajas($psClub) {
			$sQuery="SELECT jug.nombre  \n"
					." FROM rostersportemporada rpt  \n"
					." INNER JOIN jugadores jug On rpt.id_jugador=jug.id_unico\n"
					." WHERE rpt.id_equipo=".$psClub." and rpt.id_temporada=(".$this->config->item('temporada_actual')."-1) \n"
					."  AND rpt.id_jugador NOT IN (select id_jugador from contratos_jugadores where estatus=1)";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}
		
		function getJugadoresLibresAltas($psClub) {
			$sQuery="SELECT jug.nombre, jug.nombre_club  \n"
					." FROM contratos_jugadores con \n"
					." INNER JOIN jugadores jug On con.id_jugador=jug.id_unico\n"
					." WHERE con.id_jugador NOT IN (select id_jugador from rostersportemporada where id_temporada=(".$this->config->item('temporada_actual')."-1))"
					."  AND con.estatus=1"
					."  AND con.id_club=".$psClub;
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}
		
		
		function getMovimientosConsejo($psConsejo) {
			if ($psConsejo=='__EXTRA__') 
				$sFilter="";
			else
				$sFilter="WHERE rd.id_consejo=".$psConsejo;
			$sQuery="SELECT jug.nombre, cl.nombre_corto  \n"
					." FROM rosters_draft rd \n"
					." INNER JOIN jugadores jug On rd.id_jugador=jug.id_unico\n"
					." INNER JOIN rostersportemporada rpt On rd.id_jugador=rpt.id_jugador AND (rd.id_club IS NULL) AND rpt.id_temporada=".$this->config->item('temporada_actual')."\n"
					." LEFT JOIN clubes cl On rpt.id_equipo=cl.id_unico \n"
					." LEFT JOIN cat_consejos con ON rd.id_consejo=con.id_unico \n"
					.$sFilter;
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}

		function getListaPreliminar($piClub) {
			$sQuery="SELECT jug.nombre, cpos.iniciales_esp, jug.total_puntos, '' as posicion,  jug.id_unico,  "
			." con.temporada_inicio, con.precio_base "
			." From jugadores jug "
			." INNER JOIN contratos_jugadores con ON con.id_jugador=jug.id_unico "
			." INNER JOIN cat_posiciones cpos ON jug.posicion_registrada=cpos.id_unico "
			." WHERE con.id_club='".$piClub."'"
			." AND con.estatus=1 ";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}

		function getJugadoresSinDefinir($piConsejo) {
			$sQuery="SELECT jug.nombre, cpos.iniciales_esp, jug.total_puntos, rd.posicion, jug.id_unico "
			." From jugadores jug "
			." INNER JOIN rosters_draft rd ON rd.id_jugador=jug.id_unico "
			." INNER JOIN cat_posiciones cpos ON jug.posicion_registrada=cpos.id_unico "
			." WHERE rd.id_club is Null AND rd.id_consejo='".$piConsejo."'  Order By rd.posicion";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}

		function getClubesConsejo($piConsejo) {
			$sQuery="SELECT cl.id_unico, cl.nombre_corto, cl.ruta_logo "
			." FROM clubes cl "
			." WHERE cl.id_consejo='".$piConsejo."'"
			."	OR cl.administrado_por=".$piConsejo
			." ORDER BY nombre_corto";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}

		function getContratoJugador($piTemporadaActual, $piJugador) {
			$asResult=$this->tools_lib->consulta (array	('QUERY' => "SELECT con.*, cl.nombre_corto as nombre_club "
			." FROM contratos_jugadores con "
			." INNER JOIN clubes cl ON con.id_club=cl.id_unico "
			." WHERE con.temporada_inicio+con.duracion >=".$piTemporadaActual
			."	AND con.id_jugador=".$piJugador
			."  AND con.estatus=1 ",
			'UNICA_FILA' => true));
			return ($asResult);
		}

		function getContratoByUUID($psUUID) {
			$asResult=$this->tools_lib->consulta (array	('QUERY' => "SELECT * "
			." FROM contratos_jugadores "
			." WHERE id_unico ='".$psUUID."'",
			'UNICA_FILA' => true));
			return ($asResult);
		}
		
		function getContratados($piTemporadaActual, $piClub) {
			$asResult=$this->tools_lib->consulta (array	('QUERY' => "SELECT con.*, jug.nombre "
			." FROM contratos_jugadores con "
			." INNER JOIN jugadores jug ON con.id_jugador=jug.id_unico"
			." WHERE temporada_inicio+duracion >=".$piTemporadaActual
			."	AND id_club=".$piClub
			."  AND estatus=1 "));
			return ($asResult);
		}
		
		function verificaContrato($piClub, $piJugador, $piTemporada) {
			$asResult=$this->tools_lib->consulta (array	('QUERY' => "SELECT con.*, jug.nombre "
			." FROM contratos_jugadores con "
			." INNER JOIN jugadores jug ON con.id_jugador=jug.id_unico"
			." WHERE temporada_inicio+duracion >=".$piTemporada
			."	AND id_club=".$piClub
			."  AND id_jugador=".$piJugador
			."  AND estatus=1 "));
			return ($asResult);
		}
		
		function getOfertados($piTemporadaActual, $piClub, $piEstatus=0) {
			$sCondicionEstatus=($piEstatus!=0) ? " AND estatus=".$piEstatus : "";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => "SELECT ofe.*, jug.nombre, ceo.descripcion, cl.nombre_corto,  "
			." DATE_ADD(ofe.fecha_oferta, INTERVAL ofe.duracion_oferta MINUTE) as 'tiempo_limite'"
			." FROM draft_ofertas ofe "
			." INNER JOIN jugadores jug ON ofe.id_jugador=jug.id_unico "
			." INNER JOIN cat_estatus_ofertas ceo ON ofe.estatus=ceo.id_unico "
			." INNER JOIN clubes cl ON cl.id_unico=ofe.id_club "
			." WHERE id_temporada =".$piTemporadaActual
			."	AND id_club=".$piClub
			.$sCondicionEstatus
			." GROUP BY ofe.id_jugador "
			));
			return ($asResult);
		}
		
		function getEstatusSubasta($piTemporada, $piJugador, $piClub) {
			$asResult=$this->tools_lib->consulta (array	('QUERY' => "SELECT ofe.*, jug.nombre, ceo.descripcion, cl.nombre_corto,  "
			." DATE_ADD(ofe.fecha_oferta, INTERVAL ofe.duracion_oferta MINUTE) as 'tiempo_limite'"
			." FROM draft_ofertas ofe "
			." INNER JOIN jugadores jug ON ofe.id_jugador=jug.id_unico "
			." INNER JOIN clubes cl ON cl.id_unico=ofe.id_club "
			." WHERE id_temporada =".$piTemporada
			."	AND (estatus=1 O)"
			."  AND jugador=".$piJugador
			." ORDER BY ofe.fecha_oferta DESC"
			));
			return ($asResult);
			
			
		}

		function getOfertadosConsejo($piTemporadaActual, $piConsejo, $piEstatus=0) {
			$sCondicionEstatus=($piEstatus!=0) ? " AND estatus=".$piEstatus : "";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => "SELECT ofe.*, jug.nombre, ceo.descripcion, cl.nombre_corto,  "
			." DATE_ADD(ofe.fecha_oferta, INTERVAL ofe.duracion_oferta MINUTE) as 'tiempo_limite'"
			." FROM draft_ofertas ofe "
			." INNER JOIN jugadores jug ON ofe.id_jugador=jug.id_unico "
			." INNER JOIN cat_estatus_ofertas ceo ON ofe.estatus=ceo.id_unico "
			." INNER JOIN clubes cl ON cl.id_unico=ofe.id_club "
			." WHERE id_temporada =".$piTemporadaActual
			."	AND id_club IN (select id_unico from clubes where id_consejo=".$piConsejo." or administrado_por=".$piConsejo.")"
			.$sCondicionEstatus
			." ORDER BY ofe.fecha_oferta DESC"
			));
			return ($asResult);
		}
		

		function getMejorOferta($piTemporada, $piJugador) {
			$asResult=$this->tools_lib->consulta (array	('QUERY' => "SELECT ofe.*,  cl.nombre_corto as 'nombre_club' "
			." FROM draft_ofertas ofe "
			." INNER JOIN jugadores jug ON ofe.id_jugador=jug.id_unico "
			." INNER JOIN cat_estatus_ofertas ceo ON ofe.estatus=ceo.id_unico "
			." INNER JOIN clubes cl ON cl.id_unico=ofe.id_club "
			." WHERE ofe.id_temporada =".$piTemporada
			."	AND ofe.id_jugador=".$piJugador
			."  AND ofe.estatus=1",
			'UNICA_FILA' => true
			));
			return ($asResult);
		}

		function setMovimiento ($piJugador, $piClubDestino, $piConsejo="") {
			if ($piClubDestino!="__EXTRA__")
				$sQuery="UPDATE rosters_draft SET id_club=".$piClubDestino." WHERE id_jugador=".$piJugador;
			else 
				$sQuery="UPDATE rosters_draft SET id_club=NULL, id_consejo=".$piConsejo." WHERE id_jugador=".$piJugador;
			$asResult=$this->tools_lib->ejecutar_query (array
				('QUERY' => $sQuery)
			);
			return ($asResult);
		}
		
		function insertaBitacoraMovimiento($piJugador, $piClubOrigen, $piClubDestino) {
			$sDestino=($piClubDestino=="__EXTRA__") ? 0 : $piClubDestino;
			$sOrigen=($piClubOrigen=="__EXTRA__") ? 0 : $piClubOrigen;
			$sQuery="INSERT INTO movimientos_internos (claveJugador, EquipoOrigen, EquipoDestino, fecha) "
					." VALUES (".$piJugador.",".$sOrigen.",".$sDestino.",Now())";
			$asResult=$this->tools_lib->ejecutar_query (array ('QUERY' => $sQuery));
			return ($asResult);
		}
		
		function bitacora_movimientos_lista($psFechaInicio, $psFechaFin, $piPagina) {
			$sQuery="SELECT * "
			." From contratos_jugadores "
			." WHERE id_club=".$piClub." AND id_jugador=".$piJugador."";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery, 'UNICA_FILA' => true));
			return ($asResult);
		}
		
		function getContrato($piClub, $piJugador) {
			$sQuery="SELECT * "
			." From contratos_jugadores "
			." WHERE id_club=".$piClub." AND id_jugador=".$piJugador."";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery, 'UNICA_FILA' => true));
			return ($asResult);
		}
		
		function mejores_ofertas_get () {
			$sQuery="SELECT jug.nombre as 'TX Jugador', cl.nombre_corto as 'TX Club', ofe.sueldo_base as 'CY Cantidad' "
			." FROM draft_ofertas ofe "
			." INNER JOIN clubes cl ON cl.id_unico=ofe.id_club "
			." INNER JOIN jugadores jug ON jug.id_unico=ofe.id_jugador"
			." WHERE ofe.estatus=1 "
			." ORDER BY ofe.sueldo_base DESC"
			." LIMIT 0,10";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery, 'CAMPOS_NUMERICOS' => false));
			return ($asResult);
		}
		function verificaJugadorContratado($piJugador) {
			$sQuery="SELECT con.*, cl.nombre_corto as 'club' "
			." From contratos_jugadores con "
			." INNER JOIN clubes cl ON con.id_club=cl.id_unico "
			." WHERE con.estatus=1 AND con.id_jugador=".$piJugador."";
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
				case 'V':
					$sTipo='VE';
					break;
				case 'T':
					$sTipo='TR';
					break;
			}
			$sQuery="INSERT INTO contratos_jugadores (id_unico, temporada_inicio, id_jugador, id_club, precio_base, duracion, tipo, estatus, posicion_temporal) "
					." VALUES (UUID(),".$piTemporada.",".$pasDatos[0].",".$piClub.",".$pasDatos[3].",".$pasDatos[2].",'".$sTipo."',1,99)";
			$asResult=$this->tools_lib->ejecutar_query (array ('QUERY' => $sQuery));
			return ($asResult);
		}
		

	

		function getJugadoresFiltro($psTodos, $pasCondiciones, $psScope, $piPagina) {
			$sCondiciones=" WHERE jug.jugador_supernova=1 ";
			for ($i=0;$i<count($pasCondiciones);$i++) {
				$sCampo=$pasCondiciones[$i][0];
				$sValor=str_replace("%20"," ", $pasCondiciones[$i][2]);
				switch ($pasCondiciones[$i][1]) {
					case "IGUAL":
						$sComplemento="='".$sValor."'";
						break;
					case "MAYOR":
						$sComplemento=">".$sValor;
						break;
					case "MENOR":
						$sComplemento="<".$sValor;
						break;
					case "MAYOR_IGUAL":
						$sComplemento=">=".$sValor;
						break;
					case "MENOR_IGUAL":
						$sComplemento="<=".$sValor;
						break;
					case "DIFERENTE":
						$sComplemento="<>".$sValor;
						break;
					case "PARECIDO_A":
						$sComplemento=" LIKE '%".$sValor."%'";
						break;
					case "ENTRE":
						$asValores=explode("-",$sValor);
						$sComplemento="BETWEEN (".$asValores[0]." AND ".$asValores[1].")";
						break;
				}
				$sCondiciones.=" AND jug.".$sCampo.$sComplemento;
			}
			if ($psScope=="L") {
				//Solo busca los libres de contrato
				$iTemporada=$this->config->item('temporada_actual');
				$sCondiciones.=" AND jug.id_unico NOT IN (Select con.id_jugador From contratos_jugadores con Where con.id_jugador=jug.id_unico AND con.estatus=1)";
			}
			$iOffset=($piPagina-1) * 10;
			$asConteo=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT count(*) as conteo FROM jugadores jug "
								.$sCondiciones,
						'UNICA_FILA' => true)
			);
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT jug.id_unico as 'IN clave', jug.nombre as 'TX Nombre', "
								." CONCAT('<a href=\"JavaScript: verHabilidades(', jug.id_unico, ',1);\">',jug.total_puntos,'</a>',"
								." '&nbsp;<a href=\"JavaScript: verHabilidades(', jug.id_unico, ',2);\">2</a>',"
								." '&nbsp;<a href=\"JavaScript: verHabilidades(', jug.id_unico, ',3);\">3</a>') as 'TX Habilidades', "
								." '' as 'TX Club Anterior', "
								." '' as 'TX Mayor oferta/Temporadas', "
								." '' as 'TX Minimo para ofertar', "
								." '' as 'TX Vencimiento',"
								." '' as 'TX Ofertar'"
								." FROM jugadores jug "
								.$sCondiciones
								." ORDER BY jug.total_puntos DESC "
								." LIMIT ".$iOffset.",10",
						'CAMPOS_NUMERICOS' => false)
			);
			if ($asResult['ESTATUS']==1)
				$asResult['TOTAL']=$asConteo['DATOS']['conteo'];
			return($asResult);
		}
		
		function getUltimoClub($piJugador) {
			$iTemporada=$this->config->item('temporada_actual');
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT cl.nombre_corto "
								." FROM clubes cl  "
								." INNER JOIN rostersportemporada rpt ON rpt.id_equipo=cl.id_unico "
								." WHERE rpt.id_jugador=".$piJugador
								."  AND rpt.id_temporada=".($iTemporada-1)
								." ORDER BY rpt.id_temporada DESC"
								." LIMIT 0,1",
						'UNICA_FILA' => true)
			);
			switch ($asResult['ESTATUS']) {
					case -1:
						$sSalida=$asResult['MENSAJE'];
						break;
					case 0:
						$sSalida='Agente Libre';
						break;
					case 1:
						$sSalida=$asResult['DATOS']['nombre_corto'];
						break;
			}
			return($sSalida);
		}
		
		function getOfertas($piJugador, $piModo) {
			$iTemporada=$this->config->item('temporada_actual');
			$sCondicionEstatus=($piModo==1) ? " AND estatus=1" : "";
			$sQuery="SELECT ofe.*, cl.nombre_corto as club "                      
			." FROM draft_ofertas ofe "
			." INNER JOIN clubes cl ON ofe.id_club=cl.id_unico "
			." WHERE ofe.id_temporada=".$iTemporada." AND ofe.id_jugador=".$piJugador
			.$sCondicionEstatus
			." ORDER BY ofe.fecha_oferta";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}

		function getHistorialOfertas($piJugador) {
			$iTemporada=$this->config->item('temporada_actual');
			$sQuery="SELECT ofe.*, cl.nombre_corto as club, DATE_ADD(ofe.fecha_oferta, INTERVAL ofe.duracion_oferta MINUTE) as 'tiempo_limite' " 
			." FROM draft_ofertas ofe "
			." INNER JOIN clubes cl ON ofe.id_club=cl.id_unico "
			." WHERE ofe.id_temporada=".$iTemporada." AND ofe.id_jugador=".$piJugador
			." ORDER BY ofe.fecha_oferta";
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery));
			return ($asResult);
		}

		
		
		function getUltimaOferta($piJugador) {
			$iTemporada=$this->config->item('temporada_actual');
			$sQuery="SELECT ofe.* "                      
			." From draft_ofertas ofe "
			." WHERE ofe.id_temporada=".$iTemporada." AND ofe.id_jugador=".$piJugador
			." ORDER BY ofe.fecha_oferta DESC LIMIT 0,1" ;
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery, 'UNICA_FILA' => true));
			return ($asResult);
		}

		function insertaOferta ($pasDatos) {
			//Desactiva las ofertas anteriores
			$sQueryDescarta="UPDATE draft_ofertas SET estatus=-1 WHERE id_temporada=".$pasDatos['temporada']." AND id_jugador=".$pasDatos['jugador'];
			$asResultadoDescarte=$this->tools_lib->ejecutar_query (array ('QUERY' => $sQueryDescarta));
			if ($asResultadoDescarte['ESTATUS']==1) {
				/*$asUltimaOferta=$this->getUltimaOferta($pasDatos['jugador']);
				if ($asUltimaOferta['ESTATUS']==1)
					$sMinutosDuracion=$asUltimaOferta['DATOS']['duracion_oferta'];
				else
					$sMinutosDuracion=$this->config->item('minutos_duracion_1ra_puja');
				if ($this->config->item('puja_descendiente')==true)
					$sMinutosDuracion=round($sMinutosDuracion/2);
				*/
				//Checa dependiendo del dia que se hace la oferta, cuantas horas se van a agregar
				switch (date("w")) {
						case 1:
						case 2:
						case 3:
						case 4:
							$sMinutosDuracion=$this->config->item('horas_puja_workdays')*60;	
								break;
						case 5:
						case 6:
						case 0:		
							$sMinutosDuracion=$this->config->item('horas_puja_weekends')*60;	
								break;
				}
				$sQuery="INSERT INTO draft_ofertas (id_unico, id_jugador, id_club, id_temporada, sueldo_base, duracion, fecha_oferta, duracion_oferta, estatus) "
						." VALUES (UUID(),".$pasDatos['jugador'].",".$pasDatos['club'].",".$pasDatos['temporada'].",".$pasDatos['oferta'].","
						.$pasDatos['duracion'].", CURRENT_TIMESTAMP(), ".$sMinutosDuracion.",1)";
				$asResult=$this->tools_lib->ejecutar_query (array ('QUERY' => $sQuery));
				return ($asResult);
			}
			else
				return ($asResultadoDescarte);
		}
		
		function setOfertaGanadora ($psUuid) {
			//Desactiva las ofertas anteriores
			$sQuery="UPDATE draft_ofertas SET estatus=10, fecha_contratacion=Now() WHERE id_unico='".$psUuid."'";
			$asResultado=$this->tools_lib->ejecutar_query (array ('QUERY' => $sQuery));
			return ($asResultado);
		}

		function setEstatusOferta ($psUuid, $piEstatus, $psCampoFecha="") {
			$sCambioFecha=($psCampoFecha!="") ? ", ".$psCampoFecha."=Now() " : "" ;
			//Desactiva las ofertas anteriores
			$sQuery="UPDATE draft_ofertas SET estatus=".$piEstatus.$sCambioFecha." WHERE id_unico='".$psUuid."'";
			$asResultado=$this->tools_lib->ejecutar_query (array ('QUERY' => $sQuery));
			return ($asResultado);
		}

		function getOfertasGanadoras () {
			$iTemporada=$this->config->item('temporada_actual');
			$asResult=$this->tools_lib->consulta (array	('QUERY' => "SELECT ofe.*, jug.nombre as 'nombre_jugador', cl.nombre_corto as 'nombre_club' "
			." FROM draft_ofertas ofe "
			." INNER JOIN jugadores jug ON ofe.id_jugador=jug.id_unico "
			." INNER JOIN clubes cl ON ofe.id_club=cl.id_unico "
			." WHERE id_temporada =".$iTemporada
			."	AND DATE_ADD(ofe.fecha_oferta, INTERVAL ofe.duracion_oferta MINUTE) < CURRENT_TIMESTAMP() "
			."  AND ofe.estatus=1"
			." ORDER BY ofe.fecha_oferta "));
			return ($asResult);
		}

		function getInfoContratoJugador($piJugador) {
			$iTemporada=$this->config->item('temporada_actual');
			$sQuery="SELECT ofe.* "                      
			." From draft_ofertas ofe "
			." WHERE ofe.id_temporada=".$iTemporada." AND ofe.id_jugador=".$piJugador
			." AND ofe.estatus=10 LIMIT 0,1" ;
			$asResult=$this->tools_lib->consulta (array	('QUERY' => $sQuery, 'UNICA_FILA' => true));
			return ($asResult);
		}

		function modulos_get($psModo="todos") {
			switch ($psModo) {
				case "todos": $sCondicion=""; break;
				case "activos" : $sCondicion=" WHERE activo=1"; break;
			}
			$asResult=$this->tools_lib->consulta (array	(
				'QUERY' => "SELECT * "                      
						." From modulos_draft "
						.$sCondicion
						." ORDER BY orden "));
			return ($asResult);	
		}
	}
?>