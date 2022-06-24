<?php

	class Articulos_model extends CI_Model {
	
		function __construct() {
			parent::__construct();
		}
	
		function dame_ultimos_articulos() {
			$sSQL="SELECT * FROM articulos LIMIT 0,10";
			if ($rRec=mysql_query($sSQL))
				return ($rRec);
			else
				return ('Error '.mysql_error());
		}
	
		function get_articulo ($sId) {
			$sQy="SELECT * FROM articulos WHERE id=".$sId;
			if ($rRec=mysql_query($sQy)) {
				if (mysql_num_rows($rRec)>0)
					return (mysql_fetch_array($rRec));
				else
					return (false);
			}
			else
				return ('Error '.mysql_errno().': '.mysql_error());
		}
	
	
	}


?>