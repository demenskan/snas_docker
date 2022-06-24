		<table>
			<thead>
				<th colspan="2">
					Historial de {JUGADOR}
				</th>
			</thead>
			<tr>
				<td>Temporada</td>
				<td>Club</td>
			</tr>
			{BLOQUE_TEMPORADAS}
			<tr>
				<td>{TEMPORADA}</td>
				<td><a href="JavaScript: LlenaRoster({ID_CLUB},{TEMPORADA});">{NOMBRE_CLUB}</a></td>
			</tr>
			{/BLOQUE_TEMPORADAS}
		</table>