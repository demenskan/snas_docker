		<table>
			<thead>
				<th colspan="3">Roster de {CLUB}, temporada {TEMPORADA}</th>
			</thead>
			<tr>
				<td>ID</td>
				<td>Playera</td>
				<td>Jugador</td>
			</tr>
			{BLOQUE_JUGADORES}
			<tr>
				<td>{ID_JUGADOR}</td>
				<td>{NUMERO}</td>
				<td><a href="JavaScript: LlenaHistorial({ID_JUGADOR});">{NOMBRE_JUGADOR}</a></td>
			</tr>
			{/BLOQUE_JUGADORES}
		</table>