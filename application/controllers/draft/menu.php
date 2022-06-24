<?php

	class menu extends CI_Controller {
	
	  function __construct() {
		  parent::__construct();
	  }
	  
	  function index() {
		$asPrincipal=array(
		  'RUTA_RAIZ' => base_url(),
		  'INDEX_URI' => $this->config->item('index_uri')
		);
		$asContenido=array (
			'PRINCIPAL' => $this->parser->parse('draft/menu_principal_vw', $asPrincipal, true)
		);
		$asControlador= array (
			'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
			'TIPO_ACCESO' => 'ADMIN' );
		$this->main_lib->display($asControlador);
	  }
	}
?>