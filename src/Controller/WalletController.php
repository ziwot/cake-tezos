<?php
declare(strict_types=1);

namespace CakeTezos\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Wallet Controller
 */
class WalletController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function login(): ?Response
    {
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            return $this->response->withType('application/json')->withStatus(200)
                ->withStringBody(json_encode($result->getData()));
        } else {
            return $this->response->withType('application/json')->withStatus(400)
                ->withStringBody(json_encode($result->getErrors()));
        }
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function logout(): ?Response
    {
        $this->Authentication->logout();

        return $this->redirect(['_name' => 'homepage']);
    }
}
