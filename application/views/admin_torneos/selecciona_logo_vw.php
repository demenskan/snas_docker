	<script language="JavaScript">
	
		function Selecciona(psNombreArchivo) {
			opener.document.frmNuevoTorneo.txtLogoGaleria.value=psNombreArchivo;
			self.close();
		}
	
	
	</script>

	<fieldset>
		<legend>Selecciona un logotipo</legend>
		<table class="table" width="300">
			<?=$LOGOTIPOS?>
		</table>
	</fieldset>
