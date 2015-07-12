<?php
 /**
 * Sky Framework
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @package		Sky Framework
 * @author		Hansen Wong
 * @copyright	Copyright (c) 2015, Rockbeat.
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @link		http://rockbeat.web.id
 * @since		Version 1.0
 */
namespace Sky\core;

class Controller extends BaseClass{

	public $SKY;

	/**
	* Start initialize controller
	* 
	* @param string
	* @param array
	* @return void
	*/
	public function __init($method,$params){

		$this->SKY = (object)[
			'method' => $method,
			'params' => $params
		];

		// is class have method ?
		if(!method_exists($this,$method)){
			
			Loader::getClass('Sky.core.Log')->write(300,$method.' Page Not Found');
			
			Exceptions::show404();
		}
		
		// run before controller if have?
		if(method_exists($this,'__before')){
		
			$this->__before();
			
		}

		call_user_func_array([$this,$method],$params);
		
		// run after controller if have?
		if(method_exists($this,'__after')){
		
			$this->__after();
			
		}
	}
}