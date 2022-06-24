<script type="text/javascript" src="scripts/ajax_tabs.js" language="JavaScript"></script>
<script type="text/javascript" src="scripts/jquery-1.4.1.js" language="JavaScript"></script>
<script type="text/javascript" src="scripts/jquery.autocomplete.min.js" language="JavaScript"></script>
<link rel="stylesheet" type="text/css" href="scripts/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="scripts/cwcalendar.css" media="all" />
<script language="JavaScript" type="text/javascript" src="scripts/calendar.js" /></script>




<script type="text/javascript" language="JavaScript">
		var Lista_jugadores=[
				<?=$LISTA_JUGADORES?>
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
			var sURL='admin_torneos/xi_agrega_evento.php?jug='+document.frmPartidos.txtJugador.value
					+'&min='+document.frmPartidos.txtMinuto.value
					+'&tor='+document.frmPartidos.hdnTorneo.value
					+'&ssn='+document.frmPartidos.hdnTemporada.value
					+'&cve='+document.frmPartidos.hdnPartido.value
					+'&eve='+document.frmPartidos.slcEvento.value;
			callPage(sURL,'lista-eventos','Cargando...','Error:'.sURL);
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
					return row[0].replace(/(<.+?>)/gi, '');
				}
			
				$("#id-jugador").autocomplete(Lista_jugadores, {
					matchContains: true,
					minChars: 0,
					formatItem: function(row, i, max) {
						return i + "/" + max + ": \"" + row.nombre + "\" [" + row.clave + "]";
					},
					formatResult: function(row) {
						return row.clave + "|" + row.estatus;
					}
				});
				
		});
		
</script>
<form name="frmPartidos" action="generador.php">
	<fieldset>
		<legend>Captura de Partidos</legend>
		<table class="data-table" width="700">
			<tr>
				<th>Jugador</th>
				<th>Minuto</th>
				<th colspan="2">Evento</th>
			</tr>
			<tr>
				<td><input class="text" type="text" name="txtJugador" autocomplete="off" id="id-jugador" size="25" /></td>
				<td><input class="text" type="text" name="txtMinuto" autocomplete="off" size="2" value="-1" /></td>
				<td><?=$LISTA_EVENTOS?></td>
				<td><input type="button" class="button" value="Agregar" onclick="JavaScript: agregaEvento();"></td>
			</tr>
			<tr>
				<td>Comentarios: <br>
					<textarea name="taComentario" cols="35" rows="7"></textarea>
				</td>
				<td colspan="2">Fecha juego:
					<input type="checkbox" name="chkNoEspecificado" checked="checked" />No especificado
					<input type="text" class="text" name="txtFechaJugado" size="10" readonly="readonly" value="<?=$FECHA_JUEGO?>" id="fecha" onClick="fPopCalendar('fecha')" />
					<a onclick="fPopCalendar('fecha')"><img src="img/calendar-icon-small.gif" border="0" height="18" /></a><br />
					<fieldset>
						<legend>Despues de grabar:</legend>
						<ul>
							<li><input type="radio" name="rbAccion" value="1" checked="checked">  Ir al siguiente partido</li>
							<li><input type="radio" name="rbAccion" value="2"> Regresar al seleccionador</li>
						</ul>
					</fieldset>
					<input type="button" class="button" value="Ok" onclick="JavaScript: mandaForma();">
				</td>
				<td>
					<div id="lista-eventos">	
						<table class="tabla_resultados" align="center" cellspacing="0" cellpadding="10" width="500">
							<tr>
								<th><img src=<?=$LOGO_LOCAL?> alt="<?=$NOMBRE_LOCAL?>"  width="30" height="30" /></th>
								<th><h3><?=$MARCADOR_LOCAL?></h3></th>
								<th><img src=<?=$LOGO_VISITA?> alt="<?=$NOMBRE_VISITA?>" width="30" height="30" /></th>
								<th colspan="2"><h3><?=$MARCADOR_VISITA?></h3></th>	
							</tr>
							<tr>
								<td colspan="5"></td>
							</tr>
							<?=$EVENTOS?>
						</table>		
					</div>
				</td>
			</tr>
			<tr>
				<td>Sede: <?=$ESTADIO?></td>
				<td colspan="2">Torneo: <?=$NOMBRE_TORNEO?></td>
				<td>Temporada: <?=$TEMPORADA?></td>
			</tr>
		</table>
	</fieldset>
	<input type="hidden" name="hdnJornada" value="<?=$JORNADA?>" />
	<input type="hidden" name="hdnTemporada" value="<?=$TEMPORADA?>" />
	<input type="hidden" name="hdnTorneo" value="<?=$TORNEO?>" />
	<input type="hidden" name="hdnPartido" value="<?=$PARTIDO?>" />
	<input type="hidden" name="code" value="880497B0" />
</form>
