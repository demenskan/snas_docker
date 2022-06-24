<p>
	<h2>Cancelacion de propuestas</h2>
	Estas son las propuestas que tienes. Si no te apetece, la puedes cancelar o rechazar.
</p>
<form name="frmPropuestas" action="{RUTA_RAIZ}admin/draft_propuestas/cancela" method="post">
	<table class="tabla_resultados" width="100%">
		<tr>
			<td id="titulo">Clave</td>
			<td id="titulo">Cliente</td>
			<td id="titulo">Ofrece</td>
			<td id="titulo">Solicita</td>
			<td id="titulo">Mensaje</td>
			<td id="titulo">Fecha Solicitud</td>
			<td id="titulo">Rechazar?</td>
		</tr>
{BLOQUE_PROPUESTAS}
		<tr class="{CLASE}">
			<td>{CLAVE}</td>
			<td>{OFERTADOR}</td>
			<td>{JUGADORES_OFRECE}</td>
			<td>{JUGADORES_SOLICITA}</td>
			<td>{MENSAJE}</td>
			<td>{FECHA}</td>
			<td>{ESTATUS}</td>
		</tr>
{/BLOQUE_PROPUESTAS}
		<tr>
			<td colspan="6" align="center"><input type="submit" name="x" value="Rechazar propuesta" /></td>
		</tr>
	</table>
</form>
