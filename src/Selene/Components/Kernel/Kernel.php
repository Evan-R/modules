<?php

/**
 * This File is part of the Selene\Components\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Symfony\Component\HttpKernel\TerminableInterface;
use \Selene\Components\Events\DispatcherInterface;
use \Selene\Components\Routing\RouterInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;

/**
 * @class Application
 * @package Selene\Components\Kernel
 * @version $Id$
 */
class Kernel implements HttpKernelInterface
{
    private $router;

    private $events;

    public function __construct(DispatcherInterface $events, RouterInterface $router)
    {
        $this->events = $events;
        $this->router = $router;
    }

    /**
     * getEventDispatcher
     *
     *
     * @access public
     * @return DispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->events;
    }

    /**
     * getRouter
     *
     * @access public
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->router->dispatch($request);
        return null;
    }

    public function terminate(Request $request, Response $response)
    {
        return null;
    }

    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->setUpRouterEvents();
    }

    /**
     * setUpRouterEvents
     *
     * @access protected
     * @return mixed
     */
    protected function setUpRouterEvents()
    {
        $this->events->on('router_dispatch', function ($event) {

        });
    }
}
