<p>
	<h2>Lista General de Propuestas</h2>
	Estas son las propuestas que se han hecho hasta la fecha.
</p>
	<table class="Reportes" width="100%">
		<thead>
			<th>Clave</th>
			<th>Ofertante</th>
			<th>Involucrado</th>
			<th>Ofrece</th>
			<th>Solicita</th>
			<th>Mensaje</th>
			<th>Fecha Solicitud</th>
			<th>Estatus</th>
		</thead>
{BLOQUE_PROPUESTAS}
		<tr class="{CLASE}">
			<td>{CLAVE}</td>
			<td>{OFERTANTE}</td>
			<td>{INVOLUCRADO}</td>
			<td>{OFRECE}</td>
			<td>{SOLICITA}</td>
			<td>{MENSAJE}</td>
			<td>{FECHA_SOLICITUD}</td>
			<td>{ESTATUS}</td>
		</tr>
{/BLOQUE_PROPUESTAS}
		<tr>
			<td colspan="8" align="center"><a href="{RUTA_RAIZ}{INDEX_URI}admin/inicio/draft">Regresar al menu del draft</a></td>
		</tr>
	</table>
</form>
