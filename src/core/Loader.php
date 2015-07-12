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
use Sky\core\Config;

class Loader{
	static $files = [];
	static $instance = [];
	static $env = [];
	
	/**
	Load Files
	
	@param string
	@param string
	*/
	static function load($namespace,$folder = ''){
	
		if(!isset(self::$files[$namespace])){
			$ns = self::getName($namespace,$folder);
			
			if(!file_exists($ns->path)){
				user_error('File '.$ns->path.' Not Found');
				exit;
			}
			
			require_once $ns->path;
			self::$files[$namespace] = $ns;
		}
	}
	/**
	* create or get Class
	* 
	* @param string
	* @param array
	* @param string
	* @return mix
	*/
	static function getClass($namespace = '',$config = [],$new = false){
	
		if($namespace == ''){
			return self::$instance;
		}
		
		if($new === false && isset(self::$instance[$namespace])){
			return self::$instance[$namespace];
		}
		
		$ns = self::getName($namespace);
		
		self::$instance[$namespace] = new $ns->namespace();
		
		return self::$instance[$namespace] ;
	}
	/**
	* set environment path
	* if param not set or environment not set it will load from config Environment.php
	*
	* @param array
	* @return void
	*/
	static function setEnvironment($data = []){
	
		if($data && is_array($data)){
			self::$env = $data;
			return;
		}
		
		// Check environment is set?
		if(count(self::$env ) == 0){
			self::$env = (require APP_PATH.'config' . DS . 'Environment.php');
		}
	}
	/**
	* parse name class
	* 
	* @param string
	* @param string
	* @return object
	*/
	static function getName($namespace,$folder = ''){

		// is namespace empty?
		if($namespace == ''){
			user_error('No Namespace');
			exit;
		}
		self::setEnvironment();
		
		// check environment have data?
		if(count(self::$env) == 0){
			user_error('Environment Registry is empty');
			exit;
		}
		
		$segments = preg_split('/(\.|\\\\)/',$namespace);
		$appname = $segments[0];

		if($folder !== ''){
			array_splice($segments,1,0,[$folder]);
		}

		$path = VENDOR_PATH;

		if(isset(self::$env[$appname])){
			$path = self::$env[$appname];	
		}
		
		// remove first array
		array_shift($segments);

		$p = implode(DS,$segments);

		$name = (object)[
			'app' => $appname,
			'namespace' => '\\'.$appname.'\\'.str_replace('/','\\',$p),
			'class' => end($segments),
			'path' => $path . $p. '.php',
			'segments' => $segments
		];
		return $name;
	}
	/**
	* add push instance class
	* 
	* @param object class
	* @return void
	*/
	static function addInstance($object){
		$name = str_replace(DS,'.',get_class($object));
		self::$instance[$name] = $object;
	}
	/**
	* This is for autoload sky
	* dont use this if using autoloader from composer
	*/
	static function autoloadRegister(){
		spl_autoload_register('self::load');
	}
}