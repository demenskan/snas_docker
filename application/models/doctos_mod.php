<?php

    class doctos_mod extends CI_Model {

        function __construct() {
            parent::__construct();
            $this->load->library('Tools_lib');
        }

        function Lista ($psTipo, $psCodigo="") {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT * FROM documentos WHERE tipo='".$psTipo."' AND codigo='".$psCodigo."'"
                      )
            );
            return ($asResult);
        }

        function VerDocto ($psClave) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT * FROM documentos WHERE id_unico=".$psClave,
                        'UNICA_FILA' => true)
            );
            return ($asResult);
        }

    }
?>
