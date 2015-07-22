<?php
namespace Sky;
use Sky\core\Load;
use Sky\core\View;
use Sky\core\Config;
use Sky\core\Language;

class App {
	function __construct(){
		$this->Loader = Loader::getClass('Sky.core.Loader');
	}
}