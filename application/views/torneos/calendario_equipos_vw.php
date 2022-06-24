<form name="frmEquipo" action="<?=$RUTA_RAIZ?>torneos/CalendarioEquipo/<?=$TEMPORADA?>/<?=$TORNEO?>" method="post" >
	<table width="100%">
		<tr>
			<td>Seleccione un equipo:</td>
			<td>
				<?=$SELECTOR?>
				<input type="submit" value="Ver" class="button" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tabla_resultados" align="center" cellspacing="0" cellpadding="10" width="500">
					<tr>
						<td colspan="8"><h2><?=$LOGOTIPO?><?=$NOMBRE_EQUIPO?></h2></td>
					</tr>
				</table>		
				<?=$RESULTADO?>
			</td>
		</tr>
	</table>
	<input type="hidden" name="ssn" value="<?=$TEMPORADA?>" />
	<input type="hidden" name="tor" value="<?=$TORNEO?>" />
</form>