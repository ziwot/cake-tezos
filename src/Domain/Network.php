<?php
declare(strict_types=1);

namespace CakeTezos\Domain;

/**
 * Stores defaults Tezos & Tzkt nodes
 */
enum Network: string
{
    case Mainnet = 'mainnet';
    case Ghostnet = 'ghostnet';
    case Local = 'local';

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            Network::Mainnet => 'Mainnet',
            Network::Ghostnet => 'Ghostnet',
            Network::Local => 'Local',
        };
    }

    /**
     * @return string
     */
    public function networkId(): string
    {
        return match ($this) {
            Network::Mainnet => 'NetXdQprcVkpaWU',
            Network::Ghostnet => 'NetXnHfVqm9iesp',
            Network::Local => 'NetXtJqPyJGB6Pc',
        };
    }

    /**
     * @return array
     */
    public function network(): array
    {
        return [
            'type' => $this === Network::Local
                ? 'custom'
                : $this->value,
            'rpcUrl' => $this->rpcUrl(),
        ];
    }

    /**
     * @return string
     */
    public function rpcUrl(): string
    {
        return match ($this) {
            Network::Mainnet => 'https://rpc.tzbeta.net',
            Network::Ghostnet => 'https://rpc.ghostnet.teztnets.com',
            Network::Local => 'http://localhost:20000',
        };
    }

    /**
     * @return string
     */
    public function tzktUrl(): string
    {
        return match ($this) {
            Network::Mainnet => 'https://api.tzkt.io',
            Network::Ghostnet => 'https://api.ghostnet.tzkt.io',
            Network::Local => 'http://localhost:5000',
        };
    }
}
