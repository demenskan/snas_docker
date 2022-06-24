<?php

	class Test extends CI_Controller {


		function __construct() {
			parent::__construct();
		}
		
		
		
		function Index () {
			echo CI_VERSION;
		}
	}
