<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Validator\Nodes;

use \Selene\Components\Config\Validator\Nodes\EnumNode;
use \Selene\Components\Config\Validator\Exception\ValidationException;

/**
 * @class EnumNodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class EnumNodeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new EnumNode;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShouldSetDefaultValues()
    {
        $node = new EnumNode;
        $node->values('foo', 'bar', 'baz');
        $this->assertTrue($node->validate('foo'));

        try {
            $node->validate('invalidvalue');
        } catch (ValidationException $e) {
            $this->assertSame('allowed values: "foo", "bar", "baz", but value "invalidvalue" given', $e->getMessage());
            return;
        }
        $this->fail();
    }
}
