	<script language="JavaScript">
	
		function Selecciona(psNombreArchivo) {
			opener.document.frmNoticias.txtImagenGaleria.value=psNombreArchivo;
			self.close();
		}
	
	</script>

	<fieldset>
		<legend>Selecciona una imagen</legend>
		<table class="table" width="300">
			<?=$IMAGENES?>
		</table>
	</fieldset>
