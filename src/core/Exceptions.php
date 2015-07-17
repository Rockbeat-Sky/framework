<?php
namespace Sky\core;


class Exceptions extends BaseClass{
	
	static $is_error = false;
	
	static $message = [];
	
	static function getError(){
		
		return self::$is_error;
	
	}

	static function show404(){
		$heading = "404 Page Not Found";
		$message = "The page you requested was not found.";

		self::showError($heading, $message, 'App.Error404', 404);
	}
	static function showError($heading, $message, $template = 'App.ErrorGeneral', $status_code = 500){
		
		$prop = Loader::getName($template,'view'.DS.'error');
		
		_setStatusHeader($status_code);
		
		ob_start();
		include $prop->path;
		$buffer = ob_get_contents();
		
		ob_end_clean();
		
		echo  $buffer;
		if(Config::read('App.Base.backtrace_print')){
			echo '<pre>';
			debug_print_backtrace();
			echo '</pre>';
		}
		exit;
	}
	static function backtrace(){
		
	}
}