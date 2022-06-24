<?php

	class inicio extends CI_Controller {
	
	  function __construct() {
		  parent::__construct();
		  $this->load->model('draft_mod');
	  }
	  
	  function index() {
		$asPrincipal=array(
		  'RUTA_RAIZ' => base_url(),
		  'INDEX_URI' => $this->config->item('index_uri'),
		  'AVISOS_SUBASTA' => $this->avisos_subasta(),
		  'CONTRATACIONES_RECIENTES' => $this->contrataciones_recientes(),
		  'PROPUESTAS_MIS_CLUBES' => $this->propuestas_mis_clubes(),
		  'REPORTE_CANTIDAD_CONTRATADOS_POR_CLUB' => $this->reporte_contratados()
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
		$sSalida=$this->parser->parse ('draft/inicio_propuestas_vw', $asListaPropuestas, true);
		return ($sSalida);
	  }
	  
	  function contrataciones_recientes() {
		
	  }
	  
	  function avisos_subasta() {
		
	  }
	  
	  function reporte_contratados() {
		
	  }
	  
	}
?>