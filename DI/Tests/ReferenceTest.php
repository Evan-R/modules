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

use \Selene\Components\DI\Reference;

class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\DI\Reference', new Reference('service'));
    }

    /** @test */
    public function itShouldBeStringAble()
    {
        $ref = new Reference('service');

        $this->assertSame('service', $ref->get());
        $this->assertSame('service', (string)$ref);
    }
}
