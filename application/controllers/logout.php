<?php

	class Login extends CI_Controller {
	
		function __construct() {
			parent::__construct();
		}
		
		function index() {
			$asLogin=array('RUTA_RAIZ' => base_url());
			$asContenido=array (
				'PRINCIPAL' => $this->load->view('login_vw',$asLogin, true),
				'BARRA_DERECHA' => ""
			);
			$asControlador=array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
				'TIPO_ACCESO' => 'PUBLIC'
			);
			$this->main_lib->display($asControlador);
		}
		
		function verifica() {
			$sLogin=$_POST["txtUser"];
			$sPass=$_POST["txtPWD"];
			$this->load->model('usuarios_mod');
			//Medida anti inyeccion de codigo SQL
			$sLogin=str_replace("'","QQ",$sLogin);
			$asCredenciales=$this->usuarios_mod->VerificaLogin($sLogin, $sPass);
			if ($asCredenciales['ESTATUS']==1){
				$this->session->set_userdata('sUsuario', $sLogin);
				if ($sLogin=="root") {
						redirect ('adminroot/inicio');
				}		
				else {
						redirect('admin/inicio');
				}
			}
			else
				redirect('error/01');
		}
	}
	
	
?>