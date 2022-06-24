<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/ajax_tabs.js" language="JavaScript"></script>
<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/jquery-1.4.1.js" language="JavaScript"></script>


<form action="<?=$RUTA_RAIZ?>admin/torneos/graba_documento/<?=$MODO?>" method="post" name="frmNuevoDocumento"  enctype="multipart/form-data">
	<fieldset>
		<legend>Documentos de torneo</legend>
		<table class="reportes" width="100%">
			<tr>
				<td>Temporada: </td>
				<td><?=$LISTA_TEMPORADAS?></td>
			</tr>
			<tr class="even">
				<td>Identificador: </td>
				<td><input type="text" class="text" name="txtIdentificador" size="20" <?=$NOMBRE_TORNEO?> /><?=$LISTA_CLASES?></td>
			</tr>
			<tr>
				<td>Clave: </td>
				<td>
					<?=$CLAVE_TORNEO?>
				</td>
			</tr>
			<tr class="even">
				<td>Tipo: </td>
				<td><?=$LISTA_TIPOS?></td>
			</tr>
			<tr>
				<td>Titulo: </td>
				<td><input type="text" class="text" name="txtTitulo" size="20" <?=$TITULO?> /></td>
			</tr>
			<tr class="even">
				<td>Texto: </td>
				<td><textarea class="text" name="taTexto" cols="20" rows="5"><?=$TEXTO?></textarea></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" class="button" value="Guardar" />
				</td>
			</tr>
		</table>
	<input type="hidden" name="modo" value="<?=$MODO?>" />
	<input type="hidden" name="MAX_FILE_SIZE" value="400000" />
	</fieldset>
</form>
