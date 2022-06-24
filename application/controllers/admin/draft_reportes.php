<?php
	
	class draft_reportes extends CI_Controller {
	
		function __construct() {
			parent::__construct();
			$this->load->model('draft_mod');
		}
	
		function resumenmovimientos() {
			$this->load->model('clubes_mod');
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
				'COMBO_CONSEJOS' => $this->tools_lib->GeneraCombo(
							array (
								'NOMBRE' => "slcConsejo",
								'TABLA' => "cat_consejos", 'CAMPO_CLAVE' => "id_unico", 'LEYENDA' => "iniciales",
								'OPCION_EXTRA' => "TODOS", 'DEFAULT' => $this->input->post('slcConsejo')
							)
					)
			);
			$sTablaMovimientos="";
			$iEquipoActual=0;
			$sColor="non";
			if ($this->input->post('slcConsejo')!="") {
				if ($this->input->post('slcConsejo')=="__EXTRA__")
					$asClubes=$this->clubes_mod->Lista();
				else
					$asClubes=$this->clubes_mod->Lista(array("id_consejo=".$this->input->post('slcConsejo')));
				for ($i=0;$i<count($asClubes['DATOS']);$i++) {
					$asJugadoresAlta=$this->draft_mod->getMovimientosJugadores($asClubes['DATOS'][$i]['id_unico'], "altas");
					//var_dump ($asJugadoresAlta['QUERY']);
					$sAltas=""; 
					if ($asJugadoresAlta['ESTATUS']==1) {
						for ($j=0;$j<count($asJugadoresAlta['DATOS']);$j++)
							$sAltas.=$asJugadoresAlta['DATOS'][$j]['nombre']." (".$asJugadoresAlta['DATOS'][$j]['nombre_corto'].")<br/>";
					}
					$asJugadoresLibresAltas=$this->draft_mod->getJugadoresLibresAltas($asClubes['DATOS'][$i]['id_unico']);
					if ($asJugadoresLibresAltas['ESTATUS']==1) {
						for ($j=0;$j<count($asJugadoresLibresAltas['DATOS']);$j++)
							$sAltas.=$asJugadoresLibresAltas['DATOS'][$j]['nombre']." (".$asJugadoresLibresAltas['DATOS'][$j]['nombre_club'].")<br/>";
					}
					
					$sBajas="";
					$asJugadoresBaja=$this->draft_mod->getMovimientosJugadores($asClubes['DATOS'][$i]['id_unico'], "bajas");
					if ($asJugadoresBaja['ESTATUS']==1) {
						for ($j=0;$j<count($asJugadoresBaja['DATOS']);$j++)
							if ($asJugadoresBaja['DATOS'][$j]['nombre_corto']!="")
								$sBajas.=$asJugadoresBaja['DATOS'][$j]['nombre']." (".$asJugadoresBaja['DATOS'][$j]['nombre_corto'].")<br/>";
							else
								$sBajas.=$asJugadoresBaja['DATOS'][$j]['nombre']." (".$asJugadoresBaja['DATOS'][$j]['consejo'].")<br/>";
					}
					$asJugadoresLibresBajas=$this->draft_mod->getJugadoresLibresBajas($asClubes['DATOS'][$i]['id_unico']);
					if ($asJugadoresLibresBajas['ESTATUS']==1) {
						for ($j=0;$j<count($asJugadoresLibresBajas['DATOS']);$j++)
							$sBajas.=$asJugadoresLibresBajas['DATOS'][$j]['nombre']." (Agente libre)<br/>";
					}
					
					$asPrincipal['BLOQUE_EQUIPOS'][]=array (
						'RUTA_LOGO' => "s".$asClubes['DATOS'][$i]['ruta_logo'],
						'CLUB' => $asClubes['DATOS'][$i]['nombre_corto'],
						'ALTAS' => $sAltas,
						'BAJAS' => $sBajas,
						'CLASE' => ($i%2==0) ? "non" : "par"
					);
				}
				$sConsejos="";
				$asJugadoresConsejo=$this->draft_mod->getMovimientosConsejo($this->input->post('slcConsejo'));
				if ($asJugadoresConsejo['ESTATUS']==1) {
					for ($j=0;$j<count($asJugadoresConsejo['DATOS']);$j++)
						$sConsejos.=$asJugadoresConsejo['DATOS'][$j]['nombre']." (".$asJugadoresConsejo['DATOS'][$j]['nombre_corto'].")<br/>";
				}
				$asPrincipal['BLOQUE_EQUIPOS'][]=array (
					'RUTA_LOGO' => "sx",
					'CLUB' => "Sin club definido",
					'ALTAS' => $sConsejos,
					'BAJAS' => "",
					'CLASE' => ($i%2==0) ? "non" : "par"
				);
			}
			else
				$asPrincipal['BLOQUE_EQUIPOS']=array();
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/resumen_movimientos_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}
		
		
		function listaspreliminares() {
			$this->load->model(array('clubes_mod', 'consejos_mod'));
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri')
			);
/*			if ($this->session->userdata('sUsuario')!="root") {
				$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
				$asClubes=$this->clubes_mod->Lista(array('id_consejo='.$asConsejo['DATOS']['id_unico']));
			}
			else*/
				$asClubes=$this->clubes_mod->Lista(array('1'));
			if ($asClubes['ESTATUS']==1) {
				for ($i=0;$i<count($asClubes['DATOS']);$i++) {
					$asPrincipal['BLOQUE_EQUIPOS'][]=array (
						'RUTA_RAIZ' => base_url(),
						'INDEX_URI' => $this->config->item('index_uri'),
						'ID_CLUB' => $asClubes['DATOS'][$i]['id_unico'],
						'NOMBRE_CLUB' => $asClubes['DATOS'][$i]['nombre_corto']
					);
				}
				$asPrincipal['BLOQUE_EQUIPOS'][]=array (
					'RUTA_RAIZ' => base_url(),
					'INDEX_URI' => $this->config->item('index_uri'),
					'ID_CLUB' => "-1",
					'NOMBRE_CLUB' => "Sin club definido"
				);

			}
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse('draft/listas_preliminares_vw', $asPrincipal, true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}
		
		function preliminarequipo($piClub) {
			$this->load->model(array('clubes_mod','consejos_mod'));
			$asPrincipal=array (
				'RUTA_RAIZ' => base_url(),
				'INDEX_URI' => $this->config->item('index_uri'),
			);

			if ($piClub!=-1) {
				$asClub=$this->clubes_mod->RegresaDatos($piClub);
				if ($asClub['ESTATUS']==1) {
					$asPrincipal['RUTA_LOGO']= $asClub['DATOS']['ruta_logo'];
					$asPrincipal['NOMBRE_CLUB'] = $asClub['DATOS']['nombre_corto'];
					$asJugadores=$this->draft_mod->getListaPreliminar($piClub);
				}
				else
					echo "Error!".$asClub['QUERY'];
			}
			else {
				$asPrincipal['RUTA_LOGO']= "x";
				$asPrincipal['NOMBRE_CLUB'] = "Sin Definir";
				$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
				$asJugadores=$this->draft_mod->getJugadoresSinDefinir($asConsejo['DATOS']['id_unico']);
			}
			if ($asJugadores['ESTATUS']==1) {
				for ($i=0;$i<count($asJugadores['DATOS']);$i++) {
					$asPrincipal['BLOQUE_JUGADORES'][]=array (
						'POSICION' => $asJugadores['DATOS'][$i]['posicion'],
						'NOMBRE' =>$asJugadores['DATOS'][$i]['nombre'],
						'PUNTOS' =>$asJugadores['DATOS'][$i]['total_puntos'],
						'INICIALES' => $asJugadores['DATOS'][$i]['iniciales_esp']
					);
				}
			}
			$this->parser->parse('draft/roster_preliminar_vw', $asPrincipal);
		}
		
		
		function lista_equipo() {
		$sTabs="\t\t\t\t\t\t\t";
		$iCantidadJugadores=0;
		$iTotalPuntos=0;
		$sQuery="Select hab.Nombre, cpos.InicialesESP, rt.posicion, hab.total_puntos "
			." From habilidades hab "
			." Inner Join cat_posiciones cpos On hab.PosicionRegistrada=cpos.Clave "
			." Inner Join rostertemporal rt On rt.claveJugador=hab.ID_NUMBER "
			." Where rt.claveEquipo='".$piClaveEquipo."' And rt.posicion<>0 Order By rt.posicion";
		$rLocal=mysql_query($sQuery);
		$sJugadores=$sTabs."\t<tr>\n"
					.$sTabs."\t\t<td id=\"titulo\" align=\"center\" colspan=\"4\">Titulares</td>\n"
					.$sTabs."\t<tr>\n";
		$sColor="";
		while ($row=mysql_fetch_object($rLocal)) {
			switch ($row->posicion) {
				case 1:
					$sColor="pos_Portero";
					break;
				case 2:
					$sColor="pos_Defensa";
					break;
				case 3:
					$sColor="pos_Medio";
					break;
				case 4:
					$sColor="pos_Atacante";
					break;
			}	
			$sJugadores.=$sTabs."\t<tr>\n"
					.$sTabs."\t\t<td id=\"".$sColor."\" align=\"right\">&nbsp;</td>\n"
					.$sTabs."\t\t<td id=\"".$sColor."\">&nbsp;".$row->InicialesESP."&nbsp;</td>\n"
					.$sTabs."\t\t<td class=\"".$sColor."\">".$row->Nombre."</td>\n"
					.$sTabs."\t\t<td class=\"".$sColor."\" align=\"right\">".$row->total_puntos."</td>\n"
					.$sTabs."\t<tr>\n";
			$iCantidadJugadores++;
			$iTotalPuntos+=$row->total_puntos;
		}
		mysql_free_result($rLocal);
		$sJugadores.=$sTabs."\t<tr>\n"
					.$sTabs."\t\t<td id=\"titulo\" align=\"center\" colspan=\"4\">Banca</td>\n"
					.$sTabs."\t<tr>\n";

		$sQuery="Select hab.Nombre, cpos.InicialesESP, rt.posicion, hab.total_puntos "
			." From habilidades hab "
			." Inner Join cat_posiciones cpos On hab.PosicionRegistrada=cpos.Clave "
			." Inner Join rostertemporal rt On rt.claveJugador=hab.ID_NUMBER "
			." Where rt.claveEquipo='".$piClaveEquipo."' And rt.posicion=0 Order By cpos.InicialesESP";
		$rLocal=mysql_query($sQuery);
		$sColor="";
		while ($row=mysql_fetch_object($rLocal)) {
			$sColor="pos_Banca";
			$sJugadores.=$sTabs."\t<tr>\n"
					.$sTabs."\t\t<td id=\"".$sColor."\" align=\"right\">&nbsp;</td>\n"
					.$sTabs."\t\t<td id=\"".$sColor."\">&nbsp;".$row->InicialesESP."&nbsp;</td>\n"
					.$sTabs."\t\t<td class=\"".$sColor."\">".$row->Nombre."</td>\n"
					.$sTabs."\t\t<td class=\"".$sColor."\" align=\"right\">".$row->total_puntos."</td>\n"
					.$sTabs."\t</tr>\n";
			$iCantidadJugadores++;
			$iTotalPuntos+=$row->total_puntos;
		}
		mysql_free_result($rLocal);
		if ($iCantidadJugadores>0)
				$fPromedio=round($iTotalPuntos/$iCantidadJugadores,2);
		else
				$fPromedio=0;
		$sJugadores.=$sTabs."\t<tr>\n"
				.$sTabs."\t\t<td id=\"totales\" align=\"right\" colspan=\"3\">Total jugadores:</td>\n"
				.$sTabs."\t\t<td id=\"totales\" align=\"right\">".$iCantidadJugadores."</td>\n"
				.$sTabs."\t</tr>\n"
				.$sTabs."\t<tr>\n"
				.$sTabs."\t\t<td id=\"totales\" align=\"right\" colspan=\"3\">Total puntos:</td>\n"
				.$sTabs."\t\t<td id=\"totales\" align=\"right\">".$iTotalPuntos."</td>\n"
				.$sTabs."\t</tr>\n"
				.$sTabs."\t<tr>\n"
				.$sTabs."\t\t<td id=\"totales\" align=\"right\" colspan=\"3\">Promedio:</td>\n"
				.$sTabs."\t\t<td id=\"totales\" align=\"right\">".$fPromedio."</td>\n"
				.$sTabs."\t</tr>\n";
		return ($sJugadores);
	}
	
		
		function habilidades($psFiltro="8", $psOrden="") {
			$this->load->model(array('clubes_mod', 'consejos_mod'));
			$iFiltro=($this->input->post('slcFiltro')!="") ? $this->input->post('slcFiltro') : $psFiltro;
			$sOrden=($this->input->post('orden')!="") ? $this->input->post('orden')  : $psOrden;
			$asCamposHabilidades=$this->draft_mod->getCamposHabilidades();
			if ($asCamposHabilidades['ESTATUS']==1) {
				$asEncabezados=array();
				for ($i=0;$i<count($asCamposHabilidades['DATOS']);$i++) {
					$asEncabezados[]=array (
						'RUTA_RAIZ' => base_url(),
						'LEYENDA' => $asCamposHabilidades['DATOS'][$i]['leyenda'],
						'CAMPO' => $asCamposHabilidades['DATOS'][$i]['campo'],
						'FILTRO' => $iFiltro
					);
				}
			}
			$asValores=$this->draft_mod->getHabilidadesPorBloque($iFiltro, $sOrden);
			if ($asValores['ESTATUS']==1) {
				$asFilasSalidas=array();
				for ($i=0;$i<count($asValores['DATOS']);$i++) {
					$sCodigoFila="	<tr>\n";
					for ($j=0;$j<count($asCamposHabilidades['DATOS']);$j++) {
						if ($asCamposHabilidades['DATOS'][$j]['graficado']==1) {
								if ($asCamposHabilidades['DATOS'][$j]['boleano']=='F') {
									$iMaximoCampo=$asCamposHabilidades['DATOS'][$j]['max'];	
									$iPorcentaje=intval(($asValores['DATOS'][$i][$asCamposHabilidades['DATOS'][$j]['campo']]*100)/$iMaximoCampo);	
									if ($iPorcentaje>90)
										$sColor="#FF3333";
									elseif 	(($iPorcentaje<=90) && ($iPorcentaje>80))
										$sColor="#FF0000";
									elseif 	(($iPorcentaje<=80) && ($iPorcentaje>70))
										$sColor="#F03333";
									elseif 	(($iPorcentaje<=70) && ($iPorcentaje>60))
										$sColor="#AA9900";
									elseif 	(($iPorcentaje<=60) && ($iPorcentaje>50))
										$sColor="#EE33FF";
									elseif 	($iPorcentaje<=50)
										$sColor="#00CE60";
									$sCodigoFila.=" 		<td onmouseover=\"this.style.borderColor='Black';\" style=\"color: ".$sColor."\" title=\"".$asValores['DATOS'][$i]['nombre'].",".$asCamposHabilidades['DATOS'][$j]['leyenda']."\">".$asValores['DATOS'][$i][$asCamposHabilidades['DATOS'][$j]['campo']]."</td>\n";
								}
								else {
									if ($aiHabilidades[$aoCampos[$i]["Campo"]]==1) 	
										$sCodigoFila.=" 		<td>*</td>\n";
									else	
										$sCodigoFila.=" 		<td>&nbsp;</td>\n";	
								}
						}
						else
								$sCodigoFila.=" 		<td  title=\"".$asValores['DATOS'][$i]['nombre'].",".$asCamposHabilidades['DATOS'][$j]['leyenda']."\">".$asValores['DATOS'][$i][$asCamposHabilidades['DATOS'][$j]['campo']]."</td>\n";
					}
					$sCodigoFila.="	</tr>\n";
					$asFilasSalidas[]=array (
						'CLASE' => ($i%2==0) ? "non" : "par",
						'VALORES' => $sCodigoFila
					);
				}
				$asOpcionesFiltro=array();
				$asClubes=$this->clubes_mod->Lista();
				if ($asClubes['ESTATUS']==1) {
					for ($i=0;$i<count($asClubes['DATOS']);$i++) {
						$asOpcionesFiltro['DATOS'][]=array (
							0 => $asClubes['DATOS'][$i]['id_unico'],
							1 => $asClubes['DATOS'][$i]['nombre_corto']
						);
					}
				}
				$asConsejos=$this->consejos_mod->getLista();
				if ($asConsejos['ESTATUS']==1) {
					for ($i=0;$i<count($asConsejos['DATOS']);$i++) {
						$asOpcionesFiltro['DATOS'][]=array (
							0 => $asConsejos['DATOS'][$i]['id_unico'],
							1 => $asConsejos['DATOS'][$i]['iniciales']
						);
					}
				}
				
				
				//var_dump($asOpcionesFiltro);
				$asPrincipal=array (
					'RUTA_RAIZ' => base_url(),
					'COMBO_FILTRO' =>  $this->tools_lib->GeneraCombo(array  (
											'NOMBRE' => "slcFiltro",
											'DATASET' => $asOpcionesFiltro,
											'DEFAULT' => $iFiltro
											)),
					'BLOQUE_ENCABEZADO' => $asEncabezados,
					'BLOQUE_FILA' => $asFilasSalidas
				);
				$this->main_lib->simple_display($asPrincipal, 'draft/lista_habilidades_vw','una-columna_vw','ADMIN' );
			}
			else
				$this->main_lib->escribe_mensaje(array('MENSAJE' => "NO HAY RESULTADOS"));
/*		
		
		
		
		if (isset($_REQUEST["slcFiltro"]))
			$iFiltro=$_REQUEST["slcFiltro"];
		else
			$iFiltro=8;
		if (isset($_REQUEST["orden"]))
			$sDefineOrden=" ORDER BY ".$_REQUEST["orden"]." DESC";
		else
			$sDefineOrden="";
			
		$oCon=m_ConectaBase();
		$sQyCampos="SELECT * FROM graficasjugadores \n"
				." WHERE tipo<>2 ORDER BY Orden";
		$rCampos=mysql_query($sQyCampos);
		$iTotal=0;
		$sDatosJugadores="<table class=\"tabla_normal\" >\n"
				."	<tr>\n";
		while ($aCampos=mysql_fetch_array($rCampos)) {
			$aoCampos[$iTotal]["Campo"]=$aCampos["Campo"];
			$aoCampos[$iTotal]["Leyenda"]=$aCampos["Leyenda"];
			$aoCampos[$iTotal]["Tipo"]=$aCampos["Tipo"];
			$aoCampos[$iTotal]["Boleano"]=$aCampos["Boleano"];
			$aoCampos[$iTotal]["Max"]=$aCampos["Max"];
			$aoCampos[$iTotal]["Orden"]=$aCampos["Orden"];
			$aoCampos[$iTotal]["Graficado"]=$aCampos["Graficado"];
			$sDatosJugadores.="		<td id=\"titulo\">".$aoCampos[$iTotal]["Leyenda"]
							 ."			<a href=\"generador.php?code=55040400&slcFiltro=".$iFiltro."&orden=".$aoCampos[$iTotal]["Campo"]."\"><img src=\"img/arrow-down-circle.jpg\" border=\"0\"></a>\n"
							 ."		</td>\n";
			$iTotal++;
		}
		$sDatosJugadores.="	</tr>\n";
		//echo var_dump($aoCampos);
		if ($iFiltro==0)
			$sQyHabilidades="SELECT * \n"
				." FROM habilidades hab \n"
				." INNER JOIN equipos cl on cl.nombre_pes=hab.NombreClub "
				.$sDefineOrden;
		elseif (($iFiltro>=1) && ($iFiltro<=5))
			$sQyHabilidades="SELECT * \n"
				." FROM habilidades hab \n"
				." INNER JOIN rostertemporal rt on rt.claveJugador=hab.ID_NUMBER \n"
				." INNER JOIN equipos cl on rt.claveEquipo=cl.id_equipo \n"
				." WHERE cl.consejo=".$iFiltro." \n"
				.$sDefineOrden;
		elseif ($iFiltro>=8)
			$sQyHabilidades="SELECT * \n"
				." FROM habilidades hab \n"
				." INNER JOIN rostertemporal rt on rt.claveJugador=hab.ID_NUMBER \n"
				." WHERE rt.claveEquipo=".$iFiltro." \n"
				.$sDefineOrden;
		
		if ($rRec=mysql_query($sQyHabilidades)) {
			while ($aiHabilidades=mysql_fetch_array($rRec)) {
				if ($sColorFila=="non")
						$sColorFila="par";
				else
						$sColorFila="non";
				//$sDatosJugadores.="<tr id=\"".$sColorFila."\" onmouseover=\"this.style.border='1px solid #ff0000';\">\n";
				$sDatosJugadores.="<tr id=\"".$sColorFila."\" onmouseover=\"this.style.background-color='Black';\">\n";
				for ($i=0;$i<$iTotal;$i++) {
						//echo "[".$aoCampos[$i]["Graficado"]."]\n";
						if ($aoCampos[$i]["Graficado"]=="1") {
								if ($aoCampos[$i]["Boleano"]=="F") {
									$iMaximoCampo=$aoCampos[$i]["Max"];	
									$iPorcentaje=intval(($aiHabilidades[$aoCampos[$i]["Campo"]]*100)/$iMaximoCampo);	
									if ($iPorcentaje>90)
										$sColor="#FF3333";
									elseif 	(($iPorcentaje<=90) && ($iPorcentaje>80))
										$sColor="#FF0000";
									elseif 	(($iPorcentaje<=80) && ($iPorcentaje>70))
										$sColor="#F03333";
									elseif 	(($iPorcentaje<=70) && ($iPorcentaje>60))
										$sColor="#AA9900";
									elseif 	(($iPorcentaje<=60) && ($iPorcentaje>50))
										$sColor="#EE33FF";
									elseif 	($iPorcentaje<=50)
										$sColor="#00CE60";
									$sDatosJugadores.=" 		<td onmouseover=\"this.style.borderColor='Black';\" style=\"color: ".$sColor."\" title=\"".$aiHabilidades["Nombre"].",".$aoCampos[$i]["Leyenda"]."\">".$aiHabilidades[$aoCampos[$i]["Campo"]]."</td>\n";
								}
								else {
									if ($aiHabilidades[$aoCampos[$i]["Campo"]]==1) 	
										$sDatosJugadores.=" 		<td>*</td>\n";
									else	
										$sDatosJugadores.=" 		<td>&nbsp;</td>\n";	
								}
						}
						else
								$sDatosJugadores.=" 		<td  title=\"".$aiHabilidades["Nombre"].",".$aoCampos[$i]["Leyenda"]."\">".$aiHabilidades[$aoCampos[$i]["Campo"]]."</td>\n";
				}
				$sDatosJugadores.="</tr>\n";
			}	
			$sDatosJugadores.="</table>";	
			//echo var_dump($aiHabilidades);
		}
		else
			$sDatosJugadores=mysql_errno().":".mysql_error();	
		mysql_free_result($rCampos);
		mysql_free_result($rRec);
		m_CierraConexion($oCon);
		$sSalida=m_CargaArchivo("Draft/lista_habilidades.html");
		$sSalida=str_replace("<!--DATOS_JUGADORES-->",$sDatosJugadores,$sSalida);
		$sSalida=str_replace("<!--LISTA_FILTROS-->", GeneraFiltro() ,$sSalida);
		return ($sSalida);*/
	}
	
	
	
		function GeneraFiltro() {
			$oCon=m_ConectaBase();
			if (isset($_REQUEST["slcFiltro"]))
				$iFiltro=$_REQUEST["slcFiltro"];
			else
				$iFiltro=0;	
			
			$sTabs="\t\t\t";
			$sResultado=$sTabs."<select name=\"slcFiltro\">\n"
						.$sTabs."\t<option value=\"0\">Todos los jugadores</option>\n";	
			$sQyConsejos="SELECT clave, corto FROM cat_consejos ORDER BY clave";
			$rRec=mysql_query($sQyConsejos);
			while ($row=mysql_fetch_object($rRec)) {
				if ($iFiltro==$row->clave)	
					$sResultado.=$sTabs."\t<option value=\"".$row->clave."\" selected=\"yes\">".$row->corto."</option>\n";
				else
					$sResultado.=$sTabs."\t<option value=\"".$row->clave."\">".$row->corto."</option>\n";
			}
			mysql_free_result($rRec);
	
			$sQyEquipos="SELECT id_equipo, nombre_corto FROM equipos WHERE id_equipo<>0 ORDER BY id_equipo";
			$rRec=mysql_query($sQyEquipos);
			while ($row=mysql_fetch_object($rRec)) {
				if ($iFiltro==$row->id_equipo)
					$sResultado.=$sTabs."\t<option value=\"".$row->id_equipo."\" selected=\"yes\">".$row->nombre_corto."</option>\n";
				else
					$sResultado.=$sTabs."\t<option value=\"".$row->id_equipo."\">".$row->nombre_corto."</option>\n";
			}
			mysql_free_result($rRec);
			m_CierraConexion($oCon);
			$sResultado.=$sTabs."</select>\n";
			return($sResultado);
		}
		
		
		function mejora_oferta() {
			//Avisa si hay mejoras de oferta de alguno de los candidatos de los clubes del usuario
			$this->load->model(array ('consejos_mod', 'draft_mod'));
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			//Primero, obtencion de los clubes del usuario y sus apadrinados
			$asClubes=$this->consejos_mod->getListaClubes($asConsejo['DATOS']['id_unico'], true);
			$asOfertasSuperadas=array();
			$iTemporada=$this->config->item('temporada_actual');
			if ($asClubes['ESTATUS']==1) {
				$iCont=0;
				for ($i=0;$i<count($asClubes['DATOS']);$i++) {
					$asOfertasClub=$this->draft_mod->getOfertados($iTemporada, $asClubes['DATOS'][$i]['id_unico'],-1);
					if ($asOfertasClub['ESTATUS']==1) {
						for ($j=0;$j<count($asOfertasClub['DATOS']);$j++) {
							$asMejorOferta=$this->draft_mod->getMejorOferta($iTemporada, $asOfertasClub['DATOS'][$j]['id_jugador']);
							if ($asMejorOferta['ESTATUS']==1) {
								$asOfertasSuperadas[]=array (
									'CLASE_FILA' => ($iCont%2==0) ? "non" : "par",
									'NOMBRE' => $asOfertasClub['DATOS'][$j]['nombre'],
									'CLUB_OFERTO' => $asOfertasClub['DATOS'][$j]['nombre_corto'],
									'CLUB_SUBIO' => $asMejorOferta['DATOS']['nombre_club'],
									'NUEVA_OFERTA' => "<a href=\"admin/draft_subastas/captura_oferta/"
										.$asOfertasClub['DATOS'][$j]['id_jugador']."/".$asClubes['DATOS'][$i]['id_unico']."\">"
										.$asMejorOferta['DATOS']['sueldo_base']."</a>",
								);
							}
							$iCont++;
						}
					}
				}
				$asContenido=array (
					'PRINCIPAL' => $this->parser->parse ('draft/reporte_alerta_mejoras_oferta_vw', array('BLOQUE_AVISOS' => $asOfertasSuperadas), true)
				);
				$asControlador= array (
					'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
					'TIPO_ACCESO' => 'ADMIN' );
				$this->main_lib->display($asControlador);
			}
			else
				$this->main_lib->mensaje('No tienes clubes designados');
		}
		
		function bitacora_contrataciones($psFechaInicio="ALL", $psFechaFin="ALL", $piPagina=1, $piElementosxPagina=20) {
			$this->load->model('bitacora_draft_mod');
			$sFechaInicio= ($this->input->post('txtFechaInicio')=="") ? $psFechaInicio : $this->input->post('txtFechaInicio');
			$sFechaFin= ($this->input->post('txtFechaFin')=="") ? $psFechaFin : $this->input->post('txtFechaFin');
			//($psFechaInicio=="") ? date("Y-m-d", strtotime ( '-1 week',time()))   : $psFechaInicio;
			//$sFechaFin=($psFechaFin=="") ? date("Y-m-d") : $psFechaFin;
			$asEventos=$this->bitacora_draft_mod->lista($sFechaInicio,$sFechaFin, $piPagina, $piElementosxPagina);
			$asEventos['total']=$this->bitacora_draft_mod->conteo($sFechaInicio,$sFechaFin, $piPagina, $piElementosxPagina);
			if ($asEventos['ESTATUS']==1) {
				$asReporte=array();
				//Llena el reporte
				for ($i=0;$i<count($asEventos['DATOS']);$i++) {
					$asReporte[]=array ('CLASE' => ($i%2==0) ? "non" : "par",
										'FECHA' => $asEventos['DATOS'][$i]['fecha'],
										'TIPO' => $asEventos['DATOS'][$i]['tipo'],
										'CLUB' => $asEventos['DATOS'][$i]['club'],
										'JUGADOR' => $asEventos['DATOS'][$i]['jugador'],
										'CONSEJO' => $asEventos['DATOS'][$i]['consejo'],
										'DETALLES' => $asEventos['DATOS'][$i]['observaciones']
										);
				}
				//Llena el combo de paginas
				$iTotalPaginas=floor($asEventos['total']/$piElementosxPagina) + 1;
				
				$asOpcionesCombo['DATOS']=array();
				for ($i=1;$i<=$iTotalPaginas;$i++) {
					$asOpcionesCombo['DATOS'][] = array ( 0 => $i, 1 => "Pagina ".$i);
				}
				$sURLFechaInicio=($sFechaInicio=="") ? "ALL" : $sFechaInicio;
				$sURLFechaFin=($sFechaFin=="") ? "ALL" : $sFechaFin;
				$asPrincipal=array(
						'FECHA_INICIO' => $sFechaInicio, 'FECHA_FIN' => $sFechaFin,
						'COMBO_PAGINAS' => $this->tools_lib->GeneraCombo(array(
												'NOMBRE' => "slcPagina",
												'DATASET' => $asOpcionesCombo,
												'DEFAULT' => $piPagina,
												'EVENTOS' => "onChange=\"javascript: gotoPagina('".$sURLFechaInicio."','".$sURLFechaFin."',this.value,".$piElementosxPagina.");\""
											)),
						'BLOQUE_EVENTOS' => $asReporte   
						);
				$asContenido=array (
					'PRINCIPAL' => $this->parser->parse('draft/reporte_movimientos_vw', $asPrincipal, true)
				);
				$asControlador= array (
					'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
					'TIPO_ACCESO' => 'ADMIN' );
				$this->main_lib->display($asControlador);
			}
			else
				$this->main_lib->mensaje('No hay eventos del draft');
		}
		
		
		function contrataciones_consejo() {
			//Avisa cuales subastas se han ganado por alguno de los clubes del usuario
			$iTemporada=$this->config->item('temporada_actual');
			$this->load->model(array ('consejos_mod', 'draft_mod'));
			$asConsejo=$this->consejos_mod->getDatosPorOperador($this->session->userdata('sUsuario'));
			//Primero, obtencion de los clubes del usuario y sus apadrinados
			$asClubes=$this->consejos_mod->getListaClubes($asConsejo['DATOS']['id_unico'], true);
			$asBloqueGanadores=array();
			$iCont=0;
			if ($asClubes['ESTATUS']==1) {
				for ($i=0;$i<count($asClubes['DATOS']);$i++) {
					$asOfertasGanadoras=$this->draft_mod->getOfertados($iTemporada, $asClubes['DATOS'][$i]['id_unico'],10);
					if ($asOfertasGanadoras['ESTATUS']==1) {
						for ($j=0;$j<count($asOfertasGanadoras['DATOS']);$j++)
							if ($iCont<10) {
								$asBloqueGanadores[]=array(
									'CLASE_FILA' => ($iCont%2==0) ? 'non' : 'par',
									'NOMBRE' => $asOfertasGanadoras['DATOS'][$j]['nombre'],
									'ID_CLUB' => $asOfertasGanadoras['DATOS'][$j]['id_club'],
									'CLUB' => $asOfertasGanadoras['DATOS'][$j]['nombre_corto'],
									'SUELDO_BASE' => $asOfertasGanadoras['DATOS'][$j]['sueldo_base'],
									'TEMPORADAS' => $asOfertasGanadoras['DATOS'][$j]['duracion'],
									'FECHA_CONTRATACION' => $asOfertasGanadoras['DATOS'][$j]['fecha_contratacion']
								);
								$iCont++;
							}
					}
				}
				foreach ($asBloqueGanadores as $key => $fila) 
					$adFechaContratacion[$key] = $fila["FECHA_CONTRATACION"];
				if (count($asBloqueGanadores)>0)
					array_multisort($adFechaContratacion, SORT_DESC, $asBloqueGanadores);
			}
			$asContenido=array (
				'PRINCIPAL' => $this->parser->parse ('draft/contrataciones_recientes_vw', array('BLOQUE_CONTRATADOS' => $asBloqueGanadores), true)
			);
			$asControlador= array (
				'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
				'TIPO_ACCESO' => 'ADMIN' );
			$this->main_lib->display($asControlador);
		}
		
		function presupuestos_clubes() {
			$this->load->model('draft_presupuestos_mod');
			$sGenero=($this->input->post('slcGenero')!="") ? $this->input->post('slcGenero') : "__EXTRA__";
			$sClave=($this->input->post('slcClave')!="") ? $this->input->post('slcClave') : "__EXTRA__" ;
			$iTemporada=$this->config->item('temporada_actual');
			$asClubes=$this->draft_presupuestos_mod->clubes_lista($sGenero,$sClave, $iTemporada);
			if ($asClubes['ESTATUS']==1) {
				$asBloqueClubes=array();
				for ($i=0;$i<count($asClubes['DATOS']);$i++){
					$asBloqueClubes[]=array(
						'CLASE' => ($i%2==0) ? "non" : "par",
						'NOMBRE' => $asClubes['DATOS'][$i]['nombre_club'],
						'PRESUPUESTO' => $asClubes['DATOS'][$i]['presupuesto'],
					);		
				}
				$asContenido=array (
					'PRINCIPAL' => $this->parser->parse ('draft/presupuestos_clubes_vw',
										array('BLOQUE_CLUBES' => $asBloqueClubes
											  ), true)
				);
				$asControlador= array (
					'CONTENIDO' => $this->load->view("templates/".$this->main_lib->sTemplateEnUso."/una-columna_vw", $asContenido, true),
					'TIPO_ACCESO' => 'ADMIN' );
				$this->main_lib->display($asControlador);
			}
			else
				$this->main_lib->mensaje('No hay presupuestos designados en este bloque');
		}
		
		
		
		/*Fin de la clase*/
	}
?>