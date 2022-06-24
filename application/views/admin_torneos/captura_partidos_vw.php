<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
<script type="text/javascript" src="{RUTA_RAIZ}externos/js/jquery-1.4.1.js" language="JavaScript"></script>
<script type="text/javascript" src="{RUTA_RAIZ}externos/js/jquery.autocomplete.min.js" language="JavaScript"></script>
<link rel="stylesheet" type="text/css" href="{RUTA_RAIZ}externos/js/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="{RUTA_RAIZ}externos/js/cwcalendar.css" media="all" />
<script language="JavaScript" type="text/javascript" src="{RUTA_RAIZ}externos/js/calendar.js" /></script>




<script type="text/javascript" language="JavaScript">
		var Lista_jugadores=[
{BLOQUE_LISTA_JUGADORES}
			{ clave: {CLAVE}, nombre: '{NUMERO} -{NOMBRE} ({INICIALES})', estatus: '{ESTATUS}' 	}{COMA}{/BLOQUE_LISTA_JUGADORES}
		];
		
		
		function mandaForma () {
			/*Esta funcion es para que no se vea la cadenota en el codigo, solo da un poco de orden */
			/*var sCadena= 'admin_torneos/cierra_partido.php?'
				+'ssn='+document.frmPartidos.hdnTemporada.value
				+'&tor='+document.frmPartidos.hdnTorneo.value
				+'&cve='+document.frmPartidos.hdnPartido.value
				+'&coment='+document.frmPartidos.taComentario.value
				+'&fecha='+document.frmPartidos.txtFechaJugado.value
				+'&chk='+document.frmPartidos.chkNoEspecificado.checked
				+'&jor='+document.frmPartidos.hdnJornada.value
				+'&acc='+document.frmPartidos.rbAccion.value;	
			location.href=sCadena;*/
			document.frmPartidos.submit();
		}
		
		function agregaEvento() {
			var sURL='{RUTA_RAIZ}admin/torneos/agrega_evento/'
				+document.frmPartidos.hdnTemporada.value
				+'/'+document.frmPartidos.hdnTorneo.value
				+'/'+document.frmPartidos.hdnPartido.value
				+'/'+document.frmPartidos.txtJugador.value
				+'/'+document.frmPartidos.txtMinuto.value
				+'/'+document.frmPartidos.slcEvento.value;
		    
			callPage(sURL,'lista-eventos','Cargando...',sURL);
			document.frmPartidos.txtJugador.focus();
			document.frmPartidos.txtJugador.value='';		
		}
		

		$().ready(function() {

				function findValueCallback(event, data, formatted) {
					$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
				}
			
				function formatItem(row) {
					return row[0] + " (<strong>id: " + row[1] + "</strong>)";
				}
				
				function formatResult(row) {
					return row[0].replace(/(<.+})/gi, '');
				}
			
				$("#id-jugador").autocomplete(Lista_jugadores, {
					matchContains: true,
					minChars: 0,
					formatItem: function(row, i, max) {
						return i + "/" + max + ": \"" + row.nombre + "\" [" + row.clave + "]";
					},
					formatResult: function(row) {
						return row.clave + "_" + row.estatus;
					}
				});
				
		});
		
</script>
<form name="frmPartidos" action="{RUTA_RAIZ}admin/torneos/cierra_partido" method="post">
	<fieldset>
		<legend>Captura de Partidos</legend>
		<table class="Reportes" width="700">
			<tr>
				<th>Jugador</th>
				<th>Minuto</th>
				<th colspan="2">Evento</th>
			</tr>
			<tr>
				<td><input class="text" type="text" name="txtJugador" autocomplete="off" id="id-jugador" size="25" /></td>
				<td><input class="text" type="text" name="txtMinuto" autocomplete="off" size="2" /></td>
				<td>{COMBO_EVENTOS}</td>
				<td><input type="button" class="button" value="Agregar" onclick="JavaScript: agregaEvento();"></td>
			</tr>
			<tr>
				<td colspan="2" rowspan="2">
					<div id="lista-eventos">	
						{EVENTOS}
					</div>
				</td>
				<td colspan="2">
						Comentarios: <br>
						<textarea name="taComentario" cols="35" rows="7"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">Fecha juego:
					<input type="checkbox" name="chkNoEspecificado" />No especificado
					<input type="text" class="text" name="txtFechaJugado" size="10" readonly="readonly" value="{FECHA_JUEGO}" id="fecha" onClick="fPopCalendar('fecha')" />
					<a onclick="fPopCalendar('fecha')"><img src="{RUTA_RAIZ}img/calendar-icon-small.gif" border="0" height="18" /></a><br />
					<fieldset>
						<legend>Despues de grabar:</legend>
						<ul>
							<li><input type="radio" name="rbAccion" value="1" checked="checked">  Ir al siguiente partido</li>
							<li><input type="radio" name="rbAccion" value="2"> Regresar al seleccionador</li>
						</ul>
					</fieldset>
					<input type="button" class="button" value="Ok" onclick="JavaScript: mandaForma();">
				</td>
			</tr>
			<tr>
				<td>Sede: {ESTADIO}</td>
				<td colspan="2">Torneo: {NOMBRE_TORNEO}</td>
				<td>Temporada: {TEMPORADA}</td>
			</tr>
		</table>
	</fieldset>
	<input type="hidden" name="hdnJornada" value="{JORNADA}" />
	<input type="hidden" name="hdnTemporada" value="{TEMPORADA}" />
	<input type="hidden" name="hdnTorneo" value="{TORNEO}" />
	<input type="hidden" name="hdnPartido" value="{PARTIDO}" />
	<input type="hidden" name="code" value="880497B0" />
</form>