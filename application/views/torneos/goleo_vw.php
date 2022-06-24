<script type="text/javascript" language="JavaScript">
	
	function ValidaForma() {
		if (document.frmFiltroGoleo.slcDesdeJornada.value > document.frmFiltroGoleo.slcHastaJornada.value)
			alert ('Conflicto con las jornadas');
		else
			document.frmFiltroGoleo.submit();
	}

</script>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<fieldset>
	<form name="frmFiltroGoleo" action="<?=$RUTA_RAIZ?>torneos/Goleo/<?=$ID_TEMPORADA?>/<?=$ID_TORNEO?>" method="post">
		<table class="data-table" >
			<tr>
				<th colspan="4">Opciones Filtro</th>
			</tr>
			<tr>
				<td>Desde la jornada:</td>
				<td><?=$LISTA_JORNADAS_DESDE?></td>
				<td>Hasta la jornada:</td>
				<td><?=$LISTA_JORNADAS_HASTA?></td>
			</tr>	
			<tr>
				<td>Club:</td>
				<td><?=$LISTA_CLUBES?></td>
				<td>Limite:</td>
				<td><input type="text" name="txtLimite" value="<?=$LIMITE?>" class="text" />
			</tr>
			<tr class="even">
				<td colspan="4"><input type="button" class="button" onclick="JavaScript: ValidaForma();" value="Revisar Filtrado" />
			</tr>
		</table>
		<input type="hidden" name="ssn" value="<?=$ID_TEMPORADA?>" />
		<input type="hidden" name="tor" value="<?=$ID_TORNEO?>" />
	</form>
	<legend> Goleo </legend>
	<table class="tabla_resultados" align="center" cellspacing="0" width="500">
	<?=$ENCABEZADO?>
	<?=$RESULTADO?>
	</table>
</fieldset>