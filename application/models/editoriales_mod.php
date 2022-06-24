<?php

    class editoriales_mod extends CI_Model {

        function __construct() {
            parent::__construct();
            $this->load->library('Tools_lib');
        }

        function get ($psClave) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT ed.id_unico, ed.cuerpo, ed.titulo, ca.nombre as 'autor', ca.columna, ed.fecha, ca.imagen, ed.id_temporada, ca.operador, ed.id_columna "
                        ." FROM editoriales ed "
                        ." INNER JOIN cat_autores ca ON ed.id_columna=ca.id_unico"
                        ." WHERE ed.id_unico=".$psClave,
                        'UNICA_FILA' => true)
            );
            return ($asResult);
        }

        function lista ($psUsuario="") {
            $sCondicion=(($psUsuario=="")||($psUsuario=="root")) ? "" : " WHERE ca.operador='".$psUsuario."'";
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT ed.id_unico, ed.cuerpo, ed.titulo, ca.nombre as 'autor', ca.columna, ed.fecha, ca.imagen "
                        ." FROM editoriales ed "
                        ." INNER JOIN cat_autores ca ON ed.id_columna=ca.id_unico"
                        .$sCondicion
                )
            );
            return ($asResult);
        }

        function comentarios_lista($psIdEditorial) {
            $asResult=$this->tools_lib->consulta(
                array ('QUERY' => "SELECT * "
                        ." FROM editoriales_comentarios ec "
                        ." WHERE ec.id_editorial=".$psIdEditorial
                    )
            );
            return ($asResult);
        }
        
        function graba($pasInput) {
            $asResult=$this->tools_lib->ejecutar_query(array(
                'QUERY' => "INSERT INTO editoriales (id_unico, titulo, "
                            ." id_equipo, cuerpo, "
                            ." id_columna, id_torneo, "
                            ." fecha, id_temporada) values "
                            ." (".$pasInput['id'].",'".$pasInput['titulo']."',"
                            ."'".$pasInput['id_equipo']."','".$pasInput['cuerpo']."',"
                            ."'".$pasInput['id_columna']."','".$pasInput['id_torneo']."',"
                            ." Now(),".$pasInput['id_temporada'].")"));
            return($asResult);
        }

        function actualiza($pasInput) {
            $asResult=$this->tools_lib->ejecutar_query(array(
                'QUERY' => "UPDATE editoriales SET titulo='".$pasInput['titulo']."', "
                                ." id_equipo='".$pasInput['id_equipo']."', id_columna='".$pasInput['id_columna']."', cuerpo='".$pasInput['cuerpo']."', "
                                ." id_torneo='".$pasInput['id_torneo']."', id_temporada='".$pasInput['id_temporada']."' "
                                ." WHERE id_unico=".$pasInput['id']));
            return($asResult);
        }
        
    }
?>
