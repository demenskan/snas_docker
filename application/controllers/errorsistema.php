<?php


	class ErrorSistema extends CI_Controller {
	
		function Muestra ($psCodigo) {
			$asMensajes= array (
				'33' => 'No se tienen permisos para el acceso a esta pagina',
				'09' => 'Error en SQL',
				'80' => 'Fallaron las credenciales de la aplicacion',
				'83' => 'Nombre de usuario o contrase&ntilde;a incorrectos',
				'84' => 'Nombre de usuario o contrase&ntilde;a incorrectos',
				'85' => 'Error interno del servicio web',
				'11' => 'Error carga cliente webservice',
				'91' => 'La sesi&oacute;n ha expirado',
				'99' => 'No tiene permisos para acceder a este modulo',
				'95' => 'No tiene permisos definidos esta aplicacion'
			);
			$sMensaje= (isset($asMensajes[$psCodigo])) ? $asMensajes[$psCodigo] : "Error desconocido";
			$asContenido=array (
				'CLASE' => "error",
				'TITULO' => "Error ".$psCodigo.":",
				'MENSAJE' => $sMensaje, 'RUTA_RAIZ' => base_url()
			);
			
			$asSalida = array ( 'CONTENIDO'=> $this->load->view('mensaje_vw',$asContenido, true), 'TIPO_ACCESO' => 'PUBLIC');
			$this->main_lib->display($asSalida);
		}
	}


?>