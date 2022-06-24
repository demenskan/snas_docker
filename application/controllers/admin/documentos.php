<?php

    class documentos extends CI_Controller {

        function __construct() {
            parent::__construct();
            $this->load->model(array('doctos_mod','torneos_mod'));
        }

        function torneo($piTemporada, $piTorneo) {
            $this->principal("torneo","$piTemporada/$piTorneo");
        }

        function principal($psTipo, $psCodigo="") {
            $sMargen="\t\t\t\t";
            $asDatos=$this->doctos_mod->Lista($psTipo, $psCodigo);
            if ($asDatos['ESTATUS']==1) {
                $asDoctos=array();
                for ($i=0;$i<count($asDatos['DATOS']);$i++) {
                    $asDoctos[]=array (
                        'ID_UNICO' => $asDatos['DATOS'][$i]['id_unico'] ,
                        'TITULO' => $asDatos['DATOS'][$i]['titulo'],
                        'AUTOR' => $asDatos['DATOS'][$i]['autor'],
                    );
                }
                if ($psTipo=="torneo") {
                    $asParams=explode("/",$psCodigo);
                    $sBarraDerecha=$this->barra_derecha($asParams[0],$asParams[1]);
                }
                else
                    $sBarraDerecha="";
                /*
                var_dump($asDoctos);
                 */
                $asPrincipal=array (
                    'SECCION' => $psTipo,
                    'BLOQUE_DOCTOS' => $asDoctos,
                );
                $asContenido=array (
                    'PRINCIPAL' => $this->parser->parse('doctos/principal_vw', $asPrincipal, true),
                    'BARRA_DERECHA' => $sBarraDerecha
                );
                $asControlador=array (
                    'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                    'TIPO_ACCESO' => 'ADMIN'
                );
                $this->main_lib->display($asControlador);

            }
            else {
                echo ($asDatos['ESTATUS']);
            }
        }

        function barra_derecha($piTemporada, $piClave) {
            /*
             */
            $asDatosTorneo=$this->torneos_mod->DatosTorneo($piTemporada, $piClave);
            $asBarraDerecha=array ('RUTA_RAIZ' => base_url(), 'ID_TEMPORADA' => $piTemporada, 'ID_TORNEO' => $piClave, 'INDEX_URI' => $this->config->item('index_uri'),
                    'LOGO_RUTA' => "<img src=\"".base_url().$asDatosTorneo['DATOS']['logotipo']."\" />", 'NOMBRE' => $asDatosTorneo['DATOS']['nombre']);
            return ($this->load->view('admin_torneos/barra_derecha_vw', $asBarraDerecha, true));
        }

        function ver($psId) {
        
        
        
        }
        /*Fin de la clase*/
    }
?>
