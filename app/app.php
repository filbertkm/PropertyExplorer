<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

$app = new Silex\Application();

$app->register( new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/../templates'
) );

$app->register( new Silex\Provider\DoctrineServiceProvider(), $dbConfig );

$app['store.property'] = new PropertyExplorer\Store\PropertyInfoStore(
	$app['dbs']['wikidatawiki']
);

require_once __DIR__ . '/routes.php';
