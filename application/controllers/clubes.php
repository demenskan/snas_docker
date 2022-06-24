<?php

	class Clubes extends CI_Controller {

		function __construct() {
			parent::__construct();
			$this->load->library('tools_lib');
			$this->load->model('clubes_mod');
		}
		
		
		
		function Inicio ($psInput) {
			$asDatos=$this->clubes_mod->RegresaDatosPorURL($psInput);
			$asContenido=array (
				'PRINCIPAL' => $this->Noticias($asDatos['DATOS']['id_unico']),
				'BARRA_IZQUIERDA' => $this->Generales($psInput).$this->GoleadoresHistoricos($asDatos['DATOS']['id_unico']),
				'BARRA_DERECHA' => $this->Roster($psInput)
			);
			$asControlador=array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/tres-columnas_vw", $asContenido, true),
				'TIPO_ACCESO' => 'PUBLIC'
			);
			$this->main_lib->display($asControlador);
		}

		function Generales ($psInput) {
			$asDatos=$this->clubes_mod->RegresaDatosPorURL($psInput);
			$asPrincipal=array (
				'NOMBRE_CORTO' =>  $asDatos['DATOS']['nombre_corto'],
				'LOGOTIPO' =>  base_url()."img/escudos/".$asDatos['DATOS']['ruta_logo'].".gif",
				'NOMBRE_LARGO' =>  $asDatos['DATOS']['nombre'],
				'ESTADIO' =>  $asDatos['DATOS']['nombre_estadio'],
				'NUMERO_PERSONAS' =>  $asDatos['DATOS']['capacidad'],
				'SEDE' =>  $asDatos['DATOS']['ubicacion'],
				'CONSEJO' =>  $asDatos['DATOS']['nombre_consejo'],
				'LEMA' =>  $asDatos['DATOS']['lema'],
				'DESCRIPCION' =>  ''
			);
			return ($this->load->view('clubes/generales_vw', $asPrincipal, true));
		}

				
		function Roster ($psInput, $piTemporada='actual') {
			$asDatosClub=$this->clubes_mod->RegresaDatosPorURL($psInput);
			//$this->tools_lib->dump($asDatosClub['DATOS']);
			$asDatosRoster=$this->clubes_mod->Roster($asDatosClub['DATOS']['id_unico'], $piTemporada);
			if ($asDatosRoster['ESTATUS']==1) {
				for ($i=0;$i<count($asDatosRoster['DATOS']);$i++) {
					$asReporte[$i]=array (
					'TX #' => $asDatosRoster['DATOS'][$i]['numero'],
					'TX Nombre' => "<a href=\"".base_url()."jugadores/ver/".$asDatosRoster['DATOS'][$i]['id_unico']."\">".$asDatosRoster['DATOS'][$i]['nombre']."</a>"
					);
				}
			}
			$asPrincipal=array (
				'LISTA_ROSTER' => $this->tools_lib->genera_reporte (array ('DATOS' => $asReporte, 'ANCHO' => '700', 'TITULO' => "ROSTER TEMPORADA ".$piTemporada))
			);
			return ($this->load->view('clubes/roster_vw', $asPrincipal, true));
		}
		
		function Lista() {
			$sTabs="\t\t\t";
			$sOut=$sTabs."<tr>\n";
			$asClubes=$this->clubes_mod->Lista();
			if ($asClubes['ESTATUS']!=-1) {
				for ($i=0;$i<count($asClubes['DATOS']);$i++) {
					if ($i%4==0)
						$sOut.=$sTabs."</tr>\n"
								.$sTabs."<tr>\n";
					$sOut.=$sTabs."\t<td><img src=\"".base_url()."img/escudos/mini/s".$asClubes['DATOS'][$i]['ruta_logo'].".gif\"></td>\n"
							.$sTabs."\t<td><a href=\"".base_url()."clubes/inicio/".$asClubes['DATOS'][$i]['ruta_logo']."\">".$asClubes['DATOS'][$i]['nombre_corto']."</td>\n";
				}
			}
			$sOut.=$sTabs."</tr>\n";
			$asPrincipal=array ('EQUIPOS' => $sOut);
			$asContenido=array ('PRINCIPAL' => $this->load->view('listado/clubes_vw', $asPrincipal, true));
			$asControlador= array ('CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
						'TIPO_ACCESO' => 'PUBLIC');	
			$this->main_lib->display($asControlador);
		}
		
		
		
		function Noticias ($piClaveClub) {
			$asNoticias=$this->clubes_mod->Noticias($piClaveClub);
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