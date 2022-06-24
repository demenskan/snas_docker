<?php


    class Portada extends CI_Controller {

        function __construct() {
            parent::__construct();
            $this->load->library('Main_lib');
            $this->load->model('portada_mod');
        }

        function index() {
            $asContenido=array (
                'PRINCIPAL' => $this->contenido(),
                'BARRA_IZQUIERDA' => $this->barra_centro_derecha(),
                'BARRA_DERECHA' => $this->barra_extrema_derecha(),
            );

            $asControlador=array (
                'RUTA_RAIZ' => base_url(),
                'TIPO_ACCESO' => 'PUBLIC',
                'CONTENIDO' => $this->load->view('templates/simple-magazine/tres-columnas_vw', $asContenido, true),
                'WRAPPER' => "portada_wrapper_vw"
            );
            $this->main_lib->display($asControlador);
        }

        function contenido() {
            $asNoticias=$this->portada_mod->noticias();
            $asSalida=array (
                'RESUMENES' => $asNoticias['RESUMENES'],
                'ROTADOR_IMAGENES' => $asNoticias['IMAGENES'],
                'TABULADORES' => $asNoticias['TABULADORES'],
                'NOTICIAS_VIEJAS' => $asNoticias['VIEJAS'],
                'NOTICIAS_COMPLEMENTARIAS' => $asNoticias['COMPLEMENTARIAS'],
                'VIDEOS' => '', 'RUTA_RAIZ' => base_url()
            );
            return ($this->load->view("templates/".$this->main_lib->sTemplateEnUso."/portada_contenido_vw",$asSalida, true));
        }

        function barra_centro_derecha() {
            $asSalida=array (
                'BANNERS'=> $this->portada_mod->banners(),
                'EDITORIALES'=> $this->portada_mod->editoriales()
            );
            return ($this->load->view("templates/".$this->main_lib->sTemplateEnUso."/portada_centro_derecha_vw",$asSalida, true));
        }

        function barra_extrema_derecha() {
            $asSalida=array (
                'RESULTADOS' => $this->portada_mod->resultados(),
                'GOLEADORES_TEMPORADA'=> $this->portada_mod->goleadores("temporada"),
                'GOLEADORES_HISTORICOS' => $this->portada_mod->goleadores("total", "5")
            );
            return ($this->load->view("templates/".$this->main_lib->sTemplateEnUso."/portada_extrema_derecha_vw",$asSalida, true));
        }
    }
?>
