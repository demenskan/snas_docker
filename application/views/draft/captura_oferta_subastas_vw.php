<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
	<script>
		function MandaForma() {
			fOferta=parseFloat(document.frmGeneraOferta.txtOferta.value);
			fMinima=parseFloat(document.getElementById('oferta-minima').innerHTML);
			if (fOferta>=fMinima) {
				if (parseInt(document.frmGeneraOferta.txtDuracion.value) > 0) 
					document.frmGeneraOferta.submit();
				else
					alert('Debe poner un valor numerico en la duracion');
			}
			else
				alert('Debe ofertar mas que el minimo');
		}	
	
	</script>
	<form action="{RUTA_RAIZ}{INDEX_URI}admin/draft_subastas/procesa_oferta" method="post" name="frmGeneraOferta">
		<table width="100%" border="0">
			<tr>
				<td>
					<table width="100%" border="0">
						<tr>
							<td colspan="4"><h2>OFERTA DE SUBASTAS</h2></td>
						</tr>
						<tr>
							<td colspan="4"><b>INSTRUCCIONES:</b> 
							</td>
						</tr>
						<tr>
							<td valign="top">Jugador:</td>
							<td valign="top">{NOMBRE_JUGADOR}</td>
						</tr>
						<tr>
							<td valign="top">Club:</td>
							<td valign="top">{NOMBRE_CLUB}</td>
						</tr>
						<tr>
							<td valign="top">Oferta minima (KiloCacaos):</td>	
							<td valign="top"><div id="oferta-minima">{OFERTA_MINIMA}</div></td>
						</tr>
						<tr>
							<td valign="top">Sueldo base ofrecido:</td>
							<td valign="top"><input type="text" class="text" name="txtOferta" size="5" value="{OFERTA_MINIMA}" />&nbsp;Kilocacaos</td>
						</tr>
						<tr>
							<td valign="top">Duracion (Temporadas):</td>
							<td valign="top"><input type="text" class="text" name="txtDuracion" value="1" /></td>
						</tr>
						<tr>
							<td valign="top"><input type="button" class="button" name="smtManda" value="Hacer oferta" onclick="MandaForma();" /></td>
						</tr>
					</table>
				</td>
				<td>
					<h3>Historial de Ofertas</h3>
					<table width="100%">
						<thead>
							<th>Club</th>
							<th>Ofreci&oacute;</th>
							<th>Temporadas</th>
							<th>Fecha</th>
						</thead>
					{BLOQUE_OFERTAS}
						<tr class="{CLASE}">
							<td>{CLUB}</td>
							<td>{PRECIO}</td>
							<td>{TEMPORADAS}</td>
							<td>{FECHA}</td>
						</tr>
					{/BLOQUE_OFERTAS}
					</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="hdnMinima" value="{OFERTA_MINIMA}" />
		<input type="hidden" name="hdnJugador" value="{ID_JUGADOR}" />
		<input type="hidden" name="hdnClub" value="{ID_CLUB}" />
		<input type="hidden" name="hdnQueryAnterior" value="{QUERY_ANTERIOR}" />
		<input type="hidden" name="hdnPaginaQuery" value="{PAGINA_QUERY}" />
		<input type="hidden" name="hdnScope" value="{SCOPE}" />
	</form>