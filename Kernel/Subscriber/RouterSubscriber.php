<?php

/**
 * This File is part of the Selene\Components\Kernel\Listener package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Subscriber;


use \Symfony\Component\HttpFoundation\Response;
use \Selene\Components\Kernel\KernelInterface;
use \Selene\Components\Routing\RouterInterface;
use \Selene\Components\Events\Traits\SubscriberTrait;
use \Selene\Components\Kernel\Events\HandleRequestEvent;
use \Selene\Components\Kernel\Events\KernelEvents as Events;

/**
 * @class RouterListener
 * @package Selene\Components\Kernel\Listener
 * @version $Id$
 */
class RouterSubscriber implements KernelSubscriber
{
    use SubscriberTrait;

    /**
     * router
     *
     * @var RouterInterface
     */
    private $router;

    /**
     * subscriptions
     *
     * @var array
     */
    private static $subscriptions = [
        Events::REQUEST => 'onHandleRequest'
    ];

    /**
     * @param Kernel $app
     *
     * @access public
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {inheritdoc}
     */
    public function subscribeToKernel(KernelInterface $kernel)
    {
        $kernel->getEvents()->addSubscriber($this);
    }

    /**
     * onHandleRequest
     *
     * @param mixed $event
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onHandleRequest(HandleRequestEvent $event)
    {
        $response = $this->router->dispatch($event->getRequest());

        $event->stopPropagation();
        $event->setResponse($response = $this->getResponse($response));

        return $response;
    }

    /**
     * getResponse
     *
     * @param mixed $result
     *
     * @return Response
     */
    private function getResponse($result)
    {
        if ($result instanceof Response) {
            return $result;
        }

        return new Response($result);
    }
}
