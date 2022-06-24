<?php

	class Draft extends CI_Controller {
	
		function __construct() {
			parent::__construct();
		}
		
		function reiniciatemporal() {
			$this->load->model('draft_mod');	
			$asInicio=array('RUTA_RAIZ' => base_url(), 'INDEX_URI' => $this->config->item('index_uri'));
			$asResult=$this->draft_mod->ReiniciaTemporalDraft();
			if ($asResult['ESTATUS']==1)
				$this->main_lib->mensaje("ok","Reseteo de draft", "success");
			else
				$this->main_lib->mensaje("Error");
		}
		
		function calculahabilidades() {
			$this->load->model('root_mod');
			$asCampos=$this->root_mod->getCamposHabilidades();
			for ($iSeccion=0;$iSeccion<51;$iSeccion++) {
				$asHabilidades=$this->root_mod->getHabilidadesJugadores($iSeccion*100);
				//var_dump($asHabilidades['ESTATUS']);
				for ($i=0;$i<count($asHabilidades['DATOS']);$i++) {
					$iTotalPuntos=0;
					for ($a=0;$a<count($asCampos['DATOS']);$a++) {
							$iPesoHabilidad=$asCampos['DATOS'][$a]['valor_peso'];
							$iValorHabilidad=$asHabilidades['DATOS'][$i][$asCampos['DATOS'][$a]['campo']];
							$iTotalPuntos+=$iValorHabilidad*$iPesoHabilidad;
					}
					$sResult=$this->root_mod->ActualizaPuntos($iTotalPuntos, $asHabilidades['DATOS'][$i]['id_unico']);
					/*if (!$sResult['ESTATUS']!=1) {
							echo $sResult['MENSAJE']."|".$sResult['QUERY']."\n";
							flush();
					}*/
				}
				echo "Listo bloque ".$iSeccion;
			}
		}
	}
	
	
?>