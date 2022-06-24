<style>
.encabezado {
  -webkit-transform: rotate(-90deg);
  -moz-transform: rotate(-90deg);
  -ms-transform: rotate(-90deg);
  -o-transform: rotate(-90deg);
  transform: rotate(-90deg);

  /* also accepts left, right, top, bottom coordinates; not required, but a good idea for styling */
-webkit-transform-origin: 50% 50%;
  -moz-transform-origin: 50% 50%;
  -ms-transform-origin: 50% 50%;
  -o-transform-origin: 50% 50%;
  transform-origin: 50% 50%;

  /* Should be unset in IE9+ I think. */
  /*filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);*/
  
  height: 250px;
  width: 37px;
  padding-left: 0px;
}
</style>
<form action="{RUTA_RAIZ}admin/draft_reportes/habilidades" method="post">
  <table class="Reportes">
	<tr>
	  <td>Filtrar por:</td>
	  <td>{COMBO_FILTRO}</td>
	  <td><input type="submit" class="button" value="Filtrar" /></td>
	</tr>
  </table>
  <table class="Reporte" border="1">
	<tr>	
{BLOQUE_ENCABEZADO}
		<td>
			<!--<div class="encabezado">{LEYENDA}</div>-->
			<a href="{RUTA_RAIZ}admin/draft_reportes/habilidades/{FILTRO}/{CAMPO}"><img src="{RUTA_RAIZ}img/arrow-down-circle.jpg" border="0"></a>
		</td>
{/BLOQUE_ENCABEZADO}
	</tr>
{BLOQUE_FILA}
	<tr class="{CLASE}">
		{VALORES}
	</tr>
{/BLOQUE_FILA}
  </table>
  <input type="hidden" name="code" value="55040400" />
</form>