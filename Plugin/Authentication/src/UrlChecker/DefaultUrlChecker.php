<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Authentication\UrlChecker;

use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Default URL checker for CakePHP applications. Uses CakePHP Router.
 */
class DefaultUrlChecker implements UrlCheckerInterface
{
    /**
     * Default Options
     *
     * - `checkFullUrl` Whether to check the full request URI.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultOptions = [
        'checkFullUrl' => false,
    ];

    /**
     * @inheritDoc
     */
    public function check(ServerRequestInterface $request, array|string $loginUrls, array $options = []): bool
    {
        $options = $this->_mergeDefaultOptions($options);
        $url = $this->_getUrlFromRequest($request, $options['checkFullUrl']);

        // Support both string URLs and array-based routes (like Router::url())
        $validUrl = Router::url($loginUrls, $options['checkFullUrl']);

        return $validUrl === $url;
    }

    /**
     * Merges given options with the defaults.
     *
     * @param array<string, mixed> $options Options to merge in
     * @return array<string, mixed>
     */
    protected function _mergeDefaultOptions(array $options): array
    {
        return $options + $this->_defaultOptions;
    }

    /**
     * Returns current url.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server Request
     * @param bool $getFullUrl Get the full URL or just the path
     * @return string
     */
    protected function _getUrlFromRequest(ServerRequestInterface $request, bool $getFullUrl = false): string
    {
        $uri = $request->getUri();

        $requestBase = $request->getAttribute('base');
        if ($requestBase) {
            $uri = $uri->withPath($requestBase . $uri->getPath());
        }

        if ($getFullUrl) {
            return (string)$uri;
        }

        return $uri->getPath();
    }
}
