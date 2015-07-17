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

class Benchmark{

	/**
	 * List of all benchmark markers and when they were added
	 *
	 * @var array
	 */
	static $marker = [];

	// --------------------------------------------------------------------

	/**
	 * Set a benchmark marker
	 *
	 * Multiple calls to this function can be made so that several
	 * execution points can be timed
	 *
	 * @access	public
	 * @param	string	$name	name of the marker
	 * @return	void
	 */
	static function mark($name){
		self::$marker[$name] = microtime();
	}

	// --------------------------------------------------------------------

	/**
	 * Calculates the time difference between two marked points.
	 *
	 * If the first parameter is empty this function instead returns the
	 * {elapsed_time} pseudo-variable. This permits the full system
	 * execution time to be shown in a template. The output class will
	 * swap the real value for this variable.
	 *
	 * @access	public
	 * @param	string	a particular marked point
	 * @param	string	a particular marked point
	 * @param	integer	the number of decimal places
	 * @return	mixed
	 */
	function elapsedTime($point1, $point2 = '', $decimals = 4){

		if ( ! isset(self::$marker[$point1])){
			return '';
		}

		if ( ! isset(self::$marker[$point2])){
			self::$marker[$point2] = microtime();
		}

		list($sm, $ss) = explode(' ', self::$marker[$point1]);
		list($em, $es) = explode(' ', self::$marker[$point2]);

		return number_format(($em + $es) - ($sm + $ss), $decimals);
	}
	function elapsedPoint($decimals = 4){
	
	}
	/**
	* get CPU Memory Usage
	* 
	* @return string
	*/
	function MemoryUsage(){
	
		$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';
		
		return $memory;
	} 
}