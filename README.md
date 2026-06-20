# CakeTezos

![tests](https://github.com/ziwot/cake-tezos/workflows/tests/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/ziwot/cake-tezos/v)](https://packagist.org/packages/ziwot/cake-tezos)
[![Total Downloads](https://poser.pugx.org/ziwot/cake-tezos/downloads)](https://packagist.org/packages/ziwot/cake-tezos)
[![Latest Unstable Version](https://poser.pugx.org/ziwot/cake-tezos/v/unstable)](https://packagist.org/packages/ziwot/cake-tezos)
[![License](https://poser.pugx.org/ziwot/cake-tezos/license)](https://packagist.org/packages/ziwot/cake-tezos)
[![PHP Version Require](https://poser.pugx.org/ziwot/cake-tezos/require/php)](https://packagist.org/packages/ziwot/cake-tezos)

Tezos plugin for CakePHP

This plugin provides:

- Authenticator and Identifier for the [Authentication plugin](https://book.cakephp.org/authentication/4).
- Wallet connection via [SIWT](https://github.com/StakeNow/SIWT).

Install with :

```sh
composer require ziwot/cake-tezos
```

Load the plugin :

```sh
bin/cake plugin load CakeTezos
```

Link assets :

```sh
cake plugin assets symlink
```

You should also add it to your `.gitignore` :

```
# Plugins
/webroot/cake_tezos
```

Of course, when you deploy to prod, then, copy the assets :

```sh
cake plugin assets copy
```

## Configuration

The plugin is configurable via `Configure::write('CakeTezos', [...])` in your app's `config/app.php`:

```php
<?php
return [
    // ...
    'CakeTezos' => [
        'defaultNetwork' => 'mainnet',

        'networks' => [
            'mainnet' => [
                'rpcUrl' => 'https://rpc.tzbeta.net',
                'tzktUrl' => 'https://api.tzkt.io',
                'networkId' => 'NetXdQprcVkpaWU',
                'label' => 'Mainnet',
            ],
            'shadownet' => [
                'rpcUrl' => 'https://rpc.shadownet.teztnets.com',
                'tzktUrl' => 'https://api.shadownet.tzkt.io',
                'networkId' => 'NetXsqzbfFenSTS',
                'label' => 'Shadownet',
            ],
            'local' => [
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
                'config' => 'default',
            ],
        ],
    ],
];
```

You only need to specify the keys you want to override; the rest use the defaults above.

### Per-element overrides

The `connect` element accepts `statement` and `redirectUrl` overrides:

```php
<?= $this->element('CakeTezos.connect', [
    'statement' => 'I accept the conditions',
    'redirectUrl' => '/dashboard',
]) ?>
```

## Authentication

Load Authenticator and Identifier :

```php
// Load Authenticator & Identifier
$service->loadAuthenticator('CakeTezos.SignedMessage', [
    'identifier' => 'CakeTezos.TezosBase',
]);
```

## Network Component

Load Component in (`src/Controller/AppController`)  :

```php
$this->loadComponent('CakeTezos.Network', [
    'network' => Network::Mainnet->value,
]);
```

## Helpers

Load Helper in (`src/View/AppView`) :

```php
$this->addHelper('CakeTezos.Tz');
```

## Elements

:warning: To be able to use the elements, you need to import the js module,
you must add this at the top of your page :

```php
$this->append('script', $this->Html->importmap([
    'CakeTezos' => '/cake_tezos/dist/cake-tezos.js'
]));
```

### connect

In a view, load the element to allow connect :

```php
<?= $this->element('CakeTezos.connect') ?>
```

The statement is configurable :

```php
<?= $this->element('CakeTezos.connect', ['statement' => 'I accept the conditions']) ?>
```

### get_metadata

```php
<?= $this->element('CakeTezos.get_metadata', [
        'address' => $airdrop->address,
        'callBackUrl' => $this->Url->build([
            '_name' => 'admin:airdrops:edit',
            $airdrop->id
        ]),
        'csrfToken' => $this->request->getAttribute('csrfToken'),

        // js functions for UI
        'successHandler' => 'handleSuccess',
        'errorHandler' => 'handleError',
]) ?>
```

## Development

1. Install dependencies : `composer install && npm install`
2. Build assets : `npm run build`
