<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Loader;

use \Mockery as m;
use \Selene\Components\DI\Loader\PhpLoader;
use \Selene\Components\DI\ContainerInterface;

/**
 * @class PhpLoaderInterface
 * @package Selene\Components\DI\Tests\Loader
 * @version $Id$
 */
class PhpLoaderTest extends \PHPUnit_Framework_TestCase
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
        $container = m::mock('\Selene\Components\DI\ContainerInterface');

        $loader = new PhpLoader($container);

        $this->assertInstanceof('\Selene\Components\Config\Loader\ConfigLoader', $loader);
        $this->assertInstanceof('\Selene\Components\DI\Loader\PhpLoader', $loader);
    }

    /**
     * @test
     */
    public function itShouldSupportPhpFiles()
    {

        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $loader = new PhpLoader($container);

        $this->assertTrue($loader->supports('php'));
        $this->assertFalse($loader->supports('xml'));
    }

    /**
     * @test
     */
    public function itShouldLoadPhpFiles()
    {
        $file = dirname(__DIR__) . '/config/services.php';

        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $container->shouldReceive('addFileResource')->with($file);
        $container->shouldReceive('addParameter')->with('php', 'loaded');

        $loader = new PhpLoader($container);

        $loader->load($file);
    }
}
