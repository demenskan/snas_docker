<fieldset>
    <legend>Documentos</legend>
    <p>
        Seccion:{SECCION}
    </p>
    <table>
        <thead>
            <th>TITULO</th>
            <th>AUTOR</th>
            <th colspan="3">OPERACIONES</th>
        </thead>
        {BLOQUE_DOCTOS}
        <tr>
            <td>{TITULO}</td>
            <td>{AUTOR}</td>
            <td><a href="documentos/ver/{ID_UNICO}">VER</a></td>
            <td><a href="admin/documentos/editar/{ID_UNICO}">EDITAR</a></td>
            <td><a href="admin/documentos/eliminar/{ID_UNICO}">BORRAR</a></td>
        </tr>
        {/BLOQUE_DOCTOS}
    </table>
</fieldset>
