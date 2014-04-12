<?php

/**
 * This File is part of the Selene\Components\Core package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Stack;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Symfony\Component\HttpKernel\TerminableInterface;

/**
 * @class StackedCore implements AppCoreInterface
 * @see AppCoreInterface
 *
 * @package Selene\Components\Core
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class KernelStack implements HttpKernelInterface, TerminableInterface
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
    public function __construct(HttpKernelInterface $app, array $middlewares = [])
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
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return $this->app->handle($request, $type, $catch);
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
    public function terminate(Request $request, Response $response)
    {
        $iterator = new \ArrayIterator($this->middlewares);
        $valid = count($this->middlewares);

        foreach ($this->middlewares as $middleware) {
            if ($middleware instanceof TerminableInterface) {
                $middleware->terminate($request, $response);
            }
        }
    }
}
