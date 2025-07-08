<?php

declare(strict_types=1);

namespace CakeTezos\Controller\Component;

use Cake\Controller\Component;
use CakeTezos\Domain\Network;

class NetworkComponent extends Component
{
    /**
     * Default configuration
     *
     * @var array<string, Network>
     */
    protected array $_defaultConfig = [
        'network' => Network::Local->value,
    ];

    /**
     * beforeFilter callback.
     *
     * @return void
     */
    public function beforeFilter($event): void
    {
        if (!$this->getController()->getRequest()->getSession()->check('CakeTezos.Network')) {
            $this->getController()->getRequest()->getSession()->write('CakeTezos.Network', $this->getConfig('network'));
        }
    }

    public function set(Network $network): void
    {
        $this->getController()->getRequest()->getSession()->write('CakeTezos.Network', $network);
    }

    public function get(): void
    {
        $this->getController()->getRequest()->getSession()->read('CakeTezos.Network');
    }
}
