<?php
declare(strict_types=1);

namespace CakeTezos\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\EventInterface;
use CakeTezos\Domain\Network;

class NetworkComponent extends Component
{
    /**
     * Default configuration
     *
     * @var array<string, \CakeTezos\Domain\Network>
     */
    protected array $_defaultConfig = [
        'network' => Network::Local->value,
    ];

    /**
     * beforeFilter callback.
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        if (!$this->getController()->getRequest()->getSession()->check('CakeTezos.Network')) {
            $this->getController()->getRequest()->getSession()->write('CakeTezos.Network', $this->getConfig('network'));
        }
    }

    /**
     * Set network
     *
     * @param \CakeTezos\Domain\Network $network
     */
    public function set(Network $network): void
    {
        $this->getController()->getRequest()->getSession()->write('CakeTezos.Network', $network);
    }

    /**
     * Get network
     */
    public function get(): void
    {
        $this->getController()->getRequest()->getSession()->read('CakeTezos.Network');
    }
}
