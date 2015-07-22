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

class Router extends BaseClass{
	/**
	 * Config class
	 *
	 * @var object
	 * @access public
	 */
	public $_config = [
		'class_surfix' => 'Controller',
		'controller_folder' => 'controller',
		'default_controller' => 'welcome',
		'default_method'	=> 'index',
		'404_override' => '',
		'map' => []
	];

	/**
	 * List of error routes
	 *
	 * @var array
	 * @access public
	 */
	public $error_routes	= array();
	/**
	 * Current class name
	 *
	 * @var string
	 * @access public
	 */
	public $class			= '';
	/**
	 * Current method name
	 *
	 * @var string
	 * @access public
	 */
	public $method			= '';
	/**
	 * Sub-directory that contains the requested controller class
	 *
	 * @var string
	 * @access public
	 */
	public $directory		= '';
	/**
	 * Constructor
	 *
	 * Runs the route mapping function.
	 */
	public $segments = [];
	
	public $app_path = APP_PATH;
	 
	function __construct($conf = []){
	
		parent::__construct($conf);
		
		$this->uri = Loader::getClass('Sky.core.URI');
		
		$this->method = $this->getConfig('default_method');
		$this->setClass($this->getConfig('default_controller'));
		
		if (!$this->class){
			Exceptions::showError("Server Error","Unable to determine what should be displayed. A default route has not been specified in the routing file.");
			exit;
		}
		$this->_initialize();
		
	}

	// --------------------------------------------------------------------

	/**
	 * Set the route mapping
	 *
	 * This function determines what should be served based on the URI request,
	 * as well as any "routes" that have been set in the routing config file.
	 *
	 * @access	private
	 * @return	void
	 */
	protected function _initialize(){

		// Fetch the complete URI string
		$this->uri->_fetch_uri_string();
		
		// Do we need to remove the URL suffix?
		$this->uri->_remove_url_suffix();

		// Compile the segments into an array
		$this->uri->_explode_segments();

		// Set Segments
		$this->segments = $this->uri->segments;

<<<<<<< HEAD

=======
>>>>>>> origin/master
		// Parse any custom routing that may exist
		$this->_parseRoutes();

		// Re-index the segment array so that it starts with 1 rather than 0
		$this->uri->_reindex_segments();

	}


	// --------------------------------------------------------------------

	/**
	 * Set the Route
	 *
	 * This function takes an array of URI segments as
	 * input, and sets the current class/method
	 *
	 * @access	private
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	protected function _setRequest($segments = array()){
		$segments = $this->_validateRequest($segments);
		if (count($segments) == 0){
			return $this->uri->_reindex_segments();
		}

		$this->setClass($segments[0]);

		if (isset($segments[1]))
		{
			// A standard method request
			$this->method = $segments[1];
		}
		else
		{
			// This lets the "routed" segment array identify that the default
			// index method is being used.
			$segments[1] = 'index';
		}

		// Update our "routed" segment array to contain the segments.
		// Note: If there is no custom routing, this array will be
		// identical to $this->uri->segments
		$this->uri->rsegments = $segments;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the supplied segments.  Attempts to determine the path to
	 * the controller.
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */
<<<<<<< HEAD
	protected function _validateRequest($segments){

		if(count($segments) == 0){
			$segments = [
				$this->getConfig('default_controller'),
				$this->getConfig('default_method')
			];
=======
	function _validate_request($segments){
		if (count($segments) == 0){
			Exceptions::showError('Server Error','No Segments');
			exit;
>>>>>>> origin/master
		}
		
		$controller_path = $this->app_path.$this->getConfig('controller_folder').DS;

		// Does the requested controller exist in the root folder?
		if (file_exists($controller_path.ucfirst($segments[0]).$this->getConfig('class_surfix').'.php')){
			return $segments;
		}

		// Is the controller in a sub-folder?
		if (is_dir($controller_path.$segments[0])){
			// Set the directory and remove it from the segment array
			$this->set_directory($segments[0]);

			$segments = array_slice($segments, 1);

			if (count($segments) > 0){
				// Does the requested controller exist in the sub-folder?
				if ( ! file_exists($controller_path.$this->directory.$segments[0].$this->getConfig('class_surfix').'.php')){
					if ( ! $this->getConfig('404_override')){
						$x = explode('/', $this->getConfig('404_override'));
						$this->set_directory('');
						$this->setClass($x[0]);
						$this->method = isset($x[1]) ? $x[1] : 'index';

						return $x;
					}
					else{
						show_404($this->fetch_directory().$segments[0]);
					}
				}
			}
			else
			{
				// Is the method being specified in the route?
				if (strpos($this->class, '/') !== FALSE)
				{
					$x = explode('/', $this->default_controller);

					$this->setClass($x[0]);
					$this->method = $x[1];
				}

				// Does the default controller exist in the sub-folder?
				if ( ! file_exists($controller_path.$this->directory.$this->class.'.php'))
				{
					$this->directory = '';
					return [];
				}

			}

			return $segments;
		}
	}

	// --------------------------------------------------------------------

	/**
	 *  Parse Routes
	 *
	 * This function matches any routes that may exist in
	 * the config/routes.php file against the URI to
	 * determine if the class/method need to be remapped.
	 *
	 * @access	private
	 * @return	void
	 */
	function _parseRoutes(){
		$routes = $this->getConfig('map');
		
		$uri = implode('/',$this->segments);
<<<<<<< HEAD
		// Is have route to vendor?
		if(isset($routes['root:vendor'])){
			$this->app_path = VENDOR_PATH.$routes['root:vendor'];
		}
		elseif(isset($routes[current($this->segments).':vendor'])){
			$this->app_path = VENDOR_PATH.$routes[current($this->segments).':vendor'];
=======

		if(isset($routes[$this->uri->segments[0].'->vendor'])){
			$this->app_path = VENDOR_PATH.$routes[$this->uri->segments[0].'->vendor'].DS;
>>>>>>> origin/master
			array_shift($this->segments);
			if(count($this->segments) == 0){
				$this->segments = [
					$this->getConfig('default_controller'),
					$this->getConfig('default_method')
				];
			}
			//return $this->_setRequest($this->segments);
		}
		
		// Is there a literal match?  If so we're done
		elseif (isset($routes[$uri])){
			explode('/', $routes[$uri]);
			return $this->_setRequest(explode('/', $routes[$uri]));
		}
		
		// Loop through the route array looking for wild-cards
		foreach ($routes as $key => $val){
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri))
			{
				// Do we have a back-reference?
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}

				return $this->_setRequest(explode('/', $val));
			}
		}

		
		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route

		$this->_setRequest($this->segments);
		
	}
	// --------------------------------------------------------------------
	/**
	* Set class name
	* 
	* @param string
	* @return void
	*/
	function setClass($class){
		
		$this->class = ucfirst($class).$this->getConfig('class_surfix');
	}
	/**
	 *  Set the directory name
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function set_directory($dir){
		$this->directory = str_replace(array('/', '.'), '', $dir).DS;
		return $this;
	}

	// --------------------------------------------------------------------

	public function getPath(){
		
		return $this->app_path.$this->getConfig('controller_folder').DS.$this->directory.$this->class.'.php';
	}
}
