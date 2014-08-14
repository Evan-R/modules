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
use \Selene\Module\Routing\Controller\ContainerAwareDispatcher;

/**
 * @class ContainerAwareDispatcherTest
 * @package Selene\Module\Routing\Tests\Controller
 * @version $Id$
 */
class ContainerAwareDispatcherTest extends DispatcherTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        parent::itShouldBeInstantiable();
        $this->assertInstanceof('Selene\Module\DI\ContainerAwareInterface', $this->newDispatcher());
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
        return m::mock('Selene\Module\DI\ContainerInterface');
    }

    protected function mockController($container = false)
    {
        if (!$container) {
            return m::mock('Selene\Module\Routing\Controller\Controller');
        }

        return m::mock(
            'Selene\Module\Routing\Controller\Controller, Selene\Module\DI\ContainerAwareInterface'
        );
    }


    protected function newDispatcher()
    {
        return new ContainerAwareDispatcher;
    }
}
