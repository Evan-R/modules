<?php

/**
 * This File is part of the Selene\Module\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests;

use \Selene\Module\DI\Alias;

class AliasTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Module\DI\Alias', new Alias('foo'));
    }

    /** @test */
    public function itShouldGetTheServiceId()
    {
        $this->assertSame('foo', (new Alias('foo'))->getId());
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
