				<script src="externos/js/jquery-1.7.2.min.js"></script>
				<link rel="stylesheet" href="css/folder-tabs.css" type="text/css" />
				<script language="JavaScript">
				//Tomado de http://www.red-team-design.com/css3-jquery-folder-tabs
				$(document).ready(function() {
						$("#content div").hide(); // Initially hide all content
						$("#tabs li:first").attr("id","current"); // Activate first tab
						$("#content div:first").fadeIn(); // Show first tab content
						
						$('#tabs a').click(function(e) {
							e.preventDefault();
							if ($(this).closest("li").attr("id") == "current"){ //detection for current tab
							 return       
							}
							else{             
							$("#content div").hide(); //Hide all content
							$("#tabs li").attr("id",""); //Reset id's
							$(this).parent().attr("id","current"); // Activate this
							$('#' + $(this).attr('name')).fadeIn(); // Show content for current tab
							}
						});
					});
				</script>
				<div id="Page_header">
					<table class="tabla_normal" width="100%" cellspacing="10">
						<tr>
							<td>
								<h2>{TITULO}</h2></br>
								<h4>{SUBTITULO}</h4>
							</td>
						</tr>
						<tr>
							<td valign="top" align="justify">
									<ul id="tabs">
									{BLOQUE_TABS}
										<li><a href="#" name="tab{CONSECUTIVO}">[ {CONSECUTIVO} ]</a></li>
									{/BLOQUE_TABS}
									</ul>
									<div id="content">
									{BLOQUE_CONTENIDOS}
										<div id="tab{CONSECUTIVO}" >
											<img width="520" height="280" src="{URL}" />
											<p>{LEYENDA}</p>
										</div>
									{/BLOQUE_CONTENIDOS}
									</div>
								{FECHA}<br />
								{CUERPO}
							</td>
						</tr>
						<tr>
							<td>
								<p><h3>Comentarios</h3><br/>
								{BLOQUE_COMENTARIOS}
								<div class="notice">
									<h6>{OPERADOR} - {FECHA_HORA}</h6><br />
									<p>{COMENTARIO}</p>
								</div>
									<?=$COMENTARIO?>
								</p>
								{/BLOQUE_COMENTARIOS}
							</td>
						</tr>
						<tr>
							<td>
								<form action="generador.php" name="frmAgregaComentario" method="post">
									<table class="tabla_normal" width="100%">
										<tr>
											<td colspan="2" id="titulo">Agregar un comentario</td>
										</tr>
										<tr>
											<td id="non">Usuario: <input type="text" class="text" name="txtUsuario" maxlength="15" size="10" /> </td>
											<td id="non">Contrase&ntilde;a: <input type="password" class="text" name="txtPass" maxlength="15" size="10" /> </td>
										</tr>
										<tr>
											<td colspan="2" id="par" align="center">
												<textarea name="taComentario" cols="30" rows="5"></textarea>
											</td>
										</tr>
										<tr>
											<td colspan="2" id="total" align="center">
												<input type="submit" class="button" name="ok" value="Agregar Comentario" />
											</td>
										</tr>
									</table>
									<input type="hidden" name="code" value="010A0098" />
									<input type="hidden" name="hdnClaveNoticia" value=<?=$CLAVE_NOTICIA?> />
								</form>
							</td>
						</tr>
					</table>
				</div>
				<div id="Page_top">
				</div>
