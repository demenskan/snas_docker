<form name="frmDesignados" action="admin/torneos/procesa_designados" method="post">
		<fieldset>
			<legend><h2>Captura de designados</h2></legend>		
			<table class="data-table">
				<tr>
					<th>Local</th>
					<th>Visitante</th>
					<th colspan="2">Datos</th>
				</tr>
				<tr>
					<td rowspan="2"><img src="img/escudos/mini/s{LOGO_LOCAL}.gif"/></td>
					<td rowspan="2"><img src="img/escudos/mini/s{LOGO_VISITA}.gif"/></td>
					<td><strong>Tipo:</strong></td>
					<td>{TIPO}</td>
				</tr>
				<tr>
					<td><strong>Jornada:</strong></td>
					<td>{JORNADA}</td>
				</tr>
				<tr>
					<td>{NOMBRE_LOCAL}</td>
					<td>{NOMBRE_VISITA}</td>
					<td><strong>Temporada:</strong></td>
					<td>{TEMPORADA}</td>
				</tr>
				<tr>
					<td>{COMBO_DESIGNADO_LOCAL}</td>
					<td>{COMBO_DESIGNADO_VISITANTE}</td>
					<td><strong>Torneo:</strong></td>
					<td>{NOMBRE_TORNEO}</td>
				</tr>
				<tr>
					<td colspan="4"><input type="submit" value="Actualizar" class="button"></td>
				</tr>
			</table>
		</fieldset>
		<input type="hidden" name="hdnTemporada" value="{TEMPORADA}" />
		<input type="hidden" name="hdnTorneo" value="{TORNEO}" />
		<input type="hidden" name="hdnPartido" value="{PARTIDO}" />
		<input type="hidden" name="hdnJornada" value="{JORNADA}" />

</form>