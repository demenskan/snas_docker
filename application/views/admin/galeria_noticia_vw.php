<table class="Reportes">
    <thead>
	    <th>Tipo</th>
	    <th style="width: 450px">Fuente</th>
	    <th>Pie de foto</th>
    </thead>
    {BLOQUE_FILA}
    <tr class="{CLASE}">
		<td>
		    <select name="slcTipoEntrada{CONSECUTIVO}" onchange="CambiaFuente({CONSECUTIVO}, this.value);">
			    <option value="Nuevo">Subir archivo</option>
			    <option value="Galeria" selected="selected">Galeria actual</option>
			    <option value="Link">Link externo</option>
		    </select>
	    </td>
	    <td>
		    <span id="fuente-{CONSECUTIVO}"><input type="text" class="text" value="{URL}" name="txtGaleria{CONSECUTIVO}"/><input type="button" class="button" value="Seleccionar de galeria..." onClick="AbreVentana({CONSECUTIVO});" /></span>
	    </td>
	    <td>
		    <input type="text" name="txtCaption{CONSECUTIVO}" value="{CAPTION}" class="text" size="30" />
	    </td>
	</tr>
    {/BLOQUE_FILA}
</table>