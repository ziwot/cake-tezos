<?php
declare(strict_types=1);

namespace CakeTezos\Identifier;

use ArrayAccess;
use ArrayObject;
use Authentication\Identifier\AbstractIdentifier;
use Pezos\Keys\PubKey;

/**
 * An identifier that returns the address, given a message and its signature
 */
class TezosBaseIdentifier extends AbstractIdentifier
{
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
     *
     * @var array<string, string>
     */
    protected array $_defaultConfig = [
        'addressField' => 'address',
    ];

    /**
     * @inheritDoc
     */
    public function identify(array $credentials): ArrayAccess|array|null
    {
        if (
            !isset($credentials[static::CREDENTIAL_PK])
            || !isset($credentials[static::CREDENTIAL_PKH])
            || !isset($credentials[static::CREDENTIAL_MESSAGE])
            || !isset($credentials[static::CREDENTIAL_SIGNATURE])
        ) {
            return null;
        }

        return $this->_checkSignature(
            $credentials[static::CREDENTIAL_PK],
            $credentials[static::CREDENTIAL_MESSAGE],
            $credentials[static::CREDENTIAL_SIGNATURE],
        ) ? new ArrayObject([
            $this->getConfig('addressField') => $credentials[static::CREDENTIAL_PKH],
        ]) : null;
    }

    /**
     * @param string $pk
     * @param string $message
     * @param string $signature
     * @return bool
     */
    private function _checkSignature(string $pk, string $message, string $signature): bool
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
