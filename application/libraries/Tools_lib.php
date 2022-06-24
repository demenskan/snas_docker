<?php

    class Tools_lib {
    
        private $CI;
    
        function __construct() {
            $this->CI=& get_instance();
        }
            
        function GeneraCombo ($pasEntradas) {
            /*
                --ENTRADAS OBLIGATORIAS
                *   NOMBRE: El nombre del ComboBox
                
                --DATOS
                *   QUERY: Un Query de SQL con al menos 2 campos, el primero es la clave y el segundo la leyenda            O
                *   DATASET: Un array relacional con los datos a poner                                                      O   
                *   TABLA|CAMPO_CLAVE|LEYENDA|[CONDICIONES]
                
                --OPCIONALES
                *   OPCION_EXTRA: Una opcion fuera de los datos. El valor de la opcion sera '__EXTRA__' mientras lo que se especifique en el parametro es lo que sale
                *   DEFAULT:    Que opcion entra por default
                *   EVENTOS:    Eventos y funciones de Javascript
                *   ID:         Un id para Javascript y los CSS
            */
            $vCampoClave=(isset($pasEntradas['clave'])) ? $pasEntradas['clave'] : 0 ;
            $vLeyenda=(isset($pasEntradas['leyenda'])) ? $pasEntradas['leyenda'] : 1 ;
            $sHtmlId=(isset($pasEntradas['ID'])) ? " id=\"".$pasEntradas['ID']."\"" : "";
            $sEventos=(isset($pasEntradas['EVENTOS'])) ? $pasEntradas['EVENTOS'] : "";
            $sSalida="  <select name=\"".$pasEntradas['NOMBRE']."\"".$sHtmlId." ".$sEventos.">\n";
            if (isset($pasEntradas['OPCION_EXTRA']))
                $sSalida.="\t\t<option value=\"__EXTRA__\" selected=\"selected\">".$pasEntradas["OPCION_EXTRA"]."</option>\n";
            if (!isset($pasEntradas['DATASET'])) {
                $asTabla=$this->consulta($pasEntradas);
                if ($asTabla['ESTATUS']==1) {
                    for ($i=0;$i<count($asTabla['DATOS']);$i++) {
                        if (isset($pasEntradas['DEFAULT']))
                            if ($pasEntradas['DEFAULT']==$asTabla['DATOS'][$i][$pasEntradas["CAMPO_CLAVE"]])
                                $sSeleccionado="selected=\"selected\"";
                            else
                                $sSeleccionado="";
                        else
                            $sSeleccionado="";
                        $sSalida.="\t\t<option value=\"".$asTabla['DATOS'][$i][$pasEntradas["CAMPO_CLAVE"]]."\" ".$sSeleccionado.">".$asTabla['DATOS'][$i][$pasEntradas["LEYENDA"]]."</option>\n";
                    }
                    $sSalida.=" </select>\n";
                }   
                else
                    $sSalida=$asTabla['ERROR'];
            }
            else {
                $asTabla=$pasEntradas['DATASET']['DATOS'];
                for ($i=0;$i<count($asTabla);$i++) {
                    if (isset($pasEntradas['DEFAULT']))
                        if ($pasEntradas['DEFAULT']==$asTabla[$i][$vCampoClave])
                            $sSeleccionado="selected=\"selected\"";
                        else
                            $sSeleccionado="";
                    else
                        $sSeleccionado="";
                    $sSalida.="\t\t<option value=\"".$asTabla[$i][$vCampoClave]."\" ".$sSeleccionado.">".$asTabla[$i][$vLeyenda]."</option>\n";
                }
                $sSalida.=" </select>\n";
            }
            return ($sSalida);
        }
    
    
        function regresa_tabla ($pasOpciones) {
            $sCondiciones=isset($pasOpciones['condiciones']) ? " WHERE ".$pasOpciones['condiciones'] : "" ;
            $sOrden=isset($pasOpciones['orden']) ? " ORDER BY ".$pasOpciones['orden'] : "" ;
            if (isset($pasOpciones["DB"]))
                $oConn=$this->CI->load->database($pasOpciones["DB"], true);
            else
                $oConn=$this->CI->load->database($this->CI->session->userdata('APP_DB'), true);
            $sQy="SELECT ".$pasOpciones['campo_clave'].", ".$pasOpciones['leyenda']." FROM ".$pasOpciones['tabla'].$sCondiciones.$sOrden;
            if ($rRec=$oConn->query($sQy)) {
                $iCont=0;
                foreach ($rRec->result_array() as $aoFila) {
                    $asSalida[$iCont]=$aoFila;
                    $iCont++;
                }
                $rRec->free_result();
            }
            else 
                $asSalida=array (
                        'ESTATUS' => -1,
                        'MENSAJE' =>  mysql_errno().":".mysql_error()
                );
                
            return ($asSalida);
        }
        
        function consulta ($pasOpciones) {
            $bCamposNumericos=(isset($pasOpciones['CAMPOS_NUMERICOS'])) ? $pasOpciones['CAMPOS_NUMERICOS'] : true;
            //var_dump($this->CI->session->userdata('APP_DB'));
            if (!isset($pasOpciones['UNICA_FILA']))
                $pasOpciones['UNICA_FILA']=false;
            if (isset($pasOpciones['DB']))
                $oConn=$this->CI->load->database($pasOpciones['DB'], true);
            else
                $oConn=$this->CI->load->database('default', true);
            if (isset($pasOpciones['QUERY'])) 
                $sQy=$pasOpciones['QUERY'];
            else {
                $sCondiciones=isset($pasOpciones['CONDICIONES']) ? " WHERE ".$pasOpciones['CONDICIONES'] : "" ;
                $sOrden=isset($pasOpciones['ORDEN']) ? " ORDER BY ".$pasOpciones['ORDEN'] : "" ;
                $sQy="SELECT ".$pasOpciones['CAMPO_CLAVE'].", ".$pasOpciones['LEYENDA']." FROM ".$pasOpciones['TABLA'].$sCondiciones.$sOrden;
            }
            $oConn->query("SET NAMES 'utf8'");
            
            if ($rRec=$oConn->query($sQy)) {
                if (!$pasOpciones['UNICA_FILA']) {
                    $iCont=0;
                    foreach ($rRec->result_array() as $aoFila) {
                        $asDatos[$iCont]=$aoFila;
                        if ($bCamposNumericos==true) {
                            //Seccion para que devuelva tambien en formato numerico los campos
                            $iContCampos=0;
                            foreach ($aoFila as $key => $value) {
                                $asDatos[$iCont][$iContCampos]=$value;
                                $iContCampos++;
                            }
                        }
                        $iCont++;
                    }
                    if ($iCont==0)
                        $asSalida= array( 'ESTATUS' => 0 , 'MENSAJE'=>'VACIO', 'DATOS' => array());
                    else
                        $asSalida= array( 'ESTATUS' => 1 , 'MENSAJE'=>'OK', 'DATOS' => $asDatos, 'CONTEO' => $iCont);
                }
                else {
                    if ($rRec->num_rows()>0) {
                        $asDatos=$rRec->row_array();
                        $asSalida= array( 'ESTATUS' => 1 , 'MENSAJE'=>'OK', 'DATOS' => $asDatos);
                    }
                    else {
                        $asSalida['MENSAJE']='VACIO';
                        $asSalida= array( 'ESTATUS' => 0 , 'MENSAJE'=>'VACIO', 'DATOS' => array());
                    }
                }
                $rRec->free_result();
            }
            else { 
                $asSalida=array (
                        'ESTATUS'   => -1, 
                        'MENSAJE' => "Error en la base" ,
                        'ERROR' => mysql_errno().": ".mysql_error()."\n".$sQy
                );
            }   
            $asSalida['QUERY']=$sQy;
            return ($asSalida);
        }
        
        function ejecutar_query ($pasOpciones) {
            if (isset($pasOpciones['DB']))
                $oConn=$this->CI->load->database($pasOpciones['DB'], true);
            else
                $oConn=$this->CI->load->database('default', true);
            $oConn->query("SET NAMES 'utf8'");  
            $sQy=$pasOpciones['QUERY'];
            if ($rRec=$oConn->query($sQy)) {
                $asSalida=array ('ESTATUS' => 1, 'MENSAJE'=>'OK', 'NUEVO_ID' => $oConn->insert_id(), 'QUERY' => $pasOpciones['QUERY']);
            }
            else 
                $asSalida=array ('ESTATUS' => 0, 'MENSAJE' => mysql_errno().": ".mysql_error()."\n".$sQy, 'QUERY' => $pasOpciones['QUERY']);
            return ($asSalida);
        }
        
        function consulta_rapida ($pasOpciones) {
            if (isset($pasOpciones['DB']))
                $sDB=$pasOpciones['DB'];
            else
                $sDB="default";
            $sCondicionExtra=(isset($pasOpciones['condicion_extra'])) ? $pasOpciones['condicion_extra'] : "" ;
            if (!isset($pasOpciones['QUERY']))  
                $sQy="SELECT ".$pasOpciones['campo_valor']." FROM ".$pasOpciones['tabla']
                    ." WHERE ".$pasOpciones['campo_clave']."='".$pasOpciones['valor']."' "
                    .$sCondicionExtra." LIMIT 0,1";
            else
                $sQy=$pasOpciones['QUERY'];
            
            $asSalida=$this->consulta(array ('QUERY' => $sQy, 'DB' => $sDB, 'UNICA_FILA' => true ));
            switch ($asSalida['ESTATUS']) {
                case 1: $sSalida=$asSalida['DATOS'][$pasOpciones['campo_valor']]; break;
                case 0: $sSalida=""; break;
                case -1: $sSalida="ERROR: ".$asSalida['ERROR']; break;
            }
            return ($sSalida);
        }   
    

        function dump ($paInput, $psLeyenda="DUMP") {
            $sSalida="<style> \n"
                ."  .Dump {   margin: 5px 5px 5px 5px;  border-spacing: 1px;  font-size: 0.8em;  background-color: #FFF6BF; } \n" 
                ."  .Dump .non { background-color: #F6EAB6;  } \n"
                ."  .Dump .par { background-color: #FFFACA; } \n "
                ."  </style>\n"
                ."<table class=\"Dump\">\n"
                ."<thead><th colspan=\"2\">".$psLeyenda."</th></thead>\n";
            $sClase="par";      
            foreach($paInput as $key => $value) {
                if (!is_array($value))
                    $sSalida.="<tr class=\"".$sClase."\"><td>".$key."</td><td>".$value."</td>\n";
                else {
                    $sSubArray="<table class=\"".$sClase."\">\n";
                    foreach ($value as $xKey => $xValue) {
                        $sSubArray.="<tr><td>".$xKey."</td><td>".$xValue."</td></tr>\n";
                    }
                    $sSubArray.="</table>";
                    $sSalida.="<tr><td>".$key."</td><td>".$sSubArray."</td>\n";
                }
                $sClase=($sClase=="par") ? "non" : "par";
            }
            $sSalida.="</table>";
            echo ($sSalida);
        }
        

        
        function alfanumerico_espacios ($psInput) {
        // Valida si $psInput solamente tiene caracteres alfabeticos, numeros y espacios. Cualquier otra cosa devuelve FALSE.
        // Si $psInput esta vacia, devuelve TRUE
            if ($psInput=="")
                return TRUE;
            else {
                if (! preg_match("/^[a-zA-Z0-9 ]+$/", $psInput)) {
                    $this->form_validation->set_message('alfanumerico_espacios', 'El campo %s solo puede contener letras, numeros y espacios.');
                    return FALSE;
                }
                else
                    return TRUE;
            }
        }
        
        function genera_reporte ($pasInput) {
        /*
            ENTRADAS:
                * QUERY|DATOS
                * [ANCHO]
                * TITULO
                * [TEXTO_SEPARADOR], [AGRUPAR_POR]
                * 
        
        */
            $sTabs="\t\t\t\t\t\t";
            $sClase="";
            if (isset($pasInput['QUERY'])) {
                $asResult=$this->regresa_tabla_query($pasInput);
                $iEstatus=$asResult['ESTATUS'];
            }
            else {
                $asResult=$pasInput['DATOS'];
                $iEstatus=(count($asResult)>0) ? 1 : 0;
            }
            if (isset($pasInput['ANCHO']))
                $sAncho=$pasInput['ANCHO'];
            else
                $sAncho="100%";
            if (!isset($pasInput['NOMBRES_CAMPOS']))
                $sNombresCampos=true;
            else
                $sNombresCampos=$pasInput['NOMBRES_CAMPOS'];
            $sSalida=$sTabs."<table class=\"Reportes\" width=\"".$sAncho."\">\n"
                    .$sTabs."   <thead>\n"
                    .$sTabs."       <tr>\n"
                    .$sTabs."           <th colspan=\"99\">".$pasInput['TITULO']."</th>\n"
                    .$sTabs."       </tr>\n"
                    .$sTabs."   </thead>\n"
                    .$sTabs."   <tbody>\n";
            switch ($iEstatus) {
                case 1:
                    $sLeyendaGrupo="";
                    for ($i=0;$i<count($asResult);$i++) {
                        //--- Titulos de los campos
                        if (($i==0) && ($sNombresCampos===true)) {  
                            $sSalida.=$sTabs."  <tr class=\"titulo_1\">\n";
                            foreach ($asResult[0] as $key => $value) {
                                if (substr($key, 0,2)!="IN")
                                    $sSalida.=$sTabs."      <td>".substr($key,3)."</td>\n";
                            }
                            $sSalida.=$sTabs."  </tr>\n";
                        }
                        $sClase=($sClase=="par") ? "impar" : "par";
                        if (!isset($asResult[$i]['TEXTO_SEPARADOR'])) {
                            //---- Checa si hay agrupaciones
                            if ((isset($pasInput['AGRUPAR_POR'])) && $sLeyendaGrupo!=$asResult[$i][$pasInput['AGRUPAR_POR']]) {
                                $sSalida.=$sTabs."  <tr class=\"titulo_2\"><td colspan=\"99\">".$asResult[$i][$pasInput['AGRUPAR_POR']]."</td></tr>\n";
                                $sLeyendaGrupo=$asResult[$i][$pasInput['AGRUPAR_POR']];
                            }
                            //---- Datos normales
                            $sSalida.=$sTabs."  <tr class=\"".$sClase."\">\n";
                            foreach ($asResult[$i] as $key => $value) {
                                $sPrefijo=substr($key, 0,2);
                                switch ($sPrefijo) {
                                    case 'TX':  $sSalida.=$sTabs."      <td>".$value."</td>\n"; break; //texto
                                    //case 'FE':    $sSalida.=$sTabs."      <td>".$value."</td>\n"; break;
                                    case 'FE':  $sSalida.=$sTabs."      <td>".date("d M Y",strtotime($value))."</td>\n"; break; //fecha
                                    case 'MO':  $sSalida.=$sTabs."      <td>$".$value."</td>\n"; break; //Moneda
                                    case 'NU':  $sSalida.=$sTabs."      <td align=\"right\">".$value."</td>\n"; break; //Numero
                                    case 'CB':  $sSalida.=$sTabs."      <td><input type=\"checkbox\" name=\"chkRep".$value."\"></td>\n"; break; //CheckBox
                                    case 'IN':  $sSalida.=""; break; //Invisible
                                      default:  $sSalida.=$sTabs."      <td>".$value."</td>\n"; break;
                                }
                            }
                            $sSalida.=$sTabs."  </tr>\n";
                        }
                        else {
                            //---- Separador
                            $sSalida.=$sTabs."  <tr class=\"".$asResult[$i]['CLASE_SEPARADOR']."\"><td colspan=\"99\">".$asResult[$i]['TEXTO_SEPARADOR']."</td></tr>\n";
                        }
                    }
                break;
                case 0:
                    $sSalida.=$sTabs."  <tr><td>No hay resultados</td></tr>\n";
                break;
                case -1:
                    $sSalida.=$sTabs."  <tr><td>".$asResult['MENSAJE']."</td></tr>\n";
                break;
            }
            $sSalida.=$sTabs."  </tbody>\n"
                     .$sTabs."</table>\n";
            return ($sSalida);
        }

    
    
        function trae_ultimo_indice ($pasInput) {
            $sCondiciones=(isset($pasInput['condiciones'])) ? " WHERE ".$pasInput['condiciones'] : "" ;
            $asResultado=$this->consulta(array (
                'QUERY' => "SELECT ".$pasInput['nombre_campo']." FROM ".$pasInput['tabla'].$sCondiciones." ORDER BY ".$pasInput['nombre_campo']." DESC",
                'UNICA_FILA' => true));
            switch ($asResultado['ESTATUS']) {
                case 1:
                    $sSalida=$asResultado['DATOS'][$pasInput['nombre_campo']] + 1;
                    break;
                case 0: 
                    $sSalida=1;
                    break;
                case -1:
                    $sSalida=$asResultado['ERROR'];
                    break;
            }
            return($sSalida);
        }
    }

?>
