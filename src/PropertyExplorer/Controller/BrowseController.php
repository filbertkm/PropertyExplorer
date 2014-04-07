<?php

namespace PropertyExplorer\Controller;

use PropertyExplorer\Store\PropertyInfoStore;
use Silex\Application;
use Silex\ControllerProviderInterface;

class BrowseController implements ControllerProviderInterface {

	private $propertyInfoStore;

	public function __construct( PropertyInfoStore $propertyInfoStore ) {
		$this->propertyInfoStore = $propertyInfoStore;
	}

	public function connect( Application $app ) {
		$controller = $app['controllers_factory'];

		$controller->get( '/{id}/', array( $this, 'browseFromId' ) );
		$controller->match( '/', array( $this, 'browse' ) );

		return $controller;
	}

	public function browse( Application $app ) {
		return $this->browseFromId( $app, 0 );
	}

	public function browseFromId( Application $app, $id ) {
		$properties = $this->propertyInfoStore->getPropertyInfo( $id );

		return $app['twig']->render(
			'index.twig',
			array(
				'properties' => $properties
			)
		);
	}

}
