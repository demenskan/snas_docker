	<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
		<table class="tabla_normal">
			<thead>
				<th>&nbsp;</th>
				<th>Jugador</th>
				<th>Precio base</th>
				<th>Inflacion</th>
				<th>Penalizaci&oacute;n</th>
				<th>Total a pagar esta temporada</th>
			</thead>
{BLOQUE_JUGADORES}
			<tr>
				<td class="celda_normal" id="{PREFIJO}c_{ID_JUGADOR}">{CHECKBOX}</td>
				<td class="{CLASE}" id="{PREFIJO}n_{ID_JUGADOR}">{NOMBRE}</td>
				<td class="{CLASE}" id="{PREFIJO}n_{ID_JUGADOR}">{PRECIO_BASE}</td>
				<td class="{CLASE}" id="{PREFIJO}n_{ID_JUGADOR}">{INFLACION}</td>
				<td class="{CLASE}" id="{PREFIJO}n_{ID_JUGADOR}">{PENALIZACION}</td>
				<td class="{CLASE}" id="{PREFIJO}n_{ID_JUGADOR}">{TOTAL_PAGAR_TEMPORADA}</td>
			</tr>
{/BLOQUE_JUGADORES}
			<tr>
				<td colspan="6">Presupuesto del club: {PRESUPUESTO} KCacaos</td>
			</tr>
		</table>
		<input type="hidden" name="hdnTemporada" value="{TEMPORADA}" />
		<input type="hidden" name="hdnClaveEquipo" value="{CLAVE_CLUB}" />
