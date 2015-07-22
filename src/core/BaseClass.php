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
use Sky\core\Log;

class BaseClass{
	public $_config = [];

	public function __construct(){
	
		if(func_num_args() > 0){
			$config = func_get_arg(0);

			$this->setConfig($config);
		}
		Loader::getClass('Sky.core.Log')->write(100,get_class($this).' Class Initialize');
		
	}
	/**
	* Set config class
	* 
	* @param string
	* @return void
	*/
	public function setConfig($key,$value = ''){
		if(is_array($key)){
			$this->_config = array_merge($this->_config,$key);
		}
		else{
			$this->_config[$key] = $value;
		}
		$this->updateConfig();
		
		return $this;
	}
	/**
	* get config value
	* 
	* @param string
	* @return mix
	*/
	public function getConfig($index = ''){
		if($index === ''){
			return $this->_config;
		}
		if(isset($this->_config[$index])){
			return $this->_config[$index];
		}
		return null;
	}
	/**
	* initialize config
	* 
	* create magic method config
	* 
	* @param array
	* @return array
	*/
	public function updateConfig(){
	
		foreach($this->_config as $name => $val){
		
			$method = '__Set'._camelize($name);
			
			if(method_exists($this,$method)){
				$this->_config[$name] = call_user_func(array($this,$method),$val);
			}
		}
		return $this;
	}
}