<?php
namespace Sky;
use Sky\core\Loader;
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
//$AutoLoad->setPsr4('Sky\\', VENDOR_PATH . 'sky' . DS . 'framework' . DS . 'src' . DS);

/*
|--------------------------------------------------------------------------
| Register Instance Loader Class
|--------------------------------------------------------------------------
|
| Register Autoload Composer Class to instance class.
|
*/
Loader::addInstance($AutoLoad);