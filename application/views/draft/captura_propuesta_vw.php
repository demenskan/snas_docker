<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
	<script language="JavaScript" type="text/javascript">
	<!--
		var aiClaveJugadoresOferta= new Array(10), aiClaveJugadoresSolicita= new Array(10);
		var asNombreJugadoresOferta= new Array(10), asNombreJugadoresSolicita= new Array(10);
		var iContOferta=0, iContSolicita=0;
		
		function ActualizaLista(piClub, piModo) {
			sLink='{RUTA_RAIZ}{INDEX_URI}admin/draft_propuestas/xi_roster_propuesta/'+piModo+'/'+piClub;
			callPage(sLink,'ListaJugadores_'+piModo,'Cargando...','Error: '+sLink);
			document.getElementById('clave-club-' + piModo).value=piClub;
		}
		
		function ClubesDestino(piConsejo) {
			sLink='{RUTA_RAIZ}{INDEX_URI}admin/draft_propuestas/xi_clubes_destino/'+piConsejo;
			callPage(sLink,'EquiposDestino','Cargando...','Error: '+sLink);
			document.frmGeneraPropuesta.hdnConsejoDestino.value=piConsejo;
		}
		
		
		function AgregaJugador(piLado) {
			if (piLado==1) {
				oForma=document.getElementById('forma1');
				document.getElementById('lista-clubes-origen').disabled=true;
				document.getElementById('boton-agregar-origen').disabled=true;
			}
			else {
				oForma=document.getElementById('forma2');
				document.getElementById('lista-clubes-destino').disabled=true;
				document.getElementById('boton-agregar-destino').disabled=true;
			}
			if (piLado==1) {
				for(i=0; i<oForma.elements.length; i++) {
					sControl=oForma.elements[i].name;
					sPrefijo=sControl.substr(0,3);
					if ((sPrefijo=="chk") && (oForma.elements[i].checked==true)) {
						if (sControl=="chkEfectivoorigen") {
							if (bNoRepetido("000", iContOferta, 1)) {
								aiClaveJugadoresOferta[iContOferta]="EFE_" + document.getElementById('text-efectivo-origen').value;
								asNombreJugadoresOferta[iContOferta]=document.getElementById('text-efectivo-origen').value +  "KCacaos";
							}
						}
						else {
							if (bNoRepetido(sControl.substr(6), iContOferta, 1)) {
								aiClaveJugadoresOferta[iContOferta]=sControl.substr(6);
								asNombreJugadoresOferta[iContOferta]=document.getElementById("orn_" + sControl.substr(6)).innerHTML;
							}
						}
						iContOferta++;
					}
				}
			}
			else {
				for(i=0; i<oForma.elements.length; i++) {
					sControl=oForma.elements[i].name;
					sPrefijo=sControl.substr(0,3);
					if ((sPrefijo=="chk") && (oForma.elements[i].checked==true)) {
						if (sControl=="chkEfectivodestino") {
							if (bNoRepetido("000", iContSolicita, 1)) {
								aiClaveJugadoresSolicita[iContSolicita]="EFE_" + document.getElementById('text-efectivo-destino').value;
								asNombreJugadoresSolicita[iContSolicita]=document.getElementById('text-efectivo-destino').value +  "KCacaos";
							}
						}
						else {						
							if (bNoRepetido(sControl.substr(6), iContSolicita, 2)) {
								aiClaveJugadoresSolicita[iContSolicita]=sControl.substr(6);
								asNombreJugadoresSolicita[iContSolicita]=document.getElementById("den_" + sControl.substr(6)).innerHTML;
								
							}
						}
						iContSolicita++;
					}
				}
			}
			CreaTabla(piLado);
		} 

		function CreaTabla (iSeccion) {
			if (iSeccion==1) {
				iContador=iContOferta;
				aLista=aiClaveJugadoresOferta;
				aNombres=asNombreJugadoresOferta;
				sIdControl="Tabla_jugadores_oferta";
				sIdInterno="propuestas";				
				sPrefijo="orn_";
			}
			else {
				iContador=iContSolicita;
				aLista=aiClaveJugadoresSolicita;
				aNombres=asNombreJugadoresSolicita;
				sIdControl="Tabla_jugadores_solicita";
				sIdInterno="solicitudes";
				sPrefijo="den_";
			}
			sSalida="<table>"
					+	" <tr> ";
			sCodigoInterno="";
			if (iContador==0) {
				sSalida+="	<td>Sin jugadores seleccionados</td>";
			}
			else {
				for (i=0;i<iContador;i++) {
					sSalida+="	<tr><td>"+ aNombres[i] +"</td></tr>";
					sCodigoInterno+="<input type=hidden name=hdn" + sIdInterno + i + " value='" + aLista[i] + "' />";
				}
			}
			sSalida+="	</tr>"
					+ "</table>";
			document.getElementById(sIdControl).innerHTML=sSalida;
			document.getElementById(sIdInterno).innerHTML=sCodigoInterno;
		}
		
		function bNoRepetido (piClaveJugador, piLimite, piLista) {
			/* Checa que no se repita alguna clave en los arreglos */
			bResultado=true;
			if (piLista==1) { 		//Ofertas
				for (j=0;j<piLimite;j++) {
					//alert (aiClaveJugadoresOferta[j]+ ':' +piClaveJugador);
					if (aiClaveJugadoresOferta[j]==piClaveJugador)
						bResultado=false;
				}
			}
			else { 		//Solicitudes
				for (j=0;j<piLimite;j++) {
					//alert (aiClaveJugadoresSolicita[j]+ ':' +piClaveJugador);
					if (aiClaveJugadoresSolicita[j]==piClaveJugador)
						bResultado=false;
				}
			}
			return (bResultado);
		}

		function MandaForma() {
			document.frmGeneraPropuesta.hdnMensaje.value=document.getElementById('taMensaje').value;
			document.frmGeneraPropuesta.submit();		
		}
		
	//-->
	</script>
	<table width="100%" border="0">
		<tr>
			<td colspan="4"><h2>PROPUESTAS DE CAMBIOS</h2></td>
			<td rowspan="4" valign="top">
				<table class="tabla_normal">
					<tr><td id="titulo">Jugador(es) que ofrezco</td></tr>
					<tr><td class="resaltado"><div id="Tabla_jugadores_oferta"></div></td></tr>
					<tr><td id="titulo">Jugador(es) que pido</td></tr>
					<tr><td  class="resaltado"><div id="Tabla_jugadores_solicita"></div></td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4"><b>INSTRUCCIONES:</b> 
				Seleccione los jugadores que quiera intercambiar, y al final haga click en el boton 
				de HACER PROPUESTA. Una vez que el operador del otro consejo acepte la propuesta, se hace
				el cambio automaticamente. Se pueden hacer cambios internos si asi lo desea.
			</td>
		</tr>
		<tr>
			<td id="titulo" colspan="2">Mis equipos (origen)</td>
			<td id="titulo" colspan="2">Equipos (destino)</td>
		</tr>
		<tr>
			<td valign="top" width="10%" height="500">
				<img src="img/pixel.gif" width="1" height="80" />			
				<img src="img/pixel.gif" width="10" height="1" />
				{COMBO_CLUBES_ORIGEN}
			</td>
			<td valign="top" width="40%">
				<img src="img/pixel.gif" width="25" height="1" />
				<div id="ListaJugadores_origen"></div>
			</td>
			<td valign="top" width="10%">
				<img src="img/pixel.gif" width="10" height="1" />			
				<div id="EquiposDestino">
					{CONSEJOS}
					<a href="Javascript: ClubesDestino({CLAVE_CONSEJO});" >{NOMBRE_CORTO}</a><br/>
					{/CONSEJOS}
				</div>
			</td>
			<td valign="top" width="40%">
				<img src="img/pixel.gif" width="25" height="1" />			
				<div id="ListaJugadores_destino"></div>
			</td>
		</tr>
		<tr>
			<td>Mensaje:</td>
			<td colspan="2">
				<textarea id="taMensaje" rows="8" cols="40"></textarea>
			</td>
			<td valign="top"><input type="button" class="button" name="smtManda" value="Hacer propuesta" onclick="MandaForma();" /></td>
		</tr>
	</table>
	<form action="{RUTA_RAIZ}{INDEX_URI}admin/draft_propuestas/genera" method="post" name="frmGeneraPropuesta">
		<div id="propuestas"></div>
		<div id="solicitudes"></div>
		<input type="hidden" name="hdnMensaje" value="" />
		<input type="hidden" name="code" value="550004CC" />
		<input type="hidden" name="hdnClubOrigen" id="clave-club-origen" value="" />
		<input type="hidden" name="hdnClubDestino" id="clave-club-destino" value="" />
		<input type="hidden" name="hdnConsejoOrigen" id="clave-consejo-origen" value="{CONSEJO_ORIGEN}" />
		<input type="hidden" name="hdnConsejoDestino" id="clave-consejo-destino" value="" />
	</form>