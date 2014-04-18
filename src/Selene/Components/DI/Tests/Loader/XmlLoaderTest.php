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
use \Selene\Components\DI\BaseContainer;
use \Selene\Components\DI\Loader\XmlLoader;

/**
 * @class XmlLoaderTest
 * @package Selene\Components\DI\Tests\Loader
 * @version $Id$
 */
class XmlLoaderTest extends \PHPUnit_Framework_TestCase
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
        $loader = new XmlLoader(m::mock('\Selene\Components\DI\ContainerInterface'));
        $this->assertInstanceof('\Selene\Components\DI\Loader\XmlLoader', $loader);
    }

    /**
     * @test
     */
    public function itShouldSupportXml()
    {

        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $loader = new XmlLoader($container);

        $this->assertTrue($loader->supports('xml'));
        $this->assertFalse($loader->supports('php'));
    }

    /**
     * @test
     */
    public function testTestCase()
    {
        $file = __DIR__.'/../config/config.xml';

        $container = new BaseContainer;

        $loader = new XmlLoader($container);
        $loader->load($file);

        $this->assertTrue($container->hasFileResource($file));
    }
}
