<?php

namespace Ocdla;


	
class View {


	public static function renderTemplate($path, $vars = array()) {

		extract($vars);

		ob_start();
		require $path;
		$content = trim(ob_get_contents()); // get contents of buffer
		
		ob_end_clean();

		return str_replace(array("\r", "\n"), '', $content);
	}



}