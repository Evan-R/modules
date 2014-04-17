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
use \Selene\Components\DI\Loaders\XmlLoader;

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
        $this->assertInstanceof('\Selene\Components\DI\Loaders\XmlLoader', $loader);
    }

    /**
     * @test
     */
    public function testTestCase()
    {
        $loader = new XmlLoader($container = new BaseContainer);
        $loader->load(__DIR__.'/../config/config.xml');

        $container->compile();

        $p = $container->getParameters();
        var_dump($p['test_concat']);
        var_dump($p['test_array']);
        var_dump($p['foo_str']);
    }
}
