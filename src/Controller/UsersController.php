<?php

declare(strict_types=1);

namespace CakeTezos\Controller;

use Cake\Event\EventInterface;

/**
 * Users Controller
 */
class UsersController extends AppController
{

	/**
	 * @param \Cake\Event\EventInterface $event
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event)
	{
		parent::beforeFilter($event);

		$this->Authentication->allowUnauthenticated(['login']);
	}

	/**
	 * @return \Cake\Http\Response|null|void
	 */
	public function login()
	{
		$result = $this->Authentication->getResult();

		if ($result && $result->isValid()) {
			return $this->response->withType('application/json')->withStatus(200)
				->withStringBody('OK');
		} else {
			return $this->response->withType('application/json')->withStatus(400)
				->withStringBody('Backend Failure.');
		}
	}

	/**
	 * @return \Cake\Http\Response|null|void
	 */
	public function logout()
	{
		$this->Authentication->logout();

		return $this->redirect(['controller' => 'Users', 'action' => 'login']);
	}
}
