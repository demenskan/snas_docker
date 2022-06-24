<form action="{RUTA_RAIZ}{INDEX_URI}admin/draft_reportes/resumenmovimientos" method="post" name="frmResumenMovimientos">
	<table class="Reportes" align="center" width="100%">
		<tr>
			<td colspan="3" id="titulo"><h2>Resumen de movimientos</h2></td>
		</tr>
		<tr>
			<td colspan="3">
				{COMBO_CONSEJOS}
				<input type="submit" value="Refrescar" class="button" />
			</td>
		</tr>
	</table>
	<table class="Reportes" width="100%">
		<thead>
			<th>Equipo</th>
			<th>Altas</th>
			<th>Bajas</th>
		</thead>
{BLOQUE_EQUIPOS}
		<tr class="{CLASE}">
			<td><img src="{RUTA_RAIZ}img/escudos/mini/{RUTA_LOGO}.gif"/>{CLUB}</td>
			<td>{ALTAS}</td>
			<td>{BAJAS}</td>
		</tr>
{/BLOQUE_EQUIPOS}		
	</table>
</form>