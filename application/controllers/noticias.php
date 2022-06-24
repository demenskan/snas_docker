<?php

	class noticias extends CI_Controller {
	
		function __construct() {
			parent::__construct();
			$this->load->model('noticias_mod');
		}
	
		function ver ($piClave) {
			$asContenido=array (
				'PRINCIPAL' => $this->principal($piClave),
				'BARRA_DERECHA' => $this->barraderecha($piClave)
			);
			$asControlador=array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
				'TIPO_ACCESO' => 'PUBLIC'
			);
			$this->main_lib->display($asControlador);
		}
	
	
		function principal($piClaveNoticia) {
			$sSalidagc="";
			$asNoticia=$this->noticias_mod->LeeNoticia($piClaveNoticia);
			$sMargen="\t\t\t\t";
			$sClaseTab="ui-tabs-nav-item ui-tabs-selected";
			$iCont=1;
			
			switch ($asNoticia['ESTATUS']) {
				case 1:
					$sCuerpoNoticia=str_replace("\n","<br/>\n",$asNoticia['DATOS']['cuerpo']);
					$asImagenes=$this->noticias_mod->getGaleria($piClaveNoticia);
					$asTabsTitulo=array();
					$asTabsContenido=array();
					if ($asImagenes['ESTATUS']==1) {
						for ($i=0;$i<count($asImagenes['DATOS']);$i++) {
							$asTabsTitulo[]=array(
								'CONSECUTIVO' => $asImagenes['DATOS'][$i]['consecutivo'],
							);
							$asTabsContenido[]=array (
								'URL' => $asImagenes['DATOS'][$i]['url'],
								'CONSECUTIVO' => $asImagenes['DATOS'][$i]['consecutivo'],
								'LEYENDA' => $asImagenes['DATOS'][$i]['leyenda']
							);
						}
					}
					$asContenido=array (
						'TITULO' => $asNoticia['DATOS']['titulo'],
						'SUBTITULO' => $asNoticia['DATOS']['subtitulo'],
						'CUERPO' => $sCuerpoNoticia,
						'PIE_FOTO' => $asNoticia['DATOS']['pie_foto'],
						'FECHA' => $asNoticia['DATOS']['fecha'],
						'CLAVE_NOTICIA' => $piClaveNoticia,
						'IMAGEN' => '',
						'BLOQUE_TABS' => $asTabsTitulo,
						'BLOQUE_CONTENIDOS' => $asTabsContenido
					);
					break;
				case 0:
						$asContenido['MENSAJE']="No hay noticias con este codigo";
					break;
				case -1:
						$asContenido['MENSAJE']="Error: ".$asNoticia['ERROR'];
					break;
			}
			$asComentarios=$this->noticias_mod->LeeComentarios($piClaveNoticia);
			$asContenido['BLOQUE_COMENTARIOS']=array();
			
			if ($asComentarios['ESTATUS']==1) {
				for ($i=0;$i<count($asComentarios['DATOS']);$i++) {
					$asContenido['BLOQUE_COMENTARIOS'][]=array (
						'OPERADOR' => $asComentarios['DATOS'][$i]['operador'],
						'FECHA_HORA' => $asComentarios['DATOS'][$i]['fecha_hora'],
						'COMENTARIO' => $asComentarios['DATOS'][$i]['comentario']
					);
				}
			}
			return ($this->parser->parse('ver_noticia_vw', $asContenido, true));		
		}
	
		function barraderecha($psId) {
			$asTags=$this->noticias_mod->TagsNoticias($psId);
			$this->load->model(array('torneos_mod','jugadores_mod', 'clubes_mod', 'consejos_mod'));
			$sTags="<li>No hay</li>";
			$asRelacionadas=array();
			$aiConteoOcurrencias=array();
			$sLigasRel="<li>No hay</li>";
			if ($asTags['ESTATUS']==1) {
				$sTags="";
				for ($i=0;$i<count($asTags['DATOS']);$i++) {
					$sValor=$asTags['DATOS'][$i]['valor'];
					
					$asNoticiasRelacionadasxTag=$this->noticias_mod->getRelacionadas($asTags['DATOS'][$i]['campo'], $sValor, $psId);
					if ($asNoticiasRelacionadasxTag['ESTATUS']!=-1) {
						/*Al menos en teoria, el ciclo comienza una nueva posicion si encuentra una nueva clave, si no, aumenta la ocurrencia*/
						for ($j=0;$j<count($asNoticiasRelacionadasxTag['DATOS']);$j++) {
							if (isset($asRelacionadas[$asNoticiasRelacionadasxTag['DATOS'][$j]['id_docto']]))
								$asRelacionadas[$asNoticiasRelacionadasxTag['DATOS'][$j]['id_docto']]++;
							else
								$asRelacionadas[$asNoticiasRelacionadasxTag['DATOS'][$j]['id_docto']]=1;
							/*$iPosClave=array_search($asNoticiasRelacionadasxTag['DATOS'][$j]['id_docto'], $asRelacionadas);
							if ($iPosClave!=false) {
								$aiConteoOcurrencias[$iPosClave]++;
							}
							else {
								array_push($asRelacionadas, $asNoticiasRelacionadasxTag['DATOS'][$j]['id_docto']);
								array_push($aiConteoOcurrencias,1);
							}*/
						}
					}
					switch ($asTags['DATOS'][$i]['campo']) {
						case 'torneo':
								$iTemporada=substr($sValor,0,strpos($sValor,","));
								$iClaveTorneo=substr($sValor,strpos($sValor,",")+1);
								$sLiga="<a href=\"".base_url()."torneos/ver/".$iTemporada."/".$iClaveTorneo."\">"
									.$this->torneos_mod->NombreTorneo($iTemporada, $iClaveTorneo)
									."</a>";
								break;
						case 'jugador':
								$sLiga="<a href=\"".base_url()."jugadores/ver/".$sValor."\">"
								.$this->jugadores_mod->NombreJugador($sValor)
								."</a>";
							break;
						case 'equipo':
								$this->load->model('clubes_mod');
								$asClub=$this->clubes_mod->RegresaDatos($sValor);
								$sLiga="<a href=\"".base_url()."clubes/inicio/".$asClub['DATOS']['ruta_logo']."\">"
								.$this->clubes_mod->RegresaNombre($sValor)
								."</a>";
								break;
						case 'consejo':
								$sLiga="<a href=\"".base_url()."consejos/ver/".$sValor."\">"
								.$this->consejos_mod->RegresaNombre($sValor)
								."</a>";
								break;
					}
					$sTags.="\t\t\t\t<li>".$sLiga."</li>\n";
				}
				if (count($asRelacionadas)>0) {
					arsort($asRelacionadas);
					$sLigasRel="";
					foreach ($asRelacionadas as $key => $value) {
						$asDatosNoticiaRelacionada=$this->noticias_mod->LeeNoticia($key);
						$sLigasRel.="\t\t\t\t<li><a href=\"".base_url()."noticias/ver/".$key."\">"
								.$asDatosNoticiaRelacionada['DATOS']['titulo']." (".$value.")"
								."</a></li>\n";
					}
				}
				
			}
			//echo "Relacionadas:"; var_dump ($asRelacionadas);

			$sContenidoTags=$this->load->view('templates/simple-magazine/barra_noticias_vw', array ('TITULO' => "Tags", 'TAGS_NOTICIAS' => $sTags), true)
							.$this->load->view('templates/simple-magazine/barra_noticias_vw', array ('TITULO' => "Noticias relacionadas", 'TAGS_NOTICIAS' => $sLigasRel), true);
			return($sContenidoTags."\n");
		}
	
	
		function captura ($piClave=-1, $psMensaje="") {
			//Carga el elemento de numero de imagenes a trepar
			$asDataCantidad=array();
			for ($i=0;$i<12;$i++) {
				$asDataCantidad['DATOS'][]=array (0=>$i, 1=>$i);
			}
			
			
			
			
			$this->load->model(array('torneos_mod', 'jugadores_mod', 'consejos_mod', 'clubes_mod'));
			$sMensaje=($psMensaje!="") ? "<div class=\"error\">".$sMensaje."</div>" : "" ;
			$sEvento=" onChange=\"document.getElementById('textarea-torneos').value='';\"";			
			$asEquipos=$this->noticias_mod->getListaClubes();
			$sListaEquipos="";
			if ($asEquipos['ESTATUS']==1) {
				for ($i=0;$i<count($asEquipos['DATOS']);$i++) {
					$sComa=($sListaEquipos!="") ? "," : "";
					$sListaEquipos.="\t ".$sComa."'".$asEquipos['DATOS'][$i]["nombre_corto"]."' \n";	
				}
			}
			else
				$sListaEquipos=$asEquipos['ERROR'];
	
			$asConsejos=$this->noticias_mod->getListaConsejos();
			$sListaConsejos="";
			if ($asConsejos['ESTATUS']==1) {
				for ($i=0;$i<count($asConsejos['DATOS']);$i++) {
					$sComa=($sListaConsejos!="") ? "," : "";
					$sListaConsejos.="\t ".$sComa."'".$asConsejos['DATOS'][$i]["iniciales"]."' \n";	
				}
			}
			else
				$sListaConsejos=$asConsejos['ERROR'];
			
			$sModoCaptura=($piClave==-1) ? "A" : "E";

			if ($sModoCaptura=="E") { //Edicion
				$sIdNoticia=$piClave;
				$asNoticia=$this->noticias_mod->LeeNoticia($sIdNoticia);
				if ($asNoticia['ESTATUS']==1) {
					$iTemporada=$asNoticia['DATOS']['temporada'];
					$sTitulo=$asNoticia['DATOS']['titulo'];
					$sSubTitulo=$asNoticia['DATOS']['subtitulo'];
					$sResumen=$asNoticia['DATOS']['resumen'];
					$sCuerpo=$asNoticia['DATOS']['cuerpo'];
					$sImagenURL="";
					$asGaleria=$this->noticias_mod->getGaleria($sIdNoticia);
					if ($asGaleria['ESTATUS']==1) {
						$iDefaultNumImagenes=count($asGaleria['DATOS']);
						for ($i=0;$i<count($asGaleria['DATOS']);$i++) {
							$asVistaGaleria['BLOQUE_FILA'][]=array (
								'CONSECUTIVO' => $asGaleria['DATOS'][$i]['consecutivo'],
								'CLASE' => ($i%2==0) ? "par" : "non",
								'CAPTION' => $asGaleria['DATOS'][$i]['leyenda'],
								'URL' => $asGaleria['DATOS'][$i]['url']
							);
						}
						$asSalidaGaleria=$this->parser->parse("admin/galeria_noticia_vw",$asVistaGaleria,true);
					}
					else {
						$iDefaultNumImagenes=0;
						$asSalidaGaleria="";
					}
					
					//Carga etiquetas
					$asTagsNoticias=$this->noticias_mod->TagsNoticias($sIdNoticia);
					$sEtiquetasTorneos="";
					$sEtiquetasConsejos="";
					$sEtiquetasEquipos="";
					$sEtiquetasJugadores="";
					if ($asTagsNoticias['ESTATUS']!=-1) {
						for($i=0;$i<count($asTagsNoticias['DATOS']);$i++) {
							switch ($asTagsNoticias['DATOS'][$i]["campo"]) {
								case 'torneo':
										$asValores=explode(",",$asTagsNoticias['DATOS'][$i]["valor"]);
										$sEtiquetasTorneos.=$this->torneos_mod->NombreTorneo($asValores[0], $asValores[1]).", ";
										break;
								case 'jugador':
										$sEtiquetasJugadores.=$this->jugadores_mod->NombreJugador($asTagsNoticias['DATOS'][$i]['valor']).", ";
										break;
								case 'consejo':
										$sEtiquetasConsejos.=$this->consejos_mod->RegresaNombre($asTagsNoticias['DATOS'][$i]['valor']).", ";
										break;
								case 'equipo':
										$sEtiquetasEquipos.=$this->clubes_mod->RegresaNombre($asTagsNoticias['DATOS'][$i]['valor']).", ";
										break;
							}
						}
					}
					else
						echo ($asTagsNoticias['ERROR']);
				}
			}
			else {					//ALTA
				$iTemporada=$this->main_lib->iTemporadaActual;
				$iDefaultNumImagenes=1;
				$sEtiquetasTorneos="";
				$sEtiquetasEquipos="";
				$sEtiquetasJugadores="";
				$sEtiquetasConsejos="";
				$sIdNoticia="";
				$sTitulo="";
				$sSubTitulo="";
				$sResumen="";
				$sCuerpo="";
				$sPieFoto="";
				$sImagen="";
				$sImagenURL="";
				$asVistaGaleria['BLOQUE_FILA'][]=array (
								'CONSECUTIVO' => 1,
								'CLASE' =>  "non",
								'CAPTION' => "",
								'URL' => ""
							);
				$asSalidaGaleria=$this->parser->parse("admin/galeria_noticia_vw",$asVistaGaleria,true);

			}
			
			$asFormulario=array (
				'EQUIPOS' => $sListaEquipos, 'CONSEJOS' => $sListaConsejos, 'MODO_CAPTURA' => $sModoCaptura,
				'COMBO_TEMPORADA' => $this->tools_lib-> GeneraCombo(array (
								'NOMBRE' => "slcTemporada", 'TABLA' => "temporadas",  'CAMPO_CLAVE' => "temporada",
								'LEYENDA' => "temporada", 'DEFAULT' => $iTemporada, 'OPCION_EXTRA' => "Seleccione una...",
								'EVENTOS' => $sEvento
								)),
				'COMBO_NUM_IMAGENES' => $this->tools_lib-> GeneraCombo(array (
								'NOMBRE' => "slcNumImagenes", 'DATASET' => $asDataCantidad,
								'DEFAULT' => $iDefaultNumImagenes, 
								'EVENTOS' => "onchange=\"Javascript: CambiaCantidadImagenes(this.value);\""
								)),
				'FILAS_GALERIA' => $asSalidaGaleria,
				'LISTA_TORNEOS' => $sEtiquetasTorneos, 'LISTA_EQUIPOS' => $sEtiquetasEquipos, 'LISTA_JUGADORES' => $sEtiquetasJugadores,
				'LISTA_CONSEJOS' => $sEtiquetasConsejos, 'ID_NOTICIA' => $sIdNoticia,
				'TITULO_NOTICIA' => $sTitulo ,'SUBTITULO_NOTICIA' => $sSubTitulo,'RESUMEN_NOTICIA' => $sResumen ,'CUERPO_NOTICIA' => $sCuerpo,
				'RUTA_RAIZ' => base_url(), 'MENSAJE' => $sMensaje
			);
			
			$asContenido=array (
				'PRINCIPAL' => $this->load->view('admin/captura_noticia_vw', $asFormulario, true)
			);
			$asControlador=array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN'
			);
			$this->main_lib->display($asControlador);
		
		
		}
		function listajugadores() {
			$this->load->model('jugadores_mod');
			$sSearch=$_REQUEST["q"];
			$asJugadores=$this->jugadores_mod->ListaJugadoresBusqueda($sSearch);
			
			if ($asJugadores['ESTATUS']!=-1) {
				$sSalida="";
				for ($i=0;$i<count($asJugadores['DATOS']);$i++) {
					$sSalida.=$asJugadores['DATOS'][$i]['id_unico']."|".$asJugadores['DATOS'][$i]['nombre']."\n";
				}
			}
			else
				$sSalida=$asJugadores['ERROR'];
			echo ($sSalida);
		}
		
		function listatorneos() {
			$this->load->model('torneos_mod');
			$sTemporada=(isset($_REQUEST["season"])) ? $_REQUEST["season"] : "6";
			if ($sTemporada!="__EXTRA__") {
				$sSearch=(isset($_REQUEST["q"])) ? $_REQUEST['q'] : "";
				$asTorneos=$this->torneos_mod->ListaTorneosBusqueda($sTemporada, $sSearch);
				if ($asTorneos['ESTATUS']!=-1) {
					$sSalida="";
					for ($i=0;$i<count($asTorneos['DATOS']);$i++) 
						$sSalida.=$asTorneos['DATOS'][$i]["clave"]."|".$asTorneos['DATOS'][$i]["nombre"]."\n";
				}
				else
					$sSalida=$asTorneos['ERROR'];
				echo $sSalida;
			}
		}
		
		function seleccionaimagen() {
			$sLista="";
			$sClase="";
			$sTabs="\t\t\t\t\t\t";
			//** Lo que no se quiere mostrar
			$asExcepciones=array(
				".",
				"..",
				".DS_Store",
				"Thumbs.db");
	
			if ($handle = opendir('img/news')) {
				//echo "Directory handle: $handle\n";
				//echo "Files:\n";
				/* This is the correct way to loop over the directory. */
				while (false !== ($file = readdir($handle))) {
					if($sClase=="")
						$sClase="even";
					else
						$sClase="";
					if ((array_search($file, $asExcepciones)==false) && ($file!=".")){	
						$sLista.=$sTabs."<tr class=\"".$sClase."\">\n"
							.$sTabs."\t<td><img src=\"".base_url()."img/news/".$file."\" width=\"100\" /></td>\n"
							.$sTabs."\t<td><a href=\"JavaScript: Selecciona('img/news/".$file."');\">".$file."</a></td>\n"
							.$sTabs."</tr>";
					}
				}
				closedir($handle);
			}
			else
				$sLista="Error";
			$asContenido=array ('IMAGENES' => $sLista);
			echo ($this->load->view("admin/selecciona_imagen_noticia_vw", $asContenido, true));
		}

		function graba() {
			$asNoticia=array (
				'modo' => $this->input->post('modo'),
				'tipoImagen' => $this->input->post('rbTipoImagen'),
				'codigoNoticia' => $this->input->post('hdnIdNoticia'),
				'titulo' => $this->input->post('txtTitulo'),
				'subtitulo' => $this->input->post('txtSubtitulo'),
				'tagsTorneos' => $this->input->post('taListaTorneos'),
				'tagsEquipos' => $this->input->post('taListaEquipos'),
				'tagsJugadores' => $this->input->post('taListaJugadores'),
				'tagsConsejos' => $this->input->post('taListaConsejos'),
				'temporada' => $this->input->post('slcTemporada'),
				'resumen' => $this->input->post('taResumen'),
				'cuerpo' => $this->input->post('taCuerpo'),
				'maxSize' => $this->input->post('MAX_FILE_SIZE'),
			);
			// Graba todo en la base de datos
			$bErrorDB=false;
			$sResult="";
			if ($asNoticia['modo']=="A") {
				$asNoticia['codigoNoticia']=$this->tools_lib->trae_ultimo_indice(array('nombre_campo' => 'id_unico', 'tabla' => 'noticias'));
				$asResultDB=$this->noticias_mod->graba($asNoticia);
			}
			else {
				$asResultDB=$this->noticias_mod->actualiza($asNoticia);
				if ($asResultDB['ESTATUS']==0) {
					$sMensajeError.=$asResultDB['ERROR']."\n";
					$bErrorDB=true;
				}
				$asBorraTags=$this->noticias_mod->BorraEtiquetas($asNoticia['codigoNoticia']);
				if ($asBorraTags['ESTATUS']==0) {
					$sMensajeError.=$asBorraTags['ERROR']."\n";
					$bErrorDB=true;
				}
			}
			if ($asResultDB['ESTATUS']==0) {    //Al usar ejecutar_query solo regresa 0 o 1
				$sMensajeError.=$asResultDB['ERROR']."\n";
				$bErrorDB=true;
			}
			//torneos
			$asResultDB=$this->GrabaTags('torneo', $asNoticia['tagsTorneos'], $asNoticia['codigoNoticia']);
			if ($asResultDB['ESTATUS']==-1) {
				$sMensajeError.=$asResultDB['ERROR']."1\n";
				$bErrorDB=true;
			}
			//jugadores
			$asResultDB=$this->GrabaTags('jugador', $asNoticia['tagsJugadores'], $asNoticia['codigoNoticia']);
			if ($asResultDB['ESTATUS']==-1) {
				$sMensajeError.=$asResultDB['ERROR']."2\n";
				$bErrorDB=true;
			}
			//clubes
			$asResultDB=$this->GrabaTags('equipo', $asNoticia['tagsEquipos'], $asNoticia['codigoNoticia']);
			if ($asResultDB['ESTATUS']==-1) {
				$sResult.=$asResultDB['ERROR']."3\n";
				$bErrorDB=true;
			}
			//consejos
			$asResultDB=$this->GrabaTags('consejo', $asNoticia['tagsConsejos'], $asNoticia['codigoNoticia']);
			if ($asResultDB['ESTATUS']==-1) {
				$sResult.=$asResultDB['ERROR']."4\n";
				$bErrorDB=true;
			}
			$asFileConfig=array (
			'upload_path' => "uploads",
			'allowed_types' => 'gif|jpg|png',	'max_size'	=> '2048',
			'max_width'  => '1024',	'max_height'  => '768',	'remove_spaces' => true	);
			$this->load->library('upload', $asFileConfig);
			$this->noticias_mod->BorraLinksImagenes($asNoticia['codigoNoticia']);
			for ($i=1;$i<=$this->input->post('slcNumImagenes');$i++) {
				$iErrorImagen=0;
				$sMensajeError="";
				switch ($this->input->post("slcTipoEntrada".$i)) {
					case "Galeria":
							$this->noticias_mod->GrabaLinkImagen(array (
								'id_noticia' => $asNoticia['codigoNoticia'],
								'url' =>$this->input->post("txtGaleria".$i),
								'leyenda' => $this->input->post("txtCaption".$i),
								'consecutivo' => $i,
								'portada' => ($i==1) ? "1" : "0" 
								));
							//$bSubirFoto=false;
						break;
					case "Nuevo":
							$sNombreArchivo=$_FILES["fImagen".$i]["name"]; //---Nombre del archivo en la maquina del usuario
							$sTipoArchivo= $_FILES["fImagen".$i]["type"];  //--- Mime type
							$iTamanoArchivo= $_FILES["fImagen".$i]["size"]; //---
							$asNoticia['rutaFoto']="'img/news/".$asNoticia['codigoNoticia']."/".$i.".".$sTipoArchivo."'";
							if (!$this->upload->do_upload('fImagen'.$i)) {
								$sMensajeError=$this->upload->display_errors()."\n";
								$iErrorImagen=1;
								echo $sMensajeError;
							}
							else {
								$asDatosArchivoSubido=$this->upload->data();
								if (!is_dir("img/news/".$asNoticia['codigoNoticia']))
									mkdir ("img/news/".$asNoticia['codigoNoticia'], 0777);
								rename("uploads/".$asDatosArchivoSubido['file_name'],"img/news/".$asNoticia['codigoNoticia']."/".$i.$asDatosArchivoSubido['file_ext']);
								//hace el thumbnail
								/*$image_path = 'img/news/'.$asNoticia['codigoNoticia']."/";
								$thumb = PhpThumbFactory::create($image_path.$i.$asDatosArchivoSubido['file_ext']);
								$thumb->resize(100, 100);
								$thumb->save($image_path.'thumb_'.$i.'.png', 'png');*/
								$bUnaImagenBuena=1;
								$this->noticias_mod->GrabaLinkImagen(array (
									'id_noticia' => $asNoticia['codigoNoticia'],
									'url' => "img/news/".$asNoticia['codigoNoticia']."/".$i.$asDatosArchivoSubido['file_ext'],
									'leyenda' => $this->input->post("txtCaption".$i),
									'consecutivo' => $i,
									'portada' => ($i==1) ? "1" : "0" 
								));
								$iErroriErrorImagen=0;
							}
							$bSubirFoto=true;
						break;
					case "Link":
							$this->noticias_mod->GrabaLinkImagen(array (
								'id_noticia' => $asNoticia['codigoNoticia'],
								'url' =>$this->input->post("txtLink".$i),
								'leyenda' => $this->input->post("txtCaption".$i),
								'consecutivo' => $i,
								'portada' => ($i==1) ? "1" : "0" 
								));
						break;
				} //Switch
			} //For
			
			//Display
			if (!$bErrorDB) {
				$asPrincipal=array (
					'RUTA_RAIZ' => base_url(),
					'ID_NOTICIA' => $this->input->post('hdnIdNoticia')
				);
				$asControlador= array ('CONTENIDO' => $this->load->view('admin/noticia_guardada_vw', $asPrincipal, true),
					'TIPO_ACCESO' => 'ADMIN' );
				$this->main_lib->display($asControlador);
			}
			else {
				$asPrincipal=array (
					'MENSAJE' => $sMensajeError, 'CLASE' => "error",
					'TITULO' => "Error en captura de noticia", 'RUTA_RAIZ' => base_url()
				);
				$asControlador= array ('CONTENIDO' => $this->load->view('mensaje_vw', $asPrincipal, true),
					'TIPO_ACCESO' => 'ADMIN' );
				$this->main_lib->display($asControlador);
			}
		}
		
		function GrabaTags($psModo,$psFuente, $piIndice) {
			$asTags=explode(",",$psFuente);
			for ($i=0;$i<count($asTags);$i++)
				$asTags[$i]=trim($asTags[$i]);
			$bError=0;
			$sMensaje="";
			if (count($asTags)>1) {
				for ($i=0;$i<count($asTags);$i++) {
					switch ($psModo) {
						case 'torneo':
							$sTemporada=$this->input->post("slcTemporada");
							$asConsulta=array (
								'campo_valor' => "clave",
								'campo_clave' => "nombre", 
								'valor' =>	$asTags[$i],
								'tabla' => "torneos",
								'condicion_extra' => " AND id_temporada=".$sTemporada
							);
							$sValorTag=$sTemporada.",";
						break;
						case 'equipo':
							$asConsulta=array (
								'campo_valor' => "id_unico",
								'campo_clave' => "nombre_corto", 
								'valor' =>	$asTags[$i],
								'tabla' => "clubes"
							);
							$sValorTag="";
						break;
						case 'jugador':
							$asConsulta=array (
								'campo_valor' => "id_unico",
								'campo_clave' => "nombre", 
								'valor' =>	$asTags[$i],
								'tabla' => "jugadores"
							);
							$sValorTag="";
						break;
						case 'consejo':
							$asConsulta=array (
								'campo_valor' => "id_unico",
								'campo_clave' => "iniciales", 
								'valor' =>	$asTags[$i],
								'tabla' => "cat_consejos",
							);
							$sValorTag="";
						break;
					}
					$sCodigoTag=$this->tools_lib->consulta_rapida($asConsulta);
					if (substr($sCodigoTag,0,5)!="ERROR") {
						if ($sCodigoTag!="")
							$asSalida=$this->noticias_mod->InsertaTag($psModo, $sValorTag.$sCodigoTag, $piIndice);
					}
					else {
						$asSalida=array (
							'ESTATUS' => -1,
							'ERROR' => $sCodigoTag
						);
						var_dump($asConsulta);
					} //if
				} //for
			}  //if
			else {
				$asSalida=array ('ESTATUS' => 1, 'MENSAJE' => "nothing to do here" );
			}
			return($asSalida);
		}

		function busca ($psModo="") {
			//Checa que pagina es la que va a poner
			$iPaginaActual=($this->input->get_post('pg')) ? $this->input->get_post('pg') : 1;
			$iModo=($this->input->get_post('modo')) ? $this->input->get_post('modo') : $psModo;
			$iElementosxPagina=20;
			$iOffset=($iPaginaActual-1) * $iElementosxPagina;
			$sTabs="\t\t\t\t\t\t";
			
			$sListaCombo="";
			if (($this->input->post('txtSearch'))) {
				$sBusca=$this->input->post('txtSearch');
				$asNoticias=$this->noticias_mod->BuscaNoticia($sBusca, $iOffset, $iElementosxPagina, $iModo);
				if ($asNoticias['ESTATUS']!=-1)
					$sResultados=$this->tools_lib->genera_reporte(array ('TITULO' => "Resultados, pagina ".$iPaginaActual, 'DATOS' => $asNoticias['DATOS'], 'ANCHO' => "700"));
				else
					$sResultados=$asNoticias['ERROR'];
				$iNumElementos=$asNoticias['CONTEO_TOTAL'];
				if ($iNumElementos > $iElementosxPagina) {
					$iPaginaSiguiente=intval($iPaginaActual) + 1;
					$iPaginaAnterior=intval($iPaginaActual) - 1;
					if ($iNumElementos%$iElementosxPagina>0)
						$iTotalPaginas=intval($iNumElementos / $iElementosxPagina) + 1;
					else
						$iTotalPaginas=intval($iNumElementos / $iElementosxPagina);
					if ($iPaginaActual>1) 
						$sRegresarPagina=$sTabs."<td id=\"par\"><input type=\"button\" class=\"button\" name=\"txtPrevia\" value=\" &lt;&lt; \" OnClick=\"Ir_a_pagina(".$iPaginaAnterior.");\"/></td>\n";
					else
						$sRegresarPagina=$sTabs."<td id=\"par\"><input type=\"button\" class=\"button\" name=\"txtPrevia\" value=\" &lt;&lt; \" disabled=\"yes\" /></td>\n";
					
					if ($iPaginaActual < $iTotalPaginas) 
						$sAvanzarPagina=$sTabs."<td id=\"par\"><input type=\"button\" class=\"button\" name=\"txtAdelante\" value=\" &gt;&gt; \" OnClick=\"Ir_a_pagina(".$iPaginaSiguiente.");\"/></td>\n";
					else
						$sAvanzarPagina=$sTabs."<td id=\"par\"><input type=\"button\" class=\"button\" name=\"txtAdelante\" value=\" &gt;&gt; \" disabled=\"yes\" \"/></td>\n";
					$sUltimaPagina=$sTabs."<td id=\"par\"><input type=\"button\" class=\"button\" name=\"txtUltima\" value=\" &gt;| \" OnClick=\"Ir_a_pagina(".$iTotalPaginas.");\"/></td>\n";
					$sPrimeraPagina=$sTabs."<td id=\"par\"><input type=\"button\" class=\"button\" name=\"txtPrimera\" value=\" |&lt; \" OnClick=\"Ir_a_pagina(1);\"/></td>\n";
					for ($i=1;$i<=$iTotalPaginas;$i++) {
						if ($i==$iPaginaActual) 
							$sSelected="Selected=\"yes\"";
						else
							$sSelected="";
						$sListaCombo.=$sTabs."\t\t<option value=\"".$i."\" ".$sSelected.">".$i."</option>\n";
					}
					$sComboPaginas=$sTabs."\t<td id=\"par\">\n"
									.$sTabs."\t<select name=\"slc_salto_pagina\" >\n"
									.$sListaCombo
									.$sTabs."\t</select>\n"
									.$sTabs."\t<input type=\"button\" class=\"button\" value=\" Ir \" OnClick=\"Ir_a_pagina(document.frmBuscaNoticias.slc_salto_pagina.value);\">\n"
									.$sTabs."\t</td>\n"; 
					$sNavegacion=$sPrimeraPagina.$sRegresarPagina.$sComboPaginas.$sAvanzarPagina.$sUltimaPagina;
				}
				else {
					$iTotalPaginas=1;
					$sNavegacion="<tr><td colspan=\"5\" id=\"par\">Pagina Unica</td></tr>";
				}	
			}
			else {
				$sBusca="";
				$sNavegacion="";
				$sResultados="";
			}
			$asContenido=array (
				'NAVEGACION' => $sNavegacion, 'RUTA_RAIZ' => base_url(), 'MODO' => $iModo, 
				'TEXTO_BUSQUEDA' => $sBusca,  'RESULTADOS' => $sResultados
			);
			$asControlador=array ('CONTENIDO' => $this->load->view('admin/busca_noticia_vw', $asContenido, true), 'TIPO_ACCESO' => "PUBLIC");
			$this->main_lib->display($asControlador);
		}
		function busca_etiqueta () {
			$this->load->model(array('torneos_mod', 'clubes_mod', 'jugadores_mod', 'consejos_mod'));
			$sSearch=$this->input->get('q');
			$sCodigo=substr($sSearch,0,1);
			switch ($sCodigo) {
				case "#":
					$asResultado=$this->torneos_mod->getListaNombres(substr($sSearch,1));
					$sSalida="";
					if ($asResultado['ESTATUS']!=-1) {
						for ($i=0;$i<count($asResultado['DATOS']);$i++) {
							$sSalida.="torneo|".$asResultado['DATOS'][$i]['id_temporada'].",".$asResultado['DATOS'][$i]['clave']."|".$asResultado['DATOS'][$i]['nombre']."\n";
						}
					}
					break;
				case "$":
					$asResultado=$this->clubes_mod->getDatosPorNombre(substr($sSearch,1));
					$sSalida="";
					if ($asResultado['ESTATUS']!=-1) {
						for ($i=0;$i<count($asResultado['DATOS']);$i++) {
							$sSalida.="equipo|".$asResultado['DATOS'][$i]['id_unico']."|".$asResultado['DATOS'][$i]['nombre']."\n";
						}
					}
					break;
				case "%":
					$asResultado=$this->jugadores_mod->ListaJugadoresBusqueda(substr($sSearch,1));
					$sSalida="";
					if ($asResultado['ESTATUS']!=-1) {
						for ($i=0;$i<count($asResultado['DATOS']);$i++) {
							$sSalida.="jugador|".$asResultado['DATOS'][$i]['id_unico']."|".$asResultado['DATOS'][$i]['nombre']."\n";
						}
					}
					break;
				case "!":
					$asResultado=$this->consejos_mod->getDatosPorIniciales(substr($sSearch,1));
					$sSalida="";
					if ($asResultado['ESTATUS']!=-1) {
						for ($i=0;$i<count($asResultado['DATOS']);$i++) {
							$sSalida.="consejo|".$asResultado['DATOS'][$i]['id_unico']."|".$asResultado['DATOS'][$i]['iniciales']."\n";
						}
					}
					break;
				default:
					die ("no code");
				break;	
			}
			echo $sSalida;
		}
	}

?>