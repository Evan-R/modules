<?php

/**
 * This File is part of the Selene\Components\Config\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests;

use \Mockery as m;
use \Selene\Components\Config\Tests\Stubs\Config;

/**
 * @class ConfigurationTest
 * @package Selene\Components\Config\Tests
 * @version $Id$
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Components\Config\ConfigurationInterface', new Config);
    }

    /** @test */
    public function itShouldLoadValues()
    {
        $config = new Config;
        $config->load($this->mockBuilder(), $values = ['foo' => 'bar']);

        $this->assertSame($values, $config->getLoadedValues());
    }

    protected function mockBuilder()
    {
        return m::mock('Selene\Components\DI\BuilderInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
