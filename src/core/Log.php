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
use Sky\core\Exceptions;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log{

	public $handler= [];
	
	public $log = [];
	
	function __construct($name = 'SKY'){

		$date = date(Config::read('App.Base.log_split_time'));
		
		$path = Config::read('App.Base.log_path');
		
		if(!is_dir($path)){
		
			Exceptions::showError('Server Error','Missing Log Folder. to fix this error, create folder at '.$path);
		}
		if(is_array($log_file = Config::read('App.Base.log_filename'))){
			
			foreach(Config::read('App.Base.log_filename') as $level => $file){	
			
				if(in_array($level, Config::read('App.Base.enable_log'))){
				
					$this->log[$level] = new Logger($name);
					
					$this->log[$level]->pushHandler(new StreamHandler($path.$date.' '.$file,$level));
				}
			}
		}
	}
	/**
	* Writing log
	* 
	* @param number level log
	* @param string
	* @param array
	*/
	function write($level,$text,$context = []){
		if(isset($this->log[$level])){
		
			$log = $this->log[$level];

			$log->addRecord($level,$text,$context);
		}
	}
	/*
	* add handler
	*
	* @param number level log
	* @param object Monolog\Handler\HandlerInterface;
	*/
	function addHandler($level,HandlerInterface $handler){
	
		$this->log[$level]->pushHandler($handler);
		
	}
}