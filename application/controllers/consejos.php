<?php

	class Consejos extends CI_Controller {

		function __construct() {
			parent::__construct();
			$this->load->library('tools_lib');
			$this->load->model('consejos_mod');
		}
		
		
		
		function Inicio ($psInput) {
			$asDatos=$this->consejos_mod->RegresaDatosPorURL($psInput);
			$asContenido=array (
				'PRINCIPAL' => $this->Noticias($asDatos['DATOS']['id_unico']),
				'BARRA_IZQUIERDA' => $this->Generales($psInput),
				'BARRA_DERECHA' => $this->Miembros($asDatos['DATOS']['id_unico'])
			);
			$asControlador=array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/tres-columnas_vw", $asContenido, true),
				'TIPO_ACCESO' => 'PUBLIC'
			);
			$this->main_lib->display($asControlador);
		}

		function Generales ($psInput) {
			$asDatos=$this->consejos_mod->RegresaDatosPorURL($psInput);
			$asPrincipal=array (
				'INICIALES' =>  $asDatos['DATOS']['iniciales'],
				'LOGOTIPO' =>  base_url()."img/consejos/".$asDatos['DATOS']['imagen'],
				'NOMBRE' =>  $asDatos['DATOS']['nombre'],
			);
			return ($this->load->view('consejos/generales_vw', $asPrincipal, true));
		}

				
		function Miembros ($piClave) {
			$asMiembros=$this->consejos_mod->getListaClubes($piClave);
			if ($asMiembros['ESTATUS']==1) {
				for ($i=0;$i<count($asMiembros['DATOS']);$i++) {
					$asReporte[$i]=array (
					'TX ' => "<img src=\"img/escudos/mini/s".$asMiembros['DATOS'][$i]['ruta_logo'].".gif\">",
					'TX Nombre' => "<a href=\"clubes/inicio/".$asMiembros['DATOS'][$i]['ruta_logo']."\">".$asMiembros['DATOS'][$i]['nombre_corto']."</a>"
					);
				}
			}
			$asPrincipal=array (
				'LISTA_ROSTER' => $this->tools_lib->genera_reporte (array ('DATOS' => $asReporte, 'ANCHO' => '700', 'TITULO' => "Miembros del consejo"))
			);
			return ($this->load->view('consejos/roster_vw', $asPrincipal, true));
		}
		
		function Noticias ($piClaveClub) {
			$asNoticias=$this->consejos_mod->Noticias_lista($piClaveClub);
			//$this->tools_lib->dump($asNoticias['DATOS']);
			if ($asNoticias['ESTATUS']==1) {
				$asSalida=array ('RESUMENES' => '', 'ROTADOR_IMAGENES' => '', 'NOTICIAS_COMPLEMENTARIAS' => '', 'NOTICIAS_VIEJAS' => '', 'TABULADORES' => '');
				$sSalidagc="";
				$sMargen="\t\t\t\t\t\t";
				$sClaseTab="ui-tabs-nav-item ui-tabs-selected";
				$asSeccion[4]="left";
				$asSeccion[5]="col3-mid left";
				$asSeccion[6]="right";
				for ($iCont=0;$iCont<count($asNoticias['DATOS']);$iCont++) {
					//RECIENTES
					/*Pone los tabuladores de las noticias*/
					if ($iCont<3) {
						$asSalida['TABULADORES'].="<li class=\"".$sClaseTab."\" id=\"nav-fragment-".($iCont+1)."\">\n"
								.$sMargen."\t<a href=\"#fragment-".($iCont+1)."\">\n"
								.$sMargen."\t\t<span>[".substr($asNoticias['DATOS'][$iCont]["titulo"],0,24)."...]</span>\n"
								.$sMargen."\t</a>\n"
								.$sMargen."</li>";
						/*Los contenidos de las noticias*/
						$asSalida['RESUMENES'].="<div id=\"fragment-".($iCont+1)."\" class=\"ui-tabs-panel\" style=\"\">\n"
								.$sMargen."\t<h2>".substr($asNoticias['DATOS'][$iCont]['titulo'],0,47)."</h2>\n"
								.$sMargen."\t\t<p>".$asNoticias['DATOS'][$iCont]["resumen"]."[<a href=\"".base_url()."noticias/ver/".$asNoticias['DATOS'][$iCont]["id_unico"]."\">Ver mas</a>]</p>\n"
								.$sMargen."</div>\n";
						/*Las imagenes a cargar*/
						$asSalida['ROTADOR_IMAGENES'].="#rotator #fragment-".($iCont+1)." { \n"
									.$sMargen."\t\tbackground:transparent url('".base_url().$asNoticias['DATOS'][$iCont]["imagen"]."') no-repeat top right;\n"
									.$sMargen."}\n";
						$sClaseTab="ui-tabs-nav-item";
					}
					elseif ($iCont<6) {
						//COMPLEMENTARIAS
						$asSalida['NOTICIAS_COMPLEMENTARIAS'].="<div class=\"col3 ".$asSeccion[$iCont+1]."\">\n"
						   ."					<div class=\"column-content\">\n"
						   ."						<div class=\"post\">\n"
						   ."							<p><a href=\"".base_url()."noticias/ver/".$asNoticias['DATOS'][$iCont]["id_unico"]."\"><img src=\"".base_url().$asNoticias['DATOS'][$iCont]["imagen"]."\" alt=\"nada\" class=\"bordered\" width=\"152\" /></a></p>\n"
						   ."							<h4><a href=\"".base_url()."noticias/ver/".$asNoticias['DATOS'][$iCont]["id_unico"]."\">".$asNoticias['DATOS'][$iCont]["titulo"]."</a></h4>\n"
						   ."							".$asNoticias['DATOS'][$iCont]["resumen"]."<br/>\n"
						   ."						</div>\n"
						   ."					</div>\n"
						   ."				</div>\n";
					}
					else {
						//VIEJAS
						$asSalida['NOTICIAS_VIEJAS'].="<li> \n"
							."		<a href=\"".base_url()."noticias/ver/".$asNoticias['DATOS'][$iCont]["id_unico"]."\">".$asNoticias['DATOS'][$iCont]["titulo"]."</a>\n"
							."</li>\n";
					}
				}
			}
			else {
				$asSalida=array ('RESUMENES' => $asNoticias['MENSAJE'], 'TABULADORES' => '', 'IMAGENES' => '', 'NOTICIAS_COMPLEMENTARIAS' => '', 'NOTICIAS_VIEJAS' => '');
			}
			return ($this->load->view('clubes/noticias_vw', $asSalida, true));
		}


		function GoleadoresHistoricos ($piClaveClub) {
			$asGoleadores=$this->clubes_mod->Goleadores($piClaveClub);
			if ($asGoleadores['ESTATUS']!=-1) {
				for ($i=0;$i<count($asGoleadores['DATOS']);$i++) {
					$asRepGol[$i]=array (
						'TX Nombre' => "<a href=\"".base_url()."jugadores/ver/".$asGoleadores['DATOS'][$i]['id_unico']."\">".$asGoleadores['DATOS'][$i]['nombre']."</a>",
						'TX Goles' => $asGoleadores['DATOS'][$i]['goles']
					);
				}
				$sSalida=$this->tools_lib->genera_reporte(array ('DATOS' => $asRepGol, 'TITULO' => "Goleadores Historicos", 'ANCHO' => "120"));
			}
			else
				$sSalida=$asGoleadores['MENSAJE'];
			return ($sSalida);
		}
	}
?>