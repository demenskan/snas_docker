			<table width="90%">
                <thead>
                     <th>Club</th>
                     <th>Sale</th>
                     <th>Entra</th>
                     <th>Minuto</th>
                    <th>Operaciones</th>
                </thead>
                {BLOQUE_CAMBIOS}
                <tr>
                    <td><img src="img/escudos/mini/s{ESCUDO_CLUB}.gif" /></td>
                    <td>{NOMBRE_SALE}</td>
                    <td>{NOMBRE_ENTRA}</td>
                    <td>{MINUTO}</td>
                    <td><img src="img/destroy.png" onClick="Javascript: Borra('{NUMERO}');" />
                </tr>
                {/BLOQUE_CAMBIOS}
            </table>
