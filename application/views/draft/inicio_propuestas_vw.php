	<h3>Propuestas recibidas</h3>
	<table class="Reportes" width="100%">
		<thead>
			<th>Cliente</th>
			<th>Ofrece</th>
			<th>Solicita</th>
		</thead>
		{PROPUESTAS_RECIBIDAS}
		<tr class="{CLASE_FILA}">
			<td>{CLIENTE}</td>
			<td>{OFRECE}</td>
			<td>{SOLICITA}</td>
		</tr>
		{/PROPUESTAS_RECIBIDAS}
		<tr>
			<td colspan="3">
				<a href="admin/draft_propuestas/revisa">Ver todas las propuestas...</a>
			</td>
		</tr>
	</table>
