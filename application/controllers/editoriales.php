<?php

    class Editoriales extends CI_Controller {

        function __construct() {
            parent::__construct();
            $this->load->library('tools_lib');
            $this->load->model('editoriales_mod');
        }
        
        function index() {
            $this->load->model('portada_mod');
            $asEditoriales=$this->editoriales_mod->lista();
            $asBloqueEditoriales=array();
            if ($asEditoriales['ESTATUS']==1) {
                for($i=0;$i<count($asEditoriales['DATOS']);$i++) {
                    $asBloqueEditoriales[]=array(
                        'ID' => $asEditoriales['DATOS'][$i]['id_unico'],
                        'IMAGEN' => ($asEditoriales['DATOS'][$i]['imagen']!="") ? "img/columnas/".$asEditoriales['DATOS'][$i]['imagen'] : "",
                        'COLUMNA' =>$asEditoriales['DATOS'][$i]['columna'],
                        'TITULO' => $asEditoriales['DATOS'][$i]['titulo'],
                        'AUTOR' => $asEditoriales['DATOS'][$i]['autor']
                    );
                }
            }
            $asPrincipal=array(
                'BLOQUE_EDITORIALES' => $asBloqueEditoriales
            );
            $asContenido=array (
                'PRINCIPAL' => $this->parser->parse('editoriales/lista_editoriales_vw',$asPrincipal,true),
                'BARRA_DERECHA' => $this->portada_mod->banners()
            );
            $asControlador=array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'PUBLIC'
            );
            $this->main_lib->display($asControlador);
        }
        
        function ver ($psID) {
            $this->load->model('portada_mod');
            $asDatos=$this->editoriales_mod->get($psID);
            if ($asDatos['ESTATUS']==1) {
                $asComentarios=$this->editoriales_mod->comentarios_lista($psID);
                $asBloqueComentarios=array();
                if ($asComentarios['ESTATUS']==1) {
                    for($i=0;$i<count($asComentarios['DATOS']);$i++) {
                        $asBloqueComentarios[]=array (
                            'OPERADOR' =>  $asComentarios['DATOS'][$i]['operador'],
                            'FECHA' =>  $asComentarios['DATOS'][$i]['fecha_hora'],
                            'COMENTARIO' =>  $asComentarios['DATOS'][$i]['comentario']
                        );
                    }
                }
                $asPrincipal=array(
                    'IMAGEN' => ($asDatos['DATOS']['imagen']!="") ?  "<img src=\"img/columnas/".$asDatos['DATOS']['imagen']."\" />" : '' ,
                    'AUTOR' =>  $asDatos['DATOS']['autor'],
                    'COLUMNA' =>  $asDatos['DATOS']['columna'],
                    'TITULO' =>  $asDatos['DATOS']['titulo'],
                    'FECHA' =>  $asDatos['DATOS']['fecha'],
                    'CUERPO' =>  $asDatos['DATOS']['cuerpo'],
                    'ID' =>  $asDatos['DATOS']['id_unico'],
                    'BLOQUE_COMENTARIOS' =>  $asBloqueComentarios
                );
                //Hasta que realmente sea necesario, le pongo el formulario de comentarios.
                // 'FORMULARIO' => $this->load->view('editoriales/formulario_comentario_vw',array(),true)
                
                $asContenido=array (
                    'PRINCIPAL' => $this->parser->parse('editoriales/ver_editorial_vw',$asPrincipal,true),
                    'BARRA_DERECHA' => $this->portada_mod->banners()
                );
                $asControlador=array (
                    'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                    'TIPO_ACCESO' => 'PUBLIC'
                );
                $this->main_lib->display($asControlador);
            }
            else
                $this->main_lib->mensaje('Esta pagina no existe');
        }

        function lista_edicion() {
            $asEditoriales=$this->editoriales_mod->lista($this->session->userdata('sUsuario'));
            $asBloqueEditoriales=array();
            if ($asEditoriales['ESTATUS']==1) {
                for($i=0;$i<count($asEditoriales['DATOS']);$i++) {
                    $asBloqueEditoriales[]=array(
                        'ID' => $asEditoriales['DATOS'][$i]['id_unico'],
                        'IMAGEN' => ($asEditoriales['DATOS'][$i]['imagen']!="") ? "img/columnas/".$asEditoriales['DATOS'][$i]['imagen'] : "",
                        'COLUMNA' =>$asEditoriales['DATOS'][$i]['columna'],
                        'TITULO' => $asEditoriales['DATOS'][$i]['titulo'],
                        'AUTOR' => $asEditoriales['DATOS'][$i]['autor']
                    );
                }
            }
            $asPrincipal=array(
                'BLOQUE_EDITORIALES' => $asBloqueEditoriales
            );
            $asContenido=array (
                'PRINCIPAL' => $this->parser->parse('admin/lista_editoriales_vw',$asPrincipal,true),
            );
            $asControlador=array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
                'TIPO_ACCESO' => 'ADMIN'
            );
            $this->main_lib->display($asControlador);
        }



        function agrega_comentario() {
        }

        function captura($psClave="0") {
            if ($psClave=="0") {
                $sColumna='';
                $sTitulo='';
                $sTemporada='__EXTRA__';
                $sTorneo='__EXTRA__';
                $sEquipo='__EXTRA__';
                $sCuerpo='';
            }
            else {
                $asEditorial=$this->editoriales_mod->get($psClave);
                if ($asEditorial['ESTATUS']!=1) {
                    $this->main_lib->mensaje("No existe la editorial seleccionada: $psClave");
                    return;
                }
                if ($asEditorial['DATOS']['operador']!=$this->session->userdata('sUsuario')) {
                    $this->main_lib->mensaje("Esta editorial no te pertenece [".$asEditorial['DATOS']['operador']."] - [".$this->session->userdata('sUsuario')."]");
                    return;
                }
                $sColumna=$asEditorial['DATOS']['id_columna'];
                $sTitulo=$asEditorial['DATOS']['titulo'];
                $sTemporada=$asEditorial['DATOS']['id_temporada'];
                $sTorneo=$asEditorial['DATOS']['id_torneo'];
                $sEquipo=$asEditorial['DATOS']['id_equipo'];
                $sCuerpo=$asEditorial['DATOS']['cuerpo'];
            }
            $asPrincipal=array (
                'RUTA_RAIZ' => base_url(),
                'COMBO_COLUMNA' => $this->tools_lib->generacombo ( array (
                    'NOMBRE' => 'slcColumna',
                    'QUERY' => "SELECT id_unico, concat(columna,'(',nombre,')') as columna FROM cat_autores where operador='".$this->session->userdata('sUsuario')."'",
                    'CAMPO_CLAVE' => 'id_unico',
                    'LEYENDA' => 'columna',
                    'DEFAULT' => $sColumna,
                    'DB' => 'default'
                )),
                'COMBO_TEMPORADA' => $this->tools_lib->generacombo ( array (
                    'NOMBRE' => 'slcTemporada', 'TABLA' => 'temporadas', 'CAMPO_CLAVE' => 'temporada',
                    'LEYENDA' => 'nombre_corto', 'DEFAULT' => $sTemporada, 'OPCION_EXTRA' => 'Todas las temporadas',
                    'DB' => 'default'
                )),
                'COMBO_TORNEO' => $this->tools_lib->generacombo ( array (
                    'NOMBRE' => 'slcTorneo', 'TABLA' => 'torneos', 'CAMPO_CLAVE' => 'clave',
                    'LEYENDA' => 'nombre', 'DEFAULT' => $sTorneo, 'OPCION_EXTRA' => 'Sin torneo aludido',
                    'DB' => 'default'
                )),
                'COMBO_EQUIPO' => $this->tools_lib->generacombo ( array (
                    'NOMBRE' => 'slcEquipo', 'TABLA' => 'equipos', 'CAMPO_CLAVE' => 'id_unico',
                    'LEYENDA' => 'nombre_corto', 'DEFAULT' => $sEquipo, 'OPCION_EXTRA' => 'Sin equipo aludido',
                    'DB' => 'default'
                )),
                'CUERPO' => $sCuerpo,
                'ID_EDITORIAL' => $psClave,
                'TITULO_EDITORIAL' => $sTitulo,
                'MENSAJE' => $sMensaje);
            $asContenido=array (
                'PRINCIPAL' => $this->load->view('admin/captura_editorial_vw', $asPrincipal, true),
            );
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
                'TIPO_ACCESO' => 'ADMIN' );
            $this->main_lib->display($asControlador);
        }

        function graba() {
            //var_dump($_POST);
            $asEditorial=array (
                'id' => $this->input->post('hdnIdEditorial'),
                'id_columna' => $this->input->post('slcColumna'),
                'titulo' => $this->input->post('txtTitulo'),
                'id_torneo' => ($this->input->post('slcTorneo') <> '__EXTRA__') ? $this->input->post('slcTorneo') : 0,
                'id_equipo' => ($this->input->post('slcEquipo') <> '__EXTRA__') ? $this->input->post('slcEquipo') : 0,
                'id_temporada' => ($this->input->post('slcTemporada') <> '__EXTRA__') ? $this->input->post('slcTemporada') : 0,
                'cuerpo' => $this->input->post('taCuerpo'),
            );
            // Graba en la base de datos
            $bErrorDB=false;
            $sResult="";
            if ($asEditorial['id']=="0") {   //Inserta una nueva
                $asEditorial['id']=$this->tools_lib->trae_ultimo_indice(array('nombre_campo' => 'id_unico', 'tabla' => 'editoriales'));
                //var_dump($asEditorial);
                $asResultDB=$this->editoriales_mod->graba($asEditorial);
            }
            else {    //Actualiza una existente
                $asResultDB=$this->editoriales_mod->actualiza($asEditorial);
                if ($asResultDB['ESTATUS']==0) {
                    $sMensajeError.=$asResultDB['ERROR']."\n";
                    $bErrorDB=true;
                }
            }
            echo ("asResultDB:");
            //var_dump ($asResultDB);
            if ($asResultDB['ESTATUS']==0) {    //Al usar ejecutar_query solo regresa 0 o 1
                $sMensajeError.=$asResultDB['ERROR']."\n";
                $bErrorDB=true;
            }
            //Display
            if (!$bErrorDB) {
                $asPrincipal=array (
                    'RUTA_RAIZ' => base_url(),
                    'ID_EDITORIAL' => $asEditorial['id']
                );
                $asControlador= array ('CONTENIDO' => $this->load->view('admin/editorial_guardada_vw', $asPrincipal, true),
                    'TIPO_ACCESO' => 'ADMIN' );
                $this->main_lib->display($asControlador);
            }
            else {
                $asPrincipal=array (
                    'MENSAJE' => $sMensajeError, 'CLASE' => "error",
                    'TITULO' => "Error en captura de editorial", 'RUTA_RAIZ' => base_url()
                );
                $asControlador= array ('CONTENIDO' => $this->load->view('mensaje_vw', $asPrincipal, true),
                    'TIPO_ACCESO' => 'ADMIN' );
                $this->main_lib->display($asControlador);
            }
        }

    }
?>
