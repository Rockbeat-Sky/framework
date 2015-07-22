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
use Sky\core\Log;

define('SKY_VERSION','1.0.1-alpha');

/*
|--------------------------------------------------------------------------
| Get Host Name
|--------------------------------------------------------------------------
*/
if (isset($_SERVER['HTTP_HOST'])){
	
	$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
	$base_url .= '://'. $_SERVER['HTTP_HOST'];
	$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
	
}
else{
	$base_url = 'http://localhost/';
}

define('HOST_NAME',$base_url);

require_once __DIR__ . DS . 'Bootstrap.php';

require_once __DIR__ . DS . 'Global.php';
/*
|--------------------------------------------------------------------------
| Load Config Base
|--------------------------------------------------------------------------
*/
Config::load('App.Base');

date_default_timezone_set(Config::read('App.Base.default_timezone'));

$LOG = Loader::getClass('Sky.core.Log');

$LOG->write(100,'---------------Starting Processing -----------------------');
/*
|--------------------------------------------------------------------------
| Starting System Benchmark
|--------------------------------------------------------------------------
*/

$BM = Loader::getClass('Sky.core.Benchmark');
$BM->mark('start_sky');
/*
|--------------------------------------------------------------------------
| initialize prase URI and Router
|--------------------------------------------------------------------------
*/
Config::load('App.Routes');

$RTR = Loader::getClass('Sky.core.Router',Config::read('App.Routes'));

$class  = ucfirst($RTR->class);
$method = $RTR->method;

$OUT = Loader::getClass('Sky.core.Output');

if(Config::read('App.Base.enable_cache')){
	
	$CH = Loader::getClass('Sky.core.Cache');
	if($CH_OUT = $CH->read(implode($RTR->segments,'.'))){
		
		$OUT->append(View::getTemp());
		$LOG->write(100,'Cache Rendered');
		goto OUTPUT;
	}
}


/*
|--------------------------------------------------------------------------
| Starting Controller Benchmark
|--------------------------------------------------------------------------
*/

$BM->mark('start_controller');

$PATH = $RTR->getPath();

if(!file_exists($PATH)){

<<<<<<< HEAD
=======
if(!file_exists($RTR->getPath())){

>>>>>>> origin/master
	Exceptions::show404();

}

$ns = DS.(require_once $PATH).DS;

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

if(!method_exists($SKY,'__init') or !method_exists($SKY,'__output')){

	$LOG->write(500,$class.' Missing Method __init or __output');
	
	Exceptions::showError('Server Error','Missing Method __init or __output');
	
}
/*
|--------------------------------------------------------------------------
| Start Running Controller
|--------------------------------------------------------------------------
*/
$SKY->__init($method, array_slice($RTR->segments, 2));


/*
|--------------------------------------------------------------------------
| Render Output Controller
|--------------------------------------------------------------------------
*/
OUTPUT:
if(isset($SKY)){
	$SKY->__output(View::getTemp());
}
else{
	$OUT->render();
}
// Banzai!!!!!
$LOG->write(100,'*********** Completed at : '.$BM->elapsedTime('start_sky').' | '.$BM->memoryUsage().' ***********');