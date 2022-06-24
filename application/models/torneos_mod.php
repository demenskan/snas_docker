<?php

    class torneos_mod extends CI_Model {
    
        function __construct() {
            parent::__construct();
            $this->load->library('Tools_lib');
        }
        
        function NombreTorneo($psTemporada, $psClave) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT nombre FROM torneos WHERE id_temporada=".$psTemporada." AND clave=".$psClave,
                        'UNICA_FILA' => true)
            );
            switch ($asResult['ESTATUS']) {
                case 1:
                    return ($asResult['DATOS']['nombre']);
                    break;
                case 0:
                    return ('NOT_FOUND');
                    break;
                case -1:
                    return ($asResult['ERROR']);
                    break;
            }
        }
        
        function DatosTorneo ($psTemporada, $psClave) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT * FROM torneos WHERE id_temporada=".$psTemporada." AND clave=".$psClave,
                        'UNICA_FILA' => true)
            );
            return ($asResult);
        }
        
        function Noticias ($psTemporada, $psClave) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT news.id_unico, news.titulo, news.resumen, news.imagen, img.url as 'url_imagen' "
                                ." FROM noticias news "
                                ."  INNER JOIN tags_noticias tags ON news.id_unico=tags.id_docto AND tags.tipo_docto=1 "
                                ."  LEFT JOIN imagenes_noticias img ON img.id_noticia=news.id_unico And img.portada=1 "
                                ."WHERE tags.campo='torneo' AND tags.valor='".$psTemporada.",".$psClave."'"
                                ." ORDER BY news.id_unico DESC ",
                        )
            );
            return ($asResult);
        }
    
        function MaximoJornadas ($psTemporada, $psClave, $psTipo=null) {
            $sTipo=($psTipo==null) ? "" : " AND tipo=".$psTipo;
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT max(jornada) as maximojornada FROM partidos WHERE id_torneo=".$psClave." AND id_temporada=".$psTemporada
                                .$sTipo, 'UNICA_FILA' => true));
            switch ($asResult['ESTATUS']) {
                case 1:
                    $sSalida=$asResult['DATOS']['maximojornada'];
                    break;
                case 0:
                    $sSalida="0";
                case -1:
                    $sSalida="0";
            }
            return ($sSalida);
        }

        function InicializaTabla ($psTemporada, $psClave) {
            $asClubes=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT ag.id_equipo, ag.grupo, cl.nombre_corto, cl.ruta_logo "
                                ."FROM acomodo_grupos ag "
                                ."INNER JOIN clubes cl ON ag.id_equipo=cl.id_unico "
                                ."WHERE ag.id_temporada=".$psTemporada." AND ag.id_torneo=".$psClave));
            if ($asClubes['ESTATUS']==1) {
                for ($i=0;$i<count($asClubes['DATOS']);$i++) {
                    $asResult[$i]=array (
                        'clave' => $asClubes['DATOS'][$i]['id_equipo'],
                        'grupo' => $asClubes['DATOS'][$i]['grupo'],
                        'nombre' => $asClubes['DATOS'][$i]['nombre_corto'],
                        'ruta_logo' => $asClubes['DATOS'][$i]['ruta_logo'],
                        'JJ' => 0,'JG' => 0,'JE' => 0,'JP' => 0,
                        'GF' => 0,'GC' => 0,'DIF' => 0,'PTS' => 0,
                        'L' => 0,'SPE' => 0
                    );
                }
            }
            else
                $asResult=array();
            return ($asResult);
        }
        
        function PartidosJugados ($psTemporada, $psClave, $piJornadaMax, $piJornadaMin) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT id_equipo_local, id_equipo_visitante, marcador_local, marcador_visitante "
                                ." FROM partidos p" 
                                ." WHERE p.id_temporada=".$psTemporada." and p.id_torneo=".$psClave." and jugado=1"
                                ."  AND p.tipo=1 And p.jornada<=".$piJornadaMax." And p.jornada>=".$piJornadaMin));
            return ($asResult);
        }

        function ListaJornadas ($psTemporada, $psClave) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT  distinct par.jornada as 'clave', par.jornada as 'leyenda' "
                                    ."FROM partidos par "
                                    ."INNER JOIN torneos tor ON par.id_torneo=tor.clave AND par.id_temporada=tor.id_temporada "
                                    ."WHERE tor.id_temporada=".$psTemporada." AND tor.clave=".$psClave." "
                                    ."ORDER BY par.jornada"));
            return ($asResult);
        }
        function EquiposTorneo ($psTemporada, $psClave) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT ag.id_equipo as 'clave', cl.nombre_corto as 'leyenda' "
                                ."FROM acomodo_grupos ag "
                                ."INNER JOIN clubes cl On ag.id_equipo=cl.id_unico "
                                ."WHERE ag.id_temporada=".$psTemporada." AND ag.id_torneo=".$psClave." "
                                ."ORDER BY ag.id_equipo"));
            return ($asResult);
        }
        
        function CalendarioEquipo ($piTemporada, $piTorneo, $piClub) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT pa.jornada, cla.nombre_corto as nombre_local, clb.nombre_corto as nombre_visita, "
                                ."cla.ruta_logo as logo_local, clb.ruta_logo as logo_visita, "
                                ."marcador_local, marcador_visitante, jornada, pa.clave as clave_partido, ces.nombre as nombreestadio, pa.jugado, "
                                ."pa.id_equipo_local, pa.id_equipo_visitante, pa.fecha_jugado, ctp.descripcion as 'tipo_partido' "
                                ."FROM partidos pa \n"
                                ."INNER JOIN clubes cla On pa.id_equipo_local=cla.id_unico \n"
                                ."INNER JOIN clubes clb On pa.id_equipo_visitante=clb.id_unico \n"
                                ."INNER JOIN cat_estadios ces On pa.id_estadio=ces.id_unico \n"
                                ."INNER JOIN cat_tipos_partido ctp On pa.tipo=ctp.id_unico \n"
                                ."WHERE pa.id_temporada=".$piTemporada." and pa.id_torneo=".$piTorneo
                                ."      and (pa.id_equipo_local=".$piClub." Or pa.id_equipo_visitante=".$piClub.")"
                        )
            );
            return ($asResult);
        }
                
        function JornadasDescanso ($piTemporada, $piTorneo, $piClub) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT jornada "
                                    ."FROM descansos_torneos "
                                    ."WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo
                                    ." AND id_equipo=".$piClub
                        )
            );
            return ($asResult);
        }

        function ListaTorneos ($piTemporada) {
            if ($piTemporada=='__EXTRA__')
                $sCondicion=" ORDER BY tor.id_temporada, cct.orden";
            else
                $sCondicion="WHERE tor.id_temporada=".$piTemporada." ORDER BY cct.orden";
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT tor.*, cct.descripcion as 'nombre_clase' "
                                ."FROM torneos tor "
                                ."INNER JOIN cat_clases_torneos cct ON tor.clase=cct.id_unico "
                                .$sCondicion
                        )
            );
            return ($asResult);
        }
        
        function ListaPartidosJornada ($piTemporada, $piTorneo, $piJornada) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "Select eqa.nombre_corto as nombre_local, eqb.nombre_corto as nombre_visita, "
                ."eqa.ruta_logo as logo_local, eqb.ruta_logo as logo_visita, "
                ."marcador_local, marcador_visitante, jornada, pa.clave, pa.jugado, ctp.descripcion as 'tipo_partido' "
                ."From partidos pa \n"
                ."Inner Join clubes eqa On pa.id_equipo_local=eqa.id_unico \n"
                ."Inner Join clubes eqb On pa.id_equipo_visitante=eqb.id_unico \n"
                ."INNER JOIN cat_tipos_partido ctp On pa.tipo=ctp.id_unico \n"
                ."Where pa.id_temporada=".$piTemporada." and pa.id_torneo=".$piTorneo." AND pa.jornada=".$piJornada
                )
            );
            return ($asResult);
        }
        function EquiposQueDescansan($piTemporada, $piTorneo, $piJornada) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT cl.id_unico, cl.nombre, cl.nombre_corto  "
                                    ."FROM  descansos_torneos dt "
                                    ."LEFT JOIN clubes cl ON dt.id_equipo=cl.id_unico "
                                    ."WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo
                                    ." AND jornada=".$piJornada
                        )
            );
            return ($asResult);
        }
        
        function GeneralesPartido ($piTemporada, $piTorneo, $piClavePartido) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "Select eqa.nombre_corto as nombre_local, eqb.nombre_corto as nombre_visita, "
                                    ."eqa.ruta_logo as logo_local, eqb.ruta_logo as logo_visita, "
                                    ."eqa.color_frente as cfrente_local, eqa.color_fondo as cfondo_local, eqa.color_extra as cextra_local, "
                                    ."eqb.color_frente as cfrente_visita, eqb.color_fondo as cfondo_visita, eqb.color_extra as cextra_visita, "
                                    ."marcador_local, marcador_visitante, jornada, pa.clave, ces.nombre as nombreestadio, pa.jugado, "
                                    ." pa.id_equipo_local, pa.id_equipo_visitante, "
                                    ." pa.fecha_jugado, pa.comentarios, pa.fecha_captura, pa.jornada, pa.tipo as 'tipo_partido_num', "
                                    ." ctp.descripcion as 'tipo_partido_desc', ctp.tipo as 'ida_vuelta' "
                                    ."From partidos pa \n"
                                    ."Inner Join clubes eqa On pa.id_equipo_local=eqa.id_unico \n"
                                    ."Inner Join clubes eqb On pa.id_equipo_visitante=eqb.id_unico \n"
                                    ."Inner Join cat_estadios ces On pa.id_estadio=ces.id_unico \n"
                                    ."Inner Join cat_tipos_partido ctp On pa.tipo=ctp.id_unico \n"
                                    ."Where pa.id_temporada=".$piTemporada." and pa.id_torneo=".$piTorneo
                                    ."      and pa.clave=".$piClavePartido,
                        'UNICA_FILA' => true)
            );
            return ($asResult);
        }

        function EventosPartido ($piTemporada, $piTorneo, $piClavePartido) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT jug.nombre, ev.minuto, ctev.imagen, rpt.id_equipo, ctev.descripcion  \n"
                                    ." FROM eventos ev \n"
                                    ."      INNER JOIN jugadores jug ON ev.id_jugador=jug.id_unico \n"
                                    ."      INNER JOIN cat_tipos_evento ctev ON ev.id_tipo=ctev.id_unico \n"
                                    ."      INNER JOIN rostersportemporada rpt ON ev.id_jugador=rpt.id_jugador AND rpt.id_temporada=".$piTemporada."\n"
                                    ."  WHERE \n"
                                    ."      ev.id_temporada=".$piTemporada." AND ev.id_torneo=".$piTorneo."\n"
                                    ."      AND ev.id_partido=".$piClavePartido."\n"
                                    ." ORDER BY minuto \n")
            );
            return ($asResult);
        }

        function GoleoTorneo ($piTemporada, $piTorneo, $piJornadaInicial=0, $piJornadaFinal=0, $piClaveClub="", $piLimite=10) {
            $sCondicionJornadaMin=($piJornadaInicial!=0) ? " And par.jornada>=".$piJornadaInicial." " : "" ;
            $sCondicionJornadaMax=($piJornadaFinal!=0) ? " And par.jornada<=".$piJornadaFinal." " : "" ;
            $sCondicionClub=(($piClaveClub!="") && ($piClaveClub!="__EXTRA__"))? " And rpt.id_equipo=".$piClaveClub." ": "";
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "Select jug.nombre, cl.nombre_corto, jug.id_unico, cl.id_unico as 'id_club', "
                        ."cl.ruta_logo as logo, Count(*) as goles "
                        ."FROM jugadores jug \n"
                        ."INNER JOIN eventos ev On jug.id_unico=ev.id_jugador \n"
                        ."INNER JOIN rostersportemporada rpt On rpt.id_jugador=jug.id_unico \n"
                        ."INNER JOIN clubes cl On rpt.id_equipo=cl.id_unico and rpt.id_temporada=".$piTemporada." \n"
                        ."INNER JOIN partidos par On par.id_temporada=ev.id_temporada AND par.id_torneo=ev.id_torneo AND par.clave=ev.id_partido "
                        ."WHERE ev.id_temporada=".$piTemporada." and ev.id_torneo=".$piTorneo
                        ." And (ev.id_tipo=1 Or ev.id_tipo=6)"
                        .$sCondicionJornadaMin
                        .$sCondicionJornadaMax
                        .$sCondicionClub
                        ." GROUP BY jug.nombre, cl.nombre_corto, cl.ruta_logo, jug.id_unico, cl.id_unico "
                        ." ORDER BY goles Desc"
                        ." Limit 0,".$piLimite
                )
            );
            return ($asResult);
        }
        
        function GoleoJugador ($piTemporada, $piTorneo, $piClaveJugador) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "Select ev.minuto, ev.id_tipo as 'tipo', cl.nombre_corto, cl.id_unico as 'equipo_rival', \n"
                            ."cl.ruta_logo as logo, rpt.id_equipo, par.clave, jug.nombre as 'nombre_jugador', par.jornada  \n"
                            ."FROM eventos ev \n"
                            ."INNER JOIN jugadores jug On jug.id_unico=ev.id_jugador \n"
                            ."INNER JOIN partidos par On par.id_temporada=ev.id_temporada and par.id_torneo=ev.id_torneo and par.clave=ev.id_partido \n"
                            ."INNER JOIN rostersportemporada rpt On rpt.id_jugador=ev.id_jugador and rpt.id_temporada=ev.id_temporada \n"
                            ."INNER JOIN clubes cl On ((cl.id_unico=par.id_equipo_local) And par.id_equipo_local<>rpt.id_equipo) or ((cl.id_unico=par.id_equipo_visitante) And par.id_equipo_visitante<>rpt.id_equipo)  \n"
                            ."INNER JOIN cat_tipos_evento cte on ev.id_tipo=cte.id_unico \n"
                            ."WHERE ev.id_jugador=".$piClaveJugador." And ev.id_temporada=".$piTemporada." And ev.id_torneo=".$piTorneo."\n"
                            ." And (ev.id_tipo=1 Or ev.id_tipo=6)\n"
                            ." ORDER BY par.jornada"
                )
            );
            return ($asResult);
        }

        function Tarjetas ($piTemporada, $piTorneo) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT jug.nombre, cl.nombre_corto,  \n"
                                    ."cl.ruta_logo as logo,  \n"
                                    ."  (Select count(*) from eventos ex where ex.id_temporada=ev.id_temporada and ex.id_torneo=ev.id_torneo and ex.id_jugador=ev.id_jugador and ex.id_tipo=2) as amarillas, \n"
                                    ."  (Select count(*) from eventos ex where ex.id_temporada=ev.id_temporada and ex.id_torneo=ev.id_torneo and ex.id_jugador=ev.id_jugador and ex.id_tipo=3) as roja_amarillas, \n"
                                    ."  (Select count(*) from eventos ex where ex.id_temporada=ev.id_temporada and ex.id_torneo=ev.id_torneo and ex.id_jugador=ev.id_jugador and ex.id_tipo=4) as roja_directa, \n"
                                    ." Count(*) as total \n"
                                    ."FROM jugadores jug \n"
                                    ."INNER JOIN eventos ev On jug.id_unico=ev.id_jugador \n"
                                    ."INNER JOIN rostersportemporada rpt On rpt.id_jugador=jug.id_unico \n"
                                    ."INNER JOIN clubes cl On rpt.id_equipo=cl.id_unico and rpt.id_temporada=".$piTemporada." \n"
                                    ."Where ev.id_temporada=".$piTemporada." and ev.id_torneo=".$piTorneo."\n"
                                    ." And (ev.id_tipo=2 Or ev.id_tipo=3 Or ev.id_tipo=4)\n"
                                    ." Group by jug.nombre, cl.nombre_corto, cl.ruta_logo \n"
                                    ." Order by total Desc\n"
                )
            );
            return ($asResult);
        }
        
        function ListaDocumentos ($piTemporada, $piTorneo) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT titulo, identificador, fecha, id_unico  \n"
                                    ."FROM documentos \n"
                                    ."WHERE tipo='torneo' and codigo='".$piTemporada."/".$piTorneo."' \n"
                                    ." ORDER BY id_unico\n"
                )
            );
            return ($asResult);
        
        }
        function ListaTorneosBusqueda ($piTemporada, $psSearch) {
            if ($piTemporada=='__EXTRA__')
                $sCondicion="WHERE nombre LIKE '%".$psSearch."%'";
            else
                $sCondicion="WHERE nombre LIKE '%".$psSearch."%' AND id_temporada=".$piTemporada;
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT * FROM torneos ".$sCondicion." Order By id_temporada, clave")
            );
            return ($asResult);
        }

        function getClaveTorneo ($piNombre) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT id_temporada, clave FROM torneos WHERE nombre=".$piNombre )
            );
            return ($asResult);
        }
        
        function getListaNombres ($piBusqueda) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT nombre FROM torneos WHERE nombre LIKE '%".$psSearch."%' Order By id_temporada, clave")
            );
            return ($asResult);
        }
        
        function getRangosClaves ($piTemporada, $piClaveMinima, $piClaveMaxima) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT id_temporada, clave, nombre "
                                ." FROM  torneos "
                                ." WHERE id_temporada<=".$piTemporada
                                ." and clave>=".$piClaveMinima." and clave<=".$piClaveMaxima)
            );
            return ($asResult);
        }

        function insertaTorneo ($pasInput) {
            $sLogoFileName=($pasInput['tipo_logo']=="archivo") ? $this->main_lib->sRutaLogosTorneos."/".$pasInput['file_name'] : $pasInput['ruta_galeria'] ;
            $asResult=$this->tools_lib->ejecutar_query (array
                ('QUERY' => "INSERT INTO torneos (id_temporada, clave, nombre, descripcion, logotipo, "
                                ." estatus, tipo, clase) values "
                                ." (".$pasInput['temporada'].",".$pasInput['clave'].",'".$pasInput['nombre']."','".$pasInput['descripcion']
                                ."','".$sLogoFileName."',0, "
                                .$pasInput['tipo'].",".$pasInput['clase'].")")
            );
            return ($asResult);
        }
        
        function actualizaTorneo ($pasInput) {
            $sLogoFileName=($pasInput['tipo_logo']=="archivo") ? $this->main_lib->sRutaLogosTorneos."/".$pasInput['file_name'] : $pasInput['ruta_galeria'] ;
            $asResult=$this->tools_lib->ejecutar_query (array
                ('QUERY' => "UPDATE torneos SET nombre='".$pasInput['nombre']."', descripcion='".$pasInput['descripcion']."',"
                                ."logotipo='".$sLogoFileName."', "
                                ." estatus=".$pasInput['estatus'].", tipo=".$pasInput['tipo'].", clase= ".$pasInput['clase']
                                ." WHERE id_temporada=".$pasInput['temporada']." AND clave=".$pasInput['clave']));
            return ($asResult);
        }
    
        function getGruposTorneo ($piTemporada, $piClave) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT cl.id_unico, aco.grupo, cl.nombre_corto, cl.ruta_logo  \n"
                                    ." FROM acomodo_grupos aco \n"
                                    ." INNER JOIN clubes cl On cl.id_unico=aco.id_equipo \n"
                                    ." WHERE aco.id_temporada=".$piTemporada." AND aco.id_torneo=".$piClave
                                    ." ORDER BY aco.grupo ")
            );
            return ($asResult);
        }
        
        function getListaGrupos ($piTemporada, $piTorneo) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT DISTINCT grupo FROM acomodo_grupos WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo)
            );
            return ($asResult);
        }

        function getPartidosGrupo ($piTemporada, $piTorneo, $psGrupo) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT loc.nombre_corto as 'club_local', loc.ruta_logo as 'logo_local',   \n"
                                    ." vis.nombre_corto as 'club_visita', vis.ruta_logo as 'logo_visita', \n"
                                    ." par.marcador_local, par.marcador_visitante, par.jugado, par.clave as 'clave_partido',  "
                                    ." par.jornada "
                                    ." FROM partidos par \n"
                                    ." INNER JOIN clubes loc ON loc.id_unico=par.id_equipo_local \n"
                                    ." INNER JOIN clubes vis ON vis.id_unico=par.id_equipo_visitante \n"
                                    ." INNER JOIN acomodo_grupos aco ON aco.id_temporada=par.id_temporada AND aco.id_torneo=par.id_torneo AND aco.id_equipo=par.id_equipo_local "
                                    ." WHERE par.id_temporada=".$piTemporada." AND par.id_torneo=".$piTorneo
                                    ."  AND aco.grupo='".$psGrupo."'"
                                    ." ORDER BY par.jornada ")
            );
            return ($asResult);
        }

        function InsertaClubGrupo ($piTemporada, $piClave, $piClub, $psGrupo, $psColor="NULL") {
            $asResult=$this->tools_lib->ejecutar_query (array
                ('QUERY' => "INSERT INTO acomodo_grupos (id_temporada, id_torneo, grupo, id_equipo, color) "
                    ." VALUES (".$piTemporada.",".$piClave.",'".$psGrupo."',".$piClub.",'".$psColor."')")
            );
            return ($asResult);
        }

        function InsertaDescanso ($piTemporada, $piTorneo, $piClub, $piJornada) {
            $asResult=$this->tools_lib->ejecutar_query (array
                ('QUERY' => "INSERT INTO descansos_torneos (id_temporada, id_torneo, id_equipo, jornada) "
                    ." VALUES (".$piTemporada.",".$piTorneo.",".$piClub.",".$piJornada.")")
            );
            return ($asResult);
        }

        function BorraClubGrupo ($piTemporada, $piClave, $piClub, $psGrupo) {
            $asResult=$this->tools_lib->ejecutar_query (array('QUERY' => "DELETE FROM acomodo_grupos "
                                ."WHERE id_temporada=".$piTemporada." AND id_torneo=".$piClave
                                ." AND grupo='".$psGrupo."' AND id_equipo=".$piClub));
            return ($asResult);
        }

        function getParticipantes ($piTemporada, $piClave) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT cl.id_unico, cl.nombre_corto "
                    ." FROM clubes cl "
                    ." INNER JOIN acomodo_grupos aco On cl.id_unico=aco.id_equipo "
                    ." WHERE aco.id_temporada=".$piTemporada." AND aco.id_torneo=".$piClave
                    ." ORDER BY nombre_corto")
            );
            return ($asResult);
        }

        function getCalendario($piTemporada, $piClave, $piJornada=-1) {
            $sCondicionJornada=($piJornada<>-1) ? " AND par.jornada=".$piJornada : "";
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT cl1.nombre_corto as 'NombreLocal', cl2.nombre_corto as 'NombreVisitante', \n"
                    ." cl1.ruta_logo as 'LogoLocal', cl2.ruta_logo as 'LogoVisitante', par.clave, par.id_temporada, \n"
                    ." par.id_torneo, ctp.descripcion as 'TipoPartido', est.nombre as 'Estadio', par.jornada, par.jugado,  "
                    ." par.marcador_local, par.marcador_visitante, "
                    ." (select count(*) from alineaciones alix "
                    ."   where alix.id_temporada=par.id_temporada "
                    ."     and alix.id_torneo=par.id_torneo "
                    ."     and alix.id_partido=par.clave) as alineaciones,  "
                    ." par.designado_local, par.designado_visita "
                    ." FROM partidos par \n"
                    ." INNER JOIN clubes cl1 On cl1.id_unico=par.id_equipo_local \n"
                    ." INNER JOIN clubes cl2 On cl2.id_unico=par.id_equipo_visitante \n"
                    ." INNER JOIN cat_tipos_partido ctp On par.tipo=ctp.id_unico \n"
                    ." LEFT JOIN cat_estadios est On est.id_unico=par.id_estadio \n"
                    ." WHERE par.id_temporada=".$piTemporada." AND par.id_torneo=".$piClave.$sCondicionJornada."\n"
                    ." ORDER BY par.tipo, par.jornada, par.clave ")
            );
            return ($asResult);
        }
        
        function partidos_designados_get($piTemporada, $piTorneo, $piJornada) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT  "
                    ." par.designado_local, par.designado_visita "
                    ." FROM partidos par \n"
                    ." WHERE par.id_temporada=".$piTemporada." AND par.id_torneo=".$piTorneo."\n"
                    ." AND par.jornada=".$piJornada
                    ." AND par.designado_local IS NOT NULL "
                    ." AND par.designado_visita IS NOT NULL"
                )
            );
            return ($asResult);
        }

        function getMaxJornadas ($piTemporada, $piTorneo) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT max(jornada) as MaxJornada \n"
                                    ." FROM partidos \n"
                                    ." WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo,
                        'UNICA_FILA' => true)
            );
            return ($asResult);
        }

        function getJugadoresPartido ($piTemporada, $piTorneo, $piClave) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT jug.nombre as 'Nombre', jug.id_unico as 'ID_NUMBER', IF(rt.id_Equipo=par.id_equipo_local,'L','V') as estatus,  "
                            ." rt.numero, eq.iniciales "
                            ." FROM jugadores jug "
                            ." INNER JOIN rostersportemporada rt ON jug.id_unico = rt.id_jugador "
                            ." INNER JOIN partidos par ON (par.id_equipo_local=rt.id_equipo OR par.id_equipo_visitante=rt.id_equipo) AND par.id_temporada=rt.id_temporada "
                            ." INNER JOIN clubes eq ON rt.id_equipo=eq.id_unico"
                            ." WHERE par.id_temporada=".$piTemporada." AND par.id_torneo=".$piTorneo." AND par.clave=".$piClave
                            ." ORDER BY jug.posicion_registrada")
            );
            return ($asResult);
        }

        function getMaxClavePartido ($piTemporada, $piTorneo) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT max(clave) as MaxClave \n"
                                    ." FROM partidos \n"
                                    ." WHERE id_temporada=".$piTemporada." AND id_torneo=".$piTorneo,
                        'UNICA_FILA' => true)
            );
            return ($asResult);
        }
        
        function getPartidosJornada($piTemporada, $piTorneo, $piJornada) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT par.id_equipo_local, par.id_equipo_visitante, par.tipo, cl.id_estadio, par.clave "
                        ." FROM partidos par "
                        ." INNER JOIN clubes cl ON cl.id_unico=par.id_equipo_visitante "
                        ." WHERE par.jornada=".$piJornada
                        ." AND par.id_torneo=".$piTorneo." AND par.id_temporada=".$piTemporada
                )
            );
            return ($asResult);
        }
        
        function getPartidosPlayoffs($piTemporada, $piTorneo) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT cla.nombre_corto as nombre_local, clb.nombre_corto as nombre_visita, "
                        ."cla.ruta_logo as logo_local, clb.ruta_logo as logo_visita, "
                        ."marcador_local, marcador_visitante, jornada, pa.clave, ctp.descripcion as 'tipo_partido', "
                        ."pa.jugado, pa.fecha_jugado, est.nombre as 'estadio', ctp.tipo as 'ida_vuelta' "
                        ."FROM partidos pa \n"
                        ."INNER JOIN clubes cla On pa.id_equipo_local=cla.id_unico \n"
                        ."INNER JOIN clubes clb On pa.id_equipo_visitante=clb.id_unico \n"
                        ."INNER JOIN cat_tipos_partido ctp On pa.tipo=ctp.id_unico \n"
                        ."LEFT  JOIN cat_estadios est ON pa.id_estadio=est.id_unico \n"
                        ."WHERE pa.id_temporada=".$piTemporada." and pa.id_torneo=".$piTorneo
                        ."      and pa.tipo<>1 "
                        ."ORDER BY pa.tipo"
                )
            );
            return ($asResult);
        }

        function getEspecialesPartido($piTemporada, $piTorneo, $piPartido) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT * "
                        ."FROM especiales_partido ep \n"
                        ."WHERE ep.id_temporada=".$piTemporada." and ep.id_torneo=".$piTorneo
                        ."      and ep.id_partido=".$piPartido,
                        'UNICA_FILA' => true
                )
            );
            return ($asResult);
        }

        function getClubesFaltantes($piTemporada, $piTorneo, $piJornada) {
            $asResult=$this->tools_lib->consulta(
                array   ('QUERY' => "SELECT aco.id_equipo "
                    ." FROM acomodo_grupos aco "
                    ." WHERE aco.id_torneo=".$piTorneo." AND aco.id_temporada=".$piTemporada
                    ." AND aco.id_equipo NOT IN "
                    ."      (Select parx.id_equipo_local "
                    ."          from partidos parx "
                    ."          where parx.id_temporada=aco.id_temporada "
                    ."          and parx.id_torneo=aco.id_torneo "
                    ."          and jornada=".$piJornada.")"
                    ." AND aco.id_equipo NOT IN "
                    ."      (Select parx.id_equipo_visitante "
                    ."          from partidos parx "
                    ."          where parx.id_temporada=aco.id_temporada "
                    ."          and parx.id_torneo=aco.id_torneo "
                    ."          and jornada=".$piJornada.")"
                )
            );
            return ($asResult);
        }

        function getMarcadorGlobal ($piTemporada, $piTorneo, $piClaveVuelta) {
            $this->load->model('partidos_mod');
            $asVuelta=$this->partidos_mod->getDatos($piTemporada, $piTorneo, $piClaveVuelta);
            if ($asVuelta['ESTATUS']==1) {
                $asPartidosIda=$this->getPartidosJornada($piTemporada, $piTorneo, $asVuelta['DATOS']['jornada']-1);
                if ($asPartidosIda['ESTATUS']==1) {
                    for ($i=0;$i<count($asPartidosIda['DATOS']);$i++) {
                        if (($asPartidosIda['DATOS'][$i]['id_equipo_local']==$asVuelta['DATOS']['IDVisitante'])&&($asPartidosIda['DATOS'][$i]['id_equipo_visitante']==$asVuelta['DATOS']['IDLocal']))
                            $iClaveIda=$asPartidosIda['DATOS'][$i]['clave'];
                    }
                    $asIda=$this->partidos_mod->getDatos($piTemporada, $piTorneo, $iClaveIda);
                    return ( array (
                        'ESTATUS' => 1,
                        'LOCAL' => $asVuelta['DATOS']['marcador_local']+$asIda['DATOS']['marcador_visitante'],
                        'VISITA' => $asVuelta['DATOS']['marcador_visitante']+$asIda['DATOS']['marcador_local']
                    ));
                }
                else 
                    return (array('ESTATUS'=> -1));
            }
            else 
                return (array('ESTATUS'=> -1));
        }
    }
?>
