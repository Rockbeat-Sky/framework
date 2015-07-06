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
use Sky\core\Config;

class Language extends BaseClass{
	/**
	* cache data language
	* @var array
	*/
	static $language = [];
	/**
	* Load file language
	* 
	* @param string
	* @return void
	*/
	static function load($name){

		$lang = Config::read('App.Base.language');
		
		$prop = Loader::getName($name,'locale'.DS.$lang);
		
		// if file already loaded? if so done!
		if(isset(self::$language[$prop->app])){
			return self::$language[$prop->app];
		}
		
		// is file not exists?
		if(!file_exists($prop->path)){
			user_error('Language Not Found '.$prop->path);
			exit;
		}

		self::$language[$prop->app] = (require_once $prop->path);
	}
	/**
	* get Item Language and replace string language
	* 
	* @param string
	* @param array
	* @return string
	*/
	static function item($name,$replace = []){
		
		// get info name and key
		$prop = self::engineName($name);
		
		$lang = $prop->key;
		
		// check language
		if(isset(self::$language[$prop->app][$prop->key])){
			$lang = self::$language[$prop->app][$prop->key];
		}

		$map_replace = [
			'today' => date(Config::read('App.Base.fmt_date'))
		];
		
		// replace string
		$replace = array_merge($replace,$map_replace);
		foreach($replace as $index => $text){
			$i[] = '{'.$index.'}';
			$t[] = $text;
		}
		$lang = str_replace($i,$t,$lang);

		return $lang;
	}
	/**
	* parse name app and key item language
	* 
	* @param string
	* @param object
	*/
	static function engineName($name){
		$segments = explode('.',$name);
		$app = 'App';
		if(count($segments) == 2){
			$app = current($segments);
		}
		return (object)[
			'app' => $app,
			'key' => end($segments)
		];
	}
}

function __($name,$replace = []){
	return Language::item($name,$replace);
}