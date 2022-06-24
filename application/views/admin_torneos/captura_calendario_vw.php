<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/ajax_tabs.js" language="JavaScript"></script>
<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/jquery-1.4.1.js" language="JavaScript"></script>
<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/jquery.autocomplete.min.js" language="JavaScript"></script>
<link rel="stylesheet" type="text/css" href="<?=$RUTA_RAIZ?>externos/js/jquery.autocomplete.css" />
<script type="text/javascript" language="JavaScript">
		var Lista_equipos=[
				<?=$LISTA_EQUIPOS?>
		];

		$().ready(function() {

				function findValueCallback(event, data, formatted) {
					$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
				}
			
				function formatItem(row) {
					return row[0] + " (<strong>id: " + row[1] + "</strong>)";
				}
				function formatResult(row) {
					return row[0].replace(/(<.+?>)/gi, '');
				}
			
				$("#id-equipo-local").autocomplete(Lista_equipos, {
					matchContains: true,
					minChars: 0
				});
				
				$("#id-equipo-visitante").autocomplete(Lista_equipos, {
					matchContains: true,
					minChars: 0
				});
		});
		
		function AgregaPartido () {
			sURL='<?=$RUTA_RAIZ?>admin/torneos/agrega_partido/'+document.frmPartidos.hdnTemporada.value+'/'
			+document.frmPartidos.hdnTorneo.value+'/'+document.frmPartidos.txtJornada.value+'/'
			+document.frmPartidos.txtEquipoLocal.value+'/'+document.frmPartidos.txtEquipoVisitante.value+'/'
			+document.frmPartidos.slcTipo.value;
			callPage(sURL,'lista-partidos-capturados','Cargando...','Error '+ sURL);
			document.frmPartidos.txtEquipoLocal.value='';
			document.frmPartidos.txtEquipoVisitante.value='';
			document.frmPartidos.txtEquipoLocal.focus();
		}
</script>
<form name="frmPartidos">
	<table class="data-table">
		<tr>
			<th colspan="5" align="center"><h3>Captura de Partidos</h3></th>
		<tr>
			<th>Local</th>
			<th>Visitante</th>
			<th>Jornada</th>
			<th>Tipo</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td><input type="text" class="text" name="txtEquipoLocal" autocomplete="off" id="id-equipo-local" size="15" /></td>
			<td><input type="text" class="text" name="txtEquipoVisitante" autocomplete="off" id="id-equipo-visitante" size="15" /></td>
			<td><input type="text" class="text" name="txtJornada" autocomplete="off" size="2" /></td>
			<td><?=$LISTA_TIPOS_PARTIDOS?></td>
			<td><input type="button" class="button" value="Agregar" onclick="Javascript: AgregaPartido()"></td>
		</tr>
	</table>
	<div id="lista-partidos-capturados">	
		<?=$TABLA_PARTIDOS?>
	</div>
	<input type="hidden" name="hdnTemporada" value="<?=$TEMPORADA?>" />
	<input type="hidden" name="hdnTorneo" value="<?=$TORNEO?>" />
</form>
