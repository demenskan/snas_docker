    <h2>Operaciones Especiales</h2>
    <form name="frmGeneradorCompleto" action="{RUTA_RAIZ}{INDEX_URI}admin/torneos/generador_completo" method="post">
        <fieldset>
            <legend>Generador de calendario completo</legend>
            <table>
                <tr>
                    <td>Cantidad de vueltas:</td>
                    <td><select name="slcCantidadVueltas"><option value="1">1</option><option value="2">2</option></select></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" class="button" value="Generar" /></td>
                </tr>
            </table>
        </fieldset>
        <input type="hidden" name="tor" value="{CLAVE_TORNEO}" />
        <input type="hidden" name="ssn" value="{CLAVE_TEMPORADA}" />
    </form>
    <form name="frmGeneradorEspejo" action="{RUTA_RAIZ}{INDEX_URI}admin/torneos/generador_espejo" method="post">
        <fieldset>
            <legend>Generador de calendarios espejo</legend>
            <table>
                <tr>
                    <td>Ultima Jornada de la vuelta:</td>
                    <td>{COMBO_JORNADA_MEDIA}</td>
                </tr>   
                <tr>
                    <td>Generar partidos a partir de la jornada:</td>
                    <td><input type="text" class="text" name="txtJornadaInicial" /></td>
                </tr>
                <tr>
                    <td>Hasta la jornada:</td>
                    <td><input type="text" class="text" name="txtJornadaFinal" /></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" class="button" value="Generar" /></td>
                </tr>
            </table>
        </fieldset>
        <input type="hidden" name="tor" value="{CLAVE_TORNEO}" />
        <input type="hidden" name="ssn" value="{CLAVE_TEMPORADA}" />
    </form>
    <form name="frmGeneradorDescansos" action="{RUTA_RAIZ}{INDEX_URI}admin/torneos/generador_descansos" method="post">
        <fieldset>
            <legend>Generador automatico de descansos</legend>
            <table>
                <tr>
                    <td colspan="2">
                        Todos los equipos que esten registrados en el acomodo de grupos que no esten registrados para un
                        partido en una jornada en particular, tendran un descanso automatico
                    </td>
                </tr>   
                <tr>
                    <td>Generar descansos a partir de la jornada:</td>
                    <td>{COMBO_DESCANSO_INICIAL}</td>
                </tr>
                <tr>
                    <td>Hasta la jornada:</td>
                    <td>{COMBO_DESCANSO_FINAL}</td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" class="button" value="Generar" /></td>
                </tr>
            </table>
        </fieldset>
        <input type="hidden" name="tor" value="{CLAVE_TORNEO}" />
        <input type="hidden" name="ssn" value="{CLAVE_TEMPORADA}" />
    </form>
    
