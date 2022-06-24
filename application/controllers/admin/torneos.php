<?php

    class torneos extends CI_Controller {

        function __construct() {
            parent::__construct();
            $this->load->model('torneos_mod');
        }

        function test() {
            echo "test";
        }

        function lista ($piTemporada="") {
            if ($piTemporada=="")
                $sTemporada=($this->input->post('slcTemporada')=="") ? $this->main_lib->iTemporadaActual : $this->input->post('slcTemporada');
            else
                $sTemporada=$piTemporada;
            $sMensaje=($this->input->get('msg')=="") ? "" : $this->input->get('msg');
            $asTorneos=$this->torneos_mod->ListaTorneos($sTemporada);
            $sTabs="\t\t\t";
            $sOut=$sTabs."<tr>\n";
            $iCantidadColumnas=4;
            if ($asTorneos['ESTATUS']==1) {
                for ($iCont=0;$iCont<count($asTorneos['DATOS']);$iCont++) {
                    if (($iCont%$iCantidadColumnas==0)&&($iCont!=0)){
                        $sOut.= $sTabs."</tr><!--".$iCont."%".$iCantidadColumnas."=".$iCont%$iCantidadColumnas."-->\n"
                               .$sTabs."<tr>\n";
                    }
                    if ($asTorneos['DATOS'][$iCont]['logotipo']=='')
                        $sRutaLogo=base_url()."img/torneos/SNLogo_small.gif";
                    else
                        $sRutaLogo=base_url().$asTorneos['DATOS'][$iCont]['logotipo'];
                    $sOut.= $sTabs."\t<td>\n"
                            .$sTabs."\t\t<div class=\"sidebox\">\n"
                            .$sTabs."\t\t\t<div class=\"boxbody\">\n"
                            .$sTabs."\t\t\t\t<p><a href=\"".base_url()."admin/torneos/principal/".$asTorneos['DATOS'][$iCont]['id_temporada']."/".$asTorneos['DATOS'][$iCont]['clave']."\"><img src=\"".$sRutaLogo."\"  width=\"100\"  border=\"0\"></a></p>\n"
                            .$sTabs."\t\t\t\t<p><a href=\"".base_url()."admin/torneos/principal/".$asTorneos['DATOS'][$iCont]['id_temporada']."/".$asTorneos['DATOS'][$iCont]['clave']."\">".$asTorneos['DATOS'][$iCont]['nombre']."</a></p>\n"
                            .$sTabs."\t\t\t</div>\n"
                            .$sTabs."\t\t</div>"
                            .$sTabs."\t</td>";
                }
                $sOut.=$sTabs."</tr>\n";
            }
            else
                $sOut=$asTorneos['ERROR'];
            $asPrincipal=array (
                'RUTA_RAIZ' => base_url(),
                'TABLA_TORNEOS' => $sOut,
                'LISTA_TEMPORADAS' => $this->tools_lib->generacombo ( array (
                    'NOMBRE' => 'slcTemporada', 'TABLA' => 'temporadas', 'CAMPO_CLAVE' => 'temporada',
                    'LEYENDA' => 'nombre_corto', 'DEFAULT' => $sTemporada, 'OPCION_EXTRA' => 'Todas las temporadas',
                    'DB' => 'default'
                )),
                'MENSAJE' => $sMensaje);
            $asContenido=array (
                'PRINCIPAL' => $this->load->view('admin_torneos/lista_vw', $asPrincipal, true),
            );
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
                'TIPO_ACCESO' => 'ADMIN' );
            $this->main_lib->display($asControlador);
        }   
        
    
        function principal($piTemporada, $piClave) {
            $sMargen="\t\t\t\t";
            $asDatos=$this->torneos_mod->DatosTorneo($piTemporada, $piClave);
            if ($asDatos['ESTATUS']==1) {
                $sRutaLogo=(isset($asDatos['DATOS']['logotipo'])) ? $asDatos['DATOS']['logotipo'] : "img/torneos/SNLogo_small.gif" ;
                $asPrincipal=array (
                    'LOGO_RUTA' => "<img src=\"".base_url().$sRutaLogo."\" width=\"100\" />",
                    'NOMBRE' => "<h2>".$asDatos['DATOS']['nombre']."</h2>",
                    'DESCRIPCION' => $asDatos['DATOS']['descripcion'],
                    'ESTATUS' => "Estatus: ".$asDatos['DATOS']['estatus'],
                    'TEMPORADA' => $piTemporada
                );
                $asContenido=array (
                    'PRINCIPAL' => $this->load->view('admin_torneos/principal_vw', $asPrincipal, true),
                    'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piClave) 
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
            $asDatosTorneo=$this->torneos_mod->DatosTorneo($piTemporada, $piClave);
            $asBarraDerecha=array ('RUTA_RAIZ' => base_url(), 'ID_TEMPORADA' => $piTemporada, 'ID_TORNEO' => $piClave, 'INDEX_URI' => $this->config->item('index_uri'),
                    'LOGO_RUTA' => "<img src=\"".base_url().$asDatosTorneo['DATOS']['logotipo']."\" />", 'NOMBRE' => $asDatosTorneo['DATOS']['nombre']);
            return ($this->load->view('admin_torneos/barra_derecha_vw', $asBarraDerecha, true));
        }

        function datos_generales ($piModo, $piTemporada=-1, $piClave=-1) {
            $sMargen="\t\t\t\t";
            if ($piModo=="nuevo") {   //Nuevo
                $sPagina="'".base_url()."admin/torneos/claves_torneos/'+document.frmNuevoTorneo.slcTemporada.value+'/'+document.frmNuevoTorneo.slcClase.value";
                $sTemporada=$this->tools_lib->GeneraCombo(
                    array ('NOMBRE' => "slcTemporada", 'TABLA' => "temporadas", 'CAMPO_CLAVE' => "temporada",
                            'LEYENDA' => "temporada", 'DEFAULT' => $this->main_lib->iTemporadaActual,
                            'EVENTOS' => "OnChange=\"JavaScript: callPage(".$sPagina.",'lista-claves','Cargando...','Error!');\""));
                $iTipoTorneo=-1;  // Opciones Default
                $sClaseTorneo=$this->tools_lib->GeneraCombo(array (
                    'NOMBRE' => "slcClase", 'TABLA' => "cat_clases_torneos", 'CAMPO_CLAVE' => "id_unico",
                    'LEYENDA' => "descripcion", 'DEFAULT' => -1,  'EVENTOS' => "OnChange=\"JavaScript: callPage(".$sPagina.",'lista-claves','Cargando...','Error!');\"")
                );
                $iEstatus="0";
                $sNombre="";
                $sDescripcion="";
                $sLogotipo="";
                $sClaveTorneo="                 <input type=\"hidden\" name=\"hdnClave\" value=\"null\" />\n"
                            ."                  <div id=\"lista-claves\">\n"
                            ."                  </div>\n";
                $sCodigoAccion="nuevo";
                $sTemplate="una-columna_vw";
            }
            else {                  //edicion
                $sTemporada=$piTemporada."<input type=\"hidden\" name=\"slcTemporada\" value=\"".$piTemporada."\" />\n";
                $asTorneo=$this->torneos_mod->DatosTorneo($piTemporada,$piClave);
                switch ($asTorneo['ESTATUS']) {
                    case 1:
                        $iTipoTorneo=$asTorneo['DATOS']["tipo"];
                        $sClaseTorneo=$this->tools_lib->GeneraCombo(array (
                            'NOMBRE' => "slcClase", 'TABLA' => "cat_clases_torneos",
                            'CAMPO_CLAVE' => "id_unico", 'LEYENDA' => "descripcion",
                            'DEFAULT' => $asTorneo['DATOS']["clase"]));
                        $sNombre=" value=\"".$asTorneo['DATOS']["nombre"]."\"";
                        $sDescripcion=$asTorneo['DATOS']["descripcion"];
                        $sLogotipo=" value=\"".$asTorneo['DATOS']["logotipo"]."\"";
                        $iEstatus=$asTorneo['DATOS']["estatus"];
                        $sClaveTorneo=$piClave."<input type=\"hidden\" name=\"hdnClave\" value=\"".$piClave."\" />\n";
                        $sCodigoAccion="edicion";
                        
                    break;
                    case 0:
                        $sDescripcion="No hay";
                    break;
                    case -1:
                        $sDescripcion=$asTorneo['ERROR'];
                    break;
                }
                $sTemplate="dos-columnas_vw";
            }
            $asPrincipal=array (
                'RUTA_RAIZ' => base_url(),
                'LISTA_TEMPORADAS' => $sTemporada,
                'LISTA_TIPOS' => $this->tools_lib->GeneraCombo(array(
                        'NOMBRE' => "slcTipo", 'TABLA' => "cat_tipos_torneos", 'CAMPO_CLAVE' => "id_unico",
                        'LEYENDA' => "descripcion", 'DEFAULT' => $iTipoTorneo)),
                'LISTA_CLASES' => $sClaseTorneo,
                'LISTA_ESTATUS' => $this->tools_lib->GeneraCombo(array(
                        'NOMBRE' => "slcEstatus", 'TABLA' => "cat_estatus_torneos", 'CAMPO_CLAVE' => "id_unico",
                        'LEYENDA' => "descripcion", 'DEFAULT' => $iEstatus)),
                'NOMBRE_TORNEO' => $sNombre, 'DESCRIPCION' => $sDescripcion,
                'RUTA_LOGO' => $sLogotipo, 'MODO' => $piModo, 'CLAVE_TORNEO' => $sClaveTorneo
            );
            $asContenido=array (
                'PRINCIPAL' => $this->load->view('admin_torneos/datos_generales_vw', $asPrincipal, true)
            );
            if ($piModo=="edicion")
                $asContenido['BARRA_DERECHA']=$this->barra_derecha($piTemporada, $piClave);
            $asControlador=array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/".$sTemplate, $asContenido, true),
                'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display ($asControlador);
            /*Lo que se necesita actualizar en un torneo (clave y temporada)
                eventos
                partidos
                acomodo_grupos
                torneos =of course==
                
                arreglar: lo de rostersportemporada (id_temporada=2005)
            */
        }

    function ListaDocumentos ($piTemporada, $piTorneo) {
        $asDoctos=$this->torneos_mod->ListaDocumentos($piTemporada, $piTorneo);
        if ($asDoctos['ESTATUS']!=-1) {
            $asReporte=array();
            for ($i=0;$i<count($asDoctos['DATOS']);$i++) {
                $asReporte[]=array (
                    'ID' => $asDoctos['DATOS'][$i]['id_unico'],
                    'TITULO' => $asDoctos['DATOS'][$i]['titulo']);
                $sLista=$this->parser->parse('admin_torneos/lista_torneos_vw',$asReporte,true);
            }
        }
        $asContenido=array (
            'PRINCIPAL' =>  $this->tools_lib->genera_reporte (array ('DATOS' => $asReporte, 'TITULO' => 'Documentos', 'ANCHO' => '700')),
            'BARRA_DERECHA' => $this->Barra_derecha($piTemporada, $piTorneo),
        );
        $asControlador= array (
            'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
            'TIPO_ACCESO' => 'PUBLIC'
        );
        $this->main_lib->display($asControlador);

    }

    function DetallesDocto($piClave, $piTemporada, $piTorneo) {
        $this->load->model('doctos_mod');
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

        $asContenido=array (
            'PRINCIPAL' =>  $sSalida,
            'BARRA_DERECHA' => $this->Barra_derecha($piTemporada, $piTorneo),
        );
        $asControlador= array (
            'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
            'TIPO_ACCESO' => 'PUBLIC'
        );
        $this->main_lib->display($asControlador);

    }



        
    function edita_documento ($piModo, $piTemporada=-1, $piClave=-1) {
            $sMargen="\t\t\t\t";
            if ($piModo=="nuevo") {   //Nuevo
                $sPagina="'".base_url()."admin/torneos/claves_torneos/'+document.frmNuevoTorneo.slcTemporada.value+'/'+document.frmNuevoTorneo.slcClase.value";
                $sTemporada=$this->tools_lib->GeneraCombo(
                    array ('NOMBRE' => "slcTemporada", 'TABLA' => "temporadas", 'CAMPO_CLAVE' => "temporada",
                            'LEYENDA' => "temporada", 'DEFAULT' => $this->main_lib->iTemporadaActual,
                            'EVENTOS' => "OnChange=\"JavaScript: callPage(".$sPagina.",'lista-claves','Cargando...','Error!');\""));
                $iTipoTorneo=-1;  // Opciones Default
                $sClaseTorneo=$this->tools_lib->GeneraCombo(array (
                    'NOMBRE' => "slcClase", 'TABLA' => "cat_clases_torneos", 'CAMPO_CLAVE' => "id_unico",
                    'LEYENDA' => "descripcion", 'DEFAULT' => -1,  'EVENTOS' => "OnChange=\"JavaScript: callPage(".$sPagina.",'lista-claves','Cargando...','Error!');\"")
                );
                $iEstatus="0";
                $sNombre="";
                $sDescripcion="";
                $sLogotipo="";
                $sClaveTorneo="                 <input type=\"hidden\" name=\"hdnClave\" value=\"null\" />\n"
                            ."                  <div id=\"lista-claves\">\n"
                            ."                  </div>\n";
                $sCodigoAccion="nuevo";
                $sTemplate="una-columna_vw";
            }
            else {                  //edicion
                $sTemporada=$piTemporada."<input type=\"hidden\" name=\"slcTemporada\" value=\"".$piTemporada."\" />\n";
                $asTorneo=$this->torneos_mod->DatosTorneo($piTemporada,$piClave);
                switch ($asTorneo['ESTATUS']) {
                    case 1:
                        $iTipoTorneo=$asTorneo['DATOS']["tipo"];
                        $sClaseTorneo=$this->tools_lib->GeneraCombo(array (
                            'NOMBRE' => "slcClase", 'TABLA' => "cat_clases_torneos",
                            'CAMPO_CLAVE' => "id_unico", 'LEYENDA' => "descripcion",
                            'DEFAULT' => $asTorneo['DATOS']["clase"]));
                        $sNombre=" value=\"".$asTorneo['DATOS']["nombre"]."\"";
                        $sDescripcion=$asTorneo['DATOS']["descripcion"];
                        $sLogotipo=" value=\"".$asTorneo['DATOS']["logotipo"]."\"";
                        $iEstatus=$asTorneo['DATOS']["estatus"];
                        $sClaveTorneo=$piClave."<input type=\"hidden\" name=\"hdnClave\" value=\"".$piClave."\" />\n";
                        $sCodigoAccion="edicion";
                        
                    break;
                    case 0:
                        $sDescripcion="No hay";
                    break;
                    case -1:
                        $sDescripcion=$asTorneo['ERROR'];
                    break;
                }
                $sTemplate="dos-columnas_vw";
            }
            $asPrincipal=array (
                'RUTA_RAIZ' => base_url(),
                'LISTA_TEMPORADAS' => $sTemporada,
                'LISTA_TIPOS' => $this->tools_lib->GeneraCombo(array(
                        'NOMBRE' => "slcTipo", 'TABLA' => "cat_tipos_torneos", 'CAMPO_CLAVE' => "id_unico",
                        'LEYENDA' => "descripcion", 'DEFAULT' => $iTipoTorneo)),
                'LISTA_CLASES' => $sClaseTorneo,
                'LISTA_ESTATUS' => $this->tools_lib->GeneraCombo(array(
                        'NOMBRE' => "slcEstatus", 'TABLA' => "cat_estatus_torneos", 'CAMPO_CLAVE' => "id_unico",
                        'LEYENDA' => "descripcion", 'DEFAULT' => $iEstatus)),
                'NOMBRE_TORNEO' => $sNombre, 'DESCRIPCION' => $sDescripcion,
                'RUTA_LOGO' => $sLogotipo, 'MODO' => $piModo, 'CLAVE_TORNEO' => $sClaveTorneo
            );
            $asContenido=array (
                'PRINCIPAL' => $this->load->view('admin_torneos/documentos_vw', $asPrincipal, true)
            );
            if ($piModo=="edicion")
                $asContenido['BARRA_DERECHA']=$this->barra_derecha($piTemporada, $piClave);
            $asControlador=array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/".$sTemplate, $asContenido, true),
                'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display ($asControlador);
            /*Lo que se necesita actualizar en un torneo (clave y temporada)
                eventos
                partidos
                acomodo_grupos
                torneos =of course==
                
                arreglar: lo de rostersportemporada (id_temporada=2005)
            */
        }
        function graba ($piModo) {
            /*$sTemporada=$_REQUEST["slcTemporada"];
            $iClase=$_REQUEST["slcClase"];
            $iClave=$_REQUEST["hdnClave"];
            $iTipo=$_REQUEST["slcTipo"];
            $sNombre=$_REQUEST["txtNombre"];
            $sDescripcion=$_REQUEST["taDescripcion"];
            $sTipoLogo=$_REQUEST["rbTipoLogo"];
            $sRutaGaleria=$_REQUEST["txtLogoGaleria"];
            $sDescripcion=str_replace("\n", "</br>\n", $sDescripcion);*/
            $sMaxSize=$_POST["MAX_FILE_SIZE"]; 
            $asDatos=array (
                'temporada' =>$this->input->post('slcTemporada'),   'clase' =>$this->input->post('slcClase'),
                'clave' =>$this->input->post('hdnClave'),           'tipo' =>$this->input->post('slcTipo'),
                'nombre' =>$this->input->post('txtNombre'),         'descripcion' =>str_replace("\n", "<br/>\n", $this->input->post('taDescripcion')),
                'tipo_logo' =>$this->input->post('rbTipoLogo'),     'ruta_galeria' =>$this->input->post('txtLogoGaleria'),
                'max_size' =>$this->input->post('MAX_FILE_SIZE'),   'estatus' => $this->input->post('slcEstatus'),
                'file_name' => $_FILES["fLogo"]["name"], //---Nombre del archivo en la maquina del usuario
                'file_type' => $_FILES["fLogo"]["type"],  //--- Mime type
                'file_size' => $_FILES["fLogo"]["size"] //--- tamano
            );
            if ($asDatos['tipo_logo']=="archivo") {
                //---Comprueba los datos
                if (!(strpos($asDatos['file_type'], "gif") || strpos($asDatos['file_type'], "jpeg") || strpos($asDatos['file_type'], "png"))) {
                    $iError=1;
                }
                elseif ($asDatos['file_size'] > $sMaxSize)
                    $iError=2;
                else {
                    //---- Define la ruta fisica a donde depositar el archivo
                    if (move_uploaded_file($_FILES["fLogo"]["tmp_name"],$this->main_lib->sRutaLogosTorneos."/".$asDatos['file_name'])) {
                        $iError=0;
                    }
                    else {
                        $iError=3;
                    }
                }
            }
            else //Seleccion desde galeria
                $iError=0;
            switch ($iError) {
            case 0:     //Todo ok con el archivo. Graba todo en la base de datos
                    if ($piModo=="nuevo") 
                        $asResult=$this->torneos_mod->insertaTorneo($asDatos);
                    else
                        $asResult=$this->torneos_mod->actualizaTorneo($asDatos);
                    if ($asResult['ESTATUS']==1) 
                        $asContenido=array ('CLASE' => 'success', 'TITULO' => "Nuevo Torneo", 'MENSAJE' => "Todo OK");
                    else
                        $asContenido=array ('CLASE' => 'error', 'TITULO' => "Nuevo Torneo", 'MENSAJE' => $asResult['ERROR']);
                    break;
            case 1: 
                    $asContenido=array ('CLASE' => 'error', 'TITULO' => "Nuevo Torneo", 'MENSAJE' => "Error de tipo de imagen: ".var_dump($_FILES));
                    break;
            case 2: 
                    $asContenido=array ('CLASE' => 'error', 'TITULO' => "Nuevo Torneo", 'MENSAJE' => "Error de tama&ntilde;o: ".var_dump($_FILES));
                    break;
            case 3: 
                    $asContenido=array ('CLASE' => 'error', 'TITULO' => "Nuevo Torneo", 'MENSAJE' => "Error desconocido: ".var_dump($_FILES));
                    echo ($_FILES["fLogo"]["error"]);
                    break;
            }
            $asContenido['RUTA_RAIZ']=base_url();
            $asPrincipal=array ('PRINCIPAL' => $this->load->view('mensaje_vw', $asContenido, true));
            $asControlador=array ('CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asPrincipal, true),
                                    'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display($asControlador);
        }
        function claves_torneos($piTemporada, $piClase) {
            $sMargen="\t\t\t\t";
            $iClaveMaxima=$this->tools_lib->consulta_rapida(array ('campo_valor' => "clave_maximo", 'campo_clave' => "id_unico", 'tabla' => "cat_clases_torneos", 'valor' => $piClase));
            $iClaveMinima=$this->tools_lib->consulta_rapida(array ('campo_valor' => "clave_minimo", 'campo_clave' => "id_unico", 'tabla' => "cat_clases_torneos", 'valor' => $piClase));
            $asRangoClaves=$this->torneos_mod->getRangosClaves($piTemporada, $iClaveMinima, $iClaveMaxima);
            if ($asRangoClaves['ESTATUS']!=-1) {
                $iCont=0;
                $iTemporadaAnt=$piTemporada-1;
                $iTemporadaAnt2=$piTemporada-2;
                for ($iCont=$iClaveMinima;$iCont<=$iClaveMaxima;$iCont++) {
                    $sDisponible="<a href=\"JavaScript: SeleccionaClave(".$iCont.");\"><font color=\"green\">Si</font></a>";
                    $sNombreTemporadaAnt="";
                    $sNombreTemporadaAnt2="";
                    for ($i=0;$i<count($asRangoClaves['DATOS']);$i++) {
                        if (($asRangoClaves['DATOS'][$i]["id_temporada"]==$piTemporada) && ($asRangoClaves['DATOS'][$i]["clave"]==$iCont))
                            $sDisponible="<font color=\"red\">no (".$asRangoClaves['DATOS'][$i]["nombre"].")</font>";
                        if (($asRangoClaves['DATOS'][$i]["id_temporada"]==($piTemporada-1)) && ($asRangoClaves['DATOS'][$i]["clave"]==$iCont))
                            $sNombreTemporadaAnt=$asRangoClaves['DATOS'][$i]["nombre"];
                        if (($asRangoClaves['DATOS'][$i]["id_temporada"]==($piTemporada-2)) && ($asRangoClaves['DATOS'][$i]["clave"]==$iCont))
                            $sNombreTemporadaAnt2=$asRangoClaves['DATOS'][$i]["nombre"];
                            
                    }
                    $asReporte[$iCont-$iClaveMinima]=array (
                        'TX CLAVE' => $iCont,
                        'TX DISPONIBLE' => $sDisponible,
                        'TX USO TEMPORADA '.$iTemporadaAnt => $sNombreTemporadaAnt,
                        'TX USO TEMPORADA '.$iTemporadaAnt2 => $sNombreTemporadaAnt2
                    );
                }
                $sSalida=$this->tools_lib->genera_reporte(array ('TITULO' => 'Claves Disponibles', 'DATOS' => $asReporte, 'ANCHO' => "700"));
            }
            else
                $sSalida=$asRangoClaves['ERROR'];
            echo ($sSalida);
        }
        function selecciona_logo () {
            $sLista="";
            $sClase="";
            $sTabs="\t\t\t\t\t\t";
            //** Lo que no se quiere mostrar
            $asExcepciones=array(
                ".",
                "..",
                ".DS_Store",
                "Thumbs.db");
    
            if ($handle = opendir('img/torneos')) {
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
                            .$sTabs."\t<td><img src=\"".base_url()."img/torneos/".$file."\" width=\"100\" /></td>\n"
                            .$sTabs."\t<td><a href=\"JavaScript: Selecciona('img/torneos/".$file."');\">".$file."</a></td>\n"
                            .$sTabs."</tr>";
                    }
                }
                closedir($handle);
            }
            else
                $sLista="Error";
            $asContenido=array ('LOGOTIPOS' => $sLista);    
            echo ($this->load->view('admin_torneos/selecciona_logo_vw', $asContenido, true));
        }
    
        function grupos ($piTemporada, $piClave) {
            $asPrincipal=array (
                    'LISTA_EQUIPOS' => $this->tools_lib->GeneraCombo(array ('NOMBRE' => "slcEquipo", 'TABLA' => "equipos", 'CAMPO_CLAVE' => "id_unico", 'LEYENDA' => "nombre_corto")),
                    'TABLA_GRUPOS' => $this->lista_grupos($piTemporada, $piClave),
                    'TEMPORADA' => $piTemporada,'RUTA_RAIZ' => base_url(), 
                    'TORNEO' => $piClave,
                );
            $asContenido=array (
                'PRINCIPAL' => $this->load->view('admin_torneos/captura_grupos_vw', $asPrincipal, true),
                'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piClave)
            );
            $asControlador=array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'ADMIN'
            );
            $this->main_lib->display($asControlador);
        }
        
        function lista_grupos ($piTemporada, $piClave) {
            $asGrupos=$this->torneos_mod->getGruposTorneo($piTemporada, $piClave);
            if ($asGrupos['ESTATUS']!=-1) {
                $sGrupoActual="";
                $sEquiposAgrupados="";
                $sRowId="";
                $asReporte=array();
                for ($i=0;$i<count($asGrupos['DATOS']);$i++) {
                    $asReporte[$i]=array (
                        'IN 1' => "GRUPO ".$asGrupos['DATOS'][$i]['grupo'],
                        'TX 2' => "<img src=\"".base_url()."img/escudos/mini/s".$asGrupos['DATOS'][$i]['ruta_logo'].".gif\" />",
                        'TX 3' => $asGrupos['DATOS'][$i]['nombre_corto'],
                        'TX 4' => "<input type=\"button\" onclick=\"callPage('".base_url()."admin/torneos/quita_club_grupo/"
                                .$piTemporada."/".$piClave."/".$asGrupos['DATOS'][$i]['id_unico']."/"
                                .$asGrupos['DATOS'][$i]['grupo']."','lista-equipos-capturados','Cargando...','Error@Carga')\" "
                                ."class=\"button\" value=\"Borrar\" />"
                    );
                }
                return ($this->tools_lib->genera_reporte(array('DATOS' => $asReporte, 'TITULO' => "Ordenamiento de grupos", 'AGRUPAR_POR' => 'IN 1' , 'NOMBRES_CAMPOS' => false )));
            }
            else
                return ($asGrupos['ERROR']);
            $this->load->view('admin_torneos/captura_grupos_vw', $asPrincipal, true);
        }
        
        
        function agrega_club_grupo ($piTemporada, $piClave,  $piClub, $psGrupo) {
            $asResult=$this->torneos_mod->InsertaClubGrupo($piTemporada, $piClave, $piClub, $psGrupo);
            if ($asResult['ESTATUS']!=-1)   
                echo ($this->lista_grupos($piTemporada, $piClave));
            else
                echo ($asResult['ERROR']);
        }
        
        function quita_club_grupo ($piTemporada,$piClave,$piClub, $psGrupo) {
            $asResult=$this->torneos_mod->BorraClubGrupo($piTemporada, $piClave, $piClub, $psGrupo);
            if ($asResult['ESTATUS']!=-1)   
                echo ($this->lista_grupos($piTemporada, $piClave));
            else
                echo ($asResult['ERROR']);
        }
        
        function calendario($piTemporada, $piClave) {
            $sMargen="\t\t\t\t";
            $asParticipantes=$this->torneos_mod->getParticipantes($piTemporada, $piClave);
            $sListaEquipos="";
            for  ($i=0;$i<count($asParticipantes['DATOS']);$i++) {
                $sComa=($i==0) ? "" : "," ;
                $sListaEquipos.=$sComa."\n\t'".$asParticipantes['DATOS'][$i]['nombre_corto']."'";
            }
            $asPrincipal=array (
                'LISTA_TIPOS_PARTIDOS' => $this->tools_lib->GeneraCombo(array ('NOMBRE' => "slcTipo", 'TABLA' => "cat_tipos_partido", 'CAMPO_CLAVE' => "id_unico", 'LEYENDA' => "descripcion", 'DEFAULT' => "1")),
                'TABLA_PARTIDOS' => $this->lista_partidos($piTemporada, $piClave),
                'LISTA_EQUIPOS' => $sListaEquipos,
                'TEMPORADA' => $piTemporada, 'TORNEO' => $piClave, 'RUTA_RAIZ' => base_url()
            );
            $asContenido=array ('PRINCIPAL' => $this->load->view('admin_torneos/captura_calendario_vw', $asPrincipal, true),
                                'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piClave));
            $asControlador=array ('CONTENIDO' => $this->load->view('templates/'.$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                                'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display($asControlador);
        }
    
        function lista_partidos ($piTemporada, $piClaveTorneo, $piJornada=-1) {
            $asCalendario=$this->torneos_mod->getCalendario($piTemporada, $piClaveTorneo, $piJornada);
            $asDatosReporte=array();
            if ($asCalendario['ESTATUS']!=-1) {
                for ($i=0;$i<count($asCalendario['DATOS']);$i++) {
                    $asDatosReporte[$i]=array (
                        'IN 1' => $asCalendario['DATOS'][$i]['jornada'],
                        'TX 2' => "<img src=\"".base_url()."img/escudos/mini/s".$asCalendario['DATOS'][$i]['LogoLocal'].".gif\" />",
                        'TX 3' => $asCalendario['DATOS'][$i]['NombreLocal'],
                        'TX 4' => ($asCalendario['DATOS'][$i]['jugado']==1) ? $asCalendario['DATOS'][$i]['marcador_local'].":".$asCalendario['DATOS'][$i]['marcador_visitante'] : "",
                        'TX 5' => $asCalendario['DATOS'][$i]['NombreVisitante'],
                        'TX 6' => "<img src=\"".base_url()."img/escudos/mini/s".$asCalendario['DATOS'][$i]['LogoVisitante'].".gif\" />",
                        'TX 7' => $asCalendario['DATOS'][$i]['Estadio'],
                        'TX 8' => "<a href=\"".base_url()."admin/torneos/cambia_datos_partido/".$piTemporada."/".$piClaveTorneo."/".$asCalendario['DATOS'][$i]['clave']."\">Cambiar Datos</a>",
                        'TX 9' => ($asCalendario['DATOS'][$i]['jugado']==0) ? "<a href=\"".base_url()."admin/torneos/confirma_borrar_partido/".$piTemporada."/".$piClaveTorneo."/".$asCalendario['DATOS'][$i]['clave']."\">Eliminar Partido</a>" : "Partido jugado"
                    );
                }
                return ($this->tools_lib->genera_reporte (array ('TITULO' => "", 'DATOS' => $asDatosReporte, 'ANCHO' => 700, 'NOMBRES_CAMPOS' => false, 'AGRUPAR_POR' => 'IN 1')));
            }   
            else
                return ($asCalendario['ERROR']);
        }
        function agrega_partido ($piTemporada, $piClaveTorneo, $piJornada, $psNombreClubLocal, $psNombreClubVisita, $piTipo) {
            $this->load->model(array ('clubes_mod', 'partidos_mod'));
            $asClubLocal=$this->clubes_mod->getDatosPorNombre($psNombreClubLocal);
            $iClaveLocal=$asClubLocal['DATOS'][0]['id_unico'];
            $asClubVisita=$this->clubes_mod->getDatosPorNombre($psNombreClubVisita);
            $iClaveVisita=$asClubVisita['DATOS'][0]['id_unico'];
            $sMargen="\t\t\t\t\t\t";
            $asResult=$this->partidos_mod->inserta($piTemporada, $piClaveTorneo, $piJornada, $iClaveLocal, $iClaveVisita, $piTipo); 
            if ($asResult['ESTATUS']==1) {
                echo ($this->lista_partidos($piTemporada, $piClaveTorneo, $piJornada));
            }
            else
                echo ($asResult['ERROR']);
        }
        function cambia_datos_partido ($piTemporada, $piTorneo, $piClave) {
            $this->load->model('partidos_mod');
            $asDatosPartido=$this->partidos_mod->getDatos($piTemporada, $piTorneo, $piClave);
            $asParticipantes=$this->torneos_mod->getParticipantes($piTemporada, $piTorneo);
            if ($asDatosPartido['ESTATUS']) {
                $asPrincipal=array (
                    'LISTA_ESTADIOS' => $this->tools_lib->GeneraCombo(array ('NOMBRE' => "slcEstadio", 'TABLA' => "cat_estadios", 'CAMPO_CLAVE' => "id_unico", 'LEYENDA' => "nombre", 'DEFAULT' => $asDatosPartido['DATOS']['id_estadio'])),
                    'NOMBRE_CLUB_LOCAL' => $this->tools_lib->GeneraCombo (array ('NOMBRE' => "slcEquipoLocal", 'DATASET' => $asParticipantes, 'DEFAULT' => $asDatosPartido['DATOS']['IDLocal'])),
                    'NOMBRE_CLUB_VISITANTE' => $this->tools_lib->GeneraCombo (array ('NOMBRE' => "slcEquipoVisita", 'DATASET' => $asParticipantes, 'DEFAULT' => $asDatosPartido['DATOS']['IDVisitante'])),
                    'LISTA_TIPOS_PARTIDO' => $this->tools_lib->GeneraCombo (array ('NOMBRE' => "slcTiposPartido", 'TABLA' => "cat_tipos_partido", 'CAMPO_CLAVE' => "id_unico", 'LEYENDA' => "descripcion", 'DEFAULT' => $asDatosPartido['DATOS']['TipoPartido'])),
                    'LOGO_CLUB_LOCAL' => base_url()."img/escudos/mini/s".$asDatosPartido['DATOS']['LogoLocal'].".gif",
                    'LOGO_CLUB_VISITANTE' => base_url()."img/escudos/mini/s".$asDatosPartido['DATOS']['LogoVisitante'].".gif",
                    'ID_TEMPORADA' => $piTemporada, 'ID_TORNEO' => $piTorneo, 'ID_PARTIDO' => $piClave,
                    'JORNADA' => $asDatosPartido['DATOS']['jornada'],
                    'TEMPORADA' => $this->tools_lib->consulta_rapida(array('campo_valor' => 'nombre_corto', 'tabla' => 'temporadas', 'campo_clave' => 'temporada', 'valor' => $piTemporada)),
                    'TORNEO' => $this->tools_lib->consulta_rapida(array('campo_valor' => 'nombre', 'tabla' => 'torneos', 'campo_clave' => 'id_temporada', 'valor' => $piTemporada, 'condicion_extra' => "AND clave=".$piTorneo)),
                    'RUTA_RAIZ' => base_url()
                );
                $sPrincipal=$this->load->view('admin_torneos/captura_datos_partido_vw', $asPrincipal, true);
            }   
            else {
                $asMensaje=array ('TITULO' => "Error", 'CLASE' => "error", 'MENSAJE' => $asDatosPartido['ERROR']);
                $sPrincipal=$this->load->view('mensaje_vw', $asMensaje, true);
            }
            $asContenido=array ('PRINCIPAL' => $sPrincipal,
                            'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piTorneo));
            $asControlador=array ('CONTENIDO' => $this->load->view('templates/'.$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                            'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display($asControlador);
        }
        
        function actualiza_datos_partido () {
            $this->load->model("partidos_mod");
            $asPost=array (
                'EQUIPO_LOCAL' => $this->input->post('slcEquipoLocal'),
                'EQUIPO_VISITA' => $this->input->post('slcEquipoVisita'),
                'TIPO' => $this->input->post('slcTiposPartido'),
                'ESTADIO' => $this->input->post('slcEstadio'),
                'JORNADA' => $this->input->post('txtJornada'),
                'ID_TEMPORADA' => $this->input->post('hdnTemporada'),
                'ID_TORNEO' => $this->input->post('hdnTorneo'),
                'ID_PARTIDO' => $this->input->post('hdnPartido')
            );
            $asResult=$this->partidos_mod->actualizaDatosGenerales($asPost);
            if ($asResult['ESTATUS']==1) 
                redirect("admin/torneos/calendario/".$asPost['ID_TEMPORADA']."/".$asPost['ID_TORNEO']);
            else {
                $asError=array ('MENSAJE' => $asResult['ERROR'], 'TITULO' => 'Error cambiando datos partido', 'CLASE' => "Error" );
                $this->main_lib->send_message ($asError);
            }
        }
        
        function confirma_borrar_partido ($piTemporada, $piTorneo, $piPartido) {
            $asPrincipal=array(
                'TITULO' => "Borrar Partido",
                'TEXTO' => "Desea usted borrar el partido?",
                'ESTILO' => "notice",
                'BLOQUE_BOTONES' => array (
                    array ('CAPTION' => "Aceptar", 'LINK' => base_url().$this->config->item('index_uri')."admin/torneos/borrapartido/".$piTemporada."/".$piTorneo."/".$piPartido),
                    array ('CAPTION' => "Cancelar", 'LINK' => base_url().$this->config->item('index_uri')."admin/torneos/calendario/".$piTemporada."/".$piTorneo)
                )
            );
            $asContenido=array('PRINCIPAL' => $this->parser->parse("pregunta_simple_vw",$asPrincipal,true),
                                'BARRA_DERECHA' => $this->barra_derecha($piTemporada,$piTorneo));
            $asControlador=array ('CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                                    'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display($asControlador);
        }

        function borrapartido ($piTemporada, $piTorneo, $piPartido) {
            $this->load->model('partidos_mod');
            $asEventos=$this->partidos_mod->getEventos($piTemporada, $piTorneo, $piPartido);
            $sMensaje="";
            if ($asEventos['ESTATUS']==1) {
                for ($i=0;$i<count($asEventos['DATOS']);$i++){
                    $sResult=$this->partidos_mod->elimina_evento($piTemporada,$piTorneo,$piPartido,$asEventos['DATOS'][$i]['id_relativo']);
                    if ($sResult['ESTATUS']!=1)
                        $sMensaje.="<div class=\"error\">".$sResult['ERROR']."</div>\n";
                }
            }
            if ($sMensaje=="") {
                $asResultado=$this->partidos_mod->elimina($piTemporada,$piTorneo,$piPartido);
                if ($asResultado['ESTATUS']==1)
                    $this->calendario($piTemporada,$piTorneo);
                else
                    $this->main_lib->mensaje($asResultado['ERROR']);
            }
            else
                $this->main_lib->mensaje($sMensaje);
        }
        
        function resultados ($piTemporada, $piTorneo, $piJornada=1) {
            $sTabs="\t\t\t\t";
            $sTablaJornadas="";
            $asCalendario=$this->torneos_mod->getCalendario($piTemporada, $piTorneo, $piJornada);
            $asDatosReporte=array();
            if ($asCalendario['ESTATUS']!=-1) {
                for ($i=0;$i<count($asCalendario['DATOS']);$i++) {
                    $sLeyendaEventos=($asCalendario['DATOS'][$i]['jugado']==1) ? "Editar eventos" : "Capturar eventos";
                    $sLeyendaAlineaciones=($asCalendario['DATOS'][$i]['alineaciones']>0) ? "Editar alineaciones" : "Capturar alineaciones";
                    $sLeyendaDesignados=($asCalendario['DATOS'][$i]['designado_local']!="") ? "Editar designados" : "Capturar designados";
                    $asDatosReporte[$i]=array (
                        'IN 1' => "Jornada ".$asCalendario['DATOS'][$i]['jornada'],
                        'TX 2' => "<img src=\"".base_url()."img/escudos/mini/s".$asCalendario['DATOS'][$i]['LogoLocal'].".gif\" />",
                        'TX 3' => $asCalendario['DATOS'][$i]['NombreLocal'],
                        'TX 4' => ($asCalendario['DATOS'][$i]['jugado']==1) ? $asCalendario['DATOS'][$i]['marcador_local'].":".$asCalendario['DATOS'][$i]['marcador_visitante'] : "",
                        'TX 5' => $asCalendario['DATOS'][$i]['NombreVisitante'],
                        'TX 6' => "<img src=\"".base_url()."img/escudos/mini/s".$asCalendario['DATOS'][$i]['LogoVisitante'].".gif\" />",
                        'TX 7' => $asCalendario['DATOS'][$i]['Estadio'],
                        'TX 8' => "<a href=\"".base_url()."admin/torneos/captura_partido/".$piTemporada."/".$piTorneo."/".$asCalendario['DATOS'][$i]['clave']."\">".$sLeyendaEventos."</a>",
                        'TX 9' => "<a href=\"".base_url()."admin/torneos/titulares_cambios/".$piTemporada."/".$piTorneo."/".$asCalendario['DATOS'][$i]['clave']."\">".$sLeyendaAlineaciones."</a>",
                        'TX 10' => "<a href=\"".base_url()."admin/torneos/captura_designados/".$piTemporada."/".$piTorneo."/".$asCalendario['DATOS'][$i]['clave']."\">".$sLeyendaDesignados."</a>"
                    );
                }
                $sLista=$this->tools_lib->genera_reporte (array ('TITULO' => "Captura de Resultados", 'DATOS' => $asDatosReporte, 'ANCHO' => 700, 'NOMBRES_CAMPOS' => false, 'AGRUPAR_POR' => 'IN 1'));
            }   
            else
                $sLista=$asCalendario['ERROR'];
            $asContJornada=$this->torneos_mod->getMaxJornadas($piTemporada, $piTorneo);
            for ($i=1;$i<=$asContJornada['DATOS']['MaxJornada'];$i++)
                $sTablaJornadas.=$sTabs."<td><a href=\"".base_url()."admin/torneos/resultados/".$piTemporada."/".$piTorneo."/".$i."\">".$i."</a></td>\n";
            $asPrincipal=array (
                'LISTA_JORNADAS' => $sTablaJornadas,
                'TABLA_PARTIDOS' => $sLista
            );
            $asContenido=array ('PRINCIPAL' => $this->load->view('admin_torneos/lista_partidos_vw', $asPrincipal, true),
                                'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piTorneo));
            $asControlador=array ('CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                                    'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display($asControlador);
        }
        
        function captura_partido($piTemporada, $piTorneo, $piClave) {
            $this->load->model('partidos_mod');
            $asListaJugadores=array();
            $asListaLocales=array();
            $iContLocales=0;
            $asListaVisitantes=array();
            $iContVisitantes=0;
            $asJugadores=$this->torneos_mod->getJugadoresPartido($piTemporada, $piTorneo, $piClave);
            $sListaJugadores="";
            if ($asJugadores['ESTATUS']==1) {
                for ($i=0;$i<count($asJugadores['DATOS']);$i++){
                    $asListaJugadores[]=array (
                        'CLAVE' => $asJugadores['DATOS'][$i]['ID_NUMBER'],
                        'NOMBRE' => $asJugadores['DATOS'][$i]['Nombre'],
                        'NUMERO' => $asJugadores['DATOS'][$i]['numero'],
                        'INICIALES' => $asJugadores['DATOS'][$i]['iniciales'],
                        'ESTATUS' =>$asJugadores['DATOS'][$i]['estatus'],
                        'COMA' => ($i==(count($asJugadores['DATOS'])-1)) ? "" : ","
                    );
                    if ($asJugadores['DATOS'][$i]['estatus']=='L') {
                        $asListaLocales[]=array (
                            'ID_JUGADOR' => $asJugadores['DATOS'][$i]['ID_NUMBER'],
                            'NOMBRE_JUGADOR' => $asJugadores['DATOS'][$i]['Nombre'],     
                            'NUMERO' => $iContLocales + 1
                        );
                        $iContLocales++;
                    }
                    else {
                        $asListaVisitantes[]=array (
                            'ID_JUGADOR' => $asJugadores['DATOS'][$i]['ID_NUMBER'],
                            'NOMBRE_JUGADOR' => $asJugadores['DATOS'][$i]['Nombre'],     
                            'NUMERO' => $iContVisitantes + 1
                        );
                        $iContVisitantes++;                     
                    }
                    
                }
            }
            else
                $asListaJugadores[]=array();
            if ($piClave>1) {
                $sPartidoAnterior=$this->partidos_mod->getCampo('fecha_jugado', $piTemporada, $piTorneo, $piClave-1);
                if (($sPartidoAnterior!='Vacio') && ($sPartidoAnterior!='Error!'))
                    if ($sPartidoAnterior!="0000-00-00 00:00:00")
                        $sFechaJuego=$sPartidoAnterior;
                    else
                        $sFechaJuego=date("Y-m-d");
                else
                    $sFechaJuego=date("Y-m-d");
            }
            else
                $sFechaJuego=date("Y-m-d");

            $asPartido=$this->partidos_mod->getDatosExtendido($piTemporada, $piTorneo, $piClave);
            if ($asPartido['ESTATUS']==1) {
                $asPrincipal=array(
                    'RUTA_RAIZ' => base_url(),
                    'INICIALES_LOCAL' => $asPartido['DATOS']['iniciales_local'],
                    'INICIALES_VISITA' => $asPartido['DATOS']['iniciales_visita'],
                    'LOGO_LOCAL' => $asPartido['DATOS']['logo_local'],
                    'LOGO_VISITA' => $asPartido['DATOS']['logo_visita'],
                    'NOMBRE_LOCAL' => $asPartido['DATOS']['nombre_local'],
                    'NOMBRE_VISITA' => $asPartido['DATOS']['nombre_visita'],
                    'MARCADOR_LOCAL' => $asPartido['DATOS']['marcador_local'],
                    'MARCADOR_VISITA' => $asPartido['DATOS']['marcador_visitante'],
                    'NOMBRE_TORNEO' => $asPartido['DATOS']['nombre_torneo'],
                    'ESTADIO' => $asPartido['DATOS']['nombreestadio'],
                    'COMBO_EVENTOS' => $this->tools_lib->GeneraCombo(
                                        array ('NOMBRE' => "slcEvento", 'TABLA' => "cat_tipos_evento", 'CAMPO_CLAVE' => "id_unico",
                                                'LEYENDA' => "listacombo" )
                                        ),
                    'BLOQUE_LISTA_JUGADORES' => $asListaJugadores,
                    'BLOQUE_LOCALES' => $asListaLocales,
                    'TOTAL_LOCALES' => $iContLocales,
                    'BLOQUE_VISITANTES' => $asListaVisitantes,
                    'TOTAL_VISITANTES' => $iContVisitantes,
                    'TEMPORADA' => $piTemporada,
                    'TORNEO' => $piTorneo,
                    'PARTIDO' => $piClave,
                    'JORNADA' => $asPartido['DATOS']['jornada'],
                    'FECHA_JUEGO' => $sFechaJuego,
                    'EVENTOS' => $this->listado_eventos($piTemporada,$piTorneo,$piClave)
                );
                $sClaveLocal= $asPartido['DATOS']['id_equipo_local'];
                $sClaveVisita= $asPartido['DATOS']['id_equipo_visitante'];
            }
            else
                $this->main_lib->mensaje($asPartido['MENSAJE']);
            $asContenido=array ('PRINCIPAL' => $this->parser->parse('admin_torneos/captura_partidos_vw', $asPrincipal, true),
                                'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piTorneo));
            $asControlador=array ('CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                                    'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display($asControlador);
        }
        
        function captura_designados($piTemporada, $piTorneo, $piClave) {
            $this->load->model('partidos_mod');
            $asPartido=$this->partidos_mod->getDatosExtendido($piTemporada, $piTorneo, $piClave);
            $asPrincipal=array(
                'RUTA_RAIZ' => base_url(),
                'INICIALES_LOCAL' => $asPartido['DATOS']['iniciales_local'],
                'INICIALES_VISITA' => $asPartido['DATOS']['iniciales_visita'],
                'LOGO_LOCAL' => $asPartido['DATOS']['logo_local'],
                'LOGO_VISITA' => $asPartido['DATOS']['logo_visita'],
                'NOMBRE_LOCAL' => $asPartido['DATOS']['nombre_local'],
                'NOMBRE_VISITA' => $asPartido['DATOS']['nombre_visita'],
                'MARCADOR_LOCAL' => $asPartido['DATOS']['marcador_local'],
                'MARCADOR_VISITA' => $asPartido['DATOS']['marcador_visitante'],
                'NOMBRE_TORNEO' => $asPartido['DATOS']['nombre_torneo'],
                'TEMPORADA' => $piTemporada,
                'TIPO' => '',
                'TORNEO' => $piTorneo,
                'PARTIDO' => $piClave,
                'JORNADA' => $asPartido['DATOS']['jornada'],
                'COMBO_DESIGNADO_LOCAL' => $this->tools_lib->GeneraCombo(
                    array(
                    'NOMBRE' => "slcDesignadoLocal",
                    'TABLA' => "operadores",
                    'CAMPO_CLAVE' => "Login",
                    'LEYENDA' => "Login" ,
                    'OPCION_EXTRA' => "Sin definir",
                    'DEFAULT' => ($asPartido['DATOS']['designado_local']=="") ? "__EXTRA__" : $asPartido['DATOS']['designado_local']
                    )
                ),
                'COMBO_DESIGNADO_VISITANTE' => $this->tools_lib->GeneraCombo(
                    array(
                    'NOMBRE' => "slcDesignadoVisitante",
                    'TABLA' => "operadores",
                    'CAMPO_CLAVE' => "Login",
                    'LEYENDA' => "Login" ,
                    'OPCION_EXTRA' => "Sin definir",
                    'DEFAULT' => ($asPartido['DATOS']['designado_local']=="") ? "__EXTRA__" : $asPartido['DATOS']['designado_visita']
                    )
                )
                
            );
            $asContenido=array ('PRINCIPAL' => $this->parser->parse('admin_torneos/captura_designados_vw', $asPrincipal, true),
                                'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piTorneo));
            $asControlador=array ('CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                                    'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display($asControlador);
            
        }
        
        function titulares_cambios ($piTemporada, $piTorneo, $piClave) {
            $this->load->model('partidos_mod');
            $asListaLocales=array();
            $asComboLocales=array('DATOS' => array());
            $iContLocales=0;
            $asListaVisitantes=array();
            $asComboVisitantes=array('DATOS' => array());
            $iContVisitantes=0;
            $asJugadores=$this->torneos_mod->getJugadoresPartido($piTemporada, $piTorneo, $piClave);
            $asAlineaciones=$this->partidos_mod->getAlineacionesTitulares($piTemporada, $piTorneo, $piClave);
            //var_dump($asAlineaciones);
            if ($asJugadores['ESTATUS']==1) {
                for ($i=0;$i<count($asJugadores['DATOS']);$i++){
                    if ($asAlineaciones['ESTATUS']==1)
                        $bAlineado=$this->BuscaJugador($asJugadores['DATOS'][$i]['ID_NUMBER'],$asAlineaciones['DATOS']);
                    else
                        $bAlineado=false;
                    if ($asJugadores['DATOS'][$i]['estatus']=='L') {
                        $asComboLocales['DATOS'][]=array(
                            '0' => $asJugadores['DATOS'][$i]['ID_NUMBER'],
                            '1' => $asJugadores['DATOS'][$i]['Nombre']
                        );
                        $asListaLocales[]=array (
                            'ID_JUGADOR' => $asJugadores['DATOS'][$i]['ID_NUMBER'],
                            'NOMBRE_JUGADOR' => $asJugadores['DATOS'][$i]['Nombre'],     
                            'NUMERO' => $iContLocales + 1,
                            'CHECKED' => ($bAlineado==true) ? "checked=\"checked\"" : ""
                        );
                        $iContLocales++;
                    }
                    else {
                        $asComboVisitantes['DATOS'][]=array(
                            '0' => $asJugadores['DATOS'][$i]['ID_NUMBER'],
                            '1' => $asJugadores['DATOS'][$i]['Nombre']
                        );
                        $asListaVisitantes[]=array (
                            'ID_JUGADOR' => $asJugadores['DATOS'][$i]['ID_NUMBER'],
                            'NOMBRE_JUGADOR' => $asJugadores['DATOS'][$i]['Nombre'],     
                            'NUMERO' => $iContVisitantes + 1,
                            'CHECKED' => ($bAlineado==true) ? "checked=\"checked\"" : ""
                        );
                        $iContVisitantes++;                     
                    }
                }
            }
            else {
                echo ("No hay jugadores definidos para este partido!");
            }
            
            $asPartido=$this->partidos_mod->getDatosExtendido($piTemporada, $piTorneo, $piClave);
            if ($asPartido['ESTATUS']==1) {
                $sComboJugadorSaleLocal="";
                $sComboJugadorSaleVisitante="";
                $sComboJugadorEntraLocal="";
                $sComboJugadorEntraVisitante="";
                $sVariableCambios="";
                $sTablaCambios="";
                //Averigua si hay cambios
                $asCambios=$this->partidos_mod->getAlineacionesCambios($piTemporada, $piTorneo, $piClave);
                if ($asCambios['ESTATUS']==1) {
                    $asBloqueCambios=array();
                    for ($j=0;$j<count($asCambios['DATOS']);$j++) {
                        $asBloqueCambios[]=array (
                            'ESCUDO_CLUB' =>  $asCambios['DATOS'][$j]['ruta_logo'],
                            'NOMBRE_SALE' => $asCambios['DATOS'][$j]['nombre_sale'],                          
                            'NOMBRE_ENTRA' =>  $asCambios['DATOS'][$j]['nombre_entra'],
                            'MINUTO' => $asCambios['DATOS'][$j]['min_entra'],
                            'NUMERO' =>  $j
                        );
                        $sComa=($j==0) ? "" : ",\n";
                        $sVariableCambios.= $sComa
                            ."      { \n"
                            ."          escudo: '".$asCambios['DATOS'][$j]['ruta_logo']."',\n"
                            ."          nombre_sale: '".$asCambios['DATOS'][$j]['nombre_sale']."',\n"
                            ."          nombre_entra: '".$asCambios['DATOS'][$j]['nombre_entra']."',\n"
                            ."          id_sale: '".$asCambios['DATOS'][$j]['entra_por']."',\n"
                            ."          id_entra: '".$asCambios['DATOS'][$j]['id_jugador']."',\n"
                            ."          minuto: '".$asCambios['DATOS'][$j]['min_entra']."'\n"
                            ."      }";
                    }
                    $sTablaCambios=$this->parser->parse('admin_torneos/lista_cambios_vw', array('BLOQUE_CAMBIOS' => $asBloqueCambios), true);
                }
                $sDuracion=(isset($asPartido['DATOS']['duracion']))? $asPartido['DATOS']['duracion'] : "";
                $sComboJugadorSaleLocal=$this->tools_lib->GeneraCombo(array(
                                        'NOMBRE' => "slcJugadorSaleLocal",
                                        'ID' => "select-sale-local",
                                        'DATASET' => $asComboLocales
                                        ));
                $sComboJugadorEntraLocal=$this->tools_lib->GeneraCombo(array(
                                        'NOMBRE' => "slcJugadorEntraLocal",
                                        'ID' => "select-entra-local",
                                        'DATASET' => $asComboLocales
                                        ));
                $sComboJugadorSaleVisitante=$this->tools_lib->GeneraCombo(array(
                                        'NOMBRE' => "slcJugadorSaleVisitante",
                                        'ID' => "select-sale-visitante",
                                        'DATASET' => $asComboVisitantes
                                        ));
                $sComboJugadorEntraVisitante=$this->tools_lib->GeneraCombo(array(
                                        'NOMBRE' => "slcJugadorEntraVisitante",
                                        'ID' => "select-entra-visitante",
                                        'DATASET' => $asComboVisitantes
                                        ));


                $asPrincipal=array(
                    'RUTA_RAIZ' => base_url(),
                    'INICIALES_LOCAL' => $asPartido['DATOS']['iniciales_local'],
                    'INICIALES_VISITA' => $asPartido['DATOS']['iniciales_visita'],
                    'LOGO_LOCAL' => $asPartido['DATOS']['logo_local'],
                    'LOGO_VISITA' => $asPartido['DATOS']['logo_visita'],
                    'NOMBRE_LOCAL' => $asPartido['DATOS']['nombre_local'],
                    'NOMBRE_VISITA' => $asPartido['DATOS']['nombre_visita'],
                    'NOMBRE_TORNEO' => $asPartido['DATOS']['nombre_torneo'],
                    'ESTADIO' => $asPartido['DATOS']['nombreestadio'],
                    'BLOQUE_LOCALES' => $asListaLocales,
                    'TOTAL_LOCALES' => $iContLocales,
                    'BLOQUE_VISITANTES' => $asListaVisitantes,
                    'TOTAL_VISITANTES' => $iContVisitantes,
                    'TEMPORADA' => $piTemporada,
                    'TORNEO' => $piTorneo,
                    'PARTIDO' => $piClave,
                    'VARIABLE_ARRAY_CAMBIOS' => $sVariableCambios,
                    'JORNADA' => $asPartido['DATOS']['jornada'],
                    'COMBO_JUGADOR_SALE_LOCAL' => $sComboJugadorSaleLocal,
                    'COMBO_JUGADOR_ENTRA_LOCAL' => $sComboJugadorEntraLocal,
                    'COMBO_JUGADOR_SALE_VISITANTE' => $sComboJugadorSaleVisitante,
                    'COMBO_JUGADOR_ENTRA_VISITANTE' => $sComboJugadorEntraVisitante,
                    'TABLA_CAMBIOS' => $sTablaCambios,
                    'DURACION' => $sDuracion
                );
                $sClaveLocal= $asPartido['DATOS']['id_equipo_local'];
                $sClaveVisita= $asPartido['DATOS']['id_equipo_visitante'];
            }
            else
                $this->main_lib->mensaje($asPartido['MENSAJE']);
            $asContenido=array ('PRINCIPAL' => $this->parser->parse('admin_torneos/captura_titulares_cambios_vw', $asPrincipal, true),
                                'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piTorneo));
            $asControlador=array ('CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                                    'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display($asControlador);
        }   
    
        
        function procesa_titulares_cambios($psTitulares, $psCambios, $psDuracion, $piTemporada, $piTorneo, $piPartido, $piJornada) {
            $this->load->model('partidos_mod');
            $this->partidos_mod->elimina_alineacion(array(
                            'TEMPORADA' => $piTemporada,
                            'TORNEO' => $piTorneo,
                            'PARTIDO' => $piPartido));
            $asTitulares=explode("_",$psTitulares);
            //Procesa a los titulares. Por default todos comienzan en el minuto 0 y terminan al final
            for ($i=0;$i<count($asTitulares)-1;$i++) {
                $this->partidos_mod->inserta_alineacion(array(
                    'TEMPORADA' => $piTemporada,
                    'TORNEO' => $piTorneo,
                    'PARTIDO' => $piPartido,
                    'JUGADOR' => $asTitulares[$i],
                    'MIN_ENTRA' => 0,
                    'MIN_SALE' => $psDuracion ));
            }
            $asCambios=explode("__A__", $psCambios);
            //-Procesa los cambios 
            for ($i=0;$i<count($asCambios)-1;$i++) {
                
                $asElementos=explode("_",$asCambios[$i]);
                //Inserta el que entra, 
                $asResInsercion=$this->partidos_mod->inserta_alineacion(array(
                    'TEMPORADA' => $piTemporada,
                    'TORNEO' => $piTorneo,
                    'PARTIDO' => $piPartido,
                    'JUGADOR' => $asElementos[1],
                    'MIN_ENTRA' => $asElementos[2],
                    'MIN_SALE' => $psDuracion,
                    'ENTRA_POR' => $asElementos[0]));
                //var_dump($asResInsercion);
                //Modifica el que sale
                $asResModificacion=$this->partidos_mod->modifica_alineacion(array(
                    'TEMPORADA' => $piTemporada,
                    'TORNEO' => $piTorneo,
                    'PARTIDO' => $piPartido,
                    'JUGADOR' => $asElementos[0],
                    'MIN_SALE' => $asElementos[2],
                    'SALE_POR' => $asElementos[1]));
                //var_dump($asResModificacion);
            }
            $this->partidos_mod->modifica_duracion($piTemporada, $piTorneo, $piPartido, $psDuracion);
            redirect("admin/torneos/resultados/".$piTemporada."/".$piTorneo."/".$piJornada);
        }
        
        function procesa_designados() {
            $this->load->model('partidos_mod');
            $iTemporada=$this->input->post('hdnTemporada');
            $iTorneo=$this->input->post('hdnTorneo');
            $iPartido=$this->input->post('hdnPartido');
            $iJornada=$this->input->post('hdnJornada');
            $sDesignadoLocal=($this->input->post('slcDesignadoLocal') <> "__EXTRA__") ? "'".$this->input->post('slcDesignadoLocal')."'" : "NULL";
            $sDesignadoVisitante=($this->input->post('slcDesignadoVisitante') <> "__EXTRA__") ? "'".$this->input->post('slcDesignadoVisitante')."'" : "NULL";
            $asResultado=$this->partidos_mod->designados_update($iTemporada, $iTorneo, $iPartido, $sDesignadoLocal, $sDesignadoVisitante);
            redirect("admin/torneos/resultados/".$iTemporada."/".$iTorneo."/".$iJornada);
        }
        
        function agrega_evento ($piTemporada, $piTorneo, $piPartido, $piJugador, $piMinuto, $piTipo) {
            $this->load->model('partidos_mod');
            $iJugador=substr($piJugador,0,strpos($piJugador,"_"));
            $iEstatus=substr($piJugador,strpos($piJugador,"_")+1);
            $sResult=$this->partidos_mod->inserta_evento($piTemporada,$piTorneo,$piPartido,$piTipo,$iJugador,$piMinuto);
            if ($sResult['ESTATUS']==1){
                $asMarcador=$this->partidos_mod->calculaMarcador($piTemporada, $piTorneo, $piPartido);
                $sResultMarcador=$this->partidos_mod->actualizaMarcador($piTemporada, $piTorneo, $piPartido, $asMarcador['LOCAL'], $asMarcador['VISITA']);
                echo ($this->listado_eventos($piTemporada, $piTorneo, $piPartido));
            }
            else
                echo ($asEventos['MENSAJE']);
        }
        
        function borra_evento ($piTemporada, $piTorneo, $piPartido, $piRelativo) {
            $this->load->model('partidos_mod');
            $sResult=$this->partidos_mod->elimina_evento($piTemporada,$piTorneo,$piPartido,$piRelativo);
            if ($sResult['ESTATUS']==1){
                $asMarcador=$this->partidos_mod->calculaMarcador($piTemporada, $piTorneo, $piPartido);
                $sResultMarcador=$this->partidos_mod->actualizaMarcador($piTemporada, $piTorneo, $piPartido,  $asMarcador['LOCAL'], $asMarcador['VISITA']);
                echo ($this->listado_eventos($piTemporada, $piTorneo, $piPartido));
            }
            else
                echo ($asEventos['MENSAJE']);
        }
        
        function listado_eventos ($piTemporada, $piTorneo, $piPartido) {
            $asPartido=$this->partidos_mod->getDatosExtendido($piTemporada, $piTorneo, $piPartido);
            if ($asPartido['ESTATUS']==1) {
                $asPrincipal=array(
                    'RUTA_RAIZ' => base_url(),
                    'LOGO_LOCAL' => $asPartido['DATOS']['logo_local'],
                    'LOGO_VISITA' => $asPartido['DATOS']['logo_visita'],
                    'NOMBRE_LOCAL' => $asPartido['DATOS']['nombre_local'],
                    'NOMBRE_VISITA' => $asPartido['DATOS']['nombre_visita'],
                    'MARCADOR_LOCAL' => $asPartido['DATOS']['marcador_local'],
                    'MARCADOR_VISITA' => $asPartido['DATOS']['marcador_visitante'],
                    'TEMPORADA' => $piTemporada,
                    'TORNEO' => $piTorneo,
                    'PARTIDO' => $piPartido,
                    'JORNADA' => $asPartido['DATOS']['jornada']
                );
                $sClaveLocal= $asPartido['DATOS']['id_equipo_local'];
                $sClaveVisita= $asPartido['DATOS']['id_equipo_visitante'];
                $asEventos=$this->partidos_mod->getEventos($piTemporada, $piTorneo, $piPartido);
                $asPrincipal['BLOQUE_EVENTOS']=array();
                if ($asEventos['ESTATUS']==1) {
                    for ($i=0;$i<count($asEventos['DATOS']);$i++) {
                        $asPrincipal['BLOQUE_EVENTOS'][]=array (
                            'COLOR' => ($i%2==0) ? "par" : "non",
                            'RUTA_LOGO' => ($asEventos['DATOS'][$i]['id_equipo']==$sClaveLocal) ? $asPartido['DATOS']['logo_local'] : $asPartido['DATOS']['logo_visita'],
                            'NOMBRE' => $asEventos['DATOS'][$i]['nombre'],
                            'MINUTO' => $asEventos['DATOS'][$i]['minuto'],
                            'IMAGEN' => $asEventos['DATOS'][$i]['imagen'],
                            'DESCRIPCION' => $asEventos['DATOS'][$i]['descripcion'],
                            'TEMPORADA' => $asEventos['DATOS'][$i]['id_temporada'],
                            'TORNEO' => $asEventos['DATOS'][$i]['id_torneo'],
                            'PARTIDO' => $asEventos['DATOS'][$i]['id_partido'],
                            'RELATIVO' => $asEventos['DATOS'][$i]['id_relativo']
                        );  
                    }
                }
                return($this->parser->parse('admin_torneos/lista_eventos_partido_vw',$asPrincipal,true));
            }
            else
                return($asPartido['MENSAJE']);
        }
        
        function cierra_partido() {
            $sComentario=$this->input->post('taComentario');
            $sComentario=str_replace("\n", "</br>\n", $sComentario);
            $sComentario=($sComentario=="") ? "Null" : "'".$sComentario."'";
            $sFechaJuego=($this->input->post('chkNoEspecificado')=="") ? $this->input->post('txtFechaJugado') : "0000-00-00 00:00:00";
            $this->load->model('partidos_mod');
            $asResult=$this->partidos_mod->cierra($sComentario, $this->input->post('hdnTemporada'), $this->input->post('hdnTorneo'), $this->input->post('hdnPartido'), $sFechaJuego);
            if ($asResult['ESTATUS']==1) {
                switch ($this->input->post('rbAccion')) {
                    case 1:
                        $iNuevaClavePartido=$this->input->post('hdnPartido')+1;
                        redirect (base_url()."admin/torneos/captura_partido/".$this->input->post('hdnTemporada')."/".$this->input->post('hdnTorneo')."/".$iNuevaClavePartido);
                        break;
                    case 2:
                        redirect (base_url()."admin/torneos/resultados/".$this->input->post('hdnTemporada')."/".$this->input->post('hdnTorneo'));
                        break;
                }
            }
            else
                $this->principal_lib->escribe_mensaje($asResult['MENSAJE']);
        }
    
        function especiales($piTemporada, $piTorneo) {
            $this->load->model('partidos_mod');
            $this->load->library('tools_lib');
            $asJornadas=$this->partidos_mod->getJornadas($piTemporada, $piTorneo);
            if ($asJornadas['ESTATUS']==1)
                $asPrincipal=array (
                    'RUTA_RAIZ' => base_url(),
                    'INDEX_URI' => $this->config->item('index_uri'),
                    'COMBO_JORNADA_MEDIA' =>  $this->tools_lib->GeneraCombo(array ('NOMBRE' => "slcJornadaMedia", 'DATASET' => $asJornadas)),
                    'COMBO_DESCANSO_INICIAL' => $this->tools_lib->GeneraCombo(array ('NOMBRE' => "slcDescansoInicial", 'DATASET' => $asJornadas)),
                    'COMBO_DESCANSO_FINAL' => $this->tools_lib->GeneraCombo(array ('NOMBRE' => "slcDescansoFinal", 'DATASET' => $asJornadas)),
                    'CLAVE_TORNEO' => $piTorneo,
                    'CLAVE_TEMPORADA' => $piTemporada
                );
            else
                $asPrincipal=array (
                    'RUTA_RAIZ' => base_url(),
                    'INDEX_URI' => $this->config->item('index_uri'),
                    'COMBO_JORNADA_MEDIA' => "Sin jornadas definidas",
                    'COMBO_DESCANSO_INICIAL' => "Sin jornadas definidas",
                    'COMBO_DESCANSO_FINAL' => "Sin jornadas definidas",
                    'CLAVE_TORNEO' => $piTorneo,
                    'CLAVE_TEMPORADA' => $piTemporada
                );
            $asContenido=array (
                'PRINCIPAL' => $this->parser->parse('admin_torneos/operaciones_especiales_vw', $asPrincipal, true),
                'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piTorneo)
            );
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'ADMIN' );
            $this->main_lib->display($asControlador);
        }

        function generador_espejo() {
            $this->load->model('partidos_mod');
            $iClaveTorneo=$this->input->post("tor");
            $iTemporada=$this->input->post("ssn");
            $iJornadaMedia=$this->input->post("slcJornadaMedia");
            $iJornadaInicial=$this->input->post("txtJornadaInicial");
            $iJornadaFinal=$this->input->post("txtJornadaFinal");
            $asTorneo=$this->torneos_mod->DatosTorneo($iTemporada,$iClaveTorneo);
            $sMensaje="";
            if ($asTorneo['ESTATUS']==1)    {
                for ($a=$iJornadaInicial;$a<=$iJornadaFinal;$a++) {
                    $asPartidosJornada=$this->torneos_mod->getPartidosJornada($iTemporada,$iClaveTorneo,($a-$iJornadaMedia));
                    if ($asPartidosJornada['ESTATUS']==1) {
                        for ($i=0;$i<count($asPartidosJornada['DATOS']);$i++) {
                            $sResult=$this->partidos_mod->inserta($iTemporada, $iClaveTorneo, $a,
                                $asPartidosJornada['DATOS'][$i]['id_equipo_visitante'],
                                $asPartidosJornada['DATOS'][$i]['id_equipo_local'],
                                $asPartidosJornada['DATOS'][$i]['tipo']);
                            if ($sResult['ESTATUS']==1) 
                                $sMensaje.="<div class=\"success\">OK ".$asPartidosJornada['DATOS'][$i]['id_equipo_local']."-"
                                        .$asPartidosJornada['DATOS'][$i]['id_equipo_visitante']."</div>";
                            else
                                $sMensaje.="<div class=\"error\">".$sResult['ERROR']."</div>";
                        }
                    }
                    else
                        $sMensaje="<div class=\"error\">".$asPartidosJornada['ERROR']."</div>";
                }
                $asPrincipal=array (
                    'TEXTO' => $sMensaje
                );
                $asContenido=array (
                    'PRINCIPAL' => $this->parser->parse('simple_vw', $asPrincipal, true),
                    'BARRA_DERECHA' => $this->barra_derecha($iTemporada, $iClaveTorneo)
                );
                $asControlador= array (
                    'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                    'TIPO_ACCESO' => 'ADMIN' );
                $this->main_lib->display($asControlador);
            }
            else 
                $this->main_lib->mensaje("No existe ese torneo");
        }
        
        function generador_descansos() {
            $iClaveTorneo=$this->input->post("tor");
            $iTemporada=$this->input->post("ssn");
            $iJornadaInicial=$this->input->post("slcDescansoInicial");
            $iJornadaFinal=$this->input->post("slcDescansoFinal");
            $sMensaje="";
            $asTorneo=$this->torneos_mod->DatosTorneo($iTemporada,$iClaveTorneo);
            if ($asTorneo['ESTATUS']==1)    {
                for ($a=$iJornadaInicial;$a<=$iJornadaFinal;$a++) {
                    $asEquiposFaltantes=$this->torneos_mod->getClubesFaltantes($iTemporada,$iClaveTorneo,$a);
                    if ($asEquiposFaltantes['ESTATUS']==1) {
                        for ($i=0;$i<count($asEquiposFaltantes['DATOS']);$i++) {
                            $asResultado=$this->torneos_mod->InsertaDescanso($iTemporada,$iClaveTorneo,$asEquiposFaltantes['DATOS'][$i]['id_equipo'],$a);
                            if ($asResultado['ESTATUS']==1) 
                                $sMensaje.="<div class=\"success\">OK: descanso jornada ".$a."</div>";
                            else
                                $sMensaje.="<div class=\"error\">".$asResultado['ERROR']."</div>";
                        }
                    }
                    else
                        $sMensaje.="<div class=\"notice\">No sobra nadie en la jornada ".$a."</div>";
                }
            }
            else
                $sMensaje="<div class=\"error\">No existe ese torneo</div>";
            
            $asPrincipal=array (
                'TEXTO' => $sMensaje
            );
            $asContenido=array (
                'PRINCIPAL' => $this->parser->parse('simple_vw', $asPrincipal, true),
                'BARRA_DERECHA' => $this->barra_derecha($iTemporada, $iClaveTorneo)
            );
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'ADMIN' );
            $this->main_lib->display($asControlador);
        }
        
        function generador_completo() {
            //Genera una tira de partidos en formato de round robin, dado un array con equipos,
            // una sola ronda, alternando local-visitante
            $this->load->model('partidos_mod');
            $iClaveTorneo=$this->input->post("tor");
            $iTemporada=$this->input->post("ssn");
            $iVueltas=$this->input->post("slcCantidadVueltas");
            $asTorneo=$this->torneos_mod->DatosTorneo($iTemporada,$iClaveTorneo);
            $sMensaje="";

            if ($asTorneo['ESTATUS']==1)    {
                $asTeamsDB=$this->torneos_mod->EquiposTorneo($iTemporada, $iClaveTorneo);
                if ($asTeamsDB['ESTATUS']==1) {
                    $asEquipos=[];
                    foreach($asTeamsDB['DATOS'] as $asClub)
                        $asEquipos[]= $asClub['clave'];
                    if (count($asEquipos)%2==1) //Son impares?
                        $asEquipos[]='*';   // * = DESCANSO
                    $asTiraA=[];
                    for($i=0;$i<(count($asEquipos)/2);$i++)
                        $asTiraA[]=$asEquipos[$i];
                    $asTiraB=[];
                    for($i=(count($asEquipos)-1);$i>=(count($asEquipos)/2);$i--)
                        $asTiraB[]=$asEquipos[$i];
                    $asPartidos=[];
                    $iCantJornadas=($iVueltas==2) ? ((count($asEquipos)-1)*2) : (count($asEquipos)-1);
                    for ($iJornada=1;$iJornada<=$iCantJornadas;$iJornada++) {
                        //Rota Tiras, dejando la posicion 0 de A como fijo
                        $sUltimo_A=$asTiraA[count($asTiraA)-1];
                        for ($j=(count($asTiraA)-1);$j>0;$j--)
                            $asTiraA[$j]=$asTiraA[$j-1];
                        $asTiraA[1]=$asTiraB[0];
                        for ($j=0;$j<(count($asTiraB)-1);$j++)
                            $asTiraB[$j]=$asTiraB[$j+1];
                        $asTiraB[(count($asTiraB)-1)]=$sUltimo_A;
                        for($j=0;$j<count($asTiraA);$j++) {
                            //Verifica que no haya alguien con descanso
                            if (($asTiraA[$j]!='*')&&($asTiraB[$j]!='*')) {
                                if ($iJornada%2==1)
                                    $asPartido= [ 'local' => $asTiraA[$j], 'visita' => $asTiraB[$j] ];
                                else
                                    $asPartido= [ 'local' => $asTiraB[$j], 'visita' => $asTiraA[$j] ];
                                $sResult=$this->partidos_mod->inserta($iTemporada, $iClaveTorneo, $iJornada,$asPartido['local'],$asPartido['visita'],1);
                                if ($sResult['ESTATUS']==1)
                                    $sMensaje.="<div class=\"success\">OK ".$asPartido['local']."-"
                                    .$asPartido['visita']."</div>";
                                else
                                    $sMensaje.="<div class=\"error\">".$sResult['ERROR']."</div>";
                            }
                            else {
                                $iClaveDescanso=($asTiraA[$j]!='*') ? $asTiraA[$j] : $asTiraB[$j];
                                $asResultado=$this->torneos_mod->InsertaDescanso($iTemporada,$iClaveTorneo,$iClaveDescanso,$iJornada);
                                if ($asResultado['ESTATUS']==1)
                                    $sMensaje.="<div class=\"success\">bye ".$iClaveDescanso."</div>";
                                else
                                    $sMensaje.="<div class=\"error\">".$asResultado['ERROR']."</div>";
                            }


                        }
                    }
                }
                else
                    $this->main_lib->mensaje("No hay equipos definidos");

                $asPrincipal=array (
                    'TEXTO' => $sMensaje
                );
                $asContenido=array (
                    'PRINCIPAL' => $this->parser->parse('simple_vw', $asPrincipal, true),
                    'BARRA_DERECHA' => $this->barra_derecha($iTemporada, $iClaveTorneo)
                );
                $asControlador= array (
                    'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                    'TIPO_ACCESO' => 'ADMIN' );
                $this->main_lib->display($asControlador);
            }
            else
                $this->main_lib->mensaje("No existe ese torneo");
        }
        
        function BuscaJugador($piClave, $pasListaAlineaciones) {
            $bFound=false;
            for ($i=0;$i<count($pasListaAlineaciones);$i++) {
                if ($pasListaAlineaciones[$i]['id_jugador']==$piClave)
                    $bFound=true;
            }
            return($bFound);
        }

        function reporte_avances($piTemporada, $piTorneo) {
            $this->load->model('partidos_mod');
            $iMaxJornada=$this->torneos_mod->MaximoJornadas($piTemporada, $piTorneo);
            $asBloque=array();
            for ($i=1;$i<=$iMaxJornada;$i++) {
                $asPartidosJornada=$this->torneos_mod->getPartidosJornada($piTemporada, $piTorneo, $i);
                $iPartidosAlineaciones=0;
                if ($asPartidosJornada['ESTATUS']==1) {
                    for ($j=0;$j<count($asPartidosJornada['DATOS']);$j++) {
                        $asAlineaciones=$this->partidos_mod->getAlineacionesTitulares($piTemporada, $piTorneo, $asPartidosJornada['DATOS'][$j]['clave']);
                        if ($asAlineaciones['ESTATUS']==1)
                            $iPartidosAlineaciones++;
                    }
                    
                }
                $asPartidosJugados=$this->torneos_mod->PartidosJugados($piTemporada, $piTorneo,$i,$i);
                $asPartidosDesignados=$this->torneos_mod->partidos_designados_get($piTemporada, $piTorneo,$i);
                
                $asBloque[]=array(
                    'JORNADA' => $i,
                    'PARTIDOS' => ($asPartidosJornada['ESTATUS']==1) ? count($asPartidosJornada['DATOS']) : 0,
                    'EVENTOS' => ($asPartidosJugados['ESTATUS']==1) ? count($asPartidosJugados['DATOS']) : 0,
                    'DESIGNADOS' => ($asPartidosDesignados['ESTATUS']==1) ? count($asPartidosDesignados['DATOS']) : 0,
                    'ALINEACIONES' => $iPartidosAlineaciones,
                    'NOTICIA' => ":",
              );
            }
            
            
            $asPrincipal=array (
                'BLOQUE' => $asBloque
            );
            $asContenido=array ('PRINCIPAL' => $this->parser->parse('admin_torneos/reporte_avances_vw', $asPrincipal, true),
                                'BARRA_DERECHA' => $this->barra_derecha($piTemporada, $piTorneo));
            $asControlador=array ('CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                                    'TIPO_ACCESO' => 'ADMIN');
            $this->main_lib->display($asControlador);
            
            
            
        }
    
    /*Fin de la clase*/
    }
?>
