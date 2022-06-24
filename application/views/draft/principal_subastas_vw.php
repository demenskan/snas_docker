<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
	<script>
	
	var asCondiciones={ARRAY_CONDICIONES}; //new Array();
	var asJugadoresComparar=new Array();
	
		function CambiaClubes(piCodigoClub) {
			sParam='';
			for (i=0;i<asCondiciones.length;i++) {
				sParam+=asCondiciones[i][0]
					+'__' + asCondiciones[i][1]
					+'__' + asCondiciones[i][2]+'~';
			}
			if (document.getElementById('radio-libres').checked) 
				sScope="L";
			else
				sScope="T";
			if (sParam=='') {
				sParam='ALL'
			}
			if (typeof document.getElementById('numero-pagina') != 'undefined')
				iPagina=document.getElementById('numero-pagina').value;
			else
				iPagina=1;
			sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_subastas/principal/'+piCodigoClub+'/0/'+sParam+'/'+sScope+'/'+iPagina;
			location.href=sURL;
		}
		
		function VerProyecciones(piBase, piTemporadas, nombre) {
			sTabla='<h3>Proyeccion para: '+ nombre + '</h3><table class="Reportes"><thead><th>Temporada</th><th>Precio</th></thead>';
			iFactorCrecimiento=1;
			for (i=0;i<piTemporadas;i++) {
				iPrecio=piBase*iFactorCrecimiento;
				iFactorCrecimiento+=0.3;
				sColor=(i%2==0) ? 'non' : 'par';
				sTabla+='<tr class="'+ sColor + '"><td>' + (i+1) + '</td><td>' + iPrecio.toFixed(3) + '</td></tr>';
			}
			sTabla+='</table>';
			document.getElementById('proyecciones').innerHTML=sTabla;
		}
		
		function AgregaCondicion() {
			//Agrega una condicion de filtro en la memoria
			iConteo=asCondiciones.length;
			asCondiciones[iConteo]=new Array();
			asCondiciones[iConteo][0]=document.getElementById('select-campos').value;
			asCondiciones[iConteo][1]=document.getElementById('select-operadores').value;
			asCondiciones[iConteo][2]=document.getElementById('text-valor').value;
			GeneraTabla();
		}
		
		function GeneraTabla() {
			//Pinta la tabla de condiciones en la pagina
			sSalida='<table class="Reportes">';
			for (i=0;i<asCondiciones.length;i++) {
				sClase=(i%2==0) ? 'non' : 'par';
				sSalida+='<tr class="'+ sClase + '">' 
					+ '<td>' + asCondiciones[i][0] + '</td>'
					+ '<td>' + asCondiciones[i][1] + '</td>'
					+ '<td>' + asCondiciones[i][2] + '</td>'
					+ '<td><input type="button" value=" X " class="button" onClick="Javascript: QuitaCondicion('+ [i] + ');" /></td>'
					+ '</tr>';
			}
			sSalida+='</table>';
			document.getElementById('tabla-condiciones').innerHTML=sSalida;
		}
		
		function QuitaCondicion(piPosicion) {
			//Remueve un elemento de la lista de condiciones y regenera la tabla visual
			asCondiciones.splice(piPosicion,1);
			GeneraTabla();
		}
		
		function Buscar() {
			if (document.getElementById('combo-clubes').value!="__EXTRA__") {
				sParam='';
				for (i=0;i<asCondiciones.length;i++) {
					sParam+=asCondiciones[i][0]
						+'__' + asCondiciones[i][1]
						+'__' + asCondiciones[i][2]+'~';
				}
				iClub=document.getElementById('clave-club').value;
				if (document.getElementById('radio-libres').checked) 
					sScope="L";
				else
					sScope="T";
				if (sParam=='') {
					sParam='ALL'
				}
				sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_subastas/xi_busqueda/'+iClub+'/'+sParam+'/'+sScope;
				console.log=sURL;
				callPage(sURL,'tabla-resultados','Cargando...','Error@' + sURL);
				document.getElementById('tabla-informacion').innerHTML='';
			}
			else{
				alert ('Primero seleccione el club con el que va a ofertar');
			}
		}
		
		function verHabilidades(piCodigo, piOrden) {
			//var x=window.open('{RUTA_RAIZ}{INDEX_URI}admin/draft_franquicias/xi_habilidades/'+piCodigo);
			asJugadoresComparar[piOrden]=piCodigo;
			sParams='';
			for (i=1;i<=asJugadoresComparar.length;i++) {
				sParams+='/'+ asJugadoresComparar[i];
			}
			sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_subastas/xi_habilidades/'+sParams;
			//callPage(sURL,'tabla-informacion','Cargando...','Error@' + sURL);
			document.getElementById('tabla-graficas').innerHTML='<img src="'+ sURL + '" />';
		}

		function verHistorial(piCodigo) {
			//var x=window.open('{RUTA_RAIZ}{INDEX_URI}admin/draft_franquicias/xi_habilidades/'+piCodigo);
			sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_subastas/xi_historial_ofertas/'+piCodigo;
			callPage(sURL,'tabla-informacion','Cargando...','Error@' + sURL);
		}
		
		function Ofertar(piClave, psQuery, piPagina) {
			if (document.getElementById('radio-libres').checked) 
				sScope="L";
			else
				sScope="T";
			location.href='{RUTA_RAIZ}{INDEX_URI}admin/draft_subastas/captura_oferta/'+piClave+'/'+document.getElementById('clave-club').value+'/'+psQuery+'/'+sScope+'/'+piPagina;
		}
		
		function Evalua() {
			if (parseInt(document.getElementById('franquicias-seleccionados').innerHTML)>3) {
				sSalida='<div class="error">Te has pasado el limite de franquicias</div>';
				document.getElementById('mensaje').innerHTML=sSalida;
			}
			else {
				if (parseInt(document.getElementById('bases-seleccionados').innerHTML)>5) {
					sSalida='<div class="error">Te has pasado el limite de bases</div>';
					document.getElementById('mensaje').innerHTML=sSalida;
				}
				else {
					//document.getElementById('costo-'+psCodigo).innerHTML=piValor+'';
					sParametro='';
					asCodigos=sCadenaCodigos.split('|');
					for (i=0;i<asCodigos.length;i++) {
						sParametro+=asCodigos[i]+'-'
								+document.getElementById('tipo-contrato-'+asCodigos[i]).value+'-'
								+document.getElementById('text-temporadas-'+asCodigos[i]).value+'-'
								+document.getElementById('precio-base-'+asCodigos[i]).innerHTML+'_';	
					}
					iClub=document.getElementById('id-club').value
					sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_franquicias/guardaCambios/{TEMPORADA}/'+iClub+'/'+sParametro;
					callPage(sURL,'mensaje','Procesando...','Error @' + sURL);
				/*	sCadenaCodigos=document.getElementById('lista-jugadores').value;
					document.getElementById('tipo-contrato-'+ psCodigo).value=psTipoContrato;
					
					fTotal=0;
					iFranquiciasSeleccionados=0;
					iBasesSeleccionados=0;
					for (i=0;i<asCodigos.length;i++) {
						
					
						fTotal+=parseFloat(document.getElementById('costo-'+asCodigos[i]).innerHTML);
						switch (document.getElementById('tipo-contrato-'+ asCodigos[i]).value) {
							case 'F': iFranquiciasSeleccionados++;
									break;
							case 'B': iBasesSeleccionados++;
									break;
						}
					}*/
				}
			}
		}

	</script>
		<table width="100%" border="0" class="Reportes">
			<tr>
				<td colspan="3"><h2>SUBASTA DE AGENTES LIBRES</h2></td>
			</tr>
			<tr>
				<td colspan="3"><b>INSTRUCCIONES:</b>
					Seleccionar el club que va a hacer la oferta. Luego hacer b&uacute;squedas segun los criterios deseados.
					En caso de encontrar algun agente que cumpla con los requisitos, se puede hacer click en la liga 'hacer oferta'. El jugador
					quedara automaticamente contratado al pasar 24 horas de la mejor oferta.
				</td>
			</tr>
			<tr>	
				<td colspan="3">Club:
					{COMBO_CLUBES}
				</td>
			</tr>
			<tr>
				<td style="vertical-align: top">
					<table class="" width="100%">
						<tr>
							<td rowspan="8"><img src="img/escudos/mini/	s{LOGO_CLUB}.gif" /></td>
						</tr>
						<tr>
							<td>Asignado:</td>
							<td>{PRESUPUESTO_ASIGNADO}</td>
						</tr>
						<tr>
							<td>Gastado:</td>
							<td>{PRESUPUESTO_GASTADO}</td>
						</tr>
						<tr>
							<td>Disponible:</td>
							<td>{PRESUPUESTO_DISPONIBLE}</td>
						</tr>
						<tr>
							<td>Prometido:</td>
							<td>{PRESUPUESTO_PROMETIDO}</td>
						</tr>
						<tr>
							<td>Libre:</td>
							<td>{PRESUPUESTO_LIBRE}</td>
						</tr>						
						<tr>
							<td>Jugadores contratados:</td>
							<td>{CONTADOR_CONTRATADOS}</td>
						</tr>						
						<tr>
							<td>Ofertas activas:</td>
							<td>{CONTADOR_OFERTAS_ACTIVAS}</td>
						</tr>						
					</table>
					<table class="Reportes">
						<thead>
							<th>Codigo</th>
							<th>Nombre</th>
							<th>Sueldo base</th>
							<th>T. Restantes</th>
							<th>Estatus</th>
						</thead>
					{BLOQUE_ROSTER}
						<tr class="{CLASE}">
							<td>{CODIGO}</td>	
							<td>{NOMBRE}</td>	
							<td>{SUELDO_BASE}</td>	
							<td>{TEMPORADAS_RESTANTES}</td>	
							<td>{ESTATUS}</td>	
						</tr>
					{/BLOQUE_ROSTER}
					</table>
					<input type="button" name="btnVerTodos" class="button" value="Ver todas las ofertas" onclick="location.href='admin/draft_subastas/principal/{ID_CLUB}/1'" />
					<div id="roster-club"></div>
				</td>
				<td style="vertical-align: top">
					<form name="frmCondiciones">
						<table class="Reportes">
							<tr>
								<td colspan="3">
									Buscar prospectos:
									<input type="button" class="button" value="Agregar condicion" onclick="JavaScript: AgregaCondicion();" />
									<input type="button" class="button" value="Buscar" onclick="JavaScript: Buscar();" />
								</td>
							</tr>
							<tr>
								<td>{COMBO_CAMPOS}</td>
								<td>
									<select name="slcOperadores" id="select-operadores">
										<option value="IGUAL">=</option>
										<option value="MAYOR">&gt;</option>
										<option value="MENOR">&lt;</option>
										<option value="MAYOR_IGUAL">&gt;=</option>
										<option value="MENOR_IGUAL">&lt;=</option>
										<option value="DIFERENTE">&lt;&gt;</option>
										<option value="PARECIDO_A">se parece a</option>
										<option value="ENTRE">entre</option>
									</select>
								</td>
								<td><input type="text" id="text-valor" name="txtValorCondicion" class="text" /></td>
							</tr>
							<tr>
								<td>
									<input type="radio" id="radio-libres" name="rbEstatusContrato" value="L" {LIBRE_CHECADO}  />Agentes Libres
									<input type="radio" id="radio-todos" name="rbEstatusContrato" value="T" {TODOS_CHECADO} />Todos
								</td>
							</tr>
							<tr><td><div id="tabla-condiciones">{TABLA_CONDICIONES}</div></td></tr>
						</table>
					</form>
					<div id="tabla-graficas"></div>
					<div id="tabla-resultados">{TABLA_RESULTADOS}</div>
				</td>
				<td style="vertical-align: top">
					<div id="tabla-informacion"></div>
				</td>
			</tr>
		</table>
		<input type="hidden" name="hdnIdClub" id="clave-club" value="{ID_CLUB}" />
	</form>