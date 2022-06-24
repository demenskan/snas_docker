						<table class="Reportes" align="center" cellspacing="0" cellpadding="10" width="500">
							<tr>
								<th><img src="{RUTA_RAIZ}img/escudos/mini/s{LOGO_LOCAL}.gif" alt="{NOMBRE_LOCAL}"  width="30" height="30" /></th>
								<th><h3>{MARCADOR_LOCAL}</h3></th>
								<th><img src="{RUTA_RAIZ}img/escudos/mini/s{LOGO_VISITA}.gif" alt="{NOMBRE_VISITA}" width="30" height="30" /></th>
								<th colspan="2"><h3>{MARCADOR_VISITA}</h3></th>	
							</tr>
							<tr>
								<td colspan="5"></td>
							</tr>
{BLOQUE_EVENTOS}
							<tr class="{COLOR}">
								<td><img src="{RUTA_RAIZ}img/escudos/{RUTA_LOGO}.gif" width="30"></td>
								<td>{NOMBRE}</td>
								<td>{MINUTO}</td>
								<td><img src="{RUTA_RAIZ}img/{IMAGEN}.gif" title="{DESCRIPCION}" alt="{DESCRIPCION}"></td>
								<td><a href="Javascript: callPage('{RUTA_RAIZ}admin/torneos/borra_evento/{TEMPORADA}/{TORNEO}/{PARTIDO}/{RELATIVO}','lista-eventos','Cargando...','{RUTA_RAIZ}admin/torneos/borra_evento/{TEMPORADA}/{TORNEO}/{PARTIDO}/{RELATIVO}');">Borrar</a></td>
							</tr>
{/BLOQUE_EVENTOS}
						</table>		
