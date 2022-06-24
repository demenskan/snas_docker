<?php

	class Inicio extends CI_Controller {
	
		function __construct() {
			parent::__construct();
		}
		
		function index() {
			$sRequest=(isset($_REQUEST['mensaje'])) ? $_REQUEST['mensaje'] : "";
			switch ($sRequest) {
				case "100" : $sMensaje="<div class=\"success\">La noticia se ha agregado con exito</div>\n"; break;
				case "": $sMensaje=""; break;
			}
			$asInicio=array('RUTA_RAIZ' => base_url(), 'MENSAJE' => $sMensaje);
			$asContenido=array (
				'PRINCIPAL' => $this->load->view('admin/inicio_vw',$asInicio, true),
				'BARRA_DERECHA' => ""
			);
			$asControlador=array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN'
			);
			$this->main_lib->display($asControlador);
		}
		
		function draft() {
			$this->load->model('draft_mod');
			$asModulos=$this->draft_mod->modulos_get("activos");
			$asModulosSalida=array();
			for ($i=0;$i<count($asModulos['DATOS']);$i++) {
				/*if ($i%4==0) {  //<-Mejorar condicion
						$sPrefijoGris=($asModulos['DATOS'][$i]['activo']==1) ? "" : "gris_";
						$asIcono=$this->parser->parse('draft/menu_principal_elemento_icono_vw',
								array ('URL' => $asModulos['DATOS'][$i]['url'],
									   'IMG' => $sPrefijoGris.$asModulos['DATOS'][$i]['imagen']), true);
						$asCaption=$this->parser->parse('draft/menu_principal_elemento_caption_vw',
								array ('URL' => $asModulos['DATOS'][$i]['url'],
									   'CAPTION' => $sPrefijoGris.$asModulos['DATOS'][$i]['imagen']), true);
				}*/
				$asModulosSalida[]=array('URL' => $asModulos['DATOS'][$i]['url'],
										 'CAPTION' => $asModulos['DATOS'][$i]['caption'],
										 'IMAGEN' => $asModulos['DATOS'][$i]['imagen']
				);
			}
			$asPrincipal=array(
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'BLOQUE_RENGLON' => $asModulosSalida,
				'AVISOS_SUBASTA' => $this->avisos_subasta(),
				'CONTRATACIONES_RECIENTES' => $this->contrataciones_recientes(),
				'PROPUESTAS_MIS_CLUBES' => $this->propuestas_mis_clubes(),
				'REPORTE_CANTIDAD_CONTRATADOS_POR_CLUB' => $this->reporte_contratados(),
				'MEJORES_OFERTAS' => $this->mejores_ofertas()
			);
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/menu_principal_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}
		
		function propuestas_mis_clubes() {
			$asPropuestas=$this->draft_mod->getPropuestas('otros');
			$asListaPropuestas=array();
			$sColor="non";
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
/*						switch ($asPropuestas['DATOS'][$i]['estatus']) {
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
						}*/
						$sCampoClub="club_origen";
						$asListaPropuestas[]=array (
							'CLIENTE' => $asPropuestas['DATOS'][$i]['cliente']." (".$asPropuestas['DATOS'][$i][$sCampoClub].")",
							'OFRECE' => ($asPropuestas['DATOS'][$i]['tipo_propuesto']==1) ? $sJugador : "",
							'SOLICITA' =>($asPropuestas['DATOS'][$i]['tipo_propuesto']==2) ? $sJugador : "",
							'CLASE_FILA' => $sColor
						);
						$iContFilas++;
						$sClavePropuestaActual=$asPropuestas['DATOS'][$i]['id_unico'];
					}
				}
			}
			else
				$asListaPropuestas[]=array (
					'CLIENTE' => "",'OFRECE' => "No hay propuestas", 'SOLICITA' =>"",'CLASE_FILA' => "notice"
				);
		$sSalida=$this->parser->parse ('draft/inicio_propuestas_vw', array('PROPUESTAS_RECIBIDAS' => $asListaPropuestas), true);
		return ($sSalida);
	  }
	  
		function contrataciones_recientes() {
			//Avisa cuales subastas se han ganado por alguno de los clubes del usuario
			$iTemporada=$this->config->item('temporada_actual');
			$this->load->model(array ('consejos_mod', 'draft_mod'));
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			//Primero, obtencion de los clubes del usuario y sus apadrinados
			$asClubes=$this->consejos_mod->getListaClubes($asConsejo['DATOS']['id_unico'], true);
			$asBloqueGanadores=array();
			$iCont=0;
			if ($asClubes['ESTATUS']==1) {
				for ($i=0;$i<count($asClubes['DATOS']);$i++) {
					$asOfertasGanadoras=$this->draft_mod->getOfertados($iTemporada, $asClubes['DATOS'][$i]['id_unico'],10);
					if ($asOfertasGanadoras['ESTATUS']==1) {
						for ($j=0;$j<count($asOfertasGanadoras['DATOS']);$j++)
							if ($iCont<10) {
								$asBloqueGanadores[]=array(
									'CLASE_FILA' => ($iCont%2==0) ? 'non' : 'par',
									'NOMBRE' => $asOfertasGanadoras['DATOS'][$j]['nombre'],
									'CLUB' => $asOfertasGanadoras['DATOS'][$j]['nombre_corto'],
									'SUELDO_BASE' => $asOfertasGanadoras['DATOS'][$j]['sueldo_base'],
									'TEMPORADAS' => $asOfertasGanadoras['DATOS'][$j]['duracion'],
									'FECHA_CONTRATACION' => $asOfertasGanadoras['DATOS'][$j]['fecha_contratacion']
								);
								$iCont++;
							}
					}
				}
				foreach ($asBloqueGanadores as $key => $fila) 
					$adFechaContratacion[$key] = $fila["FECHA_CONTRATACION"];
				if (count($asBloqueGanadores)>0)
					array_multisort($adFechaContratacion, SORT_DESC, $asBloqueGanadores);
			}
			$sSalida=$this->parser->parse ('draft/inicio_contrataciones_recientes_vw', array('BLOQUE_CONTRATADOS' => $asBloqueGanadores), true);
			return ($sSalida);
		}
	  
		function avisos_subasta() {
			//Avisa si hay mejoras de oferta de alguno de los candidatos de los clubes del usuario
			$this->load->model(array ('consejos_mod', 'draft_mod'));
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			//Primero, obtencion de los clubes del usuario y sus apadrinados
			$asOfertasSuperadas=array();
			$iTemporada=$this->config->item('temporada_actual');
			$iCont=0;
			$asOfertasClub=$this->draft_mod->getOfertadosConsejo($iTemporada, $asConsejo['DATOS']['id_unico'],-1);
			if ($asOfertasClub['ESTATUS']==1) {
				for ($j=0;$j<count($asOfertasClub['DATOS']);$j++) {
					$asMejorOferta=$this->draft_mod->getMejorOferta($iTemporada, $asOfertasClub['DATOS'][$j]['id_jugador']);
					if (($asMejorOferta['ESTATUS']==1) && ($iCont<10)) {
						$asOfertasSuperadas[]=array (
							'CLASE_FILA' => ($iCont%2==0) ? "non" : "par",
							'NOMBRE' => $asOfertasClub['DATOS'][$j]['nombre'],
							'CLUB_OFERTO' => $asOfertasClub['DATOS'][$j]['nombre_corto'],
							'CLUB_SUBIO' => $asMejorOferta['DATOS']['nombre_club'],
							'NUEVA_OFERTA' => "<a href=\"admin/draft_subastas/captura_oferta/"
								.$asOfertasClub['DATOS'][$j]['id_jugador']."/".$asOfertasClub['DATOS'][$j]['id_club']."\">"
								.$asMejorOferta['DATOS']['sueldo_base']."</a>",
						);
						$iCont++;
					}
				}
			}
			$sSalida=$this->parser->parse ('draft/inicio_alerta_mejoras_oferta_vw', array('BLOQUE_AVISOS' => $asOfertasSuperadas), true);
			return ($sSalida);
		}
	  
		function reporte_contratados() {
				// Hace un listado de la cantidad de jugadores contratados por cada club, para referencia
				// orden: de menos a mas
				$this->load->model(array ('consejos_mod', 'draft_mod'));
				$iTemporada=$this->config->item('temporada_actual');
				$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
				$asClubes=$this->consejos_mod->getListaClubes($asConsejo['DATOS']['id_unico'], true);
				$asBloqueReporte=array();
				for ($i=0;$i<count($asClubes['DATOS']);$i++) {
						$asContratados=$this->draft_mod->getContratados($iTemporada, $asClubes['DATOS'][$i]['id_unico']);
						$iCantidad=($asContratados['ESTATUS']==1) ? count($asContratados['DATOS']) : 0;
						$sClase= (($iCantidad >= 16) && ($iCantidad <= 23)) ? "success" : "error";
						$asBloqueReporte[]=array ('CLUB' => $asClubes['DATOS'][$i]['nombre_corto'], 'CANTIDAD' => $iCantidad, 'CLASE_FILA' => $sClase);
				}
				foreach ($asBloqueReporte as $key => $fila) 
						$aiCantidad[$key] = $fila["CANTIDAD"];
				array_multisort($aiCantidad, SORT_ASC, $asBloqueReporte);
				$sSalida=$this->parser->parse ('draft/inicio_reporte_contratados_vw', array('BLOQUE_CLUBES' => $asBloqueReporte), true);
				return ($sSalida);
		}


		function mejores_ofertas() {
			$asMejoresOfertas=$this->draft_mod->mejores_ofertas_get();
			return($this->tools_lib->genera_reporte(array(
						'DATOS' => $asMejoresOfertas['DATOS'],
						'TITULO' => 'Mejores ofertas'  
			)));
		}
	}
	
	
?>