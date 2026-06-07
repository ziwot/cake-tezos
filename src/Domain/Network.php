<?php
declare(strict_types=1);

namespace CakeTezos\Domain;

use Cake\Core\Configure;

/**
 * Stores defaults Tezos & Tzkt nodes
 */
enum Network: string
{
    case Mainnet = 'mainnet';
    case Shadownet = 'shadownet';
    case Local = 'local';

    /**
     * @return string
     */
    public function label(): string
    {
        return Configure::read('CakeTezos.networks.' . $this->value . '.label')
            ?? match ($this) {
                Network::Mainnet => 'Mainnet',
                Network::Shadownet => 'Shadownet',
                Network::Local => 'Local',
            };
    }

    /**
     * @return string
     */
    public function networkId(): string
    {
        return Configure::read('CakeTezos.networks.' . $this->value . '.networkId')
            ?? match ($this) {
                Network::Mainnet => 'NetXdQprcVkpaWU',
                Network::Shadownet => 'NetXsqzbfFenSTS',
                Network::Local => 'NetXtJqPyJGB6Pc',
            };
    }

    /**
     * @return array{type: string, rpcUrl: string}
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
        return Configure::read('CakeTezos.networks.' . $this->value . '.rpcUrl')
            ?? match ($this) {
                Network::Mainnet => 'https://rpc.tzbeta.net',
                Network::Shadownet => 'https://rpc.shadownet.teztnets.com',
                Network::Local => 'http://localhost:8732',
            };
    }

    /**
     * @return string
     */
    public function tzktUrl(): string
    {
        return Configure::read('CakeTezos.networks.' . $this->value . '.tzktUrl')
            ?? match ($this) {
                Network::Mainnet => 'https://api.tzkt.io',
                Network::Shadownet => 'https://api.shadownet.tzkt.io',
                Network::Local => 'http://localhost:5000',
            };
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function configured(): array
    {
        $networks = Configure::read('CakeTezos.networks') ?? [];

        return array_map(
            fn(array $network) => [
                'rpcUrl' => $network['rpcUrl'] ?? '',
                'tzktUrl' => $network['tzktUrl'] ?? '',
                'networkId' => $network['networkId'] ?? '',
                'label' => $network['label'] ?? '',
            ],
            $networks,
        );
    }
}
