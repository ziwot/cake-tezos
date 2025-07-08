<?php

$routes->plugin(
    'CakeTezos',
    function ($routes): void {
        // SIWT expects a 'signin' endpoint
        $routes->post(
            '/signin',
            ['controller' => 'Wallet', 'action' => 'login'],
        );
        $routes->get(
            '/logout',
            ['controller' => 'Wallet', 'action' => 'logout'],
        );

        $routes->post(
            '/network',
            ['controller' => 'Network', 'action' => 'select'],
        );
    },
);
