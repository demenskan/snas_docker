<form action="<?=$RUTA_RAIZ?>admin/torneos/lista" method="post">
	<fieldset>
		<legend>Administrador de torneos</legend>
		<table class="tabla_normal" width="100%">
			<tr>
				<td>Editar un torneo de esta temporada: <?=$LISTA_TEMPORADAS?><input type="submit" class="button" value="VER"></td>
			</tr>
			<tr>
				<td align="center">
					<?=$MENSAJE?>
					<div id="lista_torneos">
						<table width="100%" border="0">
						<?=$TABLA_TORNEOS?>
						</table>
					</div>	
				</td>
			</tr>
		</table>
	</fieldset>
</form>
