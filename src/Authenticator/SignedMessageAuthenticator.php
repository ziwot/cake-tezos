<?php

declare(strict_types=1);

namespace CakeTezos\Authenticator;

use Authentication\Authenticator\AbstractAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Authentication\UrlChecker\UrlCheckerTrait;
use CakeTezos\Identifier\TezosIdentifier;
use Psr\Http\Message\ServerRequestInterface;

/**
 * SignedMessage Authenticator
 *
 * Authenticates an identity based on the POST data of the request.
 */
class SignedMessageAuthenticator extends AbstractAuthenticator
{

	use UrlCheckerTrait;

	/**
	 * Default config for this object.
	 * - `fields` The fields to use to identify a user by.
	 * - `loginUrl` Login URL or an array of URLs.
	 * - `urlChecker` Url checker config.
	 *
	 * @var array<string, mixed>
	 */
	protected array $_defaultConfig = [
		'loginUrl' => '/signin',
		'urlChecker' => 'Authentication.Default',
		'fields' => [
			TezosIdentifier::CREDENTIAL_PK => 'pk',
			TezosIdentifier::CREDENTIAL_PKH => 'pkh',
			TezosIdentifier::CREDENTIAL_MESSAGE => 'message',
			TezosIdentifier::CREDENTIAL_SIGNATURE => 'signature',
		],
	];

	/**
	 * Checks the fields to ensure they are supplied.
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
	 *
	 * @return array<int|string, string>|null Username and password retrieved from a request body.
	 */
	protected function _getData(ServerRequestInterface $request): ?array
	{
		$fields = $this->_config['fields'];
		/** @var array<string, string> $body */
		$body = $request->getParsedBody();

		$data = [];
		foreach ($fields as $key => $field) {
			if (!isset($body[$field])) {
				return null;
			}

			$value = $body[$field];
			if (!is_string($value) || !strlen($value)) {
				return null;
			}

			$data[$key] = $value;
		}

		return $data;
	}

	/**
	 * Prepares the error object for a login URL error
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
	 *
	 * @return \Authentication\Authenticator\ResultInterface
	 */
	protected function _buildLoginUrlErrorResult(ServerRequestInterface $request): ResultInterface
	{
		$uri = $request->getUri();
		$base = $request->getAttribute('base');
		if ($base !== null) {
			$uri = $uri->withPath((string)$base . $uri->getPath());
		}

		$checkFullUrl = $this->getConfig('urlChecker.checkFullUrl', false);
		if ($checkFullUrl) {
			$uri = (string)$uri;
		} else {
			$uri = $uri->getPath();
		}

		$errors = [
			sprintf(
				'Login URL `%s` did not match `%s`.',
				$uri,
				implode('` or `', (array)$this->getConfig('loginUrl')),
			),
		];

		return new Result(null, Result::FAILURE_OTHER, $errors);
	}

	/**
	 * Authenticates the identity contained in a request. Will use the `config.userModel`, and `config.fields`
	 * to find POST data that is used to find a matching record in the `config.userModel`. Will return false if
	 * there is no post data, either username or password is missing, or if the scope conditions have not been met.
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
	 *
	 * @return \Authentication\Authenticator\ResultInterface
	 */
	public function authenticate(ServerRequestInterface $request): ResultInterface
	{
		if (!$this->_checkUrl($request)) {
			return $this->_buildLoginUrlErrorResult($request);
		}

		$data = $this->_getData($request);

		if ($data === null) {
			return new Result(
				null,
				Result::FAILURE_CREDENTIALS_MISSING,
				[
					'Login credentials not found',
				],
			);
		}

		$user = $this->_identifier->identify($data);

		if (empty($user)) {
			return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identifier->getErrors());
		}

		return new Result($user, Result::SUCCESS);
	}
}
