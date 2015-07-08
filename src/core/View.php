<?php
 /**
 * Sky Framework
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions ofComposer.Autoload.ClassLoaderComposer.Autoload.ClassLoaderComposer.Autoload.ClassLoaderComposer.Autoload.ClassLoaderComposer.Autoload.ClassLoader files must retain the above copyright notice.
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

class View extends BaseClass{
	/**
	* temp html string
	* @var html
	*/
	protected static $temp = '';
	/**
	* Load View File
	* 
	* @param string
	* @param array
	* @param boolean
	* @return void|html;
	*/
	public static function load($_sky_name,$_sky_var = [],$_sky_return = false){
	
		$__prop = Loader::getName($_sky_name,'view');

		if(!file_exists($__prop->path)){
			user_error('View Not Found: '.$__prop->path);
			return;
		}
		// extract array to variable
		extract($_sky_var);
		
		ob_start();
		
		include $__prop->path;
		
		$__view = ob_get_contents();
		
		ob_end_clean();
		
		if($_sky_return){
			return $__view;
		}
		
		self::$temp .= $__view;
	}
	/**
	* get all content html
	* 
	* @return html
	*/
	public static function getTemp(){
		return self::$temp;
	}
}