<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
<script type="text/javascript" src="{RUTA_RAIZ}externos/js/jquery-1.9.1.js" language="JavaScript"></script>
<script type="text/javascript" src="{RUTA_RAIZ}externos/js/jquery-1.4.1.js" language="JavaScript"></script>
<script type="text/javascript" src="{RUTA_RAIZ}externos/js/jquery.autocomplete.min.js" language="JavaScript"></script>
<link rel="stylesheet" type="text/css" href="{RUTA_RAIZ}externos/js/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="{RUTA_RAIZ}externos/js/cwcalendar.css" media="all" />
<script language="JavaScript" type="text/javascript" src="{RUTA_RAIZ}externos/js/calendar.js" /></script>
<link rel="stylesheet" href="css/folder-tabs.css" type="text/css" />




<script type="text/javascript" language="JavaScript">
		
		var asCambios=[
					   {VARIABLE_ARRAY_CAMBIOS}
					   ];
		
		function mandaForma () {
			document.frmPartidos.submit();
		}
		
		function AgregaCambio(psTipo) {
			oComboSale=document.getElementById('select-sale-' + psTipo);
			oComboEntra=document.getElementById('select-entra-' + psTipo);
			sMinutoCambio=document.getElementById('text-minuto-cambio-' + psTipo).value;
			if (document.getElementById('text-duracion').value!="") {
				if ((isNumeric(sMinutoCambio)) && (parseInt(sMinutoCambio)>=0) && (parseInt(sMinutoCambio)<=parseInt(document.getElementById('text-duracion').value))) {
					if (oComboSale.value!=oComboEntra.value) {
							oLinea={
								escudo: document.getElementById('logo-' + psTipo).value,
								nombre_sale: oComboSale.options[oComboSale.selectedIndex].innerHTML,
								nombre_entra: oComboEntra.options[oComboEntra.selectedIndex].innerHTML,
								id_sale: oComboSale.value,
								id_entra: oComboEntra.value,
								minuto: sMinutoCambio
							};
							asCambios.push (oLinea);
							GeneraTabla();
					}
					else
						alert ("Algo esta mal con el cambio XD.. un jugador no puede cambiarse por si mismo!");
				}
				else 
					alert ("Debe poner un numero valido en el minuto");
			}
			else
				alert ("Debe definir la duracion del partido primero");
		}

		function Borra(piPosicion) {
			asCambios.splice(piPosicion,1);
			GeneraTabla();
		}
 
		function isNumeric(n) {
		  return !isNaN(parseFloat(n)) && isFinite(n);
		}

 
		function GeneraTabla() {
			//code
			sSalida='<table width="90%">'
					+ '<thead>'
					+ '     <th>Club</th>'
					+ '     <th>Sale</th>'
					+ '     <th>Entra</th>'
					+ '     <th>Minuto</th>'
					+ ' 	<th>Operaciones</th>'
					+ '</thead>';
			for (i=0;i<asCambios.length;i++) {
					sSalida+='<tr>'
							+ '<td><img src="img/escudos/mini/s' + asCambios[i]['escudo'] + '.gif" /></td>'
							+ '<td>' + asCambios[i]['nombre_sale'] + '</td>'
							+ '<td>' + asCambios[i]['nombre_entra'] + '</td>'
							+ '<td>' + asCambios[i]['minuto'] + '</td>'
							+ '<td><img src="img/destroy.png" onClick="Javascript: Borra(' + i + ');" />'
							+ '</tr>';
			}
			sSalida+='</table>';
			document.getElementById('tabla-cambios').innerHTML=sSalida;
		}
		
		function ProcesaTodo() {
				//Condensa el array en una cadena que se manda como parametro
				if (document.getElementById('text-duracion').value!="") {
					sTitularesQS='';
					iTotalTitularesLocales=0;
					iTotalTitularesVisitantes=0;
					for (i=1;i<=document.getElementById('total-locales').value;i++) {
						if (document.getElementById('local-'+i).checked) {
							sTitularesQS+=document.getElementById('local-'+i).name.substring(3)+"_";
							iTotalTitularesLocales++;
						}
					}
					for (i=1;i<=document.getElementById('total-visitantes').value;i++) {
						if (document.getElementById('visitante-'+i).checked) {
							sTitularesQS+=document.getElementById('visitante-'+i).name.substring(3)+"_";
							iTotalTitularesVisitantes++;
						}
					}
					if ((iTotalTitularesLocales==11) && (iTotalTitularesVisitantes==11)) {
						sCambiosQS='';
						for (i=0;i<asCambios.length;i++) {
								sCambiosQS+=asCambios[i]['id_sale'] + '_' + asCambios[i]['id_entra'] + '_' + asCambios[i]['minuto'] + '__A__';
						}
						sCambiosQS=(sCambiosQS=='') ? 'NO' : sCambiosQS; 
						location.href='admin/torneos/procesa_titulares_cambios/' + sTitularesQS
								+ '/' + sCambiosQS
								+ '/' + document.getElementById('text-duracion').value
								+ '/' + document.getElementById('hidden-temporada').value
								+ '/' + document.getElementById('hidden-torneo').value
								+ '/' + document.getElementById('hidden-partido').value
								+ '/' + document.getElementById('hidden-jornada').value;
						
					}
					else {
						alert ('Error en la cantidad de titulares. Locales:' + iTotalTitularesLocales + " Visitantes:"+ iTotalTitularesVisitantes);
					}
				}
				else
					alert ('Debe definir una duracion del partido');
		}

</script>
		<h3>Captura de titulares y cambios</h3>
		<form name="frmPartidos" action="{RUTA_RAIZ}admin/torneos/procesa_titulares_cambios" method="post">
				<fieldset>
						<legend>Partido</legend>
						<table>
								<tr>
										<td>Duracion del partido:
												<input type="button" class="button" value="90" onclick="document.getElementById('text-duracion').value='90';"  />
												<input type="button" class="button" value="120" onclick="document.getElementById('text-duracion').value='120';" />
												<input type="text" class="text" size="3" id="text-duracion" name="txtDuracionMinutos" value="{DURACION}" />
										</td>
										<td>
												<input type="button" value="Enviar datos" class="button" onclick="ProcesaTodo();" />
										</td>
								</tr>
						</table>
				</fieldset>
				<fieldset>
						<legend>Titulares</legend>
						<table class="Reportes" width="700">
							<tr>
								<td><img src="img/escudos/mini/s{LOGO_LOCAL}.gif"</td>
								<td><img src="img/escudos/mini/s{LOGO_VISITA}.gif"</td>
							</tr>
							<tr>
								<td valign="top">
									<table>
										{BLOQUE_LOCALES}
											<tr>
												<td><input type="checkbox" name="chk{ID_JUGADOR}" id="local-{NUMERO}" {CHECKED}/></td>
												<td>{NOMBRE_JUGADOR}</td>
											</tr>
										{/BLOQUE_LOCALES}
									</table>
									<input type="hidden" name="hdnTotalLocales" id="total-locales" value="{TOTAL_LOCALES}" />
								</td>
								<td valign="top">
									<table>
										{BLOQUE_VISITANTES}
											<tr>
												<td><input type="checkbox" name="chk{ID_JUGADOR}" id="visitante-{NUMERO}" {CHECKED} /></td>
												<td>{NOMBRE_JUGADOR}</td>
											</tr>
										{/BLOQUE_VISITANTES}
									</table>
									<input type="hidden" name="hdnTotalVisitantes" id="total-visitantes" value="{TOTAL_VISITANTES}" />
								</td>
							</tr>
						</table>
				</fieldset>
				<fieldset>
						<legend>Cambios</legend>
						<table>
								<tr>
										<td>Club</td>
										<td>Jugador que sale</td>
										<td>Jugador que entra</td>
										<td>Minuto del cambio</td>
										<td>&nbsp;</td>
								</tr>		
								<tr>
										<td>{NOMBRE_LOCAL}</td>
										<td>{COMBO_JUGADOR_SALE_LOCAL}</td>
										<td>{COMBO_JUGADOR_ENTRA_LOCAL}</td>
										<td><input type="text" name="txtMinutoCambioLocal" id="text-minuto-cambio-local" size="3" class="text" /></td>
										<td><input type="button" name="btnOkLocal" class="button" value="Agregar" onclick="AgregaCambio('local');" /></td>
								</tr>
								<tr>
										<td>{NOMBRE_VISITA}</td>
										<td>{COMBO_JUGADOR_SALE_VISITANTE}</td>
										<td>{COMBO_JUGADOR_ENTRA_VISITANTE}</td>
										<td><input type="text" name="txtMinutoCambioVisitante" id="text-minuto-cambio-visitante" size="3" class="text" /></td>
										<td><input type="button" name="btnOkVisitante" class="button" value="Agregar" onclick="AgregaCambio('visitante');" /></td>
								</tr>
						</table>
						<div id="tabla-cambios">
								{TABLA_CAMBIOS}
						</div>
				</fieldset>
				<input type="hidden" name="hdnLogoLocal" id="logo-local" value="{LOGO_LOCAL}" />
				<input type="hidden" name="hdnLogoVisita" id="logo-visitante" value="{LOGO_VISITA}" />
				<input type="hidden" name="hdnJornada" id="hidden-jornada" value="{JORNADA}" />
				<input type="hidden" name="hdnTemporada" id="hidden-temporada" value="{TEMPORADA}" />
				<input type="hidden" name="hdnTorneo" id="hidden-torneo" value="{TORNEO}" />
				<input type="hidden" name="hdnPartido" id="hidden-partido" value="{PARTIDO}" />
		</form>