						<table class="Reportes" width="700">
							<thead>
								<tr>
									<th colspan="{TOTAL_GRUPOS}">Calendario por grupos</th>
								</tr>
							</thead>
							<tbody>
							<tr class="par">
{BLOQUE_SELECCION}							
								<td><a href="{RUTA_RAIZ}{INDEX_URI}torneos/calendariogrupo/{TEMPORADA}/{TORNEO}/{GRUPO_SEL}">{GRUPO_SEL}</a></td>
{/BLOQUE_SELECCION}
							</tr>
							</tbody>
						</table>
					<table class="tabla_normal" align="center" cellspacing="0" cellpadding="10">
						<table class="Reportes" width="700">
							<thead>
								<tr>
									<th colspan="6">GRUPO {GRUPO}</th>
								</tr>
							</thead>
							<tbody>
					{BLOQUE_PARTIDOS}
								{JORNADA}
								<tr class="{CLASE}">
									<td><img src="{RUTA_RAIZ}img/escudos/mini/s{LOGO_LOCAL}.gif" /></td>
									<td>{CLUB_LOCAL}</td>
									<td>{MARCADOR}</td>
									<td>{CLUB_VISITA}</td>
									<td><img src="{RUTA_RAIZ}img/escudos/mini/s{LOGO_VISITA}.gif" /></td>
									<td><a href="{RUTA_RAIZ}torneos/detallepartido/{TEMPORADA}/{TORNEO}/{CLAVE}">Ver detalle...</a></td>
								</tr>
					{/BLOQUE_PARTIDOS}
							</tbody>
						</table>
					</table>