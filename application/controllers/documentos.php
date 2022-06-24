<?php

class Documentos extends CI_Controller {

    public $asClavesEquipos;

    function __construct() {
        parent::__construct();
        $this->load->model(array('torneos_mod','doctos_mod'));
        $this->load->library('tools_lib');
    }

    function ListaTorneo($psCodigo) {
        $asDatos=$this->doctos_mod->Lista('torneo', $psCodigo);
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
            $asPrincipal=array (
                'SECCION' => $psTipo,
                'BLOQUE_DOCTOS' => $asDoctos,
            );
            $asContenido=array (
                'PRINCIPAL' => $this->parser->parse('doctos/listado_publico_vw', $asPrincipal, true),
                'BARRA_DERECHA' => $sBarraDerecha
            );
            $asControlador=array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'PUBLIC'
            );
            $this->main_lib->display($asControlador);

        
    }


    function Ver($piClave) {
        $asContenido=array (
            'PRINCIPAL' => $this->Principal($piClave),
            'BARRA_DERECHA' => $this->barra_derecha($piClave),
        );
        $asControlador= array (
            'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
            'TIPO_ACCESO' => 'PUBLIC'
        );
        $this->main_lib->display($asControlador);
    }

    function Principal($piClave) {
        $asDocto=$this->doctos_mod->VerDocto($piClave);
        if ($asDocto['ESTATUS']==1) {
            $asContenido=array (
                'TITULO' => $asDocto['DATOS']['titulo'],
                'CONTENIDO' => $asDocto['DATOS']['texto'],
                'RUTA_RAIZ' => base_url()
            );
            $sSalida=$this->load->view('ver_docto_vw', $asContenido, true);
        }
        else
            $sSalida=$this->load->view('mensaje_vw', array ('MENSAJE' => 'No existe un documento con esta clave', 'CLASE' => 'Notice'), true);
        return($sSalida);
    }

    function Barra_derecha($piClave) {
        $asDocto=$this->doctos_mod->VerDocto($piClave);
        if ($asDocto['ESTATUS']==1) {
            if ($asDocto['DATOS']['tipo']=='torneo') {
                $asTemporadaClave=explode('/',$asDocto['DATOS']['codigo']);
                $iTemporada=$asTemporadaClave[0];
                $iClaveTorneo=$asTemporadaClave[1];
                //$sSalida=$this->load->view('ver_docto_vw', $asContenido, true);
                $asTorneo=$this->torneos_mod->DatosTorneo($iTemporada, $iClaveTorneo);
                if ($asTorneo['ESTATUS']==1) {
                    $asContenido=array (
                            'LOGO_RUTA' => base_url().$asTorneo['DATOS']['logotipo'],
                            'RUTA_RAIZ' => base_url(),
                            'NOMBRE' => $asTorneo['DATOS']['nombre'],
                            'ID_TEMPORADA' => $iTemporada,
                            'ID_TORNEO' => $iClaveTorneo
                        );
                    if ($asTorneo['DATOS']['logotipo']=='') $asContenido['LOGO_RUTA']=base_url()."img/torneos/SNLogo.gif";
                    $sOpciones="";
                    $sOpciones.=$this->AgregaOpcion('Documentos', 'Documentos', $iTemporada, $iClaveTorneo);
                    $sOpciones.=$this->AgregaOpcion('TablaGeneral', 'Tabla General', $iTemporada, $iClaveTorneo);
                    $sOpciones.=$this->AgregaOpcion('TablaGrupos', 'Tabla Grupos', $iTemporada, $iClaveTorneo);
                    $sOpciones.=$this->AgregaOpcion('Playoffs', 'Playoffs', $iTemporada, $iClaveTorneo);
                    $sOpciones.=$this->AgregaOpcion('Calendario', 'Calendario', $iTemporada, $iClaveTorneo);
                    $sOpciones.=$this->AgregaOpcion('CalendarioEquipo', 'Calendario por Equipo', $iTemporada, $iClaveTorneo);
                    $sOpciones.=$this->AgregaOpcion('CalendarioGrupo', 'Calendario por Grupos', $iTemporada, $iClaveTorneo);
                    $sOpciones.=$this->AgregaOpcion('Goleo', 'Goleo', $iTemporada, $iClaveTorneo);
                    $sOpciones.=$this->AgregaOpcion('Tarjetas', 'Tarjetas', $iTemporada, $iClaveTorneo);
                    $sOpciones.=$this->AgregaOpcion('Lista','Lista', $iTemporada, '');
                    $asContenido['OPCIONES']=$sOpciones;
                    //$sSalida=print_r($asContenido,true);
                    $sSalida=$this->load->view('barra_derecha_torneos_vw',$asContenido, true);
                }
                else
                    $sSalida="No hay torneos con este codigo";
                return ($sSalida);
            }
        }
        else
            $sSalida=$this->load->view('mensaje_vw', array ('MENSAJE' => 'No existe un documento con esta clave', 'CLASE' => 'Notice'), true);
        return ($sSalida);

        /*
        }
        else
        return ('');
         */
    }

    function AgregaOpcion($psModulo, $psNombre, $psTemporada, $psClaveTorneo) {
        if ($psModulo!='Lista')
            return ("\t\t\t\t\t<li><a href=\"".base_url()."torneos/".$psModulo."/".$psTemporada."/".$psClaveTorneo."\">".$psNombre."</a></li>\n");
        else
            return ("\t\t\t\t\t<li><a href=\"".base_url()."torneos/lista/".$psTemporada."\">".$psNombre."</a></li>\n");
    }
}

?>
