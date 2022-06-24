<html>
	<head>
		<title>1a. vista code igniter</title>
	</head>
	<body>
		<h2>Welcome!</h2>
		<p>Esta es una vista</p>
		
		<p>Las subdirecciones son</p>
<?php
		var_dump($lista);
		while ($aoFila=mysql_fetch_array($lista)) {
			echo '<p>';
			echo '<a href="'.site_url('servicios/muestra_subdireccion/'.$aoFila['id_subdireccion']).'">'.$aoFila['subdireccion'].'</a>';
			echo '</p>';
		}



?>
	</body>
</html>