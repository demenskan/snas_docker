
<script>
	function CambiaComboClave(piModo) {
		switch (piModo) {
			case '__EXTRA__' :
				sSalida="";
				break;
			case 'consejo' :
				sSalida='<select name="slcClave" >'
					+ '	<option value="1">AFP</option>'
					+ '	<option value="2">CIMAF</option>'
					+ '	<option value="3">CELFUS</option>'
					+ '	<option value="4">CSN</option>'
					+ '	<option value="5">CIF</option>'
					+ '	<option value="6">CO</option>'
					+ '</select>';					
				break;
			case 'liga' :
				sSalida='<select name="slcClave" >'
					+ '	<option value="22">Canis Menor</option>'
					+ '	<option value="6">Canis Mayor</option>'
					+ '	<option value="21">Polaris Beta</option>'
					+ '	<option value="7">Polaris Alfa</option>'
					+ '	<option value="20">Orion B</option>'
					+ '	<option value="4">Orion A</option>'
					+ '	<option value="23">Centaurus 2da</option>'
					+ '	<option value="5">Centaurus Master</option>'
					+ '</select>';					
				break;
		}
		document.getElementById('combo-clave').innerHTML=sSalida;
	}

</script>
<p>
	<h2>Presupuestos para clubes</h2>
</p>
<form action="admin/draft_reportes/presupuestos_clubes" method="post">
	<table class="Reportes">
		<tr>
			<td>Genero: </td>
			<td>
				<select name="slcGenero" onchange="CambiaComboClave(this.value)"> 
					<option value="__EXTRA__">Todos</option>
					<option value="consejo">Consejo</option>
					<option value="liga">Liga</option>
				</select>
			</td>
		</tr>				
		<tr>
			<td>Especifico:</td>
			<td><div id="combo-clave"></div>
				<input type="submit" value="Ver"  class="button" />
			</td>
		</tr>	
{BLOQUE_CLUBES}
		<tr class="{CLASE}">
			<td>{NOMBRE}</td>
			<td>{PRESUPUESTO}</td>
		</tr>
{/BLOQUE_CLUBES}
		</tr>
	</table>
</form>