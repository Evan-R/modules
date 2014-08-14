<?php

/**
 * This File is part of the Selene\Module\Routing\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests\Loader;

use \Mockery as m;
use \Selene\Module\Routing\RouteCollection;
use \Selene\Module\Routing\Loader\PhpLoader;

/**
 * @class PhpLoaderTest
 * @package Selene\Module\Routing\Tests\Loader
 * @version $Id$
 */
class PhpLoaderTest extends \PHPUnit_Framework_TestCase
{
    use LoaderTestHelper;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            '\Selene\Module\Routing\Loader\PhpLoader',
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
