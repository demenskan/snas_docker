	<!--Referencia tabs: http://htmlrockstars.com/blog/using-css-to-create-a-tabbed-content-area-no-js-required/-->
	<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/ajax_tabs.js" language="JavaScript"></script>
	<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/jquery-1.4.1.js" language="JavaScript"></script>
	<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/jquery.autocomplete.min.js" language="JavaScript"></script>
	<!--<script src="externos/js/jquery-1.7.2.min.js"></script>-->
	<link rel="stylesheet" href="css/folder-tabs.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="<?=$RUTA_RAIZ?>externos/js/jquery.autocomplete.css" />	
	<script type="text/javascript" language="JavaScript">
		var asListaEquipos=[
			<?=$EQUIPOS?>
			];
		
		var asListaConsejos=[
			<?=$CONSEJOS?>
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
			
				$("#textarea-equipos").autocomplete(asListaEquipos, {
					multiple: true,
					mustMatch: true,
					autoFill: true
				});
				
				$("#textarea-consejos").autocomplete(asListaConsejos, {
					multiple: true,
					mustMatch: true,
					autoFill: true
				});

				$("#textarea-torneos").autocomplete('<?=$RUTA_RAIZ?>noticias/listatorneos', {
					width: 300,
					multiple: true,
					mustMatch: true,
					formatItem: formatItem,
					extraParams: {
						season: function() {  return (document.frmNoticias.slcTemporada.value); } ,
						search: function() {  return (document.frmNoticias.slcTemporada.value); }
					},
					formatResult: function(row) {
						return row[1];
					}
				});
				$("#textarea-jugadores").autocomplete('<?=$RUTA_RAIZ?>noticias/listajugadores', {
					width: 300,
					multiple: true,
					mustMatch: true, 
					formatItem: formatItem,
					formatResult: function(row) {
						return row[1];
					}
				});
				
				$("#textarea-jugadores").result(function(event, data, formatted) {
					var hidden = $(this).parent().next().find(">:input");
					hidden.val( (hidden.val() ? hidden.val() + ";" : hidden.val()) + data[1]);
					return data[0]; //agregado
				});

		});

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

		function AbreVentana() {
			var x=window.open('<?=$RUTA_RAIZ?>noticias/seleccionaimagen','Select','width=600, height=400, scrollbars=1');
		}
	
		function CambiaFuente(piFila, piValor) {
			switch (piValor) {
				case 'Nuevo':
					sSalida='<input type="file" class="text" name="fImagen'+piFila+'" />';
				break;
				case 'Galeria':
					sSalida='<input type="text" class="text" name="txtGaleria'+piFila+'"/><input type="button" class="button" value="Seleccionar de galeria..." onClick="AbreVentana();" />';
				break;
				case 'Link':
					sSalida='<input type="text" class="text" name="txtLink'+piFila+'"/>';
				break;
			}
			document.getElementById('fuente-'+piFila).innerHTML=sSalida;		
		}
		
		function CambiaCantidadImagenes(piCantidad) {
			sSalida='<table class="Reportes">'
					+'	<thead>'
					+'		<th>Tipo</th>'
					+'		<th style="width: 450px">Fuente</th>'
					+'		<th>Pie de foto</th>'
					+'	</thead>';
					
			for (i=1;i<=piCantidad;i++) {
				/*sClase='even';*/
				sClase=(i%2==0) ? 'odd' : 'even' ;
				sSalida+='	<tr class="'+sClase+'">'
					+'		<td>'
					+'			<select name="slcTipoEntrada'+i+'" onchange="CambiaFuente('+i+', this.value);">'
					+'				<option value="Nuevo" selected="selected">Subir archivo</option>'
					+'				<option value="Galeria">Galeria actual</option>'
					+'				<option value="Link">Link externo</option>'
					+'			</select>'
					+'		</td>'
					+'		<td>'
					+'			<span id="fuente-'+i+'"><input class="text" type="file" name="fImagen'+i+'" /></span>'
					+'		</td>'
					+'		<td>'
					+'			<input type="text" name="txtCaption'+i+'" class="text" size="30" />'
					+'		</td>'
					+'	</tr>'
			}
			sSalida+='</table>';
			document.getElementById('filas-captura').innerHTML=sSalida;
		}
	</script>

	<?=$MENSAJE?>	
	<form action="<?=$RUTA_RAIZ?>noticias/graba" method="post" enctype="multipart/form-data" name="frmNoticias">
		
		<ul id="tabs">
			<li><a href="#" name="tituloTab">Titulos</a></li>
			<li><a href="#" name="imagenTab">Imagenes</a></li>
			<li><a href="#" name="resumenTab">Resumen</a></li>
			<li><a href="#" name="cuerpoTab">Cuerpo</a></li>
			<li><a href="#" name="etiquetasTab">Etiquetas</a></li>
		</ul>
			
		<div id="content">
			<div id="tituloTab">
				<table class="Reportes">
					<tr>
						<td align="right">Titulo:</td>
						<td align="left"><input type="text" class="text" name="txtTitulo" maxlength="50" size="50" value="<?=$TITULO_NOTICIA?>" /></td>
					</tr>				
					<tr>
						<td align="right">Subtitulo:</td>
						<td align="left"><input type="text" class="text" name="txtSubtitulo" maxlength="255" size="50" value="<?=$SUBTITULO_NOTICIA?>" /></td>
					</tr>
					<tr>
						<td align="right">Temporada:</td>
						<td align="left"><?=$COMBO_TEMPORADA?></td>
					</tr>
				</table>
			</div>
			<div id="imagenTab">
				<table class="Reportes">
					<tr>
						<td>Cantidad de imagenes en la noticia:
						<?=$COMBO_NUM_IMAGENES?>
						</td>
					</tr>
				</table>
				<span id="filas-captura">
					<?=$FILAS_GALERIA?>
					<!--<table class="Reportes">
						<thead>
							<th>Tipo</th>
							<th style="width: 450px">Fuente</th>
							<th>Pie de foto</th>
						</thead>
						<tr class="even">
							<td>
								<select name="slcTipoEntrada1" onchange="CambiaFuente(1, this.value);">
									<option value="Nuevo" selected="selected">Subir archivo</option>
									<option value="Galeria">Galeria actual</option>
									<option value="Link">Link externo</option>
								</select>
							</td>
							<td>
								<span id="fuente-1"><input class="text" type="file" name="fImagen1" /></span>
							</td>
							<td>
								<input type="text" name="txtCaption1" class="text" size="30" />
							</td>
						</tr>
					</table>-->
				</span><!--Se usa span porque el div desaparece por los tabs-->
			</div>
			<div id="resumenTab">
				<table class="Reportes">
					<tr>
						<th colspan="2">RESUMEN</th>
					</tr>
					<tr>
						<td align="right">Resumen:</td>
						<td align="left">
							<textarea name="taResumen" cols="70" rows="10"><?=$RESUMEN_NOTICIA?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<div id="cuerpoTab">
				<table class="Reportes">
					<tr>
						<td align="right">Cuerpo de la noticia:</td>
						<td align="left">
							<textarea name="taCuerpo" cols="70" rows="10"><?=$CUERPO_NOTICIA?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<div id="etiquetasTab">
				<table class="Reportes">
					<tr>
						<td colspan="2">Tags Extras:</td>
					</tr>
					<tr>
						<td align="right">Torneo(s):</td>
						<td align="left"><!--<div id="lista-torneos">--><textarea id="textarea-torneos" name="taListaTorneos" rows="2" cols="50" class="text"><?=$LISTA_TORNEOS?></textarea><!--</div>--></td>
					</tr>
					<tr>
						<td align="right">Equipo(s):</td>
						<td align="left"><!--<div id="lista-equipos">--><textarea id="textarea-equipos" name="taListaEquipos" rows="2" cols="50" class="text"><?=$LISTA_EQUIPOS?></textarea><!--</div>--></td>
					</tr>
					<tr>
						<td align="right">Jugador(es):</td>
						<td align="left"><!--<div id="lista-jugadores">--><textarea id="textarea-jugadores" name="taListaJugadores" rows="2" cols="50" class="text"><?=$LISTA_JUGADORES?></textarea><!--</div>--></td>
					</tr>
					<tr>
						<td align="right">Consejo(s):</td>
						<td align="left"><!--<div id="lista-consejos">--><textarea id="textarea-consejos" name="taListaConsejos" rows="1" cols="50" class="text"><?=$LISTA_CONSEJOS?></textarea><!--</div>--></td>
					</tr>
				</table>
			</div>
		</div>
		<table class="Reportes">
			<tr>
				<td colspan="2" align="center"><input type="submit" class="button" value="Agregar noticia" /></td>
			</tr>
		</table>
		<input type="hidden" name="code" value="AA000209" />
		<input type="hidden" name="MAX_FILE_SIZE" value="200000" />
		<input type="hidden" name="modo" value="<?=$MODO_CAPTURA?>" />
		<input type="hidden" name="hdnIdNoticia" value="<?=$ID_NOTICIA?>" />
	</form>
