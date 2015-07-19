<?php
namespace Sky\doctrine;
use Sky\core\BaseClass;
use Sky\core\Config;
use Sky\core\Exceptions;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class ORM {
	public $entity;
	function __construct(){

		Config::load('App.DB');
		$entityType = 'Create'.ucfirst(Config::read('App.DB.entity.type')).'Configuration';
		
		if(!method_exists($setup = new Setup,$entityType)){
			Exceptions::showError('Server Error','Invalid Entity Type use Annotation, XML or YAML');
		}

		$this->entity = EntityManager::create(
			Config::read('App.DB.connect'),
			$setup->$entityType(Config::read('App.DB.entity.path'),true)
		);
	}
}