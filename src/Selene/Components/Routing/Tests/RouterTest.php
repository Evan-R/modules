<?php

/**
 * This File is part of the Selene\Components\Routing\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests;

use \Mockery as m;
use \Selene\Components\Routing\Route;
use \Selene\Components\Routing\Router;
use \Selene\Components\Routing\RouteCollectionInterface;

/**
 * @class RouterTest
 * @package Selene\Components\Routing\Tests
 * @version $Id$
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\Routing\Router', new Router);
        $this->assertInstanceof('\Selene\Components\Routing\RouterInterface', new Router);
    }

    /** @test */
    public function itShouldDispatchAGivenRequest()
    {
        $router = $this->getRouter();

        $router->dispatch($this->getRequestMock());
    }

    /**
     * getRouter
     *
     * @access protected
     * @return mixed
     */
    protected function getRouter()
    {
        $router = new Router;

        return $router;
    }

    protected function getRequestMock()
    {
        return m::mock('\Symfony\Component\HttpFoundation\Request');
    }


    protected function tearDown()
    {
        return m::close();
    }
}
