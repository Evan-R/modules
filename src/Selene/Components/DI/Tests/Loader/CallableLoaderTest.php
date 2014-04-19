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
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Loader\CallableLoader;

/**
 * @class CallableLoaderTest
 * @package Selene\Components\DI\Tests\Loader
 * @version $Id$
 */
class CallableLoaderTest extends \PHPUnit_Framework_TestCase
{
    public static $self;

    protected function tearDown()
    {
        static::$self = null;
        m::close();
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $container = m::mock('\Selene\Components\DI\ContainerInterface');

        $loader = new CallableLoader($container);

        $this->assertInstanceof('\Selene\Components\Config\Loader\ConfigLoader', $loader);
        $this->assertInstanceof('\Selene\Components\DI\Loader\CallableLoader', $loader);
    }

    /**
     * @test
     */
    public function itShouldSupportCallables()
    {

        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $loader = new CallableLoader($container);

        $this->assertTrue($loader->supports(function () {
        }));
        $this->assertTrue($loader->supports([$this, 'dummyLoad']));
        $this->assertFalse($loader->supports('xml'));
    }

    /**
     * @test
     */
    public function itShouldLoadCloures()
    {
        $container = m::mock('\Selene\Components\DI\ContainerInterface');

        $loader = new CallableLoader($container);

        $closure = function ($container) {
            return $this->dummyLoad($container);
        };

        $container->shouldReceive('addFileResource')->with(__FILE__);

        $loader->load($closure);
    }

    /**
     * @test
     */
    public function itShouldLoadInstanceMethods()
    {
        $container = m::mock('\Selene\Components\DI\ContainerInterface');

        $loader = new CallableLoader($container);

        $container->shouldReceive('addFileResource')->with(__FILE__);

        $loader->load([$this, 'dummyLoad']);
    }

    /**
     * @test
     */
    public function itShouldLoadClassMethods()
    {
        static::$self = $this;

        $container = m::mock('\Selene\Components\DI\ContainerInterface');

        $loader = new CallableLoader($container);

        $container->shouldReceive('addFileResource')->with(__FILE__);

        $loader->load(__CLASS__.'::dummyLoadStatic');

        $fn = __NAMESPACE__.'\\doDummyLoadContainer';
        $loader->load($fn);
    }

    /**
     * dummyLoad
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function dummyLoad($container)
    {
        $this->assertInstanceof('\Selene\Components\DI\ContainerInterface', $container);
    }

    /**
     * dummyLoadStatic
     *
     * @param mixed $container
     *
     * @access public
     * @return void
     */
    public static function dummyLoadStatic($container)
    {
        return static::$self->dummyLoad($container);
    }
}

if (!function_exists(__NAMESPACE__.'\\doDummyLoadContainer')) {
    function doDummyLoadContainer($container)
    {
        CallableLoaderTest::$self->dummyLoad($container);
    }
}
