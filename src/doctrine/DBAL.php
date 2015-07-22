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
namespace Sky\doctrine;
use Sky\core\BaseClass;
use Sky\core\Config;
use Sky\core\Loader;
use Sky\core\Exceptions;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

final DBAL extends BaseClass{
	
	protected $db;
	
	function __construct(array $params = [], Configuration $config = null, EventManager $eventManager = null){
		
		if(!$params){
			Config::load('App.DB');
			$params = Config::read('App.DB.connect');
		}
		
		parent::__construct();

		$this->db = DriverManager::getConnection($params, new Configuration);
		
		if($this->db->connect()){
			Loader::getClass('Sky.core.Log')->write(100,'database connected');
		}
		
	}
	function __call($key,$params){
	
		if(!method_exists($this->db,$key)){
		
			Exceptions::showError('Database Error',$key.' Method Not Exists');
			
		}
		return call_user_func_array([$this->db,$key], $params);
	
	}
}