<?php

	class usuarios_mod extends CI_Model {
	
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}
		
		function VerificaLogin($psUser, $psPass) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "Select * from operadores where login='".$psUser."' And pass='".$psPass."'",
						'UNICA_FILA' => true)
			);
			return ($asResult);
		}
	
	}
?>