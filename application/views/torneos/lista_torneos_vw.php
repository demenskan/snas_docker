		<style>
			.img-with-text {
				/*display: inline;*/
				text-align: justify;
				width: 100px;
				margin: 5px 40px 5px 40px;
			}
	
			.img-with-text img {
				display: block;
				margin: 0 auto;
			}
		</style>
	
		<form action="torneos/lista" method="post" >
			<table class="data-table">
				<tr>
					<th colspan="2"><h2>Lista de torneos</h2></th>
				</tr>
				<tr>
					<td>Temporada:</td>
					<td>{LISTA_TEMPORADAS}</td>
				</tr>
				<tr class="even">
					<td colspan="2"><input type="submit" class="button" value="Seleccionar" /></td>
				</tr>
			</table>
			<input type="hidden" name="code" value="lista" />
		</form>
		{BLOQUE_TORNEOS}
			{SEPARADOR_TEMPORADAS}
			{CLASE}
			<div style="display:inline-block">
				<div class="img-with-text">
					<a href="torneos/ver/{TEMPORADA}/{CLAVE}">
						<img src="{RUTA_LOGO}" height="50" border="0" title="{NOMBRE}"/>
						<p>{NOMBRE}</p>
					</a>
				</div>
			</div>
		{/BLOQUE_TORNEOS}
