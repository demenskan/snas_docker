		<form action="editoriales/agrega_comentario" name="frmAgregaComentario" method="post">
			<table class="tabla_normal" width="100%">
				<tr>
					<td colspan="2" id="titulo">Agregar un comentario</td>
				</tr>
				<tr>
					<td id="non">Usuario: <input type="text" class="text" name="txtUsuario" maxlength="15" /> </td>
					<td id="non">Contrase&ntilde;a: <input type="password" class="text" name="txtPass" maxlength="15" /> </td>
				</tr>
				<tr>
					<td colspan="2" id="par" align="center">
						<textarea name="taComentario" cols="50" rows="10"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2" id="total" align="center">
						<input type="submit" class="button" name="ok" value="Agregar Comentario" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="hdnClaveEditorial" value="{ID}" />
		</form>