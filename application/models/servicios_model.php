<?php

	class Servicios_model extends CI_Model {
	
		function __construct() {
			parent::__construct();
		}
	
		function dame_servicios() {
			$sSQL="SELECT * FROM cat_subdirecciones ";
			if ($rRec=mysql_query($sSQL))
				return ($rRec);
			else
				return ('Error '.mysql_error());
		}
	
		function get_subdireccion($sId) {
			$sQy="SELECT * FROM cat_subdirecciones WHERE id_subdireccion=".$sId;
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