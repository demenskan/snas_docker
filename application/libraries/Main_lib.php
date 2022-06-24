<?php

	class Main_lib {
	
		protected $CI;
		public $iTemporadaActual=6;
		public $sTemplateEnUso="simple-magazine";
		public $sRutaArchivosSubidos;
		public $sRutaLogosTorneos="img/torneos";
	
		function __construct(){
			$this->CI=& get_instance();
			$this->CI->load->helper('url');
			$this->CI->load->model('main_mod');
			$this->iTemporadaActual=$this->CI->config->item('temporada_actual');
			switch ($this->CI->input->server('HTTP_HOST')) {
				case "ligasupernova.co.cc" :
				case "miespacio.ucol.mx":					
				case "supernova.co.cc":					
						$this->sRutaArchivosSubidos="/htdocs/luis.avalos/ci.snas/img/news/";
						break;
				case "10.0.2.2":
				case "localhost":
				case "148.213.229.56":
						$this->sRutaArchivosSubidos="D:\\Archiv~1\\wamp\\www\\SNAS_2009\\img\\news\\";
						break;
				case "localhost:8888":
				case "148.213.4.43:8888":
						$this->sRutaArchivosSubidos="/Applications/MAMP/htdocs/snas_ci/img/news/";
						break;
				default:
						$this->sRutaArchivosSubidos="E:\\PROGRA~1\\wamp\\www\\SNAS_ci\\img\\news\\";
						break;
			}
			
		}
	
		function encabezado($psOpcionSeleccionada=0) {
			$asTabsSelected= array ("","","");
			$asTabsSelected[$psOpcionSeleccionada]="class=\"current-tab\"";		
			if ($this->CI->session->userdata('sUsuario')=='') {
				$sUsuario="			<a href=\"".base_url()."login\">login</a>";
				$sNavegacion="<ul class=\"tabbed\" id=\"network-tabs\">\n"
							."				<li></li>\n"
							."				<li></li>\n"
							."			</ul>\n";
			}
			elseif ($this->CI->session->userdata('sUsuario')=="root") {
				$sUsuario=$this->CI->session->userdata('sUsuario')."<span class=\"text-separator\">|</span><a href=\"".base_url()."login/salir\">Salir</a>";
				$sNavegacion="<ul class=\"tabbed\" id=\"network-tabs\">\n"
							."				<li ".$asTabsSelected[0]."><a href=\"".base_url()."\">SITE</a></li>\n"
							."				<li ".$asTabsSelected[1]."><a href=\"".base_url()."admin/inicio\">Admin</a></li>\n"
							."				<li ".$asTabsSelected[2]."><a href=\"".base_url()."root/menu\">[root]</a></li>\n"
							."			</ul>\n";
			}
			else {
				$sUsuario=$this->CI->session->userdata('sUsuario')."<span class=\"text-separator\">|</span><a href=\"".base_url()."login/salir\">Salir</a>";
				$sNavegacion="<ul class=\"tabbed\" id=\"network-tabs\">\n"
							."				<li ".$asTabsSelected[0]."><a href=\"".base_url()."\">SITE</a></li>\n"
							."				<li ".$asTabsSelected[1]."><a href=\"".base_url()."admin/inicio\">Admin</a></li>\n"
							."			</ul>\n";
			}
			
			$asContHeader=array (
				'FECHA' => date("l, j M Y"),
				'LOGIN' => $sUsuario,
				'MENU_NAVEGACION' => $sNavegacion
			);
			return ($this->CI->load->view("templates/".$this->sTemplateEnUso."/cont_encabezado_vw", $asContHeader, true));
		}

		
		function pie_pagina() {
			$asOut['RUTA_RAIZ']=base_url();
			return($this->CI->load->view("templates/".$this->sTemplateEnUso."/pie_pagina_vw",$asOut,true));
		}
	
		function menu($psSeccion=0, $psMain="inicio", $psSub="") {
			$aoFila=$this->CI->main_mod->menu_navegacion_principales($psSeccion);
			if ($aoFila['ESTATUS']==1) {
				$sMenu="";
				$iContHijos=0;
				$sSubMenuActual="";
				$sSubMenus="";
				$sOpcionesHijos="";
				for ($i=0;$i<count($aoFila['DATOS']);$i++) {
					if ($aoFila['DATOS'][$i]['url']=="_SUBMENU")
						$sURI="JavaScript: submenuDisplay(".$aoFila['DATOS'][$i]['id_unico'].");";
					else
						$sURI=base_url().$aoFila['DATOS'][$i]['url'];

					if ($aoFila['DATOS'][$i]["texto"]==$psMain)
						if ($psSeccion==0)
							$sMenu.="						<li class=\"current-tab\" id=\"opc".$aoFila['DATOS'][$i]["id_unico"]."\"><a href=\"".$sURI."\">".$aoFila['DATOS'][$i]["texto"]."</a></li>\n";
						else
							$sMenu.="						<li class=\"current-tab\" id=\"opc".$aoFila['DATOS'][$i]["id_unico"]."\" style=\"background: url('img/navigation-admin.gif') no-repeat center bottom;\"><a href=\"".$sURI."\">".$aoFila['DATOS'][$i]["texto"]."</a></li>\n";
					else 
						$sMenu.="						<li id=\"opc".$aoFila['DATOS'][$i]["id_unico"]."\"><a href=\"".$sURI."\" >".$aoFila['DATOS'][$i]["texto"]."</a></li>\n";
					
					$aoSubs=$this->CI->main_mod->menu_navegacion_hijos($aoFila['DATOS'][$i]['id_unico']);
					if ($aoSubs['ESTATUS']==1) {
						$sTempSub="";
						for ($j=0;$j<count($aoSubs['DATOS']);$j++) {
							if ($aoFila['DATOS'][$i]["texto"]==$psMain)
								if ($aoSubs['DATOS'][$j]["texto"]==$psSub)
									$sSubMenuActual.="<li class=\"current-tab\"><a href=\"".base_url().$aoSubs['DATOS'][$j]["url"]."\">".$aoSubs['DATOS'][$j]["texto"]."</a></li>";
								else
									$sSubMenuActual.="<li><a href=\"".base_url().$aoSubs['DATOS'][$j]["url"]."\">".$aoSubs['DATOS'][$j]["texto"]."</a></li>";	
							if ($aoSubs['DATOS'][$j]["texto"]==$psSub)
								$sTempSub.="<li class=\"current-tab\"><a href=\"".base_url().$aoSubs['DATOS'][$j]["url"]."\">".$aoSubs['DATOS'][$j]["texto"]."</a></li>";
							else
								$sTempSub.="<li><a href=\"".base_url().$aoSubs['DATOS'][$j]["url"]."\">".$aoSubs['DATOS'][$j]["texto"]."</a></li>";	
						}
						if ($sTempSub=="")
							$sSubMenus.="\t\t\t\t\tsubmenu[".$aoFila['DATOS'][$i]["id_unico"]."]='<li></li>';\n";
						else {
							$sSubMenus.="\t\t\t\t\tsubmenu[".$aoFila['DATOS'][$i]["id_unico"]."]='".$sTempSub."';\n";
							$sOpcionesHijos.=" aMenusConHijos[".$iContHijos."]=".$aoFila['DATOS'][$i]["id_unico"].";\n";
							$iContHijos++;
						}
						if ($sSubMenuActual=="")
							$sSubMenuActual="<li class=\"current-tab\"><a href=\"#\">&nbsp;</a></li>\n";
					}
					else
						$sSubMenus.="submenu[".$aoFila['DATOS'][$i]["id_unico"]."]='Error: ".$aoSubs['MENSAJE']."';\n";
				}
			}
			else
				$sMenu=$aoFila['DATOS'];
			
			switch ($psSeccion) {
				case 0:		$sColorFondo="";
						break;
				case 1:		$sColorFondo="style=\"background: #CC6699; padding: 0 5px; border: 4px double #CC6699;\"";
						break;
				case 2:		$sColorFondo="style=\"background: black; padding: 0 5px; border: 4px double black;\"";
						break;
			}
			
			$asMenuContents= array (
				'MENU_PRINCIPAL' =>  $sMenu,
				'SUB_MENU' =>  $sSubMenuActual,
				'OPCIONES_SUB_MENUS' =>  $sSubMenus,
				'MENUS_CON_HIJOS' =>  $sOpcionesHijos,
				'COLOR_FONDO' =>  $sColorFondo
			);
			return($this->CI->load->view("templates/".$this->sTemplateEnUso."/menu_navegacion_vw.php", $asMenuContents, true));
		}
		
		
		function ImagenEncabezado ($psSeccion=0) {
			$sOutput="<img src=\"".base_url()."img/header1.jpg\" />";
			return($sOutput);
		}

		
		
		function display ($asParametros) {
			$bPermitido=false;
			if ($asParametros['TIPO_ACCESO']=='PUBLIC')
				$bPermitido=true;
			elseif (($asParametros['TIPO_ACCESO']=='ADMIN')&&($this->CI->session->userdata('sUsuario')!=''))
				$bPermitido=true;
			elseif (($asParametros['TIPO_ACCESO']=='ROOT')&&($this->CI->session->userdata('sUsuario')=='root'))
				$bPermitido=true;
			if ($bPermitido) {
				switch ($asParametros['TIPO_ACCESO']) {
					case 'PUBLIC': $iSeccion=0; break;
					case 'ADMIN': $iSeccion=1; break;
					case 'ROOT': $iSeccion=2; break;
				}
				$asSalida['ENCABEZADO']=$this->encabezado($iSeccion);
				$asSalida['MENU_NAVEGACION']=$this->menu($iSeccion);
				$asSalida['PIE_PAGINA']=$this->pie_pagina();
				$asSalida['RUTA_RAIZ']=base_url();
				$asSalida['IMAGEN_ENCABEZADO']=$this->ImagenEncabezado();
				$asSalida['CONTENIDO']=$asParametros['CONTENIDO'];
				if (!isset($asParametros['PLANTILLA']))
					$sPlantilla="templates/simple-magazine/una-columna_vw";
				else
					$sPlantilla="templates/simple-magazine/".$asParametros['PLANTILLA'];
				$sContent=$this->CI->load->view($sPlantilla, $asParametros, true);
				if (!isset($asParametros['WRAPPER']))
					$this->CI->load->view('wrapper_vw.php',$asSalida);
				else
					$this->CI->load->view($asParametros['WRAPPER'],$asSalida);
			}
			else {
					redirect ('errorsistema/muestra/33');
			}
		}
		
		function mensaje($psMensaje, $psTitulo="", $psEstilo="", $psLigaDestino="", $psCaptionDestino="") {
			//echo $psMensaje;
			$asContenido=array(
				'MENSAJE' => $psMensaje,
				'TITULO' => ($psTitulo=="") ? "Error" : $psTitulo,
				'CLASE' => ($psEstilo=="") ? "error" : $psEstilo,
				'RUTA_DESTINO' => ($psLigaDestino=="") ? "" : "<a href=\"".base_url().$psLigaDestino."\">".$psCaptionDestino."</a>",
			);
			switch ($this->CI->session->userdata('sUsuario')) {
				case 'root':
					$iSeccion=1;
					break;
				case '':
					$iSeccion=0;
					break;
				default:
					$iSeccion=1;
					break;
			}
			$asSalida=array (
				'ENCABEZADO' => $this->encabezado($iSeccion),
				'MENU_NAVEGACION' => $this->menu($iSeccion),
				'PIE_PAGINA' => $this->pie_pagina(),
				'RUTA_RAIZ' => base_url(),
				'IMAGEN_ENCABEZADO' => $this->ImagenEncabezado(),
				'CONTENIDO' => $this->CI->parser->parse("mensaje_vw", $asContenido, true)
			);
			$sContent=$this->CI->load->view("templates/simple-magazine/una-columna_vw", $asSalida, true);
			$this->CI->load->view('wrapper_vw.php',$asSalida);
		}
				
			
		function simple_display ($pasContenido, $psVista, $psTemplate, $psAcceso="PUBLIC", $psTipo="parser") {
			if ($psTipo=="parser")
				$asContenido=array ('PRINCIPAL' => $this->CI->parser->parse($psVista, $pasContenido, true));
			else
				$asContenido=array ('PRINCIPAL' => $this->CI->load->view($psVista, $pasContenido, true));	
			$asControlador= array (
				'CONTENIDO' => $this->CI->load->view("templates/".$this->sTemplateEnUso."/".$psTemplate, $asContenido, true),
				'TIPO_ACCESO' => $psAcceso );
			$this->display($asControlador);
		}
	}



?>
