	<h3>CONTRATACIONES RECIENTES</h3>
	<table class="Reportes" width="100%">
		<thead>
			<th>Nombre</th>
			<th>Club</th>
			<th>Sueldo base</th>
			<th>Temporadas</th>
		</thead>
		{BLOQUE_CONTRATADOS}
		<tr class="{CLASE_FILA}">
			<td>{NOMBRE}</td>
			<td>{CLUB}</td>
			<td>{SUELDO_BASE}</td>
			<td>{TEMPORADAS}</td>
		</tr>
		{/BLOQUE_CONTRATADOS}
		<tr>
			<td colspan="3">
				<a href="admin/draft_reportes/contrataciones_consejo">Ver todas las contrataciones...</a>
			</td
		</tr>
	</table>
