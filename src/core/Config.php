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
use Sky\core\Loader;

class Config{
	/**
	* keep cache data config
	* @var array
	**/
	static $data = [];
	/**
	* load file config
	* 
	* @param string
	* @return array
	*/
	static function load($name){

		if($name == ''){
			return self::$data;
		}
		$name = Loader::getName($name,'config');
		$key = $name->app.'.'.$name->class;
		
		if(!isset(static::$data[$key])){
			// file config is exist?
			if(!file_exists($name->path)){
				user_error('Config '.$name->path.' Not Found');
				exit;
			}
			self::$data[$key] = (require_once $name->path);
		}
		return self::$data[$key];
	}
	/**
	* read config data
	* 
	* @param string
	* @return array
	*/
	static function read($key = ''){
		if($key === ''){
			return self::$data;
		}
		
		$key = self::engineName($key);
		
		$file = implode($key->file,'.');


		if(!isset(self::$data[$file])){
			return null;
		}
		$config = self::$data[$file];
		
		foreach($key->keys as $keys){
			if(!isset($config[$keys])){
				return null;
			}
			$config = $config[$keys];
		}
		return $config;
	}
	/**
	* create or update config data
	* 
	* @param string
	* @param mix
	* @return void
	*/
	static function write($key,$value){
	
		$segments = self::engineName($key);
		$file = [implode($segments->file,'.')];
		
		// set key data
		$keys = array_merge($file,$segments->keys);
		
		// update config data
		self::$data = array_replace_recursive(
			self::$data ,
			self::_makeDimensional($keys,$value)
		);
	}
	/**
	* parse name key and value config
	* 
	* @param string
	* @return object
	**/
	public static function engineName($name){
		$segments = explode('.',$name);
		$index = 0;
		
		foreach($segments as $i => $name){
			if(ctype_upper(substr($name,0,1)) and $i != 0){
				$index = $i+1;
				break;
			}
		}
		
		if($index == 0){
			user_error('No Index Key Name');
			exit;
		}
		
		return (object)[
			'file' => array_slice($segments,0,$index),
			'keys' => array_slice($segments,$index)
		];
		
	}
	/**
	* make path multidimensional array
	* 
	* @param array
	* @param string | array
	* @return array
	*/
	protected static function _makeDimensional($path, $value){
		$a = [];
		$temp = &$a;
		foreach ( $path as $key ) {
			$temp = &$temp[$key];
		}
		$temp = $value;
		return $a;
	}
}