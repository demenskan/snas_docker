<h1>Listado de art’culos</h1>
<p>Estos son los œltimos art’culos publicados.</p>
<?php
while ($fila = mysql_fetch_array($rs_articulos)){
   echo '<p>';
   echo '<a href="' . site_url('/articulos_plantilla/muestra/' . $fila['id']) . '">' . $fila['titulo'] . '</a>';
   echo '</p>';
}
?>