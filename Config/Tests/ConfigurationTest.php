<?php

/**
 * This File is part of the Selene\Module\Config\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Tests;

use \Mockery as m;
use \Selene\Module\Config\Tests\Stubs\Config;

/**
 * @class ConfigurationTest
 * @package Selene\Module\Config\Tests
 * @version $Id$
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Module\Config\ConfigurationInterface', new Config);
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
        return m::mock('Selene\Module\DI\BuilderInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
