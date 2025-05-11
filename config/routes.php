<?php

$routes->plugin(
    'CakeTezos',
    function ($routes): void {
        // SIWT expects a 'signin' endpoint
        $routes->post(
            '/signin',
            ['controller' => 'Users', 'action' => 'login'],
        );
        $routes->get(
            '/logout',
            ['controller' => 'Users', 'action' => 'logout'],
        );
    },
);
