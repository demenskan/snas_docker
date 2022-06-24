<form action="<?=$RUTA_RAIZ?>admin/torneos/actualiza_datos_partido" method="post">
	<fieldset>
		<legend>Cambio de Datos de Partido</legend>
		<table class="Reportes" width="100%">
			<tr>
				<td><img src="<?=$LOGO_CLUB_LOCAL?>"></td>
				<td><?=$NOMBRE_CLUB_LOCAL?></td>
				<td><img src="<?=$LOGO_CLUB_VISITANTE?>"></td>
				<td><?=$NOMBRE_CLUB_VISITANTE?></td>
			</tr>
		</table>
		<table class="data-table">
			<tr>
				<td>Tipo</td>
				<td><?=$LISTA_TIPOS_PARTIDO?></td>
			</tr>
			<tr>
				<td>Torneo:</td>
				<td><?=$TORNEO?></td>
			</tr>
			<tr>
				<td>Jornada:</td>
				<td><input type="text" class="text" name="txtJornada" value="<?=$JORNADA?>" size="4" /></td>
			</tr>
			<tr>
				<td>Temporada:</td>
				<td><?=$TEMPORADA?></td>
			</tr>
			<tr>
				<td>Estadio:</td>
				<td><?=$LISTA_ESTADIOS?></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" class="button" value="Cambiar" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="hdnTorneo" value="<?=$ID_TORNEO?>" />
		<input type="hidden" name="hdnPartido" value="<?=$ID_PARTIDO?>" />
		<input type="hidden" name="hdnTemporada" value="<?=$ID_TEMPORADA?>" />		
	</fieldset>
</form>
