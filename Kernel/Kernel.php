<?php

/**
 * This File is part of the Selene\Module\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Kernel;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Symfony\Component\HttpKernel\TerminableInterface;
use \Selene\Module\Events\DispatcherInterface;
use \Selene\Module\Routing\RouterInterface;
use \Selene\Module\Http\RequestStack;
use \Selene\Module\DI\ContainerAwareInterface;
use \Selene\Module\DI\Traits\ContainerAwareTrait;
use \Selene\Module\Events\SubscriberInterface;
use \Selene\Module\Routing\Events\RouteDispatchEvent;
use \Selene\Module\Routing\Events\RouteFilterAbortEvent;
use \Selene\Module\Kernel\Events\FilterResponseEvent;
use \Selene\Module\Kernel\Events\HandleRequestEvent;
use \Selene\Module\Kernel\Events\HandleResponsetEvent;
use \Selene\Module\Kernel\Events\HandleExceptionEvent;
use \Selene\Module\Kernel\Events\AbortRequestEvent;
use \Selene\Module\Kernel\Events\HandleShutDownEvent;
use \Selene\Module\Kernel\Events\HandleRequestEndEvent;
use \Selene\Module\Kernel\Events\ResponseEvent;
use \Selene\Module\Kernel\Events\KernelEvents as Events;
use \Selene\Module\Kernel\Subscriber\KernelSubscriber;

/**
 * @class Kernel implements HttpKernelInterface
 * @see HttpKernelInterface
 *
 * @package Selene\Module\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Kernel implements KernelInterface
{
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
    public function __construct(DispatcherInterface $events, RequestStack $stack = null)
    {
        $this->events = $events;

        $this->requestStack = $stack ?: new RequestStack;
    }

    /**
     * handle
     *
     * @param Request $request
     * @param string  $type
     * @param int     $catch
     *
     * @throws \Exception if $catch is false and program raised exception
     * during request handling.
     *
     * @return Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->requestStack->push($request);

        try {
            $response = $this->handleRequest($request, $type, $catch);
        } catch (\Exception $e) {
            if (!$catch) {

                $this->endRequest($request, $type);

                throw $e;
            }

            return $this->handleRequestException($request, $e, $type);
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
        //Fire finishing events
        $this->getEvents()->dispatch(
            Events::HANDLE_SHUTDOWN,
            $event = new HandleShutDownEvent($request, $response)
        );
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
     * registerKernelSubscriber
     *
     * @param KernelSubscriber $subscriber
     *
     * @access public
     * @return void
     */
    public function registerKernelSubscriber(SubscriberInterface $subscriber)
    {
        $this->getEvents()->addSubscriber($subscriber);
    }

    /**
     * handleRequest
     *
     * @param Request $request
     * @param int     $type
     * @param boolean $catch
     *
     * @return Resoonse
     */
    protected function handleRequest(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->events->dispatch(
            Events::RESPONSE,
            $event = new ResponseEvent($this, $request, $type)
        );

        if ($response = $event->getResponse()) {
            return $this->filterResponse($request, $response, $type);
        }

        //Fire the first kernel event in order to retreive an response. If no
        //response is set on the event, return a 404 response.
        $this->events->dispatch(
            Events::REQUEST,
            $event = new HandleRequestEvent($this, $request, $type)
        );

        return $this->filterResponse($request, $event->getResponse() ?: new Response('Not Found', 404), $type);
    }

    /**
     * filterResponse
     *
     * @param Request  $request
     * @param Response $response
     * @param int      $type
     *
     * @return Resopnse
     */
    protected function filterResponse(Request $request, Response $response, $type)
    {
        // Dispatch the `kernel.filter_response` event:
        $this->events->dispatch(
            Events::FILTER_RESPONSE,
            $event = new FilterResponseEvent($this, $request, $type, $response)
        );

        $this->endRequest($request, $type);

        return $event->getResponse();
    }

    /**
     * handleRequestException
     *
     * @param Request    $request
     * @param \Exception $e
     * @param int        $type
     *
     * @return Response
     */
    protected function handleRequestException(Request $request, \Exception $e, $type)
    {
        $this->events->dispatch(
            Events::HANDLE_EXCEPTION,
            $event = new HandleExceptionEvent($this, $request, $type, $e)
        );

        $response = $event->getResponse();

        if (!$response instanceof Response) {

            $response = new Response($e->getMessage());

            if ($e instanceof NotFoundHttpException) {
                $response->setStatusCode($e->getStatusCode());
            } else {
                $response->setStatusCode(500);
            }
        }

        $response = $this->filterResponse($request, $response, $type);

        $this->events->dispatch(
            Events::ABORT_REQUEST,
            $event = new AbortRequestEvent($this, $request, $type, $e)
        );

        //$this->endRequest($request, $type);

        return $response;
    }

    /**
     * endRequest
     *
     * @param Request $request
     * @param mixed $type
     *
     * @access protected
     * @return void
     */
    protected function endRequest(Request $request, $type)
    {
        $this->events->dispatch(
            Events::END_REQUEST,
            new HandleRequestEndEvent($this, $request, $type)
        );

        $this->requestStack->pop();
    }
}
