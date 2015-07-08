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

class BaseClass{
	public $_config = [];

	public function __CONSTRUCT($config = []){
		$this->setConfig($config);
	}
	/**
	* Set config class
	* 
	* @param string
	* @return void
	*/
	public function setConfig($config){
		$this->_config = array_merge($this->_config,$this->__init_config($config));
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
	protected function __init_config($config){
		foreach($config as $name => $val){
			$method = '__Apply'.ucfirst($name);
			if(method_exists($this,$method)){
				$config[$name] = call_user_func(array($this,$method),$val);
			}
		}
		return $config;
	}
}