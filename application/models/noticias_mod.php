<?php

	class noticias_mod extends CI_Model {
	
		function __construct() {
			parent::__construct();
			$this->load->library('Tools_lib');
		}
		
		function LeeNoticia($psID) {
			$asNoticia=$this->tools_lib->consulta(array(
				'QUERY' => "SELECT * FROM noticias WHERE id_unico=".$psID, 'UNICA_FILA' => true));
			return($asNoticia);
		}
		
		function LeeComentarios($psID) {
			$asResult=$this->tools_lib->consulta(array(
				'QUERY' => "SELECT * FROM noticias_comentarios WHERE id_noticia=".$psID." ORDER BY fecha_hora DESC"));
			return($asResult);
		}
		
		function TagsNoticias($psID) {
			$asResult=$this->tools_lib->consulta(array(
				'QUERY' => "SELECT tag.campo, tag.valor \n"
							." FROM tags_noticias tag \n"
							." WHERE tag.id_docto=".$psID." AND tag.tipo_docto=1"));
			return($asResult);
		}
		
		function getListaClubes() {
			$asResult=$this->tools_lib->consulta(array(
				'QUERY' => "SELECT id_unico, nombre_corto FROM clubes WHERE id_unico <> 0"));
			return($asResult);
		}
		
		function getListaConsejos() {
			$asResult=$this->tools_lib->consulta(array('QUERY' => "SELECT id_unico, iniciales FROM cat_consejos"));
			return($asResult);
		}
		
		function graba($pasInput) {
			$asResult=$this->tools_lib->ejecutar_query(array(
				'QUERY' => "INSERT INTO noticias (id_unico, titulo, "
							." subtitulo, resumen, cuerpo, "
							." fecha, temporada) values "
							." (".$pasInput['codigoNoticia'].",'".$pasInput['titulo']."',"
							."'".$pasInput['subtitulo']."','".$pasInput['resumen']."','"
							.$pasInput['cuerpo']."', Now(),".$pasInput['temporada'].")"));
			return($asResult);
		}

		function actualiza($pasInput) {
			$asResult=$this->tools_lib->ejecutar_query(array(
				'QUERY' => "UPDATE noticias SET titulo='".$pasInput['titulo']."', "
								." subtitulo='".$pasInput['subtitulo']."', resumen='".$pasInput['resumen']."', cuerpo='".$pasInput['cuerpo']."', "
								." temporada=".$pasInput['temporada']." "
								." WHERE id_unico=".$pasInput['codigoNoticia']));
			return($asResult);
		}
		
		function BorraEtiquetas ($piClaveNoticia) {
			$asResult=$this->tools_lib->ejecutar_query(array(
				'QUERY' => "DELETE FROM tags_noticias WHERE id_docto=".$piClaveNoticia." AND tipo_docto=1"));
			return($asResult);
		}

		function InsertaTag ($psModo, $psValor, $psIndice) {
			$asResult=$this->tools_lib->ejecutar_query(array(
				'QUERY' => "INSERT INTO tags_noticias (campo, valor, id_docto, tipo_docto) "
								." VALUES ('".$psModo."', '".$psValor."', ".$psIndice.",1)"	));
			return ($asResult);
		}	
		function BuscaNoticia ($psSearch, $piOffset, $piElementosxPagina, $piModo="normal") {
			if ($psSearch=="*ALL")
				$sCondicion="";
			else
				$sCondicion="WHERE MATCH(titulo, subtitulo, resumen, cuerpo) "
				."	AGAINST ('".$psSearch."' IN BOOLEAN MODE)";
			$asConteo=$this->tools_lib->consulta(array(
				'QUERY' => "SELECT COUNT(*) as 'conteo' FROM noticias ".$sCondicion,
				'UNICA_FILA' => true));
			if ($asConteo['ESTATUS']==1) {
				$sControl=($piModo=="edicion") ? "captura" : "ver" ;		
				$asSalida=$this->tools_lib->consulta(array(
					'QUERY' => "SELECT id_unico as 'TX Clave', concat('<a href=\"".base_url()."noticias/".$sControl."/',id_unico,'\">',titulo,'</a>') as ' TX Titulo', resumen as 'TX Resumen' "
								." FROM noticias "
								.$sCondicion
								." LIMIT ".$piOffset.",".$piElementosxPagina,
					'CAMPOS_NUMERICOS'=> false));
				$asSalida['CONTEO_TOTAL']=$asConteo['DATOS']['conteo'];
			}
			else 
				$asSalida=array ('ESTATUS' => -1, "ERROR" => "CONTEO: ".$asConteo['ERROR']);
			return ($asSalida);
		}
		
		function getRelacionadas ($psCampoTag, $psValorTag, $psIdNoticia) {
			$asResult=$this->tools_lib->consulta(array('QUERY' => "SELECT id_docto "
					."FROM tags_noticias "
					."WHERE campo='".$psCampoTag."' AND valor='".$psValorTag."' AND tipo_docto=1 AND id_docto <> ".$psIdNoticia));
			return($asResult);
		}

		function getGaleria($psIDNoticia) {
			$asNoticia=$this->tools_lib->consulta(array(
				'QUERY' => "SELECT * FROM imagenes_noticias WHERE id_noticia=".$psIDNoticia));
			return($asNoticia);
		}
		
		function GrabaLinkImagen($psInput) {
			$asResult=$this->tools_lib->ejecutar_query(array(
				'QUERY' => "INSERT INTO imagenes_noticias (id_noticia, url, consecutivo, leyenda, portada) "
								." VALUES (".$psInput['id_noticia'].", '".$psInput['url']."', "
								.$psInput['consecutivo'].",'".$psInput['leyenda']."',".$psInput['portada'].")"	));
			return ($asResult);
		}

		function BorraLinksImagenes ($psCodigo) {
			$asResult=$this->tools_lib->ejecutar_query(array(
				'QUERY' => "DELETE FROM imagenes_noticias WHERE id_noticia=".$psCodigo	));
			return ($asResult);
		}
	}
?>