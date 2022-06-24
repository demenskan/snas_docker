<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Access_lib {
	
		function __construct() {
			$this->CI=& get_instance();
			$this->CI->load->helper('url');
			$this->CI->load->model('permisos_model');
			$this->CI->load->library('genericos'); //Borrar
		}
	
		
		function PuedePasar ($pasOpciones) {
			if ($pasOpciones['sesion_usuario']!='') {
				$sPerfilUsuario=$this->DefinePerfilUsuario($pasOpciones);	
			}
			else {
				$sPerfilUsuario='PUBLIC';
			}
			$asArgsPermisos= array ( 	'aplicacion' => $this->DefineClaveAplicacion($pasOpciones) ,
										'clase' => $pasOpciones['clase']);
			$sPermisoClase=$this->CI->permisos_model->encuentra_permiso_clase($asArgsPermisos);
			if ($sPermisoClase=='NO_DEFINIDO') {
				$asRes['PERMITIDO']=FALSE;
				$asRes['CODIGO_ERROR']='95';
			}
			else {
				if (($sPermisoClase=='PUBLIC') || (($sPermisoClase=='LOGGED')&&($sPerfilUsuario!='PUBLIC')) || ($sPerfilUsuario=='SU') || ($sPerfilUsuario=='ADM')) {
						$asRes['PERMITIDO']=TRUE;
						
				}
				else {
					$asArgs=array (	'aplicacion' => $this->DefineClaveAplicacion($pasOpciones) ,
						'perfil' => $sPerfilUsuario,
						'clase' => $pasOpciones['clase']
					);
					if ($this->CI->permisos_model->encuentra_permiso($asArgs)) {
						$asRes['PERMITIDO']=TRUE;
					}
					else {
						$asRes['PERMITIDO']=FALSE;
						$asRes['CODIGO_ERROR']='99';
					}
				}	
			}
			//$this->CI->genericos->dump('PuedePasar', $pasOpciones);
			return ($asRes);
		}
		
		
		function DefinePerfilUsuario ($pasParams) {
			$asArgsPerfil['aplicacion']=$this->DefineClaveAplicacion($pasParams);
			$asArgsPerfil['usuario']=$pasParams['sesion_usuario'];
			$sPerfil=$this->CI->permisos_model->encuentra_perfil_usuario($asArgsPerfil);
			if ($sPerfil=='LOGGED') {
				$asArgsPerfil['aplicacion']='main';
				$asArgsPerfil['usuario']=$pasParams['sesion_usuario'];
				$sPerfil=($this->CI->permisos_model->encuentra_perfil_usuario($asArgsPerfil)=='SU') ? 'SU' : 'LOGGED';
			}
			return ($sPerfil);
		}
		
		
		function DefineClaveAplicacion ($pasEntradas) {
			$asSecciones=$pasEntradas['URL'];
			if (!isset($pasEntradas['URL'][1]))
				$sAplicacion='main';
			elseif ($asSecciones[1]==$pasEntradas['clase'])
				$sAplicacion='main';
			elseif ($asSecciones[1]=='main')      //Por los menus contextuales
				$sAplicacion='main';
			else
				$sAplicacion=$this->CI->permisos_model->encuentra_aplicacion($asSecciones[1]);
			return ($sAplicacion);
		}
	
	}

	

?>