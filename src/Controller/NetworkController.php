<?php
declare(strict_types=1);

namespace CakeTezos\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Network Controller
 */
class NetworkController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['select']);
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function select(): ?Response
    {
        if ($this->request->is('post')) {
            $this->request->getSession()->write('CakeTezos.Network', $this->request->getData('network'));

            return $this->redirect($this->referer());
        }
    }
}
