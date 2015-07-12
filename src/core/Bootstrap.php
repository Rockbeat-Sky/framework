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
namespace Sky;
use Sky\core\Loader;
use Sky\core\Log;
/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/
$AutoLoad = require VENDOR_PATH . DS . 'autoload.php';

/*
|--------------------------------------------------------------------------
| Add Register psr4 for Application folder
|--------------------------------------------------------------------------
*/
//$AutoLoad->setPsr4('App\\', APP_PATH);

/*
|--------------------------------------------------------------------------
| Add Register psr4 for Sky folder
|--------------------------------------------------------------------------
*/
//$AutoLoad->setPsr4('Sky\\', VENDOR_PATH . 'sky' . DS . 'framework' . DS . 'src');

/*
|--------------------------------------------------------------------------
| Register Instance Loader Class
|--------------------------------------------------------------------------
|
| Register Autoload Composer Class to instance class.
|
*/
Loader::addInstance($AutoLoad);