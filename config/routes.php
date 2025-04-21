<?php

use Cake\Routing\Route\DashedRoute;

$routes->plugin(
	'CakeTezos',
	['path' => '/cake-tezos'],
	function ($routes) {
		$routes->get('/users/login', ['controller' => 'Users', 'action' => 'login']);
		$routes->put('/users/logout', ['controller' => 'Users', 'action' => 'logout']);
	}
);
