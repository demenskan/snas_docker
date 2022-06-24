{TABLA_RESULTADOS}
<table class="Resultados" width="100%">
    <tr>
        <td>{BOTON_PRIMERO}</td>
        <td>{BOTON_ANTERIOR}</td>
        <td>P&aacute;gina</td>
        <td><select id="numero-pagina" onchange="gotoPagina(this.value)">
                {BLOQUE_OPCIONES}
                    <option value="{VALOR}" {SELECTED}>{VALOR}</option>
                {/BLOQUE_OPCIONES}
            </select>
        </td>
        <td>de {TOTAL_PAGINAS}</td>
        <td>{BOTON_SIGUIENTE}</td>
        <td>{BOTON_ULTIMO}</td>
    </tr>
    
</table>
Club: {CLUB} Query:{QUERY} Scope:{SCOPE}
<script>
    function gotoPagina(piPagina) {
        sURLsearch="admin/draft_subastas/xi_busqueda/{CLUB}/{QUERY}/{SCOPE}/"+piPagina;
        //sURL="admin/draft_subastas/principal/{CLUB}/0/{QUERY}/{SCOPE}/"+piPagina;
        //alert(sURLsearch);
        callPage(sURLsearch,'tabla-resultados','Cargando...','Error@');
        //location.href=sURL;
    }
</script>