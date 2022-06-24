<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
<script>
	function Roster(piClaveClub) {
		sUrl='{RUTA_RAIZ}{INDEX_URI}admin/draft_reportes/preliminarequipo/'+piClaveClub;
		callPage(sUrl,'jugadores','loading...', 'Error '+ sUrl);
	}

</script>
<style>
	.pos_P {
		background-color: #FFAD40;
		color: #FFFFFF;
	}
	
	.pos_D {
		background-color: #4EA429;
		color: #FFFFFF;
	}
	
	.pos_M {
		background-color: #5CCDC9;
		color: #FFFFFF;
	}

	.pos_A {
		background-color: #FF5D40;
		color: #FFFFFF;
	}
		
</style>
<p>
	<h2>Equipos con el draft</h2>
	Asi es como van las listas despues de los movimientos aceptados del draft
</p>
	<table class="Reportes">
		<tr>
			<td>
				<select name="slcClub" size="20" onclick="JavaScript: Roster(this.value);">
{BLOQUE_EQUIPOS}
					<option value="{ID_CLUB}">{NOMBRE_CLUB}</option>
{/BLOQUE_EQUIPOS}
				</select>
			</td>
			<td>
				<div id="jugadores"></div>
			</td>
		</tr>
	</table>
</form>