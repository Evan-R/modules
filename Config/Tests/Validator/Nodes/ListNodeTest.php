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

use \Mockery as m;
use \Selene\Components\Config\Validator\Nodes\ListNode;
use \Selene\Components\Config\Validator\Exception\ValidationException;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;

/**
 * @class ListNodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class ListNodeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new ListNode;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShouldValidateIndexedArrays()
    {
        $node = new ListNode;
        $node->setKey('list');

        $this->assertTrue($node->validate([1, 2, 3, 4]));

        try {
            $node->validate([1, 'bar' => 'string']);
        } catch (ValidationException $e) {
            $this->assertSame('invalid key "bar" in list', $e->getMessage());
        }

        try {
            $node->validate([1, 'foo' => null, 'bar' => 'string']);
        } catch (ValidationException $e) {
            $this->assertSame('invalid keys "foo", "bar" in list', $e->getMessage());
            return;
        }

        $this->fail('test failed');
    }
}
