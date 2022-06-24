	    	<style>
        			<?=$ROTADOR_IMAGENES?>	
			</style>
			<script>
					$(document).ready(function(){
						$("#rotator > ul").tabs({fx:{opacity: "toggle"}}).tabs("rotate", 7000, true);
					});
			</script>
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
