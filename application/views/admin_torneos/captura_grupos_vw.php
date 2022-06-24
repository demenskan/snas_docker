<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/ajax_tabs.js" language="JavaScript"></script>
<script type="text/javascript">
		function manda() {
				sLink='<?=$RUTA_RAIZ?>admin/torneos/agrega_club_grupo/'
					+document.frmAcomodoGrupos.hdnTemporada.value+'/'
					+document.frmAcomodoGrupos.hdnTorneo.value+'/'
					+document.frmAcomodoGrupos.slcEquipo.value+'/'
					+document.frmAcomodoGrupos.slcGrupos.value;
				callPage (sLink,'lista-equipos-capturados','Cargando...','Error@Carga: '+sLink);
				document.frmAcomodoGrupos.slcEquipo.focus();
		}

</script>
<form name="frmAcomodoGrupos">
		<fieldset>
			<legend><h2>Captura de grupos</h2></legend>		
			<table class="data-table">
				<tr>
					<th>Equipo</th>
					<th>Grupo</th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<td><?=$LISTA_EQUIPOS?></td>
					<td>
						<select name="slcGrupos">
							<option value="A">A</option>
							<option value="B">B</option>
							<option value="C">C</option>
							<option value="D">D</option>
							<option value="E">E</option>
							<option value="F">F</option>
							<option value="G">G</option>
							<option value="H">H</option>
							<option value="I">I</option>
							<option value="J">J</option>
							<option value="K">K</option>
							<option value="ZZ">Unico</option>
						</select>	
					</td>
					<td><input type="button" value="Agregar" class="button" onclick="JavaScript: manda();"></td>
				</tr>
			</table>
			<div id="lista-equipos-capturados">	
				<table class="data-table" width="50%">	
					<?=$TABLA_GRUPOS?>
				</table>
			</div>
		</fieldset>
		<input type="hidden" name="hdnTemporada" value=<?=$TEMPORADA?> />
		<input type="hidden" name="hdnTorneo" value=<?=$TORNEO?> />
</form>