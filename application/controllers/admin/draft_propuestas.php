<?php
	
	class draft_propuestas extends CI_Controller {
	
		function __construct() {
			parent::__construct();
			$this->load->model('draft_mod');
		}
	
		function autoriza() {
			$this->load->model('bitacora_draft_mod');
			$sError="";
			$asJugadores=$this->draft_mod->getJugadoresPropuesta($this->input->post('rbPropuesta'));
			//var_dump($asJugadores);
			if ($asJugadores['ESTATUS']==1) {
				for ($i=0;$i<count($asJugadores['DATOS']);$i++) {
					if ($asJugadores['DATOS'][$i]['id_jugador']!=0) {
						//Verifica si el club tiene todavia en su poder la carta del jugador
						$sCampoClub=($asJugadores['DATOS'][$i]['tipo_propuesto']==1) ? 'club_origen' : 'club_destino';
						$asVerificacion=$this->draft_mod->verificaContrato($asJugadores['DATOS'][$i][$sCampoClub],$asJugadores['DATOS'][$i]['id_jugador'], $this->config->item('temporada_actual'));
					}
					else {
						//Si ofrece dinero, verifica que sea solvente
						$cyPresupuesto=$this->draft_mod->getPresupuestoClub($this->config->item('temporada_actual'),$asJugadores['DATOS'][$i][$sCampoClub]);
						if ($cyPresupuesto<$asJugadores['DATOS'][$i]['cantidad']) {
							$sError="Se suspende la operacion por falta de presupuesto";
							$this->bitacora_draft_mod->inserta(array (
										'operador' => 	'root',
										'tipo' => 'transferencia',
										'id_club' => $asJugadores['DATOS'][$i][$sCampoClub],
										'id_jugador' => $asJugadores['DATOS'][$i]['id_jugador'],
										'temporada' => $this->config->item('temporada_actual'),
										'observaciones' => "Operacion suspendida por falta de presupuesto"
										));
						}
					}
					if ($asVerificacion['ESTATUS']!=1) {
						$sError="Se suspende la operacion por falta de derechos";
						$this->bitacora_draft_mod->inserta(array (
							'operador' => 	'root',
							'tipo' => 'transferencia',
							'id_club' => $asJugadores['DATOS'][$i][$sCampoClub],
							'id_jugador' => $asJugadores['DATOS'][$i]['id_jugador'],
							'temporada' => $this->config->item('temporada_actual'),
							'observaciones' => "Operacion suspendida por falta de derechos"
						));
					}
				}
			}
			if ($sError=="") {
				$asResult=$this->draft_mod->AutorizaPropuesta($this->input->post('rbPropuesta'));
				$this->bitacora_draft_mod->inserta(array (
					'operador' => 	'root',
					'tipo' => 'transferencia',
					'temporada' => $this->config->item('temporada_actual'),
					'observaciones' => "Operacion de transferencia realizada ".$this->input->post('rbPropuesta')
					));
				$iTemporada=$this->config->item('temporada_actual');
				for ($i=0;$i<count($asJugadores['DATOS']);$i++) {
					if ($asJugadores['DATOS'][$i]['id_jugador']!=0) {
						//Si implica jugador, hace el cambio de propietario del contrato
						$sCampoClubRecibe=($asJugadores['DATOS'][$i]['tipo_propuesto']==1) ? 'club_destino' : 'club_origen';
						$asContratoCancelar=$this->draft_mod->getContratoJugador($iTemporada,$asJugadores['DATOS'][$i]['id_jugador']);
						//si fue por subasta, cancela la oferta ganadora
						if ($asContratoCancelar['DATOS']['tipo']=="SU"){
							$asMejorOferta=$this->draft_mod->getMejorOferta($iTemporada,$asJugadores['DATOS'][$i]['id_jugador']);
							$this->draft_mod->setEstatusOferta($asMejorOferta['DATOS']['id_unico'],-3);
						}
						$this->draft_mod->Contrato_cancela($asContratoCancelar['DATOS']['id_unico'], 2);
						
						$iTemporadasTranscurridas=$iTemporada-$asContratoCancelar['DATOS']['temporada_inicio'];
						$fInflacion=($iTemporadasTranscurridas*$this->config->item('inflacion_temporada')) + 1; 
						$this->draft_mod->insertaContrato($iTemporada,$asJugadores['DATOS'][$i][$sCampoClubRecibe],
										array (
											0 => $asJugadores['DATOS'][$i]['id_jugador'] ,   
											1 => 'V', 
											2 => $asContratoCancelar['DATOS']['duracion']-$iTemporadasTranscurridas,
											3 => $asContratoCancelar['DATOS']['precio_base'] * $fInflacion
										));
						$asResultado=$this->draft_mod->CancelaPropuestasInvolucradas(
										$asJugadores['DATOS'][$i]['id_jugador'],
										$asJugadores['DATOS'][$i]['consejo_origen']);						
						echo ("|TRANSFERENCIAS: ".$asResultado['QUERY']."\n");
					}
					else {
						//Si no, hace la transferencia de fondos entre clubes
						
						$sCampoClubPaga=($asJugadores['DATOS'][$i]['tipo_propuesto']==1) ? 'club_origen' : 'club_destino';
						$sCampoClubRecibe=($asJugadores['DATOS'][$i]['tipo_propuesto']==1) ? 'club_destino' : 'club_origen';
						$asResult=$this->draft_mod->OperacionesFinancieras_insert(
								$iTemporada,$asJugadores['DATOS'][$i]['cantidad'],
								3, $asJugadores['DATOS'][$i][$sCampoClubPaga], 
								3, $asJugadores['DATOS'][$i][$sCampoClubRecibe]
						);
					}
				}
				$asPrincipal=array(
					'RUTA_RAIZ' => base_url(), 'INDEX_URI' => $this->config->item('index_uri'),
					'MENSAJE' => "Se ha realizado con exito la operacion ".$this->input->post('rbPropuesta')
				);	
				$asContenido=array (
					'PRINCIPAL' => $this->parser->parse('draft/autoriza_propuestas_vw', $asPrincipal, true)
				);
				$asControlador= array (
					'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
					'TIPO_ACCESO' => 'ADMIN' );
				$this->main_lib->display($asControlador);
			}
			else 
				$this->main_lib->mensaje($sError);
		}
		
		function cancela($psClavePropuesta) {
			$asResult=$this->draft_mod->CancelaPropuesta($psClavePropuesta);
			if ($asResult['ESTATUS']!=1)
				$sMensaje="Error: ".$asResult['MENSAJE']."\n";
			else
				$sMensaje="<div id=\"sucess\">Se ha cancelado la propuesta </div>";
			$asPrincipal=array ('RUTA_RAIZ' => base_url(), 'MENSAJE' => $sMensaje);
			$this->main_lib->simple_display($asPrincipal,'draft/cancela_propuestas_vw', 'una-columna_vw', 'ADMIN');
		}
	
		function disponibles() {
			$asClubes=$this->draft_mod->getClubesAdministrados();
			$sComboClubes=$this->tools_lib->GeneraCombo(
				array(
					'NOMBRE' => 'slcListaEquipos',
					'DATASET' => $asClubes,
					'EVENTOS' => "onclick=\"Listado(this.value)\"",
					'OPCION_EXTRA' => "Sin Equipo"
				)
			);
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'COMBO_CLUBES' => $sComboClubes
			);
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/selecciona_disponibles_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}
		
		function revisa() {
			$sTemporada=$this->config->item('temporada_actual');
			$asPrincipal=array (
				'RUTA_RAIZ' =>base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'PROPUESTAS_RECIBIDAS' => $this->listapropuestas('otros'),
				'PROPUESTAS_PROPIAS' => $this->listapropuestas('propias')
			);
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/muestra_propuesta_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}
		
		function listapropuestas($piOrigen) {
			$sColor="";
			$asListaPropuestas=array();
			$asPropuestas=$this->draft_mod->getPropuestas($piOrigen);
			$iContFilas=0;
			if ($asPropuestas['ESTATUS']==1) {
				$sClavePropuestaActual="";
				for ($i=0;$i<count($asPropuestas['DATOS']);$i++) {
					if ($asPropuestas['DATOS'][$i]['nombre']!="")
						$sJugador="\t\t\t".$asPropuestas['DATOS'][$i]['nombre']." <br/>\n";
					else
						$sJugador="\t\t\t".$asPropuestas['DATOS'][$i]['cantidad']." KCacaos \n";
					if ($sClavePropuestaActual===$asPropuestas['DATOS'][$i]['id_unico']) {
						if ($asPropuestas['DATOS'][$i]['tipo_propuesto']==1)
							$asListaPropuestas[$iContFilas-1]['OFRECE'].=$sJugador;
						else
							$asListaPropuestas[$iContFilas-1]['SOLICITA'].=$sJugador;
					}
					else {
						$sColor=($sColor=="non") ? "par" : "non";
						switch ($asPropuestas['DATOS'][$i]['estatus']) {
							case 0:
								if ($piOrigen=="otros")
									$sRadioButton="<input type=\"radio\" name=\"rbPropuesta\" value=\"".$asPropuestas['DATOS'][$i]['id_unico']."\" />";
								else
									$sRadioButton="En espera ";
								$sLinkCancelar="<a href=\"".base_url()."admin/draft_propuestas/cancela/".$asPropuestas['DATOS'][$i]['id_unico']."\">cancelar</a>";
								break;
							case 1:
								$sRadioButton="Operacion realizada";
								$sLinkCancelar="";
								break;
							case 3:
								$sRadioButton="Rechazada";
								$sLinkCancelar="";
								break;
							case 4:
								$sRadioButton="Cancelada por falta de derechos";
								$sLinkCancelar="";
								break;
						}
						$sCampoClub=($piOrigen=="propias") ? "club_destino" : "club_origen";
						$asListaPropuestas[]=array (
							'CLAVE' => $asPropuestas['DATOS'][$i]['id_unico'],
							'CLIENTE' => $asPropuestas['DATOS'][$i]['cliente']." (".$asPropuestas['DATOS'][$i][$sCampoClub].")",
							'OFRECE' => ($asPropuestas['DATOS'][$i]['tipo_propuesto']==1) ? $sJugador : "",
							'SOLICITA' =>($asPropuestas['DATOS'][$i]['tipo_propuesto']==2) ? $sJugador : "",
							'MENSAJE' => $asPropuestas['DATOS'][$i]['mensaje'],
							'FECHA_SOLICITUD' => $asPropuestas['DATOS'][$i]['fecha_creacion'],
							'RADIO' => $sRadioButton,
							'LINK_CANCELAR' => $sLinkCancelar,
							'CLASE_FILA' => $sColor
						);
						$iContFilas++;
						$sClavePropuestaActual=$asPropuestas['DATOS'][$i]['id_unico'];
					}
				}
			}
			else
				$asListaPropuestas[]=array (
					'CLAVE' => "",'CLIENTE' => "",'OFRECE' => "", 'SOLICITA' =>"",	'MENSAJE' => "No hay propuestas",
					'FECHA_SOLICITUD' => "", 'RADIO' => "",	'CLASE_FILA' => "notice"
				);
			return ($asListaPropuestas);
		}
		
		function nueva() {
			$this->load->model('consejos_mod');
			$asClubesAdministrados=$this->draft_mod->getClubesAdministrados();
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			$sComboClubes=$this->tools_lib->GeneraCombo(
				array (
					'NOMBRE' => "slcListaEquiposOrigen",
					'DATASET' => $asClubesAdministrados,
					'EVENTOS' => "onChange=\"ActualizaLista(this.value, 'origen');\" size=\"15\"",
					'ID' => "lista-clubes-origen"
				)
			);
			$sListaEquiposOrigen="";
			$sConsejos="";
			$sTemporada=$this->config->item('temporada_actual');
			$asConsejosDemas=$this->draft_mod->getConsejosDemas($asConsejo['DATOS']['id_unico']);
			$asListaConsejos=array();
			if ($asConsejosDemas['ESTATUS']==1) {
				for ($i=0;$i<count($asConsejosDemas['DATOS']);$i++) {
					$asListaConsejos[]=array (
						'CLAVE_CONSEJO' => $asConsejosDemas['DATOS'][$i]['id_unico'],
						'NOMBRE_CORTO' => $asConsejosDemas['DATOS'][$i]['iniciales']
					);
				}
			}
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'COMBO_CLUBES_ORIGEN' => $sComboClubes,
				'CONSEJOS' => $asListaConsejos,
				'CONSEJO_ORIGEN' => $asConsejo['DATOS']['id_unico']
			);
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/captura_propuesta_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}

		function genera() {
			$asResult=$this->draft_mod->GeneraTituloPropuesta($this->input->post('hdnClubOrigen'),$this->input->post('hdnClubDestino'), $this->input->post('hdnConsejoOrigen'),$this->input->post('hdnConsejoDestino'));
			if ($asResult['ESTATUS']==1) {
				if ($this->input->post("hdnMensaje")!="") 
					$this->draft_mod->InsertaMensajePropuesta($asResult['NUEVO_ID'], $this->input->post("hdnMensaje"), $this->input->post('hdnClubOrigen'));
				foreach ($_POST as $key => $value) {
					if (substr($key,0,13)=="hdnpropuestas") {
						if (substr($value,0,4)=="EFE_") {
							$this->draft_mod->InsertaJugadorPropuesta($asResult['NUEVO_ID'], 0, 1, substr($value,4));
						}
						else {
							$sClaveJugador=$value;
							$this->draft_mod->InsertaJugadorPropuesta($asResult['NUEVO_ID'], $sClaveJugador, 1);
						}
					}
					if (substr($key,0,14)=="hdnsolicitudes") {
						if (substr($value,0,4)=="EFE_") {
							$this->draft_mod->InsertaJugadorPropuesta($asResult['NUEVO_ID'], 0, 2, substr($value,4));
						}
						else {
							$sClaveJugador=$value;
							$this->draft_mod->InsertaJugadorPropuesta($asResult['NUEVO_ID'], $sClaveJugador, 2);
						}
					}
				}
				$sSalida="La operacion se ha logrado con exito. Clave: ".$asResult['NUEVO_ID'];
			}
			else
				$sSalida=$asResult['ERROR'];
			$asPrincipal=array(
				'RUTA_RAIZ' => base_url(), 'INDEX_URI' => $this->config->item('index_uri'),
				'MENSAJE' => $sSalida
			);	
				
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/genera_propuesta_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}
		
		function xi_roster($piClub) {
			$asDisponibles=$this->draft_mod->getDisponiblesClub($piClub);
			$asJugadores=array();
			if ($asDisponibles['ESTATUS']==1) {
				for ($i=0;$i<count($asDisponibles['DATOS']);$i++) {
					$sColor= ($asDisponibles['DATOS'][$i]['disponible']==0) ? "celda_normal" : "celda_seleccionada";
					$sValor= ($asDisponibles['DATOS'][$i]['disponible']==0) ? "" : " checked=\"true\"";
					$asJugadores[]= array (
						'CLAVE' => $asDisponibles['DATOS'][$i]['id_unico'],
						'NOMBRE' => $asDisponibles['DATOS'][$i]['nombre'],
						'COLOR' => $sColor,
						'CHECADO' => $sValor
					);
				}
			}
			else
				$asJugadores[]= array ('CLAVE' => "",	'NOMBRE' => $asDisponibles['MENSAJE'], 'COLOR' => "",	'CHECADO' => ""	);
			$asPrincipal=array (
				'LISTA_JUGADORES' => $asJugadores,
				'CLAVE_CLUB' => $piClub,
				'TEMPORADA' => $this->config->item('temporada_actual')
			);
			echo ($this->parser->parse("draft/xi_roster_vw", $asPrincipal, true));
		}

		function xi_actualiza_disponibles($piTemporada, $piClub, $psJugadores) {
			$asResult=$this->draft_mod->ReseteaDisponibles($piTemporada, $piClub);
			$asJugadores=explode("_",urldecode($psJugadores));
			//var_dump ($asJugadores);
			for ($i=0;$i<count($asJugadores)-1;$i++) {
				$sClaveJugador=substr($asJugadores[$i],2);
				$asResult=$this->draft_mod->setDisponible($piTemporada, $sClaveJugador);
			}
			echo ("<div class=\"success\">Actualizacion Completa</div>");
		}

		function xi_roster_propuesta($piModo, $piClub) {
			if ($piModo=="origen") {
				$sControlCheckBox="chkOF";
				$sIDClave="orc";
				$sIDNombre="orn";
				$sParametro="1";
			}
			else {
				$sControlCheckBox="chkSL";
				$sIDClave="dec";
				$sIDNombre="den";
				$sParametro="2";
			}
			$asListaJugadores=array();
			$iTemporada=$this->config->item('temporada_actual');
			$sOperador=$this->session->userdata('sUsuario');
			$iPresupuesto=$this->draft_mod->getPresupuestoClub($iTemporada, $piClub);
			$sClaveEquipo=$piClub;
			$asHabilidades=$this->draft_mod->getHabilidadesJugadores($iTemporada, $sClaveEquipo);
			if ($asHabilidades['ESTATUS']==1) {
				for ($i=0;$i<count($asHabilidades['DATOS']);$i++) {
					switch($asHabilidades['DATOS'][$i]['iniciales_esp']) {
						case "P":
							$sColor="pos_Portero";
							break;
						case "D":
							$sColor="pos_Defensa";
							break;
						case "M":
							$sColor="pos_Medio";
							break;
						case "A":
							$sColor="pos_Atacante";
							break;
					}
					if ($asHabilidades['DATOS'][$i]['tipo']!='FR') {
						$sCheckBoxSalida="<input type=\"checkbox\" name=\"".$sControlCheckBox."_".$asHabilidades['DATOS'][$i]['id_unico']."\" />";
					}
					else {
						$sCheckBoxSalida="FR";
						$sColor="notice";
					}
					$asListaJugadores[]=array (
						'CLAVE_JUGADOR' => $asHabilidades['DATOS'][$i]['id_unico'],
						'COLOR' => $sColor,
						'NOMBRE' => $asHabilidades['DATOS'][$i]['nombre'],
						'SALARIO' => $asHabilidades['DATOS'][$i]['precio_base'],
						'TEMPORADAS_RESTANTES' => ($asHabilidades['DATOS'][$i]['temporada_inicio'] + $asHabilidades['DATOS'][$i]['duracion']) - $iTemporada,
						'TOTAL_PUNTOS' => $asHabilidades['DATOS'][$i]['total_puntos'],
						'CONTROL_CHECKBOX' => $sCheckBoxSalida,
						'ID_CLAVE' => $sIDClave,
						'ID_NOMBRE' => $sIDNombre
					);
				}
			}
			else
				$asListaJugadores[]=array (
					'CLAVE_JUGADOR' => "0",
					'COLOR' => "notice",
					'NOMBRE' => $asHabilidades['MENSAJE'],
					'TOTAL_PUNTOS' => "",
					'TEMPORADAS_RESTANTES' => "",
					'CONTROL_CHECKBOX' => "",
					'SALARIO' => "",
					'ID_CLAVE' => $sIDClave,
					'ID_NOMBRE' => $sIDNombre
				);
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'LISTA_JUGADORES' => $asListaJugadores,
				'MODO' => $piModo,
				'CLAVE_EQUIPO' => $piClub,
				'PRESUPUESTO' => $iPresupuesto,
				'TEMPORADA' => $iTemporada,
				'PARAMETRO' => $sParametro
			);
			echo $this->parser->parse('draft/xi_roster_propuesta_vw', $asPrincipal, true);
		}

		function xi_clubes_destino($piConsejo) {
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'COMBO_EQUIPOS_DESTINO' => $this->tools_lib->GeneraCombo( array (
					'NOMBRE' => "slcEquiposDestino", 'TABLA' => "clubes", 'CAMPO_CLAVE' => "id_unico",
					'LEYENDA' => 'nombre_corto', 'CONDICIONES' => "id_consejo=".$piConsejo." OR administrado_por=".$piConsejo,
					'EVENTOS' => "size=\"20\" onclick=\"ActualizaLista(this.value, 'destino');\"",
					'ID' => 'lista-clubes-destino'
				)),
			);
			echo ($this->parser->parse('draft/xi_equipos_destino_vw', $asPrincipal, true));
		}

		function CaracteresValidos($sParametro) {
			$sSalida=$sParametro;
			$sSalida=str_replace(chr(160),"&aacute;", $sSalida);
			$sSalida=str_replace(chr(130),"&eacute;", $sSalida);
			$sSalida=str_replace(chr(161),"&iacute;", $sSalida);
			$sSalida=str_replace(chr(162),"&oacute;", $sSalida);
			$sSalida=str_replace(chr(163),"&uacute;", $sSalida);
			return($sSalida);
		}
		
		function listageneral() {
			$asPropuestas=$this->draft_mod->getPropuestasGeneral($this->config->item('temporada_actual'));
			if ($asPropuestas['ESTATUS']==1) {
				$asPrincipal=array (
					'RUTA_RAIZ' => base_url(),
					'INDEX_URI' => $this->config->item('index_uri'),
				);
				$sClaveActual="";
				$iContFilas=0;
				for ($i=0;$i<count($asPropuestas['DATOS']);$i++) {
					$sRefJugador=$asPropuestas['DATOS'][$i]['nombre']."(".$asPropuestas['DATOS'][$i]['iniciales'].")<br>";
					if ($asPropuestas['DATOS'][$i]['clave_propuesta']===$sClaveActual) {
						if ($asPropuestas['DATOS'][$i]['tipo_propuesto']==1)
							$asPrincipal['BLOQUE_PROPUESTAS'][$iContFilas-1]['OFRECE'].=$sRefJugador;
						else
							$asPrincipal['BLOQUE_PROPUESTAS'][$iContFilas-1]['SOLICITA'].=$sRefJugador;
					}
					else {
						switch ($asPropuestas['DATOS'][$i]['estatus']) {
							case 0:
								$sEstatus="En espera";
								break;
							case 1:
								$sEstatus="Operacion realizada";
								break;
							case 3:
								$sEstatus="Rechazada";
								break;
							case 4:
								$sEstatus="Cancelada por falta de derechos";
								break;
						}
						$asPrincipal['BLOQUE_PROPUESTAS'][]	=array (
							'CLAVE' => $asPropuestas['DATOS'][$i]['clave_propuesta'],
							'OFERTANTE' => $asPropuestas['DATOS'][$i]['ofertante'],
							'INVOLUCRADO' => $asPropuestas['DATOS'][$i]['involucrado'],
							'OFRECE' => ($asPropuestas['DATOS'][$i]['tipo_propuesto']==1) ? $sRefJugador : "",
							'SOLICITA' => ($asPropuestas['DATOS'][$i]['tipo_propuesto']==2) ? $sRefJugador : "",
							'MENSAJE' => $asPropuestas['DATOS'][$i]['mensaje'],
							'FECHA_SOLICITUD' => $asPropuestas['DATOS'][$i]['fecha_creacion'],
							'ESTATUS' => $sEstatus,
							'CLASE' => ($iContFilas%2==0) ? "non" : "par"
						);
						$iContFilas++;
						$sClaveActual=$asPropuestas['DATOS'][$i]['clave_propuesta'];
					}
				}
				$asContenido=array (
					'PRINCIPAL' => $this->parser->parse('draft/lista_general_propuestas_vw', $asPrincipal, true)
				);
				$asControlador= array (
					'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
					'TIPO_ACCESO' => 'ADMIN' );
				$this->main_lib->display($asControlador);
			}
			else
				$this->main_lib->mensaje("No hay movimientos");
		}
		/*Fin de la clase*/
	}
?>