		<script src="externos/js/jquery-1.9.1.js"></script>
		<script>
			function LlenaRoster(piClub, piTemporada) {
				$.ajax( 'root/procesos_especiales/xi_roster/' +  piClub + '/' + piTemporada)
					.done(function(response) {
					  $('#lista-jugadores').html(response);
					})
					.fail(function() {
					   $('#lista-jugadores').html("error");
					});
			}
			
			function LlenaHistorial(piJugador) {
				//Llena el historial de clubes de un jugador
				$.ajax( 'root/procesos_especiales/xi_historial/' +  piJugador)
					.done(function(response) {
					  $('#lista-historial').html(response);
					})
					.fail(function() {
					   $('#lista-historial').html("error");
					});
			}
			
		</script>

		<form name="frmReporte" action="root/procesos_especiales/reporte_directo_rosters" method="post">
			<table class="data-table">
				<tr>
					<th>Mostrar de:</th>
					<th></th>
				</tr>	
				<tr>
					<td>{COMBO_CONSEJOS}</td>
					<td><input type="submit" value="Ver" class="button"  />
				</tr>	
			</table>
		</form>
		<table>
			<tr>
				<td width="50%">
					{REPORTE}
				</td>
				<td  width="25%">
					<div id="lista-jugadores"></div>
				</td>
				<td  width="25%">
					<div id="lista-historial"></div>
				</td>
			</tr>
		</table>