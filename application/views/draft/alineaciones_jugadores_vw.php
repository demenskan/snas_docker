<script type="text/javascript" src="{RUTA_RAIZ}externos/js/ajax_tabs.js" language="JavaScript"></script>
<script type="text/javascript">
	function verHabilidades(piClaveJugador) {
  /*	sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_movimientos/xi_habilidades/'+piClaveJugador;
	  callPage(sURL,'habilidades-jugador','Cargando...','Error@' + sURL);*/
	  sURL='{RUTA_RAIZ}{INDEX_URI}admin/draft_subastas/xi_habilidades/'+piClaveJugador;
	  document.getElementById('habilidades-jugador').innerHTML='<img src="'+ sURL + '" />';
	}
  
	function CambiaClubes(piCodigoClub) {
		sURL='admin/draft_movimientos/alineacionesjugadores/'+piCodigoClub;
		location.href=sURL;
	}
</script>
<style type="text/css" media="all">
  .posiciones {
	color: #FFFFFF;
	border: 1px solid #000000;
	text-align: center;
	margin-bottom: 1px;
  }
  
  #portero {
	background-color: #FF9920;
  }
  #defensa {
	background-color: #1DD300;
  }
  #medio {
	background-color: #009B95;
  }
  #atacante {
	background-color: #A61000;
  }
  
  
</style>
{COMBO_CLUBES}
<form action="{RUTA_RAIZ}{INDEX_URI}admin/draft_movimientos/alineacionesjugadores" method="post">
  <table width="800" style="border:2px solid #000000;">
	<tr>
	  <td style="width: 425px;">
		<img src="{RUTA_RAIZ}img/escudos/mini/s{RUTA_LOGO}.gif" align="left" /><h2>{NOMBRE_CLUB}</h2><br/>
			Formacion:	({NUMERO_DEFENSAS}-{NUMERO_MEDIOS}-{NUMERO_DELANTEROS})
	  </td>
	  <td style="width:300px;">{MENSAJE}</td>
	  <td colspan="2"><input type="submit" class="button" value="hacer cambios" />
	</tr>
	<tr>
	  <td valign="top">
		<table class="tabla_normal" border="1">
{BLOQUE_LISTA_JUGADORES}
		  <tr class="{CLASE}">
			<td>&nbsp;{POSICION}&nbsp;</td>
			<td><a href="javascript:verHabilidades('{ID_JUGADOR}');">{NOMBRE_JUGADOR}</a></td>
			<td align="right">
			  {COMBO_POSICIONES}
			</td>
		  </tr>
{/BLOQUE_LISTA_JUGADORES}
		</table>
		
	  </td>
	  <td valign="top" align="left">
		<div id="habilidades-jugador"></div>
		<table width="300" height="400" background="{RUTA_RAIZ}img/soccer_field_vert300x400.jpg" border="1">
		  <tr>
			<td height="30">&nbsp;</td>
		  </tr>
		  <tr>
			<td align="center">
{BLOQUE_PORTERO}
				<div class="posiciones" id="portero">{NOMBRE}</div>
{/BLOQUE_PORTERO}
			</td>
		  </tr>
		  <tr>
			<td height="30">&nbsp;</td>
		  </tr>
		  <tr>
			<td align="center" height="60">
{BLOQUE_DEFENSAS}
				<div class="posiciones" id="defensa">{NOMBRE}</div>
{/BLOQUE_DEFENSAS}
			</td>
		  </tr>
		  <tr>
			<td align="center" height="160">
{BLOQUE_MEDIOS}
				<div class="posiciones" id="medio">{NOMBRE}</div>
{/BLOQUE_MEDIOS}
			</td>
		  </tr>
		  <tr>
			<td align="center" height="80">
{BLOQUE_DELANTEROS}
				<div class="posiciones" id="atacante">{NOMBRE}</div>
{/BLOQUE_DELANTEROS}			
			</td>
		  </tr>
		</table>
	  </td>
	  <td valign="top" align="left">
		<table class="alineaciones">
		  <tr>
			<td colspan="2"><h3>Banca</h3></td>
		  </tr>
{BLOQUE_BANCA}
			<tr class="{CLASE}">
				<td>{POSICION}</td>
				<td>{NOMBRE}</td>	
			</tr>
{/BLOQUE_BANCA}
		</table>
	  </td>
	  <td>
		
	  </td>
	</tr>
  </table>
  <input type="hidden" name="code" value="55003C50" />
  <input type="hidden" name="hdnClaveClub" value={CLAVE_CLUB} />
  <input type="hidden" name="hdnModo" value="Actualizacion" />
</form>