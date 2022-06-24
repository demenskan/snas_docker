<?php
	
	class draft_movimientos extends CI_Controller {
	
		function __construct() {
			parent::__construct();
			$this->load->model('draft_mod');
		}
	
		function internos() {
			$this->load->model('consejos_mod');
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			$asClubesConsejo=$this->draft_mod->getClubesConsejo($asConsejo['DATOS']['id_unico']);
			$asPrincipal=array(
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'ORIGEN' => $this->tools_lib->GeneraCombo(
							array (
								'NOMBRE' => "slcListaEquiposOrigen",
								'DATASET' => $asClubesConsejo,
								'OPCION_EXTRA' => "Sin Club",
								'EVENTOS' => " size=\"15\" onclick=\"MuestraOrigen(this.value);\""
								)
							),
				'DESTINO' => $this->tools_lib->GeneraCombo(
							array (
								'NOMBRE' => "slcListaEquiposDestino",
								'DATASET' => $asClubesConsejo,
								'OPCION_EXTRA' => "Sin Club",
								'EVENTOS' => " size=\"15\" onclick=\"MuestraDestino(this.value);\""
								)
							)
			);
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/movimientos_internos_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}
		
		function xi_lista ($psTipo, $piClub) {
			if ($piClub!="__EXTRA__")
				$asJugadores=$this->draft_mod->getListaPreliminar($piClub);
			else {
				$this->load->model('consejos_mod');
				$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
				$asJugadores=$this->draft_mod->getJugadoresSinDefinir($asConsejo['DATOS']['id_unico']);
			}
			if ($asJugadores['ESTATUS']==1) {
				$asPrincipal=array(
					'RUTA_RAIZ' => base_url(),
					'INDEX_URI' => $this->config->item('index_uri'),
					'PRESUPUESTO' => $this->draft_mod->getPresupuestoClub($this->config->item('temporada_actual'), $piClub)
				);
				for ($i=0;$i<count($asJugadores['DATOS']);$i++) {
					$iTemporadasTranscurridas=$asJugadores['DATOS'][$i]['temporada_inicio']-$this->config->item('temporada_actual');
					$fPorcentajeInflacion=($this->config->item('inflacion_temporada')*$iTemporadasTranscurridas);
					$fInflacion= round($asJugadores['DATOS'][$i]['precio_base']*$fPorcentajeInflacion,3);
					$fPenalizacion=round(($asJugadores['DATOS'][$i]['precio_base']+$fInflacion) * $this->config->item('penalizacion_traspaso'),3);
					$asPrincipal['BLOQUE_JUGADORES'][]=array (
						'ID_JUGADOR' => $asJugadores['DATOS'][$i]['id_unico'],
						'NOMBRE' => $asJugadores['DATOS'][$i]['nombre'],
						'CLASE' => "pos_".$asJugadores['DATOS'][$i]['iniciales_esp'],
						'PREFIJO' => ($psTipo=="origen") ? "or" : "de",
						'CHECKBOX' => ($psTipo=="origen") ? "<input type=\"checkbox\" name=\"chkOF_".$asJugadores['DATOS'][$i]['id_unico']."\" />" : "",
						'PRECIO_BASE' => $asJugadores['DATOS'][$i]['precio_base'],
						'INFLACION' => $fInflacion,
						'PENALIZACION' => $fPenalizacion,
						'TOTAL_PAGAR_TEMPORADA' => $asJugadores['DATOS'][$i]['precio_base'] + $fInflacion + $fPenalizacion
					);
				}
				$this->parser->parse('draft/xi_lista_mov_int_vw', $asPrincipal);
			}
			else
				echo ("nanay!");
		}
		
		function xi_realiza_movimientos($piClubOrigen, $piClubDestino, $psClavesJugadores) {
			$this->load->model('consejos_mod');
			$iTemporada=$this->config->item('temporada_actual');
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			$asJugadores=explode("-", $psClavesJugadores);
			//var_dump($asJugadores);
			$sMensaje="";
			for ($i=0;$i<(count($asJugadores)-1);$i++) {
				//Verifica que el club destino tenga solvencia
				$cyPresupuesto=$this->draft_mod->getPresupuestoClub($iTemporada, $piClubDestino);
				$asContratoJugador=$this->draft_mod->getContratoJugador($iTemporada, $asJugadores[$i]);
				$cyPrecioBase=$asContratoJugador['DATOS']['precio_base'];
				$iTemporadasTranscurridas=$iTemporada-$asContratoJugador['DATOS']['temporada_inicio'];
				$fFactorInflacion=$this->config->item('inflacion_temporada')*$iTemporadasTranscurridas;
				$cyInflacion=$cyPrecioBase*$fFactorInflacion;
				$cyPenalizacion=($cyPrecioBase+$cyInflacion) * $this->config->item('penalizacion_traspaso');
				$cyTotalOperacion=$cyPrecioBase + $cyInflacion + $cyPenalizacion;
				if ($cyPresupuesto>=$cyTotalOperacion) {
					$asResult=$this->draft_mod->Contrato_update ($asContratoJugador['DATOS']['id_unico'], $piClubDestino);
					if ($asResult['ESTATUS']!=1) echo ($asResult['MENSAJE']);
					$asResult=$this->draft_mod->insertaBitacoraMovimiento($asJugadores[$i], $piClubOrigen, $piClubDestino);
					//El club destino le paga al club origen su parte proporcional del contrato
					$this->draft_mod->OperacionesFinancieras_insert($iTemporada, $cyPrecioBase+$cyInflacion, 3, $piClubDestino, 3, $piClubOrigen);
					//Se paga la penalizacion a la federacion
					$this->draft_mod->OperacionesFinancieras_insert($iTemporada, $cyPenalizacion, 3, $piClubDestino, 1, 0);
					$sMensaje.="Operacion exitosa de ".$asJugadores[$i]."<br/>\n";
				}
				else {
					$sMensaje.="falta de fondos para ".$asJugadores[$i]."<br/>\n";
				}
				if ($asResult['ESTATUS']!=1) echo ($asResult['MENSAJE']);
			}
			echo ("<div class=\"success\">".$sMensaje."</div");
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


		function alineaciones() {
			$this->load->model('consejos_mod');
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			$asClubesConsejo=$this->draft_mod->getClubesConsejo($asConsejo['DATOS']['id_unico']);
			$asPrincipal=array(
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri')
			);
			if ($asClubesConsejo['ESTATUS']==1) {
				for ($i=0;$i<count($asClubesConsejo['DATOS']);$i++) {
					$asPrincipal['BLOQUE_EQUIPOS'][]=array(
						'RUTA_RAIZ' => base_url(),
						'INDEX_URI' => $this->config->item('index_uri'),
						'ID_CLUB' => $asClubesConsejo['DATOS'][$i]['id_unico'],
						'NOMBRE_CLUB' => $asClubesConsejo['DATOS'][$i]['nombre_corto'],
						'RUTA_LOGO' => $asClubesConsejo['DATOS'][$i]['ruta_logo']
					);
				}
			}
			$this->main_lib->simple_display($asPrincipal, 'draft/alineaciones_vw', 'una-columna_vw', 'ADMIN');
		}
		
		function alineacionesjugadores($piClub="") {
			$this->load->model('clubes_mod');
			$iClaveClub=($piClub=="") ? $this->input->post('hdnClaveClub') : $piClub;
			$sModo=($this->input->post('hdnModo')=="") ? "Primera_vista" : $this->input->post('hdnModo');
			if ($sModo=="Actualizacion") {
				foreach ($_POST as $key => $value) {
					if (substr($key,0,3)=="slc") {
						$sClaveJugador=substr($key,3);
						$this->draft_mod->ActualizaPosicion($value, $sClaveJugador);
					}
				}	
			}
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'LOGO_NOMBRE_CLUB' => ""
			);
			$aiContadorPosiciones=array(0,0,0,0);
			$iContadorTitulares=0;
			$asJugadores=$this->draft_mod->getPosicionesTemporales($iClaveClub);
			//var_dump($asJugadores);
			if ($asJugadores['ESTATUS']==1) {
				$asPrincipal['BLOQUE_BANCA']=array();
				$asPrincipal['BLOQUE_PORTERO']=array();
				$asPrincipal['BLOQUE_DEFENSAS']=array();
				$asPrincipal['BLOQUE_MEDIOS']=array();
				$asPrincipal['BLOQUE_DELANTEROS']=array();
				$asPosiciones=$this->draft_mod->getPosiciones();
				for ($i=0;$i<count($asJugadores['DATOS']);$i++) {
					$asPrincipal['BLOQUE_LISTA_JUGADORES'][]=array (
						'CLASE' => ($i%2==0) ? "non" : "par",
						'POSICION' => $asJugadores['DATOS'][$i]['iniciales_esp'],
						'ID_JUGADOR' => $asJugadores['DATOS'][$i]['id_unico'],
						'NOMBRE_JUGADOR' => $asJugadores['DATOS'][$i]['nombre'],
						'COMBO_POSICIONES' => $this->tools_lib->GeneraCombo( array(
							'NOMBRE' => "slc".$asJugadores['DATOS'][$i]['id_unico'],
							'DATASET' => $asPosiciones,
							'DEFAULT' => $asJugadores['DATOS'][$i]['posicion']
						))
					);
					switch ($asJugadores['DATOS'][$i]['posicion']) {
						case 99:
							$asPrincipal['BLOQUE_BANCA'][]=array (
								'CLASE' => "non",
								'POSICION' => $asJugadores['DATOS'][$i]['iniciales_juego'],
								'NOMBRE' => $asJugadores['DATOS'][$i]['nombre']
							);
							break;
						case 0:
							$asPrincipal['BLOQUE_PORTERO'][]=array (
								'NOMBRE' => $asJugadores['DATOS'][$i]['nombre']
							);
							$iContadorTitulares++;
							$aiContadorPosiciones[0]++;
							break;
						case 1:
						case 2:
						case 3:
							$asPrincipal['BLOQUE_DEFENSAS'][]=array (
								'NOMBRE' => $asJugadores['DATOS'][$i]['nombre']
							);
							$iContadorTitulares++;
							$aiContadorPosiciones[1]++;
							break;
						case 4:
						case 5:
						case 6:
						case 7:
						case 8:
							$asPrincipal['BLOQUE_MEDIOS'][]=array (
								'NOMBRE' => $asJugadores['DATOS'][$i]['nombre']
							);
							$iContadorTitulares++;
							$aiContadorPosiciones[2]++;
							
							break;
						case 9:
						case 10:
							$asPrincipal['BLOQUE_DELANTEROS'][]=array (
								'NOMBRE' => $asJugadores['DATOS'][$i]['nombre']
							);
							$iContadorTitulares++;
							$aiContadorPosiciones[3]++;
							break;
					}
				}
				$asClub=$this->clubes_mod->RegresaDatos($iClaveClub);
				$asPrincipal['RUTA_LOGO']=$asClub['DATOS']['ruta_logo'];
				$asPrincipal['NOMBRE_CLUB']=$asClub['DATOS']['nombre'];
				$asPrincipal['NUMERO_DEFENSAS']=$aiContadorPosiciones[1];
				$asPrincipal['NUMERO_MEDIOS']=$aiContadorPosiciones[2];
				$asPrincipal['NUMERO_DELANTEROS']=$aiContadorPosiciones[3];
				$asPrincipal['CLAVE_CLUB']=$iClaveClub;
				$asClubesAdministrados=$this->draft_mod->getClubesAdministrados();
				$asPrincipal['COMBO_CLUBES']=  $this->tools_lib->GeneraCombo(array (
					'NOMBRE' => "slcClub",
					'DATASET' => $asClubesAdministrados, 
					'EVENTOS' => 'onchange="JavaScript: CambiaClubes(this.value);"',
					'ID' => 'combo-clubes',
					'DEFAULT' => $iClaveClub
				));
				$bError=0;
				$asPrincipal['MENSAJE']="";
				if ($iContadorTitulares>11) {
					$bError=1;	
					$asPrincipal['MENSAJE']="<div class=\"error\">Ha seleccionado mas de 11 titulares.</div>";	
				}
				if ($iContadorTitulares<11) {
					$bError=1;	
					$asPrincipal['MENSAJE'].="<div class=\"error\">Faltan titulares de seleccionar.</div>";	
				}
				if ($aiContadorPosiciones[0] > 1) {
					$bError=1;	
					$asPrincipal['MENSAJE'].="<div class=\"error\">Esta seleccionado mas de un portero.</div>";	
				}
				if ($bError==0)
					$asPrincipal['MENSAJE']="<div class=\"success\">Todo OK</div>";	

			}
			$this->main_lib->simple_display($asPrincipal, 'draft/alineaciones_jugadores_vw', 'una-columna_vw', 'ADMIN');
/*
		
		$sTabs="\t\t\t\t";
		$sQuery="Select rt.claveJugador, hab.Nombre, cpos.InicialesESP, rt.posicion, hab.ID_NUMBER "
			." From habilidades hab "
			." Inner Join cat_posiciones cpos On hab.PosicionRegistrada=cpos.Clave "
			." Inner Join rostertemporal rt On rt.claveJugador=hab.ID_NUMBER "
			." Where rt.claveEquipo='".$iClaveEquipo."' Order By hab.PosicionRegistrada";
		$rLocal=mysql_query($sQuery);
		$sJugadores="";
		$sColor="";
		$asPosiciones=array("Banca", "Portero", "Defensa", "Medio", "Delantero");
		$sListaBanca="";
		$sListaPortero="";
		$sListaDefensas="";
		$sListaMedios="";
		$sListaDelanteros="";
		$aiContadorPosiciones=array(0,0,0,0);
		$iContadorTitulares=0;
		while ($row=mysql_fetch_object($rLocal)) {
			if ($sColor=="non")
				$sColor="par";
			else
				$sColor="non";
				
			$sListaJugadores.=$sTabs."\t<tr>\n"
					.$sTabs."\t\t<td id=\"".$sColor."\">&nbsp;".$row->InicialesESP."&nbsp;</td>\n"
					.$sTabs."\t\t<td id=\"".$sColor."\"><a href=\"javascript: callPage('Draft/xi_habilidades_jugador.php?id=".$row->ID_NUMBER."','habilidades-jugador','Cargando...','Error@Carga'); \">".$row->Nombre."</a></td>\n"
					.$sTabs."\t\t<td id=\"".$sColor."\" align=\"right\">\n"
					.$sTabs."\t\t\t<select name=\"slc".$row->claveJugador."\" class=\"loquesea\">\n";
					
			for ($i=0;$i<5;$i++) {
				if ($i==$row->posicion) {
					$sListaJugadores.=$sTabs."\t\t\t\t<option value=\"".$i."\" selected=\"yes\">".$asPosiciones[$i]."</option>\n";
				}
				else
					$sListaJugadores.=$sTabs."\t\t\t\t<option value=\"".$i."\">".$asPosiciones[$i]."</option>\n";
			}		
			$sListaJugadores.=$sTabs."\t\t\t</select>\n"
					.$sTabs."\t\t</td>\n"
					.$sTabs."\t</tr>\n";
			switch ($row->posicion) {
				case "0":
					$sListaBanca.=$sTabs."\t<tr>\n"
					.$sTabs."\t\t<td id=\"".$sColor."\">&nbsp;".$row->InicialesESP."&nbsp;</td>\n"
					.$sTabs."\t\t<td id=\"".$sColor."\">".$row->Nombre."</td>\n"	
					.$sTabs."\t</tr>\n";	
					break;
				case "1":
					$sListaPortero.=$sTabs."\t<div class=\"pos_Portero\">\n"
						.$sTabs."\t\t".$row->Nombre."\n"	
						.$sTabs."\t</div>\n";
					$iContadorTitulares++;
					$aiContadorPosiciones[0]++;	
					break;
				case "2":
					$sListaDefensas.=$sTabs."\t<div class=\"pos_Defensa\">\n"
						.$sTabs."\t\t".$row->Nombre."\n"	
						.$sTabs."\t</div>\n";
					$aiContadorPosiciones[1]++;	
					$iContadorTitulares++;
					break;
				case "3":
					$sListaMedios.=$sTabs."\t<div class=\"pos_Medio\">\n"
						.$sTabs."\t\t".$row->Nombre."\n"	
						.$sTabs."\t</div>\n";
					$aiContadorPosiciones[2]++;
					$iContadorTitulares++;
					break;
				case "4":
					$sListaDelanteros.=$sTabs."\t<div class=\"pos_Atacante\">\n"
						.$sTabs."\t\t".$row->Nombre."\n"	
						.$sTabs."\t</div>\n";
					$aiContadorPosiciones[3]++;
					$iContadorTitulares++;
					break;
			}	
		}
		mysql_free_result($rLocal);
		$sQuery="SELECT cl.nombre_corto, cl.ruta_logo FROM equipos cl WHERE cl.id_equipo=".$iClaveEquipo;
		$rLocal=mysql_query($sQuery);
		$row=mysql_fetch_object($rLocal);
		$sNombreClub="<img src=\"escudos/mini/s".$row->ruta_logo.".gif\" align=\"left\" /><h2>".$row->nombre_corto."</h2><br/>\n"
				."	(".$aiContadorPosiciones[1]."-".$aiContadorPosiciones[2]."-".$aiContadorPosiciones[3].")";
		mysql_free_result($rLocal);
		m_CierraConexion($oCon);
		$sOutput=m_CargaArchivo("Draft/alineaciones_jugadores.html");
		$sOutput=str_replace("<!--LISTA_JUGADORES-->",$sListaJugadores,$sOutput);
		$sOutput=str_replace("<!--LOGO_NOMBRE_CLUB-->",$sNombreClub, $sOutput);
		$sOutput=str_replace("<!--CLAVE_CLUB-->","\"".$iClaveEquipo."\"", $sOutput);
		$sOutput=str_replace("<!--PORTERO-->", $sListaPortero, $sOutput);
		$sOutput=str_replace("<!--DEFENSAS-->", $sListaDefensas, $sOutput);
		$sOutput=str_replace("<!--MEDIOS-->", $sListaMedios, $sOutput);
		$sOutput=str_replace("<!--DELANTEROS-->", $sListaDelanteros, $sOutput);
		$sOutput=str_replace("<!--LISTA_BANCA-->", $sListaBanca, $sOutput);
		$bError=0;
		if ($iContadorTitulares>11) {
			$bError=1;	
			$sMensaje="Ha seleccionado mas de 11 titulares.\n";	
		}
		if ($iContadorTitulares<11) {
			$bError=1;	
			$sMensaje.="Faltan titulares de seleccionar.\n";	
		}
		if ($aiContadorPosiciones[0] > 1) {
			$bError=1;	
			$sMensaje.="Esta seleccionado mas de un portero.\n";	
		}
		if ($bError==0)
			$sMensaje="Todo OK";	
		$sOutput=str_replace("<!--MENSAJE-->", $sMensaje, $sOutput);
		return ($sOutput);
*/
		}
		
		/*function xi_habilidades ($piJugador) {
			$this->load->model('jugadores_mod');
			$asHabilidades=$this->jugadores_mod->Habilidades($piJugador);
			if ($asHabilidades['ESTATUS']==1) {
				$asGrafJug=$this->jugadores_mod->getGraficasJugadores();
				for ($i=0;$i<count($asGrafJug['DATOS']);$i++) {
					if ($asGrafJug['DATOS'][$i]['boleano']=="F") {
						$iMaximoCampo=$asGrafJug['DATOS'][$i]['max'];	
						$iPorcentaje=intval(($asHabilidades['DATOS'][$asGrafJug['DATOS'][$i]['campo']]*100)/$iMaximoCampo);	
						if ($iPorcentaje>90)
							$sColor="#FF3333";
						elseif 	(($iPorcentaje<=90) && ($iPorcentaje>80))
							$sColor="#FF0000";
						elseif 	(($iPorcentaje<=80) && ($iPorcentaje>70))
							$sColor="#F03333";
						elseif 	(($iPorcentaje<=70) && ($iPorcentaje>60))
							$sColor="#AA9900";
						elseif 	(($iPorcentaje<=60) && ($iPorcentaje>50))
							$sColor="#EE33FF";
						elseif 	($iPorcentaje<=50)
							$sColor="#00CE60";
						$asPrincipal['BLOQUE_HABILIDADES'][]=array (
							'LEYENDA' => $asGrafJug['DATOS'][$i]['leyenda'],
							'VALOR' => $asHabilidades['DATOS'][$asGrafJug['DATOS'][$i]['campo']],
							'PORCENTAJE' => $iPorcentaje,
							'COLOR' => $sColor
						);
					}
					else {
						if ($asHabilidades['DATOS'][$asGrafJug['DATOS'][$i]['campo']]==1) {
							$asPrincipal['BLOQUE_HABILIDADES'][]=array (
								'LEYENDA' => $asGrafJug['DATOS'][$i]['leyenda'],
								'VALOR' => "*",
								'PORCENTAJE' => "",
								'COLOR' => ""
							);
						}
					}					
				}
				$asPrincipal['NOMBRE']=$asHabilidades['DATOS']['nombre'];
				$this->parser->parse('draft/xi_habilidades_jugadores_vw', $asPrincipal);
			}
			else
				echo "No existe ese jugador";
		}
		*/
		
		function xi_habilidades ($piJugador, $piJugador2=0, $piJugador3=0) {
			/* pChart library inclusions */ 
			include("externos/pChart2.1.4/class/pData.class.php"); 
			include("externos/pChart2.1.4/class/pDraw.class.php"); 
			include("externos/pChart2.1.4/class/pRadar.class.php"); 
			include("externos/pChart2.1.4/class/pImage.class.php");
			$iAlto=270;
			$iAncho=530;
			$asHabilidades=array();
			$this->load->model('jugadores_mod');
			$asHabilidades[0]=$this->jugadores_mod->Habilidades($piJugador);
			$iJugadores=1;
			if ($piJugador2<>0){
				$asHabilidades[1]=$this->jugadores_mod->Habilidades($piJugador2);
				$iJugadores++;
			}
			if ($piJugador3<>0) {
				$asHabilidades[2]=$this->jugadores_mod->Habilidades($piJugador3);
				$iJugadores++;
			}
			if ($asHabilidades[0]['ESTATUS']==1) {
				$asGrafJug=$this->jugadores_mod->getGraficasJugadores();
				$MyData = new pData();
				$aiData=array();
				for ($i=0;$i<$iJugadores;$i++)
					$aiData[$i]= array(1 => array(), 2 => array(), 3 => array());
				$asCampo=array(1 => array(), 2 => array(), 3 => array());
				for ($i=0;$i<count($asGrafJug['DATOS']);$i++) {
					if ($asGrafJug['DATOS'][$i]['boleano']=="F") {
						if (($asGrafJug['DATOS'][$i]['tipo']==3) || ($asGrafJug['DATOS'][$i]['tipo']==4)) {
								for ($k=0;$k<$iJugadores;$k++) {
									$aiData[$k][$asGrafJug['DATOS'][$i]['grupo']][]=$asHabilidades[$k]['DATOS'][$asGrafJug['DATOS'][$i]['campo']];
								}
								$asCampo[$asGrafJug['DATOS'][$i]['grupo']][]=$asGrafJug['DATOS'][$i]['corto'];
						}
						
					}
					else {
						if ($asHabilidades[0]['DATOS'][$asGrafJug['DATOS'][$i]['campo']]==1) {
							$asPrincipal['BLOQUE_HABILIDADES'][]=array (
								'LEYENDA' => $asGrafJug['DATOS'][$i]['leyenda'],
								'VALOR' => "*",
								'PORCENTAJE' => "",
								'COLOR' => ""
							);
						}
					}					
				} //for
				//var_dump ($aiData);
				//var_dump ($asCampo);
				for ($k=0;$k<$iJugadores;$k++) {
					$MyData->addPoints($aiData[$k][1], "A".$k);
					$MyData->setSerieDescription("A".$k,$asHabilidades[$k]['DATOS']['nombre']);
				}
				/* Define the absissa serie */
				$MyData->addPoints($asCampo[1],"Labels");
				$MyData->setAbscissa("Labels");

				/* Write the picture title */
				/* Create the pChart object */
				$myPicture = new pImage($iAncho,$iAlto,$MyData);
				/* Draw a solid background */
				$Settings = array("R"=>179, "G"=>217, "B"=>91, "Dash"=>1, "DashR"=>199, "DashG"=>237, "DashB"=>111);
				$myPicture->drawFilledRectangle(0,0,$iAncho,$iAlto,$Settings);
			   
				/* Overlay some gradient areas */
				$Settings = array("StartR"=>194, "StartG"=>231, "StartB"=>44, "EndR"=>43, "EndG"=>107, "EndB"=>58, "Alpha"=>50);
				$myPicture->drawGradientArea(0,0,$iAncho,$iAlto,DIRECTION_VERTICAL,$Settings);
				$myPicture->drawGradientArea(0,0,$iAncho,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100));
			   
				/* Add a border to the picture */
				$myPicture->drawRectangle(0,0,$iAncho-1,$iAlto-1,array("R"=>0,"G"=>0,"B"=>0));

				$myPicture->setFontProperties(array("FontName"=>"externos/pChart2.1.4/fonts/Silkscreen.ttf","FontSize"=>6));
				$myPicture->drawText(10,13,"Habilidades",array("R"=>255,"G"=>255,"B"=>255));
			   
				/* Set the default font properties */ 
				$myPicture->setFontProperties(array("FontName"=>"externos/pChart2.1.4/fonts/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));
			   
				/* Enable shadow computing */ 
				$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
			   
				/* Create the pRadar object */ 
				$SplitChart = new pRadar();
			   
				/* Draw a radar chart  #1*/ 
				$myPicture->setGraphArea(10,25,150,225);
				$Options = array(
								 "Layout"=>RADAR_LAYOUT_STAR,
								 "DrawPoly" => true,
								 "BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>100,"EndR"=>207,"EndG"=>227,"EndB"=>125,"EndAlpha"=>50),
								 "FontName"=>"externos/pChart2.1.4/fonts/pf_arma_five.ttf",
								 "FontSize"=>6,
								 "LabelPos" => RADAR_LABELS_HORIZONTAL);
				/*$Options = array(
								"DrawPoly"=>TRUE,
								"WriteValues"=>TRUE,
								"ValueFontSize"=>8,
								"Layout"=>RADAR_LAYOUT_CIRCLE,
								"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>100,"EndR"=>207,"EndG"=>227,"EndB"=>125,"EndAlpha"=>50)
								);*/
				$SplitChart->drawRadar($myPicture,$MyData,$Options);

				/* Draw a radar chart  #2*/
				$MyData2 = new pData();

				for ($k=0;$k<$iJugadores;$k++) {
					$MyData2->addPoints($aiData[$k][2], "A".$k);
				}
				
				/* Define the absissa serie */
				$MyData2->addPoints($asCampo[2],"Labels");
				$MyData2->setAbscissa("Labels");


				$myPicture->setGraphArea(155,25,450,195);
				$Options = array(
								 "Layout"=>RADAR_LAYOUT_STAR,
								 "DrawPoly" => true,
								 "BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>100,"EndR"=>207,"EndG"=>227,"EndB"=>125,"EndAlpha"=>50),
								 "FontName"=>"externos/pChart2.1.4/fonts/pf_arma_five.ttf",
								 "FontSize"=>6,
								 "LabelPos" => RADAR_LABELS_HORIZONTAL);
				$SplitChart->drawRadar($myPicture,$MyData2,$Options);

				
				// Draw a radar chart  #3 
				$MyData3 = new pData();

				for ($k=0;$k<$iJugadores;$k++) {
					$MyData3->addPoints($aiData[$k][3], "A".$k);
				}
				// Define the absissa serie 
				$MyData3->addPoints($asCampo[3],"Labels");
				$MyData3->setAbscissa("Labels");
				$myPicture->setGraphArea(455,25,565,225);
				$Options = array(
								 "Layout"=>RADAR_LAYOUT_CIRCLE,
								 "DrawPoly" => true,
								 "BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>100,"EndR"=>207,"EndG"=>227,"EndB"=>125,"EndAlpha"=>50),
								 "FontName"=>"externos/pChart2.1.4/fonts/pf_arma_five.ttf",
								 "FontSize"=>6,
								 "LabelPos" => RADAR_LABELS_HORIZONTAL);
				$SplitChart->drawRadar($myPicture,$MyData3,$Options);
			

				/* Write the chart legend */ 
				$myPicture->setFontProperties(array("FontName"=>"externos/pChart2.1.4/fonts/pf_arma_five.ttf","FontSize"=>6));
				$myPicture->drawLegend(235,205,array("Style"=>LEGEND_BOX,"Mode"=>LEGEND_HORIZONTAL));
				//$asPrincipal['NOMBRE']=$asHabilidades['DATOS']['nombre'];
				//$this->parser->parse('draft/xi_habilidades_jugadores_vw', $asPrincipal);
			}
			else {
				$myPicture->setFontProperties(array("FontName"=>"externos/pChart2.1.4/fonts/Silkscreen.ttf","FontSize"=>6));
				$myPicture->drawText(10,13,"Habilidades",array("R"=>255,"G"=>255,"B"=>255));
			}
			
			/* Create and populate the pData object */
			/*$MyData->addPoints(array(8,10,12,20,30,15),"ScoreB"); 
			$MyData->addPoints(array(4,8,16,32,16,8),"ScoreC"); 
			$MyData->setSerieDescription("ScoreB","Application B");
			$MyData->setSerieDescription("ScoreC","Application C");*/
		   
			/* Define the absissa serie */
			/*$MyData->addPoints(array("Size","Speed","Reliability","Functionalities","Ease of use","Weight"),"Labels");
			$MyData->setAbscissa("Labels");*/
		   
			/* Draw a radar chart */ 
			/*$myPicture->setGraphArea(390,25,690,225);
			$Options = array("Layout"=>RADAR_LAYOUT_CIRCLE,"LabelPos"=>RADAR_LABELS_HORIZONTAL,"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>50,"EndR"=>32,"EndG"=>109,"EndB"=>174,"EndAlpha"=>30), "FontName"=>"externos/pChart2.1.4/fonts/pf_arma_five.ttf","FontSize"=>6);
			$SplitChart->drawRadar($myPicture,$MyData,$Options);*/
			/* Render the picture (choose the best way) */
			$myPicture->autoOutput("pictures/example.radar.png");			
			
		}

		
		
		/*Fin de la clase*/
	}
?>