# CakeTezos

![tests](https://github.com/ziwot/cake-tezos/workflows/tests/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/ziwot/cake-tezos/v)](https://packagist.org/packages/ziwot/cake-tezos)
[![Total Downloads](https://poser.pugx.org/ziwot/cake-tezos/downloads)](https://packagist.org/packages/ziwot/cake-tezos)
[![Latest Unstable Version](https://poser.pugx.org/ziwot/cake-tezos/v/unstable)](https://packagist.org/packages/ziwot/cake-tezos)
[![License](https://poser.pugx.org/ziwot/cake-tezos/license)](https://packagist.org/packages/ziwot/cake-tezos)
[![PHP Version Require](https://poser.pugx.org/ziwot/cake-tezos/require/php)](https://packagist.org/packages/ziwot/cake-tezos)

Tezos plugin for CakePHP

This plugin provides:

- Authenticator and Identifier for the [Authentication plugin](https://book.cakephp.org/authentication/3/en/index.html).
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

Load Authenticator and Identifier :

```php
// Load Authenticator & Identifier
$service->loadAuthenticator('CakeTezos.SignedMessage', [
    'identifier' => 'CakeTezos.TezosBase',
]);
```

In a view, load the element to allow connect :

```php
<?= $this->element('CakeTezos.connect') ?>
```

The statement is configurable :

```php
<?= $this->element('CakeTezos.connect', ['statement' => 'I accept the conditions']) ?>
```
