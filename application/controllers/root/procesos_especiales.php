<?php

	class procesos_especiales extends CI_Controller {
	
		function __construct() {
			parent::__construct();
			$this->load->model('root_mod');
		}
		
		function reporte_directo_rosters() {
			/*Inicio seccion viejita
			  require_once("modulos/herramientas.php");
                if (isset($_REQUEST["slcConsejo"]))
                        $iConsejo=$_REQUEST["slcConsejo"];
                else 
                        $iConsejo=-1;
                if ($iConsejo!=-1) {
                        if ($iConsejo!="_EXTRA_") 
                                $sCondicion= " WHERE cl.id_consejo=".$iConsejo;
                        else 
                                $sCondicion=""; 
                        for ($a=1;$a<7;$a++) {
                                $sComa= $a<6 ? "," : "";
                                $sSubQuerys.=" (select concat('<a href=\"JavaScript: callPage(\'procesos_especiales/xi_roster.php?cl=', cl.id_unico ,'&ssn=".$a."\',"
                                                        ."\'lista-jugadores\',\'<img src=img/loading.gif>\',\'Error en carga\')\">', "
                                                        ."count(*) "
                                                        ."+ (Select count(*) from jugadores_sin_registrar jsrx Where jsrx.id_equipo=cl.id_unico AND jsrx.id_temporada=".$a.")"
                                                        .",'</a>') "
                                                        ."  from rostersportemporada rptx "
                                                        ."  where rptx.id_equipo=cl.id_unico "
                                                        ."    and rptx.id_temporada=".$a.") as '".$a."'".$sComa." \n";
                        }
                        
                        $sQy="SELECT cl.nombre_corto as 'Equipo', ".$sSubQuerys
                                ." FROM equipos cl "
                                .$sCondicion
                                ." ORDER BY cl.id_unico";
                        $sReporte=mh_GeneraTablaResultados("Jugadores contados por temporada",$sQy);
                }
                else
                        $sReporte="";
                        
                $sSalida=mp_CargaArchivo("procesos_especiales/reporte_directo_rosters.html");
                $sSalida=str_replace("<!--LISTA_CONSEJOS-->",mh_GeneraCombo("slcConsejo","cat_consejos","id_unico","iniciales",$iConsejo,"Todos"),$sSalida);
                $sSalida=str_replace("<!--REPORTE-->",$sReporte,$sSalida);
                return($sSalida);
			Fin seccion viejita */
			$iConsejo=($this->input->post('slcConsejo')<>"") ? $this->input->post('slcConsejo') : '__EXTRA__';
			$iCantidadTemporadas=7;
			if ($iConsejo!='__EXTRA__') {
				$this->load->model(array('consejos_mod'));
				$asClubes=$this->consejos_mod->getListaClubes($iConsejo);
				$asReporte=array('BLOQUE_CLUBES' => array());
				for ($i=0;$i<count($asClubes['DATOS']);$i++) {
					$asBloqueFilas=array();
					for ($j=1;$j<=$iCantidadTemporadas;$j++) {
						$iConteo=$this->root_mod->roster_conteo($asClubes['DATOS'][$i]['id_unico'],$j);
						$asBloqueFilas[]=array(
							'ID_TEMPORADA' => $j,
							'ID_CLUB' => $asClubes['DATOS'][$i]['id_unico'],
							'CONTEO' => $iConteo['DATOS']['conteo']
						);
					}
					$asFilas=array (
						'NOMBRE_CLUB' => $asClubes['DATOS'][$i]['nombre_corto'],
						'BLOQUE_TEMPORADAS' => $asBloqueFilas
					);
					$asReporte['BLOQUE_CLUBES'][]=array(
						'FILAS' => $this->parser->parse('root/reporte_directo_rosters_filas_vw',$asFilas,true)
					);
				}
				$sReporte=$this->parser->parse('root/reporte_directo_rosters_clubes_vw',$asReporte,true);
			}
			else
				$sReporte="";
			$asPrincipal=array (
				'COMBO_CONSEJOS' => $this->tools_lib->GeneraCombo(array(
					'NOMBRE' => "slcConsejo",
					'TABLA' => "cat_consejos",
					'CAMPO_CLAVE' => "id_unico",
					'LEYENDA' => "iniciales",
					'DEFAULT' => $iConsejo,
					'OPCION_EXTRA' => "Todos"
				)),
				'REPORTE' => $sReporte
			);
			
			$asContenido=array ('PRINCIPAL' => $this->parser->parse('root/reporte_directo_rosters_vw', $asPrincipal, true));
			$asControlador= array ('CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
						'TIPO_ACCESO' => 'ROOT');	
			$this->main_lib->display($asControlador);
		}
	
		function xi_roster ($piClub, $piTemporada) {
			$asJugadores=$this->root_mod->roster_lista($piClub, $piTemporada);
			$this->load->model('clubes_mod');
			$asClub=$this->clubes_mod->RegresaDatos($piClub);
			//var_dump($asClub);
			if ($asJugadores['ESTATUS']==1) {
				$asBloque=array();
				for ($i=0;$i<count($asJugadores['DATOS']);$i++) {
					$asBloque[]=array (
						'ID_JUGADOR' => $asJugadores['DATOS'][$i]['id_unico'],
						'NOMBRE_JUGADOR' => $asJugadores['DATOS'][$i]['nombre'],
						'NUMERO' => $asJugadores['DATOS'][$i]['numero'],
					);
				}
				$asSalida=array(
					'CLUB' => $asClub['DATOS']['nombre_corto'],
					'TEMPORADA' => $piTemporada,
					'BLOQUE_JUGADORES' => $asBloque
				);
				$sSalida=$this->parser->parse('root/reporte_directo_rosters_listado_vw', $asSalida , true);
			}
			else
				$sSalida="No hay elementos";
			echo ($sSalida);
		}
		
		function xi_historial ($piJugador) {
			$asClubes=$this->root_mod->roster_historial($piJugador);
			$this->load->model('jugadores_mod');
			if ($asClubes['ESTATUS']==1) {
				$asBloque=array();
				for ($i=0;$i<count($asClubes['DATOS']);$i++) {
					$asBloque[]=array (
						'TEMPORADA' => $asClubes['DATOS'][$i]['id_temporada'],
						'NOMBRE_CLUB' => $asClubes['DATOS'][$i]['club'],
						'ID_CLUB' => $asClubes['DATOS'][$i]['id_club'],
					);
				}
				$asSalida=array(
					'JUGADOR' => $this->jugadores_mod->NombreJugador($piJugador),
					'BLOQUE_TEMPORADAS' => $asBloque
				);
				$sSalida=$this->parser->parse('root/reporte_directo_rosters_historial_vw', $asSalida, true);
			}
			else
				$sSalida="No hay elementos";
			echo ($sSalida);
		}
		
		
		
	}
	
	
?>