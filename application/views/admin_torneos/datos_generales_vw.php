<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/ajax_tabs.js" language="JavaScript"></script>
<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/jquery-1.4.1.js" language="JavaScript"></script>

<script language="JavaScript">

	function AbreVentana() {
		var x=window.open('<?=$RUTA_RAIZ?>admin/torneos/selecciona_logo','Select','width=600, height=400, scrollbars=1');
	}

	function SeleccionaClave(piClave) {
		document.frmNuevoTorneo.hdnClave.value=piClave;
		document.getElementById('lista-claves').innerHTML=piClave;
	}

</script>

<form action="<?=$RUTA_RAIZ?>admin/torneos/graba/<?=$MODO?>" method="post" name="frmNuevoTorneo"  enctype="multipart/form-data">
	<fieldset>
		<legend>Creacion de torneos</legend>
		<table class="reportes" width="100%">
			<tr>
				<td>Temporada: </td>
				<td><?=$LISTA_TEMPORADAS?></td>
			</tr>
			<tr class="even">
				<td>Clase: </td>
				<td><?=$LISTA_CLASES?></td>
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
				<td>Nombre: </td>
				<td><input type="text" class="text" name="txtNombre" size="20" <?=$NOMBRE_TORNEO?> /></td>
			</tr>
			<tr class="even">
				<td>Descripcion: </td>
				<td><textarea class="text" name="taDescripcion" cols="20" rows="5"><?=$DESCRIPCION?></textarea></td>
			</tr>
			<tr>
				<th colspan="2">Logotipo</th>
			</tr>
			<tr class="even">
				<td>
					<input type="radio" checked="checked"  name="rbTipoLogo" value="galeria">Seleccionar de la <a href="JavaScript: AbreVentana();">galeria</a>
				</td>
				<td>
					<input type="text" class="text" name="txtLogoGaleria" readonly="readonly" <?=$RUTA_LOGO?> />
				</td>	
			</tr>
			<tr>
				<td>
					<input type="radio" name="rbTipoLogo" value="archivo">Subir uno nuevo (200x200 pixeles)
				</td>
				<td>
					<input type="file" name="fLogo" />
				</td>	
			</tr>
			<tr class="even">
				<td>Estatus: </td>
				<td><?=$LISTA_ESTATUS?></td>
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
