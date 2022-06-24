	<script>
		function CalculaTotal(piPosicion, piLado) {
			iPresupuestoTotal=parseFloat(document.frmAsignaPresupuesto.hdnPresupuestoConsejo.value);
			iTotal=document.frmAsignaPresupuesto.hdnTotalClubes.value;
			if (piLado==1) {//se cambio un porcentaje
				iCantidadLateral=roundToTwo(parseFloat(document.getElementById('text-porcentaje-' + piPosicion).value) * iPresupuestoTotal / 100);
				document.getElementById('text-cantidad-' + piPosicion).value=iCantidadLateral;
			}
			else {
				iPorcentajeLateral=roundToTwo(parseFloat(document.getElementById('text-cantidad-' + piPosicion).value) * 100 / iPresupuestoTotal);
				document.getElementById('text-porcentaje-' + piPosicion).value=iPorcentajeLateral;
			}
			iPorcentaje=0;
			iCantidad=0;
			for (i=0;i<iTotal;i++) {
				iPorcentaje+=parseFloat(document.getElementById('text-porcentaje-' + i).value);
				iCantidad+=parseFloat(document.getElementById('text-cantidad-' + i).value);
			}
			document.getElementById('label-porcentaje-total').innerHTML=iPorcentaje;
			document.getElementById('label-cantidad-total').innerHTML=iCantidad;
			document.frmAsignaPresupuesto.hdnPorcentajeAsignado.value=iPorcentaje;
			document.frmAsignaPresupuesto.hdnCantidadAsignada.value=iCantidad;
		}
		
		function roundToTwo(num) {
			/*http://stackoverflow.com/questions/11832914/round-to-at-most-2-decimal-places-in-javascript*/
		    return +(Math.round(num + "e+2")  + "e-2");
		}
		
		function Valida() {
			iLimiteMaximoPorClub={LIMITE_MAXIMO_POR_CLUB};
			iLimiteMinimoPorClub={LIMITE_MINIMO_POR_CLUB};
			sMensajeError="";
			if ((document.getElementById('label-porcentaje-total').innerHTML=='NaN') || (document.getElementById('label-cantidad-total').innerHTML=='NaN'))
				alert ('No tiene cantidades numericas validas');
			else {
				iTotalClubes=document.frmAsignaPresupuesto.hdnTotalClubes.value;
				bLimiteValido=true;
				for (i=0;i<iTotalClubes;i++) {
					if (parseFloat(document.getElementById('text-porcentaje-' + i).value)>iLimiteMaximoPorClub)
						sMensajeError+="El presupuesto de "+ document.getElementById('club-' + i).innerHTML + ' no debe pasar del '+ iLimiteMaximoPorClub +'%\n';
					if (parseFloat(document.getElementById('text-porcentaje-' + i).value)<iLimiteMinimoPorClub)
						sMensajeError+="El presupuesto de "+ document.getElementById('club-' + i).innerHTML + ' no debe ser menor al ' + iLimiteMinimoPorClub  + '%\n';
					if (parseFloat(document.getElementById('text-cantidad-' + i).value)<parseFloat(document.getElementById('minimo-' + i).innerHTML))
						sMensajeError+="El presupuesto de "+ document.getElementById('club-' + i).innerHTML + ' no debe ser menor de ' + document.getElementById('minimo-' + i).innerHTML + '\n';
				}
				if (sMensajeError!='')
					alert (sMensajeError);
				else
					if (parseFloat(document.getElementById('label-porcentaje-total').innerHTML) > 100)
						alert ('Esta intentando asignar mas presupuesto del que tiene');
					else
						document.frmAsignaPresupuesto.submit();
			}
		}
	</script>
	<form action="{RUTA_RAIZ}{INDEX_URI}admin/draft_presupuestos/procesa_asignacion" method="post" name="frmAsignaPresupuesto">
		<table width="100%" border="0" class="Reportes">
			<tr>
				<td colspan="3"><h2>ASIGNACION DE PRESUPUESTOS A CLUBES</h2></td>
			</tr>
			<tr>
				<td colspan="3"><b>INSTRUCCIONES:</b> 
					Asigne un porcentaje o cantidad directa a cada club de su consejo. Si no se asigna el 100% del monto quedar&aacute; como saldo
					para la siguiente temporada
				</td>
			</tr>
			<tr class="subtitulo">
				<td>Club</td>
				<td>Porcentaje</td>
				<td>Cantidad (KCacaos)</td>
				<td>Minimo para operar (KCacaos)</td>
			</tr>
			{CLUBES}
			<tr class="{CLASE}">
				<td><div id="club-{CONSECUTIVO}">{NOMBRE_CLUB}</div></td>
				<td>
					<input type="text" size="5" name="txtPorcentaje{CODIGO_CLUB}" class="text" value="{PORCENTAJE_ASIGNADO}" class="derecha" id="text-porcentaje-{CONSECUTIVO}" onblur="CalculaTotal({CONSECUTIVO},1);" />%
				</td>
				<td>
					<input type="text" size="7" name="txtCantidad{CODIGO_CLUB}"  class="text" value="{CANTIDAD_ASIGNADA}" class="derecha" id="text-cantidad-{CONSECUTIVO}" onblur="CalculaTotal({CONSECUTIVO},2);" /> KCACAOS
				</td>
				<td><div id="minimo-{CONSECUTIVO}">{MINIMO_OPERACIONAL}</div></td>
			</tr>
			{/CLUBES}
			<tr class="totales">
				<td>Total asignado:</td>
				<td>
					<div class="notice" id="label-porcentaje-total">{PORCENTAJE_TOTAL}</div>
				</td>
				<td colspan="2">
					<div class="notice" id="label-cantidad-total">{CANTIDAD_TOTAL}</div>
				</td>
			</tr>
			<tr>
				<td colspan="3"><input type="button" value="Asignar" class="button" onclick="Valida();" /></td>
			</tr>
			<tr>
				<td colspan="3"><div class="success">Presupuesto del consejo: {PRESUPUESTO_CONSEJO} K</div></td>
			</tr>
		</table>
		<input type="hidden" name="hdnTotalClubes" value="{TOTAL_CLUBES}" />
		<input type="hidden" name="hdnPresupuestoConsejo" value="{PRESUPUESTO_CONSEJO}" />
		<input type="hidden" name="hdnPorcentajeAsignado" value="{PORCENTAJE_TOTAL_ASIGNADO}" />
		<input type="hidden" name="hdnCantidadAsignada" value="{CANTIDAD_TOTAL_ASIGNADA}" />
		<input type="hidden" name="hdnListaClubes" value="{LISTA_CLUBES}" />
	</form>