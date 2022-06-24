    <!--Referencia tabs: http://htmlrockstars.com/blog/using-css-to-create-a-tabbed-content-area-no-js-required/-->
    <script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/ajax_tabs.js" language="JavaScript"></script>
    <script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/jquery-1.4.1.js" language="JavaScript"></script>
    <script type="text/javascript" src="<?=$RUTA_RAIZ?>externos/js/jquery.autocomplete.min.js" language="JavaScript"></script>
    <!--<script src="externos/js/jquery-1.7.2.min.js"></script>-->
    <link rel="stylesheet" href="css/folder-tabs.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="<?=$RUTA_RAIZ?>externos/js/jquery.autocomplete.css" /> 

    <?=$MENSAJE?>
    <form action="<?=$RUTA_RAIZ?>editoriales/graba" method="post" enctype="multipart/form-data" name="frmEditoriales">
        <table class="Reportes">
            <tr>
                <td align="right">Columna:</td>
                <td align="left"><?=$COMBO_COLUMNA?></td>
            </tr>
            <tr>
                <td align="right">Titulo:</td>
                <td align="left"><input type="text" class="text" name="txtTitulo" maxlength="50" size="50" value="<?=$TITULO_EDITORIAL?>" /></td>
            </tr>
            <tr>
                <td align="right">Temporada:</td>
                <td align="left"><?=$COMBO_TEMPORADA?></td>
            </tr>
            <tr>
                <td align="right">Torneo:</td>
                <td align="left"><?=$COMBO_TORNEO?></td>
            </tr>
            <tr>
                <td align="right">Equipo:</td>
                <td align="left"><?=$COMBO_EQUIPO?></td>
            </tr>
            <tr>
                <td align="right">Texto:</td>
                <td align="left">
                <textarea name="taCuerpo" cols="70" rows="20"><?=$CUERPO?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center"><input type="submit" class="button" value="Agregar" /></td>
            </tr>
        </table>
        <input type="hidden" name="modo" value="<?=$MODO_CAPTURA?>" />
        <input type="hidden" name="hdnIdEditorial" value="<?=$ID_EDITORIAL?>" />
    </form>
