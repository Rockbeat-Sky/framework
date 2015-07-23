<?php
namespace Sky\doctrine;
use Sky\core\BaseClass;
use Sky\core\Config;
use Sky\core\Exceptions;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class ORM extends BaseClass{
	
	public $em;
	public $config;
	
	function __construct(){
		
		parent::__construct();
		Config::load('App.DB');
		$entityType = 'Create'.ucfirst(Config::read('App.DB.entity.type')).'Configuration';
		
		if(!method_exists($setup = new Setup,$entityType)){
			Exceptions::showError('Server Error','Invalid Entity Type use Annotation, XML or YAML');
		}
		$this->config = $setup->$entityType(Config::read('App.DB.entity.path'),true);
		
		$this->em = EntityManager::create(
			Config::read('App.DB.connect'),
			$this->config
		);
	}
	
	function __call($key,$args){

		return call_user_func_array([$this->em,$key],$args);
	}
	
	function getEntityManager(){
		return $this->em;
	}
}