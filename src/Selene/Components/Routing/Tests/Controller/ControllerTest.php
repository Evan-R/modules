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
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Routing\Tests\Controller\Stubs\Controller;

/**
 * @class ControllerTest
 * @package Selene\Components\Routing\Tests\Controller
 * @version $Id$
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $controller = new Controller;
    }

    /**
     * @test
     */
    public function itShouldBeContainerAware()
    {
        $controller = $this->setupController();
        $this->assertInstanceof('\Selene\Components\DI\ContainerInterface', $controller->getContainer());
    }

    /**
     * @test
     */
    public function itShouldBeViewAware()
    {
        $controller = $this->setupController();
        $controller->getContainer()->shouldReceive('get')->with('view')->andReturn($view = m::mock('View'));
        $this->assertSame($view, $controller->getView());
    }

    /**
     * @test
     */
    public function itShouldBeRenderAware()
    {
        $str = 'Hello World';

        $controller = $this->setupController();
        $controller->getContainer()->shouldReceive('get')->with('view')->andReturn($view = m::mock('View'));
        $view->shouldReceive('render')->with($str)->andReturn($str);

        $this->assertSame($str, $controller->callAction('actionIndex', [$str]));
    }

    /**
     * @test
     */
    public function itShouldBeRequestAware()
    {
        $controller = $this->setupController();
        $controller->getContainer()->shouldReceive('get')
            ->with('request.stack')
            ->andReturn($stack = m::mock('RequestStack'));
        $stack->shouldReceive('getCurrent')->andReturn($req = m::mock('Request'));

        $req->request = $bag = m::mock('Bag');
        $bag->shouldReceive('get')->with('user_id')->andReturn(12);

        $this->assertSame(12, $controller->callAction('createUser', []));
    }

    protected function setupController(ContainerInterface $container = null)
    {
        $container = $container ?: m::mock('\Selene\Components\DI\ContainerInterface');
        $controller = new Controller;
        $controller->setContainer($container);

        return $controller;
    }
}
