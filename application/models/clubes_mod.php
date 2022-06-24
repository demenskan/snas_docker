<?php

	class clubes_mod extends CI_Model {
	
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}

		function RegresaDatos($psClave) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT * FROM clubes WHERE id_unico=".$psClave,
						'UNICA_FILA' => true)
			);
			return ($asResult);
		}

		function RegresaDatosPorURL($psClave) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT cl.*, "
								." ce.nombre as 'nombre_estadio', ce.ubicacion, ce.capacidad,  "
								." con.id_unico as 'clave_consejo', con.nombre as 'nombre_consejo', con.imagen as 'imagen_consejo', con.iniciales "
								."FROM clubes cl "
								."LEFT JOIN cat_estadios ce ON cl.id_estadio=ce.id_unico "
								."LEFT JOIN cat_consejos con ON cl.id_consejo=con.id_unico "
								."WHERE ruta_logo='".$psClave."'",
						'UNICA_FILA' => true)
			);
			return ($asResult);
		}
				
		function RegresaNombre($psClave) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT nombre_corto FROM clubes WHERE id_unico=".$psClave,
						'UNICA_FILA' => true)
			);
			if ($asResult['ESTATUS']==1) 
				return ($asResult['DATOS']['nombre_corto']);
			else
				return ($asResult['ERROR']);
		}
	
		function Lista($paFiltro=array()) {
			$sCondicion="id_unico<>0";
			if (count($paFiltro)>0) {
				$sCondicion="";
				for ($i=0;$i<count($paFiltro);$i++) {
					$sCondicion.=$paFiltro[$i];
				}
			}
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT * From clubes WHERE ".$sCondicion." ORDER BY id_unico")
			);
			return ($asResult);
		}


		function Roster ($psClub, $piTemporada) {
			if ($piTemporada=="actual")
				$iTemporada=$this->main_lib->iTemporadaActual;
			else
				$iTemporada=$piTemporada;
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT jug.nombre, rpt.numero, jug.id_unico, jug.precio_base "
								."FROM rostersportemporada rpt "
								."INNER JOIN jugadores jug ON rpt.id_jugador=jug.id_unico "
								."INNER JOIN clubes cl ON rpt.id_equipo=cl.id_unico "
								."WHERE rpt.id_equipo=".$psClub." AND rpt.id_temporada=".$iTemporada ,
					)
			);
			return ($asResult);
		}
		
		function Noticias ($psClub) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT nt.id_unico, nt.titulo, nt.resumen, nt.imagen "
								."FROM noticias nt "
								."INNER JOIN tags_noticias tag ON nt.id_unico=tag.id_docto "
								."WHERE tag.tipo_docto=1 AND tag.campo='equipo' AND tag.valor='".$psClub."'"
								."ORDER BY fecha DESC LIMIT 0,12")
			);
			return ($asResult);
		}
		function Goleadores ($psClub) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT jug.id_unico, jug.nombre, count(ev.id_tipo) as goles "
								."FROM eventos ev "
								."INNER JOIN jugadores jug ON jug.id_unico=ev.id_jugador "
								."INNER JOIN rostersportemporada rpt ON ev.id_jugador=rpt.id_jugador AND ev.id_temporada=rpt.id_temporada  "
								."WHERE (ev.id_tipo=6 OR ev.id_tipo=1) AND rpt.id_equipo=".$psClub." "
								."GROUP BY jug.id_unico, jug.nombre "
								."ORDER BY goles DESC LIMIT 0,10")
			);
			return ($asResult);
		}

		function getDatosPorNombre ($psNombre) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT * FROM clubes WHERE nombre_corto like '%".$psNombre."%'")
			);
			return ($asResult);
		}

		function getDivision ($piClub) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT division FROM clubes WHERE id_unico=".$piClub,
						'UNICA_FILA' => true)
			);
			if ($asResult['ESTATUS']==1)
				$sSalida=$asResult['DATOS']['division'];
			else
				$sSalida=$asResult['MENSAJE'];
			return ($sSalida);
		}

	}
?>