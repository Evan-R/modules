<?php

/**
 * This File is part of the Selene\Components\Routing\Tests\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Controller;

use \Mockery as m;
use \Selene\Components\Routing\Route;
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Routing\Controller\Dispatcher;

/**
 * @class ResolverTest
 * @package Selene\Components\Routing\Tests\Controller
 * @version $Id$
 */
class DispatcherTest extends TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Components\Routing\Controller\Dispatcher', new Dispatcher);
    }

    /** @test */
    public function itShouldFindAndDispatchAController()
    {
        $resolver = new Dispatcher;

        $this->assertSame('ok', $resolver->dispatch($this->getContext()));
    }

    /** @test */
    public function itShouldThrowAnExceptionIfControllerDosntExist()
    {
        $resolver = new Dispatcher;

        $route = $this->mockRoute();
        $route->shouldReceive('getAction')->andReturn('Controller@action');

        try {
            $this->assertSame('ok', $resolver->dispatch($this->getContext($route)));
        } catch (\RuntimeException $e) {
            $this->assertSame('Controller "Controller" could not be found.', $e->getMessage());

            return;
        }

        $this->giveUp();
    }

    protected function tearDown()
    {
        m::close();
    }

    protected function getContext($route = null, $request = null, array $params = [])
    {
        $context = m::mock('Selene\Components\Routing\Matchers\MatchContext');

        if (null === $route) {
            $route = $this->mockRoute();
            $route->shouldReceive('getAction')
                ->andReturn('Selene\Components\Routing\Tests\Controller\Stubs\Controller@testAction');
        }

        if (null === $request) {
            $request = $this->mockRequest();
            $request->shouldReceive('getMethod')->andReturn('GET');
        }

        $context->shouldReceive('getRoute')->andReturn($route);
        $context->shouldReceive('getRequest')->andReturn($request);
        $context->shouldReceive('getParameters')->andReturn($params);

        return $context;
    }

    protected function mockRoute()
    {
        $route = m::mock('Selene\Components\Routing\Route');

        return $route;
    }

    protected function mockRequest()
    {
        $request = m::mock('Symfony\Component\HttpFoundation\Request');

        return $request;
    }

    protected function mockContainer()
    {
        return m::mock('Selene\Components\DI\ContainerInterface');
    }
}
