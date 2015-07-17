<?php
namespace Sky\doctrine;
use Sky\core\BaseClass;
use Sky\core\Config;
use Sky\core\Exceptions;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class ORM extends BaseClass{
	function __construct(){
		Config::load('App.DB');
		$entityType = 'Create'.ucfirst(Config::read('App.DB.entity_type')).'MetadataConfiguration';
		
		if(!method_exists($setup = new Setup,$entityType)){
			Exceptions::showError('Server Error','Invalid Entity Type use Annotation, XML or YAML');
		}

		$entityManager = EntityManager::create(
			Config::read('App.DB.connect'),
			$setup->$entityType([APP_PATH],true)
		);
		print_r( \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager));
	}
}