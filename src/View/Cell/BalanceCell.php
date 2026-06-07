<?php
declare(strict_types=1);

namespace CakeTezos\View\Cell;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\View\Cell;
use CakeTezos\Domain\Mutez;
use CakeTezos\Domain\Network;
use Pezos\Http\ClientFactory;

class BalanceCell extends Cell
{
    /**
     * @return void
     */
    public function display(): void
    {
        $cacheConfig = Configure::read('CakeTezos.cache.balance');
        $network = Network::from($this->request->getSession()->read('CakeTezos.Network'));
        $identity = $this->request->getAttribute('identity')->getOriginalData();

        $cacheKey = sprintf(
            'balance_%s_%s',
            $network->network()['type'],
            $identity['address'],
        );

        if ($cacheConfig['enabled']) {
            $balance = Cache::remember($cacheKey, function () use ($network, $identity): float {
                return $this->_fetchBalance($network, $identity['address']);
            }, $cacheConfig['config']);
        } else {
            $balance = $this->_fetchBalance($network, $identity);
        }

        $this->set('balance', $balance);
    }

    /**
     * @param \CakeTezos\Domain\Network $network
     * @param string $address
     * @return float
     */
    private function _fetchBalance(Network $network, string $address): float
    {
        $client = ClientFactory::createProto($network->rpcUrl(), '/chains/main/blocks/head');
        $response = $client->getContextContractsByContractIdBalance($address);
        $mutez = (int)(is_string($response) ? $response : '0');

        return (new Mutez($mutez))->tez();
    }
}
