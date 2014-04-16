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
    /**
     * router
     *
     * @var mixed
     */
    private $router;

    /**
     * events
     *
     * @var mixed
     */
    private $events;

    /**
     * responseStack
     *
     * @var mixed
     */
    protected $responseStack;

    /**
     * @param DispatcherInterface $events
     * @param RouterInterface $router
     *
     * @access public
     * @return mixed
     */
    public function __construct(DispatcherInterface $events, RouterInterface $router)
    {
        $this->events = $events;
        $this->router = $router;
        $this->responseStack = new \SplStack;
        $this->setUpRouterEvents();
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

    /**
     * handle
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
        try {
            $response = $this->handleRequest($request, $type, $catch);
        } catch (\Exception $e) {
            if ($catch) {
                return $this->handleRequestException($e);
            }
            throw $e;
        }

        return $response;
    }

    /**
     * terminate
     *
     * @param Request $request
     * @param Response $response
     *
     * @access public
     * @return mixed
     */
    public function terminate(Request $request, Response $response)
    {
        $this->booted = false;

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
     * handleRequest
     *
     * @param Request $request
     * @param mixed $type
     * @param mixed $catch
     *
     * @access protected
     * @return mixed
     */
    protected function handleRequest(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->router->dispatch($request);

        return $this->filterResponse();
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
            $this->responseStack->push($event);
        });

        $this->events->on('router_abort', function ($event) {
            $this->responseStack->push($event);
        });
    }

    /**
     * filterResponse
     *
     * @access protected
     * @return mixed
     */
    protected function filterResponse()
    {
        if (!$this->responseStack->count()) {
            throw new \Exception();
        }

        $event = $this->responseStack->pop();
        $response = $event->getResponse();

        if ($response instanceof Response) {
            return $response;
        }

        return new Response($response);
    }

    protected function handleRequestException(\Exception $e)
    {
        throw $e;
        die;
    }
}
