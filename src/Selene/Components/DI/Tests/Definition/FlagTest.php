<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Definition;

use \Selene\Components\DI\Definition\Flag;

/**
 * @class FlagTest
 * @package Selene\Components\DI
 * @version $Id$
 */
class FlagTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $flag = new Flag('foo');
        $this->assertInstanceof('Selene\Components\DI\Definition\FlagInterface', $flag);
    }

    /** @test */
    public function itShouldSetItsName()
    {
        $flag = new Flag(['name' => 'foo']);

        $this->assertSame('foo', $flag->getName());

        $flag = new Flag('foo');

        $this->assertSame('foo', $flag->getName());
    }

    /** @test */
    public function itShouldGetAttributes()
    {
        $flag = new Flag('foo', ['foo' => 'bar']);

        $this->assertSame('bar', $flag->get('foo'));
        $this->assertNull($flag->get('bar'));
        $this->assertSame('test', $flag->get('bar', 'test'));
    }
}
