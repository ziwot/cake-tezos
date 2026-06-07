<?php
declare(strict_types=1);

use Cake\Core\Configure;
use Cake\Utility\Hash;
use CakeTezos\Domain\Network;

$defaults = [
    'defaultNetwork' => Network::Local->value,
    'networks' => [
        Network::Mainnet->value => [
            'rpcUrl' => 'https://rpc.tzbeta.net',
            'tzktUrl' => 'https://api.tzkt.io',
            'networkId' => 'NetXdQprcVkpaWU',
            'label' => 'Mainnet',
        ],
        Network::Shadownet->value => [
            'rpcUrl' => 'https://rpc.shadownet.teztnets.com',
            'tzktUrl' => 'https://api.shadownet.tzkt.io',
            'networkId' => 'NetXsqzbfFenSTS',
            'label' => 'Shadownet',
        ],
        Network::Local->value => [
            'rpcUrl' => 'http://localhost:8732',
            'tzktUrl' => 'http://localhost:5000',
            'networkId' => 'NetXtJqPyJGB6Pc',
            'label' => 'Local',
        ],
    ],
    'redirect' => [
        'afterLogin' => '/',
        'afterLogout' => ['_name' => 'homepage'],
    ],
    'siwt' => [
        'statement' => 'I accept the Terms of Service',
    ],
    'cache' => [
        'balance' => [
            'enabled' => true,
            'duration' => '+5 minutes',
            'config' => 'default',
        ],
    ],
];

$userConfig = Configure::read('CakeTezos') ?? [];
Configure::write('CakeTezos', Hash::merge($defaults, $userConfig));
