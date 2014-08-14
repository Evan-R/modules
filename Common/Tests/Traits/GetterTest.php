<?php

/**
 * This File is part of the Selene\Module\Common\Tests\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Tests\Traits;

use \Selene\Module\TestSuite\TestCase;
use \Selene\Module\Common\Tests\Stubs\Traits\GetterClass;

/**
 * @class GetterTest
 * @package Selene\Module\Common\Tests\Traits
 * @version $Id$
 */
class GetterTest extends TestCase
{
    /**
     * @test
     */
    public function testGetDefault()
    {
        $getter = new GetterClass(['foo' => 'bar']);

        $this->assertSame('bar', $getter->getterGetDefault('foo'));
        $this->assertNull($getter->getterGetDefault('bar'));
    }

    /**
     * @test
     */
    public function testGetDefaultKey()
    {
        $getter = new GetterClass(['foo' => null]);

        $this->assertNull($getter->getterGetDefaultUsingKey('foo', 'bar'));
        $this->assertSame('bar', $getter->getterGetDefaultUsingKey('fuzz', 'bar'));
    }

    /**
     * @test
     */
    public function testGetDefaultUsing()
    {
        $getter = new GetterClass(['foo' => null, 'bar' => 'baz']);

        $this->assertSame('bar', $getter->getterGetDefaultUsing('foo', function () {
            return 'bar';
        }));

        $this->assertSame('baz', $getter->getterGetDefaultUsing('bar', function () {
            return 'foo';
        }));
    }

    /**
     * @test
     */
    public function testGetDefaultArray()
    {
        $getter = new GetterClass(['foo' => ['bar' => 'baz']]);

        $this->assertSame('baz', $getter->getterGetDefaultArray('foo.bar'));
        $this->assertSame('default', $getter->getterGetDefaultArray('foo.bam', 'default'));
    }

    /**
     * @test
     */
    public function testHasKey()
    {
        $getter = new GetterClass(['foo' => 'bar', 'bar' => null]);

        $this->assertTrue($getter->getterHasKey('foo'));
        $this->assertTrue($getter->getterHasKey('bar'));
        $this->assertFalse($getter->getterHasKey('bam'));
    }
}
