	<table width="300">
		<tr>
			<td>
				<table class="Reportes">
					<thead>
						<th>Posicion</th>
						<th>Nombre</th>
						<th>Puntos</th>
					</thead>
{BLOQUE_JUGADORES}
					<tr class="pos_{INICIALES}">
						<td>{INICIALES}</td>
						<td>{NOMBRE}</td>
						<td>{PUNTOS}</td>
					</tr>
{/BLOQUE_JUGADORES}
				</table>
			</td>
			<td>
				<img src="{RUTA_RAIZ}img/escudos/mini/s{RUTA_LOGO}.gif" />
				{NOMBRE_CLUB}
			</td>
		</tr>
	</table>