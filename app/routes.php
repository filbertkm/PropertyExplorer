<?php

$app->mount( '/browse', new PropertyExplorer\Controller\BrowseController( $app['store.propertyinfo'] ) );
$app->mount( '/', new PropertyExplorer\Controller\IndexController( $app['store.propertyinfo'] ) );
