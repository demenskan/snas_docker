	<script type="text/javascript" src="scripts/ajax_tabs.js" language="JavaScript"></script>
	<form name="formDisponibles" method="post">
		<table class="tabla_normal">
			{LISTA_JUGADORES}
			<tr>
				<td class="celda_normal" id="a{CLAVE}"><input type="checkbox" name="id{CLAVE}" {CHECADO} OnClick="ChangeColorOf({CLAVE});" /></td>
				<td class="{COLOR}" id="b{CLAVE}">{NOMBRE}</td>
			</tr>
			{/LISTA_JUGADORES}
			<tr>
				<td colspan="3"><input type="button" class="button" value="Actualizar Lista" OnClick="Actualiza();" /></td>
			</tr>
		</table>
		<input type="hidden" name="hdnTemporada" value="{TEMPORADA}" />
		<input type="hidden" name="hdnClaveEquipo" value="{CLAVE_CLUB}" />
	</form>
