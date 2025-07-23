<?php
declare(strict_types=1);

namespace CakeTezos\View\Cell;

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
        $network = Network::from($this->request->getSession()->read('CakeTezos.Network'));
        $client = ClientFactory::createProto($network->rpcUrl(), '/chains/main/blocks/head');

        $identity = $this->request->getAttribute('identity')->getOriginalData();

        $mutez = (int)$client->getContextContractsByContractIdBalance($identity['address']);
        $balance = (new Mutez($mutez))->tez();

        $this->set('balance', $balance);
    }
}
