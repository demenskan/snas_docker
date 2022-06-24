	<div id="Page_header">
		<table class="tabla_normal" width="100%" cellspacing="10">
			<tr>
				<td>{IMAGEN}</td>
				<td>
					<h2>{COLUMNA}</h2></br>
					<h4>{AUTOR}</h4>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top" align="justify">
					<h3>{TITULO}</h3>
					{FECHA}<br />
					{CUERPO}
				</td>
			</tr>
		</table>
	</div>
	<div id="Page_top">
		<p><h3>Comentarios</h3><br/>
			{BLOQUE_COMENTARIOS}
			<div class="notice">
				<h6>{OPERADOR} - {FECHA}</h6></br>
				<p>{COMENTARIO}</p>
			</div>
			{/BLOQUE_COMENTARIOS}
		</p>
		
	</div>
