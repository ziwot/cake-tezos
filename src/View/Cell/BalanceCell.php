<?php
declare(strict_types=1);

namespace CakeTezos\View\Cell;

use Cake\View\Cell;
use CakeTezos\Domain\Mutez;
use CakeTezos\Domain\Network;
use Tzkt\Api\AccountsApi;
use Tzkt\Configuration;
use function Tzkt\get_client;

class BalanceCell extends Cell
{
    /**
     * @return void
     */
    public function display(): void
    {
        $network = Network::from($this->request->getSession()->read('CakeTezos.Network'));
        $config = (new Configuration())->setHost($network->tzktUrl());

        $AccountsApi = new AccountsApi(get_client(), $config);

        $identity = $this->request->getAttribute('identity')->getOriginalData();
        $mutez = $AccountsApi->accountsGetBalance($identity['address']);
        $balance = (new Mutez($mutez))->tez();

        $this->set('balance', $balance);
    }
}
