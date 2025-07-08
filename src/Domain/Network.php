<?php

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
    public function rpcUrl(): string
    {
        return match ($this) {
            Network::Mainnet => 'https://rpc.tzbeta.net',
            Network::Ghostnet => 'https://ghostnet.tezos.marigold.dev',
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
