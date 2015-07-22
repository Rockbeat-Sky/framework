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


class Output extends BaseClass{

	protected $zlib = FALSE;
	
	public $mimes;
	
	public $final = '';
	
	public $header = [];
	
	public $cache;
	
	public function __construct(){
	
		parent::__construct();
		
		$this->_zlib = @ini_get('zlib.output_compression');
		
		$this->mimes = Config::load('App.Mimes');
		
	}
	public function append($content){
	
		$this->final .= $content;
		
		return $this;
	}
	/**
	* Set Header
	*
	* Lets you set a server header which will be outputted with the final display.
	*
	* Note:  If a file is cached, headers will not be sent.  We need to figure out
	* how to permit header data to be saved with the cache data...
	*
	* @access	public
	* @param	string
	* @param 	bool
	* @return	void
	*/
	public function setHeader($header, $replace = TRUE){
		// If zlib.output_compression is enabled it will compress the output,
		// but it will not modify the content-length header to compensate for
		// the reduction, causing the browser to hang waiting for more data.
		// We'll just skip content-length in those cases.

		if ($this->_zlib && strncasecmp($header, 'content-length', 14) == 0)
		{
			return;
		}
		
		$this->headers[] = array($header, $replace);
		return $this;
	}
	/**
	* set Content Type
	* 
	* @param string (Mimes Type, list mimes type at config/Mimes.php)
	* @return void
	*/
	public function setContentType($mimes){
		if (strpos($mimes, '/') === FALSE)
		{
			$extension = ltrim($mimes, '.');

			// Is this extension supported?
			if (isset($this->mimes[$extension]))
			{
				$mime_type =& $this->mimes[$extension];

				if (is_array($mime_type))
				{
					$mime_type = current($mime_type);
				}
			}
		}

		$header = 'Content-Type: '.$mime_type;

		$this->headers[] = array($header, TRUE);

		return $this;
	}
	/**
	* set Status Header Code
	* 
	* @param number
	* @param string
	* @return void
	*/
	public function setStatusHeader($code = 200, $text = ''){
		
		_setStatusHeader($code, $text);

		return $this;
	}
	/**
	* Return all content final
	* 
	* @return html
	*/
	public function getOutputFinal(){
		return $this->final;
	}
	/**
	* Display Output
	*
	* All "view" data is automatically put into this variable by the controller class:
	*
	* $this->final_output
	*
	* This function sends the finalized output data to the browser along
	* with any server headers and profile data.  It also stops the
	* benchmark timer so the page rendering speed and memory usage can be shown.
	*/
	public function render(){

		if(Config::read('App.Base.enable_cache') and $this->cache !== 0){
		
			$title = implode(Loader::getClass('Sky.core.Router')->segments,'.');
			
			Loader::getClass('Sky.core.Cache')->setConfig([
				'cache_expire' => $this->cache
			])->write($title,$this->final);
		}

		if (count($this->header) > 0)
		{
			foreach ($this->header as $header)
			{
				@header($header[0], $header[1]);
			}
		}
		
		echo $this->final;
		
	}
	/**
	* Cache time
	* 
	* @param number (time in minutes)
	* @return void
	*/
	public function cache($n){
		$this->cache = $n;
		return $this;
	}
}