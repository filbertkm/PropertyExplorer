<?php

namespace PropertyExplorer\Controller;

use PropertyExplorer\Store\PropertyInfoStore;
use Silex\Application;
use Silex\ControllerProviderInterface;

class IndexController implements ControllerProviderInterface {

	private $propertyStore;

	public function __construct( PropertyInfoStore $propertyStore ) {
		$this->propertyStore = $propertyStore;
	}

	public function connect( Application $app ) {
		$controller = $app['controllers_factory'];

		$controller->match( '/', array( $this, 'indexDisplay' ) );

		return $controller;
	}

	public function indexDisplay( Application $app ) {
		$properties = $this->propertyStore->getProperties();

		return $app['twig']->render(
			'index.twig',
			array(
				'properties' => $properties
			)
		);
	}

	public function indexForm( Application $app ) {
		$data = array(
			'category' => ''
		);

		$form = $app['form.factory']->createBuilder( 'form', $data )
			->add( 'category' )
			->getForm();

		$form->handleRequest( $app['request'] );

		if ( $form->isValid() ) {
			$data = $form->getData();
			return $app->redirect( '/category/' . $data['category'] );
		}

		return $app['twig']->render(
			'index_form.twig',
			array(
				'form' => $form->createView()
			)
		);
	}

}
