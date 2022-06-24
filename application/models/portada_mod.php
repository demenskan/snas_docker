<?php

    class portada_mod extends CI_Model {

        function __construct() {
            parent::__construct();
            $this->load->library('Tools_lib');
        }

        function noticias() {
            $asArgs=array ('QUERY' => "SELECT * FROM noticias noti "
                                ." LEFT JOIN imagenes_noticias im ON noti.id_unico=im.id_noticia AND im.portada=1 "
                                ." ORDER BY fecha DESC LIMIT 0,12");
            $asResult=$this->tools_lib->consulta($asArgs);
            if ($asResult['ESTATUS']==1) {
                $asSalida=array ('RESUMENES' => '', 'IMAGENES' => '', 'COMPLEMENTARIAS' => '', 'VIEJAS' => '', 'TABULADORES' => '');
                $sSalidagc="";
                $sMargen="\t\t\t\t\t\t";
                $sClaseTab="ui-tabs-nav-item ui-tabs-selected";
                $asSeccion[4]="left";
                $asSeccion[5]="col3-mid left";
                $asSeccion[6]="right";
                for ($iCont=0;$iCont<count($asResult['DATOS']);$iCont++) {
                    //RECIENTES
                    /*Pone los tabuladores de las noticias*/
                    if ($iCont<3) {
                        $asSalida['TABULADORES'].="<li class=\"".$sClaseTab."\" id=\"nav-fragment-".($iCont+1)."\">\n"
                                .$sMargen."\t<a href=\"#fragment-".($iCont+1)."\">\n"
                                .$sMargen."\t\t<span>[".substr($asResult['DATOS'][$iCont]["titulo"],0,24)."...]</span>\n"
                                .$sMargen."\t</a>\n"
                                .$sMargen."</li>";
                        /*Los contenidos de las noticias*/
                        $asSalida['RESUMENES'].="<div id=\"fragment-".($iCont+1)."\" class=\"ui-tabs-panel\" style=\"\">\n"
                                .$sMargen."\t<h2>".substr($asResult['DATOS'][$iCont]['titulo'],0,47)."</h2>\n"
                                .$sMargen."\t\t<p>".$asResult['DATOS'][$iCont]["resumen"]."[<a href=\"".base_url()."noticias/ver/".$asResult['DATOS'][$iCont]["id_unico"]."\">Ver mas</a>]</p>\n"
                                .$sMargen."</div>\n";
                        /*Las imagenes a cargar*/
                        $asSalida['IMAGENES'].="#rotator #fragment-".($iCont+1)." { \n"
                                    .$sMargen."\t\tbackground:transparent url('".base_url().$asResult['DATOS'][$iCont]["url"]."') no-repeat top center;\n"
                                    .$sMargen."}\n";
                        $sClaseTab="ui-tabs-nav-item";
                    }
                    elseif ($iCont<6) {
                        //COMPLEMENTARIAS
                        $asSalida['COMPLEMENTARIAS'].="<div class=\"col3 ".$asSeccion[$iCont+1]."\">\n"
                           ."                   <div class=\"column-content\">\n"
                           ."                       <div class=\"post\">\n"
                           ."                           <p><a href=\"".base_url()."noticias/ver/".$asResult['DATOS'][$iCont]["id_unico"]."\"><img src=\"".base_url().$asResult['DATOS'][$iCont]["url"]."\" alt=\"nada\" class=\"bordered\" width=\"152\" /></a></p>\n"
                           ."                           <h4><a href=\"".base_url()."noticias/ver/".$asResult['DATOS'][$iCont]["id_unico"]."\">".$asResult['DATOS'][$iCont]["titulo"]."</a></h4>\n"
                           ."                           ".$asResult['DATOS'][$iCont]["resumen"]."<br/>\n"
                           ."                       </div>\n"
                           ."                   </div>\n"
                           ."               </div>\n";
                    }
                    else {
                        //VIEJAS
                        $asSalida['VIEJAS'].="<li> \n"
                            ."      <a href=\"".base_url()."noticias/ver/".$asResult['DATOS'][$iCont]["id_unico"]."\">".$asResult['DATOS'][$iCont]["titulo"]."</a>\n"
                            ."</li>\n";
                    }
                }
            }
            else {
                $asSalida=array ('RESUMENES' => $asResult['MENSAJE'], 'IMAGENES' => '', 'COMPLEMENTARIAS' => '', 'VIEJAS' => '');
            }
            return ($asSalida);
        }
    
        function resultados() {
            /*Resultados de partidos*/
            $asArgs=array (
                'QUERY' =>  "SELECT par.clave, par.id_torneo, par.id_temporada, el.iniciales as 'equipo_local', \n"
                        ."  el.ruta_logo as 'logo_local', ev.iniciales as 'equipo_visita', ev.ruta_logo as 'logo_visita', \n"
                        ."  par.marcador_local, par.marcador_visitante, tor.nombre, par.jornada \n"
                        ."  FROM partidos par \n"
                        ."  INNER JOIN clubes el On par.id_equipo_local=el.id_unico \n"
                        ."  INNER JOIN clubes ev On par.id_equipo_visitante=ev.id_unico \n"
                        ."  INNER JOIN torneos tor On par.id_torneo=tor.clave And par.id_temporada=tor.id_temporada \n"
                        ."  WHERE par.jugado=1 and par.id_temporada=".$this->main_lib->iTemporadaActual."\n"
                        ."  ORDER BY par.fecha_captura DESC LIMIT 0,10" );
            $asResultados=$this->tools_lib->consulta($asArgs);
            switch ($asResultados['ESTATUS']) {
                case 1:
                    $sSalida="";
                    $sTorneoActual="";
                    $sJornadaActual="";
                    $sTipoActual="";
                    $sMargen="\t\t\t\t\t\t\t\t";
                    for ($i=0;$i<count($asResultados['DATOS']);$i++) {
                        if ($asResultados['DATOS'][$i]["nombre"]!=$sTorneoActual) {
                            $sTorneoActual=$asResultados['DATOS'][$i]["nombre"];
                            $sSalida.=$sMargen."<li>\n"
                                .$sMargen."\t<div class=\"left\">&nbsp;</div>\n"
                                .$sMargen."\t<div class=\"right\">\n"
                                .$sMargen."\t\t<span class=more><a href=\"".base_url()."torneos/ver/".$asResultados['DATOS'][$i]['id_temporada']."/".$asResultados['DATOS'][$i]['id_torneo']."\">".$asResultados['DATOS'][$i]["nombre"]."</a></span>\n"
                                .$sMargen."\t</div><tr>\n"
                                .$sMargen."\t<div class=\"clearer\"></div>\n"
                                .$sMargen."<li>\n";
                        }
                        $sSalida.=$sMargen."<li>\n"
                            .$sMargen."\t<div class=\"left\">\n"
                            .$sMargen."\t\t<img src=\"".base_url()."img/escudos/mini/s".$asResultados['DATOS'][$i]["logo_local"].".gif\" width=\"20\">\n"
                            .$sMargen."\t\t<a href=\"".base_url()."torneos/detallepartido/".$asResultados['DATOS'][$i]["id_temporada"]."/".$asResultados['DATOS'][$i]["id_torneo"]."/".$asResultados['DATOS'][$i]["clave"]."\">".$asResultados['DATOS'][$i]["equipo_local"]."</a>\n"
                            .$sMargen."\t\t".$asResultados['DATOS'][$i]["marcador_local"]."\n"
                            .$sMargen."\t\t:\n"
                            .$sMargen."\t\t".$asResultados['DATOS'][$i]["marcador_visitante"]."\n"
                            .$sMargen."\t\t<a href=\"".base_url()."torneos/detallepartido/".$asResultados['DATOS'][$i]["id_temporada"]."/".$asResultados['DATOS'][$i]["id_torneo"]."/".$asResultados['DATOS'][$i]["clave"]."\">".$asResultados['DATOS'][$i]["equipo_visita"]."</a>\n"
                            .$sMargen."\t\t<img src=\"".base_url()."img/escudos/mini/s".$asResultados['DATOS'][$i]["logo_visita"].".gif\" width=\"20\">\n"
                            .$sMargen."\t</div>\n"
                            .$sMargen."\t<div class=\"right\">\n"
                            .$sMargen."\t</div>\n"
                            .$sMargen."\t<div class=\"clearer\"></div>\n"
                            .$sMargen."</li>\n";
                    }
                    break;
                case 0:
                        $sSalida="No hay partidos en la temporada actual";
                    break;
                case -1:
                        $sSalida=$asResultados['ERROR'];
                    break;
            }
            return ($sSalida);
        }

        function goleadores($piModo="total", $piTotal="10") {
            $sCondicion=($piModo=="temporada") ? "   AND eve.id_temporada=".$this->main_lib->iTemporadaActual : "";
            $asArgs=array ('QUERY' => "SELECT jug.id_unico, jug.nombre, count(eve.id_unico) as 'goles' \n"
                                    ."  FROM jugadores jug \n"
                                    ."  INNER JOIN eventos eve On jug.id_unico=eve.id_jugador \n"
                                    ."  WHERE (eve.id_tipo=1 OR eve.id_tipo=6) \n"
                                    .$sCondicion
                                    ."  GROUP BY jug.id_unico \n"
                                    ."  ORDER BY goles DESC LIMIT 0,".$piTotal);
            $asGoleadores=$this->tools_lib->consulta($asArgs);
            switch ($asGoleadores['ESTATUS']) {
                case 1:
                    $sSalida="";
                    $sMargen="\t\t\t\t\t";
                    for ($iCont=1;$iCont<=count($asGoleadores['DATOS']);$iCont++) {
                        if ($iCont>1) {
                            if ($asGoleadores['DATOS'][$iCont-2]["goles"]!=$asGoleadores['DATOS'][$iCont-1]["goles"])
                                $iPlace=$iCont;
                        }
                        else
                            $iPlace=1;
                        $sSalida.=$sMargen."<li>\n"
                            .$sMargen."\t<div class=\"left\">\n"
                            .$sMargen."\t\t<span class=\"quiet\">".$iPlace.".</span> <a href=\"generador.php?code=goleadores&id=".$asGoleadores['DATOS'][$iCont-1]["id_unico"]."\">".htmlspecialchars($asGoleadores['DATOS'][$iCont-1]["nombre"])."</a>\n"
                            .$sMargen."\t</div>\n"
                            .$sMargen."\t<div class=\"right\">".$asGoleadores['DATOS'][$iCont-1]["goles"]."</div>\n"
                            .$sMargen."\t<div class=\"clearer\">&nbsp;</div>\n"
                            .$sMargen."</li>\n";
                    }
                    break;
                case 0:
                    $sSalida="No hay goleo (piuuu)";
                    break;
                case -1:
                    $sSalida=$asGoleadores['ERROR'];
                    break;
            }
            return($sSalida);
        }

        function editoriales() {
            $asEditoriales=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT ed.titulo, ca.imagen, ed.id_unico \n"
                            ."  FROM editoriales ed \n"
                            ."  INNER JOIN cat_autores ca On ed.id_columna=ca.id_unico \n"
                            ."  ORDER BY ed.fecha DESC LIMIT 0,3"));
            if ($asEditoriales['ESTATUS']==1){
                $sEditorial="";
                for ($i=0;$i<count($asEditoriales['DATOS']);$i++) {
                    $sEditorial.="<div class=\"post\">\n"
                                ."                  <p><a href=\"".base_url()."editoriales/ver/".$asEditoriales['DATOS'][$i]["id_unico"]."\"><img src=\"".base_url()."img/columnas/".$asEditoriales['DATOS'][$i]["imagen"]."\" alt=\"\" class=\"bordered\" width=\"185\" height=\"100\" /></a></p>\n"
                                ."                  <h5><a href=\"".base_url()."editoriales/ver/".$asEditoriales['DATOS'][$i]["id_unico"]."\">".$asEditoriales['DATOS'][$i]["titulo"]."</a></h5>"
                                ."              </div>\n"
                                ."              <div class=\"content-separator\"></div>";
                }
            }
            else
                $sEditorial=$asEditoriales["Error"];
            return ($sEditorial);
        }
        
        function banners () {
            $asBanners=$this->tools_lib->consulta(array(
                'QUERY' => "SELECT nombre_archivo FROM banners WHERE activado=1 ORDER BY Rand() LIMIT 1"));
            if ($asBanners['ESTATUS']==1) 
                $sOutput="<img src=\"".base_url()."img/banners/".$asBanners['DATOS'][0]["nombre_archivo"]."\" height=\"100\" width=\"150\" alt=\"\" />\n";
            else
                $sOutput="";
            return($sOutput);
        }
        
        function torneos () {
            $asTorneos=$this->tools_lib->consulta(array(
                'QUERY' => "SELECT tor.clave, tor.nombre, tor.minilogo, tor.id_temporada \n"
                        ."  FROM torneos tor \n"
                        ."  WHERE tor.id_temporada=".$this->main_lib->iTemporadaActual." AND tor.estatus=1 ORDER BY tor.clave"));
            if ($asTorneos['ESTATUS']==1){
                $sTorneos="";
                for ($i=0;$i<count($asTorneos['DATOS']);$i++) {
                    $sTorneos.="<li>\n"
                                ."                  <a href=\"".base_url()."torneos/ver/".$asTorneos['DATOS'][$i]["id_temporada"]."/".$asTorneos['DATOS'][$i]['clave']."\"><img src=\"".base_url()."img/torneos/".$asTorneos['DATOS'][$i]["minilogo"]."\" /></a>"
                                ."              </li>\n";
                }
            }
            else
                $sTorneos=$asTorneos['MENSAJE'];
            return ($sTorneos);
        }
    }
?>
