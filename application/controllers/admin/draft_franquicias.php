<?php
	
	class draft_franquicias extends CI_Controller {
	
		function __construct() {
			parent::__construct();
			$this->load->model('draft_mod');
		}
	
		function index() {
			$this->load->model('consejos_mod');
			$iTemporada=$this->config->item('temporada_actual');
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			$iConsejo=$asConsejo['DATOS']['id_unico'];
			$asClubes=$this->draft_mod->getClubesAdministrados();
			$asPrincipal=array(
				'RUTA_RAIZ' => base_url(), 'INDEX_URI' => $this->config->item('index_uri'),
				'COMBO_CLUBES' => $this->tools_lib->GeneraCombo(array (
					'NOMBRE' => "slcClub",
					'DATASET' => $asClubes, 
					'OPCION_EXTRA' => 'Escoja un club',
					'EVENTOS' => 'onchange="JavaScript: CambiaClubes(this.value);"'
				)),
				'TEMPORADA' => $this->config->item('temporada_actual')
			);	
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/designacion_franquicias_vw', $asPrincipal, true)
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
			$fPresupuestoClubAsignado=$this->draft_mod->getPresupuestoClubDesignado($iTemporadaActual, $piCodigo);
			$fPresupuestoClubGastado=$this->draft_mod->getPresupuestoClubGastado($iTemporadaActual, $piCodigo);
			$fPresupuestoClubDisponible=$this->draft_mod->getPresupuestoClub($iTemporadaActual, $piCodigo);
			$asBloqueJugadoresLibres=array();
			$asBloqueJugadoresContratados=array();
			$asBloqueJugadoresFuera=array();
			$sListaCodigos="";
			$iFranquiciasSeleccionados=0;
			$iBasesSeleccionados=0;
			$iTotalTemporada=0;
			$iContLibres=0;
			$iContContratados=0;
			for ($i=0;$i<count($asJugadores['DATOS']);$i++) {
				$asCosto=$this->draft_mod->getContratoJugador($iTemporadaActual, $asJugadores['DATOS'][$i]['id_unico']);
				if ($asCosto['ESTATUS']==1) {
					if ($asCosto['DATOS']['id_club']==$piCodigo) {
						$asBloqueJugadoresContratados[]=array (
							'CLASE' => ($iContContratados%2==0) ? 'non' : 'par',
							'CODIGO' => $asJugadores['DATOS'][$i]['id_unico'],
							'NOMBRE' => $asJugadores['DATOS'][$i]['nombre'],
							'TIPO' => $asCosto['DATOS']['tipo'],
							'PRECIO' => $asCosto['DATOS']['precio_base'], 
							'TEMPORADAS' => ($asCosto['DATOS']['tipo']=="FR") ? "FRANQUICIA" : $asCosto['DATOS']['duracion'],
							'CONTRATO' => $asCosto['DATOS']['id_unico']
						);
						$sEstatusLibre="";
						if ($asCosto['DATOS']['tipo']=="FR") {
							$iFranquiciasSeleccionados++;
						}
						else {
							$iBasesSeleccionados++;
						}
						$iContContratados++;
					}
					else {
						$asBloqueJugadoresFuera[]=array (
							'CLASE' => ($iContContratados%2==0) ? 'non' : 'par',
							'CODIGO' => $asJugadores['DATOS'][$i]['id_unico'],
							'NOMBRE' => $asJugadores['DATOS'][$i]['nombre'],
							'TIPO' => $asCosto['DATOS']['tipo'],
							'PRECIO' => $asCosto['DATOS']['precio_base'], 
							'TEMPORADAS' =>  $asCosto['DATOS']['duracion'],
							'CLUB' => $asCosto['DATOS']['nombre_club']
						);
					}
				}
				else {
					$asBloqueJugadoresLibres[]=array (
						'CLASE' => ($iContLibres%2==0) ? 'non' : 'par',
						'CODIGO' => $asJugadores['DATOS'][$i]['id_unico'],
						'NOMBRE' => $asJugadores['DATOS'][$i]['nombre'],
						'PRECIO_BASE' => $asJugadores['DATOS'][$i]['precio_base'], 
						'ESTATUS_LIBRE' => "checked=\"checked\"",
						'ESTATUS_BASE' => "",
						'ESTATUS_FRANQUICIA' =>  "",
						'TEMPORADAS' => 1,
						'TIPO_CONTRATO' => "L",
						'COSTO' => 0 
					);
					$sListaCodigos.=($sListaCodigos=="") ? $asJugadores['DATOS'][$i]['id_unico'] : "|".$asJugadores['DATOS'][$i]['id_unico'];
					$iContLibres++;
				}
			}
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'PRESUPUESTO_ASIGNADO' =>    $fPresupuestoClubAsignado,
				'PRESUPUESTO_GASTADO' =>    $fPresupuestoClubGastado,
				'PRESUPUESTO_DISPONIBLE' =>    $fPresupuestoClubDisponible,
				'COSTO_FRANQUICIA' =>   $fPresupuestoClubAsignado*0.15 ,
				'FRANQUICIAS_SELECCIONADOS' =>   $iFranquiciasSeleccionados,
				'BASES_SELECCIONADOS' => $iBasesSeleccionados,
				'BLOQUE_JUGADORES_CONTRATADOS' =>  $asBloqueJugadoresContratados,
				'BLOQUE_JUGADORES_LIBRES' =>  $asBloqueJugadoresLibres,
				'BLOQUE_JUGADORES_FUERA' =>  $asBloqueJugadoresFuera,
				'LISTA_JUGADORES' => $sListaCodigos,
				'ID_CLUB' => $piCodigo,
				'TEMPORADA' => $this->config->item('temporada_actual'),
				'TOTAL_TEMPORADA' => $iTotalTemporada
			);
			echo ($this->parser->parse('draft/lista_franquicias_vw', $asPrincipal, true));
		}
		
		function guardaCambios($piTemporada, $piClub, $psCadena) {
			$this->load->model(array('financieros_mod','bitacora_draft_mod', 'clubes_mod'));
			$asClub=$this->clubes_mod->RegresaDatos($piClub);
			$asJugadores=explode("_",$psCadena);
			$sError="";
			for ($i=0;$i<count($asJugadores)-1;$i++) {
				$asValores=explode("-", $asJugadores[$i]);
				if (!($asValores[1]==='L')) { //Se pone negada para evitar broncas con la comparacion de cadenas
					$asInserta=$this->draft_mod->insertaContrato($piTemporada, $piClub, $asValores);
					switch ($asValores[1]) {
						case 'B':
							$sTipo='Base';
							$sObservaciones="Contrato realizado precio base: ".$asValores[3]." KCacaos por ".$asValores[2]." temporadas";
							break;
						case 'F':
							$sTipo='Franquicia';
							$sObservaciones="Contrato realizado precio: ".$asValores[3]." KCacaos la temporada actual";
							break;
					}
					$this->bitacora_draft_mod->inserta(array (
							'operador' => 	'root',
							'tipo' => 'Contrato realizado '.$sTipo,
							'temporada' => $piTemporada,
							'id_consejo' => $asClub['DATOS']['id_consejo'],
							'id_club' => $piClub,
							'id_jugador' => $asValores[0],
							'observaciones' => $sObservaciones
							));
					$asInsertaOperacion=$this->financieros_mod->insertaOperacion($piTemporada, $asValores[3],3, $piClub, 4, $asValores[0]);
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

		
		function xi_habilidades ($piJugador) {
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
		}		/*Fin de la clase*/
		function cancela_contrato ($psContrato) {
			$this->load->model(array('financieros_mod','bitacora_draft_mod', 'clubes_mod'));
			$iTemporadaActual=$this->config->item('temporada_actual');
			$asContrato=$this->draft_mod->getContratoByUUID($psContrato);
			$asTransaccion=$this->financieros_mod->getOperacion($iTemporadaActual, 3, $asContrato['DATOS']['id_club'], 4, $asContrato['DATOS']['id_jugador']);
			if ($asContrato['ESTATUS']==1) {
				$asCancelacion=$this->draft_mod->contrato_cancela($psContrato,5);
				//El jugador devuelve el dinero al club
				$asInsertaOperacion=$this->financieros_mod->insertaOperacion($iTemporadaActual, $asTransaccion['DATOS']['cantidad'],4, $asContrato['DATOS']['id_jugador'], 3,$asContrato['DATOS']['id_club'] );
				switch ($asContrato['DATOS']['tipo']) {
					case 'BA': $sTipoContrato="base"; break;
					case 'FR': $sTipoContrato="franquicia"; break;
				}
				$asClub=$this->clubes_mod->RegresaDatos($asContrato['DATOS']['id_club']);
				$this->bitacora_draft_mod->inserta(array (
						'operador' => 	'root',
						'tipo' => 'cancelacion contrato '.$sTipoContrato,
						'temporada' => $iTemporadaActual,
						'id_consejo' => $asClub['DATOS']['id_consejo'],
						'id_club' => $asContrato['DATOS']['id_club'],
						'id_jugador' => $asContrato['DATOS']['id_jugador'],
						'observaciones' => "Contrato cancelado por ajustes "
				));
				if ($asCancelacion['ESTATUS']!=1)
					$this->main_lib->mensaje($asCancelacion['MENSAJE']);
				else
					$this->main_lib->mensaje("El contrato ".$psContrato." se ha cancelado correctamente",'Cancelacion de contratos','success');
			}
			else
				$this->main_lib->mensaje("No existe contrato con ese codigo");
		}
	}
?>