<?php

/**
 * This File is part of the Selene\Components\Core package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Core\Stack;

use \Selene\Components\Net\Request;
use \Selene\Components\Net\Response;
use \Selene\Components\Core\AppCoreInterface;
use \Selene\Components\Core\ShutdownInterface;

/**
 * @class StackedCore implements AppCoreInterface
 * @see AppCoreInterface
 *
 * @package Selene\Components\Core
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class StackedCore implements AppCoreInterface, ShutdownInterface
{
    /**
     * app
     *
     * @var AppCoreInterface
     */
    protected $app;

    /**
     * middlewares
     *
     * @var array
     */
    protected $middlewares;

    /**
     * @param AppCoreInterface $app
     * @param array $middlewares
     *
     * @access public
     */
    public function __construct(AppCoreInterface $app, array $middlewares = [])
    {
        $this->app = $app;
        $this->middlewares = $middlewares;
    }

    /**
     * handleRequest
     *
     * @param Request $request
     * @param mixed $type
     * @param mixed $catch
     *
     * @access public
     * @return Response
     */
    public function handleRequest(Request $request, $type = self::REQUEST, $catch = self::CORE_CATCH_EXCEPTIONS)
    {
        return $this->app->handleRequest($request, $type, $catch);
    }

    /**
     * shutDown
     *
     * @param Request $request
     * @param Response $response
     *
     * @access public
     * @return void
     */
    public function shutDown(Request $request = null, Response $response = null)
    {
        $iterator = new \ArrayIterator($this->middlewares);
        $valid = count($this->middlewares);

        foreach ($this->middlewares as $middleware) {
            if ($middleware instanceof ShutdownInterface) {
                $middleware->shutdown($request, $response);
            }
        }
    }
}
