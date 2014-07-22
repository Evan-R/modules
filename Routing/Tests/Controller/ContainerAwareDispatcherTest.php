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
use \Selene\Components\Routing\Controller\ContainerAwareDispatcher;

/**
 * @class ContainerAwareDispatcherTest
 * @package Selene\Components\Routing\Tests\Controller
 * @version $Id$
 */
class ContainerAwareDispatcherTest extends DispatcherTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        parent::itShouldBeInstantiable();
        $this->assertInstanceof('Selene\Components\DI\ContainerAwareInterface', $this->newDispatcher());
    }

    /** @test */
    public function itShouldResolveControllerFromContainer()
    {
        $dispatcher = $this->newDispatcher();
        $dispatcher->setContainer($container = $this->mockContainer());

        $container->shouldReceive('has')->with('acme_controller')->andReturn(true);
        $container->shouldReceive('get')->with('acme_controller')->andReturn($ctrl = $this->mockController(true));

        $ctrl->shouldReceive('callAction')->andReturn('ok');
        $ctrl->shouldReceive('setContainer')->with($container);

        $route = $this->mockRoute();
        $route->shouldReceive('getAction')
            ->andReturn('acme_controller:callAction');

        $this->assertSame('ok', $dispatcher->dispatch($this->getContext($route)));
    }

    protected function mockContainer()
    {
        return m::mock('Selene\Components\DI\ContainerInterface');
    }

    protected function mockController($container = false)
    {
        if (!$container) {
            return m::mock('Selene\Components\Routing\Controller\Controller');
        }

        return m::mock(
            'Selene\Components\Routing\Controller\Controller, Selene\Components\DI\ContainerAwareInterface'
        );
    }


    protected function newDispatcher()
    {
        return new ContainerAwareDispatcher;
    }
}
