<?php
	
	class draft_subastas extends CI_Controller {
		
		function __construct() {
			parent::__construct();
			$this->load->model('draft_mod');
		}
		
		
		function index() {
			$this->principal();
		}
	
		function principal($piCodigoClub=-1, $piModo=0, $psQuery="ALL", $psScope="L", $piPagina=1) {
			$this->load->model(array('consejos_mod','clubes_mod'));
			$iTemporada=$this->config->item('temporada_actual');
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			$iConsejo=$asConsejo['DATOS']['id_unico'];
			$asClubes=$this->draft_mod->getClubesAdministrados();
			$asBloqueRoster=array();
 			if ($piCodigoClub==-1)
				$piCodigoClub=intval($asClubes['DATOS'][0]['id_unico']);
			if ($piCodigoClub!=-1) {
				$asClub=$this->clubes_mod->RegresaDatos($piCodigoClub);
				$sNombreClub= ($asClub['ESTATUS']==1) ? $asClub['DATOS']['nombre_corto'] : 'Error';
				$sLogoClub= ($asClub['ESTATUS']==1) ? $asClub['DATOS']['ruta_logo'] : 'Error';				
				$iPresupuestoDisponible=$this->draft_mod->getPresupuestoClub($iTemporada, $piCodigoClub);
				$iPresupuestoAsignado=$this->draft_mod->getPresupuestoClubDesignado($iTemporada, $piCodigoClub);
				$iPresupuestoGastado=$this->draft_mod->getPresupuestoClubGastado($iTemporada, $piCodigoClub);
				$iPresupuestoPrometido=$this->draft_mod->getPresupuestoClubPrometido($iTemporada, $piCodigoClub);
				$asContratados=$this->draft_mod->getContratados($iTemporada,$piCodigoClub);
				$iContContratados=count($asContratados['DATOS']);
				$iContOfertasActivas=0;
				if ($asContratados['ESTATUS']==1) {
					for($i=0;$i<count($asContratados['DATOS']);$i++) {
						$asBloqueRoster[]=array (
							'CLASE' => ($i%2==0) ? "non" : "par",
							'CODIGO' => $asContratados['DATOS'][$i]['id_jugador'],
							'NOMBRE' => $asContratados['DATOS'][$i]['nombre'],
							'SUELDO_BASE' => $asContratados['DATOS'][$i]['precio_base'],
							'TEMPORADAS_RESTANTES' => $asContratados['DATOS'][$i]['duracion'] +  $asContratados['DATOS'][$i]['temporada_inicio'] - $iTemporada,
							'ESTATUS' => 'Contratado'
						);
					}
				}
				if ($piModo=0)
					$asOfertados=$this->draft_mod->getOfertados($iTemporada,$piCodigoClub,1);
				else
					$asOfertados=$this->draft_mod->getOfertados($iTemporada,$piCodigoClub);
				if ($asOfertados['ESTATUS']==1) {
					for($i=0;$i<count($asOfertados['DATOS']);$i++) {
						/*switch ($asOfertados['DATOS'][$i]['estatus']) {
							case 1: 
								$sClase="notice";
								$sNombre=$asOfertados['DATOS'][$i]['nombre'];
								$sEstatus=$asOfertados['DATOS'][$i]['descripcion']." (".$asOfertados['DATOS'][$i]['tiempo_limite'].")";
								$iContOfertasActivas++;
								break;
							case -1:
								$sClase="error";
								$asJugadorContratado=$this->draft_mod->getInfoContratoJugador($asOfertados['DATOS'][$i]['id_jugador']);
								if ($asJugadorContratado['ESTATUS']==1) {
									$sNombre=$asOfertados['DATOS'][$i]['nombre'];
									$sEstatus="<a href=\"javascript: verHistorial(".$asOfertados['DATOS'][$i]['id_jugador'].");\">Contrato ganado por otro</a>";
								}
								else {
									$sNombre="<a href=\"admin/draft_subastas/captura_oferta/".$asOfertados['DATOS'][$i]['id_jugador']."/".$piCodigoClub."\">".$asOfertados['DATOS'][$i]['nombre']."</a>";
									$sEstatus="<a href=\"javascript: verHistorial(".$asOfertados['DATOS'][$i]['id_jugador'].");\">".$asOfertados['DATOS'][$i]['descripcion']."</a>";
								}
								break;
							case -2:
								$sClase="error";
								$sNombre=$asOfertados['DATOS'][$i]['nombre'];
								$sEstatus=$asOfertados['DATOS'][$i]['descripcion'];
								break;
							case 10:
								$sClase="success";
								$sNombre=$asOfertados['DATOS'][$i]['nombre'];
								$sEstatus=$asOfertados['DATOS'][$i]['descripcion'];
								break;
						}*/
						$asEstatusSubasta=$this->estatus_subasta($piCodigoClub,$asOfertados['DATOS'][$i]['id_jugador']);
						if ($asEstatusSubasta['OFERTAR']==true)
							$sNombre="<a href=\"admin/draft_subastas/captura_oferta/".$asOfertados['DATOS'][$i]['id_jugador']."/".$piCodigoClub."\">".$asOfertados['DATOS'][$i]['nombre']."</a>";
						else
							$sNombre=$asOfertados['DATOS'][$i]['nombre'];
						$sEstatus="<a href=\"javascript: verHistorial(".$asOfertados['DATOS'][$i]['id_jugador'].");\">".$asEstatusSubasta['ESTATUS']." ".$asEstatusSubasta['MENSAJE']."</a>";
						if ($asEstatusSubasta['ESTATUS']=='ACTIVA') {
							if ($asEstatusSubasta['MENSAJE']=='Lider Actual')
								$iContOfertasActivas++;
							$asBloqueRoster[]=array (
								'CLASE' => $asEstatusSubasta['CLASE'],
								'CODIGO' => $asOfertados['DATOS'][$i]['id_jugador'],
								'NOMBRE' => $sNombre,
								'SUELDO_BASE' => $asEstatusSubasta['MEJOR_OFERTA'],
								'TEMPORADAS_RESTANTES' => $asEstatusSubasta['EXPIRACION'],
								'ESTATUS' => $sEstatus
							);
						}
						//Esto es para checar si se repiten las ofertas por los mismos jugadores
						/*$iPosRepetido=-1;
						 *
						 *$asOfertados['DATOS'][$i]['sueldo_base'],$asOfertados['DATOS'][$i]['duracion']
						for ($j=0;$j<count($asBloqueRoster);$j++)
							if ($asOfertados['DATOS'][$i]['id_jugador']==$asBloqueRoster[$j]['CODIGO'])
								$iPosRepetido=$j;
/*						if ($iPosRepetido==-1)
							$asBloqueRoster[]=array (
								'CLASE' => $sClase,
								'CODIGO' => $asOfertados['DATOS'][$i]['id_jugador'],
								'NOMBRE' => $sNombre,
								'SUELDO_BASE' => $asOfertados['DATOS'][$i]['sueldo_base'],
								'TEMPORADAS_RESTANTES' => $asOfertados['DATOS'][$i]['duracion'],
								'ESTATUS' => $sEstatus
							);
						else {
							$asBloqueRoster[$iPosRepetido]=array (
								'CLASE' => $sClase,
								'CODIGO' => $asOfertados['DATOS'][$i]['id_jugador'],
								'NOMBRE' => $sNombre,
								'SUELDO_BASE' => $asOfertados['DATOS'][$i]['sueldo_base'],
								'TEMPORADAS_RESTANTES' => $asOfertados['DATOS'][$i]['duracion'],
								'ESTATUS' => $sEstatus
							);
						}*/
						//if ($iPosRepetido==-1)
						/*else {
							$asBloqueRoster[$iPosRepetido]=array (
								'CLASE' => $asEstatusSubasta['CLASE'],
								'CODIGO' => $asOfertados['DATOS'][$i]['id_jugador'],
								'NOMBRE' => $sNombre,
								'SUELDO_BASE' => $asOfertados['DATOS'][$i]['sueldo_base'],
								'TEMPORADAS_RESTANTES' => $asOfertados['DATOS'][$i]['duracion'],
								'ESTATUS' => $sEstatus
							);
						}*/
					}
				}
			}
			else {
				$sNombreClub='Elija primero';
				$iPresupuestoDisponible='';
				$iPresupuestoAsignado='';
				$iPresupuestoGastado='';
				$iPresupuestoPrometido='';
				$sLogoClub="";
				$iContContratados="";
				$iContOfertasActivas="";
			}
			//Si hay condiciones previas, las coloca
			if ($psQuery!="ALL") {
				$asQuery=explode("~",$psQuery); //Antes %7C
				$asCondicionesArray=array();
				for ($i=0;$i<count($asQuery)-1;$i++) {
					$asElementos=explode("__",$asQuery[$i]);
					$asCondiciones[]=$asElementos;
					$asCondicionesArray['BLOQUE_ELEMENTOS'][]=array (
									'CAMPO' => $asElementos[0],
									'OPERADOR' => $asElementos[1],
									'VALOR' => $asElementos[2],
									'CLASE' => ($i%2==0) ? "non" : "par",
									'CONTADOR' => $i
					);
				}
				$sArrayCondiciones=$this->parser->parse('draft/array_busqueda_principal_vw',$asCondicionesArray, true);
				$sTablaCondiciones=$this->parser->parse('draft/tabla_busqueda_principal_vw',$asCondicionesArray, true);
			}
			else {
				$sArrayCondiciones="new Array()";
				$sTablaCondiciones="";
			}
			$asCampos=$this->draft_mod->getCamposHabilidades(1);
			$asPrincipal=array(
				'RUTA_RAIZ' => base_url(), 'INDEX_URI' => $this->config->item('index_uri'),
				'COMBO_CLUBES' => $this->tools_lib->GeneraCombo(array (
					'NOMBRE' => "slcClub",
					'DATASET' => $asClubes, 
					'EVENTOS' => 'onchange="JavaScript: CambiaClubes(this.value);"',
					'ID' => 'combo-clubes',
					'DEFAULT' => $piCodigoClub
				)),
				'COMBO_CAMPOS' => $this->tools_lib->GeneraCombo(array (
					'NOMBRE' => "slcCampo",
					'ID' => 'select-campos',
					'DATASET' => $asCampos
				)),
				'TEMPORADA' => $this->config->item('temporada_actual'),
				'BLOQUE_ROSTER' => $asBloqueRoster,
				'PRESUPUESTO_DISPONIBLE' => $iPresupuestoDisponible,
				'NOMBRE_CLUB' => $sNombreClub,
				'LOGO_CLUB' => $sLogoClub,
				'PRESUPUESTO_ASIGNADO' => $iPresupuestoAsignado,
				'PRESUPUESTO_LIBRE' => ($piCodigoClub!=-1) ? $iPresupuestoDisponible - $iPresupuestoPrometido : "",
				'PRESUPUESTO_GASTADO' => $iPresupuestoGastado,
				'PRESUPUESTO_PROMETIDO' => $iPresupuestoPrometido,
				'CONTADOR_CONTRATADOS' => $iContContratados,
				'CONTADOR_OFERTAS_ACTIVAS' => $iContOfertasActivas,
				'ARRAY_CONDICIONES' => $sArrayCondiciones,
				'TABLA_CONDICIONES' => $sTablaCondiciones,
				'TABLA_RESULTADOS' => ($piCodigoClub!=-1) ? $this->xi_busqueda($piCodigoClub,$psQuery,$psScope, $piPagina, true) : "" ,
				'ID_CLUB' => $piCodigoClub,
				'LIBRES_CHECADO' => ($psScope=="L") ? "checked=\"checked\"" : "" ,
				'TODOS_CHECADO' => ($psScope=="T") ? "checked=\"checked\"" : "" ,
			);	
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/principal_subastas_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}

		function lista_club($piCodigo) {
			$iMaxFranquicias=3;
			$iMaxBases=5;
			$this->load->model(array('consejos_mod', 'clubes_mod'));
			$iTemporadaActual=$this->config->item('temporada_actual');
			$asJugadores=$this->clubes_mod->roster($piCodigo,$iTemporadaActual-1);
			$asPresupuestoClub=$this->draft_mod->getPresupuestoClub($iTemporadaActual, $piCodigo);
			$asBloqueJugadores=array();
			$sListaCodigos="";
			$iFranquiciasSeleccionados=0;
			$iBasesSeleccionados=0;
			$iTotalTemporada=0;
			for ($i=0;$i<count($asJugadores['DATOS']);$i++) {
				$asCosto=$this->draft_mod->getContratoJugador($iTemporadaActual, $asJugadores['DATOS'][$i]['id_unico']);
				if ($asCosto['ESTATUS']==1) {
					$iDuracion=$asCosto['DATOS']['duracion'];
					$sEstatusLibre="";
					if ($asCosto['DATOS']['tipo']=="FR") {
						$sEstatusFranquicia="checked=\"checked\"";
						$sEstatusBase="";
						$iFranquiciasSeleccionados++;
						$fCosto=$asPresupuestoClub['DATOS']['cantidad']*0.15;
						$sTipoContrato="F";
					}
					else {
						$sEstatusFranquicia="";
						$sEstatusBase="checked=\"checked\"";
						$iBasesSeleccionados++;
						$fCosto=$asCosto['DATOS']['precio_base'];
						$sTipoContrato="B";
					}
				}
				else {
					$sEstatusFranquicia="";
					$sEstatusBase="";
					$sEstatusLibre="checked=\"checked\"";
					$fCosto=0;
					$iDuracion=1;
					$sTipoContrato="L";
				}
				$iTotalTemporada+=$fCosto;
				$asBloqueJugadores[]=array (
					'CLASE' => ($i%2==0) ? 'non' : 'par',
					'CODIGO' => $asJugadores['DATOS'][$i]['id_unico'],
					'NOMBRE' => $asJugadores['DATOS'][$i]['nombre'],
					'PRECIO_BASE' => $asJugadores['DATOS'][$i]['precio_base'], 
					'ESTATUS_LIBRE' => $sEstatusLibre,
					'ESTATUS_BASE' => $sEstatusBase,
					'ESTATUS_FRANQUICIA' =>  $sEstatusFranquicia,
					'TEMPORADAS' => $iDuracion,
					'TIPO_CONTRATO' => $sTipoContrato,
					'COSTO' => $fCosto 
				);
				$sListaCodigos.=($sListaCodigos=="") ? $asJugadores['DATOS'][$i]['id_unico'] : "|".$asJugadores['DATOS'][$i]['id_unico'];
			}
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'PRESUPUESTO' =>    $asPresupuestoClub['DATOS']['cantidad'],
				'COSTO_FRANQUICIA' =>   $asPresupuestoClub['DATOS']['cantidad']*0.15 ,
				'FRANQUICIAS_SELECCIONADOS' =>   $iFranquiciasSeleccionados,
				'BASES_SELECCIONADOS' => $iBasesSeleccionados,
				'BLOQUE_JUGADORES' =>  $asBloqueJugadores,
				'LISTA_JUGADORES' => $sListaCodigos,
				'ID_CLUB' => $piCodigo,
				'TEMPORADA' => $this->config->item('temporada_actual'),
				'TOTAL_TEMPORADA' => $iTotalTemporada
			);
			echo ($this->parser->parse('draft/lista_franquicias_vw', $asPrincipal, true));
		}
		
		function guardaCambios($piTemporada, $piClub, $psCadena) {
			$asJugadores=explode("_",$psCadena);
			$sError="";
			for ($i=0;$i<count($asJugadores)-1;$i++) {
				$asValores=explode("-", $asJugadores[$i]);
				$asExiste=$this->draft_mod->getContrato($piClub, $asValores[0]);
				if ($asExiste['ESTATUS']==1) {
					$asBorrado=$this->draft_mod->borraContrato($asExiste['DATOS']['id_unico']);
					if ($asBorrado['ESTATUS']!=1)
					$sError.=$asBorrado['MENSAJE']."<br/>\n";
				}
				if (!($asValores[1]==='L')) { //Se pone negada para evitar broncas con la comparacion de cadenas
					$asInserta=$this->draft_mod->insertaContrato($piTemporada, $piClub, $asValores);
					if ($asInserta['ESTATUS']!=1)
					$sError.=$asInserta['MENSAJE']."<br/>\n";
				}
			}
			if ($sError=="")
				$sSalida="<div class=\"success\">Los contratos se han guardado con exito</div>";
			else
				$sSalida="<div class=\"error\">".$sError."</div>";
			echo $sSalida;
		}

		
		function xi_habilidades ($piJugador, $piJugador2=0, $piJugador3=0) {
			/* pChart library inclusions */ 
			include("externos/pChart2.1.4/class/pData.class.php"); 
			include("externos/pChart2.1.4/class/pDraw.class.php"); 
			include("externos/pChart2.1.4/class/pRadar.class.php"); 
			include("externos/pChart2.1.4/class/pImage.class.php"); 			
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
				$myPicture = new pImage(570,230,$MyData);
				/* Draw a solid background */
				$Settings = array("R"=>179, "G"=>217, "B"=>91, "Dash"=>1, "DashR"=>199, "DashG"=>237, "DashB"=>111);
				$myPicture->drawFilledRectangle(0,0,570,230,$Settings);
			   
				/* Overlay some gradient areas */
				$Settings = array("StartR"=>194, "StartG"=>231, "StartB"=>44, "EndR"=>43, "EndG"=>107, "EndB"=>58, "Alpha"=>50);
				$myPicture->drawGradientArea(0,0,570,230,DIRECTION_VERTICAL,$Settings);
				$myPicture->drawGradientArea(0,0,570,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100));
			   
				/* Add a border to the picture */
				$myPicture->drawRectangle(0,0,569,229,array("R"=>0,"G"=>0,"B"=>0));

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
	
		function xi_busqueda ($piClub, $psQuery="", $psScope, $piPagina=1, $pbRetornaValor=false) {
			date_default_timezone_set('America/Mexico_City');
			$this->load->model(array('jugadores_mod', 'clubes_mod'));
			$this->load->library('tools_lib');
			$asCondiciones=array();
			if ($psQuery=="ALL") $psQuery="nombre__PARECIDO_A__~"; //parche por si no se pone nada
			$asQuery=explode("~",$psQuery); //Antes %7C
			for ($i=0;$i<count($asQuery)-1;$i++) {
				$asElementos=explode("__",$asQuery[$i]);
				$asCondiciones[]=$asElementos;
			}
			//var_dump($asCondiciones);
			$iDivisionClubInteresado=$this->clubes_mod->getDivision($piClub);
			$fMinimoPorDivision=($iDivisionClubInteresado==1) ? $this->config->item('salario_minimo_1ra') : $this->config->item('salario_minimo_2da');
			$asResultados=$this->draft_mod->getJugadoresFiltro(1, $asCondiciones, $psScope, $piPagina);
			for ($i=0;$i<count($asResultados['DATOS']);$i++) {
				$asResultados['DATOS'][$i]['TX Club Anterior']=$this->draft_mod->getUltimoClub($asResultados['DATOS'][$i]['IN clave']);
				$asOferta=$this->draft_mod->getOfertas($asResultados['DATOS'][$i]['IN clave'], 1);
				if ($asOferta['ESTATUS']==1) {
					$dFechaOferta=strtotime($asOferta['DATOS'][0]['fecha_oferta']);
					$dFinOferta=$dFechaOferta + ($asOferta['DATOS'][0]['duracion_oferta']*60);
					$sFinFechaOferta=date("Y-m-d H:i",$dFinOferta);
					$iSueldoIncrementado=$asOferta['DATOS'][0]['sueldo_base']*$this->config->item('incremento');
					if ($dFinOferta>strtotime(date("Y-m-d H:i"))) 
						if ($asOferta['DATOS'][0]['id_club']!=$piClub)
							$sBotonOfertar="<input type=\"button\" class=\"button\" value=\"ofertar\" onclick=\"JavaScript: Ofertar(".$asResultados['DATOS'][$i]['IN clave'].",'".$psQuery."',".$piPagina.");\" />";
						else
							$sBotonOfertar="Mayor oferta propia";
					else
						$sBotonOfertar="Subasta cerrada ".date("Y-m-d H:i")." cierre el ".$sFinFechaOferta;
					$asResultados['DATOS'][$i]['TX Mayor oferta/Temporadas']="<a href=\"JavaScript: verHistorial(".$asResultados['DATOS'][$i]['IN clave'].");\">".$asOferta['DATOS'][0]['sueldo_base']." / ".$asOferta['DATOS'][0]['duracion']."</a>";
					$asResultados['DATOS'][$i]['TX Minimo para ofertar']=($iSueldoIncrementado > $fMinimoPorDivision) ? $iSueldoIncrementado : $fMinimoPorDivision;
					$asResultados['DATOS'][$i]['TX Vencimiento']=$sFinFechaOferta;
					$asResultados['DATOS'][$i]['TX Ofertar']=$sBotonOfertar;
				}
				else {
					$asResultados['DATOS'][$i]['TX Mayor oferta/Temporadas']="--";
					$asResultados['DATOS'][$i]['TX Minimo para ofertar']="Salario minimo";
					$asResultados['DATOS'][$i]['TX Vencimiento']="--";
					$asResultados['DATOS'][$i]['TX Ofertar']="<input type=\"button\" class=\"button\" value=\"ofertar\" onclick=\"JavaScript: Ofertar(".$asResultados['DATOS'][$i]['IN clave'].",'".$psQuery."',".$piPagina.");\" />";
				}
				//Si ya fue contratado el jugador, le encima el valor de la columna final
				$asJugadorContratado=$this->draft_mod->verificaJugadorContratado($asResultados['DATOS'][$i]['IN clave']);
				if ($asJugadorContratado['ESTATUS']==1) {
					$sDuracion=($asJugadorContratado['DATOS']['tipo']=="FR") ? "Franquicia" : $asJugadorContratado['DATOS']['duracion'];
					$asResultados['DATOS'][$i]['TX Mayor oferta/Temporadas']=$asJugadorContratado['DATOS']['precio_base']."/".$sDuracion." (".$asJugadorContratado['DATOS']['club'].")";
					$asResultados['DATOS'][$i]['TX Minimo para ofertar']="--";
					$asResultados['DATOS'][$i]['TX Vencimiento']="--";
					$asResultados['DATOS'][$i]['TX Ofertar']="<a href=\"javascript: verHistorial(".$asResultados['DATOS'][$i]['IN clave'].");\">Contratado</a>";
				}
			}
			$iTotalRegistros=(isset($asResultados['TOTAL'])) ? $asResultados['TOTAL'] : 0;
			$iTotalPaginas=ceil($iTotalRegistros/10);
			$aiOpcionesCombo=array();
			for ($i=1;$i<=$iTotalPaginas;$i++)
				$aiOpcionesCombo[]=array (
					'VALOR' => $i,
					'SELECTED' => ($i==$piPagina) ? "selected=\"selected\"" : ""
				);
			$asSalida=array(
					'TABLA_RESULTADOS' => $this->tools_lib->genera_reporte( array(
											'DATOS' => $asResultados['DATOS'],
											'TITULO' => $iTotalRegistros." Resultados"
										)),
					'PAGINA' => $piPagina,
					'TOTAL_PAGINAS' => $iTotalPaginas,
					'CLUB' => $piClub,
					'QUERY' => $psQuery,
					'SCOPE' => $psScope,
					'BLOQUE_OPCIONES' => $aiOpcionesCombo,
					'BOTON_ANTERIOR' => ($piPagina>1) ? "<input type=\"button\" class=\"button\" value=\"&lt;\" onClick=\"gotoPagina(".($piPagina-1).");\"/>" : "",
					'BOTON_SIGUIENTE' => ($piPagina<$iTotalPaginas) ? "<input type=\"button\" class=\"button\" value=\"&gt;\" onClick=\"gotoPagina(".($piPagina+1).");\"/>" : "",
					'BOTON_PRIMERO' => ($piPagina>1) ? "<input type=\"button\" class=\"button\" value=\"&lt;&lt;\" onClick=\"gotoPagina(1);\"/>" : "",
					'BOTON_ULTIMO' => ($piPagina<$iTotalPaginas) ? "<input type=\"button\" class=\"button\" value=\"&gt;&gt;\" onClick=\"gotoPagina(".$iTotalPaginas.");\"/>" : "",
			);
//					'BOTON_ANTERIOR' => ($piPagina>1) ? "<input type=\"button\" class=\"button\" value=\"Pagina anterior\" onClick=\"callPage('admin/draft_subastas/xi_busqueda/".$piClub."/".$psQuery."/".$psScope."/".($piPagina-1)."','tabla-resultados','Cargando...','Error@');\"/>" : "",
//					'BOTON_SIGUIENTE' => ($piPagina<$iTotalPaginas) ? "<input type=\"button\" class=\"button\" value=\"Siguiente Pagina\" onClick=\"callPage('admin/draft_subastas/xi_busqueda/".$piClub."/".$psQuery."/".$psScope."/".($piPagina+1)."','tabla-resultados','Cargando...','Error@');\"/>" : ""

			if ($pbRetornaValor==true)
				return ($this->parser->parse('draft/xi_busqueda_subastas_vw', $asSalida, true));
			else
				$this->parser->parse('draft/xi_busqueda_subastas_vw', $asSalida);
		}
		
		function xi_historial_ofertas($piJugador) {
			$this->load->model('jugadores_mod');
			$asHistorial=$this->draft_mod->getOfertas($piJugador, 0);
			$sSalida="<h3>Historial de ofertas del jugador ".$this->jugadores_mod->NombreJugador($piJugador)."</h3>"
			."<table class=\"Reportes\"><thead><th>Club</th><th>Oferta</th><th>Temporadas</ht></thead>";
			if ($asHistorial['ESTATUS']==1) {
				for ($i=0;$i<count($asHistorial['DATOS']);$i++) {
					$sClase=($i%2==0) ? "par" : "non";
					$sSalida.="<tr class=\"".$sClase."\">"
						."<td>".$asHistorial['DATOS'][$i]['club']."</td>"
						."<td>".$asHistorial['DATOS'][$i]['sueldo_base']."</td>"
						."<td>".$asHistorial['DATOS'][$i]['duracion']."</td>"
						."</tr>";
				}
				$sSalida.="</table>";
			}
			else
				$sSalida="<div class=\"error\">No se encontraron ofertas</div>";
			echo $sSalida;
		}
		
		function captura_oferta($piJugador, $piClub, $psQuery="ALL", $psScope="L",  $piPagina=1) {
			date_default_timezone_set('America/Mexico_City');
			$this->load->model(array ('jugadores_mod','clubes_mod'));
			$this->load->library('tools_lib');
			$sUltimoClub=$this->draft_mod->getUltimoClub($piJugador);
			$asBloqueHistorialOfertas=array();
			$iDivisionClubInteresado=$this->clubes_mod->getDivision($piClub);
			$fMinimoPorDivision=($iDivisionClubInteresado==1) ? $this->config->item('salario_minimo_1ra') : $this->config->item('salario_minimo_2da');
			$asDatosClub=$this->clubes_mod->RegresaDatos($piClub);
			$asOferta=$this->draft_mod->getOfertas($piJugador, 1);
			if ($asOferta['ESTATUS']==1) {
				$dFechaOferta=strtotime($asOferta['DATOS'][0]['fecha_oferta']);
				$iDuracion=$asOferta['DATOS'][0]['duracion_oferta'];
				$dFinOferta=$dFechaOferta + ($iDuracion*60);
				$sFechaFinOferta=date("Y-m-d H:i",$dFinOferta);
				if ($dFinOferta>strtotime(date(DATE_RFC2822))) 
					$iOferta=$asOferta['DATOS'][0]['sueldo_base']*$this->config->item('incremento');
				else{
					$this->main_lib->mensaje("La subasta para este jugador ya se cerro");
					echo "la subasta ya se cerro para este jugador";
					exit();
				}
			}
			else 
				$iOferta=$fMinimoPorDivision;
			$cyOfertaMinima=($iOferta > $fMinimoPorDivision) ? $iOferta : $fMinimoPorDivision;
			$cyPresupuestoClub=$this->draft_mod->getPresupuestoClub($this->config->item('temporada_actual'), $piClub);
			if ($cyPresupuestoClub>=$cyOfertaMinima) {
				$asHistorial=$this->draft_mod->getOfertas($piJugador, 0); //Trae todas las ofertas
				if ($asHistorial['ESTATUS']==1) {
					for ($i=0;$i<count($asHistorial['DATOS']);$i++) {
						$asBloqueHistorialOfertas[]=array (
							'CLASE' => ($i%2==0) ? 'par' : 'non',
							'CLUB' => $asHistorial['DATOS'][$i]['club'],
							'PRECIO' =>	$asHistorial['DATOS'][$i]['sueldo_base'],
							'TEMPORADAS' => $asHistorial['DATOS'][$i]['duracion'],
							'FECHA' => $asHistorial['DATOS'][$i]['fecha_oferta']
						);	
					}
				}
				else {
					$asBloqueHistorialOfertas[]=array (
						'CLASE' => 'notice',
						'CLUB' => 'Esta ser&iacute;a la primera oferta',
						'PRECIO' =>	'',
						'TEMPORADAS' => '',
						'FECHA' => ''
					);	
				}
				$asPrincipal=array(
					'RUTA_RAIZ' => base_url(),
					'INDEX_URI' => $this->config->item('index_uri'),
					'NOMBRE_JUGADOR' => $this->jugadores_mod->NombreJugador($piJugador),
					'ID_TEMPORADA' => $this->config->item('temporada_actual'),
					'OFERTA_MINIMA' => $cyOfertaMinima,
					'ID_JUGADOR' => $piJugador,
					'QUERY_ANTERIOR' => $psQuery,
					'PAGINA_QUERY' => $piPagina,
					'SCOPE' => $psScope,
					'ID_CLUB' => $piClub,
					'BLOQUE_OFERTAS' => $asBloqueHistorialOfertas,
					'NOMBRE_CLUB' => $asDatosClub['DATOS']['nombre_corto']
				);	
				$asContenido=array (
					'PRINCIPAL' => $this->parser->parse('draft/captura_oferta_subastas_vw', $asPrincipal, true)
				);
				$asControlador= array (
					'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
					'TIPO_ACCESO' => 'ADMIN' );
				$this->main_lib->display($asControlador);
			}
			else {
				$this->main_lib->mensaje("El presupuesto del club no alcanza pa pagar");
			}
		}

		function procesa_oferta() {
			$iMinima=$this->input->post('hdnMinima');
			if ($iMinima<=$this->input->post('txtOferta')) {
				$cyPresupuestoClub=$this->draft_mod->getPresupuestoClub($this->config->item('temporada_actual'), $this->input->post('hdnClub'));
				if ($cyPresupuestoClub>=$this->input->post('txtOferta')) {
					$asArgs=array (
						'temporada' => $this->config->item('temporada_actual'),
						'club' => $this->input->post('hdnClub'),
						'jugador' => $this->input->post('hdnJugador'),
						'oferta' => $this->input->post('txtOferta'),
						'duracion' => $this->input->post('txtDuracion')
					);
					$asResult=$this->draft_mod->insertaOferta($asArgs);
					if ($asResult['ESTATUS']==1) {
						$sMensaje="Su oferta ha sido colocada";	
						$sClase="success";
					}
					else {
						$sMensaje=$asResult['MENSAJE'];
						$sClase="error";
					}
				}
				else {
					$sMensaje="El presupuesto del club no le alcanza para pagar";
					$sClase="error";
				}
			}
			else {
				$sMensaje="Su oferta no pasa del minimo";
				$sClase="error";
			}
			$asPrincipal=array(
				'ESTILO' => $sClase, 
				'MENSAJE' => $sMensaje,
				'CLUB' => $this->input->post('hdnClub'),
				'QUERY_ANTERIOR' => $this->input->post('hdnQueryAnterior'),
				'PAGINA_QUERY' =>$this->input->post('hdnPaginaQuery'),
				'SCOPE' =>$this->input->post('hdnScope')
			);
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/procesa_oferta_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}
		
		function procesa_ganadores() {
			$this->load->model(array ('financieros_mod', 'bitacora_draft_mod', 'clubes_mod'));
			$iTemporada=$this->config->item('temporada_actual');
			$asGanadoras=$this->draft_mod->getOfertasGanadoras();
			//Hace un barrido de todas las ofertas ganadoras
			for ($i=0;$i<count($asGanadoras['DATOS']);$i++) {
				$cyPresupuestoClub=$this->draft_mod->getPresupuestoClub($this->config->item('temporada_actual'), $asGanadoras['DATOS'][$i]['id_club']);
				$asClub=$this->clubes_mod->RegresaDatos($asGanadoras['DATOS'][$i]['id_club']);
				if ($cyPresupuestoClub>=$asGanadoras['DATOS'][$i]['sueldo_base']) {
					$asROfertaGanadora=$this->draft_mod->setOfertaGanadora($asGanadoras['DATOS'][$i]['id_unico']);
					if ($asROfertaGanadora['ESTATUS']==1)
						$this->bitacora_draft_mod->inserta(array (
										'operador' => 	'root',
										'temporada' => $iTemporada,
										'tipo' => 'subasta',
										'observaciones' => "Marcando la oferta ".$asGanadoras['DATOS'][$i]['id_unico']." como ganadora"
															));
					else
						$this->bitacora_draft_mod->inserta(array (
								'operador' => 	'root',
								'tipo' => 'subasta',
								'temporada' => $iTemporada,
								'observaciones' => $asROfertaGanadora['MENSAJE']
															));

					$asROperacion=$this->financieros_mod->insertaOperacion(                              
							$asGanadoras['DATOS'][$i]['id_temporada'],
							$asGanadoras['DATOS'][$i]['sueldo_base'],
							3, $asGanadoras['DATOS'][$i]['id_club'],
							4, $asGanadoras['DATOS'][$i]['id_jugador']);
					
					if ($asROperacion['ESTATUS']==1)
						$this->bitacora_draft_mod->inserta(array (
										'operador' => 	'root',
										'tipo' => 'financiera',
										'id_club' => $asGanadoras['DATOS'][$i]['id_club'],
										'id_jugador' => $asGanadoras['DATOS'][$i]['id_jugador'],
										'id_consejo' => $asClub['DATOS']['id_consejo'],
										'temporada' => $iTemporada,
										'observaciones' => "Operacion financiera club ".$asGanadoras['DATOS'][$i]['nombre_club']." contrata jugador ".$asGanadoras['DATOS'][$i]['nombre_jugador']
										));
					else
						$this->bitacora_draft_mod->inserta(array (
										'operador' => 	'root',
										'tipo' => 'financiera',
										'id_club' => $asGanadoras['DATOS'][$i]['id_club'],
										'id_jugador' => $asGanadoras['DATOS'][$i]['id_jugador'],
										'id_consejo' => $asClub['DATOS']['id_consejo'],
										'temporada' => $iTemporada,
										'observaciones' => $asROperacion['MENSAJE']
										));
					$asRContrato=$this->financieros_mod->insertaContrato(
						$asGanadoras['DATOS'][$i]['id_temporada'],
						$asGanadoras['DATOS'][$i]['id_club'],
						array (0 =>  $asGanadoras['DATOS'][$i]['id_jugador'],
							1 => 'S',
							2 => $asGanadoras['DATOS'][$i]['duracion'],	
							3 => $asGanadoras['DATOS'][$i]['sueldo_base']	
						)
					);
					if ($asRContrato['ESTATUS']==1)
						$this->bitacora_draft_mod->inserta(array (
								'operador' => 	'root',
								'tipo' => 'contrato',
								'temporada' => $iTemporada,
								'id_club' => $asGanadoras['DATOS'][$i]['id_club'],
								'id_jugador' => $asGanadoras['DATOS'][$i]['id_jugador'],
								'id_consejo' => $asClub['DATOS']['id_consejo'],
								'observaciones' => "Contrato  club ".$asGanadoras['DATOS'][$i]['nombre_club']."- jugador ".$asGanadoras['DATOS'][$i]['nombre_jugador']." realizado"
							));
					else
						$this->bitacora_draft_mod->inserta(array (
							'operador' => 	'root',
							'tipo' => 'contrato',
							'id_club' => $asGanadoras['DATOS'][$i]['id_club'],
							'id_jugador' => $asGanadoras['DATOS'][$i]['id_jugador'],
							'id_consejo' => $asClub['DATOS']['id_consejo'],
							'temporada' => $iTemporada,
							'observaciones' => $asRContrato['MENSAJE']
							));
				}
				else {
					$this->draft_mod->setEstatusOferta($asGanadoras['DATOS'][$i]['id_unico'], -2);
					$this->bitacora_draft_mod->inserta(array (
							'operador' => 	'root',
							'tipo' => 'cancelacion',
							'temporada' => $iTemporada,
							'id_club' => $asGanadoras['DATOS'][$i]['id_club'],
							'id_consejo' => $asClub['DATOS']['id_consejo'],
							'id_jugador' => $asGanadoras['DATOS'][$i]['id_jugador'],
							'observaciones' => "Oferta cancelada por falta de fondos del club ".$asGanadoras['DATOS'][$i]['nombre_club']."- jugador ".$asGanadoras['DATOS'][$i]['nombre_jugador']
							));

				}
			}
		}
		function estatus_subasta($piClub, $piJugador) {
			$asHistorial=$this->draft_mod->getHistorialOfertas($piJugador);
			if ($asHistorial['ESTATUS']==1) {
				$iClubLider="";
				$sNombreClubLider="";
				$iCantidad="";
				$sFechaLimite="";
				$sEstatus="";
				for ($i=0;$i<count($asHistorial['DATOS']);$i++) {
					if (($asHistorial['DATOS'][$i]['estatus']==1)||($asHistorial['DATOS'][$i]['estatus']==10)) {
						if ($asHistorial['DATOS'][$i]['estatus']==1) {
							$sEstatus="ACTIVA";
							$sClase="notice";
						}
						else {
							$sEstatus="CERRADA";
							$sClase="success";
						}
						$iClubLider=$asHistorial['DATOS'][$i]['id_club'];
						$sNombreClubLider=$asHistorial['DATOS'][$i]['club'];
						$iCantidad=$asHistorial['DATOS'][$i]['sueldo_base'];
						$sFechaLimite=$asHistorial['DATOS'][$i]['tiempo_limite'];
					}
				}
				if ($piClub!=$iClubLider) {
					$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
					$iConsejo=$asConsejo['DATOS']['id_unico'];
					$asClubes=$this->draft_mod->getClubesAdministrados();
					$sMensaje="Lider :".$sNombreClubLider;
					$sClase="error";
					for ($i=0;$i<count($asClubes['DATOS']);$i++) {
						if ($asClubes['DATOS'][$i]['id_unico']==$iClubLider) {
							$sMensaje="Lider :".$sNombreClubLider;
							$sClase="azul";
						}
					}
					$bOfertar=true;
				}
				else {
					$sMensaje="Lider Actual";
					$sClase="notice";
					$sNombreClubLider="";
					$bOfertar=false;
				}
			}
			else {
				$sMensaje="";
				$sClase="";
				$sNombreClubLider="";
				$sEstatus="error";
				$bOfertar=true;
				$iCantidad=0;
				$sFechaLimite="";
			}
			return (array (
				'MENSAJE' => $sMensaje,
				'CLASE' => $sClase,
				'CLUB_LIDER' => $sNombreClubLider,
				'ESTATUS' => $sEstatus,
				'OFERTAR' => $bOfertar,
				'MEJOR_OFERTA' => $iCantidad,
				'EXPIRACION' => $sFechaLimite
				   )
			);
		}
	}
?>
