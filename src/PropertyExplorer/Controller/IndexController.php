<?php

namespace PropertyExplorer\Controller;

use PropertyExplorer\Store\PropertyInfoStore;
use Silex\Application;
use Silex\ControllerProviderInterface;

class IndexController implements ControllerProviderInterface {

	private $propertyInfoStore;

	public function __construct( PropertyInfoStore $propertyInfoStore ) {
		$this->propertyInfoStore = $propertyInfoStore;
	}

	public function connect( Application $app ) {
		$controller = $app['controllers_factory'];

		$controller->match( '/', array( $this, 'index' ) );

		return $controller;
	}

	public function index( Application $app ) {
		$properties = $this->propertyInfoStore->getPropertyInfo( 0 );

		return $app['twig']->render(
			'index.twig',
			array(
				'properties' => $properties
			)
		);
	}

}
