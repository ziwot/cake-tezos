<?php
declare(strict_types=1);

namespace CakeTezos\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;
use CakeTezos\Domain\Network;

/**
 * @method \App\Controller\AppController getController()
 */
class NetworkComponent extends Component
{
    /**
     * Default configuration
     *
     * @var array<string, string>
     */
    protected array $_defaultConfig = [
        'network' => Network::Local->value,
    ];

    /**
     * beforeFilter callback.
     *
     * @param \Cake\Event\Event<\Cake\Controller\Controller> $event
     */
    public function beforeFilter(Event $event): void
    {
        if (!$this->getController()->getRequest()->getSession()->check('CakeTezos.Network')) {
            $network = $this->getConfig('network')
                ?? Configure::read('CakeTezos.defaultNetwork')
                ?? Network::Local->value;
            $this->getController()->getRequest()->getSession()->write('CakeTezos.Network', $network);
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
    public function get(): string
    {
        return $this->getController()->getRequest()->getSession()->read('CakeTezos.Network');
    }
}
