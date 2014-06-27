<?php

/**
 * This File is part of the Selene\Components\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests;

use \Selene\Components\DI\Alias;

class AliasTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Components\DI\Alias', new Alias('foo'));
    }

    /** @test */
    public function itShouldNotBeInternal()
    {
        $this->assertFalse((new Alias('foo'))->isInternal());
    }

    /** @test */
    public function itShouldBeInternal()
    {
        $this->assertTrue((new Alias('foo', true))->isInternal());
        $alias = new Alias('foo');

        $alias->setInternal(true);
        $this->assertTrue($alias->isInternal());
    }
}