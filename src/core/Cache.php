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

class Cache extends BaseClass{
	public $_config = [
		'cache_path' => APP_PATH . 'cache' . DS,
		'cache_expire' => 30
	];
	function __construct(){
		$path = $this->getConfig('cache_path');
		
		$uri = Loader::getClass('Sky.core.Router');
		
		$key = implode($uri->segments,'.').implode($_GET,'.');
		
		if(!is_dir($path)){

			Exceptions::showError('Server Error','Cache Folder Not Found at: '.$path);
			
		}
		if(!is_writable($path)){
			
			Exceptions::showError('Server Error','Unable to write Cache Folder');
		
		}
		parent::__construct();
	}
	/**
	* Write or create new cache file
	*
	* @param string
	* @param string | html
	* @return void
	*/
	function write($filename,$content){
		if(!Config::read('App.Base.enable_cache')){
			return;
		}
		$expire = time() + ($this->getConfig('cache_expiration') * 60);

		$filename = md5($filename);
		$filepath = $this->getConfig('cache_path').$filename.'.tmp';

		if ( ! $fp = @fopen($filepath, 'w')){
			Loader::getClass('Sky.core.Log')->write(400,'Unable to write cache file: '.$filepath);
			Exceptions::showError('error', "Unable to write cache file: ".$filepath);
			
		}
		if (flock($fp, LOCK_EX))
		{
			fwrite($fp, $expire.'T_SKY-->'.$content);
			flock($fp, LOCK_UN);
		}
		else{
			Loader::getClass('Sky.core.Log')->write(300,'Unable to secure a file lock for file at: '.$filepath);
			return;
		}
		fclose($fp);
		@chmod($filepath,0755);
		
	}
	/**
	* read and get cache file
	* 
	* @param string
	* @return html
	*/
	function read($filename){
		$filename = md5($filename);
		$filepath = $this->getConfig('cache_path').$filename.'.tmp';
		
		if(!file_exists($filepath)){
			return FALSE;
		}
		
		if ( ! $fp = @fopen($filepath, 'r')){
			return FALSE;
		}
		flock($fp, LOCK_SH);

		$cache = '';
		if (filesize($filepath) > 0)
		{
			$cache = fread($fp, filesize($filepath));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		// Strip out the embedded timestamp
		if ( ! preg_match("/(\d+T_SKY-->)/", $cache, $match))
		{
			return FALSE;
		}
		// Has the file expired? If so we'll delete it.
		if (time() >= trim(str_replace('TS--->', '', $match['1'])))
		{
			if (is_writable($filepath))
			{
				@unlink($filepath);
				return FALSE;
			}
		}

		// Display the cache
		return str_replace($match['0'], '', $cache);
	}
}