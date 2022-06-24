	<table class="Reportes" align="center" cellspacing="0" cellpadding="10" width="200">
		<thead>
			<td colspan="3"><?=$TIPO_PARTIDO?></td>
		</thead>		
		<tr>
			<td rowspan="2" ><img src=<?=$LOGO_LOCAL?>  width="50" height="50" /></td>
			<td colspan="2"><?=$REGISTRO_LOCAL?></td>
		</tr>	
		<tr <?=$ESTILO_LOCAL?>>
			<td><?=$NOMBRE_LOCAL?></td>
			<td><?=$MARCADOR_LOCAL?></td>
		</tr>
		<tr >
			<td rowspan="2"><img src=<?=$LOGO_VISITA?>  width="50" height="50" /></td>
			<td colspan="2"><?=$REGISTRO_VISITA?></td>
		</tr>
		<tr <?=$ESTILO_VISITA?>>
			<td><?=$NOMBRE_VISITA?></td>
			<td><?=$MARCADOR_VISITA?></td>
		</tr>
		<tr>
			<td colspan="2" align="center">Estadio: <?=$ESTADIO?></td>
			<td>Jornada <?=$JORNADA?></td>
		</tr>
	</table>	
		<?=$EVENTOS?>
	<table class="Reportes">
		<thead>
			<td colspan="2"><?=$FECHA_JUEGO?></td>
		</thead>
		<tr class="par">
			<td>Comentarios:</td>
			<td><?=$COMENTARIOS?></td>
		</tr>
	</table>
