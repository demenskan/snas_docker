<p>
	<h2>Propuestas de cambios</h2>
	Estas son las propuestas que tienes. Si aceptas alguna, el cambio se har&aacute; oficial.
</p>
<form name="frmPropuestas" action="{RUTA_RAIZ}{INDEX_URI}admin/draft_propuestas/autoriza" method="post">
	<h3>Propuestas de otros</h3>
	<table class="Reportes" width="100%">
		<thead>
			<th>Clave</th>
			<th>Cliente</th>
			<th>Ofrece</th>
			<th>Solicita</th>
			<th>Mensaje</th>
			<th>Fecha Solicitud</th>
			<th>Aprobar?</th>
			<th>&nbsp;</th>
		</thead>
		{PROPUESTAS_RECIBIDAS}
		<tr class="{CLASE_FILA}">
			<td>{CLAVE}</td>
			<td>{CLIENTE}</td>
			<td>{OFRECE}</td>
			<td>{SOLICITA}</td>
			<td>{MENSAJE}</td>
			<td>{FECHA_SOLICITUD}</td>
			<td>{RADIO}</td>
			<td>{LINK_CANCELAR}</td>
		</tr>
		{/PROPUESTAS_RECIBIDAS}
		<tr>
			<td colspan="6" align="center"><input type="submit" name="x" class="button" value="Aceptar propuesta" /></td>
		</tr>
	</table>
	<h3>Mis propuestas</h3>
	<table class="Reportes" width="100%">
		<thead>
			<th>Clave</th>
			<th>Cliente</th>
			<th>Ofrezco</th>
			<th>Solicito</th>
			<th>Mensaje</th>
			<th>Fecha Solicitud</th>
			<th>Estatus</th>
			<th>&nbsp;</th>
		</thead>
		{PROPUESTAS_PROPIAS}
		<tr class="{CLASE_FILA}">
			<td>{CLAVE}</td>
			<td>{CLIENTE}</td>
			<td>{OFRECE}</td>
			<td>{SOLICITA}</td>
			<td>{MENSAJE}</td>
			<td>{FECHA_SOLICITUD}</td>
			<td>{RADIO}</td>
			<td>{LINK_CANCELAR}</td>
		</tr>
		{/PROPUESTAS_PROPIAS}
	</table>
	<input type="hidden" name="code" value="55000405" />
</form>
