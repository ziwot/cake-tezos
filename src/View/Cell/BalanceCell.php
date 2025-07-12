<?php
declare(strict_types=1);

namespace CakeTezos\View\Cell;

use Cake\View\Cell;
use CakeTezos\Domain\Mutez;
use Tzkt\Api\AccountsApi;
use function Tzkt\get_client;

class BalanceCell extends Cell
{
    /**
     * @param string $network
     * @return void
     */
    public function display(string $network = 'local'): void
    {
        $AccountsApi = new AccountsApi(get_client());

        $identity = $this->request->getAttribute('identity')->getOriginalData();
        $mutez = $AccountsApi->accountsGetBalance($identity['address']);
        $balance = (new Mutez($mutez))->tez();

        $this->set('balance', $balance);
    }
}
