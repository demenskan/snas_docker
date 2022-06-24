<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
	<script language="JavaScript" type="text/javascript">
	<!--
		function ChangeColorOf (iNumero) {
			if (document.formDisponibles["id"+iNumero].checked==true) {
				//document.getElementById('a'+iNumero).className='celda_seleccionada';
				document.getElementById('b'+iNumero).className='celda_seleccionada';
			}
			else {
				//document.getElementById('a'+iNumero).className='celda_normal';
				document.getElementById('b'+iNumero).className='celda_normal';
			}
		}
		
		function Actualiza() {
			var sParametro='', iClaveEquipo, iTemporada;
			for(i=0;i<document.formDisponibles.elements.length;i++){
				if(document.formDisponibles.elements[i].type=="checkbox") {
					if (document.formDisponibles.elements[i].checked==true)
						sParametro+=document.formDisponibles.elements[i].name + '_';
				}
			}
			iClaveEquipo=document.formDisponibles.hdnClaveEquipo.value;
			iTemporada=document.formDisponibles.hdnTemporada.value;
			sUrl='{RUTA_RAIZ}{INDEX_URI}admin/draft_propuestas/xi_actualiza_disponibles/'+iTemporada+'/'+iClaveEquipo+'/'+sParametro;
			callPage(sUrl,'ListaJugadores','Cargando...',sUrl);
		}
		
		function Listado(piClub) {
			sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_propuestas/xi_roster/'+piClub;
			callPage(sURL,'ListaJugadores','Cargando...',sURL);
		}
		
	//-->
	</script>
	<table width="100%">
		<tr>
			<td colspan="2"><h2>SELECCION DE JUGADORES DISPONIBLES</h2></td>
		</tr>
		<tr>
			<td colspan="2"><b>INSTRUCCIONES:</b> Seleccione los jugadores que considere que estan disponibles para intercambiar</td>
		</tr>
		<tr>
			<td valign="top">
				{COMBO_CLUBES}				
			</td>
			<td valign="top">
				<div id="ListaJugadores"></div>
			</td>
		</tr>
	</table>