<?php

/**
 * This File is part of the Selene\Module\Routing\Tests\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests\Controller;

use \Mockery as m;
use \Selene\Module\Routing\Route;
use \Selene\Module\TestSuite\TestCase;
use \Selene\Module\Routing\Controller\Dispatcher;

/**
 * @class ResolverTest
 * @package Selene\Module\Routing\Tests\Controller
 * @version $Id$
 */
class DispatcherTest extends TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Module\Routing\Controller\Dispatcher', $this->newDispatcher());
    }

    /** @test */
    public function itShouldFindAndDispatchAController()
    {
        $resolver = $this->newDispatcher();

        $this->assertSame('ok', $resolver->dispatch($this->getContext()));
    }

    /** @test */
    public function itShouldThrowAnExceptionIfControllerDosntExist()
    {
        $resolver = $this->newDispatcher();

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
        $context = m::mock('Selene\Module\Routing\Matchers\MatchContext');

        if (null === $route) {
            $route = $this->mockRoute();
            $route->shouldReceive('getAction')
                ->andReturn('Selene\Module\Routing\Tests\Controller\Stubs\Controller@testAction');
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
        $route = m::mock('Selene\Module\Routing\Route');

        return $route;
    }

    protected function mockRequest()
    {
        $request = m::mock('Symfony\Component\HttpFoundation\Request');

        return $request;
    }

    protected function mockContainer()
    {
        return m::mock('Selene\Module\DI\ContainerInterface');
    }

    protected function newDispatcher()
    {
        return new Dispatcher;
    }
}
