<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing;

use \Selene\Adapter\Http\RequestStack;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class UrlBuilder
 * @package Selene\Module\Routing
 * @version $Id$
 */
class UrlBuilder
{
    /**
     * @var int
     */
    const ABSOLUTE_PATH = 22;

    /**
     * @var int
     */
    const RELATIVE_PATH = 144;

    /**
     * routes
     *
     * @var \Selene\Module\Routing\RouteCollectionInterface
     */
    private $routes;

    /**
     * routes
     *
     * @var \Selene\Module\Http\RequestStack
     */
    private $stack;

    /**
     * noEncode
     *
     * @var array
     */
    private static $noEncode = [
        '%2F' => '/',
        '%40' => '@',
        '%3A' => ':',
        '%3B' => ';',
        '%2C' => ',',
        '%3D' => '=',
        '%2B' => '+',
        '%21' => '!',
        '%2A' => '*',
        '%7C' => '|',
    ];

    /**
     * @param RouteCollectionInterface $routs
     *
     * @access public
     */
    public function __construct(RouteCollectionInterface $routes, RequestStack $stack)
    {
        $this->routes = $routes;
        $this->stack = $stack;
    }

    /**
     * getRequest
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->stack->getCurrent();
    }

    /**
     * currentUrl
     *
     * @param int $type
     *
     * @return string
     */
    public function currentUrl($type = self::RELATIVE_PATH)
    {
        $req = $this->getRequest();

        if ($type === self::RELATIVE_PATH) {
            $qs = $req->getQueryString() ?  '?'.$req->getQueryString() : '';

            return $req->getBaseUrl().$req->getPathInfo().$qs;
        }

        return $type === self::ABSOLUTE_PATH ? $req->getUri() : null;
    }

    /**
     * currentPath
     *
     * @param int $type
     *
     * @return string
     */
    public function currentPath($type = self::RELATIVE_PATH)
    {
        $req = $this->getRequest();

        if ($type === self::RELATIVE_PATH) {
            return $req->getBaseUrl().$req->getPathInfo();
        }

        return $type === self::ABSOLUTE_PATH ? $req->getSchemeAndHttpHost().$req->getBaseUrl().$req->getPathInfo() :
            null;
    }

    /**
     * Get a url from a route name.
     *
     * @param string $name
     * @param array  $parameters
     * @param string $host
     * @param int    $type
     *
     * @return string
     */
    public function getPath($name, array $parameters = [], $host = null, $type = self::RELATIVE_PATH)
    {
        if (!$route = $this->routes->get($name)) {
            throw new \InvalidArgumentException(sprintf('A route with name "%s" could not be found', $name));
        }

        return $this->compilePath($route, $parameters, $host, $type);
    }

    /**
     * setRouteParameters
     *
     * @param Route  $route
     * @param array  $parameters
     * @param string $host
     * @param int    $type
     *
     * @return string
     */
    protected function compilePath(Route $route, array $parameters, $host, $type)
    {
        $route->compile();

        $prefix = '';

        if (static::ABSOLUTE_PATH === $type) {
            $prefix = $this->getPathPrefix($route, $parameters, $host);
        } elseif ($route->hasHost()) {
            throw new \InvalidArgumentException(
                'Can\'t create relative path because route requires a deticated hostname'
            );
        }

        if (!(bool)$route->getVars()) {
            $url = $prefix.$route->getPattern();

            return 1 === strlen($url) ? $url : rtrim($url, '/');
        }

        $defaults = $route->getDefaults();

        $parameters = array_merge(
            array_combine(array_values($v = $route->getVars()), array_fill(1, count($v), null)),
            $parameters
        );

        $isNull = null;

        $parts = [];

        foreach ($route->getTokens() as $token) {

            if ('variable' === $token[0]) {
                if (!isset($parameters[$token[3]])) {
                    if (false === $token[4]) {
                        throw new \InvalidArgumentException('required parameter');
                    }
                    if (false === $isNull) {
                        throw new \InvalidArgumentException(sprintf('Variable "%s" must not be empty.', $token[3]));
                    }
                    $isNull = true;
                } else {
                    $this->varMatchesRequirement($token, $parameters);
                    $parts[] = $parameters[$token[3]];
                    $parts[] = $token[1];
                    $isNull = false;
                }
                continue;
            }

            if ('text' === $token[0]) {
                $parts[] = $token[1];
            }
        }

        $uri = strtr(rawurlencode(implode('', array_reverse($parts))), static::$noEncode);

        return rtrim($prefix.$uri, '/');
    }

    /**
     * varMatchesRequirement
     *
     * @param array $token
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    private function varMatchesRequirement(array $token, array $parameters)
    {
        if ((bool)preg_match($regexp = '#^'.$token[2].'$#', $param = $parameters[$token[3]])) {
            return;
        }

        throw new \InvalidArgumentException(sprintf('invalid value "%s" for variable "%s"', $param, $token[3]));
    }

    /**
     * getPathPrefix
     *
     * @param Route $route
     * @param array $parameters
     * @param mixed $host
     *
     * @return mixed
     */
    private function getPathPrefix(Route $route, array $parameters, $host)
    {
        if (!$route->hasHost()) {
            $host = $this->getRequest()->getHost();
        } elseif (null === $host) {
            throw new \InvalidArgumentException('Route requires host, no host given.');
        } elseif (null !== $host && !(bool)preg_match($route->getHostRegexp(), $host)) {
            throw new \InvalidArgumentException('Host requirement does not match given host.');
        }

        return sprintf('%s://%s', $this->getRouteProtocol($route, $this->getRequest()), $host);
    }

    /**
     * getRouteProtocol
     *
     * @param Route $route
     * @param Request $request
     *
     * @return string
     */
    private function getRouteProtocol(Route $route, Request $request)
    {
        $requestScheme = $request->getScheme();

        $schemes = $route->getSchemes();

        if (in_array($requestScheme, $schemes)) {
            return $requestScheme;
        }

        return current($schemes) ?: 'http';
    }
}
