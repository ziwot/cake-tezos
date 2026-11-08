# CakeTezos

CakePHP 5 plugin for Tezos integration: SIWT wallet auth, network switching, balance display.

## Commands

Setup: `composer install && npm install` (PHP needs the `gmp` extension).

| Command | What it runs |
|---|---|
| `composer check` | `phpunit` → `phpstan analyse` → `phpcs` |
| `composer test` | `phpunit` |
| `composer stan` | `phpstan analyse` (analyzes `src/` only) |
| `composer cs-check` / `cs-fix` | `phpcs` / `phpcbf` (CakePHP standard) |
| `npm run build` | esbuild `assets/main.js` → `webroot/dist/cake-tezos.js` |
| `npm run check` / `ci` | `biome check` / `biome ci` (only `assets/**/*.js`) |

Run one test: `vendor/bin/phpunit tests/TestCase/Identifier/TezosBaseIdentifierTest.php` or
`composer test -- --filter testIdentify`.

## Test / PHPStan quirks

- No database: no models, tables, or fixtures. Tests are pure unit tests under `tests/TestCase/`.
- `tests/test_app/` provides `TestApp\Controller\AppController`; `tests/bootstrap.php` loads `config/bootstrap.php` and then `return`s before its `class_alias` call, so the runtime `App\Controller\AppController` alias never executes. Only identifier tests exist today; controller tests would need that autoloading fixed.
- PHPStan level 8 with `cakedc/cakephp-phpstan`. The plugin's `AppController` extends `App\Controller\AppController`, so `tests/test_app/src/AppControllerAlias.php` declares that class and is added to `scanFiles`. Its class name doesn't match its filename, so `phpcs.xml` excludes it. If you change the base controller, update both files.

## Architecture

- **Plugin**: `CakeTezos\CakeTezosPlugin` — bootstrap + routes enabled; middleware, console, services, events disabled.
- **Controllers**: `NetworkController::select`, `WalletController::{login,logout,refreshBalance}`.
- **Auth**: `SignedMessageAuthenticator` reads POST fields `pk`/`pkh`/`message`/`signature` from `/cake-tezos/signin`; `TezosBaseIdentifier` verifies via `Pezos\Keys\PubKey` (tries both raw and hex-encoded message).
- **Component**: `NetworkComponent` stores the selected network in session key `CakeTezos.Network`. Its default is `local`, and its configured `network` shadows `CakeTezos.defaultNetwork`, so that config default only applies if the component network is explicitly nulled.
- **Domain**: `Network` enum (Mainnet/Shadownet/Local) with config-aware `rpcUrl()`/`tzktUrl()`/`networkId()`/`label()`; `Mutez` value object.
- **View**: `TzHelper` (address shortening), `BalanceCell` (RPC balance, cached via `CakeTezos.cache.balance`).
- **Templates**: elements `connect`, `get_metadata`, `network_selector`; cell `Balance/display`.
- **Routes** under `/cake-tezos`: `POST /signin` → login, `GET /logout`, `POST /network` → select, `POST /refresh-balance` → refreshBalance.
- **JS**: vanilla ESM in `assets/`; `wallet.js` wraps Octez Connect (`@tezos-x/octez.connect-sdk` v5) and exposes `getWallet`/`callContract`/`requestOperation`; `main.js` exports `connect`/`getMetadata`. Bundled only via `npm run build`; `webroot/dist/` is gitignored on `dev/*` branches.
- **Config**: defaults in `config/bootstrap.php`, merged with user `Configure::write('CakeTezos', ...)` via `Hash::merge`.

## CI / release

- `tests.yml`: push to `dev/main` only. Runs `composer check` (PHP 8.2 + gmp) and a separate JS job (Node 24) that only runs `npm ci`, not Biome.
- `build.yml`: triggers on pushes to `dev/main`, `dev/develop`, `dev/feature/**`, `dev/hotfix/**` (path-filtered to `src/`, `assets/`, manifests, workflow) plus `workflow_dispatch`. Uses `inpsyde/reusable-workflows` with Node 22; `PRE_SCRIPT` strips `composer.json`'s `version` so Packagist uses tags. Compiled assets land on the corresponding non-`dev/` branch.
- `release.yml`: on `v*` tag push, runs `gh release create --generate-notes`. Tags must be created on the `main` build branch. `.github` is intentionally in the build output (not `.distignore`) so tag-push workflows resolve from the tag tree.
