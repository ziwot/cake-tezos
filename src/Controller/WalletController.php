<?php
declare(strict_types=1);

namespace CakeTezos\Controller;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use CakeTezos\Domain\Network;

/**
 * Wallet Controller
 */
class WalletController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function refreshBalance(): ?Response
    {
        $identity = $this->Authentication->getIdentity();
        if ($identity === null) {
            return $this->redirect($this->referer());
        }

        $identity = $identity->getOriginalData();
        $network = Network::from($this->request->getSession()->read('CakeTezos.Network'));
        $cacheKey = sprintf(
            'balance_%s_%s',
            $network->network()['type'],
            $identity['address'],
        );
        Cache::delete($cacheKey, Configure::read('CakeTezos.cache.balance.config') ?? 'default');

        return $this->redirect($this->referer());
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function login(): ?Response
    {
        $result = $this->Authentication->getResult();

        if (!$result || !$result->isValid()) {
            $errors = $result?->getErrors() ?? [];
            $body = json_encode($errors) ?: '[]';

            return $this->response->withType('application/json')->withStatus(400)
                ->withStringBody($body);
        }

        $body = json_encode($result->getData()) ?: '{}';

        return $this->response->withType('application/json')->withStatus(200)
            ->withStringBody($body);
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?Response
    {
        $this->Authentication->logout();

        $redirect = Configure::read('CakeTezos.redirect.afterLogout');

        return $this->redirect($redirect);
    }
}
