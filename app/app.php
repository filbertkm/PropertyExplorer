<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

$app = new Silex\Application();

$app->register( new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/../templates'
) );

$app->register( new Silex\Provider\DoctrineServiceProvider(), $dbConfig );

$app['api.client'] = new WikiClient\MediaWiki\ApiClient( $wiki, $user );
$app['store.entity'] = new PropertyExplorer\Store\Api\EntityApiLookup( $app['api.client'] );

$app['store.propertyinfo'] = new PropertyExplorer\Store\PropertyInfoStore(
	$app['dbs']['wikidatawiki'],
	$app['store.entity']
);

require_once __DIR__ . '/routes.php';
