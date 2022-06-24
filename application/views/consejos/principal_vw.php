		<div class="main" id="main-three-columns">

			<div class="left" id="main-left">
	    		<style>
					<?=$ROTADOR_IMAGENES?>	
				</style>

				<div id="rotator">
				<!--Tabs-->
					<ul class="ui-tabs-nav">
					<?=$TABULADORES?>
					</ul>
					<?=$RESUMENES?>
				</div><!--end rotator-->

				<div class="content-separator"></div>

				<?=$NOTICIAS_COMPLEMENTARIAS?>

				<div class="clearer">&nbsp;</div>
				
				<div class="section-title">Noticias Antiguas</div>
					<ul class="nice-list">
						<?=$NOTICIAS_VIEJAS?>
						<li><a href="generador.php?code=search" class="more">Ver mas noticias...</a></li>
					</ul>
				<div class="clearer">&nbsp;</div>
				
				<?=$VIDEOS?>
				
			</div>

			<div class="left sidebar" id="sidebar-1">

				<h3><a href="#">Datos Generales</a></h3>

				<?=$EDITORIALES?>
				
				<div class="post">

					<h3><a href="#">Publicidad</a></h3>

					<p><?=$BANNERS?></p>

				</div>				

			</div>

			<div class="right sidebar" id="sidebar-2">

				<div class="section">

					<div class="section-title">

						<div class="left">&Uacute;ltimos resultados</div>
						<div class="right"><img src="img/icon-time.gif" width="14" height="14" alt="" /></div>

						<div class="clearer">&nbsp;</div>

					</div>

					<div class="section-content">

						<ul class="nice-list">
							<?=$RESULTADOS?>
							<li><a href="generador.php?code=torneos" class="more">Ver todos &#187;</a></li>
						</ul>

					</div>

				</div>

				<div class="section">

					<div class="section-title">Carrera por el botin dorado</div>

					<div class="section-content">

						<ul class="nice-list">
							<?=$GOLEADORES?>
							<li><a href="generador.php?code=goleo_todos" class="more">Ver todos &#187;</a></li>
						</ul>
						
					</div>

				</div>

				<div class="section network-section">

					<div class="section-title">Torneos Actuales</div>

					<div class="section-content">

						<ul class="nice-list">
							<li><a href="#">Nullam eros</a></li>
							<?=$TORNEOS?>
						</ul>
						
					</div>

				</div>

			</div>