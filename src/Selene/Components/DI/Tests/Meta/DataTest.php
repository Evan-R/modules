<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Meta;

use \Selene\Components\DI\Meta\Data;

/**
 * @class DataTest
 * @package Selene\Components\DI
 * @version $Id$
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $Data = new Data('foo');
        $this->assertInstanceof('Selene\Components\DI\Meta\MetaDataInterface', $Data);
    }

    /** @test */
    public function itShouldSetItsName()
    {
        $Data = new Data(['name' => 'foo']);

        $this->assertSame('foo', $Data->getName());

        $Data = new Data('foo');

        $this->assertSame('foo', $Data->getName());
    }

    /** @test */
    public function itShouldGetAttributes()
    {
        $Data = new Data('foo', ['foo' => 'bar']);

        $this->assertSame('bar', $Data->get('foo'));
        $this->assertNull($Data->get('bar'));
        $this->assertSame('test', $Data->get('bar', 'test'));
    }
}
