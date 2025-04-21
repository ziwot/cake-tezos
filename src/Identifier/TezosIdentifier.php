<?php

declare(strict_types=1);

namespace CakeTezos\Identifier;

use ArrayAccess;
use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\Resolver\ResolverAwareTrait;
use Pezos\Keys\PubKey;

class TezosIdentifier extends AbstractIdentifier
{
	use ResolverAwareTrait;

	/**
	 * @var string
	 */
	public const CREDENTIAL_PK = 'pk';

	/**
	 * @var string
	 */
	public const CREDENTIAL_PKH = 'pkh';

	/**
	 * @var string
	 */
	public const CREDENTIAL_MESSAGE = 'message';

	/**
	 * @var string
	 */
	public const CREDENTIAL_SIGNATURE = 'signature';

	/**
	 * Default configuration.
	 * - `fields` The fields to use to identify a user by:
	 *   - `pk`: a public key
	 *   - `pkh`: the matching public key hash
	 *   - `message` the payload
	 *   - `signature` the signed payload
	 * - `resolver` The resolver implementation to use.
	 *
	 * @var array<string, mixed>
	 */
	protected array $_defaultConfig = [
		'fields' => [
			self::CREDENTIAL_PK => 'pk',
			self::CREDENTIAL_PKH => 'pkh',
			self::CREDENTIAL_MESSAGE => 'message',
			self::CREDENTIAL_SIGNATURE => 'signature',
		],
		'resolver' => 'CakeTezos.Basic',
	];

	/**
	 * @inheritDoc
	 *
	 * @param array<string, mixed> $credentials
	 *
	 * @return \ArrayAccess|array<string, mixed>|null
	 */
	public function identify(array $credentials): ArrayAccess|array|null
	{
		$fields = $this->getConfig('fields');

		if (!isset($credentials[static::CREDENTIAL_PKH])) {
			return null;
		}


		if (
			array_key_exists(static::CREDENTIAL_MESSAGE, $credentials)
			&& array_key_exists(static::CREDENTIAL_SIGNATURE, $credentials)
		) {
			$pk = $credentials[static::CREDENTIAL_PK];
			$message = $credentials[static::CREDENTIAL_MESSAGE];
			$signature = $credentials[static::CREDENTIAL_SIGNATURE];
			if (!$this->_checkSignature($pk, $message, $signature)) {
				return null;
			}
		}

		return new \ArrayObject([
			$fields[self::CREDENTIAL_PKH] => $credentials[static::CREDENTIAL_PKH],
		]);
	}

	/**
	 * @param string $pk
	 * @param string $message
	 * @param string $signature
	 *
	 * @return bool
	 */
	protected function _checkSignature(string $pk, string $message, string $signature): bool
	{
		$pubKey = PubKey::fromBase58($pk);

		if (
			!$pubKey->verifySignature($signature, $message)
			&& !$pubKey->verifySignature($signature, bin2hex($message))
		) {
			return false;
		}

		return true;
	}
}
