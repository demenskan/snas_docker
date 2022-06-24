					<p>
						<form action="<?=$RUTA_RAIZ?>login/verifica" method="post">
							<fieldset>
								<legend>Entrada</legend>
								<table>
									<tr>
										<td><label>Usuario:</label></td>
										<td><input type="text" class="text" name="txtUser" /></td>
									</tr>
									<tr>
										<td><label>Contrase&ntilde;a:</label></td>
										<td><input type="password" class="text" name="txtPWD" /></td>
									</tr>
									<tr>
										<td colspan="2"><input type="submit" class="button" value="Entrar" /></td>
									</tr>
								</table>
							</fieldset>
							<input type="hidden" name="code" value="verifica" /> 
						</form>
					</p>
