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
use \Selene\Components\Http\RequestStack;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;
use \Selene\Components\Events\SubscriberInterface;
use \Selene\Components\Routing\Events\RouteDispatchEvent;
use \Selene\Components\Routing\Events\RouteFilterAbortEvent;
use \Selene\Components\Kernel\Events\HandleRequestEvent;
use \Selene\Components\Kernel\Events\KernelExceptionEvent;

/**
 * @class Kernel implements HttpKernelInterface
 * @see HttpKernelInterface
 *
 * @package Selene\Components\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Kernel implements HttpKernelInterface, SubscriberInterface
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
     * requestStack
     *
     * @var mixed
     */
    protected $requestStack;

    /**
     * Create a new Kernel instance.
     *
     * @param DispatcherInterface $events
     * @param RouterInterface $router
     *
     * @access public
     * @return mixed
     */
    public function __construct(DispatcherInterface $events, RouterInterface $router, RequestStack $stack = null)
    {
        $this->events = $events;
        $this->router = $router;

        $this->requestStack = $stack ?: new RequestStack;
        $this->responseStack = new \SplStack;

        $this->events->addSubscriber($this);
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
        $this->requestStack->push($request);
        $this->getEvents()->dispatch('kernel.handle_request', new HandleRequestEvent($request));

        try {
            $response = $this->handleRequest($request, $type, $catch);
        } catch (\Exception $e) {
            if ($catch) {
                return $this->handleRequestException($request, $e);
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

    /**
     * boot
     *
     * @access public
     * @return mixed
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->setUpRouterEvents();
    }

    /**
     * onRouterDispatch
     *
     * @access public
     * @return mixed
     */
    public function onRouterDispatch(RouteDispatchEvent $event)
    {
        $this->responseStack->push($event);
    }

    /**
     * onRouterDispatch
     *
     * @param RouteFilterAbortEvent $event
     *
     * @access public
     * @return mixed
     */
    public function onRouterAbort(RouteFilterAbortEvent $event)
    {
        $this->responseStack->push($event);
    }

    /**
     * getSubscriptions
     *
     * @param mixed $
     *
     * @access public
     * @return mixed
     */
    public function getSubscriptions()
    {
        return [
            'router_dispatch' => 'onRouterDispatch',
            'router_abort'    => 'onRouterAbort'
        ];
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
     * getRouter
     *
     *
     * @access public
     * @return mixed
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * getEvents
     *
     *
     * @access public
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * setUpRouterEvents
     *
     * @access protected
     * @return void
     */
    protected function setUpRouterEvents()
    {
        $this->events->addSubscriber($this);
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
            throw new \Exception('no response given');
        }

        $event = $this->responseStack->pop();
        $response = $event->getResponse();

        if ($response instanceof Response) {
            return $response;
        }

        return new Response($response);
    }

    protected function handleRequestException(Request $request, \Exception $e)
    {
        $this->getEvents()->dispatch('kernel.handle_exception', new KernelExceptionEvent($request, $e));
    }
}
