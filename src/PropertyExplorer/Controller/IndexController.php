<?php

namespace PropertyExplorer\Controller;

use PropertyExplorer\Store\PropertyInfoStore;
use Silex\Application;
use Silex\ControllerProviderInterface;

class IndexController implements ControllerProviderInterface {

	private $propertyInfoStore;

	public function __construct( PropertyInfoStore $propertyInfoStore ) {
		$this->propertyStore = $propertyInfoStore;
	}

	public function connect( Application $app ) {
		$controller = $app['controllers_factory'];

		$controller->match( '/', array( $this, 'indexDisplay' ) );

		return $controller;
	}

	public function indexDisplay( Application $app ) {
		$properties = $this->propertyInfoStore->getPropertyInfo();

		return $app['twig']->render(
			'index.twig',
			array(
				'properties' => $properties
			)
		);
	}

}
