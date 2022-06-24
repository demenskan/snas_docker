<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<?=$MENSAJE?>
<table class="tabla_resultados" align="center" cellspacing="0" width="500">
	<tr>
		<th id="titulo">&nbsp;</th>
		<th id="titulo">&nbsp;</th>
		<th id="titulo">Nombre</th>
		<th id="titulo">&nbsp;</th>
		<th id="titulo">JJ</th>
		<th id="titulo">JG</th>
		<th id="titulo">JE</th>
		<th id="titulo">JP</th>
		<th id="titulo">&nbsp;</th>
		<th id="titulo">GF</th>
		<th id="titulo">GC</th>
		<th id="titulo">&nbsp;</th>
		<th id="titulo">PTS</th>
		<th id="titulo">DIF</th>
	</tr>
<?=$CONTENIDO?>
</table>
	<form name="frmFiltro" action="<?=$RUTA_RAIZ?>torneos/<?=$SCRIPT?>/<?=$ID_TEMPORADA?>/<?=$ID_TORNEO?>" method="post">
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
			<tr class="even">
				<td colspan="4"><input type="button" class="button" onclick="JavaScript: ValidaForma();" value="Revisar Filtrado" />
			</tr>
		</table>
		<input type="hidden" name="ssn" value="<?=$ID_TEMPORADA?>" />
		<input type="hidden" name="tor" value="<?=$ID_TORNEO?>" />
		<input type="hidden" name="script" value="<?=$SCRIPT?>" />
	</form>
<script type="text/javascript" language="JavaScript">
	
	function ValidaForma() {
		if (parseInt(document.frmFiltro.slcDesdeJornada.value) > parseInt(document.frmFiltro.slcHastaJornada.value))
			alert ('Conflicto con las jornadas');
		else
			document.frmFiltro.submit();
	}

</script>