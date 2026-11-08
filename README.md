# CakeTezos

![tests](https://github.com/ziwot/cake-tezos/workflows/tests/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/ziwot/cake-tezos/v)](https://packagist.org/packages/ziwot/cake-tezos)
[![Total Downloads](https://poser.pugx.org/ziwot/cake-tezos/downloads)](https://packagist.org/packages/ziwot/cake-tezos)
[![Latest Unstable Version](https://poser.pugx.org/ziwot/cake-tezos/v/unstable)](https://packagist.org/packages/ziwot/cake-tezos)
[![License](https://poser.pugx.org/ziwot/cake-tezos/license)](https://packagist.org/packages/ziwot/cake-tezos)
[![PHP Version Require](https://poser.pugx.org/ziwot/cake-tezos/require/php)](https://packagist.org/packages/ziwot/cake-tezos)

Tezos plugin for CakePHP: SIWT wallet authentication, network switching, and balance
display.

**Requirements:** PHP 8.2+ with the `gmp` extension and CakePHP 5+.

## Installation

```sh
composer require ziwot/cake-tezos
bin/cake plugin load CakeTezos
cake plugin assets symlink
```

Add the asset directory to `.gitignore`:

```gitignore
# Plugins
/webroot/cake_tezos
```

On production, copy the assets instead of symlinking them:

```sh
cake plugin assets copy
```

## Configuration

Configure the plugin with `Configure::write('CakeTezos', [...])` in `config/app.php`.
Only override the keys you need; defaults live in `config/bootstrap.php` and are merged
with `Hash::merge`.

```php
'CakeTezos' => [
    'defaultNetwork' => 'mainnet', // used when the Network component's network is null
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
```

Default network endpoints:

| Network | `rpcUrl` | `tzktUrl` | `networkId` |
| --- | --- | --- | --- |
| `mainnet` | `https://rpc.tzbeta.net` | `https://api.tzkt.io` | `NetXdQprcVkpaWU` |
| `shadownet` | `https://rpc.shadownet.teztnets.com` | `https://api.shadownet.tzkt.io` | `NetXsqzbfFenSTS` |
| `local` | `http://localhost:8732` | `http://localhost:5000` | `NetXtJqPyJGB6Pc` |

Override `networks.<name>` to use custom endpoints.

## Authentication

Load the authenticator and identifier through the Authentication plugin:

```php
$service->loadAuthenticator('CakeTezos.SignedMessage', [
    'identifier' => 'CakeTezos.TezosBase',
]);
```

## Network Component

Load the component in `src/Controller/AppController`:

```php
use CakeTezos\Domain\Network;

$this->loadComponent('CakeTezos.Network', [
    'network' => Network::Mainnet->value,
]);
```

The selected network is stored in the `CakeTezos.Network` session key. The component
defaults to `local`; its `network` option takes precedence over `CakeTezos.defaultNetwork`.

## Helper

Load the helper in `src/View/AppView`:

```php
$this->addHelper('CakeTezos.Tz');
```

## Elements

> **Note:** Elements and the JavaScript API require this import map at the top of the page.

```php
$this->append('script', $this->Html->importmap([
    'CakeTezos' => '/cake_tezos/dist/cake-tezos.js',
]));
```

### connect

Render the connect element:

```php
<?= $this->element('CakeTezos.connect') ?>
```

Override the statement and redirect URL:

```php
<?= $this->element('CakeTezos.connect', [
    'statement' => 'I accept the conditions',
    'redirectUrl' => '/dashboard',
]) ?>
```

### get_metadata

```php
<?= $this->element('CakeTezos.get_metadata', [
    'address' => $airdrop->address,
    'callBackUrl' => $this->Url->build([
        '_name' => 'admin:airdrops:edit',
        $airdrop->id,
    ]),
    'csrfToken' => $this->request->getAttribute('csrfToken'),

    // JavaScript UI callbacks
    'successHandler' => 'handleSuccess',
    'errorHandler' => 'handleError',
]) ?>
```

## JavaScript API

Import the wallet helpers after setting up the import map:

```js
import { getWallet, requestOperation, callContract } from "CakeTezos";
```

Each helper takes a `network` object shaped as `{ type, rpcUrl }`, as returned by
`Network::network()`:

```php
$network = \CakeTezos\Domain\Network::from(
    $this->request->getSession()->read('CakeTezos.Network'),
);

// ['type' => 'mainnet', 'rpcUrl' => 'https://rpc.tzbeta.net']
```

The optional `name` argument defaults to `window.location.hostname` and scopes the
shared Octez Connect client.

### getWallet

```js
const { client, account } = await getWallet(network);
```

Returns the active account, requesting wallet permissions if needed, and throws when no
account is available. `client` is the underlying Octez Connect `DAppClient`; `account`
contains the wallet address and account info.

### requestOperation

```js
const response = await requestOperation(network, [
    {
        kind: "transaction",
        destination: "tz1...",
        amount: "1000000",
    },
]);
```

Sends raw operation details through the connected wallet. Resolves with the Octez Connect
response (`response.transactionHash` after broadcast) without waiting for confirmation.

### callContract

```js
const { account } = await getWallet(network);

const { hash } = await callContract({
    network,
    contract: "KT1...",
    entrypoint: "transfer", // defaults to "default"
    args: {
        from: "tz1...",
        to: "tz1...",
        value: "1000000",
    },
    amount: "0", // in mutez
    sourceAddress: account.address, // optional safety check
});
```

Encodes the call with Taquito, then sends it through the wallet. Pass `args` as an object
for named or annotated parameters, or as an array for positional parameters. When
`sourceAddress` is provided, the call fails if the connected address does not match.
Returns the operation hash without waiting for confirmation.

## Development

```sh
composer install
npm install
npm run build
```

| Command | Description |
| --- | --- |
| `composer check` | Run PHPUnit, PHPStan, and PHPCS |
| `composer test` | Run PHPUnit |
| `composer stan` | Run PHPStan on `src/` |
| `composer cs-check` / `cs-fix` | Check or fix PHP coding standards |
| `npm run check` / `ci` | Check JS assets with Biome |

Run a single test:

```sh
vendor/bin/phpunit tests/TestCase/Identifier/TezosBaseIdentifierTest.php
```

## Release

Pushing to `dev/main` triggers `build-and-distribute`, which compiles assets and pushes
them to the `main` branch. Create releases from tags on that branch so the released source
includes the compiled assets:

```sh
git checkout dev/main && git pull
git checkout main && git pull
git tag v0.0.11
git push origin v0.0.11
```

The `v*` tag triggers the `Release` workflow, which creates the GitHub release with
auto-generated notes.

## Resources

- [Taquito message signing](https://tezostaquito.io/docs/signing#signing-michelson-data)
- [SIWT (Sign-In With Tezos)](https://github.com/StakeNow/SIWT)
