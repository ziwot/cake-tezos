# CakeTezos

CakePHP 5 plugin for Tezos blockchain integration (SIWT wallet auth, network switching).

## Commands

| Command | What it runs |
|---|---|
| `composer check` | `phpunit` → `phpstan analyse` → `phpcs` |
| `composer test` | `phpunit` |
| `composer stan` | `phpstan analyse` |
| `composer cs-check` / `cs-fix` | `phpcs` / `phpcbf` (CakePHP standard) |
| `npm run build` | esbuild `assets/main.js` → `webroot/dist/cake-tezos.js` |
| `npm run check` / `ci` | `biome check` / `biome ci` |

## PHPStan quirks

- Level 8, uses `cakedc/cakephp-phpstan` for CakePHP rules.
- Plugin's `AppController` extends `App\Controller\AppController` — a class that only exists via `class_alias` in `tests/bootstrap.php`. PHPStan cannot resolve it from runtime aliases, so `tests/test_app/src/AppControllerAlias.php` is added to `scanFiles` in `phpstan.neon.dist`. This file's class name doesn't match its filename, so it's excluded from phpcs in `phpcs.xml`. If you change the base controller, update both files.

## Test app

- `tests/test_app/` provides `TestApp\Controller\AppController` (extends `Cake\Controller\Controller`).
- `tests/bootstrap.php` aliases it to `App\Controller\AppController` so the plugin resolves at test time.
- PHP requires the `gmp` extension (Tezos cryptography).

## Architecture

- **Plugin**: `CakeTezos\CakeTezosPlugin` (routes enabled, no middleware/console/services).
- **Controllers**: `NetworkController` (select network), `WalletController` (login/logout via SIWT).
- **Auth**: `SignedMessageAuthenticator` + `TezosBaseIdentifier` — verifies Ed25519 signatures from POST data.
- **Component**: `NetworkComponent` — persists selected network in session.
- **Domain**: `Network` enum (Mainnet/Shadownet/Local), `Mutez` value object.
- **View**: `TzHelper`, `BalanceCell`.
- **Templates**: `connect`, `get_metadata`, `network_selector` elements.
- **Routes** (under `/cake-tezos`): `POST /signin` → Wallet::login, `GET /logout` → Wallet::logout, `POST /network` → Network::select.
- **JS assets**: Vanilla JS in `assets/`, bundled by esbuild with `esbuild-plugin-polyfill-node` for browser ESM output.
- **Config**: `Configure::write('CakeTezos', ...)` — defaults in `config/bootstrap.php`, merged with user config.

## No database

Plugin has no models, tables, or fixtures. Tests are pure unit tests (`tests/TestCase/Identifier/`).

## CI

- `tests.yml`: runs `composer check` on push to main (PHP 8.2 + gmp, Node 24).
- `build.yml`: deploys built assets to `$ref-built` branch.
