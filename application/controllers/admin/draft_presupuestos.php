<?php
	
	class draft_presupuestos extends CI_Controller {
	
		function __construct() {
			parent::__construct();
			$this->load->model('draft_mod');
		}
	
		function asignacion() {
			$this->load->model('consejos_mod');
			$iTemporada=$this->config->item('temporada_actual');
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			$iConsejo=$asConsejo['DATOS']['id_unico'];
			$asClubes=$this->draft_mod->getClubesAdministrados(true);
			$asClubesAsignacion=array();
			$fCantidadTotalAsignada=0;
			$fPorcentajeTotalAsignado=0;
			$sListaClubes='';
			$asPresupuestoConsejo=$this->draft_mod->getPresupuestoConsejo($iTemporada, $iConsejo);
			if ($asPresupuestoConsejo['ESTATUS']==1)
				$iPresupuestoConsejo=$asPresupuestoConsejo['DATOS']['cantidad'];
			else
				var_dump ($asPresupuestoConsejo);
			for($i=0;$i<count($asClubes['DATOS']);$i++) {
				/*$asPresupuestoClub=$this->draft_mod->getPresupuestoClub($iTemporada, $asClubes['DATOS'][$i]['id_unico']);
				if ($asPresupuestoClub['ESTATUS']==1) 
					$iCantidadAsignada=($asPresupuestoClub['DATOS']['cantidad']==null) ? 0 : $asPresupuestoClub['DATOS']['cantidad'];
				else
					var_dump($asPresupuestoClub);*/
				$iCantidadAsignada=$this->draft_mod->getPresupuestoClubDesignado($iTemporada, $asClubes['DATOS'][$i]['id_unico']);
				$fPorcentaje=round(($iCantidadAsignada*100)/$iPresupuestoConsejo,2);
				$asClubesAsignacion[$i]=array (
					'CLASE' => ($i%2==0) ? 'non' : 'par',
					'CONSECUTIVO' => $i,
					'NOMBRE_CLUB' => $asClubes['DATOS'][$i]['nombre_corto'],
					'CODIGO_CLUB' => $asClubes['DATOS'][$i]['id_unico'],
					'MINIMO_OPERACIONAL' => ($asClubes['DATOS'][$i]['division']==1) ? "48" : "16" ,
					'CANTIDAD_ASIGNADA' => $iCantidadAsignada,
					'PORCENTAJE_ASIGNADO' => $fPorcentaje
				);
				$sListaClubes.=($sListaClubes=='') ? $asClubes['DATOS'][$i]['id_unico'] : "|".$asClubes['DATOS'][$i]['id_unico'];
				$fCantidadTotalAsignada+=$iCantidadAsignada;
				$fPorcentajeTotalAsignado+=$fPorcentaje;
			}
			$asPrincipal=array(
				'RUTA_RAIZ' => base_url(), 'INDEX_URI' => $this->config->item('index_uri'),
				'CLUBES' => $asClubesAsignacion,
				'PRESUPUESTO_CONSEJO' => $iPresupuestoConsejo,
				'CANTIDAD_TOTAL' => $fCantidadTotalAsignada,
				'PORCENTAJE_TOTAL' => $fPorcentajeTotalAsignado,
				'TOTAL_CLUBES' => count($asClubes['DATOS']),
				'LIMITE_MAXIMO_POR_CLUB' => $this->config->item('maximo_asignacion_club'),
				'LIMITE_MINIMO_POR_CLUB' => $this->config->item('minimo_asignacion_club'),
				'LISTA_CLUBES' => $sListaClubes
			);	
				
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/asignacion_presupuestos_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}

		function procesa_asignacion() {
			//Toma la cadena de las claves y las procesa haciendo asignaciones a los clubes en la tabla de
			//operaciones financieras
			$this->load->model(array('consejos_mod','financieros_mod','bitacora_draft_mod'));
			$iTemporada=$this->config->item('temporada_actual');
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			$iConsejo=$asConsejo['DATOS']['id_unico'];
			$asCodigosClubes=explode('|',$this->input->post('hdnListaClubes'));
			$sErrores="";
			for ($i=0;$i<count($asCodigosClubes);$i++) {
				$asAsignacion=$this->financieros_mod->getOperacion($iTemporada, 2, $iConsejo, 3,  $asCodigosClubes[$i]);
				if ($asAsignacion['ESTATUS']==1) {
					$asResult=$this->financieros_mod->actualizaOperacion($asAsignacion['DATOS']['referencia'],$this->input->post('txtCantidad'.$asCodigosClubes[$i]));
					$this->bitacora_draft_mod->inserta(array (
						'operador' => 	'root',
						'tipo' => 'reasignacion presupuesto',
						'temporada' => $iTemporada,
						'id_consejo' => $iConsejo,
						'id_club' => $asCodigosClubes[$i],
						'observaciones' => "Reasignacion por ".$this->input->post('txtCantidad'.$asCodigosClubes[$i])." KCacaos (".$this->input->post('txtPorcentaje'.$asCodigosClubes[$i])."%) "
					));
				}
				else {
					$asResult=$this->financieros_mod->insertaOperacion($iTemporada, $this->input->post('txtCantidad'.$asCodigosClubes[$i]),2, $iConsejo, 3, $asCodigosClubes[$i]);
					$this->bitacora_draft_mod->inserta(array (
						'operador' => 	'root',
						'tipo' => 'asignacion presupuesto',
						'temporada' => $iTemporada,
						'id_consejo' => $iConsejo,
						'id_club' => $asCodigosClubes[$i],
						'observaciones' => "Asignacion por ".$this->input->post('txtCantidad'.$asCodigosClubes[$i])." KCacaos (".$this->input->post('txtPorcentaje'.$asCodigosClubes[$i])."%) "
					));
				}
				if ($asResult['ESTATUS']!="1")
					$sErrores.=$asResult['MENSAJE'];
			}
			if ($sErrores=="")
				$asSalida=array (
					'MENSAJE' => "Se ha logrado con exito la asignacion",
					'CLASE' => "success"
				);
			else
				$asSalida=array (
					'MENSAJE' => $sErrores,
					'CLASE' => "error"
				);
			$asSalida['TITULO']="Asignacion presupuestos consejo";
			$asSalida['RUTA_RAIZ']=base_url();
			$asContenido=array (
				'PRINCIPAL' => $this->load->view('mensaje_vw', $asSalida, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}

		/*Fin de la clase*/
	}
?>