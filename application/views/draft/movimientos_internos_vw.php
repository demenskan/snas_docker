<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
	<script language="JavaScript" type="text/javascript">
	<!--
		var aiClaveJugadores= new Array(20);
		
		function ProcesoCambio() {
			var iEquipoOrigen=document.frmMI.slcListaEquiposOrigen.value;
			var iCont=0;
			var sParametro="";
			var iEquipoDestino=document.frmMI.slcListaEquiposDestino.value;
			if (iEquipoOrigen!=iEquipoDestino) {
				for(i=0; i<document.frmMI.elements.length; i++) {
					sControl=document.frmMI.elements[i].name;
					sPrefijo=sControl.substr(0,3);
					if ((sPrefijo=="chk") && (document.frmMI.elements[i].checked==true)) {
							aiClaveJugadores[iCont]=sControl.substr(6);
							iCont++;
					}	
				}
				for (i=0;i<iCont;i++) {
					sParametro+=aiClaveJugadores[i]+"-"; 
				}
				sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_movimientos/xi_realiza_movimientos/'+iEquipoOrigen+'/'+iEquipoDestino+'/'+sParametro;
				callPage(sURL,'ResultadoOperacion','Cargando...','Error: '+sURL);
				document.getElementById('ListaJugadoresOrigen').innerHTML="Ok";
				document.getElementById('ListaJugadoresDestino').innerHTML="Ok";
				/*while (req.readyState!=4)
					a=1;
				callPage('{RUTA_RAIZ}{INDEX_URI}admin/draft_movimientos/xi_lista/origen/'+iEquipoOrigen,'ListaJugadoresOrigen','Cargando...'+iEquipoOrigen,'Error@Carga');
				while (req.readyState!=4)
					a=1;
				callPage('{RUTA_RAIZ}{INDEX_URI}admin/draft_movimientos/xi_lista/destino/'+iEquipoDestino,'ListaJugadoresDestino','Cargando...'+iEquipoDestino,'Error@Carga');*/
			}
			else
				alert ('No se pueden seleccionar dos veces el mismo equipo');
		}

		function MuestraOrigen (iClaveEquipo) {
			callPage('{RUTA_RAIZ}{INDEX_URI}admin/draft_movimientos/xi_lista/origen/'+iClaveEquipo,'ListaJugadoresOrigen','Cargando...'+iClaveEquipo,'Error@Carga');
		}

		function MuestraDestino (iClaveEquipo) {
			callPage('{RUTA_RAIZ}{INDEX_URI}admin/draft_movimientos/xi_lista/destino/'+iClaveEquipo,'ListaJugadoresDestino','Cargando...'+iClaveEquipo,'Error@Carga');
		}

		function ActualizaListas() {
				MuestraDestino(iEquipoDestino);
				for (i=0;i<32000;i++) a=1;
				MuestraOrigen(iEquipoOrigen);
		}

	//-->
	</script>
	<style>
		.pos_P {
			background-color: #FFAD40;
			color: #FFFFFF;
		}
		
		.pos_D {
			background-color: #4EA429;
			color: #FFFFFF;
		}
		
		.pos_M {
			background-color: #5CCDC9;
			color: #FFFFFF;
		}
	
		.pos_A {
			background-color: #FF5D40;
			color: #FFFFFF;
		}
	</style>

	<form name="frmMI">
		<table width="100%" border="0">
			<tr>
				<td colspan="4"><h2>MOVIMIENTOS INTERNOS</h2></td>
			</tr>
			<tr>
				<td colspan="4"><b>INSTRUCCIONES:</b> 
					Seleccione primero el equipo origen del movimiento, luego seleccione el(los) jugador(es)
					que considere cambiar. Seleccione a que equipo quiere que se vaya(n) y presione el boton
					'enviar'. El cambio se efectuara de manera inmediata.
				</td>
			</tr>
			<tr>
				<td id="titulo" colspan="2">Equipo Origen</td>
				<td id="titulo" colspan="2">Equipo Destino</td>
			</tr>
			<tr>
				<td valign="top" width="10%" height="500">
					<img src="img/pixel.gif" width="1" height="80" />			
					<img src="img/pixel.gif" width="10" height="1" />
					{ORIGEN}
				</td>
				<td valign="top" width="40%">
					<img src="img/pixel.gif" width="25" height="1" />
					<div id="ListaJugadoresOrigen"></div>
				</td>
				<td valign="top" width="10%">
					<img src="img/pixel.gif" width="10" height="1" />
					{DESTINO}
				</td>
				<td valign="top" width="40%">
					<img src="img/pixel.gif" width="25" height="1" />			
					<div id="ListaJugadoresDestino"></div>
				</td>
				<td>
					<input type="button" class="button"  value="Hacer cambio" onclick="ProcesoCambio();"  /><br />
					Resultado de la operacion:
					<div id="ResultadoOperacion"></div>
				</td>
			</tr>
		</table>
		<input type="hidden" name="code" value="550004CC" />
		<input type="hidden" name="hdnConsejoOrigen" value="" />
		<input type="hidden" name="hdnConsejoDestino"  />
	</form>
