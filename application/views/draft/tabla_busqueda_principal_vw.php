                <table class="Reportes">
                    {BLOQUE_ELEMENTOS}
                    <tr class="{CLASE}">
                        <td>{CAMPO}</td>
                        <td>{OPERADOR}</td>
                        <td>{VALOR}</td>
                        <td><input type="button" value=" X " class="button" onClick="Javascript: QuitaCondicion('+ {CONTADOR} + ');" /></td>
                    </tr>
                    {/BLOQUE_ELEMENTOS}
                </table>