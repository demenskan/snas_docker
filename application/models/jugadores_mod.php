<?php

	class jugadores_mod extends CI_Model {
	
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}
		
		function NombreJugador($psClave) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT nombre FROM jugadores WHERE id_unico=".$psClave,
						'UNICA_FILA' => true)
			);
			if ($asResult['ESTATUS']==1) 
				return ($asResult['DATOS']['nombre']);
			else
				return ($asResult['ERROR']);
		}

		function ListaJugadoresBusqueda($psSearch) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT id_unico, nombre FROM jugadores WHERE nombre LIKE '%".$psSearch."%'")
			);
			return($asResult);
		}
		
		function getJugadoresFiltro($pasCondiciones) {
			if (count($pasCondiciones)>0) {
				$sCondiciones=" WHERE ";
				for ($i=0;$i<count($pasCondiciones);$i++) {
					$sCampo=$pasCondiciones[$i][0];
					$sValor=$pasCondiciones[$i][2];
					switch ($pasCondiciones[$i][1]) {
						case "IGUAL":
							$sComplemento="='".$sValor."'";
							break;
						case "MAYOR":
							$sComplemento=">".$sValor;
							break;
						case "MENOR":
							$sComplemento="<".$sValor;
							break;
						case "MAYOR_IGUAL":
							$sComplemento=">=".$sValor;
							break;
						case "MENOR_IGUAL":
							$sComplemento="<=".$sValor;
							break;
						case "DIFERENTE":
							$sComplemento="<>".$sValor;
							break;
						case "PARECIDO A":
							$sComplemento="LIKE '%".$sValor."%'";
							break;
						case "ENTRE":
							$asValores=explode("-",$sValor);
							$sComplemento="BETWEEN (".$asValores[0]." AND ".$asValores[1].")";
							break;
					}
					if ($sCondiciones==" WHERE ") 
						$sCondiciones.=$sCampo.$sComplemento;
					else
						$sCondiciones.=" AND ".$sCampo.$sComplemento;
				}
			}
			else
				$sCondiciones="";
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT id_unico, nombre FROM jugadores ".$sCondiciones)
			);
			return($asResult);
		}


		function Habilidades($piJugador) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT * FROM jugadores WHERE id_unico=".$piJugador,
						'UNICA_FILA' => true)
			);
			return($asResult);
		}

		function getGraficasJugadores() {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT * FROM graficasjugadores "
					." WHERE tipo<>1 AND tipo<>2 ORDER BY Orden")
			);
			return($asResult);
		}

		function getListaGoleo($piTemporada) {
			$sCondicion=(($piTemporada!="") && ($piTemporada!="__EXTRA__")) ? "	 AND eve.id_temporada=".$piTemporada : "";
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT jug.id_unico as 'id_jugador', jug.nombre, count(eve.id_unico) as 'goles', \n"
									." cl.nombre_corto as 'nombre_club', cl.ruta_logo as 'logo_club' \n "
									."	FROM jugadores jug \n"
									."	INNER JOIN eventos eve On jug.id_unico=eve.id_jugador \n"
									."	INNER JOIN rostersportemporada rpt ON jug.id_unico=rpt.id_jugador AND rpt.id_temporada=eve.id_temporada \n"
									."	INNER JOIN clubes cl ON cl.id_unico=rpt.id_equipo \n"
									."	WHERE (eve.id_tipo=1 OR eve.id_tipo=6) \n"
									.$sCondicion
									."  GROUP BY jug.id_unico \n"
									."	ORDER BY goles DESC LIMIT 0,20")
			);
			return($asResult);
		}

		function getClubActual($piJugador) {
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT  cl.nombre_corto as 'nombre_club'\n"
									."  \n "
									."	FROM jugadores jug \n"
									."	INNER JOIN rostersportemporada rpt ON jug.id_unico=rpt.id_jugador AND rpt.id_temporada=".$this->config->item('temporada_actual')." \n"
									."	INNER JOIN clubes cl ON cl.id_unico=rpt.id_equipo \n"
									."	WHERE jug.id_unico=".$piJugador." \n",
						'UNICA_FILA' => true)
			);
			if ($asResult['ESTATUS']==1) 
				return ($asResult['DATOS']['nombre_club']);
			else
				return ("Sin definir");
		}
		
		function getGoleoDetalladoTemporadas($piJugador, $piTemporada) {
			$sCondicion=(($piTemporada!="") && ($piTemporada!="__EXTRA__")) ? "	 AND eve.id_temporada=".$piTemporada : "";
			$asResult=$this->tools_lib->consulta(
				array ('QUERY' => "SELECT tor.nombre as 'nombre_torneo', eve.id_temporada as 'temporada', count(eve.id_unico) as 'goles', \n"
									."  tor.clave as 'id_torneo' \n "
									."	FROM jugadores jug \n"
									."	INNER JOIN eventos eve ON jug.id_unico=eve.id_jugador \n"
									."	INNER JOIN torneos tor ON tor.clave=eve.id_torneo AND tor.id_temporada=eve.id_temporada \n"
									."	WHERE (eve.id_tipo=1 OR eve.id_tipo=6) AND jug.id_unico=".$piJugador." \n"
									.$sCondicion
									."  GROUP BY 'temporada','nombre_torneo','id_torneo' \n"
									."	ORDER BY 'temporada', 'id_torneo'")
			);
			return($asResult);
		}
	}
?>