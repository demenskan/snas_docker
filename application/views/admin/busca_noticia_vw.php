<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/ajax_tabs.js" language="JavaScript"></script>
<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/jquery/jquery-1.4.1.js" language="JavaScript"></script>
<script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/jquery/jquery.autocomplete.min.js" language="JavaScript"></script>
<link rel="stylesheet" type="text/css" href="<?=$RUTA_RAIZ?>externos/jquery/jquery.autocomplete.css" />	

<script type="text/javascript" language="Javascript">
	function AmpliaForma(){
		var sCodigo= '<table class="data-table">'
				+ '	<tr>'
				+ '		<td>Etiqueta(s):</td>'
				+ '		<td><input type="text" name="txtEtiquetas" class="text" id="text-etiquetas" /></td>'
				+ '	</tr>'
				+ '	<tr>'
				+ '		<td colspan="2">Codigos: #=Torneos, $=equipos, %=jugadores, !=consejos<br/>'
				+ '				ejemplo: $Locos, %Negrito, #Champions, !CSN 	</td>'
				+ '	</tr>'
				+ '</table>';
		document.getElementById('marca').innerHTML='Busqueda avanzada [<a href="JavaScript: ReduceForma();">-</a>]';		
		document.getElementById('busqueda-avanzada').innerHTML=sCodigo;
		//document.getElementById('busqueda-avanzada').style="visibility: visible;";
	}

	function ReduceForma() {
		var sCodigo='Busqueda avanzada [<a href="JavaScript: AmpliaForma();">+</a>]';
		document.getElementById('marca').innerHTML='Busqueda avanzada [<a href="JavaScript: AmpliaForma();">+</a>]';		
		document.getElementById('busqueda-avanzada').innerHTML="";
	}

	function Ir_a_pagina(iNumeroPagina) {
		document.frmBuscaNoticias.pg.value=iNumeroPagina;
		document.frmBuscaNoticias.submit();
	}
	
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
		
			$("#text-etiquetas").autocomplete('<?=$RUTA_RAIZ?>noticias/busca_etiqueta', {
				width: 300,
				multiple: true,
				mustMatch: true, 
				formatItem: formatItem,
				formatResult: function(row) {
					return row[1];
				}
			});
			
			$("#text-etiquetas").result(function(event, data, formatted) {
				var hidden = $(this).parent().next().find(">:input");
				hidden.val( (hidden.val() ? hidden.val() + ";" : hidden.val()) + data[1]);
				return data[0]; //agregado
			});

	});

	
</script>
<form name="frmBuscaNoticias" method="post" action="<?=$RUTA_RAIZ?>noticias/busca">
	<table class="Reportes">
		<thead>
			<th colspan="2">BUSQUEDA DE NOTICIAS</th>
		</thead>
		<tr>
			<td>Buscar:</td>
			<td><input type="text" class="text" name="txtSearch" size="40" value="<?=$TEXTO_BUSQUEDA?>"><input type="submit" class="button" value="Buscar" /></td>
		</tr>
		<tr>
			<td colspan="2"><div id="marca">Busqueda avanzada [<a href="JavaScript: AmpliaForma();">+</a>]</div>
				<div id="busqueda-avanzada">
					<table class="Reportes">
						<tr>
							<td>Etiqueta(s):</td>
							<td><div id="lista-etiquetas"><input type="text" name="txtEtiquetas" class="text" id="text-etiquetas" /></div></td>
						</tr>
						<tr>
							<td colspan="2">Codigos: #=Torneos, $=equipos, %=jugadores, !=consejos<br/>
								ejemplo: $Locos, %Negrito, #Champions, !CSN 	</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<table class="tabla_normal">
		<tr>
			<td colspan="5" id="titulo" align="center">Navegacion</td>
		</tr>
		<?=$NAVEGACION?>
	</table>
	<input type="hidden" name="pg" value="1" />
	<input type="hidden" name="modo" value="<?=$MODO?>">
</form>
<?=$RESULTADOS?>
