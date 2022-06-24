	<h3>CONTRATACIONES RECIENTES</h3>
	<table class="Reportes" width="100%">
		<thead>
			<th>Nombre</th>
			<th>Club</th>
			<th>Sueldo base (KiloCacaos)</th>
			<th>Temporadas</th>
		</thead>
		{BLOQUE_CONTRATADOS}
		<tr class="{CLASE_FILA}">
			<td>{NOMBRE}</td>
			<td><a href="admin/draft_movimientos/alineacionesjugadores/{ID_CLUB}">{CLUB}</a></td>
			<td>{SUELDO_BASE}</td>
			<td>{TEMPORADAS}</td>
		</tr>
		{/BLOQUE_CONTRATADOS}
	</table>
