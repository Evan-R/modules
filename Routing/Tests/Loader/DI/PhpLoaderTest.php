<?php

/**
 * This File is part of the Selene\Components\Routing\Tests\Loader\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Loader\DI;

use \Mockery as m;
use \Selene\Components\Routing\RouteCollection;
use \Selene\Components\Routing\Loader\DI\PhpLoader;
use \Selene\Components\Routing\Tests\Loader\PhpLoaderTest as BasePhpLoaderTest;


/**
 * @class PhpLoaderTest
 * @package Selene\Components\Routing\Tests\Loader\DI
 * @version $Id$
 */
class PhpLoaderTest extends BasePhpLoaderTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $loader = new PhpLoader($this->builder, $this->routes, $this->locator);
        $this->assertInstanceof(
            '\Selene\Components\Routing\Loader\PhpLoader',
            $loader
        );
        $this->assertInstanceof(
            '\Selene\Components\Routing\Loader\DI\PhpLoader',
            $loader
        );
    }

    /** @test */
    public function itShouldLoadingFileWithRoutesAsArgument()
    {
        $added = false;

        $this->builder->shouldReceive('addFileResource')
            ->with(dirname(__DIR__).'/Fixures/routes.0.php')
            ->andReturnUsing(function () use (&$added) {
                $added = true;
            });

        parent::itShouldLoadingFileWithRoutesAsArgument();

        $this->assertTrue($added);
    }

    protected function newLoader($routes = null)
    {
        return new PhpLoader($this->builder, $routes ?: new RouteCollection, $this->locator);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->builder = $this->mockBuilder();
        $this->routes = new RouteCollection;
    }
}
