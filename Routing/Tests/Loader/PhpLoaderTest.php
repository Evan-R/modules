<?php

/**
 * This File is part of the Selene\Components\Routing\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Loader;

use \Mockery as m;
use \Selene\Components\Routing\RouteCollection;
use \Selene\Components\Routing\Loader\PhpLoader;

/**
 * @class PhpLoaderTest
 * @package Selene\Components\Routing\Tests\Loader
 * @version $Id$
 */
class PhpLoaderTest extends \PHPUnit_Framework_TestCase
{
    use LoaderTestHelper;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            '\Selene\Components\Routing\Loader\PhpLoader',
            new PhpLoader(
                new RouteCollection,
                $this->locator
            )
        );
    }

    /** @test */
    public function itShouldLoadingFileWithRoutesAsArgument()
    {
        $fname = 'routes.0.php';
        $file  = __DIR__.'/Fixures/'.$fname;
        $loader = $this->newLoader($routes = new RouteCollection);

        $this->locator->shouldReceive('locate')
            ->with($fname, m::any())
            ->andReturn($file);

        $loader->load($name = 'routes.0.php');

        $this->assertTrue($routes->has('index'));
    }

    protected function newLoader($routes = null)
    {
        return new PhpLoader($routes ?: new RouteCollection, $this->locator);
    }

    protected function setUp()
    {
        $this->locator = $this->mockLocator();
    }

    protected function tearDown()
    {
        m::close();
    }
}
