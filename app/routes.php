<?php

$app->mount( '/', new PropertyExplorer\Controller\IndexController( $app['store.property'] ) );
