	<div id="mensaje"></div>
	<h2>ROSTER DE LA TEMPORADA PASADA</h2>
		<table class="data-table">
			<tr>
				<td>Presupuesto Asignado</td>
				<td>Presupuesto Gastado</td>
				<td>Presupuesto Disponible</td>
				<td>Costo Franquicia</td>
				<td>Franquicias seleccionados</td>
				<td>Bases seleccionados</td>
			</tr>
			<tr>
				<td>{PRESUPUESTO_ASIGNADO} KC</td>
				<td>{PRESUPUESTO_GASTADO} KC</td>
				<td>{PRESUPUESTO_DISPONIBLE} KC</td>
				<td>{COSTO_FRANQUICIA}</td>
				<td><div id="franquicias-seleccionados">{FRANQUICIAS_SELECCIONADOS}</div>/3</td>
				<td><div id="bases-seleccionados">{BASES_SELECCIONADOS}</div>/5</td>
			</tr>         
		</table>
	<br/>
	<form name="frmSeleccionFranquicias" action="{RUTA_RAIZ}{INDEX_URI}admin/draft_franquicias/procesa" method="post">
		<table>
			<tr>
				<td>Contratados</td>
			</tr>
			<tr>
				<td>
					<table width="70%" border="0" class="Reportes">
						<thead>
							<th>Codigo</th>
							<th>Nombre</th>
							<th>Tipo</th>
							<th>Precio</th>
							<th>Temporadas</th>
							<th>Cancelar</th>
						</thead>
						{BLOQUE_JUGADORES_CONTRATADOS}
						<tr class="{CLASE}">
							<td>{CODIGO}</td>
							<td>{NOMBRE}</td>
							<th>{TIPO}</th>
							<td>{PRECIO}</td>
							<td>{TEMPORADAS}</td>
							<td>
								<input type="button" onclick="JavaScript: CancelaContrato('{CONTRATO}');" value="Cancelar" class="button" />
							</td>
						</tr>
						{/BLOQUE_JUGADORES_CONTRATADOS}
					</table>
				</td>
				<td>
					<div id="proyecciones">
						<table width="100%">
							<tr><td>&nbsp;</td></tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
	</form>
	<form name="frmSeleccionFranquicias" action="{RUTA_RAIZ}{INDEX_URI}admin/draft_franquicias/procesa" method="post">
		<table>
			<tr>
				<td>Libres</td>
			</tr>
			<tr>
				<td>
					<table width="70%" border="0" class="Reportes">
						<thead>
							<th>Codigo</th>
							<th>Nombre</th>
							<th>Precio base</th>
							<th>Libre</th>
							<th>Base</th>
							<th>Franquicia</th>
							<th>Temporadas</th>
							<th>Sueldo temporada</th>
						</thead>
						{BLOQUE_JUGADORES_LIBRES}
						<tr class="{CLASE}">
							<td>{CODIGO}</td>
							<td><a href="JavaScript: verHabilidades({CODIGO});">{NOMBRE}</a></td>
							<td><div id="precio-base-{CODIGO}">{PRECIO_BASE}</div></td>
							<td><input type="radio" name="rbEstatus{CODIGO}" {ESTATUS_LIBRE}  value="L" onchange="Calcula({CODIGO},0,'L');"/></td>
							<td><input type="radio" name="rbEstatus{CODIGO}" {ESTATUS_BASE} value="B" onchange="Calcula({CODIGO},{PRECIO_BASE}, 'B');" /></td>
							<td><input type="radio" name="rbEstatus{CODIGO}" {ESTATUS_FRANQUICIA} value="F" onchange="Calcula({CODIGO},{COSTO_FRANQUICIA},'F');"/></td>
							<td>
								<input type="text" name="txtTemporadas{CODIGO}" value="{TEMPORADAS}" id="text-temporadas-{CODIGO}" class="text" size="3" />
								<input type="button" onclick="JavaScript: VerProyecciones({PRECIO_BASE}, document.getElementById('text-temporadas-{CODIGO}').value, '{NOMBRE}');" value="Proyectar" class="button" />
							</td>
							<td><div id="costo-{CODIGO}">{COSTO}</div><input type="hidden" id="tipo-contrato-{CODIGO}" value="{TIPO_CONTRATO}" /></td>
						</tr>
						{/BLOQUE_JUGADORES_LIBRES}
						<tr class="titulo_2">
							<td colspan="2">Total para agregar al gasto:</td>
							<td colspan="6">
								<div id="total-temporada">{TOTAL_TEMPORADA}</div>
							</td>
						</tr>
						<tr>                                                  
							<td colspan="8">
								<input type="button" name="btnEnviar" class="button" value="Guardar" onclick="JavaScript: Evalua();" />
							</td>
						</tr>
					</table>
				</td>
				<td>
					<div id="proyecciones">
						<table width="100%">
							<tr><td>&nbsp;</td></tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
		<input type="hidden" name="hdnPresupuestoClub" id="presupuesto-club" value="{PRESUPUESTO}" />
		<input type="hidden" name="hdnIdClub" id="id-club" value="{ID_CLUB}" />
		<input type="hidden" name="hdnListaJugadores" id="lista-jugadores" value="{LISTA_JUGADORES}" />
	</form>
	<h3>Jugadores que ya estan fuera</h3>
	<table width="70%" border="0" class="Reportes">
		<thead>
			<th>Codigo</th>
			<th>Nombre</th>
			<th>Precio base</th>
			<th>Club</th>
			<th>Temporadas</th>
		</thead>
		{BLOQUE_JUGADORES_FUERA}
		<tr class="{CLASE}">
			<td>{CODIGO}</td>
			<td>{NOMBRE}</td>
			<td>{PRECIO}</td>
			<td>{CLUB}</td>
			<td>{TEMPORADAS}</td>
		</tr>
		{/BLOQUE_JUGADORES_FUERA}
	</table>
