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
use Sky\core\Benchmark;
use Sky\core\Router;

define('SKY_VERSION','1.0.1-alpha');

require __DIR__ . DS . 'Bootstrap.php';

//Loader::load('Sky.core.Common');
/*
|--------------------------------------------------------------------------
| Load Config Base
|--------------------------------------------------------------------------
*/
Config::load('App.Base');

/*
|--------------------------------------------------------------------------
| Starting System Benchmark
|--------------------------------------------------------------------------
*/
$BM = Loader::getClass('Sky.core.Benchmark');
$BM->mark('start_system');
/*
|--------------------------------------------------------------------------
| initialize prase URI and Router
|--------------------------------------------------------------------------
*/
$RTR = Loader::getClass('Sky.core.Router',Config::read('App.Routes'));

$class  = ucfirst($RTR->class);
$method = $RTR->method;

/*
|--------------------------------------------------------------------------
| Starting Controller Benchmark
|--------------------------------------------------------------------------
*/

$BM->mark('start_controller');


if(!file_exists($RTR->getPath())){
	user_error('404 : '.$RTR->getPath());
	exit;
}

$ns = DS.(require_once $RTR->getPath()).DS;

/*
|--------------------------------------------------------------------------
| Parse possibility namespace and class 
|--------------------------------------------------------------------------
*/
$nsclass = [
	$class,
	DS.'App'.DS.'controller'.DS.$class,
	$ns.$class
];

foreach($nsclass as $name){
	if(class_exists($name)){
		$class = $name;
		break;
	}
}

$SKY = new $class;
if(!method_exists($SKY,'__init')){
	user_error('Cant start controller');
}
/*
|--------------------------------------------------------------------------
| Start Running Controller
|--------------------------------------------------------------------------
*/
$SKY->__init($method, array_slice($RTR->segments, 2));

if(method_exists($SKY,'_beforeView')){
	$SKY->_beforeView();
}
/*
|--------------------------------------------------------------------------
| Render Output
|--------------------------------------------------------------------------
*/

$BM->mark('start_view');
echo View::getTemp();
$BM->mark('end_view');

if(method_exists($SKY,'_afterView')){
	$SKY->_afterView();
}

$BM->mark('end_controller');