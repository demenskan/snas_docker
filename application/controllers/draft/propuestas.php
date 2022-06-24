<?php
	
	class propuestas extends CI_Controller {
	
		function __construct() {
			parent::__construct();
			$this->load->model('draft_mod');
		}
	
		function disponibles() {
			$sLista="";
			$sTemporada=$gsTemporadaActual;
			$asClubes=$this->draft_mod->getClubesAdministrados();
			$sComboClubes=$this->tools_lib->GeneraCombo(
				array(
					'NOMBRE' => 'slcListaEquipos',
					'DATASET' => $asClubes,
					'EVENTO' => "onclick=\"callPage('Draft/xi_roster.php?tm='+this.value,'ListaJugadores','Cargando...','Error@Carga')\""
				)
			);
			$asPrincipal=array (
				'COMBO_CLUBES' => $sComboClubes
			);
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/selecciona_disponibles_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}

	/*Fin de la clase*/
	}
?>