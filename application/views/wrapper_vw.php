<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="description" content=""/>
	<meta name="keywords" content="" />
	<meta name="author" content="" />
	<base href="<?=base_url()?>" />
	<link rel="stylesheet" type="text/css" href="<?=$RUTA_RAIZ?>css/tabbed-rotator.css" />
	<link rel="stylesheet" type="text/css" href="<?=$RUTA_RAIZ?>css/simple-magazine/style.css" media="screen" />
	<script src="<?=$RUTA_RAIZ?>externos/js/jquery132.min.js" type="text/javascript"></script>
	<script src="<?=$RUTA_RAIZ?>externos/js/jquery-ui-personalized-1.5.3.packed.js" type="text/javascript"></script>
	<script type="text/javascript">
		var submenu= new Array();
		var aMenusConHijos= new Array();
		function submenuDisplay (piOpcion) {
		  document.getElementById('sub-nav').innerHTML='<ul class="tabbed">'	+ submenu[piOpcion] + '</ul>';
		  for (i=0;i<aMenusConHijos.length;i++)
			document.getElementById('opc'+aMenusConHijos[i]).className='';
		  document.getElementById('opc'+piOpcion).className='current-tab';
		}

	</script>
	<title>SNAS MMX+1 &lt;&lt; Supernova International Soccer League</title>
</head>

<body id="top">

<div id="network">
	<div class="center-wrapper">

			<?=$ENCABEZADO?>

			<div class="clearer">&nbsp;</div>
		
		</div>
		
		<div class="clearer">&nbsp;</div>

	</div>
</div>

<div id="site">
	<div class="center-wrapper">

		<div id="header">

			<div class="clearer">&nbsp;</div>

			<div id="site-title">

				<a href="<?=$RUTA_RAIZ?>portada/"><?=$IMAGEN_ENCABEZADO?></a>

			</div>
			<?=$MENU_NAVEGACION?>
		</div>
		<?=$CONTENIDO?>
		<div class="clearer">&nbsp;</div>

		<?=$PIE_PAGINA?>
	</div>
</div>

</body>
</html>