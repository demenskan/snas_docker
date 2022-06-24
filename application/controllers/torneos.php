<?php

    class Torneos extends CI_Controller {

        public $asClavesEquipos;

        function __construct() {
            parent::__construct();
            $this->load->model('torneos_mod');
            $this->load->library('tools_lib');
        }

        function Ver($psTemporada, $piClaveTorneo) {
            $asContenido=array (
                'PRINCIPAL' => $this->Principal($psTemporada, $piClaveTorneo),
                'BARRA_DERECHA' => $this->barra_derecha($psTemporada, $piClaveTorneo),
            );
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'PUBLIC'
            );
            $this->main_lib->display($asControlador);
        }

        function Principal($psTemporada, $piClaveTorneo) {
            $sTabs="\t\t\t";
            $sOut="";
            $asDatosTorneo=$this->torneos_mod->DatosTorneo($psTemporada, $piClaveTorneo);
            $sOut.=$sTabs."<tr>\n";
            switch ($asDatosTorneo['ESTATUS']) {
                case 1:
                    $asContenido=array (
                        'RUTA_LOGO' => base_url().$asDatosTorneo['DATOS']['logotipo'],
                        'DESCRIPCION' => $asDatosTorneo['DATOS']['descripcion'],
                        'NOMBRE_TORNEO' => $asDatosTorneo['DATOS']['nombre'],
                        'ACTIVO' => ($asDatosTorneo['DATOS']['estatus']==0) ? "No" : "Si",
                        'TEMPORADA' => $psTemporada
                    );
                    if ($asDatosTorneo['DATOS']['logotipo']=='') $asContenido['RUTA_LOGO']=base_url()."img/torneos/SNLogo.gif";
                    $asNoticias=$this->torneos_mod->Noticias($psTemporada, $piClaveTorneo);
                    switch ($asNoticias['ESTATUS']) {
                        case 1:
                            $sTablaNoticias=$sTabs."<table class=\"tabla-resultados\">\n"
                                            .$sTabs."\t<tr>\n"
                                            .$sTabs."\t\t<th colspan=\"2\">NOTICIAS</th>\n"
                                            .$sTabs."\t</tr>\n";
                            $sColor="";
                            
                            for ($i=0;$i<count($asNoticias['DATOS']);$i++) {
                                $sURLImagen=($asNoticias['DATOS'][$i]['imagen']!="") ? $asNoticias['DATOS'][$i]['imagen'] : $asNoticias['DATOS'][$i]['url_imagen'];
                                $sTablaNoticias.=$sTabs."\t<tr class=\"".$sColor."\">\n"
                                            .$sTabs."\t\t<td rowspan=\"2\"><img src=\"".$sURLImagen."\" width=\"100\" /></td>\n"
                                            .$sTabs."\t\t<td><a href=\"".base_url()."noticias/ver/".$asNoticias['DATOS'][$i]['id_unico']."\">".$asNoticias['DATOS'][$i]['titulo']."</a></td>\n"
                                            .$sTabs."\t</tr>\n"
                                            .$sTabs."\t<tr class=\"".$sColor."\">\n"
                                            .$sTabs."\t\t<td>".$asNoticias['DATOS'][$i]['resumen']."</td>\n"
                                            .$sTabs."\t</tr>\n";
                                if ($sColor=="even")
                                    $sColor="";
                                else
                                    $sColor="even";
                            }
                            $sTablaNoticias.=$sTabs."</table>\n";
                            break;
                        case 0:
                            $sTablaNoticias="<div class=\"Notice\">No hay noticas</div>";
                            
                            break;
                        case -1:
                            $sTablaNoticias="<div class=\"Error\">".$asNoticias['ERROR']."</div>";
                            break;
                    }
                    $asContenido['TABLA_NOTICIAS'] = $sTablaNoticias;
                    //OBTIENE LOS PARTICIPANTES
                    $asParticipantes=$this->torneos_mod->getGruposTorneo($psTemporada, $piClaveTorneo);
                    $sParticipantes="";
                    if ($asParticipantes['ESTATUS']==1) {
                        $sGrupoActual=$asParticipantes['DATOS'][0]['grupo'];
                        for ($i=0;$i<count($asParticipantes['DATOS']);$i++) {
                            if ($sGrupoActual!=$asParticipantes['DATOS'][$i]['grupo']) {
                                $sParticipantes.="&nbsp;&nbsp;&nbsp;&nbsp;";
                                $sGrupoActual=$asParticipantes['DATOS'][$i]['grupo'];
                            }
                            $sParticipantes.="<a href=\"clubes/inicio/".$asParticipantes['DATOS'][$i]['ruta_logo']."\"><img witdh=\"25\" height=\"25\" border=\"0\" src=\"img/escudos/mini/s".$asParticipantes['DATOS'][$i]['ruta_logo'].".gif\" /></a>";
                        }
                    }
                    $asContenido['LISTA_PARTICIPANTES']=$sParticipantes;
                    $sSalida=$this->load->view('ver_torneo_vw', $asContenido, true);
                    break;
                case 0:
                    $sSalida=$this->load->view('mensaje_vw', array ('MENSAJE' => 'No existe un torneo con esta clave', 'CLASE' => 'Notice'), true);
                    break;
                case -1:
                    $sSalida=$this->load->view('mensaje_vw', array ('MENSAJE' => $asDatosTorneo['ERROR'] , 'CLASE' => 'Error'), true);
                    break;
            }
            return($sSalida);
        }
    
        
    
        function Barra_derecha($psTemporada, $piClaveTorneo) {
            $asDatosTorneo=$this->torneos_mod->DatosTorneo($psTemporada, $piClaveTorneo);   
            if ($asDatosTorneo['ESTATUS']==1) {
                $asContenido=array (
                        'LOGO_RUTA' => base_url().$asDatosTorneo['DATOS']['logotipo'],
                        'RUTA_RAIZ' => base_url(), 
                        'NOMBRE' => $asDatosTorneo['DATOS']['nombre'],
                        'ID_TEMPORADA' => $psTemporada,
                        'ID_TORNEO' => $piClaveTorneo
                    );
                    if ($asDatosTorneo['DATOS']['logotipo']=='') $asContenido['LOGO_RUTA']=base_url()."img/torneos/SNLogo.gif";
                $sOpciones="";
                $sOpciones.=$this->AgregaOpcion('Documentos', 'Documentos', $psTemporada, $piClaveTorneo);
                $sOpciones.=$this->AgregaOpcion('TablaGeneral', 'Tabla General', $psTemporada, $piClaveTorneo);
                $sOpciones.=$this->AgregaOpcion('TablaGrupos', 'Tabla Grupos', $psTemporada, $piClaveTorneo);
                $sOpciones.=$this->AgregaOpcion('Playoffs', 'Playoffs', $psTemporada, $piClaveTorneo);
                $sOpciones.=$this->AgregaOpcion('Calendario', 'Calendario', $psTemporada, $piClaveTorneo);
                $sOpciones.=$this->AgregaOpcion('CalendarioEquipo', 'Calendario por Equipo', $psTemporada, $piClaveTorneo);
                $sOpciones.=$this->AgregaOpcion('CalendarioGrupo', 'Calendario por Grupos', $psTemporada, $piClaveTorneo);
                $sOpciones.=$this->AgregaOpcion('Goleo', 'Goleo', $psTemporada, $piClaveTorneo);
                $sOpciones.=$this->AgregaOpcion('Tarjetas', 'Tarjetas', $psTemporada, $piClaveTorneo);
                $sOpciones.=$this->AgregaOpcion('Lista','Lista', $psTemporada, '');
                $asContenido['OPCIONES']=$sOpciones;
                $sSalida=$this->load->view('barra_derecha_torneos_vw',$asContenido, true);
                return ($sSalida);
            }
            else
                return ('');
        }
    
        function AgregaOpcion($psModulo, $psNombre, $psTemporada, $psClaveTorneo) {
            if ($psModulo!='Lista')
                return ("\t\t\t\t\t<li><a href=\"".base_url()."torneos/".$psModulo."/".$psTemporada."/".$psClaveTorneo."\">".$psNombre."</a></li>\n");
            else
                return ("\t\t\t\t\t<li><a href=\"".base_url()."torneos/lista/".$psTemporada."\">".$psNombre."</a></li>\n");
        }
    
        function TablaGeneral($psTemporada, $piClaveTorneo, $pbGrupos=false) {
            $sColor="";
            $sOut="";
            $sTabs="\t\t\t";
            $sScript=($pbGrupos==true) ? "TablaGrupos" : "TablaGeneral";
            $sTemporada= ($psTemporada=="") ? $this->input->post('ssn') : $psTemporada;
            $sClave= ($piClaveTorneo=="") ? $this->input->post('tor') : $piClaveTorneo;
            $iJornadaMax=($this->input->post('slcHastaJornada')!='') ? $this->input->post('slcHastaJornada'): $this->torneos_mod->MaximoJornadas($psTemporada, $piClaveTorneo, 1);
            $iJornadaMin=($this->input->post('slcDesdeJornada')!='') ? $this->input->post('slcDesdeJornada'): 1;
            $asClavesEquipos=$this->torneos_mod->InicializaTabla($psTemporada, $piClaveTorneo); 
            if (count($asClavesEquipos) >0) {      //verifica que existan equipos definidos
                if ($iJornadaMax!=NULL) {
                    $asPartidos=$this->torneos_mod->PartidosJugados($psTemporada, $piClaveTorneo, $iJornadaMax, $iJornadaMin);
                    $iCont=0;
                    if ($asPartidos['ESTATUS']==1) {
                        for ($i=0;$i<count($asPartidos['DATOS']);$i++) {
                            $iPosLocal=$this->RegresaPosicion($asPartidos['DATOS'][$i]["id_equipo_local"], $asClavesEquipos);
                            $iPosVisita=$this->RegresaPosicion($asPartidos['DATOS'][$i]["id_equipo_visitante"], $asClavesEquipos);
                            $asClavesEquipos[$iPosLocal]["JJ"]++;
                            $asClavesEquipos[$iPosVisita]["JJ"]++;
                            if ($asPartidos['DATOS'][$i]["marcador_local"]>$asPartidos['DATOS'][$i]["marcador_visitante"]) { //gano el local
                                $asClavesEquipos[$iPosLocal]["JG"]++;
                                $asClavesEquipos[$iPosVisita]["JP"]++;
                                $asClavesEquipos[$iPosLocal]["PTS"]+=3;
                            }
                            elseif  ($asPartidos['DATOS'][$i]["marcador_local"]<$asPartidos['DATOS'][$i]["marcador_visitante"]) { //gano el visitante
                                $asClavesEquipos[$iPosVisita]["JG"]++;
                                $asClavesEquipos[$iPosLocal]["JP"]++;
                                $asClavesEquipos[$iPosVisita]["PTS"]+=3;
                            }
                            else { //empate
                                $asClavesEquipos[$iPosLocal]["JE"]++;
                                $asClavesEquipos[$iPosVisita]["JE"]++;
                                $asClavesEquipos[$iPosLocal]["PTS"]+=1;
                                $asClavesEquipos[$iPosVisita]["PTS"]+=1;
                            }
                            $asClavesEquipos[$iPosLocal]["GF"]+=$asPartidos['DATOS'][$i]["marcador_local"];
                            $asClavesEquipos[$iPosLocal]["GC"]+=$asPartidos['DATOS'][$i]["marcador_visitante"];
                            $asClavesEquipos[$iPosVisita]["GF"]+=$asPartidos['DATOS'][$i]["marcador_visitante"];
                            $asClavesEquipos[$iPosVisita]["GC"]+=$asPartidos['DATOS'][$i]["marcador_local"];
                            $asClavesEquipos[$iPosVisita]["DIF"]=$asClavesEquipos[$iPosVisita]["GF"]-$asClavesEquipos[$iPosVisita]["GC"];
                            $asClavesEquipos[$iPosLocal]["DIF"]=$asClavesEquipos[$iPosLocal]["GF"]-$asClavesEquipos[$iPosLocal]["GC"];
                            //Asigna array para ordenarlos
                            foreach ($asClavesEquipos as $key => $fila) {
                                    $aiGrupos[$key] = $fila["grupo"];
                                    $aiPuntos[$key]  = $fila["PTS"];
                                    $aiDiferencia[$key] = $fila["DIF"];
                                    $aiGolesFavor[$key] = $fila["GF"];
                            }
                        }
                        if ($pbGrupos==true)
                            array_multisort($aiGrupos, $aiPuntos, SORT_DESC, $aiDiferencia, SORT_DESC, $aiGolesFavor, SORT_DESC,  $asClavesEquipos);
                        else
                            array_multisort($aiPuntos, SORT_DESC, $aiDiferencia, SORT_DESC, $aiGolesFavor, SORT_DESC,  $asClavesEquipos);
                        $sGrupo='';
                        for ($i=0;$i<count($asClavesEquipos);$i++) {
                            if (($asClavesEquipos[$i]["grupo"]<>$sGrupo) && ($pbGrupos)) {
                                $sGrupo=$asClavesEquipos[$i]["grupo"];
                                $sOut.= $sTabs."\t<tr>\n"
                                    .$sTabs."\t\t<th colspan=\"14\" id=\"titulo\">GRUPO ".$sGrupo."</th>\n"
                                    .$sTabs."\t</tr>\n";
                            }
                            if ($sColor=="even")
                                $sColor="";
                            else
                                $sColor="even";
                            $sOut.= $sTabs."\t<tr class=\"".$sColor."\">\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">&nbsp;</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\"><img src=\"".base_url()."img/escudos/mini/s".$asClavesEquipos[$i]["ruta_logo"].".gif\" width=\"50\"/></td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\"><a href=\"".base_url()."torneos/CalendarioEquipo/".$sTemporada."/".$sClave."/".$asClavesEquipos[$i]["clave"]."\">".$asClavesEquipos[$i]["nombre"]."</a></td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">&nbsp;</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">".$asClavesEquipos[$i]["JJ"]."</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">".$asClavesEquipos[$i]["JG"]."</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">".$asClavesEquipos[$i]["JE"]."</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">".$asClavesEquipos[$i]["JP"]."</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">&nbsp;</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">".$asClavesEquipos[$i]["GF"]."</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">".$asClavesEquipos[$i]["GC"]."</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">&nbsp;</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">".$asClavesEquipos[$i]["PTS"]."</td>\n"
                                    .$sTabs."\t\t<td id=\"".$sColor."\">".$asClavesEquipos[$i]["DIF"]."</td>\n"
                                    .$sTabs."\t</tr>\n";
                        }
                    }
                }
                else
                    $sOut="<tr><td colspan=\"14\">No hay partidos definidos</td></tr>";     
            }
            else {
                $sOut="<tr><td colspan=\"14\">No hay grupos de participantes definidos</td></tr>";      
            }
            if ($this->input->post('slcDesdeJornada')!='') 
                $sMensaje="<div class=\"notice\">Tabla filtrada de la jornada ".$iJornadaMin." a la ".$iJornadaMax."</div>";
            else
                $sMensaje="";
            $asJornadas=$this->torneos_mod->ListaJornadas($psTemporada, $piClaveTorneo);
            $sTablaJornadas="";
            $asPrincipal=array (
                'RUTA_RAIZ' => base_url(),
                'CONTENIDO' => $sOut,
                'LISTA_JORNADAS' => $sTablaJornadas,
                'LISTA_JORNADAS_DESDE' => $this->tools_lib->GeneraCombo(array ('NOMBRE' => 'slcDesdeJornada', 'DATASET'=> $asJornadas, 'DEFAULT' => $iJornadaMin)),
                'LISTA_JORNADAS_HASTA'=> $this->tools_lib->GeneraCombo(array ('NOMBRE' => 'slcHastaJornada', 'DATASET'=> $asJornadas, 'DEFAULT' => $iJornadaMax)),
                'ID_TEMPORADA' => $psTemporada,
                'ID_TORNEO' => $piClaveTorneo,
                'SCRIPT' => $sScript,
                'MENSAJE' => $sMensaje
            );
            
            $asContenido=array (
                'PRINCIPAL' => $this->load->view('torneos/tabla_general_vw', $asPrincipal, true),
                'BARRA_DERECHA' => $this->Barra_derecha($psTemporada, $piClaveTorneo),
            );
        
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'PUBLIC'
            );
            $this->main_lib->display($asControlador);
        }
    
        function TablaGrupos ($piTemporada, $piTorneo) {
            $this->TablaGeneral($piTemporada, $piTorneo, true);
        }

        function RegresaPosicion($piClaveEquipo, $pasEquipos) {
            $i=0;
            while (($pasEquipos[$i]["clave"]<>$piClaveEquipo) && ($i<=count($pasEquipos))) {
                //iteraciona hasta que el parametro se iguala con el del array
                $i++;
            }
            return ($i);
        }

        function CalendarioEquipo($psTemporada, $piClaveTorneo, $piClub=0) {
            $this->load->model('clubes_mod');
            if ($this->input->post('slcEquipo')<>"")
                $iClaveEquipo=$this->input->post('slcEquipo');
            else
                $iClaveEquipo=$piClub;
            $asEquiposTorneo=$this->torneos_mod->EquiposTorneo($psTemporada, $piClaveTorneo);
            if ($iClaveEquipo!=0) {
                $asCalendario=$this->torneos_mod->CalendarioEquipo($psTemporada, $piClaveTorneo, $iClaveEquipo);
                $sTabs="\t\t\t";
                $sOut=$sTabs."<tr>\n";
                $iJornada=0;
                $iPartido=0;
                $sColor="";
                $asReporte=array();
                if ($asCalendario['ESTATUS']==1) {
                    for ($i=0;$i<count($asCalendario['DATOS']);$i++) {
                        //--Deduce de que equipo esta presentando el reporte
                        if ($asCalendario['DATOS'][$i]['id_equipo_local']==$iClaveEquipo) {
                            $asReporte[$asCalendario['DATOS'][$i]['jornada']-1] = array (
                                'NU Jornada' => $asCalendario['DATOS'][$i]['jornada'],
                                'TX Marcador' => ($asCalendario['DATOS'][$i]['jugado']==1) ? $asCalendario['DATOS'][$i]['marcador_local'].":".$asCalendario['DATOS'][$i]['marcador_visitante'] : "pendiente",
                                'TX ' => "vs.",
                                'TX  ' => "<img src=\"".base_url()."/img/escudos/mini/s".$asCalendario['DATOS'][$i]['logo_visita'].".gif\" />",
                                'TX Rival' => "<a href=\"".base_url()."torneos/CalendarioEquipo/".$psTemporada."/".$piClaveTorneo."/".$asCalendario['DATOS'][$i]['id_equipo_visitante']."\">".$asCalendario['DATOS'][$i]['nombre_visita']."</a>",
                                'TX Detalles' => "<a href=\"".base_url()."torneos/DetallePartido/".$psTemporada."/".$piClaveTorneo."/".$asCalendario['DATOS'][$i]['clave_partido']."\">Ver</a>",
                                'TX Sede' => $asCalendario['DATOS'][$i]['nombreestadio'],
                                'FE Fecha' => ($asCalendario['DATOS'][$i]['fecha_jugado']!=null) ? $asCalendario['DATOS'][$i]['fecha_jugado'] : 'no especificado',
                                'IN TipoPartido' => $asCalendario['DATOS'][$i]['tipo_partido']
                            );
                        }
                        else {
                            $asReporte[$asCalendario['DATOS'][$i]['jornada']-1] = array (
                                'NU Jornada' => $asCalendario['DATOS'][$i]['jornada'],
                                'TX Marcador' => ($asCalendario['DATOS'][$i]['jugado']==1) ? $asCalendario['DATOS'][$i]['marcador_visitante'].":".$asCalendario['DATOS'][$i]['marcador_local'] : "pendiente",
                                'TX ' => "@",
                                'TX  ' => "<img src=\"".base_url()."/img/escudos/mini/s".$asCalendario['DATOS'][$i]['logo_local'].".gif\" />",
                                'TX Rival' => "<a href=\"".base_url()."torneos/CalendarioEquipo/".$psTemporada."/".$piClaveTorneo."/".$asCalendario['DATOS'][$i]['id_equipo_local']."\">".$asCalendario['DATOS'][$i]['nombre_local']."</a>",
                                'TX Detalles' => "<a href=\"".base_url()."torneos/DetallePartido/".$psTemporada."/".$piClaveTorneo."/".$asCalendario['DATOS'][$i]['clave_partido']."\">Ver</a>",
                                'TX Sede' => $asCalendario['DATOS'][$i]['nombreestadio'],
                                'FE Fecha' => ($asCalendario['DATOS'][$i]['fecha_jugado']!=null) ? $asCalendario['DATOS'][$i]['fecha_jugado'] : 'no especificado',
                                'IN TipoPartido' => $asCalendario['DATOS'][$i]['tipo_partido']
                            );
                        }
                    }
                    $asDescansos=$this->torneos_mod->JornadasDescanso($psTemporada, $piClaveTorneo, $iClaveEquipo);
                    if ($asDescansos['ESTATUS']==1) {
                        for ($j=0;$j<count($asDescansos['DATOS']);$j++)
                            $asReporte[$asDescansos['DATOS'][$j]['jornada']-1]= array(
                                'CLASE_SEPARADOR' => 'success', 'TEXTO_SEPARADOR' => " ".$asDescansos['DATOS'][$j]['jornada'].' - Descanso'
                            );
                    }
                }
                for ($i=0;$i<=count($asCalendario['DATOS']);$i++) {
                    if (!isset($asReporte[$i]))
                        $asReporte[$i]=array(
                            'NU Jornada' => $i+1,
                                'TX Marcador' => "--",
                                'TX ' => "",
                                'TX  ' => "",
                                'TX Rival' => "",
                                'TX Detalles' => "",
                                'TX Sede' => "",
                                'FE Fecha' => "",
                                'IN TipoPartido' => ($i==0) ? $asReporte[$i+1]["IN TipoPartido"] : $asReporte[$i-1]["IN TipoPartido"]
                        );
                }
                $asDatosClub=$this->clubes_mod->RegresaDatos($iClaveEquipo);
                $sReporteFinal= $this->tools_lib->Genera_reporte(array (
                        'TITULO' => 'Calendario por equipo', 'ANCHO' => '700',  'DATOS' => $asReporte, 'ESTATUS' => 1, 'AGRUPAR_POR' => 'IN TipoPartido'
                        ));
                $sLogoClub="<img src=\"".base_url()."img/escudos/mini/s".$asDatosClub['DATOS']['ruta_logo'].".gif\" />";
                $sNombreClub=$asDatosClub['DATOS']['nombre'];
                }
            else {
                $sReporteFinal="";
                $sLogoClub="";
                $sNombreClub="";
            }
            $asPrincipal=array ('SELECTOR' => $this->tools_lib->generacombo(array(
                                            'NOMBRE' => 'slcEquipo', 'DATASET' => $asEquiposTorneo, 
                                            'DEFAULT' => $iClaveEquipo)),
                                'RESULTADO' => $sReporteFinal,
                                'TEMPORADA' => $psTemporada,
                                'TORNEO' => $piClaveTorneo,
                                'RUTA_RAIZ' => base_url(),
                                'LOGOTIPO' => $sLogoClub,
                                'NOMBRE_EQUIPO' => $sNombreClub 
                        );
            $asContenido=array (
                'PRINCIPAL' => $this->load->view('torneos/calendario_equipos_vw', $asPrincipal, true),
                'BARRA_DERECHA' => $this->Barra_derecha($psTemporada, $piClaveTorneo)
            );
            $asControlador=array (
                'CONTENIDO' => $this->load->view('templates/'.$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'PUBLIC'
            );
            $this->main_lib->display($asControlador);
        }
    
        function CalendarioGrupo ($piTemporada, $piTorneo, $psGrupo="") {
            $asTorneo=$this->torneos_mod->DatosTorneo($piTemporada, $piTorneo);
            if ($asTorneo['ESTATUS']==1) {
                $asGrupos=$this->torneos_mod->getListaGrupos($piTemporada, $piTorneo);
                $sGrupoActual=($psGrupo=="") ? $asGrupos['DATOS'][0]['grupo'] : $psGrupo;
                if ($asGrupos['ESTATUS']==1) {
                    $asPrincipal=array (
                        'RUTA_RAIZ' => base_url(),
                        'TOTAL_GRUPOS' => $asGrupos['CONTEO'],
                        'GRUPO' => $sGrupoActual
                    );
                    $asPrincipal['BLOQUE_SELECCION']=array(); 
                    for ($i=0;$i<count($asGrupos['DATOS']);$i++) {
                        $asPrincipal['BLOQUE_SELECCION'][]=array (
                            'RUTA_RAIZ' => base_url(),
                            'INDEX_URI' => $this->config->item('index_uri'),
                            'TEMPORADA' => $piTemporada,
                            'TORNEO' => $piTorneo,
                            'GRUPO_SEL' => $asGrupos['DATOS'][$i]['grupo']
                        ); 
                    }
                    $asPartidos=$this->torneos_mod->getPartidosGrupo($piTemporada, $piTorneo, $sGrupoActual);
                    $asPrincipal['BLOQUE_PARTIDOS']=array();
                    if ($asPartidos['ESTATUS']==1) {
                        $iJornadaActual=0;
                        for ($j=0;$j<count($asPartidos['DATOS']);$j++) {
                            if ($asPartidos['DATOS'][$j]['jornada']!=$iJornadaActual) {
                                $sJornada= "<tr><td colspan=\"6\" class=\"titulo_2\">Jornada ".$asPartidos['DATOS'][$j]['jornada']."</td></tr>\n";
                                $iJornadaActual=$asPartidos['DATOS'][$j]['jornada'];
                            }
                            else
                                $sJornada="";
                            $asPrincipal['BLOQUE_PARTIDOS'][]=array(
                                'RUTA_RAIZ' => base_url(),
                                'CLASE' => ($j%2==0) ? "non" : "par",
                                'LOGO_LOCAL' => $asPartidos['DATOS'][$j]['logo_local'],
                                'LOGO_VISITA' => $asPartidos['DATOS'][$j]['logo_visita'],
                                'CLUB_LOCAL' => $asPartidos['DATOS'][$j]['club_local'],
                                'CLUB_VISITA' => $asPartidos['DATOS'][$j]['club_visita'],
                                'MARCADOR' => ($asPartidos['DATOS'][$j]['jugado']==1) ? $asPartidos['DATOS'][$j]['marcador_local']." : ".$asPartidos['DATOS'][$j]['marcador_visitante'] : "pendiente",
                                'TEMPORADA' => $piTemporada,
                                'TORNEO' => $piTorneo,
                                'CLAVE' => $asPartidos['DATOS'][$j]['clave_partido'],
                                'JORNADA' => $sJornada
                            );
                        }
                    }
                    $asContenido=array (
                        'PRINCIPAL' => $this->parser->parse('torneos/calendario_grupos_vw', $asPrincipal, true),
                        'BARRA_DERECHA' => $this->Barra_derecha($piTemporada, $piTorneo)
                    );
                    $asControlador=array (
                        'CONTENIDO' => $this->load->view('templates/'.$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                        'TIPO_ACCESO' => 'PUBLIC'
                    );
                    $this->main_lib->display($asControlador);
                }
                else
                    $this->main_lib->escribe_mensaje(array('MENSAJE' => "No hay equipos definidos"));
            }
            else
                $this->main_lib->escribe_mensaje(array('MENSAJE' => "No existe ese torneo"));
        }
    
        function Playoffs ($piTemporada, $piTorneo) {
            $asTorneo=$this->torneos_mod->DatosTorneo($piTemporada, $piTorneo);
            if ($asTorneo['ESTATUS']==1) {
                $asPartidos=$this->torneos_mod->getPartidosPlayoffs($piTemporada, $piTorneo);
                if ($asPartidos['ESTATUS']==1) {
                    
                    $asPrincipal=array (
                        'RUTA_RAIZ' => base_url()
                    );
                    $sTipoActual="";
                    for ($i=0;$i<count($asPartidos['DATOS']);$i++) {
                        if ($asPartidos['DATOS'][$i]['tipo_partido']!=$sTipoActual) {
                            $sSeparador=$this->parser->parse('torneos/separador_playoffs_vw',array ('TIPO' => $asPartidos['DATOS'][$i]['tipo_partido']),true);
                            $sTipoActual=$asPartidos['DATOS'][$i]['tipo_partido'];
                        }
                        else
                            $sSeparador="";
                        $sTE="";
                        //Checa si es vuelta para poner el marcador global
                        if ($asPartidos['DATOS'][$i]['ida_vuelta']=='V') {
                            $asGlobal=$this->torneos_mod->getMarcadorGlobal($piTemporada, $piTorneo, $asPartidos['DATOS'][$i]['clave']);
                            if ($asGlobal['ESTATUS']==1) {
                                $sTE.="<br>\nGlobal ".$asGlobal['LOCAL']."-".$asGlobal['VISITA'];
                            }
                        }
                        //Checa si hubo tiempo extra y/o penales
                        $asTiempoExtra=$this->torneos_mod->getEspecialesPartido($piTemporada, $piTorneo, $asPartidos['DATOS'][$i]['clave']);
                        if ($asTiempoExtra['ESTATUS']==1) {
                            if ($asTiempoExtra['DATOS']['tiempo_extra']==1) 
                                $sTE.="(OT)";
                            if ($asTiempoExtra['DATOS']['marcador_penales_local']!="") {
                                $sTE.="<br>\n(".$asTiempoExtra['DATOS']['marcador_penales_local'].":".$asTiempoExtra['DATOS']['marcador_penales_visita']." PK)";
                            }
                        }
                        $asPrincipal['BLOQUE_FILA'][]=array (
                            'CLASE' => ($i%2==0) ? "non" : "par",
                            'INDEX_URI' => $this->config->item('index_uri'),
                            'TEMPORADA' => $piTemporada,
                            'TORNEO' => $piTorneo,
                            'CLAVE' => $asPartidos['DATOS'][$i]['clave'],
                            'FECHA' => ($asPartidos['DATOS'][$i]['fecha_jugado']!="0000-00-00 00:00:00") ? date("d/M/Y",strtotime($asPartidos['DATOS'][$i]['fecha_jugado'])) : "sin definir", 
                            'SEDE' => $asPartidos['DATOS'][$i]['estadio'],
                            'LOGO_LOCAL' => $asPartidos['DATOS'][$i]['logo_local'],
                            'LOGO_VISITANTE' => $asPartidos['DATOS'][$i]['logo_visita'],
                            'CLUB_LOCAL' => $asPartidos['DATOS'][$i]['nombre_local'],
                            'CLUB_VISITANTE' => $asPartidos['DATOS'][$i]['nombre_visita'],
                            'SEPARADOR' => $sSeparador,
                            'MARCADOR' => ($asPartidos['DATOS'][$i]['jugado']==1) ? $asPartidos['DATOS'][$i]['marcador_local'].":".$asPartidos['DATOS'][$i]['marcador_visitante'].$sTE : "(pendiente)"
                        );
                    }
                    $asContenido=array (
                        'PRINCIPAL' => $this->parser->parse('torneos/playoffs_vw', $asPrincipal, true),
                        'BARRA_DERECHA' => $this->Barra_derecha($piTemporada, $piTorneo)
                    );
                    $asControlador=array (
                        'CONTENIDO' => $this->load->view('templates/'.$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                        'TIPO_ACCESO' => 'PUBLIC'
                    );
                    $this->main_lib->display($asControlador);
                }
                else
                    $this->main_lib->mensaje("No hay partidos definidos");
            }
            else
                $this->main_lib->mensaje("No existe ese torneo");
        }
        
        function Lista ($piTemporada='__EXTRA__') {
            $sTemporada=($this->input->post('slcTemporada')=="") ? $piTemporada : $this->input->post('slcTemporada');
            $asTorneos=$this->torneos_mod->ListaTorneos($sTemporada);
            $iCantidadColumnas=4;
            $asVistaTorneos=array();
            if ($asTorneos['ESTATUS']==1) {
                $sClaseActual="";
                $sTemporadaActual="";
                for ($iCont=0;$iCont<count($asTorneos['DATOS']);$iCont++) {
                    if ($asTorneos['DATOS'][$iCont]['nombre_clase']!=$sClaseActual) {
                        $sClase="<h4>".$asTorneos['DATOS'][$iCont]['nombre_clase']."</h4>";
                        $sClaseActual=$asTorneos['DATOS'][$iCont]['nombre_clase'];
                    }
                    else
                        $sClase="";
                    //Separador de temporadas
                    if ($sTemporada=="__EXTRA__") {
                        if ($asTorneos['DATOS'][$iCont]['id_temporada']!=$sTemporadaActual) {
                            $sSeparador="<hr width=80% /><h2>Temporada ".$asTorneos['DATOS'][$iCont]['id_temporada']."</h2>";
                            $sTemporadaActual=$asTorneos['DATOS'][$iCont]['id_temporada'];
                        }
                        else
                            $sSeparador="";
                    }
                    else
                        $sSeparador="";
                    
                    $asVistaTorneos[]=array (
                        'SEPARADOR_TEMPORADAS' => $sSeparador,
                        'CLASE' => $sClase,
                        'TEMPORADA' => $asTorneos['DATOS'][$iCont]['id_temporada'],
                        'CLAVE' => $asTorneos['DATOS'][$iCont]['clave'],
                        'RUTA_LOGO' => $asTorneos['DATOS'][$iCont]['logotipo'],
                        'NOMBRE' => $asTorneos['DATOS'][$iCont]['nombre']
                    );
                }
            }
            $asPrincipal=array (
                'BLOQUE_TORNEOS' => $asVistaTorneos,
                'LISTA_TEMPORADAS' => $this->tools_lib->generacombo ( array (
                    'NOMBRE' => 'slcTemporada', 'TABLA' => 'temporadas', 'CAMPO_CLAVE' => 'temporada',
                    'LEYENDA' => 'nombre_corto', 'DEFAULT' => $sTemporada, 'OPCION_EXTRA' => 'Todas las temporadas',
                    'DB' => 'default'
                )));
            $asContenido=array (
                'PRINCIPAL' => $this->parser->parse('torneos/lista_torneos_vw', $asPrincipal, true),
            );
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
                'TIPO_ACCESO' => 'PUBLIC' );
            $this->main_lib->display($asControlador);
        }
        
        function Calendario ($piTemporada, $piTorneo, $piJornada=1) {
            $asPartidosJornada=$this->torneos_mod->ListaPartidosJornada($piTemporada, $piTorneo, $piJornada);
            if ($asPartidosJornada['ESTATUS']==1) {
                $asReportePartidos=array();
                for ($i=0;$i<count($asPartidosJornada['DATOS']);$i++) {
                    if ($asPartidosJornada['DATOS'][$i]['jugado']==1)
                        $sMarcador=$asPartidosJornada['DATOS'][$i]['marcador_local']." : ".$asPartidosJornada['DATOS'][$i]['marcador_visitante'];
                    else
                        $sMarcador="PENDIENTE";
                    $asReportePartidos[$i]=array (
                        "TX 1" => "<img src=\"".base_url()."img/escudos/mini/s".$asPartidosJornada['DATOS'][$i]['logo_local'].".gif\" />",
                        "TX 2" => $asPartidosJornada['DATOS'][$i]['nombre_local'],
                        "TX 3" => $sMarcador,
                        "TX 4" => $asPartidosJornada['DATOS'][$i]['nombre_visita'],
                        "TX 5" => "<img src=\"".base_url()."img/escudos/mini/s".$asPartidosJornada['DATOS'][$i]['logo_visita'].".gif\" />",
                        "TX 6" => "<a href=\"".base_url()."torneos/detallepartido/".$piTemporada."/".$piTorneo."/".$asPartidosJornada['DATOS'][$i]['clave']."\">Ver detalle...</a>"
                    );
                }
                $sCalendarioJornada=$this->tools_lib->genera_reporte (array (
                    'TITULO' => "Jornada ".$piJornada." - ".$asPartidosJornada['DATOS'][0]['tipo_partido'],
                    'DATOS' => $asReportePartidos, 'ANCHO' => '700',
                    'NOMBRES_CAMPOS' => false
                ));
                $iMaxJornadas=$this->torneos_mod->MaximoJornadas($piTemporada, $piTorneo);
                $asListaJornadas=array();
                for ($i=1;$i<=$iMaxJornadas;$i++)
                    $asListaJornadas[0]["TX ".$i]="<a href=\"".base_url()."torneos/calendario/".$piTemporada."/".$piTorneo."/".$i."\">".$i."</a>";
                $sTablaJornadas=$this->tools_lib->genera_reporte (array (
                    'TITULO' => "Lista de jornadas",
                    'DATOS' => $asListaJornadas, 'ANCHO' => '700',
                    'NOMBRES_CAMPOS' => false
                ));
                $asEquiposDescanso=$this->torneos_mod->EquiposQueDescansan($piTemporada, $piTorneo, $piJornada);
                if ($asEquiposDescanso['ESTATUS']==1) {
                    $sMensajeBye="<div class=\"Notice\">BYE: ";
                    for ($i=0;$i<count($asEquiposDescanso['DATOS']);$i++) {
                        if ($i==0)
                            $sMensajeBye.=$asEquiposDescanso['DATOS'][$i]['nombre_corto'];
                        else
                            $sMensajeBye.=", ".$asEquiposDescanso['DATOS'][$i]['nombre_corto'];
                    }
                    $sMensajeBye.="</div>\n";
                }
                else
                    $sMensajeBye="";
                
                $asPrincipal=array (
                'RUTA_RAIZ' => base_url(),
                'LISTA_JORNADAS' => $sTablaJornadas,
                'LISTA_PARTIDOS' => $sCalendarioJornada,
                'LISTA_BYE' => $sMensajeBye
                );
            
                $asContenido=array (
                    'PRINCIPAL' => $this->load->view('torneos/calendario_vw', $asPrincipal, true),
                    'BARRA_DERECHA' => $this->Barra_derecha($piTemporada, $piTorneo),
                );
            
                $asControlador= array (
                    'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                    'TIPO_ACCESO' => 'PUBLIC'
                );
                $this->main_lib->display($asControlador);
            }
        }
        function DetallePartido ($piTemporada, $piTorneo, $piClavePartido=1) {
            $sSelector="";
            $asGeneralesPartido=$this->torneos_mod->GeneralesPartido($piTemporada, $piTorneo, $piClavePartido);
            if ($asGeneralesPartido['ESTATUS']==1) {
                if ($asGeneralesPartido['DATOS']['jugado']==1) {
                    $sMarcardorLocal=$asGeneralesPartido['DATOS']['marcador_local'];
                    $sMarcardorVisita=$asGeneralesPartido['DATOS']['marcador_visitante'];
                }
                else {
                    $sMarcardorLocal="--";
                    $sMarcardorVisita="--";
                }
                if ($asGeneralesPartido['DATOS']['fecha_jugado']!='0000-00-00 00:00:00')
                    $sFechaJugado="Fecha de juego: ".date("l, j M Y", strtotime($asGeneralesPartido['DATOS']['fecha_jugado']));
                elseif ($asGeneralesPartido['DATOS']['fecha_captura']!='0000-00-00 00:00:00')
                    $sFechaJugado="Fecha de captura: ".date("l, j M Y", strtotime($asGeneralesPartido['DATOS']['fecha_captura']));
                else
                    $sFechaJugado="Fecha: indefinida";
                $sLogoLocal="\"".base_url()."img/escudos/".$asGeneralesPartido['DATOS']['logo_local'].".gif\""; 
                $sLogoVisita="\"".base_url()."img/escudos/".$asGeneralesPartido['DATOS']['logo_visita'].".gif\"";   
                $sNombreLocal=$asGeneralesPartido['DATOS']['nombre_local'];
                $sNombreVisita=$asGeneralesPartido['DATOS']['nombre_visita'];
                $sEstadio=$asGeneralesPartido['DATOS']['nombreestadio'];
                $sClaveLocal=$asGeneralesPartido['DATOS']['id_equipo_local'];
                $sClaveVisita=$asGeneralesPartido['DATOS']['id_equipo_visitante'];
                $sComentario=$asGeneralesPartido['DATOS']['comentarios'];
                $sJornada=$asGeneralesPartido['DATOS']['jornada'];
                $sTipoPartido=$asGeneralesPartido['DATOS']['tipo_partido_desc'];
                $sFrenteLocal= (isset($asGeneralesPartido['DATOS']['cfrente_local'])) ?  $asGeneralesPartido['DATOS']['cfrente_local'] : '#000000';
                $sFondoLocal= (isset($asGeneralesPartido['DATOS']['cfondo_local'])) ?  $asGeneralesPartido['DATOS']['cfondo_local'] : '#FFFFFF';
                $sExtraLocal= (isset($asGeneralesPartido['DATOS']['cextra_local'])) ?  $asGeneralesPartido['DATOS']['cextra_local'] : '#CCCCCC';
                $sFrenteVisita= (isset($asGeneralesPartido['DATOS']['cfrente_visita'])) ?  $asGeneralesPartido['DATOS']['cfrente_visita'] : '#000000';
                $sFondoVisita= (isset($asGeneralesPartido['DATOS']['cfondo_visita'])) ?  $asGeneralesPartido['DATOS']['cfondo_visita'] : '#FFFFFF';
                $sExtraVisita= (isset($asGeneralesPartido['DATOS']['cextra_visita'])) ?  $asGeneralesPartido['DATOS']['cextra_visita'] : '#CCCCCC';
                $sEstiloLocal="style=\"background-color:".$sFondoLocal."; color:".$sFrenteLocal."; text-shadow: ".$sExtraLocal." 3px 3px 2px; font-size:1.5em;\" ";
                $sEstiloVisita="style=\"background-color:".$sFondoVisita."; color:".$sFrenteVisita."; text-shadow: ".$sExtraVisita." 3px 3px 2px; font-size:1.5em;\" ";
                $asEventos=$this->torneos_mod->EventosPartido($piTemporada, $piTorneo, $piClavePartido);
                if ($asEventos['ESTATUS']!=-1) {
                    $sEventos="";
                    $asDatosReporte=array();
                    for ($i=0;$i<count($asEventos['DATOS']);$i++) {
                        if ($asEventos['DATOS'][$i]['id_equipo']==$sClaveLocal) {
                            $asDatosReporte[$i]=array (
                                'TX EL' => "<img src=\"".base_url()."img/".$asEventos['DATOS'][$i]['imagen'].".gif\" alt=\"".$asEventos['DATOS'][$i]['descripcion']."\">",
                                'TX NL' => $asEventos['DATOS'][$i]['nombre'],
                                'TX ML' => $asEventos['DATOS'][$i]['minuto'],
                                'TX EV' => "",
                                'TX NV' => "",
                                'TX MV' => ""
                            );
                        }
                        else {
                            $asDatosReporte[$i]=array (
                                'TX EL' => "",
                                'TX NL' => "",
                                'TX ML' => "",
                                'TX EV' => "<img src=\"".base_url()."img/".$asEventos['DATOS'][$i]['imagen'].".gif\" alt=\"".$asEventos['DATOS'][$i]['descripcion']."\">",
                                'TX NV' => $asEventos['DATOS'][$i]['nombre'],
                                'TX MV' => $asEventos['DATOS'][$i]['minuto']
                            );
                        }
                    }
                    $sEventos=$this->tools_lib->genera_reporte(array
                            ('DATOS' => $asDatosReporte, 'ANCHO' => '700', 'TITULO' => 'Detalles del partido', 'NOMBRES_CAMPOS' => false)
                    );
                }
                else
                    $sEventos=$asDatosReporte['ERROR'];
                //----Calculo de Supers
                if ($asGeneralesPartido['DATOS']['tipo_partido_num']==1) {
                    
                }
                
                    
                $asPrincipal=array (
                    'RUTA_RAIZ' => base_url(),
                    'LOGO_LOCAL' => $sLogoLocal,
                    'LOGO_VISITA' => $sLogoVisita,
                    'NOMBRE_LOCAL' => $sNombreLocal,
                    'NOMBRE_VISITA' => $sNombreVisita,
                    'ESTILO_LOCAL' => $sEstiloLocal,
                    'ESTILO_VISITA' => $sEstiloVisita,
                    'ESTADIO' => $sEstadio,
                    'SELECTOR' => $sSelector,
                    'EVENTOS' => $sEventos,
                    'FECHA_JUEGO' => $sFechaJugado,
                    'COMENTARIOS' => $sComentario,
                    'MARCADOR_LOCAL' => $sMarcardorLocal,
                    'MARCADOR_VISITA' => $sMarcardorVisita,
                    'JORNADA' => "<a href=\"".base_url()."torneos/calendario/".$piTemporada."/".$piTorneo."/".$sJornada."\">".$sJornada."</a>",
                    'TIPO_PARTIDO' => $sTipoPartido,
                    'REGISTRO_LOCAL' => $this->CalculaRegistro($piTemporada, $piTorneo, $sJornada, $asGeneralesPartido['DATOS']['id_equipo_local']),
                    'REGISTRO_VISITA' => $this->CalculaRegistro($piTemporada, $piTorneo, $sJornada, $asGeneralesPartido['DATOS']['id_equipo_visitante']),
                );
                $asContenido=array (
                    'PRINCIPAL' => $this->load->view('torneos/detalles_partido_vw', $asPrincipal, true),
                    'BARRA_DERECHA' => $this->Barra_derecha($piTemporada, $piTorneo),
                );
            }
            else {
                $asContenido=array (
                    'PRINCIPAL' => "No hay partido definido con estas condiciones",
                    'BARRA_DERECHA' => $this->Barra_derecha($piTemporada, $piTorneo),
                );
            }
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'PUBLIC'
            );
            $this->main_lib->display($asControlador);
        }
        
        function CalculaRegistro ($piTemporada, $piTorneo, $piJornada, $piClaveEquipo) {
            $asRecord=$this->torneos_mod->CalendarioEquipo($piTemporada, $piTorneo, $piClaveEquipo);
            
            //echo ($this->tools_lib->genera_reporte(array ('TITULO' => "", 'ANCHO' => "300", 'DATOS' => $asRecord['DATOS'])));
            if ($asRecord['ESTATUS']!=-1) {
                $iJG=0; $iJE=0; $iJP=0; $iPts=0; $iGF=0; $iGC=0;
                for ($i=0;$i<count($asRecord['DATOS']); $i++) {
                    if ($asRecord['DATOS'][$i]['jornada']>$piJornada)
                        break;
                    
                    if ($asRecord['DATOS'][$i]['jugado']==1) {
                        if ($asRecord['DATOS'][$i]['id_equipo_local']==$piClaveEquipo) {
                            if ($asRecord['DATOS'][$i]['marcador_local']>$asRecord['DATOS'][$i]['marcador_visitante']) {
                                $iJG++;
                                $iPts+=3;
                            }
                            elseif ($asRecord['DATOS'][$i]['marcador_local']<$asRecord['DATOS'][$i]['marcador_visitante']) 
                                $iJP++;
                            else {
                                $iJE++;
                                $iPts+=1;
                            }
                            $iGF+=$asRecord['DATOS'][$i]['marcador_local'];
                            $iGC+=$asRecord['DATOS'][$i]['marcador_visitante'];
                        }
                        else {
                            if ($asRecord['DATOS'][$i]['marcador_visitante']>$asRecord['DATOS'][$i]['marcador_local']) {
                                $iJG++;
                                $iPts+=3;
                            }
                            elseif ($asRecord['DATOS'][$i]['marcador_visitante']<$asRecord['DATOS'][$i]['marcador_local']) 
                                $iJP++;
                            else {
                                $iJE++;
                                $iPts+=1;
                            }
                            $iGF+=$asRecord['DATOS'][$i]['marcador_visitante'];
                            $iGC+=$asRecord['DATOS'][$i]['marcador_local'];
                        }
                    }
                }
                $sDiferencia=$iGF-$iGC;
                $sDiferencia=($sDiferencia>0) ? "+".$sDiferencia : $sDiferencia;
                $sSalida=$iPts." Pts. (".$iJG."-".$iJE."-".$iJP."), Dif. ".$sDiferencia." (".$iGF.":".$iGC.")";
            }
            else
                $sSalida="Error! =>".$asRecord['ERROR'];
            return ($sSalida);
        }
        
        function Goleo ($piTemporada, $piTorneo, $piClaveJugador="0", $piClub="") {
            // SI NO ESTA ESPECIFICADA LA JORNADA LIMITE MAXIMO, LA CALCULA CONTANDOLAS 
            if ($this->input->post("slcHastaJornada")!="")
                $iJornadaMax=$this->input->post("slcHastaJornada");
            else 
                $iJornadaMax=$this->torneos_mod->MaximoJornadas($piTemporada, $piTorneo);
            // JORNADA MINIMA
            if ($this->input->post("slcDesdeJornada")!="")
                $iJornadaMin=$this->input->post("slcDesdeJornada");
            else
                $iJornadaMin=1;
            // CLUB SELECCIONADO
            if (($this->input->post("slcClub")!="")&&($this->input->post("slcClub")!="_EXTRA_")) 
                $iClaveClub=$this->input->post("slcClub");
            else 
                $iClaveClub="";
            if ($piClub!="") $iClaveClub=$piClub;
            // LIMITE
            if ($this->input->post("txtLimite")!="")
                $sLimite=$this->input->post("txtLimite");
            else
                $sLimite="10";
            if ($piClaveJugador=="0") {             //si no esta definida la identidad del jugador, muestra el goleo del torneo
                $asResult=$this->torneos_mod->GoleoTorneo($piTemporada, $piTorneo, $iJornadaMin, $iJornadaMax, $iClaveClub, $sLimite);
                
                if ($asResult['ESTATUS']!=-1) {
                    //$this->tools_lib->dump ($asResult['DATOS']);
                    $asDatosReporte=array();
                    $iLugar=1;
                    for ($i=0;$i<count($asResult['DATOS']);$i++) {
                        if ($i>0)
                            if ($asResult['DATOS'][$i-1]['goles']>$asResult['DATOS'][$i]['goles']) $iLugar++;
                        $asDatosReporte[$i]= array (
                            'TX LUGAR' => $iLugar, 
                            'TX ' => "<a href=\"".base_url()."torneos/Goleo/".$piTemporada."/".$piTorneo."/0/".$asResult['DATOS'][$i]['id_club']."\"><img src=\"".base_url()."img/escudos/mini/s".$asResult['DATOS'][$i]['logo'].".gif\" border=\"0\"></a>",
                            'TX Club' => "<a href=\"".base_url()."torneos/Goleo/".$piTemporada."/".$piTorneo."/0/".$asResult['DATOS'][$i]['id_club']."\">".$asResult['DATOS'][$i]['nombre_corto']."</a>",
                            'TX Jugador' => "<a href=\"".base_url()."torneos/Goleo/".$piTemporada."/".$piTorneo."/".$asResult['DATOS'][$i]['id_unico']."\">".$asResult['DATOS'][$i]['nombre']."</a>",
                            'TX Goles' => $asResult['DATOS'][$i]['goles']
                        );
                    }
                    $sReporteResultado=$this->tools_lib->genera_reporte (array ('DATOS' => $asDatosReporte, 'TITULO' => 'Goleo', 'ANCHO' => '700'));
                }
                else
                    $asDatosReporte=array ('TX Error' => $asDatosReporte['ERROR']);
            }
            else {
                $asResult=$this->torneos_mod->GoleoJugador($piTemporada, $piTorneo, $piClaveJugador);
                $asDatosReporte=array();
                if ($asResult['ESTATUS']!=-1) {
                    for ($i=0;$i<count($asResult['DATOS']);$i++) {
                        $asDatosReporte[$i]= array (
                            'TX Rival' => "<a href=\"".base_url()."torneos/detallepartido/".$piTemporada."/".$piTorneo."/".$asResult['DATOS'][$i]['clave']."\"><img src=\"".base_url()."img/escudos/mini/s".$asResult['DATOS'][$i]['logo'].".gif\" border=\"0\">".$asResult['DATOS'][$i]['nombre_corto']."</a>"
                                ,
                            'TX Jornada' => "<a href=\"".base_url()."torneos/detallepartido/".$piTemporada."/".$piTorneo."/".$asResult['DATOS'][$i]['clave']."\">".$asResult['DATOS'][$i]['jornada']."</a>",
                            'TX Minuto' => $asResult['DATOS'][$i]['minuto'],
                            'TX Tipo' => ($asResult['DATOS'][$i]['tipo']==6) ?  "Penalty" : "Gol normal"
                        );
                    }
                    //'TX Tipo' => 
                }
                else {
                    $asDatosReporte=array ('TX Error' => $asDatosReporte['ERROR']);
                }
                $sReporteResultado=$this->tools_lib->genera_reporte (array ('DATOS' => $asDatosReporte, 'TITULO' => 'Registro de '.$asResult['DATOS'][0]['nombre_jugador'], 'ANCHO' => '700'));
            }
            $sQueryJornadas="SELECT  distinct par.jornada, par.jornada"
                        ."  FROM partidos par"
                        ."  INNER JOIN torneos tor ON par.id_torneo=tor.clave AND par.id_temporada=tor.id_temporada"
                        ."  WHERE tor.id_temporada=".$piTemporada." AND tor.clave=".$piTorneo
                        ."  ORDER BY par.jornada";
            $sQueryClubes=" SELECT ag.id_equipo, cl.nombre_corto "
                        ."  FROM acomodo_grupos ag "
                        ."  INNER JOIN clubes cl ON ag.id_equipo=cl.id_unico "
                        ."  WHERE ag.id_temporada=".$piTemporada." AND ag.id_torneo=".$piTorneo;

            $asPrincipal=array (
                'RUTA_RAIZ' => base_url(),
                'LISTA_JORNADAS_DESDE' => $this->tools_lib->GeneraCombo(array ('NOMBRE' => 'slcDesdeJornada', 'QUERY' => $sQueryJornadas, 'DEFAULT' => $iJornadaMin, 'CAMPO_CLAVE' => 'jornada', 'LEYENDA' => 'jornada')),
                'LISTA_JORNADAS_HASTA' => $this->tools_lib->GeneraCombo(array ('NOMBRE' => 'slcHastaJornada', 'QUERY' => $sQueryJornadas, 'DEFAULT' => $iJornadaMax, 'CAMPO_CLAVE' => 'jornada', 'LEYENDA' => 'jornada')),
                'LISTA_CLUBES' => $this->tools_lib->GeneraCombo(array('NOMBRE' => 'slcClub', 'QUERY' => $sQueryClubes, 'DEFAULT' => $piClub, 'CAMPO_CLAVE' => 'id_equipo', 'LEYENDA' => 'nombre_corto', 'OPCION_EXTRA' => 'Todos')),
                'LIMITE' => $sLimite,
                'ID_TEMPORADA' => $piTemporada,
                'ID_TORNEO' => $piTorneo,
                'ENCABEZADO' => "",
                'RESULTADO' =>  $sReporteResultado
            );
            
            $asContenido=array (
                'PRINCIPAL' => $this->load->view('torneos/goleo_vw', $asPrincipal, true),
                'BARRA_DERECHA' => $this->Barra_derecha($piTemporada, $piTorneo),
            );
        
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'PUBLIC'
            );
            $this->main_lib->display($asControlador);
        }
        
        function Tarjetas ($piTemporada, $piTorneo) {
            $asResult=$this->torneos_mod->Tarjetas($piTemporada, $piTorneo);
            if ($asResult['ESTATUS']!=-1) {
                //$this->tools_lib->dump ($asResult['DATOS']);
                $asDatosReporte=array();
                $iLugar=1;
                $sAmarillas="<img src=\"".base_url()."img/yellow_card.gif\" />";
                $sRojaAmarillas="<img src=\"".base_url()."img/double_yellow_card.gif\" />";
                $sRojaDirecta="<img src=\"".base_url()."img/red_card.gif\" />";
                for ($i=0;$i<count($asResult['DATOS']);$i++) {
                    $asDatosReporte[$i]= array (
                        'TX ' => "<img src=\"".base_url()."img/escudos/mini/s".$asResult['DATOS'][$i]['logo'].".gif\" border=\"0\">",
                        'TX Club' => $asResult['DATOS'][$i]['nombre_corto'],
                        'TX Nombre' => $asResult['DATOS'][$i]['nombre'],
                        'TX '.$sAmarillas => $asResult['DATOS'][$i]['amarillas'],
                        'TX '.$sRojaAmarillas => $asResult['DATOS'][$i]['roja_amarillas'],
                        'TX '.$sRojaDirecta => $asResult['DATOS'][$i]['roja_directa'],
                        'TX Total' => $asResult['DATOS'][$i]['total'],
                    );
                }
                $sReporteResultado=$this->tools_lib->genera_reporte (array ('DATOS' => $asDatosReporte, 'TITULO' => 'Disciplina', 'ANCHO' => '700'));
            }
            else
                $asDatosReporte=array ('TX Error' => $asDatosReporte['ERROR']);
            $asPrincipal=array (
                'RUTA_RAIZ' => base_url(),
                'RESULTADO' =>  $sReporteResultado
            );
            $asContenido=array (
                'PRINCIPAL' => $this->load->view('torneos/tarjetas_vw', $asPrincipal, true),
                'BARRA_DERECHA' => $this->Barra_derecha($piTemporada, $piTorneo),
            );
            $asControlador= array (
                'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/dos-columnas_vw", $asContenido, true),
                'TIPO_ACCESO' => 'PUBLIC'
            );
            $this->main_lib->display($asControlador);
        }

        function Documentos ($piTemporada, $piTorneo) {
            $asDoctos=$this->torneos_mod->ListaDocumentos($piTemporada, $piTorneo);
            if ($asDoctos['ESTATUS']!=-1) {
                $asReporte=array();
                for ($i=0;$i<count($asDoctos['DATOS']);$i++) {
                    $asReporte[$i]=array (
                        'TX Id' => $asDoctos['DATOS'][$i]['identificador'],
                        'TX Titulo' => "<a href=\"".base_url()."torneos/DetallesDocto/".$asDoctos['DATOS'][$i]['id_unico']."/".$piTemporada."/".$piTorneo."\">".$asDoctos['DATOS'][$i]['titulo']."</a>"
                    );
                }
            }
            else {
            
            
            
            
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
                    'RUTA_RAIZ' => base_url(),
                    'LINK_ADICIONAL' => "<a href=\"torneos/Documentos/".$piTemporada."/".$piTorneo."\">Lista de torneo</a>"
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
    
    }

?>
