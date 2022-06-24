<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
	<script>
		function CambiaClubes(piCodigoClub) {
			sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_franquicias/lista_club/'+piCodigoClub;
			callPage(sURL,'tabla-jugadores','Cargando...','Error@' + sURL);
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
		
		function Calcula(psCodigo, piValor, psTipoContrato) {
			document.getElementById('costo-'+psCodigo).innerHTML=piValor+'';
			sCadenaCodigos=document.getElementById('lista-jugadores').value;
			document.getElementById('tipo-contrato-'+ psCodigo).value=psTipoContrato;
			asCodigos=sCadenaCodigos.split('|');
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
			}
			document.getElementById('total-temporada').innerHTML=fTotal;
			document.getElementById('franquicias-seleccionados').innerHTML=iFranquiciasSeleccionados;
			document.getElementById('bases-seleccionados').innerHTML=iBasesSeleccionados;
		}
		
		function verHabilidades(piCodigo) {
			//var x=window.open('{RUTA_RAIZ}{INDEX_URI}admin/draft_franquicias/xi_habilidades/'+piCodigo);
			sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_franquicias/xi_habilidades/'+piCodigo;
			callPage(sURL,'proyecciones','Cargando...','Error@' + sURL);

		}

		function CancelaContrato(psCodigo) {
			sURL='admin/draft_franquicias/cancela_contrato/'+psCodigo;
			alert (sURL);
			location.href=sURL;
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
					if (parseFloat(document.getElementById('total-temporada').innerHTML)>parseFloat(document.getElementById('presupuesto-club').value)) {
						sSalida='<div class="error">No rinde el presupuesto</div>';
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
									+document.getElementById('costo-'+asCodigos[i]).innerHTML+'_';	
						}
						iClub=document.getElementById('id-club').value
						sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_franquicias/guardaCambios/{TEMPORADA}/'+iClub+'/'+sParametro;
						console.log (sURL);
						callPage(sURL,'tabla-jugadores','Procesando...','Error @' + sURL);
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
		}

	</script>
		<table width="100%" border="0" class="Reportes">
			<tr>
				<td colspan="2"><h2>DESIGNACION DE JUGADORES FRANQUICIA Y BASES</h2></td>
			</tr>
			<tr>
				<td colspan="2"><b>INSTRUCCIONES:</b>
					De la lista de jugadores que tiene cada club, designe los que van a ser franquicia (a cada uno se le pagara con el 15%
					del presupuesto de la temporada del club) y 5 bases (cuyo precio base esta fijo, solo hay que seleccionar la duracion del contrato).
					Aquellos que se dejen en estatus de "libre", tendran el estatus de AGENTE LIBRE automaticamente al comenzar las subastas.
				</td>
			</tr>
			<tr>	
				<td colspan="2">Club:
					{COMBO_CLUBES}
				</td>
			</tr>
			<tr>
				<td>
					<div id="tabla-jugadores"></div>
				</td>
				<td>
					<div id="proyecciones"></div>
				</td>
			</tr>
		</table>