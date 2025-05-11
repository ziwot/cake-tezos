# CakeTezos

Tezos plugin for CakePHP

This plugin provides:

- Authenticator and Identifier for the [Authentication plugin](https://book.cakephp.org/authentication/3/en/index.html).
- Wallet connection via [SIWT](https://github.com/StakeNow/SIWT).

Install with :

```sh
composer require ziwot/cake-tezos:dev-main-built
```

Load the plugin :

```sh
bin/cake plugin load CakeTezos
```

Link assets :

```sh
cake plugin assets symlink
```

Load Authenticator and Indentifier :

```php
// Load Authenticator
$service->loadAuthenticator('CakeTezos.SignedMessage');

// Load identifier
$service->loadIdentifier('CakeTezos.TezosBase');
```
