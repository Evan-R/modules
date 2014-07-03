<?php

/**
 * This File is part of the Selene\Components\Kernel\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Tests;

use \Mockery as m;
use \Selene\Components\Kernel\Kernel;
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Http\RequestStack;
use \Selene\Components\Kernel\Events\KernelEvents as Events;

/**
 * @class KernelTest
 * @package Selene\Components\Kernel\Tests
 * @version $Id$
 */
class KernelTest extends TestCase
{
    protected $events;

    protected $router;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Components\Kernel\Kernel', new Kernel($this->mockEvents()));
    }

    /** @test */
    public function itShouldReturnItsEventDispatcher()
    {
        $kernel = $this->newKernel();

        $this->assertSame($this->events, $kernel->getEvents());
    }

    /** @test */
    public function itShouldHandleARequest()
    {
        $kernel = $this->newKernel();

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::REQUEST, m::any())
            ->andReturnUsing(function ($eventName, $event) {
                $this->assertSame(Events::REQUEST, $eventName);
                $this->assertInstanceof('Selene\Components\Kernel\Events\HandleRequestEvent', $event);
                $event->setResponse($this->mockResponse('handle request', 200));
            });

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::FILTER_RESPONSE, m::any())
            ->andReturnUsing(function ($eventName, $event) {
                $this->assertSame(Events::FILTER_RESPONSE, $eventName);
                $this->assertInstanceof('Selene\Components\Kernel\Events\FilterResponseEvent', $event);
                $event->setResponse($this->mockResponse('handle request', 200));
            });

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::END_REQUEST, m::any());

        $response = $kernel->handle($this->mockRequest(), Kernel::MASTER_REQUEST, false);

        $this->assertSame('handle request', $response->getContent());
    }

    /** @test */
    public function itShouldHandleExceptions()
    {
        $kernel = $this->newKernel();

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::REQUEST, m::any())
            ->andReturnUsing(function ($eventName, $event) {
                $handled = true;
                throw new \Exception('exception thrown');
            });

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::HANDLE_EXCEPTION, m::any())
            ->andReturnUsing(function ($eventName, $event) {
                $exception = true;
                $this->assertSame(Events::HANDLE_EXCEPTION, $eventName);
                $this->assertInstanceof('Selene\Components\Kernel\Events\HandleExceptionEvent', $event);
                $this->assertSame('exception thrown', $m = $event->getException()->getMessage());

                $event->setResponse($this->mockResponse($m, 500));
            });

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::ABORT_REQUEST, m::any());
        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::FILTER_RESPONSE, m::any())
            ->andReturnUsing(function ($eventName, $event) {
                $filtered = true;
                $this->assertSame(Events::FILTER_RESPONSE, $eventName);
            });

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::END_REQUEST, m::any());

        $response = $kernel->handle($this->mockRequest(), Kernel::MASTER_REQUEST, true);

        $this->assertSame(500, $response->getStatus());
        $this->assertSame('exception thrown', $response->getContent());
    }

    /** @test */
    public function itShouldFireAnAbortEventIfNoListenerReturnsAResponse()
    {
        $aborted = false;

        $kernel = $this->newKernel();

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::REQUEST, m::any())
            ->andReturnUsing(function ($eventName, $event) {
                throw new \InvalidArgumentException('exception thrown');
            });

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::HANDLE_EXCEPTION, m::any());

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::ABORT_REQUEST, m::any())
            ->andReturnUsing(function ($eventName, $event) use (&$aborted) {
                $aborted = true;
            });

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::END_REQUEST, m::any());

        try {
            $response = $kernel->handle($this->mockRequest(), Kernel::MASTER_REQUEST, true);
        } catch (\Exception $e) {
            //$this->assertTrue($aborted);
        }

        $this->assertTrue($aborted);
    }

    /** @test */
    public function howeverItShouldThrowAnExceptionIfCatchingIsOff()
    {
        $kernel = $this->newKernel();

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::REQUEST, m::any())
            ->andReturnUsing(function ($eventName, $event) use (&$handled) {
                $handled = true;
                throw new \InvalidArgumentException('exception thrown');
            });

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::END_REQUEST, m::any());

        try {
            $kernel->handle($this->mockRequest(), Kernel::MASTER_REQUEST, false);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('exception thrown', $e->getMessage());

            return;
        }

        $this->fail('Test failed');
    }

    /** @test */
    public function itShouldHandleSubrequests()
    {
        $kernel = $this->newKernel($stack = new RequestStack);

        $srq = $this->mockRequest();
        $subHandled = false;

        $this->events->shouldReceive('dispatch')
            ->with(Events::REQUEST, m::any())
            ->andReturnUsing(function ($eventName, $event) use ($stack, $kernel, $srq, &$subHandled) {

                if ($event->getRequest() === $srq) {
                    $subHandled = true;
                } else {
                    $event->setResponse($this->mockResponse('handle request', 200));
                    // start the subrequest
                    $kernel->handle($srq, Kernel::SUB_REQUEST);
                }
            });

        $this->events->shouldReceive('dispatch')
            ->with(Events::FILTER_RESPONSE, m::any())
            ->andReturnUsing(function ($eventName, $event) {
                $event->setResponse($this->mockResponse('handle request', 200));
            });

        $this->events->shouldReceive('dispatch')
            ->with(Events::END_REQUEST, m::any());

        $response = $kernel->handle($this->mockRequest(), Kernel::MASTER_REQUEST, true);

        $this->assertSame('handle request', $response->getContent());
        $this->assertTrue($subHandled);
    }

    /** @test */
    public function whenTerminatingItShouldFireShutdownEvent()
    {
        $evt = null;

        $kernel = $this->newKernel();
        $req = $this->mockRequest();
        $res = $this->mockResponse();

        $this->events->shouldReceive('dispatch')
            ->once()
            ->with(Events::HANDLE_SHUTDOWN, m::any())
            ->andReturnUsing(function ($name, $event) use (&$evt) {
                $evt = $event;
            });


        $kernel->terminate($req, $res);

        $this->assertSame($req, $evt->getRequest());
        $this->assertSame($res, $evt->getResponse());
    }

    /** @test */
    public function itShouldAddSubscribers()
    {
        $added = false;

        $kernel = $this->newKernel();

        $subscriber = m::mock('Selene\Components\Events\SubscriberInterface');

        $this->events->shouldReceive('addSubscriber')
            ->once()
            ->with($subscriber)
            ->andReturnUsing(function () use (&$added) {
                $added = true;
            });

        $kernel->registerKernelSubscriber($subscriber);

        $this->assertTrue($added);
    }

    protected function newKernel($stack = null)
    {
        return new Kernel($this->mockEvents(), $stack);
    }

    protected function mockEvents()
    {
        return $this->events = m::mock('Selene\Components\Events\DispatcherInterface');
    }

    protected function mockRequest()
    {
        return m::mock('Symfony\Component\HttpFoundation\Request');
    }

    protected function mockResponse($content = null, $status = 200)
    {
        $resp = m::mock('Symfony\Component\HttpFoundation\Response');
        $resp->shouldReceive('getContent')->andReturn($content);
        $resp->shouldReceive('getStatus')->andReturn($status);

        return $resp;
    }

    protected function mockRouter()
    {
        return $this->router = m::mock('Selene\Components\Routing\RouterInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
