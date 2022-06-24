<?php

	class Menu extends CI_Controller {
	
		function __construct() {
			parent::__construct();
		}
		
		function index() {
			$asInicio=array('RUTA_RAIZ' => base_url(), 'INDEX_URI' => $this->config->item('index_uri'));
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('root/inicio_vw',$asInicio, true),
				'BARRA_DERECHA' => ""
			);
			$asControlador=array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ROOT'
			);
			$this->main_lib->display($asControlador);
		}
	}
	
	
?>