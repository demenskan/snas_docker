	<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
	<form name="frmJugadores" id="forma{PARAMETRO}" method="post">
		Presupuesto disponible: {PRESUPUESTO} KCacaos. <br/>
		<input type="checkbox" name="chkEfectivo{MODO}" /> Efectivo (Kcacaos): <input type="text" id="text-efectivo-{MODO}" name="txtEfectivo{MODO}" class="text" size="5" />
		<table class="tabla_normal">
			<thead>
				<th></th>
				<th>Jugador</th>
				<th>Salario</th>
				<th>Temp. Restantes</th>
				<th>Puntos Hab.</th>
			</thead>
			{LISTA_JUGADORES}
			<tr class="{COLOR}">
				<td id="{ID_CLAVE}_{CLAVE_JUGADOR}">{CONTROL_CHECKBOX}</td>
				<td id="{ID_NOMBRE}_{CLAVE_JUGADOR}">{NOMBRE}</td>
				<td id="{ID_NOMBRE}_{CLAVE_JUGADOR}">{SALARIO}</td>
				<td id="{ID_NOMBRE}_{CLAVE_JUGADOR}">({TEMPORADAS_RESTANTES})</td>
				<td id="{ID_NOMBRE}_{CLAVE_JUGADOR}" align="right">{TOTAL_PUNTOS}</td>
			</tr>
			{/LISTA_JUGADORES}
			<tr>
				<td colspan="4"><input type="button" class="button" id="boton-agregar-{MODO}" value="Agregar jugador" OnClick="AgregaJugador({PARAMETRO});" /></td>
			</tr>
		</table>
		<input type="hidden" name="hdnTemporada" value="{TEMPORADA}" />
		<input type="hidden" name="hdnClaveEquipo" value="{CLAVE_EQUIPO}" />
		<input type="hidden" name="hdnPresupuestoMaximo" value="{PRESUPUESTO}" />
	</form>
