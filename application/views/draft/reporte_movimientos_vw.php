<script language="javascript">
	function gotoPagina(sFechaInicio, sFechaFin, iPagina, iRenglonesxPagina) {
		sUrl="admin/draft_reportes/bitacora_contrataciones/" + sFechaInicio + "/" + sFechaFin + "/" + iPagina + "/" + iRenglonesxPagina;
		location.href=sUrl;
	}	
	
</script>
<form action="admin/draft_reportes/bitacora_contrataciones" method="post" name="frmContrataciones">
	<table class="Reportes" align="center" width="100%">
		<tr>
			<td colspan="3" id="titulo"><h2>Resumen de movimientos</h2></td>
		</tr>
		<tr>
			<td colspan="3">
				Fecha Inicio: <input type="date" name="txtFechaInicio" value="{FECHA_INICIO}" />
				Fecha Fin: <input type="date" name="txtFechaFin" value="{FECHA_FIN}" />
				Pagina: {COMBO_PAGINAS}
				<input type="submit" value="Revisar" class="button" />
			</td>
		</tr>
	</table>
	<table class="Reportes" width="100%">
		<thead>
			<th>Fecha</th>
			<th>Tipo</th>
			<th>Consejo</th>
			<th>Club</th>
			<th>Jugador</th>
			<th>Detalles</th>
		</thead>
{BLOQUE_EVENTOS}
		<tr class="{CLASE}">
			<td>{FECHA}</td>
			<td>{TIPO}</td>
			<td>{CONSEJO}</td>
			<td>{CLUB}</td>
			<td>{JUGADOR}</td>
			<td>{DETALLES}</td>
		</tr>
{/BLOQUE_EVENTOS}		
	</table>
</form>